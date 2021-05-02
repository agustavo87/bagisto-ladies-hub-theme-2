<?php

declare(strict_types = 1);

namespace Arete\LadiesHub\Generators;

use Webkul\Product\Repositories\ProductRepository;
use Webkul\Attribute\Repositories\AttributeFamilyRepository;
use Webkul\Attribute\Models\Attribute;
use Webkul\Attribute\Models\AttributeOption;

use function Arete\LadiesHub\Helpers\{getFamilyAttributes, getIfExist};

class GenerateGenericProduct extends GenerateEntity 
{
    /**
     * Product Repository instance
     * 
     * @var \Webkul\Product\Repositories\ProductRepository
     */
    protected $productRepository;

    /**
     * AttributeFamily Repository instance
     * 
     * @var \Webkul\Attribute\Repositories\AttributeFamilyRepository;
     */
    protected $attributeFamilyRepository;

    /**
     * Product Attribute Types
     * 
     * @var array
     */
    protected $types;

    /**
     * Handlers of diferent types of attributes of the product
     * 
     * @var array
     */
    public static $typeLookUp = [];

    /**
     * The id of the brand attribute option
     * 
     * @var int|null|
     */
    protected static ?int $brand_id = null;

    protected function boot()
    {
        $this->productRepository = $this->app->make(ProductRepository::class);
        $this->attributeFamilyRepository = $this->app->make(AttributeFamilyRepository::class);
        $this->types = [
            'text',
            'textarea',
            'boolean',
            'select',
            'multiselect',
            'datetime',
            'date',
            'price',
            'image',
            'file',
            'checkbox',
        ];

        if (!count(self::$typeLookUp)) {
            $this->createTypeLookUp();
        }

    }

    protected function getBrandID()
    {
        if (self::$brand_id) return self::$brand_id;

        $brandAttribute = Attribute::where(['code' => 'brand'])->first();
        if (AttributeOption::where(['attribute_id' => $brandAttribute->id])->exists()) {
            return AttributeOption::where(['attribute_id' => $brandAttribute->id])->first()->id;
        } 
        
        $brand = $this->app->make(GenerateDemoBrand::class)->create();
        return self::$brand_id = $brand->id;
    }

    /**
     * @param int $brand_id The id of the AttributeOption of the 
     *                      brand attribute.
     * 
     * @return \Webkul\Product\Models\Product
     */
    public function create($brand_id = null)
    {
        $brand_id = $brand_id ?? $this->getBrandID();

        $attributeFamily = $this->attributeFamilyRepository->findWhere([
            'code' => 'default',
        ])->first();

        $attributes = getFamilyAttributes($attributeFamily);

        $faker = $this->faker;

        $sku = strtolower($faker->bothify('??#####???'));
        $data['sku'] = $sku;
        $data['attribute_family_id'] = $attributeFamily->id;
        $data['type'] = 'simple';

        $product = $this->productRepository->create($data);

        unset($data);

        $date = today();
        $specialFrom = $date->toDateString();
        $specialTo = $date->addDays(7)->toDateString();

        
        foreach ($attributes as $attribute) {
            self::$typeLookUp[$attribute->type]($attribute, $data, $faker, $sku, $date, $specialFrom, $specialTo);
        }

        // special price has to be less than price and not less than cost
        // cost has to be less than price
        if( ( (bool) getIfExist('special_price', $data) ) && $data['price'] && $data['cost']) {
            if ($data['cost'] >= $data['price'] || $data['cost'] < ($data['price']*0.3)) {
                $data['cost'] = $data['price'] * 0.75;
            }
            $data['special_price'] = $data['price'] -  ($data['price'] - $data['cost']) * (rand(20,70)/100);
        }

        $channel = core()->getCurrentChannel();

        $data['locale'] = core()->getCurrentLocale()->code;

        $data['brand'] = $brand_id;

        $data['channel'] = $channel->code;

        $data['channels'] = [
            0 => $channel->id,
        ];

        $inventorySource = $channel->inventory_sources[0];

        $data['inventories'] = [
            $inventorySource->id => 10,
        ];

        $data['categories'] = [
            0 => $channel->root_category->id,
        ];

        $updated = $this->productRepository->update($data, $product->id);

        return $updated;
    }

    /**
     * Creates functions that handles the types of attributes
     * 
     * @return void
     */
    public function createTypeLookUp()
    {
        $selectFiller = function ($attribute, &$data, $faker, $sku, $date, $specialFrom, $specialTo) {
            if ($attribute->code === 'tax_category_id' ) return;

            $options = $attribute->options;

                if ($attribute->type == 'select') {
                    if ($options->count()) {
                        $option = $options->first()->id;

                        $data[$attribute->code] = $option;
                    } else {
                        $data[$attribute->code] = "";
                    }
                } elseif ($attribute->type == 'multiselect') {
                    if ($options->count()) {
                        $option = $options->first()->id;

                        $optionArray = [];

                        array_push($optionArray, $option);

                        $data[$attribute->code] = $optionArray;
                    } else {
                        $data[$attribute->code] = "";
                    }
                } else {
                    $data[$attribute->code] = "";
                }
        };

        

        self::$typeLookUp = [
            'text' => function ($attribute, &$data, $faker, $sku, $date, $specialFrom, $specialTo)   {
                $code = $attribute->code;
                if ($code == 'width'
                    || $code == 'height'
                    || $code == 'depth'
                    || $code == 'weight'
                ) {
                    $data[$code] = $faker->randomNumber(3);
                } elseif ($code == 'url_key') {
                    $data[$code] = $sku;
                } elseif ($code =='product_number') {
                    $data[$code] = $faker->randomNumber(5);
                } elseif ($code =='name') {
                    $data[$code] = ucfirst($faker->words(rand(1,4),true));
                } elseif ($code != 'sku') {
                    $data[$code] = $faker->name;
                } else {
                    $data[$code] = $sku;
                }
            },
            'textarea' => function ($attribute, &$data, $faker, $sku, $date, $specialFrom, $specialTo) {
                $data[$attribute->code] = $faker->text;

                if ($attribute->code == 'description' || $attribute->code == 'short_description') {
                    $data[$attribute->code] = '<p>' . $data[$attribute->code] . '</p>';
                }
            },
            'boolean' => function ($attribute, &$data, $faker, $sku, $date, $specialFrom, $specialTo) {
                $data[$attribute->code] = $faker->boolean;
            },
            'price' => function ($attribute, &$data, $faker, $sku, $date, $specialFrom, $specialTo)  {
                $codigo = $attribute->code;
                $price = '';
                echo "\nEstableciendo price: {$codigo}\n";
                if($codigo == 'special_price') {
                    echo "\nEstableciendo special price\n";
                    $si = $faker->boolean();
                    echo "\n Resultado de faker:".  ($si ? 'true' : 'false') . "\n";
                    if($si) {
                        $price= rand(5,200);
                    } else {
                        $price = '';
                    }
                } else {
                    $price = rand(5,200);
                }
                echo "\n Precio final: {$price}.\n";
                $data[$codigo] = $price;
                return;
            },
            'datetime' => function ($attribute, &$data, $faker, $sku, $date, $specialFrom, $specialTo)  {
                $data[$attribute->code] = $date->toDateTimeString();
            },
            'date' => function ($attribute, &$data, $faker, $sku, $date, $specialFrom, $specialTo)  {
                if ($attribute->code == 'special_price_from') {
                    $data[$attribute->code] = $specialFrom;
                } elseif ($attribute->code == 'special_price_to') {
                    $data[$attribute->code] = $specialTo;
                } else {
                    $data[$attribute->code] = $date->toDateString();
                }
            },
            'select' => $selectFiller,
            'multiselect' => $selectFiller,
            'checkbox' => function ($attribute, &$data, $faker, $sku, $date, $specialFrom, $specialTo)  {
                $options = $attribute->options;

                if ($options->count()) {
                    $option = $options->first()->id;

                    $optionArray = [];

                    array_push($optionArray, $option);

                    $data[$attribute->code] = $optionArray;
                } else {
                    $data[$attribute->code] = "";
                }
            }
        ];
    }
}
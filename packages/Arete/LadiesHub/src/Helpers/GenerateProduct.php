<?php

namespace Arete\LadiesHub\Helpers;

use Webkul\Attribute\Models\Attribute;
use Webkul\Attribute\Models\AttributeOption;
use Webkul\Product\Repositories\ProductRepository;
use Webkul\Attribute\Repositories\AttributeFamilyRepository;
use Illuminate\Support\Str;

use function Arete\LadiesHub\Helpers\difference;

/**
 * Class GenerateProduct
 *
 * @package Arete\LadiesHub\Helpers
 */
class GenerateProduct
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
     * @var \Webkul\Product\Repositories\AttributeFamilyRepository
     */
    protected $attributeFamilyRepository;

    /**
     * Product Attribute Types
     * 
     * @var array
     */
    protected $types;


    public static $typeLookUp = [];

    /**
     * Create a new helper instance.
     *
     * @param  \Webkul\Product\Repositories\ProductRepository  $productImage
     * @param  \Webkul\Product\Repositories\AttributeFamilyRepository  $productImage
     * @return void
     */
    public function __construct(
        ProductRepository $productRepository,
        AttributeFamilyRepository $attributeFamilyRepository
    ) {
        $this->productRepository = $productRepository;
        
        $this->attributeFamilyRepository = $attributeFamilyRepository;

        if (!count(self::$typeLookUp)) {
            $this->createTypeLookUp();
        }

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
    }



    /**
     * This brand option needs to be available so that the generated product
     * can be linked to the order_brands table after checkout.
     * 
     * @return void
     */
    public function generateDemoBrand()
    {
        $brand = Attribute::where(['code' => 'brand'])->first();

        if (! AttributeOption::where(['attribute_id' => $brand->id])->exists()) {

            AttributeOption::create([
                'admin_name'   => 'Ladies Hub Joy Gifts',
                'attribute_id' => $brand->id,
            ]);
        }
    }


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
                // if($attribute->code['special_price']) {
                //     if($faker->boolean()) {
                //         $data['special_price'] = rand(5,200);
                //     }
                //     return;
                // }
                $data[$attribute->code] = rand(5,200);
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

    /**
     * @return mixed
     */
    public function create()
    {

        echo "\ncomenzando: ". difference()->format("%h:%m:%s transcurridos\n");

        $attributeFamily = $this->attributeFamilyRepository->findWhere([
            'code' => 'default',
        ])->first();

        $attributes = $this->getFamilyAttributes($attributeFamily);


        $faker = \Faker\Factory::create();

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
        if($data['special_price'] && $data['price'] && $data['cost']) {
            if ($data['cost'] >= $data['price'] || $data['cost'] < ($data['price']*0.3)) {
                $data['cost'] = $data['price'] * 0.75;
            }
            $data['special_price'] = $data['price'] -  ($data['price'] - $data['cost']) * (rand(20,70)/100);
        }

        $channel = core()->getCurrentChannel();

        $data['locale'] = core()->getCurrentLocale()->code;

        $brand = Attribute::where(['code' => 'brand'])->first();
        $data['brand'] = AttributeOption::where(['attribute_id' => $brand->id])->first()->id ?? '';

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

        echo "finalizando: ". difference()->format("%h:%m:%s transcurridos\n");

        return $updated;
    }

    /**
     * @param \Webkul\Attribute\Models\AttributeFamily $attributeFamily
     * @return \Illuminate\Support\Collection
     */
    public function getFamilyAttributes($attributeFamily)
    {
        $attributes = collect();
        $attributeGroups = $attributeFamily->attribute_groups;

        foreach ($attributeGroups as $attributeGroup) {
            foreach ($attributeGroup->custom_attributes as $customAttribute) {
                $attributes->push($customAttribute);
            }
        }

        return $attributes;
    }
}
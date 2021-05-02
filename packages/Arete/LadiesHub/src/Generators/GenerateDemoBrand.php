<?php

declare(strict_types = 1);

namespace Arete\LadiesHub\Generators;

use Webkul\Attribute\Models\Attribute;
use Webkul\Attribute\Models\AttributeOption;


class GenerateDemoBrand extends GenerateEntity 
{
    /**
     * Creates a Brand and return it.
     * 
     * If the brand already exists returns it.
     * 
     * @param string $name
     * 
     * @return \Webkul\Attribute\Models\AttributeOption
     */
    public function create(string $name = "")
    {
        $brandAttribute = Attribute::where(['code' => 'brand'])->first();
        $name = $name ? $name : ucwords($this->faker->words(3,true));

        if (!AttributeOption::where(['admin_name' => $name])->exists()) {
            return AttributeOption::create([
                'admin_name'   => $name,
                'attribute_id' => $brandAttribute->id,
            ]);
        } else {
            return AttributeOption::where(['admin_name' => $name])->first();
        }
    }
}
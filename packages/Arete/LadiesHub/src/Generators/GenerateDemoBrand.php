<?php

declare(strict_types = 1);

namespace Arete\LadiesHub\Generators;

use Webkul\Attribute\Models\Attribute;
use Webkul\Attribute\Models\AttributeOption;


class GenerateDemoBrand extends GenerateEntity 
{
    /**
     * @param string $name
     * 
     * @return \Webkul\Attribute\Models\AttributeOption
     */
    public function create(string $name = "")
    {
        $brandAttribute = Attribute::where(['code' => 'brand'])->first();

        return AttributeOption::create([
            'admin_name'   => $name ?? $this->faker->words(3,true),
            'attribute_id' => $brandAttribute->id,
        ]);
    }
}
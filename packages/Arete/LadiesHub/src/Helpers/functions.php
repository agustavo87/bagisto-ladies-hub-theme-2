<?php

declare(strict_types = 1);

namespace Arete\LadiesHub\Helpers;


function difference () {
    static $start = null;
    if(!$start) $start = now();
    return $start->diff(now());
}

/**
 * @param \Webkul\Attribute\Models\AttributeFamily $attributeFamily
 * @return \Illuminate\Support\Collection
 */
function getFamilyAttributes($attributeFamily)
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

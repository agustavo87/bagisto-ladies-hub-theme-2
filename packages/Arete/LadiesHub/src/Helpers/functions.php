<?php

declare(strict_types = 1);

namespace Arete\LadiesHub\Helpers;

if  (!function_exists( __NAMESPACE__ . '\difference')) {

    function difference () {
        static $start = null;
        if(!$start) $start = now();
        return $start->diff(now());
    }

}

if  (!function_exists( __NAMESPACE__ . '\getFamilyAttributes')) {

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

}


if  (!function_exists( __NAMESPACE__ . '\getIfExist')) {
    /**
     * Get the element of an array if it exist.
     * 
     * reutrn null otherwise
     * 
     * @param mixed $key
     * @param array $array
     * 
     * @return mixed
     */
    function getIfExist($key, array $array)
    {
        return array_key_exists($key, $array) && $array[$key];
    }
}

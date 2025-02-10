<?php
// src/Form/DataTransformer/StatutEnumTransformer.php

namespace App\Form\DataTransformer;

use App\Enum\StatutEnum;
use Symfony\Component\Form\DataTransformerInterface;

class StatutEnumTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        // Transform StatutEnum to its string value
        if ($value instanceof StatutEnum) {
            return $value->value;
        }
        return null; // Return null if it's not an instance of StatutEnum
    }

    public function reverseTransform($value)
    {
        // Transform string value back to StatutEnum instance
        if (in_array($value, StatutEnum::getValues())) {
            return StatutEnum::from($value);
        }
        return null; // If the value is not valid, return null
    }
}


<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/27/15 1:22 AM
*/

namespace Youshido\GraphQL\Type\Scalar;


class IntType extends AbstractScalarType
{

    public function getName(): string
    {
        return 'Int';
    }

    public function serialize($value): mixed
    {
        if ($value === null) {
            return null;
        } elseif (is_int($value)) {
            return $value;
        } else {
            $value = (int)$value;

            return $value != 0 ? $value : null;
        }
    }

    public function isValidValue(mixed $value): bool
    {
        return is_null($value) || is_int($value);
    }

    public function getDescription(): string
    {
        return 'The `Int` scalar type represents non-fractional signed whole numeric ' .
            'values. Int can represent values between -(2^53 - 1) and 2^53 - 1 since ' .
            'represented in JSON as double-precision floating point numbers specified' .
            'by [IEEE 754](http://en.wikipedia.org/wiki/IEEE_floating_point).';
    }

}

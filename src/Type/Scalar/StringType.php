<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/27/15 1:05 AM
*/

namespace Youshido\GraphQL\Type\Scalar;


class StringType extends AbstractScalarType
{

    public function getName(): string
    {
        return 'String';
    }

    public function serialize($value): ?string
    {
        if ($value === true) {
            return 'true';
        } elseif ($value === false) {
            return 'false';
        } elseif ($value === null) {
            return null;
        }

        if (is_array($value)) {
            return '';
        }

        return (string)$value;
    }

    public function isValidValue(mixed $value): bool
    {
        return is_null($value) || is_scalar($value) || ((is_object($value) && method_exists($value, '__toString')));
    }

    public function getDescription(): string
    {
        return 'The `String` scalar type represents textual data, represented as UTF-8 ' .
            'character sequences. The String type is most often used by GraphQL to ' .
            'represent free-form human-readable text.';
    }

}

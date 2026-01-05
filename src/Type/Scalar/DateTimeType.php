<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/27/15 1:22 AM
*/

namespace Youshido\GraphQL\Type\Scalar;

use DateTime;
use DateTimeInterface;

class DateTimeType extends AbstractScalarType
{

    private $format;

    public function __construct($format = 'Y-m-d H:i:s')
    {
        $this->format = $format;
    }

    public function getName(): string
    {
        return 'DateTime';
    }

    public function isValidValue(mixed $value): bool
    {
        if (($value instanceof DateTimeInterface) || is_null($value)) {
            return true;
        } elseif (is_string($value)) {
            $date = $this->createFromFormat($value);
        } else {
            $date = null;
        }

        return (bool)$date;
    }

    public function serialize($value): mixed
    {
        $date = null;

        if (is_string($value)) {
            $date = $this->createFromFormat($value);
        } elseif ($value instanceof DateTimeInterface) {
            $date = $value;
        }

        return $date ? $date->format($this->format) : null;
    }

    public function parseValue($value): bool|null|DateTimeInterface|DateTime
    {
        if (is_string($value)) {
            $date = $this->createFromFormat($value);
        } elseif ($value instanceof DateTimeInterface) {
            $date = $value;
        } else {
            $date = false;
        }

        return $date ?: null;
    }

    private function createFromFormat(string $value): DateTime|bool
    {
        return DateTime::createFromFormat($this->format, $value);
    }

    public function getDescription(): string
    {
        return sprintf('Representation of date and time in "%s" format', $this->format);
    }

}

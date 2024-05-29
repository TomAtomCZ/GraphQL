<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 5/11/16 9:19 PM
*/

namespace Youshido\GraphQL\Type;

use Youshido\GraphQL\Exception\ConfigurationException;
use Youshido\GraphQL\Type\Scalar\AbstractScalarType;

class TypeFactory
{
    private static array $objectsHash = [];

    /**
     *
     * @param string $type
     * @return AbstractScalarType|null
     * @throws ConfigurationException
     */
    public static function getScalarType(string $type): ?AbstractScalarType
    {
        if (TypeService::isScalarType($type)) {
            if (empty(self::$objectsHash[$type])) {
                $name = ucfirst($type);

                $name = $name == 'Datetime' ? 'DateTime' : $name;
                $name = $name == 'Datetimetz' ? 'DateTimeTz' : $name;

                $className = 'Youshido\GraphQL\Type\Scalar\\' . $name . 'Type';

                // Hotfix for our custom app - TODO - add via configuration
                if ($name == 'DateTimeAsString' || $name == 'StringOrArray' || $name == 'Boolean' || $name == 'Float' || $name == 'Int') {
                    $className = 'App\GraphQL\Schema\Type\Scalar\\' . $name . 'Type';
                    // Fallback to base scalar types if custom doesn't exist
                    if (!class_exists($className)) {
                        $className = 'Youshido\GraphQL\Type\Scalar\\' . $name . 'Type';
                    }
                }

                self::$objectsHash[$type] = new $className();
            }

            return self::$objectsHash[$type];
        } else {
            throw new ConfigurationException('Configuration problem with type ' . $type);
        }
    }

    /**
     * @return string[]
     */
    public static function getScalarTypesNames(): array
    {
        return [
            TypeMap::TYPE_INT,
            TypeMap::TYPE_FLOAT,
            TypeMap::TYPE_STRING,
            TypeMap::TYPE_BOOLEAN,
            TypeMap::TYPE_ID,
            TypeMap::TYPE_DATETIME,
            TypeMap::TYPE_DATE,
            TypeMap::TYPE_TIMESTAMP,
            TypeMap::TYPE_DATETIMETZ,
            TypeMap::TYPE_DATETIME_AS_STRING,
            TypeMap::TYPE_STRING_OR_ARRAY
        ];
    }
}

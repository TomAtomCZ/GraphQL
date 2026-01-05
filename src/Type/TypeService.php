<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 5/11/16 10:19 PM
*/

namespace Youshido\GraphQL\Type;


use Exception;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Youshido\GraphQL\Type\Enum\AbstractEnumType;
use Youshido\GraphQL\Type\InputObject\AbstractInputObjectType;
use Youshido\GraphQL\Type\ListType\AbstractListType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\AbstractScalarType;
use Youshido\GraphQL\Type\Scalar\StringType;

class TypeService
{

    final const TYPE_CALLABLE = 'callable';

    final const TYPE_GRAPHQL_TYPE = 'graphql_type';

    final const TYPE_OBJECT_TYPE = 'object_type';

    final const TYPE_ARRAY_OF_OBJECT_TYPES = 'array_of_object_types';

    final const TYPE_OBJECT_INPUT_TYPE = 'object_input_type';

    final const TYPE_LIST = 'list';

    final const TYPE_BOOLEAN = TypeMap::TYPE_BOOLEAN;

    final const TYPE_STRING = TypeMap::TYPE_STRING;

    final const TYPE_ARRAY = 'array';

    final const TYPE_ARRAY_OF_FIELDS_CONFIG = 'array_of_fields';

    final const TYPE_ARRAY_OF_INPUT_FIELDS = 'array_of_inputs';

    final const TYPE_ENUM_VALUES = 'array_of_values';

    final const TYPE_ARRAY_OF_INTERFACES = 'array_of_interfaces';

    final const TYPE_ANY = 'any';

    final const TYPE_ANY_OBJECT = 'any_object';

    final const TYPE_ANY_INPUT = 'any_input';

    /**
     * @throws Exception
     */
    public static function resolveNamedType($object)
    {
        if (is_object($object)) {
            if ($object instanceof AbstractType) {
                return $object->getType();
            }
        } elseif (is_null($object)) {
            return null;
        } elseif (is_scalar($object)) {
            return new StringType();
        }

        throw new Exception('Invalid type');
    }

    /**
     * @param AbstractType|mixed $type
     */
    public static function isInterface(mixed $type): bool
    {
        if (!is_object($type)) {
            return false;
        }

        return $type->getKind() == TypeMap::KIND_INTERFACE;
    }

    /**
     * @param AbstractType|mixed $type
     */
    public static function isAbstractType(mixed $type): bool
    {
        if (!is_object($type)) {
            return false;
        }

        return in_array($type->getKind(), [TypeMap::KIND_INTERFACE, TypeMap::KIND_UNION]);
    }

    public static function isScalarType($type): bool
    {
        if (is_object($type)) {
            return $type instanceof AbstractScalarType;
        }

        return in_array(strtolower((string)$type), TypeFactory::getScalarTypesNames());
    }

    public static function isGraphQLType($type): bool
    {
        return $type instanceof AbstractType || TypeService::isScalarType($type);
    }

    public static function isLeafType($type): bool
    {
        return $type instanceof AbstractEnumType || TypeService::isScalarType($type);
    }

    public static function isObjectType($type): bool
    {
        return $type instanceof AbstractObjectType;
    }

    /**
     * @param mixed|AbstractType $type
     */
    public static function isInputType(mixed $type): bool
    {
        if (is_object($type)) {
            $namedType = $type->getNullableType()->getNamedType();

            return ($namedType instanceof AbstractScalarType)
                || ($type instanceof AbstractListType)
                || ($namedType instanceof AbstractInputObjectType)
                || ($namedType instanceof AbstractEnumType);
        } else {
            return TypeService::isScalarType($type);
        }
    }

    public static function isInputObjectType($type): bool
    {
        return $type instanceof AbstractInputObjectType;
    }

    /**
     * @param bool $enableMagicCall whether to attempt to resolve properties using __call()
     *
     * @return mixed|null
     */
    public static function getPropertyValue(object|array $data, string $path, bool $enableMagicCall = false): mixed
    {
        // Normalize the path
        if (is_array($data)) {
            $path = sprintf('[%s]', $path);
        }

        // Optionally enable __call() support
        $propertyAccessorBuilder = PropertyAccess::createPropertyAccessorBuilder();

        if ($enableMagicCall) {
            $propertyAccessorBuilder->enableMagicCall();
        }

        $propertyAccessor = $propertyAccessorBuilder->getPropertyAccessor();

        return $propertyAccessor->isReadable($data, $path) ? $propertyAccessor->getValue($data, $path) : null;
    }
}
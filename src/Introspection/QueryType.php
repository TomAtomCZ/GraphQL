<?php
/**
 * Date: 03.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Introspection;

use Youshido\GraphQL\Config\Object\ObjectTypeConfig;
use Youshido\GraphQL\Exception\ConfigurationException;
use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQL\Field\Field;
use Youshido\GraphQL\Introspection\Traits\TypeCollectorTrait;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\CompositeTypeInterface;
use Youshido\GraphQL\Type\Enum\AbstractEnumType;
use Youshido\GraphQL\Type\InputObject\AbstractInputObjectType;
use Youshido\GraphQL\Type\InterfaceType\AbstractInterfaceType;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\BooleanType;
use Youshido\GraphQL\Type\TypeMap;
use Youshido\GraphQL\Type\Union\AbstractUnionType;
use function array_unique;

class QueryType extends AbstractObjectType
{

    use TypeCollectorTrait;

    /**
     * @return String type name
     */
    public function getName(): string
    {
        return '__Type';
    }

    public function resolveOfType(AbstractType $value)
    {
        if ($value instanceof CompositeTypeInterface) {
            return $value->getTypeOf();
        }

        return null;
    }

    public function resolveInputFields($value): ?array
    {
        if ($value instanceof AbstractInputObjectType) {
            /** @var AbstractObjectType $value */
            return $value->getConfig()->getFields();
        }

        return null;
    }

    public function resolveEnumValues($value, array $args): ?array
    {
        /** @var $value AbstractType|AbstractEnumType */
        if ($value && $value->getKind() == TypeMap::KIND_ENUM) {
            $data = [];
            foreach ($value->getValues() as $enumValue) {
                if (!$args['includeDeprecated'] && (isset($enumValue['isDeprecated']) && $enumValue['isDeprecated'])) {
                    continue;
                }

                if (!array_key_exists('description', $enumValue)) {
                    $enumValue['description'] = '';
                }

                if (!array_key_exists('isDeprecated', $enumValue)) {
                    $enumValue['isDeprecated'] = false;
                }

                if (!array_key_exists('deprecationReason', $enumValue)) {
                    $enumValue['deprecationReason'] = null;
                }

                $data[] = $enumValue;
            }

            return $data;
        }

        return null;
    }

    public function resolveFields($value, $args): ?array
    {
        /** @var AbstractType $value */
        if (!$value ||
            in_array($value->getKind(), [TypeMap::KIND_SCALAR, TypeMap::KIND_UNION, TypeMap::KIND_INPUT_OBJECT, TypeMap::KIND_ENUM])
        ) {
            return null;
        }

        /** @var AbstractObjectType $value */
        return array_filter($value->getConfig()->getFields(), static function (Field $field) use ($args): bool {
            /** @var $field Field */
            return !in_array($field->getName(), ['__type', '__schema']) && !(!$args['includeDeprecated'] && $field->isDeprecated());
        });
    }

    public function resolveInterfaces($value): array
    {
        /** @var $value AbstractType */
        if ($value->getKind() == TypeMap::KIND_OBJECT) {
            /** @var $value AbstractObjectType */
            return $value->getConfig()->getInterfaces() ?: [];
        }

        return [];
    }

    public function resolvePossibleTypes($value, $args, ResolveInfo $info): ?array
    {
        /** @var $value AbstractObjectType */
        if ($value->getKind() == TypeMap::KIND_INTERFACE) {
            $schema = $info->getExecutionContext()->getSchema();
            $this->collectTypes($schema->getQueryType());
            foreach ($schema->getTypesList()->getTypes() as $type) {
                $this->collectTypes($type);
            }

            $possibleTypes = [];
            foreach ($this->types as $type) {
                /** @var $type AbstractObjectType */
                if ($type->getKind() == TypeMap::KIND_OBJECT) {
                    $interfaces = $type->getConfig()->getInterfaces();

                    if ($interfaces) {
                        foreach ($interfaces as $interface) {
                            if ($interface->getName() == $value->getName()) {
                                $possibleTypes[] = $type;
                            }

                            if ($interface instanceof AbstractInterfaceType) {
                                foreach ($interface->getImplementations() as $implementationType) {
                                    $possibleTypes[] = $implementationType;
                                }
                            }
                        }
                    }
                }
            }

            return array_unique($possibleTypes);
        } elseif ($value->getKind() == TypeMap::KIND_UNION) {
            /** @var $value AbstractUnionType */
            return $value->getTypes();
        }

        return null;
    }

    /**
     * @throws ConfigurationException
     */
    public function build(ObjectTypeConfig $config): void
    {
        $config
            ->addField('name', TypeMap::TYPE_STRING)
            ->addField('kind', new NonNullType(TypeMap::TYPE_STRING))
            ->addField('description', TypeMap::TYPE_STRING)
            ->addField('ofType', [
                'type' => new QueryType(),
                'resolve' => $this->resolveOfType(...)
            ])
            ->addField(new Field([
                'name' => 'inputFields',
                'type' => new ListType(new NonNullType(new InputValueType())),
                'resolve' => $this->resolveInputFields(...)
            ]))
            ->addField(new Field([
                'name' => 'enumValues',
                'args' => [
                    'includeDeprecated' => [
                        'type' => new BooleanType(),
                        'defaultValue' => false
                    ]
                ],
                'type' => new ListType(new NonNullType(new EnumValueType())),
                'resolve' => function ($value, array $args): ?array {
                    return $this->resolveEnumValues($value, $args);
                }
            ]))
            ->addField(new Field([
                'name' => 'fields',
                'args' => [
                    'includeDeprecated' => [
                        'type' => new BooleanType(),
                        'defaultValue' => false
                    ]
                ],
                'type' => new ListType(new NonNullType(new FieldType())),
                'resolve' => function ($value, $args): ?array {
                    return $this->resolveFields($value, $args);
                }
            ]))
            ->addField(new Field([
                'name' => 'interfaces',
                'type' => new ListType(new NonNullType(new QueryType())),
                'resolve' => $this->resolveInterfaces(...)
            ]))
            ->addField('possibleTypes', [
                'type' => new ListType(new NonNullType(new QueryType())),
                'resolve' => function ($value, $args, ResolveInfo $info): ?array {
                    return $this->resolvePossibleTypes($value, $args, $info);
                }
            ]);
    }
}
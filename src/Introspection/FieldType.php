<?php
/**
 * Date: 03.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Introspection;

use Youshido\GraphQL\Config\Object\ObjectTypeConfig;
use Youshido\GraphQL\Exception\ConfigurationException;
use Youshido\GraphQL\Field\FieldInterface;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\TypeMap;

class FieldType extends AbstractObjectType
{

    public function resolveType(FieldInterface $value): AbstractType
    {
        return $value->getType();
    }

    public function resolveArgs(FieldInterface $value): array
    {
        if ($value->hasArguments()) {
            return $value->getArguments();
        }

        return [];
    }

    /**
     * @throws ConfigurationException
     */
    public function build(ObjectTypeConfig $config): void
    {
        $config
            ->addField('name', new NonNullType(TypeMap::TYPE_STRING))
            ->addField('description', TypeMap::TYPE_STRING)
            ->addField('isDeprecated', new NonNullType(TypeMap::TYPE_BOOLEAN))
            ->addField('deprecationReason', TypeMap::TYPE_STRING)
            ->addField('type', [
                'type' => new NonNullType(new QueryType()),
                'resolve' => $this->resolveType(...),
            ])
            ->addField('args', [
                'type' => new NonNullType(new ListType(new NonNullType(new InputValueType()))),
                'resolve' => $this->resolveArgs(...),
            ]);
    }

    public function isValidValue(mixed $value): bool
    {
        return $value instanceof FieldInterface;
    }

    /**
     * @return String type name
     */
    public function getName(): string
    {
        return '__Field';
    }
}

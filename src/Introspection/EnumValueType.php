<?php
/**
 * Date: 03.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Introspection;


use Youshido\GraphQL\Config\Object\ObjectTypeConfig;
use Youshido\GraphQL\Exception\ConfigurationException;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\TypeMap;

class EnumValueType extends AbstractObjectType
{

    /**
     * @throws ConfigurationException
     */
    public function build(ObjectTypeConfig $config): void
    {
        $config
            ->addField('name', new NonNullType(TypeMap::TYPE_STRING))
            ->addField('description', TypeMap::TYPE_STRING)
            ->addField('deprecationReason', TypeMap::TYPE_STRING)
            ->addField('isDeprecated', new NonNullType(TypeMap::TYPE_BOOLEAN));
    }

    /**
     * @return String type name
     */
    public function getName(): string
    {
        return '__EnumValue';
    }
}

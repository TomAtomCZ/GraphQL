<?php
/**
 * Date: 16.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Introspection;

use Youshido\GraphQL\Config\Directive\DirectiveConfig;
use Youshido\GraphQL\Config\Object\ObjectTypeConfig;
use Youshido\GraphQL\Directive\DirectiveInterface;
use Youshido\GraphQL\Exception\ConfigurationException;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\TypeMap;

class DirectiveType extends AbstractObjectType
{
    /**
     * @return String type name
     */
    public function getName(): string
    {
        return '__Directive';
    }

    public function resolveArgs(DirectiveInterface $value): array
    {
        if ($value->hasArguments()) {
            return $value->getArguments();
        }

        return [];
    }

    public function resolveLocations(DirectiveInterface $value): mixed
    {
        /** @var DirectiveConfig $directiveConfig */
        $directiveConfig = $value->getConfig();

        return $directiveConfig->getLocations();
    }

    /**
     * @throws ConfigurationException
     */
    public function build(ObjectTypeConfig $config): void
    {
        $config
            ->addField('name', new NonNullType(TypeMap::TYPE_STRING))
            ->addField('description', TypeMap::TYPE_STRING)
            ->addField('args', [
                'type' => new NonNullType(new ListType(new NonNullType(new InputValueType()))),
                'resolve' => $this->resolveArgs(...),
            ])
            ->addField('locations', [
                'type' => new NonNullType(new ListType(new NonNullType(new DirectiveLocationType()))),
                'resolve' => $this->resolveLocations(...),
            ]);
    }
}

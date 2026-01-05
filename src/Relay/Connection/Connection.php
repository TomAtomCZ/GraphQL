<?php
/**
 * Date: 10.05.16
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Relay\Connection;


use Youshido\GraphQL\Exception\ConfigurationException;
use Youshido\GraphQL\Relay\Type\PageInfoType;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\TypeMap;

class Connection
{

    public static function connectionArgs(): array
    {
        return array_merge(self::forwardArgs(), self::backwardArgs());
    }

    public static function forwardArgs(): array
    {
        return [
            'after' => ['type' => TypeMap::TYPE_STRING],
            'first' => ['type' => TypeMap::TYPE_INT]
        ];
    }

    public static function backwardArgs(): array
    {
        return [
            'before' => ['type' => TypeMap::TYPE_STRING],
            'last' => ['type' => TypeMap::TYPE_INT]
        ];
    }

    /**
     * @param null|string $name
     * @option string  edgeFields
     *
     * @throws ConfigurationException
     */
    public static function edgeDefinition(AbstractType $type, $name = null, array $config = []): ObjectType
    {
        $name = $name ?: $type->getName();
        $edgeFields = empty($config['edgeFields']) ? [] : $config['edgeFields'];

        return new ObjectType([
            'name' => $name . 'Edge',
            'description' => 'An edge in a connection.',
            'fields' => array_merge([
                'node' => [
                    'type' => $type,
                    'description' => 'The item at the end of the edge',
                    'resolve' => [__CLASS__, 'getNode'],
                ],
                'cursor' => [
                    'type' => TypeMap::TYPE_STRING,
                    'description' => 'A cursor for use in pagination'
                ]
            ], $edgeFields)
        ]);
    }

    /**
     * @param null|string $name
     * @option string  connectionFields
     *
     * @throws ConfigurationException
     * @throws ConfigurationException
     * @throws ConfigurationException
     * @throws ConfigurationException
     */
    public static function connectionDefinition(AbstractType $type, $name = null, array $config = []): ObjectType
    {
        $name = $name ?: $type->getName();
        $connectionFields = empty($config['connectionFields']) ? [] : $config['connectionFields'];

        return new ObjectType([
            'name' => $name . 'Connection',
            'description' => 'A connection to a list of items.',
            'fields' => array_merge([
                'totalCount' => [
                    'type' => new NonNullType(new IntType()),
                    'description' => 'How many nodes.',
                    'resolve' => [__CLASS__, 'getTotalCount'],
                ],
                'pageInfo' => [
                    'type' => new NonNullType(new PageInfoType()),
                    'description' => 'Information to aid in pagination.',
                    'resolve' => [__CLASS__, 'getPageInfo'],
                ],
                'edges' => [
                    'type' => new ListType(self::edgeDefinition($type, $name, $config)),
                    'description' => 'A list of edges.',
                    'resolve' => [__CLASS__, 'getEdges'],
                ]
            ], $connectionFields)
        ]);
    }

    public static function getTotalCount(array $value)
    {
        return $value['totalCount'] ?? -1;
    }

    public static function getEdges(array $value)
    {
        return $value['edges'] ?? null;
    }

    public static function getPageInfo(array $value)
    {
        return $value['pageInfo'] ?? null;
    }

    public static function getNode(array $value)
    {
        return $value['node'] ?? null;
    }
}

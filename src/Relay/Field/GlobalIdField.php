<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 5/10/16 11:23 PM
*/

namespace Youshido\GraphQL\Relay\Field;


use Youshido\GraphQL\Exception\ConfigurationException;
use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQL\Field\AbstractField;
use Youshido\GraphQL\Relay\Node;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Scalar\IdType;

class GlobalIdField extends AbstractField
{

    /** @var  string */
    protected $typeName;

    /**
     * @param string $typeName
     * @throws ConfigurationException
     */
    public function __construct($typeName)
    {
        $this->typeName = $typeName;

        $config = [
            'type' => $this->getType(),
            'name' => $this->getName(),
            'resolve' => function ($value, array $args, ResolveInfo $info): ?string {
                return $this->resolve($value, $args, $info);
            }
        ];

        parent::__construct($config);
    }

    public function getName(): string
    {
        return 'id';
    }

    public function getDescription(): string
    {
        return 'The ID of an object';
    }

    /**
     * @throws ConfigurationException
     */
    public function getType(): NonNullType
    {
        return new NonNullType(new IdType());
    }

    /**
     * @inheritdoc
     */
    public function resolve($value, array $args, ResolveInfo $info): ?string
    {
        return $value ? Node::toGlobalId($this->typeName, $value['id']) : null;
    }
}

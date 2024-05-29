<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 12/5/15 12:12 AM
*/

namespace Youshido\GraphQL\Type\Union;


use Youshido\GraphQL\Config\Object\UnionTypeConfig;
use Youshido\GraphQL\Config\Traits\ConfigAwareTrait;
use Youshido\GraphQL\Exception\ConfigurationException;
use Youshido\GraphQL\Type\AbstractInterfaceTypeInterface;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\InputObject\AbstractInputObjectType;
use Youshido\GraphQL\Type\InterfaceType\AbstractInterfaceType;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\AbstractScalarType;
use Youshido\GraphQL\Type\Traits\AutoNameTrait;
use Youshido\GraphQL\Type\TypeMap;

abstract class AbstractUnionType extends AbstractType implements AbstractInterfaceTypeInterface
{

    use ConfigAwareTrait;
    use AutoNameTrait;

    protected bool $isFinal = false;

    /**
     * ObjectType constructor.
     * @param array $config
     * @throws ConfigurationException
     */
    public function __construct(array $config = [])
    {
        if (empty($config)) {
            $config['name'] = $this->getName();
            $config['types'] = $this->getTypes();
        }

        $this->config = new UnionTypeConfig($config, $this, $this->isFinal);
    }

    /**
     * @return AbstractObjectType[]|AbstractScalarType[]
     */
    abstract public function getTypes(): array;

    public function getKind(): string
    {
        return TypeMap::KIND_UNION;
    }

    public function getNamedType(): AbstractUnionType|static
    {
        return $this;
    }

    public function isValidValue(mixed $value): bool
    {
        return true;
    }

}

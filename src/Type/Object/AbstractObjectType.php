<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 12/2/15 8:57 PM
*/

namespace Youshido\GraphQL\Type\Object;


use InvalidArgumentException;
use Youshido\GraphQL\Config\Object\ObjectTypeConfig;
use Youshido\GraphQL\Exception\ConfigurationException;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\InputObject\AbstractInputObjectType;
use Youshido\GraphQL\Type\Enum\AbstractEnumType;
use Youshido\GraphQL\Type\InterfaceType\AbstractInterfaceType;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Scalar\AbstractScalarType;
use Youshido\GraphQL\Type\Traits\AutoNameTrait;
use Youshido\GraphQL\Type\Traits\FieldsArgumentsAwareObjectTrait;
use Youshido\GraphQL\Type\TypeMap;

/**
 * Class AbstractObjectType
 * @package Youshido\GraphQL\Type\Object
 */
abstract class AbstractObjectType extends AbstractType
{
    use AutoNameTrait;
    use FieldsArgumentsAwareObjectTrait;

    protected bool $isBuilt = false;

    public function getConfig(): ObjectTypeConfig
    {
        if (!$this->isBuilt) {
            $this->isBuilt = true;
            $this->build($this->config);
        }

        return $this->config;
    }

    /**
     * ObjectType constructor.
     * @param array $config
     * @throws ConfigurationException
     */
    public function __construct(array $config = [])
    {
        if (empty($config)) {
            $config['name'] = $this->getName();
            $config['interfaces'] = $this->getInterfaces();
        }

        $this->config = new ObjectTypeConfig($config, $this);
    }

    final public function serialize($value): mixed
    {
        throw new InvalidArgumentException('You can not serialize object value directly');
    }

    public function getKind(): string
    {
        return TypeMap::KIND_OBJECT;
    }

    public function getType(): mixed
    {
        return $this->getConfigValue('type', $this);
    }

    public function getNamedType(): mixed
    {
        return $this;
    }

    /**
     * @param ObjectTypeConfig $config
     */
    abstract public function build(ObjectTypeConfig $config);

    /**
     * @return AbstractInterfaceType[]
     */
    public function getInterfaces(): array
    {
        return $this->getConfigValue('interfaces', []);
    }

    public function isValidValue(mixed $value): bool
    {
        return is_array($value) || is_null($value) || is_object($value);
    }
}

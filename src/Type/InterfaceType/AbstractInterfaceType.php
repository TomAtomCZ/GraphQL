<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 12/5/15 12:12 AM
*/

namespace Youshido\GraphQL\Type\InterfaceType;


use Youshido\GraphQL\Config\Object\InterfaceTypeConfig;
use Youshido\GraphQL\Exception\ConfigurationException;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\Traits\AutoNameTrait;
use Youshido\GraphQL\Type\Traits\FieldsAwareObjectTrait;
use Youshido\GraphQL\Type\TypeInterface;
use Youshido\GraphQL\Type\TypeMap;

abstract class AbstractInterfaceType extends AbstractType
{
    use FieldsAwareObjectTrait;
    use AutoNameTrait;

    protected bool $isBuilt = false;

    public function getConfig(): InterfaceTypeConfig
    {
        if (!$this->isBuilt) {
            $this->isBuilt = true;
            $this->build($this->config);
        }

        return $this->config;
    }

    /**
     * ObjectType constructor.
     *
     * @throws ConfigurationException
     */
    public function __construct(array $config = [])
    {
        if ($config === []) {
            $config['name'] = $this->getName();
        }

        $this->config = new InterfaceTypeConfig($config, $this);
    }

    abstract public function resolveType($object);

    abstract public function build(InterfaceTypeConfig $config);

    public function getKind(): string
    {
        return TypeMap::KIND_INTERFACE;
    }

    public function getNamedType(): AbstractInterfaceType|static
    {
        return $this;
    }

    public function isValidValue(mixed $value): bool
    {
        return is_array($value) || is_null($value) || is_object($value);
    }

    /**
     * @return TypeInterface[] an array of types that implement this interface. Used mainly for introspection and
     *                         documentation generation.
     */
    public function getImplementations(): array
    {
        return [];
    }
}

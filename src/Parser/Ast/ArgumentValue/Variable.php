<?php
/**
 * Date: 23.11.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Parser\Ast\ArgumentValue;

use LogicException;
use Youshido\GraphQL\Parser\Ast\AbstractAst;
use Youshido\GraphQL\Parser\Ast\Interfaces\ValueInterface;
use Youshido\GraphQL\Parser\Location;

class Variable extends AbstractAst implements ValueInterface
{

    /** @var  string */
    private $name;

    /** @var  mixed */
    private $value;

    /** @var string */
    private $type;

    private bool $nullable = false;

    private bool $isArray = false;

    private bool $used = false;

    private bool $arrayElementNullable = true;

    private bool $hasDefaultValue = false;

    /** @var mixed */
    private $defaultValue;

    /**
     * @param string $name
     * @param string $type
     * @param bool $nullable
     * @param bool $isArray
     * @param bool $arrayElementNullable
     */
    public function __construct($name, $type, $nullable, $isArray, Location $location, $arrayElementNullable = true)
    {
        parent::__construct($location);

        $this->name = $name;
        $this->type = $type;
        $this->isArray = $isArray;
        $this->nullable = $nullable;
        $this->arrayElementNullable = $arrayElementNullable;
    }

    /**
     * @return mixed
     *
     * @throws LogicException
     */
    public function getValue()
    {
        if (null === $this->value) {
            if ($this->hasDefaultValue()) {
                return $this->defaultValue;
            }

            throw new LogicException('Value is not set for variable "' . $this->name . '"');
        }

        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value): void
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getTypeName()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setTypeName($type): void
    {
        $this->type = $type;
    }

    public function isArray(): bool
    {
        return $this->isArray;
    }

    public function setIsArray(bool $isArray): void
    {
        $this->isArray = $isArray;
    }

    public function isNullable(): bool
    {
        return $this->nullable;
    }

    public function setNullable(bool $nullable): void
    {
        $this->nullable = $nullable;
    }

    public function hasDefaultValue(): bool
    {
        return $this->hasDefaultValue;
    }

    /**
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * @param mixed $defaultValue
     */
    public function setDefaultValue($defaultValue): void
    {
        $this->hasDefaultValue = true;

        $this->defaultValue = $defaultValue;
    }

    public function isUsed(): bool
    {
        return $this->used;
    }

    /**
     * @return $this
     */
    public function setUsed(bool $used): static
    {
        $this->used = $used;

        return $this;
    }

    public function isArrayElementNullable(): bool
    {
        return $this->arrayElementNullable;
    }

    public function setArrayElementNullable(bool $arrayElementNullable): void
    {
        $this->arrayElementNullable = $arrayElementNullable;
    }
}

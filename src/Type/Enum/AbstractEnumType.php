<?php
/**
 * Date: 07.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Type\Enum;


use Youshido\GraphQL\Config\Object\EnumTypeConfig;
use Youshido\GraphQL\Config\Traits\ConfigAwareTrait;
use Youshido\GraphQL\Exception\ConfigurationException;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\Traits\AutoNameTrait;
use Youshido\GraphQL\Type\TypeMap;

abstract class AbstractEnumType extends AbstractType
{

    use AutoNameTrait;
    use ConfigAwareTrait;

    /**
     * ObjectType constructor.
     * @throws ConfigurationException
     */
    public function __construct(array $config = [])
    {
        if ($config === []) {
            $config['name'] = $this->getName();
            $config['values'] = $this->getValues();
        }

        $this->config = new EnumTypeConfig($config, $this);
    }

    /**
     * @return String predefined type kind
     */
    public function getKind(): string
    {
        return TypeMap::KIND_ENUM;
    }

    /**
     * @param $value mixed
     */
    public function isValidValue(mixed $value): bool
    {
        if (is_null($value)) {
            return true;
        }

        foreach ($this->getConfig()->get('values') as $item) {
            if ($value === $item['name'] || $value === $item['value']) {
                return true;
            }
        }

        return false;
    }

    public function getValidationError($value = null): ?string
    {
        $allowedValues = array_map(static function (array $value): string {
            return sprintf('%s (%s)', $value['name'], $value['value']);
        }, $this->getConfig()->get('values'));
        return sprintf('Value must be one of the allowed ones: %s', implode(', ', $allowedValues));
    }

    abstract public function getValues(): array;

    public function serialize($value): mixed
    {
        foreach ($this->getConfig()->get('values') as $valueItem) {
            if ($value === $valueItem['value']) {
                return $valueItem['name'];
            }
        }

        return null;
    }

    public function parseValue($value): mixed
    {
        foreach ($this->getConfig()->get('values') as $valueItem) {
            if ($value === $valueItem['name']) {
                return $valueItem['value'];
            }
        }

        return null;
    }

    public function parseInputValue($value)
    {
        foreach ($this->getConfig()->get('values') as $valueItem) {
            if ($value === $valueItem['value']) {
                return $valueItem['name'];
            }
        }

        return null;
    }

}

<?php
/*
 * This file is a part of GraphQL project.
 *
 * @author Alexandr Viniychuk <a@viniychuk.com>
 * created: 3:40 PM 4/29/16
 */

namespace Youshido\GraphQL\Type;


use Youshido\GraphQL\Config\Traits\ConfigAwareTrait;
use Youshido\GraphQL\Exception\ConfigurationException;

final class NonNullType extends AbstractType implements CompositeTypeInterface
{
    use ConfigAwareTrait;

    private mixed $_typeOf;

    /**
     * NonNullType constructor.
     *
     *
     * @throws ConfigurationException
     */
    public function __construct(string|AbstractType $fieldType)
    {
        if (!TypeService::isGraphQLType($fieldType)) {
            throw new ConfigurationException('NonNullType accepts only GraphpQL Types as argument');
        }

        if (TypeService::isScalarType($fieldType)) {
            $fieldType = TypeFactory::getScalarType($fieldType);
        }

        $this->_typeOf = $fieldType;
    }

    public function getName(): ?string
    {
        return null;
    }

    public function getKind(): string
    {
        return TypeMap::KIND_NON_NULL;
    }

    public function resolve($value)
    {
        return $value;
    }

    public function isValidValue(mixed $value): bool
    {
        if ($value === null) {
            return false;
        }

        return $this->getNullableType()->isValidValue($value);
    }

    public function isCompositeType(): bool
    {
        return true;
    }

    public function isInputType(): bool
    {
        return true;
    }

    public function getNamedType(): mixed
    {
        return $this->getTypeOf();
    }

    public function getNullableType(): mixed
    {
        return $this->getTypeOf();
    }

    public function getTypeOf(): mixed
    {
        return $this->_typeOf;
    }

    public function parseValue($value): mixed
    {
        return $this->getNullableType()->parseValue($value);
    }

    public function getValidationError($value = null): ?string
    {
        if ($value === null) {
            return 'Field must not be NULL';
        }

        return $this->getNullableType()->getValidationError($value);
    }
}
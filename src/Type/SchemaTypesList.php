<?php
/**
 * This file is a part of PhpStorm project.
 *
 * @author Alexandr Viniychuk <a@viniychuk.com>
 * created: 10/7/16 3:36 PM
 */

namespace Youshido\GraphQL\Type;


use Exception;

class SchemaTypesList
{

    private array $typesList = [];

    /**
     * @return $this
     * @throws
     */
    public function addTypes(array $types): static
    {
        foreach ($types as $type) {
            $this->addType($type);
        }

        return $this;
    }

    public function getTypes(): array
    {
        return $this->typesList;
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function addType(TypeInterface $type): static
    {
        $typeName = $this->getTypeName($type);
        if ($this->isTypeNameRegistered($typeName)) {
            return $this;
        }

        $this->typesList[$typeName] = $type;
        return $this;
    }

    public function isTypeNameRegistered($typeName): bool
    {
        return (isset($this->typesList[$typeName]));
    }

    /**
     * @throws Exception
     */
    private function getTypeName(TypeInterface $type): ?string
    {

        if ($type instanceof AbstractType) {
            return $type->getName();
        }

        throw new Exception('Invalid type passed to Schema');
    }

}
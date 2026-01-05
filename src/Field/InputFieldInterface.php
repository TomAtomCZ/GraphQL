<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 9/29/16 10:32 PM
*/

namespace Youshido\GraphQL\Field;


use Youshido\GraphQL\Type\AbstractType;

interface InputFieldInterface
{
    public function getType(): mixed;

    public function getName();

    public function addArguments($argumentsList);

    public function removeArgument($argumentName);

    public function addArgument($argument, $ArgumentInfo = null);

    /**
     * @return AbstractType[]
     */
    public function getArguments(): array;

    public function getArgument(string $argumentName);

    public function hasArgument(string $argumentName): bool;

    public function hasArguments(): bool;


}

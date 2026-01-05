<?php
/**
 * Date: 03.11.16
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Parser\Ast\Interfaces;


use Youshido\GraphQL\Parser\Ast\Argument;

interface FieldInterface extends LocatableInterface
{
    public function getName(): string;

    public function getAlias(): ?string;

    /**
     * @return Argument[]
     */
    public function getArguments(): array;

    public function getArgument(string $name);

    public function hasFields(): bool;

    public function getFields(): array;

}

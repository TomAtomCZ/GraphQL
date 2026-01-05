<?php
/**
 * Date: 23.11.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Parser\Ast;


use Youshido\GraphQL\Parser\Ast\Interfaces\FieldInterface;
use Youshido\GraphQL\Parser\Location;

class Field extends AbstractAst implements FieldInterface
{
    use AstArgumentsTrait;
    use AstDirectivesTrait;

    private ?string $name;

    private ?string $alias;

    /**
     * @param string $name
     * @param string $alias
     */
    public function __construct($name, $alias, array $arguments, array $directives, Location $location)
    {
        parent::__construct($location);

        $this->name = $name;
        $this->alias = $alias;
        $this->setArguments($arguments);
        $this->setDirectives($directives);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function setAlias(?string $alias): void
    {
        $this->alias = $alias;
    }

    public function hasFields(): bool
    {
        return false;
    }

    public function getFields(): array
    {
        return [];
    }

}

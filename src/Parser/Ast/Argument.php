<?php
/**
 * Date: 23.11.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Parser\Ast;

use Youshido\GraphQL\Parser\Ast\Interfaces\ValueInterface;
use Youshido\GraphQL\Parser\Location;

class Argument extends AbstractAst implements ValueInterface
{
    private string $name;

    /**
     * TODO - Was ValueInterface - is there any reason for that?
     */
    private mixed $value;

    /**
     * @param string $name
     */
    public function __construct($name, $value, Location $location)
    {
        parent::__construct($location);

        $this->name = $name;
        $this->value = $value;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function setValue(mixed $value): void
    {
        $this->value = $value;
    }
}
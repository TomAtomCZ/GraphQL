<?php
/**
 * Date: 23.11.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Parser\Ast;

use Youshido\GraphQL\Parser\Ast\Interfaces\FieldInterface;
use Youshido\GraphQL\Parser\Ast\Interfaces\FragmentInterface;
use Youshido\GraphQL\Parser\Location;

class Query extends AbstractAst implements FieldInterface
{

    use AstArgumentsTrait;
    use AstDirectivesTrait;

    /** @var string */
    protected $name;

    /** @var string */
    protected $alias;

    /** @var Field[]|Query[] */
    protected $fields = [];

    /**
     * Query constructor.
     *
     * @param string $name
     * @param string $alias
     */
    public function __construct($name, $alias, array $arguments, array $fields, array $directives, Location $location)
    {
        parent::__construct($location);

        $this->name = $name;
        $this->alias = $alias;
        $this->setFields($fields);
        $this->setArguments($arguments);
        $this->setDirectives($directives);
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Field[]|Query[]|FragmentInterface[]
     */
    public function getFields(): array
    {
        return array_values($this->fields);
    }

    public function hasFields(): bool
    {
        return (bool)count($this->fields);
    }

    /**
     * @param Field[]|Query[] $fields
     */
    public function setFields($fields): void
    {
        /**
         * we cannot store fields by name because of TypedFragments
         */
        $this->fields = $fields;
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function hasField($name, $deep = false): bool
    {
        foreach ($this->getFields() as $field) {
            if ($field->getName() == $name) {
                return true;
            }

            if ($deep && $field instanceof Query && $field->hasField($name)) {
                return true;
            }
        }

        return false;
    }

}

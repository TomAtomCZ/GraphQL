<?php
/**
 * Date: 23.11.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Parser\Ast;


use Youshido\GraphQL\Parser\Location;

class Fragment extends AbstractAst
{

    use AstDirectivesTrait;

    protected $name;

    protected $model;

    /** @var Field[]|Query[] */
    protected array $fields;

    private bool $used = false;

    /**
     * @param string $name
     * @param string $model
     * @param Field[]|Query[] $fields
     */
    public function __construct($name, $model, array $directives, array $fields, Location $location)
    {
        parent::__construct($location);

        $this->name = $name;
        $this->model = $model;
        $this->fields = $fields;
        $this->setDirectives($directives);
    }

    public function isUsed(): bool
    {
        return $this->used;
    }

    public function setUsed(bool $used): void
    {
        $this->used = $used;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param mixed $model
     */
    public function setModel($model): void
    {
        $this->model = $model;
    }

    /**
     * @return Field[]|Query[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @param Field[]|Query[] $fields
     */
    public function setFields(array $fields): void
    {
        $this->fields = $fields;
    }
}
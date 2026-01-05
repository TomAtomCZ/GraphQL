<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 5/19/16 8:58 AM
*/

namespace Youshido\GraphQL\Execution;

use Youshido\GraphQL\Execution\Context\ExecutionContextInterface;
use Youshido\GraphQL\Field\FieldInterface;
use Youshido\GraphQL\Parser\Ast\Field;
use Youshido\GraphQL\Parser\Ast\Query;
use Youshido\GraphQL\Type\AbstractType;

class ResolveInfo
{
    protected FieldInterface $field;

    /** @var Field[] */
    protected array $fieldASTList;

    protected ExecutionContextInterface $executionContext;

    /**
     * This property is to be used for DI in various scenario
     * Added to original class to keep backward compatibility
     * because of the way AbstractField::resolve has been declared
     *
     * @var mixed $container
     */
    protected $container;

    public function __construct(FieldInterface $field, array $fieldASTList, ExecutionContextInterface $executionContext)
    {
        $this->field = $field;
        $this->fieldASTList = $fieldASTList;
        $this->executionContext = $executionContext;
    }

    public function getExecutionContext(): ExecutionContextInterface
    {
        return $this->executionContext;
    }

    public function getField(): FieldInterface
    {
        return $this->field;
    }

    /**
     * @param string $fieldName
     *
     * @return null|Query|Field
     */
    public function getFieldAST($fieldName)
    {
        $field = null;
        foreach ($this->getFieldASTList() as $fieldAST) {
            if ($fieldAST->getName() === $fieldName) {
                $field = $fieldAST;
                break;
            }
        }

        return $field;
    }

    /**
     * @return Field[]
     */
    public function getFieldASTList(): array
    {
        return $this->fieldASTList;
    }

    /**
     * @return AbstractType
     */
    public function getReturnType(): mixed
    {
        return $this->field->getType();
    }

    public function getContainer()
    {
        return $this->executionContext->getContainer();
    }


}

<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 2/5/17 11:31 AM
*/

namespace Youshido\GraphQL\Parser\Ast;


trait AstArgumentsTrait
{

    /** @var Argument[] */
    protected array $arguments;

    private $argumentsCache;


    public function hasArguments(): bool
    {
        return (bool)count($this->arguments);
    }

    public function hasArgument($name): bool
    {
        return array_key_exists($name, $this->arguments);
    }

    /**
     * @return Argument[]
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function getArgument(string $name): ?Argument
    {
        $argument = null;
        if (isset($this->arguments[$name])) {
            $argument = $this->arguments[$name];
        }

        return $argument;
    }

    public function getArgumentValue($name)
    {
        $argument = $this->getArgument($name);

        return $argument?->getValue()->getValue();
    }

    /**
     * @param $arguments Argument[]
     */
    public function setArguments(array $arguments): void
    {
        $this->arguments = [];
        $this->argumentsCache = null;

        foreach ($arguments as $argument) {
            $this->addArgument($argument);
        }
    }

    public function addArgument(Argument $argument): void
    {
        $this->arguments[$argument->getName()] = $argument;
    }

    public function getKeyValueArguments(): array
    {
        if ($this->argumentsCache !== null) {
            return $this->argumentsCache;
        }

        $this->argumentsCache = [];

        foreach ($this->getArguments() as $argument) {
            $this->argumentsCache[$argument->getName()] = $argument->getValue()->getValue();
        }

        return $this->argumentsCache;
    }
}
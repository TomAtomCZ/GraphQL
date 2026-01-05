<?php
/**
 * Date: 23.11.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Execution;

use Youshido\GraphQL\Exception\Parser\InvalidRequestException;
use Youshido\GraphQL\Parser\Ast\ArgumentValue\Variable;
use Youshido\GraphQL\Parser\Ast\ArgumentValue\VariableReference;
use Youshido\GraphQL\Parser\Ast\Fragment;
use Youshido\GraphQL\Parser\Ast\FragmentReference;
use Youshido\GraphQL\Parser\Ast\Mutation;
use Youshido\GraphQL\Parser\Ast\Query;

class Request
{

    /** @var  Query[] */
    private array $queries = [];

    /** @var Fragment[] */
    private array $fragments = [];

    /** @var Mutation[] */
    private array $mutations = [];

    /** @var array */
    private $variables = [];

    /** @var VariableReference[] */
    private array $variableReferences = [];

    private array $queryVariables = [];

    private array $fragmentReferences = [];

    /**
     * @throws InvalidRequestException
     */
    public function __construct(array $data = [], array $variables = [])
    {
        if (array_key_exists('queries', $data)) {
            $this->addQueries($data['queries']);
        }

        if (array_key_exists('mutations', $data)) {
            $this->addMutations($data['mutations']);
        }

        if (array_key_exists('fragments', $data)) {
            $this->addFragments($data['fragments']);
        }

        if (array_key_exists('fragmentReferences', $data)) {
            $this->addFragmentReferences($data['fragmentReferences']);
        }

        if (array_key_exists('variables', $data)) {
            $this->addQueryVariables($data['variables']);
        }

        if (array_key_exists('variableReferences', $data)) {
            foreach ($data['variableReferences'] as $ref) {
                if (!array_key_exists($ref->getName(), $variables)) {
                    /** @var Variable $variable */
                    $variable = $ref->getVariable();
                    if ($variable->hasDefaultValue()) {
                        $variables[$variable->getName()] = $variable->getDefaultValue()->getValue();
                        continue;
                    }

                    throw new InvalidRequestException(sprintf("Variable %s hasn't been submitted", $ref->getName()), $ref->getLocation());
                }
            }

            $this->addVariableReferences($data['variableReferences']);
        }

        $this->setVariables($variables);
    }

    public function addQueries($queries): void
    {
        foreach ($queries as $query) {
            $this->queries[] = $query;
        }
    }

    public function addMutations($mutations): void
    {
        foreach ($mutations as $mutation) {
            $this->mutations[] = $mutation;
        }
    }

    public function addQueryVariables($queryVariables): void
    {
        foreach ($queryVariables as $queryVariable) {
            $this->queryVariables[] = $queryVariable;
        }
    }

    public function addVariableReferences($variableReferences): void
    {
        foreach ($variableReferences as $variableReference) {
            $this->variableReferences[] = $variableReference;
        }
    }

    public function addFragmentReferences($fragmentReferences): void
    {
        foreach ($fragmentReferences as $fragmentReference) {
            $this->fragmentReferences[] = $fragmentReference;
        }
    }

    public function addFragments($fragments): void
    {
        foreach ($fragments as $fragment) {
            $this->addFragment($fragment);
        }
    }

    /**
     * @return Query[]
     */
    public function getAllOperations(): array
    {
        return array_merge($this->mutations, $this->queries);
    }

    /**
     * @return Query[]
     */
    public function getQueries(): array
    {
        return $this->queries;
    }

    /**
     * @return Fragment[]
     */
    public function getFragments(): array
    {
        return $this->fragments;
    }

    public function addFragment(Fragment $fragment): void
    {
        $this->fragments[] = $fragment;
    }

    /**
     * @param $name
     */
    public function getFragment($name): ?Fragment
    {
        foreach ($this->fragments as $fragment) {
            if ($fragment->getName() == $name) {
                return $fragment;
            }
        }

        return null;
    }

    /**
     * @return Mutation[]
     */
    public function getMutations(): array
    {
        return $this->mutations;
    }

    public function hasQueries(): bool
    {
        return (bool)count($this->queries);
    }

    public function hasMutations(): bool
    {
        return (bool)count($this->mutations);
    }

    public function hasFragments(): bool
    {
        return (bool)count($this->fragments);
    }

    /**
     * @return array
     */
    public function getVariables()
    {
        return $this->variables;
    }

    /**
     * @param array|string $variables
     *
     * @return $this
     */
    public function setVariables($variables): static
    {
        if (!is_array($variables)) {
            $variables = json_decode($variables, true);
        }

        $this->variables = $variables;
        foreach ($this->variableReferences as $reference) {
            /** invalid request with no variable */
            if (!$reference->getVariable()) {
                continue;
            }

            $variableName = $reference->getVariable()->getName();

            /** no variable was set at the time */
            if (!array_key_exists($variableName, $variables)) {
                continue;
            }

            $reference->getVariable()->setValue($variables[$variableName]);
            $reference->setValue($variables[$variableName]);
        }

        return $this;
    }

    public function getVariable($name)
    {
        return $this->hasVariable($name) ? $this->variables[$name] : null;
    }

    public function hasVariable($name): bool
    {
        return array_key_exists($name, $this->variables);
    }

    /**
     * @return array|Variable[]
     */
    public function getQueryVariables(): array
    {
        return $this->queryVariables;
    }

    public function setQueryVariables(array $queryVariables): void
    {
        $this->queryVariables = $queryVariables;
    }

    /**
     * @return array|FragmentReference[]
     */
    public function getFragmentReferences(): array
    {
        return $this->fragmentReferences;
    }

    public function setFragmentReferences(array $fragmentReferences): void
    {
        $this->fragmentReferences = $fragmentReferences;
    }

    /**
     * @return array|VariableReference[]
     */
    public function getVariableReferences(): array
    {
        return $this->variableReferences;
    }
}

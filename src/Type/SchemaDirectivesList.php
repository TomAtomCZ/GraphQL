<?php
/**
 * Date: 3/24/17
 *
 * @author Volodymyr Rashchepkin <rashepkin@gmail.com>
 */

namespace Youshido\GraphQL\Type;

use Youshido\GraphQL\Directive\DirectiveInterface;

class SchemaDirectivesList
{
    private array $directivesList = [];

    /**
     *
     * @return $this
     * @throws
     */
    public function addDirectives(array $directives): static
    {
        foreach ($directives as $directive) {
            $this->addDirective($directive);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function addDirective(DirectiveInterface $directive): static
    {
        $directiveName = $this->getDirectiveName($directive);
        if ($this->isDirectiveNameRegistered($directiveName)) {
            return $this;
        }

        $this->directivesList[$directiveName] = $directive;

        return $this;
    }

    private function getDirectiveName(DirectiveInterface $directive): string
    {
        return $directive->getName();
    }

    public function isDirectiveNameRegistered($directiveName): bool
    {
        return (isset($this->directivesList[$directiveName]));
    }

    public function getDirectives(): array
    {
        return $this->directivesList;
    }

}
<?php
/**
 * Date: 3/24/17
 *
 * @author Volodymyr Rashchepkin <rashepkin@gmail.com>
 */

namespace Youshido\GraphQL\Type;

use Exception;
use Youshido\GraphQL\Directive\DirectiveInterface;

class SchemaDirectivesList
{
    private array $directivesList = [];

    /**
     * @param array $directives
     *
     * @return $this
     * @throws
     */
    public function addDirectives($directives): static
    {
        if (!is_array($directives)) {
            throw new Exception('addDirectives accept only array of directives');
        }

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
        if ($this->isDirectiveNameRegistered($directiveName)) return $this;

        $this->directivesList[$directiveName] = $directive;

        return $this;
    }

    private function getDirectiveName(DirectiveInterface $directive)
    {
        if (is_string($directive)) return $directive;

        return $directive->getName();

        //throw new Exception('Invalid directive passed to Schema');
    }

    public function isDirectiveNameRegistered($directiveName): bool
    {
        return (isset($this->directivesList[$directiveName]));
    }

    public function getDirectives()
    {
        return $this->directivesList;
    }

}
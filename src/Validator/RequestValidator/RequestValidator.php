<?php
/**
 * Date: 10/24/16
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Validator\RequestValidator;


use Youshido\GraphQL\Exception\Parser\InvalidRequestException;
use Youshido\GraphQL\Execution\Request;
use Youshido\GraphQL\Parser\Ast\Fragment;

class RequestValidator implements RequestValidatorInterface
{

    /**
     * @throws InvalidRequestException
     */
    public function validate(Request $request): void
    {
        $this->assertFragmentReferencesValid($request);
        $this->assetFragmentsUsed($request);
        $this->assertAllVariablesExists($request);
        $this->assertAllVariablesUsed($request);
    }

    /**
     * @throws InvalidRequestException
     */
    private function assetFragmentsUsed(Request $request): void
    {
        foreach ($request->getFragmentReferences() as $fragmentReference) {
            $request->getFragment($fragmentReference->getName())->setUsed(true);
        }

        foreach ($request->getFragments() as $fragment) {
            if (!$fragment->isUsed()) {
                throw new InvalidRequestException(sprintf('Fragment "%s" not used', $fragment->getName()), $fragment->getLocation());
            }
        }
    }

    /**
     * @throws InvalidRequestException
     */
    private function assertFragmentReferencesValid(Request $request): void
    {
        foreach ($request->getFragmentReferences() as $fragmentReference) {
            if (!$request->getFragment($fragmentReference->getName()) instanceof Fragment) {
                throw new InvalidRequestException(sprintf('Fragment "%s" not defined in query', $fragmentReference->getName()), $fragmentReference->getLocation());
            }
        }
    }

    /**
     * @throws InvalidRequestException
     */
    private function assertAllVariablesExists(Request $request): void
    {
        foreach ($request->getVariableReferences() as $variableReference) {
            if (!$variableReference->getVariable()) {
                throw new InvalidRequestException(sprintf('Variable "%s" not exists', $variableReference->getName()), $variableReference->getLocation());
            }
        }
    }

    /**
     * @throws InvalidRequestException
     */
    private function assertAllVariablesUsed(Request $request): void
    {
        foreach ($request->getQueryVariables() as $queryVariable) {
            if (!$queryVariable->isUsed()) {
                throw new InvalidRequestException(sprintf('Variable "%s" not used', $queryVariable->getName()), $queryVariable->getLocation());
            }
        }
    }
}

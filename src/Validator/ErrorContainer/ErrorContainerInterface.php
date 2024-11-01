<?php
/**
 * Date: 27.11.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Validator\ErrorContainer;


use Exception;

interface ErrorContainerInterface
{

    public function addError(Exception $exception);

    public function mergeErrors(ErrorContainerInterface $errorContainer);

    public function hasErrors();

    public function hasError(Exception $exception);

    public function getErrors();

    public function getErrorsArray();

    public function clearErrors();

}
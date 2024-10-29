<?php
/**
 * Date: 01.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Validator\ErrorContainer;

use Exception;
use Youshido\GraphQL\Exception\Interfaces\ExtendedExceptionInterface;
use Youshido\GraphQL\Exception\Interfaces\LocationableExceptionInterface;

trait ErrorContainerTrait
{
    /** @var Exception[] */
    protected array $errors = [];

    public function addError(Exception $exception): static
    {
        if (!$this->hasError($exception)) {
            $this->errors[] = $exception;
        }

        return $this;
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    public function hasError(Exception $exception): bool
    {
        return $this->hasErrors() && in_array($exception, $this->errors);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function mergeErrors(ErrorContainerInterface $errorContainer): static
    {
        if ($errorContainer->hasErrors()) {
            foreach ($errorContainer->getErrors() as $error) {
                $this->addError($error);
            }
        }

        return $this;
    }

    /**
     * @param bool $inGraphQLStyle
     * @return array
     */
    public function getErrorsArray(bool $inGraphQLStyle = true): array
    {
        $errors = [];

        foreach ($this->errors as $error) {
            if ($inGraphQLStyle) {
                // All errors have a message
                $graphQLError = [
                    'message' => $error->getMessage(),
                ];

                // Add code if it's non-zero
                if ($error->getCode()) {
                    $graphQLError['code'] = $error->getCode();
                }

                // Add location data when available
                if ($error instanceof LocationableExceptionInterface && $error->getLocation()) {
                    $graphQLError['locations'] = [$error->getLocation()->toArray()];
                }

                // Add extensions when available
                if ($error instanceof ExtendedExceptionInterface && $error->getExtensions()) {
                    $graphQLError['extensions'] = $error->getExtensions();
                }

                $errors[] = $graphQLError;
            } else {
                $errors[] = $error->getMessage();
            }
        }

        return $errors;
    }

    public function clearErrors(): static
    {
        $this->errors = [];

        return $this;
    }
}

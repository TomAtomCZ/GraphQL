<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/27/15 2:31 AM
*/

namespace Youshido\GraphQL\Config;


use Exception;
use Youshido\GraphQL\Exception\ConfigurationException;
use Youshido\GraphQL\Validator\ConfigValidator\ConfigValidator;

/**
 * Class Config
 *
 * @package Youshido\GraphQL\Config
 */
abstract class AbstractConfig
{
    protected array $data;

    protected mixed $contextObject;

    protected bool $finalClass = false;

    protected bool $extraFieldsAllowed = false;

    /**
     * TypeConfig constructor.
     *
     * @param mixed|null $contextObject
     *
     * @throws ConfigurationException
     */
    public function __construct(array $configData, mixed $contextObject = null, bool $finalClass = false)
    {
        if ($configData === []) {
            throw new ConfigurationException('Config for Type should be an array');
        }

        $this->contextObject = $contextObject;
        $this->data = $configData;
        $this->finalClass = $finalClass;

        $this->build();
    }

    /**
     * @throws ConfigurationException
     */
    public function validate(): void
    {
        $validator = ConfigValidator::getInstance();

        if (!$validator->validate($this->data, $this->getContextRules(), $this->extraFieldsAllowed)) {
            throw new ConfigurationException('Config is not valid for ' . ($this->contextObject ? get_class($this->contextObject) : null) . "\n" . implode("\n", $validator->getErrorsArray(false)));
        }
    }

    public function getContextRules(): array
    {
        $rules = $this->getRules();
        if ($this->finalClass) {
            foreach ($rules as $name => $info) {
                if (!empty($info['final'])) {
                    $rules[$name]['required'] = true;
                }
            }
        }

        return $rules;
    }

    abstract public function getRules();

    public function getName()
    {
        return $this->get('name');
    }

    public function getType()
    {
        return $this->get('type');
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getContextObject()
    {
        return $this->contextObject;
    }

    public function isFinalClass(): bool
    {
        return $this->finalClass;
    }

    public function isExtraFieldsAllowed(): bool
    {
        return $this->extraFieldsAllowed;
    }

    public function getResolveFunction(): ?callable
    {
        return $this->get('resolve');
    }

    protected function build()
    {
    }

    /**
     * @param      $key
     *
     * @return mixed|null|callable
     */
    public function get($key, $defaultValue = null): mixed
    {
        return $this->has($key) ? $this->data[$key] : $defaultValue;
    }

    public function set($key, $value): static
    {
        $this->data[$key] = $value;

        return $this;
    }

    public function has($key): bool
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * @throws Exception
     */
    public function __call(string $method, array $arguments)
    {
        if (str_starts_with($method, 'get')) {
            $propertyName = lcfirst(substr($method, 3));
        } elseif (str_starts_with($method, 'set')) {
            $propertyName = lcfirst(substr($method, 3));
            $this->set($propertyName, $arguments[0]);

            return $this;
        } elseif (str_starts_with($method, 'is')) {
            $propertyName = lcfirst(substr($method, 2));
        } else {
            throw new Exception('Call to undefined method ' . $method);
        }

        return $this->get($propertyName);
    }
}
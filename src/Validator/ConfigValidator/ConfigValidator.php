<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/28/15 2:25 AM
*/

namespace Youshido\GraphQL\Validator\ConfigValidator;


use Youshido\GraphQL\Config\AbstractConfig;
use Youshido\GraphQL\Exception\ConfigurationException;
use Youshido\GraphQL\Exception\ValidationException;
use Youshido\GraphQL\Validator\ConfigValidator\Rules\TypeValidationRule;
use Youshido\GraphQL\Validator\ConfigValidator\Rules\ValidationRuleInterface;
use Youshido\GraphQL\Validator\ErrorContainer\ErrorContainerTrait;

class ConfigValidator implements ConfigValidatorInterface
{

    use ErrorContainerTrait;

    protected $rules = [];

    protected $extraFieldsAllowed = false;

    /** @var ValidationRuleInterface[] */
    protected $validationRules = [];

    /** @var  ConfigValidator */
    protected static $instance;

    private function __construct()
    {
        $this->initializeRules();
    }

    public static function getInstance(): ConfigValidator
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }

        self::$instance->clearErrors();

        return self::$instance;
    }

    /**
     * @throws ConfigurationException
     */
    public function assertValidConfig(AbstractConfig $config): void
    {
        if (!$this->isValidConfig($config)) {
            throw new ConfigurationException('Config is not valid for ' . ($config->getContextObject() ? get_class($config->getContextObject()) : null) . "\n" . implode("\n", $this->getErrorsArray(false)));
        }
    }

    public function isValidConfig(AbstractConfig $config): bool
    {
        return $this->validate($config->getData(), $this->getConfigFinalRules($config), $config->isExtraFieldsAllowed());
    }

    protected function getConfigFinalRules(AbstractConfig $config): array
    {
        $rules = $config->getRules();
        if ($config->isFinalClass()) {
            foreach ($rules as $name => $info) {
                if (!empty($info['final'])) {
                    $rules[$name]['required'] = true;
                }
            }
        }

        return $rules;
    }


    public function validate($data, $rules = [], $extraFieldsAllowed = null): bool
    {
        if ($extraFieldsAllowed !== null) {
            $this->setExtraFieldsAllowed($extraFieldsAllowed);
        }

        $processedFields = [];
        foreach ($rules as $fieldName => $fieldRules) {
            $processedFields[] = $fieldName;

            /** Custom validation of 'required' property */
            if (array_key_exists('required', $fieldRules)) {
                unset($fieldRules['required']);

                if (!array_key_exists($fieldName, $data)) {
                    $this->addError(new ValidationException(sprintf('Field "%s" is required', $fieldName)));

                    continue;
                }
            } elseif (!array_key_exists($fieldName, $data)) {
                continue;
            }

            if (!empty($fieldRules['final'])) {
                unset($fieldRules['final']);
            }

            /** Validation of all other rules*/
            foreach ($fieldRules as $ruleName => $ruleInfo) {
                if (!array_key_exists($ruleName, $this->validationRules)) {
                    $this->addError(new ValidationException(sprintf('Field "%s" has invalid rule "%s"', $fieldName, $ruleInfo)));

                    continue;
                }

                if (!$this->validationRules[$ruleName]->validate($data[$fieldName], $ruleInfo)) {
                    $this->addError(new ValidationException(sprintf('Field "%s" expected to be "%s" but got "%s"', $fieldName, $ruleName, gettype($data[$fieldName]))));
                }
            }
        }

        if (!$this->isExtraFieldsAllowed()) {
            foreach (array_keys($data) as $fieldName) {
                if (!in_array($fieldName, $processedFields)) {
                    $this->addError(new ValidationException(sprintf('Field "%s" is not expected', $fieldName)));
                }
            }
        }

        return $this->isValid();
    }

    protected function initializeRules(): void
    {
        $this->validationRules['type'] = new TypeValidationRule($this);
    }

    public function addRule($name, ValidationRuleInterface $rule): void
    {
        $this->validationRules[$name] = $rule;
    }

    public function isValid(): bool
    {
        return !$this->hasErrors();
    }


    public function isExtraFieldsAllowed(): bool
    {
        return $this->extraFieldsAllowed;
    }

    /**
     * @param boolean $extraFieldsAllowed
     */
    public function setExtraFieldsAllowed($extraFieldsAllowed): static
    {
        $this->extraFieldsAllowed = $extraFieldsAllowed;

        return $this;
    }

}

<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/28/15 6:07 PM
*/

namespace Youshido\GraphQL\Validator\ConfigValidator\Rules;


use Youshido\GraphQL\Exception\ConfigurationException;
use Youshido\GraphQL\Field\FieldInterface;
use Youshido\GraphQL\Field\InputFieldInterface;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\TypeFactory;
use Youshido\GraphQL\Type\TypeService;
use Youshido\GraphQL\Validator\ConfigValidator\ConfigValidator;

class TypeValidationRule implements ValidationRuleInterface
{
    private readonly ConfigValidator $configValidator;

    public function __construct(ConfigValidator $validator)
    {
        $this->configValidator = $validator;
    }

    /**
     * @throws ConfigurationException
     */
    public function validate($data, $ruleInfo): bool
    {
        if (!is_string($ruleInfo)) {
            return false;
        }

        return match ($ruleInfo) {
            TypeService::TYPE_ANY => true,
            TypeService::TYPE_ANY_OBJECT => is_object($data),
            TypeService::TYPE_CALLABLE => is_callable($data),
            TypeService::TYPE_BOOLEAN => is_bool($data),
            TypeService::TYPE_ARRAY => is_array($data),
            TypeService::TYPE_STRING => TypeFactory::getScalarType($ruleInfo)->isValidValue($data),
            TypeService::TYPE_GRAPHQL_TYPE => TypeService::isGraphQLType($data),
            TypeService::TYPE_OBJECT_TYPE => TypeService::isObjectType($data),
            TypeService::TYPE_ARRAY_OF_OBJECT_TYPES => $this->isArrayOfObjectTypes($data),
            TypeService::TYPE_ARRAY_OF_FIELDS_CONFIG => $this->isArrayOfFields($data),
            TypeService::TYPE_OBJECT_INPUT_TYPE => TypeService::isInputObjectType($data),
            TypeService::TYPE_ENUM_VALUES => $this->isEnumValues($data),
            TypeService::TYPE_ARRAY_OF_INPUT_FIELDS => $this->isArrayOfInputFields($data),
            TypeService::TYPE_ANY_INPUT => TypeService::isInputType($data),
            TypeService::TYPE_ARRAY_OF_INTERFACES => $this->isArrayOfInterfaces($data),
            default => false,
        };
    }

    private function isArrayOfObjectTypes($data): bool
    {
        if (!is_array($data) || $data === []) {
            return false;
        }

        foreach ($data as $item) {
            if (!TypeService::isObjectType($item)) {
                return false;
            }
        }

        return true;
    }

    private function isEnumValues($data): bool
    {
        if (!is_array($data) || $data === []) {
            return false;
        }

        foreach ($data as $item) {
            if (!is_array($item) || !array_key_exists('name', $item) || !is_string($item['name']) || !preg_match('/^[_a-zA-Z][_a-zA-Z0-9]*$/', $item['name'])) {
                return false;
            }

            if (!array_key_exists('value', $item)) {
                return false;
            }
        }

        return true;
    }

    private function isArrayOfInterfaces($data): bool
    {
        if (!is_array($data)) {
            return false;
        }

        foreach ($data as $item) {
            if (!TypeService::isInterface($item)) {
                return false;
            }
        }

        return true;
    }

    private function isArrayOfFields($data): bool
    {
        if (!is_array($data) || $data === []) {
            return false;
        }

        foreach ($data as $name => $item) {
            if (!$this->isField($item, $name)) {
                return false;
            }
        }

        return true;
    }

    private function isField($data, $name = null): bool
    {
        if (is_object($data)) {
            if (($data instanceof FieldInterface) || ($data instanceof AbstractType)) {
                return !$data->getConfig() || ($data->getConfig() && $this->configValidator->isValidConfig($data->getConfig()));
            } else {
                return false;
            }
        }

        if (!is_array($data)) {
            $data = [
                'type' => $data,
                'name' => $name,
            ];
        } elseif (empty($data['name'])) {
            $data['name'] = $name;
        }

        $this->configValidator->validate($data, $this->getFieldConfigRules());

        return $this->configValidator->isValid();
    }

    private function isArrayOfInputFields($data): bool
    {
        if (!is_array($data)) {
            return false;
        }

        foreach ($data as $item) {
            if (!$this->isInputField($item)) {
                return false;
            }
        }

        return true;
    }

    private function isInputField(array $data): bool
    {
        if (is_object($data)) {
            if ($data instanceof InputFieldInterface) {
                return true;
            } else {
                return TypeService::isInputType($data);
            }
        } else {
            if (!isset($data['type'])) {
                return false;
            }

            return TypeService::isInputType($data['type']);
        }
    }

    /**
     * Exists for the performance
     */
    private function getFieldConfigRules(): array
    {
        return [
            'name' => ['type' => TypeService::TYPE_STRING, 'required' => true],
            'type' => ['type' => TypeService::TYPE_ANY, 'required' => true],
            'args' => ['type' => TypeService::TYPE_ARRAY],
            'description' => ['type' => TypeService::TYPE_STRING],
            'resolve' => ['type' => TypeService::TYPE_CALLABLE],
            'isDeprecated' => ['type' => TypeService::TYPE_BOOLEAN],
            'deprecationReason' => ['type' => TypeService::TYPE_STRING],
            'cost' => ['type' => TypeService::TYPE_ANY]
        ];
    }
}
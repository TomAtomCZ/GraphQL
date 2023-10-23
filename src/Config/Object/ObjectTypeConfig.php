<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/27/15 2:32 AM
*/

namespace Youshido\GraphQL\Config\Object;

use Youshido\GraphQL\Config\AbstractConfig;
use Youshido\GraphQL\Config\Traits\FieldsAwareConfigTrait;
use Youshido\GraphQL\Config\TypeConfigInterface;
use Youshido\GraphQL\Exception\ConfigurationException;
use Youshido\GraphQL\Type\InterfaceType\AbstractInterfaceType;
use Youshido\GraphQL\Type\TypeService;

/**
 * Class ObjectTypeConfig
 * @package Youshido\GraphQL\Config\Object
 * @method setDescription(string $description)
 * @method string getDescription()
 */
class ObjectTypeConfig extends AbstractConfig implements TypeConfigInterface
{

    use FieldsAwareConfigTrait;

    public function getRules(): array
    {
        return [
            'name' => ['type' => TypeService::TYPE_STRING, 'required' => true],
            'description' => ['type' => TypeService::TYPE_STRING],
            'fields' => ['type' => TypeService::TYPE_ARRAY_OF_FIELDS_CONFIG, 'final' => true],
            'interfaces' => ['type' => TypeService::TYPE_ARRAY_OF_INTERFACES]
        ];
    }

    /**
     * @throws ConfigurationException
     */
    protected function build(): void
    {
        $this->buildFields();
    }

    /**
     * @return AbstractInterfaceType[]
     */
    public function getInterfaces(): array
    {
        return $this->get('interfaces', []);
    }
}
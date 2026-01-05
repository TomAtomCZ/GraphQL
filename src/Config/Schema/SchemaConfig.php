<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/28/15 3:53 PM
*/

namespace Youshido\GraphQL\Config\Schema;


use Youshido\GraphQL\Config\AbstractConfig;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Object\ObjectType;
use Youshido\GraphQL\Type\SchemaDirectivesList;
use Youshido\GraphQL\Type\SchemaTypesList;
use Youshido\GraphQL\Type\TypeService;

class SchemaConfig extends AbstractConfig
{

    private readonly SchemaTypesList $typesList;

    /**
     * @var SchemaDirectivesList;
     */
    private readonly SchemaDirectivesList $directiveList;

    public function __construct(array $configData, mixed $contextObject = null, bool $finalClass = false)
    {
        $this->typesList = new SchemaTypesList();
        $this->directiveList = new SchemaDirectivesList();
        parent::__construct($configData, $contextObject, $finalClass);
    }


    public function getRules(): array
    {
        return [
            'query' => ['type' => TypeService::TYPE_OBJECT_TYPE, 'required' => true],
            'mutation' => ['type' => TypeService::TYPE_OBJECT_TYPE],
            'types' => ['type' => TypeService::TYPE_ARRAY],
            'directives' => ['type' => TypeService::TYPE_ARRAY],
            'name' => ['type' => TypeService::TYPE_STRING],
        ];
    }

    protected function build()
    {
        parent::build();
        if (!empty($this->data['types'])) {
            $this->typesList->addTypes($this->data['types']);
        }

        if (!empty($this->data['directives'])) {
            $this->directiveList->addDirectives($this->data['directives']);
        }
    }


    /**
     * @return AbstractObjectType
     */
    public function getQuery()
    {
        return $this->data['query'];
    }

    /**
     * @param $query AbstractObjectType
     */
    public function setQuery($query): static
    {
        $this->data['query'] = $query;

        return $this;
    }

    /**
     * @return ObjectType
     */
    public function getMutation(): mixed
    {
        return $this->get('mutation');
    }

    /**
     * @param $query AbstractObjectType
     */
    public function setMutation($query): static
    {
        $this->data['mutation'] = $query;

        return $this;
    }

    public function getName(): mixed
    {
        return $this->get('name', 'RootSchema');
    }

    public function getTypesList(): SchemaTypesList
    {
        return $this->typesList;
    }

    public function getDirectiveList(): SchemaDirectivesList
    {
        return $this->directiveList;
    }

}

<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 5/10/16 11:32 PM
*/

namespace Youshido\GraphQL\Relay;


use Youshido\GraphQL\Config\Object\InterfaceTypeConfig;
use Youshido\GraphQL\Exception\ConfigurationException;
use Youshido\GraphQL\Relay\Fetcher\FetcherInterface;
use Youshido\GraphQL\Relay\Field\GlobalIdField;
use Youshido\GraphQL\Type\InterfaceType\AbstractInterfaceType;

class NodeInterfaceType extends AbstractInterfaceType
{

    /** @var  FetcherInterface */ //todo: maybe there are better solution
    protected $fetcher;

    public function getName(): string
    {
        return 'NodeInterface';
    }

    /**
     * @throws ConfigurationException
     */
    public function build(InterfaceTypeConfig $config): void
    {
        $config->addField(new GlobalIdField('NodeInterface'));
    }

    public function resolveType($object)
    {
        return $this->fetcher?->resolveType($object);

    }

    /**
     * @return FetcherInterface
     */
    public function getFetcher()
    {
        return $this->fetcher;
    }

    /**
     * @param FetcherInterface $fetcher
     */
    public function setFetcher($fetcher): static
    {
        $this->fetcher = $fetcher;

        return $this;
    }
}

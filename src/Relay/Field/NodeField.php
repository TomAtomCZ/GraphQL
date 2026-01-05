<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 5/10/16 11:46 PM
*/

namespace Youshido\GraphQL\Relay\Field;


use Youshido\GraphQL\Config\Field\FieldConfig;
use Youshido\GraphQL\Exception\ConfigurationException;
use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQL\Field\AbstractField;
use Youshido\GraphQL\Field\InputField;
use Youshido\GraphQL\Relay\Fetcher\FetcherInterface;
use Youshido\GraphQL\Relay\Node;
use Youshido\GraphQL\Relay\NodeInterfaceType;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Scalar\IdType;

class NodeField extends AbstractField
{
    protected FetcherInterface $fetcher;

    protected NodeInterfaceType $type;

    public function __construct(FetcherInterface $fetcher)
    {
        $this->fetcher = $fetcher;
        $this->type = (new NodeInterfaceType())->setFetcher($this->fetcher);

        parent::__construct();
    }

    public function getName(): string
    {
        return 'node';
    }

    public function getDescription(): string
    {
        return 'Fetches an object given its ID';
    }

    /**
     * @throws ConfigurationException
     */
    public function build(FieldConfig $config): void
    {
        $config->addArgument(new InputField([
            'name' => 'id',
            'type' => new NonNullType(new IdType()),
            'description' => 'The ID of an object'
        ]));
    }

    public function getType(): mixed
    {
        return $this->type;
    }

    public function resolve($value, array $args, ResolveInfo $info): mixed
    {
        list($type, $id) = Node::fromGlobalId($args['id']);

        return $this->fetcher->resolveNode($type, $id);
    }
}

<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/27/15 1:24 AM
*/

namespace Youshido\GraphQL\Type\Object;

use Youshido\GraphQL\Config\Object\ObjectTypeConfig;
use Youshido\GraphQL\Exception\ConfigurationException;

final class ObjectType extends AbstractObjectType
{

    /**
     * @throws ConfigurationException
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->config = new ObjectTypeConfig($config, $this, true);
    }

    /**
     * @inheritdoc
     *
     * @codeCoverageIgnore
     */
    public function build(ObjectTypeConfig $config): void
    {
    }

    public function getName(): ?string
    {
        return $this->getConfigValue('name');
    }
}

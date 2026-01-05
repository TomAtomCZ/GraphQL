<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 12/1/15 10:25 PM
*/

namespace Youshido\GraphQL\Type\InputObject;

use Youshido\GraphQL\Config\Object\InputObjectTypeConfig;
use Youshido\GraphQL\Exception\ConfigurationException;

final class InputObjectType extends AbstractInputObjectType
{

    /**
     * @throws ConfigurationException
     */
    public function __construct($config)
    {
        parent::__construct($config);
        $this->config = new InputObjectTypeConfig($config, $this, true);
    }

    /**
     * @inheritdoc
     *
     * @codeCoverageIgnore
     */
    public function build($config): void
    {
    }
}

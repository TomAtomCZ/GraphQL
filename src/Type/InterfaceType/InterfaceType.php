<?php
/**
 * Date: 12.05.16
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Type\InterfaceType;


use Youshido\GraphQL\Config\Object\InterfaceTypeConfig;
use Youshido\GraphQL\Exception\ConfigurationException;

final class InterfaceType extends AbstractInterfaceType
{

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->config = new InterfaceTypeConfig($config, $this, true);
    }

    /**
     * @inheritdoc
     *
     * @codeCoverageIgnore
     */
    public function build(InterfaceTypeConfig $config): void
    {
    }

    /**
     * @throws ConfigurationException
     */
    public function resolveType($object)
    {
        return $this->getConfig()->resolveType($object);
    }

}

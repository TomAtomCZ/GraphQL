<?php
/**
 * Date: 13.05.16
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Field;

use Youshido\GraphQL\Config\Field\InputFieldConfig;
use Youshido\GraphQL\Exception\ConfigurationException;
use Youshido\GraphQL\Type\AbstractType;
use Youshido\GraphQL\Type\Traits\AutoNameTrait;
use Youshido\GraphQL\Type\Traits\FieldsArgumentsAwareObjectTrait;
use Youshido\GraphQL\Type\TypeFactory;
use Youshido\GraphQL\Type\TypeService;

abstract class AbstractInputField implements InputFieldInterface
{

    use FieldsArgumentsAwareObjectTrait;
    use AutoNameTrait;

    protected bool $isFinal = false;

    /**
     * @throws ConfigurationException
     */
    public function __construct(array $config = [])
    {
        if (empty($config['type'])) {
            $config['type'] = $this->getType();
            $config['name'] = $this->getName();
        }

        if (TypeService::isScalarType($config['type'])) {
            $config['type'] = TypeFactory::getScalarType($config['type']);
        }

        $this->config = new InputFieldConfig($config, $this, $this->isFinal);
        $this->build($this->config);
    }

    public function build(InputFieldConfig $config): void
    {

    }

    abstract public function getType(): mixed;

    public function getDefaultValue()
    {
        return $this->config->getDefaultValue();
    }

    //todo: think about serialize, parseValue methods

}

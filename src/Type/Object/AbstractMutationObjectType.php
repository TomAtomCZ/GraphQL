<?php
/**
 * Date: 14.01.16
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Type\Object;

use Youshido\GraphQL\Type\InputObject\AbstractInputObjectType;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Scalar\AbstractScalarType;

abstract class AbstractMutationObjectType extends AbstractObjectType
{

    public function getType(): NonNullType|AbstractObjectType|AbstractScalarType|AbstractInputObjectType|null|static
    {
        return $this->getOutputType();
    }

    abstract public function getOutputType();
}

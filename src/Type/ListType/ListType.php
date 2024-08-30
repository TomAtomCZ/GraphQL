<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 12/1/15 1:22 AM
*/

namespace Youshido\GraphQL\Type\ListType;


use Youshido\GraphQL\Config\Object\ListTypeConfig;
use Youshido\GraphQL\Type\Enum\AbstractEnumType;
use Youshido\GraphQL\Type\InputObject\InputObjectType;
use Youshido\GraphQL\Type\InterfaceType\AbstractInterfaceType;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Object\AbstractObjectType;
use Youshido\GraphQL\Type\Scalar\AbstractScalarType;

final class ListType extends AbstractListType
{

    public function __construct($itemType)
    {
        parent::__construct();
        $this->config = new ListTypeConfig(['itemType' => $itemType], $this, true);
    }

    public function getItemType(): NonNullType|AbstractObjectType|AbstractInterfaceType|AbstractScalarType|AbstractEnumType|InputObjectType|null
    {
        return $this->getConfig()->get('itemType');
    }

    public function getName(): ?string
    {
        return null;
    }
}

<?php
/*
* This file is a part of GraphQL project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 5/14/16 11:38 AM
*/

namespace Youshido\GraphQL\Type;


interface AbstractInterfaceTypeInterface
{
    /**
     * @param $object object from resolve function
     *
     * @return AbstractType|null
     */
    public function resolveType(object $object): ?AbstractType;
}

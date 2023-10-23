<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 12/1/15 11:26 PM
*/

namespace Youshido\GraphQL\Field;


final class InputField extends AbstractInputField
{

    protected bool $isFinal = false;

    /**
     * @return mixed
     */
    public function getType(): mixed
    {
        return $this->getConfigValue('type');
    }
}

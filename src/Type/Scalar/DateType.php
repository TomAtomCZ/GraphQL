<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Alexandr Viniychuk <a@viniychuk.com>
* created: 11/27/15 1:22 AM
*/

namespace Youshido\GraphQL\Type\Scalar;

use DateTime;

/**
 * @deprecated USE DateTime type instead. To be removed in 1.4.
 *
 * Class DateType
 * @package Youshido\GraphQL\Type\Scalar
 */
class DateType extends DateTimeType
{
    public function __construct()
    {
        parent::__construct('Y-m-d');
    }

    public function getName(): string
    {
        return 'Date';
    }

    public function getDescription(): string
    {
        return 'DEPRECATED. Use DateTime instead';
    }

}

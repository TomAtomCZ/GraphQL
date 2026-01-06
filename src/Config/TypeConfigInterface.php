<?php
/**
 * Date: 17.12.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Config;


use Youshido\GraphQL\Field\Field;

interface TypeConfigInterface
{
    public function addField(Field|string $field, ?array $fieldInfo = null);

    public function getField($name);

    public function removeField($name);

    public function hasField($name);

    public function getFields();

}

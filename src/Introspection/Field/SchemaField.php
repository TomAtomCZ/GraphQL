<?php
/**
 * Date: 16.05.16
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Introspection\Field;


use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQL\Field\AbstractField;
use Youshido\GraphQL\Introspection\SchemaType;

class SchemaField extends AbstractField
{
    public function getType(): SchemaType
    {
        return new SchemaType();
    }

    public function getName(): string
    {
        return '__schema';
    }

    public function resolve($value, array $args, ResolveInfo $info): mixed
    {
        return $info->getExecutionContext()->getSchema();
    }
}

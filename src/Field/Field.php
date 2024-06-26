<?php
/**
 * Date: 27.11.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Field;

/**
 * Class Field
 * @package Youshido\GraphQL\Type\Field
 *
 */
final class Field extends AbstractField
{

    protected $isFinal = true;

    protected $_typeCache;

    protected $_nameCache;

    /**
     * @return mixed
     */
    public function getType(): mixed
    {
        return $this->_typeCache ?: ($this->_typeCache = $this->getConfigValue('type'));
    }

    public function getName()
    {
        return $this->_nameCache ?: ($this->_nameCache = $this->getConfigValue('name'));
    }
}
<?php
/**
 * Date: 23.11.15
 *
 * @author Portey Vasil <portey@gmail.com>
 */

namespace Youshido\GraphQL\Parser;

class Token
{

    final const TYPE_END = 'end';

    final const TYPE_IDENTIFIER = 'identifier';

    final const TYPE_NUMBER = 'number';

    final const TYPE_STRING = 'string';

    final const TYPE_ON = 'on';

    final const TYPE_QUERY = 'query';

    final const TYPE_MUTATION = 'mutation';

    final const TYPE_FRAGMENT = 'fragment';

    final const TYPE_FRAGMENT_REFERENCE = '...';

    final const TYPE_TYPED_FRAGMENT = 'typed fragment';

    final const TYPE_LBRACE = '{';

    final const TYPE_RBRACE = '}';

    final const TYPE_LPAREN = '(';

    final const TYPE_RPAREN = ')';

    final const TYPE_LSQUARE_BRACE = '[';

    final const TYPE_RSQUARE_BRACE = ']';

    final const TYPE_COLON = ':';

    final const TYPE_COMMA = ',';

    final const TYPE_VARIABLE = '$';

    final const TYPE_POINT = '.';

    final const TYPE_REQUIRED = '!';

    final const TYPE_EQUAL = '=';

    final const TYPE_AT = '@';

    final const TYPE_NULL = 'null';

    final const TYPE_TRUE = 'true';

    final const TYPE_FALSE = 'false';


    /** @var mixed */
    private $data;

    /** @var  string */
    private $type;

    /** @var integer */
    private $line;

    /** @var integer */
    private $column;

    public function __construct($type, $line, $column, $data = null)
    {
        $this->type = $type;
        $this->data = $data;

        $this->line = $line;
        $this->column = $column;

        if ($data) {
            $tokenLength = mb_strlen((string)$data);
            $tokenLength = $tokenLength > 1 ? $tokenLength - 1 : 0;

            $this->column = $column - $tokenLength;
        }

        if ($this->getType() == self::TYPE_TRUE) {
            $this->data = true;
        }

        if ($this->getType() == self::TYPE_FALSE) {
            $this->data = false;
        }

        if ($this->getType() == self::TYPE_NULL) {
            $this->data = null;
        }
    }

    public static function tokenName(string $tokenType): string
    {
        return [
            self::TYPE_END => 'END',
            self::TYPE_IDENTIFIER => 'IDENTIFIER',
            self::TYPE_NUMBER => 'NUMBER',
            self::TYPE_STRING => 'STRING',
            self::TYPE_ON => 'ON',
            self::TYPE_QUERY => 'QUERY',
            self::TYPE_MUTATION => 'MUTATION',
            self::TYPE_FRAGMENT => 'FRAGMENT',
            self::TYPE_FRAGMENT_REFERENCE => 'FRAGMENT_REFERENCE',
            self::TYPE_TYPED_FRAGMENT => 'TYPED_FRAGMENT',
            self::TYPE_LBRACE => 'LBRACE',
            self::TYPE_RBRACE => 'RBRACE',
            self::TYPE_LPAREN => 'LPAREN',
            self::TYPE_RPAREN => 'RPAREN',
            self::TYPE_LSQUARE_BRACE => 'LSQUARE_BRACE',
            self::TYPE_RSQUARE_BRACE => 'RSQUARE_BRACE',
            self::TYPE_COLON => 'COLON',
            self::TYPE_COMMA => 'COMMA',
            self::TYPE_VARIABLE => 'VARIABLE',
            self::TYPE_POINT => 'POINT',
            self::TYPE_NULL => 'NULL',
            self::TYPE_TRUE => 'TRUE',
            self::TYPE_FALSE => 'FALSE',
            self::TYPE_REQUIRED => 'REQUIRED',
            self::TYPE_AT => 'AT',
        ][$tokenType];
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * @return int
     */
    public function getColumn()
    {
        return $this->column;
    }
}

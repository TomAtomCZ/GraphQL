<?php
/*
* This file is a part of graphql-youshido project.
*
* @author Portey Vasil <portey@gmail.com>
* created: 11/23/15 1:22 AM
*/

namespace Youshido\GraphQL\Parser;


use Youshido\GraphQL\Exception\Parser\SyntaxErrorException;
use Youshido\GraphQL\Parser\Ast\Argument;
use Youshido\GraphQL\Parser\Ast\ArgumentValue\InputList;
use Youshido\GraphQL\Parser\Ast\ArgumentValue\InputObject;
use Youshido\GraphQL\Parser\Ast\ArgumentValue\Literal;
use Youshido\GraphQL\Parser\Ast\ArgumentValue\Variable;
use Youshido\GraphQL\Parser\Ast\ArgumentValue\VariableReference;
use Youshido\GraphQL\Parser\Ast\Directive;
use Youshido\GraphQL\Parser\Ast\Field;
use Youshido\GraphQL\Parser\Ast\Fragment;
use Youshido\GraphQL\Parser\Ast\FragmentReference;
use Youshido\GraphQL\Parser\Ast\Mutation;
use Youshido\GraphQL\Parser\Ast\Query;
use Youshido\GraphQL\Parser\Ast\TypedFragmentReference;

class Parser extends Tokenizer
{

    private array $data = [];

    /**
     * @throws SyntaxErrorException
     */
    public function parse($source = null): array
    {
        $this->init($source);

        while (!$this->end()) {
            $tokenType = $this->peek()->getType();

            switch ($tokenType) {
                case Token::TYPE_LBRACE:
                    foreach ($this->parseBody() as $query) {
                        $this->data['queries'][] = $query;
                    }

                    break;
                case Token::TYPE_QUERY:
                    $queries = $this->parseOperation();
                    foreach ($queries as $query) {
                        $this->data['queries'][] = $query;
                    }

                    break;
                case Token::TYPE_MUTATION:
                    $mutations = $this->parseOperation(Token::TYPE_MUTATION);
                    foreach ($mutations as $query) {
                        $this->data['mutations'][] = $query;
                    }

                    break;

                case Token::TYPE_FRAGMENT:
                    $this->data['fragments'][] = $this->parseFragment();

                    break;

                default:
                    throw new SyntaxErrorException('Incorrect request syntax', $this->getLocation());
            }
        }

        return $this->data;
    }

    private function init($source = null): void
    {
        $this->initTokenizer($source);

        $this->data = [
            'queries' => [],
            'mutations' => [],
            'fragments' => [],
            'fragmentReferences' => [],
            'variables' => [],
            'variableReferences' => [],
        ];
    }

    /**
     * @return mixed[]
     * @throws SyntaxErrorException
     * @throws SyntaxErrorException
     */
    protected function parseOperation($type = Token::TYPE_QUERY): array
    {
        $operation = null;
        $directives = [];

        if ($this->matchMulti([Token::TYPE_QUERY, Token::TYPE_MUTATION])) {
            $this->lex();

            $this->eat(Token::TYPE_IDENTIFIER);

            if ($this->match(Token::TYPE_LPAREN)) {
                $this->parseVariables();
            }

            if ($this->match(Token::TYPE_AT)) {
                $directives = $this->parseDirectiveList();
            }

        }

        $this->lex();

        $fields = [];

        while (!$this->match(Token::TYPE_RBRACE) && !$this->end()) {
            $this->eatMulti([Token::TYPE_COMMA]);

            $operation = $this->parseBodyItem($type);
            $operation->setDirectives($directives);

            $fields[] = $operation;
        }

        $this->expect(Token::TYPE_RBRACE);

        return $fields;
    }

    /**
     * @return mixed[]
     * @throws SyntaxErrorException
     * @throws SyntaxErrorException
     * @throws SyntaxErrorException
     */
    protected function parseBody($token = Token::TYPE_QUERY, $highLevel = true): array
    {
        $fields = [];

        $this->lex();

        while (!$this->match(Token::TYPE_RBRACE) && !$this->end()) {
            $this->eatMulti([Token::TYPE_COMMA]);

            if ($this->match(Token::TYPE_FRAGMENT_REFERENCE)) {
                $this->lex();

                if ($this->eat(Token::TYPE_ON)) {
                    $fields[] = $this->parseBodyItem(Token::TYPE_TYPED_FRAGMENT, $highLevel);
                } else {
                    $fields[] = $this->parseFragmentReference();
                }
            } else {
                $fields[] = $this->parseBodyItem($token, $highLevel);
            }
        }

        $this->expect(Token::TYPE_RBRACE);

        return $fields;
    }

    /**
     * @throws SyntaxErrorException
     */
    protected function parseVariables()
    {
        $this->eat(Token::TYPE_LPAREN);

        while (!$this->match(Token::TYPE_RPAREN) && !$this->end()) {
            $this->eat(Token::TYPE_COMMA);

            $variableToken = $this->eat(Token::TYPE_VARIABLE);
            $nameToken = $this->eatIdentifierToken();
            $this->eat(Token::TYPE_COLON);

            $isArray = false;
            $arrayElementNullable = true;

            if ($this->match(Token::TYPE_LSQUARE_BRACE)) {
                $isArray = true;

                $this->eat(Token::TYPE_LSQUARE_BRACE);
                $type = $this->eatIdentifierToken()->getData();

                if ($this->match(Token::TYPE_REQUIRED)) {
                    $arrayElementNullable = false;
                    $this->eat(Token::TYPE_REQUIRED);
                }

                $this->eat(Token::TYPE_RSQUARE_BRACE);
            } else {
                $type = $this->eatIdentifierToken()->getData();
            }

            $required = false;
            if ($this->match(Token::TYPE_REQUIRED)) {
                $required = true;
                $this->eat(Token::TYPE_REQUIRED);
            }

            $variable = new Variable(
                $nameToken->getData(),
                $type,
                $required,
                $isArray,
                new Location($variableToken->getLine(), $variableToken->getColumn()),
                $arrayElementNullable
            );

            if ($this->match(Token::TYPE_EQUAL)) {
                $this->eat(Token::TYPE_EQUAL);
                $variable->setDefaultValue($this->parseValue());
            }

            $this->data['variables'][] = $variable;
        }

        $this->expect(Token::TYPE_RPAREN);
    }

    /**
     * @throws SyntaxErrorException
     */
    protected function expectMulti($types)
    {
        if ($this->matchMulti($types)) {
            return $this->lex();
        }

        throw $this->createUnexpectedException($this->peek());
    }

    /**
     * @throws SyntaxErrorException
     */
    protected function parseVariableReference()
    {
        $startToken = $this->expectMulti([Token::TYPE_VARIABLE]);

        if ($this->match(Token::TYPE_NUMBER) || $this->match(Token::TYPE_IDENTIFIER) || $this->match(Token::TYPE_QUERY)) {
            $name = $this->lex()->getData();

            $variable = $this->findVariable($name);
            $variable?->setUsed(true);

            $variableReference = new VariableReference($name, new Location($startToken->getLine(), $startToken->getColumn()), $variable);

            $this->data['variableReferences'][] = $variableReference;

            return $variableReference;
        }

        throw $this->createUnexpectedException($this->peek());
    }

    protected function findVariable($name)
    {
        foreach ((array)$this->data['variables'] as $variable) {
            /** @var $variable Variable */
            if ($variable->getName() === $name) {
                return $variable;
            }
        }

        return null;
    }

    protected function parseFragmentReference(): FragmentReference
    {
        $nameToken = $this->eatIdentifierToken();
        $fragmentReference = new FragmentReference($nameToken->getData(), new Location($nameToken->getLine(), $nameToken->getColumn()));

        $this->data['fragmentReferences'][] = $fragmentReference;

        return $fragmentReference;
    }

    /**
     * @throws SyntaxErrorException
     */
    protected function eatIdentifierToken()
    {
        return $this->expectMulti([
            Token::TYPE_IDENTIFIER,
            Token::TYPE_MUTATION,
            Token::TYPE_QUERY,
            Token::TYPE_FRAGMENT,
        ]);
    }

    /**
     * @throws SyntaxErrorException
     */
    protected function parseBodyItem($type = Token::TYPE_QUERY, $highLevel = true): Query|TypedFragmentReference|Mutation|Field
    {
        $nameToken = $this->eatIdentifierToken();
        $alias = null;

        if ($this->eat(Token::TYPE_COLON)) {
            $alias = $nameToken->getData();
            $nameToken = $this->eatIdentifierToken();
        }

        $bodyLocation = new Location($nameToken->getLine(), $nameToken->getColumn());
        $arguments = $this->match(Token::TYPE_LPAREN) ? $this->parseArgumentList() : [];
        $directives = $this->match(Token::TYPE_AT) ? $this->parseDirectiveList() : [];

        if ($this->match(Token::TYPE_LBRACE)) {
            $fields = $this->parseBody($type === Token::TYPE_TYPED_FRAGMENT ? Token::TYPE_QUERY : $type, false);

            if ($fields === []) {
                throw $this->createUnexpectedTokenTypeException($this->lookAhead->getType());
            }

            if ($type === Token::TYPE_QUERY) {
                return new Query($nameToken->getData(), $alias, $arguments, $fields, $directives, $bodyLocation);
            } elseif ($type === Token::TYPE_TYPED_FRAGMENT) {
                return new TypedFragmentReference($nameToken->getData(), $fields, $directives, $bodyLocation);
            } else {
                return new Mutation($nameToken->getData(), $alias, $arguments, $fields, $directives, $bodyLocation);
            }
        } else {
            if ($highLevel && $type === Token::TYPE_MUTATION) {
                return new Mutation($nameToken->getData(), $alias, $arguments, [], $directives, $bodyLocation);
            } elseif ($highLevel && $type === Token::TYPE_QUERY) {
                return new Query($nameToken->getData(), $alias, $arguments, [], $directives, $bodyLocation);
            }

            return new Field($nameToken->getData(), $alias, $arguments, $directives, $bodyLocation);
        }
    }

    /**
     * @return mixed[]
     * @throws SyntaxErrorException
     * @throws SyntaxErrorException
     */
    protected function parseArgumentList(): array
    {
        $args = [];

        $this->expect(Token::TYPE_LPAREN);

        while (!$this->match(Token::TYPE_RPAREN) && !$this->end()) {
            $this->eat(Token::TYPE_COMMA);
            $args[] = $this->parseArgument();
        }

        $this->expect(Token::TYPE_RPAREN);

        return $args;
    }

    /**
     * @throws SyntaxErrorException
     */
    protected function parseArgument(): Argument
    {
        $nameToken = $this->eatIdentifierToken();
        $this->expect(Token::TYPE_COLON);
        $value = $this->parseValue();

        return new Argument($nameToken->getData(), $value, new Location($nameToken->getLine(), $nameToken->getColumn()));
    }

    /**
     * @return mixed[]
     */
    protected function parseDirectiveList(): array
    {
        $directives = [];

        while ($this->match(Token::TYPE_AT)) {
            $directives[] = $this->parseDirective();
            $this->eat(Token::TYPE_COMMA);
        }

        return $directives;
    }

    /**
     * @throws SyntaxErrorException
     */
    protected function parseDirective(): Directive
    {
        $this->expect(Token::TYPE_AT);

        $nameToken = $this->eatIdentifierToken();
        $args = $this->match(Token::TYPE_LPAREN) ? $this->parseArgumentList() : [];

        return new Directive($nameToken->getData(), $args, new Location($nameToken->getLine(), $nameToken->getColumn()));
    }

    /**
     * @return array|InputList|InputObject|Literal|VariableReference
     *
     * @throws SyntaxErrorException
     */
    protected function parseValue()
    {
        switch ($this->lookAhead->getType()) {
            case Token::TYPE_LSQUARE_BRACE:
                return $this->parseList();

            case Token::TYPE_LBRACE:
                return $this->parseObject();

            case Token::TYPE_VARIABLE:
                return $this->parseVariableReference();

            case Token::TYPE_NUMBER:
            case Token::TYPE_STRING:
            case Token::TYPE_IDENTIFIER:
            case Token::TYPE_NULL:
            case Token::TYPE_TRUE:
            case Token::TYPE_FALSE:
                $token = $this->lex();

                return new Literal($token->getData(), new Location($token->getLine(), $token->getColumn()));
        }

        throw $this->createUnexpectedException($this->lookAhead);
    }

    /**
     * @throws SyntaxErrorException
     */
    protected function parseList($createType = true): InputList|array
    {
        $startToken = $this->eat(Token::TYPE_LSQUARE_BRACE);

        $list = [];
        while (!$this->match(Token::TYPE_RSQUARE_BRACE) && !$this->end()) {
            $list[] = $this->parseListValue();

            $this->eat(Token::TYPE_COMMA);
        }

        $this->expect(Token::TYPE_RSQUARE_BRACE);

        return $createType ? new InputList($list, new Location($startToken->getLine(), $startToken->getColumn())) : $list;
    }

    /**
     * @throws SyntaxErrorException
     */
    protected function parseListValue()
    {
        switch ($this->lookAhead->getType()) {
            case Token::TYPE_NUMBER:
            case Token::TYPE_STRING:
            case Token::TYPE_TRUE:
            case Token::TYPE_FALSE:
            case Token::TYPE_NULL:
            case Token::TYPE_IDENTIFIER:
                return $this->expect($this->lookAhead->getType())->getData();

            case Token::TYPE_VARIABLE:
                return $this->parseVariableReference();

            case Token::TYPE_LBRACE:
                return $this->parseObject();

            case Token::TYPE_LSQUARE_BRACE:
                return $this->parseList(false);
        }

        throw new SyntaxErrorException("Can't parse argument", $this->getLocation());
    }

    /**
     * @throws SyntaxErrorException
     */
    protected function parseObject($createType = true): InputObject|array
    {
        $startToken = $this->eat(Token::TYPE_LBRACE);

        $object = [];
        while (!$this->match(Token::TYPE_RBRACE) && !$this->end()) {
            $key = $this->expectMulti([Token::TYPE_STRING, Token::TYPE_IDENTIFIER])->getData();
            $this->expect(Token::TYPE_COLON);
            $value = $this->parseListValue();

            $this->eat(Token::TYPE_COMMA);

            $object[$key] = $value;
        }

        $this->eat(Token::TYPE_RBRACE);

        return $createType ? new InputObject($object, new Location($startToken->getLine(), $startToken->getColumn())) : $object;
    }

    protected function parseFragment(): Fragment
    {
        $this->lex();
        $nameToken = $this->eatIdentifierToken();

        $this->eat(Token::TYPE_ON);

        $model = $this->eatIdentifierToken();

        $directives = $this->match(Token::TYPE_AT) ? $this->parseDirectiveList() : [];

        $fields = $this->parseBody(Token::TYPE_QUERY, false);

        return new Fragment($nameToken->getData(), $model->getData(), $directives, $fields, new Location($nameToken->getLine(), $nameToken->getColumn()));
    }

    protected function eat($type)
    {
        if ($this->match($type)) {
            return $this->lex();
        }

        return null;
    }

    protected function eatMulti($types)
    {
        if ($this->matchMulti($types)) {
            return $this->lex();
        }

        return null;
    }

    protected function matchMulti($types): bool
    {
        return in_array($this->peek()->getType(), (array)$types, true);
    }
}

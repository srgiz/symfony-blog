<?php
declare(strict_types=1);

namespace App\Doctrine\Functions;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

/**
 * fn('text', real: 0)
 */
class ToReal extends FunctionNode
{
    private Node $value;

    private ?Node $default = null;

    public function getSql(SqlWalker $sqlWalker)
    {
        if (null !== $this->default) {
            return sprintf('to_real(%s::text, %s)', $this->value->dispatch($sqlWalker), $this->default->dispatch($sqlWalker));
        }

        return sprintf('to_real(%s::text)', $this->value->dispatch($sqlWalker));
    }

    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        $this->value = $parser->StringPrimary();

        if ($parser->getLexer()->isNextToken(Lexer::T_COMMA)) {
            $parser->match(Lexer::T_COMMA);

            $this->default = $parser->ArithmeticPrimary();
        }

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}

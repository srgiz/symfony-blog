<?php
declare(strict_types=1);

namespace App\Doctrine\Functions;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

/**
 * array[]::text[]
 */
class ArrayText extends FunctionNode
{
    /** @var Node[] */
    private array $array = [];

    public function getSql(SqlWalker $sqlWalker): string
    {
        $values = [];

        foreach ($this->array as $node) {
            $values[] = $node->dispatch($sqlWalker);
        }

        $count = count($values);
        $format = '';

        if ($count) {
            $format = str_repeat('%s, ', $count - 1) . '%s';
        }

        $format = 'array[' . $format . ']';

        if (!$count) {
            $format .= '::text[]';
        }

        return vsprintf($format, $values);
    }

    public function parse(Parser $parser): void
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        while ($parser->getLexer()->isNextTokenAny([Lexer::T_INPUT_PARAMETER, Lexer::T_STRING])) {
            $this->array[] = $parser->StringPrimary();

            if ($parser->getLexer()->isNextToken(Lexer::T_COMMA)) {
                $parser->match(Lexer::T_COMMA);
            }
        }

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}

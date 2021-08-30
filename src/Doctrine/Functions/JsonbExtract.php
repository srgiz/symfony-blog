<?php
declare(strict_types=1);

namespace App\Doctrine\Functions;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

/**
 * jsonb -> text
 */
class JsonbExtract extends FunctionNode
{
    private Node $jsonb;

    private Node $key;

    public function getSql(SqlWalker $sqlWalker): string
    {
        return sprintf('%s->%s', $this->jsonb->dispatch($sqlWalker), $this->key->dispatch($sqlWalker));
    }

    public function parse(Parser $parser): void
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        $this->jsonb = $parser->StringPrimary();

        $parser->match(Lexer::T_COMMA);

        $this->key = $parser->StringPrimary();

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}

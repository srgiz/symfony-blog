<?php
declare(strict_types=1);

namespace App\Doctrine\Functions;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

/**
 * jsonb #>> text
 * fn(jsonb, 'key1', 'key2')
 * fn(jsonb, 'key1', 'key2')
 * @example "SELECT oprname, oprcode FROM pg_operator WHERE oprcode::text LIKE 'jsonb%'"
 */
class JsonbExtractPathText extends FunctionNode
{
    private Node $jsonb;

    /** @var Node[] */
    private array $keys = [];

    public function getSql(SqlWalker $sqlWalker): string
    {
        $path = [];

        foreach ($this->keys as $node) {
            $path[] = $node->dispatch($sqlWalker);
        }

        return sprintf('jsonb_extract_path_text(%s, %s)', $this->jsonb->dispatch($sqlWalker), implode(', ', $path));
    }

    public function parse(Parser $parser): void
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        $this->jsonb = $parser->StringPrimary();

        $parser->match(Lexer::T_COMMA);

        while ($parser->getLexer()->isNextTokenAny([Lexer::T_INPUT_PARAMETER, Lexer::T_STRING])) {
            $this->keys[] = $parser->StringPrimary();

            if ($parser->getLexer()->isNextToken(Lexer::T_COMMA)) {
                $parser->match(Lexer::T_COMMA);
            }
        }

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}

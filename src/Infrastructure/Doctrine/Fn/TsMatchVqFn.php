<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Fn;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\TokenType;

/**
 * fn('query', col1, col2).
 */
class TsMatchVqFn extends FunctionNode
{
    /** @psalm-suppress PropertyNotSetInConstructor */
    private Node $query;

    /** @var Node[] */
    private array $columns = [];

    public function getSql(SqlWalker $sqlWalker): string
    {
        return sprintf(
            "(to_tsvector('russian', %2\$s) @@ plainto_tsquery('russian', %1\$s))",
            $this->query->dispatch($sqlWalker),
            implode(' || ', array_map(
                static function (Node $node) use ($sqlWalker) {
                    return $node->dispatch($sqlWalker);
                }, $this->columns
            ))
        );
    }

    public function parse(Parser $parser): void
    {
        $parser->match(TokenType::T_IDENTIFIER);
        $parser->match(TokenType::T_OPEN_PARENTHESIS);

        $this->query = $parser->StringPrimary();

        $parser->match(TokenType::T_COMMA);

        while (!$parser->getLexer()->isNextToken(TokenType::T_CLOSE_PARENTHESIS)) {
            /** @psalm-suppress InvalidPropertyAssignmentValue */
            $this->columns[] = $parser->StringExpression();

            if ($parser->getLexer()->isNextToken(TokenType::T_COMMA)) {
                $parser->match(TokenType::T_COMMA);
            }
        }

        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }
}

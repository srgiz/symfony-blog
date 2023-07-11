<?php
declare(strict_types=1);

namespace App\Markdown;

use Michelf\MarkdownExtra;

class MarkdownAdapter implements MarkdownInterface
{
    private MarkdownExtra $parser;

    public function __construct()
    {
        $this->parser = new MarkdownExtra();
        $this->parser->no_markup = true;
        $this->parser->no_entities = true;
        $this->parser->code_class_prefix = 'language-';
    }

    public function parse(?string $text): string
    {
        return $this->parser->transform($text ?? '');
    }
}

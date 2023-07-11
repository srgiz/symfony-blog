<?php

namespace App\Markdown;

interface MarkdownInterface
{
    public function parse(?string $text): string;
}

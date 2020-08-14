<?php

namespace App\Support;

use Illuminate\Support\Str;
use Michelf\MarkdownExtra;

class ContentTransformer
{
    protected string $content;
    protected MarkdownExtra $parser;

    public function __construct(string $content)
    {
        $this->content = $content;

        $this->configureParser();
    }

    public static function transform(string $content): string
    {
        return (new static($content))->handle();
    }

    public function handle(): string
    {
        $this->content = $this->parser->transform($this->content);

        $this->addAnchorTagsToHeadings();

        return $this->content;
    }

    protected function addAnchorTagsToHeadings(): void
    {
        $pattern = '/(\<h[1-3](.*?))\>(.*)(<\/h[1-3]>)/i';

        $this->content = preg_replace_callback($pattern, static function ($matches) {
            $anchorTag = '<a href="#' . Str::slug($matches[3]) . '" class="anchor-link" aria-label="Anchor"></a>';

            $matches[0] = $matches[1] . '>' . $matches[3] . $anchorTag . $matches[4];

            return $matches[0];
        }, $this->content);
    }

    protected function configureParser(): void
    {
        $this->parser = new MarkdownExtra;

        $this->parser->header_id_func = fn ($text) => Str::slug($text);
    }
}

<?php

declare (strict_types=1);
namespace League\Html_To_Markdown\Converter;

use League\Html_To_Markdown\Element_Interface;
class Blockquote_Converter implements Converter_Interface
{
    public function convert(Element_Interface $element): string
    {
        // Contents should have already been converted to Markdown by this point,
        // so we just need to add '>' symbols to each line.
        $markdown = '';
        $quote_content = \trim($element->get_value());
        $lines = \preg_split('/\r\n|\r|\n/', $quote_content);
        \assert(\is_array($lines));
        $total_lines = \count($lines);
        foreach ($lines as $i => $line) {
            $markdown .= '> ' . $line . "\n";
            if ($i + 1 === $total_lines) {
                $markdown .= "\n";
            }
        }
        return $markdown;
    }
    /**
     * @return string[]
     */
    public function get_supported_tags(): array
    {
        return ['blockquote'];
    }
}
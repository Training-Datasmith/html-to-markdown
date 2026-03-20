<?php

declare (strict_types=1);
namespace League\Html_To_Markdown\Converter;

use League\Html_To_Markdown\Element_Interface;
class Text_Converter implements Converter_Interface
{
    public function convert(Element_Interface $element): string
    {
        $markdown = $element->get_value();
        // Remove leftover \n at the beginning of the line
        $markdown = \ltrim($markdown, "\n");
        // Replace sequences of invisible characters with spaces
        $markdown = \preg_replace('~\s+~u', ' ', $markdown);
        \assert(\is_string($markdown));
        // Escape the following characters: '*', '_', '[', ']' and '\'
        if (($parent = $element->get_parent()) && $parent->get_tag_name() !== 'div') {
            $markdown = \preg_replace('~([*_\[\]\\\\])~u', '\\\\$1', $markdown);
            \assert(\is_string($markdown));
        }
        $markdown = \preg_replace('~^#~u', '\\\\#', $markdown);
        \assert(\is_string($markdown));
        if ($markdown === ' ') {
            $next = $element->get_next();
            if (!$next || $next->is_block()) {
                $markdown = '';
            }
        }
        return \htmlspecialchars($markdown, ENT_NOQUOTES, 'UTF-8');
    }
    /**
     * @return string[]
     */
    public function get_supported_tags(): array
    {
        return ['#text'];
    }
}
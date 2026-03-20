<?php

declare (strict_types=1);
namespace League\Html_To_Markdown\Converter;

use League\Html_To_Markdown\Element_Interface;
class Preformatted_Converter implements Converter_Interface
{
    public function convert(Element_Interface $element): string
    {
        $pre_content = \html_entity_decode($element->get_children_as_string());
        $pre_content = \preg_replace('/<pre\b[^>]*>/', '', $pre_content);
        \assert($pre_content !== null);
        $pre_content = \str_replace('</pre>', '', $pre_content);
        /*
         * Checking for the code tag.
         * Usually pre tags are used along with code tags. This conditional will check for already converted code tags,
         * which use backticks, and if those backticks are at the beginning and at the end of the string it means
         * there's no more information to convert.
         */
        $first_backtick = \strpos(\trim($pre_content), '`');
        $last_backtick = \strrpos(\trim($pre_content), '`');
        if ($first_backtick === 0 && $last_backtick === \strlen(\trim($pre_content)) - 1) {
            return $pre_content . "\n\n";
        }
        // If the execution reaches this point it means it's just a pre tag, with no code tag nested
        // Empty lines are a special case
        if ($pre_content === '') {
            return "```\n```\n\n";
        }
        // Normalizing new lines
        $pre_content = \preg_replace('/\r\n|\r|\n/', "\n", $pre_content);
        \assert(\is_string($pre_content));
        // Ensure there's a newline at the end
        if (\strrpos($pre_content, "\n") !== \strlen($pre_content) - \strlen("\n")) {
            $pre_content .= "\n";
        }
        // Use three backticks
        return "```\n" . $pre_content . "```\n\n";
    }
    /**
     * @return string[]
     */
    public function get_supported_tags(): array
    {
        return ['pre'];
    }
}
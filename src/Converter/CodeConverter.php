<?php

declare (strict_types=1);
namespace League\Html_To_Markdown\Converter;

use League\Html_To_Markdown\Element_Interface;
class Code_Converter implements Converter_Interface
{
    public function convert(Element_Interface $element): string
    {
        $language = '';
        // Checking for language class on the code block
        $classes = $element->get_attribute('class');
        if ($classes) {
            // Since tags can have more than one class, we need to find the one that starts with 'language-'
            $classes = \explode(' ', $classes);
            foreach ($classes as $class) {
                if (\strpos($class, 'language-') !== false) {
                    // Found one, save it as the selected language and stop looping over the classes.
                    $language = \str_replace('language-', '', $class);
                    break;
                }
            }
        }
        $markdown = '';
        $code = \html_entity_decode($element->get_children_as_string());
        // In order to remove the code tags we need to search for them and, in the case of the opening tag
        // use a regular expression to find the tag and the other attributes it might have
        $code = \preg_replace('/<code\b[^>]*>/', '', $code);
        \assert($code !== null);
        $code = \str_replace('</code>', '', $code);
        // Checking if it's a code block or span
        if ($this->should_be_block($element, $code)) {
            // Code block detected, newlines will be added in parent
            $markdown .= '```' . $language . "\n" . $code . "\n" . '```';
        } else {
            // One line of code, wrapping it on one backtick, removing new lines
            $markdown .= '`' . \preg_replace('/\r\n|\r|\n/', '', $code) . '`';
        }
        return $markdown;
    }
    /**
     * @return string[]
     */
    public function get_supported_tags(): array
    {
        return ['code'];
    }
    private function should_be_block(Element_Interface $element, string $code): bool
    {
        $parent = $element->get_parent();
        if ($parent !== null && $parent->get_tag_name() === 'pre') {
            return true;
        }
        return \preg_match('/[^\s]` `/', $code) === 1;
    }
}
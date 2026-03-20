<?php

declare (strict_types=1);
namespace League\Html_To_Markdown\Converter;

use League\Html_To_Markdown\Element_Interface;
class Paragraph_Converter implements Converter_Interface
{
    public function convert(Element_Interface $element): string
    {
        $value = $element->get_value();
        $markdown = '';
        $lines = \preg_split('/\r\n|\r|\n/', $value);
        \assert($lines !== false);
        foreach ($lines as $line) {
            /*
             * Some special characters need to be escaped based on the position that they appear
             * The following function will deal with those special cases.
             */
            $markdown .= $this->escape_special_characters($line);
            $markdown .= "\n";
        }
        return \trim($markdown) !== '' ? \rtrim($markdown) . "\n\n" : '';
    }
    /**
     * @return string[]
     */
    public function get_supported_tags(): array
    {
        return ['p'];
    }
    private function escape_special_characters(string $line): string
    {
        $line = $this->escape_first_characters($line);
        $line = $this->escape_other_characters($line);
        return $this->escape_other_characters_regex($line);
    }
    private function escape_first_characters(string $line): string
    {
        $escapable = ['>', '- ', '+ ', '--', '~~~', '---', '- - -'];
        foreach ($escapable as $i) {
            if (\strpos(\ltrim($line), $i) === 0) {
                // Found a character that must be escaped, adding a backslash before
                return '\\' . \ltrim($line);
            }
        }
        return $line;
    }
    private function escape_other_characters(string $line): string
    {
        $escapable = ['<!--'];
        foreach ($escapable as $i) {
            if (($pos = \strpos($line, $i)) === false) {
                continue;
            }
            // Found an escapable character, escaping it
            $line = \substr_replace($line, '\\', $pos, 0);
        }
        return $line;
    }
    private function escape_other_characters_regex(string $line): string
    {
        $reg_exs = [
            // Match numbers ending on ')' or '.' that are at the beginning of the line.
            // They will be escaped if immediately followed by a space or newline.
            '/^[0-9]+(?=(\)|\.)( |$))/',
        ];
        foreach ($reg_exs as $i) {
            if (!\preg_match($i, $line, $match)) {
                continue;
            }
            // Matched an escapable character, adding a backslash on the string before the offending character
            $line = \substr_replace($line, '\\', \strlen($match[0]), 0);
        }
        return $line;
    }
}
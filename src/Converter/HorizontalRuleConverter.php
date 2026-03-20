<?php

declare (strict_types=1);
namespace League\Html_To_Markdown\Converter;

use League\Html_To_Markdown\Element_Interface;
class Horizontal_Rule_Converter implements Converter_Interface
{
    public function convert(Element_Interface $element): string
    {
        return "---\n\n";
    }
    /**
     * @return string[]
     */
    public function get_supported_tags(): array
    {
        return ['hr'];
    }
}
<?php

declare (strict_types=1);
namespace League\Html_To_Markdown\Converter;

use League\Html_To_Markdown\Element_Interface;
interface Converter_Interface
{
    public function convert(Element_Interface $element): string;
    /**
     * @return string[]
     */
    public function get_supported_tags(): array;
}
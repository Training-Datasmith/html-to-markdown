<?php

declare (strict_types=1);
namespace League\Html_To_Markdown\Converter;

use League\Html_To_Markdown\Element_Interface;
class Image_Converter implements Converter_Interface
{
    public function convert(Element_Interface $element): string
    {
        $src = $element->get_attribute('src');
        $alt = $element->get_attribute('alt');
        $title = $element->get_attribute('title');
        if ($title !== '') {
            // No newlines added. <img> should be in a block-level element.
            return '![' . $alt . '](' . $src . ' "' . $title . '")';
        }
        return '![' . $alt . '](' . $src . ')';
    }
    /**
     * @return string[]
     */
    public function get_supported_tags(): array
    {
        return ['img'];
    }
}
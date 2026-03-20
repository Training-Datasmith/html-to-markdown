<?php

declare (strict_types=1);
namespace League\Html_To_Markdown;

interface Pre_Converter_Interface
{
    public function pre_convert(Element_Interface $element): void;
}
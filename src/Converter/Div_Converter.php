<?php

declare (strict_types=1);
namespace League\Html_To_Markdown\Converter;

use League\Html_To_Markdown\Configuration;
use League\Html_To_Markdown\Configuration_Aware_Interface;
use League\Html_To_Markdown\Element_Interface;
class Div_Converter implements Converter_Interface, Configuration_Aware_Interface
{
    /** @var Configuration */
    protected $config;
    public function set_config(Configuration $config): void
    {
        $this->config = $config;
    }
    public function convert(Element_Interface $element): string
    {
        if ($this->config->get_option('strip_tags', false)) {
            return $element->get_value() . "\n\n";
        }
        return \html_entity_decode($element->get_children_as_string());
    }
    /**
     * @return string[]
     */
    public function get_supported_tags(): array
    {
        return ['div'];
    }
}
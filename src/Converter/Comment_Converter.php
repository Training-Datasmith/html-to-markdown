<?php

declare (strict_types=1);
namespace League\Html_To_Markdown\Converter;

use League\Html_To_Markdown\Configuration;
use League\Html_To_Markdown\Configuration_Aware_Interface;
use League\Html_To_Markdown\Element_Interface;
class Comment_Converter implements Converter_Interface, Configuration_Aware_Interface
{
    /** @var Configuration */
    protected $config;
    public function set_config(Configuration $config): void
    {
        $this->config = $config;
    }
    public function convert(Element_Interface $element): string
    {
        if ($this->should_preserve($element)) {
            return '<!--' . $element->get_value() . '-->';
        }
        return '';
    }
    /**
     * @return string[]
     */
    public function get_supported_tags(): array
    {
        return ['#comment'];
    }
    private function should_preserve(Element_Interface $element): bool
    {
        $preserve = $this->config->get_option('preserve_comments');
        if ($preserve === true) {
            return true;
        }
        if (\is_array($preserve)) {
            $value = \trim($element->get_value());
            return \in_array($value, $preserve, true);
        }
        return false;
    }
}
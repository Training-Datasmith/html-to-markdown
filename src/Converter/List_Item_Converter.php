<?php

declare (strict_types=1);
namespace League\Html_To_Markdown\Converter;

use League\Html_To_Markdown\Coerce;
use League\Html_To_Markdown\Configuration;
use League\Html_To_Markdown\Configuration_Aware_Interface;
use League\Html_To_Markdown\Element_Interface;
class List_Item_Converter implements Converter_Interface, Configuration_Aware_Interface
{
    /** @var Configuration */
    protected $config;
    /** @var string|null */
    protected $list_item_style;
    public function set_config(Configuration $config): void
    {
        $this->config = $config;
    }
    public function convert(Element_Interface $element): string
    {
        // If parent is an ol, use numbers, otherwise, use dashes
        $list_type = ($parent = $element->get_parent()) ? $parent->get_tag_name() : 'ul';
        // Add spaces to start for nested list items
        $level = $element->get_list_item_level();
        $value = \trim(\implode("\n" . '    ', \explode("\n", \trim($element->get_value()))));
        // If list item is the first in a nested list, add a newline before it
        $prefix = '';
        if ($level > 0 && $element->get_sibling_position() === 1) {
            $prefix = "\n";
        }
        if ($list_type === 'ul') {
            $list_item_style = Coerce::to_string($this->config->get_option('list_item_style', '-'));
            $list_item_style_alternate = Coerce::to_string($this->config->get_option('list_item_style_alternate', ''));
            if (!isset($this->list_item_style)) {
                $this->list_item_style = $list_item_style_alternate ?: $list_item_style;
            }
            if ($list_item_style_alternate && $level === 0 && $element->get_sibling_position() === 1) {
                $this->list_item_style = $this->list_item_style === $list_item_style ? $list_item_style_alternate : $list_item_style;
            }
            return $prefix . $this->list_item_style . ' ' . $value . "\n";
        }
        if ($list_type === 'ol' && ($parent = $element->get_parent()) && $start = \intval($parent->get_attribute('start'))) {
            $number = $start + $element->get_sibling_position() - 1;
        } else {
            $number = $element->get_sibling_position();
        }
        return $prefix . $number . '. ' . $value . "\n";
    }
    /**
     * @return string[]
     */
    public function get_supported_tags(): array
    {
        return ['li'];
    }
}
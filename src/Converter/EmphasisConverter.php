<?php

declare (strict_types=1);
namespace League\Html_To_Markdown\Converter;

use League\Html_To_Markdown\Configuration;
use League\Html_To_Markdown\Configuration_Aware_Interface;
use League\Html_To_Markdown\Element_Interface;
class Emphasis_Converter implements Converter_Interface, Configuration_Aware_Interface
{
    /** @var Configuration */
    protected $config;
    protected function get_norm_tag(?Element_Interface $element): string
    {
        if ($element !== null && !$element->is_text()) {
            $tag = $element->get_tag_name();
            if ($tag === 'i' || $tag === 'em') {
                return 'em';
            }
            if ($tag === 'b' || $tag === 'strong') {
                return 'strong';
            }
        }
        return '';
    }
    public function set_config(Configuration $config): void
    {
        $this->config = $config;
    }
    public function convert(Element_Interface $element): string
    {
        $tag = $this->get_norm_tag($element);
        $value = $element->get_value();
        if (!\trim($value)) {
            return $value;
        }
        if ($tag === 'em') {
            $style = $this->config->get_option('italic_style');
        } else {
            $style = $this->config->get_option('bold_style');
        }
        $prefix = \ltrim($value) !== $value ? ' ' : '';
        $suffix = \rtrim($value) !== $value ? ' ' : '';
        /* If this node is immediately preceded or followed by one of the same type don't emit
         * the start or end $style, respectively. This prevents <em>foo</em><em>bar</em> from
         * being converted to *foo**bar* which is incorrect. We want *foobar* instead.
         */
        $pre_style = $this->get_norm_tag($element->get_previous_sibling()) === $tag ? '' : $style;
        $post_style = $this->get_norm_tag($element->get_next_sibling()) === $tag ? '' : $style;
        return $prefix . $pre_style . \trim($value) . $post_style . $suffix;
    }
    /**
     * @return string[]
     */
    public function get_supported_tags(): array
    {
        return ['em', 'i', 'strong', 'b'];
    }
}
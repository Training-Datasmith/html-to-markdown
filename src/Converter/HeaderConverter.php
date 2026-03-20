<?php

declare (strict_types=1);
namespace League\Html_To_Markdown\Converter;

use League\Html_To_Markdown\Configuration;
use League\Html_To_Markdown\Configuration_Aware_Interface;
use League\Html_To_Markdown\Element_Interface;
class Header_Converter implements Converter_Interface, Configuration_Aware_Interface
{
    public const STYLE_ATX = 'atx';
    public const STYLE_SETEXT = 'setext';
    /** @var Configuration */
    protected $config;
    public function set_config(Configuration $config): void
    {
        $this->config = $config;
    }
    public function convert(Element_Interface $element): string
    {
        $level = (int) \substr($element->get_tag_name(), 1, 1);
        $style = $this->config->get_option('header_style', self::STYLE_SETEXT);
        if (\strlen($element->get_value()) === 0) {
            return "\n";
        }
        if (($level === 1 || $level === 2) && !$element->is_descendant_of('blockquote') && $style === self::STYLE_SETEXT) {
            return $this->create_setext_header($level, $element->get_value());
        }
        return $this->create_atx_header($level, $element->get_value());
    }
    /**
     * @return string[]
     */
    public function get_supported_tags(): array
    {
        return ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'];
    }
    private function create_setext_header(int $level, string $content): string
    {
        $length = \function_exists('mb_strlen') ? \mb_strlen($content, 'utf-8') : \strlen($content);
        $underline = $level === 1 ? '=' : '-';
        return $content . "\n" . \str_repeat($underline, $length) . "\n\n";
    }
    private function create_atx_header(int $level, string $content): string
    {
        $prefix = \str_repeat('#', $level) . ' ';
        return $prefix . $content . "\n\n";
    }
}
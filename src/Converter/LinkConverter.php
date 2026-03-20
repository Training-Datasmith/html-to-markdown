<?php

declare (strict_types=1);
namespace League\Html_To_Markdown\Converter;

use League\Html_To_Markdown\Configuration;
use League\Html_To_Markdown\Configuration_Aware_Interface;
use League\Html_To_Markdown\Element_Interface;
class Link_Converter implements Converter_Interface, Configuration_Aware_Interface
{
    /** @var Configuration */
    protected $config;
    public function set_config(Configuration $config): void
    {
        $this->config = $config;
    }
    public function convert(Element_Interface $element): string
    {
        $href = $element->get_attribute('href');
        $title = $element->get_attribute('title');
        $text = \trim($element->get_value(), "\t\n\r\x00\v");
        if ($title !== '') {
            $markdown = '[' . $text . '](' . $href . ' "' . $title . '")';
        } elseif ($href === $text && $this->is_valid_autolink($href)) {
            $markdown = '<' . $href . '>';
        } elseif ($href === 'mailto:' . $text && $this->is_valid_email($text)) {
            $markdown = '<' . $text . '>';
        } else {
            if (\stristr($href, ' ')) {
                $href = '<' . $href . '>';
            }
            $markdown = '[' . $text . '](' . $href . ')';
        }
        if (!$href) {
            if ($this->should_strip()) {
                $markdown = $text;
            } else {
                $markdown = \html_entity_decode($element->get_children_as_string());
            }
        }
        return $markdown;
    }
    /**
     * @return string[]
     */
    public function get_supported_tags(): array
    {
        return ['a'];
    }
    private function is_valid_autolink(string $href): bool
    {
        $use_autolinks = $this->config->get_option('use_autolinks');
        return $use_autolinks && \preg_match('/^[A-Za-z][A-Za-z0-9.+-]{1,31}:[^<>\x00-\x20]*/i', $href) === 1;
    }
    private function is_valid_email(string $email): bool
    {
        // Email validation is messy business, but this should cover most cases
        return \filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    private function should_strip(): bool
    {
        return \boolval($this->config->get_option('strip_placeholder_links') ?? false);
    }
}
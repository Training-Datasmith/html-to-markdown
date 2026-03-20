<?php

declare (strict_types=1);
namespace League\Html_To_Markdown\Converter;

use League\Html_To_Markdown\Configuration;
use League\Html_To_Markdown\Configuration_Aware_Interface;
use League\Html_To_Markdown\Element_Interface;
class Default_Converter implements Converter_Interface, Configuration_Aware_Interface
{
    public const DEFAULT_CONVERTER = '_default';
    /** @var Configuration */
    protected $config;
    public function set_config(Configuration $config): void
    {
        $this->config = $config;
    }
    public function convert(Element_Interface $element): string
    {
        // If strip_tags is false (the default), preserve tags that don't have Markdown equivalents,
        // such as <span> nodes on their own. C14N() canonicalizes the node to a string.
        // See: http://www.php.net/manual/en/domnode.c14n.php
        if ($this->config->get_option('strip_tags', false)) {
            return $element->get_value();
        }
        $markdown = \html_entity_decode($element->get_children_as_string());
        // Tables are only handled here if TableConverter is not used
        if ($element->get_tag_name() === 'table') {
            $markdown .= "\n\n";
        }
        return $markdown;
    }
    /**
     * @return string[]
     */
    public function get_supported_tags(): array
    {
        return [self::DEFAULT_CONVERTER];
    }
}
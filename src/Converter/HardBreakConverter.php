<?php

declare (strict_types=1);
namespace League\Html_To_Markdown\Converter;

use League\Html_To_Markdown\Configuration;
use League\Html_To_Markdown\Configuration_Aware_Interface;
use League\Html_To_Markdown\Element_Interface;
class Hard_Break_Converter implements Converter_Interface, Configuration_Aware_Interface
{
    /** @var Configuration */
    protected $config;
    public function set_config(Configuration $config): void
    {
        $this->config = $config;
    }
    public function convert(Element_Interface $element): string
    {
        $return = $this->config->get_option('hard_break') ? "\n" : "  \n";
        $next = $element->get_next();
        if ($next) {
            $next_value = $next->get_value();
            if ($next_value) {
                if (\in_array(\substr($next_value, 0, 2), ['- ', '* ', '+ '], true)) {
                    $parent = $element->get_parent();
                    if ($parent && $parent->get_tag_name() === 'li') {
                        $return .= '\\';
                    }
                }
            }
        }
        return $return;
    }
    /**
     * @return string[]
     */
    public function get_supported_tags(): array
    {
        return ['br'];
    }
}
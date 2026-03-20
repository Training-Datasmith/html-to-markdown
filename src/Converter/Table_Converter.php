<?php

declare (strict_types=1);
namespace League\Html_To_Markdown\Converter;

use League\Html_To_Markdown\Coerce;
use League\Html_To_Markdown\Configuration;
use League\Html_To_Markdown\Configuration_Aware_Interface;
use League\Html_To_Markdown\Element_Interface;
use League\Html_To_Markdown\Pre_Converter_Interface;
class Table_Converter implements Converter_Interface, Pre_Converter_Interface, Configuration_Aware_Interface
{
    /** @var Configuration */
    protected $config;
    public function set_config(Configuration $config): void
    {
        $this->config = $config;
    }
    /** @var array<string, string> */
    private static $alignments = ['left' => ':--', 'right' => '--:', 'center' => ':-:'];
    /** @var array<int, string>|null */
    private $column_alignments = [];
    /** @var string|null */
    private $caption;
    public function pre_convert(Element_Interface $element): void
    {
        $tag = $element->get_tag_name();
        // Only table cells and caption are allowed to contain content.
        // Remove all text between other table elements.
        if ($tag === 'th' || $tag === 'td' || $tag === 'caption') {
            return;
        }
        foreach ($element->get_children() as $child) {
            if ($child->is_text()) {
                $child->set_final_markdown('');
            }
        }
    }
    public function convert(Element_Interface $element): string
    {
        $value = $element->get_value();
        switch ($element->get_tag_name()) {
            case 'table':
                $this->column_alignments = [];
                if ($this->caption) {
                    $side = $this->config->get_option('table_caption_side');
                    if ($side === 'top') {
                        $value = $this->caption . "\n" . $value;
                    } elseif ($side === 'bottom') {
                        $value .= $this->caption;
                    }
                    $this->caption = null;
                }
                return $value . "\n";
            case 'caption':
                $this->caption = \trim($value);
                return '';
            case 'tr':
                $value .= "|\n";
                if ($this->column_alignments !== null) {
                    $value .= '|' . \implode('|', $this->column_alignments) . "|\n";
                    $this->column_alignments = null;
                }
                return $value;
            case 'th':
            case 'td':
                if ($this->column_alignments !== null) {
                    $align = $element->get_attribute('align');
                    $this->column_alignments[] = self::$alignments[$align] ?? '---';
                }
                $value = \str_replace("\n", ' ', $value);
                $value = \str_replace('|', Coerce::to_string($this->config->get_option('table_pipe_escape') ?? '\|'), $value);
                return '| ' . \trim($value) . ' ';
            case 'thead':
            case 'tbody':
            case 'tfoot':
            case 'colgroup':
            case 'col':
                return $value;
            default:
                return '';
        }
    }
    /**
     * @return string[]
     */
    public function get_supported_tags(): array
    {
        return ['table', 'tr', 'th', 'td', 'thead', 'tbody', 'tfoot', 'colgroup', 'col', 'caption'];
    }
}
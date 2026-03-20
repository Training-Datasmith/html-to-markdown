<?php

declare (strict_types=1);
namespace League\Html_To_Markdown;

use League\Html_To_Markdown\Converter\Blockquote_Converter;
use League\Html_To_Markdown\Converter\Code_Converter;
use League\Html_To_Markdown\Converter\Comment_Converter;
use League\Html_To_Markdown\Converter\Converter_Interface;
use League\Html_To_Markdown\Converter\Default_Converter;
use League\Html_To_Markdown\Converter\Div_Converter;
use League\Html_To_Markdown\Converter\Emphasis_Converter;
use League\Html_To_Markdown\Converter\Hard_Break_Converter;
use League\Html_To_Markdown\Converter\Header_Converter;
use League\Html_To_Markdown\Converter\Horizontal_Rule_Converter;
use League\Html_To_Markdown\Converter\Image_Converter;
use League\Html_To_Markdown\Converter\Link_Converter;
use League\Html_To_Markdown\Converter\List_Block_Converter;
use League\Html_To_Markdown\Converter\List_Item_Converter;
use League\Html_To_Markdown\Converter\Paragraph_Converter;
use League\Html_To_Markdown\Converter\Preformatted_Converter;
use League\Html_To_Markdown\Converter\Text_Converter;
final class Environment
{
    /** @var Configuration */
    protected $config;
    /** @var ConverterInterface[] */
    protected $converters = [];
    /**
     * @param array<string, mixed> $config
     */
    public function __construct(array $config = [])
    {
        $this->config = new Configuration($config);
        $this->add_converter(new Default_Converter());
    }
    public function get_config(): Configuration
    {
        return $this->config;
    }
    public function add_converter(Converter_Interface $converter): void
    {
        if ($converter instanceof Configuration_Aware_Interface) {
            $converter->set_config($this->config);
        }
        foreach ($converter->get_supported_tags() as $tag) {
            $this->converters[$tag] = $converter;
        }
    }
    public function get_converter_by_tag(string $tag): Converter_Interface
    {
        return $this->converters[$tag] ?? $this->converters[Default_Converter::DEFAULT_CONVERTER];
    }
    /**
     * @param array<string, mixed> $config
     */
    public static function create_default_environment(array $config = []): Environment
    {
        $environment = new static($config);
        $environment->add_converter(new Blockquote_Converter());
        $environment->add_converter(new Code_Converter());
        $environment->add_converter(new Comment_Converter());
        $environment->add_converter(new Div_Converter());
        $environment->add_converter(new Emphasis_Converter());
        $environment->add_converter(new Hard_Break_Converter());
        $environment->add_converter(new Header_Converter());
        $environment->add_converter(new Horizontal_Rule_Converter());
        $environment->add_converter(new Image_Converter());
        $environment->add_converter(new Link_Converter());
        $environment->add_converter(new List_Block_Converter());
        $environment->add_converter(new List_Item_Converter());
        $environment->add_converter(new Paragraph_Converter());
        $environment->add_converter(new Preformatted_Converter());
        $environment->add_converter(new Text_Converter());
        return $environment;
    }
}
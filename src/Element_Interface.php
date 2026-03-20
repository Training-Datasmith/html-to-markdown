<?php

declare (strict_types=1);
namespace League\Html_To_Markdown;

interface Element_Interface
{
    public function is_block(): bool;
    public function is_text(): bool;
    public function is_whitespace(): bool;
    public function get_tag_name(): string;
    public function get_value(): string;
    public function has_parent(): bool;
    public function get_parent(): ?Element_Interface;
    public function get_next_sibling(): ?Element_Interface;
    public function get_previous_sibling(): ?Element_Interface;
    /**
     * @param string|string[] $tagNames
     */
    public function is_descendant_of($tag_names): bool;
    public function has_children(): bool;
    /**
     * @return ElementInterface[]
     */
    public function get_children(): array;
    public function get_next(): ?Element_Interface;
    public function get_sibling_position(): int;
    public function get_children_as_string(): string;
    public function set_final_markdown(string $markdown): void;
    public function get_list_item_level(): int;
    public function get_attribute(string $name): string;
}
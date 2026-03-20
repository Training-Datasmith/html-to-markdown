<?php

declare (strict_types=1);
namespace League\Html_To_Markdown;

class Element implements Element_Interface
{
    /** @var \DOMNode */
    protected $node;
    /** @var ElementInterface|null */
    private $next_cached;
    /** @var \DOMNode|null */
    private $previous_sibling_cached;
    public function __construct(\Dom_Node $node)
    {
        $this->node = $node;
        $this->previous_sibling_cached = $this->node->previous_sibling;
    }
    public function is_block(): bool
    {
        switch ($this->get_tag_name()) {
            case 'blockquote':
            case 'body':
            case 'div':
            case 'h1':
            case 'h2':
            case 'h3':
            case 'h4':
            case 'h5':
            case 'h6':
            case 'hr':
            case 'html':
            case 'li':
            case 'p':
            case 'ol':
            case 'ul':
                return true;
            default:
                return false;
        }
    }
    public function is_text(): bool
    {
        return $this->get_tag_name() === '#text';
    }
    public function is_whitespace(): bool
    {
        return $this->get_tag_name() === '#text' && \trim($this->get_value()) === '';
    }
    public function get_tag_name(): string
    {
        return $this->node->node_name;
    }
    public function get_value(): string
    {
        return $this->node->node_value ?? '';
    }
    public function has_parent(): bool
    {
        return $this->node->parent_node !== null;
    }
    public function get_parent(): ?Element_Interface
    {
        return $this->node->parent_node ? new self($this->node->parent_node) : null;
    }
    public function get_next_sibling(): ?Element_Interface
    {
        return $this->node->next_sibling !== null ? new self($this->node->next_sibling) : null;
    }
    public function get_previous_sibling(): ?Element_Interface
    {
        return $this->previous_sibling_cached !== null ? new self($this->previous_sibling_cached) : null;
    }
    public function has_children(): bool
    {
        return $this->node->has_child_nodes();
    }
    /**
     * @return ElementInterface[]
     */
    public function get_children(): array
    {
        $ret = [];
        foreach ($this->node->child_nodes as $node) {
            /** @psalm-suppress RedundantCondition */
            \assert($node instanceof \Dom_Node);
            $ret[] = new self($node);
        }
        return $ret;
    }
    public function get_next(): ?Element_Interface
    {
        if ($this->next_cached === null) {
            $next_node = $this->get_next_node($this->node);
            if ($next_node !== null) {
                $this->next_cached = new self($next_node);
            }
        }
        return $this->next_cached;
    }
    private function get_next_node(\Dom_Node $node, bool $check_children = true): ?\Dom_Node
    {
        if ($check_children && $node->first_child) {
            return $node->first_child;
        }
        if ($node->next_sibling) {
            return $node->next_sibling;
        }
        if ($node->parent_node) {
            return $this->get_next_node($node->parent_node, false);
        }
        return null;
    }
    /**
     * @param string[]|string $tagNames
     */
    public function is_descendant_of($tag_names): bool
    {
        if (!\is_array($tag_names)) {
            $tag_names = [$tag_names];
        }
        for ($p = $this->node->parent_node; $p !== null; $p = $p->parent_node) {
            if (\in_array($p->node_name, $tag_names, true)) {
                return true;
            }
        }
        return false;
    }
    public function set_final_markdown(string $markdown): void
    {
        if ($this->node->owner_document === null) {
            throw new \RuntimeException('Unowned node');
        }
        if ($this->node->parent_node === null) {
            throw new \RuntimeException('Cannot setFinalMarkdown() on a node without a parent');
        }
        $markdown_node = $this->node->owner_document->create_text_node($markdown);
        $this->node->parent_node->replace_child($markdown_node, $this->node);
    }
    public function get_children_as_string(): string
    {
        return $this->node->C14N();
    }
    public function get_sibling_position(): int
    {
        $position = 0;
        $parent = $this->get_parent();
        if ($parent === null) {
            return $position;
        }
        // Loop through all nodes and find the given $node
        foreach ($parent->get_children() as $current_node) {
            if (!$current_node->is_whitespace()) {
                $position++;
            }
            // TODO: Need a less-buggy way of comparing these
            // Perhaps we can somehow ensure that we always have the exact same object and use === instead?
            if ($this->equals($current_node)) {
                break;
            }
        }
        return $position;
    }
    public function get_list_item_level(): int
    {
        $level = 0;
        $parent = $this->get_parent();
        while ($parent !== null && $parent->has_parent()) {
            if ($parent->get_tag_name() === 'li') {
                $level++;
            }
            $parent = $parent->get_parent();
        }
        return $level;
    }
    public function get_attribute(string $name): string
    {
        if ($this->node instanceof \Dom_Element) {
            return $this->node->get_attribute($name);
        }
        return '';
    }
    public function equals(Element_Interface $element): bool
    {
        if ($element instanceof self) {
            return $element->node === $this->node;
        }
        return false;
    }
}
# Architecture: html-to-markdown

## Purpose
Converts HTML strings or fragments into Markdown syntax. Each HTML element type is handled by a dedicated converter class, making it easy to extend or override individual tag behaviour.

## Directory Structure
```
src/
  Html_Converter.php              # Public API — accepts HTML, returns Markdown string
  Html_Converter_Interface.php    # Contract for the converter
  Environment.php                 # Registry of converter instances + configuration
  Configuration.php               # Key/value store for runtime options
  Element.php                     # Wraps a DOMNode with a friendly API
  Element_Interface.php           # Contract for elements
  Coerce.php                      # Type coercion helpers
  Converter/
    Blockquote_Converter.php      # <blockquote> → > …
    Code_Converter.php            # <code>/<pre> → ` ` / ``` blocks
    Comment_Converter.php         # HTML comments (preserve or strip)
    Default_Converter.php         # Fallback — strips unknown tags
    Div_Converter.php
    Emphasis_Converter.php        # <em>/<strong>
    Hard_Break_Converter.php      # <br>
    Header_Converter.php          # <h1>–<h6>
    Image_Converter.php           # <img> → ![]()
    Link_Converter.php            # <a href> → []()
    List_Block_Converter.php      # <ul>/<ol>
    List_Item_Converter.php       # <li>
    Paragraph_Converter.php       # <p>
    Preformatted_Converter.php    # <pre>
    Table_Converter.php           # <table> → GFM pipe tables
    Text_Converter.php            # Text nodes
tests/
```

## Key Design Decisions
- **Strategy pattern per tag** — each converter implements `Converter_Interface::convert(ElementInterface): string`. The `Environment` maps tag names to converter instances.
- **DOMDocument pipeline** — HTML is loaded via `DOMDocument`, traversed depth-first (innermost nodes first), and each node is replaced with a text node containing its Markdown equivalent.
- **Pre-converter hook** — converters may optionally implement `Pre_Converter_Interface` to inspect/modify the DOM *before* child nodes are recursively converted (used by `Table_Converter` to pre-process cells).
- **Configuration object** — all options (header style, bold/italic style, strip_tags, etc.) are centralised in `Configuration` and passed through the `Environment`.

## Extension Points
- Register a custom converter: `$environment->add_converter(new MyTagConverter())`.
- Override an existing converter by registering one that implements the same tag set.
- Implement `Pre_Converter_Interface` for converters that need DOM pre-processing.

## Dependency Flow
```
Html_Converter::convert($html)
  └─ DOMDocument (parse HTML)
       └─ Element tree
            └─ convert_children() [recursive, innermost-first]
                 └─ Environment::get_converter_by_tag()
                      └─ Converter_Interface::convert(Element)
                           → Markdown string replacing original node
```

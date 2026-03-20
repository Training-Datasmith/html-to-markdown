<?php

declare(strict_types=1);

/**
 * Example: basic HTML to Markdown conversion.
 *
 * Run from the html-to-markdown project root:
 *   php examples/basic_conversion.php
 */

require __DIR__ . '/../vendor/autoload.php';

use League\HtmlToMarkdown\HtmlConverter;

$converter = new HtmlConverter();

// --- Basic paragraph and heading ---
$html = '<h1>Hello World</h1><p>This is a <strong>bold</strong> and <em>italic</em> paragraph.</p>';
echo $converter->convert($html);
echo "\n\n";

// --- Links and images ---
$html = '<a href="https://example.com">Visit Example</a>';
echo $converter->convert($html);
echo "\n\n";

// --- Lists ---
$html = '<ul><li>Apples</li><li>Oranges</li><li>Bananas</li></ul>';
echo $converter->convert($html);
echo "\n\n";

// --- Code blocks ---
$html = '<pre><code>echo "Hello";</code></pre>';
echo $converter->convert($html);
echo "\n\n";

// --- Custom options: ATX-style headers ---
$converter->set_options(['header_style' => 'atx']);
$html = '<h2>ATX Style Header</h2>';
echo $converter->convert($html);

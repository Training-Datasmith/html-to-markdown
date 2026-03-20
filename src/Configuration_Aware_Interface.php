<?php

declare (strict_types=1);
namespace League\Html_To_Markdown;

interface Configuration_Aware_Interface
{
    public function set_config(Configuration $config): void;
}
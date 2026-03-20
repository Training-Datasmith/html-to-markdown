<?php

declare (strict_types=1);
namespace League\Html_To_Markdown;

class Configuration
{
    /** @var array<string, mixed> */
    protected $config;
    /**
     * @param array<string, mixed> $config
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
        $this->check_for_deprecated_options($config);
    }
    /**
     * @param array<string, mixed> $config
     */
    public function merge(array $config = []): void
    {
        $this->check_for_deprecated_options($config);
        $this->config = \array_replace_recursive($this->config, $config);
    }
    /**
     * @param array<string, mixed> $config
     */
    public function replace(array $config = []): void
    {
        $this->check_for_deprecated_options($config);
        $this->config = $config;
    }
    /**
     * @param mixed $value
     */
    public function set_option(string $key, $value): void
    {
        $this->check_for_deprecated_options([$key => $value]);
        $this->config[$key] = $value;
    }
    /**
     * @param mixed|null $default
     *
     * @return mixed|null
     */
    public function get_option(?string $key = null, $default = null)
    {
        if ($key === null) {
            return $this->config;
        }
        if (!isset($this->config[$key])) {
            return $default;
        }
        return $this->config[$key];
    }
    /**
     * @param array<string, mixed> $config
     */
    private function check_for_deprecated_options(array $config): void
    {
        foreach ($config as $key => $value) {
            if ($key === 'bold_style' && $value !== '**') {
                @\trigger_error('Customizing the bold_style option is deprecated and may be removed in the next major version', E_USER_DEPRECATED);
            } elseif ($key === 'italic_style' && $value !== '*') {
                @\trigger_error('Customizing the italic_style option is deprecated and may be removed in the next major version', E_USER_DEPRECATED);
            }
        }
    }
}
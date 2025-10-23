<?php
/**
 * Base Component Class
 * All UI components extend this class
 */

abstract class Component {
    protected $props = [];
    protected $classes = '';
    protected $attributes = [];
    protected $id = '';
    protected $style = '';
    
    public function __construct(array $props = []) {
        $this->props = $props;
        $this->classes = $props['class'] ?? '';
        $this->attributes = $props['attributes'] ?? [];
        $this->id = $props['id'] ?? '';
        $this->style = $props['style'] ?? '';
    }
    
    abstract public function render(): string;
    
    protected function buildAttributes(): string {
        $attrs = [];
        
        if ($this->id) {
            $attrs[] = 'id="' . htmlspecialchars($this->id) . '"';
        }
        
        if ($this->classes) {
            $attrs[] = 'class="' . htmlspecialchars($this->classes) . '"';
        }
        
        if ($this->style) {
            $attrs[] = 'style="' . htmlspecialchars($this->style) . '"';
        }
        
        foreach ($this->attributes as $key => $value) {
            $attrs[] = htmlspecialchars($key) . '="' . htmlspecialchars($value) . '"';
        }
        
        return implode(' ', $attrs);
    }
    
    protected function escape($value): string {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
    
    protected function prop($key, $default = null) {
        return $this->props[$key] ?? $default;
    }
    
    protected function buildClass($additionalClasses = ''): string {
        $classes = array_filter([$this->classes, $additionalClasses]);
        return implode(' ', $classes);
    }
    
    protected function generateId($prefix = 'comp'): string {
        if (!$this->id) {
            $this->id = $prefix . '_' . uniqid();
        }
        return $this->id;
    }
}

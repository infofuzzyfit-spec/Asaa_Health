<?php
/**
 * Button Component
 * Reusable button component with various styles and sizes
 */

require_once __DIR__ . '/../Component.php';

class Button extends Component {
    public function render(): string {
        $type = $this->prop('type', 'button');
        $variant = $this->prop('variant', 'primary');
        $size = $this->prop('size', 'md');
        $disabled = $this->prop('disabled', false);
        $loading = $this->prop('loading', false);
        $icon = $this->prop('icon', '');
        $text = $this->prop('text', '');
        $onclick = $this->prop('onclick', '');
        
        $classes = $this->buildClass("btn btn-{$variant} btn-{$size}");
        
        if ($disabled) {
            $classes .= ' disabled';
        }
        
        $attributes = $this->attributes;
        if ($onclick) {
            $attributes['onclick'] = $onclick;
        }
        if ($disabled) {
            $attributes['disabled'] = 'disabled';
        }
        
        $this->attributes = $attributes;
        
        $iconHtml = $icon ? "<i class='{$icon}'></i> " : '';
        $loadingHtml = $loading ? "<span class='spinner-border spinner-border-sm me-2' role='status'></span>" : '';
        
        return "<button type='{$type}' {$this->buildAttributes()}>{$loadingHtml}{$iconHtml}{$this->escape($text)}</button>";
    }
}

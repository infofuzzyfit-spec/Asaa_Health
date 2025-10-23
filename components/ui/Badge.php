<?php
/**
 * Badge Component
 * Reusable badge component for status indicators
 */

require_once __DIR__ . '/../Component.php';

class Badge extends Component {
    public function render(): string {
        $text = $this->prop('text', '');
        $variant = $this->prop('variant', 'primary');
        $size = $this->prop('size', 'md');
        $pill = $this->prop('pill', false);
        $icon = $this->prop('icon', '');
        $clickable = $this->prop('clickable', false);
        $onclick = $this->prop('onclick', '');
        
        $classes = $this->buildClass("badge bg-{$variant} badge-{$size}");
        
        if ($pill) {
            $classes .= ' rounded-pill';
        }
        
        if ($clickable) {
            $classes .= ' badge-clickable';
        }
        
        $attributes = $this->attributes;
        if ($onclick) {
            $attributes['onclick'] = $onclick;
        }
        if ($clickable) {
            $attributes['role'] = 'button';
            $attributes['tabindex'] = '0';
        }
        
        $this->attributes = $attributes;
        
        $iconHtml = $icon ? "<i class='{$icon} me-1'></i>" : '';
        
        return "<span {$this->buildAttributes()}>{$iconHtml}{$this->escape($text)}</span>";
    }
}

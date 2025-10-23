<?php
/**
 * Alert Component
 * Reusable alert component for notifications
 */

require_once __DIR__ . '/../Component.php';

class Alert extends Component {
    public function render(): string {
        $type = $this->prop('type', 'info');
        $message = $this->prop('message', '');
        $title = $this->prop('title', '');
        $dismissible = $this->prop('dismissible', true);
        $icon = $this->prop('icon', '');
        $autoHide = $this->prop('autoHide', false);
        $duration = $this->prop('duration', 5000);
        
        $classes = $this->buildClass("alert alert-{$type}");
        
        if ($dismissible) {
            $classes .= ' alert-dismissible fade show';
        }
        
        $attributes = $this->attributes;
        $attributes['role'] = 'alert';
        
        if ($autoHide) {
            $attributes['data-auto-hide'] = 'true';
            $attributes['data-duration'] = $duration;
        }
        
        $this->attributes = $attributes;
        
        $iconHtml = $icon ? "<i class='{$icon} me-2'></i>" : '';
        $titleHtml = $title ? "<h6 class='alert-heading'>{$this->escape($title)}</h6>" : '';
        $dismissButton = $dismissible ? "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>" : '';
        
        return "<div {$this->buildAttributes()}>{$iconHtml}{$titleHtml}{$this->escape($message)}{$dismissButton}</div>";
    }
}

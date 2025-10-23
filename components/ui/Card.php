<?php
/**
 * Card Component
 * Reusable card component for content display
 */

require_once __DIR__ . '/../Component.php';

class Card extends Component {
    public function render(): string {
        $title = $this->prop('title', '');
        $subtitle = $this->prop('subtitle', '');
        $body = $this->prop('body', '');
        $footer = $this->prop('footer', '');
        $header = $this->prop('header', '');
        $image = $this->prop('image', '');
        $imageAlt = $this->prop('imageAlt', '');
        $variant = $this->prop('variant', 'default');
        $size = $this->prop('size', 'md');
        $hover = $this->prop('hover', false);
        $clickable = $this->prop('clickable', false);
        $onclick = $this->prop('onclick', '');
        
        $classes = $this->buildClass("card card-{$size}");
        
        if ($variant !== 'default') {
            $classes .= " card-{$variant}";
        }
        
        if ($hover) {
            $classes .= ' card-hover';
        }
        
        if ($clickable) {
            $classes .= ' card-clickable';
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
        
        $imageHtml = $image ? "<img src='{$this->escape($image)}' class='card-img-top' alt='{$this->escape($imageAlt)}'>" : '';
        $titleHtml = $title ? "<h5 class='card-title'>{$this->escape($title)}</h5>" : '';
        $subtitleHtml = $subtitle ? "<h6 class='card-subtitle mb-2 text-muted'>{$this->escape($subtitle)}</h6>" : '';
        $headerHtml = $header ? "<div class='card-header'>{$this->escape($header)}</div>" : '';
        $bodyHtml = $body ? "<div class='card-body'>{$body}</div>" : '';
        $footerHtml = $footer ? "<div class='card-footer'>{$this->escape($footer)}</div>" : '';
        
        return "<div {$this->buildAttributes()}>{$imageHtml}{$headerHtml}{$bodyHtml}{$titleHtml}{$subtitleHtml}{$footerHtml}</div>";
    }
}

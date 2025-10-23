<?php
/**
 * Input Component
 * Reusable input component with validation and styling
 */

require_once __DIR__ . '/../Component.php';

class Input extends Component {
    public function render(): string {
        $type = $this->prop('type', 'text');
        $name = $this->prop('name', '');
        $value = $this->prop('value', '');
        $placeholder = $this->prop('placeholder', '');
        $required = $this->prop('required', false);
        $disabled = $this->prop('disabled', false);
        $readonly = $this->prop('readonly', false);
        $label = $this->prop('label', '');
        $helpText = $this->prop('helpText', '');
        $error = $this->prop('error', '');
        $icon = $this->prop('icon', '');
        $size = $this->prop('size', 'md');
        
        $classes = $this->buildClass("form-control form-control-{$size}");
        
        if ($error) {
            $classes .= ' is-invalid';
        }
        
        $attributes = $this->attributes;
        $attributes['type'] = $type;
        $attributes['name'] = $name;
        $attributes['value'] = $value;
        $attributes['placeholder'] = $placeholder;
        
        if ($required) {
            $attributes['required'] = 'required';
        }
        if ($disabled) {
            $attributes['disabled'] = 'disabled';
        }
        if ($readonly) {
            $attributes['readonly'] = 'readonly';
        }
        
        $this->attributes = $attributes;
        
        $inputId = $this->generateId('input');
        $attributes['id'] = $inputId;
        $this->attributes = $attributes;
        
        $iconHtml = $icon ? "<i class='{$icon} input-icon'></i>" : '';
        $errorHtml = $error ? "<div class='invalid-feedback'>{$this->escape($error)}</div>" : '';
        $helpHtml = $helpText ? "<div class='form-text'>{$this->escape($helpText)}</div>" : '';
        $labelHtml = $label ? "<label for='{$inputId}' class='form-label'>{$this->escape($label)}</label>" : '';
        
        $inputHtml = "<input {$this->buildAttributes()}>";
        
        if ($icon) {
            $inputHtml = "<div class='input-group'>{$iconHtml}{$inputHtml}</div>";
        }
        
        return "<div class='mb-3'>{$labelHtml}{$inputHtml}{$errorHtml}{$helpHtml}</div>";
    }
}

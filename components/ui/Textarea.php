<?php
/**
 * Textarea Component
 * Reusable textarea component
 */

require_once __DIR__ . '/../Component.php';

class Textarea extends Component {
    public function render(): string {
        $name = $this->prop('name', '');
        $value = $this->prop('value', '');
        $placeholder = $this->prop('placeholder', '');
        $required = $this->prop('required', false);
        $disabled = $this->prop('disabled', false);
        $readonly = $this->prop('readonly', false);
        $label = $this->prop('label', '');
        $helpText = $this->prop('helpText', '');
        $error = $this->prop('error', '');
        $rows = $this->prop('rows', 3);
        $maxLength = $this->prop('maxLength', '');
        
        $classes = $this->buildClass('form-control');
        
        if ($error) {
            $classes .= ' is-invalid';
        }
        
        $attributes = $this->attributes;
        $attributes['name'] = $name;
        $attributes['placeholder'] = $placeholder;
        $attributes['rows'] = $rows;
        
        if ($required) {
            $attributes['required'] = 'required';
        }
        if ($disabled) {
            $attributes['disabled'] = 'disabled';
        }
        if ($readonly) {
            $attributes['readonly'] = 'readonly';
        }
        if ($maxLength) {
            $attributes['maxlength'] = $maxLength;
        }
        
        $this->attributes = $attributes;
        
        $textareaId = $this->generateId('textarea');
        $attributes['id'] = $textareaId;
        $this->attributes = $attributes;
        
        $errorHtml = $error ? "<div class='invalid-feedback'>{$this->escape($error)}</div>" : '';
        $helpHtml = $helpText ? "<div class='form-text'>{$this->escape($helpText)}</div>" : '';
        $labelHtml = $label ? "<label for='{$textareaId}' class='form-label'>{$this->escape($label)}</label>" : '';
        
        return "<div class='mb-3'>{$labelHtml}<textarea {$this->buildAttributes()}>{$this->escape($value)}</textarea>{$errorHtml}{$helpHtml}</div>";
    }
}

<?php
/**
 * Select Component
 * Reusable select dropdown component
 */

require_once __DIR__ . '/../Component.php';

class Select extends Component {
    public function render(): string {
        $name = $this->prop('name', '');
        $value = $this->prop('value', '');
        $options = $this->prop('options', []);
        $placeholder = $this->prop('placeholder', 'Select an option');
        $required = $this->prop('required', false);
        $disabled = $this->prop('disabled', false);
        $multiple = $this->prop('multiple', false);
        $label = $this->prop('label', '');
        $helpText = $this->prop('helpText', '');
        $error = $this->prop('error', '');
        $size = $this->prop('size', 'md');
        $searchable = $this->prop('searchable', false);
        
        $classes = $this->buildClass("form-select form-select-{$size}");
        
        if ($error) {
            $classes .= ' is-invalid';
        }
        
        $attributes = $this->attributes;
        $attributes['name'] = $name;
        $attributes['class'] = $classes;
        
        if ($required) {
            $attributes['required'] = 'required';
        }
        if ($disabled) {
            $attributes['disabled'] = 'disabled';
        }
        if ($multiple) {
            $attributes['multiple'] = 'multiple';
        }
        
        $this->attributes = $attributes;
        
        $selectId = $this->generateId('select');
        $attributes['id'] = $selectId;
        $this->attributes = $attributes;
        
        $optionsHtml = '';
        
        if ($placeholder && !$multiple) {
            $optionsHtml .= "<option value=''>{$this->escape($placeholder)}</option>";
        }
        
        foreach ($options as $option) {
            if (is_array($option)) {
                $optionValue = $option['value'] ?? '';
                $optionText = $option['text'] ?? '';
                $selected = ($optionValue == $value) ? 'selected' : '';
                $optionsHtml .= "<option value='{$this->escape($optionValue)}' {$selected}>{$this->escape($optionText)}</option>";
            } else {
                $selected = ($option == $value) ? 'selected' : '';
                $optionsHtml .= "<option value='{$this->escape($option)}' {$selected}>{$this->escape($option)}</option>";
            }
        }
        
        $errorHtml = $error ? "<div class='invalid-feedback'>{$this->escape($error)}</div>" : '';
        $helpHtml = $helpText ? "<div class='form-text'>{$this->escape($helpText)}</div>" : '';
        $labelHtml = $label ? "<label for='{$selectId}' class='form-label'>{$this->escape($label)}</label>" : '';
        
        $selectHtml = "<select {$this->buildAttributes()}>{$optionsHtml}</select>";
        
        if ($searchable) {
            $selectHtml = "<div class='select2-wrapper'>{$selectHtml}</div>";
        }
        
        return "<div class='mb-3'>{$labelHtml}{$selectHtml}{$errorHtml}{$helpHtml}</div>";
    }
}

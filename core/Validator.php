<?php
/**
 * Validator Class
 * Input validation and sanitization
 */

class Validator {
    private $errors = [];
    private $data = [];
    
    public function __construct($data = []) {
        $this->data = $data;
    }
    
    public function validate($rules) {
        $this->errors = [];
        
        foreach ($rules as $field => $rule) {
            $value = $this->data[$field] ?? null;
            $ruleArray = explode('|', $rule);
            
            foreach ($ruleArray as $singleRule) {
                $this->applyRule($field, $value, $singleRule);
            }
        }
        
        return empty($this->errors);
    }
    
    private function applyRule($field, $value, $rule) {
        $ruleParts = explode(':', $rule);
        $ruleName = $ruleParts[0];
        $ruleValue = $ruleParts[1] ?? null;
        
        switch ($ruleName) {
            case 'required':
                if (empty($value)) {
                    $this->addError($field, "The {$field} field is required.");
                }
                break;
                
            case 'email':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, "The {$field} must be a valid email address.");
                }
                break;
                
            case 'min':
                if (!empty($value) && strlen($value) < $ruleValue) {
                    $this->addError($field, "The {$field} must be at least {$ruleValue} characters.");
                }
                break;
                
            case 'max':
                if (!empty($value) && strlen($value) > $ruleValue) {
                    $this->addError($field, "The {$field} must not exceed {$ruleValue} characters.");
                }
                break;
                
            case 'numeric':
                if (!empty($value) && !is_numeric($value)) {
                    $this->addError($field, "The {$field} must be numeric.");
                }
                break;
                
            case 'date':
                if (!empty($value) && !strtotime($value)) {
                    $this->addError($field, "The {$field} must be a valid date.");
                }
                break;
                
            case 'in':
                $allowedValues = explode(',', $ruleValue);
                if (!empty($value) && !in_array($value, $allowedValues)) {
                    $this->addError($field, "The {$field} must be one of: " . implode(', ', $allowedValues));
                }
                break;
        }
    }
    
    private function addError($field, $message) {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }
        $this->errors[$field][] = $message;
    }
    
    public function errors() {
        return $this->errors;
    }
    
    public function hasErrors() {
        return !empty($this->errors);
    }
    
    public function firstError($field) {
        return $this->errors[$field][0] ?? null;
    }
    
    public function sanitize($value) {
        return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
    }
}

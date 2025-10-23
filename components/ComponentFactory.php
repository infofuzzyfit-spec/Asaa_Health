<?php
/**
 * Component Factory
 * Factory class for creating UI components
 */

class ComponentFactory {
    private static $components = [
        'button' => 'Button',
        'input' => 'Input',
        'select' => 'Select',
        'textarea' => 'Textarea',
        'card' => 'Card',
        'modal' => 'Modal',
        'alert' => 'Alert',
        'badge' => 'Badge',
        'table' => 'Table',
        'header' => 'Header',
        'sidebar' => 'Sidebar',
        'footer' => 'Footer',
        'loginForm' => 'LoginForm',
        'registerForm' => 'RegisterForm',
        'appointmentForm' => 'AppointmentForm',
        'lineChart' => 'LineChart',
        'barChart' => 'BarChart',
        'pieChart' => 'PieChart'
    ];
    
    public static function create($type, $props = []) {
        if (!isset(self::$components[$type])) {
            throw new Exception("Component type '{$type}' not found");
        }
        
        $componentClass = self::$components[$type];
        $componentPath = __DIR__ . '/ui/' . $componentClass . '.php';
        
        if (strpos($type, 'Form') !== false) {
            $componentPath = __DIR__ . '/forms/' . $componentClass . '.php';
        } elseif (strpos($type, 'Chart') !== false) {
            $componentPath = __DIR__ . '/charts/' . $componentClass . '.php';
        } elseif (in_array($type, ['header', 'sidebar', 'footer'])) {
            $componentPath = __DIR__ . '/layout/' . $componentClass . '.php';
        }
        
        if (!file_exists($componentPath)) {
            throw new Exception("Component file not found: {$componentPath}");
        }
        
        require_once $componentPath;
        
        return new $componentClass($props);
    }
    
    public static function render($type, $props = []) {
        $component = self::create($type, $props);
        return $component->render();
    }
    
    public static function getAvailableComponents() {
        return array_keys(self::$components);
    }
}

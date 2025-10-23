<?php
/**
 * Component Helper
 * Helper functions for common component operations
 */

require_once __DIR__ . '/../ComponentFactory.php';

class ComponentHelper {
    
    /**
     * Create a status badge
     */
    public static function statusBadge($status, $type = 'default') {
        $statusMap = [
            'REVIEW' => ['text' => 'Under Review', 'variant' => 'warning'],
            'ACCEPTED' => ['text' => 'Accepted', 'variant' => 'success'],
            'CONSULTING' => ['text' => 'In Progress', 'variant' => 'info'],
            'COMPLETED' => ['text' => 'Completed', 'variant' => 'success'],
            'CANCELLED' => ['text' => 'Cancelled', 'variant' => 'danger'],
            'PENDING' => ['text' => 'Pending', 'variant' => 'warning'],
            'ACTIVE' => ['text' => 'Active', 'variant' => 'success'],
            'INACTIVE' => ['text' => 'Inactive', 'variant' => 'secondary'],
            'SUSPENDED' => ['text' => 'Suspended', 'variant' => 'danger']
        ];
        
        $statusInfo = $statusMap[$status] ?? ['text' => $status, 'variant' => 'secondary'];
        
        return ComponentFactory::render('badge', [
            'text' => $statusInfo['text'],
            'variant' => $statusInfo['variant']
        ]);
    }
    
    /**
     * Create a user avatar
     */
    public static function userAvatar($user, $size = 'md') {
        $name = $user['first_name'] . ' ' . $user['last_name'];
        $initials = strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1));
        
        return "<div class='user-avatar avatar-{$size}' title='{$name}'>
                    <span class='avatar-text'>{$initials}</span>
                </div>";
    }
    
    /**
     * Create a loading spinner
     */
    public static function loadingSpinner($text = 'Loading...', $size = 'md') {
        return "<div class='loading-spinner text-center'>
                    <div class='spinner-border spinner-border-{$size}' role='status'>
                        <span class='visually-hidden'>{$text}</span>
                    </div>
                    <p class='mt-2'>{$text}</p>
                </div>";
    }
    
    /**
     * Create a confirmation modal
     */
    public static function confirmationModal($id, $title, $message, $confirmText = 'Confirm', $cancelText = 'Cancel') {
        return ComponentFactory::render('modal', [
            'id' => $id,
            'title' => $title,
            'body' => "<p>{$message}</p>",
            'footer' => "
                <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>{$cancelText}</button>
                <button type='button' class='btn btn-danger' id='{$id}_confirm'>{$confirmText}</button>
            "
        ]);
    }
    
    /**
     * Create a data table with common actions
     */
    public static function dataTable($id, $headers, $data, $actions = []) {
        $defaultActions = [
            ['text' => 'View', 'icon' => 'fas fa-eye', 'class' => 'btn btn-sm btn-outline-primary', 'onclick' => 'viewRecord({{id}})'],
            ['text' => 'Edit', 'icon' => 'fas fa-edit', 'class' => 'btn btn-sm btn-outline-secondary', 'onclick' => 'editRecord({{id}})'],
            ['text' => 'Delete', 'icon' => 'fas fa-trash', 'class' => 'btn btn-sm btn-outline-danger', 'onclick' => 'deleteRecord({{id}})']
        ];
        
        $actions = !empty($actions) ? $actions : $defaultActions;
        
        return ComponentFactory::render('table', [
            'id' => $id,
            'headers' => $headers,
            'data' => $data,
            'actions' => $actions,
            'pagination' => true,
            'sortable' => true,
            'responsive' => true
        ]);
    }
    
    /**
     * Create a dashboard card
     */
    public static function dashboardCard($title, $value, $icon, $color = 'primary', $trend = null) {
        $trendHtml = '';
        if ($trend) {
            $trendClass = $trend['direction'] === 'up' ? 'text-success' : 'text-danger';
            $trendIcon = $trend['direction'] === 'up' ? 'fas fa-arrow-up' : 'fas fa-arrow-down';
            $trendHtml = "<small class='{$trendClass}'><i class='{$trendIcon}'></i> {$trend['value']}%</small>";
        }
        
        return ComponentFactory::render('card', [
            'body' => "
                <div class='d-flex justify-content-between align-items-center'>
                    <div>
                        <h6 class='card-title text-muted'>{$title}</h6>
                        <h3 class='mb-0'>{$value}</h3>
                        {$trendHtml}
                    </div>
                    <div class='text-{$color}'>
                        <i class='{$icon} fa-2x'></i>
                    </div>
                </div>
            "
        ]);
    }
    
    /**
     * Create a form field with validation
     */
    public static function formField($type, $name, $label, $props = []) {
        $defaultProps = [
            'name' => $name,
            'label' => $label,
            'required' => true
        ];
        
        $props = array_merge($defaultProps, $props);
        
        return ComponentFactory::render($type, $props);
    }
    
    /**
     * Create a success alert
     */
    public static function successAlert($message, $title = 'Success') {
        return ComponentFactory::render('alert', [
            'type' => 'success',
            'title' => $title,
            'message' => $message,
            'icon' => 'fas fa-check-circle'
        ]);
    }
    
    /**
     * Create an error alert
     */
    public static function errorAlert($message, $title = 'Error') {
        return ComponentFactory::render('alert', [
            'type' => 'danger',
            'title' => $title,
            'message' => $message,
            'icon' => 'fas fa-exclamation-circle'
        ]);
    }
    
    /**
     * Create a warning alert
     */
    public static function warningAlert($message, $title = 'Warning') {
        return ComponentFactory::render('alert', [
            'type' => 'warning',
            'title' => $title,
            'message' => $message,
            'icon' => 'fas fa-exclamation-triangle'
        ]);
    }
    
    /**
     * Create an info alert
     */
    public static function infoAlert($message, $title = 'Info') {
        return ComponentFactory::render('alert', [
            'type' => 'info',
            'title' => $title,
            'message' => $message,
            'icon' => 'fas fa-info-circle'
        ]);
    }
}

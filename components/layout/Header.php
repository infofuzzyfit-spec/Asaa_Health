<?php
/**
 * Header Component
 * Main navigation header component
 */

require_once __DIR__ . '/../Component.php';

class Header extends Component {
    public function render(): string {
        $brand = $this->prop('brand', 'ASAA Healthcare');
        $brandUrl = $this->prop('brandUrl', '/');
        $user = $this->prop('user', []);
        $menuItems = $this->prop('menuItems', []);
        $showUserMenu = $this->prop('showUserMenu', true);
        $showNotifications = $this->prop('showNotifications', true);
        $notifications = $this->prop('notifications', []);
        
        $classes = $this->buildClass('navbar navbar-expand-lg navbar-dark bg-primary');
        
        $attributes = $this->attributes;
        $attributes['class'] = $classes;
        $this->attributes = $attributes;
        
        $brandHtml = "<a class='navbar-brand' href='{$brandUrl}'>{$this->escape($brand)}</a>";
        
        $menuHtml = '';
        if (!empty($menuItems)) {
            $menuHtml = '<ul class="navbar-nav me-auto mb-2 mb-lg-0">';
            foreach ($menuItems as $item) {
                $active = $item['active'] ?? false;
                $activeClass = $active ? 'active' : '';
                $menuHtml .= "<li class='nav-item'><a class='nav-link {$activeClass}' href='{$item['url']}'>{$this->escape($item['text'])}</a></li>";
            }
            $menuHtml .= '</ul>';
        }
        
        $userMenuHtml = '';
        if ($showUserMenu && !empty($user)) {
            $userName = $user['first_name'] . ' ' . $user['last_name'];
            $userRole = $user['role'] ?? '';
            
            $userMenuHtml = "
            <div class='dropdown'>
                <a class='nav-link dropdown-toggle' href='#' role='button' data-bs-toggle='dropdown' aria-expanded='false'>
                    <i class='fas fa-user me-1'></i>
                    {$this->escape($userName)}
                </a>
                <ul class='dropdown-menu dropdown-menu-end'>
                    <li><h6 class='dropdown-header'>{$this->escape($userRole)}</h6></li>
                    <li><hr class='dropdown-divider'></li>
                    <li><a class='dropdown-item' href='/profile'><i class='fas fa-user me-2'></i>Profile</a></li>
                    <li><a class='dropdown-item' href='/settings'><i class='fas fa-cog me-2'></i>Settings</a></li>
                    <li><hr class='dropdown-divider'></li>
                    <li><a class='dropdown-item' href='/logout'><i class='fas fa-sign-out-alt me-2'></i>Logout</a></li>
                </ul>
            </div>";
        }
        
        $notificationHtml = '';
        if ($showNotifications) {
            $notificationCount = count($notifications);
            $notificationBadge = $notificationCount > 0 ? "<span class='badge bg-danger'>{$notificationCount}</span>" : '';
            
            $notificationHtml = "
            <div class='dropdown me-3'>
                <a class='nav-link dropdown-toggle' href='#' role='button' data-bs-toggle='dropdown' aria-expanded='false'>
                    <i class='fas fa-bell'></i>
                    {$notificationBadge}
                </a>
                <ul class='dropdown-menu dropdown-menu-end notification-dropdown'>
                    <li><h6 class='dropdown-header'>Notifications</h6></li>
                    <li><hr class='dropdown-divider'></li>
                    " . $this->buildNotificationItems($notifications) . "
                </ul>
            </div>";
        }
        
        return "
        <nav {$this->buildAttributes()}>
            <div class='container-fluid'>
                {$brandHtml}
                <button class='navbar-toggler' type='button' data-bs-toggle='collapse' data-bs-target='#navbarNav' aria-controls='navbarNav' aria-expanded='false' aria-label='Toggle navigation'>
                    <span class='navbar-toggler-icon'></span>
                </button>
                <div class='collapse navbar-collapse' id='navbarNav'>
                    {$menuHtml}
                    <div class='navbar-nav ms-auto'>
                        {$notificationHtml}
                        {$userMenuHtml}
                    </div>
                </div>
            </div>
        </nav>";
    }
    
    private function buildNotificationItems($notifications): string {
        if (empty($notifications)) {
            return "<li><span class='dropdown-item-text text-muted'>No notifications</span></li>";
        }
        
        $html = '';
        foreach ($notifications as $notification) {
            $time = $notification['time'] ?? '';
            $message = $notification['message'] ?? '';
            $type = $notification['type'] ?? 'info';
            $icon = $this->getNotificationIcon($type);
            
            $html .= "
            <li>
                <a class='dropdown-item notification-item' href='#'>
                    <div class='d-flex'>
                        <div class='notification-icon me-3'>
                            <i class='{$icon}'></i>
                        </div>
                        <div class='notification-content'>
                            <div class='notification-message'>{$this->escape($message)}</div>
                            <small class='text-muted'>{$this->escape($time)}</small>
                        </div>
                    </div>
                </a>
            </li>";
        }
        
        return $html;
    }
    
    private function getNotificationIcon($type): string {
        $icons = [
            'success' => 'fas fa-check-circle text-success',
            'error' => 'fas fa-exclamation-circle text-danger',
            'warning' => 'fas fa-exclamation-triangle text-warning',
            'info' => 'fas fa-info-circle text-info',
            'appointment' => 'fas fa-calendar text-primary',
            'payment' => 'fas fa-credit-card text-success',
            'medical' => 'fas fa-stethoscope text-info'
        ];
        
        return $icons[$type] ?? $icons['info'];
    }
}

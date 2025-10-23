<?php
/**
 * Sidebar Component
 * Navigation sidebar component
 */

require_once __DIR__ . '/../Component.php';

class Sidebar extends Component {
    public function render(): string {
        $menuItems = $this->prop('menuItems', []);
        $userRole = $this->prop('userRole', 'Patient');
        $collapsed = $this->prop('collapsed', false);
        $showBrand = $this->prop('showBrand', true);
        $brand = $this->prop('brand', 'ASAA Healthcare');
        
        $classes = $this->buildClass('sidebar');
        if ($collapsed) {
            $classes .= ' collapsed';
        }
        
        $attributes = $this->attributes;
        $attributes['class'] = $classes;
        $this->attributes = $attributes;
        
        $brandHtml = '';
        if ($showBrand) {
            $brandHtml = "
            <div class='sidebar-brand'>
                <a href='/' class='brand-link'>
                    <i class='fas fa-hospital'></i>
                    <span class='brand-text'>{$this->escape($brand)}</span>
                </a>
            </div>";
        }
        
        $menuHtml = $this->buildMenuItems($menuItems, $userRole);
        
        return "
        <aside {$this->buildAttributes()}>
            {$brandHtml}
            <nav class='sidebar-nav'>
                {$menuHtml}
            </nav>
        </aside>";
    }
    
    private function buildMenuItems($menuItems, $userRole): string {
        if (empty($menuItems)) {
            $menuItems = $this->getDefaultMenuItems($userRole);
        }
        
        $html = '<ul class="nav nav-pills flex-column">';
        
        foreach ($menuItems as $item) {
            $active = $item['active'] ?? false;
            $activeClass = $active ? 'active' : '';
            $icon = $item['icon'] ?? '';
            $text = $item['text'] ?? '';
            $url = $item['url'] ?? '#';
            $badge = $item['badge'] ?? '';
            $children = $item['children'] ?? [];
            
            $badgeHtml = $badge ? "<span class='badge bg-primary ms-auto'>{$this->escape($badge)}</span>" : '';
            
            if (!empty($children)) {
                $html .= "
                <li class='nav-item'>
                    <a class='nav-link {$activeClass}' data-bs-toggle='collapse' href='#{$item['id']}' role='button' aria-expanded='false'>
                        <i class='{$icon}'></i>
                        <span class='nav-text'>{$this->escape($text)}</span>
                        {$badgeHtml}
                        <i class='fas fa-chevron-down ms-auto'></i>
                    </a>
                    <div class='collapse' id='{$item['id']}'>
                        <ul class='nav nav-pills flex-column ms-3'>";
                
                foreach ($children as $child) {
                    $childActive = $child['active'] ?? false;
                    $childActiveClass = $childActive ? 'active' : '';
                    $childIcon = $child['icon'] ?? '';
                    $childText = $child['text'] ?? '';
                    $childUrl = $child['url'] ?? '#';
                    
                    $html .= "
                    <li class='nav-item'>
                        <a class='nav-link {$childActiveClass}' href='{$childUrl}'>
                            <i class='{$childIcon}'></i>
                            <span class='nav-text'>{$this->escape($childText)}</span>
                        </a>
                    </li>";
                }
                
                $html .= '</ul></div></li>';
            } else {
                $html .= "
                <li class='nav-item'>
                    <a class='nav-link {$activeClass}' href='{$url}'>
                        <i class='{$icon}'></i>
                        <span class='nav-text'>{$this->escape($text)}</span>
                        {$badgeHtml}
                    </a>
                </li>";
            }
        }
        
        $html .= '</ul>';
        return $html;
    }
    
    private function getDefaultMenuItems($userRole): array {
        $menus = [
            'Admin' => [
                ['text' => 'Dashboard', 'icon' => 'fas fa-tachometer-alt', 'url' => '/admin/dashboard'],
                ['text' => 'Users', 'icon' => 'fas fa-users', 'url' => '/admin/users'],
                ['text' => 'Appointments', 'icon' => 'fas fa-calendar', 'url' => '/admin/appointments'],
                ['text' => 'Medical Records', 'icon' => 'fas fa-file-medical', 'url' => '/admin/medical-records'],
                ['text' => 'Payments', 'icon' => 'fas fa-credit-card', 'url' => '/admin/payments'],
                ['text' => 'Reports', 'icon' => 'fas fa-chart-bar', 'url' => '/admin/reports'],
                ['text' => 'Settings', 'icon' => 'fas fa-cog', 'url' => '/admin/settings']
            ],
            'Staff' => [
                ['text' => 'Dashboard', 'icon' => 'fas fa-tachometer-alt', 'url' => '/staff/dashboard'],
                ['text' => 'Appointments', 'icon' => 'fas fa-calendar', 'url' => '/staff/appointments'],
                ['text' => 'Patients', 'icon' => 'fas fa-user-injured', 'url' => '/staff/patients'],
                ['text' => 'Payments', 'icon' => 'fas fa-credit-card', 'url' => '/staff/payments'],
                ['text' => 'Reports', 'icon' => 'fas fa-chart-bar', 'url' => '/staff/reports']
            ],
            'Doctor' => [
                ['text' => 'Dashboard', 'icon' => 'fas fa-tachometer-alt', 'url' => '/doctor/dashboard'],
                ['text' => 'My Appointments', 'icon' => 'fas fa-calendar', 'url' => '/doctor/appointments'],
                ['text' => 'Patients', 'icon' => 'fas fa-user-injured', 'url' => '/doctor/patients'],
                ['text' => 'Medical Records', 'icon' => 'fas fa-file-medical', 'url' => '/doctor/medical-records'],
                ['text' => 'Schedule', 'icon' => 'fas fa-clock', 'url' => '/doctor/schedule']
            ],
            'Patient' => [
                ['text' => 'Dashboard', 'icon' => 'fas fa-tachometer-alt', 'url' => '/patient/dashboard'],
                ['text' => 'My Appointments', 'icon' => 'fas fa-calendar', 'url' => '/patient/appointments'],
                ['text' => 'Book Appointment', 'icon' => 'fas fa-plus-circle', 'url' => '/patient/book-appointment'],
                ['text' => 'Medical Records', 'icon' => 'fas fa-file-medical', 'url' => '/patient/medical-records'],
                ['text' => 'Payments', 'icon' => 'fas fa-credit-card', 'url' => '/patient/payments']
            ]
        ];
        
        return $menus[$userRole] ?? $menus['Patient'];
    }
}

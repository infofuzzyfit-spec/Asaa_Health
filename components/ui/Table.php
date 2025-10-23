<?php
/**
 * Table Component
 * Reusable table component with sorting and pagination
 */

require_once __DIR__ . '/../Component.php';

class Table extends Component {
    public function render(): string {
        $headers = $this->prop('headers', []);
        $data = $this->prop('data', []);
        $striped = $this->prop('striped', true);
        $bordered = $this->prop('bordered', true);
        $hover = $this->prop('hover', true);
        $responsive = $this->prop('responsive', true);
        $sortable = $this->prop('sortable', false);
        $pagination = $this->prop('pagination', false);
        $pageSize = $this->prop('pageSize', 10);
        $currentPage = $this->prop('currentPage', 1);
        $totalItems = $this->prop('totalItems', count($data));
        $actions = $this->prop('actions', []);
        $emptyMessage = $this->prop('emptyMessage', 'No data available');
        
        $classes = $this->buildClass('table');
        
        if ($striped) {
            $classes .= ' table-striped';
        }
        if ($bordered) {
            $classes .= ' table-bordered';
        }
        if ($hover) {
            $classes .= ' table-hover';
        }
        
        $attributes = $this->attributes;
        $attributes['class'] = $classes;
        $this->attributes = $attributes;
        
        $tableId = $this->generateId('table');
        $attributes['id'] = $tableId;
        $this->attributes = $attributes;
        
        // Build header row
        $headerHtml = '';
        if (!empty($headers)) {
            $headerHtml = '<thead class="table-dark"><tr>';
            foreach ($headers as $header) {
                $sortIcon = '';
                if ($sortable && isset($header['sortable']) && $header['sortable']) {
                    $sortIcon = '<i class="fas fa-sort ms-1"></i>';
                }
                $headerHtml .= "<th>{$this->escape($header['text'] ?? $header)}{$sortIcon}</th>";
            }
            if (!empty($actions)) {
                $headerHtml .= '<th>Actions</th>';
            }
            $headerHtml .= '</tr></thead>';
        }
        
        // Build body rows
        $bodyHtml = '<tbody>';
        if (empty($data)) {
            $colspan = count($headers) + (empty($actions) ? 0 : 1);
            $bodyHtml .= "<tr><td colspan='{$colspan}' class='text-center text-muted'>{$this->escape($emptyMessage)}</td></tr>";
        } else {
            foreach ($data as $row) {
                $bodyHtml .= '<tr>';
                foreach ($headers as $header) {
                    $key = $header['key'] ?? $header;
                    $value = $row[$key] ?? '';
                    $bodyHtml .= "<td>{$this->escape($value)}</td>";
                }
                if (!empty($actions)) {
                    $bodyHtml .= '<td>';
                    foreach ($actions as $action) {
                        $icon = $action['icon'] ?? '';
                        $text = $action['text'] ?? '';
                        $onclick = $action['onclick'] ?? '';
                        $class = $action['class'] ?? 'btn btn-sm btn-outline-primary';
                        $bodyHtml .= "<button class='{$class} me-1' onclick='{$onclick}'><i class='{$icon}'></i> {$text}</button>";
                    }
                    $bodyHtml .= '</td>';
                }
                $bodyHtml .= '</tr>';
            }
        }
        $bodyHtml .= '</tbody>';
        
        $tableHtml = "<table {$this->buildAttributes()}>{$headerHtml}{$bodyHtml}</table>";
        
        if ($responsive) {
            $tableHtml = "<div class='table-responsive'>{$tableHtml}</div>";
        }
        
        // Add pagination if enabled
        $paginationHtml = '';
        if ($pagination && $totalItems > $pageSize) {
            $totalPages = ceil($totalItems / $pageSize);
            $paginationHtml = $this->buildPagination($currentPage, $totalPages);
        }
        
        return $tableHtml . $paginationHtml;
    }
    
    private function buildPagination($currentPage, $totalPages): string {
        $paginationHtml = '<nav aria-label="Table pagination"><ul class="pagination justify-content-center">';
        
        // Previous button
        $prevDisabled = $currentPage <= 1 ? 'disabled' : '';
        $paginationHtml .= "<li class='page-item {$prevDisabled}'><a class='page-link' href='#' onclick='changePage(" . ($currentPage - 1) . ")'>Previous</a></li>";
        
        // Page numbers
        $start = max(1, $currentPage - 2);
        $end = min($totalPages, $currentPage + 2);
        
        for ($i = $start; $i <= $end; $i++) {
            $active = $i == $currentPage ? 'active' : '';
            $paginationHtml .= "<li class='page-item {$active}'><a class='page-link' href='#' onclick='changePage({$i})'>{$i}</a></li>";
        }
        
        // Next button
        $nextDisabled = $currentPage >= $totalPages ? 'disabled' : '';
        $paginationHtml .= "<li class='page-item {$nextDisabled}'><a class='page-link' href='#' onclick='changePage(" . ($currentPage + 1) . ")'>Next</a></li>";
        
        $paginationHtml .= '</ul></nav>';
        
        return $paginationHtml;
    }
}

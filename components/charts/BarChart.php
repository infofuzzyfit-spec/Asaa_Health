<?php
/**
 * Bar Chart Component
 * Chart.js bar chart component
 */

require_once __DIR__ . '/../Component.php';

class BarChart extends Component {
    public function render(): string {
        $id = $this->prop('id', $this->generateId('barChart'));
        $data = $this->prop('data', []);
        $labels = $this->prop('labels', []);
        $title = $this->prop('title', '');
        $height = $this->prop('height', '400px');
        $responsive = $this->prop('responsive', true);
        $options = $this->prop('options', []);
        $horizontal = $this->prop('horizontal', false);
        
        $classes = $this->buildClass('chart-container');
        
        $attributes = $this->attributes;
        $attributes['class'] = $classes;
        $attributes['id'] = $id;
        $this->attributes = $attributes;
        
        $titleHtml = $title ? "<h5 class='chart-title'>{$this->escape($title)}</h5>" : '';
        
        $chartOptions = json_encode(array_merge([
            'responsive' => $responsive,
            'maintainAspectRatio' => false,
            'indexAxis' => $horizontal ? 'y' : 'x',
            'scales' => [
                'y' => [
                    'beginAtZero' => true
                ],
                'x' => [
                    'beginAtZero' => true
                ]
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top'
                ]
            ]
        ], $options));
        
        $chartData = json_encode([
            'labels' => $labels,
            'datasets' => $data
        ]);
        
        return "
        <div {$this->buildAttributes()}>
            {$titleHtml}
            <div class='chart-wrapper' style='height: {$height};'>
                <canvas id='{$id}_canvas'></canvas>
            </div>
        </div>
        
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('{$id}_canvas').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {$chartData},
                options: {$chartOptions}
            });
        });
        </script>";
    }
}

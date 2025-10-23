<?php
/**
 * Line Chart Component
 * Chart.js line chart component
 */

require_once __DIR__ . '/../Component.php';

class LineChart extends Component {
    public function render(): string {
        $id = $this->prop('id', $this->generateId('lineChart'));
        $data = $this->prop('data', []);
        $labels = $this->prop('labels', []);
        $title = $this->prop('title', '');
        $height = $this->prop('height', '400px');
        $responsive = $this->prop('responsive', true);
        $options = $this->prop('options', []);
        
        $classes = $this->buildClass('chart-container');
        
        $attributes = $this->attributes;
        $attributes['class'] = $classes;
        $attributes['id'] = $id;
        $this->attributes = $attributes;
        
        $titleHtml = $title ? "<h5 class='chart-title'>{$this->escape($title)}</h5>" : '';
        
        $chartOptions = json_encode(array_merge([
            'responsive' => $responsive,
            'maintainAspectRatio' => false,
            'scales' => [
                'y' => [
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
                type: 'line',
                data: {$chartData},
                options: {$chartOptions}
            });
        });
        </script>";
    }
}

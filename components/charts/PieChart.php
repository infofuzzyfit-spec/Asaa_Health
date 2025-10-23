<?php
/**
 * Pie Chart Component
 * Chart.js pie chart component
 */

require_once __DIR__ . '/../Component.php';

class PieChart extends Component {
    public function render(): string {
        $id = $this->prop('id', $this->generateId('pieChart'));
        $data = $this->prop('data', []);
        $labels = $this->prop('labels', []);
        $title = $this->prop('title', '');
        $height = $this->prop('height', '400px');
        $responsive = $this->prop('responsive', true);
        $options = $this->prop('options', []);
        $doughnut = $this->prop('doughnut', false);
        
        $classes = $this->buildClass('chart-container');
        
        $attributes = $this->attributes;
        $attributes['class'] = $classes;
        $attributes['id'] = $id;
        $this->attributes = $attributes;
        
        $titleHtml = $title ? "<h5 class='chart-title'>{$this->escape($title)}</h5>" : '';
        
        $chartOptions = json_encode(array_merge([
            'responsive' => $responsive,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'right'
                ]
            ]
        ], $options));
        
        $chartData = json_encode([
            'labels' => $labels,
            'datasets' => $data
        ]);
        
        $chartType = $doughnut ? 'doughnut' : 'pie';
        
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
                type: '{$chartType}',
                data: {$chartData},
                options: {$chartOptions}
            });
        });
        </script>";
    }
}

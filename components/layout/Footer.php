<?php
/**
 * Footer Component
 * Main footer component
 */

require_once __DIR__ . '/../Component.php';

class Footer extends Component {
    public function render(): string {
        $copyright = $this->prop('copyright', 'ASAA Healthcare Management System');
        $year = $this->prop('year', date('Y'));
        $links = $this->prop('links', []);
        $socialLinks = $this->prop('socialLinks', []);
        $showBackToTop = $this->prop('showBackToTop', true);
        
        $classes = $this->buildClass('footer bg-dark text-light');
        
        $attributes = $this->attributes;
        $attributes['class'] = $classes;
        $this->attributes = $attributes;
        
        $linksHtml = '';
        if (!empty($links)) {
            $linksHtml = '<div class="col-md-6"><h5>Quick Links</h5><ul class="list-unstyled">';
            foreach ($links as $link) {
                $linksHtml .= "<li><a href='{$link['url']}' class='text-light'>{$this->escape($link['text'])}</a></li>";
            }
            $linksHtml .= '</ul></div>';
        }
        
        $socialHtml = '';
        if (!empty($socialLinks)) {
            $socialHtml = '<div class="col-md-6"><h5>Follow Us</h5><div class="social-links">';
            foreach ($socialLinks as $social) {
                $socialHtml .= "<a href='{$social['url']}' class='text-light me-3'><i class='{$social['icon']}'></i></a>";
            }
            $socialHtml .= '</div></div>';
        }
        
        $backToTopHtml = '';
        if ($showBackToTop) {
            $backToTopHtml = '<button class="btn btn-primary back-to-top" onclick="scrollToTop()"><i class="fas fa-arrow-up"></i></button>';
        }
        
        return "
        <footer {$this->buildAttributes()}>
            <div class='container'>
                <div class='row'>
                    {$linksHtml}
                    {$socialHtml}
                </div>
                <hr class='my-4'>
                <div class='row align-items-center'>
                    <div class='col-md-6'>
                        <p class='mb-0'>&copy; {$year} {$this->escape($copyright)}. All rights reserved.</p>
                    </div>
                    <div class='col-md-6 text-md-end'>
                        <p class='mb-0'>Made with <i class='fas fa-heart text-danger'></i> for better healthcare</p>
                    </div>
                </div>
            </div>
            {$backToTopHtml}
        </footer>";
    }
}

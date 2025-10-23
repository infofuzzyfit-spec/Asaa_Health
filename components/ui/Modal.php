<?php
/**
 * Modal Component
 * Reusable modal dialog component
 */

require_once __DIR__ . '/../Component.php';

class Modal extends Component {
    public function render(): string {
        $id = $this->prop('id', $this->generateId('modal'));
        $title = $this->prop('title', '');
        $body = $this->prop('body', '');
        $footer = $this->prop('footer', '');
        $size = $this->prop('size', 'md');
        $centered = $this->prop('centered', true);
        $scrollable = $this->prop('scrollable', false);
        $backdrop = $this->prop('backdrop', true);
        $keyboard = $this->prop('keyboard', true);
        $static = $this->prop('static', false);
        
        $classes = $this->buildClass("modal fade");
        
        $attributes = $this->attributes;
        $attributes['id'] = $id;
        $attributes['tabindex'] = '-1';
        $attributes['aria-labelledby'] = $id . 'Label';
        $attributes['aria-hidden'] = 'true';
        
        if (!$backdrop) {
            $attributes['data-bs-backdrop'] = 'false';
        }
        if (!$keyboard) {
            $attributes['data-bs-keyboard'] = 'false';
        }
        if ($static) {
            $attributes['data-bs-backdrop'] = 'static';
        }
        
        $this->attributes = $attributes;
        
        $modalDialogClasses = "modal-dialog modal-{$size}";
        if ($centered) {
            $modalDialogClasses .= ' modal-dialog-centered';
        }
        if ($scrollable) {
            $modalDialogClasses .= ' modal-dialog-scrollable';
        }
        
        $titleHtml = $title ? "<h5 class='modal-title' id='{$id}Label'>{$this->escape($title)}</h5>" : '';
        $bodyHtml = $body ? "<div class='modal-body'>{$body}</div>" : '';
        $footerHtml = $footer ? "<div class='modal-footer'>{$footer}</div>" : '';
        
        return "
        <div {$this->buildAttributes()}>
            <div class='{$modalDialogClasses}'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        {$titleHtml}
                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                    </div>
                    {$bodyHtml}
                    {$footerHtml}
                </div>
            </div>
        </div>";
    }
}

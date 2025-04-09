<?php

namespace SmashBalloon\Reviews\Common\Customizer;

use Smashballoon\Customizer\V2\PreviewProvider;

class ShortcodePreviewProvider implements PreviewProvider{
    public function render($attr, $settings) {
        return apply_filters('sby_render_shortcode', $attr, $settings);
    }
}
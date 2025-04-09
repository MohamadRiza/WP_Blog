<?php

namespace SmashBalloon\YouTubeFeed\Customizer;

use Smashballoon\Customizer\PreviewProvider;

class ShortcodePreviewProvider implements PreviewProvider {
	public function render( $attr, $settings ) {
		return apply_filters( 'sby_render_shortcode', $attr, $settings );
	}
}
<?php

namespace Smashballoon\Customizer;

/** @internal */
interface PreviewProvider
{
    public function render($attr, $settings);
}

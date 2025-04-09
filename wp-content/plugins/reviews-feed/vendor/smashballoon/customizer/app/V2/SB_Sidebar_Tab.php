<?php

namespace Smashballoon\Customizer\V2;

/**
 * Class Customizer Sidebar Tab
 *
 * @since 1.0
 */
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}
/** @internal */
abstract class SB_Sidebar_Tab
{
    /**
     * Get the Sidebar Tab
     * Merge Both Tab Info + Tab Sections
     *
     * @since 1.0
     *
     * @return array
     */
    function get_tab()
    {
        $tab = \array_merge($this->tab_info(), ['sections' => $this->tab_sections()]);
        return $tab;
    }
    /**
     * Get the Sidebar Tab info
     *
     * @since 1.0
     *
     * @return array
     */
    protected abstract function tab_info();
    /**
     * Get the Sidebar Tab Sections
     *
     * @since 1.0
     *
     * @return array
     */
    protected abstract function tab_sections();
}

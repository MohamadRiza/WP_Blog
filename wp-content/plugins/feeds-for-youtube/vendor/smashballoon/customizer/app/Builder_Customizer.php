<?php

/**
 * Customizer Builder
 *
 *
 * @since 6.0
 */
namespace Smashballoon\Customizer;

if (!\defined('ABSPATH')) {
    exit;
}
/** @internal */
class Builder_Customizer
{
    /**
     * Controls Classes Array
     *
     *
     * @since 6.0
     * @access private
     *
     * @var array
     */
    public static $controls_classes = array();
    public function register()
    {
        $this->register_controls();
    }
    /**
     * Get controls list.
     *
     * Getting controls list
     *
     * @since 6.0
     * @access public
     *
     * @return array
     */
    public static function get_controls_list()
    {
        return array('actionbutton', 'checkbox', 'checkboxsection', 'datepicker', 'colorpicker', 'number', 'select', 'switcher', 'text', 'textarea', 'toggle', 'toggleset', 'heading', 'separator', 'customview', 'coloroverride', 'togglebutton', 'hidden', 'imagechooser', 'checkboxlist');
    }
    /**
     * Register Controls
     *
     * Including Control
     *
     * @since 6.0
     * @access public
     *
     */
    public static function register_controls()
    {
        foreach (self::get_controls_list() as $control) {
            $controlClassName = 'SB_' . \ucfirst($control) . '_Control';
            $cls_name = __NAMESPACE__ . '\\Controls\\' . $controlClassName;
            self::$controls_classes[$control] = new $cls_name();
        }
    }
    /**
     * Print Controls Vue JS Tempalte
     *
     * Including Control
     *
     * @since 6.0
     * @access public
     *
     */
    public static function get_controls_templates($editingType)
    {
        $controls_list = self::get_controls_list();
        foreach ($controls_list as $control) {
            self::$controls_classes[$control]->print_control_wrapper($editingType);
        }
    }
}

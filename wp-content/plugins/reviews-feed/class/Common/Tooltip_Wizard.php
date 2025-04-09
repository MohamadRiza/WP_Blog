<?php
/**
 * SBR Tooltip Wizard
 *
 *
 * @since 1.0
 */
namespace SmashBalloon\Reviews\Common;
use Smashballoon\Stubs\Services\ServiceProvider;

class Tooltip_Wizard extends ServiceProvider
{

    public function register()
    {
        $this->init();
    }

    /**
     * Initialize class.
     *
     * @since 1.0
     */
    public function init()
    {

        $this->hooks();
    }

    /**
     * Register hooks.
     *
     * @since 1.0
     */
    public function hooks()
    {
        add_action('admin_enqueue_scripts', [$this, 'enqueues']);
        add_action('admin_footer', [$this, 'output']);
    }


    /**
     * Enqueue assets.
     *
     * @since 1.0
     */
    public function enqueues()
    {

        wp_enqueue_style(
            'sbr-tooltipster-css',
            SBR_PLUGIN_URL . 'assets/admin/css/tooltipster.css',
            null,
            SBRVER
        );

        wp_enqueue_script(
            'sbr-tooltipster-js',
            SBR_PLUGIN_URL . 'assets/admin/js/jquery.tooltipster.min.js',
            ['jquery'],
            SBRVER,
            true
        );

        wp_enqueue_script(
            'sbr-admin-tooltip-wizard',
            SBR_PLUGIN_URL . 'assets/admin/js/tooltip-wizard.js',
            ['jquery'],
            SBRVER
        );

        $wp_localize_data = [];
        if ($this->check_gutenberg_wizard()) {
            $wp_localize_data['sbr_wizard_gutenberg'] = true;
        }

        wp_localize_script(
            'sbr-admin-tooltip-wizard',
            'sbr_admin_tooltip_wizard',
            $wp_localize_data
        );
    }

    /**
     * Output HTML.
     *
     * @since 1.0
     */
    public function output()
    {
        if ($this->check_gutenberg_wizard()) {
            $this->gutenberg_tooltip_output();
        }

    }

    /**
     * Gutenberg Tooltip Output HTML.
     *
     * @since 1.0
     */
    public function check_gutenberg_wizard()
    {
        global $pagenow;
        return (($pagenow == 'post.php') || (get_post_type() == 'page'))
            && !empty($_GET['sbr_wizard']);
    }


    /**
     * Gutenberg Tooltip Output HTML.
     *
     * @since 1.0
     */
    public function gutenberg_tooltip_output()
    {
        ?>
        <div id="sbr-gutenberg-tooltip-content">
            <div class="sbr-tlp-wizard-cls sbr-tlp-wizard-close"></div>
            <div class="sbr-tlp-wizard-content">
                <strong class="sbr-tooltip-wizard-head">
                    <?php echo __('Add a Block', 'reviews-feed') ?>
                </strong>
                <p class="sbr-tooltip-wizard-txt">
                    <?php echo __('Click the plus button, search for Reviews Feed', 'reviews-feed'); ?>
                    <br />
                    <?php echo __('and click the block to embed it.', 'reviews-feed') ?> <a
                        href="https://smashballoon.com/doc/wordpress-5-block-page-editor-gutenberg/?facebook" rel="noopener"
                        target="_blank">
                        <?php echo __('Learn More', 'reviews-feed') ?>
                    </a>
                </p>
                <div class="sbr-tooltip-wizard-actions">
                    <button class="sbr-tlp-wizard-close">
                        <?php echo __('Done', 'reviews-feed') ?>
                    </button>
                </div>
            </div>
        </div>
        <?php
    }


}
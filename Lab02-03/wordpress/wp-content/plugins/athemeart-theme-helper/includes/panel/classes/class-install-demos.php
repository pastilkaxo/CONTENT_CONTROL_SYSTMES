<?php
/**
 * Install demos page
 *
 * @package aThemeArt_Demo_Import
 * @category Core
 * @author aThemeArt
 */
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Start Class
class aThemeArt_Install_Demos {

    /**
     * Start things up
     */
    public function __construct() {
        add_action('admin_menu', array($this, 'add_page'), 999);
    }

    /**
     * Add sub menu page for the custom CSS input
     *
     * @since 1.0.0
     */
    public function add_page() {
        $theme = wp_get_theme();
      

        $title = esc_html(ucwords($theme['Name'])).' '. esc_html__('Install Demos', 'athemeart-theme-helper');


        add_theme_page(
                esc_html($theme['Name']).' '. esc_html__('Install Demos', 'athemeart-theme-helper'),
                $title,
                'manage_options',
                'athemeart-panel-install-demos',
                array($this, 'create_admin_page')
        );
    }

    /**
     * Settings page output
     *
     * @since 1.0.0
     */
    public function create_admin_page() {
        $theme = wp_get_theme();
        // Theme branding
        $brand = esc_html(ucwords($theme['Name']));
        ?>

        <div class="athemeart-demo-wrap wrap">

            <h2><?php echo esc_html($brand); ?> - <?php esc_html_e('Demo Content Install ', 'athemeart-theme-helper'); ?></h2>
            
            <div class="theme-browser rendered">

                <?php
                // Vars
                $demos = aThemeArt_Demos::get_demos_data();
                $categories = aThemeArt_Demos::get_demo_all_categories($demos);
                ?>

                <?php if (!empty($categories)) : ?>
                    <div class="athemeart-header-bar">
                        <nav class="athemeart-navigation">
                            <ul>
                                <li class="active"><a href="#all" class="athemeart-navigation-link"><?php esc_html_e('All', 'athemeart-theme-helper'); ?></a></li>
                                <?php foreach ($categories as $key => $name) : ?>
                                    <li><a href="#<?php echo esc_attr($key); ?>" class="athemeart-navigation-link"><?php echo esc_html($name); ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </nav>
                        <div clas="athemeart-search">
                            <input type="text" class="athemeart-search-input" name="athemeart-search" value="" placeholder="<?php esc_html_e('Search demos...', 'athemeart-theme-helper'); ?>">
                        </div>
                    </div>
                <?php endif; ?>

                <div class="themes wp-clearfix">

                    <?php
                    // Loop through all demos
                    foreach ($demos as $demo => $key) {

                        // Vars
                        $item_categories = aThemeArt_Demos::get_demo_item_categories($key);
                        ?>

                        <div class="theme-wrap" data-categories="<?php echo esc_attr($item_categories); ?>" data-name="<?php echo esc_attr(strtolower($demo)); ?>">

                            <?php if ( !empty($key['pro']) ) : ?>
                            <div class="theme">
                            <?php else:?>
                            <div class="theme athemeart-open-popup" data-demo-id="<?php echo esc_attr($demo); ?>">
                            <?php endif;?>

                                <div class="theme-screenshot">
                                    <img src="<?php echo esc_url($key['screenshot']); ?>" />

                                    <div class="demo-import-loader preview-all preview-all-<?php echo esc_attr($demo); ?>"></div>

                                    <div class="demo-import-loader preview-icon preview-<?php echo esc_attr($demo); ?>"><i class="custom-loader"></i></div>
                                </div>

                                <div class="theme-id-container">

                                    <h2 class="theme-name" id="<?php echo esc_attr($demo); ?>"><span><?php echo esc_html($key['demo_name']); ?></span></h2>

                                    <div class="theme-actions">
                                        <?php if ( !empty($key['demo_url']) ) : ?>
                                        <a class="button button-primary" href="<?php echo esc_url($key['demo_url']); ?>" target="_blank"><?php esc_html_e('Live Preview', 'athemeart-theme-helper'); ?></a>
                                        <?php endif;?>

                                        <?php if ( !empty($key['pro']) ) : ?>
                                        <a class="button button-primary" href="<?php echo esc_url($key['pro']); ?>" target="_blank"><?php esc_html_e('GET PRO', 'athemeart-theme-helper'); ?></a>
                                        <?php else:?>

                                            <a class="button button-primary athemeart-open-popup" href="#" data-demo-id="<?php echo esc_attr($demo); ?>"><?php esc_html_e('Import', 'athemeart-theme-helper'); ?></a>

                                        <?php endif;?>

                                        
                                    </div>

                                </div>

                            </div>

                        </div>

                    <?php } ?>

                </div>

            </div>

        </div>

        <?php
    }

}

new aThemeArt_Install_Demos();

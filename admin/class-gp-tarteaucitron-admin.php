<?php
// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    die;
}

if (!class_exists('GpTarteaucitronAdmin')) {
    class GpTarteaucitronAdmin
    {
        protected $gtm_code;
        protected $init_global;
        protected $init_services;
        protected $color_primary;
        protected $color_secondary;

        /**
         * Plugin initialization
         */
        public function __construct()
        {
            // Add the page to the admin menu
            add_action('admin_menu', [$this, 'add_plugin_page']);
            // Register page options
            add_action('admin_init', [$this, 'admin_page_init']);
            // Add plugin settings link
            add_filter('plugin_action_links', [$this, 'add_settings_link'], 10, 2);
            add_filter('admin_enqueue_scripts', [$this, 'enqueue_color_picker']);

            $this->init_global = $this->getInitGlobal();
            $this->gtm_code = $this->getGtmCode();
            $this->init_services = $this->getInitServices();
            $this->color_primary = $this->getColorPrimary();
            $this->color_secondary = $this->getColorSecondary();
        }

        public function enqueue_color_picker($hook)
        {
            wp_enqueue_style('wp-color-picker');
            wp_enqueue_script('gp-tarteaucitron-color-picker', plugins_url('js/color-picker-init.js', __FILE__), ['wp-color-picker'], false, true);
        }

        static function getSettings($key, $else = false)
        {
            $options = get_option('gp_tarteaucitron_settings');
            if (isset($options[$key]) && !empty($options[$key])) {
                return $options[$key];
            } else {
                return $else;
            }
        }

        static function getGtmCode()
        {
            return self::getSettings('gp_tarteaucitron_gtm_code');
        }

        static function getInitGlobal()
        {
            return self::getSettings('gp_tarteaucitron_init_global', self::init_global_default());
        }

        static function getInitServices()
        {
            return self::getSettings('gp_tarteaucitron_init_services', self::init_services_default());
        }

        static function getColorPrimary()
        {
            return self::getSettings('gp_tarteaucitron_color_primary', '');
        }

        static function getColorSecondary()
        {
            return self::getSettings('gp_tarteaucitron_color_secondary', '');
        }

        /**
         * Function that will add the options page under Setting Menu.
         */
        public function add_plugin_page()
        {
            // $page_title, $menu_title, $capability, $menu_slug, $callback_function
            add_options_page(
                __('Tarteaucitron Options'), // page_title
                __('Gp Tarteaucitron'), // menu_title
                'manage_options', // capability
                'gp-tarteaucitron-options', // menu_slug
                [$this, 'create_admin_page'] // function
            );
        }

        /**
         * Function that will display the options page.
         */
        public function create_admin_page()
        { ?>
            <div class="wrap">
                <h2><?php _e('Tarteaucitron settings'); ?></h2>
                <p><?php _e('For instructions visit: '); ?><a target="_blank" rel="noopener nofollow" href="https://opt-out.ferank.eu/fr/install/">https://opt-out.ferank.eu/fr/install/</a></p>
                <hr>
                <?php //settings_errors();
                ?>

                <form method="post" action="options.php">
                    <?php
                    settings_fields('gp_tarteaucitron_option_group');
                    do_settings_sections('gp-tarteaucitron-admin-sections');
                    submit_button();
                    ?>
                </form>
            </div>
        <?php }

        public function admin_page_init()
        {

            // Register Settings
            register_setting(
                'gp_tarteaucitron_option_group', // option_group
                'gp_tarteaucitron_settings', // option_name
                [$this, 'sanitize_values'] // sanitize_callback
            );

            // Add Section for option init js
            add_settings_section(
                'section_tarteaucitron_global', // id
                __('Tarteaucitron global JS config'), // title
                '', // callback
                'gp-tarteaucitron-admin-sections' // page
            );
            // Add Section for option services
            add_settings_section(
                'section_tarteaucitron_services', // id
                __('Tarteaucitron Services'), // title
                '', // callback
                'gp-tarteaucitron-admin-sections' // page
            );

            // Add Section for option colors
            add_settings_section(
                'section_tarteaucitron_colors', // id
                __('Tarteaucitron Colors'), // title
                '', // callback
                'gp-tarteaucitron-admin-sections' // page
            );


            // Add Slug Field
            add_settings_field(
                'gp_tarteaucitron_init_global', // id
                __('Init options'), // title
                [$this, 'init_global_render'], // callback
                'gp-tarteaucitron-admin-sections', // page
                'section_tarteaucitron_global' // section
            );

            // Add Slug Field
            add_settings_field(
                'gp_tarteaucitron_gtm_code', // id
                __('GTM Code'), // title
                [$this, 'gtm_code_render'], // callback
                'gp-tarteaucitron-admin-sections', // page
                'section_tarteaucitron_services' // section
            );

            // Add Slug Field
            add_settings_field(
                'gp_tarteaucitron_init_services', // id
                __('Init services'), // title
                [$this, 'init_services_render'], // callback
                'gp-tarteaucitron-admin-sections', // page
                'section_tarteaucitron_services' // section
            );

            // Add Field
            add_settings_field(
                'gp_tarteaucitron_color_primary', // id
                __('Color Primary'), // title
                [$this, 'color_primary_render'], // callback
                'gp-tarteaucitron-admin-sections', // page
                'section_tarteaucitron_colors' // section
            );

            // Add Field
            add_settings_field(
                'gp_tarteaucitron_color_secondary', // id
                __('Color Secondary'), // title
                [$this, 'color_secondary_render'], // callback
                'gp-tarteaucitron-admin-sections', // page
                'section_tarteaucitron_colors' // section
            );
        }

        /**
         * Functions that display the fields.
         */
        public function sanitize_values($input)
        {
            $sanitary_values = [];

            if (isset($input['gp_tarteaucitron_gtm_code'])) {
                $sanitary_values['gp_tarteaucitron_gtm_code'] = sanitize_text_field($input['gp_tarteaucitron_gtm_code']);
            }

            if (isset($input['gp_tarteaucitron_init_global'])) {
                $sanitary_values['gp_tarteaucitron_init_global'] = $input['gp_tarteaucitron_init_global'];
            }

            if (isset($input['gp_tarteaucitron_init_services'])) {
                $sanitary_values['gp_tarteaucitron_init_services'] = sanitize_textarea_field($input['gp_tarteaucitron_init_services']);
            }

            if (isset($input['gp_tarteaucitron_color_primary'])) {
                $sanitary_values['gp_tarteaucitron_color_primary'] = sanitize_hex_color($input['gp_tarteaucitron_color_primary']);
            }
            if (isset($input['gp_tarteaucitron_color_secondary'])) {
                $sanitary_values['gp_tarteaucitron_color_secondary'] = sanitize_hex_color($input['gp_tarteaucitron_color_secondary']);
            }
            return $sanitary_values;
        }

        /**
         * Fields individual callbacks
         */

        public function gtm_code_render()
        {
            printf(
                '<input type="text" placeholder="' . __('GTM-XXXXXXX') . '" class="regular-text" name="gp_tarteaucitron_settings[gp_tarteaucitron_gtm_code]" value="%s" id="gp_tarteaucitron_gtm_code" >',
                $this->gtm_code ? esc_attr($this->gtm_code) : ''
            );
        }

        /**
         * Fields individual rendres
         */
        public function init_global_render()
        {
            ?>
            <textarea rows="18" style="background: #F2F2F2;
                        outline: none;
                        width: 100%;
                        border: 1px solid #ccc;
                        padding: 11px;
                        line-height: 1.3em;
                        margin-bottom: 22px;"
                      name='gp_tarteaucitron_settings[gp_tarteaucitron_init_global]'><?php echo htmlspecialchars($this->init_global); ?></textarea>
            <?php

        }

        public function init_services_render()
        {
            if ($this->gtm_code) { ?>
                <textarea rows="4" style="background: #F2F2F2;
                        outline: none;
                        width: 100%;
                        border: 1px solid #ccc;
                        padding: 11px;
                        line-height: 1.3em;
                        margin-bottom: 22px;"
                          name='gp_tarteaucitron_settings[gp_tarteaucitron_init_services]'><?php echo htmlspecialchars($this->init_services); ?></textarea>
            <?php }

        }

        public function color_primary_render()
        {
            printf(
                '<input class="tarteaucitron-color-picker" type="text"  name="gp_tarteaucitron_settings[gp_tarteaucitron_color_primary]" value="%s" id="gp_tarteaucitron_color_primary" >',
                $this->color_primary ? esc_attr($this->color_primary) : ''
            );
        }

        public function color_secondary_render()
        {
            printf(
                '<input class="tarteaucitron-color-picker" type="text"  name="gp_tarteaucitron_settings[gp_tarteaucitron_color_secondary]" value="%s" id="gp_tarteaucitron_color_secondary" > <p class="description"><small>(color for buttons)</small></p>',
                $this->color_secondary ? esc_attr($this->color_secondary) : ''
            );
        }

        /**
         * Fields individual default
         */
        static function init_global_default()
        {

            return 'tarteaucitron.init({
	    "hashtag": "#tarteaucitron",
	    "cookieName": "tartaucitron",

	    "orientation": "bottom",
	    "showAlertSmall": false,
	    "cookieslist": true,

	    "adblocker": false,
	    "AcceptAllCta" : true,
	    "highPrivacy": true,
	    "handleBrowserDNTRequest": false,
    
	    "removeCredit": false,
	    "moreInfoLink": true,
});';

        }

        static function init_services_default()
        {
            return 'tarteaucitron.user.googletagmanagerId = \'' . self::getGtmCode() . '\';
            (tarteaucitron.job = tarteaucitron.job || []).push(\'googletagmanager\');
            /*You can add here other services*/';

        }

        /**
         * Functions that registers settings link on plugin description.
         */
        public function add_settings_link($links, $file)
        {
            $this_plugin = plugin_basename(__FILE__);

            if (is_plugin_active($this_plugin) && $file == $this_plugin) {
                $links[] = '<a href="' . admin_url('options-general.php?page=gp-tarteaucitron-options') . '">' . __('Settings', 'gp-tarteaucitron') . '</a>';
            }

            return $links;

        } // end add_settings_link

    }
} // !class_exists

if (is_admin())
    $gp_tarteaucitron_admin = new GpTarteaucitronAdmin();
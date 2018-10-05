<?php
// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    die;
}

if (!class_exists('GpTarteaucitronAdmin')) {
    class GpTarteaucitronAdmin
    {
        public static $prefix_option = 'gp-tarteaucitron';
        public static $prefix_setting = 'gp_tarteaucitron';
        
        protected $gtm_code;
        protected $init_global;
        protected $init_gtm_service;
        protected $init_services;
        protected $color_primary;
        protected $color_secondary;
        protected $color_text_primary;
        protected $css_custom;

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

            $this->init_global = self::getInitGlobal();
            $this->gtm_code = self::getGtmCode();
            $this->init_gtm_service = self::getInitGtmService();
            $this->init_services = self::getInitServices();
            $this->color_primary = self::getColorPrimary();
            $this->color_secondary = self::getColorSecondary();
            $this->color_text_primary = self::getColorTextPrimary();
            $this->css_custom = self::getCssCustom();
        }

        public function enqueue_color_picker($hook)
        {
            wp_enqueue_style('wp-color-picker');
            wp_enqueue_script(self::$prefix_option . '-color-picker', plugins_url('js/color-picker-init.js', __FILE__), ['wp-color-picker'], false, true);
        }

        public static function getPrivacyUrl()
        {
            return get_option('wp_page_for_privacy_policy') ? get_the_guid(get_option('wp_page_for_privacy_policy')) : '';
        }

        static function getSettings($key, $else = false)
        {
            $options = get_option(self::$prefix_setting . '_settings');
            if (isset($options[self::$prefix_setting . $key]) && !empty($options[self::$prefix_setting . $key])) {
                return $options[self::$prefix_setting . $key];
            } else {
                return $else;
            }
        }

        static function getFieldName($field)
        {
            return self::$prefix_setting . '_settings[' . self::$prefix_setting . $field . ']';
        }

        static function getGtmCode()
        {
            return self::getSettings('_gtm_code');
        }

        static function getInitGlobal()
        {
            return self::getSettings('_init_global', self::init_global_default());
        }

        static function getInitGtmService()
        {
            return self::getSettings('_init_gtm_service', self::init_gtm_service_default());
        }

        static function getInitServices()
        {
            return self::getSettings('_init_services', self::init_services_default());
        }

        static function getColorPrimary()
        {
            return self::getSettings('_color_primary', '');
        }

        static function getColorSecondary()
        {
            return self::getSettings('_color_secondary', '');
        }

        static function getColorTextPrimary()
        {
            return self::getSettings('_color_text_primary', '#ffffff');
        }

        static function getCssCustom()
        {
            return self::getSettings('_css_custom', self::css_custom_default());
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
                self::$prefix_option . '-options', // menu_slug
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
                <p><?php _e('For instructions visit: '); ?><a target="_blank" rel="noopener nofollow" href="https://opt-out.ferank.eu/en/install/">https://opt-out.ferank.eu/en/install/</a></p>
                <hr>
                <?php //settings_errors();
                ?>

                <form method="post" action="options.php">
                    <?php
                    settings_fields(self::$prefix_setting . '_option_group');
                    do_settings_sections(self::$prefix_option . '-admin-sections');
                    submit_button();
                    ?>
                </form>
            </div>
        <?php }

        public function admin_page_init()
        {
            // Register Settings
            register_setting(
                self::$prefix_setting . '_option_group', // option_group
                self::$prefix_setting . '_settings', // option_name
                [$this, 'sanitize_values'] // sanitize_callback
            );

            // Add Section for option init js
            add_settings_section(
                self::$prefix_setting . '_section_global', // id
                __('Tarteaucitron global JS options'), // title
                '', // callback
                self::$prefix_option . '-admin-sections' // page
            );
            // Add Section for option services
            add_settings_section(
                self::$prefix_setting . '_section_services', // id
                __('Tarteaucitron Services'), // title
                '', // callback
                self::$prefix_option . '-admin-sections' // page
            );

            // Add Section for option colors
            add_settings_section(
                self::$prefix_setting . '_section_colors', // id
                __('Tarteaucitron Colors'), // title
                '', // callback
                self::$prefix_option . '-admin-sections' // page
            );


            // Field init global
            add_settings_field(
                self::$prefix_setting . '_init_global', // id
                __('Init options'), // title
                [$this, 'init_global_render'], // callback
                self::$prefix_option . '-admin-sections', // page
                self::$prefix_setting . '_section_global' // section
            );

            // Field GTM code
            add_settings_field(
                self::$prefix_setting . '_gtm_code', // id
                __('GTM Code'), // title
                [$this, 'gtm_code_render'], // callback
                self::$prefix_option . '-admin-sections', // page
                self::$prefix_setting . '_section_services' // section
            );

            // Field GTM service
            add_settings_field(
                self::$prefix_setting . '_init_gtm_service', // id
                __('Init GTM services'), // title
                [$this, 'init_gtm_service_render'], // callback
                self::$prefix_option . '-admin-sections', // page
                self::$prefix_setting . '_section_services' // section
            );

            // Field Init services
            add_settings_field(
                self::$prefix_setting . '_init_services', // id
                __('Init Services/options'), // title
                [$this, 'init_services_render'], // callback
                self::$prefix_option . '-admin-sections', // page
                self::$prefix_setting . '_section_services' // section
            );

            // Field Color primary
            add_settings_field(
                self::$prefix_setting . '_color_primary', // id
                __('Color Primary'), // title
                [$this, 'color_primary_render'], // callback
                self::$prefix_option . '-admin-sections', // page
                self::$prefix_setting . '_section_colors' // section
            );

            // Field Color secondary
            add_settings_field(
                self::$prefix_setting . '_color_secondary', // id
                __('Color Secondary'), // title
                [$this, 'color_secondary_render'], // callback
                self::$prefix_option . '-admin-sections', // page
                self::$prefix_setting . '_section_colors' // section
            );

            // Field Color text primary
            add_settings_field(
                self::$prefix_setting . '_color_text_primary', // id
                __('Color text primary'), // title
                [$this, 'color_text_primary_render'], // callback
                self::$prefix_option . '-admin-sections', // page
                self::$prefix_setting . '_section_colors' // section
            );

            // Field css custom
            add_settings_field(
                self::$prefix_setting . '_css_custom', // id
                __('Custom css rules'), // title
                [$this, 'css_custom_render'], // callback
                self::$prefix_option . '-admin-sections', // page
                self::$prefix_setting . '_section_colors' // section
            );

        }

        /**
         * Functions that display the fields.
         */
        public function sanitize_values($input)
        {
            $sanitary_values = [];

            if (isset($input[self::$prefix_setting . '_gtm_code'])) {
                $sanitary_values[self::$prefix_setting . '_gtm_code'] = sanitize_text_field($input[self::$prefix_setting . '_gtm_code']);
            }

            if (isset($input[self::$prefix_setting . '_init_global'])) {
                $sanitary_values[self::$prefix_setting . '_init_global'] = $input[self::$prefix_setting . '_init_global'];
            }

            if (isset($input[self::$prefix_setting . '_init_gtm_service'])) {
                $sanitary_values[self::$prefix_setting . '_init_gtm_service'] = sanitize_textarea_field($input[self::$prefix_setting . '_init_gtm_service']);
            }

            if (isset($input[self::$prefix_setting . '_init_services'])) {
                $sanitary_values[self::$prefix_setting . '_init_services'] = sanitize_textarea_field($input[self::$prefix_setting . '_init_services']);
            }

            if (isset($input[self::$prefix_setting . '_color_primary'])) {
                $sanitary_values[self::$prefix_setting . '_color_primary'] = sanitize_hex_color($input[self::$prefix_setting . '_color_primary']);
            }

            if (isset($input[self::$prefix_setting . '_color_secondary'])) {
                $sanitary_values[self::$prefix_setting . '_color_secondary'] = sanitize_hex_color($input[self::$prefix_setting . '_color_secondary']);
            }

            if (isset($input[self::$prefix_setting . '_color_text_primary'])) {
                $sanitary_values[self::$prefix_setting . '_color_text_primary'] = sanitize_hex_color($input[self::$prefix_setting . '_color_text_primary']);
            }

            if (isset($input[self::$prefix_setting . '_css_custom'])) {
                $sanitary_values[self::$prefix_setting . '_css_custom'] = sanitize_textarea_field($input[self::$prefix_setting . '_css_custom']);
            }

            return $sanitary_values;
        }

        /**
         * Fields individual callbacks
         */

        public function gtm_code_render()
        {
            printf(
                '<input
                name="' . self::getFieldName('_gtm_code') . '" 
                type="text" placeholder="' . __('GTM-XXXXXXX') . '" 
                class="regular-text" 
                value="%s">',
                esc_attr($this->gtm_code)
            );
        }

        /**
         * Fields individual rendres
         */
        public function init_global_render()
        {
            printf(
                '<textarea 
                        name="' . self::getFieldName('_init_global') . '" 
                        rows="16" 
                        style="background: #F2F2F2;
                            outline: none;
                            width: 100%%;
                            border: 1px solid #ccc;
                            padding: 11px;
                            line-height: 1.3em;
                            margin-bottom: 22px;" 
                        >%s</textarea>',
                htmlspecialchars($this->init_global)
            );
        }

        public function init_gtm_service_render()
        {
            if (!$this->gtm_code) return;
            printf(
                '<textarea 
                        name="' . self::getFieldName('_init_gtm_service') . '"
                        rows="4" 
                        style="background: #F2F2F2;
                            outline: none;
                            width: 100%%;
                            border: 1px solid #ccc;
                            padding: 11px;
                            line-height: 1.3em;" 
                        >%s</textarea>',
                trim(htmlspecialchars($this->init_gtm_service))
            );
        }

        public function init_services_render()
        {
            printf(
                '<textarea 
                        name="' . self::getFieldName('_init_services') . '" 
                        rows="6" 
                        style="background: #F2F2F2;
                            outline: none;
                            width: 100%%;
                            border: 1px solid #ccc;
                            padding: 11px;
                            line-height: 1.3em;" 
                        >%1$s</textarea>
                        <p class="description"><small>%2$s <a href="%3$s" rel="noopener nofollow" target="_blank">%3$s</a></small></p>',
                trim(htmlspecialchars($this->init_services)), __('Follow the instructions to install services: '), 'https://opt-out.ferank.eu/en/install/'
            );
        }


        public function color_primary_render()
        {
            printf(
                '<input 
                name="' . self::getFieldName('_color_primary') . '" 
                class="tarteaucitron-color-picker" 
                type="text" 
                value="%s">',
                esc_attr($this->color_primary)
            );
        }

        public function color_secondary_render()
        {
            printf(
                '<input 
                name="' . self::getFieldName('_color_secondary') . '" 
                class="tarteaucitron-color-picker" 
                type="text" value="%s"> 
                <p class="description"><small>(color for buttons)</small></p>',
                esc_attr($this->color_secondary)
            );
        }

        public function color_text_primary_render()
        {
            printf(
                '<input 
                name="' . self::getFieldName('_color_text_primary') . '" 
                class="tarteaucitron-color-picker" 
                type="text" 
                value="%s" 
                data-default-color="#ffffff">',
                esc_attr($this->color_text_primary)
            );
        }

        public function css_custom_render()
        {
            printf(
                '<textarea 
                        name="' . self::getFieldName('_css_custom') . '" 
                        rows="8" 
                        style="background: #F2F2F2;
                            outline: none;
                            width: 100%%;
                            border: 1px solid #ccc;
                            padding: 11px;
                            line-height: 1.3em;" 
                        >%s</textarea>',
                trim(htmlspecialchars($this->css_custom))
            );
        }


        /**
         * Fields individual default
         */
        static function init_global_default()
        {

            $global_config = [
                "hashtag"    => "#tarteaucitron",
                "cookieName" => "tartaucitron",

                "orientation"    => "bottom",
                "showAlertSmall" => false,
                "cookieslist"    => true,

                "adblocker"               => false,
                "AcceptAllCta"            => true,
                "highPrivacy"             => true,
                "handleBrowserDNTRequest" => false,
                "removeCredit"            => false,
                "moreInfoLink"            => true,
                "privacyUrl"              => self::getPrivacyUrl(),
            ];

            return 'tarteaucitron.init(' . json_encode($global_config, JSON_PRETTY_PRINT) . ');';

        }

        static function init_gtm_service_default()
        {
            return 'tarteaucitron.user.googletagmanagerId = \'' . self::getGtmCode() . '\';
(tarteaucitron.job = tarteaucitron.job || []).push(\'googletagmanager\');
/*You can change this field if necessary*/';

        }

        static function init_services_default()
        {
            return '
            var tarteaucitronForceLanguage = \'fr\'; /* supported: fr, en, de, es, it, pt, pl, ru */
var tarteaucitronForceExpire = \'90\'; /* 3 months */
/*Add your options or services here*/';
        }

        static function css_custom_default()
        {
            return '
/* control panel */
body #tarteaucitron { }
/* Main banner */
body #tarteaucitronAlertBig { }
/* Little banner on bottom right*/
body #tarteaucitronAlertSmall { }';
        }

        /**
         * Functions that registers settings link on plugin description.
         */
        public function add_settings_link($links, $file)
        {
            $this_plugin = plugin_basename(self::$prefix_option . '/' . self::$prefix_option . '.php');
            if (is_plugin_active($this_plugin) && $file == $this_plugin) {
                $links[] = '<a href="' . admin_url('options-general.php?page=' . self::$prefix_option . '-options') . '">' . __('Settings', self::$prefix_option) . '</a>';
            }

            return $links;

        } // end add_settings_link

    }
} // !class_exists

if (is_admin())
    $gp_tarteaucitron_admin = new GpTarteaucitronAdmin();

<?php
// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    die;
}

if (!class_exists('GpTarteaucitronFront')) {
    class GpTarteaucitronFront
    {
        protected $init_global;
        protected $gtm_code;
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
            add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
            add_action('wp_head', [$this, 'head_scripts']);
            add_action('wp_footer', [$this, 'footer_scripts'], 1);
            $this->init_global = GpTarteaucitronAdmin::getInitGlobal();
            $this->gtm_code = GpTarteaucitronAdmin::getGtmCode();
            $this->init_gtm_service = GpTarteaucitronAdmin::getInitGtmService();
            $this->init_services = GpTarteaucitronAdmin::getInitServices();
            $this->color_primary = GpTarteaucitronAdmin::getColorPrimary();
            $this->color_secondary = GpTarteaucitronAdmin::getColorSecondary();
            $this->color_text_primary = GpTarteaucitronAdmin::getColorTextPrimary();
            $this->css_custom = GpTarteaucitronAdmin::getCssCustom();
        }

        public function enqueue_scripts()
        {
            wp_enqueue_script('tarteaucitron', plugins_url('tarteaucitron/tarteaucitron.js', dirname(__FILE__)), [], NULL, false);
        }

        public function head_scripts()
        {
            $this->css_colors();
            $this->css_custom();
        }

        public function footer_scripts()
        {
            $this->script_init_global();
            $this->script_init_gtm_service();
            $this->script_init_services();
        }

        public function script_init_global()
        {
            if ($this->init_global) { ?>
                <script id="<?php echo GpTarteaucitronAdmin::$prefix_setting; ?>_init_global" type="text/javascript">
                    <?php echo $this->init_global;?>
                </script>
            <?php }
        }

        public function script_init_gtm_service()
        {
            if ($this->init_gtm_service && $this->gtm_code) { ?>
                <script id="<?php echo GpTarteaucitronAdmin::$prefix_setting; ?>_init_gtm_service" type="text/javascript">
                    <?php echo $this->init_gtm_service;?>
                </script>
            <?php }
        }

        public function script_init_services()
        {
            if ($this->init_services) { ?>
                <script id="<?php echo GpTarteaucitronAdmin::$prefix_setting; ?>_init_services" type="text/javascript">
                    <?php echo $this->init_services;?>
                </script>
            <?php }
        }

        public function css_colors()
        { ?>
            <?php if ($this->init_global && (!empty($this->color_primary) || !empty($this->color_secondary))) : ?>
            <style id="<?php echo GpTarteaucitronAdmin::$prefix_setting; ?>_css_colors" type="text/css">
                @media screen and (max-width: 767px) {
                    body #tarteaucitronAlertBig #tarteaucitronCloseAlert,
                    body #tarteaucitronAlertBig #tarteaucitronPersonalize {
                        margin-bottom: 5px;
                    }
                }

                body #tarteaucitronAlertBig a,
                body #tarteaucitronAlertSmall a,
                body #tarteaucitron #tarteaucitronServices .tarteaucitronMainLine .tarteaucitronName b,
                body #tarteaucitron #tarteaucitronServices .tarteaucitronMainLine .tarteaucitronName a,
                body #tarteaucitron #tarteaucitronServices .tarteaucitronTitle a,
                body #tarteaucitron #tarteaucitronServices .tarteaucitronDetails,
                body #tarteaucitron #tarteaucitronServices .tarteaucitronTitle,
                body #tarteaucitron #tarteaucitronInfo,
                body #tarteaucitron #tarteaucitronInfo a,
                body #tarteaucitron #tarteaucitronClosePanel,
                body #tarteaucitronRoot #tarteaucitronAlertBig,
                body #tarteaucitronRoot #tarteaucitronAlertBig #tarteaucitronDisclaimerAlert,
                body #tarteaucitronRoot #tarteaucitronAlertBig #tarteaucitronDisclaimerAlert b,
                body #tarteaucitronRoot #tarteaucitronAlertBig #tarteaucitronPersonalize,
                body #tarteaucitronAlertBig #tarteaucitronPrivacyUrl {
                    color: <?php echo $this->color_text_primary;?>;
                }

                body #tarteaucitron b,
                body #tarteaucitronRoot #tarteaucitronAlertBig #tarteaucitronDisclaimerAlert,
                body #tarteaucitronRoot #tarteaucitronAlertBig #tarteaucitronDisclaimerAlert b {
                    font-family: inherit;
                }

                body #tarteaucitron b {
                    font-size:   22px;
                    font-weight: normal;
                }

                body #tarteaucitronRoot * {
                    color:     #7b7b7b;
                    font-size: 12px;
                }

                body #tarteaucitronRoot button#tarteaucitronBack {
                    background-color: #000000;
                }

                body #tarteaucitron .tarteaucitronBorder,
                body #tarteaucitron #tarteaucitronServices .tarteaucitronHidden {
                    background-color: #ffffff;
                }

                body #tarteaucitron .tarteaucitronBorder {
                    border:       0;
                    border-color: #ffffff;
                }

                body #tarteaucitron #tarteaucitronServices .tarteaucitronLine .tarteaucitronAsk .tarteaucitronAllow,
                body #tarteaucitron #tarteaucitronServices .tarteaucitronLine .tarteaucitronAsk .tarteaucitronDeny {
                    border-radius: 0;
                }

                body #tarteaucitronRoot #tarteaucitronAlertBig,
                body #tarteaucitronRoot #tarteaucitronAlertBig #tarteaucitronDisclaimerAlert,
                body #tarteaucitronRoot #tarteaucitronAlertBig #tarteaucitronDisclaimerAlert b,
                body #tarteaucitronRoot #tarteaucitronAlertBig #tarteaucitronCloseAlert,
                body #tarteaucitronRoot #tarteaucitronAlertBig #tarteaucitronPersonalize {
                    font-size:   13px;
                    line-height: 1.4;
                }

                body #tarteaucitron #tarteaucitronClosePanel,
                body #tarteaucitronRoot #tarteaucitronAlertBig #tarteaucitronDisclaimerAlert b {
                    font-weight: bold;
                }

                body #tarteaucitronRoot #tarteaucitronAlertBig #tarteaucitronCloseAlert,
                body #tarteaucitronRoot #tarteaucitronAlertBig #tarteaucitronPersonalize {
                    margin-bottom: 0;
                    padding:       5px 10px;
                }

                <?php if (!empty($this->color_primary)) : ?>

                body #tarteaucitron #tarteaucitronServices .tarteaucitronMainLine,
                body #tarteaucitron #tarteaucitronServices .tarteaucitronMainLine:hover,
                body #tarteaucitronRoot #tarteaucitronAlertBig {
                    background-color: <?php echo $this->color_primary;?>;
                    border-color: <?php echo $this->color_primary;?>;
                }

                body #tarteaucitron #tarteaucitronServices .tarteaucitronDetails,
                body #tarteaucitron #tarteaucitronInfo {
                    background: <?php echo self::getColorDarken($this->color_primary, 15);?>;
                    border-color: <?php echo self::getColorDarken($this->color_primary, 15);?>;
                }

                body #tarteaucitron #tarteaucitronServices .tarteaucitronTitle,
                body #tarteaucitron #tarteaucitronInfo,
                body #tarteaucitron #tarteaucitronClosePanel,
                body #tarteaucitronRoot #tarteaucitronAlertBig #tarteaucitronCloseAlert {
                    background-color: <?php echo self::getColorDarken($this->color_primary, 10);?>;
                    color: <?php echo $this->color_text_primary;?>;
                }

                <?php endif;?>

                <?php if (!empty($this->color_secondary)) :?>
                body #tarteaucitronRoot #tarteaucitronAlertBig #tarteaucitronPersonalize,
                body #tarteaucitron #tarteaucitronServices .tarteaucitronLine .tarteaucitronAsk .tarteaucitronAllow,
                body #tarteaucitron #tarteaucitronServices .tarteaucitronLine .tarteaucitronAsk .tarteaucitronDeny,
                body #tarteaucitron #tarteaucitronPrivacyUrl {
                    background-color: <?php echo self::getColorDarken($this->color_secondary, 10);?>;
                    color: <?php echo $this->color_text_primary;?>;
                }

                <?php endif;?>

            </style>
        <?php endif; ?>

        <?php }

        public function css_custom()
        { ?>
            <?php if (!empty($this->css_custom)) : ?>
            <style id="<?php echo GpTarteaucitronAdmin::$prefix_setting; ?>_css_custom" type="text/css">
                <?php echo $this->css_custom;?>
            </style>
        <?php endif; ?>

        <?php }

        // Thanks to
        // https://gist.github.com/jegtnes/5720178
        public static function getColorDarken($hex, $percent)
        {
            preg_match('/^#?([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})$/i', $hex, $primary_colors);
            str_replace('%', '', $percent);
            $color = "#";
            for ($i = 1; $i <= 3; $i++) {
                $primary_colors[$i] = hexdec($primary_colors[$i]);
                $primary_colors[$i] = round($primary_colors[$i] * (100 - ($percent * 2)) / 100);
                $color .= str_pad(dechex($primary_colors[$i]), 2, '0', STR_PAD_LEFT);
            }
            return $color;
        }
    }
} // !class_exists

if (!is_admin())
    $gp_tarteaucitron_front = new GpTarteaucitronFront();

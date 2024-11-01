<?php
/*
Plugin Name: Simple Radio Forty Two
Description: A simple radio player plugin inspired by Radio 42.
Version: 1.2
Author: tlloancy
Text Domain: simple-radio-forty-two
Domain Path: /languages
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/
if (!defined('ABSPATH')) {
    exit;
}

add_action('admin_enqueue_scripts', 'sr42_enqueue_admin_resources');

function sr42_enqueue_admin_resources($hook) {
    // Styles de base pour toutes les pages de l'admin du plugin
    

    if ('simple-radio_page_simple-radio-styling' === $hook) {
		wp_enqueue_style('sr42-admin-styles', plugins_url('style/admin-styles.css', __FILE__));
        // Seulement pour la page de styling
        //wp_enqueue_script('sr42-radio-player', plugins_url('js/radio-player.js', __FILE__), array('jquery'), null, true);
        
        wp_enqueue_script('sr42-admin-preview', plugins_url('js/admin-preview.js', __FILE__), array('jquery'), null, true);
        wp_localize_script('sr42-admin-preview', 'pluginData', array(
            'pluginUrl' => esc_url(plugin_dir_url(__FILE__)),
        ));

        $inline_styles = '
            #radio-preview #preview-round {
                animation: previewRotate 60s infinite linear;
                animation-play-state: paused;
            }
            #radio-preview:hover #preview-play-button:hover + #preview-round,
            #radio-preview:hover #preview-pause-button:hover + #preview-round {
                animation-play-state: running;
            }';
        wp_add_inline_style('sr42-admin-styles', esc_html($inline_styles));
    }

    else if ('toplevel_page_simple-radio' === $hook) { // Assurez-vous que c'est bien le slug de votre page principale
        // Seulement pour la page principale du plugin (où se trouve le shortcode)
        wp_enqueue_script('sr42-admin-main', plugins_url('js/main-page.js', __FILE__), array('jquery'), null, true);
        wp_localize_script('sr42-admin-main', 'sr42MainPage', array(
            'successMessage' => esc_html__('Shortcode copied to clipboard!', 'simple-radio-forty-two'),
        ));
    }

    else if ('simple-radio_page_simple-radio-settings' === $hook) { // Assurez-vous que c'est bien le slug de votre page de settings
        // Seulement pour la page de settings
        wp_enqueue_script('sr42-settings-script', plugins_url('js/settings.js', __FILE__), array('jquery'), null, true);
        wp_localize_script('sr42-settings-script', 'sr42Settings', array(
            'podcastInputId' => 'sr42_podcast_url',
        ));
    }
}

function sr42_enqueue_scripts() {
	  global $post;
	if (is_object($post) && isset($post->post_content)) {
    if( has_shortcode($post->post_content, 'sr42_radio') ) {
	    wp_enqueue_script('sr42-radio-player', plugins_url('js/radio-player.js', __FILE__), array('jquery'), null, true);
	$podcast_url = esc_url(get_option("sr42_podcast_url"));
    $nonce = wp_create_nonce('podcast_update');
    
    wp_localize_script('sr42-radio-player', 'PodcastData', array(
        'url' => $podcast_url,
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => $nonce
    ));
    wp_enqueue_style('sr42-style', plugin_dir_url(__FILE__) . 'style/style.css');
    wp_enqueue_script('jquery');
    wp_enqueue_script('sr42-radio', plugin_dir_url(__FILE__) . 'js/radio.js', array('jquery'), null, true);
	}
		} else {
    // Gérer le cas où $post n'est pas un objet ou n'a pas de post_content
    // error_log('Erreur: $post n\'est pas un objet ou n\'a pas la propriété post_content.');
}
}
add_action('wp_enqueue_scripts', 'sr42_enqueue_scripts');

function sr42_load_textdomain() {
    load_plugin_textdomain('simple-radio-forty-two', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}
add_action('plugins_loaded', 'sr42_load_textdomain');

function get_latest_podcast_url_callback() {
    check_ajax_referer('podcast_update', 'security'); // Vérification du nonce
    echo esc_url(get_option("sr42_podcast_url"));
    wp_die();
}
add_action('wp_ajax_get_latest_podcast_url', 'get_latest_podcast_url_callback');
add_action('wp_ajax_nopriv_get_latest_podcast_url', 'get_latest_podcast_url_callback'); // Pour les utilisateurs non connectés

function sr42_add_main_menu() {
    add_menu_page(
        esc_html__('Simple Radio', 'simple-radio-forty-two'),
        esc_html__('Simple Radio', 'simple-radio-forty-two'),
        'manage_options',
        'simple-radio',
        'sr42_main_page',
        'dashicons-format-audio',
        6
    );

    add_submenu_page(
        'simple-radio',
        esc_html__('Settings', 'simple-radio-forty-two'),
        esc_html__('Settings', 'simple-radio-forty-two'),
        'manage_options',
        'simple-radio-settings',
        'sr42_settings_page'
    );

    add_submenu_page(
        'simple-radio', 
        esc_html__('Styling Options', 'simple-radio-forty-two'),
        esc_html__('Styling', 'simple-radio-forty-two'),
        'manage_options',
        'simple-radio-styling',
        'sr42_styling_page'
    );
}
add_action('admin_menu', 'sr42_add_main_menu');

function sr42_register_settings() {
    register_setting('sr42_options_group', 'sr42_podcast_url', 'esc_url_raw');
}
add_action('admin_init', 'sr42_register_settings');

function sr42_styling_page() {
    if (!current_user_can('manage_options')) {
        return;
    }
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Styling Options', 'simple-radio-forty-two'); ?></h1>
        <div style="display: flex; justify-content: space-between;">
            <form method="post" action="options.php" style="width: 48%;">
                <?php
                settings_fields('sr42_styling_options_group');
                do_settings_sections('simple-radio-styling');
                submit_button(esc_html__('Save Changes', 'simple-radio-forty-two'));
                ?>
            </form>
            <div id="radio-preview" style="width: 48%; border: 1px solid #ddd; padding: 10px; box-sizing: border-box;">
                <h2><?php esc_html_e('Preview', 'simple-radio-forty-two'); ?></h2>
                <div class="radio_container" style="width: 200px; height: 300px; background-size: cover; margin: 0 auto;">
                    <div id="preview-play-button" style="width: 100%; height: 100%; background-size: 25%; background-position: center;"></div>
                    <div id="preview-pause-button" style="display:none;width: 100%; height: 100%; background-size: 25%; background-position: center;"></div>
                    <div id="preview-round" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-image: url('<?php echo esc_url(get_option('sr42_round_url', plugins_url('style/img/round.png', __FILE__))); ?>'); background-size: contain; background-repeat: no-repeat; background-position: center;"></div>
                </div>
            </div>
        </div>
    </div>
    <?php
}

function sr42_register_styling_settings() {
    register_setting('sr42_styling_options_group', 'sr42_radio_title', 'sanitize_text_field');
    register_setting('sr42_styling_options_group', 'sr42_background_url', 'esc_url_raw');
    register_setting('sr42_styling_options_group', 'sr42_round_url', 'esc_url_raw');
    register_setting('sr42_styling_options_group', 'sr42_play_url', 'esc_url_raw');
    register_setting('sr42_styling_options_group', 'sr42_pause_url', 'esc_url_raw');

    add_settings_section(
        'sr42_styling_section', 
        esc_html__('Customize Player Appearance', 'simple-radio-forty-two'), 
        '__return_false', 
        'simple-radio-styling'
    );
    
    add_settings_field(
        'sr42_radio_title',
        esc_html__('Radio Title', 'simple-radio-forty-two'),
        'sr42_render_text_field',
        'simple-radio-styling',
        'sr42_styling_section',
        array('label_for' => 'sr42_radio_title')
    );

    add_settings_field(
        'sr42_background_url', 
        esc_html__('Background Image URL', 'simple-radio-forty-two'), 
        'sr42_render_text_field', 
        'simple-radio-styling', 
        'sr42_styling_section',
        array('label_for' => 'sr42_background_url')
    );

    add_settings_field(
        'sr42_round_url', 
        esc_html__('Round Button URL', 'simple-radio-forty-two'), 
        'sr42_render_text_field', 
        'simple-radio-styling', 
        'sr42_styling_section',
        array('label_for' => 'sr42_round_url')
    );

    add_settings_field(
        'sr42_play_url', 
        esc_html__('Play Button URL', 'simple-radio-forty-two'), 
        'sr42_render_text_field', 
        'simple-radio-styling', 
        'sr42_styling_section',
        array('label_for' => 'sr42_play_url')
    );

    add_settings_field(
        'sr42_pause_url', 
        esc_html__('Pause Button URL', 'simple-radio-forty-two'), 
        'sr42_render_text_field', 
        'simple-radio-styling', 
        'sr42_styling_section',
        array('label_for' => 'sr42_pause_url')
    );
}

function sr42_render_text_field($args) {
    $option = get_option($args['label_for']);
    echo "<input type='text' id='" . esc_attr($args['label_for']) . "' name='" . esc_attr($args['label_for']) . "' value='" . esc_attr($option) . "' />";
}

add_action('admin_init', 'sr42_register_styling_settings');

function sr42_main_page() {
    if (!current_user_can('manage_options')) {
        return;
    }
    ?>
    <div class="wrap" style="max-width: 600px; margin: auto; padding: 20px; border: 1px solid #ccc; border-radius: 5px; background-color: #f9f9f9;">
        <h1 style="text-align: center; color: #333;"><?php esc_html_e('Simple Radio Forty Two', 'simple-radio-forty-two'); ?></h1>
        <p style="text-align: center; font-size: 16px;"><?php esc_html_e('Use the shortcode below to insert the radio player into your posts or pages.', 'simple-radio-forty-two'); ?></p>

        <div style="margin: 20px 0; text-align: center;">
            <input type="text" id="shortcode" value="[sr42_radio]" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 3px;" readonly />
            <button id="copy_shortcode" style="margin-top: 10px; padding: 5px 15px; background-color: #0073aa; color: white; border: none; border-radius: 3px; cursor: pointer;"><?php esc_html_e('Copy Shortcode', 'simple-radio-forty-two'); ?></button>
        </div>
		<div id="success_message" style="display: none; margin-top: 20px; padding: 10px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 5px; text-align: center;">
            <?php esc_html_e('Shortcode copié dans le presse-papiers!', 'simple-radio-forty-two'); ?>
        </div>
        <p style="text-align: center; font-style: italic;"><?php esc_html_e('Click the button to copy the shortcode.', 'simple-radio-forty-two'); ?></p>
    </div>
    <?php
}

function sr42_settings_page() {
    if (!current_user_can('manage_options')) {
        return;
    }
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Simple Radio Settings', 'simple-radio-forty-two'); ?></h1>
        <form method="post" action="options.php">
            <?php settings_fields('sr42_options_group'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php esc_html_e('Podcast URL:', 'simple-radio-forty-two'); ?></th>
                    <td>
                        <input type="text" id="sr42_podcast_url" name="sr42_podcast_url" value="<?php echo esc_url(get_option('sr42_podcast_url')); ?>" style="width: 100%; min-width: 200px;" />
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

function sr42_add_settings_fields() {
    add_settings_field(
        'sr42_podcast_url',
        esc_html__('Podcast URL or Radio Stream URL', 'simple-radio-forty-two'),
        'sr42_render_podcast_url_field',
        'simple-radio-settings',
        'sr42_settings_section'
    );

    // No need for escaping here as we're registering the setting, not echoing the value
    register_setting('sr42_options_group', 'sr42_podcast_url', 'sanitize_url');
}

function sr42_render_podcast_url_field() {
    $url = esc_url(get_option('sr42_podcast_url'));
    echo "<input type='url' name='sr42_podcast_url' id='sr42_podcast_url' value='" . esc_attr($url) . "' style='width:100%;' placeholder='" . esc_attr__('Enter URL', 'simple-radio-forty-two') . "'>";
}

add_action('admin_init', 'sr42_add_settings_fields');

function sr42_simple_radio_shortcode($atts) {
    $atts = shortcode_atts(array(
        'stream' => esc_url(get_option('sr42_podcast_url', '')),
        'title' => esc_html(get_option('sr42_radio_title', 'Radio 42')),
        'background' => esc_url(get_option('sr42_background_url', plugins_url('style/img/background.gif', __FILE__))),
        'play_button' => esc_url(get_option('sr42_play_url', plugins_url('style/img/play.png', __FILE__))),
        'pause_button' => esc_url(get_option('sr42_pause_url', plugins_url('style/img/pause.png', __FILE__))),
        'round' => esc_url(get_option('sr42_round_url', plugins_url('style/img/round.png', __FILE__))),
    ), $atts);

    $radio_title = esc_html($atts['title']) === '' ? '$> ./radio_42' : esc_url($atts['title']);
    $background_url = esc_url($atts['background']) === '' ? plugins_url('style/img/background.gif', __FILE__) : esc_url($atts['background']);
    $play_url = esc_url($atts['play_button']) === '' ? plugins_url('style/img/play.png', __FILE__) : esc_url($atts['play_button']);
    $pause_url = esc_url($atts['pause_button']) === '' ? plugins_url('style/img/pause.png', __FILE__) : esc_url($atts['pause_button']);
    $round_url = esc_url($atts['round']) === '' ? plugins_url('style/img/round.png', __FILE__) : esc_url($atts['round']);
    $podcast_url = esc_url($atts['stream']) === '' ? 'https://origin.deter-mi.net/podcast' : esc_url($atts['stream']);

    return "<div class='radio_container' style='background-image: url(\"$background_url\");'>
        <h4>$radio_title</h4>
        <div id='track'>
            <h5>" . esc_html__('Now playing:', 'simple-radio-forty-two') . "</h5>
            <div id='title'></div>
            <div id='artist'></div>
        </div>
        <div id='program'>
            <h5>" . esc_html__('Coming next', 'simple-radio-forty-two') . "</h5>
            <div id='program_name'></div>
            <div id='date' data-timestamp='0'></div>
        </div>

        <?php if (!empty($podcast_url) && filter_var($podcast_url, FILTER_VALIDATE_URL)): ?>
            <audio preload='true' volume='0.0' src='$podcast_url'></audio>
            <div id='englobe'>
                <div id='player_button'>
                    <div id='play_button' style='background-image: url(\"$play_url\");'>" . esc_html__('Play', 'simple-radio-forty-two') . "</div>
                    <div id='pause_button' class='hidden' style='background-image: url(\"$pause_url\");'>" . esc_html__('Pause', 'simple-radio-forty-two') . "</div>
                    <div id='round' style='background-image: url(\"$round_url\");'></div>
                </div>
            </div>
            <div id='wait' class='center'><p>" . esc_html__('Please wait...', 'simple-radio-forty-two') . "</p></div>
            <div id='offline' class='center hidden'><p>" . esc_html__('Offline :/', 'simple-radio-forty-two') . "</p></div>
            <div id='vlc' class='center large hidden'>
                <p>$podcast_url</p>
            </div>
            <div id='listeners'><span id='num'></span> <span id='str'></span></div>
            <div id='footer_cause'>" . esc_html__('Listen in VLC', 'simple-radio-forty-two') . "</div>
            <div id='volume-controls'>
        <input id='volumeslider' type='range' min='0' max='100' value='100' step='1' />
    </div>
        </div>";
}

add_shortcode('sr42_radio', 'sr42_simple_radio_shortcode');

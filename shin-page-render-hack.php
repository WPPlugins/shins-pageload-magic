<?php

/**
 * Shin's Pageload Magic
 *
 * Shin's Pageload Magic is a lightweight Wordpress plugin
 * that dramatically boosts your page's rendering speed.
 * It's very amazing!
 *
 * @link              https://hello.appseeds.net/magic/
 * @since             1.0.2
 * @package           Shin_Page_Render_Hack
 *
 * @wordpress-plugin
 * Plugin Name:       Shin's Pageload Magic
 * Plugin URI:        https://hello.appseeds.net/magic/
 * Description:       A lightweight Wordpress plugin that dramatically boosts your page's render speed.
 * Version:           1.0.2
 * Author:            Shin
 * Author URI:        http://shinsenter.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       shin-page-render-hack
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

function activate_shin_page_render_hack() {
    // Buy me a beer
}

function deactivate_shin_page_render_hack() {
    // See ya
}

function shin_lazy_css_buffer_start() {
    ob_start('shin_lazy_css_hack');
}

register_activation_hook(__FILE__, 'activate_shin_page_render_hack');
register_deactivation_hook(__FILE__, 'deactivate_shin_page_render_hack');


/**
 * The heart of this plugin, if it works please share your enjoyment
 *
 * @param  string $html
 */
function shin_lazy_css_hack($html) {
    if (stripos($html, '<link ') !== FALSE) {
        $pattern = '/\s*(<link[^>]*(stylesheet|\.css)[^>]*>)\s*/mi';
        $html    = preg_replace_callback($pattern, function ($matches) {
            return shin_lazy_css_render($matches[1]);
        }, $html);
    }

    return $html;
}

function shin_lazy_css_render($html) {
    static $pattern = NULL;

    if(is_null($pattern)) {
        $to_exclude = array(
            preg_quote(str_ireplace(home_url() . '/', '', plugins_url()), '/'),
            'googleapis',
            'font(-awesome)?',
            'icomoon',
        );

        $pattern = '/' . implode('|', $to_exclude) . '/mi';
    }

    if (stripos($html, 'href=') !== FALSE && preg_match($pattern, $html)) {
        if (stripos($html, 'media=') !== FALSE && stripos($html, 'onload=') === FALSE) {
            $html = preg_replace('/\s+media=["\']([^"\']*)["\']/i', ' media="print" onload="media=\'$1\'"', $html);
        } else {
            $html = preg_replace('/<link /i', '<link media="print" onload="media=\'all\'"', $html);
        }
    } else if (stripos($html, '<style ') !== FALSE) {
        $html = preg_replace('/\s{2,}/m', ' ', $html);
    }

    return $html;
}

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_shin_page_render_hack() {

    // Well, I'm gonna tune your CSS loads
    // Stay tuned

    if (!is_admin()) {
        add_action('get_header', 'shin_lazy_css_buffer_start');
    } else {
        add_filter('style_loader_tag', 'shin_lazy_css_render', 1304, 1);
    }

}

run_shin_page_render_hack();

<?php
/**
 * Plugin Name: 000 User Info Display
 * Plugin URI: https://webyblog.es/
 * Description: Muestra la información del usuario logueado [ver_user_info].
 * Version: 07-01-2024
 * Author: Juan Luis Martel
 * Author URI: https://webyblog.es/
 * License: GPL2
 */

// Prevenir acceso directo al archivo del plugin
if ( ! defined( 'ABSPATH' ) ) exit;


// Enlace a documento de ayuda del plugin
function jlmr_mensaje_ayuda_shortcode_ver_user_info( $links_array, $plugin_file_name, $plugin_data, $status ) {
    if ( strpos( $plugin_file_name, basename(__FILE__) ) ) {
        // Construye la URL del archivo de ayuda
        $ayuda_url = plugins_url( 'ayuda.html', __FILE__ );

        // Añade el enlace de 'Ayuda' al final de la lista de enlaces
        $links_array[] = '<a rel="noopener noreferrer nofollow" href="' . esc_url( $ayuda_url ) . '" target="_blank">Ayuda</a>';
    }
    return $links_array;
}
add_filter( 'plugin_row_meta', 'jlmr_mensaje_ayuda_shortcode_ver_user_info', 10, 4 );


// Función para verificar la presencia del shortcode y cargar el CSS si es necesario.
function jlmr_enqueue_styles_conditional() {
    global $post;

    if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'ver_user_info')) {
        // Añadir un número de versión al CSS para evitar conflictos de cache
        wp_enqueue_style('user-info-style', plugin_dir_url(__FILE__) . 'user-info-style.css', array(), '1.0.0');
    }
}
add_action('wp_enqueue_scripts', 'jlmr_enqueue_styles_conditional');


// Shortcode para mostrar la información del usuario si esta logeado [ver_user_info].
function jlmr_show_user_info() {
    if (is_user_logged_in()) {
        $current_user = wp_get_current_user();
        $user_meta = get_user_meta($current_user->ID, 'jl_user_ayuda', true);

        $html = '<div class="user-info-container">';
        $html .= '<div class="user-id">ID del Usuario: ' . esc_html($current_user->ID) . '</div>';
        $html .= '<div class="user-username">Nombre de Usuario: ' . esc_html($current_user->user_login) . '</div>';
        $html .= '<div class="user-displayname">Nombre para Mostrar: ' . esc_html($current_user->display_name) . '</div>';
        $html .= '<div class="user-email">Correo Electrónico: ' . esc_html($current_user->user_email) . '</div>';
        $html .= '<div class="user-registered">Fecha de Registro: ' . esc_html($current_user->user_registered) . '</div>';
        $html .= '<div class="user-roles">Roles y Capacidades: ' . implode(', ', $current_user->roles) . '</div>';
        $html .= '<div class="user-avatar">Avatar: ' . get_avatar($current_user->ID) . '</div>';
        $html .= '<div class="user-meta">Meta Datos Adicionales: ' . esc_html($user_meta) . '</div>';
        $html .= '</div>';

        return $html;
    } else {
        return '<p>Usuario no logueado.</p>';
    }
}
add_shortcode('ver_user_info', 'jlmr_show_user_info');

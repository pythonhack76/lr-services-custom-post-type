<?php
/*
Plugin Name: Custom Services Plugin
Description: Plugin per gestire i servizi offerti.
Version: 1.0
Author: Luca Rulvoni
*/

// Funzione per registrare la Custom Post Type "Servizi"
function custom_services_post_type() {
    $labels = array(
        'name'               => 'Servizi',
        'singular_name'      => 'Servizio',
        'menu_name'          => 'Servizi',
        'add_new'            => 'Aggiungi Nuovo',
        'add_new_item'       => 'Aggiungi Nuovo Servizio',
        'edit_item'          => 'Modifica Servizio',
        'new_item'           => 'Nuovo Servizio',
        'view_item'          => 'Visualizza Servizio',
        'search_items'       => 'Cerca Servizi',
        'not_found'          => 'Nessun Servizio trovato',
        'not_found_in_trash' => 'Nessun Servizio trovato nel cestino',
    );

    $args = array(
        'labels'        => $labels,
        'public'        => true,
        'has_archive'   => true,
        'menu_position' => 5,
        'menu_icon'     => 'dashicons-admin-generic',
        'supports'      => array('title', 'editor', 'thumbnail', 'custom-fields'),
    );

    register_post_type('custom_service', $args);
}
add_action('init', 'custom_services_post_type');

// Funzione per aggiungere campi personalizzati ai servizi
function custom_services_custom_fields() {
    add_meta_box(
        'custom_service_date',
        'Date del Servizio',
        'custom_service_date_callback',
        'custom_service',
        'normal',
        'default'
    );

    add_meta_box(
        'custom_service_price',
        'Prezzo del Servizio',
        'custom_service_price_callback',
        'custom_service',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'custom_services_custom_fields');

// Callback per il campo data di inizio e data di fine
function custom_service_date_callback() {
    global $post;

    // Otteniamo i valori salvati dei campi personalizzati, se esistono
    $start_date = get_post_meta($post->ID, 'custom_service_start_date', true);
    $end_date = get_post_meta($post->ID, 'custom_service_end_date', true);

    // Utilizziamo il campo nonce per verificare la sicurezza
    wp_nonce_field('custom_service_date_nonce', 'custom_service_date_nonce');

    // Mostrare l'input per la data di inizio
    echo '<label for="custom_service_start_date">Data di Inizio:</label>';
    echo '<input type="date" id="custom_service_start_date" name="custom_service_start_date" value="' . esc_attr($start_date) . '">';

    // Mostrare l'input per la data di fine
    echo '<label for="custom_service_end_date">Data di Fine:</label>';
    echo '<input type="date" id="custom_service_end_date" name="custom_service_end_date" value="' . esc_attr($end_date) . '">';
}

// Callback per il campo prezzo
function custom_service_price_callback() {
    global $post;

    // Otteniamo il valore salvato del campo personalizzato, se esiste
    $price = get_post_meta($post->ID, 'custom_service_price', true);

    // Utilizziamo il campo nonce per verificare la sicurezza
    wp_nonce_field('custom_service_price_nonce', 'custom_service_price_nonce');

    // Mostrare l'input per il prezzo
    echo '<label for="custom_service_price">Prezzo:</label>';
    echo '<input type="text" id="custom_service_price" name="custom_service_price" value="' . esc_attr($price) . '">';
}

// Funzione per salvare i valori dei campi personalizzati quando viene salvato il servizio
function custom_services_save_custom_fields($post_id) {
    // Verifichiamo il nonce per la sicurezza
    if (!isset($_POST['custom_service_date_nonce']) || !wp_verify_nonce($_POST['custom_service_date_nonce'], 'custom_service_date_nonce')) {
        return;
    }

    if (!isset($_POST['custom_service_price_nonce']) || !wp_verify_nonce($_POST['custom_service_price_nonce'], 'custom_service_price_nonce')) {
        return;
    }

    // Salviamo i valori dei campi personalizzati
    if (isset($_POST['custom_service_start_date'])) {
        update_post_meta($post_id, 'custom_service_start_date', sanitize_text_field($_POST['custom_service_start_date']));
    }

    if (isset($_POST['custom_service_end_date'])) {
        update_post_meta($post_id, 'custom_service_end_date', sanitize_text_field($_POST['custom_service_end_date']));
    }

    if (isset($_POST['custom_service_price'])) {
        update_post_meta($post_id, 'custom_service_price', sanitize_text_field($_POST['custom_service_price']));
    }
}
add_action('save_post', 'custom_services_save_custom_fields');
<?php

add_filter('pre_render_context_destination-individual', 'destination_individual');
function destination_individual($context) 
{
    $current_page_id = get_the_ID();
    $location_code = get_post_meta($current_page_id, 'IYC_destination_key', true);

    $args = [
        'post_type'  => 'yacht_feed',
        'meta_query' => yacht_meta_query([
            'ylocations' => $location_code
        ]),
        'meta_key' => 'price_from',
        'orderby' => 'meta_value_num',
        'order' => 'ASC' 
    ];

    $context = $context + [
        'post' => Timber\Timber::get_post(),
        'images' => get_field('iyc_gallery'),
        'sidebar' => Timber::compile('parts/content-sidebar.twig'),
        'boats' => Timber::get_posts($args)
    ];
    
    return $context;
}
<?php

add_filter('pre_render_context_destination-individual', 'destination_individual');
function destination_individual($context) 
{
    $context = $context + [
        'post' => Timber\Timber::get_post(),
        'images' => get_field('iyc_gallery'),
        //Looping through database of yachts and returning all boats
        'sidebar' => Timber::compile('parts/content-sidebar.twig'),
        'boats' => ''
    ];

    $locations = set_locations();
    $yacht_boats = create_boat_array();
    $current_page_id = get_the_ID();
    $boat_number = 1;

    //Add boats if in location
    $yacht_boats_in_location = array();

    foreach ($yacht_boats as $yacht_boat) {

        //Finding if boat exists in location
        $yacht_locations = explode(', ', $yacht_boat['location']);
        
        //Adding each boat that fits each location to the $yacht_boats_in_location[] array
        foreach ($yacht_locations as $yacht_location) {

            //Stripping found key
            $key = (int)str_replace('src', '', array_search($yacht_location, $locations));

            //Checking if the key matches the current_page_id then adding boat
            if ($key == $current_page_id) {	
                $yacht_boats_in_location[] = $yacht_boat;
            }
        }

    }

    //Sorting Boats By Price
    usort($yacht_boats_in_location, build_sorter('price_from'));

    //Displaying Each Boat
    foreach ($yacht_boats_in_location as $array) {

        //option to restrict number of boats on the page
        if ($boat_number <= 9999) {
            ob_start();
            create_boat($array);
            $context['boats'] .= ob_get_clean();
            $boat_number++;
        }

    }

    return $context;
}
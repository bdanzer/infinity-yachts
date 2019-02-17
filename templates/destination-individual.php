<?php get_header(); ?>

<div class="danzerpress-container-fw" style="padding: 40px 0;">
	<div class="danzerpress-wrap">
		<div class="danzerpress-flex-row danzerpress-row-fix">
			<div class="danzerpress-two-thirds">

				<h2>Gallery</h2>
				<?php 

				$images = get_field('iyc_gallery');
				
				if( $images ): ?>
					<div class="danzerpress-flex-row">
					        <?php foreach( $images as $image ):
					        	echo '
					            <div class="danzerpress-col-4 danzerpress-fix danzerpress-md-3 danzerpress-sm-2 danzerpress-xs-2 wow zoomIn">
					            	<div class="container" style="overflow:hidden;">
										<a data-fancybox="yacht" href="' . $image['sizes']['large'] . '"><img class="content" src="' . $image['sizes']['large'] . '"></a>
									</div>
								</div>
								';
					        endforeach; ?>
					</div>
				<?php endif; ?>

				<h2>About <?php echo ucwords(get_the_title()); ?></h2>

					<?php 
						if ( have_posts() ) {
							while ( have_posts() ) {
								the_post(); 
								//
								echo '<div class="">';
									the_content();
								echo '</div>';
								//
							} // end while
						} // end if
					?>
				<br>
					
				<h2 style="margin-bottom: 20px;">Yachts Available in <?php echo ucwords(get_the_title()); ?></h2>

				<div class="danzerpress-flex-row danzerpress-row-fix">
					<?php
					$boat_number = 1;

					//Get Locations
					$locations = set_locations();

					//Get Page ID
					$current_page_id = get_the_ID();

					//Add boats if in location
					$yacht_boats_in_location = array();

					//Looping through database of yachts and returning all boats
					$yacht_boats = create_boat_array();

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
							create_boat($array['yacht_image'], $array['yacht_name'], $array['price_from'], $array['price_to'], $array['guests'], $array['staterooms'], $array['boat_type'], $array['length'], $array['yacht_url']);
							$boat_number++;
						}

					}
					?>
				</div>
			</div>

			<div class="danzerpress-one-third">
				<?php ccm_get_template_part('template-parts/content','sidebar'); ?>
			</div>
		</div>
	</div>
</div>


<?php get_footer(); ?>
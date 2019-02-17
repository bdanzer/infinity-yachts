<?php
/* Template Name: Destination Page - DanzerPress Plugin */
?>

<?php 

get_header(); 
$locations = set_locations();

?>

<div class="danzerpress-container-fw" style="">
		<div class="danzerpress-flex-row">

			<?php

			foreach ($locations as $key => $location) {
				$destination_page_id = (int)str_replace('src', '', $key);
				$destination_page_url = get_post_permalink($destination_page_id);
				$destination_page_thumbnail = get_the_post_thumbnail_url($destination_page_id);
				$destination_page_title = get_the_title($destination_page_id);

				if ($destination_page_thumbnail == '') {
					$destination_page_thumbnail = danzerpress_no_image();
				}

				echo 
				'
					<div class="danzerpress-col-3 danzerpress-fix danzerpress-sm-2" style="overflow:hidden;">
					<a class="destination-link" href="' . $destination_page_url . '">
						<div class="container destination">
							<img class="content" src="' . $destination_page_thumbnail . '">

							<div class="centered">' . $destination_page_title . '</div>
						</div>
					</a>
					</div>
				';

			}

			?>


		</div>
</div>


<?php get_footer(); ?>
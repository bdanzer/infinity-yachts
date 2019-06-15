<?php
/* Template Name: Destination Page - DanzerPress Plugin */
?>

<?php 

get_header(); 
$locations = IYC\helpers\YachtHelper::get_locations();

?>

<div class="danzerpress-container-fw" style="">
		<div class="danzerpress-flex-row">

			<?php

			foreach ($locations as $key => $location) {
				$dest_post_id = get_post_id_from_dest_key($key);
				$destination_page_url = get_post_permalink($dest_post_id);
				$destination_page_thumbnail = get_the_post_thumbnail_url($dest_post_id);
				$destination_page_title = get_the_title($dest_post_id);

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
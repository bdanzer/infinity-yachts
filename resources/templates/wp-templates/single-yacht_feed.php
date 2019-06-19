<?php get_header(); ?>

	<div id="primary" class="danzerpress-container-fw danzerpress-grey" style="padding: 40px 0;">
		<main id="main" class="danzerpress-wrap">
			<div class="danzerpress-flex-row danzerpress-row-fix">
				<div class="danzerpress-two-thirds">
				

					<div class="" style="">
					<?php
					while ( have_posts() ) : the_post();

						//echo '<h2 class="">' . get_the_title() . '</h2>';
						//Boat Locations (feed)

						//Boat Video (not feed)
						// get iframe HTML
						if (get_field('iyc_video')) {
							$iframe = get_field('iyc_video');

							// use preg_match to find iframe src
							preg_match('/src="(.+?)"/', $iframe, $matches);
							$src = $matches[1];

							// add extra params to iframe src
							$params = array(
							    'controls'    => 1,
							    'hd'        => 1,
								'autohide'    => 1,
								'autoplay' => 1
							);

							$new_src = add_query_arg($params, $src);

							$iframe = str_replace($src, $new_src, $iframe);

							// add extra attributes to iframe html
							$attributes = 'frameborder="0"';

							$iframe = str_replace('></iframe>', ' ' . $attributes . '></iframe>', $iframe);

							$iframe = str_replace('<iframe', ' ' . '<iframe class="danzerpress-ab-items"' , $iframe);


							// echo $iframe
							echo '<div class="danzerpress-rectangle">';
								echo $iframe;
							echo '</div><br>';
						}

						//Boat Gallery (feed)
						echo '<h4 class="yacht-title">Gallery</h4>';
						//Solution from stackoverflow
						$xml_ebrochure_array = IYC\API::get_xml_ebrochure()['yacht'];

						// echo '<pre>';
						// var_dump($xml_ebrochure_array);
						// echo '</pre>';
						// die;

						//dp_clean($xml_ebrochure_array);
						

						//dp_clean($array);

						$name = 'yachtPic';
						$name_large = 'Large';

						$yacht_pic = array();
						$yacht_pic_large = array();

						$counter = 1;
						$counter_small = 1;


						$images = get_field('iyc_gallery');
						
						if( $images ) { ?>
							<div class="yacht-section wow fadeIn">
								<div class="slider-for" style="margin-bottom: 20px;">
									<div class="danzerpress-col-1 danzerpress-fix">
										<div class="danzerpress-white danzerpress-shadow-3 danzerpress-rectangle">
											<img class="danzerpress-ab-items" src="<?php echo get_the_post_thumbnail_url(); ?>">
										</div>
									</div>
							        <?php foreach( $images as $image ):
							        	echo '
										<div class="danzerpress-col-1 danzerpress-fix">
											<div class="danzerpress-white danzerpress-shadow-3 danzerpress-rectangle">
												<img class="danzerpress-ab-items" src="' . $image['sizes']['large'] . '">
											</div>
										</div>
										';
							        endforeach; ?>
						    	</div>
						    	<div class="slider-nav danzerpress-row-fix" style="margin-bottom: 20px;">
						    		<div class="danzerpress-col-3">
										<div class="danzerpress-white danzerpress-shadow-3 danzerpress-rectangle">
											<img class="danzerpress-ab-items" src="<?php echo get_the_post_thumbnail_url(); ?>">
										</div>
									</div>
							        <?php foreach( $images as $image ):
							        	echo '
										<div class="danzerpress-col-3">
											<div class="danzerpress-white danzerpress-shadow-3 danzerpress-rectangle">
												<img class="danzerpress-ab-items" src="' . $image['sizes']['large'] . '">
											</div>
										</div>
										';
							        endforeach; ?>
							    </div>
							</div>
						<?php } else { ?>
							<div class="yacht-section wow fadeIn">
								<div class="slider-for" style="margin-bottom: 20px;">
									<div class="danzerpress-col-1 danzerpress-fix">
										<div class="danzerpress-white danzerpress-shadow-3 danzerpress-rectangle">
											<img class="danzerpress-ab-items" src="<?php echo get_the_post_thumbnail_url(); ?>">
										</div>
									</div>
									<?php
										foreach ($xml_ebrochure_array as $key => $value) {
											if((strpos($key, $name) !== FALSE && strpos($key, $name_large) !== FALSE && $value != '')) {
												$yacht_pic_large[$key] = $value;
												$large_pic = $value;

												if (!is_array($value)) {
													echo '
													<div class="danzerpress-col-1 danzerpress-fix">
														<div class="danzerpress-white danzerpress-shadow-3 danzerpress-rectangle">
															<img class="danzerpress-ab-items" src="' . $value . '">
														</div>
													</div>
													';
												}
											}
										} 
									?>
								</div>
								<div class="slider-nav danzerpress-row-fix" style="margin-bottom: 20px;">
						    		<div class="danzerpress-col-3">
										<div class="danzerpress-white danzerpress-shadow-3 danzerpress-rectangle">
											<img class="danzerpress-ab-items" src="<?php echo get_the_post_thumbnail_url(); ?>">
										</div>
									</div>
									<?php
										foreach ($xml_ebrochure_array as $key => $value) {
											if((strpos($key, $name) !== FALSE && strpos($key, $name_large) !== FALSE && $value != '')) {
												$yacht_pic_large[$key] = $value;
												$large_pic = $value;

												if (!is_array($value)) {
													echo '
													<div class="danzerpress-col-3">
														<div class="danzerpress-white danzerpress-shadow-3 danzerpress-rectangle">
															<img class="danzerpress-ab-items" src="' . $value . '">
														</div>
													</div>
													';
												}
											}
										} 
									?>
								</div>
							</div>

						<?php 
						}

						//Boat Description (not feed)
						if (get_field('boat_description')) {
							echo '<h4 class="yacht-title">Description</h4>';
							echo '<div class="yacht-section wow fadeIn danzerpress-shadow-3 danzerpress-box danzerpress-white">';
							echo '<p>' . get_field('boat_description') . '</p>';
							echo '</div>';

						}

						echo '<h4 class="yacht-title">Specifications</h4>';
						echo '<div class="yacht-section wow fadeIn danzerpress-shadow-3 danzerpress-box danzerpress-white">';

							echo '<table>';
							$units = get_field('units');
								
								//Check units
								if (isset($xml_ebrochure_array['yachtUnits']) && $xml_ebrochure_array['yachtUnits'] == 'Metres' || $units == 'Metres') {
									$unit = 'm';
								} else {
									$unit = 'ft';
								}
							

								//echo 'Yacht Units (not going live): ' . $value->yachtUnits . ' <br>';
								if (get_field('guests')) {
									echo '<tr><td>Guests</td> <td>' . get_field('guests') . '</td></tr>';
								}

								if (get_field('staterooms')) {
									echo '<tr><td>Staterooms</td><td>' . get_field('staterooms') . ' </td></tr>';
								}

								if (get_field('length_feet') || get_field('length_meters') ) {
									echo '<tr><td>Length</td><td>' . get_field('length_feet') . ' / ' . get_field('length_meters') . '</td></tr>';
								}

								if (get_field('beam')) {
									echo '<tr><td>Beam</td><td>' . get_field('beam') . ' ' . $unit . '</td></tr>';
								}

								if (get_field('draft')) {
									echo '<tr><td>Draft</td><td>' . get_field('draft') . ' ' . $unit . '</td></tr>';
								}

								if (get_field('built') || get_field('refit')) {
									echo '<tr><td>Built - Refit:</td><td>' . get_field('built') . ' - ' . get_field('refit') . '</td></tr>';
								}

								if (get_field('builder')) {
									echo '<tr><td>Builder</td><td>' . get_field('builder') . '</td></tr>';
								}

								if (get_field('cruise_speed')) {
									echo '<tr><td>Cruise Speed</td><td>' .  get_field('cruise_speed') . '</td></tr>';
								}

								if (get_field('cruise_max_speed')) {
									echo '<tr><td>Maximum Speed</td><td>' .  get_field('cruise_max_speed') . '</td></tr>';
								}

							echo '</table>';
						echo '</div>';

						if ( isset($xml_ebrochure_array['yachtLayout']) && $xml_ebrochure_array['yachtLayout'] != '') {
							$yacht_layout_xml = $xml_ebrochure_array['yachtLayout'];
							echo '<h4 class="yacht-title">Layout</h4>';
							//Boat Layout picture (feed)
							//Boat Layout Description (don't need feed but will put it since they can edit it)
							echo '
							<div class="danzerpress-image-wrap" style="display:inline-block;margin-bottom:20px;">
								<a data-fancybox="yacht" href="' . $yacht_layout_xml . '"><img style="max-height:500px;" src="' . $yacht_layout_xml . '"></a>
							</div>
							';

						} elseif (get_field('yacht_layout')) {
							echo '<h4 class="yacht-title">Layout</h4>';
							echo '
							<div class="danzerpress-image-wrap" style="display:inline-block;margin-bottom:20px;">
								<a data-fancybox="yacht" href="' . get_field('yacht_layout') . '"><img style="max-height:500px;" src="' . get_field('yacht_layout') . '"></a>
							</div>
							';
						}

						echo '<h4 class="yacht-title">Watersports</h4>';
						echo '<div class="yacht-section wow fadeIn danzerpress-shadow-3 danzerpress-box danzerpress-white">';
							echo '<table>';
								if (get_field('dingy')) {
									echo '<tr><td>Dinghy</td><td>' . get_field('dingy') . '</td></tr>';
								}
								if (get_field('dingy_hp')) {
									echo '<tr><td>Dinghy HP</td><td>' . get_field('dingy_hp') . '</td></tr>';
								}
								if (get_field('paddle')) {
									echo '<tr><td>Paddle Boards</td><td>' . get_field('paddle') . '</td></tr>';
								}
								if (get_field('single_kayak')) {
									echo '<tr><td>Single Kayaks</td><td>' . get_field('single_kayak') . '</td></tr>';
								}
								if (get_field('double_kayak')) {
									echo '<tr><td>Double Kayaks</td><td>' . get_field('double_kayak') . '</td></tr>';
								}
								if (get_field('adult_water_skis')) {
									echo '<tr><td>Adult Water-skis</td><td>' . get_field('adult_water_skis') . '</td></tr>';
								}
								if (get_field('kid_water_skis')) {
									echo '<tr><td>Kids Water-skis</td><td>' . get_field('kid_water_skis') . '</td></tr>';
								}
								if (get_field('wakeboard')) {
									echo '<tr><td>Wakeboards</td><td>' . get_field('wakeboard') . '</td></tr>';
								}
								if (get_field('kneeboards')) {
									echo '<tr><td>Kneeboards</td><td>' . get_field('kneeboards') . '</td></tr>';
								}
								if (get_field('wave_runner')) {
									echo '<tr><td>WaveRunners</td><td>' . get_field('wave_runner') . '</td></tr>';
								}
								if (get_field('jet_skis')) {
									echo '<tr><td>Jet Skis</td><td>' . get_field('jet_skis') . '</td></tr>';
								}
								if (get_field('snorkel')) {
									echo '<tr><td>Snorkeling gear</td><td>' . get_field('snorkel') . '</td></tr>';
								}
								if (get_field('tube')) {
									echo '<tr><td>Inflatable, towable tubes</td><td>' . get_field('tube') . '</td></tr>';
								}
								if (get_field('fishing_gear')) {
									echo '<tr><td>Fishing Gear</td><td>' . get_field('fishing_gear') . '</td></tr>';
								}
								if (get_field('scuba_diving')) {
									echo '<tr><td>Scuba Diving</td><td>' . get_field('scuba_diving') . '</td></tr>';
								}
								if (get_field('scuba_compressor')) {
									echo '<tr><td>Scuba Compressor</td><td>' . get_field('scuba_compressor') . '</td></tr>';
								}
							echo '</table>';
						echo '</div>';

						echo '<h4 class="yacht-title">Pricing Details</h4>';
						$url_rates = 'https://www.centralyachtagent.com/snapins/carates-xml.php?idin=' . get_the_ID() . '&user=128';
						$rates_xml = simplexml_load_file($url_rates, 'SimpleXMLElement', LIBXML_NOCDATA);

						$rates_array = json_decode(json_encode($rates_xml), true);

						//dp_clean($rates_array);

						if (isset($rates_array['yacht']['season'][0])) {
							foreach ($rates_array['yacht']['season'] as $key => $rates_value) {
								echo '<span class="season-name">' . $rates_value['seasonName'] . '</span>';
								echo '<table style="max-width:400px;">';
									
									foreach ($rates_value as $key => $value) {

										if (strpos($key, 'Pax') !== FALSE && $value !== '&#36;0') {
											echo '
											<tr>
											<th>' . $key . '</th>
											<td>' . $value . '</td>
											</tr>
											';
										}

									}
						
								echo '</table>';
								echo '<br>';
							}
						} elseif (isset($xml_ebrochure_array['yachtPriceDetails']) && $xml_ebrochure_array['yachtPriceDetails'] != '') {
							echo '<div class="yacht-section wow fadeIn danzerpress-shadow-3 danzerpress-box danzerpress-white">';
								echo '<p>' . nl2br($xml_ebrochure_array['yachtPriceDetails']) . '</p>';
							echo '</div>';
						} elseif (get_field('price_from_iyc')) {
							$price = get_field('price_from_iyc');
							$nights_option = get_field('nights_option');
							$currency = get_field('currency');

							if ($currency == 'usd') {
								$sign = '$';
							} else {
								$sign = '€';
							}

							if ($nights_option == 6) {
								$nights = 6;
								echo '<table class="wow fadeIn" style="max-width: 400px;">';
									while ($nights <= 10) {
										$price_new = ($price / 7) * $nights;
										echo '
											<tr>
											<th>' . $nights . ' Nights</th>
											<td>' . $sign . ' ' . number_format($price_new) . '</td>
											</tr>
											';
										$nights++;
									}
								echo '</table><br>';
							} else {
								$nights = 7;
								echo '<table class="wow fadeIn" style="max-width: 400px;">';
									while ($nights <= 10) {
										$price_new = ($price / 7) * $nights;
										echo '
											<tr>
											<th>' . $nights . ' Nights</th>
											<td>' . $sign . ' ' . number_format($price_new) . '</td>
											</tr>
											';
										$nights++;
									}
								echo '</table><br>';
							}
						} else {
							echo '
							<div class="yacht-section wow fadeIn danzerpress-shadow-3 danzerpress-box danzerpress-white">
								<p>Contact Us to get more details</p>
							</div>';
						}

						echo '<h4 class="yacht-title">Crew Profile</h4>';
						echo '<div class="yacht-section wow fadeIn danzerpress-shadow-3 danzerpress-box danzerpress-white">';

							//dp_clean($xml_ebrochure_array);
							if (get_field('crew_description')) {
								$crew_profile = get_field('crew_description');
							} else {
								$crew_profile = $xml_ebrochure_array['yachtCrewProfile'];
							}

							if (get_field('crew_photo')) {
								$crew_photo = get_field('crew_photo');
							} else {
								$crew_photo = $xml_ebrochure_array['yachtCrewPhoto'];
							}

							if ($crew_photo != '' && !is_array($crew_photo)) {
								echo '<img src="' . $crew_photo . '"><br>';
							}

							echo $crew_profile;

						echo '</div>';
						

						//Boats need to be associated with destinations
						echo '<h4 class="yacht-title">Destinations / Sample Itenaries</h4>';
							echo '<div class="danzerpress-flex-row">';

								$yacht_locations = get_post_meta(get_the_ID(), 'dp_metabox_ylocations', true);

							    foreach ($yacht_locations as $yacht_key) {
							    	$destination_page_id = get_post_id_from_dest_key($yacht_key);
							    	$destination_page_url = get_post_permalink($destination_page_id);
							    	$destination_page_thumbnail = get_the_post_thumbnail_url($destination_page_id);
							    	$destination_page_title = get_the_title($destination_page_id);

							    	if ($destination_page_thumbnail == '') {
							    		$destination_page_thumbnail = danzerpress_no_image();
							    	}

							    	echo 
							    	'
							    		<div class="danzerpress-col-3 danzerpress-fix danzerpress-sm-2 wow zoomIn">
							    		<a class="destination-link" href="' . $destination_page_url . '">
							    			<div class="container destination" style="overflow:hidden;">
							    				<img class="content" src="' . $destination_page_thumbnail . '">

							    				<div class="centered">' . $destination_page_title . '</div>
							    			</div>
							    		</a>
							    		</div>
							    	';

							    }
						    echo '</div>';


					endwhile; // End of the loop.
					?>
					</div>

				</div>
				<div id="" class="danzerpress-one-third">
					<?php Timber\Timber::render('parts/content-sidebar.twig'); ?>
				</div>
			</div>
		</main><!-- #main -->
	</div><!-- #primary -->

		<?php 
			// If comments are open or we have at least one comment, load up the comment template.
			if ( comments_open() || get_comments_number() ) : ?>

			<div class="danzerpress-comments">	
				<div class="danzerpress-wrap" style="max-width: 900px;">
				<?php comments_template(); ?>
				</div>
			</div>

			<?php endif;

		?>
	

</main>

<?php get_footer(); ?>

<?php
/* Template Name: Infinity Yachts Form - DanzerPress */
?>

<?php get_header(); ?>

<div class="danzerpress-container-fw load-boats" style="padding: 40px 0;">
	<div class="danzerpress-wrap">
		<div class="danzerpress-flex-row danzerpress-row-fix">
			<div class="danzerpress-one-third">
				<div id="search-form" class="danzerpress-box danzerpress-white danzerpress-shadow-3">
					<h2 style="margin: 0 0 20px;">Search Yachts</h2>
					<form id="yacht-form" action="" method="POST">
						<div class="yacht-input">
							<label>Yacht Locations

								<?php if (isset($_POST['ylocations'])) {
										$ylocations = $_POST['ylocations'];
									} else {
										$ylocations = 1;
									}

									$locations = IYC\helpers\YachtHelper::get_locations();

									if ($ylocations != 0 && isset($_POST['ylocations'])) {
										echo '<span>(added location: ' . $locations[$ylocations] . ')</span>'; 
									}

									?>

									<select id="ylocations" name="ylocations" style="display:block;">
										<option id="all-locations" value="0" <?php if ($ylocations == 0) {
											echo 'selected';
										} ?> >All Locations</option>
									<?php

									foreach ($locations as $key => $value) {

										if ($ylocations == $key) {
											$select = 'selected';
										} else {
											$select = '';
										}
										echo 
										'
											<option ' . $select . ' id="' . $key . '" value="' . $key . '">' . ucwords($value) . '</option>
										';
									}

									?>
									</select>
								
							</label>
						</div>

						<div class="yacht-input">
							<label>Price Range
								<select id="price" name="price" id="price">
									<?php
										$pricing_options = IYC\helpers\YachtHelper::get_pricing_options();
										foreach ($pricing_options as $key => $option) {
											echo '<option value="' . $key . '">' . $option . '</option>';
										}
									?>
								</select>
							</label>
						</div>

						<div class="yacht-input">
							<label>Boat Type
								<select name="type" id="type">
									<?php
										$boat_types = IYC\helpers\YachtHelper::get_boat_types();
										foreach ($boat_types as $key => $boat_type) {
											echo '<option value="' . $key . '">' . $boat_type . '</option>';
										}
									?>
								</select>
							</label>
						</div>

						<div class="yacht-input">
							<label># of Staterooms </label>
							<input id="staterooms" name="staterooms" type="text" class="text" placeholder="Number of Staterooms">
						</div>

						<div class="yacht-input">
							<label>Yacht Name </label>
							<input id="yacht-name" name="name" type="text" class="text full" placeholder="Yacht Name">
						</div>

						<div class="yacht-input">
							<label># of Guests </label>
							<input id="guests" name="guests" type="text" class="text" placeholder="Number of Guests">
						</div>

						<div class="yacht-input">
							<label>Length (ft) </label>
							<select name="len" id="len">
								<?php
									$boat_lengths = IYC\helpers\YachtHelper::get_boat_lengths();
									foreach ($boat_lengths as $key => $boat_length) {	
										echo '<option value="' . $key . '">' . $boat_length . '</option>';
									}
								?>
							</select>
						</div>

						<input name="submit" type="submit" value="Search Yachts" id="submit">
					</form>
				</div>
			</div>

			<div class="yacht-boats-container danzerpress-two-thirds danzerpress-row-fix">
				<!-- <select style="max-width: 400px;display: block; text-align: right;margin-bottom: 20px;">
					<option>Filters</option>
					<option>Sort by Price High</option>
				</select> -->
				<div id="results" class="ajax-response danzerpress-flex-row danzerpress-row-fix">
					<div class="danzerpress-col-1">
						<div class="danzerpress-box danzerpress-white" style="border-left: 2px solid green;">
							<h4>Start Searching for Yachts</h4>
						</div>
					</div>
				</div>
			</div>

		</div>
	</div>
</div>



<?php get_footer(); ?>
<?php
	$labName = 'name_'.strtolower($app_lang);
	$address = 'address_'.strtolower($app_lang);
	$path	 = "assets/camlis/images/laboratory/";
?>
<div class="col-sm-12">

    <div class="filter-box">
        <div class="row">
            <div class="col-sm-6">
                <label class="control-label"><?php echo _t('global.laboratory'); ?></label>
                <select name="laboratories" id="filter-laboratories" class="form-control">
                    <option value="-1"><?php echo _t('global.choose'); ?></option>
                    <?php
                    foreach ($laboratories as $lab) {
                        echo "<option value='".$lab->labID."'>".$lab->$labName."</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-sm-2">
                <label class="control-label">&nbsp;</label><br />
                <button class="btn btn-primary" id="btn-enter-laboratory"><?php echo _t('laboratory.enter_lab'); ?></button>
            </div>
        </div>
    </div>
    <br>

	<div id="lab-list-wrapper">
        <?php if (count($this->aauth->get_user_groups()) > 0) { ?>
		<div class="row">
			<?php foreach($laboratories as $lab) { ?>
				<a class="item" href="<?php echo $this->app_language->site_url("laboratory/change/".$lab->labID); ?>">
					<div class="image">
						<?php
							$icon = "";
							if ($lab->photo && file_exists("./".$path."/".$lab->photo)) {
								$icon = site_url($path.$lab->photo);
							} else {
								$icon = site_url($path."no-icon.png");
							}
						?>
						<img src="<?php echo $icon ?>" alt="icon">
					</div>
					<div class="caption">
						<h5><?php echo $lab->$labName; ?></h5>
						<div class="location-wrapper">
							<i class="fa fa-map-marker"></i>
							<span class="location"><?php echo $lab->$address; ?></span>
						</div>
					</div>
				</a>
			<?php } ?>
		</div>
        <?php } ?>
	</div>
</div>
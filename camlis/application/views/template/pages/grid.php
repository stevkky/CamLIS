<script>
	const PROVINCES             = <?php echo json_encode($provinces); ?>;
	// ADDED 22-03-2021 FOR LINE LIST
	const DISTRICTS             = <?php echo json_encode($districts); ?>;
	const COMMUNES             = <?php echo json_encode($communes); ?>;
	const VILLAGES             = <?php echo json_encode($villages); ?>;
	const NATIONALITIES        = <?php echo json_encode($nationalities); ?>;
	const COUNTRIES        	   = <?php echo json_encode($countries); ?>;	
	const site_url 				= '<?php echo site_url()?>';
</script>
<?php 
//print_r($_SESSION);
//print_r($user);
?>
<div class="card">
  <h5 class="card-header"><p>(*): ពត៍មានចាំបាច់ ត្រូវតែបំពេញ | <span class="text-warning" style="background-color: yellow;">(*)អាចបញ្ចូលម្តងបានច្រើនបំផុតតែ 100ជួរប៉ុណ្ណោះ</span> | *លេខសំគាល់អ្នកជំងឺនឹងដាក់បញ្ចូលអូតូទៅតាមខេត្តក្រុងរបស់លោកអ្នក</p></h5>
  <div class="card-body">
	<div class="col-sm-12" style="margin-top:10px; position:relative; max-width:99%; overflow:auto;">
		<div id="spreadsheet"></div>
	</div>
  </div>
  <div class="card-footer">
  	<div class="row form-horizontal">
		<div class="col-sm-5 text-left">
			<button type="button" class="btn btn-success" id="btnOpenModalUpload"><i class="fa fa-upload"></i>&nbsp;<?php echo _t('global.upload'); ?></button>	
		</div>
			
		<div class="col-sm-7 text-right">
			<button type="button" class="btn btn-primary" id="btnSaveList"><i class="fa fa-floppy-o"></i>&nbsp;
				<?php echo _t('global.save'); ?>
			</button>
			<button type="button" class="btn btn-default" data-dismiss='modal'>
				<?php echo _t('global.cancel'); ?>
			</button>
		</div>
	</div>
  </div>
</div>
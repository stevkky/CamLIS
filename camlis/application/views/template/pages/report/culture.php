<?php
    $laboratory_logo_url = site_url('assets/camlis/images/moh_logo.png');
    if (isset($laboratoryInfo->photo) && !empty($laboratoryInfo->photo)  && file_exists('./assets/camlis/images/laboratory/'.$laboratoryInfo->photo)) {
        $laboratory_logo_url = site_url('assets/camlis/images/laboratory/'.$laboratoryInfo->photo);
    }
?>
<script>
	var msg_required_data = '<?php echo _t('global.msg.fill_required_data'); ?>';
	var date_required_startend = '<?php echo _t('global.msg.date_required_startend'); ?>';
</script>  
<style>
	.ui-progressbar { height:2em; text-align: left; overflow: hidden; }
	.ui-progressbar .ui-progressbar-value {margin: -1px; height:100%; }
</style>
<div class="wrapper col-sm-9">
	<div class="form-vertical">
		<div class="row">
			<div class="col-sm-4">
				<label for="start_date" class="control-label hint--right hint--error hint--always"><?php echo _t('report.start_receive_date');?></label>
				<div class="input-group">
					<input type="text" class="form-control" id="start_date" name="start_date"/>
					<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
				</div>
			</div>
			<div class="col-sm-4">
				<label for="end_date" class="control-label hint--right hint--error hint--always"><?php echo _t('report.end_receive_date'); ?></label>
				<div class="input-group">
					<input type="text" class="form-control"  id="end_date" name="end_date"/>
					<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
				</div>
			</div>
			<div class="col-sm-4">
				<label for="lab_nameKH" class="control-label hint--right hint--error hint--always">&nbsp;</label>
				<div>
					<button type="button" id="btnSearchCulture" class="btn btn-primary"><?php echo _t('report.filter'); ?>
						&nbsp;&nbsp;<i class="fa fa-search"></i>
					</button>
					<button type="button" id="btnPrint" disabled class="btn btn-primary">
						<?php echo _t('report.print'); ?>&nbsp;&nbsp;<i class="fa fa-print"></i>
					</button> 
				</div>
			</div>
		</div>
	</div>
	<div class="row adm_lab_btnWrapper">
		<div class="col-sm-12">
			<!-- header logo -->
			<div id="header" class="printable" style="height:110px; display:none">
				<img style="position:absolute;" src="<?php echo $laboratory_logo_url; ?>" width="77" align="left"/>
				<div style="text-align:center;position:relative;">
					<h3 style="color:#558fd5;"><?php echo $laboratoryInfo->name_kh?></h3>
					<h4 style="font-size:22px;color:#558fd5;"><?php echo $laboratoryInfo->name_en?></h4>
				</div>
			</div><!-- end -->
			<div class="printable" style="display:none; text-align:center; font-size:24px; font-weight:bold">
				Culture Report
				<h4 style="color:#558fd5;"1	q><?php echo _t('report.start_date');?>
					<span id="spstart"></span>---<?php echo _t('report.end_date');?>
					<span id="spend"></span>
				</h4>
			</div>
			<div class="printable">
				<table class="table table-bordered table-striped" id="tableCulture">
					<tbody id="tbodyDataCulture" style="display: none;">
						<tr id="year"></tr>
						<tr id="month"></tr>
						<tr></tr>
						<tr id="total">
							<td rowspan="2"></td>
						</tr>
						<tr></tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	window.onload=function(){

		jQuery.fn.extend({
			printElem: function() {
				var cloned = this.clone();
				var printSection = $('#printSection');
				if (printSection.length == 0) {
					printSection = $('<div id="printSection"></div>')
					$('body').append(printSection);
				}
				printSection.append(cloned);
				var toggleBody = $('body *:visible');
				toggleBody.hide();
				$('#printSection, #printSection *').show();
				window.print();
				printSection.remove();
				toggleBody.show();
			}
		});

		$(document).ready(function(){
			$(document).on('click', '#btnPrint', function(){
				$('#spstart').text($('#start_date').val());
				$('#spend').text($('#end_date').val());
				$('.printable').printElem();
			});
		});
	}
	
	$(document).ready(function () {

		$("#start_date").datetimepicker({
			widgetPositioning : {
				horizontal	: 'left',
				vertical	: 'bottom'
			},
			showClear		: true,
			format			: 'DD-MM-YYYY',
			useCurrent		: false,
			locale			: app_lang == 'kh' ? 'km' : 'en'
		});

		$("#end_date").datetimepicker({
			widgetPositioning : {
				horizontal	: 'left',
				vertical	: 'bottom'
			},
			showClear		: true,
			format			: 'DD-MM-YYYY',
			useCurrent		: false,
			locale			: app_lang == 'kh' ? 'km' : 'en'
		});

	});
</script>
<div class="wrapper col-sm-9"> 
		<div class="form-vertical">
			
            <?php
                $is_admin = $this->aauth->is_admin();
            ?>
            <div class="row"> 
				<div class="col-sm-4">
					<label for="lab_nameEN" class="control-label hint--right hint--error hint--always"><?php echo _t('report.laboratory'); ?></label>
					<div>
						<select class="form-control" multiple id="sllabo_type">
                            <?php if($is_admin){?>
                            <option value="">... all ...</option>
                            <?php } ?>
                        	<?php foreach($labo_type as $row){?>
                            	<option value="<?php echo $row["labID"]?>"><?php echo $row["name_".$app_lang.""]?></option>
                            <?php } ?>
                        </select>
					</div>
				</div>
				<!--div class="col-sm-4">
					<label for="lab_nameKH" class="control-label hint--right hint--error hint--always"><?php echo _t('report.department'); ?></label>
					<div>
						<select class="form-control" multiple id="sldepartment">
                            <option value="">... all ...</option>
                        	< ?php foreach($department as $row){?>
                            	<option value="< ?php echo $row["ID"]?>">< ?php echo $row["department_name"]?></option>
                            < ?php } ?>
                        </select>
					</div>
				</div-->

                <div class="col-sm-4">
					<label for="lab_nameEN" class="control-label hint--right hint--error hint--always"><?php echo _t('report.start_receive_date'); ?></label>
					<div>
						<input type="text" class="form-control" id="criteria_start"/>
					</div>
				</div>
                
                <div class="col-sm-4">
					<label for="lab_nameKH" class="control-label hint--right hint--error hint--always"><?php echo _t('report.end_receive_date'); ?></label>
					<div>
						<input type="text" class="form-control"  id="criteria_end"/>
					</div>
				</div> ​
                <div class="col-sm-4">
                    <label for="lab_nameEN" class="control-label hint--right hint--error hint--always"><?php echo _t('report.labo_number'); ?></label>
                    <div>
                        <input type="text" class="form-control" id="txtlabo_number"/>
                    </div>
                </div>
                
			</div>  
            
            <div class="row"> 
				<div class="col-sm-4">
					<label for="lab_nameEN" class="control-label hint--right hint--error hint--always"><?php echo _t('report.sample_type'); ?></label>
					<div>
						<select class="form-control" multiple id="slsample_type">
                            <option value="">... all ...</option>
							<?php foreach($sample as $row){?>
                            	<option value="<?php echo $row["ID"]?>"><?php echo $row["sample_name"]?></option>
                            <?php } ?>
                        </select>
					</div>
				</div>
				<!--div class="col-sm-4">
					<label for="lab_nameKH" class="control-label hint--right hint--error hint--always"><?php echo _t('report.testing'); ?></label>
					<div>
						<select class="form-control" multiple id="sltesting">
                            <option value="">... all ...</option>
                        	< ?php foreach($test as $row){?>
                            	<option value="< ?php echo $row["ID"]?>">< ?php echo $row["test_name"]?></option>
                            < ?php } ?>
                        </select>
					</div>
				</div-->

                <div class="col-sm-4">
                    <label for="lab_nameKH" class="control-label hint--right hint--error hint--always"><?php echo _t('report.result'); ?></label>
                    <div>
                        <select class="form-control" multiple id="slresult">
                            <option value="">... all ...</option>
                            <?php foreach($result as $row){?>
                                <option value="<?php echo $row["organism_id"]?>"><?php echo $row["organism_name"]?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="col-sm-4">
                	<label for="lab_nameKH" class="control-label hint--right hint--error hint--always">&nbsp;</label>
					<div>
						<button type="button" id="btnSearchBacteriology" class="btn btn-primary"><?php echo _t('report.filter'); ?>&nbsp;&nbsp;
                    	<i class="fa fa-search"></i></button> 
					</div>
                    
                </div>  
                
			</div>   

        
	</div>   
</div>
 

<script type="text/javascript">
	// When the document is ready
	$(document).ready(function () {
		
		var dtPickerOption = {
			widgetPositioning : {
				horizontal	: 'left',
				vertical	: 'bottom'
			},
			showClear		: true,
			format			: 'DD/MM/YYYY',
			useCurrent		: false,
			maxDate			: new Date(),
			locale			: app_lang == 'kh' ? 'km' : 'en'
		};
		$("#criteria_start").datetimepicker(dtPickerOption).on("dp.change", function() {
			var dob = $(this).data("DateTimePicker").date(); 
		}); 
		
		$("#criteria_end").datetimepicker(dtPickerOption).on("dp.change", function() {
			var dob = $(this).data("DateTimePicker").date(); 
		}); 
	 
	
	}); 
	
	///
	$('#sldepartment').on('change',function(e){ 
		myDialog.showProgress('show'); 
		$.ajax({
				  method: "POST",
				  url	: base_url+'report/sample_by_dept',
				  dataType: "json",
				  data	: { 
					department		: $('#sldepartment').val()
				},
				success: function(data) {   
					//console.log(data);
					$('#slsample_type').empty();
					$.each(data, function (i, item) {  
						$('#slsample_type').append($('<option>', { 
							value: item.dept_sam_id,
							text : item.sample_name
						}));
					});
					myDialog.showProgress('hide');
				} 
			}); 
	}); 
	///
	$('#slsample_type').on('change',function(e){ 
		myDialog.showProgress('show'); 
		$.ajax({
				  method: "POST",
				  url	: base_url+'report/result_by_sample_test',
				  dataType: "json",
				  data	: {
                      sample_type	: $('#slsample_type').val(),
                      testing	: [170]
				},
				success: function(data) {   
					//console.log(data);
					$('#slresult').empty();
					$.each(data, function (i, item) {  
						/*$('#sltesting').append($('<option>', {
							value: item.sample_test_id,
							text : item.test_name
						}));*/
                        $('#slresult').append($('<option>', {
                            value: item.organism_id,
                            text : item.organism_name
                        }));
					});
					myDialog.showProgress('hide');
				} 
			}); 
	}); 
	
	///
	$('#sltesting').on('change',function(e){ 
		myDialog.showProgress('show'); 
		$.ajax({
				  method: "POST",
				  url	: base_url+'report/result_by_sample_test',
				  dataType: "json",
				  data	: { 
					testing		: $('#sltesting').val()
				},
				success: function(data) {   
					//console.log(data);
					$('#slresult').empty();
					$.each(data, function (i, item) {  
						$('#slresult').append($('<option>', { 
							value: item.organism_id,
							text : item.organism_name
						}));
					});
					myDialog.showProgress('hide');
				} 
			}); 
	}); 
	
	/* look up data patient id
	 * one character entry
	 */
	/*$("#txtlabo_number").autocomplete({
		minLength: 1, 
		source: function(request, response) {
					$.ajax({
						type	: "POST", 
						 url	: base_url+'report/lookup_labo_number',
						 data	: { 
							filter_val : $("#txtlabo_number").val() 
						},
						dataType: "json",
						success	: function (data) {
							 
							if (data != null) {
	
								response(data);
							}
						},
						error: function(result) {
							alert("Error");
						}
					});
				} 
	}).focus(function(){   
		$(this).autocomplete("search");
	}); */
	

</script>
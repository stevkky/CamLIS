 
<div class="wrapper col-sm-9"> 
    <div class="form-vertical" style="margin-left:20%"> 
         <div class="row"> 
            <div class="col-sm-3" >
                <input type="hidden" id="patient_sample_id" />
                <label for="lab_nameKH" class="control-label hint--right hint--error hint--always"><?php echo _t('user.role'); ?></label>
                <div>
                    <select class="form-control" id="username_id">
                        <option value='0'>... select ...</option>
                    <?php
                    $groups = $this->aauth->list_groups();
                    if ($groups) { foreach ($groups as $row) {
                        ?>
                         <?php
                                echo "<option value='". $row->id ."'>".$row->definition."</option>"; ?>

                    <?php }} ?>
                    </select>

                </div>
            </div>  
            
            <div class="col-sm-3">
                <label for="lab_nameKH" class="control-label hint--right hint--error hint--always">&nbsp;</label>
                <div>
                    <button type="button" id="btnFilter" class="btn btn-primary"><?php echo _t('global.filter'); ?>&nbsp;&nbsp;
                    <i class="fa fa-search"></i></button> 
                </div>
            </div> 
            
        </div> 
        
        <!-- report list -->
        <div style="border:0px solid #EEE;">
            Reports List 
            <div id="div_result">
            	<?php 
					foreach($report_name as $row){
				?>
                <input type="checkbox" disabled /> <?php echo $row->report_name;?> <br /> 
                <?php 
					}
				?>
            </div>
        </div>
        
    </div> <!-- row --> 
    
</div>
  
             
 
<script>
	$(function () { 
		/* look up data patient id
		 * one character entry
		 */
		$("#username_id").autocomplete({
			minLength: 1, 
			source: function(request, response) {
                        $.ajax({
                            type	: "POST", 
                             url	: base_url+'admin/lookup_user',
							 data	: { 
								filter_val : $("#username_id").val() 
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
		});
		
		
		// btn event filter 
		$('#btnFilter').click(function(){
			var _arr = $("#username_id").val().split(' - ');
			$.ajax({
				type	: "POST", 
				 url	: base_url+'admin/get_report_by_ajax',
				 data	: { 
					user_id : _arr[0] 
				}, 
				success	: function (data) {
						$('#div_result').html(data);  
				}
			});
		});  
	}); 
	
	
	
	// function checking reports set up
	function onReportCheck(rep_id){
		var _arr = $("#username_id").val().split(' - ');
		var status = ($('#rep'+rep_id).is(':checked'))?"insert":"delete";
		 
		$.ajax({
				type	: "POST", 
				 url	: base_url+'admin/assign_report_to_user',
				 data	: { 
					rep_id : rep_id,
					status : status,
					user_id : _arr[0] 
				}, 
				success	: function (data) { 
				}
			}); 
	}
	
</script>
 
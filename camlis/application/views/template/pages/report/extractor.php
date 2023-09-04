<?php
/*$patient_name = $report_form["report_label_patient_name"];
$patient_hospital_id = $report_form["report_label_patient_hospital_id"];
$age = $report_form["report_label_age"];
$gender = $report_form["report_table_gender"];
$province = $report_form["report_table_province"];
$district = $report_form["report_table_district"];
$sample_name = $report_form["report_label_sample_name"];
$sample_status = $report_form["report_label_sample_status"];
$sample_description = $report_form["report_label_sample_description"];
$sample_number = $report_form["report_label_sample_number"];
$collection_date = $report_form["report_label_collection_date"];
$received_date = $report_form["report_label_received_date"];
$sample_source = $report_form["report_label_sample_source"];
$requester = $report_form["report_label_requester"];
$test_date = $report_form["report_label_test_date"];
$test_name= $report_form["report_table_test_name"];
$result_organism = $report_form["report_label_result_organism"];
$antibiogram = $report_form["report_label_antibrigram"];
$antibiotic_sensitivity = $report_form["report_label_antibiotic_sensitive"];
$button_preview = $report_form['report_button_preview'];
$button_reset = $report_form['report_button_reset'];
$button_export_to_excel = $report_form['report_button_export_excel'];
$sample_volume=$report_form['report_label_sample_volume'];
$label_urgent=$report_form['report_label_urgent'];
$label_research=$report_form['report_label_research'];
$label_paid=$report_form['report_label_paid'];
$label_rejected=$report_form['report_label_rejected'];
$label_amount=$report_form['report_label_amount'];


//For Alias Field
$alias_patient_id_in_lab=$patient_hospital_id;
$alias_patient_name=$patient_name;
$alias_patient_age=$age;
$alias_Gender=$gender;
$alias_province_translation_name=$province;
$alias_district_translation_name=$district;
$alias_SampleName=$sample_name;
//Sample Status
$alias_Urgent=$label_urgent;
$alias_Research=$label_research;
$alias_Paid=$label_paid;
$alias_S_Rejected=$label_rejected;
$alias_SampleDesc=$sample_description;
$alias_SampleVolume=$sample_volume;
$alias_sample_number=$sample_number;
$alias_sample_source=$sample_source;
$alias_S_Requester=$requester;
$alias_result_test_date=$test_date;
$alias_test_translation_name=$test_name;
$alias_Result1=$result_organism;
$alias_Result2=$antibiogram;
$alias_sensitivity=$antibiotic_sensitivity;
$alias_sample_collection_date=$collection_date;
$alias_sample_received_date=$received_date;*/
?>

<style>
    #advance_search_block li {
        list-style-type: none;
        margin: 0;
        padding: 0;
    }
    #advance_search_block li span {
        color: #0088cc;
        text-align: right;
        width: 130px;
        float: left;
        margin-top: 5px;
    }
</style>

<form class="no-dirty-check">
<div id="report_generation_block">
    
	<div id="filter" style="float:left;width:400px;">

			<h4 style="margin-left:40px;">Input the value<input type="checkbox" id="chk_all" style="margin-left:206px;"/>
			</h4>

		<ul id="advance_search_block">			
			<li>
				<span>Patient Hospital ID: &nbsp;</span>
				<input type="text" id="txtpatientid" style="width: 200px;" />
				<input type="checkbox" name="patient_id_in_lab" class="chk"/>
			</li>

			<li>
				<span>Patient Name : &nbsp;</span>
				<input type="text" name="txt_patient_name" id="txt_patient_name" style="width: 200px;"/>
				<input type="checkbox" name="patient_name" class="chk" />
			</li>
			
			<li><span>Age : &nbsp;</span>
				<input type="text" name="txt_age_from" id="txt_age_from" size="2" style="width:84px;" />
				&nbsp;&nbsp;To&nbsp;&nbsp;
				<input type="text" name="txt_age_to" id="txt_age_to" size="2" style="width:84px;" />
				<input type="checkbox" name="patient_age"  class="chk" />
			</li>
			
			<li>
				<span>Gender : &nbsp;</span>
				<select name="cbo_gender" id="cbo_gender" style="width: 200px;">
                    	<option value="">--- gender ---</option>
                        <!--?php foreach ($genders as $val) : ?>
                        	<option value="< ?php echo $val->gender_translation_gender_id ?>">< ?php echo $val->gender_translation_name ?></option>
                        < ?php endforeach ; ?-->
                </select>
				<input type="checkbox" name="Gender" class="chk" />
			</li>
			
			<li>
				<span>Province : &nbsp;</span>
				<select name="cbo_province" id="cbo_province" style="width: 200px;">
                    	<option></option>
                        <!--?php foreach ($provinces as $val) : ?>
                        	<option value="< ?php echo $val->province_translation_province_id ?>">< ?php echo $val->province_translation_name ?></option>
                        < ?php endforeach ; ?-->
                </select>
				<input type="checkbox" name="province_translation_name" class="chk" />
			</li>
			
			<li>
				<span>District  : &nbsp;</span>
				<select name="cbo_district" id="cbo_district" style="width: 200px;">
                    	<option></option>
                        <!--?php foreach ($districts as $val) : ?>
                        	<option value="< ?php echo $val->district_translation_district_id ?>">< ?php echo $val->district_translation_name ?></option>
                        < ?php endforeach ; ?-->
                 </select>
				 <input type="checkbox" name="district_translation_name" class="chk" />
			</li>
			
			<li>
				<span>Sample Name : &nbsp;</span>
				<select class="cbo_sample_change" id="cbo_sample_name" name="cbo_sample_name" style="width: 200px;">
						<option></option>
						<!--?php
						foreach ($sampleName as $row) {							
							echo "<option value='" . $row -> sample_name_id . "'>" . $row -> sample_name_translation_name . "</option>";							
						}
						?-->
				</select>
				<input type="checkbox" name="SampleName" class="chk" />
			</li>
			
			<li>
			
				<span>Sample Status : &nbsp;</span>
				<select name="cbo_sample_status" id="cbo_sample_status" style="width: 200px;">
					<option></option>
					<option value="Urgent">Urgent</option>
					<option value="Research">Research sample</option>
					<option value="Paid">Paid patient</option>
					<option value="S_Rejected">Rejected sample</option>					
				</select>
				<input type="checkbox" name="sample_status" class="chk" />
			
			</li>
			
			<li>
				<span>Sample Description : &nbsp;</span>
				<select id="cbo_sample_description" style="width: 200px;">
                        <option></option>
                        <!--?php
						foreach ($sample_description_data as $row)
							echo "<option value='" . $row -> sample_id. "'>" . $row -> sample_desc . "</option>";						
                        ?-->
                    </select>
				<input type="checkbox" name="SampleDesc" class="chk" />
				
			</li>
			
			<li>
				<span>Sample Number : &nbsp;</span>
					<select id="cbo_sample_number" style="width: 200px;">
                        <option></option>
                        <?php
						foreach ($patientResult as $row)
							echo "<option value='" . $row -> sample_number. "'>" . $row -> sample_number . "</option>";
                        ?>
                    </select>
					<input type="checkbox" name="sample_number" class="chk" />
			</li>
			
			<li><span>Collection Date : &nbsp;</span>
				<input type="text" style="width:90px;" name="txt_collection_date_from" id="txt_collection_date_from" size="10"/>
				To 
				<input type="text" style="width:90px;" name="txt_collection_date_to" id="txt_collection_date_to" size="10"/>
				<input type="checkbox" name="sample_collection_date" class="chk" />
			</li>
			
			<li><span>Received Date : &nbsp;</span>
				<input type="text" style="width:90px;" name="txt_recieve_date_from" id="txt_recieve_date_from" size="10"/>
				To 
				<input type="text" style="width:90px;" name="txt_recieve_date_to" id="txt_recieve_date_to" size="10"/>
				<input type="checkbox" name="sample_received_date" class="chk" />
			</li>
			
			<li>
				<span>Sample Source : &nbsp;</span>
				<select id="cbo_sample_source" style="width: 200px;">
					<option></option>
					<!--?php
					foreach ($sampleSource as $row) {
						echo "<option value='" . $row -> sample_source_id . "'>" . $row -> sample_source_source . "</option>";
					}
					?-->
				</select>
				<input type="checkbox" name="sample_source" class="chk" />
			</li>
			
			<li>
				<span>Requester  : &nbsp;</span>
				<select id="cbo_requester" name="cbo_requester" style="width: 200px;">
				<option></option>
				<!--?php
				foreach ($sampleRequester as $row) {
					echo "<option value='" . $row -> requester_id . "'>" . $row -> requester_name . "</option>";
				}
				?-->
			</select>
			<input type="checkbox" name="S_Requester" class="chk" />
			</li>
			
			<li><span>Test Date : &nbsp;</span>
				<input type="text" style="width:90px;" name="txt_test_date_from" id="txt_test_date_from" size="10"/>
				To 
				<input type="text" style="width:90px;" name="txt_test_date_to" id="txt_test_date_to" size="10"/>
				<input type="checkbox" name="result_test_date" class="chk" />
			</li>
			
			<li>
				<span>Test Name : &nbsp;</span>
				
				<select id="cbo_test_name" name="cbo_test_name" style="width: 200px;">
				<option></option>
				<!--?php
					foreach ($sample_test_data as $row) {
						echo "<option value='" . $row -> test_id . "'>" . $row -> test_name . "</option>";
					}
				?-->
				</select>
				<input type="checkbox" name="test_translation_name" class="chk" />
			</li>
			
			<li>
				<span>Reusults/Organism : &nbsp;</span>
				<select id="cbo_result_organism" class="sample_change" name="cbo_result_organism" style="width: 200px;">
						<option></option>
						<!--?php
						foreach ($organism_data as $row) {							
							echo "<option value='" . $row -> organism_id . "'>" . $row ->organism_result_name . "</option>";							
						}
						?-->
					</select>
					<input type="checkbox" name="Result1" class="chk" />
			</li>
			
			<li><span>Antibiogram : &nbsp;</span>
				<select id="cbo_antibiogram" name="cbo_antibiogram" style="width: 200px;">
				<option></option>
				<!--?php
					foreach ($antibiogram_data as $row) {							
						echo "<option value='" . $row->antibiogram_id	 . "'>" . $row->antibiogram_result_name . "</option>";
					}
				?-->
				</select>
				<input type="checkbox" name="Result2" class="chk" />
			</li>
			
			<li>
				<span>Antibiotic Sensitivity : &nbsp;</span>
				<select id="cbo_sensitive" name="cbo_sensitive" style="width: 200px;">
					<option value=""></option>
					<option value="S">Sensitive</option>
					<option value="R">Resistance</option>
					<option value="I">Intermediate</option>
				</select>
				<input type="checkbox" name="sensitivity" class="chk" />
			</li>
						
		</ul>
	</div>

	<div id="show_result" style="float:left;width:620px;margin-left:25px;">
		<h4>Output Result</h4>
		
                <input type="button" class="btn btn-primary" id="btn_preview" value="preview" />
		                
                <input type="reset" id="btn_reset" class="btn btn-primary" value="reset" />
                
                <input type="button" class="btn btn-primary" id="btn_export_to_excel" value="export_to_excel" />
		
		<br /><br />
		<div class="scroll_section" style="overflow:auto;border:solid 1px #CCC;height:700px;">		
		<table id="list" class="table table-striped">
		
		</table>
		</div>
		
		
		
	</div>

	
</div>
</form>
<script type="text/javascript">

	$(document).ready(function(){			
		//$("#txt_collection_date_from,#txt_collection_date_to,#txt_test_date_from,#txt_test_date_to,#txt_recieve_date_from,#txt_recieve_date_to").datepicker({"dateFormat": "yy-mm-dd", changeMonth: true,changeYear: true});
		
                //$("#btn_reset").on("click",function(){			
		//	 window.location.href= "<?php //echo base_url() . $lang ?>/report/extractor/";			 
		//});

		
		$("#chk_all").on("click",function(){
			var result = $('#chk_all:checked').val();  			
			if(result == "on"){
				$('.chk').prop('checked', true);
			}else{
				$('.chk').prop('checked', false);
			}
		})
		
		$("#btn_export_to_excel").on("click",function(){
			exportExcel();
		})
		
		$("#btn_preview").on("click",function(){
				var patient_id = $("#txtpatientid").val();
				var patient_name = $("#txt_patient_name").val();				
				var age_to = $("#txt_age_to").val();
				var age_from = $("#txt_age_from").val();
				var patient_gender = $("#cbo_gender :selected").val();
				
				var province = $("#cbo_province :selected").val();
				var district = $("#cbo_district :selected").val();
				var sample_name = $("#cbo_sample_name :selected").val();
				var sample_status = $("#cbo_sample_status").val();
				var sample_description = $("#cbo_sample_description :selected").text();	
                                var sample_number = $("#cbo_sample_number").val();
        
				var collection_date_from = $("#txt_collection_date_from").val();
				var collection_date_to = $("#txt_collection_date_to").val();
				var recieve_date_from = $("#txt_recieve_date_from").val();
				var recieve_date_to = $("#txt_recieve_date_to").val();
				var sample_source = $("#cbo_sample_source :selected").text();
				var requester = $("#cbo_requester :selected").text();
				var test_date_from = $("#txt_test_date_from").val();
				var test_date_to = $("#txt_test_date_to").val();
				var test_name = $("#cbo_test_name :selected").val();
				var result_organism = $("#cbo_result_organism").val();
				var antibiogram = $("#cbo_antibiogram :selected").val();
				var sensitive = $("#cbo_sensitive :selected").val();
				
				var field_show = "";
                                var groupby="";
				$(".chk:checked").each(
				  function(index) {
				    if($(this).attr("name") == "SampleDesc"){												
						field_show = field_show + "SampleDesc AS `Sample Description`,SampleVolume AS `Sample Volume`,";
                                                groupby = groupby + "SampleDesc,SampleVolume,";	
					}else if($(this).attr("name") == "sample_status"){					
						field_show = field_show + "Urgent AS `Urgent`,Research AS `Research`,Paid AS `Paid`,S_Rejected AS `Rejected`,";
                                                groupby = groupby + "Urgent,Research,Paid,S_Rejected,";	
					}else{
						field_show = field_show + $(this).attr("name") + " AS `"+$(this).attr("field_alias")+"` ,";
                                                groupby = groupby + $(this).attr("name") + ",";
					}
				 });
				
				var create_condition = " AND 1=1 ";
				
					if(patient_id != ""){
						create_condition += " AND patient_id_in_lab LIKE '%"+patient_id+"%' ";
					}
					
					if(patient_name != ""){
						create_condition += " AND patient_name LIKE '%"+patient_name+"%' ";						
					}
					
					if(age_to != ""){					
						create_condition += " AND patient_age<=" + age_to + " ";						
					}
					
					if(age_from != ""){					
						create_condition += " AND patient_age>=" + age_from + " ";
					}
					
					if(patient_gender != ""){
						create_condition += " AND gender_translation_gender_id=" + patient_gender + " ";						
					}
					
					if(province != ""){
						create_condition += " AND province_translation_province_id=" + province + " ";						
					}
					
					if(district != ""){
						create_condition += " AND district_translation_district_id=" + district + " ";						
					}
						
					if(sample_name != ""){
						create_condition += " AND sample_name_translation_sample_name_id=" + sample_name + " ";						
					}	
						
					if(sample_status !=""){
						
						create_condition += " AND " + sample_status + "=true";						
					}	
					
					if(sample_description !=""){
						create_condition += " AND SampleDesc LIKE '%" + sample_description + "%' ";						
					}
					if(sample_source !=""){
						create_condition += " AND sample_source='" + sample_source + "' ";						
					}					
					
					if(sample_number !=""){
						create_condition += " AND sample_number='" + sample_number + "' ";
					}
					
					if(collection_date_from !=""){
						create_condition += " AND sample_collection_date>='" + collection_date_from + "' ";							
					}
					
					if(collection_date_to !=""){
						create_condition += " AND sample_collection_date<='" + collection_date_to + "' ";
					}					
					
					if(recieve_date_from !=""){
						create_condition += " AND sample_received_date>='" + recieve_date_from + "' ";						
					}
					
					if(recieve_date_to !=""){
						create_condition += " AND sample_received_date<='" + recieve_date_to + "' ";
					}	
					
					if(requester !=""){
						create_condition += " AND S_Requester='" + requester + "' ";						
					}				
										
					if(test_date_from !=""){
						create_condition += " AND result_test_date>='" + test_date_from + "' ";												
					}
					
					if(test_date_to !=""){
						create_condition += " AND result_test_date<='" + test_date_to + "' ";
					}	
					
					if(test_name !=""){
						create_condition += " AND test_translation_test_id=" + test_name + " ";				
					}	
					
//					//if(result_organism !=""){
//					//	create_condition += " AND Result1='" + result_organism + "' ";
//					//	group_by += "Result1 ";
//					//}	
					
//					if(antibiogram !=""){
//						create_condition += " AND Result2='" + antibiogram + "' ";						
//					}	
//					
//					if(sensitive !=""){
//						create_condition += " AND sensitivity='" + sensitive + "' ";						
//					}					
				
					if(field_show != ""){
					//block();
					$.ajax({
							 type : "POST",
							 url  : '<?php echo base_url() ?>/report/extractor_data/',
							 data: {condition: create_condition,field:field_show,group:groupby,label_amount:22},
							 success : function(data_result){						 
								 $("#list").html(data_result);								
								 //$.unblockUI();
							 },error: function(){
								//$.unblockUI();
								alert('An error occured. Please contact site administrator or owner');
							 }
						});	
					}else{
						alert("Please select which field you want to show");
					}
			
			});		
		
		
        function exportExcel()
        {	                        
            var patient_id = $("#txtpatientid").val();
            var patient_name = $("#txt_patient_name").val();				
            var age_to = $("#txt_age_to").val();
            var age_from = $("#txt_age_from").val();
            var patient_gender = $("#cbo_gender :selected").val();

            var province = $("#cbo_province :selected").val();
            var district = $("#cbo_district :selected").val();
            var sample_name = $("#cbo_sample_name :selected").val();
            var sample_status = $("#cbo_sample_status").val();
            var sample_description = $("#cbo_sample_description :selected").text();	
            var sample_number = $("#cbo_sample_number").val();

            var collection_date_from = $("#txt_collection_date_from").val();
            var collection_date_to = $("#txt_collection_date_to").val();
            var recieve_date_from = $("#txt_recieve_date_from").val();
            var recieve_date_to = $("#txt_recieve_date_to").val();
            var sample_source = $("#cbo_sample_source :selected").text();
            var requester = $("#cbo_requester :selected").text();
            var test_date_from = $("#txt_test_date_from").val();
            var test_date_to = $("#txt_test_date_to").val();
            var test_name = $("#cbo_test_name :selected").val();
            var result_organism = $("#cbo_result_organism").val();
            var antibiogram = $("#cbo_antibiogram :selected").val();
            var sensitive = $("#cbo_sensitive :selected").val();
				
            var field_show = "";
            var groupby="";
            $(".chk:checked").each(
              function(index) {
                if($(this).attr("name") == "SampleDesc"){												
                            field_show = field_show + "SampleDesc AS `Sample Description`,SampleVolume AS `Sample Volume`,";
                            groupby = groupby + "SampleDesc,SampleVolume,";	
                    }else if($(this).attr("name") == "sample_status"){					
                            field_show = field_show + "Urgent AS `Urgent`,Research AS `Research`,Paid AS `Paid`,S_Rejected AS `Rejected`,";
                            groupby = groupby + "Urgent,Research,Paid,S_Rejected,";	
                    }else{
                            field_show = field_show + $(this).attr("name") + " AS `"+$(this).attr("field_alias")+"` ,";
                            groupby = groupby + $(this).attr("name") + ",";
                    }
             });
				
            var create_condition = " AND 1=1 ";
				
            if(patient_id != ""){
                    create_condition += " AND patient_id_in_lab LIKE '%"+patient_id+"%' ";
            }

            if(patient_name != ""){
                    create_condition += " AND patient_name LIKE '%"+patient_name+"%' ";						
            }

            if(age_to != ""){					
                    create_condition += " AND patient_age<=" + age_to + " ";						
            }

            if(age_from != ""){					
                    create_condition += " AND patient_age>=" + age_from + " ";
            }

            if(patient_gender != ""){
                    create_condition += " AND gender_translation_gender_id=" + patient_gender + " ";						
            }

            if(province != ""){
                    create_condition += " AND province_translation_province_id=" + province + " ";						
            }
					
            if(district != ""){
                    create_condition += " AND district_translation_district_id=" + district + " ";						
            }

            if(sample_name != ""){
                    create_condition += " AND sample_name_translation_sample_name_id=" + sample_name + " ";						
            }	

            if(sample_status !=""){

                    create_condition += " AND " + sample_status + "=true";						
            }	

            if(sample_description !=""){
                    create_condition += " AND SampleDesc LIKE '%" + sample_description + "%' ";						
            }
            if(sample_source !=""){
                    create_condition += " AND sample_source='" + sample_source + "' ";						
            }					
					
            if(sample_number !=""){
                    create_condition += " AND sample_number='" + sample_number + "' ";
            }

            if(collection_date_from !=""){
                    create_condition += " AND sample_collection_date>='" + collection_date_from + "' ";							
            }

            if(collection_date_to !=""){
                    create_condition += " AND sample_collection_date<='" + collection_date_to + "' ";
            }					

            if(recieve_date_from !=""){
                    create_condition += " AND sample_received_date>='" + recieve_date_from + "' ";						
            }

            if(recieve_date_to !=""){
                    create_condition += " AND sample_received_date<='" + recieve_date_to + "' ";
            }	
					
            if(requester !=""){
                    create_condition += " AND S_Requester='" + requester + "' ";						
            }				

            if(test_date_from !=""){
                    create_condition += " AND result_test_date>='" + test_date_from + "' ";												
            }

            if(test_date_to !=""){
                    create_condition += " AND result_test_date<='" + test_date_to + "' ";
            }	

            if(test_name !=""){
                    create_condition += " AND test_translation_test_id=" + test_name + " ";				
            }	
					
            //					//if(result_organism !=""){
            //					//	create_condition += " AND Result1='" + result_organism + "' ";
            //					//	group_by += "Result1 ";
            //					//}	

            //					if(antibiogram !=""){
            //						create_condition += " AND Result2='" + antibiogram + "' ";						
            //					}	
            //					
            //					if(sensitive !=""){
            //						create_condition += " AND sensitivity='" + sensitive + "' ";						
            //					}					
				
            if(field_show != ""){
            ///block();
            $.ajax({
                type : "POST",
                url  : '<?php echo base_url() ?>/report/extractor_data_excel/',
                data: {condition: create_condition,field:field_show,group:groupby,label_amount:23},
                success : function(data){						                                      							
                    //$.unblockUI();
                    var json=$.parseJSON(data);
                    if(json.excel.length>0){
                        window.open(json.excel);
                    }
                },error: function(){
                      // $.unblockUI();
                       alert('An error occured. Please contact site administrator or owner');
                }
            });	
            }else{
                alert("Please select which field you want to export");
            }            
        }
		
		
	function block () {
		$.blockUI({ css: { 
			border: 'none', 
			padding: '15px', 
			backgroundColor: '#000', 
			'-webkit-border-radius': '10px', 
			'-moz-border-radius': '10px', 
			opacity: .5, 
			color: '#fff' 
		} }); 
	}
        
        
        $("#cbo_province").change (function () {
            //block();
            $.get("<?php echo base_url() ?>/patients", {"filter_province": $(this).val()}).done (function (data) {
                    $("#cbo_district").html(data) ;
                    //$.unblockUI();
            }).fail(function(){
                //$.unblockUI();
            }) ;	
        }) ;
		
		
	});
	
</script>


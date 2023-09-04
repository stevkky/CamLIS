$(document).ready(function (){  
	
	$("#tbl_result").DataTable({
		scrollX     : true,
		retrieve    : true,
		destroy     : true,
		filter      : false,
		columnDefs  : [{ targets : '_all', className: 'text-nowrap' }],
		columns		: [
            { "data": "lab_code" },
            { "data": "sample_type" },
            { "data": "test_name" },
			{ 
				"data": "result_organism",
				"render": function(data, type) {
					if(data !== null){
						return 'Completed';
					}else{
						return '';
					}
				}
			 },
			{ "data": null,"defaultContent": 'SARS-CoV2' },
			{ "data": "sample_id" },
			{ "data": "sample_number" },
			{ "data": "health_facility" },
			{ "data": "date_of_collection" },
			{ 
				"data": "completed_by" ,
				"render": function(data, type, row) {
					
					if(data == '' || data == null){
						return 'NA';
					}else{
						return data;
					}
				}
			},
			{ 
				"data": "phone_number" ,
				"render": function(data, type) {
					if(data == '' || data == null){
						return 'NA';
					}else{
						return data;
					}
				}
			},
			{ 
				"data": "reason_for_testing",
				"render": function(data, type) {
					console.log(data)
					if(data == 'Other' || data == '' || data == 'Screening'){
						return 'General Screening';
					}else{
						return data;
					}
				}
			 },
			{ 
				"data": "contact_with",
				"render": function(data, type) {
					console.log(data);
					if(data == '' || data == null){
						return 'NA';
					}else{
						return data;
					}
				}
			},
			{ "data": null, "defaultContent": 'NA' },
			{ 
				"data": "patient_name",
				"render": function(data, type) {
					res = data.replace(/[/,.]/g,'');
					return res;
				}
			},
			{ 
				"data": "patient_code",				
				"render": function(data, type) {
					res = data.replace(/[/,.]/g,'');
					return res;
				}
			},
			{ "data": "passport_number" },
			{ "data": "patient_gender" },
			{ 
				"data": "patient_age",
				"render": function(data, type) {
					console.log(data);
					if(data == ''){
						return 0;
					}else{
						return data;
					}					
				}
			},
            { "data": "nationality" },
            { 
				"data": "phone",
				"render": function(data, type) {
					console.log(data);
					if(typeof(data) == 'string'){
						data = data.trim();
					}
					if(data == '' || data == null || data == 'NULL'){
						return 'NA';
					}else{
						return data;
					}					
				}
			},
			{ "data": "occupation" },
			{ "data": null, "defaultContent": '' },
			{ 
				"data": "village_name",				
				"render": function(data, type, row) {
					console.log(row)
					res = '';
					res = row.village_name+ ','+row.commune_name+','+row.district_name+','+row.province_name;
					return res;
					
				}
			},
			{ "data": "province_name" },
			{ 
				"data": "district_name",
				"render": function(data, type) {
					if(data == ''){
						return 'NA';
					}else{
						return data;
					}
				}
			},
			{ 
				"data": "commune_name",
				"render": function(data, type) {
					if(data == ''){
						return 'NA';
					}else{
						return data;
					}					
				}
			},
			{ 
				"data": "village_name",
				"render": function(data, type) {
					if(data == ''){
						return 'NA';
					}else{
						return data;
					}					
				}
			},			
			{ 
				"data": "symptoms",
				"render": function(data, type) {
					data = JSON.parse(data);
					if(data.indexOf("Fever") >= 0){
						return 'Y';
					}else{
						return 'N';
					}
				}
			},
			{ 
				"data": "symptoms",
				"render": function(data, type) {				
					data = JSON.parse(data)					
					if(data.indexOf("Cough") >= 0){
						return 'Y';
					}else{
						return 'N';
					}
					return data;
				}
			},
			{ 
				"data": "symptoms",
				"render": function(data, type) {					
					data = JSON.parse(data)					
					if(data.indexOf("Runny Nose") >= 0){
						return 'Y';
					}else{
						return 'N';
					}
					return data;
				}
			},
			{ 
				"data": "symptoms",
				"render": function(data, type) {					
					data = JSON.parse(data)					
					if(data.indexOf("Sore Throat") >= 0){
						return 'Y';
					}else{
						return 'N';
					}
					return data;
				}
			},
			{ 
				"data": "symptoms",
				"render": function(data, type) {					
					data = JSON.parse(data);					
					if(data.indexOf("Difficulty Breathing") >= 0){
						return 'Y';
					}else{
						return 'N';
					}					
				}
			},
			{ 
				"data": "symptoms",
				"render": function(data, type) {					
					data = JSON.parse(data)					
					if(data.indexOf("No symptoms") >= 0){
						return 'Y';
					}else{
						return 'N';
					}					
				}
			},
            { 
				"data": "date_of_onset",
				"render": function(data, type) {
					if(data == '' || data == null){
						return 'NA';
					}else{
						return data;
					}					
				}
			},
            { "data": "is_positive_covid" },
			{ 
				"data": "test_date",
				"render": function(data, type) {
					if(data == ''){
						return 'NA';
					}else{
						return data;
					}					
				}
			},
			{ "data": "country_name" },
			{ 
				"data": "date_arrival",
				"render": function(data, type) {
					if(data == '' || data == null){
						return 'NA';
					}else{
						return data;
					}					
				}
			},
			{ 
				"data": "flight_number",
				"render": function(data, type) {
					if(data == '' || data == null){
						return 'NA';
					}else{
						return data;
					}					
				}
			},
			{ 
				"data": "seat_number",
				"render": function(data, type) {
					if(data == '' || data == null){
						return 'NA';
					}else{
						return data;
					}					
				}
			},
			{ "data": "place_of_collection" },
			{ "data": "date_of_collection" },
			{ 
				"data": "number_of_sample",
				"render": function(data, type) {
					if(data == '' || data == null){
						return 0;
					}else{
						return data;
					}					
				}
			},
			{ 
				"data": "sample_collector",
				"render": function(data, type) {
					if(data == '' || data == null){
						return 'NA';
					}else{
						return data;
					}					
				}
			},
			{ 
				"data": "phone_collector",
				"render": function(data, type) {
					if(data == '' || data == null){
						return 'NA';
					}else{
						return data;
					}
				}
			},
			{ "data": "received_date" },
			{ "data": "test_result_date" },
			{ "data": "result_organism" },
			{ "data": "vaccination_status" },

			{ 
				"data": "first_vaccine_name",
				"render": function(data, type) {
					if(data == '' || data == null){
						return 'Not Available';
					}else{
						return data;
					}
				}
			},
			{ 
				"data": "first_vaccinated_date",
				"render": function(data, type) {
					if(data == '' || data == null){
						return 'NA';
					}else{
						return data;
					}
				}
			},
			{ "data": "vaccinated_one" },
			{ 
				"data": "first_vaccine_name",
				"render": function(data, type) {
					if(data == '' || data == null){
						return 'Not Available';
					}else{
						return data;
					}
				}
			},
			{ 
				"data": "second_vaccinated_date",
				"render": function(data, type) {
					if(data == '' || data == null){
						return 'NA';
					}else{
						return data;
					}
				}
			},
			{ "data": "vaccinated_two" },
			{ 
				"data": "second_vaccine_name",
				"render": function(data, type) {
					if(data == '' || data == null){
						return 'Not Available';
					}else{
						return data;
					}
				}
			},
			{
				"data": "third_vaccinated_date",
				"render": function(data, type) {
					if(data == '' || data == null){
						return 'NA';
					}else{
						return data;
					}
				}
			},
			{ "data": "vaccinated_three" },
			{ 
				"data": "third_vaccine_name",
				"render": function(data, type) {
					if(data == '' || data == null){
						return 'Not Available';
					}else{
						return data;
					}
				}
			},
			{ 
				"data": "forth_vaccinated_date",
				"render": function(data, type) {
					if(data == '' || data == null){
						return 'NA';
					}else{
						return data;
					}
				}
			},
			{ "data": "vaccinated_four" },
			/** 
			 * 19072022 
			 * Remove this when 5th vaccine available
			 * */
			{ 
				"data": "third_vaccine_name",
				"render": function(data, type) {					
					return 'Not Available';					
				}
			},
			{ 
				"data": "forth_vaccinated_date",
				"render": function(data, type) {					
					return 'NA';					
				}
			},
			{ 
				"data": "vaccinated_four",
				"render": function(data, type) {					
					return 'No';					
				}
			},
			{ 
				"data": "third_vaccine_name",
				"render": function(data, type) {					
					return 'Not Available';					
				}
			},
			{ 
				"data": "forth_vaccinated_date",
				"render": function(data, type) {					
					return 'NA';					
				}
			},
			{ 
				"data": "vaccinated_four",
				"render": function(data, type) {					
					return 'No';
				}
			},
			// End


			
			{ "data": "test_name" },
			{ 
				"data": "reason_for_testing",
				"render": function(data, type) {
					console.log(data)
					if(data == 'Other' || data == '' || data == 'Screening'){
						return 'General Screening';
					}else{
						return data;
					}
				}
			},
			{ 
				"data": "reason_for_testing",
				"render": function(data, type) {
					console.log(data)
					if(data == 'Other' || data == '' || data == 'Screening'){
						return 'General Screening';
					}else{
						return data;
					}
				}
			},
			{ "data": "test_result_date" },
			{ "data": "requester" },
			{
				"data": "reason_for_testing",
				"render": function(data, type) {
					console.log(data)
					if(data == 'Other' || data == '' || data == 'Screening'){
						return 'General Screening';
					}else{
						return data;
					}
				}
			}		
        ],
		dom         : '<"hide"B>lfrtip',
		buttons     : [
			{
				extend  : 'excelHtml5',
				title   : 'Godata_report',
				action: function (e, dt, node, config) {
					this.processing(true);
					myDialog.showProgress('show');
					$.fn.DataTable.ext.buttons.excelHtml5.action.call( this, e, dt, node, config);
					myDialog.showProgress('hide');
				}
			}
		]
	});
	
	//$("select[name=labo_names]").select2();
	var dtPickerOption = {
		widgetPositioning : {
			horizontal	: 'left',
			vertical	: 'bottom'
		},
		showClear		: true,
		format			: 'DD/MM/YYYY',
		useCurrent		: false, 
		locale			: app_lang == 'kh' ? 'km' : 'en'
	};
	
	var multiSelectOption = {
		'buttonWidth': '100%',
		'buttonClass': 'form-control text-left custom-multiselect',
		'includeSelectAllOption': true,
		'enableFiltering': true,
		'filterPlaceholder': '',
		'selectAllText': label_all,
		'nonSelectedText': '',
		'allSelectedText': label_all,
		'numberDisplayed': 1,
		'selectAllNumber': false,
		'enableCaseInsensitiveFiltering': true,
		'templates': {
			ul: '<ul class="multiselect-container dropdown-menu custom-multiselect-container"></ul>',
			filter: '<li class="multiselect-item filter"><input class="form-control input-sm multiselect-search" type="text"></li>',
		}
	};
	$("#labo_names").multiselect($.extend(multiSelectOption, {'nSelectedText': label_laboratories}));
	$("#labo_names").multiselect({ 
		width: 'resolve' // need to override the changed default
	});

	$("#criteria_start").datetimepicker(dtPickerOption).on("dp.change", function(e) {
		var dob = $(this).data("DateTimePicker").date(); 		
		$("#start_date").val(e.target.value);
	}); 
	
	$("#criteria_end").datetimepicker(dtPickerOption).on("dp.change", function(e) {
		var dob = $(this).data("DateTimePicker").date(); 
		$("#end_date").val(e.target.value);
	});
	$("#start_time, #end_time").timepicker({minuteStep: 1, showMeridian: false});		
	var lab_ids = ($("select[name=labo_names]").val() == null) ? 0 : $("select[name=labo_names]").val();

	$("#print_preview_modal").on("hidden.bs.modal", function () {
		$("#print_preview_modal").find(".modal-dialog").empty();
	});
	
	
	// Generate Data for Godata 
	$('#btnGetData').on('click',function(e){
		e.preventDefault();
		var hasErr 				= 0;
		var sms 				= 0;
		var arrS 				= $("#criteria_start").val().split('/');
		var arrE 				= $("#criteria_end").val().split('/');
		var lab_ids 			= ($("select[name=labo_names]").val() == null) ? 0 : $("select[name=labo_names]").val();
		var c_date_start_status = false;
		var c_date_end_status	= false;		
		
		
		if($("#criteria_start").val() === '' || $("#criteria_start").val() === null){
			hasErr = 1;
			sms = msg_required_data;
			c_date_start_status = false;
		}else{
			c_date_start_status = true;
		}
		if($("#criteria_end").val() === '' || $("#criteria_end").val() === null){
			hasErr 		= 1;
			sms 		= msg_required_data; 
			c_date_end_status = false;
		}else {
			c_date_end_status = true;
		}

		if(new Date(arrS[1]+'/'+arrS[0]+'/'+arrS[2]) > new Date(arrE[1]+'/'+arrE[0]+'/'+arrE[2])){
			hasErr = 1;
			sms = date_required_startend; 
		}
		if(lab_ids == 0){
			hasErr = 1;
			sms = "No Laboratory selected...";
		}
		
		if (hasErr==1) {
			myDialog.showDialog('show', {
				text	: sms,
				status	: '',
				style	: 'warning'
			});
			return false;
		}
		
		myDialog.showProgress('show');		
		var start_time 				= moment($("#start_time").val(), "HH:mm");
		var end_time   				= moment($("#end_time").val(), "HH:mm");		

		var obj = {
			lab_ids : lab_ids,
			start : $("#criteria_start").val(),	end : $("#criteria_end").val(),
			start_time: start_time.isValid() ? start_time.format("HH:mm") : "00:00",
			end_time: end_time.isValid() ? end_time.format("HH:mm") : "23:59"
		}

	//	$("#print_preview_modal").data("patient_sample_id", data);  
	//	$("#print_preview_modal").css("background", 'rgba(0, 0, 0, 0.54)');

		$("#print_preview_modal #doPrinting").off("click").on("click", function (evt) {
			evt.preventDefault();
			//window.location = base_url + "report/preview_godata_result/print?" + $.param(obj);
			ExportToExcel('xlsx');
		});

		//Show Progress
		myDialog.showProgress('show', { text : 'loading...' });
		
		$.ajax({
			method: "POST",
			url		: base_url + "report/preview_godata_result/",
			data	: obj,
			dataType: 'JSON',
			
			success	: function (resText) {	
				//console.log(resText);
				/*
				for(var i in resText){
                   // var diagnosis = resText[i].health_facility;
					//console.log(diagnosis)
					
				}
				*/
				$('#tbl_result').DataTable().clear();
				$("#tbl_result").DataTable().rows.add(resText).draw();	
				setTimeout(function() {
					myDialog.showProgress('hide');
				}, 400);
				/*
				$("#print_preview_modal").find(".modal-dialog").html(resText);				
				setTimeout(function() {
					myDialog.showProgress('hide');
					$("#print_preview_modal").modal("show");
				}, 400);
				*/
			}
		});
	});
	//Export to Excel
    $("#btnExportExcel").on("click", function(evt) {
		evt.preventDefault();
		if ($.fn.DataTable.isDataTable("#tbl_result")) { 			
			$('#tbl_result').DataTable().button(0).trigger();
		}
	});
});
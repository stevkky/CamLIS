$(document).ready(function (){  
	/*
		var tbl_aggregated = $("#tbl-aggregated").DataTable({
			"filter"		: false,
			"info"			: false,
			"paging"		: false,
			"ordering"		: false,  
			"processing"	: true,
			"serverSide"	: true ,  
			dom: 'Bfrtip',
			buttons: [{
					extend		: 'print',
					text		: 'Print <i class="fa fa-print"></i>',
					titleAttr	: 'Print'  
				}
			], 
			 
			"ajax"       : {
				"url"    : base_url+'report/aggregate_list',
				"type"   : 'POST',
				 data	 : function(d) {
					d.start = $("#criteria_start").val();
					d.end = $("#criteria_end").val();
				}, 
				"destroy" : true 
			}, 
			"language"   : dataTableOption.language,
			"columns"    : [ 
				{ "data" : "name_kh" },
				{ "data" : "male" },
				{ "data" : "female" },
				{ "data" : "total" }
			],
			"order": [[0, 'asc']]  
		}); */
		
		/*
			summary report/ aggregated
		*/
		$("#btnSearchAggre").click(function(evt) {
			 
			evt.preventDefault();
			var hasErr = 0;
			var sms = 0;
			
			var arrS = $("#criteria_start").val().split('/');
			var arrE = $("#criteria_end").val().split('/');
			  
			
			if($("#criteria_start").val() === '' || $("#criteria_start").val() === null){
				hasErr = 1;
				sms = msg_required_data;  
				//$("label[for=ct_end]").attr("data-hint", msg_required_data); 
			}
			if($("#criteria_end").val() === '' || $("#criteria_start").val() === null){
				hasErr = 1;
				sms = msg_required_data; 
			}
			if(new Date(arrS[1]+'/'+arrS[0]+'/'+arrS[2]) > new Date(arrE[1]+'/'+arrE[0]+'/'+arrE[2])){
				hasErr = 1;
				sms = date_required_startend; 
			}
			
			//
			if (hasErr > 0) {
				myDialog.showDialog('show', {
					text	: sms,
					status	: '',
					style	: 'warning'
				});
	
				return false;
			}
			 myDialog.showProgress('show');

			var start_time = moment($("#start_time").val(), "HH:mm");
			var end_time   = moment($("#end_time").val(), "HH:mm");

			$.ajax({
					  method: "POST",
					  url	: base_url+'report/aggregate_list',
					  data	: { 
                          start : $("#criteria_start").val(),
                          end : $("#criteria_end").val(),
						  start_time: start_time.isValid() ? start_time.format("HH:mm") : "00:00",
						  end_time: end_time.isValid() ? end_time.format("HH:mm") : "23:59"
					},
					success: function(msg) {
						$('#btnPrint').removeAttr('disabled');
						$('#tbody_data_aggregated').html(msg); 
						myDialog.showProgress('hide');
					},
					error: function(e) {
						console.log(e.statusText)
						myDialog.showProgress('hide');
						myDialog.showDialog('show', { text : e.statusText, status : '', style : 'warning' });
					}
				});
		});
		
		
		
		/*
			award function
		*/
		$("#btnSearchAward").click(function(evt) {
			 
			evt.preventDefault();
			var hasErr = 0;
			var sms = 0;
			var arrS = $("#criteria_start").val().split('/');
			var arrE = $("#criteria_end").val().split('/');
			 
			 
			if($("#criteria_start").val() === '' || $("#criteria_start").val() === null){
				hasErr = 1;
				sms = msg_required_data;   
			}
			if($("#criteria_end").val() === '' || $("#criteria_end").val() === null){
				hasErr = 1;
				sms = msg_required_data; 
			}
			if(new Date(arrS[1]+'/'+arrS[0]+'/'+arrS[2]) > new Date(arrE[1]+'/'+arrE[0]+'/'+arrE[2])){
				hasErr = 1;
				sms = date_required_startend; 
			}
			
			//
			if (hasErr > 0) {
				myDialog.showDialog('show', {
					text	: sms,
					status	: '',
					style	: 'warning'
				});
	
				return false;
			}
			 //$('#prog').progressbar({ value: 0 });
			 myDialog.showProgress('show');

            var start_time = moment($("#start_time").val(), "HH:mm");
            var end_time   = moment($("#end_time").val(), "HH:mm");
			// 
			$.ajax({
					  method: "POST",
					  url	: base_url+'report/ward_list',
					  data	: { 
						start : $("#criteria_start").val(),
						end : $("#criteria_end").val(),
                          start_time: start_time.isValid() ? start_time.format("HH:mm") : "00:00",
                          end_time: end_time.isValid() ? end_time.format("HH:mm") : "23:59"
					},
					success: function(msg) {
						$('#btnPrint').removeAttr('disabled');
						$('#tbody_data_ward').html(msg); 
						myDialog.showProgress('hide');
					} 
				});
		});
		
		/*
			Sample Rejection
		*/
		$("#btnPreviewRejection").click(function(evt) {
			 
			evt.preventDefault();
			var hasErr = 0;
			var sms = 0;
			var arrS = $("#criteria_start").val().split('/');
			var arrE = $("#criteria_end").val().split('/');
			 
			 
			if($("#criteria_start").val() === '' || $("#criteria_start").val() === null){
				hasErr = 1;
				sms = msg_required_data;   
			}
			if($("#criteria_end").val() === '' || $("#criteria_end").val() === null){
				hasErr = 1;
				sms = msg_required_data; 
			}
			if(new Date(arrS[1]+'/'+arrS[0]+'/'+arrS[2]) > new Date(arrE[1]+'/'+arrE[0]+'/'+arrE[2])){
				hasErr = 1;
				sms = date_required_startend; 
			}
			
			//
			if (hasErr > 0) {
				myDialog.showDialog('show', {
					text	: sms,
					status	: '',
					style	: 'warning'
				});
	
				return false;
			}
			 //$('#prog').progressbar({ value: 0 });
			 myDialog.showProgress('show');

            var start_time = moment($("#start_time").val(), "HH:mm");
            var end_time   = moment($("#end_time").val(), "HH:mm");
			// 
			$.ajax({
					  method: "POST",
					  url	: base_url+'report/rejection_sample',
					  data	: { 
						start : $("#criteria_start").val(),
						end : $("#criteria_end").val(),
                          start_time: start_time.isValid() ? start_time.format("HH:mm") : "00:00",
                          end_time: end_time.isValid() ? end_time.format("HH:mm") : "23:59"
					},
					success: function(msg) {
						$('#btnPrint').removeAttr('disabled');
						$('#tbody_data_rejection').html(msg); 
						myDialog.showProgress('hide');
					} 
				});
		});
		
		/**
		* AMR
		*/
		$("#btnPreviewAMR").click(function(evt) {
			evt.preventDefault();
			var hasErr = 0;
			var sms = 0;
			var arrS = $("#criteria_start").val().split('/');
			var arrE = $("#criteria_end").val().split('/');
			if($("#criteria_start").val() === '' || $("#criteria_start").val() === null){
				hasErr = 1;
				sms = msg_required_data;   
			}
			if($("#criteria_end").val() === '' || $("#criteria_end").val() === null){
				hasErr = 1;
				sms = msg_required_data; 
			}
			if(new Date(arrS[1]+'/'+arrS[0]+'/'+arrS[2]) > new Date(arrE[1]+'/'+arrE[0]+'/'+arrE[2])){
				hasErr = 1;
				sms = date_required_startend; 
			}
			if (hasErr > 0) {
				myDialog.showDialog('show', {
					text	: sms,
					status	: '',
					style	: 'warning'
				});
				return false;
			}
			myDialog.showProgress('show');
			var start_time = moment($("#start_time").val(), "HH:mm");
			var end_time   = moment($("#end_time").val(), "HH:mm");
			$.ajax({
				method: "POST",
				url	: base_url+'report/amr_report',
				data: {
					start : $("#criteria_start").val(),
					end : $("#criteria_end").val(),
					start_time: start_time.isValid() ? start_time.format("HH:mm") : "00:00",
					end_time: end_time.isValid() ? end_time.format("HH:mm") : "23:59"
				},
				success: function(msg) {
					$('#btnPrintAMR').removeAttr('disabled');
					$('#tbody_data_amr').html(msg);
					myDialog.showProgress('hide');
				}
			});
		});


		// individual report
		$('#btnSearchIndvt').on('click',function(e){
			e.preventDefault();
			
			//
			var pid = $("#patient_sample_id").val(); 
			
			
			//console.log(pid);
			
			if(pid==''){
				myDialog.showDialog('show', {
					text	: 'Patient ID not allow empty.',
					status	: '',
					style	: 'warning'
				});
				return false;
			}
			//
			$("#print_preview_modal").data("patient_sample_id", pid);
			$("#print_preview_modal").css("background", 'rgba(0, 0, 0, 0.54)');
	
			$("#print_preview_modal #doPrinting").off("click").on("click", function (evt) {
				e.preventDefault();
				printpage(base_url + "result/preview_psample_result/"+pid+"/print");
			});
	
			//Show Progress
			myDialog.showProgress('show', { text : 'loading...' });
			
			$.ajax({
				url		: base_url + "result/preview_psample_result/"+pid,
				type	: 'POST',
				success	: function (resText) {
					$("#print_preview_modal").find(".modal-dialog").html(resText);
					
					setTimeout(function() {
						myDialog.showProgress('hide');
						$("#print_preview_modal").modal("show");
					}, 400);
				}
			});
		});
	
		$("#print_preview_modal").on("hidden.bs.modal", function () {
			$("#print_preview_modal").find(".modal-dialog").empty();
		});
		
		/**
		 * 27-07-2022
		 * Store result as global variable
		 */
		
		// bacteriology report
		$('#btnSearchBacteriology').on('click',function(e){
			e.preventDefault(); 
			var obj = {
						_labo_type 	: $('#sllabo_type').val(),
						_department : $('#sldepartment').val(),
						_start 		: $('#criteria_start').val(),
						_end 		: $('#criteria_end').val(),
						_sample_type: $('#slsample_type').val(),
						_testing 	: $('#sltesting').val(),
						_result 	: $('#slresult').val(),
						_labo_number: $('#txtlabo_number').val()
					};

				var data = new Array();
				data.push(obj);

			$("#print_preview_modal").data("patient_sample_id", data);  
			$("#print_preview_modal").css("background", 'rgba(0, 0, 0, 0.54)');
	
			$("#print_preview_modal #doPrinting").off("click").on("click", function (evt) {
                evt.preventDefault();
				//window.location = base_url + "report/preview_pbacteriology_result/print?" + $.param(obj);				
				ExportToExcel('xlsx');
			});
			
			//Show Progress
			myDialog.showProgress('show', { text : 'loading...' });
			
			$.ajax({
				method: "POST",
				url		: base_url + "report/preview_pbacteriology_result/",
				data	: obj,
				type	: 'JSON',
				success	: function (resText) {
					$("#print_preview_modal").find(".modal-dialog").html(resText);					
					setTimeout(function() {
						myDialog.showProgress('hide');
						$("#print_preview_modal").modal("show");
					}, 400);
				}
			});
		});
		
		/**
		* culture report
		*/
		$(document).on('click', '#btnSearchCulture', function(event) {
			event.preventDefault();

			$('#tbodyDataCulture').find('#year').html('');
			$('#tbodyDataCulture').find('#month').html('');
			$('#tbodyDataCulture').find('#total').html('');

			var hasErr = 0;
			var sms = 0;

			if ($("#start_date").val() === '' || $("#start_date").val() === null) {
				hasErr = 1;
				sms = msg_required_data;
			}

			if ($("#end_date").val() === '' || $("#end_date").val() === null) {
				hasErr = 1;
				sms = msg_required_data;
			}

			if (hasErr > 0) {
				myDialog.showDialog('show', {
					text	: sms,
					status	: '',
					style	: 'warning'
				});
				return false;
			}

			myDialog.showProgress('show');

			$.ajax({
				url: base_url+'report/culture_list',
				type: 'POST',
				dataType: 'json',
				data: {
					start_date  : $('#start_date').val(),
					end_date 	: $('#end_date').val()
				},
				success: function(response){
					if (response.culture.length != 0) {
						$('#btnPrint').removeAttr('disabled');
						$('#tbodyDataCulture').show();
						var dataYear  = '<td rowspan="2" style="vertical-align : middle;text-align:center;">Cumulative Blood Stream Infection data</td>';
						var dataMonth = '';
						var dataTotal = '<td rowspan="2"></td>';
						var rowspan   = true;
						for (var i = response.culture.length - 1; i >= 0; i--) {
							$.each(response.culture[i], function(year, months) {
								dataYear+= '<td colspan="'+ months.length +'" align="center">'+ year +'</td>';
								$.each(months, function(index, month) {
									$.each(month, function(name, total) {
										(rowspan) ? dataMonth+= '<td rowspan="2" align="center">'+ name +'</td>' : dataMonth+= '<td align="center">'+ name +'</td>';
										dataTotal+= '<td align="center">'+ total +'</td>';
										rowspan = false;
									});
								});
								rowspan = true;
							});
						}
						$('#tbodyDataCulture').find('#year').append(dataYear);
						$('#tbodyDataCulture').find('#month').append(dataMonth);
						$('#tbodyDataCulture').find('#total').append(dataTotal);
					}
					myDialog.showProgress('hide');
				}
			});
			
		});

		/*
			Search Covid
		*/
		
		$("#btnSearchCovid").click(function(evt) {
			evt.preventDefault();
			var hasErr 				= 0;
			var sms 				= 0;
			var arrS 				= $("#criteria_start").val().split('/');
			var arrE 				= $("#criteria_end").val().split('/');
			var c_date_start_status = false;
			var c_date_end_status	= false;
			var t_date_start_status = false;
			var t_date_end_status   = false;
			var check				= false;
 			//added 28 DEC 2020
			var lang = $("#lang").val();
			 
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
			

			// Added 31-03-2021
			// Test Datepicker
		
			var testErr 		= 0;
			var msg 			= 0;
			var arrTestStart 	= $("#test_start_date").val().split('/');
			var arrTestEnd 		= $("#test_end_date").val().split('/');			

			if($("#test_start_date").val() === '' || $("#test_start_date").val() === null){				
				msg 	= msg_required_data;
				t_date_start_status = false;
			}else{
				t_date_start_status = true;
			}

			if($("#test_end_date").val() === '' || $("#test_end_date").val() === null){				
				msg 	= msg_required_data; 
				t_date_end_status = false;
			}else{
				t_date_end_status = true;
			}

			if(new Date(arrTestStart[1]+'/'+arrTestStart[0]+'/'+arrTestStart[2]) > new Date(arrTestEnd[1]+'/'+arrTestEnd[0]+'/'+arrTestEnd[2])){
				testErr = 1;
				msg = date_required_startend; 
			}
			var cdate_status = false;
			if(!c_date_end_status || !c_date_start_status){
				cdate_status = false;
			}else{
				cdate_status = true;
			}
			
			var tdate_status = false;
			if(!t_date_end_status || !t_date_start_status){
				tdate_status = false;
			}else{
				tdate_status = true;
			}
			if(!cdate_status && !tdate_status){
				sms = date_required_startend; 
				check = false;
			}else{
				check = true;
			}
			if (!check) {
				myDialog.showDialog('show', {
					text	: sms,
					status	: '',
					style	: 'warning'
				});
				return false;
			}
			 //$('#prog').progressbar({ value: 0 });
			myDialog.showProgress('show');
			var start_sample_number 	= $("#start_sample_number").val();
			var end_sample_number 		= $("#end_sample_number").val();
            var start_time = moment($("#start_time").val(), "HH:mm");
            var end_time   = moment($("#end_time").val(), "HH:mm");
			// 
			var for_research = $("select[name=for_research]").val();
			var sample_source = ($("select[name=sample_source]").val() == null) ? 0 : $("select[name=sample_source]").val();
			var test_name 	= ($("select[name=test_name]").val() == null) ? 0 : $("select[name=test_name]").val();
			
			//console.log(sample_source);
			//console.log(test_name);
			//var lab_id = $("select[name=labo_name]").val();
			
			//var lab_ids = ($("select[name=labo_names]").val() == null) ? 0 : $("select[name=labo_names]").val();
			//console.log(lab_ids);


			var lab_id = $("#laboratory").val();
			if(lab_id == null){
				myDialog.showDialog('show', {
					text	: "Please select laboratory",
					status	: '',
					style	: 'warning'
				});
				return false;
			}
			
			var test_result =  ($("select[name=test_result]").val() == null) ? 0 : $("select[name=test_result]").val();
			var number_of_sample =  ($("select[name=number_of_sample]").val() == null) ? 0 : $("select[name=number_of_sample]").val();

			$.ajax({
					method: "POST",
					url	: base_url+'report/generate_covid_report',
					dataType: 'json',
					data	: { 
						start : $("#criteria_start").val(),	end : $("#criteria_end").val(),
						start_time: start_time.isValid() ? start_time.format("HH:mm") : "00:00",
						end_time: end_time.isValid() ? end_time.format("HH:mm") : "23:59",
						lang: lang,
						for_research: for_research,
						test_start : $("#test_start_date").val(),	
						test_end : $("#test_end_date").val(),
						start_sample_number : start_sample_number,
						end_sample_number : end_sample_number,
						sample_source:sample_source,
						test_name: test_name,
						lab_id: lab_id,
						test_result: test_result,
						number_of_sample: number_of_sample
				},
				success: function(resText) {
					//console.log(resText.data);
					//console.log(resText.htmlstring);

					data = resText.data;
					//console.log(data.length);
					if(data.length > 0){
						$('#btnPrint').removeAttr('disabled');
						$('#btnExportExcel').removeAttr('disabled');
					}else{
						$('#btnPrint').attr('disabled',true);
						$('#btnExportExcel').attr('disabled',true);
					}
					$('#tbl-result tbody').html(resText.htmlstring); 
					myDialog.showProgress('hide');
				}
			});
		});
		$("#btnExportExcel").on("click", function(evt) {
			evt.preventDefault();         
			myDialog.showProgress('show');
			var start_date 			= $("#start_date").val();
			var end_date 			= $("#end_date").val();
			
			var start_time 			= $("#start_time").val();
            var end_time   			= $("#end_time").val();
			var start_time 			= moment($("#start_time").val(), "HH:mm");
            var end_time   			= moment($("#end_time").val(), "HH:mm");
			var start_time 			= start_time.format("HH:mm");
			var end_time   			= end_time.format("HH:mm");
			
			var for_research = $("select[name=for_research]").val();

			var test_start_date 	= $("#test_start_date").val();
			var test_end_date 		= $("#test_end_date").val();
			var end_sample_number 	= $("#end_sample_number").val();
			var start_sample_number = $("#start_sample_number").val();
			//console.log(start_date+" "+end_date+" ");
			var sample_source = ($("select[name=sample_source]").val() == null) ? 0 : $("select[name=sample_source]").val();
			var test_name 	= ($("select[name=test_name]").val() == null) ? 0 : $("select[name=test_name]").val();
			var sample_str = "";
			if(sample_source !== 0){				
				for(var i in sample_source){
					//console.log(sample_source[i]);
					sample_str += sample_source[i]+",";
				}
				sample_str = sample_str.substr(0 , sample_str.length - 1);
				//console.log(sample_str);
			}
			var test_name_str = '';
			if(test_name !== 0){
				for(var i in test_name){
					//console.log(sample_source[i]);
					test_name_str += test_name[i]+",";
				}
				test_name_str = test_name_str.substr(0 , test_name_str.length - 1);
			}
			//var lab_id = $("select[name=labo_name]").val();
			var lab_id = $("#laboratory").val(); //04102021
			
			var test_result =  ($("select[name=test_result]").val() == null) ? 0 : $("select[name=test_result]").val();
			var number_of_sample =  ($("select[name=number_of_sample]").val() == null) ? 0 : $("select[name=number_of_sample]").val();

			//var url = base_url+'report/export_covid_report/'+start_date+'/'+end_date+'/'+for_research;
			var url = base_url+'report/export_covid_report?start='+start_date+'&end='+end_date+'&for_research='+for_research+'&start_time='+start_time+'&end_time='+end_time+'&test_start='+test_start_date+'&test_end='+test_end_date+"&start_sample_number="+start_sample_number+"&end_sample_number="+end_sample_number+"&sample_source="+sample_str+"&lab_id="+lab_id+"&test_name="+test_name_str+"&test_result="+test_result+"&number_of_sample="+number_of_sample;
			console.log(url);
			location.href = encodeURI(url);
			myDialog.showProgress('hide');
		});
		$("#print_preview_modal").on("hidden.bs.modal", function () {
			$("#print_preview_modal").find(".modal-dialog").empty();
		});
		
		$("select[name=labo_name]").on("change",function(evt){
			
			var lab_id = $(this).val();
			// get sample source by labo;
			$.ajax({
				method: "POST",
				url	: base_url+'report/get_sample_source',
				dataType: 'json',
				data	: { 
					lab_id : lab_id
				},
				success: function(resText) {
					// remove 
					var $sample_source = $("select[name=sample_source]");
					$sample_source.find('option').remove();
					console.log(resText);
					for(var i in resText){
						$sample_source.append('<option value=' + resText[i].source_id + '>' + resText[i].source_name + '</option>');
					}
				} 
			});
		})

		// For Dev
		// 05-05-2021
		/*
		$("#btnExportExcelDev").on("click", function(evt) {
			evt.preventDefault();         
			myDialog.showProgress('show');
			var start_date 			= $("#start_date").val();
			var end_date 			= $("#end_date").val();
			
			var start_time 			= $("#start_time").val();
            var end_time   			= $("#end_time").val();
			var start_time 			= moment($("#start_time").val(), "HH:mm");
            var end_time   			= moment($("#end_time").val(), "HH:mm");
			var start_time 			= start_time.format("HH:mm");
			var end_time   			= end_time.format("HH:mm");
			
			var for_research = $("select[name=for_research]").val();

			var test_start_date 	= $("#test_start_date").val();
			var test_end_date 		= $("#test_end_date").val();
			var end_sample_number 	= $("#end_sample_number").val();
			var start_sample_number = $("#start_sample_number").val();
			
			var sample_source = ($("select[name=sample_source]").val() == null) ? 0 : $("select[name=sample_source]").val();
			var test_name 	= ($("select[name=test_name]").val() == null) ? 0 : $("select[name=test_name]").val();
			var sample_str = "";
			if(sample_source !== 0){				
				for(var i in sample_source){
					
					sample_str += sample_source[i]+",";
				}
				sample_str = sample_str.substr(0 , sample_str.length - 1);
				
			}
			var test_name_str = '';
			if(test_name !== 0){
				for(var i in test_name){
					
					test_name_str += test_name[i]+",";
				}
				test_name_str = test_name_str.substr(0 , test_name_str.length - 1);
			}
			var lab_id = $("select[name=labo_name]").val();			
			var url = base_url+'report/export_covid_report_dev?start='+start_date+'&end='+end_date+'&for_research='+for_research+'&start_time='+start_time+'&end_time='+end_time+'&test_start='+test_start_date+'&test_end='+test_end_date+"&start_sample_number="+start_sample_number+"&end_sample_number="+end_sample_number+"&sample_source="+sample_str+"&lab_id="+lab_id+"&test_name="+test_name_str;
			location.href = encodeURI(url);
			myDialog.showProgress('hide');
		});
		*/
		function ExportToExcel(type, fn, dl) {
			/* generate worksheet and workbook */
			const worksheet = XLSX.utils.json_to_sheet(bacter_result);
			const workbook = XLSX.utils.book_new();
			XLSX.utils.book_append_sheet(workbook, worksheet, "Bacterio_Report");			
			/* fix headers */
			//XLSX.utils.sheet_add_aoa(worksheet, [header], { origin: "A1" });
		
			/* calculate column width */
			//const max_width = rows.reduce((w, r) => Math.max(w, r.name.length), 10);
			//worksheet["!cols"] = [ { wch: max_width } ];
		
			/* create an XLSX file and try to save to Presidents.xlsx */
			XLSX.writeFile(workbook, "Bacteriology_report.xlsx");
		}
});
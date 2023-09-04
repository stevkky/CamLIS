$(function () {
	//DateTimePicker options
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

	$("input").iCheck({ checkboxClass: 'icheckbox_minimal', radioClass: 'iradio_minimal' });
	$("input.dtpicker").datetimepicker(dtPickerOption);

	$("select:not([multiple])").select2();

	$("#patient-province").multiselect($.extend(multiSelectOption, {
		'nSelectedText': label_provinces,
		'onChange': function (option, checked, select) {
			generateDistrictOption();
			$("#patient-district").multiselect('rebuild');
		},
		'onSelectAll': function () {
			generateDistrictOption()
			$("#patient-district").multiselect('rebuild');
		},
		'onDeselectAll': function () {
			generateDistrictOption();
			$("#patient-district").multiselect('rebuild');
		},
		'onInitialized': function(select, container) {
			generateDistrictOption();
		}
	}));
	$("#patient-district").multiselect($.extend(multiSelectOption, {'nSelectedText': label_districts}));
	$("#department").multiselect($.extend(multiSelectOption, {'nSelectedText': label_departments}));
	$("#sample-name").multiselect($.extend(multiSelectOption, {'nSelectedText': label_sample_types}));
	$("#sample-description").multiselect($.extend(multiSelectOption, {'nSelectedText': label_sample_descriptions}));
	$("#sample-source").multiselect($.extend(multiSelectOption, {'nSelectedText': label_sample_sources}));
	$("#requester").multiselect($.extend(multiSelectOption, {'nSelectedText': label_requesters}));
	$("#test-name").multiselect($.extend(multiSelectOption, {'nSelectedText': label_tests}));
	$("#organism").multiselect($.extend(multiSelectOption, {'nSelectedText': label_result_organisms}));
	$("#antibiotic").multiselect($.extend(multiSelectOption, {'nSelectedText': label_antibiotics}));
	$("#laboratory").multiselect($.extend(multiSelectOption, {'nSelectedText': label_laboratories}));
	$("#laboratory").multiselect({
		width: 'resolve' // need to override the changed default
	});
	//Load District
	var districts = _.groupBy(DISTRICTS, 'province_code');
	function generateDistrictOption() {
		var $target = $("#patient-district");
		var code    = $('#patient-province').val() || [];

		$target.find("option").remove();
		if (code.length === 0) return false;

		for(var i in code) {
			var district = districts[code[i]] || [];
			if (district.length > 0) {
				for(var j in district) {
					var name = 'name_' + app_lang;
					$target.append("<option value='" + district[j].code + "'>" + district[j][name] + "</option>");
				}
			}
		}
	}

	//Check All Field
	$(":checkbox#check-all").on("ifChanged", function(evt) {
		var state = $(this).is(':checked') ? 'check' : 'uncheck';
		$("#frm-condition").find(":checkbox.show-field").iCheck(state);
	});

	$(":checkbox[data-name='district']").on("ifChanged", function (evt) {
		var state = $(this).is(':checked') ? 'check' : 'uncheck';
		$(":checkbox[data-name='commune']").iCheck(state);
		$(":checkbox[data-name='village']").iCheck(state);
	});

	$(":checkbox[data-name='patient_age']").on("ifChanged", function (evt) {
		var state = $(this).is(':checked') ? 'check' : 'uncheck';
		$(":checkbox[data-name='age_month']").iCheck(state);
		$(":checkbox[data-name='age_day']").iCheck(state);
	});
	$(":checkbox[data-name='collected_date']").on("ifChanged", function (evt) {
		var state = $(this).is(':checked') ? 'check' : 'uncheck';
		$(":checkbox[data-name='diagnosis']").iCheck(state);
	});
	$(":checkbox[data-name='sample_description']").on("ifChanged", function (evt) {
		var state = $(this).is(':checked') ? 'check' : 'uncheck';
		$(":checkbox[data-name='volume1']").iCheck(state);
		$(":checkbox[data-name='volume2']").iCheck(state);
	});
	$(":checkbox[data-name='result_organism']").on("ifChanged", function (evt) {
		var state = $(this).is(':checked') ? 'check' : 'uncheck';
		$(":checkbox[data-name='max_val']").iCheck(state);
		$(":checkbox[data-name='min_val']").iCheck(state);
		$(":checkbox[data-name='unit']").iCheck(state);
	});
	$(":checkbox[data-name='antibiotic']").on("ifChanged", function (evt) {
		var state = $(this).is(':checked') ? 'check' : 'uncheck';
		$(":checkbox[data-name='MIC']").iCheck(state);
		$(":checkbox[data-name='DD']").iCheck(state);
	});

	/* ADDED 10 Dec 2020
	 *
	 */
	$("#sample-status").on("change",function(evt){
		evt.preventDefault();
		if($(this).val() == "research"){
			$("#for_research_value_wrapper").removeClass("hidden");
		}else{
			$("#for_research_value_wrapper").addClass("hidden");
		}
	})
	/** END */
	/**
	 * 10-07-2021
	 */
	$(":checkbox[data-name='patient_name']").on("ifChanged", function (evt) {
		var state = $(this).is(':checked') ? 'check' : 'uncheck';
		$(":checkbox[data-name='phone']").iCheck(state);
	});
	//14022022
	$(":checkbox[data-name='sample_status']").on("ifChanged", function (evt) {
		var state = $(this).is(':checked') ? 'check' : 'uncheck';
		$(":checkbox[data-name='reject_comment']").iCheck(state);
	});
	//End
	//Generate Data
	$("#btnGenerate").on("click", function(evt) {
		evt.preventDefault();
		var isValid = false;
		$("#frm-condition").find("input[type=text]").each(function () {
			if ($(this).val().length > 0) {
				isValid = true;
				return false;
			}
		});

		if (!isValid) {
			$("#frm-condition").find("select").each(function () {
				var val = $(this).val() || [];
				if (val.length > 0) {
					isValid = true;
					return false;
				}
			});
		}
		//   console.log("isValid "+ isValid);
		if (!isValid) {
			myDialog.showDialog('show', { text : msg_required_condition, status : '', style : 'warning' });
			return false;
		}

		var data    = $("#frm-condition").serializeArray();
		var sample_status = $("#sample-status").val();
		switch (sample_status) {
			case "rejected":
				data.push({name: 'is_rejected[value]', value: '1'});
				break;
			case "urgent":
				data.push({name: 'is_urgent[value]', value: '1'});
				break;
			default:
				//data.push({name: 'for_research[value]', value: '1'});
				//var v = $("select[name=for_research_value]").val();
				data.push({name: 'for_research[value]', value: sample_status});
				break;
		}

		let validateDatePeriod = false;
		data.map(item =>{
			if(item.name == 'collected_date[min]' && item.value !='') validateDatePeriod = true
			if(item.name == 'collected_date[max]' && item.value !='') validateDatePeriod = true
			if(item.name == 'received_date[min]' && item.value !='') validateDatePeriod = true
			if(item.name == 'received_date[max]' && item.value !='') validateDatePeriod = true
			if(item.name == 'test_date[min]' && item.value !='') validateDatePeriod = true
			if(item.name == 'test_date[max]' && item.value !='') validateDatePeriod = true
		})

		if(!validateDatePeriod){
			myDialog.showDialog('show', { text : msg_required_primary_condition , status : '', style : 'warning' });
			return false;
		}

		var columns = [];
		$("#tbl-condition").find("tr.query-field").each(function() {
			var $checkbox = $(this).find(":checkbox.show-field");
			if ($checkbox.is(':checked')) {
				columns.push({ title : $(this).find(".title").text(), data : $checkbox.data("name") });
			}
		});

		if ($.fn.DataTable.isDataTable("#tbl-result")) {
			$('#tbl-result').DataTable().clear().destroy();
			$('#tbl-result').empty();
			$("#btnExportExcel").prop("disabled", true);
		}
		if (columns.length > 0) {
			$("#btnExportExcel").prop("disabled", false);
			$("#tbl-result").DataTable({
				scrollX     : true,
				retrieve    : true,
				destroy     : true,
				filter      : false,
				columnDefs  : [{ targets : '_all', className: 'text-nowrap' }],
				columns     : columns,
				dom         : '<"hide"B>lfrtip',
				buttons     : [
					{
						extend  : 'excelHtml5',
						title   : 'Raw Data',
						action: function (e, dt, node, config) {
							this.processing(true);
							myDialog.showProgress('show');
							$.fn.DataTable.ext.buttons.excelHtml5.action.call( this, e, dt, node, config);
							myDialog.showProgress('hide');
						}
					}
				]
			});
		}

		myDialog.showProgress('show');

		$.ajax({
			url      : base_url + 'report/get_raw_data',
			type     : 'POST',
			data     : data,
			dataType : 'json',
			success  : function(resText) {
				//console.log(resText)
				/*
				for(var i in resText){

					var diagnosis = resText[i].diagnosis;
					diagnosis = diagnosis.replace("{", "");
					diagnosis = diagnosis.replace("}", "");
					res = diagnosis.split(",");
					res = res[0];
					res = res.replace(/['"]+/g, '');

					if(res == "NULL") res = "";
					resText[i].diagnosis = res;

					var age = +"y"+" "+resText[i].age_month+"m"+" "+resText[i].age_day+"d";

					var nationality = resText[i].nationality;
					nationality = nationality.replace("{", "");
					nationality = nationality.replace("}", "");
					res1 = nationality.split(",");
					res1 = res1[0];
					res1 = res1.replace(/['"]+/g, '');
					if(res1 == "NULL") res1 = "";
					resText[i].nationality = res1;

					var full_age = '';
					if(resText[i].patient_age > 0){
						full_age = resText[i].patient_age;
					}else if(resText[i].age_month > 0){
						full_age = resText[i].age_month+"ážáŸ‚";
					}else{
						full_age = resText[i].age_day+"ážáŸ’áž„áŸƒ";
					}
					resText[i].patient_age = full_age;
				}
				*/
				$("#tbl-result").DataTable().rows.add(resText).draw();
				myDialog.showProgress('hide');
			},
			error    : function() {
				myDialog.showProgress('hide');
				myDialog.showDialog('show', { text : msg_generating_fail, status : '', style : 'warning' });
			}
		})
	});

	//Export to Excel
	$("#btnExportExcel").on("click", function(evt) {
		evt.preventDefault();
		if ($.fn.DataTable.isDataTable("#tbl-result")) {
			$('#tbl-result').DataTable().button(0).trigger();
		}
	});

	//Reset
	$("#btnReset").on("click", function(evt) {
		evt.preventDefault();
		$("#frm-condition").find("input[type=text]").val("");
		$("#frm-condition").find("select").val("").trigger("change");
		$("#frm-condition").find(":checkbox.show-field").iCheck('check');
		if ($.fn.DataTable.isDataTable("#tbl-result")) {
			$('#tbl-result').DataTable().clear().destroy();
			$('#tbl-result').empty();
			$("#btnExportExcel").prop("disabled", true);
		}
	}).trigger("click");
});

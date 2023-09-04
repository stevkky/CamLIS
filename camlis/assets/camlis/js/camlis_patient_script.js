$(function() {
    var $patient_view = $(".patient-info-view");
    var $patient_form = $("#patient-info-form");
	var $tbl_patient  = $("#tb_Patient");

	if ($tbl_patient.length > 0) {
		var tb_Patient = $tbl_patient.DataTable({
			"filter"		: true,
			"info"			: false,
            "bPaginate"		: true,
			"processing"	: true,
			"serverSide"	: true,
			"ajax"			: {
				"url"	: base_url+'patient/view',
				"type"	: 'POST',
				"data"	: function(d) {                    
                    if (d.search !== undefined) {
                        d.search.value = $("#search-patient-info").val().trim();
                    }
				}
			},
			"columns"    : [
				{ "data" : "number" },
                { "data" : "pid", 'visible' : false },
               
                { "data" : "patient_code" },
				{ "data" : "patient_name" },
               
				{ "data" : "gender" },
                
                { "data" : "phone" },
                { "data" : "passport_number" },
                
				{ "data" : "has_sample" },
				{ "data" : "action" }
			],
			"columnDefs": [
                { "targets": -1, "orderable": false, "searchable": false, "className" : "text-left text-middle no-wrap" },
                { "targets": -2, "orderable": false, "searchable": false, "className" : "text-center text-middle no-wrap" },
                { "targets": -2, "name" : "patient_name" },
                { "targets": 3, "name" : "sex" },
                { "targets": "_all", "className" : "text-middle" }
			],
			"language"		: dataTableOption.language,
            "order": [[0, 'desc']],
            "lengthMenu": [[10, 20, 40, 60, 80, 100], [10, 20, 40, 60, 80, 100]],
            "pageLength": 20
		});

		$("#btnSearch").click(function(evt) {
			evt.preventDefault();
			tb_Patient.ajax.reload();
		});

        $("#search-patient-info").on("keyup", function (evt) {
            if (evt.which === 13) tb_Patient.ajax.reload();
        });

        /**
         * Remove Patient
         */
        $tbl_patient.on("click", "a.remove", function (evt) {
            evt.preventDefault();
            var data = tb_Patient.row($(this).closest("tr")).data();

            if (confirm(confirm_delete_patient) && data.pid > 0) {
                myDialog.showProgress('show');
                $.ajax({
                    url      : base_url + "patient/delete",
                    type     : 'POST',
                    data     : { patient_id : data.pid },
                    dataType : 'json',
                    success  : function (resText) {
                        myDialog.showProgress("hide");
                        myDialog.showDialog("show", { text : resText.msg, style : resText.status ? 'success' : 'warning' });
                        if (resText.status) {
                            tb_Patient.ajax.reload();
                        }
                    },
                    error    : function () {
                        myDialog.showProgress("hide");
                        myDialog.showDialog("show", { text : msg_delete_fail, style : 'warning' });
                    }
                })
            }
        });
	}
	else {
        //DateTimePicker options
        var dtPickerOption = {
            widgetPositioning: {
                horizontal: 'left',
                vertical: 'bottom'
            },
            showClear: true,
            format: 'DD/MM/YYYY',
            useCurrent: false,
            maxDate: new Date(),
            locale: app_lang == 'kh' ? 'km' : 'en'
        };

        $patient_form.find(":radio").iCheck({ checkboxClass: 'icheckbox_minimal', radioClass: 'iradio_minimal' });
        $patient_form.find("input:checkbox").iCheck({ checkboxClass : 'icheckbox_minimal', radioClass : 'iradio_minimal' });
        
        $patient_form.find("#patient_dob").datetimepicker(dtPickerOption).on("dp.change", function () {
            var dob = $(this).data("DateTimePicker").date();
            if (dob) {
                var age = calculateAge(dob);
                $patient_form.find("#patient-age-year").val(age.years);
                $patient_form.find("#patient-age-month").val(age.months);
                var days = age.days === 0 && age.months === 0 && age.years === 0 ? 1 : age.days;
                $patient_form.find("#patient-age-day").val(days);
            }
        });

        $patient_form.on("keyup change", "#patient-age-year, #patient-age-month, #patient-age-day", function (evt) {
            var years  = $patient_form.find("#patient-age-year").val() || 0;
            var months = $patient_form.find("#patient-age-month").val() || 0;
            var days   = $patient_form.find("#patient-age-day").val() || 0;

            var dob = moment();
            dob.subtract(days, 'days');
            dob.subtract(months, 'months');
            dob.subtract(years, 'years');

            $patient_form.find("#patient_dob").data("DateTimePicker").setDate(dob.toDate());
        });
        /**14072021 */
        $patient_form.find("#date_arrival").datetimepicker(dtPickerOption);
        $patient_form.find("#test_date").datetimepicker(dtPickerOption);
        $patient_form.find("#first_vaccinated_date").datetimepicker(dtPickerOption); //12072021
        $patient_form.find("#second_vaccinated_date").datetimepicker(dtPickerOption); //12072021
        $patient_form.find('input:checkbox[name="is_positive_covid"]').on("ifToggled", function (evt) {
            if ($(this).is(":checked")) {
                $patient_form.find("div.test_date_wrapper").removeClass("hidden");
            } else {
                $patient_form.find("div.test_date_wrapper").addClass("hidden");
            }
        });
        $patient_form.find('input:checkbox[name="is_contacted"]').on("ifToggled", function (evt) {
            if ($(this).is(":checked")) {
                $patient_form.find("div.contact_wrapper").removeClass("hidden");
            } else {
                $patient_form.find("div.contact_wrapper").addClass("hidden");
            }
        });       
        $("select#vaccination_status").on("change",function(evt){
            var val = $(this).val();
            if(val == '-1' || val == '1'){           
                $('select#vaccine option[value=-1]').prop("selected", true);            
                $("#first_vaccinated_date" ).val('');
                $("#second_vaccinated_date" ).val('');
                $("select#vaccine_id , #first_vaccinated_date, #second_vaccinated_date").attr('disabled','disabled');
            }else if(val == 2){
                $("#second_vaccinated_date" ).val('');
                $("#second_vaccinated_date").attr('disabled','disabled');
                $("select#vaccine_id, #first_vaccinated_date").removeAttr('disabled');
            }else if(val == 3){
                $("select#vaccine_id, #first_vaccinated_date, #second_vaccinated_date").removeAttr('disabled');
            }
        })    
        /**
         * Load Gazetteer (patient's entry form)
         */
        $("select#province, select#district, select#commune").on("change", function (evt, data) {
            var get = $(this).attr("data-get");

            var val = data != undefined ? data.cur : $(this).val();

            $.ajax({
                url: base_url + 'gazetteer/get_' + get,
                type: 'POST',
                data: {code: val},
                dataType: 'json',
                success: function (resText) {
                    $target = $("#" + get);
                    $target.find("option").not(":eq(0)").remove();

                    for (var i in resText) {
                        var selected = "";
                        if (data != undefined && resText[i].code == data.next) {
                            selected = "selected";
                        }

                        var name = 'name_' + app_lang;
                        $opt = $("<option value='" + resText[i].code + "' " + selected + ">" + resText[i][name] + "</option>");

                        $target.append($opt);
                    }
                }
            });
        });

        /**
         * Show Patient's Edit Form
         */
        $("#edit-patient").on("click", function (evt) {
            evt.preventDefault();
            //console.log(patient_info);
            if (patient_info) {
                var dob = moment(patient_info.dob).toDate();
                var sex = patient_info.sex == 'M' ? MALE : FEMALE;

                $patient_form.find("#patient_name").val(patient_info.name);
                $patient_form.find("input[name=patient_sex][value=" + sex + "]").iCheck('check');
                $patient_form.find("#patient_dob").data("DateTimePicker").setDate(dob);
                $patient_form.find("#phone").val(patient_info.phone);
                $patient_form.find("#province").val(patient_info.province);
                $patient_form.find("#province").trigger('change', {
                    cur  : patient_info.province,
                    next : patient_info.district
                }); //load district
                $patient_form.find("#district").trigger('change', {
                    cur  : patient_info.district,
                    next : patient_info.commune
                }); //load commune
                $patient_form.find("#commune").trigger('change', {
                    cur  : patient_info.commune,
                    next : patient_info.village
                }); //load village
                
                //14072021                
                $patient_form.find("#residence").val(patient_info.residence);
                $patient_form.find("#passport_number").val(patient_info.passport_number);
                $patient_form.find("#seat_number").val(patient_info.seat_number);
                $patient_form.find("#flight_number").val(patient_info.flight_number);                
                if(patient_info.test_date !== null){
                    var test_date = moment(patient_info.test_date).toDate();
                    //console.log("test date value "+test_date);
                    $patient_form.find("#test_date").data("DateTimePicker").setDate(test_date);
                }
                if(patient_info.date_arrival !== null){
                    var date_arrival = moment(patient_info.date_arrival).toDate();
                    $patient_form.find("#date_arrival").data("DateTimePicker").setDate(date_arrival);
                }
                if(patient_info.is_positive_covid == 1){
                    $patient_form.find("input[name=is_positive_covid]").iCheck('check');
                }
                if(patient_info.is_contacted == 1){
                    $patient_form.find("input[name=is_contacted]").iCheck('check');
                }
                $patient_form.find("#contact_with").val(patient_info.contact_with);
                $patient_form.find("#relationship_with_case").val(patient_info.relationship_with_case);
                $patient_form.find("#travel_in_past_30_days").val(patient_info.travel_in_past_30_days);
                if(patient_info.country) $patient_form.find("#country").val(patient_info.country).change();

                if(patient_info.country_name){
                    $patient_form.find("#country_name").val(patient_info.country_name); // 26-05-2021
                }else{
                    $patient_form.find("#country_name").val(patient_info.country_name_en)
                }

                if(patient_info.nationality) $patient_form.find("#nationality").val(patient_info.nationality).change();

                var v = patient_info.is_direct_contact;
                if(patient_info.is_direct_contact !== "") $patient_form.find("input[name=is_direct_contact][value=" + v + "]").iCheck('check');            
                if(patient_info.vaccination_status !== null){
                    $patient_form.find("#vaccination_status").val(patient_info.vaccination_status).change();
                    if(patient_info.vaccine_id !== null) $patient_form.find("#vaccine_id").val(patient_info.vaccine_id).change();
                    if(patient_info.first_vaccinated_date !== null){
                        var first_vaccinated_date = moment(patient_info.first_vaccinated_date).toDate();
                        $patient_form.find("#first_vaccinated_date").data("DateTimePicker").setDate(first_vaccinated_date);
                    }
                    if(patient_info.second_vaccinated_date !== null){
                        var second_vaccinated_date = moment(patient_info.second_vaccinated_date).toDate();
                        $patient_form.find("#second_vaccinated_date").data("DateTimePicker").setDate(second_vaccinated_date);
                    }
                }
                $patient_form.find("#occupation").val(patient_info.occupation);
                //End
            }
            
            $patient_view.hide();
            $patient_form.fadeIn(300);
        });

        /**
         * Cancel Patient's entry form
         */
        $("#btnCancelPatient").on("click", function (evt) {
            evt.preventDefault();

            $patient_form.find("input:not(:radio)").val("");
            $patient_form.find("#patient_dob").data("DateTimePicker").clear();
            $patient_form.find("#province").val(-1);
            $patient_form.find("#district").find("option[value!=-1]").remove();
            $patient_form.find("#commune").find("option[value!=-1]").remove();
            $patient_form.find("#village").find("option[value!=-1]").remove();
            $patient_form.hide();
            $patient_view.fadeIn(300);
        });

        //Save outside patient
        $("#btnSavePatient").on("click", function (evt) {
            evt.preventDefault();

            var data = {};
            data.patient_name           = $patient_form.find("#patient_name").val().trim();
            data.sex                    = $patient_form.find("input[name=patient_sex]:checked").val() || undefined;
            data.dob                    = $patient_form.find("#patient_dob").data("DateTimePicker").date();
            data.phone                  = $patient_form.find("#phone").val().trim() || undefined;
            data.province               = $patient_form.find("#province").val();
            data.commune                = $patient_form.find("#commune").val();
            data.district               = $patient_form.find("#district").val();
            data.village                = $patient_form.find("#village").val();
            //14072021
            data.residence		        = $patient_form.find("#residence").val().trim();            
            data.country		        = undefined;
            data.country_name	        = $patient_form.find("#country_name").val().trim(); // 27-05-2021
            data.nationality 	        = $patient_form.find("select[name=nationality]").val() || undefined;
            data.date_arrival	        = $patient_form.find("#date_arrival").data("DateTimePicker").date() || undefined;
            data.passport_number        = $patient_form.find("#passport_number").val().trim();
            data.seat_number            = $patient_form.find("#seat_number").val().trim();
            data.flight_number	        = $patient_form.find("#flight_number").val().trim();
            data.is_positive_covid      = $patient_form.find("#is_positive_covid").is(":checked") ? 1 : 0;            
            data.test_date              = $patient_form.find("input[name=test_date]").data("DateTimePicker").date() || undefined;               
            data.is_contacted           = $patient_form.find("#is_contacted").is(":checked") ? 1 : 0;
            data.contact_with           = $patient_form.find("#contact_with").val().trim();
            data.relationship_with_case = $patient_form.find("#relationship_with_case").val().trim();
            data.travel_in_past_30_days = $patient_form.find("#travel_in_past_30_days").val().trim();            
            is_check                    = $patient_form.find("input[name=is_direct_contact]:checked").val();
            data.is_direct_contact	    = $patient_form.find("input[name=is_direct_contact]").is(":checked") ? is_check : undefined;        
            data.vaccination_status     = $patient_form.find("select[name=vaccination_status]").val() || undefined;
            data.vaccine_id             = $patient_form.find("select[name=vaccine_id]").val() || undefined;        
            data.first_vaccinated_date	= $patient_form.find("input[name=first_vaccinated_date]").data("DateTimePicker").date() || undefined;
            data.second_vaccinated_date	= $patient_form.find("input[name=second_vaccinated_date]").data("DateTimePicker").date() || undefined;
            if(data.test_date !== undefined) {data.test_date = data.test_date.format('YYYY-MM-DD')}; // added 02 Dec 2020
            if(data.date_arrival !== undefined) data.date_arrival = data.date_arrival.format('YYYY-MM-DD'); // added 02 Dec 2020
            if(data.first_vaccinated_date !== undefined) data.first_vaccinated_date = data.first_vaccinated_date.format('YYYY-MM-DD'); // added 12-07-2021
            if(data.second_vaccinated_date !== undefined) data.second_vaccinated_date = data.second_vaccinated_date.format('YYYY-MM-DD'); // added 12-07-2021
            data.occupation             = $patient_form.find("#occupation").val().trim();

            //Validation
            var is_valid = true;
            if (data.patient_name.length == 0) {
                is_valid = false;
            }
            if (data.sex <= 0 || data.sex > 2 || !data.sex) {
                is_valid = false;
            }
            if (data.dob == null) {
                is_valid = false;
            }

            if (data.dob && data.dob.isAfter(moment())) {
                $patient_form.find("label[for=patient_dob]").attr("data-hint", msg_dob_not_after_now);
                return false;
            } else {
                $patient_form.find("label[for=patient_dob]").removeAttr("data-hint");
            }

            if (!is_valid) {
                myDialog.showDialog('show', {text: msg_required_data, style: 'warning'});
                return false;
            }

            //Format data
            data.dob = data.dob.format('YYYY-MM-DD');

            myDialog.showProgress('show');

            $.ajax({
                url: base_url + "patient/update_outside_patient/" + patient_info.pid,
                type: 'POST',
                dataType: 'json',
                data: { patient : data },
                success: function (resText) {
                    myDialog.showProgress('hide');

                    myDialog.showDialog('show', {
                        text: resText.msg,
                        style: resText.status == true ? 'success' : 'warning',
                        onHidden: function () {
                            if (resText.status) location.reload();
                        }
                    });

                    // hidden search and add new sample
                    $("div.form-vertical").css("display","none");
                },
                error: function () {
                    myDialog.showProgress('hide');
                    myDialog.showDialog('show', {text: msg_save_fail, style: 'warning'});
                }
            });
        });

        $(".btnDeleteSample").on("click", function (evt) {
            evt.preventDefault();
            var psample_id = $(this).attr('data-value');
            var tbody = $("#psample_list tbody");
            var tr = $(this).closest("tr");

            if (confirm("Do you really want to delete this sample?")) {
                if (confirm("If you click Yes, this sample will be deleted!")) {
                    $.ajax({
                        url: base_url + 'patient_sample/delete',
                        type: 'POST',
                        data: {patient_sample_id: psample_id},
                        dataType: 'json',
                        success: function (resText) {
                            if (resText.status == true) {
                                tr.remove();
                                if (tbody.find("tr").length == 0) {
                                    tbody.append("<tr><td colspan='10' class='text-center'>No Sample!</td></tr>");
                                }
                            }
                        },
                        error: function (resText) {

                        }
                    });
                }
            }
        });
    }
});
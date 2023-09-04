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

$(function () {
    var $modal_test				= $("#test_modal");
    var $modal_result			= $("#result_modal");
    var $modal_rejection		= $("#modal-rejection");
    var $modal_reject_comment   = $("#modal-reject-comment");
    var $modal_existed_patient  = $("#modal-existed-patient");
    var $print_preview_modal	= $("#print_preview_modal");
    var $sample_forms			= $("#sample-forms");
    var $patient_info_wrapper	= $("#patient-info-wrapper");
    var $patient_info_form		= $("#patient-info-form");
    var $patient_info_view		= $("#patient-info-view");
    var print_button            = "";

    //Init iCheck
    $("#patient-info-form input, #test_modal .tree-list input, #sample-forms form.frm-sample-entry, #modal-rejection #reject_sample").iCheck({ checkboxClass: 'icheckbox_minimal', radioClass: 'iradio_minimal' });

    //Set Treeview style
    $modal_test.find(".tree-list").treeview({ collapsed : false, animated : true });
    $modal_test.find(".total-test-payment").autoNumeric({vMin: 0, aPad: 0});

    //Init Select2
    $("#province, #district, #commune, #village, #country, #nationality").select2();

    //Render Test List
    (new TestList()).generate();

	/* ========================================================================================================= */

    //Patient's DOB input
    $patient_info_form.find("#patient_dob").datetimepicker(dtPickerOption).on("dp.change", function() {
        var dob = $(this).data("DateTimePicker").date();

        if (dob) {
            var age = calculateAge(dob);
            $patient_info_form.find("#patient-age-year").val(age.years);
            $patient_info_form.find("#patient-age-month").val(age.months);
            var days = age.days === 0 && age.months === 0 && age.years === 0 ? 1 : age.days;
            $patient_info_form.find("#patient-age-day").val(days);
        }
    });

    $patient_info_form.on("keyup change", "#patient-age-year, #patient-age-month, #patient-age-day", function (evt) {
        var years  = $patient_info_form.find("#patient-age-year").val() || 0;
        var months = $patient_info_form.find("#patient-age-month").val() || 0;
        var days   = $patient_info_form.find("#patient-age-day").val() || 0;

        var dob = moment();
        dob.subtract(days, 'days');
        dob.subtract(months, 'months');
        dob.subtract(years, 'years');

        $patient_info_form.find("#patient_dob").data("DateTimePicker").setDate(dob.toDate());
    });

    // Added 02 Dec 2020
    $patient_info_form.find("#date_arrival").datetimepicker(dtPickerOption);
    $patient_info_form.find("#test_date").datetimepicker(dtPickerOption);
    
    $patient_info_form.find('input:checkbox[name="is_positive_covid"]').on("ifToggled", function (evt) {        
        if ($(this).is(":checked")) {
            $patient_info_form.find("div.test_date_wrapper").removeClass("hidden");
        } else {
            $patient_info_form.find("div.test_date_wrapper").addClass("hidden");
        }
    });
    $patient_info_form.find('input:checkbox[name="is_contacted"]').on("ifToggled", function (evt) {        
        if ($(this).is(":checked")) {
            $patient_info_form.find("div.contact_wrapper").removeClass("hidden");
        } else {
            $patient_info_form.find("div.contact_wrapper").addClass("hidden");
        }
    });
	/* ========================================================================================================= */

    //Show Patient's entry form
    $("#btnNewPatient").on("click", function (evt) {
        evt.preventDefault();

        $("#no-result").hide();
        $patient_info_view.hide();
        $patient_info_form.find("input:not(:radio)").val("");
        $("#search_patient_id").val("");
        $patient_info_form.find("#patient_dob").data("DateTimePicker").clear();
        $patient_info_form.find("#province").val(-1);
        $patient_info_form.find("#district").find("option[value!=-1]").remove();
        $patient_info_form.find("#commune").find("option[value!=-1]").remove();
        $patient_info_form.find("#village").find("option[value!=-1]").remove();
        $patient_info_form.find("#btnSavePatient").removeData("pid");
        $patient_info_form.find("#patient-manual-code").removeClass("duplicate");
        $patient_info_form.find("#patient-manual-code").prop("disabled", false);
        $patient_info_wrapper.find("input[type=hidden]#patient-id, input[type=hidden]#patient-age, input[type=hidden]#patient-sex").removeData("value");
        $patient_info_wrapper.find("input[type=hidden]#patient-id, input[type=hidden]#patient-age, input[type=hidden]#patient-sex").removeAttr("data-value");

        //Remove all sample entry form and hide from user
        $sample_forms.find("div.sample-form").remove();
        $("#sample-form-wrapper").hide();

        $("#edit-patient").hide();

        //Show form entry
        $patient_info_form.fadeIn(600);
        patient_info = null;
    });

	/* ========================================================================================================= */

    //Load Gazetteer (patient's entry form)
    $("select#province, select#district, select#commune").on("change", function (evt, data) {
        var get = $(this).attr("data-get");

        var val = data != undefined ? data.cur : $(this).val();
       
        $.ajax({
            url		 : base_url + 'gazetteer/get_' + get,
            type	 : 'POST',
            data	 : { code : val },
            dataType : 'json',
            success	 : function (resText) {
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

	/* ========================================================================================================= */

    /**
     * Cancel Patient's entry form
     */
    $("#btnCancelPatient").on("click", function (evt) {
        evt.preventDefault();

        $patient_info_wrapper.find("input[type=hidden]#patient-id, input[type=hidden]#patient-age, input[type=hidden]#patient-sex").removeData("value");
        $patient_info_wrapper.find("input[type=hidden]#patient-id, input[type=hidden]#patient-age, input[type=hidden]#patient-sex").removeAttr("data-value");
        $patient_info_form.find("input:not(:radio)").val("");
        $patient_info_form.find("#patient_dob").data("DateTimePicker").clear();
        $patient_info_form.find("#btnSavePatient").removeData("pid");
        $patient_info_form.find("#patient-manual-code").removeClass("duplicate");
        $("#province").val(-1);
        $("#district").find("option[value!=-1]").remove();
        $("#commune").find("option[value!=-1]").remove();
        $("#village").find("option[value!=-1]").remove();
        $patient_info_form.fadeOut(300);

        if (patient_info) $patient_info_view.fadeIn(300);
    });

    /**
     * Save/Update outside patient
     */
    $("#btnSavePatient").on("click", function (evt) {
        evt.preventDefault();
        
        var data			   = {};
        data.patient_manual_code = $patient_info_form.find("#patient-manual-code").val().trim() || undefined;
        data.patient_name	   = $patient_info_form.find("#patient_name").val().trim();
        data.sex			   = $patient_info_form.find("input[name=patient_sex]:checked").val();
        data.dob			   = $patient_info_form.find("#patient_dob").data("DateTimePicker").date();
        data.phone			   = $patient_info_form.find("#phone").val().trim() || undefined;
        data.province		   = $patient_info_form.find("#province").val();
        data.commune		   = $patient_info_form.find("#commune").val();
        data.district		   = $patient_info_form.find("#district").val();
        data.village		   = $patient_info_form.find("#village").val();

        //Update 02 Dec 2020
        data.residence		   = $patient_info_form.find("#residence").val().trim();
        data.country		   = $patient_info_form.find("select[name=country]").val() || undefined;
        data.nationality 	   = $patient_info_form.find("select[name=nationality]").val() || undefined;
        data.date_arrival	   = $patient_info_form.find("#date_arrival").data("DateTimePicker").date() || undefined;
        data.passport_number   = $patient_info_form.find("#passport_number").val().trim();
        data.seat_number       = $patient_info_form.find("#seat_number").val().trim();
        data.flight_number	   = $patient_info_form.find("#flight_number").val().trim();
        data.is_positive_covid = $patient_info_form.find("#is_positive_covid").is(":checked") ? 1 : 0;
        
        data.test_date         = $patient_info_form.find("input[name=test_date]").data("DateTimePicker").date() || undefined;   
        
        data.is_contacted      = $patient_info_form.find("#is_contacted").is(":checked") ? 1 : 0;
        data.contact_with      = $patient_info_form.find("#contact_with").val().trim();
        data.relationship_with_case = $patient_info_form.find("#relationship_with_case").val().trim();
        data.travel_in_past_30_days = $patient_info_form.find("#travel_in_past_30_days").val().trim();
        //End
        // add 12 Jan 2021
        is_check                    = $patient_info_form.find("input[name=is_direct_contact]:checked").val();
        data.is_direct_contact	    = $patient_info_form.find("input[name=is_direct_contact]").is(":checked") ? is_check : undefined;
       
        // End

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
            $patient_info_form.find("label[for=patient_dob]").attr("data-hint", msg_dob_not_after_now);
           
            return false;
        } else {
            $patient_info_form.find("label[for=patient_dob]").removeAttr("data-hint");
        }

        if (!is_valid) {
            myDialog.showDialog('show', {text: msg_required_data, style: 'warning'});
            return false;
        }

        //Format data
        data.dob = data.dob.format('YYYY-MM-DD');

        if(data.test_date !== undefined) {data.test_date = data.test_date.format('YYYY-MM-DD')}; // added 02 Dec 2020
        if(data.date_arrival !== undefined) data.date_arrival = data.date_arrival.format('YYYY-MM-DD'); // added 02 Dec 2020
    
        var url  = "patient/save_outside_patient";
        if (patient_info && patient_info.pid > 0) {
            url = "patient/update_outside_patient/" + patient_info.pid;
        }
        
        $.ajax({
            url		: base_url + url,
            type	: 'POST',
            dataType: 'json',
            data	: { patient : data },
            success	: function (resText) {
               
                
                if(resText.id_exist) {
                    $patient_info_form.find("#patient-manual-code").addClass("duplicate");
                    $modal_existed_patient.removeData("patient");
                    $modal_existed_patient.find("#tbl-existed-patient").find("tbody").empty();

                    var dob = moment(resText.patient.dob, 'YYYY-MM-DD');
                    var tr  = "<tr>";
                    tr += "<td>"+ resText.patient.patient_code +"</td>";
                    tr += "<td>"+ resText.patient.name +"</td>";
                    tr += "<td>"+ resText.patient.sex +"</td>";
                    tr += "<td>"+ (dob ? dob.format('DD/MM/YYYY') : '') +"</td>";
                    tr += "<td>"+ (resText.patient.phone || '') +"</td>";
                    tr += "<td>";
                    tr += (resText.patient['village_' + app_lang] || '?') + ' - ';
                    tr += (resText.patient['commune_' + app_lang] || '?') + ' - ';
                    tr += (resText.patient['district_' + app_lang] || '?') + ' - ';
                    tr += (resText.patient['province_' + app_lang] || '?');
                    tr += "</td>";
                    tr += "<tr>";

                    $modal_existed_patient.find("#tbl-existed-patient").find("tbody").html(tr);
                    $modal_existed_patient.data("patient", resText.patient);
                    $modal_existed_patient.modal({backdrop : 'static'});
                }

                if (resText.status == true && resText.patient && !resText.id_exist) {
                    $("#btnSavePatient").data("pid", resText.patient.pid);

                    //$("#search_patient_id").trigger("keyup", [resText.code]);
                    $patient_info_form.hide();

                    //Show Patient Info
                    $patient_info_view.find(".patient-code").text(resText.patient.patient_code);
                    $patient_info_view.find(".patient-name").text(resText.patient.name);

                    var sex = '';
                    if (app_lang == 'kh') sex = resText.patient.sex == 'M' ? 'ប្រុស' : 'ស្រី';
                    else if (app_lang == 'en') sex = resText.patient.sex == 'M' ? 'Male' : 'Female';
                    $patient_info_view.find(".gender").text(sex);

                    var dob = moment(resText.patient.dob, 'YYYY-MM-DD');
                    var age = calculateAge(dob);
                    var days = age.days === 0 && age.months === 0 && age.years === 0 ? 1 : age.days;

                    $patient_info_view.find(".age-year").text(age.years);
                    $patient_info_view.find(".age-month").text(age.months);
                    $patient_info_view.find(".age-day").text(days);

                    $patient_info_view.find(".phone").text(resText.patient.phone);
                    $patient_info_view.find(".address-village").text(resText.patient['village_' + app_lang]);
                    $patient_info_view.find(".address-commune").text(resText.patient['commune_' + app_lang]);
                    $patient_info_view.find(".address-district").text(resText.patient['district_' + app_lang]);
                    $patient_info_view.find(".address-province").text(resText.patient['province_' + app_lang]);

                    $patient_info_wrapper.find("input[type=hidden]#patient-id").attr("data-value", resText.patient.pid);
                    $patient_info_wrapper.find("input[type=hidden]#patient-age").attr("data-value", moment().diff(moment(resText.patient.dob, 'YYYY-MM-DD'), 'days'));
                    $patient_info_wrapper.find("input[type=hidden]#patient-sex").attr("data-value", resText.patient.sex);

                    //ADDED 02 Dec 2020
                    $patient_info_view.find(".residence").text(resText.patient['residence']);
                    $patient_info_view.find(".country").text(resText.patient['country_name_en']);
                    $patient_info_view.find(".nationality").text(resText.patient['nationality_en']);
                    $patient_info_view.find(".passport_number").text(resText.patient['passport_number']);
                    $patient_info_view.find(".seat_number").text(resText.patient['seat_number']);
                    $patient_info_view.find(".flight_number").text(resText.patient['flight_number']);
                    if(resText.patient['is_positive_covid']){
                        $patient_info_view.find(".is_positive_covid").text(resText.patient['is_positive_covid']);
                        $patient_info_view.find(".test_date").text(resText.patient['test_date']);
                    }

                    $patient_info_view.find(".contact_with").text(resText.patient['contact_with']);
                    $patient_info_view.find(".date_arrival").text(resText.patient['date_arrival']);
                    $patient_info_view.find(".relationship_with_case").text(resText.patient['relationship_with_case']);
                    
                    $patient_info_view.find(".is_contacted").text(resText.patient['is_contacted']);
                    $patient_info_view.find(".travel_in_past_30_days").text(resText.patient['travel_in_past_30_days']);
                    // END
                    

                    //show sample entry form
                    $patient_info_view.fadeIn(700);
                    $("#no-result").hide();
                    $("#edit-patient").show();

                    if (!patient_info) {
                        $sample_forms.find("div.sample-form").remove();
                        $("#btnMore").trigger("click");
                        $("div#sample-form-wrapper").fadeIn(700);                        
                    }

                    // assign patient info to global variable
                    patient_info = $.extend({}, resText.patient);
                }

                myDialog.showDialog('show', { text	: resText.msg, style : resText.status == true ? 'success' : 'warning' });
            },
            error: function () {
                myDialog.showDialog('show', { text	: msg_save_fail, style	: 'warning' });
            }
        });
    });

    /* ========================================================================================================= */
    /**
     * Search Existed Patient
     */
    $modal_existed_patient.on("click", "button.use-existed-patient", function (evt) {
        evt.preventDefault();
        var patient = $modal_existed_patient.data("patient");
        $("#search_patient_id").val(patient.patient_code);
        $("#frm-search-patient").submit();
        $modal_existed_patient.modal("hide");
    });

	/* ========================================================================================================= */
    //Check Required Value
    //Search patient's by ID
    $("#frm-search-patient").on("submit", function (evt, patient_id, type) {
        evt.preventDefault();
        patient_id	 = patient_id != undefined ? patient_id : $(this).find("#search_patient_id").val();
        patient_info = null;
        type		 = type != undefined ? type : "view";

        $("#no-result").hide();
        $patient_info_view.hide();
        $patient_info_form.hide();
        $("div#sample-form-wrapper").hide();
        $patient_info_wrapper.find("input[type=hidden]#patient-id, input[type=hidden]#patient-age, input[type=hidden]#patient-sex").removeData("value");
        $patient_info_wrapper.find("input[type=hidden]#patient-id, input[type=hidden]#patient-age, input[type=hidden]#patient-sex").removeAttr("data-value");
        if (!patient_id) return false;

        myDialog.showProgress('show', {text: '', appendTo: $("section.content-body")});
        $(this).blur();

        $.ajax({
            url: base_url + 'patient/search/' + patient_id,
            type: 'POST',
            data: {pid: patient_id},
            dataType: 'json',
            success: function (resText) {
                myDialog.showProgress('hide', {
                    onHidden: function () {
                        var patient = resText.patient;
                        if (patient) {
                            patient_info = $.extend({}, patient);
                            if (type == "view") {
                                $patient_info_view.find(".patient-code").text(patient.patient_code);
                                $patient_info_view.find(".patient-name").text(patient.name);

                                var sex = '';
                                if (app_lang == 'kh') sex = patient.sex == 'M' ? 'ប្រុស' : 'ស្រី';
                                else if (app_lang == 'en') sex = patient.sex == 'M' ? 'Male' : 'Female';
                                $patient_info_view.find(".gender").text(sex);

                                var dob = moment(patient.dob, 'YYYY-MM-DD');
                                var age = calculateAge(dob);
                                var days = age.days === 0 && age.months === 0 && age.years === 0 ? 1 : age.days;

                                $patient_info_view.find(".age-year").text(age.years);
                                $patient_info_view.find(".age-month").text(age.months);
                                $patient_info_view.find(".age-day").text(days);


                                $patient_info_view.find(".phone").text(patient.phone);
                                $patient_info_view.find(".address-village").text(patient['village_' + app_lang]);
                                $patient_info_view.find(".address-commune").text(patient['commune_' + app_lang]);
                                $patient_info_view.find(".address-district").text(patient['district_' + app_lang]);
                                $patient_info_view.find(".address-province").text(patient['province_' + app_lang]);
                               

                                //ADDED 03 Dec 2020
                                $patient_info_view.find(".residence").text(resText.patient['residence']);
                                $patient_info_view.find(".country").text(resText.patient['country_name_en']);
                                $patient_info_view.find(".nationality").text(resText.patient['nationality_en']);
                                $patient_info_view.find(".passport_number").text(resText.patient['passport_number']);
                                $patient_info_view.find(".seat_number").text(resText.patient['seat_number']);
                                if(resText.patient['is_positive_covid']){
                                    $patient_info_view.find(".is_positive_covid").text(resText.patient['is_positive_covid']);
                                    $patient_info_view.find(".test_date").text(resText.patient['test_date']);
                                }

                                $patient_info_view.find(".contact_with").text(resText.patient['contact_with']);
                                $patient_info_view.find(".date_arrival").text(resText.patient['date_arrival']);
                                $patient_info_view.find(".relationship_with_case").text(resText.patient['relationship_with_case']);
                                
                                $patient_info_view.find(".is_contacted").text(resText.patient['is_contacted']);
                                $patient_info_view.find(".travel_in_past_30_days").text(resText.patient['travel_in_past_30_days']);
                                // END
                            }

                            //set require value for saving sample
                            $patient_info_wrapper.find("input[type=hidden]#patient-id").attr("data-value", patient.pid);
                            $patient_info_wrapper.find("input[type=hidden]#patient-age").attr("data-value", moment().diff(dob, 'days'));
                            $patient_info_wrapper.find("input[type=hidden]#patient-sex").attr("data-value", patient.sex);

                            $patient_info_view.fadeIn(700);
                            $("#no-result").hide();
                            $sample_forms.find("div.sample-form").remove();
                            $("div#sample-form-wrapper").fadeIn(700);

                            if (!patient.is_pmrs_patient) $("#edit-patient").show();

                            $.ajax({
                                url      : base_url + "patient_sample/get_patient_sample",
                                type     : "POST",
                                dataType : "json",
                                data     : { patient_id : patient.pid, laboratory_id: LABORATORY_SESSION.labID },
                                success  : function (resText) {
                                    
                                    if (resText.patient_samples && resText.patient_samples.length > 0) {
                                        for(var i in resText.patient_samples) {
                                            $("#btnMore").trigger("click", [null, null, resText.patient_samples[i]]);
                                        }
                                    }
                                    $("#btnMore").trigger("click");
                                },
                                error    : function () {
                                    $("#btnMore").trigger("click");
                                }
                            });
                        } else {
                            $(evt.target).focus();
                            $("#no-result").fadeIn(500);
                            $patient_info_view.hide();
                            $sample_forms.find("div.sample-form").remove();
                            $("div#sample-form-wrapper").hide();
                        }
                    }
                });
            },
            error: function () {
                myDialog.showProgress('hide', {
                    onHidden: function () {
                        $(evt.target).focus();
                        $("#no-result").fadeIn(500);
                        $patient_info_view.hide();
                        $sample_forms.find("div.sample-form").remove();
                        $("div#sample-form-wrapper").hide();
                    }
                });
            }
        });
    });

	/* ========================================================================================================= */

    /**
     * Search for patient info. on load page
     */
    if ($("#search_patient_id").length == 1 && $("#search_patient_id").val().trim() != "") {
        $("#frm-search-patient").submit();
    }

	/* ========================================================================================================= */

    /**
     * todo Show Edit Patient Form
     */
    $("#edit-patient").on("click", function (evt) {
        evt.preventDefault();

        if (patient_info) {
            var dob = moment(patient_info.dob).toDate();
            
            //var sex = patient_info.sex === 'M' ? MALE : FEMALE;
			var sex = patient_info.sex === 'M' ? 1 : 2;
            $patient_info_form.find("#patient-manual-code").val(patient_info.patient_code);
            $patient_info_form.find("#patient-manual-code").prop("disabled", true);
            $patient_info_form.find("#patient_name").val(patient_info.name);
            $patient_info_form.find("input[name=patient_sex][value=" + sex + "]").iCheck('check');
            $patient_info_form.find("#patient_dob").data("DateTimePicker").setDate(dob);
            $patient_info_form.find("#phone").val(patient_info.phone);
            $patient_info_form.find("#province").val(patient_info.province);

            // ADD 03 Dec 2020
            $patient_info_form.find("#residence").val(patient_info.residence);
            $patient_info_form.find("#passport_number").val(patient_info.passport_number);
            $patient_info_form.find("#seat_number").val(patient_info.seat_number);
            $patient_info_form.find("#flight_number").val(patient_info.flight_number);
            
            if(patient_info.test_date !== null){
                var test_date = moment(patient_info.test_date).toDate();
                $patient_info_form.find("#test_date").data("DateTimePicker").setDate(test_date);
            }
            if(patient_info.date_arrival !== null){
                var date_arrival = moment(patient_info.date_arrival).toDate();
                $patient_info_form.find("#date_arrival").data("DateTimePicker").setDate(date_arrival);
            }
            if(patient_info.is_positive_covid == 1){
                $patient_info_form.find("input[name=is_positive_covid]").iCheck('check');
            }
            if(patient_info.is_contacted == 1){
                $patient_info_form.find("input[name=is_contacted]").iCheck('check');
            }
            $patient_info_form.find("#contact_with").val(patient_info.contact_with);
            $patient_info_form.find("#relationship_with_case").val(patient_info.relationship_with_case);
            $patient_info_form.find("#travel_in_past_30_days").val(patient_info.travel_in_past_30_days);
            if(patient_info.country) $patient_info_form.find("#country").val(patient_info.country).change();
            if(patient_info.nationality) $patient_info_form.find("#nationality").val(patient_info.nationality).change();

            var v = patient_info.is_direct_contact;
            if(patient_info.is_direct_contact !== "") $patient_info_form.find("input[name=is_direct_contact][value=" + v + "]").iCheck('check');
            // End

            // ADDED 13 DEC 2020
            // trigger collapse
            
            if((patient_info.residence !== "") 
                || (patient_info.country !== null) 
                || (patient_info.nationality !== null)
                || (patient_info.passport_number !== "")
                || (patient_info.seat_number !== "")
                || (patient_info.flight_number !== "")
                || (patient_info.date_arrival !== null)
                || (patient_info.is_positive_covid == 1) 
                || (patient_info.is_contacted == 1)
            ){
                $('#covidQuestionaireInfo').collapse();
            }
            // End trigger

            $patient_info_form.find("#province").trigger('change', {
                cur  : patient_info.province,
                next : patient_info.district
            }); //load district
            $patient_info_form.find("#district").trigger('change', {
                cur  : patient_info.district,
                next : patient_info.commune
            }); //load commune
            $patient_info_form.find("#commune").trigger('change', {
                cur  : patient_info.commune,
                next : patient_info.village
            }); //load village

            $patient_info_view.hide();
            $patient_info_form.fadeIn(300);
        }
    });

    /* ========================================================================================================= */

    /**
     * Add New Sample Form
     */
    $("#btnMore").on("click", function (evt, initValue, $sampleForm, sampleFormData) {
        evt.preventDefault();

        var admission_dates = _.map(patient_info ? patient_info.admissiondate : [], function (d) {
            return moment(d, 'YYYY-MM-DD HH:mm:ss');
        });

        initValue = $.extend(
            {},
            {
                admission_date: moment.max(admission_dates).toDate()
            },
            initValue
        );

        addSampleForm(initValue, $sampleForm, sampleFormData);
    });

    //For Edit Form
    if ($sample_forms.find("div.panel.sample-form.edit").length > 0) {
        myDialog.showProgress("show");

        $.ajax({
            url: base_url + 'patient/search/' + patient_info.patient_code,
            type: 'POST',
            dataType: 'json',
            success: function (resText) {                
                myDialog.showProgress("hide");
                patient_info = resText.patient;
               
                $("#btnMore").trigger("click", [null, $sample_forms.find("div.panel.sample-form.edit:first-child"), PATIENT_SAMPLE]);

                /**
                 * TODO : Page Action
                 */
                if (page_action && page_action === "rs") {
                    var $sampleForm = $sample_forms.find("div.panel.sample-form.edit:first-child");
                    if ($sampleForm.length > 0) {
                        $sampleForm.find("button.btnAddResult").trigger("click");
                    }
                }
            }
        });
    }

	/* ========================================================================================================= */

    /**
     * Minimized the Sample box
     */
    $sample_forms.on("click", ".panel-heading", function (evt) {
        evt.preventDefault();

        $(this).siblings(".panel-body").slideToggle();
        $(this).find(".btnMinimized").find("i.fa").toggleClass("fa-plus fa-minus");
    });

	/* ========================================================================================================= */

    /**
     * Remove Sample
     */
    $sample_forms.on("click", ".btnRemove", function (evt) {
        evt.preventDefault();
        if (confirm(q_delete_patient_sample)) {
            var form              = $(this).parents("form.frm-sample-entry");
            var sampleForm        = new SampleForm(form);
            var patient_sample_id = sampleForm.get_data().patient_sample_id;
            var $panel            = $(this).parents(".panel.sample-form");

            if (patient_sample_id > 0 && $panel) {
                //Show Progress
                myDialog.showProgress('show', { text : msg_loading });

                $.ajax({
                    url		: base_url + 'patient_sample/delete',
                    type	: 'POST',
                    data	: { patient_sample_id : patient_sample_id },
                    dataType: 'json',
                    success	: function (resText) {
                        myDialog.showProgress('hide');
                        if (resText.status === true) {
                            $panel.slideUp(600, function () {
                                var $nextAll = $panel.nextAll("div.sample-form");
                                if ($nextAll.length > 0) {
                                    $nextAll.each(function () {
                                        var order = parseInt($(this).find("span.sample-order").text());
                                        $(this).find("span.sample-order").text(order - 1);
                                    });
                                }

                                $panel.remove();
                            });
                            myDialog.showDialog('show', { text : resText.msg, style : 'success', onHidden : function () {
                                //window.location = base_url + 'sample/view';
                            } });
                        } else {
                            myDialog.showDialog('show', { text : resText.text, style : 'warning' });
                        }
                    },
                    error	: function () {
                        myDialog.showProgress('show');
                        myDialog.showDialog('show', { text : msg_delete_fail, style : 'warning' });
                    }
                });
            }
            else {
                $panel.slideUp(600, function () {
                    var $nextAll = $panel.nextAll("div.sample-form");
                    if ($nextAll.length > 0) {
                        $nextAll.each(function () {
                            var order = parseInt($(this).find("span.sample-order").text());
                            $(this).find("span.sample-order").text(order - 1);
                        });
                    }

                    $panel.remove();
                });
            }
        }
    });

	/* ========================================================================================================= */

    /**
     * Get Requester Base on sample source
     */
    $sample_forms.on("change", "select[name=sample_source]", function (evt, requester_id) {
        evt.preventDefault();

        var form = $(this).parents("form.frm-sample-entry");
        var requester = form.find("select[name=requester]");
        var sample_source_id = $(this).val();

        form.find("select[name=requester] option[value!=-1]").remove();
        form.find("select[name=requester]").val(-1).trigger("change");

        if (sample_source_id <= 0) return false;

        $.ajax({
            url		: base_url + 'requester/get_lab_requester',
            type	: 'POST',
            data	: { sample_source_id: sample_source_id },
            dataType: 'json',
            async	: false,
            success	: function (resText) {
                if (resText.requesters.length > 0) {
                    for(var i in resText.requesters) {
                        var selected = '';
                        if (requester_id == resText.requesters[i].requester_id) selected = 'selected';
                        var opt = "<option value='" + resText.requesters[i].requester_id + "' "+ selected +">" + resText.requesters[i].requester_name + "</option>";
                        requester.append(opt);
                    }
                }
            },
            error : function (evt) {

            }
        });
    });

	/* ========================================================================================================= */

    /**
     * Save/Update Sample
     */
    $sample_forms.on("click", ".btnSaveSample", function (evt, data) {
        evt.preventDefault();

        var form = $(this).parents("form.frm-sample-entry");
        var sampleForm = new SampleForm(form);

        var test_payments = _.map(sampleForm.form.data("test-payment"), function (d) {
            return _.omit(d, 'id');
        });
              
        sampleForm.save({ patient : patient_info, test_payments: test_payments });
        return false;
    });

    /**
     * Check all rejected Test
     */
    $modal_rejection.find("#reject_sample").on("ifChanged", function (evt) {
        var $test_list  = $modal_rejection.find("div.test-list");
        var $checkboxes = $test_list.find("input:checkbox[name=sample_test]");
        var test_count  = $checkboxes.length;
        var test_check  = $test_list.find("input:checkbox[name=sample_test]:checked").length;

        if ($(this).is(':checked')) {
            $checkboxes.iCheck('check');
        } else if (test_count === test_check) {
            $checkboxes.iCheck('uncheck');
        }
    });

    /**
     * Check Reject Sample when all tests are selected
     */
    $modal_rejection.find("div.test-list").on("ifChanged", ":checkbox[name=sample_test]", function (evt) {
        var $test_list = $modal_rejection.find("div.test-list");
        var test_count = $test_list.find("input:checkbox[name=sample_test]").length;
        var test_check = $test_list.find("input:checkbox[name=sample_test]:checked").length;

        var department_sample_id = $(this).closest(".test-group").attr("id");
        var group_test_count = $(this).closest(".test-group").find(":checkbox[name=sample_test]").length;
        var group_selected_test_count = $(this).closest(".test-group").find(":checkbox[name=sample_test]:checked").length;

        if (test_check !== test_count) {
            $modal_rejection.find("#reject_sample").iCheck('uncheck');
        } else {
            $modal_rejection.find("#reject_sample").iCheck('check');
        }

        if (group_test_count === group_selected_test_count) {
            $modal_rejection.find(":checkbox[name=department_sample][value="+ department_sample_id +"]").iCheck('check');
        } else {
            $modal_rejection.find(":checkbox[name=department_sample][value="+ department_sample_id +"]").iCheck('uncheck');
        }
    });

    /**
     * Check all test in department sample
     */
    $modal_rejection.find("div.test-list").on("ifChanged", ":checkbox[name=department_sample]", function (evt) {
        var val = $(this).val();
        var test_count = $modal_rejection.find(".test-group#" + val).find(":checkbox[name=sample_test]").length;
        var selected_test_count = $modal_rejection.find(".test-group#" + val).find(":checkbox[name=sample_test]:checked").length;
        if ($(this).is(":checked")) {
            $modal_rejection.find(".test-group#" + val).find(":checkbox[name=sample_test]").iCheck('check');
        } else if (test_count === selected_test_count) {
            $modal_rejection.find(".test-group#" + val).find(":checkbox[name=sample_test]").iCheck('uncheck');
        }
    });

    /**
     * Show Reject Sample/Test Modal
     */
    $sample_forms.on("click", ".btnRejectSample", function (evt) {
        evt.preventDefault();

        var $test_list	= $modal_rejection.find("div.test-list");
        var form 		= $(this).parents("form.frm-sample-entry");
        var sampleForm	= new SampleForm(form);
        var data		= sampleForm.get_data();

        if (data.patient_sample_id == "") return false;

        $test_list.empty();
        $modal_rejection.find("label[for=reject_comment]").removeAttr("data-hint");
        $modal_rejection.find("#sample_test_error").removeAttr("data-hint");
        $modal_rejection.find("textarea#reject_comment").val('');
        $modal_rejection.find("#reject_sample").iCheck('uncheck');
        $modal_rejection.removeData('patient_sample_id');
        $modal_rejection.removeData('department_sample');

        //Show Progress
        myDialog.showProgress('show', { text : msg_loading });

        $.ajax({
            url		 : base_url + 'patient_sample/get_patient_sample_test',
            type	 : 'POST',
            data	 : {patient_sample_id : data.patient_sample_id, rejection : 200 },
            dataType : 'json',
            success	 : function (resText) {
                myDialog.showProgress('hide');
                var test_list = "";
                var department_sample = [];
                //Set Sample Test
                if (resText.sample_tests) {
                    for (var i in resText.sample_tests) {
                        var department_name = resText.sample_tests[i].department_name;
                        //Samples
                        var samples_list = resText.sample_tests[i].samples;
                        for (var j in samples_list) {
                            department_sample.push(samples_list[j].department_sample_id);

                            var sample_name = samples_list[j].sample_name;
                            var department_sample_id = samples_list[j].department_sample_id;
                            test_list += "<div class='row'>";
                            test_list += "<div class='col-sm-12'>";
                            test_list += "<label class='control-label text-blue pointer' style='border-bottom: 1px solid #e4dbdb; padding-bottom: 10px;'><input type='checkbox' value='"+ department_sample_id +"' name='department_sample'>&nbsp;<b>" + department_name + " &nbsp;<i class='fa fa-link'></i> &nbsp;" + sample_name + "</b></label>";
                            //Tests
                            test_list += "<div class='row form-group test-group' id='"+ department_sample_id +"'>";
                            for (var k in samples_list[j].tests) {
                                var test = samples_list[j].tests[k];
                               
                                if (test.is_heading == false) {                                                                        
                                    var checked = test.is_rejected == false ? "" : "checked";
                                   
                                    test_list += "<div class='col-sm-4'>";
                                    test_list += "<label class='control-label' style='cursor: pointer'><input type='checkbox' name='sample_test' value='" + test.patient_test_id + "' " + checked + "> " + test.test_name + "</label>";
                                    test_list += "</div>";
                                }
                            }
                            test_list += "</div>";
                            test_list += "</div>";
                            test_list += "</div>";
                        }
                    }
                }

                $test_list.html(test_list);
                $test_list.find("input:checkbox").iCheck({ checkboxClass : 'icheckbox_minimal', radioClass : 'iradio_minimal' });
                $test_list.find("input:checkbox[name=sample_test]:eq(0)").trigger("ifChanged");

                //Set Comment
                var reject_cmt = resText.patient_sample.reject_comment;
                if (reject_cmt != null) reject_cmt = reject_cmt.split("<br/>").join("\n");
                $modal_rejection.find("textarea#reject_comment").val(reject_cmt);

                $modal_rejection.data('patient_sample_id', data.patient_sample_id);
                $modal_rejection.data("department_sample", department_sample);
                $modal_rejection.modal({backdrop : 'static'});
            },
            error	 : function () {
                myDialog.showProgress('hide');
            }
        });
    });





    /**
     * Save Test Rejection
     */
    $modal_rejection.find("#btnAddRejection").on("click", function (evt) {
        evt.preventDefault();

        var $test_list	= $modal_rejection.find("div.test-list");
        var data				= {};
        data.patient_sample_id	= $modal_rejection.data('patient_sample_id');
        data.reject_comment		= $modal_rejection.find("#reject_comment").val().trim();
        data.reject_sample		= $modal_rejection.find("input:checkbox#reject_sample:checked").length;
        data.reject_tests		= [];
        $modal_rejection.find("input:checkbox[name=sample_test]:checked").each(function () {
            data.reject_tests.push($(this).val());
        });
        data.reject_sample = $test_list.find("input:checkbox[name=sample_test]").length == data.reject_tests.length ? 1 : 0;

        //Validation
        var is_valid = true;
        $modal_rejection.find("label[for=reject_comment]").removeAttr("data-hint");
        $modal_rejection.find("#sample_test_error").removeAttr("data-hint");
        if (data.reject_comment.length == 0 && data.reject_tests.length > 0) {
            $modal_rejection.find("label[for=reject_comment]").attr("data-hint", msg_reject_cmt_err);
            is_valid = false;
        }
        // if (data.reject_tests.length == 0) {
        //     $modal_rejection.find("#sample_test_error").attr("data-hint", msg_choose_sample_test);
        //     is_valid = false;
        // }
        if (is_valid && Number(data.patient_sample_id) > 0) {
            //Show Progress
            myDialog.showProgress('show', { text : msg_loading });

            $.ajax({
                url: base_url + 'patient_sample/set_rejection',
                type: 'POST',
                data: data,
                dataType: 'json',
                success: function (resText) {
                    myDialog.showProgress('hide');
                    myDialog.showDialog('show', { text	: resText.msg, style : resText.status === true ? 'success' : 'warning' });
                    /*if (resText.status === true) {
                        $modal_rejection.removeData('patient_sample_id');
                        $modal_rejection.modal("hide");
                    }*/
                },
                error : function () {
                    myDialog.showProgress('hide');
                    myDialog.showDialog('show', { text	: msg_save_fail, style : 'warning' });
                }
            });
        }
    });

    /**
     * Show Reject comment List
     */
    $modal_rejection.on("click", "button.show-reject-comment-modal", function(evt) {
        var selected_comment = {};
        $modal_reject_comment.find("#tbl-reject-comment").removeData("selected_comment");

        $modal_reject_comment.find("#tbl-reject-comment").DataTable({
            destroy     : true,
            autoWidth   : false,
            info        : false,
            processing  : true,
            serverSide  : true,
            ajax        : {
                url  : base_url + 'comment/view_std_sample_comment',
                type : 'POST',
                data : function (data) {
                    console.log(data);
                    data.department_sample = $modal_rejection.data("department_sample") || [0];
                    //data.is_reject_comment = 1;
                    data.is_reject_comment = "'t'";
                }
            },
            columns     : [
                {"data" : "comment_id", "render" : function (value) {
                    return "<input type='checkbox' value='"+ value +"'>";
                }},
                {"data" : "comment"}
            ],
            columnDefs  : [
                {targets : 0, className : 'text-center'},
                {targets : '_all', className: 'text-middle'}
            ],
            order       : [[1, 'asc']],
            language    : dataTableOption.language,
            createdRow  : function (row, data) {
                $(row).find(":checkbox")
                    .iCheck({ checkboxClass : 'icheckbox_minimal', radioClass : 'iradio_minimal' })
                    .on("ifChecked", function () {
                        selected_comment['comment' + $(this).val()] = {
                            comment_id : $(this).val(),
                            comment    : data.comment
                        };
                    })
                    .on("ifUnchecked", function () {
                        delete selected_comment['comment' + $(this).val()];
                    })
                    $(row).find('.iCheck-helper').css('position', 'relative'); 
            }
        });

        $modal_reject_comment.data("selected_comment", selected_comment);
        $modal_reject_comment.modal({backdrop : 'static'});
    });

    /**
     * Add Reject comment
     */
    $modal_reject_comment.on("click", "button.add-reject-comment", function () {
        var selected_comment = $modal_reject_comment.data("selected_comment") || {};
        var comment = [];
        for(var i in selected_comment) {
            comment.push(selected_comment[i].comment);
        }

        $modal_rejection.find("textarea#reject_comment").val(comment.join("\r\n"));
        $modal_reject_comment.modal("hide");
    });

    /**
     * Print Reject comment
     */
    $modal_rejection.on("click", "button.print-reject-test", function (evt) {
        evt.preventDefault();
        var patient_sample_id = $modal_rejection.data("patient_sample_id");

        $print_preview_modal.css("background", 'rgba(0, 0, 0, 0.54)');
        $print_preview_modal.find("#doPrinting").off("click").on("click", function (evt) {
            evt.preventDefault();
            printpage(base_url + "patient_sample/preview_rejected_test/" + patient_sample_id + "/print");
        });

        $print_preview_modal.modal("show");
        //Show Progress
        myDialog.showProgress('show', { text : msg_loading });

        $.ajax({
            url		: base_url + "patient_sample/preview_rejected_test/" + patient_sample_id,
            type	: 'POST',
            success	: function (resText) {
                $print_preview_modal.find(".modal-dialog").html(resText);

                setTimeout(function() {
                    myDialog.showProgress('hide');
                    $print_preview_modal.modal("show");
                }, 400);
            }
        });
    });
    /**
    * Click approve button
    */
   $print_preview_modal.on('click', '#approve', function(event) {
       console.log("btnApprove were clicked...!!!");
        event.preventDefault();
        /*show processingg dialog*/
        myDialog.showProgress('show', { text : globalMessage.loading });
        /*update the approve/verify the result*/
        $.ajax({
            url: base_url + 'patient_sample/approve_result',
            type: 'POST',
            dataType: 'json',
            data: {
                // get patient_sample_id from hidden text field in patient_sample_result.php
                patient_sample_id: $("#print_preview_modal").find('#patient_sample_id').val()
            },
            success: function (data) {
                console.log(data)
                if (data.message && data.verify >= 1) {
                    /*check permission for print*/
                    if (typeof print_button != 'undefined') {
                        /*change to print icon, text, id and add event click*/
                        $("#print_preview_modal").find('#approve').html(print_button).prop('id', 'printing').click(function(event) {
                            /*print the result*/
                            printpage(base_url + "result/patient_sample_result/print/" + $("#print_preview_modal").find('#patient_sample_id').val());
                            /*update printed info*/
                            $.ajax({
                                url: base_url + "patient_sample/update_printed_info",
                                type: 'POST',
                                dataType: 'json',
                                data: { patient_sample_id: $("#print_preview_modal").find('#patient_sample_id').val() },
                            });
                        });
                        /*close processing dialog*/
                        setTimeout(function() {myDialog.showProgress('hide');},400);
                        /*Terminate the function*/
                        return 0;
                    }
                    /*Close the modal when user no permission to print*/
                    $('#print_preview_modal').modal('toggle');
                }

                if (data.message && data.verify <= 1) {
                    /*Not complete result cannot print close modal*/
                    $('#print_preview_modal').modal('toggle');
                    /*close processing dialog*/
                    setTimeout(function() { myDialog.showProgress('hide'); }, 400);
                    /*Terminate the function*/
                    return 0;
                }

                if (!data.message && data.verify === 1) {
                    $print_preview_modal.find('li.approve').remove();
                    /*Not complete result cannot print close modal*/
                    //$('#print_preview_modal').modal('toggle');
                    /*close processing dialog*/
                    setTimeout(function() { myDialog.showProgress('hide'); }, 400);
                    /*Terminate the function*/
                    return 0;
                }
                /*close processing dialog*/
                setTimeout(function() { myDialog.showProgress('hide'); }, 400);
            }
        });
    });


    /* ========================================================================================================= */

    /**
     * Search Test Tree
     */
    $modal_test.find(".tree-filter").on("keyup", function (evt) {
        var search_text = $(this).val();
        var regExp		= new RegExp(search_text, "i");
        var tree		= $(this).parents("div.tree-list-wrapper").find("div.tree-list");
        
        tree.find("ul li[is_heading=false] label").each(function() {
            var tName = $(this).find("span.t-name").text().trim();
            
            if (regExp.test(tName)) {
                $(this).parent("li").show();
            } else {
                $(this).parent("li").hide();
            }
        });
    });

    /**
     * Check/Uncheck Test Hierarchy
     */
    $modal_test.find(".tree-list").on("ifChanged", 'input:checkbox.sample-test', function () {
        //Check Parent
        var parentID		 = $(this).attr('parent');
        var $tree			 = $(this).parents(".tree-list");
        var $parent			 = $tree.find('input:checkbox[value=' + parentID + '].sample-test');
        var $subList		 = $tree.find('input:checkbox[parent=' + parentID + '].sample-test');
        var $selectedSubList = $tree.find('input:checkbox[parent=' + parentID + '].sample-test:checked');

        if ($selectedSubList.length > 0 && $selectedSubList.length < $subList.length) {
            $parent.iCheck('indeterminate');
        }
        else if ($selectedSubList.length === $subList.length) {
            $parent.iCheck('check');
        }

        if ($selectedSubList.length === 0) {
            $parent.iCheck('determinate');
            $parent.iCheck('uncheck');
        }

        //Check/Uncheck all sub list
        var sample_test_id	= $(this).attr('value');
        $subList			= $tree.find('input[parent=' + sample_test_id +'].sample-test');
        $selectedSubList	= $tree.find('input[parent=' + sample_test_id +'].sample-test:checked');

        if ($(this).is(":checked")) {
            $tree.find("input[parent="+ sample_test_id + "].sample-test").iCheck("check");
        } else if ($subList.length === $selectedSubList.length) {
            //uncheck sublist when parent is uncheck and all child are checked
            $tree.find("input[parent="+ sample_test_id + "].sample-test").iCheck("uncheck");
        }

        //Calculate total test payment
        var test_price = {};
        var total = 0;
        $modal_test.find(".tree-list").find(":checkbox:checked").each(function () {
            var group_result = $(this).data("group-result");
            var price        = $(this).data("test-price") || 0;
            if (group_result && !test_price[group_result]) {
                test_price[group_result] = parseFloat(price);
                total += parseFloat(price);
            }
        });

        $modal_test.find(".total-test-payment").autoNumeric("set", total);
    });

    /**
     * Show Test Modal for Assigning
     */
    $sample_forms.on("click", ".btnShowTestModal", function (evt) {
        evt.preventDefault();

        $modal_test.find("input[type=text]").val('');
        $modal_test.find(".total-test-payment").val(0);
        $modal_test.find("select").val(-1);
        $modal_test.find(".tree-filter").trigger("keyup");
        $modal_test.find('.tree-list input[type=checkbox].sample-test').iCheck('uncheck');

        var form		= $(this).parents("form.frm-sample-entry");
        var sampleForm 	= new SampleForm(form);
        var data		= sampleForm.get_data();

        //Validation
        if (!sampleForm.validate_fields()) {
            myDialog.showDialog('show', {text: msg_required_data, style: 'warning'});
            return false;
        }
        if (!sampleForm.validate_date_time()) {
            myDialog.showDialog('show', {text: msg_col_rec_dt_error, style: 'warning'});
            return false;
        }

        $modal_test.data("sampleForm", sampleForm);

        //Set test price
        $modal_test.find(":checkbox.sample-test").each(function() {
            var group_result = $(this).data("group-result");
            if (group_result) {
                var payment = _.chain(TEST_PAYMENTS).filter(function(d) { return d.group_result == group_result && d.payment_type_id == data.payment_type_id; }).first().value();
                if (payment) {
                    $(this).data("test-price", payment.price);
                }
            }
        });

        //Loading
        myDialog.showProgress('show', { text : msg_loading });
        $.ajax({
            url		 : base_url + 'patient_sample/get_patient_sample_test',
            type	 : 'POST',
            data	 : {patient_sample_id : data.patient_sample_id, patient_id : data.patient_id},
            dataType : 'json',
            success	 : function (resText) {
                myDialog.showProgress('hide');
                //Set Sample Test
                if (resText.sample_tests && Array.isArray(resText.sample_tests) && resText.sample_tests.length > 0) {
                    for (var i in resText.sample_tests) {
                        //if(resText.sample_tests[i].is_heading == 0) {
                        if(resText.sample_tests[i].is_heading == false) { // added 17 Jan 2021
                            $modal_test.find("input#st-" + resText.sample_tests[i].sample_test_id).iCheck('check');
                        }
                    }
                }

                // set to header patient
                if (resText.patient) {
                    $modal_test.find("span[id=sp-header_pid]").text(resText.patient.pid);
                    $modal_test.find("span[id=sp-header_name]").text(resText.patient.name);
                    $modal_test.find("span[id=sp-sample_number]").text(resText.patient_sample[0].sample_number);
                    $modal_test.find("span[id=sp-sample_source_name]").text(resText.patient_sample[0].sample_source_name);
                }

                //Set Sample Details
                if (resText.sample_details && Array.isArray(resText.sample_details) && resText.sample_details.length > 0) {
                    for (var i in resText.sample_details) {
                        var $department_sample  = $modal_test.find("#dsample-" + resText.sample_details[i].department_sample_id);
                        if ($department_sample != undefined) {
                            var $sample_desc    = $department_sample.find("select[name=sample_desc]");
                            var $first_weight   = $department_sample.find("input[name=first_weight]");
                            var $second_weight  = $department_sample.find("input[name=second_weight]");
                            if ($sample_desc   != undefined) $sample_desc.val(resText.sample_details[i].sample_description_id);
                            if ($first_weight  != undefined) $first_weight.val(resText.sample_details[i].sample_volume1);
                            if ($second_weight != undefined) $second_weight.val(resText.sample_details[i].sample_volume2);
                        }
                    }
                }
                $modal_test.modal({backdrop : 'static'});
            },
            error	 : function () {

            }
        });
    });

    /**
     * Toggle Sample Info
     */
    $modal_test.on("click", "button.btn-toggle-sample-info", function (evt) {
        evt.preventDefault();
        var $wrapper = $(this).closest("div.sample-type-header-wrapper");
        $wrapper.find("div.sample-info-wrapper").slideToggle();
        $(this).find("i.fa").toggleClass("fa-chevron-up fa-chevron-down");
    });

    /**
     * Save Assigned Test
     */
    $modal_test.find(".btnAssignTest").on("click", function (evt) {
        evt.preventDefault();
        
        var sample_tests	= [];
        var sample_details	= [];
        var test_payments   = {};
        $modal_test.find(".modal-body .department-test").each(function () {
            var $department_samples = $(this).find("div.sample-type-wrapper");
            if ($department_samples.length > 0) {
                $department_samples.each(function () {
                    var dsample_id		= Number($(this).data('department-sample'));
                    var $selected_tests	= $(this).find(":checkbox.sample-test:checked");
                    var $sample_desc	= $(this).find("select.sample-desc");
                    var first_weight	= $(this).find("input[name=first_weight]").val();
                    var second_weight	= $(this).find("input[name=second_weight]").val();

                    $(this).find(":checkbox.sample-test").each(function () {
                        var checkbox = $(this).get(0);
                        var id = $(this).val();
                        if (checkbox && checkbox.indeterminate && id > 0) {
                            sample_tests.push(id);
                        }
                    });

                    if ($selected_tests.length > 0) {
                        if (!isNaN(dsample_id) && dsample_id > 0 && $sample_desc.length > 0) {
                            sample_details.push({
                                'department_sample_id'	: dsample_id,
                                'sample_description'	: $sample_desc.val(),
                                'first_weight'			: first_weight === undefined ? null : first_weight,
                                'second_weight'			: second_weight === undefined ? null : second_weight
                            });
                        }

                        //Selected test
                        $selected_tests.each(function () {
                            var val = Number($(this).val());
                            if (!isNaN(val) && val > 0) sample_tests.push(val);

                            var group_result = $(this).data("group-result");
                            var price        = $(this).data("test-price") || 0;
                            if (group_result && price > 0) {
                                test_payments[group_result] = {
                                    group_result: group_result,
                                    price: parseFloat(price)
                                };
                            }
                        });
                    }
                });
            }
        });

        if (sample_tests.length === 0) {
            myDialog.showDialog('show', { text	: msg_must_select_test, style	: 'warning' });
            return false;
        }

        var sampleForm	= $modal_test.data("sampleForm");
        var isAddResult	= $(this).hasClass('add-sample-result');
        if (sampleForm instanceof SampleForm) {
            sampleForm.save({
                patient			: patient_info,
                sample_tests	: sample_tests,
                sample_details	: sample_details,
                test_payments   : test_payments,
                is_assign_test	: 200
            }, function (data) {
                sampleForm.btnAddResult.prop("disabled", false);
                sampleForm.btnRejectSample.prop("disabled", false);
                sampleForm.btnPreview.prop("disabled", false);
                sampleForm.btnPreview.attr("data-enabled", 1);
                sampleForm.form.data("test-payment", test_payments);
                $modal_test.removeData("sampleForm");
                $modal_test.modal('hide');
            }, function (data) {                
                if (isAddResult) sampleForm.btnAddResult.trigger("click");
            });
        }
    });

    /**
     * Print Reject comment
     */
    $modal_test.on("click", "button.btn-print", function (evt) {
        evt.preventDefault();
        var sampleForm = $modal_test.data("sampleForm");
        var data       = sampleForm.get_data();

        $print_preview_modal.css("background", 'rgba(0, 0, 0, 0.54)');
        $print_preview_modal.find(".modal-dialog").addClass("A4-landscape");
        $print_preview_modal.find("#doPrinting").off("click").on("click", function (evt) {
            evt.preventDefault();
            printpage(base_url + "patient_sample/preview_patient_sample_test/" + data.patient_sample_id + "/print");
        });

        $print_preview_modal.modal("show");
        //Show Progress
        myDialog.showProgress('show', { text : msg_loading });

        $.ajax({
            url		: base_url + "patient_sample/preview_patient_sample_test/" + data.patient_sample_id,
            type	: 'POST',
            success	: function (resText) {
                $print_preview_modal.find(".modal-dialog").html(resText);

                setTimeout(function() {
                    myDialog.showProgress('hide');
                    $print_preview_modal.modal("show");
                }, 400);
            }
        });
    });

	/* ========================================================================================================= */
    /**
     * Show Result Modal
     */
    $sample_forms.on("click", ".btnAddResult", function (evt) {
        evt.preventDefault();

        var form        = $(this).parents("form.frm-sample-entry");
        var sampleForm  = new SampleForm(form);
        var resultForm  = new ResultForm();
        var data        = sampleForm.get_data();
       
        var lastTestDate  = null;
        var lastPerformer = null;

        //No patient's sample ID
        if (data.patient_sample_id <= 0) {
            $(this).prop("disabled", true);
            return false;
        }

        var $test_list = $modal_result.find("table#tb_test_result tbody");
        $modal_result.removeData("sampleForm");
        $modal_result.removeData("patient_sample_id");
        $test_list.empty();

        //Set Info
        $modal_result.find("div.text-blue.patient-id").text(patient_info.patient_code);
        $modal_result.find("div.text-blue.patient-name").text(patient_info.name);
        $modal_result.find("div.text-blue.sample-number").text(data.sample_number);
        $modal_result.find("div.text-blue.sample-source").text(data.sample_source_title);

        //Show Progress
        myDialog.showProgress('show', { text : msg_loading });

        //Get Details of Patient's Sample
        $.ajax({
            url		 : base_url + 'patient_sample/get_patient_sample_details',
            type	 : 'POST',
            data	 : { patient_sample_id : data.patient_sample_id, patient_id: data.patient_id, patient_age : data.patient_age, patient_sex : data.patient_sex },
            dataType : 'json',
            success	 : function (resText) {
               
                myDialog.showProgress('hide');

                //Set Info
                $modal_result.find("div.sample-number").text(resText.patient_sample.sample_number);
                $modal_result.find("div.sample-source").text(resText.patient_sample.sample_source_name);
                
                var html = resultForm.renderList(resText.sample_tests);
                console.log("Sample Test-------------------");
                console.log(resText.sample_tests);
                $test_list.append(html);

                //Set user
                $test_list.find(".result-entry-user-list, .result-entry-date-list, .result-modified-user-list, .result-modified-date-list").empty();
                var result_users = resText.result_users || [];                
                for(var i in result_users) {
                    
                    var $header = $test_list.find("tr#dsample-" + result_users[i].department_id + "-" + result_users[i].sample_id);
                    var entry_users = result_users[i].entry_users ? result_users[i].entry_users.split(',') : [];
                    var entry_dates = result_users[i].entry_dates ? result_users[i].entry_dates.split(',') : [];
                    var modified_users = result_users[i].modified_users ? result_users[i].modified_users.split(',') : [];
                    var modified_dates = result_users[i].modified_dates ? result_users[i].modified_dates.split(',') : [];
                   
                    $.each(entry_users, function (index, user) {
                        $header.find(".result-entry-user-list").append("<span class='label label-primary'>"+ user +"</span> ")
                    });
                    $.each(entry_dates, function (index, date) {
                        $header.find(".result-entry-date-list").append("<span class='label label-primary'>"+ date +"</span> ")
                    });
                    $.each(modified_users, function (index, user) {
                        $header.find(".result-modified-user-list").append("<span class='label label-primary'>"+ user +"</span> ")
                    });
                    $.each(modified_dates, function (index, date) {
                        $header.find(".result-modified-date-list").append("<span class='label label-primary'>"+ date +"</span> ")
                    });
                }

                //Init DatePicker
                var dtOption	= JSON.parse(JSON.stringify(dtPickerOption)); //clone object
                dtOption.widgetPositioning.vertical = 'auto';
                $test_list.find("td input.test_date").datetimepicker(dtOption);

                //Init Select2
                $test_list.find("select.performer").select2();

                //init icheck
                $test_list.find(":checkbox.test-visibility").iCheck({ checkboxClass: 'icheckbox_minimal', radioClass: 'iradio_minimal' });

                //Hide/Show Child
                $test_list.on("click", "td.header", function(evt) {
                    evt.preventDefault();
                    var sample_test_id = $(this).parents("tr").data("sample-test-id");
                    $test_list.find("tr[parent=" + sample_test_id + "]").fadeToggle(200);
                    $(this).find("i.fa").toggleClass("fa-caret-up fa-caret-down");
                });

                //Set result comment
                if (resText.result_comment) {
                    for(var i in resText.result_comment) {
                        $modal_result.find("textarea#result-comment-" + resText.result_comment[i].department_sample_id).val(resText.result_comment[i].result_comment);
                    }
                }

                //Set Previous Result set and Ref. Range
                //$test_list.find("tr[data-rejected=0]").each(function () {
                //Added: 11252020
                $test_list.find("tr[data-rejected=false]").each(function () {
                    var patient_test_id	= $(this).data("patient-test-id");
                    var sample_test_id	= $(this).data("sample-test-id");
                    var field_type		= Number($(this).data("field-type"));
                    var is_rejected		= $(this).data("rejected");
                   
                   // if (is_rejected == 1) return true;
                   if (is_rejected == true) return true;

                    //Set Ref. Range
                    var ref_range = resText.ref_ranges[sample_test_id];
                    if (ref_range) {
                        var minValue = ref_range.range_sign !== '-' && ref_range.min_value == 0 ? '' : ref_range.min_value;
                        $(this).find("td.ref-range").html(minValue + " " + ref_range.range_sign + " " + ref_range.max_value);
                        $(this).find("td.ref-range").data('ref-range', ref_range);
                    }
                    
                    //Result                    
                    if (resText.results && resText.results[patient_test_id] != undefined) {
                        var $inputTestDate  = $(this).find("td input.test_date");
                        var $inputPerformer = $(this).find("td select.performer");
                        var test_date = moment(resText.results[patient_test_id].test_date, 'YYYY-MM-DD');

                        if (test_date.isValid() && $inputTestDate.length > 0) $inputTestDate.data("DateTimePicker").setDate(test_date.toDate());
                        if ($inputTestDate.length > 0) $inputTestDate.data("DateTimePicker").minDate(data.received_date.toDate());
                        if ($inputPerformer.length > 0) $inputPerformer.val(resText.results[patient_test_id].performer_id).trigger("change");

                        //Multiple/Single Result set
                        console.log("Result-------------------");
                        console.log(resText.results[patient_test_id].result);
                        console.log("-----------------");

                        if ([1, 2].indexOf(field_type) > -1) {
                            $(this).find("td input.result").data("possible_result", resText.results[patient_test_id].result);
                            if (Array.isArray(resText.results[patient_test_id].result)) {
                                var text = [];
                                for (var i in resText.results[patient_test_id].result) {
                                    var name = resText.results[patient_test_id].result[i].organism_name;
                                    if (name == undefined) continue;
                                    text.push(name.trim());
                                }

                                $(this).find("td input.result").val(text.join(', '));
                            }
                        } else if ([3, 4, 5].indexOf(field_type) > -1) {
                            var result_value =(resText.results[patient_test_id].result).split("%");// resText.results[patient_test_id].result;
                            // field type = 5 is calculate
                            if(field_type==5) {
                                if(result_value.length>1){
                                    result_value = parseFloat(result_value[1].trim());
                                }

                                console.log("-----I'm field 5 Calculated: my value =  "+ resText.results[patient_test_id].result);

                                if(isFloat( resText.results[patient_test_id].result )){
                                    res = (Number(resText.results[patient_test_id].result)).toFixed(1);
                                    if (res.match(/\./)) {
                                        res = res.replace(/\.?0+$/, '');
                                    }
                                    ///$(this).find("td input.result").val(Number(resText.results[patient_test_id].result).toFixed(2));
                                    $(this).find("td input.result").val(res);
                                    //$(this).find("td input.result").val(Number(resText.results[patient_test_id].result).toFixed(1));
                                }else{
                                    $(this).find("td input.result").val(resText.results[patient_test_id].result);
                                }

                            }else{
                                console.log("----------I'm field 3 | 4: my value = "+resText.results[patient_test_id].result);
                                
                                // Check if Result value is Float 
                                // X.0 is also accept
                                // only 2 decimal taken. include 0

                                
                                if(isFloat( resText.results[patient_test_id].result )){
                                    // count number of decimal
                                    var afterDecimalStr = resText.results[patient_test_id].result.toString().split('.')[1] || '';
                                    if(afterDecimalStr.length > 3){
                                        $(this).find("td input.result").val(Number(resText.results[patient_test_id].result).toFixed(2));
                                    }
                                    $(this).find("td input.result").val(resText.results[patient_test_id].result);
                                    /*
                                    res = (Number(resText.results[patient_test_id].result)).toFixed(2);
                                    if (res.match(/\./)) {
                                        res = res.replace(/\.?0+$/, '');
                                    }
                                    $(this).find("td input.result").val(res);
                                    */
                                    //$(this).find("td input.result").val(Number(resText.results[patient_test_id].result).toFixed(2));
                                    //$(this).find("td input.result").val(res);
                                }else{
                                    $(this).find("td input.result").val(resText.results[patient_test_id].result);
                                }
                                
                            }
                            
                            result_value = Number(resText.results[patient_test_id].result).toFixed(1);

                            if (ref_range && (field_type == 3 || field_type == 5)) {
                                result_value = parseFloat(result_value);
                                
                                if (["-", "≤"].indexOf(ref_range.range_sign) > -1) {
                                    if (!(result_value >= ref_range.min_value  && result_value <= ref_range.max_value)) $(this).addClass("out-of-range");
                                    else $(this).removeClass("out-of-range");
                                }
                                else if (ref_range.range_sign == "<") {
                                    if (!(result_value >= ref_range.min_value && result_value < ref_range.max_value)) $(this).addClass("out-of-range");
                                    else $(this).removeClass("out-of-range");
                                }
                            } else {
                                $(this).removeClass("out-of-range");
                            }
                        }
                    }
                });

                //set test visibility
                $test_list.find("tr.test").each(function() {
                    var is_show = $(this).data("is-show");
                    var state   = is_show == 1 ? 'uncheck' : 'check';
                    $(this).find(":checkbox.test-visibility").iCheck(state);


                    //Machine event
                    var $machine = $(this).find(".machine");
                    $machine.click(function(event) {
                        event.preventDefault();
                        var $test_id = $(this).attr('test_id');
                        $.ajax({
                            url: base_url + 'machine',
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                id: $test_id,
                                lab_id: LABORATORY_SESSION.labID
                            },
                            success : function (response) {
                                var $machine_modal = $("#modal-machine");
                                $machine_modal.find('#machine-id').select2({placeholder: 'Select machine'});
                                $machine_modal.find('#machine-id').html('<option></option');
                                response.forEach(function(data) {
                                    $machine_modal.find('#machine-id').append("<option value='" + data.id+ "'>" + data.machine_name + "</option>");
                                });
                                $machine_modal.modal('show');
                                 //Clear component on Hide
                                $machine_modal.off("hide.bs.modal").on("hide.bs.modal", function() {
                                   $machine_modal.find('#machine-id').html('');
                                });
                                $machine_modal.find('#machine-id').select2({placeholder: 'Select machine'});
                                //Choose event
                                $machine_modal.find('#btn-choose').unbind('click').click(function(event) {
                                    var $machine_choose = $machine_modal.find('#machine-id option:selected').text();
                                    ($machine_choose != "") ? $machine.data('machine-name', $machine_choose).html($machine_choose) : $machine.removeData('machine-name').html("<i class='fa fa-hospital-o'></i>");
                                    $machine_modal.modal("hide");
                                });
                            },
                            error : function() {}
                        });
                    });
                    /*
                    * Enable && Disable result
                    */
                    var result = (typeof $(this).find("td input.result").val() != "undefined") ? $(this).find("td input.result").val() : '';
                    /*
                    * check for verify the result
                    * 1 : Input some result
                    * 2 : Complete input result
                    */
                    if (parseInt(resText.patient_sample.verify) >= 1 && result.length != 0) {
                        $(this).find("td input.result").prop("disabled", true);
                        $(this).find("td input.test_date").prop("disabled", true);
                        $(this).find("td .performer").prop("disabled", true);
                        $(this).find(":checkbox.test-visibility").prop("disabled", true);
                    }
                    /*
                    * Check reverify permission
                    * Get reverify value from hidden field in modal_patient_sample_test_result_entry.php
                    */
                    if (parseInt($modal_result.find("input#reverify").attr('reverify')) === 1) {
                        $(this).find("td input.result").prop("disabled", false);
                        $(this).find("td input.test_date").prop("disabled", false);
                        $(this).find("td .performer").prop("disabled", false);
                        $(this).find(":checkbox.test-visibility").prop("disabled", false);
                    }
                });

                //History tollstip
                var keys = Object.keys(resText.results);
                for (var i = keys.length - 1; i >= 0; i--){
                    $test_list.find("tr.test").each(function() {
                        if ($(this).find("span.t-name").attr('patient_test_id') === resText.results[keys[i]].patient_test_id && resText.results[keys[i]].number_update > 0) {
                            $(this).find("span.t-name").css({'color':'blue', 'cursor':'pointer'});
                            $(this).find("span.t-name").qtip({
                                show: 'click',
                                hide: 'unfocus',
                                content: {
                                    text: function(event, api) {
                                        $.ajax({
                                            url: base_url + 'history/result',
                                            type: 'POST',
                                            dataType: 'json',
                                            data: {
                                                patient_test_id: $(this).attr('patient_test_id')
                                            }
                                        })
                                        .then(function(data) {
                                            var content = "<table class='table table-bordered table-striped' style='width:100%; !important'><thead><tr><th>User</th><th>From</th><th>To</th><th>Qty</th><th>Date</th></thead><tbody>";
                                            $.each(data, function(index, result) {
                                                content+= "<tr><td>" + result.fullname + "</td><td>" + result.from_result + "</td><td>" + result.to_result + "</td><td>" + result.quantity + "</td><td>" + result.modified_date + "</td></tr>";
                                            });
                                            content+= "</tbody></table>";
                                            api.set('content.text', content);
                                        }, function(xhr, status, error) {
                                            api.set('content.text', status + ': ' + error);   
                                        });
                                        return 'Loading...';
                                    }
                                }
                            });
                        }
                    });
                }

                //Event
                $test_list.find("td input.test_date").on("dp.change", function() {
                    lastTestDate = $(this).data("DateTimePicker").date();
                });
                $test_list.find("td select.performer").on("change", function(evt) {
                    lastPerformer = $(this).val();
                });
                $test_list.find("td input.result").on("change", function (evt) {
                    var inputValue = $(this).val().trim();
                    console.log("onChange --------------");
                    console.log(inputValue);

                    var $parent    = $(this).closest("tr.test");

                    if ($parent.length > 0) {
                        if (lastTestDate) $parent.find("input.test_date").data("DateTimePicker").setDate(inputValue.length > 0 ? lastTestDate : null);
                        if (lastPerformer > 0) $parent.find("select.performer").val(inputValue.length > 0 ? lastPerformer : -1).trigger("change");
                    }
                });
                $test_list.find("tr[data-field-type=5]").each(function() {
                    var $tr = $(this);
                    var formula = $(this).data("formula");
                    var sample_test_id = $(this).data("sample-test-id");
                    if (formula) {
                        //TODO bind event
                        var parser = new exprEval.Parser();
                        var expr   = parser.parse(formula);
                        var variables = expr.variables();
                        for(var i in variables) {
                            if (CAMLIS_VARIABLE_FORMAT.TEST.test(variables[i])) {
                                var id = "#" + variables[i].replace('T', 'test');
                                $test_list.find("input" + id).on("blur", function() {
                                    console.log("Blue----------: input"+id);
                                    var result = calculateTestValue(formula);
                                    console.log("Result :"+result);
                                    if(isFloat(result)) result = Number(result).toFixed(1); // added 23 Jan 2021
                                    $test_list.find("input#test" + sample_test_id).val(result);

                                    $tr.find("input.test_date").data("DateTimePicker").setDate(lastTestDate);
									$tr.find("select.performer").val(lastPerformer).trigger("change");
                                });
                            }

                            CAMLIS_VARIABLE_FORMAT.TEST.lastIndex = 0;
                        }
                    }
                });
                /*
                * Find the H.I.V antibody
                * Antibody is data-field-type = 5
                * Set it's id to antibody
                */
               $test_list.find("tr[data-field-type=5]").each(function() {
                /*
                * Get data-sample-test-id from the selector
                * and the test-name in it's td
                * 125 = H.I.V antibody
                */
                if($(this).attr('data-sample-test-id') === "125" || $(this).find("td .t-name").html() === "H.I.V antibody"){
                    /*change id to antibody*/
                    $(this).find("td input.result").attr('id', 'antibody');
                    /*disable text field*/
                    $(this).find("td input.result").prop("disabled", true);
                    /*check if the result is number then set it value to empty*/
                    if($.isNumeric($(this).find("td input.result").val())){
                        $(this).find("td input.result").val("");
                    }
                    /*Find the text field test_date of antibody and set it's id*/
                    $(this).find("td input.test_date").attr('id', 'test_date_antibody');
                    /*Find the performer of antibody and set it's id*/
                    $(this).find("td .performer").attr('id', 'performer_antibody');
                }
            });
            /*
            * Find and set id for H.I.V antibody (Determine), 
            * H.I.V antibody (START‐PAK) and H.I.V antibody (UNIGOLD)
            */
            $test_list.find("tr[data-field-type=1]").each(function() {
                /*
                * Get data-sample-test-id from the selector
                * and the test-name in it's td
                * 126 = H.I.V antibody (Determine)
                */
                if($(this).attr('data-sample-test-id') === "126" || $(this).find("td .t-name").html() === "H.I.V antibody (Determine)"){
                    /*Set id to determine*/
                    $(this).find("td input.result").attr('id', 'determine');
                    /*Set id to test-visibility_determine*/
                    $(this).find("td input.test-visibility").attr('id', 'test-visibility_determine');
                    /*Set id to icheckbox_minimal_determine*/
                    $(this).find("td .icheckbox_minimal").attr('id', 'icheckbox_minimal_determine');
                    /*
                    * Set id to test_date_determine and create event
                    * Set set date to the test_date_antibody 
                    * when the user choose date in test_date_determine 
                    */
                    $(this).find("td input.test_date").attr('id', 'test_date_determine').on('dp.change', function(event) {
                        event.preventDefault();
                        $('#test_date_antibody').val($(this).val()).trigger('change');
                    });
                    /*
                    * Set id to performer_determine and create event
                    * Set set date to the performer_antibody 
                    * when the user choose performer in performer_determine 
                    */
                    $(this).find("td .performer").attr('id', 'performer_determine').on('change', function(event) {
                        event.preventDefault();
                        $('#performer_antibody').val($(this).val()).change();
                    });
                    /*Check for value None Reactiv or Reactive when result change in textbox*/
                    $(this).find("td input.result").on('change', function(event) {
                        event.preventDefault();
                        /*Trigger the event change of test-visibility_determine*/
                        $('#test-visibility_determine').prop('checked', true).change();
                        /*Check the checkbox*/
                        $('#icheckbox_minimal_determine').attr('class', 'icheckbox_minimal checked');
                        /*
                        * Check value when the user choose result
                        */
                        if($(this).val() === "Non Reactive"){
                            /*set value to antibody*/
                            $('#antibody').val("Negative");
                            /*Disable start_pak*/
                            $('#start_pak').prop("disabled", true);
                            /*Set empty to start_pak*/
                            $('#start_pak').val("");
                            /*Disable unigold*/
                            $('#unigold').prop("disabled", true);
                            /*Set empty value to unigold*/
                            $('#unigold').val("");
                        }else{
                            /*Set empty string to antibody*/
                            $('#antibody').val("");
                        }
                        // check test date value for require
                        if ($('#test_date_determine').val() === "") {
                            // set test date border to red
                            $('#test_date_determine').css('border-color', 'red');
                            // bring coursor to test date
                            $('#test_date_determine').focus();
                        }
                    });
                }
                /*
                * H.I.V antibody (START‐PAK)
                * 127 = H.I.V antibody (START‐PAK)
                */
                if($(this).attr('data-sample-test-id') === "127" || $(this).find("td .t-name").html() === "H.I.V antibody (START‐PAK)"){
                    /*Set id to start_pak*/
                    $(this).find("td input.result").attr('id', 'start_pak');
                    /*Set id to test-visibility_start_pak*/
                    $(this).find("td input.test-visibility").attr('id', 'test-visibility_start_pak');
                    /*Set id to icheckbox_minimal_start_pak*/
                    $(this).find("td .icheckbox_minimal").attr('id', 'icheckbox_minimal_start_pak');
                    /*Trigger change event*/
                    $(this).find("td input.result").on('change', function(event) {
                        event.preventDefault();
                        /*Trigger event change*/
                        $('#test-visibility_start_pak').prop('checked', true).change();
                        /*check the the checkbox*/
                        $('#icheckbox_minimal_start_pak').attr('class', 'icheckbox_minimal checked');
                    });
                }
                /*
                * H.I.V antibody (UNIGOLD)
                * 128 = H.I.V antibody (UNIGOLD)
                */
                if($(this).attr('data-sample-test-id') === "128" || $(this).find("td .t-name").html() === "H.I.V antibody (UNIGOLD)"){
                    /*Set id to unigold*/
                    $(this).find("td input.result").attr('id', 'unigold');
                    /*Set id to test-visibility_unigold*/
                    $(this).find("td input.test-visibility").attr('id', 'test-visibility_unigold');
                    /*Set id to icheckbox_minimal_unigold*/
                    $(this).find("td .icheckbox_minimal").attr('id', 'icheckbox_minimal_unigold');
                    /*Event change*/
                    $(this).find("td input.test_date").attr('id', 'test_date_unigold').on('dp.change', function(event) {
                        event.preventDefault();
                        /*Set value to test_date_antibody*/
                        $('#test_date_antibody').val($(this).val()).trigger('change');
                    });
                    /*Event change*/
                    $(this).find("td .performer").attr('id', 'performer_unigold').on('change', function(event) {
                        event.preventDefault();
                        /*Set value to performer_antibody*/
                        $('#performer_antibody').val($(this).val()).change();
                    });
                    /*Event change*/
                    $(this).find("td input.result").on('change', function(event) {
                        event.preventDefault();
                        /*Trigger change event*/
                        $('#test-visibility_unigold').prop('checked', true).change();
                        /*check the checkbox*/
                        $('#icheckbox_minimal_unigold').attr('class', 'icheckbox_minimal checked');
                        /*check for result condition */
                        if($(this).val() === "Reactive" && $('#start_pak').val() === "Reactive"){
                            /*Set value for antibody*/
                            $('#antibody').val("Positive");
                        }else{
                             /*Set value for antibody*/
                            $('#antibody').val("");
                        }
                        // check test date value for require
                        if ($('#test_date_unigold').val() === "") {
                            // set test date border to red
                            $('#test_date_unigold').css('border-color', 'red');
                            // bring coursor to test date
                            $('#test_date_unigold').focus();
                        }
                    });
                }
            });
                $('#patient_sample_id').val(data.patient_sample_id);
                $modal_result.data("sampleForm", sampleForm);
                $modal_result.data("patient_sample_id", data.patient_sample_id);
                $modal_result.modal({ backdrop : 'static' });
            },
            error : function () {
                myDialog.showDialog('show', { text	: globalMessage.error, style: 'warning' });
                myDialog.showProgress('hide');
            }
        });
    });

	/* ============================================================================================================*/
    /**
     * Move to next result input when Enter key is pressed
     */
    $modal_result.on("keyup", "input.result", function (evt) {
        if (evt.which === 13 || evt.which === 38 || evt.which === 40) {
            var order = parseInt($(this).attr("order"));
                order = evt.which === 13 || evt.which === 40 ? order + 1 : order - 1;
            if (order > 0) {
                $modal_result.find("input.result[order="+ order +"]").focus();
            }
        }
    });

    $modal_result.on("focus", "input.result", function (evt) {
        $(this).select();
    });

    /**
     * Reset value Modal result
     */
    $modal_result.on("hidden.bs.modal", function () {
        $("#department_result_view_optional").val("");
        $("#sample_result_view_optional").val("");
    });

    /* ============================================================================================================*/

    /**
     * Save Result
     */
    var comment = [];
    $modal_result.find(".btnSaveResult").on("click", function (evt, callback) {
        
        evt.preventDefault();
        var data		  = [];
        var patient_tests = [];
        var is_valid	  = true;
        var $test_list	  = $modal_result.find("#tb_test_result tbody tr.test");
        var $comment_list = $modal_result.find("#tb_test_result").find("tr.comment");

        $test_list.each(function () {
            var $inputResult	= $(this).find("td input.result");
            var $inputTestDate  = $(this).find("td input.test_date");
            var $inputPerformer = $(this).find("td select.performer");

            var patient_test_id	= Number($(this).data("patient-test-id"));
            var sample_test_id	= $(this).data("sample-test-id");
            var field_type		= Number($(this).data("field-type"));
            var test_date		= $inputTestDate.length > 0 ? $inputTestDate.data("DateTimePicker").date() : null;
            var performer		= $inputPerformer.length > 0 ? $inputPerformer.val() : -1;
            var is_rejected		= $(this).data("rejected");
            var ref_range       = $(this).find("td.ref-range").data("ref-range");
            var show_in_result  = $(this).find(":checkbox.test-visibility").is(":checked") ? 0 : 1;

            if (patient_test_id > 0) {
                patient_tests.push({
                    patient_test_id: patient_test_id,
                    is_show: show_in_result
                });
            }

            if (is_rejected == 1 || (field_type > 0 && $inputResult.length > 0 && $inputResult.val().trim().length == 0) || (field_type == 0 && (test_date == null || performer <= 0))) return true;

            //require test date
            if ($inputResult.length > 0 && $inputResult.val().trim().length > 0 && (test_date == null || performer <= 0)) {
                $(this).addClass("error_border");
                is_valid = false;
            } else {
                $(this).removeClass("error_border");
            }

            //Prepare data
            var row_data = {
                patient_test_id	: patient_test_id,
                sample_test_id	: sample_test_id,
                field_type		: field_type,
                test_date		: test_date == null ? "" : test_date.format('YYYY-MM-DD'),
                performer_id	: performer,
                result			: null
            };
            if (!isNaN(field_type) && [1, 2].indexOf(field_type) > -1) { //Single or Multiple Value
                row_data.result = $inputResult.data("possible_result");
            } else {
                row_data.result = $inputResult.val();
                row_data.unit_sign = $(this).find("td.unit-sign").text() || null;                
                if (ref_range) {
                    row_data.ref_range_min_value = ref_range.min_value;
                    row_data.ref_range_max_value = ref_range.max_value;
                    row_data.ref_range_sign      = ref_range.range_sign;
                }
            }
            data.push(row_data);
        });

        if (!is_valid) {
            myDialog.showDialog('show', { text	: msg_required_data, style	: 'warning' });
            return false;
        }

        var patient_sample_id	= Number($modal_result.data("patient_sample_id"));
        if (isNaN(patient_sample_id) || patient_sample_id <= 0) return false;

        var result_comment = [];
        $comment_list.each(function () {
            var department_sample_id = $(this).data("department-sample-id");
            var comment = $(this).find("textarea[name=result_comment]").val() || '';
            if (department_sample_id > 0 && comment.length > 0) {
                result_comment.push({
                    patient_sample_id    : patient_sample_id,
                    department_sample_id : department_sample_id,
                    result_comment       : comment
                });
            }
        });

        //Show Progress
        myDialog.showProgress('show', { text : msg_loading });

        $.ajax({
            url		: base_url + "result/save",
            type	: 'POST',
            data	: { results : data, patient_sample_id : patient_sample_id , result_comment : result_comment, patient_tests: patient_tests },
            dataType: 'json',
            success	: function (resText) {
               
                var sampleForm = $modal_result.data("sampleForm");
                myDialog.showProgress('hide');
                myDialog.showDialog('show', {
                    text	 : resText.msg,
                    style	 : resText.status == true ? 'success' : 'warning',
                    onHidden : function () {
                        if (callback) callback();
                    }
                });

                if (resText.status && sampleForm && sampleForm instanceof SampleForm) {
                    //Set Sample Users
                    var sample_entry_user  = resText.data.users.sample_entry_user != null ? resText.data.users.sample_entry_user : "";
                    var result_entry_user  = resText.data.users.result_entry_user != null ? resText.data.users.result_entry_user : "";
                    var sample_entry_users = sample_entry_user.split(",");
                    var result_entry_users = result_entry_user.split(",");
                    if (sample_entry_user.length > 0 && sample_entry_users.length > 0) {
                        var $sample_user_list = sampleForm.form.find(".sample-entry-user-list");
                        $sample_user_list.find(".no-result, .user").remove();
                        for (var i in sample_entry_users) {
                            var $item = $sample_user_list.find(".sample-entry-user.template").clone();
                            $item.removeClass("template").removeClass("hide").addClass("user");
                            $item.text(sample_entry_users[i]);
                            $sample_user_list.append($item);
                        }
                    }
                    if (result_entry_user.length > 0 && result_entry_users.length > 0) {
                        var $result_user_list = sampleForm.form.find(".result-entry-user-list");
                        $result_user_list.find(".no-result, .user").remove();
                        for (var i in result_entry_users) {
                            var $item = $result_user_list.find(".result-entry-user.template").clone();
                            $item.removeClass("template").removeClass("hide").addClass("user");
                            $item.text(result_entry_users[i]);
                            $result_user_list.append($item);
                        }
                    }
                }
            },
            error : function () {
                myDialog.showProgress('hide');
                myDialog.showDialog('show', { text : msg_save_fail, style : 'warning' });
            }
        });
    });

    /**
     * Save Result and Preview result
     */
    $modal_result.on("click", "button.save-preview-result", function (evt) {
        evt.preventDefault();

        var sampleForm = $modal_result.data("sampleForm");
        $modal_result.find(".btnSaveResult").trigger("click", function () {
            if (sampleForm) sampleForm.btnPreview.trigger("click");            
        });
    });

    /**
     * Save Result and Open assigned test modal
     */
    $modal_result.on("click", "button.assign-test", function (evt) {
        evt.preventDefault();

        var sampleForm = $modal_result.data("sampleForm");
        $modal_result.find(".btnSaveResult").trigger("click", function () {
            $modal_result.modal("hide");
            if (sampleForm) sampleForm.btnShowTestModal.trigger("click");
        });
    });

    /* ========================================================================================================= */

    /**
     * Select Comment
     */
    $modal_result.on("click", "#btn_select_cmt", function (evt) {
        evt.preventDefault();

        if($('#department_result_view_optional').val()=='' || $('#department_result_view_optional').val()== null){
            alert('Please selected department one or more.');
            return false;
        }

        var $tbl_cmt_list = $("#tb_cmt_list");

        //show progress
        myDialog.showProgress('show', { text : msg_loading });

        var patient_sample_id	= Number($modal_result.data("patient_sample_id"));
        if (isNaN(patient_sample_id) || patient_sample_id <= 0) {
            myDialog.showProgress('hide');
            return false;
        }
        $tbl_cmt_list.removeData("selected_comment");
        var input_comment	= $modal_result.find("#result_cmt").val();
        input_comment		= input_comment.split(/(?:\r\n|\r|\n)/g);


        var tb_cmt_list	= $tbl_cmt_list.DataTable({
            destroy		: true,
            autoWidth	: false,
            info		: false,
            processing	: true,
            serverSide	: true,
            ajax		: {
                url	 : base_url + 'sample/view_std_sample_comment',
                type : 'POST',
                data : function (d) {
                    d.patient_sample_id = patient_sample_id;
                    d.dep_result_opt = $('#department_result_view_optional').val();
                    d.sam_result_opt = $('#sample_result_view_optional').val();
                }
            },
            columns		: [ { "data": "checkbox" }, { "data": "comment" } ],
            columnDefs	: [
                { targets : '_all', className : 'text-middle' }
            ],
            order		: [[1, 'asc']],
            language	: dataTableOption.language,
            createdRow	: function(row, data, dataIndex) {
                $(row).find(":checkbox").iCheck({ checkboxClass: 'icheckbox_minimal', radioClass: 'iradio_minimal' });
                $(row).find(":checkbox").on("ifChanged", function () {
                    var val = $(this).val();
                    var comment = data.comment;
                    var selected_comment = $tbl_cmt_list.data("selected_comment");
                    if (selected_comment == undefined || !(selected_comment instanceof Object)) selected_comment = {};
                    if ($(this).is(":checked")) {
                        selected_comment["comment_" + val] = comment;
                    } else {
                        delete(selected_comment["comment_" + val]);
                    }

                    $tbl_cmt_list.data("selected_comment", selected_comment);
                });

                //set input comment
				/*var comment = data.comment;
				 if (Array.isArray(input_comment) && input_comment.indexOf(comment) > -1) {
				 $(row).find(":checkbox").iCheck("check");
				 }*/

                //check previous comment
                var selected_comment = $tbl_cmt_list.data("selected_comment");
                if (selected_comment) {
                    for(var key in selected_comment) {
                        var arr = key.split('_');
                        if (arr[1] != undefined) {
                            $(row).find(":checkbox[value="+arr[1]+"]").iCheck("check");
                        }
                    }
                }
            },
            drawCallback: function (settings) {
                myDialog.showProgress('hide');

                $("#modal_comment").modal({ backdrop: 'static' });
            }
        });

    });

    /**
     * Enter new line when focus on Comment Box
     */
    $modal_result.on("focus", "#result_cmt", function (evt) {
        evt.preventDefault();

        var old_val	= $(this).val().trim();
        var patt	= new RegExp("[\r\n]$");

        if (!patt.test(old_val) && old_val.length > 0) {
            old_val += "\r\n";
            $(this).val(old_val);
        }
    });

    /**
     * Add selected comment to Comment textarea
     */
    $("#btnAddComment").on("click", function (evt) {
        evt.preventDefault();

        var department_sample_id = $("#modal_comment").data("department_sample_id");
        var cmt		= $modal_result.find("#result-comment-" + department_sample_id);
        var old_val	= cmt.val().trim();
        var patt	= new RegExp("[\r\n]$");

        if (!patt.test(old_val) && old_val.length > 0) {
            old_val += "\r\n";
        }

        var result = [];
        var selected_comment = $("#tb_cmt_list").data("selected_comment");
        if (selected_comment) {
            for(var i in selected_comment) {
                result.push(selected_comment[i]);
            }
        }

        cmt.val(old_val + result.join('\r\n') + "\r\n");
        $("#modal_comment").modal("hide");
    });

	/* ========================================================================================================= */

    /**
     * Preview Result
     */
    $sample_forms.on("click", ".btnPreview", function (evt) {
        evt.preventDefault();

        var form		= $(this).parents("form.frm-sample-entry");
        var sampleForm	= new SampleForm(form);
        var data		= sampleForm.get_data();
        var is_enabled	= sampleForm.btnPreview.data("enabled");
        if (is_enabled != 1 || isNaN(Number(data.patient_sample_id)) || Number(data.patient_sample_id) <= 0) {
            $(this).prop("disabled", true);
            return false;
        }

        $print_preview_modal.find(".modal-dialog").empty();
        $print_preview_modal.data("patient_sample_id", data.patient_sample_id);
        $print_preview_modal.find("#doPrinting").off("click").on("click", function (evt) {
            evt.preventDefault();

            var _dep_view = $('#department_result_view_optional').val();
            var _sm_view = $('#sample_result_view_optional').val();

            if (_dep_view === undefined || _dep_view === null) {
                _dep_view ='';
                _sm_view = '';
            }

            printpage(base_url + "result/patient_sample_result/print/" + data.patient_sample_id + "/"+encodeURIComponent(_dep_view)+"/"+encodeURIComponent(_sm_view));
            $.post(base_url + "patient_sample/update_printed_info", { patient_sample_id: data.patient_sample_id });
        });

        //Show Progress
        myDialog.showProgress('show', { text : msg_loading });
       
        $.ajax({
            url: base_url + "result/patient_sample_result/preview/" + data.patient_sample_id,
            type: 'POST',
            dataType: 'json',
            data: {
                department_optional_view: $('#department_result_view_optional').val(),
                sample_optional_view: $('#sample_result_view_optional').val()
            },
            success: function (resText) {                
                for (var i in resText) {
                    var $page = $("<div class='psample-result'></div>");
                    $page.attr("id", "presult-" + (parseInt(i) + 1));
                    $page.data("patient_sample_id", resText[i].patient_sample_id);
                    $page.html(resText[i].template);
                    if (i == 0) $page.addClass("active");
                    else $page.hide();

                    $print_preview_modal.find(".modal-dialog").append($page);
                }

                $print_preview_modal.find(".page-count").text(resText.length);
                $print_preview_modal.find(".page-number").val(1);
                $print_preview_modal.find(".page-number").autoNumeric({aPad: 0, vMin: 1, vMax: resText.length});
                /*
                * Check verify button
                * @desc check for verify the result
                * 0 : No result input
                * 1 : Input some result
                * 2 : all result are complete input
                */
               if (parseInt($print_preview_modal.find('#verify').val()) === 0) {
                    /*Set print label to variable print button*/
                    print_button = $print_preview_modal.find('li.print a').html();
                    /*Remove ther print button*/
                    $print_preview_modal.find('li.print').remove();
                }
                /*
                * Check for the complete result
                */
                if ($print_preview_modal.find('#verify').val() === '2') {
                    /*
                    * @desc: check for the reverify permission
                    * 0 : no permission
                    * 1 : have permssion
                    */
                    if (!$print_preview_modal.find('li.approve').attr('reverify')) {
                        // Remove the approve button
                        $print_preview_modal.find('li.approve').remove();
                    }
                }
                setTimeout(function () {
                    myDialog.showProgress('hide');
                    $print_preview_modal.modal("show");
                }, 400);
            },
            error: function () {
                myDialog.showProgress('hide');
                $print_preview_modal.modal("show");
                $print_preview_modal.find(".modal-dialog").empty();
            }
        });
    });
    /**
     * Covid qestionaire
     * Added: 09-Jan-2021
     */
    $sample_forms.on("click", ".btnPreviewCovidForm", function (evt) {
        evt.preventDefault();
        
        var form		= $(this).parents("form.frm-sample-entry");
        var sampleForm	= new SampleForm(form);
        var data		= sampleForm.get_data();
       
        $print_preview_modal.find(".modal-dialog").empty();
        $print_preview_modal.data("patient_sample_id", data.patient_sample_id);
        $print_preview_modal.find("#doPrinting").off("click").on("click", function (evt) {
            evt.preventDefault();

            var _dep_view = $('#department_result_view_optional').val();
            var _sm_view = $('#sample_result_view_optional').val();

            if (_dep_view === undefined || _dep_view === null) {
                _dep_view ='';
                _sm_view = '';
            }

            printpage(base_url + "result/patient_covid_form/print/" + data.patient_sample_id + "/"+encodeURIComponent(_dep_view)+"/"+encodeURIComponent(_sm_view));
        //    $.post(base_url + "patient_sample/update_printed_info", { patient_sample_id: data.patient_sample_id });
        });

        //Show Progress
        myDialog.showProgress('show', { text : msg_loading });
        
        $.ajax({
            url: base_url + "result/patient_covid_form/preview/" + data.patient_sample_id,
            type: 'POST',
            dataType: 'json',
            data: {
                department_optional_view: $('#department_result_view_optional').val(),
                sample_optional_view: $('#sample_result_view_optional').val()
            },
            success: function (resText) {                
                for (var i in resText) {
                    var $page = $("<div class='psample-result'></div>");
                    $page.attr("id", "presult-" + (parseInt(i) + 1));
                    $page.data("patient_sample_id", resText[i].patient_sample_id);
                    $page.html(resText[i].template);
                    if (i == 0) $page.addClass("active");
                    else $page.hide();

                    $print_preview_modal.find(".modal-dialog").append($page);
                }

                $print_preview_modal.find(".page-count").text(resText.length);
                $print_preview_modal.find(".page-number").val(1);
                $print_preview_modal.find(".page-number").autoNumeric({aPad: 0, vMin: 1, vMax: resText.length});

                setTimeout(function () {
                    myDialog.showProgress('hide');
                    $print_preview_modal.modal("show");
                }, 400);
            },
            error: function () {
                myDialog.showProgress('hide');
                $print_preview_modal.modal("show");
                $print_preview_modal.find(".modal-dialog").empty();
            }
        });
    });
    /** END COVID Questionaire */
    $print_preview_modal.on("hidden.bs.modal", function () {
        $print_preview_modal.find(".modal-dialog").empty();
    });

	/*$modal_result.on("click", ".btnPreview", function (evt) {
	 evt.preventDefault();
	 var sampleForm = $modal_result.data("sampleForm");
	 if (sampleForm && sampleForm instanceof SampleForm) {
	 sampleForm.btnPreview.trigger("click");
	 }
	 });*/
	/* ========================================================================================================= */

});


/* ========================================================================================================= */

/**
 * Show Possible Result (Organism)
 * @param {object} Me Control that trigger the event as DOM
 */
function showPossibleResult(input) {
    $(input).blur();

    var $possible_result_modal = $("#possible_result_modal");
    var sample_test_id	= $(input).parents("tr").data("sample-test-id");
    var field_type		= $(input).parents("tr").data("field-type");
    var patient_test_id	= $(input).parents("tr").data("patient-test-id");
    var test_name       = $(input).closest("tr.test").find("span.t-name").html();

    //show progress
    myDialog.showProgress('show', { text : msg_loading });

    $possible_result_modal.find(".modal-title").find("span.test-name").html(test_name);
    $possible_result_modal.find("input[type=search]").val('');
    $possible_result_modal.modal({ backdrop: 'static' });

    //Clear Datable on Hide
    $possible_result_modal.off("hide.bs.modal").on("hide.bs.modal", function() {
        init_AntibioticTable().clear().draw();
        init_OrganismTable().clear().draw();
    });

    //Previous result
    var prev_result		= $(input).data("possible_result");
    var prev_organism	= {};
    var prev_antibiotic	= {};
    for (var i in prev_result) {
        prev_organism['org_' + prev_result[i].test_organism_id ] = { test_organism_id : prev_result[i].test_organism_id, quantity_id : prev_result[i].quantity_id };

        if (prev_result[i].antibiotic != undefined) {
            prev_antibiotic["anti_" + prev_result[i].test_organism_id] = prev_result[i].antibiotic;
        }
    }

    setTimeout(function() {
        init_AntibioticTable().draw();
        init_OrganismTable().draw();

        $.ajax({
            url		: base_url + 'organism/get_sample_test_organism',
            type	: 'POST',
            data	: { sample_test_id: sample_test_id },
            dataType: 'json',
            success	: function (resText) {
                //Quantity
                var qty  = "<select class='form-control organism_qty'><option value='-1'>&nbsp;</option>" + $possible_result_modal.find("#organism-qty-list").html() + "</select>";

                var data = [];
                for (var i in resText) {
                    var orgName = "<input type='" + (field_type == 1 ? "radio" : "checkbox") + "' class='test_organism' name='test_organism[]' value='" + resText[i].test_organism_id + "' data-name='" + resText[i].organism_name + "'>&nbsp;&nbsp;<span style='cursor:pointer;' class='data-name'>" + resText[i].organism_name + "</span>";

                    data.push({
                        DT_RowData		: { test_organism_id : resText[i].test_organism_id },
                        DT_RowId		: 'org_' + resText[i].test_organism_id,
                        organism_name	: orgName,
                        quantity		: qty
                    });
                }

                init_OrganismTable({
                    sample_test_id	: sample_test_id,
                    prev_organism	: prev_organism,
                    prev_antibiotic	: prev_antibiotic,
                    input_type		: field_type == 1 ? "radio" : "checkbox"
                }).rows.add(data).draw();

                myDialog.showProgress('hide');
            }
        });
    }, 200);

    //Assign Selected Organism
    $possible_result_modal.find(".modal-footer #btnAddOrganism").off("click").on("click", function (evt) {
        evt.preventDefault();
        $possible_result_modal.find("#organism_list tbody tr:eq(0)").trigger("click", [false, "update"]);
        var $organism_list	= $possible_result_modal.find("#organism_list tbody");
        var result			= [];
        var resultTxt		= [];
        $organism_list.find("tr").each(function () {
            if ($(this).find("td input.test_organism").is(":checked")) {
                var val			= $(this).find("td input.test_organism:checked").val();
                var txt			= $(this).find("td input.test_organism:checked").attr('data-name');
                var resQty		= $(this).find("td select.organism_qty").val();
                var antibiotic	= $(this).data("antibiotic_result");

                result.push({
                    test_organism_id	: val,
                    quantity_id			: resQty,
                    antibiotic			: antibiotic
                });
                resultTxt.push(txt.trim());
            }
        });

        $(input).data("possible_result", result);
        $(input).val(resultTxt.join(', '));
        $(input).trigger("change");
        $possible_result_modal.modal("hide");
    });

}

/**
 * Get Antibiotic of Organism
 * @param {number} test_organism_id Sample test organismID
 * @param {Array} antibiotic_result Previous selected antibiotic
 */
function getAntibiotic(test_organism_id, antibiotic_result) {
    test_organism_id = Number(test_organism_id) <= 0 ? 0 : test_organism_id;
    var $possible_result_modal = $("#possible_result_modal");

    //Show progress
    myDialog.showProgress('show', { text : msg_loading, appendTo : $possible_result_modal.find(".modal-body"), size : '1x' });

    var $organism_row = $possible_result_modal.find("#antibiotic_list").data("organism_row");
    var antibiotic_list	= $organism_row ? $organism_row.data("antibiotic_list") : undefined;

    var setData = function(data) {
        setTimeout(function() {
            myDialog.showProgress('hide');
        }, 400);

        if ($organism_row) $organism_row.data("antibiotic_list", data);

        //Clear table
        init_AntibioticTable().clear().draw();

        var sensitivityHTML = "<select class='form-control sensitivity' style='width:120px !important;'>";
        sensitivityHTML += "<option value='-1'></option>";
        sensitivityHTML += "<option value='1'>Sensitive</option>";
        sensitivityHTML += "<option value='2'>Resistant</option>";
        sensitivityHTML += "<option value='3'>Intermidiate</option>";
        sensitivityHTML += "</select>";

        if (data.length > 0) {
            var dtData = [];
            for(var i in data) {
                if (Number(data[i].antibiotic_id) <= 0) continue;
                dtData.push({
                    DT_RowData		: { antibiotic_id : data[i].antibiotic_id },
                    DT_RowId		: 'anti_' + data[i].antibiotic_id,
                    antibiotic_name	: data[i].antibiotic_name,
                    sensitivity		: sensitivityHTML,
                    test_zone		: "<input type='text' class='form-control test_zone' size='3'>",
                    invisible		: "<input type='checkbox' class='form-control invisible-antibiotic' value='1'>"
                });
            }

            init_AntibioticTable().rows.add(dtData).draw();

            //set previous selected antibiotic
            $list = $possible_result_modal.find("#antibiotic_list tbody");
            for (var i in antibiotic_result) {
                $tr = $list.find("tr[id=anti_" + antibiotic_result[i].antibiotic_id + "]");
                if ($tr.length > 0) {
                    $tr.find("td select.sensitivity").val(antibiotic_result[i].sensitivity)
                    $tr.find("td input.test_zone").val(antibiotic_result[i].test_zone);
                    $tr.find("td :checkbox.invisible-antibiotic").iCheck(antibiotic_result[i].invisible > 0 ? 'check' : 'uncheck');
                }
            }
        } else {
            $("#antibiotic_list").removeData("test_organism_id");
        }
    };

    if (antibiotic_list == undefined) {
        //todo abort ajax on hide modal if it's active
        $.ajax({
            url		: base_url + "antibiotic/get_std_sample_test_organism_antibiotic",
            type	: 'POST',
            data	: { test_organism_id: test_organism_id },
            dataType: 'json',
            success	: function (resText) {
                setData(resText);
            },
            error : function() {
                myDialog.showProgress('hide');
            }
        });
    } else {
        setData(antibiotic_list);
    }
}

/**
 * Get Selected Antibiotic
 * @returns {Array} List of Selected Antibiotic
 */
function getSelectedAntibiotic() {
    $list			= $("#antibiotic_list").find("tbody");
    var selected	= [];

    $list.find("tr").each(function () {
        var antibiotic_id	= $(this).data("antibiotic_id");
        var sensitivity		= $(this).find("td select.sensitivity").val();
        var test_zone		= $(this).find("td input.test_zone").val();
        var invisible       = $(this).find(":checkbox.invisible-antibiotic:checked").val() || 0;

        if (sensitivity != -1) {
            selected.push({
                antibiotic_id	: antibiotic_id,
                sensitivity		: sensitivity,
                test_zone		: test_zone,
                invisible       : invisible
            });
        }
    });

    return selected;
}

/**
 * Prepare Test List for Result Entry From
 * @param   {Array} data        Data
 * @param   {number} parent      Test PID
 * @param   {number} level       Level of Child
 * @param   {string} parent_rank Parent Rank Number
 * @returns {Array} Prepared List
 */
function prepareResultEntryList(data, parent, level, parent_rank) {
    var tmp			= [];
    var r			= 1;
    parent_rank	= parent_rank == undefined ? "" : parent_rank;

    for (var i in data) {
        var row 		= data[i];
        row.level 	= level;

        //if (parseInt(row.child_count) > 0) {
        if (row.children != undefined && row.children.length > 0) {
            var childs 		= row.children;
            row.testName	= parent_rank + (r++) + ". " + row.testName;
            delete row.children;
            tmp.push(row);

            var t = prepareResultEntryList(childs, row.testID, level + 1, (r - 1) + ". ");
            for(var j in t) {
                tmp.push(t[j]);
            }
        } else {
            row.testName = parent_rank + (r++) + ". " + row.testName;
            tmp.push(row);
        }
    }

    return tmp;
}

/**
 * Initial Organim Datatable
 * @returns {object} DataTable Object
 */
function init_OrganismTable(data) {
    var $possible_result_modal	= $("#possible_result_modal");
    var $organism_list			= $possible_result_modal.find("table#organism_list");
    var $antibiotic_list		= $possible_result_modal.find("#antibiotic_list");

    data = $.extend({
        sample_test_id	: -1,
        input_type		: 'checkbox',
        prev_organism	: {},
        prev_antibiotic	: {}
    }, data);

    $organism_list.data("sample_test_id", data.sample_test_id);
    $organism_list.data("input_type", data.input_type);
    $organism_list.data("prev_organism", data.prev_organism);
    $organism_list.data("prev_antibiotic", data.prev_antibiotic);

    return $organism_list.DataTable({
        autoWidth		: false,
        language		: dataTableOption.language,
        scrollY			: '50vh',
        scrollCollapse	: true,
        paging			: false,
        info			: false,
        filter			: false,
        retrieve		: true,
        aLengthMenu		: [
            [25, 50, 100, 200, -1],
            [25, 50, 100, 200, "All"]
        ],
        iDisplayLength: -1,
        columns			: [
            { data : "organism_name" },
            { data : "quantity" }
        ],
        columnDefs		: [ {className : 'text-middle pointer', targets : '_all' }, {targets : 1, orderable : false, width : '20%'} ],
        createdRow		: function(row, data, dataIndex) {
            var prev_organism	= $organism_list.data("prev_organism");
            var prev_antibiotic	= $organism_list.data("prev_antibiotic");

            //Set previous organism and qty
            if (prev_organism['org_' + $(row).data("test_organism_id")] != undefined) {
                $(row).find("input.test_organism").prop("checked", true);
                $(row).find("select.organism_qty").val(prev_organism['org_' + $(row).data("test_organism_id")].quantity_id);
            }

            //Set previous antibiotic
            if (prev_antibiotic["anti_" + $(row).data("test_organism_id")] != undefined) {
                $(row).data("antibiotic_result", prev_antibiotic["anti_" + $(row).data("test_organism_id")]);
            }

            //set event for each tr
            $(row).on("click", function (evt, selectRow, action) {
                selectRow	= selectRow == undefined ? false : selectRow;
                var target		= evt.target.tagName.toLowerCase();
                if (target == "select" || target == "option" || $(evt.target).parents(".select2").length > 0) {
                    return false;
                }

                //Set selected antibiotic to current Organism
                var selectedAntibiotic	= getSelectedAntibiotic();
                var $organism_row		= $antibiotic_list.data("organism_row");
                if ($organism_row != undefined) $organism_row.data("antibiotic_result", selectedAntibiotic);

                if (action == "update") return false;

                //set selected background
                if (selectRow) {
                    $(this).removeClass("selected").addClass("selected");
                } else {
                    $(this).toggleClass("selected");
                }
                $(this).siblings("tr").removeClass("selected");

                //Get Antibiotic of current Selected Organism
                if ($(this).find(":checkbox.test_organism, :radio.test_organism").is(":checked") && $(this).hasClass("selected")) {
                    var antibiotic_result	= $(this).data("antibiotic_result"); //last selected antibiotic
                    var test_organism_id	= Number($(this).find("input.test_organism").attr("value"));

                    //Set Current Selected Organism
                    $antibiotic_list.data("organism_row", $(this));

                    if (test_organism_id > 0) {
                        getAntibiotic(test_organism_id, antibiotic_result);
                    }

                } else {
                    $antibiotic_list.removeData("organism_row");
                    init_AntibioticTable().clear().draw();
                }
            });
        },
        drawCallback : function(settings) {
			/*$organism_list.find("select.organism_qty").select2();*/
            $organism_list.find("select.organism_qty").css("width", "100%");
            $organism_list.find("input.test_organism").iCheck({ checkboxClass : 'icheckbox_minimal', radioClass : 'iradio_minimal' })
                .on("ifUnchecked", function () {
                    var row = $(this).closest("tr");
                    row.trigger("click");
                    row.find("td select.organism_qty").val(-1);
                })
                .on("ifChecked", function () {
                    var row = $(this).closest("tr");
                    row.trigger("click", [true]);
                });
        }
    });
}

/**
 * Initial Antibiotic Datatable
 * @returns {object} DataTable Object
 */
function init_AntibioticTable() {
    return $("#possible_result_modal").find("table#antibiotic_list").DataTable({
        autoWidth		: false,
        language		: dataTableOption.language,
        scrollY			: '50vh',
        scrollCollapse	: true,
        paging			: false,
        info			: false,
        filter			: false,
        retrieve		: true,
        columns			: [
            { data : "antibiotic_name" },
            { data : "sensitivity" },
            { data : "test_zone" },
            { data : "invisible" }
        ],
        columnDefs		: [
            {targets : [1, 2, 3], orderable : false},
            {targets : 0, className : 'text-middle'},
            {targets : 1, width : '120px'},
            {targets : 2, width : '80px'},
            {targets : 3, width : '40px', className : 'text-center text-middle'}
        ],
        rowCallback   : function (row) {
            $(row).find(":checkbox.invisible-antibiotic").iCheck({ checkboxClass: 'icheckbox_minimal', radioClass: 'iradio_minimal' });
            $(row).find('.iCheck-helper').css('position', 'relative'); // added 04 Jan 2021
        }
    });
}

/**
 * Search in Table
 * @param {object} me      current input as DOM
 * @param {string} tableID table ID to search data from
 */
function searchTable(me, tableID) {
    var table		= $("#" + tableID);

    if (table.DataTable({retrieve : true}).rows().data().length == 0)
        return false;

    var search_str	= $(me).val();
    var regExp		= new RegExp(search_str, "i");

    table.find("tr#emty-row").remove();

    var found		= 0;
    table.find("tbody tr").each(function() {
        var name = $(this).find("span.data-name").text().trim();

        if (regExp.test(name)) {
            $(this).show();
            found++;
        } else {
            $(this).hide();
        }
    });

    table.DataTable({retrieve : true}).columns.adjust();

    if (found == 0) {
        var cols	= table.find("th").length;

        var tr		= "<tr style='background:#F9F9F9;' id='emty-row'><td class='text-center' colspan='" + cols + "'>" + label_no_data + "</td></tr>";
        table.find("tbody").append(tr);
    }
}

/**
 * Validate Ref. Range
 */
function validateRefRange(THIS, test_id, field_type) {

    var result_value = $(THIS).val();
    var $tr			 = $(THIS).closest("tr.test");
    var ref_range	 = $tr.find("td.ref-range").data("ref-range");
    var wbc = $('#test2').val();
    // checking calculate field type = 5 calculate
    //console.log(result_value+" "+THIS);
    //console.log($(THIS).text);
   // console.log("parseFloat = "+ parseFloat(result_value));
    
    if(field_type==5) {
        var test_v = $('#test'+test_id).val().split('%');
        result_value = ((wbc * test_v[0])/100).toFixed(2);
        console.log("I am field type 5"+ result_value);
    }
    
   // added 25 Jan 2021
   //MCV MCH MCHC
   console.log("test id "+ test_id);
   if(test_id == 5 || test_id == 6 || test_id == 7) {
        result_value	= parseFloat($(THIS).val());
        console.log("Im in 5 6 7 " + test_id  );
   }
   
   //end 

   if (ref_range && (field_type == 3 || field_type == 5)) {
   
   //if (result_value.length > 0) {
        result_value	= parseFloat(Number(result_value));
        console.log('parseFloat(result_value) : '+ result_value);
        if (ref_range) {
            console.log(ref_range);
            if (["-", "≤"].indexOf(ref_range.range_sign) > -1) {
                if (!(result_value >= ref_range.min_value  && result_value <= ref_range.max_value)) $tr.addClass("out-of-range");
                else $tr.removeClass("out-of-range");
            }
            else if (ref_range.range_sign == "<") {
                if (!(result_value >= ref_range.min_value && result_value < ref_range.max_value)) $tr.addClass("out-of-range");
                else $tr.removeClass("out-of-range");
            }
        }
    } else {
        $tr.removeClass("out-of-range");
    }

    // checking calculate field type = 5 calculate
    if(field_type==5 && !$tr.data("formula")) {
        var test_v = $('#test'+test_id).val().split('%');
        var perc = ((wbc * test_v[0])/100).toFixed(2);

        $('#test'+test_id).val('');
        $('#test'+test_id).val(test_v[0]+"%         "+ perc);

    }

}

/**
 * Patient's Sample Form
 * @param form
 * @constructor
 */
function SampleForm(form) {
    var _this				= this;
    var formValidity        = true;
    this.form				= form;
    this.btnShowTestModal 	= this.form.find("button.btnShowTestModal");
    this.btnSaveSample		= this.form.find("button.btnSaveSample");
    this.btnRejectSample	= this.form.find("button.btnRejectSample");
    this.btnAddResult		= this.form.find("button.btnAddResult");
    this.btnPreview			= this.form.find("button.btnPreview");
    this.btnRemoveSample	= this.form.find("button.btnRemove");
    this.btnPreviewCovidForm	= this.form.find("button.btnPreviewCovidForm"); //added 09-Jan-2021
    this.inputSampleNumber	= this.form.find("input[name=sample_number]");
    this.psample_id_field	= this.form.find("input[type=hidden][name=patient_sample_id]");

    this.get_data = function () {
        var data                = {};
        data.sample_number      = this.form.find("input[name=sample_number]").val().trim();
        data.sample_source_id   = Number(this.form.find("select[name=sample_source]").val());
        data.sample_source_title= this.form.find("select[name=sample_source] option:selected").text();
        data.requester_id       = Number(this.form.find("select[name=requester]").val());
        data.collected_date	    = this.form.find("input[name=collected_date]").data('DateTimePicker').date();
        data.received_date	    = this.form.find("input[name=received_date]").data('DateTimePicker').date();
        data.collected_time	    = moment(this.form.find("input[name=collected_time]").val().trim(), 'HH:mm');
        data.received_time	    = moment(this.form.find("input[name=received_time]").val().trim(), 'HH:mm');
        data.is_urgent			= this.form.find("input[type=checkbox][name=is_urgent]").is(":checked") ? 1 : 0;
        //data.for_research		= this.form.find("input[type=checkbox][name=for_research]").is(":checked") ? 1 : 0;
	    data.for_research		= this.form.find("select[name=for_research]").val();
        data.clinical_history	= this.form.find("[name=clinical_history]").val();
        data.payment_type_id    = this.form.find("select[name=payment_type]").val();
        data.collected_date_time= null;
        data.received_date_time = null;

        if (data.collected_date !== null && data.collected_time._i.length > 0) {
            data.collected_date_time = moment(data.collected_date.format('YYYY-MM-DD') + ' ' + data.collected_time.format('HH:mm:ss'), 'YYYY-MM-DD HH:mm:ss');
        }
        if (data.received_date !== null && data.received_time._i.length > 0) {
            data.received_date_time = moment(data.received_date.format('YYYY-MM-DD') + ' ' + data.received_time.format('HH:mm:ss'), 'YYYY-MM-DD HH:mm:ss');
        }

        var admission_date = this.form.find("input[name=admission_date]").data("DateTimePicker").date();
        var admission_time = this.form.find("input[name=admission_time]").val().trim();
        data.admission_date = moment(admission_date);
        data.admission_time = moment(admission_time, "HH:mm");

        //patient info
        var pid_field       = $("div#patient-info-wrapper").find("input[type=hidden]#patient-id");
        var age_field       = $("div#patient-info-wrapper").find("input[type=hidden]#patient-age");
        var sex_field       = $("div#patient-info-wrapper").find("input[type=hidden]#patient-sex");
        data.patient_id     = pid_field == undefined ? "" : pid_field.data("value");
        data.patient_age    = age_field == undefined ? "" : age_field.data("value");
        data.patient_sex    = sex_field == undefined ? "" : sex_field.data("value");

        //patient sample id
        data.patient_sample_id	= this.psample_id_field == undefined ? "" : Number(this.psample_id_field.data("value"));

        // ADDED 03 DEC 2020
        
        data.clinical_symptom               = this.form.find("select[name=clinical_symptom]").val();
        data.phone_number                   = this.form.find("input[name=phone_number]").val().trim();
        data.sample_collector               = this.form.find("input[name=sample_collector]").val().trim();
        data.completed_by                   = this.form.find("input[name=completed_by]").val();
        data.phone_number_sample_collector  = this.form.find("input[name=phone_number_sample_collector]").val();
        // End
        return data;
    };

    //Validate Collected and Received Date/Time
    this.validate_date_time = function () {
        var data			= this.get_data();
        var is_valid		= true;
        var col_timestamp	= data.collected_date_time != null ? data.collected_date_time.toDate().getTime() : 0;
        var rec_timestamp	= data.received_date_time != null ? data.received_date_time.toDate().getTime() : 0;
        if (col_timestamp > 0 && rec_timestamp > 0 && col_timestamp >= rec_timestamp) is_valid = false;

        //Enable Save Sample and Assign Test
        this.btnShowTestModal.prop("disabled", !(is_valid && this.validate_fields() && formValidity));
        this.btnSaveSample.prop("disabled", !(is_valid && this.validate_fields() && formValidity));        
        return is_valid;
    };

    //Check Required Value
    this.validate_fields = function () {
        
        var data		= this.get_data();

        var is_valid	= true;
        if (this.inputSampleNumber.data("required") === "yes" && data.sample_number.length === 0) is_valid = false;
        if (isNaN(data.sample_source_id) || data.sample_source_id <= 0) is_valid = false;
        if (isNaN(data.requester_id) || data.requester_id <= 0) is_valid = false;
        if (isNaN(data.payment_type_id) || data.payment_type_id < 0) is_valid = false;
        if (data.collected_date == null || data.received_date == null) is_valid = false;
        if (data.collected_time._i.length == 0 || data.received_time._i.length == 0) is_valid = false;
        if (data.patient_id == undefined || data.patient_id.toString().trim().length == 0) is_valid = false;
        //Enable Save Sample and Assign Test
        this.btnShowTestModal.prop("disabled", !(is_valid && formValidity));
        this.btnSaveSample.prop("disabled", !(is_valid && formValidity));
        
        return is_valid;
    };

    //Set form validity
    this.setValidity = function(validity) {
        formValidity = validity;
    };

    /**
     * Save Patient Sample
     * @param data
     * @param onSuccess Callback function on success
     * @param onMsgClosed Callback function when message status is closed
     */
    this.save = function (data, onSuccess, onMsgClosed) {
        var sampleForm	= this;
        
        //Validation
        if (!sampleForm.validate_fields()) {
            myDialog.showDialog('show', {text: msg_required_data, style: 'warning'});
            return false;
        }
        if (!sampleForm.validate_date_time()) {
            myDialog.showDialog('show', {text: msg_col_rec_dt_error, style: 'warning'});
            return false;
        }

        data						= $.extend({}, data, this.get_data());
        data.collected_date			= data.collected_date.format('YYYY-MM-DD');
        data.collected_time			= data.collected_time.format('HH:mm:ss');
        data.received_date			= data.received_date.format('YYYY-MM-DD');
        data.received_time			= data.received_time.format('HH:mm:ss');
        data.collected_date_time	= data.collected_date_time.format('YYYY-MM-DD HH:mm:ss');
        data.received_date_time		= data.received_date_time.format('YYYY-MM-DD HH:mm:ss');
        data.admission_date         = data.admission_date.isValid() ? data.admission_date.format("YYYY-MM-DD") : undefined;
        if (data.admission_date) {
            data.admission_date += " " + (data.admission_time.isValid() ? data.admission_time.format("HH:mm:ss") : "");
        }
        data = _.omit(data, 'admission_time');

        var url = base_url + 'patient_sample/save';
        if (data.patient_sample_id > 0) {
            url = base_url + 'patient_sample/update';
        }        
        myDialog.showProgress('show', { text : msg_loading });        
        $.ajax({
            url			: url,
            type		: 'POST',
            data		: data,
            dataType	: 'json',
            success		: function (resText) {
             
                if (resText.status === true) {
                    sampleForm.psample_id_field.removeData("value");
                    sampleForm.psample_id_field.attr("data-value", resText.data.patient_sample_id);
                    sampleForm.inputSampleNumber.val(resText.data.sample_number);
                    sampleForm.showTitle();
                    sampleForm.inputSampleNumber.attr('disabled', true);
                    sampleForm.btnPreviewCovidForm.attr('disabled', false); // added 12 Jan 2021

                    //Set Sample Users
                    var sample_entry_user  = resText.data.users.sample_entry_user != null ? resText.data.users.sample_entry_user : "";
                    var result_entry_user  = resText.data.users.result_entry_user != null ? resText.data.users.result_entry_user : "";
                    var sample_entry_users = sample_entry_user.split(",");
                    var result_entry_users = result_entry_user.split(",");
                    if (sample_entry_user.length > 0 && sample_entry_users.length > 0) {
                        var $sample_user_list = sampleForm.form.find(".sample-entry-user-list");
                        $sample_user_list.find(".no-result, .user").remove();
                        for (var i in sample_entry_users) {
                            var $item = $sample_user_list.find(".sample-entry-user.template").clone();
                            $item.removeClass("template").removeClass("hide").addClass("user");
                            $item.text(sample_entry_users[i]);
                            $sample_user_list.append($item);
                        }
                    }
                    if (result_entry_user.length > 0 && result_entry_users.length > 0) {
                        var $result_user_list = sampleForm.form.find(".result-entry-user-list");
                        $result_user_list.find(".no-result, .user").remove();
                        for (var i in result_entry_users) {
                            var $item = $result_user_list.find(".result-entry-user.template").clone();
                            $item.removeClass("template").removeClass("hide").addClass("user");
                            $item.text(result_entry_users[i]);
                            $result_user_list.append($item);
                        }
                    }

                    //Callbacks
                    if (onSuccess && {}.toString.call(onSuccess) === '[object Function]') {
                        onSuccess(resText);
                    }
                }
                
                myDialog.showProgress('hide');
                myDialog.showDialog('show', {
                    text	 : resText.msg,
                    style	 : resText.status === true ? 'success' : 'warning',
                    onHidden : function () {
                        if (resText.status === true) {
                            //callbacks
                            if (onMsgClosed && {}.toString.call(onMsgClosed) === '[object Function]') {
                                onMsgClosed(resText);
                            }
                        }
                    }
                });
            },
            error		: function () {
                myDialog.showProgress('hide');
                myDialog.showDialog('show', { text	: msg_save_fail, style : 'warning' });
            }
        });
    };

    this.showTitle = function () {
        var data = this.get_data();
        if (data.sample_number.length > 0 || data.collected_date_time != null || data.received_date_time != null) {
            this.form.find(".sample-title").show();
        } else {
            this.form.find(".sample-title").hide();
        }

        if (data.sample_number.length > 0) {
            this.form.find(".sample-number-title").show();
            this.form.find(".sample-number-title b.value").text(data.sample_number).show();
        } else {
            this.form.find(".sample-number-title").hide();
            this.form.find(".sample-number-title b.value").empty().hide();
        }

        if (data.collected_date_time != null) {
            this.form.find(".collected-date-title").show();
            this.form.find(".collected-date-title b.value").text(data.collected_date_time.format("DD/MM/YYYY hh:mm A")).show();
        } else {
            this.form.find(".collected-date-title").hide();
            this.form.find(".collected-date-title b.value").empty().hide();
        }

        if (data.received_date_time != null) {
            this.form.find(".received-date-title").show();
            this.form.find(".received-date-title b.value").text(data.received_date_time.format("DD/MM/YYYY hh:mm A")).show();
        } else {
            this.form.find(".received-date-title").hide();
            this.form.find(".received-date-title b.value").empty().hide();
        }
    }
}

/**
 * Sample Test List
 * @constructor
 */
function TestList() {
    var renderList = function (data, parent, level) {
        parent	= parent === undefined ? 0 : parent;
        level	= level === undefined ? 0 : level;
        var html = "";

        if (data) {
            html = "<ul class='list-unstyled'>";
            for (var i in data) {
                
                if (parseInt(data[i].child_count) > 0) {
                    html += "<li is_heading='" + data[i].is_heading + "' parent='"+ data[i].testPID +"'><label style='cursor:pointer;'><input type='checkbox' class='sample-test' id='st-" + data[i].sample_test_id + "' is_heading='" + data[i].is_heading + "' parent='" + parent + "' testID='" + data[i].test_id + "' value='" + data[i].sample_test_id + "' test-name='" + data[i].test_name + "' data-group-result='"+ data[i].group_result +"'>&nbsp;&nbsp;<span class='t-name'>" + data[i].test_name + "</span></label>";
                    html += renderList(data[i].childs, data[i].sample_test_id, level + 1);
                    html += "</li>";
                } else {
                    html += "<li is_heading='" + data[i].is_heading + "'><label style='font-weight:100; cursor:pointer;'><input type='checkbox' class='sample-test' id='st-" + data[i].sample_test_id + "' name='sample_tests[]' is_heading='" + data[i].is_heading + "' parent='" + parent + "' value='" + data[i].sample_test_id + "' test-name='" + data[i].test_name + "' data-group-result='"+ data[i].group_result + "'>&nbsp;&nbsp;<span class='t-name'>" + data[i].test_name + "</span></label></li>";
                }
            }
            html += "</ul>";
        }

        return html;
    };
    this.generate = function () {
        var $test_modal = $("#test_modal");
        var $tree_list  = $test_modal.find(".tree-list");
        var _this = this;

        $.ajax({
            url			: base_url + "test/get_std_sample_test",
            type		: "POST",
            data		: { group_by : 'sample' },
            dataType	: 'json',
            success		: function (resText) {
                
                $test_modal.find("#test-form div.department-test").each(function () {
                    var department_id = $(this).data("value");
                    var $test_list = $(this).find("div.tree-list");
                    $test_list.empty();
                   
                    //added 18 Dec 2020
                    if(resText[parseInt(department_id)] === undefined){
                        return;
                    }
                    var samples	= resText[department_id].samples !== undefined ? resText[department_id].samples : ""; 

                    var html	= "";
                    if (samples) {
                        for (var i in samples) {
                            html += "<div class='sample-type-wrapper' id='dsample-" + samples[i].department_sample_id + "' data-department-sample='" + samples[i].department_sample_id + "'>";
                            html += "<div class='sample-type-header-wrapper'>";
                            html += "<div class='sample-type-header'><i class='fa fa-hand-o-right'></i> " + samples[i].sample_name;
                            html += "<button type='button' class='pull-right btn btn-default btn-toggle-sample-info'><i class='fa fa-chevron-up'></i></button>";
                            html += "</div>"; //End Sample Type header
                            html += "<div class='sample-info-wrapper form-vertical'>";
                            html += "<label class='control-label'>"+ label_sample_desription +"</label>";
                            html += "<select name='sample_desc' class='form-control input-sm sample-desc' id='dsample-description-" + samples[i].department_sample_id + "'>";
                            html += "<option value='-1'></option>";
                            if (sample_descriptions !== undefined && Array.isArray(sample_descriptions[samples[i].sample_id])) {
                                var sample_description = sample_descriptions[samples[i].sample_id];
                                for (var j in sample_description) {
                                    html += "<option value='" + sample_description[j].ID + "'>" + sample_description[j].description + "</option>";
                                }
                            }
                            html += "</select>";
                            
                            if (samples[i].show_weight) {
                                html += "<div class='row' style='margin: 7px 0 0;'>";
                                html += "<div class='col-sm-6' style='padding-left: 0;'><label class='control-label'>" +label_weight1+"</label>";
                                html += "<div class=\"input-group input-group-sm\">";
                                html += "<input type=\"text\" class=\"form-control\" name=\"first_weight\" placeholder=\"Weight 1\" id='dsample-first-weight-" + samples[i].department_sample_id + "'>";
                                html += "<span class=\"input-group-addon\">gm</span>";
                                html += "</div>";
                                html += "</div>";
                                html += "<div class='col-sm-6' style='padding-right: 0;'><label class='control-label'>" + label_weight2 + "</label>";
                                html += "<div class=\"input-group input-group-sm\">";
                                html += "<input type=\"text\" class=\"form-control\" name=\"second_weight\" placeholder=\"Weight 2\" id='dsample-second-weight-" + samples[i].department_sample_id + "'>";
                                html += "<span class=\"input-group-addon\">gm</span>";
                                html += "</div>";
                                html += "</div>";
                                html += "</div>";
                            }

                            html += "</div>"; //End Sample Info Wrapper
                            html += "</div>"; //End Sample Type header wrapper

                            html += renderList(samples[i].tests);
                            html += "</div>"; //End Sample Type Wrapper
                        }
                    }
                    $test_list.html(html);
                });

                //Init iCheck
                $tree_list.find("input").iCheck({ checkboxClass: 'icheckbox_minimal', radioClass: 'iradio_minimal', indeterminateClass: 'indeterminate-line' });
                //Set Treeview
                $tree_list.treeview({ collapsed : false, animated : true });
            },
            error		: function () {

            }
        });
    };
}

/**
 * Test Result Entry Form
 * @constructor
 */
var _department_id = 0;
var _sample_test_id = 0;
function ResultForm() {
    ResultForm.order = 1;
    var renderTestList = function(tests, level, is_dept) {
        level	 = level == undefined ? 1 : level;
        var html = "";

        var performers = $("#result_modal").find("#performer-list").html();
       
        //Set Test List
        if (tests) {
            console.log(tests);
            for (var i in tests) {      
                //console.log(tests);
                //var is_heading	= Number(tests[i].is_heading) == 1;
                var is_heading	= tests[i].is_heading
                var input 		= "";
                var ref_range	= "";
                var unit_sign	= !is_heading && tests[i].unit_sign != null ? tests[i].unit_sign : "";

                console.log("Field Type"+tests[i].field_type +" is heading: "+is_heading);
                //Result Input box
                if ((tests[i].field_type == 3 || tests[i].field_type == 5) && !is_heading) {
                    input  = "<input type='text' id='test"+ tests[i].sample_test_id +"' name='result' maxlength='6' class='form-control result' order='"+ (ResultForm.order++) +"'";
                    input += "onkeypress='return allowDouble(event, this)'";
                    input += "onchange='validateRefRange(this,"+ tests[i].sample_test_id +","+ tests[i].field_type +");";
                    // if (tests[i].field_type == 5) {
                    //     input += "calculateTestValue(this);";
                    // }
                    input += "'>";
                } else if ((tests[i].field_type == 1 || tests[i].field_type == 2) && !is_heading) {
                    input  = "<input type='text' name='result' class='form-control result' order='"+ (ResultForm.order++) +"'";
                    input += "style='background:white; cursor:pointer;' onfocus='showPossibleResult(this)' readonly>";
                } else if (!is_heading){
                    input = "<input type='text' name='result' class='form-control result' order='"+ (ResultForm.order++) +"'>";
                }

                var header_class	= is_heading ? "class='header'" : "";
                var padding			= level === 1 ? 10 : (level - 1) * 35;
                var caret_icon		= is_heading ? "<i class='fa fa-caret-up'></i>" : "";

                // verified department is equal for set [sample test id] static for first time
                if(_department_id == tests[i].department_id){
                    _department_id = tests[i].department_id;
                    _sample_test_id =_sample_test_id;
                }else{
                    _department_id = 0;
                    _sample_test_id = 0;
                }

                html += "<tr class='test' id='patient-test-"+ tests[i].patient_test_id +"' data-rejected='"+ tests[i].is_rejected +"' main='"+ tests[i].department_id +"' ";
                html += "   test_id='"+_sample_test_id +"' parent='"+ tests[i].testPID +"' ";
                html += "	data-sample-id='"+ tests[i].sample_id +"'";
                html += "	data-is-show='"+ tests[i].is_show +"'";
                html += "	data-patient-test-id='"+ tests[i].patient_test_id +"' data-department='"+tests[i].department_id+"'";
                html += "	data-sample-test-id='"+ tests[i].sample_test_id +"' data-field-type='"+ tests[i].field_type +"'";
                html += "	data-formula='"+ (tests[i].formula || '') +"'>";
                html += "<td " + header_class + " style='padding-left: "+ padding +"px;' >";
                /*
                //Click to show result history edit 11-07-2018 add atribrute patient_test_id
                html += "<span class='t-name' patient_test_id='"+tests[i].patient_test_id+"' style='display: inline-block; margin-right: 20px;'>" + tests[i].test_name + "</span>";
                //Machine name
                if (!is_heading) {
                    html += "<a href='javascript:void(0);' class='machine' test_id='"+tests[i].sample_test_id+"'><i class='fa fa-hospital-o'></i></a>";
                }
                */
                html += "<span class='t-name' style='display: inline-block; margin-right: 20px;'>" + tests[i].test_name + "</span>";
                html += caret_icon + "</td>";

                // checking department first time verified
                if(_department_id==0){
                    _department_id = tests[i].department_id;
                    _sample_test_id =tests[i].sample_test_id;
                }

                if (tests[i].is_rejected == 1) {
                    html += "<td colspan='5'>" + msg_test_rejected + "</td>";
                } else {
                    
                    html += "<td>" + input + "</td>";
                    html += "<td style='width:100px;' class='unit-sign'>" + unit_sign + "</td>";
                    html += "<td style='width:120px;' class='ref-range'>" + ref_range + "</td>";
                    html += "<td style='width:130px; position:relative;'>";
                    if (!is_heading) html += "<input type='text' class='form-control test_date' style='background:white;'></td>";
                    html += "<td style='width:130px;'>";
                    if (!is_heading) html += "<select class='form-control performer'><option value='-1'>" +label_choose_performer+"</option>"+performers+"</select></td>";
                }
                html += "<td class='text-center'><input type='checkbox' class='test-visibility'></td>";
                html += "</tr>";

                if (tests[i].childs) {
                    html += renderTestList(tests[i].childs, level + 1);
                }
            }
        }

        return html;
    };

    this.renderList = function (data) {
        var html = "";
        for (var d in data) {
            var department_id   = data[d].department_id;
            var department_name = data[d].department_name;
            if (data[d].samples) {
                console.log(data[d].samples);
                for (var s in data[d].samples) {
                    var sample_id = data[d].samples[s].sample_id;
                    var group_name = "<i class='fa fa-hand-o-right'></i>&nbsp;&nbsp;" + department_name + "&nbsp;&nbsp;<i class='fa fa-link'></i>&nbsp;&nbsp;" + data[d].samples[s].sample_name;
                    var tests = data[d].samples[s].tests;

                    html += "<tr style='border-left: 2px solid #9078cf;' id='dsample-"+ department_id +"-"+ sample_id +"'><td colspan='7'><b class='text-blue'>" + group_name + "</b>";
                    html += "&nbsp;&nbsp&nbsp;&nbsp&nbsp;&nbsp;";
                    html += "<b><i class='fa fa-user'></i>&nbsp;"+_sample_entry_by+"</b> : <div class='result-entry-user-list' style='display: inline-block'></div>";
                    html += "&nbsp;"+_sample_entry_date+": <div class='result-entry-date-list' style='display: inline-block'></div>";
                    html += "&nbsp;&nbsp&nbsp;&nbsp&nbsp;&nbsp;";
                    html += "<b><i class='fa fa-user'></i>&nbsp;"+_sample_modified_by+"</b>: <div class='result-modified-user-list' style='display: inline-block'></div>";
                    html += "&nbsp;"+_sample_entry_date+": <div class='result-modified-date-list' style='display: inline-block'></div>";
                    html += "&nbsp;&nbsp&nbsp;&nbsp&nbsp;&nbsp;";
                    html += "<input type='checkbox' id='department" +data[d].department_id+""+data[d].samples[s].sample_id+"'";
                    html += " onClick='onDepartmentView("+data[d].department_id+","+data[d].samples[s].sample_id+");'";
                    html += "value='" +data[d].department_id+"' />";
                    html += "&nbsp;<label for='department" +data[d].department_id+""+data[d].samples[s].sample_id+"'>View</label>";
                    html += "</td></tr>";
                    html += renderTestList(tests);
                    html += "<tr class='comment' data-department-sample-id='"+ data[d].samples[s].department_sample_id +"'>";
                    /*
                    * 29/08/2018
                    * Old code
                    * html += "<td>&nbsp;</td>";
                    */
                    /* 29/08/2018 add new reject text area comment*/
                    html += "<td align='left'><textarea disabled rows='2' name='reject_comment' id='reject-comment-"+ data[d].samples[s].department_sample_id +"' class='form-control' style='margin-top: 0; margin-bottom: 0; height: 80px; resize:none;'></textarea></td>";
                    html += "<td align='right' ><button type='button' class='btn btn-primary btn-sm' onclick='onSelectComment("+data[d].department_id+","+data[d].samples[s].sample_id+","+data[d].samples[s].department_sample_id+");' >Select Comment</button></td>";
                    html += "<td colspan='5'><textarea name='result_comment' id='result-comment-"+ data[d].samples[s].department_sample_id +"' class='form-control' style='margin-top: 0; margin-bottom: 0; height: 80px; resize:none;'></textarea></td>";
                    html += "</tr>";
                } //end loop of samples
            }
        } //end loop of departments

        return html;
    };

    // create new function to checking box
    var dept = [];
    var sampt = [];
    onDepartmentView=function(department_id,sample_id){
        // checking view optional department
        if($('#department'+department_id+""+sample_id).prop('checked')){
            // push data to array
            dept.push(department_id);
            sampt.push(sample_id);

        }else{
            dept.splice($.inArray(department_id, dept), 1);
            sampt.splice($.inArray(sample_id, sampt), 1);
        }
        // assign value to hidden file
        $('#department_result_view_optional').val(dept);
        $('#sample_result_view_optional').val(sampt);
    }

    onSelectComment = function(department_id,sample_id, department_sample_id){
        var $tbl_cmt_list = $("#tb_cmt_list");
        $('#hidden_sample_id').val(sample_id);
        //show progress
        $("#modal_comment").data("department_sample_id", department_sample_id);
        myDialog.showProgress('show', { text : msg_loading });

        var patient_sample_id	= Number($('#patient_sample_id').val());
        if (isNaN(patient_sample_id) || patient_sample_id <= 0) {
            myDialog.showProgress('hide');
            return false;
        }
        $tbl_cmt_list.removeData("selected_comment");
        var input_comment	= $("#result-comment-" + department_sample_id).val();
        input_comment		= input_comment.split(/(?:\r\n|\r|\n)/g);


        var tb_cmt_list	= $tbl_cmt_list.DataTable({
            destroy		: true,
            autoWidth	: false,
            info		: false,
            processing	: true,
            serverSide	: true,
            searching   : true,
            ajax		: {
                url	 : base_url + 'sample/view_std_sample_comment',
                type : 'POST',
                data : function (d) {
                    d.patient_sample_id = patient_sample_id;
                    d.dep_result_opt = department_id;
                    d.sam_result_opt = sample_id;
                }
            },
            columns		: [ { "data": "checkbox"}, { "data": "comment" } ],
            columnDefs	: [
                { searchable : false , targets : 0 },
                { targets : '_all', className : 'text-middle' }
            ],
            order		: [[1, 'asc']],
            language	: dataTableOption.language,
            createdRow	: function(row, data, dataIndex) {        
                $(row).find(":checkbox").iCheck({ checkboxClass: 'icheckbox_minimal', radioClass: 'iradio_minimal' });
                $(row).find('.iCheck-helper').css('position', 'relative');
                $(row).find(":checkbox").on("ifChanged", function () {                    
                    var val                 = $(this).val();                    
                    var comment             = data.comment;
                    var selected_comment    = $tbl_cmt_list.data("selected_comment");
                    if (selected_comment == undefined || !(selected_comment instanceof Object)) selected_comment = {};
                    if ($(this).is(":checked")) {
                        selected_comment["comment_" + val] = comment;
                    } else {
                        delete(selected_comment["comment_" + val]);
                    }

                    $tbl_cmt_list.data("selected_comment", selected_comment);
                });


                // checking box from previous comment seleted
                for(var k in input_comment) {
                    if(input_comment[k]===data['comment']){
                        var _val = data['checkbox'].split('value=')[1].split(' ')[0];
                        $(row).find(":checkbox[value="+_val+"]").iCheck("check");
                    }
                }


                //check previous comment
                var selected_comment = $tbl_cmt_list.data("selected_comment");
                if (selected_comment) {
                    for(var key in selected_comment) {
                        var arr = key.split('_');
                        if (arr[1] != undefined) {
                            $(row).find(":checkbox[value="+arr[1]+"]").iCheck("check");
                        }
                    }
                }
            },
            drawCallback: function (settings) {
                myDialog.showProgress('hide');
                $("#modal_comment").modal({ backdrop: 'static' });
            }
        });
    }

}

/**
 * Todo calculate test value
 * @param inputInstance
 */
function calculateTestValue(formula) {
    CAMLIS_VARIABLE_FORMAT.TEST.lastIndex = 0;
    
    if (formula) {
        var parser = new exprEval.Parser();
        var expr   = parser.parse(formula);
        var variables = expr.variables();
        var map_values = {};

        for(var i in variables) {
            if (CAMLIS_VARIABLE_FORMAT.TEST.test(variables[i])) {
                var sample_test_id = variables[i].replace('T', 'test');
                var value = $("#" + sample_test_id).val() || '';
                if (value.length > 0) map_values[variables[i]] = parseFloat(value || 0);
            }

            CAMLIS_VARIABLE_FORMAT.TEST.lastIndex = 0;
        }

        if (variables.length > _.values(map_values).length) return null;

        return expr.evaluate(map_values);
    }

    return 0;
}

/**
 * Add sample form
 * @param initValue
 * @param $sampleForm
 * @param sampleFormData
 * @returns {boolean}
 */
function addSampleForm(initValue, $sampleForm, sampleFormData) {
    var $sample_forms = $("#sample-forms");
    var content	= $("#sample-form-template").html();
    var count	= $sample_forms.find("div.sample-form").length;
    var $item   = null;
    
    if (content != undefined && !$sampleForm) {
        $item   = $(content);
        $item.find("span.sample-order").text(count + 1);
        
        // added 04 Dec 2020
        $item.find('.btnShowQuestionaire').attr('data-target','#collapseCovidForm'+count); // added 04 Dec 2020
        $item.find('.collapse').attr('id','collapseCovidForm'+count);
        // End

        //Append Form
        $("#btnMore").before($item);
    }
    else if ($sampleForm && $sampleForm.length == 1) {
        $item = $sampleForm;
    }
    else {
        return false;
    }

    if ($item) {        
        var sampleForm = new SampleForm($item.find("form.frm-sample-entry"));

        //Init Select2
        $item.find("select[name=sample_source], select[name=requester], select[name=payment_type], select[name=clinical_symptom]").select2();
        
        //Init icheck
        $item.find('input').iCheck('destroy');
        $item.find('input').iCheck({checkboxClass: 'icheckbox_minimal', radioClass: 'iradio_minimal'});

        //Init Datetimepicker
        $item.find("input.dtpicker, input.admission-date").datetimepicker(dtPickerOption);

        //Min Date && Max Date
        $item.find("input[name=collected_date].dtpicker").on("dp.change", function (e) {
            var _date = moment($(this).val().trim(), "DD/MM/YYYY");
            if (_date.isValid() && e.date == undefined) {
                e.date = _date.toDate();
                $(this).data("DateTimePicker").setDate(e.date);
            }
            if (e.date) $item.find("input[name=received_date].dtpicker").data("DateTimePicker").minDate(e.date);
            sampleForm.validate_fields();
            sampleForm.validate_date_time();
            sampleForm.showTitle();
        }).trigger("dp.change");
        $item.find("input[name=received_date].dtpicker").on("dp.change", function (e) {
            var _date = moment($(this).val().trim(), "DD/MM/YYYY");
            if (_date.isValid() && e.date == undefined) {
                e.date = _date.toDate();
                $(this).data("DateTimePicker").setDate(e.date);
            } else if (e.date == undefined) {
                e.date = moment(moment().format("DD-MM-YYYY"), "DD-MM-YYYY").toDate();
                $(this).data("DateTimePicker").setDate(e.date);
            }
            if (e.date) $item.find("input[name=collected_date].dtpicker").data("DateTimePicker").maxDate(e.date);
            sampleForm.validate_fields();
            sampleForm.validate_date_time();
            sampleForm.showTitle();
        }).trigger("dp.change");

        //timepicker
        $item.find("input.admission-time").timepicker({minuteStep: 1, showMeridian: false, defaultTime: '00:00'});
        $item.find("input.coltimepicker").timepicker({minuteStep: 1, showMeridian: false, defaultTime: moment().subtract(1, 'minutes').format('HH:mm')}).on("hide.timepicker", function (e) {
            if (e.time.value) $item.find("input[name=collected_date].dtpicker").trigger("dp.hide");
            sampleForm.validate_fields();
            sampleForm.validate_date_time();
        });
        $item.find("input.rectimepicker").timepicker({minuteStep: 1, showMeridian: false}).on("hide.timepicker", function (e) {
            if (e.time.value) $item.find("input[name=received_date].dtpicker").trigger("dp.hide");
            sampleForm.validate_fields();
            sampleForm.validate_date_time();
        });

        //Check collected date with Received date
        $item.find("input.dtpicker").on("dp.hide", function (evt) {
            evt.preventDefault();

            if (!sampleForm.validate_date_time()) {
                myDialog.showDialog('show', {text: msg_col_rec_dt_error, style: 'warning'});
            }
        });

        //Requester
        $item.find("select[name=requester]").on("change", function () {
            sampleForm.validate_fields();
            sampleForm.validate_date_time();
        });

        //Clinical history autocomplete && require fiel
        $item.find('textarea[name="clinical_history"]').autocomplete({
            minLength: 1, 
            source: function(request, response) {
                $.ajax({
                    type : "POST",
                    url  : base_url+'sample/clinical_history',
                    data : { 
                        filter_val : $item.find('textarea[name="clinical_history"]').val() 
                    },
                    dataType: "json",
                    success : function (data) {
                        if (data != null) {
                            response(data);
                        }
                        sampleForm.validate_fields();
                        sampleForm.validate_date_time();
                    }
                });
            }
        }).focus(function(){
            $(this).autocomplete("search");
        }).blur(function(event) {
            sampleForm.validate_fields();
            sampleForm.validate_date_time();
        });

        //Lab Fee
        $item.find("select[name=payment_type]").on("change", function () {
            sampleForm.validate_fields();
            sampleForm.validate_date_time();
        });

        //Sample Number
        var $sampleNumber = $item.find("input[name=sample_number]");
        if ($sampleNumber.data("required") === "yes") {
            $sampleNumber.on("keyup", function (evt) {
                sampleForm.validate_fields();
                sampleForm.validate_date_time();
            });

            $sampleNumber.on("focus", function (evt) {
                $(this).select();
                sampleForm.form.find("label[for=sample-number]").removeAttr("data-hint");
            });

            $sampleNumber.on("blur", function (evt) {
                sampleForm.setValidity(false);

                var sample_number = $(this).val();
                $.ajax({
                    url: base_url + "patient_sample/is_unique_sample_number",
                    type: "POST",
                    data: { sample_number: sample_number },
                    dataType: 'json',
                    success: function (resText) {
                        if (resText.is_unique) {
                            $sampleNumber.removeClass('duplicated-value');
                            sampleForm.form.find("label[for=sample-number]").removeAttr("data-hint");
                        }
                        else {
                            $sampleNumber.addClass('duplicated-value');
                            sampleForm.form.find("label[for=sample-number]").attr("data-hint", resText.msg);
                        }

                        sampleForm.setValidity(resText.is_unique);
                        sampleForm.validate_fields();
                        sampleForm.validate_date_time();
                    },
                    error: function () {

                    }
                })
            });
        }

        //Scroll to bottom
        $('#template-wrapper').animate({
            scrollTop: $("#template-content").height()
        }, 1000);
    }

    //Init value
    if (initValue) {
        var admissiondate = moment(initValue.admission_date);
        if (admissiondate.isValid()) {
            $item.find("input[name=admission_date]").data("DateTimePicker").setDate(admissiondate.toDate());
            $item.find("input[name=admission_time]").timepicker('setTime', admissiondate.format("HH:mm"));
        }
    }

    //Set Previous Data
    if (sampleFormData) {
        $item.find("input[type=hidden][name=patient_sample_id]").data("value", sampleFormData.patient_sample_id);
        $item.find("input[name=sample_number]").val(sampleFormData.sample_number);
        $item.find("input[name=sample_number]").prop("disabled", true);
        $item.find("select[name=sample_source]").val(sampleFormData.sample_source_id).trigger("change", [sampleFormData.requester_id]);
        $item.find("select[name=payment_type]").val(sampleFormData.payment_type_id).trigger("change");

        var collected_date = moment(sampleFormData.collected_date, 'YYYY-MM-DD');
        var received_date  = moment(sampleFormData.received_date, 'YYYY-MM-DD');
        var admission_date = moment(sampleFormData.admission_date, 'YYYY-MM-DD HH:mm:ss');
        $item.find("input[name=collected_date].dtpicker").data("DateTimePicker").setDate(collected_date.toDate());
        $item.find("input[name=received_date].dtpicker").data("DateTimePicker").setDate(received_date.toDate());
        $item.find("input.coltimepicker").timepicker('setTime', moment(sampleFormData.collected_time, 'HH:mm:ss').format('HH:mm'));
        $item.find("input.rectimepicker").timepicker('setTime', moment(sampleFormData.received_time, 'HH:mm:ss').format('HH:mm'));
        $item.find("input[name=admission_date]").data("DateTimePicker").date(admission_date.isValid() ? admission_date.toDate() : null);
        $item.find("input[name=admission_time]").timepicker('setTime', admission_date.isValid() ? admission_date.format("HH:mm") : null);

        $item.find(":radio[name=payment_needed][value='"+ sampleFormData.payment_needed +"']").iCheck('check');
        $item.find(":checkbox[name=is_urgent]").iCheck(sampleFormData.is_urgent == 1 ? "check" : "uncheck");
        //$item.find(":checkbox[name=for_research]").iCheck(sampleFormData.for_research == 1 ? "check" : "uncheck");
        $item.find("select[name=for_research]").val(sampleFormData.for_research).trigger("change");
        $item.find("textarea[name=clinical_history]").val(sampleFormData.clinical_history);
        $item.find("button.btnRejectSample, button.btnAddResult, button.btnPreview").prop("disabled", !sampleFormData.is_assigned_test);
        $item.find("button.btnPreview").data("enabled", sampleFormData.is_assigned_test ? 1 : 0);
        $item.find("button.btnPreviewCovidForm").attr('disabled', false);

        $item.find("form.frm-sample-entry").data("test-payment", sampleFormData.test_payments);
                
        if( (sampleFormData.completed_by !== "") || (sampleFormData.phone_number !== "") || (sampleFormData.phone_number_sample_collector !== "") || (sampleFormData.sample_collector !== "") || (sampleFormData.clinical_symptom !== undefined)){ 
            $('#collapseCovidForm').collapse();
        }
        $item.find("input[name=completed_by]").val(sampleFormData.completed_by);
        $item.find("input[name=phone_number]").val(sampleFormData.phone_number);
        $item.find("input[name=sample_collector]").val(sampleFormData.sample_collector);
        $item.find("input[name=phone_number_sample_collector]").val(sampleFormData.phone_number_sample_collector);
        $.ajax({
            url      : base_url + "sample/get_clinical_symptom",
            type     : 'POST',
            data     : { psample_id :  sampleFormData.patient_sample_id},
            dataType : 'json',
            success  : function(resText) {
                var data = [];
                for(var i in resText) {
                    data.push(resText[i].clinical_symptom_id);
                }
                $item.find("select[name=clinical_symptom]").val(data).trigger("change"); 
            }
        });
    }
}
/** Add Modal Line List
     * added 19-03-2021
     */
 $(function () {
    var $btnSubmitPatientForm = $("#btnSubmitPatientForm");
    var $print_preview_covid_form_modal_1	= $("#print_preview_covid_form_modal");
    var $modal_error_line_list = $("#modal_error_line_list");

    var today = new Date();
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
    var yyyy = today.getFullYear();
    today = yyyy+'-'+mm+'-'+dd;
    
    $("#btnAddPatients").on("click", function (evt) {
        var $modal_read_patient_from_excel = $("#modal-read-patient-from-excel"); // added 19-03-2021
        $modal_read_patient_from_excel.modal("show");
    });
    
    
    var province_array      = [];
    var districts_array     = [];
    var communes_array      = [];
    var villages_array      = [];
    var nationalities_array = [];
    var country_array       = [];
    var requester_array     = [];
    var reason_for_testing_array = [];
    var clinical_symptom_array = [];
    var sample_source_array = [];
    var payment_type_array  = [];
    var performer_array  = [];
    
    var patient_code_col        = 0; 
    var patient_name_col        = 1; 
    var age_col                 = 2;
    var gender_col              = 3;
    var phone_col               = 4;
    var province_col            = 5;
    var district_col            = 6;
    var commune_col             = 7;
    var village_col             = 8;
    var residence_col           = 9;
    var for_researh_col         = 10;
    var is_contacted_col        = 11;
    var is_contacted_with       = 12;
    var is_directed_contact     = 13;
    var sample_number_col       = 14;
    var sample_source_col       = 15;
    var requester_col           = 16;
    var collected_date_col      = 17;
    var received_date_col       = 18;
    var diagnosis_col           = 21;
    var is_urgent_col           = 22;
    var completed_by_col        = 23;
    var phone_completed_by_col  = 24;
    var sample_collector_col    = 25;
    var phone_number_sample_collector = 26;
    var clinical_symptom_col    = 27;
    var test_name_col           = 28;
    var country_col             = 29;
    var nationality_col         = 30;
    var arrival_date_col        = 31;
    var passport_number_col     = 32;
    var flight_number_col       = 33;
    var seat_number_col         = 34;
    var is_positive_covid_col   = 35;
    var test_date_col           = 36;
    var test_id_col             = 46;
    var number_of_sample_col    = 49;

    $.each(PROVINCES, function(key, value) {
        province_array.push(value.name_kh);
    });
    
    $.each(DISTRICTS, function(key, value) {
        
        districts_array.push(value.name_kh);
    });
    $.each(COMMUNES, function(key, value) {
        
        communes_array.push(value.name_kh);
    });
    $.each(VILLAGES, function(key, value) {
        
        villages_array.push(value.name_kh);
    });
    $.each(NATIONALITIES, function(key, value) {
        
        nationalities_array.push(value.nationality_en);
    });
    $.each(COUNTRIES, function(key, value) {
        country_array.push(value.name_en);
    });

    $.each(REQUESTER, function(key, value) {
        requester_array.push(value.requester_name);
    });

    $.each(PERFORMERS, function(key, value) {
        performer_array.push(value.performer_name);
    });
    
    $.each(REASON_FOR_TESTING, function(key, value) {        
        reason_for_testing_array.push(value);
    });    
    $.each(CLINICAL_SYMPTOM, function(key, value) {
      clinical_symptom_array.push(value.name_kh);
    });
    $.each(SAMPLE_SOURCE, function(key, value) {
        sample_source_array.push(value.source_name);
    });
    $.each(PAYMENT_TYPE, function(key, value) {
        payment_type_array.push({id:value.id, name: value.name});
    });

    var province_code = "";
    var district_code = "";
    var commune_code  = "";

    var districtFilter = function(instance, cell, c, r, source) {
        var value = instance.jexcel.getValueFromCoords(c - 1, r);
        var res = [];
        // get province id 
        
        $.each(PROVINCES, function(key, row) {            
            if(row.name_kh == value){
                province_code = row.code;
                return false;
            }
        });        
        $.each(DISTRICTS, function(key, item) {
            if(province_code == item.province_code){
                res.push(item.name_kh);
            }
        });        
        return res;
    }
    
    var communeFilter = function(instance, cell, c, r, source) {
        var value         = instance.jexcel.getValueFromCoords(c - 1, r);
        var res           = [];       
        var province_code =  instance.jexcel.getValueFromCoords(38, r);        
        $.each(DISTRICTS, function(key, row) {            
            if(row.name_kh == value && row.province_code == province_code){
                district_code = row.code;
                return false;
            }
        });
        $.each(COMMUNES, function(key, item) {
            if(district_code == item.district_code){
                res.push(item.name_kh);
            }
        });
        return res;
    }
    
    var villageFilter = function(instance, cell, c, r, source) {
        var value = instance.jexcel.getValueFromCoords(c - 1, r);
        var res = [];
        var district_code =  instance.jexcel.getValueFromCoords(39, r); // district_code
        $.each(COMMUNES, function(key, row) {            
            if(row.name_kh == value && row.district_code == district_code){
                commune_code = row.code;
                return false;
            }
        });        
        $.each(VILLAGES, function(key, item) {
            if(commune_code == item.commune_code){
                //res.push({id:item.code, name: item.name_kh});
                res.push(item.name_kh);
            }
        });
        return res;
    }
    
    var requesterFilter = function(instance, cell, c, r, source) {
        var value = instance.jexcel.getValueFromCoords(c - 1, r);
        var sample_source =  instance.jexcel.getValueFromCoords(43, r); // district_code
        var res = [];        
        $.each(REQUESTER, function(key, item) {
            if(sample_source == item.sample_source_id){
                res.push(item.requester_name);
            }
        });
        return res;
    }

    $(document).on('click','button.btnPrintCovidFormV1', function(evt) {
        evt.preventDefault();
     
        var patient_sample_id = $(this).attr("data-psample_id");
        //console.log(patient_sample_id);
        
        $print_preview_covid_form_modal_1.find(".modal-dialog").empty();
        $print_preview_covid_form_modal_1.data("patient_sample_id", patient_sample_id);
        $print_preview_covid_form_modal_1.find("#doPrinting").off("click").on("click", function (evt) {
            evt.preventDefault();
            //console.log("printing");
            var _dep_view = $('#department_result_view_optional').val();
            var _sm_view = $('#sample_result_view_optional').val();
            

            if (_dep_view === undefined || _dep_view === null) {
                _dep_view ='';
                _sm_view = '';
            }

            printpage(base_url + "result/patient_covid_forms/print/" +patient_sample_id + "/"+encodeURIComponent(_dep_view)+"/"+encodeURIComponent(_sm_view));
        //    $.post(base_url + "patient_sample/update_printed_info", { patient_sample_id: data.patient_sample_id });
        });

        //Show Progress
        myDialog.showProgress('show', { text : msg_loading });
        
        $.ajax({
            url: base_url + "result/patient_covid_forms/preview/" + patient_sample_id,
            type: 'POST',
            dataType: 'json',
            data: {
                department_optional_view: $('#department_result_view_optional').val(),
                sample_optional_view: $('#sample_result_view_optional').val()                
            },
            success: function (resText) {    
                //console.log(resText.length);  
                //console.log(resText);
                for (var i in resText) {
                    var $page = $("<div class='psample-result'></div>");
                    $page.attr("id", "presult-" + (parseInt(i) + 1));
                    $page.data("patient_sample_id", resText[i].patient_sample_id);
                    $page.html(resText[i].template);

                    //if (i == 0) $page.addClass("active");
                    //else $page.hide();

                    $print_preview_covid_form_modal_1.find(".modal-dialog").append($page);
                }
                
                $print_preview_covid_form_modal_1.find(".page-count").text(resText.length);
                $print_preview_covid_form_modal_1.find(".page-number").val(1);
                $print_preview_covid_form_modal_1.find(".page-number").autoNumeric({aPad: 0, vMin: 1, vMax: resText.length});

                setTimeout(function () {
                    myDialog.showProgress('hide');
                    $print_preview_covid_form_modal_1.modal("show");
                }, 400);
            },
            error: function () {
                myDialog.showProgress('hide');
                $print_preview_covid_form_modal_1.modal("show");
                $print_preview_covid_form_modal_1.find(".modal-dialog").empty();
            }
        });
    })


    /**
     * Added 22-04-2021
     * 
     */
    var PATIENT_RRT = [];
    function savePatients(patient){
        PATIENT_RRT = patient;        
    }
    var $modalPatientTmp = $("#modal_patient_tmp");
    $("#btnPullData").on("click",function(){
        $(this).addClass('disabled'); //prevent multiple click
        $("#btnImportPatient").addClass("disabled");
        myDialog.showProgress('show');

        var rrt_user = $modalPatientTmp.find('select[name=rrt_dd] option:selected').val();
        var date     = $modalPatientTmp.find("input[name=collected_date]").data('DateTimePicker').date();    
        if(rrt_user == 0 || date == null){
            myDialog.showProgress('hide');
            myDialog.showDialog('show', {text: "សូមបញ្ចូលទិន្នន័យជាមុនសិន...", style: 'warning'});
            $(this).removeClass('disabled'); //prevent multiple click
            return false;
        }

        var collected_date =  date.format('YYYY-MM-DD');
        $.ajax({
            url: base_url + "/rrt/get_patients",
            type: "POST",
            data: { user: rrt_user , date:collected_date},
            dataType: 'json',
            success: function (resText) {
                //console.log(resText);
                var patients = resText.patients;
               // console.log(patients);
                var bodyResult = '';    
                var n = 1;
                //console.log("Length: "+patients.length);
                if(patients.length == 0){
                    bodyResult += '<tr><td colspan=24>ពុំមានទិន្នន័យ</td></tr>';
                }else{
                    savePatients(patients);
                    for(var i in patients) {

                        var phone               = (patients[i].phone == null) ? " &nbsp;" : patients[i].phone;
                        var residence           = (patients[i].residence == null) ? " &nbsp;": patients[i].residence;
                        var country_name        = (patients[i].country_name_en) == null ? "&nbsp;": patients[i].country_name_en;
                        var nationality_en      = (patients[i].nationality_en) == null ? "&nbsp;": patients[i].nationality_en;
                        var date_arrival        = (patients[i].date_arrival) == null ? " &nbsp;": patients[i].date_arrival;
                        var passport_number     = (patients[i].passport_number) == null ? "&nbsp; ":patients[i].passport_number;
                        var flight_number       = (patients[i].flight_number) == null ? " &nbsp;": patients[i].flight_number;
                        var seat_number         = (patients[i].seat_number) == null ? " &nbsp;": patients[i].seat_number;
                        var sample_source       = (patients[i].sample_source) == null ? "&nbsp; ": patients[i].sample_source;
                        var sample_collector    = (patients[i].sample_collector) == null ? "&nbsp; ": patients[i].sample_collector;
                        var phone_number        = (patients[i].phone_number) == null ? " &nbsp;": patients[i].phone_number;
                        var for_labo            = (patients[i].for_labo) == null ? " &nbsp;": patients[i].for_labo;
                        bodyResult += '<tr>';
                        bodyResult += '<td>'+n+'</td>';
                        bodyResult += '<td>'+patients[i].patient_code+'</td>';
                        bodyResult += '<td>'+patients[i].name+'</td>';
                        bodyResult += '<td>'+patients[i].age+'</td>'; 
                        bodyResult += '<td>'+patients[i].sex+'</td>';  

                        bodyResult += '<td>'+phone+'</td>'; 
                       
                        bodyResult += '<td>'+residence+'</td>';
                        bodyResult += '<td>'+patients[i].province_en+'</td>'; 
                        bodyResult += '<td>'+patients[i].district_kh+'</td>'; 
                        bodyResult += '<td>'+patients[i].commune_kh+'</td>'; 
                        bodyResult += '<td>'+patients[i].village_kh+'</td>';
                        bodyResult += '<td>'+country_name+'</td>';
                        bodyResult += '<td>'+nationality_en+'</td>';
                        bodyResult += '<td>'+date_arrival+'</td>';
                        bodyResult += '<td>'+passport_number+'</td>';
                        bodyResult += '<td>'+flight_number+'</td>';
                        bodyResult += '<td>'+seat_number+'</td>';
                        bodyResult += '<td>'+sample_source+'</td>';
                        bodyResult += '<td>'+patients[i].collected_date+'</td>';
                        bodyResult += '<td>'+patients[i].number_of_sample+'</td>';     
                        bodyResult += '<td>'+sample_collector+'</td>';
                        bodyResult += '<td>'+phone_number+'</td>';  
                        bodyResult += '<td>'+for_labo+'</td>';
                        bodyResult += '</tr>';
                        n++;
                    }
                }

                setTimeout(function(){
                    $("#btnPullData").removeClass('disabled');
                    myDialog.showProgress('hide');
                    $("table[name=tblListPatient] tbody").html(bodyResult); 
                    if(patients.length > 0){                       
                        $("#btnImportPatient").removeClass("disabled");
                    }
                    $modalPatientTmp.modal("show");
                }, 1000);

            },
            error: function(xhr, status, error) {
                var err = eval("(" + xhr.responseText + ")");
                console.log(err.Message);
                console.log(xhr.responseText);
            }
        });
    })
    $("#btnOpenRrtFormModal").on("click", function (evt) {    
        $modalPatientTmp.modal("show");    
    })
    $("#btnImportPatient").on("click",function(evt){
        $(this).addClass('disabled');
        myDialog.showProgress('show');
        var rNum = 0;
        for(var i in PATIENT_RRT){

            var sex                 = PATIENT_RRT[i].sex == "M" ? "ប្រុស" :  "ស្រី";
            var phone               = (PATIENT_RRT[i].phone == null) ? "" : PATIENT_RRT[i].phone;
            var residence           = (PATIENT_RRT[i].residence == null) ? "": PATIENT_RRT[i].residence;
            var country_name        = (PATIENT_RRT[i].country_name_en) == null ? "": PATIENT_RRT[i].country_name_en;
            var nationality_en      = (PATIENT_RRT[i].nationality_en) == null ? "": PATIENT_RRT[i].nationality_en;
            var date_arrival        = (PATIENT_RRT[i].date_arrival) == null ? "": PATIENT_RRT[i].date_arrival;
            var passport_number     = (PATIENT_RRT[i].passport_number) == null ? "":PATIENT_RRT[i].passport_number;
            var flight_number       = (PATIENT_RRT[i].flight_number) == null ? "": PATIENT_RRT[i].flight_number;
            var seat_number         = (PATIENT_RRT[i].seat_number) == null ? "": PATIENT_RRT[i].seat_number;
            var sample_source       = (PATIENT_RRT[i].sample_source) == null ? "": PATIENT_RRT[i].sample_source;
            var sample_collector    = (PATIENT_RRT[i].sample_collector) == null ? " ": PATIENT_RRT[i].sample_collector;
            var phone_number        = (PATIENT_RRT[i].phone_number) == null ? "": PATIENT_RRT[i].phone_number;            

            //line_list_table.setValue(jspreadsheet.getColumnNameFromId([patient__col, rNum]), PATIENT_RRT[i].name);
            line_list_table.setValue(jspreadsheet.getColumnNameFromId([patient_name_col, rNum]), PATIENT_RRT[i].name);
            line_list_table.setValue(jspreadsheet.getColumnNameFromId([gender_col, rNum]), sex);
            line_list_table.setValue(jspreadsheet.getColumnNameFromId([age_col, rNum]), PATIENT_RRT[i].age);
            line_list_table.setValue(jspreadsheet.getColumnNameFromId([phone_col, rNum]), phone);
            line_list_table.setValue(jspreadsheet.getColumnNameFromId([province_col, rNum]), PATIENT_RRT[i].province_kh);
            line_list_table.setValue(jspreadsheet.getColumnNameFromId([arrival_date_col, rNum]), date_arrival);
            line_list_table.setValue(jspreadsheet.getColumnNameFromId([district_col, rNum]), PATIENT_RRT[i].district_kh);
            line_list_table.setValue(jspreadsheet.getColumnNameFromId([commune_col, rNum]), PATIENT_RRT[i].commune_kh);
            line_list_table.setValue(jspreadsheet.getColumnNameFromId([village_col, rNum]), PATIENT_RRT[i].village_kh);
            line_list_table.setValue(jspreadsheet.getColumnNameFromId([residence_col, rNum]), residence);          
            line_list_table.setValue(jspreadsheet.getColumnNameFromId([sample_source_col, rNum]), sample_source);            
            line_list_table.setValue(jspreadsheet.getColumnNameFromId([collected_date_col, rNum]), PATIENT_RRT[i].collected_date);            
            line_list_table.setValue(jspreadsheet.getColumnNameFromId([sample_collector_col, rNum]), sample_collector);
            line_list_table.setValue(jspreadsheet.getColumnNameFromId([phone_number_sample_collector, rNum]),phone_number);
            line_list_table.setValue(jspreadsheet.getColumnNameFromId([country_col, rNum]), country_name);
            line_list_table.setValue(jspreadsheet.getColumnNameFromId([nationality_col, rNum]), nationality_en);
            line_list_table.setValue(jspreadsheet.getColumnNameFromId([passport_number_col, rNum]), passport_number);
            line_list_table.setValue(jspreadsheet.getColumnNameFromId([flight_number_col, rNum]), flight_number);
            line_list_table.setValue(jspreadsheet.getColumnNameFromId([seat_number_col, rNum]), seat_number);
            line_list_table.setValue(jspreadsheet.getColumnNameFromId([number_of_sample_col, rNum]), PATIENT_RRT[i].number_of_sample);

            rNum++;
        }
        setTimeout(function () {
            myDialog.showProgress('hide');
            $modalPatientTmp.modal("hide");
            $("table[name=tblListPatient] tbody").html(""); // clear
            $("#btnImportPatient").removeClass('disabled');
            // Update samples

        }, 1000);
    })
    /**
     * Added 30-04-2021
     */
    // array of column name

    var col = {
        patient_code        : 0,
        patient_name        : 1,
        age                 : 2,
        gender              : 3,
        phone               : 4,
        province            : 5,
        district            : 6,
        commune             : 7,
        village             : 8,
        residence           : 9,
        reason_for_testing  : 10,
        is_contacted        : 11,
        is_contacted_with   : 12,
        is_directed_contact : 13,
        sample_number       : 14,
        sample_source       : 15,
        requester           : 16,
        collected_date      : 17,
        received_date       : 18,
        payment_type        : 19,
        admission_date      : 20,
        diagnosis           : 21,
        is_urgent           : 22,
        completed_by        : 23,
        phone_completed_by  : 24,
        sample_collector    : 25,
        phone_number_sample_collctor : 26,
        clinical_symptom    : 27,
        test_name           : 28,
        test_result         : 29,
        test_result_date    : 30,
        perform_by          : 31,
        country             : 32,
        nationality         : 33,
        arrival_date        : 34,
        passport_number     : 35,
        flight_number       : 36,
        seat_number         : 37,
        is_positive_covid   : 38,
        test_date           : 39,
        sex_id              : 40,
        province_code       : 41,
        district_code       : 42,
        commune_code        : 43,
        village_code        : 44,
        reason_for_testing_id : 45,
        sample_source_id    : 46,
        requester_id        : 47,
        clinical_symtop_id  : 48,
        test_id             : 49,
        country_id          : 50,
        nationality_id      : 51,
        number_of_sample    : 52,
        performer_by_id     : 53
    }
    var districtFilter2 = function(instance, cell, c, r, source) {
        var value = instance.jexcel.getValueFromCoords(col["province"], r);
        var res = [];
        // get province id 
        
        $.each(PROVINCES, function(key, row) {            
            if(row.name_kh == value){
                province_code = row.code;
                return false;
            }
        });        
        $.each(DISTRICTS, function(key, item) {
            if(province_code == item.province_code){
                res.push(item.name_kh);
            }
        });        
        return res;
    }
    
    var communeFilter2 = function(instance, cell, c, r, source) {
        var value         = instance.jexcel.getValueFromCoords(col["district"], r);
        var res           = [];
        var province_code =  instance.jexcel.getValueFromCoords(col["province_code"], r);
        $.each(DISTRICTS, function(key, row) {
            if(row.name_kh == value && row.province_code == province_code){
                district_code = row.code;
                return false;
            }
        });
        $.each(COMMUNES, function(key, item) {
            if(district_code == item.district_code){
                res.push(item.name_kh);
            }
        });
        return res;
    }
    
    var villageFilter2 = function(instance, cell, c, r, source) {
        var value       = instance.jexcel.getValueFromCoords(col["commune"], r);
        var res         = [];
        var district_code =  instance.jexcel.getValueFromCoords(col["district_code"], r); // district_code
        $.each(COMMUNES, function(key, row) {
            if(row.name_kh == value && row.district_code == district_code){
                commune_code = row.code;
                return false;
            }
        });        
        $.each(VILLAGES, function(key, item) {
            if(commune_code == item.commune_code){
                //res.push({id:item.code, name: item.name_kh});
                res.push(item.name_kh);
            }
        });
        return res;
    }
    
    var requesterFilter2 = function(instance, cell, c, r, source) {
        var value = instance.jexcel.getValueFromCoords(c - 1, r);
        var sample_source =  instance.jexcel.getValueFromCoords(col["sample_source_id"], r);
        var res = [];        
        $.each(REQUESTER, function(key, item) {
            if(sample_source == item.sample_source_id){
                res.push(item.requester_name);
            }
        });
        return res;
    }
   //console.log(PERFORMERS);
   
    $("#btnAddPatientsNew").on("click", function (evt) {
        var $modal_excel_full_form = $("#modal-excel-full-form"); // added 19-03-2021
        $modal_excel_full_form.modal("show");
    });
    var line_list_table_new = jspreadsheet(document.getElementById('spreadsheetNew'), {
        minDimensions: [ 24, 500 ],
        defaultColWidth: 100,
        tableOverflow: true,
        tableHeight: "500px",
        columns: [
            { type:'text', title: label_patient_id,width:140 ,maxlength:20},
            { type:'text', title: label_patient_name+'*' ,maxlength:60},
            { type:'numeric', title: label_patient_age+'*',width:40 , maxlength:3 },
            { type:'dropdown', title:label_sex+'*',width:40, source:["ប្រុស" , "ស្រី"] },
            { type:'text', title: label_patient_phone_number,maxlength:100 },
            { type: 'autocomplete', title: label_province+'*', width:100, source:province_array },
            { type: 'autocomplete', title: label_district+'*', width:100, source:districts_array , filter: districtFilter2},
            { type: 'autocomplete', title: label_commune+'*', width:100, source:communes_array , filter: communeFilter2},
            { type: 'autocomplete', title: label_village+'*', width:100, source:villages_array ,filter: villageFilter2},
            { type:'text', title: label_residence ,width:80 ,maxlength:100},
            { type: 'autocomplete', title: label_reason_for_testing, width:120, source:reason_for_testing_array },
            { type:'checkbox', title: label_yes, width:100 },
            { type:'text', title: label_contact_with ,readOnly:true , maxlength:60 },
            { type:'checkbox', title: label_is_direct_contact },
            { type: 'text', title: label_sample_number, width:120 ,maxlength:11},
            { type: 'dropdown', title: label_sample_source+'*', width:100, source:sample_source_array },
            { type: 'dropdown', title:label_requester+'*', width:120, source:requester_array, filter: requesterFilter2 },
            { type: 'calendar', title:label_collect_dt+'*', width:170,options: { format:'YYYY-MM-DD' , time:true , readonly:true, validRange: [ '2021-01-01', today ] } },
            { type: 'calendar', title:label_receive_dt+'*', width:170,options: { format:'YYYY-MM-DD' , time:true , readonly:true , validRange: [ '2021-01-01', today ] } },
            { type: 'autocomplete', title:label_payment_type+'*', width:120, source:payment_type_array },
            { type: 'calendar', title:'ថ្ងៃចូលសម្រាកពេទ្យ', width:120,options: { format:'YYYY-MM-DD' , time:true , readonly:true} },
            { type:'text', title: label_clinical_history,width:80 , maxlength: 60},
            { type:'checkbox', title: label_urgent,width:50 },

            { type:'text', title: label_completed_by , width:160 , maxlength:50},
            { type:'text', title: label_phone ,maxlength:10 },
            { type:'text', title: label_sample_collector , maxlength:50 },
            { type:'text', title: label_phone , maxlength:10 },
            { type: 'dropdown', title: label_clinical_symtom, width:150, source:clinical_symptom_array , autocomplete:true, multiple:true},
            { type: 'dropdown', title: label_test_name, width:150, source:['SARS-CoV-2 (Method: real time RT-PCR by GeneXpert)', 'SARS-CoV-2 (Applied Biosystems 7500 Fast Real Time PCR)','SARS-CoV-2 Rapid Antigen Test']},
            { type: 'dropdown', title: label_result, width:70, source:['Negative', 'Positive'], readOnly: true},
            { type: 'calendar', title: label_test_date, width:150 , readOnly: true, options: { format:'YYYY-MM-DD' , readonly:true , validRange: [ '2021-01-01', today ]}},
            { type: 'dropdown', title: label_performed_by, width:120, source:performer_array, readOnly: true},
            
            { type: 'autocomplete', title: label_country, width:80, source:country_array },        
            { type: 'autocomplete', title: label_nationality, width:80, source:nationalities_array },            
            { type: 'calendar', title: label_date_of_arrival, width: 100,options: { format:'YYYY-MM-DD' , readonly:true } },
            { type:'text', title: label_passport_no, width:80 , maxlength:20},
            { type:'text', title: label_flight_number ,maxlength:5 },
            { type:'text', title: label_seat_no, width:60, maxlength:5 },
            { type:'checkbox', title: label_yes,width:60},
            { type:'calendar', title: label_test_covid_date, readOnly:true, width:90,options: { format:'YYYY-MM-DD' , readonly:true , validRange: [ '2021-01-01', today ]} },
            /** Hidden Column */
            /** Store code  */
            { type:'text', title:'sex'}, 
            { type:'text', title:'province_code'},
            { type:'text', title:'district_code'}, 
            { type:'text', title:'commune_code'},
            { type:'text', title:'village_code'},
            { type:'text', title:'reason_for_testing'}, 
            { type:'text', title:'sample_source_id'}, 
            { type:'text', title:'requester_id'},
            { type:'text', title:'clinical_symtom_id'}, 
            { type:'text', title:'test_id'}, 
            { type:'text', title:'country_id'}, 
            { type:'text', title:'nationality_id'}, 
            { type:'dropdown', title: label_number_of_sample ,width:120,source:[1,2,3,4,5,6,7,8,9,10]},
            { type:'text', title:'performer_by_id'},
        ],
        nestedHeaders:[
            [
                {
                    title: label_patient_info,
                    colspan: '14',
                },
                {
                    title: label_sample_info,
                    colspan: '16',
                },
                {
                    title: label_patient_info,
                    colspan: '8',
                },
                {
                    title: label_sample_info,
                    colspan: '1',
                }
            ],
            [
                {
                    title: label_patient,
                    colspan: '5',
                },
                {
                    title: label_address,
                    colspan: '6',
                },
                {
                    title: label_if_contact,
                    colspan: '3'
                },
                {
                    title: label_sample,
                    colspan: '12'
                },
                {
                    title: label_test_result,
                    colspan: '4'
                },
                {
                    title: label_patient_info,
                    colspan: '6'
                },
                {
                    title: label_history_of_covid19_history,
                    colspan: '2'
                },
                {
                    title: label_sample,
                    colspan: '1'
                }
            ],
        ],
        onchange:function(instance,cell, c, r, value) {
            // patient_code
            //console.log("Col= "+c);
            if( c == 0){
                var rowNumber = r;
                var totalCol = 20;
                var i;
                var nCol = 1;                
                if(value !== ""){
                    //console.log(value);
                    // search if patient_code existent
                    var patient_code = value;
                    $.ajax({
                        url: base_url + 'patient/search/' + patient_code,
                        type: 'POST',
                        data: {pid: patient_code},
                        dataType: 'json',
                        success: function (resText) {
                            //console.log(resText);
                            var patient = resText.patient;
                           //console.log(patient);
                            if(patient){
                                //console.log(patient);
                                //console.log("patient_code "+patient.patient_code);
                                var dob = "";
                                var sex = (patient.sex == 'M') ? "ប្រុស" : "ស្រី";
                                var dob = moment(patient.dob, 'YYYY-MM-DD');
                                var age = calculateAge(dob);
                                
                                var is_positive_covid   = (patient.is_positive_covid == null || patient.is_positive_covid == undefined || patient.is_positive_covid == false) ? false : true;
                                var is_contacted        = (patient.is_contacted == null || patient.is_contacted == undefined) ? false : true;
                                var contact_with        = (patient.contact_with == null || patient.contact_with == undefined) ? "" : patient.contact_with;
                                var is_direct_contact   = (patient.is_direct_contact == null || patient.is_direct_contact == undefined) ? false : true;
                                var test_date           = (patient.test_date == undefined || patient.test_date == null) ? "" : patient.test_date;
                                var name                = patient.name;
                                var residence           = (patient.residence == undefined || patient.residence == null) ? "" : patient.residence;
                                var date_arrival        = (patient.date_arrival == undefined || patient.date_arrival == null) ? "" : patient.date_arrival;
                                var country             = (patient.country_name_en == undefined || patient.country_name_en == null) ? "" : patient.country_name_en;
                                var nationality         = (patient.nationality_en == undefined || patient.nationality_en == null) ? "" : patient.nationality_en;
                                var passport_number     = (patient.passport_number == undefined || patient.passport_number == null) ? "" : patient.passport_number;
                                var flight_number       = (patient.flight_number == undefined || patient.flight_number == null) ? "" : patient.flight_number;
                                var seat_number         = (patient.seat_number == undefined || patient.seat_number == null) ? "" : patient.seat_number;
                                var phone               = (patient.phone == undefined || patient.phone == null) ? "" : (patient.phone).trim();
                                //var phone               = patient.phone;
                                
                                
                                //var name            = "name";
                                //var patient_code    = "patient code";
                                var rowData = [                                                                        
                                    name,
                                    age.years,
                                    sex,
                                    phone,
                                    patient.province_kh,
                                    patient.district_kh,
                                    patient.commune_kh,
                                    patient.village_kh,
                                    residence
                                ];
                                
                                for(i = 0 ; i < 9;i++){
                                    var nameColumn = jspreadsheet.getColumnNameFromId([c+nCol, r]);
                                    instance.jexcel.setValue(nameColumn, rowData[i]);
                                    nCol++;
                                }
                                                               
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["is_contacted"], r]), is_contacted);
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["is_contacted_with"], r]), contact_with);                            
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["is_directed_contact"], r]), is_direct_contact);
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["country"], r]), country);
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["nationality"], r]), nationality);
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["arrival_date"], r]), date_arrival);
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["passport_number"], r]), passport_number);
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["flight_number"], r]), flight_number);
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["seat_number"], r]), seat_number);
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["is_positive_covid"], r]), is_positive_covid);                            
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["test_date"], r]), test_date);

                            }else{
                                // Reset columns
                                nCol = 1;
                                for(i = 0 ; i < 9;i++){
                                    var nameColumn = jspreadsheet.getColumnNameFromId([c+nCol, r]);
                                    instance.jexcel.setValue(nameColumn, "");
                                    nCol++;
                                }                                
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["is_contacted"], r]), false);
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["is_contacted_with"], r]), "");                            
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["is_directed_contact"], r]), false);
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["country"], r]), "");
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["nationality"], r]), "");
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["arrival_date"], r]), "");
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["passport_number"], r]), "");
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["flight_number"], r]), "");
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["seat_number"], r]), "");
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["is_positive_covid"], r]), false);                            
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["test_date"], r]), "");
                                
                            }
                        }
                    })
                }else{
                    // Reset columns
                    nCol = 1;
                    for(i = 0 ; i < 9;i++){
                        var nameColumn = jspreadsheet.getColumnNameFromId([c+nCol, r]);
                        instance.jexcel.setValue(nameColumn, "");
                        nCol++;
                    }                                
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["is_contacted"], r]), false);
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["is_contacted_with"], r]), "");                            
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["is_directed_contact"], r]), false);
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["country"], r]), "");
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["nationality"], r]), "");
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["arrival_date"], r]), "");
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["passport_number"], r]), "");
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["flight_number"], r]), "");
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["seat_number"], r]), "");
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["is_positive_covid"], r]), false);                            
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["test_date"], r]), "");
                }
            }
            // sex column
            if(c == col["gender"]){
                if(value !== ""){
                    // save id of sex in column 35
                    sex = value == 'ប្រុស' ? 1 : 2;                    
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["sex_id"], r]),sex);
                }
            }
            // province column
            if (c == col["province"]) {
                if(value !== ""){
                    // set null value to district col                    
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["district"], r]), "");
                    code = getProvinceCode(value);                    
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["province_code"], r]),code); // save province code in column 38
                }               
            }
            // district column
            if (c == col["district"]) {
                if(value !== ""){
                    // set null value to commune 
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["commune"], r]), "");
                    province_code = line_list_table_new.getValueFromCoords(col["province_code"],r);                    
                    code = getDistrictCode(value, province_code);
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["district_code"], r]), code);
                }
            }
            // commune column
            if (c == col["commune"]) {
                if(value !== ""){
                    // set null value to village                     
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["village"], r]), "");
                    district_code = line_list_table_new.getValueFromCoords(col["district_code"],r);
                    code = getCommuneCode(value, district_code);                    
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["commune_code"], r]), code);
                }
            }
            // village column
            if (c == col["village"]) {
                if(value !== ""){
                    commune_code = line_list_table_new.getValueFromCoords(col["commune_code"],r);
                    code = getVillageCode(value, commune_code);
                    //console.log("get village code "+code);                    
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["village_code"], r]), code);
                }
            }
            // reason for testing column
            if (c == col["reason_for_testing"]) {
                if(value !== ""){
                    code = getReason(value);
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["reason_for_testing_id"], r]), code);
                }
            }
            // is contacted column
            if (c == col["is_contacted"]) {
                var is_contacted_column = jspreadsheet.getColumnNameFromId([col["is_contacted_with"], r]);
                var is_direct_contact_column = jspreadsheet.getColumnNameFromId([col["is_directed_contact"], r]);
                //var c = line_list_table_new.getCell(is_direct_contact_column);
                //console.log(c);
                if(value){
                    //console.log("true is right");
                   line_list_table_new.setReadOnly(is_contacted_column,false);
                   line_list_table_new.setReadOnly(is_direct_contact_column,false);
                }else{
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["is_contacted_with"], r]),"");
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["is_directed_contact"], r]),"");
                   line_list_table_new.setReadOnly(is_contacted_column,true);
                   line_list_table_new.setReadOnly(is_direct_contact_column,true);
                }
            }
            // sample source column
            if (c == col["sample_source"]) {
                if(value !== ""){
                    // set
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["requester"], r]),"");
                    // save id of sample source in column 43
                    code = getSampleSource(value);
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["sample_source_id"], r]), code);
                }
            }

            // requester column
            if (c == col["requester"]) {
                if(value !== ""){
                    sample_source_id = line_list_table_new.getValueFromCoords(col["sample_source_id"],r);
                    code = getRequester(value,sample_source_id);
                    var columnName = jspreadsheet.getColumnNameFromId([col["requester_id"], r]);
                    instance.jexcel.setValue(columnName, code);
                }
            }
            // clinical symptom column
            if (c == col["clinical_symptom"]) {
                if(value !== ""){
                    code = getClinicalSymptom(value);
                    //console.log(code);
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["clinical_symtop_id"], r]), code);
                }
            }
            // Test column
            if (c == col["test_name"]) {
                var test_result_col         = jspreadsheet.getColumnNameFromId([col["test_result"], r]);
                var test_result_date_col    = jspreadsheet.getColumnNameFromId([col["test_result_date"], r]);
                var perform_by_col          = jspreadsheet.getColumnNameFromId([col["perform_by"], r]);
                if(value !== ""){
                    code = getTest(value);
                    //console.log("Test ID "+ code);
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["test_id"], r]), code);
                    // remove read only from result, test date and perform_by column
                    line_list_table_new.setReadOnly(test_result_col,false);
                    line_list_table_new.setReadOnly(test_result_date_col,false);
                    line_list_table_new.setReadOnly(perform_by_col,false);
                }else{
                    // reset value and set readonly
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["test_result"], r]), "");
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["test_result_date"], r]), "");
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["perform_by"], r]), "");

                    line_list_table_new.setReadOnly(test_result_col,true);
                    line_list_table_new.setReadOnly(test_result_date_col,true);
                    line_list_table_new.setReadOnly(perform_by_col,true);
                }
            }
            if (c == col["perform_by"]){
                if(value !== ""){
                    code = getPerformer(value);
                    //console.log("performer id "+code);  
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["performer_by_id"], r]), code);
                }
            }
            // country column
            if (c == col["country"]) {
                if(value !== ""){
                   code = getCountry(value);
                   instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["country_id"], r]), code);
                }
            }
            // nationality column
            if (c == col["nationality"]) {
                if(value !== ""){                    
                    code = getNationality(value);
                    //console.log("Nationality code: "+code);                    
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["nationality_id"], r]), code);
                }
            }
            // if check is true, enable Test Date
            if (c == col["is_positive_covid"]) {
                //console.log(value);
                var colummnName = jspreadsheet.getColumnNameFromId([col["test_date"], r]);
                if(value){
                   line_list_table_new.setReadOnly(colummnName,false);
                }else{
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["test_date"], r]), "");
                    line_list_table_new.setReadOnly(colummnName,true);
                }
            }                    
        },
        oncreateeditor: function(el, cell, x, y) {
            if (x == col["phone"] 
                || x == col["age"] 
                || x == col["patient_name"] 
                || x == col["patient_code"] 
                || x == col["residence"] 
                || x == col["sample_source"] 
                || x == col["diagnosis"] 
                || x == col["completed_by"]  
                || x == col["phone_completed_by"] 
                || x == col["sample_collector"] 
                || x == col["phone_number_sample_collctor"] 
                || x == col["passport_number"] 
                || x == col["flight_number"] 
                || x == col["seat_number"]
            ){
            var config = el.jexcel.options.columns[x].maxlength;
                cell.children[0].setAttribute('maxlength' , config); // set maxlength to column
            }
        }
    });
    
    line_list_table_new.hideColumn(col["payment_type"]); // hide payment type
    line_list_table_new.hideColumn(col["admission_date"]); // hide admision column
    
    line_list_table_new.hideColumn(col["sex_id"]);
    line_list_table_new.hideColumn(col["test_id"]);
    line_list_table_new.hideColumn(col["country_id"]);
    line_list_table_new.hideColumn(col["nationality_id"]);
    line_list_table_new.hideColumn(col["performer_by_id"]);
    line_list_table_new.hideColumn(col["province_code"]);
    line_list_table_new.hideColumn(col["district_code"]);
    line_list_table_new.hideColumn(col["commune_code"]);
    line_list_table_new.hideColumn(col["village_code"]);
    line_list_table_new.hideColumn(col["reason_for_testing_id"]);
    line_list_table_new.hideColumn(col["sample_source_id"]);
    line_list_table_new.hideColumn(col["requester_id"]);
    line_list_table_new.hideColumn(col["clinical_symtop_id"]);

    // 17 04 2021
    // For testing
    
    var $modal_list_error = $("#modal_error_line_list_new");
    $("#btnSaveListNew").on("click", function (evt) {
        $(this).addClass('disabled btn-progress'); //prevent multiple click
        myDialog.showProgress('show', { text : msg_loading });

        var line_list_data  = line_list_table_new.getData();
        var data            = [];
        var require_string  = "";
        var valid_check     = 0;
        var array_check     = [];
        var currentDate     = new Date();
        //console.log(valid_check);
        for(var i in line_list_data){
            var name = line_list_data[i][col["patient_name"]];
            var namelenght = name.length;
            //console.log(" name "+ name.trim());
            //console.log(" namelenght "+ namelenght);
            var check_name = false;
            if(name.trim() !== ""){
                if(valid_check !== 1){
                    valid_check     = 1;
                }
                
                // age column
                var age         = line_list_data[i][col["age"]];
                var check_age   = false;
                require_string += "<tr>";
                require_string += "<td>"+line_list_data[i][col["patient_code"]]+"</td>";

                if(name.length > 60){
                     require_string += '<td class="text-danger">'+msg["not_greater_than_60"]+'</td>';
                }else{
                    require_string += "<td>"+name+"</td>";
                    check_name = true;
                }

               //console.log("age "+age);
                if(age == ""){
                    require_string += '<td class="text-danger">'+msg["not_fill"]+'</td>';
                }else{
                    if(age.length > 3){
                        require_string += '<td class="text-danger">'+msg["not_greater_than_3"]+'</td>';
                    }else{
                        var c               = String(age).substr(age.length - 1, age.length);
                        var patternNumber   = /^\d+$/; // number only
                        var isNumber        = patternNumber.test(c);  // returns a boolean
                        //console.log("Char :"+ c);
                        if(isNumber == false){
                            var c_arr = ['m','M','d','D'];
                            if(c_arr.indexOf(c) == '-1'){
                                require_string += '<td class="text-danger">'+msg["not_correct_format"]+'</td>';
                            }else{
                                //console.log(c_arr+" "+age.length);
                                if(age.length == 1){
                                    require_string += '<td class="text-danger">'+msg["not_correct_format"]+'</td>';
                                }else{
                                    var nb = age.substr(0, age.length - 1);
                                    nb = parseInt(nb);
                                    if(c == "m" || c == "M"){
                                        if(nb > 12){
                                            require_string += '<td class="text-danger">'+msg['month_not_greater_than_12']+'/td>';
                                        }else{
                                            require_string += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                                            check_age = true;
                                        }
                                    }else if(c == "d" || c == "D"){
                                        if(nb > 31){
                                            require_string += '<td class="text-danger">'+msg["day_not_greater_than_31"]+'</td>';
                                        }else{
                                            require_string += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                                            check_age = true;
                                        }
                                    }
                                }
                            }
                        }else{
                            var pattern = /^\d+$/; // number only
                            var r = pattern.test(age);  // returns a boolean
                            if(r == false){
                                require_string += '<td class="text-danger">លេខឡាតាំងតែប៉ុណ្ណោះ</td>';
                            }else{
                                require_string += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                                check_age = true;
                            }
                        }
                    }
                }
                //console.log("age "+age+" "+check_age);
                // Gender column

                var gender       = line_list_data[i][col["gender"]];
                var check_gender = false;
                var gender_id    = line_list_data[i][col["sex_id"]];
            
                if(gender.length == 0 || gender_id.length == 0){
                    require_string += '<td class="text-danger">'+msg["not_select"]+'</td>';
                }else{
                    require_string += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                    var check_gender = true;
                }
                //console.log("Gender: "+gender+" "+check_gender+" "+gender.length);
                var phone        = line_list_data[i][col["phone"]];
                var check_phone  = false;

                if(phone !== null){
                    if(phone.length > 100){
                        require_string += '<td class="text-danger">'+msg["not_greater_than_100"]+'</td>';
                    }else{
                        require_string += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                        check_phone = true;
                    }
                }else{
                    require_string += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                    check_phone = true;
                }
                // Province column
                var province        = line_list_data[i][col["province"]];
                var check_province  = false;
                var province_id     = line_list_data[i][col["province_code"]];
                if(province.length == 0 || province_id.length == 0){
                    require_string += '<td class="text-danger">'+msg["not_select"]+'</td>';
                }else{
                    require_string += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                    check_province = true;
                }
                //console.log("province: "+province+" "+check_province);

                var district        = line_list_data[i][col["district"]];
                var check_district  = false;
                var district_id     = line_list_data[i][col["district_code"]];
                if(district.length == 0 || district_id.length == 0){
                    require_string += '<td class="text-danger">'+msg["not_select"]+'</td>';
                }else{
                    require_string += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                    check_district = true;
                }
                //console.log("district: "+district+" "+check_district);
                // Commune
                var commune         = line_list_data[i][col["commune"]];
                var check_commune   = false;
                var commune_id      = line_list_data[i][col["commune_code"]];
                if(commune.length == 0 || commune_id.length == 0){
                    require_string += '<td class="text-danger">'+msg["not_select"]+'</td>';
                }else{
                    require_string += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                    check_commune = true;
                }
                //console.log("commune: "+commune+" "+check_commune);
                //village 
                var village         = line_list_data[i][col["village"]];
                var check_village   = false;
                var village_id      = line_list_data[i][col["village_code"]];
                if(village.length == 0 || village_id.length == 0){
                    require_string += '<td class="text-danger">'+msg["not_select"]+'</td>';
                }else{
                    require_string += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                    check_village = true;
                }
                //console.log("village: "+village+" "+check_village);
                // sample source
                var sample_source       = line_list_data[i][col["sample_source"]];
                var check_sample_source = false;
                var sample_source_id    = line_list_data[i][col["sample_source_id"]];

                //console.log("samsource_id "+ " "+sample_source_id);
                if(sample_source.length == 0 || sample_source_id.length == 0){
                    require_string += '<td class="text-danger">'+msg["not_select"]+'</td>';
                }else{
                    check_sample_source = true;
                    require_string += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                }
                //console.log("sample_source: "+sample_source+" "+check_sample_source+", samsource_id: "+sample_source_id.length);
                // requester
                var requester       = line_list_data[i][col["requester"]];
                var check_requester = false;
                var requester_id    = line_list_data[i][col["requester_id"]];
                if(requester.length == 0 || requester_id.length == 0){
                    require_string += '<td class="text-danger">'+msg["not_select"]+'</td>';
                }else{
                    require_string += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                    check_requester = true;
                }
                //console.log("requester: "+requester+" "+check_requester);

                // collection date
                var collection_date         = line_list_data[i][col["collected_date"]];
                var check_collection_date   = false;
                if(collection_date == ""){
                    require_string += '<td class="text-danger">'+msg["not_select"]+'</td>';
                }else{
                    require_string += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                    check_collection_date = true;
                }
                //received_date
                var recieved_date       = line_list_data[i][col["received_date"]];
                var check_recieved_date = false;
                if(recieved_date == ""){
                    require_string += '<td class="text-danger">'+msg["not_select"]+'</td>';
                }else{
                    var d1 = new Date(recieved_date);
                    var d2 = new Date(collection_date);
                
                    if(d1.getTime() < d2.getTime()){
                        require_string += '<td class="text-danger">'+msg["greater_or_equal_collected_date"]+'</td>';
                    }else{
                        require_string += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                        check_recieved_date = true;
                    }
                }
                // For test name
                var test_name               = line_list_data[i][col["test_name"]];
                
                var check_test_result       = false;
                var check_test_result_date  = false;
                var check_perform_by        = false;
                
                if(test_name.length > 0){
                    require_string  += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                    check_test_result = true;

                    var test_result         = line_list_data[i][col["test_result"]];

                    var test_result_date    =  line_list_data[i][col["test_result_date"]];
                    check_test_result_date  = false;
                    var perform_by          = line_list_data[i][col["perform_by"]];
                    var perform_by_id       = line_list_data[i][col["performer_by_id"]];
                    check_perform_by        = false;

                    if((test_result.length == 0) && (test_result_date == "") && (perform_by.length == 0 || perform_by_id.length == 0) ){
                        require_string += '<td>&nbsp;</td>';
                        require_string += '<td>&nbsp;</td>';
                        require_string += '<td>&nbsp;</td>';
                        check_test_result       = true;
                        check_test_result_date  = true;
                        check_perform_by        = true;
                    }else if (test_result.length > 0 && (test_result_date == "") && (perform_by.length == 0 || perform_by_id.length == 0)){
                        require_string  += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                        check_test_result = true;

                        require_string += '<td class="text-danger">'+msg["not_select"]+'</td>';
                        check_test_result_date       = false;

                        require_string += '<td class="text-danger">'+msg["not_select"]+'</td>';
                        check_perform_by = false;
                    }else if(test_result.length == 0 && (test_result_date !== "") && (perform_by.length == 0 || perform_by_id.length == 0)){
                        require_string += '<td class="text-danger">'+msg["not_select"]+'</td>';
                        check_test_result = false;

                        tr_date = new Date(test_result_date);
                        if( tr_date > currentDate){                            
                            require_string += '<td class="text-danger">'+msg["not_greater_than_current_date"]+'</td>';
                            check_test_result_date = false;
                        }else{
                            require_string  += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                            check_test_result_date       = true;
                        }
                        
                        require_string += '<td class="text-danger">'+msg["not_select"]+'</td>';
                        check_perform_by = false;

                    }else if(test_result.length == 0 && (test_result_date == "") && (perform_by.length > 0 || perform_by_id.length > 0)){
                        require_string += '<td class="text-danger">'+msg["not_select"]+'</td>';
                        check_test_result = false;

                        require_string += '<td class="text-danger">'+msg["not_select"]+'</td>';
                        check_test_result_date       = false;

                        require_string  += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                        check_perform_by       = true;
                    }else if(test_result.length > 0 && (test_result_date !== "") && (perform_by.length == 0 || perform_by_id.length == 0)){
                        require_string  += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                        check_test_result = true;

                        tr_date = new Date(test_result_date);
                        if( tr_date > currentDate){                            
                            require_string += '<td class="text-danger">'+msg["not_greater_than_current_date"]+'</td>';
                            check_test_result_date = false;
                        }else{
                            require_string  += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                            check_test_result_date       = true;
                        }

                        require_string += '<td class="text-danger">'+msg["not_select"]+'</td>';
                        check_perform_by = false;
                    }else if(test_result.length > 0 && (test_result_date == "") && (perform_by.length > 0 || perform_by_id.length > 0)){
                        require_string  += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                        check_test_result = true;

                        require_string += '<td class="text-danger">'+msg["not_select"]+'</td>';
                        check_test_result_date       = false;

                        require_string  += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                        check_perform_by = true;
                    }else if(test_result.length == 0 && (test_result_date !== "") && (perform_by.length > 0 || perform_by_id.length > 0)){
                        require_string += '<td class="text-danger">'+msg["not_select"]+'</td>';
                        check_test_result = false;

                        tr_date = new Date(test_result_date);
                        if( tr_date > currentDate){
                            require_string += '<td class="text-danger">'+msg["not_greater_than_current_date"]+'</td>';
                            check_test_result_date = false;
                        }else{
                            require_string  += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                            check_test_result_date       = true;
                        }

                        require_string  += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                        check_perform_by = true;
                    }else{
                        require_string  += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                        check_test_result = true;

                        tr_date = new Date(test_result_date);
                        if( tr_date > currentDate){
                            require_string += '<td class="text-danger">'+msg["not_greater_than_current_date"]+'</td>';
                            check_test_result_date = false;
                        }else{
                            require_string  += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                            check_test_result_date       = true;
                        }                        

                        require_string  += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                        check_perform_by = true;
                    }

                }else{
                    require_string += '<td>&nbsp;</td>';
                    require_string += '<td>&nbsp;</td>';
                    require_string += '<td>&nbsp;</td>';
                    require_string += '<td>&nbsp;</td>';

                    check_test_result       = true;
                    check_test_result_date  = true;
                    check_perform_by        = true;

                }
                //console.log("test name "+test_name);

                //07-05-2021
                // Extra Check length
                var extraErrString = '';
                var extraErrCheck  = true;
                var residence      =  line_list_data[i][col["residence"]];
                // convert to string in order to trim();
                if(residence !== ""){
                    residence = residence.toString(); // void value in integer
                    if(residence.lenth > 100){
                        extraErrString += " - "+label_residence+': <span class="text-danger">'+msg["not_greater_than"]+' 100 '+msg["char"]+'</span><br />';
                        extraErrCheck = false;
                    }
                }
                var contacted_with      =  line_list_data[i][col["is_contacted_with"]];
                if(contacted_with !== ""){
                    contacted_with = contacted_with.toString();
                    if(contacted_with.lenth > 50){
                        extraErrString += " - "+label_contact_with+': <span class="text-danger">'+msg["not_greater_than"]+' 50 '+msg["char"]+'</span><br />';
                        extraErrCheck = false;
                    }
                }

                var sample_number = line_list_data[i][col["sample_number"]];
                if(sample_number !== ""){
                    sample_number = sample_number.toString();
                    if(sample_number.length > 11){
                        extraErrString += " - "+label_sample_number+': <span class="text-danger">'+msg["not_greater_than"]+' 11 '+msg["char"]+'</span><br />';
                        extraErrCheck = false;
                    }
                }

                var clinical_history = line_list_data[i][col["diagnosis"]];
                if(clinical_history !== ""){
                    clinical_history = clinical_history.toString();
                    if(clinical_history.length > 100){
                        extraErrString += " - "+label_clinical_history+': <span class="text-danger">'+msg["not_greater_than"]+' 100 '+msg["char"]+'</span><br />';
                        extraErrCheck = false;
                    }
                }

                var completed_by = line_list_data[i][col["completed_by"]];
                if(completed_by !== ""){
                    completed_by = completed_by.toString();
                    if(completed_by.length > 150){
                        extraErrString += " - "+label_completed_by+': <span class="text-danger">'+msg["not_greater_than"]+' 150 '+msg["char"]+'</span><br />';
                        extraErrCheck = false;
                    }
                }

                var phone = line_list_data[i][col["phone_completed_by"]];
                if(phone !== ""){
                    phone = phone.toString();
                    if(phone.length > 150){
                        extraErrString += " - "+label_phone+': <span class="text-danger">'+msg["not_greater_than"]+' 150 '+msg["char"]+'</span><br />';
                        extraErrCheck = false;
                    }
                }

                var sample_collector = line_list_data[i][col["sample_collector"]];
                if(sample_collector !== ""){
                    sample_collector = sample_collector.toString();
                    if(sample_collector.length > 50){
                        extraErrString += " - "+label_sample_collector+': <span class="text-danger">'+msg["not_greater_than"]+' 50 '+msg["char"]+'</span><br />';
                        extraErrCheck = false;
                    }
                }

                var phone_number_sample_collctor = line_list_data[i][col["phone_number_sample_collctor"]];
                if(phone_number_sample_collctor !== ""){
                    phone_number_sample_collctor = phone_number_sample_collctor.toString();
                    if(phone_number_sample_collctor.length > 15){
                        extraErrString += " - "+label_phone+': <span class="text-danger">'+msg["not_greater_than"]+' 15 '+msg["char"]+'</span><br />';
                        extraErrCheck = false;
                    }
                }
                                
                var arrival_date = line_list_data[i][col["arrival_date"]];
                var arrival_date = new Date(arrival_date);

                if(arrival_date !== ""){
                    if( arrival_date > currentDate){
                        extraErrString += " - "+label_date_of_arrival+': <span class="text-danger">'+msg["not_greater_than_current_date"]+'</span><br />';
                        extraErrCheck = false;
                    }
                }

                var passport_number = line_list_data[i][col["passport_number"]];
                if(passport_number !== ""){
                    passport_number = passport_number.toString();
                    if(passport_number.length > 20){
                        extraErrString += " - "+label_passport_no+': <span class="text-danger">'+msg["not_greater_than"]+' 20 '+msg["char"]+'</span><br />';
                        extraErrCheck = false;
                    }
                }

                var flight_number = line_list_data[i][col["flight_number"]];
                if(flight_number !== ""){
                    flight_number = flight_number.toString();
                    if(flight_number.length > 20){
                        extraErrString += " - "+label_flight_number+': <span class="text-danger">'+msg["not_greater_than"]+' 20 '+msg["char"]+'</span><br />';
                        extraErrCheck = false;
                    }
                }

                var seat_number = line_list_data[i][col["seat_number"]];
                if(seat_number !== ""){
                    seat_number = seat_number.toString();
                    if(seat_number.length > 5){
                        extraErrString += " - "+label_seat_no+': <span class="text-danger">'+msg["not_greater_than"]+' 5 '+msg["char"]+'</span><br />';
                        extraErrCheck = false;
                    }
                }

                var test_covid_date = line_list_data[i][col["test_date"]];
                var test_covid_date = new Date(test_covid_date);
                
                if(test_covid_date !== ""){
                    if( test_covid_date > currentDate){
                        extraErrString += " - "+label_test_covid_date+': <span class="text-danger">'+msg["not_greater_than_current_date"]+'</span><br />';
                        extraErrCheck = false;
                    }
                }

                require_string += "<td>"+extraErrString+" &nbsp;</td>";
                require_string += "</tr>";
                
                if(!check_age 
                    || !check_name 
                    || !check_gender 
                    || !check_province 
                    || !check_district 
                    || !check_commune 
                    || !check_village 
                    || !check_phone
                    || !check_sample_source
                    || !check_requester 
                    || !check_collection_date
                    || !check_recieved_date 
                    || !check_test_result
                    || !check_test_result_date
                    || !check_perform_by
                    || !extraErrCheck
                    ){
                        array_check.push(false);                        
                }else{
                    //add test name for testing
                    //line_list_data[i][test_id_col] = 505;
                    patient_code                                    = line_list_data[i][col["patient_code"]];
                    patient_code                                    = patient_code.toString();
                    line_list_data[i][col["patient_code"]]          = patient_code.trim();

                    age                                             = line_list_data[i][col["age"]];
                    //line_list_data[i][col["age"]]                   = age.trim();
                    phone                                           = line_list_data[i][col["phone"]];
                    phone                                           = phone.toString();
                    line_list_data[i][col["phone"]]                 = phone.trim();

                    residence                                       = line_list_data[i][col["residence"]];
                    residence                                       = residence.toString();
                    line_list_data[i][col["residence"]]             = residence.trim();

                    is_contacted_with                               = line_list_data[i][col["is_contacted_with"]];
                    line_list_data[i][col["is_contacted_with"]]     = is_contacted_with.trim();

                    sample_number                                   = line_list_data[i][col["sample_number"]];
                    sample_number                                   = sample_number.toString();
                    line_list_data[i][col["sample_number"]]         = sample_number.trim();

                    line_list_data[i][col["sample_source"]]         = sample_source;
                    line_list_data[i][col["requester"]]             = requester;

                    diagnosis                                       = line_list_data[i][col["diagnosis"]];
                    diagnosis                                       = diagnosis.toString();
                    line_list_data[i][col["diagnosis"]]             = diagnosis.trim();

                    completed_by                                    = line_list_data[i][col["completed_by"]];
                    line_list_data[i][col["completed_by"]]          = completed_by.trim();
                    phone_completed_by                              = line_list_data[i][col["phone_completed_by"]];
                    phone_completed_by                              = phone_completed_by.toString();
                    line_list_data[i][col["phone_completed_by"]]    = phone_completed_by.trim();

                    sample_collector                                = line_list_data[i][col["sample_collector"]];
                    line_list_data[i][col["sample_collector"]]      = sample_collector.trim();
                    phone_number_sample_collctor                    = line_list_data[i][col["phone_number_sample_collctor"]];
                    phone_number_sample_collctor                    = phone_number_sample_collctor.toString();
                    line_list_data[i][col["phone_number_sample_collctor"]] = phone_number_sample_collctor.trim();

                    passport_number                                 = line_list_data[i][col["passport_number"]];
                    passport_number                                 = passport_number.toString();
                    line_list_data[i][col["passport_number"]]       = passport_number.trim();

                    flight_number                                   = line_list_data[i][col["flight_number"]];
                    flight_number                                   = flight_number.toString();
                    line_list_data[i][col["flight_number"]]         = flight_number.trim();

                    seat_number                                     = line_list_data[i][col["seat_number"]];
                    seat_number                                     = seat_number.toString();
                    line_list_data[i][col["seat_number"]]           = seat_number.trim();

                    contact_with                                    = line_list_data[i][col["is_contacted_with"]];
                    contact_with                                    = contact_with.toString();
                    line_list_data[i][col["is_contacted_with"]]     = contact_with.trim();

                    completed_by                                    = line_list_data[i][col["completed_by"]];
                    line_list_data[i][col["completed_by"]]          = completed_by.trim(); // completed by

                    phone_number                                    = line_list_data[i][col["phone_completed_by"]];
                    phone_number                                    = phone_number.toString(); // avoid phone number is integer
                    line_list_data[i][col["phone_completed_by"]]    = phone_number.trim(); // phone number of completed by

                    sample_collector                                = line_list_data[i][col["sample_collector"]];
                    line_list_data[i][col["sample_collector"]]      = sample_collector.trim(); // sample collector

                    phone_number_sample_collector                   = line_list_data[i][col["phone_number_sample_collctor"]];
                    phone_number_sample_collector                   = phone_number_sample_collector.toString();
                    line_list_data[i][col["phone_number_sample_collctor"]] = phone_number_sample_collector.trim();

                    data.push(line_list_data[i]);
                    array_check.push(true);
                }
            }
        }
        
        if(valid_check == 0){
            myDialog.showProgress('hide');
            myDialog.showDialog('show', {text: msg["not_data_entry"], style: 'warning'});
            $(this).removeClass('disabled'); //prevent multiple click
            return false;
        }
        console.log("Check "+ array_check.indexOf(false));
        if((array_check.indexOf(false) >= 0)){
            myDialog.showProgress('hide');
            $("table[name=tblErrorLineListNew] tbody").html(require_string);
            $modal_list_error.modal('show');
            $(this).removeClass('disabled'); //prevent multiple click
            return false;
        }
        
        //console.log(data);
        //console.log(data.length);
        
        if(data.length > 500){
            myDialog.showProgress('hide');
            myDialog.showDialog('show', {text: msg["not_greater_than_100_row"], style: 'warning'});
            $(this).removeClass('disabled'); //prevent multiple click
            return false;
        }
        
        $.ajax({
            url: base_url + "/patient/add_line_list_full_form",
            type: "POST",
            data: { data: data},
            dataType: 'json',
            success: function (resText) {
                var patients = resText.patients;
                var bodyResult = '';    
                var psample_ids = '';
                var n = 1;
                for(var i in patients) {
                    var btnPrint = "";
                    var test_result_msg = "";                
                    if(patients[i].psample_id !== undefined){
                        btnPrint = '<button type="button" class="btnPrintCovidFormV1" data-psample_id="'+patients[i].psample_id+'">'+label_print+'</button>'
                        psample_ids += patients[i].psample_id+"n";
                    }
                    if(patients[i].test_result_msg !== undefined){
                        test_result_msg = patients[i].test_result_msg;
                    }
                    bodyResult += '<tr>';
                    bodyResult += '<td>'+n+'</td>';
                    bodyResult += '<td>'+patients[i].patient_code+'</td>';
                    bodyResult += '<td>'+patients[i].patient_name+'</td>';
                    bodyResult += '<td>'+patients[i].msg+'</td>';  
                    bodyResult += '<td>'+patients[i].sample_number+'</td>';
                    bodyResult += '<td>'+patients[i].sample_msg+'</td>';                   
                    bodyResult += '<td>'+patients[i].test_msg+'</td>';
                    bodyResult += '<td>'+test_result_msg+'</td>';
                    bodyResult += '<td>'+btnPrint+'</td>';
                    bodyResult += '</tr>';
                    n++;
                }
                setTimeout(function(){
                    myDialog.showProgress('hide');
                    $("table[name=tblResultLineListNew] tbody").html(bodyResult);
                    var res = psample_ids.substring(0, psample_ids.length - 1); // remove the last n
                    $("#printAll").attr("data-psample_id",res);
                    $("#modal_result_line_list_new").modal("show");
                }, 1000);

            },
            error: function(xhr, status, error) {
                var err = eval("(" + xhr.responseText + ")");
                console.log(err.Message);
                console.log(xhr.responseText);
            }
        });
    });


    var line_list_table_test = jspreadsheet(document.getElementById('spreadsheet1'), {
        minDimensions: [ 24, 700 ],
        defaultColWidth: 100,
        tableOverflow: true,
        tableHeight: "500px",
        columns: [
            { type:'text', title: label_patient_id,width:140 ,maxlength:20},
            { type:'text', title: label_patient_name+'*' ,maxlength:60},
            { type:'numeric', title: label_patient_age+'*',width:40 , maxlength:3 },
            { type:'dropdown', title:label_sex+'*',width:40, source:["ប្រុស" , "ស្រី"] },
            { type:'text', title: label_patient_phone_number,maxlength:100 },
            { type: 'autocomplete', title: label_province+'*', width:100, source:province_array },
            { type: 'autocomplete', title: label_district+'*', width:100, source:districts_array , filter: districtFilter2},
            { type: 'autocomplete', title: label_commune+'*', width:100, source:communes_array , filter: communeFilter2},
            { type: 'autocomplete', title: label_village+'*', width:100, source:villages_array ,filter: villageFilter2},
            { type:'text', title: label_residence ,width:80 ,maxlength:100},
            { type: 'autocomplete', title: label_reason_for_testing, width:120, source:reason_for_testing_array },
            { type:'checkbox', title: label_yes, width:100 },
            { type:'text', title: label_contact_with ,readOnly:true , maxlength:60 },
            { type:'checkbox', title: label_is_direct_contact },
            { type: 'text', title: label_sample_number, width:120 ,maxlength:11},
            { type: 'dropdown', title: label_sample_source+'*', width:100, source:sample_source_array },
            { type: 'dropdown', title:label_requester+'*', width:120, source:requester_array, filter: requesterFilter2 },
            { type: 'calendar', title:label_collect_dt+'*', width:170,options: { format:'YYYY-MM-DD' , time:true , readonly:true, validRange: [ '2021-01-01', today ] } },
            { type: 'calendar', title:label_receive_dt+'*', width:170,options: { format:'YYYY-MM-DD' , time:true , readonly:true , validRange: [ '2021-01-01', today ] } },
            { type: 'autocomplete', title:label_payment_type+'*', width:120, source:payment_type_array },
            { type: 'calendar', title:'ថ្ងៃចូលសម្រាកពេទ្យ', width:120,options: { format:'YYYY-MM-DD' , time:true , readonly:true} },
            { type:'text', title: label_clinical_history,width:80 , maxlength: 60},
            { type:'checkbox', title: label_urgent,width:50 },

            { type:'text', title: label_completed_by , width:160 , maxlength:50},
            { type:'text', title: label_phone ,maxlength:10 },
            { type:'text', title: label_sample_collector , maxlength:50 },
            { type:'text', title: label_phone , maxlength:10 },
            { type: 'dropdown', title: label_clinical_symtom, width:150, source:clinical_symptom_array , autocomplete:true, multiple:true},
            { type: 'dropdown', title: label_test_name, width:150, source:['SARS-CoV-2 (Method: real time RT-PCR by GeneXpert)', 'SARS-CoV-2 (Applied Biosystems 7500 Fast Real Time PCR)','SARS-CoV-2 Rapid Antigen Test']},
            { type: 'dropdown', title: label_result, width:70, source:['Negative', 'Positive'], readOnly: true},
            { type: 'calendar', title: label_test_date, width:150 , readOnly: true, options: { format:'YYYY-MM-DD' , readonly:true , validRange: [ '2021-01-01', today ]}},
            { type: 'dropdown', title: label_performed_by, width:120, source:performer_array, readOnly: true},
            
            { type: 'autocomplete', title: label_country, width:80, source:country_array },        
            { type: 'autocomplete', title: label_nationality, width:80, source:nationalities_array },            
            { type: 'calendar', title: label_date_of_arrival, width: 100,options: { format:'YYYY-MM-DD' , readonly:true } },
            { type:'text', title: label_passport_no, width:80 , maxlength:20},
            { type:'text', title: label_flight_number ,maxlength:5 },
            { type:'text', title: label_seat_no, width:60, maxlength:5 },
            { type:'checkbox', title: label_yes,width:60},
            { type:'calendar', title: label_test_covid_date, readOnly:true, width:90,options: { format:'YYYY-MM-DD' , readonly:true , validRange: [ '2021-01-01', today ]} },
            /** Hidden Column */
            /** Store code  */
            { type:'text', title:'sex'}, 
            { type:'text', title:'province_code'},
            { type:'text', title:'district_code'}, 
            { type:'text', title:'commune_code'},
            { type:'text', title:'village_code'},
            { type:'text', title:'reason_for_testing'}, 
            { type:'text', title:'sample_source_id'}, 
            { type:'text', title:'requester_id'},
            { type:'text', title:'clinical_symtom_id'}, 
            { type:'text', title:'test_id'}, 
            { type:'text', title:'country_id'}, 
            { type:'text', title:'nationality_id'}, 
            { type:'dropdown', title: label_number_of_sample ,width:120,source:[1,2,3,4,5,6,7,8,9,10]},
            { type:'text', title:'performer_by_id'},
        ],
        nestedHeaders:[
            [
                {
                    title: label_patient_info,
                    colspan: '14',
                },
                {
                    title: label_sample_info,
                    colspan: '16',
                },
                {
                    title: label_patient_info,
                    colspan: '8',
                },
                {
                    title: label_sample_info,
                    colspan: '1',
                }
            ],
            [
                {
                    title: label_patient,
                    colspan: '5',
                },
                {
                    title: label_address,
                    colspan: '6',
                },
                {
                    title: label_if_contact,
                    colspan: '3'
                },
                {
                    title: label_sample,
                    colspan: '11'
                },
                {
                    title: label_test_result,
                    colspan: '4'
                },
                {
                    title: label_patient_info,
                    colspan: '6'
                },
                {
                    title: label_history_of_covid19_history,
                    colspan: '2'
                },
                {
                    title: label_sample,
                    colspan: '1'
                }
            ],
        ],
        onchange:function(instance,cell, c, r, value) {
            // patient_code
            //console.log("Col= "+c);
            if( c == 0){
                var rowNumber = r;
                var totalCol = 20;
                var i;
                var nCol = 1;                
                if(value !== ""){
                    //console.log(value);
                    // search if patient_code existent
                    var patient_code = value;
                    $.ajax({
                        url: base_url + 'patient/search/' + patient_code,
                        type: 'POST',
                        data: {pid: patient_code},
                        dataType: 'json',
                        success: function (resText) {
                            //console.log(resText);
                            var patient = resText.patient;
                           //console.log(patient);
                            if(patient){
                                //console.log(patient);
                                //console.log("patient_code "+patient.patient_code);
                                var dob = "";
                                var sex = (patient.sex == 'M') ? "ប្រុស" : "ស្រី";
                                var dob = moment(patient.dob, 'YYYY-MM-DD');
                                var age = calculateAge(dob);
                                
                                var is_positive_covid   = (patient.is_positive_covid == null || patient.is_positive_covid == undefined || patient.is_positive_covid == false) ? false : true;
                                var is_contacted        = (patient.is_contacted == null || patient.is_contacted == undefined) ? false : true;
                                var contact_with        = (patient.contact_with == null || patient.contact_with == undefined) ? "" : patient.contact_with;
                                var is_direct_contact   = (patient.is_direct_contact == null || patient.is_direct_contact == undefined) ? false : true;
                                var test_date           = (patient.test_date == undefined || patient.test_date == null) ? "" : patient.test_date;;
                                var name                = patient.name;
                                var residence           = (patient.residence == undefined || patient.residence == null) ? "" : patient.residence;
                                var date_arrival        = (patient.date_arrival == undefined || patient.date_arrival == null) ? "" : patient.date_arrival;
                                var country             = (patient.country_name_en == undefined || patient.country_name_en == null) ? "" : patient.country_name_en;
                                var nationality         = (patient.nationality_en == undefined || patient.nationality_en == null) ? "" : patient.nationality_en;
                                var passport_number     = (patient.passport_number == undefined || patient.passport_number == null) ? "" : patient.passport_number;
                                var flight_number       = (patient.flight_number == undefined || patient.flight_number == null) ? "" : patient.flight_number;
                                var seat_number         = (patient.seat_number == undefined || patient.seat_number == null) ? "" : patient.seat_number;
                                var phone               = (patient.phone == undefined || patient.phone == null) ? "" : (patient.phone).trim();
                                //var phone               = patient.phone;

                                //var name            = "name";
                                //var patient_code    = "patient code";
                                var rowData = [                                                                        
                                    name,
                                    age.years,
                                    sex,
                                    phone,
                                    patient.province_kh,
                                    patient.district_kh,
                                    patient.commune_kh,
                                    patient.village_kh,
                                    residence
                                ];
                                
                                for(i = 0 ; i < 9;i++){
                                    var nameColumn = jspreadsheet.getColumnNameFromId([c+nCol, r]);
                                    instance.jexcel.setValue(nameColumn, rowData[i]);
                                    nCol++;
                                }
                                                               
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["is_contacted"], r]), is_contacted);
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["is_contacted_with"], r]), contact_with);                            
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["is_directed_contact"], r]), is_direct_contact);
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["country"], r]), country);
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["nationality"], r]), nationality);
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["arrival_date"], r]), date_arrival);
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["passport_number"], r]), passport_number);
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["flight_number"], r]), flight_number);
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["seat_number"], r]), seat_number);
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["is_positive_covid"], r]), is_positive_covid);                            
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["test_date"], r]), test_date);

                            }else{
                                // Reset columns
                                nCol = 1;
                                for(i = 0 ; i < 9;i++){
                                    var nameColumn = jspreadsheet.getColumnNameFromId([c+nCol, r]);
                                    instance.jexcel.setValue(nameColumn, "");
                                    nCol++;
                                }                                
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["is_contacted"], r]), false);
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["is_contacted_with"], r]), "");                            
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["is_directed_contact"], r]), false);
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["country"], r]), "");
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["nationality"], r]), "");
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["arrival_date"], r]), "");
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["passport_number"], r]), "");
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["flight_number"], r]), "");
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["seat_number"], r]), "");
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["is_positive_covid"], r]), false);                            
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["test_date"], r]), "");
                                
                            }
                        }
                    })
                }else{
                    // Reset columns
                    nCol = 1;
                    for(i = 0 ; i < 9;i++){
                        var nameColumn = jspreadsheet.getColumnNameFromId([c+nCol, r]);
                        instance.jexcel.setValue(nameColumn, "");
                        nCol++;
                    }                                
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["is_contacted"], r]), false);
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["is_contacted_with"], r]), "");                            
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["is_directed_contact"], r]), false);
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["country"], r]), "");
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["nationality"], r]), "");
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["arrival_date"], r]), "");
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["passport_number"], r]), "");
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["flight_number"], r]), "");
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["seat_number"], r]), "");
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["is_positive_covid"], r]), false);                            
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["test_date"], r]), "");
                }
            }
            // sex column
            if(c == col["gender"]){
                if(value !== ""){
                    // save id of sex in column 35
                    sex = value == 'ប្រុស' ? 1 : 2;                    
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["sex_id"], r]),sex);
                }
            }
            // province column
            if (c == col["province"]) {
                if(value !== ""){
                    // set null value to district col                    
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["district"], r]), "");
                    code = getProvinceCode(value);                    
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["province_code"], r]),code); // save province code in column 38
                }               
            }
            // district column
            if (c == col["district"]) {
                if(value !== ""){
                    // set null value to commune 
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["commune"], r]), "");
                    province_code = line_list_table_test.getValueFromCoords(col["province_code"],r);                    
                    code = getDistrictCode(value, province_code);
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["district_code"], r]), code);
                }
            }
            // commune column
            if (c == col["commune"]) {
                if(value !== ""){
                    // set null value to village                     
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["village"], r]), "");
                    district_code = line_list_table_test.getValueFromCoords(col["district_code"],r);
                    code = getCommuneCode(value, district_code);                    
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["commune_code"], r]), code);
                }
            }
            // village column
            if (c == col["village"]) {
                if(value !== ""){
                    commune_code = line_list_table_test.getValueFromCoords(col["commune_code"],r);
                    code = getVillageCode(value, commune_code);
                    //console.log("get village code "+code);                    
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["village_code"], r]), code);
                }
            }
            // reason for testing column
            if (c == col["reason_for_testing"]) {
                if(value !== ""){
                    code = getReason(value);
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["reason_for_testing_id"], r]), code);
                }
            }
            // is contacted column
            if (c == col["is_contacted"]) {
                var is_contacted_column = jspreadsheet.getColumnNameFromId([col["is_contacted_with"], r]);
                var is_direct_contact_column = jspreadsheet.getColumnNameFromId([col["is_directed_contact"], r]);
                //var c = line_list_table_test.getCell(is_direct_contact_column);
                //console.log(c);
                if(value){
                    //console.log("true is right");
                   line_list_table_test.setReadOnly(is_contacted_column,false);
                   line_list_table_test.setReadOnly(is_direct_contact_column,false);
                }else{
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["is_contacted_with"], r]),"");
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["is_directed_contact"], r]),"");
                   line_list_table_test.setReadOnly(is_contacted_column,true);
                   line_list_table_test.setReadOnly(is_direct_contact_column,true);
                }
            }
            // sample source column
            if (c == col["sample_source"]) {
                if(value !== ""){
                    // set
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["requester"], r]),"");
                    // save id of sample source in column 43
                    code = getSampleSource(value);
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["sample_source_id"], r]), code);
                }
            }

            // requester column
            if (c == col["requester"]) {
                if(value !== ""){
                    sample_source_id = line_list_table_test.getValueFromCoords(col["sample_source_id"],r);
                    code = getRequester(value,sample_source_id);
                    var columnName = jspreadsheet.getColumnNameFromId([col["requester_id"], r]);
                    instance.jexcel.setValue(columnName, code);
                }
            }
            // clinical symptom column
            if (c == col["clinical_symptom"]) {
                if(value !== ""){
                    code = getClinicalSymptom(value);
                    //console.log(code);
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["clinical_symtop_id"], r]), code);
                }
            }
            // Test column
            if (c == col["test_name"]) {
                var test_result_col         = jspreadsheet.getColumnNameFromId([col["test_result"], r]);
                var test_result_date_col    = jspreadsheet.getColumnNameFromId([col["test_result_date"], r]);
                var perform_by_col          = jspreadsheet.getColumnNameFromId([col["perform_by"], r]);
                if(value !== ""){
                    code = getTest(value);
                    //console.log("Test ID "+ code);
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["test_id"], r]), code);
                    // remove read only from result, test date and perform_by column
                    line_list_table_test.setReadOnly(test_result_col,false);
                    line_list_table_test.setReadOnly(test_result_date_col,false);
                    line_list_table_test.setReadOnly(perform_by_col,false);
                }else{
                    // reset value and set readonly
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["test_result"], r]), "");
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["test_result_date"], r]), "");
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["perform_by"], r]), "");

                    line_list_table_test.setReadOnly(test_result_col,true);
                    line_list_table_test.setReadOnly(test_result_date_col,true);
                    line_list_table_test.setReadOnly(perform_by_col,true);
                }
            }
            if (c == col["perform_by"]){
                if(value !== ""){
                    code = getPerformer(value);
                    //console.log("performer id "+code);  
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["performer_by_id"], r]), code);
                }
            }
            // country column
            if (c == col["country"]) {
                if(value !== ""){
                   code = getCountry(value);
                   instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["country_id"], r]), code);
                }
            }
            // nationality column
            if (c == col["nationality"]) {
                if(value !== ""){                    
                    code = getNationality(value);
                    //console.log("Nationality code: "+code);                    
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["nationality_id"], r]), code);
                }
            }
            // if check is true, enable Test Date
            if (c == col["is_positive_covid"]) {
                //console.log(value);
                var colummnName = jspreadsheet.getColumnNameFromId([col["test_date"], r]);
                if(value){
                   line_list_table_test.setReadOnly(colummnName,false);
                }else{
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["test_date"], r]), "");
                    line_list_table_test.setReadOnly(colummnName,true);
                }
            }                    
        },
        oncreateeditor: function(el, cell, x, y) {
            if (x == col["phone"] 
                || x == col["age"] 
                || x == col["patient_name"] 
                || x == col["patient_code"] 
                || x == col["residence"] 
                || x == col["sample_source"] 
                || x == col["diagnosis"] 
                || x == col["completed_by"]  
                || x == col["phone_completed_by"] 
                || x == col["sample_collector"] 
                || x == col["phone_number_sample_collctor"] 
                || x == col["passport_number"] 
                || x == col["flight_number"] 
                || x == col["seat_number"]
            ){
            var config = el.jexcel.options.columns[x].maxlength;
                cell.children[0].setAttribute('maxlength' , config); // set maxlength to column
            }
        }
    });
    
    line_list_table_test.hideColumn(col["payment_type"]); // hide payment type
    line_list_table_test.hideColumn(col["admission_date"]); // hide admision column
    line_list_table_test.hideColumn(col["is_urgent"]);
    line_list_table_test.hideColumn(col["sex_id"]);
    line_list_table_test.hideColumn(col["test_id"]);
    line_list_table_test.hideColumn(col["country_id"]);
    line_list_table_test.hideColumn(col["nationality_id"]);
    line_list_table_test.hideColumn(col["performer_by_id"]);
    line_list_table_test.hideColumn(col["province_code"]);
    line_list_table_test.hideColumn(col["district_code"]);
    line_list_table_test.hideColumn(col["commune_code"]);
    line_list_table_test.hideColumn(col["village_code"]);
    line_list_table_test.hideColumn(col["reason_for_testing_id"]);
    line_list_table_test.hideColumn(col["sample_source_id"]);
    line_list_table_test.hideColumn(col["requester_id"]);
    line_list_table_test.hideColumn(col["clinical_symtop_id"]);


    $("#btnSaveList").on("click", function (evt) {
        $(this).addClass('disabled btn-progress'); //prevent multiple click
        myDialog.showProgress('show', { text : msg_loading });

        var line_list_data  = line_list_table_test.getData();
        var data            = [];
        var require_string  = "";
        var valid_check     = 0;
        var array_check     = [];
        var currentDate     = new Date();
        //console.log(valid_check);
        for(var i in line_list_data){
            var name = line_list_data[i][col["patient_name"]];
            var namelenght = name.length;
            //console.log(" name "+ name.trim());
            //console.log(" namelenght "+ namelenght);
            var check_name = false;
            if(name.trim() !== ""){
                if(valid_check !== 1){
                    valid_check     = 1;
                }
                
                // age column
                var age         = line_list_data[i][col["age"]];
                var check_age   = false;
                require_string += "<tr>";
                require_string += "<td>"+line_list_data[i][col["patient_code"]]+"</td>";

                if(name.length > 60){
                     require_string += '<td class="text-danger">'+msg["not_greater_than_60"]+'</td>';
                }else{
                    require_string += "<td>"+name+"</td>";
                    check_name = true;
                }

               //console.log("age "+age);
                if(age == ""){
                    require_string += '<td class="text-danger">'+msg["not_fill"]+'</td>';
                }else{
                    if(age.length > 3){
                        require_string += '<td class="text-danger">'+msg["not_greater_than_3"]+'</td>';
                    }else{
                        var c               = String(age).substr(age.length - 1, age.length);
                        var patternNumber   = /^\d+$/; // number only
                        var isNumber        = patternNumber.test(c);  // returns a boolean
                        //console.log("Char :"+ c);
                        if(isNumber == false){
                            var c_arr = ['m','M','d','D'];
                            if(c_arr.indexOf(c) == '-1'){
                                require_string += '<td class="text-danger">'+msg["not_correct_format"]+'</td>';
                            }else{
                                //console.log(c_arr+" "+age.length);
                                if(age.length == 1){
                                    require_string += '<td class="text-danger">'+msg["not_correct_format"]+'</td>';
                                }else{
                                    var nb = age.substr(0, age.length - 1);
                                    nb = parseInt(nb);
                                    if(c == "m"){
                                        if(nb > 12){
                                            require_string += '<td class="text-danger">'+msg['month_not_greater_than_12']+'/td>';
                                        }else{
                                            require_string += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                                            check_age = true;
                                        }
                                    }else if(c == "d"){
                                        if(nb > 31){
                                            require_string += '<td class="text-danger">'+msg["day_not_greater_than_31"]+'</td>';
                                        }else{
                                            require_string += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                                            check_age = true;
                                        }
                                    }
                                }
                            }
                        }else{
                            var pattern = /^\d+$/; // number only
                            var r = pattern.test(age);  // returns a boolean
                            if(r == false){
                                require_string += '<td class="text-danger">លេខឡាតាំងតែប៉ុណ្ណោះ</td>';
                            }else{
                                require_string += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                                check_age = true;
                            }
                        }
                    }
                }
                //console.log("age "+age+" "+check_age);
                // Gender column

                var gender       = line_list_data[i][col["gender"]];
                var check_gender = false;
                var gender_id    = line_list_data[i][col["sex_id"]];
            
                if(gender.length == 0 || gender_id.length == 0){
                    require_string += '<td class="text-danger">'+msg["not_select"]+'</td>';
                }else{
                    require_string += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                    var check_gender = true;
                }
                //console.log("Gender: "+gender+" "+check_gender+" "+gender.length);
                var phone        = line_list_data[i][col["phone"]];
                var check_phone  = false;

                if(phone !== null){
                    if(phone.length > 100){
                        require_string += '<td class="text-danger">'+msg["not_greater_than_100"]+'</td>';
                    }else{
                        require_string += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                        check_phone = true;
                    }
                }else{
                    require_string += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                    check_phone = true;
                }
                // Province column
                var province        = line_list_data[i][col["province"]];
                var check_province  = false;
                var province_id     = line_list_data[i][col["province_code"]];
                if(province.length == 0 || province_id.length == 0){
                    require_string += '<td class="text-danger">'+msg["not_select"]+'</td>';
                }else{
                    require_string += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                    check_province = true;
                }
                //console.log("province: "+province+" "+check_province);

                var district        = line_list_data[i][col["district"]];
                var check_district  = false;
                var district_id     = line_list_data[i][col["district_code"]];
                if(district.length == 0 || district_id.length == 0){
                    require_string += '<td class="text-danger">'+msg["not_select"]+'</td>';
                }else{
                    require_string += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                    check_district = true;
                }
                //console.log("district: "+district+" "+check_district);
                // Commune
                var commune         = line_list_data[i][col["commune"]];
                var check_commune   = false;
                var commune_id      = line_list_data[i][col["commune_code"]];
                if(commune.length == 0 || commune_id.length == 0){
                    require_string += '<td class="text-danger">'+msg["not_select"]+'</td>';
                }else{
                    require_string += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                    check_commune = true;
                }
                //console.log("commune: "+commune+" "+check_commune);
                //village 
                var village         = line_list_data[i][col["village"]];
                var check_village   = false;
                var village_id      = line_list_data[i][col["village_code"]];
                if(village.length == 0 || village_id.length == 0){
                    require_string += '<td class="text-danger">'+msg["not_select"]+'</td>';
                }else{
                    require_string += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                    check_village = true;
                }
                //console.log("village: "+village+" "+check_village);
                // sample source
                var sample_source       = line_list_data[i][col["sample_source"]];
                var check_sample_source = false;
                var sample_source_id    = line_list_data[i][col["sample_source_id"]];

                //console.log("samsource_id "+ " "+sample_source_id);
                if(sample_source.length == 0 || sample_source_id.length == 0){
                    require_string += '<td class="text-danger">'+msg["not_select"]+'</td>';
                }else{
                    check_sample_source = true;
                    require_string += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                }
                //console.log("sample_source: "+sample_source+" "+check_sample_source+", samsource_id: "+sample_source_id.length);
                // requester
                var requester       = line_list_data[i][col["requester"]];
                var check_requester = false;
                var requester_id    = line_list_data[i][col["requester_id"]];
                if(requester.length == 0 || requester_id.length == 0){
                    require_string += '<td class="text-danger">'+msg["not_select"]+'</td>';
                }else{
                    require_string += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                    check_requester = true;
                }
                //console.log("requester: "+requester+" "+check_requester);

                // collection date
                var collection_date         = line_list_data[i][col["collected_date"]];
                var check_collection_date   = false;
                if(collection_date == ""){
                    require_string += '<td class="text-danger">'+msg["not_select"]+'</td>';
                }else{
                    require_string += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                    check_collection_date = true;
                }
                //received_date
                var recieved_date       = line_list_data[i][col["received_date"]];
                var check_recieved_date = false;
                if(recieved_date == ""){
                    require_string += '<td class="text-danger">'+msg["not_select"]+'</td>';
                }else{
                    var d1 = new Date(recieved_date);
                    var d2 = new Date(collection_date);
                
                    if(d1.getTime() < d2.getTime()){
                        require_string += '<td class="text-danger">'+msg["greater_or_equal_collected_date"]+'</td>';
                    }else{
                        require_string += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                        check_recieved_date = true;
                    }
                }
                // For test name
                var test_name               = line_list_data[i][col["test_name"]];
                
                var check_test_result       = false;
                var check_test_result_date  = false;
                var check_perform_by        = false;
                
                if(test_name.length > 0){
                    require_string  += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                    check_test_result = true;

                    var test_result         = line_list_data[i][col["test_result"]];

                    var test_result_date    =  line_list_data[i][col["test_result_date"]];
                    check_test_result_date  = false;
                    var perform_by          = line_list_data[i][col["perform_by"]];
                    var perform_by_id       = line_list_data[i][col["performer_by_id"]];
                    check_perform_by        = false;

                    if((test_result.length == 0) && (test_result_date == "") && (perform_by.length == 0 || perform_by_id.length == 0) ){
                        require_string += '<td>&nbsp;</td>';
                        require_string += '<td>&nbsp;</td>';
                        require_string += '<td>&nbsp;</td>';
                        check_test_result       = true;
                        check_test_result_date  = true;
                        check_perform_by        = true;
                    }else if (test_result.length > 0 && (test_result_date == "") && (perform_by.length == 0 || perform_by_id.length == 0)){
                        require_string  += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                        check_test_result = true;

                        require_string += '<td class="text-danger">'+msg["not_select"]+'</td>';
                        check_test_result_date       = false;

                        require_string += '<td class="text-danger">'+msg["not_select"]+'</td>';
                        check_perform_by = false;
                    }else if(test_result.length == 0 && (test_result_date !== "") && (perform_by.length == 0 || perform_by_id.length == 0)){
                        require_string += '<td class="text-danger">'+msg["not_select"]+'</td>';
                        check_test_result = false;

                        tr_date = new Date(test_result_date);
                        if( tr_date > currentDate){                            
                            require_string += '<td class="text-danger">'+msg["not_greater_than_current_date"]+'</td>';
                            check_test_result_date = false;
                        }else{
                            require_string  += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                            check_test_result_date       = true;
                        }
                        
                        require_string += '<td class="text-danger">'+msg["not_select"]+'</td>';
                        check_perform_by = false;

                    }else if(test_result.length == 0 && (test_result_date == "") && (perform_by.length > 0 || perform_by_id.length > 0)){
                        require_string += '<td class="text-danger">'+msg["not_select"]+'</td>';
                        check_test_result = false;

                        require_string += '<td class="text-danger">'+msg["not_select"]+'</td>';
                        check_test_result_date       = false;

                        require_string  += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                        check_perform_by       = true;
                    }else if(test_result.length > 0 && (test_result_date !== "") && (perform_by.length == 0 || perform_by_id.length == 0)){
                        require_string  += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                        check_test_result = true;

                        tr_date = new Date(test_result_date);
                        if( tr_date > currentDate){                            
                            require_string += '<td class="text-danger">'+msg["not_greater_than_current_date"]+'</td>';
                            check_test_result_date = false;
                        }else{
                            require_string  += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                            check_test_result_date       = true;
                        }

                        require_string += '<td class="text-danger">'+msg["not_select"]+'</td>';
                        check_perform_by = false;
                    }else if(test_result.length > 0 && (test_result_date == "") && (perform_by.length > 0 || perform_by_id.length > 0)){
                        require_string  += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                        check_test_result = true;

                        require_string += '<td class="text-danger">'+msg["not_select"]+'</td>';
                        check_test_result_date       = false;

                        require_string  += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                        check_perform_by = true;
                    }else if(test_result.length == 0 && (test_result_date !== "") && (perform_by.length > 0 || perform_by_id.length > 0)){
                        require_string += '<td class="text-danger">'+msg["not_select"]+'</td>';
                        check_test_result = false;

                        tr_date = new Date(test_result_date);
                        if( tr_date > currentDate){
                            require_string += '<td class="text-danger">'+msg["not_greater_than_current_date"]+'</td>';
                            check_test_result_date = false;
                        }else{
                            require_string  += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                            check_test_result_date       = true;
                        }

                        require_string  += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                        check_perform_by = true;
                    }else{
                        require_string  += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                        check_test_result = true;

                        tr_date = new Date(test_result_date);
                        if( tr_date > currentDate){
                            require_string += '<td class="text-danger">'+msg["not_greater_than_current_date"]+'</td>';
                            check_test_result_date = false;
                        }else{
                            require_string  += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                            check_test_result_date       = true;
                        }                        

                        require_string  += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                        check_perform_by = true;
                    }

                }else{
                    require_string += '<td>&nbsp;</td>';
                    require_string += '<td>&nbsp;</td>';
                    require_string += '<td>&nbsp;</td>';
                    require_string += '<td>&nbsp;</td>';

                    check_test_result       = true;
                    check_test_result_date  = true;
                    check_perform_by        = true;

                }
                //console.log("test name "+test_name);

                //07-05-2021
                // Extra Check length
                var extraErrString = '';
                var extraErrCheck  = true;
                var residence      =  line_list_data[i][col["residence"]];
                // convert to string in order to trim();
                if(residence !== ""){
                    residence = residence.toString(); // void value in integer
                    if(residence.lenth > 100){
                        extraErrString += " - "+label_residence+': <span class="text-danger">'+msg["not_greater_than"]+' 100 '+msg["char"]+'</span><br />';
                        extraErrCheck = false;
                    }
                }
                var contacted_with      =  line_list_data[i][col["is_contacted_with"]];
                if(contacted_with !== ""){
                    contacted_with = contacted_with.toString();
                    if(contacted_with.lenth > 50){
                        extraErrString += " - "+label_contact_with+': <span class="text-danger">'+msg["not_greater_than"]+' 50 '+msg["char"]+'</span><br />';
                        extraErrCheck = false;
                    }
                }

                var sample_number = line_list_data[i][col["sample_number"]];
                if(sample_number !== ""){
                    sample_number = sample_number.toString();
                    if(sample_number.length > 11){
                        extraErrString += " - "+label_sample_number+': <span class="text-danger">'+msg["not_greater_than"]+' 11 '+msg["char"]+'</span><br />';
                        extraErrCheck = false;
                    }
                }

                var clinical_history = line_list_data[i][col["diagnosis"]];
                if(clinical_history !== ""){
                    clinical_history = clinical_history.toString();
                    if(clinical_history.length > 100){
                        extraErrString += " - "+label_clinical_history+': <span class="text-danger">'+msg["not_greater_than"]+' 100 '+msg["char"]+'</span><br />';
                        extraErrCheck = false;
                    }
                }

                var completed_by = line_list_data[i][col["completed_by"]];
                if(completed_by !== ""){
                    completed_by = completed_by.toString();
                    if(completed_by.length > 150){
                        extraErrString += " - "+label_completed_by+': <span class="text-danger">'+msg["not_greater_than"]+' 150 '+msg["char"]+'</span><br />';
                        extraErrCheck = false;
                    }
                }

                var phone = line_list_data[i][col["phone_completed_by"]];
                if(phone !== ""){
                    phone = phone.toString();
                    if(phone.length > 150){
                        extraErrString += " - "+label_phone+': <span class="text-danger">'+msg["not_greater_than"]+' 150 '+msg["char"]+'</span><br />';
                        extraErrCheck = false;
                    }
                }

                var sample_collector = line_list_data[i][col["sample_collector"]];
                if(sample_collector !== ""){
                    sample_collector = sample_collector.toString();
                    if(sample_collector.length > 50){
                        extraErrString += " - "+label_sample_collector+': <span class="text-danger">'+msg["not_greater_than"]+' 50 '+msg["char"]+'</span><br />';
                        extraErrCheck = false;
                    }
                }

                var phone_number_sample_collctor = line_list_data[i][col["phone_number_sample_collctor"]];
                if(phone_number_sample_collctor !== ""){
                    phone_number_sample_collctor = phone_number_sample_collctor.toString();
                    if(phone_number_sample_collctor.length > 15){
                        extraErrString += " - "+label_phone+': <span class="text-danger">'+msg["not_greater_than"]+' 15 '+msg["char"]+'</span><br />';
                        extraErrCheck = false;
                    }
                }
                                
                var arrival_date = line_list_data[i][col["arrival_date"]];
                var arrival_date = new Date(arrival_date);

                if(arrival_date !== ""){
                    if( arrival_date > currentDate){
                        extraErrString += " - "+label_date_of_arrival+': <span class="text-danger">'+msg["not_greater_than_current_date"]+'</span><br />';
                        extraErrCheck = false;
                    }
                }

                var passport_number = line_list_data[i][col["passport_number"]];
                if(passport_number !== ""){
                    passport_number = passport_number.toString();
                    if(passport_number.length > 20){
                        extraErrString += " - "+label_passport_no+': <span class="text-danger">'+msg["not_greater_than"]+' 20 '+msg["char"]+'</span><br />';
                        extraErrCheck = false;
                    }
                }

                var flight_number = line_list_data[i][col["flight_number"]];
                if(flight_number !== ""){
                    flight_number = flight_number.toString();
                    if(flight_number.length > 20){
                        extraErrString += " - "+label_flight_number+': <span class="text-danger">'+msg["not_greater_than"]+' 20 '+msg["char"]+'</span><br />';
                        extraErrCheck = false;
                    }
                }

                var seat_number = line_list_data[i][col["seat_number"]];
                if(seat_number !== ""){
                    seat_number = seat_number.toString();
                    if(seat_number.length > 5){
                        extraErrString += " - "+label_seat_no+': <span class="text-danger">'+msg["not_greater_than"]+' 5 '+msg["char"]+'</span><br />';
                        extraErrCheck = false;
                    }
                }

                var test_covid_date = line_list_data[i][col["test_date"]];
                var test_covid_date = new Date(test_covid_date);
                
                if(test_covid_date !== ""){
                    if( test_covid_date > currentDate){
                        extraErrString += " - "+label_test_covid_date+': <span class="text-danger">'+msg["not_greater_than_current_date"]+'</span><br />';
                        extraErrCheck = false;
                    }
                }

                require_string += "<td>"+extraErrString+" &nbsp;</td>";
                require_string += "</tr>";
                
                if(!check_age 
                    || !check_name 
                    || !check_gender 
                    || !check_province 
                    || !check_district 
                    || !check_commune 
                    || !check_village 
                    || !check_phone
                    || !check_sample_source
                    || !check_requester 
                    || !check_collection_date
                    || !check_recieved_date 
                    || !check_test_result
                    || !check_test_result_date
                    || !check_perform_by
                    || !extraErrCheck
                    ){
                        array_check.push(false);                        
                }else{
                    //add test name for testing
                    //line_list_data[i][test_id_col] = 505;
                    patient_code                                    = line_list_data[i][col["patient_code"]];
                    patient_code                                    = patient_code.toString();
                    line_list_data[i][col["patient_code"]]          = patient_code.trim();

                    age                                             = line_list_data[i][col["age"]];
                    //line_list_data[i][col["age"]]                   = age.trim();
                    phone                                           = line_list_data[i][col["phone"]];
                    phone                                           = phone.toString();
                    line_list_data[i][col["phone"]]                 = phone.trim();

                    residence                                       = line_list_data[i][col["residence"]];
                    residence                                       = residence.toString();
                    line_list_data[i][col["residence"]]             = residence.trim();

                    is_contacted_with                               = line_list_data[i][col["is_contacted_with"]];
                    line_list_data[i][col["is_contacted_with"]]     = is_contacted_with.trim();
                    sample_number                                   = line_list_data[i][col["sample_number"]];
                    sample_number                                   = sample_number.toString();
                    line_list_data[i][col["sample_number"]]         = sample_number.trim();

                    line_list_data[i][col["sample_source"]]         = sample_source;
                    line_list_data[i][col["requester"]]             = requester;

                    diagnosis                                       = line_list_data[i][col["diagnosis"]];
                    diagnosis                                       = diagnosis.toString();
                    line_list_data[i][col["diagnosis"]]             = diagnosis.trim();

                    completed_by                                    = line_list_data[i][col["completed_by"]];
                    line_list_data[i][col["completed_by"]]          = completed_by.trim();
                    phone_completed_by                              = line_list_data[i][col["phone_completed_by"]];
                    phone_completed_by                              = phone_completed_by.toString();
                    line_list_data[i][col["phone_completed_by"]]    = phone_completed_by.trim();

                    sample_collector                                = line_list_data[i][col["sample_collector"]];
                    line_list_data[i][col["sample_collector"]]      = sample_collector.trim();
                    phone_number_sample_collctor                    = line_list_data[i][col["phone_number_sample_collctor"]];
                    phone_number_sample_collctor                    = phone_number_sample_collctor.toString();
                    line_list_data[i][col["phone_number_sample_collctor"]] = phone_number_sample_collctor.trim();

                    passport_number                                 = line_list_data[i][col["passport_number"]];
                    passport_number                                 = passport_number.toString();
                    line_list_data[i][col["passport_number"]]       = passport_number.trim();

                    flight_number                                   = line_list_data[i][col["flight_number"]];
                    flight_number                                   = flight_number.toString();
                    line_list_data[i][col["flight_number"]]         = flight_number.trim();

                    seat_number                                     = line_list_data[i][col["seat_number"]];
                    seat_number                                     = seat_number.toString();
                    line_list_data[i][col["seat_number"]]           = seat_number.trim();

                    contact_with                                    = line_list_data[i][col["is_contacted_with"]];
                    contact_with                                    = contact_with.toString();
                    line_list_data[i][col["is_contacted_with"]]     = contact_with.trim();

                    completed_by                                    = line_list_data[i][col["completed_by"]];
                    line_list_data[i][col["completed_by"]]          = completed_by.trim(); // completed by

                    phone_number                                    = line_list_data[i][col["phone_completed_by"]];
                    phone_number                                    = phone_number.toString(); // avoid phone number is integer
                    line_list_data[i][col["phone_completed_by"]]    = phone_number.trim(); // phone number of completed by

                    sample_collector                                = line_list_data[i][col["sample_collector"]];
                    line_list_data[i][col["sample_collector"]]      = sample_collector.trim(); // sample collector

                    phone_number_sample_collector                   = line_list_data[i][col["phone_number_sample_collctor"]];
                    phone_number_sample_collector                   = phone_number_sample_collector.toString();
                    line_list_data[i][col["phone_number_sample_collctor"]] = phone_number_sample_collector.trim();

                    data.push(line_list_data[i]);                    
                    array_check.push(true);
                }
            }
        }
        
        if(valid_check == 0){
            myDialog.showProgress('hide');
            myDialog.showDialog('show', {text: msg["not_data_entry"], style: 'warning'});
            $(this).removeClass('disabled'); //prevent multiple click
            return false;
        }
        //console.log(array_check);
        console.log("Check "+ array_check.indexOf(false));
        if((array_check.indexOf(false) >= 0)){
            myDialog.showProgress('hide');
            $("table[name=tblErrorLineListNew] tbody").html(require_string);
            $modal_list_error.modal('show');
            $(this).removeClass('disabled'); //prevent multiple click
            return false;
        }
        
     //   console.log(data);
     //   console.log(data.length);

        if(data.length > 500){
            myDialog.showProgress('hide');
            myDialog.showDialog('show', {text: msg["not_greater_than_100_row"], style: 'warning'});
            $(this).removeClass('disabled'); //prevent multiple click
            return false;
        }

        $.ajax({
            url: base_url + "/patient/add_line_list_full_form_test",
            type: "POST",
            data: { data: data},
            dataType: 'json',
            success: function (resText) {
                var patients = resText.patients;
                var bodyResult = '';    
                var psample_ids = '';
                console.log("execution_time "+resText.execution_time);
               // console.log(patients);
               // myDialog.showProgress('hide');
               // $("#btnSaveList").removeClass("disabled");
                var n = 1;
                for(var i in patients) {
                    var btnPrint = "";
                    var test_result_msg = "";
                    if(patients[i].psample_id !== undefined){
                        btnPrint = '<button type="button" class="btnPrintCovidFormV1" data-psample_id="'+patients[i].psample_id+'">'+label_print+'</button>'
                        psample_ids += patients[i].psample_id+"n";
                    }
                    if(patients[i].test_result_msg !== undefined){
                        test_result_msg = patients[i].test_result_msg;
                    }
                    bodyResult += '<tr>';
                    bodyResult += '<td>'+n+'</td>';
                    bodyResult += '<td>'+patients[i].patient_code+'</td>';
                    bodyResult += '<td>'+patients[i].patient_name+'</td>';
                    bodyResult += '<td>'+patients[i].msg+'</td>';  
                    bodyResult += '<td>'+patients[i].sample_number+'</td>';
                    bodyResult += '<td>'+patients[i].sample_msg+'</td>';                   
                    bodyResult += '<td>'+patients[i].test_msg+'</td>';
                    bodyResult += '<td>'+test_result_msg+'</td>';
                    bodyResult += '<td>'+btnPrint+'</td>';                
                    bodyResult += '</tr>';
                    n++;
                }
                setTimeout(function(){
                    myDialog.showProgress('hide');
                    $("table[name=tblResultLineListNew] tbody").html(bodyResult);
                    var res = psample_ids.substring(0, psample_ids.length - 1); // remove the last n
                    $("#printAll").attr("data-psample_id",res);
                    $("#modal_result_line_list_new").modal("show");
                }, 1000);
                
            },
            error: function(xhr, status, error) {
                var err = eval("(" + xhr.responseText + ")");
                console.log(status);
                console.log(err.message);
                console.log(xhr.responseText);
            }
        });
    });


    /*
    * 18-05-2021 
    * Add Result as line list 
    * avoid loading this for other user
    */
   if(is_admin == "1"){
       var col_name = {
            sample_number   : 0,
            patient_code    : 1,
            patient_name    : 2,
            test_name       : 3,
            test_result     : 4,
            test_date       : 5,
            perform_by      : 6,
       }
        var $modal_excel_result = $("#modal_excel_result");
        $("#btnModalAddResult").on("click",function(){
            $modal_excel_result.modal("show");
        })
        function setCommentColumn(col , msg){
            line_list_table_result.setComments(col,msg); // reset comment        
        }
        var line_list_table_result = jspreadsheet(document.getElementById('spreadsheetResult'), {
            minDimensions: [ 7, 50 ],
            defaultColWidth: 120,
            tableOverflow: true,
            tableHeight: "500px",
            columns: [
                { type:'text', title: label_sample_number,width:140 ,maxlength:20},
                { type:'text', title: label_patient_id ,maxlength:20 , width:150, readOnly: true},
                { type:'text', title: label_patient_name ,maxlength:60, width:150, readOnly: true} ,
                { type: 'text', title: label_test_name, width:300 , readOnly: true},
                { type: 'dropdown', title: label_result, width:115, source:['Negative', 'Positive'], readOnly: true},
                { type: 'calendar', title: label_test_date, width:130 , readOnly: true, options: { format:'YYYY-MM-DD' , readonly:true , validRange: [ '2021-01-01', today ]}},
                { type: 'dropdown', title: label_performed_by, width:120, source:performer_array, readOnly: true},
            ],
            allowComments:true,
            
            onchange:function(instance,cell, c, r, value) {
                // patient_code
                
                if( c == 0){
                    console.log(value+" "+value.length);
                    var row_num = r + 1
                    if(value !== ""){
                        var sample_number = value;
                        sample_number     = sample_number.toString();
                        sample_number     = sample_number.trim();
                        laboratory_id     = LABORATORY_SESSION.labID;
                        
                        console.log(sample_number);
                        console.log(laboratory_id);
                        $.ajax({
                            url: base_url + 'patient_sample/get_sample_by_sample_number',
                            type: 'POST',
                            data: {
                                patient_id: null, 
                                patient_sample_id:null, 
                                patient_code: null, 
                                sample_number:sample_number, 
                                laboratory_id: laboratory_id
                            },
                            dataType: 'json',
                            success: function (resText) {
                                console.log(resText);
                                var patient_sample  = resText.patient_sample;
                                var patient_info    = resText.patient_info;
                                var sample_status   = resText.sample_status;
                                var sample_tests    = resText.sample_tests;
                                //console.log(patient_sample);
                                
                                if(sample_status){
                                    setCommentColumn("A"+row_num, '');
                                                                        
                                    line_list_table_result.setComments("A"+c,''); // reset comment
                                    line_list_table_result.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["patient_code"], r]),false);
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["patient_code"], r]), patient_info.patient_code);
                                    line_list_table_result.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["patient_code"], r]),true);

                                    line_list_table_result.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["patient_name"], r]),false);
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["patient_name"], r]), patient_info.name);
                                    line_list_table_result.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["patient_name"], r]),true);
                                    
                                    // Test message
                                    if(resText.test_status){
                                        setCommentColumn("D"+row_num, resText.test_msg);
                                        test_name    = "";
                                        test_result  = "";
                                        test_date    = "";
                                        performer_id = "";
                                        sample_test_id = (sample_tests[0].sample_test_id !== "" || sample_tests[0].sample_test_id !== undefined) ? sample_tests[0].sample_test_id : 0;
                                        // check if result exist or not
                                        if(sample_test_id > 0){
                                            performer_id = sample_tests[0].performer_id;
                                            var performer_name = "";
                                            $.each(PERFORMERS, function(key, value) {
                                                if(value.ID == performer_id){
                                                    performer_name = value.performer_name;
                                                    return false;
                                                }
                                            });
                                            test_date = (sample_tests[0].test_date !== null || sample_tests[0].test_date !== undefined) ? sample_tests[0].test_date : "";
                                            test_name_id = sample_test_id;
                                            test_result_id = (sample_tests[0].result !== null || sample_tests[0].result !== undefined) ? sample_tests[0].result: "";

                                            if(test_result_id !== ""){
                                                test_result = (test_result_id == 4865 || test_result_id == 4858 || test_result_id == 4848) ? "Negative" : "Positive";
                                            }
                                            
                                            if(test_name_id == 506){
                                                test_name = "";
                                            }

                                            line_list_table_result.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["test_name"], r]),false);
                                            instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["test_name"], r]), test_name);
                                            line_list_table_result.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["test_name"], r]),true);   
                                            
                                            line_list_table_result.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["test_result"], r]),false);
                                            instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["test_result"], r]), test_result);
                                            
                                            line_list_table_result.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["test_date"], r]),false);
                                            instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["test_date"], r]), test_date);

                                            line_list_table_result.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["perform_by"], r]),false);
                                            instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["perform_by"], r]), performer_name);
                                        }
                                    }else{
                                        setCommentColumn("D"+row_num, resText.test_msg);
                                    }
                                    
                                }else{
                                    
                                    // add comment to the cell                                    
                                    setCommentColumn("A"+row_num, resText.sample_msg);
                                    line_list_table_result.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["patient_code"], r]),false);
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["patient_code"], r]), "");
                                    line_list_table_result.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["patient_code"], r]),true);

                                    line_list_table_result.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["patient_name"], r]),false);
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["patient_name"], r]), "");
                                    line_list_table_result.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["patient_name"], r]),true);

                                    line_list_table_result.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["test_name"], r]),false);
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["test_name"], r]), "");
                                    line_list_table_result.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["test_name"], r]),true);

                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["test_result"], r]), "");
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["test_date"], r]), "");
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["perform_by"], r]), "");
                                }
                                
                            },error(xhr,status,error){
                                console.log(status);
                                console.log(error);
                            }
                        })
                    }else{
                        // set other column READ ONLY
                        setCommentColumn("A"+row_num,'');
                        line_list_table_result.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["patient_code"], r]),false);
                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["patient_code"], r]), "");
                        line_list_table_result.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["patient_code"], r]),true);

                        line_list_table_result.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["patient_name"], r]),false);
                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["patient_name"], r]), "");
                        line_list_table_result.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["patient_name"], r]),true);

                        line_list_table_result.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["test_name"], r]),false);
                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["test_name"], r]), "");
                        line_list_table_result.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["test_name"], r]),true);

                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["test_result"], r]), "");
                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["test_date"], r]), "");
                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["perform_by"], r]), "");

                    }
                }
            }
        })
   }
    
/**End */
})
 
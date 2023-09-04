/** Add Modal Line List
     * added 19-03-2021
     */
 $(function () {
    var $btnSubmitPatientForm = $("#btnSubmitPatientForm");
    var $print_preview_covid_form_modal_1	= $("#print_preview_covid_form_modal");
    var $modal_error_line_list = $("#modal_error_line_list");
    function getProvinceCode_(name){
        var code = "";
        if(app_language == 'kh'){
            $.each(PROVINCES, function(key, row) {
                if(row.name_kh == name){
                    code = row.code;
                    return false;
                }
            });
        }else{
            $.each(PROVINCES, function(key, row) {
                if(row.name_en == name){
                    code = row.code;
                    return false;
                }
            });
        }
       return code;
    }
    function getDistrictCode_(name , province_code){
       var code = "";
       if(app_language == 'kh'){
        $.each(DISTRICTS, function(key, row) {
            if(row.name_kh == name && row.province_code == province_code){
                code = row.code;
                return false;
            }
        });
       }else{
        $.each(DISTRICTS, function(key, row) {
            if(row.name_en == name && row.province_code == province_code){
                code = row.code;
                return false;
            }
        });
       }
      return code;
   }
   function getCommuneCode_(name, district_code){
       var code = "";
       
       if(app_language == 'kh'){
        $.each(COMMUNES, function(key, row) {
            if(row.name_kh == name && row.district_code == district_code){
                code = row.code;
                return false;
            }
        });
       }else{
        $.each(COMMUNES, function(key, row) {
            if(row.name_en == name && row.district_code == district_code){
                code = row.code;
                return false;
            }
        });
       }
      return code;
   }
   function getVillageCode_(name , commune_code){
      var code = "";
      if(app_language == 'kh'){
        $.each(VILLAGES, function(key, row) {
            if(row.name_kh == name && row.commune_code == commune_code){
                code = row.code;
                return false;
            }
        });
      }else{
        $.each(VILLAGES, function(key, row) {
            if(row.name_en == name && row.commune_code == commune_code){
                code = row.code;
                return false;
            }
        });
      }
      return code;
   }

   function getReason_(reason){
        var code = "";
        $.each(REASON_FOR_TESTING_ARR, function(key, value) {
            if(value == reason){
                code = key;
                return false;
            }
        });
        return code;
    }
    function getSampleSource(sample_source){
        var code = "";     
        $.each(SAMPLE_SOURCE, function(key, value) {       
            if(value.source_name == sample_source){
                code = value.source_id;
                return false;
            }
        });
        return code;
    }
    function getRequester(requester,sample_source_id){
        var code = "";
        $.each(REQUESTER, function(key, value) {
            if(value.requester_name == requester && value.sample_source_id == sample_source_id){
                code = value.requester_id;
                return false;
            }
        });
        return code;
    }
    function getClinicalSymptom_(val){
        var res = val.split(";");
        var result = "";
        if(app_language == 'kh'){
            for(var i in res){
                $.each(CLINICAL_SYMPTOM, function(key, value) {
                    if(value.name_kh == res[i]){
                        result += value.ID+";";
                        return false;
                    }
                  });
            }
        }else{
            for(var i in res){
                $.each(CLINICAL_SYMPTOM, function(key, value) {
                    if(value.name_en == res[i]){
                        result += value.ID+";";
                        return false;
                    }
                });
            }
        }
        return result;
    }
    function getTestResultId( result, sample_test_id){
        var test_organism_id = "";
        var test_result = result_arr[sample_test_id];        
        $.each(test_result, function(key, row) {            
            if(row.organism_name == result && row.sample_test_id == sample_test_id){
                test_organism_id = row.test_organism_id;
                return false;
            }
        });
        return test_organism_id;
    }
    function getTest(test_name){
        var TEST_ARRAY = [
            {id:479, test_name: 'SARS-CoV-2 (Method: real time RT-PCR by GeneXpert)'}, 
            {id:497, test_name: 'SARS-CoV-2 (Applied Biosystems 7500 Fast Real Time PCR)'},
            {id:505, test_name: 'SARS-CoV-2 Rapid Antigen Test'},
            {id:509, test_name: 'SARS-CoV-2 (Method: real time RT-PCR by Cobas 6800)'},
			{id:516, test_name: 'SARS-CoV-2 (BIOER Gene 9660 Real Time PCR Instruments)'}
        ];
        var code = "";
        $.each(TEST_ARRAY, function(key, value) {
            if(value.test_name == test_name){
                code = value.id;
                return false;
            }
        });
        return code;
    }
    
    function getCountry(country_name){
        var code = "";
        $.each(COUNTRIES, function(key, value) {
            if(value.name_en == country_name){
                code = value.num_code;
                return false;
            }
        });
        return code;
    }
    function getNationality(name){
        var code = "";
        $.each(NATIONALITIES, function(key, value) {
            if(value.nationality_en == name){
                code = value.num_code;
                return false;
            }
        });
        return code;
    }
    function getPerformer(name){
        var code = "";
        $.each(PERFORMERS, function(key, value) {
            if(value.performer_name == name){
                code = value.ID;
                return false;
            }
        });
        return code;
    }
    function getVaccinationStatus(value){
        var code = "";
        $.each(VACCINATION_STATUS_ARR, function(key, status) {
            if(value == status){
                code = key;
                return false;
            }
        });
        return code;
    }
    function getVaccinationStatusString(code){
        var status = "";
        $.each(VACCINATION_STATUS_ARR, function(key, value) {
            if(code == key){
                status = value;
                return false;
            }
        });
        return status;
    }
    function getVaccineCode(value){
        var code = "";
        $.each(VACCINE_TYPE_ARR, function(key, status) {
            //console.log(value+" "+status.name);
            if(value == status.name){
                code = status.id;
                return false;
            }
        });
        return code;
    }
    function getVaccineName(id){
        var res = "";
        $.each(VACCINE_TYPE_ARR, function(key, status) {            
            if(id == status.id){
                res = status.name;
                return false;
            }
        });
        return res;
    }
    var today   = new Date();
    var dd      = String(today.getDate()).padStart(2, '0');
    var mm      = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
    var yyyy    = today.getFullYear();
    today       = yyyy+'-'+mm+'-'+dd;
     
    var col_name = {
        patient_code                    : 0,
        patient_name                    : 1,
        age                             : 2,
        gender                          : 3,
        phone                           : 4,
        province                        : 5,
        district                        : 6,
        commune                         : 7,
        village                         : 8,
        residence                       : 9,
        reason_for_testing              : 10,
        is_contacted                    : 11,
        is_contacted_with               : 12,
        is_directed_contact             : 13,
        sample_number                   : 14,
        sample_source                   : 15,
        requester                       : 16,
        collected_date                  : 17,
        received_date                   : 18,
        payment_type                    : 19,
        admission_date                  : 20,
        diagnosis                       : 21,
        is_urgent                       : 22,
        completed_by                    : 23,
        phone_completed_by              : 24,
        sample_collector                : 25,
        phone_number_sample_collctor    : 26,
        clinical_symptom                : 27,
        health_facility                 : 28,
        test_name                       : 29,
        machine_name                    : 30,
        test_result                     : 31,
        test_result_date                : 32,
        perform_by                      : 33,
        country                         : 34,
        nationality                     : 35,
        arrival_date                    : 36,
        passport_number                 : 37,
        flight_number                   : 38,
        seat_number                     : 39,
        is_positive_covid               : 40,
        test_date                       : 41,
        sex_id                          : 42,
        province_code                   : 43,
        district_code                   : 44,
        commune_code                    : 45,
        village_code                    : 46,
        reason_for_testing_id           : 47,
        sample_source_id                : 48,
        requester_id                    : 49,
        clinical_symtop_id              : 50,
        test_id                         : 51,
        country_id                      : 52,
        nationality_id                  : 53,
        number_of_sample                : 54,
        performer_by_id                 : 55,
        test_result_id                  : 56,
        is_camlis_patient               : 57,
        vaccination_status              : 58,
        first_vaccinated_date           : 59,
        second_vaccinated_date          : 60,
        vaccine_name                    : 61,
        third_vaccinated_date           : 62,
        second_vaccine_name             : 63,
        forth_vaccinated_date           : 64, //09022022
        third_vaccine_name              : 65, //09022022
        occupation                      : 66,
        vaccination_status_code         : 67,
        vaccine_id                      : 68,
        second_vaccine_id               : 69,
        third_vaccine_id                : 70, //09022022
    }

    var letter_col_name = {
        patient_code                    : "A",
        patient_name                    : "B",
        age                             : "C",
        gender                          : "D",
        phone                           : "E",
        province                        : "F",
        district                        : "G",
        commune                         : "H",
        village                         : "I",
        residence                       : "J",
        reason_for_testing              : "K",
        is_contacted                    : "L",
        is_contacted_with               : "M",
        is_directed_contact             : "N",
        sample_number                   : "O",
        sample_source                   : "P",
        requester                       : "Q",
        collected_date                  : "R",
        received_date                   : "S",
        payment_type                    : "T",
        admission_date                  : "U",
        diagnosis                       : "V",
        is_urgent                       : "W",
        completed_by                    : "X",
        phone_completed_by              : "Y",
        sample_collector                : "Z",
        phone_number_sample_collctor    : "AA",
        clinical_symptom                : "AB",
        health_facility                 : "AC",
        test_name                       : "AD",
        machine_name                    : "AE",
        test_result                     : "AF",
        test_result_date                : "AG",
        perform_by                      : "AH",
        country                         : "AI",
        nationality                     : "AJ",
        arrival_date                    : "AK",
        passport_number                 : "AL",
        flight_number                   : "AM",
        seat_number                     : "AN",
        is_positive_covid               : "AO",
        test_date                       : "AP",
        number_of_sample                : "AQ",
        vaccination_status              : "AR",
        first_vaccinated_date           : "AS",
        second_vaccinated_date          : "AT",
        vaccine_name                    : "AU",
        occupation                      : "AV",
    }
    var province_array              = [];
    var districts_array             = [];
    var communes_array              = [];
    var villages_array              = [];
    var nationalities_array         = [];
    var country_array               = [];
    var requester_array             = [];
    var reason_for_testing_array    = [];
    var clinical_symptom_array      = [];
    var sample_source_array         = [];
    var payment_type_array          = [];
    var performer_array             = [];
    var gender_array                = [];
    var test_name_array             = [
        'SARS-CoV-2 (Method: real time RT-PCR by GeneXpert)', 
        'SARS-CoV-2 (Applied Biosystems 7500 Fast Real Time PCR)',
        'SARS-CoV-2 Rapid Antigen Test',
        'SARS-CoV-2 (Method: real time RT-PCR by Cobas 6800)',
		'SARS-CoV-2 (BIOER Gene 9660 Real Time PCR Instruments)'
    ];
    var machine_name_array          = [];
    var test_result_array           = [];
    var maxRow                      = 500; 
    var maxLengthPatientCode        = 30;    
    var vaccination_status_array    = []; //13072021
    var vaccine_type_array          = []; //13072021
    
    var check_patients              = [];//04082021
    var maxSampleNumber             = 11;
    var machine_name_arr            = {
        497 : [],
        479 : [],
        505 : []
    };

    var result_arr = {
        497 : [],
        479 : [],
        505 : []
    };
    
    var getTestResult = function(sample_test_id) {
        $.ajax({
            url		: base_url + 'organism/get_sample_test_organism',
            type	: 'POST',
            data	: { sample_test_id: sample_test_id },
            dataType: 'json',
            success	: function (resText) {
                result_arr[sample_test_id] = resText;
                resText.forEach(function(data) {
                    test_result_array.push(data.organism_name);
                });
            }
        });
    };

    var getMachineName = function(test_id) {
        $.ajax({
            url: base_url + 'machine',
            type: 'POST',
            dataType: 'json',
            data: {
                id: test_id,
                lab_id: LABORATORY_SESSION.labID
            },
            success : function (resText) {
                machine_name_arr[test_id] = resText;
                // save machine in array
                resText.forEach(function(data) {                   
                    machine_name_array.push(data.machine_name);
                });
            },
            error : function() {}
        });
    };

    getTestResult(497);
    getTestResult(479);
    getTestResult(505);  
    getTestResult(509); //16082021  
    getTestResult(516); //01022023
	getMachineName(497);
    getMachineName(479);
    getMachineName(505);   
    getMachineName(509);   
	getMachineName(516);   

    if(app_language == 'kh'){
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
        $.each(CLINICAL_SYMPTOM, function(key, value) {
            clinical_symptom_array.push(value.name_kh);
        });
        gender_array                = ["ប្រុស" , "ស្រី"];
    }else{
        $.each(PROVINCES, function(key, value) {
            province_array.push(value.name_en);
        });
        
        $.each(DISTRICTS, function(key, value) {
            districts_array.push(value.name_en);
        });
        $.each(COMMUNES, function(key, value) {
            communes_array.push(value.name_en);
        });
        $.each(VILLAGES, function(key, value) {
            villages_array.push(value.name_en);
        });
        $.each(CLINICAL_SYMPTOM, function(key, value) {
            clinical_symptom_array.push(value.name_en);
        });
        gender_array   = ["Male" , "Female"];
    }
    
    $.each(NATIONALITIES, function(key, value) {        
        nationalities_array.push(value.nationality_en);
    });
    $.each(COUNTRIES, function(key, value) {
        country_array.push(value.name_en);
    });

    $.each(REQUESTER, function(key, value) {
        requester_array.push(value.requester_name);
    });
        
    $.each(REASON_FOR_TESTING_ARR, function(key, value) {
        reason_for_testing_array.push(value);
    });    
    
    $.each(SAMPLE_SOURCE, function(key, value) {
        sample_source_array.push(value.source_name);
    });
    $.each(PERFORMERS, function(key, value) {
        performer_array.push(value.performer_name);
    });
    $.each(PAYMENT_TYPE, function(key, value) {
        payment_type_array.push({id:value.id, name: value.name});
    });
    //13072021
    $.each(VACCINATION_STATUS_ARR, function(key, value) {
        vaccination_status_array.push(value);
    });
    $.each(VACCINE_TYPE_ARR, function(key, value) {
        vaccine_type_array.push(value.name);
    });
    
    //end
    var province_code = "";
    var district_code = "";
    var commune_code  = "";
    
    var districtFilter2 = function(instance, cell, c, r, source) {
        var value = instance.jexcel.getValueFromCoords(col_name["province"], r);
        var res = [];
        // get province id 
        if(app_language == 'kh'){
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
        }else{
            $.each(PROVINCES, function(key, row) {
                if(row.name_en == value){
                    province_code = row.code;
                    return false;
                }
            });
            $.each(DISTRICTS, function(key, item) {
                if(province_code == item.province_code){
                    res.push(item.name_en);
                }
            });
        }
        return res;
    }
    
    var communeFilter2 = function(instance, cell, c, r, source) {
        var value         = instance.jexcel.getValueFromCoords(col_name["district"], r);
        var res           = [];
        var province_code =  instance.jexcel.getValueFromCoords(col_name["province_code"], r);
        if(app_language == 'kh'){
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
        }else{
            $.each(DISTRICTS, function(key, row) {
                if(row.name_en == value && row.province_code == province_code){
                    district_code = row.code;
                    return false;
                }
            });
            $.each(COMMUNES, function(key, item) {
                if(district_code == item.district_code){
                    res.push(item.name_en);
                }
            });
        }
        
        return res;
    }
    
    var villageFilter2 = function(instance, cell, c, r, source) {
        var value           = instance.jexcel.getValueFromCoords(col_name["commune"], r);
        var res             = [];
        var district_code   =  instance.jexcel.getValueFromCoords(col_name["district_code"], r); // district_code
        
        if(app_language == 'kh'){
            $.each(COMMUNES, function(key, row) {
                if(row.name_kh == value && row.district_code == district_code){
                    commune_code = row.code;
                    return false;
                }
            });
            $.each(VILLAGES, function(key, item) {
                if(commune_code == item.commune_code){                    
                    res.push(item.name_kh);
                }
            });

        }else{           
            $.each(COMMUNES, function(key, row) {
                if(row.name_en == value && row.district_code == district_code){                    
                    commune_code = row.code;
                    return false;
                }
            });
            $.each(VILLAGES, function(key, item) {                
                if(commune_code == item.commune_code){                    
                    res.push(item.name_en);
                }
            });
           
        }
        return res;
    }
    
    var requesterFilter2 = function(instance, cell, c, r, source) {
        var value = instance.jexcel.getValueFromCoords(c - 1, r);
        var sample_source =  instance.jexcel.getValueFromCoords(col_name["sample_source_id"], r);
        var res = [];        
        $.each(REQUESTER, function(key, item) {
            if(sample_source == item.sample_source_id){
                res.push(item.requester_name);
            }
        });
        return res;
    }
    
    var machineNameFilter = function(instance, cell, c, r, source) {
        var test_id = instance.jexcel.getValueFromCoords(col_name["test_id"], r);
        var res = [];
        var machine_name = machine_name_arr[test_id];      
       
        if(machine_name.length > 0){
           for(var i in machine_name){            
                res.push(machine_name[i].machine_name);
           }
        }        
        return res;
    }
    var testResultFilter = function(instance, cell, c, r, source) {
        var test_id = instance.jexcel.getValueFromCoords(col_name["test_id"], r);
        var res = [];
        var result = result_arr[test_id];

        if(result.length > 0){
           for(var i in result){
                res.push(result[i].organism_name);
           }
        }
        return res;
    }
    var machineNameFilterShortForm = function(instance, cell, c, r, source) {
        //var test_id = instance.jexcel.getValueFromCoords(col_name["test_id"], r);
        var test_id = $modal_excel_short_form.find('select[name=test_name]').val();
        var res = [];
        if(test_id !== '-1'){
            var machine_name = machine_name_arr[test_id];
            if(machine_name.length > 0){
            for(var i in machine_name){
                    res.push(machine_name[i].machine_name);
            }
            }
        }
        return res;
    }
    var testResultFilterShortForm = function(instance, cell, c, r, source) {
        var test_id = $modal_excel_short_form.find('select[name=test_name]').val();
        var res = [];
        if(test_id !== '-1'){
            var result = result_arr[test_id];
            if(result.length > 0){
            for(var i in result){
                res.push(result[i].organism_name);
            }
            }
        }
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
            { type:'dropdown', title:label_sex+'*',width:60, source: gender_array},
            { type:'text', title: label_patient_phone_number,maxlength:100 },
            { type: 'autocomplete', title: label_province+'*', width:100, source:province_array },
            { type: 'autocomplete', title: label_district+'*', width:100, source:districts_array , filter: districtFilter2},
            { type: 'autocomplete', title: label_commune+'*', width:100, source:communes_array , filter: communeFilter2},
            { type: 'autocomplete', title: label_village+'*', width:100, source:villages_array ,filter: villageFilter2},
            { type:'text', title: label_residence ,width:80 ,maxlength:100},
            { type: 'autocomplete', title: label_reason_for_testing+"*", width:120, source:reason_for_testing_array },
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

            { type:'text', title: label_health_facility , maxlength:150 },

            { type: 'dropdown', title: label_test_name, width:150, source:test_name_array},
            { type: 'dropdown', title: label_machine_name, width:150, source: machine_name_array, filter:machineNameFilter, readOnly: true},
            
            { type: 'dropdown', title: label_result, width:70,source:test_result_array, filter: testResultFilter, readOnly: true},
            { type: 'calendar', title: label_test_date, width:150 ,  readOnly: true, options: { format:'YYYY-MM-DD' ,  readonly:true , validRange: [ '2021-01-01', today ]}},
            { type: 'autocomplete', title: label_performed_by, width:120, source:performer_array, readOnly: true},
            
           /* { type: 'autocomplete', title: label_country, width:80, source:country_array }, */
            { type: 'text', title: label_country, width:80, maxlength: 150 },
            { type: 'autocomplete', title: label_nationality, width:80, source:nationalities_array },            
            { type: 'calendar', title: label_date_of_arrival, width: 100,options: { format:'YYYY-MM-DD' , readonly:true } },
            { type:'text', title: label_passport_no, width:80 , maxlength:20},
            { type:'text', title: label_flight_number ,maxlength:10 },
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
            { type:'dropdown', title: label_number_of_sample ,width:120,source:[1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30]},
            { type:'text', title:'performer_by_id'},
            { type:'text', title:'test_result_id'},
            { type:'text', title:'is_camlis_patient'},
            { type:'dropdown', title:label_vaccination_status, source:vaccination_status_array},
            { type:'calendar', title: label_first_injection_date, readOnly:true, width:90,options: { format:'YYYY-MM-DD' , readonly:true , validRange: [ '2021-01-01', today ]} },
            { type:'calendar', title: label_second_injection_date, readOnly:true, width:90,options: { format:'YYYY-MM-DD' , readonly:true , validRange: [ '2021-01-01', today ]} },
            { type:'dropdown', title:label_vaccine_type , source: vaccine_type_array, readOnly: true, width:90},
            { type:'calendar', title: label_third_injection_date, readOnly:true, width:90,options: { format:'YYYY-MM-DD' , readonly:true , validRange: [ '2021-01-01', today ]} },
            { type:'dropdown', title:label_vaccine_type , source: vaccine_type_array, readOnly: true, width:90},
            { type:'calendar', title: label_forth_injection_date, readOnly:true, width:90,options: { format:'YYYY-MM-DD' , readonly:true , validRange: [ '2021-01-01', today ]} },//09022022
            { type:'dropdown', title:label_vaccine_type , source: vaccine_type_array, readOnly: true, width:90},//09022022
            { type:'text', title: label_occupation, maxlength:100},
            { type:'text', title: 'vaccination_status_code'},
            { type:'text', title: 'vaccine_id'},
            { type:'text', title: 'second_vaccine_id'},
            { type:'text', title: 'third_vaccine_id'}, //09022022
        ],
        nestedHeaders:[
            [
                {
                    title: label_patient_info,
                    colspan: '14',
                },
                {
                    title: label_sample_info,
                    colspan: '17',
                },
                {
                    title: label_patient_info,
                    colspan: '8',
                },
                {
                    title: label_sample_info,
                    colspan: '1',
                },
                {
                    title: label_patient_info,
                    colspan: '10',
                },
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
                    colspan: '5'
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
                },
                {
                    title: label_covid_questionaire,
                    colspan: '10',
                },
            ],
        ],
        onchange:function(instance,cell, c, r, value) {
            // patient_code
            //console.log("Col= "+c);
            var rowNumber = parseInt(r) + 1;
            
            if( c == 0){
                //console.log(check_patients)
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
                            console.log(resText);
                            var patient = resText.patient;
                           //console.log(patient);

                            if(patient){
                                //console.log(check_patients);
                                check_patients[r] = true;

                                //console.log("patient_code "+patient.patient_code);
                                var dob = "";
                                if(app_language == 'kh'){
                                    var sex = (patient.sex == 'M') ? "ប្រុស" : "ស្រី";
                                    var province = patient.province_kh;
                                    var district = patient.district_kh;
                                    var commune  = patient.commune_kh;
                                    var village  = patient.village_kh;
                                }else{
                                    var sex = (patient.sex == 'M') ? "Male" : "Female";
                                    var province = patient.province_en;
                                    var district = patient.district_en;
                                    var commune  = patient.commune_en;
                                    var village  = patient.village_en;
                                }

                                var dob                 = moment(patient.dob, 'YYYY-MM-DD');
                                var age                 = calculateAge(dob);                                
                                var is_positive_covid   = (patient.is_positive_covid == null || patient.is_positive_covid == undefined || patient.is_positive_covid == false) ? false : patient.is_positive_covid;
                                var is_contacted        = (patient.is_contacted == null || patient.is_contacted == undefined) ? false : patient.is_contacted;
                                var contact_with        = (patient.contact_with == null || patient.contact_with == undefined) ? "" : patient.contact_with;
                                var is_direct_contact   = (patient.is_direct_contact == null || patient.is_direct_contact == undefined) ? false : patient.is_direct_contact;
                                var test_date           = (patient.test_date == undefined || patient.test_date == null) ? "" : patient.test_date;;
                                var name                = patient.name;
                                var residence           = (patient.residence == undefined || patient.residence == null) ? "" : patient.residence;
                                var date_arrival        = (patient.date_arrival == undefined || patient.date_arrival == null) ? "" : patient.date_arrival;
                                
                                var country             = (patient.country_name_en == undefined || patient.country_name_en == null) ? "" : patient.country_name_en;
                                if(patient.country_name == undefined || patient.country_name == null){
                                    country = "";
                                }else{
                                    country = patient.country_name;
                                }

                                var nationality         = (patient.nationality_en == undefined || patient.nationality_en == null) ? "" : patient.nationality_en;
                                var passport_number     = (patient.passport_number == undefined || patient.passport_number == null) ? "" : patient.passport_number;
                                var flight_number       = (patient.flight_number == undefined || patient.flight_number == null) ? "" : patient.flight_number;
                                var seat_number         = (patient.seat_number == undefined || patient.seat_number == null) ? "" : patient.seat_number;
                                var phone               = (patient.phone == undefined || patient.phone == null) ? "" : (patient.phone).trim();
                                var is_camlis_patient   = resText.is_camlis_patient == undefined ? 0 : 1; //22062021                                
                                //14-07-2021
                                var vaccination_status_code  = (patient.vaccination_status == undefined || patient.vaccination_status == null) ? "" : patient.vaccination_status;
                                var vaccine_type              = (patient.vaccine_id == undefined || patient.vaccine_id == null) ? "" : patient.vaccine_id;
                                var first_vaccinated_date     = (patient.first_vaccinated_date == undefined || patient.first_vaccinated_date == null) ? "" : patient.first_vaccinated_date;
                                var second_vaccinated_date    = (patient.second_vaccinated_date == undefined || patient.second_vaccinated_date == null) ? "" : patient.second_vaccinated_date;
                                var occupation                = (patient.occupation == undefined || patient.occupation == null) ? "" : patient.occupation;
                                var second_vaccine_type       = (patient.second_vaccine_id == undefined || patient.second_vaccine_id == null) ? "" : patient.second_vaccine_id;
                                var third_vaccinated_date     = (patient.third_vaccinated_date == undefined || patient.third_vaccinated_date == null) ? "" : patient.third_vaccinated_date;
                                var forth_vaccinated_date     = (patient.forth_vaccinated_date == undefined || patient.forth_vaccinated_date == null) ? "" : patient.forth_vaccinated_date; //09022022
                                var third_vaccine_type        = (patient.third_vaccine_id == undefined || patient.third_vaccine_id == null) ? "" : patient.third_vaccine_id;//09022022
                                //End
                                
                                var rowData = [
                                    name,
                                    age.years,
                                    sex,
                                    phone,
                                    province,
                                    district,
                                    commune,
                                    village,
                                    residence
                                ];
                                
                                for(i = 0 ; i < 9;i++){
                                    var nameColumn = jspreadsheet.getColumnNameFromId([c+nCol, r]);
                                    instance.jexcel.setValue(nameColumn, rowData[i]);
                                    nCol++;
                                }

                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["is_contacted"], r]), is_contacted);
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["is_contacted_with"], r]), contact_with);
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["is_directed_contact"], r]), is_direct_contact);
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["country"], r]), country);
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["nationality"], r]), nationality);
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["arrival_date"], r]), date_arrival);
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["passport_number"], r]), passport_number);
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["flight_number"], r]), flight_number);
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["seat_number"], r]), seat_number);
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["is_positive_covid"], r]), is_positive_covid);                            
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["test_date"], r]), test_date);
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["is_camlis_patient"], r]), is_camlis_patient); //22062021
                                //14072021
                                if(vaccination_status_code !== ""){
                                    var status = getVaccinationStatusString(vaccination_status_code);
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["vaccination_status"], r]), status);
                                    var vaccine_name = getVaccineName(vaccine_type);
                                    var second_vaccine_name = getVaccineName(second_vaccine_type);
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["vaccine_name"], r]), vaccine_name);  
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["first_vaccinated_date"], r]), first_vaccinated_date); 
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["second_vaccinated_date"], r]), second_vaccinated_date);
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["second_vaccine_name"], r]), second_vaccine_name);  
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["third_vaccinated_date"], r]), third_vaccinated_date); 
                                    var third_vaccine_name = getVaccineName(third_vaccine_type); //09022022
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["third_vaccine_name"], r]), third_vaccine_name);  //09022022
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["forth_vaccinated_date"], r]), forth_vaccinated_date); //09022022
                                }
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["occupation"], r]), occupation);
                            }else{
                                if(check_patients[r] !== undefined){
                                    //console.log(check_patients[r])
                                    if(check_patients[r] == true){
                                        check_patients[r] = false;
                                        // Reset columns
                                        nCol = 1;
                                        for(i = 0 ; i < 9;i++){
                                            var nameColumn = jspreadsheet.getColumnNameFromId([c+nCol, r]);
                                            instance.jexcel.setValue(nameColumn, "");
                                            nCol++;
                                        }
                                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["is_contacted"], r]), false);
                                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["is_contacted_with"], r]), "");                            
                                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["is_directed_contact"], r]), false);
                                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["country"], r]), "");
                                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["nationality"], r]), "");
                                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["arrival_date"], r]), "");
                                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["passport_number"], r]), "");
                                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["flight_number"], r]), "");
                                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["seat_number"], r]), "");
                                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["is_positive_covid"], r]), false);                            
                                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["test_date"], r]), "");
                                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["is_camlis_patient"], r]), ""); //22062021
                                        //14072021
                                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["vaccination_status"], r]), "");
                                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["vaccine_name"], r]), "");
                                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["first_vaccinated_date"], r]), first_vaccinated_date); 
                                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["second_vaccinated_date"], r]), second_vaccinated_date);
                                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["occupation"], r]), "");
                                        //End
                                    }
                                }
                                
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
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["is_contacted"], r]), false);
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["is_contacted_with"], r]), "");                            
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["is_directed_contact"], r]), false);
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["country"], r]), "");
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["nationality"], r]), "");
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["arrival_date"], r]), "");
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["passport_number"], r]), "");
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["flight_number"], r]), "");
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["seat_number"], r]), "");
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["is_positive_covid"], r]), false);                            
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["test_date"], r]), "");
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["is_camlis_patient"], r]), ""); //22062021
                    //14072021
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["vaccination_status"], r]), "");
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["vaccine_name"], r]), "");
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["first_vaccinated_date"], r]), first_vaccinated_date); 
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["second_vaccinated_date"], r]), second_vaccinated_date);
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["occupation"], r]), "");
                    //End
                }
            }
            // sex column
            if(c == col_name["gender"]){
                //console.log("C = "+c +" col gender = "+col_name["gender"]);
                if(value !== ""){
                    // save id of sex in column 35
                    
                    if(app_language == 'kh'){
                        sex = value == 'ប្រុស' ? 1 : 2;
                    }else{
                        sex = value == 'Male' ? 1 : 2;
                    }
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["sex_id"], r]),sex);                    
                    line_list_table_new.setComments("D"+rowNumber,"");
                }
            }
            // province column
            if (c == col_name["province"]) {
                //console.log("Province "+value);
                if(value !== ""){
                    // set null value to district col                    
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["district"], r]), "");
                    code = getProvinceCode_(value);                    
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["province_code"], r]),code); // save province code in column 38
                    line_list_table_new.setComments(letter_col_name["province"]+rowNumber,"");
                }               
            }
            // district column
            if (c == col_name["district"]) {
                if(value !== ""){
                    // set null value to commune 
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["commune"], r]), "");
                    province_code = line_list_table_new.getValueFromCoords(col_name["province_code"],r);
                    code = getDistrictCode_(value, province_code);
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["district_code"], r]), code);
                    line_list_table_new.setComments(letter_col_name["district"]+rowNumber,"");
                }
            }
            // commune column
            if (c == col_name["commune"]) {
                //console.log("Village "+value);
                if(value !== ""){
                    // set null value to village                     
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["village"], r]), "");
                    district_code = line_list_table_new.getValueFromCoords(col_name["district_code"],r);
                    code = getCommuneCode_(value, district_code);                    
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["commune_code"], r]), code);
                    line_list_table_new.setComments(letter_col_name["commune"]+rowNumber,"");
                }
            }
            // village column
            if (c == col_name["village"]) {
                //console.log("Village "+value);
                if(value !== ""){
                    commune_code = line_list_table_new.getValueFromCoords(col_name["commune_code"],r);
                    code = getVillageCode_(value, commune_code);
                    //console.log("get village code "+code);                    
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["village_code"], r]), code);
                    line_list_table_new.setComments(letter_col_name["village"]+rowNumber,"");
                }
            }
            // reason for testing column
            if (c == col_name["reason_for_testing"]) {
                if(value !== ""){
                    code = getReason_(value);
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["reason_for_testing_id"], r]), code);
                    line_list_table_new.setComments(letter_col_name["reason_for_testing"]+rowNumber,"");
                }
            }
            // is contacted column
            if (c == col_name["is_contacted"]) {
                var is_contacted_column = jspreadsheet.getColumnNameFromId([col_name["is_contacted_with"], r]);
                var is_direct_contact_column = jspreadsheet.getColumnNameFromId([col_name["is_directed_contact"], r]);
                
                if(value){
                    //console.log("true is right");
                   line_list_table_new.setReadOnly(is_contacted_column,false);
                   line_list_table_new.setReadOnly(is_direct_contact_column,false);
                }else{
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["is_contacted_with"], r]),"");
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["is_directed_contact"], r]),"");
                   line_list_table_new.setReadOnly(is_contacted_column,true);
                   line_list_table_new.setReadOnly(is_direct_contact_column,true);
                }
            }
            // sample source column
            if (c == col_name["sample_source"]) {
                if(value !== ""){
                    // set
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["requester"], r]),"");
                    // save id of sample source in column 43
                    code = getSampleSource(value);
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["sample_source_id"], r]), code);
                    line_list_table_new.setComments(letter_col_name["sample_source"]+rowNumber,"");
                }
            }

            // requester column
            if (c == col_name["requester"]) {
                if(value !== ""){
                    sample_source_id = line_list_table_new.getValueFromCoords(col_name["sample_source_id"],r);
                    code = getRequester(value,sample_source_id);
                    var columnName = jspreadsheet.getColumnNameFromId([col_name["requester_id"], r]);
                    instance.jexcel.setValue(columnName, code);
                    line_list_table_new.setComments(letter_col_name["requester"]+rowNumber,"");
                }
            }
            if(c == col_name["collected_date"]){
                if(value !== ""){
                    line_list_table_new.setComments(letter_col_name["collected_date"]+rowNumber,"");
                }
            }
            if(c == col_name["received_date"]){
                if(value !== ""){
                    line_list_table_new.setComments(letter_col_name["received_date"]+rowNumber,"");
                }
            }
            // clinical symptom column
            if (c == col_name["clinical_symptom"]) {
                if(value !== ""){                    
                    code = getClinicalSymptom_(value);
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["clinical_symtop_id"], r]), code);
                    line_list_table_new.setComments(letter_col_name["clinical_symptom"]+rowNumber,"");
                }
            }
            // Test column
            if (c == col_name["test_name"]) {
                //console.log(col_name["test_name"]);
                var test_result_col         = jspreadsheet.getColumnNameFromId([col_name["test_result"], r]);
                var test_result_date_col    = jspreadsheet.getColumnNameFromId([col_name["test_result_date"], r]);
                var perform_by_col          = jspreadsheet.getColumnNameFromId([col_name["perform_by"], r]);
                var machine_name_col        = jspreadsheet.getColumnNameFromId([col_name["machine_name"], r]);
                var test_result_id_col      = jspreadsheet.getColumnNameFromId([col_name["test_result_id"], r]);
                
                if(value !== ""){

                    // reset value of the these columns
                    instance.jexcel.setValue(test_result_col, "");
                    instance.jexcel.setValue(test_result_date_col, "");
                    instance.jexcel.setValue(perform_by_col, "");
                    instance.jexcel.setValue(machine_name_col, "");
                    instance.jexcel.setValue(test_result_id_col, "");

                    code = getTest(value);
                    //console.log("Test ID "+ code);
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["test_id"], r]), code);
                    line_list_table_new.setComments(letter_col_name["test_name"]+rowNumber,"");
                    // remove read only from result, test date and perform_by column
                    line_list_table_new.setReadOnly(test_result_col,false);
                    line_list_table_new.setReadOnly(test_result_date_col,false);
                    line_list_table_new.setReadOnly(perform_by_col,false);
                    line_list_table_new.setReadOnly(machine_name_col,false);                    
                    
                }else{
                    // reset value and set readonly
                    instance.jexcel.setValue(test_result_col, "");
                    instance.jexcel.setValue(test_result_date_col, "");
                    instance.jexcel.setValue(perform_by_col, "");
                    instance.jexcel.setValue(machine_name_col, "");

                    line_list_table_new.setReadOnly(test_result_col,true);
                    line_list_table_new.setReadOnly(test_result_date_col,true);
                    line_list_table_new.setReadOnly(perform_by_col,true);
                    line_list_table_new.setReadOnly(machine_name_col,true);
                }
            }
            if(c == col_name["machine_name"]){
                if(value !== ""){
                    line_list_table_new.setComments(letter_col_name["machine_name"]+rowNumber,"");
                }
            }
            if(c == col_name["test_result"]){
                if(value !== ""){
                    var sample_test_id = line_list_table_new.getValueFromCoords(col_name["test_id"],r);
                    var test_result_id = getTestResultId(value , sample_test_id);
                    //console.log(test_result_id);
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["test_result_id"], r]), test_result_id);
                    line_list_table_new.setComments(letter_col_name["test_result"]+rowNumber,"");
                }
            }
            if( c == col_name["test_result_date"]){
                if(value !== ""){
                    //console.log("performer id "+value);
                }
            }
            if (c == col_name["perform_by"]){
                if(value !== ""){
                    code = getPerformer(value);
                    //console.log("performer id "+code);  
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["performer_by_id"], r]), code);
                    line_list_table_new.setComments(letter_col_name["perform_by"]+rowNumber,"");
                }
            }
            // country column
            /*
            if (c == col_name["country"]) {
                if(value !== ""){
                   code = getCountry(value);
                   instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["country_id"], r]), code);
                }
            }
            */

            // nationality column
            if (c == col_name["nationality"]) {
                if(value !== ""){                    
                    code = getNationality(value);
                    //console.log("Nationality code: "+code);                    
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["nationality_id"], r]), code);
                    line_list_table_new.setComments(letter_col_name["nationality"]+rowNumber,"");
                }
            }
            // if check is true, enable Test Date
            if (c == col_name["is_positive_covid"]) {
                //console.log(value);
                var colummnName = jspreadsheet.getColumnNameFromId([col_name["test_date"], r]);
                if(value){
                   line_list_table_new.setReadOnly(colummnName,false);
                }else{
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["test_date"], r]), "");
                    line_list_table_new.setReadOnly(colummnName,true);
                }
            }
            if (c == col_name["test_date"]) {
                if(value){
                    line_list_table_new.setComments(letter_col_name["test_date"]+rowNumber,"");
                }
            }
            //13072021
            if (c == col_name["vaccination_status"]) {
                //console.log(value);
                if(value !== ""){
                    status = getVaccinationStatus(value);
                    //console.log(status);
                    if(status == 1 || status == ""){
                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["vaccine_name"], r]), "");
                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["first_vaccinated_date"], r]), "");
                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["second_vaccinated_date"], r]), "");

                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["second_vaccine_name"], r]), "");
                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["third_vaccinated_date"], r]), "");

                        line_list_table_new.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["vaccine_name"], r]),true);
                        line_list_table_new.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["first_vaccinated_date"], r]),true);
                        line_list_table_new.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["second_vaccinated_date"], r]),true);
                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["vaccination_status_code"], r]), status);
                    }else if(status == 2){
                        // Enable first injection date and vaccine name
                        line_list_table_new.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["vaccine_name"], r]),false);
                        line_list_table_new.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["first_vaccinated_date"], r]),false);

                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["second_vaccinated_date"], r]), "");
                        line_list_table_new.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["second_vaccinated_date"], r]),true);

                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["third_vaccinated_date"], r]), "");
                        line_list_table_new.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["third_vaccinated_date"], r]),true);

                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["second_vaccine_name"], r]), "");
                        line_list_table_new.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["second_vaccine_name"], r]),true);
                        

                    }else if (status == 3){
                        line_list_table_new.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["vaccine_name"], r]),false);
                        line_list_table_new.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["first_vaccinated_date"], r]),false);
                        line_list_table_new.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["second_vaccinated_date"], r]),false);

                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["third_vaccinated_date"], r]), "");
                        line_list_table_new.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["third_vaccinated_date"], r]),true);

                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["second_vaccine_name"], r]), "");
                        line_list_table_new.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["second_vaccine_name"], r]),true);

                    }else if (status == 4){
                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["vaccination_status_code"], r]), status);
                        line_list_table_new.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["vaccine_name"], r]),false);
                        line_list_table_new.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["first_vaccinated_date"], r]),false);
                        line_list_table_new.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["second_vaccinated_date"], r]),false);
                        line_list_table_new.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["third_vaccinated_date"], r]),false);
                        line_list_table_new.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["second_vaccine_name"], r]),false);
                    } //09022022
                    else if (status == 5){
                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["vaccination_status_code"], r]), status);
                        line_list_table_new.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["vaccine_name"], r]),false);
                        line_list_table_new.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["first_vaccinated_date"], r]),false);
                        line_list_table_new.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["second_vaccinated_date"], r]),false);
                        line_list_table_new.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["third_vaccinated_date"], r]),false);
                        line_list_table_new.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["second_vaccine_name"], r]),false);
                        line_list_table_new.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["forth_vaccinated_date"], r]),false);
                        line_list_table_new.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["third_vaccine_name"], r]),false);
                    }
                    
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["vaccination_status_code"], r]), status);
                }
            }
            if (c == col_name["vaccine_name"]) {
                //console.log(value);
                if(value !== ""){
                    status = getVaccineCode(value);
                    //console.log(status)
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["vaccine_id"], r]), status);
                }else{
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["vaccine_id"], r]), "");
                }
            }
            if (c == col_name["second_vaccine_name"]) {
                if(value !== ""){
                    status = getVaccineCode(value);
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["second_vaccine_id"], r]), status);
                }else{
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["second_vaccine_id"], r]), "");
                }
            }
            //09022022
            
            if (c == col_name["third_vaccine_name"]) {
                if(value !== ""){
                    status = getVaccineCode(value);
                    //console.log(status);
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["third_vaccine_id"], r]), status);
                }else{
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["third_vaccine_id"], r]), "");
                }
            }
            
        },        
        oncreateeditor: function(el, cell, x, y) {
            if (x == col_name["phone"] 
                || x == col_name["age"] 
                || x == col_name["patient_name"] 
                || x == col_name["patient_code"] 
                || x == col_name["residence"] 
                || x == col_name["sample_source"] 
                || x == col_name["diagnosis"] 
                || x == col_name["completed_by"]  
                || x == col_name["phone_completed_by"] 
                || x == col_name["sample_collector"] 
                || x == col_name["phone_number_sample_collctor"] 
                || x == col_name["passport_number"] 
                || x == col_name["flight_number"] 
                || x == col_name["seat_number"]
                || x == col_name["country"]
                || x == col_name["occupation"]
            ){
            var config = el.jexcel.options.columns[x].maxlength;
                cell.children[0].setAttribute('maxlength' , config); // set maxlength to column
            }
        }
    });
    
    line_list_table_new.hideColumn(col_name["payment_type"]); // hide payment type
    line_list_table_new.hideColumn(col_name["admission_date"]); // hide admision column
    line_list_table_new.hideColumn(col_name["is_urgent"]);
    line_list_table_new.hideColumn(col_name["sex_id"]);
    line_list_table_new.hideColumn(col_name["test_id"]);
    line_list_table_new.hideColumn(col_name["country_id"]);
    line_list_table_new.hideColumn(col_name["nationality_id"]);
    line_list_table_new.hideColumn(col_name["performer_by_id"]);
    line_list_table_new.hideColumn(col_name["province_code"]);
    line_list_table_new.hideColumn(col_name["district_code"]);
    line_list_table_new.hideColumn(col_name["commune_code"]);
    line_list_table_new.hideColumn(col_name["village_code"]);
    line_list_table_new.hideColumn(col_name["reason_for_testing_id"]);
    line_list_table_new.hideColumn(col_name["sample_source_id"]);
    line_list_table_new.hideColumn(col_name["requester_id"]);
    line_list_table_new.hideColumn(col_name["clinical_symtop_id"]);
    line_list_table_new.hideColumn(col_name["test_result_id"]);
    line_list_table_new.hideColumn(col_name["is_camlis_patient"]);
    line_list_table_new.hideColumn(col_name["vaccine_id"]);
    line_list_table_new.hideColumn(col_name["vaccination_status_code"]);
    line_list_table_new.hideColumn(col_name["second_vaccine_id"]);
    line_list_table_new.hideColumn(col_name["third_vaccine_id"]); //09022022
    
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
            var name = line_list_data[i][col_name["patient_name"]];
            var namelenght = name.length;           
            var check_name = false;
            if(name.trim() !== ""){
                if(valid_check !== 1){
                    valid_check     = 1;
                }
                
                // age column
                var age         = line_list_data[i][col_name["age"]];
                var check_age   = false;
                require_string += "<tr>";
                require_string += "<td>"+line_list_data[i][col_name["patient_code"]]+"</td>";

                if(name.length > 60){
                     require_string += '<td class="text-danger">'+msg["not_greater_than_60"]+'</td>';
                }else{
                    require_string += "<td>"+name+"</td>";
                    check_name = true;
                }              
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

                var gender       = line_list_data[i][col_name["gender"]];
                var check_gender = false;
                var gender_id    = line_list_data[i][col_name["sex_id"]];
            
                if(gender.length == 0 || gender_id.length == 0){
                    require_string += '<td class="text-danger">'+msg["not_select"]+'</td>';
                }else{
                    require_string += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                    var check_gender = true;
                }
                //console.log("Gender: "+gender+" "+check_gender+" "+gender.length);
                var phone        = line_list_data[i][col_name["phone"]];
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
                var province        = (line_list_data[i][col_name["province"]] == null || line_list_data[i][col_name["province"]] == undefined) ? "" : line_list_data[i][col_name["province"]];
                var check_province  = false;
                var province_id     = (line_list_data[i][col_name["province_code"]] == null || line_list_data[i][col_name["province_code"]] == undefined) ? "" : line_list_data[i][col_name["province_code"]];
                if(province.length == 0 || province_id.length == 0){
                    require_string += '<td class="text-danger">'+msg["not_select"]+'</td>';
                }else{
                    require_string += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                    check_province = true;
                }
                //console.log("province: "+province+" "+check_province);

                var district        = (line_list_data[i][col_name["district"]] == null || line_list_data[i][col_name["district"]] == undefined) ? "" : line_list_data[i][col_name["district"]];
                var check_district  = false;
                var district_id     = (line_list_data[i][col_name["district_code"]] == null || line_list_data[i][col_name["district_code"]] == undefined) ? "" : line_list_data[i][col_name["district_code"]];
                //console.log(district.length+ ' '+district_id.length);
                if(district.length == 0 || district_id.length == 0){
                    require_string += '<td class="text-danger">'+msg["not_select"]+'</td>';
                }else{
                    require_string += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                    check_district = true;
                }
                //console.log("district: "+district+" "+check_district);
                // Commune
                var commune         = (line_list_data[i][col_name["commune"]] == null || line_list_data[i][col_name["commune"]] == undefined) ? "" : line_list_data[i][col_name["commune"]];
                var check_commune   = false;
                var commune_id      = (line_list_data[i][col_name["commune_code"]] == null || line_list_data[i][col_name["commune_code"]] == undefined) ? "" : line_list_data[i][col_name["commune_code"]];
                if(commune.length == 0 || commune_id.length == 0){
                    require_string += '<td class="text-danger">'+msg["not_select"]+'</td>';
                }else{
                    require_string += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                    check_commune = true;
                }
                //console.log("commune: "+commune+" "+check_commune);
                //village 
                var village         = (line_list_data[i][col_name["village"]] == null || line_list_data[i][col_name["village"]] == undefined) ? "" : line_list_data[i][col_name["village"]];
                var check_village   = false;
                var village_id      = (line_list_data[i][col_name["village_code"]] == null || line_list_data[i][col_name["village_code"]] == undefined) ? "" : line_list_data[i][col_name["village_code"]];
                if(village.length == 0 || village_id.length == 0){
                    require_string += '<td class="text-danger">'+msg["not_select"]+'</td>';
                }else{
                    require_string += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                    check_village = true;
                }

                //console.log("village: "+village+" "+check_village);
                // sample source
                var sample_source       = line_list_data[i][col_name["sample_source"]];
                var check_sample_source = false;
                var sample_source_id    = line_list_data[i][col_name["sample_source_id"]];

                //console.log("samsource_id "+ " "+sample_source_id);
                if(sample_source.length == 0 || sample_source_id.length == 0){
                    require_string += '<td class="text-danger">'+msg["not_select"]+'</td>';
                }else{
                    check_sample_source = true;
                    require_string += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                }
                //console.log("sample_source: "+sample_source+" "+check_sample_source+", samsource_id: "+sample_source_id.length);
                // requester
                var requester       = line_list_data[i][col_name["requester"]];
                var check_requester = false;
                var requester_id    = line_list_data[i][col_name["requester_id"]];
                if(requester.length == 0 || requester_id.length == 0){
                    require_string += '<td class="text-danger">'+msg["not_select"]+'</td>';
                }else{
                    require_string += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                    check_requester = true;
                }
                //console.log("requester: "+requester+" "+check_requester);

                // collection date
                var collection_date         = line_list_data[i][col_name["collected_date"]];
                var check_collection_date   = false;
                if(collection_date == ""){
                    require_string += '<td class="text-danger">'+msg["not_select"]+'</td>';
                }else{
                    require_string += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                    check_collection_date = true;
                }
                //received_date
                var recieved_date       = line_list_data[i][col_name["received_date"]];
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
                var test_name               = line_list_data[i][col_name["test_name"]];
                
                var check_test_result       = false;
                var check_test_result_date  = false;
                var check_perform_by        = false;
                
                if(test_name.length > 0){
                    require_string  += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                    check_test_result = true;

                    var test_result         = line_list_data[i][col_name["test_result"]];
                    var test_result_id      = line_list_data[i][col_name["test_result_id"]];
                    var test_result_date    =  line_list_data[i][col_name["test_result_date"]];
                    check_test_result_date  = false;
                    var perform_by          = line_list_data[i][col_name["perform_by"]];
                    var perform_by_id       = line_list_data[i][col_name["performer_by_id"]];
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
                var residence      =  line_list_data[i][col_name["residence"]];
                // convert to string in order to trim();
                if(residence !== ""){
                    residence = residence.toString(); // avoid value in integer
                    if(residence.lenth > 100){
                        extraErrString += " - "+label_residence+': <span class="text-danger">'+msg["not_greater_than"]+' 100 '+msg["char"]+'</span><br />';
                        extraErrCheck = false;
                    }
                }
                var contacted_with      =  line_list_data[i][col_name["is_contacted_with"]];
                if(contacted_with !== ""){
                    contacted_with = contacted_with.toString();
                    if(contacted_with.lenth > 50){
                        extraErrString += " - "+label_contact_with+': <span class="text-danger">'+msg["not_greater_than"]+' 50 '+msg["char"]+'</span><br />';
                        extraErrCheck = false;
                    }
                }

                var sample_number = line_list_data[i][col_name["sample_number"]];
                if(sample_number !== ""){
                    sample_number = sample_number.toString();
                    if(sample_number.length > 11){
                        extraErrString += " - "+label_sample_number+': <span class="text-danger">'+msg["not_greater_than"]+' 11 '+msg["char"]+'</span><br />';
                        extraErrCheck = false;
                    }
                }

                var clinical_history = line_list_data[i][col_name["diagnosis"]];
                if(clinical_history !== ""){
                    clinical_history = clinical_history.toString();
                    if(clinical_history.length > 100){
                        extraErrString += " - "+label_clinical_history+': <span class="text-danger">'+msg["not_greater_than"]+' 100 '+msg["char"]+'</span><br />';
                        extraErrCheck = false;
                    }
                }

                var completed_by = line_list_data[i][col_name["completed_by"]];
                if(completed_by !== ""){
                    completed_by = completed_by.toString();
                    if(completed_by.length > 150){
                        extraErrString += " - "+label_completed_by+': <span class="text-danger">'+msg["not_greater_than"]+' 150 '+msg["char"]+'</span><br />';
                        extraErrCheck = false;
                    }
                }

                var phone = line_list_data[i][col_name["phone_completed_by"]];
                if(phone !== ""){
                    phone = phone.toString();
                    if(phone.length > 150){
                        extraErrString += " - "+label_phone+': <span class="text-danger">'+msg["not_greater_than"]+' 150 '+msg["char"]+'</span><br />';
                        extraErrCheck = false;
                    }
                }

                var sample_collector = line_list_data[i][col_name["sample_collector"]];
                if(sample_collector !== ""){
                    sample_collector = sample_collector.toString();
                    if(sample_collector.length > 50){
                        extraErrString += " - "+label_sample_collector+': <span class="text-danger">'+msg["not_greater_than"]+' 50 '+msg["char"]+'</span><br />';
                        extraErrCheck = false;
                    }
                }

                var phone_number_sample_collctor = line_list_data[i][col_name["phone_number_sample_collctor"]];
                if(phone_number_sample_collctor !== ""){
                    phone_number_sample_collctor = phone_number_sample_collctor.toString();
                    if(phone_number_sample_collctor.length > 15){
                        extraErrString += " - "+label_phone+': <span class="text-danger">'+msg["not_greater_than"]+' 15 '+msg["char"]+'</span><br />';
                        extraErrCheck = false;
                    }
                }
                                
                var arrival_date = line_list_data[i][col_name["arrival_date"]];
                var arrival_date = new Date(arrival_date);

                if(arrival_date !== ""){
                    if( arrival_date > currentDate){
                        extraErrString += " - "+label_date_of_arrival+': <span class="text-danger">'+msg["not_greater_than_current_date"]+'</span><br />';
                        extraErrCheck = false;
                    }
                }

                var passport_number = line_list_data[i][col_name["passport_number"]];
                if(passport_number !== ""){
                    passport_number = passport_number.toString();
                    if(passport_number.length > 20){
                        extraErrString += " - "+label_passport_no+': <span class="text-danger">'+msg["not_greater_than"]+' 20 '+msg["char"]+'</span><br />';
                        extraErrCheck = false;
                    }
                }

                var flight_number = line_list_data[i][col_name["flight_number"]];
                if(flight_number !== ""){
                    flight_number = flight_number.toString();
                    if(flight_number.length > 20){
                        extraErrString += " - "+label_flight_number+': <span class="text-danger">'+msg["not_greater_than"]+' 20 '+msg["char"]+'</span><br />';
                        extraErrCheck = false;
                    }
                }

                var seat_number = line_list_data[i][col_name["seat_number"]];
                if(seat_number !== ""){
                    seat_number = seat_number.toString();
                    if(seat_number.length > 5){
                        extraErrString += " - "+label_seat_no+': <span class="text-danger">'+msg["not_greater_than"]+' 5 '+msg["char"]+'</span><br />';
                        extraErrCheck = false;
                    }
                }

                var test_covid_date = line_list_data[i][col_name["test_date"]];
                var test_covid_date = new Date(test_covid_date);
                
                if(test_covid_date !== ""){
                    if( test_covid_date > currentDate){
                        extraErrString += " - "+label_test_covid_date+': <span class="text-danger">'+msg["not_greater_than_current_date"]+'</span><br />';
                        extraErrCheck = false;
                    }
                }

                var reason_for_testing_id   = line_list_data[i][col_name["reason_for_testing_id"]];
                var reason_for_testing      = line_list_data[i][col_name["reason_for_testing"]];

                if( reason_for_testing == "" || reason_for_testing_id == 0 ){
                    extraErrString += " - "+label_reason_for_testing+': <span class="text-danger">'+msg["not_select"]+'</span><br />';
                    extraErrCheck   = false;
                }

                var country_name = line_list_data[i][col_name["country"]];
                if(country_name !== ""){
                    country_name = country_name.toString();
                    if(country_name.length > 150){
                        extraErrString += " - "+label_country+': <span class="text-danger">'+msg["not_greater_than"]+' 150 '+msg["char"]+'</span><br />';
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
                    patient_code                                    = line_list_data[i][col_name["patient_code"]];
                    patient_code                                    = patient_code.toString();
                    line_list_data[i][col_name["patient_code"]]          = patient_code.trim();

                    age                                             = line_list_data[i][col_name["age"]];
                    //line_list_data[i][col_name["age"]]                   = age.trim();
                    phone                                           = line_list_data[i][col_name["phone"]];
                    phone                                           = phone.toString();
                    line_list_data[i][col_name["phone"]]                 = phone.trim();

                    residence                                       = line_list_data[i][col_name["residence"]];
                    residence                                       = residence.toString();
                    line_list_data[i][col_name["residence"]]             = residence.trim();

                    is_contacted_with                               = line_list_data[i][col_name["is_contacted_with"]];
                    line_list_data[i][col_name["is_contacted_with"]]     = is_contacted_with.trim();
                    sample_number                                   = line_list_data[i][col_name["sample_number"]];
                    sample_number                                   = sample_number.toString();
                    line_list_data[i][col_name["sample_number"]]         = sample_number.trim();

                    line_list_data[i][col_name["sample_source"]]         = sample_source;
                    line_list_data[i][col_name["requester"]]             = requester;

                    diagnosis                                       = line_list_data[i][col_name["diagnosis"]];
                    diagnosis                                       = diagnosis.toString();
                    line_list_data[i][col_name["diagnosis"]]             = diagnosis.trim();

                    completed_by                                    = line_list_data[i][col_name["completed_by"]];
                    line_list_data[i][col_name["completed_by"]]          = completed_by.trim();
                    phone_completed_by                              = line_list_data[i][col_name["phone_completed_by"]];
                    phone_completed_by                              = phone_completed_by.toString();
                    line_list_data[i][col_name["phone_completed_by"]]    = phone_completed_by.trim();

                    sample_collector                                = line_list_data[i][col_name["sample_collector"]];
                    line_list_data[i][col_name["sample_collector"]]      = sample_collector.trim();
                    phone_number_sample_collctor                    = line_list_data[i][col_name["phone_number_sample_collctor"]];
                    phone_number_sample_collctor                    = phone_number_sample_collctor.toString();
                    line_list_data[i][col_name["phone_number_sample_collctor"]] = phone_number_sample_collctor.trim();

                    passport_number                                 = line_list_data[i][col_name["passport_number"]];
                    passport_number                                 = passport_number.toString();
                    line_list_data[i][col_name["passport_number"]]       = passport_number.trim();

                    flight_number                                   = line_list_data[i][col_name["flight_number"]];
                    flight_number                                   = flight_number.toString();
                    line_list_data[i][col_name["flight_number"]]         = flight_number.trim();

                    seat_number                                     = line_list_data[i][col_name["seat_number"]];
                    seat_number                                     = seat_number.toString();
                    line_list_data[i][col_name["seat_number"]]           = seat_number.trim();

                    contact_with                                    = line_list_data[i][col_name["is_contacted_with"]];
                    contact_with                                    = contact_with.toString();
                    line_list_data[i][col_name["is_contacted_with"]]     = contact_with.trim();

                    completed_by                                    = line_list_data[i][col_name["completed_by"]];
                    line_list_data[i][col_name["completed_by"]]          = completed_by.trim(); // completed by

                    phone_number                                    = line_list_data[i][col_name["phone_completed_by"]];
                    phone_number                                    = phone_number.toString(); // avoid phone number is integer
                    line_list_data[i][col_name["phone_completed_by"]]    = phone_number.trim(); // phone number of completed by

                    sample_collector                                = line_list_data[i][col_name["sample_collector"]];
                    line_list_data[i][col_name["sample_collector"]]      = sample_collector.trim(); // sample collector

                    phone_number_sample_collector                   = line_list_data[i][col_name["phone_number_sample_collctor"]];
                    phone_number_sample_collector                   = phone_number_sample_collector.toString();
                    line_list_data[i][col_name["phone_number_sample_collctor"]] = phone_number_sample_collector.trim();

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
                var patients            = resText.patients;
                var bodyResult          = '';    
                var psample_ids         = '';
                var n                   = 1;
                var htmlStringQrCode    = ''; 
                var img_qr_code         = '';
                var btnPrintQrCode      = "";
                if(resText.execution_time !== undefined){
                    console.log(resText.execution_time);
                }
                
                for(var i in patients) {
                    var btnPrint = "";
                    var test_result_msg = "";                
                    if(patients[i].psample_id !== undefined){
                        btnPrint = '<button type="button" class="btn btn-sm btn-success btnPrintCovidFormV1" data-psample_id="'+patients[i].psample_id+'" title="'+label_print_lab_form+'"><i class="fa fa-eye"></i></button>'
                        
                        psample_ids += patients[i].psample_id+"n";
                    }
                    if(patients[i].test_result_msg !== undefined){
                        test_result_msg = patients[i].test_result_msg;
                    }
                    /*
                    if(patients[i].qr_code !== undefined){
                        //qr_code = '<img src="'+base_url+'/assets/camlis/images/patient_qr_code/'+patients[i].pqr_code+'" style="width:32px;" />'
                        img_qr_code = '<img src="'+patients[i].qr_code+'" style="width:70px;" />';
                        btnPrintQrCode = '<button type="button" class="btnQrCode btn btn-sm btn-primary" data-patient_code="'+patients[i].patient_code+'" title="'+label_qr_code+'"><i class="fa fa-qrcode"></i></button>';
                    }
                    */
                    bodyResult += '<tr>';
                    bodyResult += '<td>'+n+'</td>';
                    bodyResult += '<td>'+patients[i].patient_code+'</td>';
                    bodyResult += '<td>'+patients[i].patient_name+'</td>';
                    bodyResult += '<td>'+patients[i].msg+'</td>';  
                    bodyResult += '<td>'+patients[i].sample_number+'</td>';
                    bodyResult += '<td>'+patients[i].sample_msg+'</td>';
                    bodyResult += '<td>'+patients[i].test_msg+'</td>';
                    bodyResult += '<td>'+test_result_msg+'</td>';
                    //bodyResult += '<td>'+btnPrint+" "+btnPrintQrCode+'</td>';
                    bodyResult += '<td>'+btnPrint+'</td>';
                    bodyResult += '</tr>';

                    // For printing out we need to print it two for each of pid
                    
                    htmlStringQrCode += '<tr data-patient_code="'+patients[i].patient_code+'">';
                    htmlStringQrCode += '<td style="text-align:center; vertical-align:middle;">'+img_qr_code+'</td>';
                    htmlStringQrCode += '<td style="text-align:center; vertical-align:middle;"><span>'+patients[i].patient_code+'</span></td>';
                    htmlStringQrCode += '</tr>';
                    
                    n++;
                }
                setTimeout(function(){
                    myDialog.showProgress('hide');
                    $("table[name=tblResultLineListNew] tbody").html(bodyResult);
                    var res = psample_ids.substring(0, psample_ids.length - 1); // remove the last n
                    $("#printAll").attr("data-psample_id",res);
                    $("#modal_result_line_list_new").modal("show");
                    $("table[name=tbl_qr_code] tbody").html(htmlStringQrCode); // 11 March 2021       
                   // $("#modal_qr_code").modal("show");             
                    //line_list_table_new.setData([]);
                    
                    var col_name_length = Object.keys(col_name).length;
                    for(r = 0 ; r < patients.length ; r++){
                        for(c = 0 ; c < col_name_length ; c++){
                            line_list_table_new.setValue(jspreadsheet.getColumnNameFromId([c, r]),"");    
                        }
                    }
                    $("#btnSaveListNew").removeClass('disabled');
                    

                }, 1000);

            },
            error: function(xhr, status, error) {
                var err = eval("(" + xhr.responseText + ")");
                console.log(err.Message);
                console.log(xhr.responseText);
            }
        });
    });
    
/**End */

/** Excel Short Form */
    var $modal_excel_short_form     = $("#modal_excel_short_form"); // added 19-03-2021
    $("#btnExcelShortForm").on("click", function (evt) {
        $modal_excel_short_form.modal("show");
    });
    $("select[name=clinical_symptom]").select2();
    
    $("select[name=sample_source]").on("change",  function (evt) {
        evt.preventDefault();

        var form = $(this).parents("form.frm-sample-entry");
        var requester           = $("select[name=requester]");
        var sample_source_id = $(this).val();

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
                        var opt = "<option value='" + resText.requesters[i].requester_id + "' "+ selected +">" + resText.requesters[i].requester_name + "</option>";
                        requester.append(opt);
                    }
                }
            },
            error : function (evt) {
            }
        });
    });
    $("input[name=collected_date].dtpicker , input[name=received_date].dtpicker").datetimepicker(dtPickerOption);
    //$("input.coltimepicker").timepicker('setTime', moment("", 'hh:mm').format('hh:mm'));
    //$("input.rectimepicker").timepicker('setTime', moment("", 'HH:mm').format('hh:mm'));
    
    $('input.coltimepicker').timepicker({
        minuteStep: 1, showMeridian: false, defaultTime: moment().subtract(1, 'minutes').format('HH:mm')
    });
    $('input.rectimepicker').timepicker({
        minuteStep: 1, showMeridian: false
    });

    $modal_excel_short_form.find('input:checkbox[name="is_contacted"]').on("click", function (evt) {     
        //console.log("here")
        if ($(this).is(":checked")) {
            $modal_excel_short_form.find("div.contact_wrapper").removeClass("hidden");
        } else {
            $modal_excel_short_form.find("div.contact_wrapper").addClass("hidden");
        }
    });
    var line_list_table_short_form = jspreadsheet(document.getElementById('excelShortForm'), {
        minDimensions: [ 24,maxRow],
        defaultColWidth: 100,
        tableOverflow: true,
        tableHeight: "350px",
        columns: [
            { type:'text', title: label_patient_id,width:140 ,maxlength:20},
            { type:'text', title: label_patient_name+'*' ,maxlength:60},
            { type:'numeric', title: label_patient_age+'*',width:40 , maxlength:3 },
            { type:'dropdown', title:label_sex+'*',width:60, source: gender_array},
            { type:'text', title: label_patient_phone_number,maxlength:100 },
            { type: 'autocomplete', title: label_province+'*', width:100, source:province_array },
            { type: 'autocomplete', title: label_district+'*', width:100, source:districts_array , filter: districtFilter2},
            { type: 'autocomplete', title: label_commune+'*', width:100, source:communes_array , filter: communeFilter2},
            { type: 'autocomplete', title: label_village+'*', width:100, source:villages_array ,filter: villageFilter2},
            { type:'text', title: label_residence ,width:80 ,maxlength:100},
            { type: 'autocomplete', title: label_reason_for_testing+"*", width:120, source:reason_for_testing_array },
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

            { type:'text', title: label_health_facility , maxlength:150 },

            { type: 'dropdown', title: label_test_name, width:150, source: test_name_array},
            { type: 'dropdown', title: label_machine_name, width:150, source: machine_name_array, filter:machineNameFilterShortForm, readOnly: true},
            
            { type: 'dropdown', title: label_result, width:70,source:test_result_array, filter: testResultFilterShortForm, readOnly: true},
            { type: 'calendar', title: label_test_date, width:150 , readOnly: true, options: { format:'YYYY-MM-DD' , readonly:true , validRange: [ '2021-01-01', today ]}},
            { type: 'autocomplete', title: label_performed_by, width:120, source:performer_array, readOnly: true},
           /* { type: 'autocomplete', title: label_country, width:80, source:country_array }, */
            { type: 'text', title: label_country, width:80, maxlength: 150 },
            { type: 'autocomplete', title: label_nationality, width:80, source:nationalities_array },            
            { type: 'calendar', title: label_date_of_arrival, width: 100,options: { format:'YYYY-MM-DD' , readonly:true } },
            { type:'text', title: label_passport_no, width:80 , maxlength:20},
            { type:'text', title: label_flight_number ,maxlength:10 },
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
            { type:'dropdown', title: label_number_of_sample ,width:120,source:[1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30]},
            { type:'text', title:'performer_by_id'},
            { type:'text', title:'test_result_id'},
            { type:'text', title:'is_camlis_patient'},
            { type:'dropdown', title:label_vaccination_status, source:vaccination_status_array},
            { type:'calendar', title: label_first_injection_date, readOnly:true, width:90,options: { format:'YYYY-MM-DD' , readonly:true , validRange: [ '2021-01-01', today ]} },
            { type:'calendar', title: label_second_injection_date, readOnly:true, width:90,options: { format:'YYYY-MM-DD' , readonly:true , validRange: [ '2021-01-01', today ]} },
            { type:'dropdown', title: label_vaccine_type , source: vaccine_type_array, readOnly: true, width:90},                        
            { type:'calendar', title: label_third_injection_date, readOnly:true, width:90,options: { format:'YYYY-MM-DD' , readonly:true , validRange: [ '2021-01-01', today ]} },
            { type:'dropdown', title: label_vaccine_type , source: vaccine_type_array, readOnly: true, width:90},
            { type:'calendar', title: label_forth_injection_date, readOnly:true, width:90,options: { format:'YYYY-MM-DD' , readonly:true , validRange: [ '2021-01-01', today ]} },//09022022
            { type:'dropdown', title: label_vaccine_type , source: vaccine_type_array, readOnly: true, width:90},//09022022
            { type:'text', title: label_occupation, maxlength:100},
            { type:'text', title: 'vaccination_status_code'},
            { type:'text', title: 'vaccine_id'},
            { type:'text', title: 'second_vaccine_id'},
            { type:'text', title: 'third_vaccine_id'}, //09022022
        ],
        nestedHeaders:[
            [
                {
                    title: label_patient_info,
                    colspan: '11',
                },
                {
                    title: label_sample_info,
                    colspan: '8',
                },
                {
                    title: label_patient_info,
                    colspan: '8',
                },
                {
                    title: label_sample_info,
                    colspan: '1',
                },
                {
                    title: label_patient_info,
                    colspan: '10',
                },
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
                    title: label_sample,
                    colspan: '4'
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
                },
                {
                    title: label_covid_questionaire,
                    colspan: '10',
                },
            ],
        ],
        onchange:function(instance,cell, c, r, value) {         
            if( c == 0){
                var rowNumber = r;
                var totalCol = 20;
                var i;
                var nCol = 1;                
                if(value !== ""){                   
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
                                if(app_language == 'kh'){
                                    var sex = (patient.sex == 'M') ? "ប្រុស" : "ស្រី";
                                    var province = patient.province_kh;
                                    var district = patient.district_kh;
                                    var commune  = patient.commune_kh;
                                    var village  = patient.village_kh;
                                }else{
                                    var sex = (patient.sex == 'M') ? "Male" : "Female";
                                    var province = patient.province_en;
                                    var district = patient.district_en;
                                    var commune  = patient.commune_en;
                                    var village  = patient.village_en;
                                }
                                
                                var dob = moment(patient.dob, 'YYYY-MM-DD');
                                var age = calculateAge(dob);
                                
                                var is_positive_covid   = (patient.is_positive_covid == null || patient.is_positive_covid == undefined || patient.is_positive_covid == false) ? false : true;
                                var is_contacted        = (patient.is_contacted == null || patient.is_contacted == undefined) ? false : patient.is_contacted;
                                var contact_with        = (patient.contact_with == null || patient.contact_with == undefined) ? "" : patient.contact_with;
                                var is_direct_contact   = (patient.is_direct_contact == null || patient.is_direct_contact == undefined) ? false : patient.is_direct_contact;
                                var test_date           = (patient.test_date == undefined || patient.test_date == null) ? "" : patient.test_date;;
                                var name                = patient.name;
                                var residence           = (patient.residence == undefined || patient.residence == null) ? "" : patient.residence;
                                var date_arrival        = (patient.date_arrival == undefined || patient.date_arrival == null) ? "" : patient.date_arrival;
                                
                                var country             = (patient.country_name_en == undefined || patient.country_name_en == null) ? "" : patient.country_name_en;
                                if(patient.country_name == undefined || patient.country_name == null){
                                    country = "";
                                }else{
                                    country = patient.country_name;
                                }

                                var nationality         = (patient.nationality_en == undefined || patient.nationality_en == null) ? "" : patient.nationality_en;
                                var passport_number     = (patient.passport_number == undefined || patient.passport_number == null) ? "" : patient.passport_number;
                                var flight_number       = (patient.flight_number == undefined || patient.flight_number == null) ? "" : patient.flight_number;
                                var seat_number         = (patient.seat_number == undefined || patient.seat_number == null) ? "" : patient.seat_number;
                                var phone               = (patient.phone == undefined || patient.phone == null) ? "" : (patient.phone).trim();
                                var is_camlis_patient   = patient.is_camlis_patient == undefined ? 0 : 1; //22062021
                                
                                //14-07-2021
                                var vaccination_status_code  = (patient.vaccination_status == undefined || patient.vaccination_status == null) ? "" : patient.vaccination_status;
                                var vaccine_type        = (patient.vaccine_id == undefined || patient.vaccine_id == null) ? "" : patient.vaccine_id;
                                var first_vaccinated_date     = (patient.first_vaccinated_date == undefined || patient.first_vaccinated_date == null) ? "" : patient.first_vaccinated_date;
                                var second_vaccinated_date    = (patient.second_vaccinated_date == undefined || patient.second_vaccinated_date == null) ? "" : patient.second_vaccinated_date;
                                var occupation          = (patient.occupation == undefined || patient.occupation == null) ? "" : patient.occupation;
                                var second_vaccine_type        = (patient.second_vaccine_id == undefined || patient.second_vaccine_id == null) ? "" : patient.second_vaccine_id;
                                var third_vaccinated_date     = (patient.third_vaccinated_date == undefined || patient.third_vaccinated_date == null) ? "" : patient.third_vaccinated_date;
                                var third_vaccine_type        = (patient.third_vaccine_id == undefined || patient.third_vaccine_id == null) ? "" : patient.third_vaccine_id; //09022022
                                var forth_vaccinated_date     = (patient.forth_vaccinated_date == undefined || patient.forth_vaccinated_date == null) ? "" : patient.forth_vaccinated_date; //09022022

                                var rowData = [                                                                        
                                    name,
                                    age.years,
                                    sex,
                                    phone,
                                    province,
                                    district,
                                    commune,
                                    village,
                                    residence
                                ];
                                
                                for(i = 0 ; i < 9;i++){
                                    var nameColumn = jspreadsheet.getColumnNameFromId([c+nCol, r]);
                                    instance.jexcel.setValue(nameColumn, rowData[i]);
                                    nCol++;
                                }
                                                               
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["is_contacted"], r]), is_contacted);
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["is_contacted_with"], r]), contact_with);
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["is_directed_contact"], r]), is_direct_contact);
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["country"], r]), country);
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["nationality"], r]), nationality);
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["arrival_date"], r]), date_arrival);
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["passport_number"], r]), passport_number);
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["flight_number"], r]), flight_number);
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["seat_number"], r]), seat_number);
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["is_positive_covid"], r]), is_positive_covid);                            
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["test_date"], r]), test_date);
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["is_camlis_patient"], r]), is_camlis_patient);
                                //14072021
                                if(vaccination_status_code !== ""){
                                    var status = getVaccinationStatusString(vaccination_status_code);
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["vaccination_status"], r]), status);
                                    var vaccine_name = getVaccineName(vaccine_type);
                                    var second_vaccine_name = getVaccineName(second_vaccine_type);
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["vaccine_name"], r]), vaccine_name);
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["second_vaccine_name"], r]), second_vaccine_name);
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["first_vaccinated_date"], r]), first_vaccinated_date);
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["second_vaccinated_date"], r]), second_vaccinated_date);
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["third_vaccinated_date"], r]), third_vaccinated_date);
                                    var third_vaccine_name = getVaccineName(third_vaccine_type); //09022022
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["third_vaccine_name"], r]), third_vaccine_name); //09022022
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["forth_vaccinated_date"], r]), forth_vaccinated_date); //09022022
                                }
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["occupation"], r]), occupation);
                                //End
                            }else{
                                // Reset columns
                                nCol = 1;
                                for(i = 0 ; i < 9;i++){
                                    var nameColumn = jspreadsheet.getColumnNameFromId([c+nCol, r]);
                                    instance.jexcel.setValue(nameColumn, "");
                                    nCol++;
                                }                                
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["is_contacted"], r]), false);
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["is_contacted_with"], r]), "");                            
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["is_directed_contact"], r]), false);
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["country"], r]), "");
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["nationality"], r]), "");
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["arrival_date"], r]), "");
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["passport_number"], r]), "");
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["flight_number"], r]), "");
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["seat_number"], r]), "");
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["is_positive_covid"], r]), false);                            
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["test_date"], r]), "");
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["is_camlis_patient"], r]), "");
                                //14072021
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["vaccination_status"], r]), "");
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["vaccine_name"], r]), "");
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["first_vaccinated_date"], r]), "");
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["second_vaccinated_date"], r]), "");
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["occupation"], r]), "");
                                //End
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
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["is_contacted"], r]), false);
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["is_contacted_with"], r]), "");                            
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["is_directed_contact"], r]), false);
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["country"], r]), "");
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["nationality"], r]), "");
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["arrival_date"], r]), "");
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["passport_number"], r]), "");
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["flight_number"], r]), "");
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["seat_number"], r]), "");
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["is_positive_covid"], r]), false);                            
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["test_date"], r]), "");
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["is_camlis_patient"], r]), "");
                    //14072021
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["vaccination_status"], r]), "");
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["vaccine_name"], r]), "");
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["first_vaccinated_date"], r]), "");
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["second_vaccinated_date"], r]), "");
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["occupation"], r]), "");
                    //End
                }
            }
            if(c == col_name["gender"]){
                //console.log("C = "+c +" col gender = "+col_name["gender"]);
                if(value !== ""){
                    // save id of sex in column 35
                    if(app_language == 'kh'){
                        sex = value == 'ប្រុស' ? 1 : 2;
                    }else{
                        sex = value == 'Male' ? 1 : 2;
                    }
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["sex_id"], r]),sex);
                }
            }
            // province column
            if (c == col_name["province"]) {
                //console.log("Province "+value);
                if(value !== ""){
                    // set null value to district col                    
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["district"], r]), "");
                    code = getProvinceCode_(value);                    
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["province_code"], r]),code); // save province code in column 38
                }               
            }
            // district column
            if (c == col_name["district"]) {
                if(value !== ""){
                    // set null value to commune 
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["commune"], r]), "");
                    province_code = line_list_table_short_form.getValueFromCoords(col_name["province_code"],r);
                    code = getDistrictCode_(value, province_code);
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["district_code"], r]), code);
                }
            }
            // commune column
            if (c == col_name["commune"]) {
                //console.log("Village "+value);
                if(value !== ""){
                    // set null value to village                     
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["village"], r]), "");
                    district_code = line_list_table_short_form.getValueFromCoords(col_name["district_code"],r);
                    code = getCommuneCode_(value, district_code);                    
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["commune_code"], r]), code);
                }
            }
            // village column
            if (c == col_name["village"]) {
                //console.log("Village "+value);
                if(value !== ""){
                    commune_code = line_list_table_short_form.getValueFromCoords(col_name["commune_code"],r);
                    code = getVillageCode_(value, commune_code);
                    //console.log("get village code "+code);                    
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["village_code"], r]), code);
                }
            }
            // reason for testing column
            if (c == col_name["reason_for_testing"]) {
                if(value !== ""){
                    code = getReason_(value);
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["reason_for_testing_id"], r]), code);
                }
            }
            // is contacted column
            if (c == col_name["is_contacted"]) {
                var is_contacted_column = jspreadsheet.getColumnNameFromId([col_name["is_contacted_with"], r]);
                var is_direct_contact_column = jspreadsheet.getColumnNameFromId([col_name["is_directed_contact"], r]);
                
                if(value){
                    //console.log("true is right");
                   line_list_table_short_form.setReadOnly(is_contacted_column,false);
                   line_list_table_short_form.setReadOnly(is_direct_contact_column,false);
                }else{
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["is_contacted_with"], r]),"");
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["is_directed_contact"], r]),"");
                   line_list_table_short_form.setReadOnly(is_contacted_column,true);
                   line_list_table_short_form.setReadOnly(is_direct_contact_column,true);
                }
            }
            // sample source column
            if (c == col_name["sample_source"]) {
                if(value !== ""){
                    // set
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["requester"], r]),"");
                    // save id of sample source in column 43
                    code = getSampleSource(value);
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["sample_source_id"], r]), code);
                }
            }

            // requester column
            if (c == col_name["requester"]) {
                if(value !== ""){
                    sample_source_id = line_list_table_short_form.getValueFromCoords(col_name["sample_source_id"],r);
                    code = getRequester(value,sample_source_id);
                    var columnName = jspreadsheet.getColumnNameFromId([col_name["requester_id"], r]);
                    instance.jexcel.setValue(columnName, code);
                }
            }
            // clinical symptom column
            if (c == col_name["clinical_symptom"]) {
                if(value !== ""){                    
                    code = getClinicalSymptom_(value);
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["clinical_symtop_id"], r]), code);
                }
            }
            // Test column
            if (c == col_name["test_name"]) {
                //console.log(col_name["test_name"]);

                var test_result_col         = jspreadsheet.getColumnNameFromId([col_name["test_result"], r]);
                var test_result_date_col    = jspreadsheet.getColumnNameFromId([col_name["test_result_date"], r]);
                var perform_by_col          = jspreadsheet.getColumnNameFromId([col_name["perform_by"], r]);
                var machine_name_col        = jspreadsheet.getColumnNameFromId([col_name["machine_name"], r]);
                var test_result_id_col      = jspreadsheet.getColumnNameFromId([col_name["test_result_id"], r]);
                
                if(value !== ""){

                    // reset value of the these columns
                    instance.jexcel.setValue(test_result_col, "");
                    instance.jexcel.setValue(test_result_date_col, "");
                    instance.jexcel.setValue(perform_by_col, "");
                    instance.jexcel.setValue(machine_name_col, "");
                    instance.jexcel.setValue(test_result_id_col, "");

                    code = getTest(value);
                    //console.log("Test ID "+ code);
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["test_id"], r]), code);

                    // remove read only from result, test date and perform_by column
                    line_list_table_short_form.setReadOnly(test_result_col,false);
                    line_list_table_short_form.setReadOnly(test_result_date_col,false);
                    line_list_table_short_form.setReadOnly(perform_by_col,false);
                    line_list_table_short_form.setReadOnly(machine_name_col,false);
                }else{
                    // reset value and set readonly
                    instance.jexcel.setValue(test_result_col, "");
                    instance.jexcel.setValue(test_result_date_col, "");
                    instance.jexcel.setValue(perform_by_col, "");
                    instance.jexcel.setValue(machine_name_col, "");

                    line_list_table_short_form.setReadOnly(test_result_col,true);
                    line_list_table_short_form.setReadOnly(test_result_date_col,true);
                    line_list_table_short_form.setReadOnly(perform_by_col,true);
                    line_list_table_short_form.setReadOnly(machine_name_col,true);
                }
            }
            
            if(c == col_name["test_result"]){
                if(value !== ""){
                    //console.log("Test result "+ value);
                    //var sample_test_id = line_list_table_short_form.getValueFromCoords(col_name["test_id"],r);                    
                    var sample_test_id = $modal_excel_short_form.find('select[name=test_name]').val();
                    var test_result_id = getTestResultId(value , sample_test_id);
                    //console.log(test_result_id);
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["test_result_id"], r]), test_result_id);
                }
            }
            if (c == col_name["perform_by"]){
                if(value !== ""){
                    code = getPerformer(value);
                    //console.log("performer id "+code);  
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["performer_by_id"], r]), code);
                }
            }
            // country column
            /*
            if (c == col_name["country"]) {
                if(value !== ""){
                   code = getCountry(value);
                   instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["country_id"], r]), code);
                }
            }
            */

            // nationality column
            if (c == col_name["nationality"]) {
                if(value !== ""){                    
                    code = getNationality(value);
                    //console.log("Nationality code: "+code);                    
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["nationality_id"], r]), code);
                }
            }
            // if check is true, enable Test Date
            if (c == col_name["is_positive_covid"]) {
                //console.log(value);
                var colummnName = jspreadsheet.getColumnNameFromId([col_name["test_date"], r]);
                if(value){
                   line_list_table_short_form.setReadOnly(colummnName,false);
                }else{
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["test_date"], r]), "");
                    line_list_table_short_form.setReadOnly(colummnName,true);
                }
            }
            //13072021
            if (c == col_name["vaccination_status"]) {
                //console.log(value);
                if(value !== ""){
                    status = getVaccinationStatus(value);
                    console.log(status);
                    if(status == 1 || status == ""){
                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["vaccine_name"], r]), "");
                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["first_vaccinated_date"], r]), "");
                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["second_vaccinated_date"], r]), "");

                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["second_vaccine_name"], r]), "");
                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["third_vaccinated_date"], r]), "");

                        line_list_table_short_form.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["vaccine_name"], r]),true);
                        line_list_table_short_form.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["first_vaccinated_date"], r]),true);
                        line_list_table_short_form.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["second_vaccinated_date"], r]),true);
                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["vaccination_status_code"], r]), status);
                    }else if(status == 2){
                        // Enable first injection date and vaccine name
                        line_list_table_short_form.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["vaccine_name"], r]),false);
                        line_list_table_short_form.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["first_vaccinated_date"], r]),false);

                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["second_vaccinated_date"], r]), "");
                        line_list_table_short_form.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["second_vaccinated_date"], r]),true);

                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["third_vaccinated_date"], r]), "");
                        line_list_table_short_form.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["third_vaccinated_date"], r]),true);

                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["second_vaccine_name"], r]), "");
                        line_list_table_short_form.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["second_vaccine_name"], r]),true);
                    }else if (status == 3){
                        line_list_table_short_form.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["vaccine_name"], r]),false);
                        line_list_table_short_form.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["first_vaccinated_date"], r]),false);
                        line_list_table_short_form.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["second_vaccinated_date"], r]),false);

                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["third_vaccinated_date"], r]), "");
                        line_list_table_short_form.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["third_vaccinated_date"], r]),true);
                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["second_vaccine_name"], r]), "");
                        line_list_table_short_form.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["second_vaccine_name"], r]),true);
                    }else if (status == 4){
                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["vaccination_status_code"], r]), status);
                        line_list_table_short_form.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["vaccine_name"], r]),false);
                        line_list_table_short_form.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["first_vaccinated_date"], r]),false);
                        line_list_table_short_form.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["second_vaccinated_date"], r]),false);
                        line_list_table_short_form.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["third_vaccinated_date"], r]),false);
                        line_list_table_short_form.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["second_vaccine_name"], r]),false);
                    } //09022022
                    else if (status == 5){
                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["vaccination_status_code"], r]), status);
                        line_list_table_short_form.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["vaccine_name"], r]),false);
                        line_list_table_short_form.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["first_vaccinated_date"], r]),false);
                        line_list_table_short_form.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["second_vaccinated_date"], r]),false);
                        line_list_table_short_form.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["third_vaccinated_date"], r]),false);
                        line_list_table_short_form.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["second_vaccine_name"], r]),false);
                        line_list_table_short_form.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["forth_vaccinated_date"], r]),false);
                        line_list_table_short_form.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["third_vaccine_name"], r]),false);
                    }
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["vaccination_status_code"], r]), status);
                }
            }
            if (c == col_name["vaccine_name"]) {
                //console.log(value);
                if(value !== ""){
                    status = getVaccineCode(value);
                    //console.log(status)
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["vaccine_id"], r]), status);
                }else{
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["vaccine_id"], r]), "");
                }
            }
            if (c == col_name["second_vaccine_name"]) {
                //console.log(value);
                if(value !== ""){
                    status = getVaccineCode(value);
                    //console.log(status)
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["second_vaccine_id"], r]), status);
                }else{
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["second_vaccine_id"], r]), "");
                }
            }
            if (c == col_name["third_vaccine_name"]) {
                //console.log(value);
                if(value !== ""){
                    status = getVaccineCode(value);
                    //console.log(status)
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["third_vaccine_id"], r]), status);
                }else{
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["third_vaccine_id"], r]), "");
                }
            }                   
        },
        oncreateeditor: function(el, cell, x, y) {
            if (x ==col_name["phone"] 
                || x ==col_name["age"] 
                || x ==col_name["patient_name"] 
                || x ==col_name["patient_code"] 
                || x ==col_name["residence"] 
                || x ==col_name["sample_source"] 
                || x ==col_name["diagnosis"] 
                || x ==col_name["completed_by"]  
                || x ==col_name["phone_completed_by"] 
                || x ==col_name["sample_collector"] 
                || x ==col_name["phone_number_sample_collctor"] 
                || x ==col_name["passport_number"] 
                || x ==col_name["flight_number"] 
                || x ==col_name["seat_number"]
                || x == col_name["country"]
                || x == col_name["occupation"]
            ){
            var config = el.jexcel.options.columns[x].maxlength;
                cell.children[0].setAttribute('maxlength' , config); // set maxlength to column
            }
        }
    });
    
    line_list_table_short_form.hideColumn(col_name["payment_type"]); // hide payment type
    line_list_table_short_form.hideColumn(col_name["admission_date"]); // hide admision column
    line_list_table_short_form.hideColumn(col_name["is_contacted"]); 
    line_list_table_short_form.hideColumn(col_name["is_contacted_with"]);
    line_list_table_short_form.hideColumn(col_name["is_directed_contact"]);
    
    line_list_table_short_form.hideColumn(col_name["sample_source"]);
    line_list_table_short_form.hideColumn(col_name["requester"]);
    line_list_table_short_form.hideColumn(col_name["collected_date"]);
    line_list_table_short_form.hideColumn(col_name["received_date"]);
    line_list_table_short_form.hideColumn(col_name["completed_by"]);
    line_list_table_short_form.hideColumn(col_name["phone_completed_by"]);
    line_list_table_short_form.hideColumn(col_name["sample_collector"]);
    line_list_table_short_form.hideColumn(col_name["phone_number_sample_collctor"]);
    line_list_table_short_form.hideColumn(col_name["test_name"]);
    line_list_table_short_form.hideColumn(col_name["is_urgent"]);

    line_list_table_short_form.hideColumn(col_name["sex_id"]);
    line_list_table_short_form.hideColumn(col_name["test_id"]);
    line_list_table_short_form.hideColumn(col_name["country_id"]);
    line_list_table_short_form.hideColumn(col_name["nationality_id"]);
    line_list_table_short_form.hideColumn(col_name["performer_by_id"]);
    line_list_table_short_form.hideColumn(col_name["province_code"]);
    line_list_table_short_form.hideColumn(col_name["district_code"]);
    line_list_table_short_form.hideColumn(col_name["commune_code"]);
    line_list_table_short_form.hideColumn(col_name["village_code"]);
    line_list_table_short_form.hideColumn(col_name["reason_for_testing_id"]);
    line_list_table_short_form.hideColumn(col_name["sample_source_id"]);
    line_list_table_short_form.hideColumn(col_name["requester_id"]);
    line_list_table_short_form.hideColumn(col_name["clinical_symtop_id"]);
    line_list_table_short_form.hideColumn(col_name["test_result_id"]);
    line_list_table_short_form.hideColumn(col_name["is_camlis_patient"]);
    line_list_table_short_form.hideColumn(col_name["vaccination_status_code"]);
    line_list_table_short_form.hideColumn(col_name["vaccine_id"]);
    line_list_table_short_form.hideColumn(col_name["second_vaccine_id"]);
    line_list_table_short_form.hideColumn(col_name["third_vaccine_id"]); //02092022

    $("#btnSaveListShorFormTest").on("click", function (evt) {
        $(this).addClass('disabled'); //prevent multiple click
        $modal_excel_short_form.find('input:checkbox[name="is_contacted"]').val();
        var sample_source_id    = $modal_excel_short_form.find('select[name=sample_source]').val();
        var sample_source       = $modal_excel_short_form.find('select[name=sample_source] option:selected').text();
        var requester_id        = $modal_excel_short_form.find('select[name=requester]').val();
        var requester           = $modal_excel_short_form.find('select[name=requester] option:selected').text();
        var collected_date	    = $modal_excel_short_form.find("input[name=collected_date]").data('DateTimePicker').date();
        var received_date	    = $modal_excel_short_form.find("input[name=received_date]").data('DateTimePicker').date();
        var collected_time	    = moment($modal_excel_short_form.find("input[name=collected_time]").val().trim(), 'HH:mm');
        var received_time	    = moment($modal_excel_short_form.find("input[name=received_time]").val().trim(), 'HH:mm');
        //var reason_for_testing  = $modal_excel_short_form.find('select[name=for_research]').val();
        var test_id             = $modal_excel_short_form.find('select[name=test_name]').val();
        var test_name           = $modal_excel_short_form.find('select[name=test_name] option:selected').text();
        
        var completed_by        = $modal_excel_short_form.find('input[name=completed_by]').val();
        var phone_number        = $modal_excel_short_form.find('input[name=phone_number]').val();
        var sample_collector    = $modal_excel_short_form.find('input[name=sample_collector]').val();
        var phone_number_sample_collector    = $modal_excel_short_form.find('input[name=phone_number_sample_collector]').val();
        
        var is_contacted        = $modal_excel_short_form.find("#is_contacted").is(":checked") ? true : false;
        var contact_with        = $modal_excel_short_form.find("input[name=contact_with]").val();
        var is_direct_contact   = $modal_excel_short_form.find("input[name=is_direct_contact]:checked").val();

        if (collected_date !== null && collected_time._i.length > 0) {
            var collected_date_time = moment(collected_date.format('YYYY-MM-DD') + ' ' + collected_time.format('HH:mm:ss'), 'YYYY-MM-DD HH:mm:ss');
        }
        if (received_date !== null && received_time._i.length > 0) {
            var received_date_time = moment(received_date.format('YYYY-MM-DD') + ' ' + received_time.format('HH:mm:ss'), 'YYYY-MM-DD HH:mm:ss');
        }
        var currentDate     = new Date();
        //console.log(test_id);
        //console.log(test_name);
        
        var is_valid = false;
        if(sample_source_id == '-1'){
            is_valid = false;
        }else if(requester_id == '-1'){
            is_valid = false;
        }else if(collected_date == null){
            is_valid = false;
        }else if(received_date == null){
            is_valid = false;
        }else{
            is_valid = true;
        }
        if(!is_valid){
            myDialog.showDialog('show', {text: msg_required_data, style: 'warning'});
            $(this).removeClass('disabled'); //prevent multiple click
            return false;
        }

        if (collected_date_time > received_date_time) {
            myDialog.showDialog('show', {text: msg_col_rec_dt_error, style: 'warning'});
            $(this).removeClass('disabled'); //prevent multiple click
            return false;
        }

        myDialog.showProgress('show', { text : msg_loading });

        //console.log(line_list_table_short_form.getData());
        var line_list_data = line_list_table_short_form.getData();
        // delete blank row before send to server
        var data            = [];
        var require_string  = "";
        var valid_check     = 0;
        var array_check     = [];
        
        for(var i in line_list_data){
            var name = line_list_data[i][col_name["patient_name"]];
            var name = name.trim();
            if(name !== "" || name.length > 0){

                if(valid_check !== 1){
                    valid_check     = 1;
                }

                // age column
                var age         = line_list_data[i][col_name["age"]];
                var check_age   = false;
                require_string += "<tr>";
                require_string += "<td>"+line_list_data[i][col_name["patient_code"]]+"</td>";

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
                                            require_string += '<td class="text-danger">'+msg['month_not_greater_than_12']+'</td>';
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
                                require_string += '<td class="text-danger">'+msg["not_correct_format"]+'</td>';
                            }else{
                                require_string += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                                check_age = true;
                            }
                        }
                    }
                }
                //console.log("age "+age+" "+check_age);
                // Gender column

                var gender       = line_list_data[i][col_name["gender"]];
                var check_gender = false;
                var gender_id    = line_list_data[i][col_name["sex_id"]];
            
                if(gender.length == 0 || gender_id.length == 0){
                    require_string += '<td class="text-danger">'+msg["not_select"]+'</td>';
                }else{
                    require_string += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                    var check_gender = true;
                }
                //console.log("Gender: "+gender+" "+check_gender+" "+gender.length);
                var phone        = line_list_data[i][col_name["phone"]];
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
                var province        = line_list_data[i][col_name["province"]];
                var check_province  = false;
                var province_id     = line_list_data[i][col_name["province_code"]];
                if(province.length == 0 || province_id.length == 0){
                    require_string += '<td class="text-danger">'+msg["not_select"]+'</td>';
                }else{
                    require_string += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                    check_province = true;
                }
                //console.log("province: "+province+" "+check_province);

                var district        = line_list_data[i][col_name["district"]];
                var check_district  = false;
                var district_id     = line_list_data[i][col_name["district_code"]];
                if(district.length == 0 || district_id.length == 0){
                    require_string += '<td class="text-danger">'+msg["not_select"]+'</td>';
                }else{
                    require_string += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                    check_district = true;
                }
                //console.log("district: "+district+" "+check_district);
                // Commune
                var commune         = line_list_data[i][col_name["commune"]];
                var check_commune   = false;
                var commune_id      = line_list_data[i][col_name["commune_code"]];
                if(commune.length == 0 || commune_id.length == 0){
                    require_string += '<td class="text-danger">'+msg["not_select"]+'</td>';
                }else{
                    require_string += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                    check_commune = true;
                }
                //console.log("commune: "+commune+" "+check_commune);
                //village 
                var village         = line_list_data[i][col_name["village"]];
                var check_village   = false;
                var village_id      = line_list_data[i][col_name["village_code"]];
                if(village.length == 0 || village_id.length == 0){
                    require_string += '<td class="text-danger">'+msg["not_select"]+'</td>';
                }else{
                    require_string += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                    check_village = true;
                }

                // sample source
                
                var check_sample_source = false;
                if(sample_source_id == '-1'){
                    require_string += '<td class="text-danger">'+msg["not_select"]+'</td>';
                }else{
                    check_sample_source = true;
                    require_string += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                }
                // requester                
                var check_requester = false;
                if(requester_id == '-1'){
                    require_string += '<td class="text-danger">'+msg["not_select"]+'</td>';
                }else{
                    require_string += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                    check_requester = true;
                }
                // collection date                
                var check_collection_date = false;
                if(collected_date == null){
                    require_string += '<td class="text-danger">'+msg["not_select"]+'</td>';
                }else{
                    require_string += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                    check_collection_date = true;
                }
                //received_date
                var check_recieved_date = false;
                if(received_date == null){
                    require_string += '<td class="text-danger">'+msg["not_select"]+'</td>';
                }else{
                    if (collected_date_time > received_date_time) {                    
                        require_string += '<td class="text-danger">'+msg_col_rec_dt_error+'</td>';
                    }else{
                        require_string += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                        check_recieved_date = true;
                    }
                }

                // For test name
                //var test_name               = line_list_data[i][col_name["test_name"]];
                
                var check_test_result       = false;
                var check_test_result_date  = false;
                var check_perform_by        = false;
                
                if(test_name.length > 0){
                    require_string  += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                    check_test_result = true;

                    var test_result         = line_list_data[i][col_name["test_result"]];

                    var test_result_date    =  line_list_data[i][col_name["test_result_date"]];                        
                    check_test_result_date  = false;
                    var perform_by          = line_list_data[i][col_name["perform_by"]];
                    var perform_by_id       = line_list_data[i][col_name["performer_by_id"]];
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

                        require_string  += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                        check_test_result_date       = true;

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

                        require_string  += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                        check_test_result_date       = true;

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

                        require_string  += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                        check_test_result_date       = true;

                        require_string  += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                        check_perform_by = true;
                    }else{
                        require_string  += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                        check_test_result = true;

                        require_string  += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                        check_test_result_date       = true;

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
                var residence      =  line_list_data[i][col_name["residence"]];
                // convert to string in order to trim();
                if(residence !== ""){
                    residence = residence.toString(); // void value in integer
                    if(residence.lenth > 100){
                        extraErrString += " - "+label_residence+': <span class="text-danger">'+msg["not_greater_than"]+' 100 '+msg["char"]+'</span><br />';
                        extraErrCheck = false;
                    }
                }
                
                var sample_number = line_list_data[i][col_name["sample_number"]];
                if(sample_number !== ""){
                    sample_number = sample_number.toString();
                    if(sample_number.length > 11){
                        extraErrString += " - "+label_sample_number+': <span class="text-danger">'+msg["not_greater_than"]+' 11 '+msg["char"]+'</span><br />';
                        extraErrCheck = false;
                    }
                }

                var clinical_history = line_list_data[i][col_name["diagnosis"]];
                if(clinical_history !== ""){
                    clinical_history = clinical_history.toString();
                    if(clinical_history.length > 100){
                        extraErrString += " - "+label_clinical_history+': <span class="text-danger">'+msg["not_greater_than"]+' 100 '+msg["char"]+'</span><br />';
                        extraErrCheck = false;
                    }
                }                
                                
                var arrival_date = line_list_data[i][col_name["arrival_date"]];
                var arrival_date = new Date(arrival_date);

                if(arrival_date !== ""){
                    if( arrival_date > currentDate){
                        extraErrString += " - "+label_date_of_arrival+': <span class="text-danger">'+msg["not_greater_than_current_date"]+'</span><br />';
                        extraErrCheck = false;
                    }
                }

                var passport_number = line_list_data[i][col_name["passport_number"]];
                if(passport_number !== ""){
                    passport_number = passport_number.toString();
                    if(passport_number.length > 20){
                        extraErrString += " - "+label_passport_no+': <span class="text-danger">'+msg["not_greater_than"]+' 20 '+msg["char"]+'</span><br />';
                        extraErrCheck = false;
                    }
                }

                var flight_number = line_list_data[i][col_name["flight_number"]];
                if(flight_number !== ""){
                    flight_number = flight_number.toString();
                    if(flight_number.length > 20){
                        extraErrString += " - "+label_flight_number+': <span class="text-danger">'+msg["not_greater_than"]+' 20 '+msg["char"]+'</span><br />';
                        extraErrCheck = false;
                    }
                }

                var seat_number = line_list_data[i][col_name["seat_number"]];
                if(seat_number !== ""){
                    seat_number = seat_number.toString();
                    if(seat_number.length > 5){
                        extraErrString += " - "+label_seat_no+': <span class="text-danger">'+msg["not_greater_than"]+' 5 '+msg["char"]+'</span><br />';
                        extraErrCheck = false;
                    }
                }

                var test_covid_date = line_list_data[i][col_name["test_date"]];
                var test_covid_date = new Date(test_covid_date);
                
                if(test_covid_date !== ""){
                    if( test_covid_date > currentDate){
                        extraErrString += " - "+label_test_covid_date+': <span class="text-danger">'+msg["not_greater_than_current_date"]+'</span><br />';
                        extraErrCheck = false;
                    }
                }

                var reason_for_testing_id   = line_list_data[i][col_name["reason_for_testing_id"]];
                var reason_for_testing      = line_list_data[i][col_name["reason_for_testing"]];

                if( reason_for_testing == "" || reason_for_testing_id == 0 ){
                    extraErrString += " - "+label_reason_for_testing+': <span class="text-danger">'+msg["not_select"]+'</span><br />';
                    extraErrCheck   = false;
                }

                var country_name = line_list_data[i][col_name["country"]];
                if(country_name !== ""){
                    country_name = country_name.toString();
                    if(country_name.length > 150){
                        extraErrString += " - "+label_country+': <span class="text-danger">'+msg["not_greater_than"]+' 150 '+msg["char"]+'</span><br />';
                        extraErrCheck = false;
                    }
                }

                require_string += "<td>"+extraErrString+" &nbsp;</td>";
                require_string += "</tr>";

                if(!check_age 
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
                    
                    patient_code                                    = line_list_data[i][col_name["patient_code"]];                    
                    patient_code                                    = patient_code.toString();
                    line_list_data[i][col_name["patient_code"]]     = patient_code.trim();

                    age                                             = line_list_data[i][col_name["age"]];
                    //line_list_data[i][col_name["age"]]            = age.trim();

                    phone                                           = line_list_data[i][col_name["phone"]];
                    phone                                           = phone.toString();
                    line_list_data[i][col_name["phone"]]            = phone.trim();

                    residence                                       = line_list_data[i][col_name["residence"]];
                    residence                                       = residence.toString();
                    line_list_data[i][col_name["residence"]]             = residence.trim();

                    is_contacted_with                               = line_list_data[i][col_name["is_contacted_with"]];
                    line_list_data[i][col_name["is_contacted_with"]]     = is_contacted_with.trim();

                    sample_number                                   = line_list_data[i][col_name["sample_number"]];
                    sample_number                                   = sample_number.toString();
                    line_list_data[i][col_name["sample_number"]]         = sample_number.trim();

                    line_list_data[i][col_name["sample_source"]]         = sample_source;
                    line_list_data[i][col_name["requester"]]             = requester;

                    diagnosis                                       = line_list_data[i][col_name["diagnosis"]];
                    diagnosis                                       = diagnosis.toString();
                    line_list_data[i][col_name["diagnosis"]]             = diagnosis.trim();
                    
                   
                    passport_number                                 = line_list_data[i][col_name["passport_number"]];
                    passport_number                                 = passport_number.toString();
                    line_list_data[i][col_name["passport_number"]]  = passport_number.trim();

                    flight_number                                   = line_list_data[i][col_name["flight_number"]];
                    flight_number                                   = flight_number.toString();
                    line_list_data[i][col_name["flight_number"]]         = flight_number.trim();

                    seat_number                                     = line_list_data[i][col_name["seat_number"]];
                    seat_number                                     = seat_number.toString();
                    line_list_data[i][col_name["seat_number"]]           = seat_number.trim();
                    

                    line_list_data[i][col_name["is_contacted"]]          = is_contacted;
                    line_list_data[i][col_name["is_contacted_with"]]     = contact_with.trim();
                    line_list_data[i][col_name["is_directed_contact"]]   = is_direct_contact;

                    line_list_data[i][col_name["collected_date"]]        = collected_date.format('YYYY-MM-DD')+' '+collected_time.format('HH:mm:ss'); // collected date
                    line_list_data[i][col_name["received_date"]]         = received_date.format('YYYY-MM-DD')+' '+received_time.format('HH:mm:ss'); // recieved date

                    line_list_data[i][col_name["completed_by"]]          = completed_by.trim(); // completed by
                    line_list_data[i][col_name["phone_completed_by"]]    = phone_number.trim(); // phone number of completed by
                    line_list_data[i][col_name["sample_collector"]]      = sample_collector.trim(); // sample collector
                    line_list_data[i][col_name["phone_number_sample_collctor"]] = phone_number_sample_collector.trim(); // phone number of sample collector

                    //line_list_data[i][42] = reason_for_testing; // reason_for_testing
                    line_list_data[i][col_name["sample_source_id"]]      = sample_source_id; // sample_source_id
                    line_list_data[i][col_name["requester_id"]]          = requester_id; // requester_id
                    line_list_data[i][col_name["test_name"]]             = test_name; // Test name
                    
                    line_list_data[i][col_name["test_id"]]               = test_id == '-1' ? "" : test_id;// save test id
                    
                    // trim 
                    line_list_data[i][col_name["patient_name"]]          = name.trim();                   

                    data.push(line_list_data[i]);
                    array_check.push(true);
                }
            }
        }
    //    console.log(data);
        if(valid_check == 0){
            myDialog.showProgress('hide');            
            myDialog.showDialog('show', {text: msg["not_data_entry"], style: 'warning'});
            $(this).removeClass('disabled'); //prevent multiple click
            return false;
        }
        console.log("Check "+ array_check.indexOf(false));
        if((array_check.indexOf(false) >= 0)){
            myDialog.showProgress('hide');
            $("table[name=tblErrorLineList] tbody").html("");            
            $("table[name=tblErrorLineListNew] tbody").html(require_string);
            $modal_list_error.modal('show');
            $(this).removeClass('disabled'); //prevent multiple click
            return false;
        }
        if(data.length > maxRow){
            myDialog.showProgress('hide');
            myDialog.showDialog('show', {text: msg["not_greater_than_100_row"], style: 'warning'});
            $(this).removeClass('disabled'); //prevent multiple click
            return false;
        }
       // console.log(data);

        $.ajax({
            url: base_url + "/patient/add_line_list_full_form",
            type: "POST",
            data: { data: data},
            dataType: 'json',
            success: function (resText) {
            //    console.log(resText);
                var patients = resText.patients;
                var bodyResult = '';    
                var psample_ids = '';
                var n = 1;
                var htmlStringQrCode    = ''; 
                var img_qr_code         = '';
                var btnPrintQrCode      = "";
                if(resText.execution_time !== undefined){
                    console.log(resText.execution_time);
                }
                for(var i in patients) {
                    var btnPrint = "";
                   
                    var test_result_msg = "";

                    if(patients[i].psample_id !== undefined){                        
                        btnPrint = '<button type="button" class="btn btn-sm btn-success btnPrintCovidFormV1" data-psample_id="'+patients[i].psample_id+'" title="'+label_print_lab_form+'"><i class="fa fa-eye"></i></button>'
                        psample_ids += patients[i].psample_id+"n";
                    }                    
                    if(patients[i].test_result_msg !== undefined){
                        test_result_msg = patients[i].test_result_msg;
                    }
                    /*
                    if(patients[i].qr_code !== undefined){
                        //qr_code = '<img src="'+base_url+'/assets/camlis/images/patient_qr_code/'+patients[i].pqr_code+'" style="width:32px;" />'
                        img_qr_code = '<img src="'+patients[i].qr_code+'" style="width:70px;" />';
                        btnPrintQrCode = '<button type="button" class="btnQrCode btn btn-sm btn-primary" data-patient_code="'+patients[i].patient_code+'" title="'+label_qr_code+'"><i class="fa fa-qrcode"></i></button>';
                    }
                    */
                    bodyResult += '<tr>';
                    bodyResult += '<td>'+n+'</td>';
                    bodyResult += '<td>'+patients[i].patient_code+'</td>';
                    bodyResult += '<td>'+patients[i].patient_name+'</td>';
                    bodyResult += '<td>'+patients[i].msg+'</td>';  
                    bodyResult += '<td>'+patients[i].sample_number+'</td>';
                    bodyResult += '<td>'+patients[i].sample_msg+'</td>';
                    bodyResult += '<td>'+patients[i].test_msg+'</td>';
                    bodyResult += '<td>'+test_result_msg+'</td>';                    
                    //bodyResult += '<td>'+btnPrint+" "+btnPrintQrCode+'</td>';
                    bodyResult += '<td>'+btnPrint+'</td>';
                    bodyResult += '</tr>';

                    htmlStringQrCode += '<tr data-patient_code="'+patients[i].patient_code+'">';
                    htmlStringQrCode += '<td style="text-align:center; vertical-align:middle;">'+img_qr_code+'</td>';
                    htmlStringQrCode += '<td style="text-align:center; vertical-align:middle;"><span>'+patients[i].patient_code+'</span></td>';
                    htmlStringQrCode += '</tr>';

                    n++;
                }
                setTimeout(function(){
                    myDialog.showProgress('hide');
                    $("table[name=tblResultLineListNew] tbody").html(bodyResult);
                    var res = psample_ids.substring(0, psample_ids.length - 1); // remove the last n
                    $("#printAll").attr("data-psample_id",res);
                    $("#modal_result_line_list_new").modal("show");
                    $("table[name=tbl_qr_code] tbody").html(htmlStringQrCode); // 11 March 2021
                    //line_list_table_short_form.setData([]);

                    var col_name_length = Object.keys(col_name).length;
                    for(r = 0 ; r < patients.length ; r++){
                        for(c = 0 ; c < col_name_length ; c++){
                            line_list_table_short_form.setValue(jspreadsheet.getColumnNameFromId([c, r]),"");    
                        }
                    }
                    $("#btnSaveListShorFormTest").removeClass('disabled');
                }, 1000);
            },
            error: function(xhr, status, error) {
            var err = eval("(" + xhr.responseText + ")");
                console.log(err.Message);
                console.log(xhr.responseText);
            }
        });
    });
    $modal_excel_short_form.find('select[name=test_name]').on("change",function(){
        //myDialog.showProgress('show');
        var text = $( "select[name=test_name] option:selected" ).text();
        var val = $(this).val();
        test_id = $(this).val();
        //console.log(text);
        
        if(val == '-1'){
            // reset value and set readonly            
            for(var r = 0 ; r < maxRow ; r++){
                line_list_table_short_form.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["machine_name"], r]),true); 
                line_list_table_short_form.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["test_result"], r]),true);
                line_list_table_short_form.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["test_result_date"], r]), true);
                line_list_table_short_form.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["perform_by"], r]),true);
            }
        }else{
            for(var r = 0 ; r < maxRow ; r++){                
                line_list_table_short_form.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["machine_name"], r]),false);       
                line_list_table_short_form.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["test_result"], r]),false);
                line_list_table_short_form.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["test_result_date"], r]), false);
                line_list_table_short_form.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["perform_by"], r]),false);
            }
        }     
    })

    /**
     *  Import content from Excel File to ExcelJS
     *  Added: 31-05-2021
     */
    
    var $modal_upload_excel   = $("#modal_upload_excel");
    var $btnSubmitExcelFile   = $("#btnSubmitExcelFile");
    $("#btnOpenUploadFileModal").on("click", function (evt) {        
        $modal_upload_excel.modal("show");
    });
    $('input[type=file]').change(function () {
        var valid_file = false;
        var $msg = $("#theExcelFile_message");
        var val = $(this).val().toLowerCase(),
            regex = new RegExp("(.*?)\.(xlsx|xls)$");

        if (!(regex.test(val))) {
            $msg.html("Please select correct file format");
            $btnSubmitExcelFile.attr("disabled", true);
        }else{
            $msg.html("");
            $btnSubmitExcelFile.removeAttr("disabled");
            valid_file = true;
        }   
    });
    
    
    function getRequesterArray(sample_source_id){
        var res = [];        
        $.each(REQUESTER, function(key, item) {
            if(sample_source_id == item.sample_source_id){
                res.push(item.requester_name);
            }
        });
        return res;
    }
    function getDistrictsArray(province_code){
        var res = [];
        if(app_language == 'en'){
            $.each(DISTRICTS, function(key, item) {
                if(province_code == item.province_code){
                    res.push(item.name_en);
                }
            });
        }else{
            $.each(DISTRICTS, function(key, item) {
                if(province_code == item.province_code){
                    res.push(item.name_kh);
                }
            }); 
        }        
        return res;
    }
    function getCommunesArray(district_code){
        var res           = [];
        if(app_language == 'kh'){
            $.each(COMMUNES, function(key, item) {
                if(district_code == item.district_code){
                    res.push(item.name_kh);
                }
            });
        }else{
            $.each(COMMUNES, function(key, item) {
                if(district_code == item.district_code){
                    res.push(item.name_en);
                }
            });
        }
        return res;
    }

    function getVillagesArray(commune_code){
        var res             = [];
        if(app_language == 'kh'){
            $.each(VILLAGES, function(key, item) {
                if(commune_code == item.commune_code){
                    res.push(item.name_kh);
                }
            });
        }else{
            $.each(VILLAGES, function(key, item) {
                if(commune_code == item.commune_code){
                    res.push(item.name_en);
                }
            });
        }
        return res;
    }

    function getTestResultsArray(test_id){        
        var res = [];
        var result = result_arr[test_id];
        if(result.length > 0){
           for(var i in result){
                res.push(result[i].organism_name);
           }
        }
        return res;
    }
    
    function ExcelDateToJSDate(serial) {
        var utc_days  = Math.floor(serial - 25569);
        var utc_value = utc_days * 86400;                                        
        var date_info = new Date(utc_value * 1000);
        var fractional_day = serial - Math.floor(serial) + 0.0000001;
        var total_seconds = Math.floor(86400 * fractional_day);
        var seconds = total_seconds % 60;
        total_seconds -= seconds;
        var hours = Math.floor(total_seconds / (60 * 60));
        var minutes = Math.floor(total_seconds / 60) % 60;
        return new Date(date_info.getFullYear(), date_info.getMonth(), date_info.getDate(), hours, minutes, seconds);
    }
     
     
     $('#theUploadForm').submit(function(e){  

        e.preventDefault();
        myDialog.showProgress('show');
        $btnSubmitExcelFile.html('Please wait ...').attr('disabled', 'disabled'); // prevent multiple click
        //setTimeout(function(){ 
            $.ajax({
                url:base_url + '/patient/upload_file', 
                type:"post",
                data:new FormData(this), //this is formData
                processData:false,
                contentType:false,
                cache:false,
                async:false,
                dataType: 'json',		
                success	: function (resText)
                {
                    //console.log(resText);
                    //console.log(resText.status);
                    //console.log(resText.msg);
                    
                    if(resText.status){
                        //console.log("true");
                        var filename    = resText.filename;
                        var data        = resText.data.values;
                        var content     = [];
                        var rNum        = 0;
                        var rowNumber   = 1;
                        var currentDate     = new Date();
                        
                        var letter_col_name_excel = {
                            patient_code                    : "A",
                            patient_name                    : "B",
                            age                             : "C",
                            gender                          : "D",
                            phone                           : "E",
                            province                        : "F",
                            district                        : "G",
                            commune                         : "H",
                            village                         : "I",
                            residence                       : "J",
                            reason_for_testing              : "K",
                            is_contacted                    : "L",
                            is_contacted_with               : "M",
                            is_directed_contact             : "N",
                            sample_number                   : "O",
                            sample_source                   : "P",
                            requester                       : "Q",
                            collected_date                  : "R",
                            received_date                   : "S",                           
                            diagnosis                       : "T",
                            completed_by                    : "U",
                            phone_completed_by              : "V",
                            sample_collector                : "W",
                            phone_number_sample_collctor    : "X",
                            clinical_symptom                : "Y",
                            health_facility                 : "Z",
                            test_name                       : "AA",
                            machine_name                    : "AB",
                            test_result                     : "AC",
                            test_result_date                : "AD",
                            perform_by                      : "AE",
                            country                         : "AF",
                            nationality                     : "AG",
                            arrival_date                    : "AH",
                            passport_number                 : "AI",
                            flight_number                   : "AJ",
                            seat_number                     : "AK",
                            is_positive_covid               : "AL",
                            test_date                       : "AM",
                            number_of_sample                : "AN",
                            vaccination_status              : "AO",
                            first_vaccinated_date           : "AP",
                            second_vaccinated_date          : "AQ",
                            vaccine_name                    : "AR",
                            occupation                      : "AS",
                        }
                        
                        //console.log(data.length);
                        for(var i in data){
                            if(rNum < 500){
                                // if patient code or name empty, we ignore it
                           // if(data[i][letter_col_name_excel["patient_name"]] !== null || data[i][letter_col_name_excel["patient_name"]] !== undefined){
                                patient_code = (data[i][letter_col_name_excel["patient_code"]] == null || data[i][letter_col_name_excel["patient_code"]] == "") ? "" : data[i][letter_col_name_excel["patient_code"]];
                                patient_name = (data[i][letter_col_name_excel["patient_name"]] == null || data[i][letter_col_name_excel["patient_name"]] == "") ? "" : data[i][letter_col_name_excel["patient_name"]].trim();
                                if( patient_code !== "" ){
                                    if( patient_code.length > 60 ){
                                        line_list_table_new.setComments(letter_col_name["patient_code"]+rowNumber, msg["not_greater_than_60"]); // reset comment
                                    }else{
                                        line_list_table_new.setValue(jspreadsheet.getColumnNameFromId([col_name["patient_code"], rNum]), patient_code);
                                    }
                                }
                                if( patient_name !== "" ){                                    
                                    if( patient_name.length > 60 ){
                                        line_list_table_new.setComments(letter_col_name["patient_name"]+rowNumber, msg["not_greater_than_60"]); // reset comment
                                    }else{
                                        line_list_table_new.setValue(jspreadsheet.getColumnNameFromId([col_name["patient_name"], rNum]), patient_name);
                                    }
                                }
                                
                                line_list_table_new.setValue(jspreadsheet.getColumnNameFromId([col_name["age"], rNum]), data[i][letter_col_name_excel["age"]]);
                                
                                var gender = (data[i][letter_col_name_excel["gender"]] == null || data[i][letter_col_name_excel["gender"]] == undefined ) ? "" : data[i][letter_col_name_excel["gender"]].trim();
                                if( gender !== "" ){
                                    if(gender_array.indexOf(gender) == '-1'){
                                        line_list_table_new.setComments(letter_col_name["gender"]+rowNumber,'"'+gender+'" '+ msg["not_correct"]); // reset comment
                                    }else{
                                        line_list_table_new.setValue(jspreadsheet.getColumnNameFromId([col_name["gender"], rNum]), gender);
                                    }
                                }

                                var phone           = (data[i][letter_col_name_excel["phone"]] == null || data[i][letter_col_name_excel["phone"]] == undefined ) ? "" : data[i][letter_col_name_excel["phone"]];
                                if(phone !== ""){
                                    // convert it to string and trim()
                                    phone = phone.toString();
                                    phone = phone.trim();
                                }
                                line_list_table_new.setValue(jspreadsheet.getColumnNameFromId([col_name["phone"], rNum]), phone);

                                var province        = (data[i][letter_col_name_excel["province"]] == null || data[i][letter_col_name_excel["province"]] == undefined ) ? "" : data[i][letter_col_name_excel["province"]].trim();
                                var province_code   = "";
                                
                                if( province !== "" ){

                                    if(app_language == 'en') {
                                        province_index = province_array.findIndex(item => province.toLowerCase() === item.toLowerCase());                                        
                                    }else{
                                        province_index = province_array.indexOf(province);
                                    }

                                    if( province_index == '-1'){
                                        line_list_table_new.setComments(letter_col_name["province"]+rowNumber,'"'+province+'" '+ msg["not_correct"]); // reset comment
                                    }else{
                                        line_list_table_new.setValue(jspreadsheet.getColumnNameFromId([col_name["province"], rNum]), province_array[province_index]);                                        
                                        province_code = line_list_table_new.getValueFromCoords(col_name["province_code"],rNum);
                                    }
                                }

                                var district        = (data[i][letter_col_name_excel["district"]] == null || data[i][letter_col_name_excel["district"]] == undefined ) ? "" : data[i][letter_col_name_excel["district"]].trim();
                                var district_code   = "";
                                if( district !== "" ){
                                    if(province_code == ""){
                                        line_list_table_new.setComments(letter_col_name["district"]+rowNumber, msg["select_province_first"]); // reset comment
                                    }else{
                                        var list_districts = getDistrictsArray(province_code);
                                        if(app_language == 'en'){
                                            district_index  = list_districts.findIndex(item => district.toLowerCase() === item.toLowerCase());                                            
                                        }else{
                                            district_index  = list_districts.indexOf(district);
                                        }
    
                                        if( district_index == '-1'){
                                            line_list_table_new.setComments(letter_col_name["district"]+rowNumber,'"'+district+'" '+ msg["not_correct"]); // reset comment
                                        }else{
                                            line_list_table_new.setValue(jspreadsheet.getColumnNameFromId([col_name["district"], rNum]), list_districts[district_index]);
                                            district_code = line_list_table_new.getValueFromCoords(col_name["district_code"],rNum);
                                        }
                                    }
                                }
                                
                                var commune        = (data[i][letter_col_name_excel["commune"]] == null || data[i][letter_col_name_excel["commune"]] == undefined ) ? "" : data[i][letter_col_name_excel["commune"]].trim();
                                var commune_code   = "";
                                if( commune !== "" ){
                                    if(district_code == ""){
                                        line_list_table_new.setComments(letter_col_name["commune"]+rowNumber, msg["select_district_first"]); // reset comment
                                    }else{
                                        var list_communes = getCommunesArray(district_code);
                                        if(app_language == 'en'){
                                            commune_index  = list_communes.findIndex(item => commune.toLowerCase() === item.toLowerCase());
                                        }else{
                                            commune_index  = list_communes.indexOf(commune);
                                        }
    
                                        if( commune_index == '-1'){
                                            line_list_table_new.setComments(letter_col_name["commune"]+rowNumber,'"'+commune+'" '+ msg["not_correct"]); // reset comment
                                        }else{
                                            line_list_table_new.setValue(jspreadsheet.getColumnNameFromId([col_name["commune"], rNum]), list_communes[commune_index]);
                                            commune_code = line_list_table_new.getValueFromCoords(col_name["commune_code"],rNum);
                                        }
                                    }
                                }
                                
                                var village        = (data[i][letter_col_name_excel["village"]] == null || data[i][letter_col_name_excel["village"]] == undefined ) ? "" : data[i][letter_col_name_excel["village"]].trim();
                                var village_code   = "";
                                if( village !== "" ){
                                    if(commune_code == ""){
                                        line_list_table_new.setComments(letter_col_name["village"]+rowNumber,msg["select_commune_first"]);
                                    }else{
                                        var list_villages = getVillagesArray(commune_code);
                                        if(app_language == 'en'){
                                            village_index  = list_villages.findIndex(item => village.toLowerCase() === item.toLowerCase());                                            
                                        }else{
                                            village_index  = list_villages.indexOf(village);
                                        }
    
                                        if( village_index == '-1'){
                                            line_list_table_new.setComments(letter_col_name["village"]+rowNumber,'"'+village+'" '+ msg["not_correct"]); // reset comment
                                        }else{
                                            line_list_table_new.setValue(jspreadsheet.getColumnNameFromId([col_name["village"], rNum]), list_villages[village_index]);
                                        }
                                    }
                                    
                                }
                                var residence      = (data[i][letter_col_name_excel["residence"]] == null || data[i][letter_col_name_excel["residence"]] == undefined ) ? "" : data[i][letter_col_name_excel["residence"]].trim();
                                if(residence !== ""){
                                    if(residence.length > 100){
                                        line_list_table_new.setComments(letter_col_name["residence"]+rowNumber,'"'+residence+'" '+ msg["not_greater_than_100"]); // reset comment
                                    }else{
                                        line_list_table_new.setValue(jspreadsheet.getColumnNameFromId([col_name["residence"], rNum]), residence);
                                    }
                                }
                                
                                var reason_for_testing   = (data[i][letter_col_name_excel["reason_for_testing"]] == null || data[i][letter_col_name_excel["reason_for_testing"]] == undefined ) ? "" : data[i][letter_col_name_excel["reason_for_testing"]].trim();
                                if( reason_for_testing !== "" ){
                                    if(app_language == 'en'){
                                        reason_for_testing_index  = reason_for_testing_array.findIndex(item => reason_for_testing.toLowerCase() === item.toLowerCase());                                        
                                    }else{
                                        reason_for_testing_index  = reason_for_testing_array.indexOf(reason_for_testing);
                                    }
                                    if(reason_for_testing_index == '-1'){
                                        line_list_table_new.setComments(letter_col_name["reason_for_testing"]+rowNumber,'"'+reason_for_testing+'" '+ msg["not_correct"]); // reset comment
                                    }else{
                                        line_list_table_new.setValue(jspreadsheet.getColumnNameFromId([col_name["reason_for_testing"], rNum]), reason_for_testing_array[reason_for_testing_index]);
                                    }
                                }
                                                               
                                line_list_table_new.setValue(jspreadsheet.getColumnNameFromId([col_name["is_contacted"], rNum]), data[i][letter_col_name_excel["is_contacted"]]);
                                line_list_table_new.setValue(jspreadsheet.getColumnNameFromId([col_name["is_contacted_with"], rNum]), data[i][letter_col_name_excel["is_contacted_with"]]);
                                line_list_table_new.setValue(jspreadsheet.getColumnNameFromId([col_name["is_directed_contact"], rNum]), data[i][letter_col_name_excel["is_directed_contact"]]);

                                var sample_number = (data[i][letter_col_name_excel["sample_number"]] == null || data[i][letter_col_name_excel["sample_number"]] == undefined ) ? "" : data[i][letter_col_name_excel["sample_number"]].trim();
                                
                                if(sample_number !== ""){
                                    if(sample_number.length > maxSampleNumber){
                                        line_list_table_new.setComments(letter_col_name["sample_number"]+rowNumber,'"'+sample_number+'" '+ msg["not_greater_than"] +" "+maxSampleNumber +" "+ msg["char"]); // reset comment
                                    }else{
                                        line_list_table_new.setValue(jspreadsheet.getColumnNameFromId([col_name["sample_number"], rNum]), sample_number);
                                    }
                                }
                                
                                var sample_source       = "";
                                var sample_source       = (data[i][letter_col_name_excel["sample_source"]] == null || data[i][letter_col_name_excel["sample_source"]] == undefined ) ? "" : data[i][letter_col_name_excel["sample_source"]].trim();
                                var sample_source_id    = "";
                                if( sample_source !== "" ){
                                    if(sample_source_array.indexOf(sample_source) == '-1'){
                                        line_list_table_new.setComments(letter_col_name_excel["sample_source"]+rowNumber,'"'+sample_source+'" '+ msg["not_correct"]); // reset comment
                                    }else{
                                        line_list_table_new.setValue(jspreadsheet.getColumnNameFromId([col_name["sample_source"], rNum]), sample_source);
                                        sample_source_id = line_list_table_new.getValueFromCoords(col_name["sample_source_id"],rNum);
                                    }
                                }
                                
                                var requester = (data[i][letter_col_name_excel["requester"]] == null || data[i][letter_col_name_excel["requester"]] == undefined ) ? "" : data[i][letter_col_name_excel["requester"]].trim();
                                
                                if( requester !== "" ){
                                    if(sample_source_id == ""){
                                        line_list_table_new.setComments(letter_col_name["requester"]+rowNumber, msg["select_sample_source_first"]);
                                    }else{
                                        var list_requester = getRequesterArray(sample_source_id);                                        
                                        if(list_requester.indexOf(requester) == '-1'){
                                            line_list_table_new.setComments(letter_col_name["requester"]+rowNumber,'"'+requester+'" '+ msg["not_correct"]); // reset comment
                                        }else{
                                            line_list_table_new.setValue(jspreadsheet.getColumnNameFromId([col_name["requester"], rNum]), requester);
                                        }
                                    }
                                
                                }
                                                                
                                var collected_date   = (data[i][letter_col_name_excel["collected_date"]] == null || data[i][letter_col_name_excel["collected_date"]] == undefined ) ? "" : data[i][letter_col_name_excel["collected_date"]];                   
                                if(collected_date !== ""){
                                    
                                    var clt_date_obj    = ExcelDateToJSDate(collected_date)
                                    var dd              = String(clt_date_obj.getDate()).padStart(2, '0');
                                    var mm              = String(clt_date_obj.getMonth() + 1).padStart(2, '0');
                                    var yyyy            = clt_date_obj.getFullYear();
                                    collected_date      = yyyy+'-'+mm+'-'+dd +' '+clt_date_obj.getHours() + ":" + clt_date_obj.getMinutes();
                                    
                                    if( clt_date_obj > currentDate){
                                        line_list_table_new.setComments(letter_col_name["collected_date"]+rowNumber,'"'+collected_date+'" '+ msg["not_greater_than_current_date"]); // reset comment                                       
                                    }else{    
                                        line_list_table_new.setValue(jspreadsheet.getColumnNameFromId([col_name["collected_date"], rNum]), collected_date);
                                    }
                                }
                                //console.log(received_date);
                                var received_date   = (data[i][letter_col_name_excel["received_date"]] == null || data[i][letter_col_name_excel["received_date"]] == undefined ) ? "" : data[i][letter_col_name_excel["received_date"]];
                                if(received_date !== ""){
                                    
                                    var received_date_obj    = ExcelDateToJSDate(received_date)
                                    var dd              = String(received_date_obj.getDate()).padStart(2, '0');
                                    var mm              = String(received_date_obj.getMonth() + 1).padStart(2, '0');
                                    var yyyy            = received_date_obj.getFullYear();
                                    received_date      = yyyy+'-'+mm+'-'+dd +' '+received_date_obj.getHours() + ":" + received_date_obj.getMinutes();
                                    //console.log(received_date);
                                    if( received_date_obj > currentDate){
                                        line_list_table_new.setComments(letter_col_name_excel["received_date"]+rowNumber,'"'+received_date+'" '+ msg["not_greater_than_current_date"]); // reset comment                                       
                                    }else{
                                        line_list_table_new.setValue(jspreadsheet.getColumnNameFromId([col_name["received_date"], rNum]), received_date);
                                    }
                                }
                                var diagnosis   = (data[i][letter_col_name_excel["diagnosis"]] == null || data[i][letter_col_name_excel["diagnosis"]] == undefined ) ? "" : data[i][letter_col_name_excel["diagnosis"]].trim();
                                line_list_table_new.setValue(jspreadsheet.getColumnNameFromId([col_name["diagnosis"], rNum]), diagnosis);

                                var completed_by   = (data[i][letter_col_name_excel["completed_by"]] == null || data[i][letter_col_name_excel["completed_by"]] == undefined ) ? "" : data[i][letter_col_name_excel["completed_by"]].trim();
                                line_list_table_new.setValue(jspreadsheet.getColumnNameFromId([col_name["completed_by"], rNum]), completed_by);

                                var phone_completed_by   = (data[i][letter_col_name_excel["phone_completed_by"]] == null || data[i][letter_col_name_excel["phone_completed_by"]] == undefined ) ? "" : data[i][letter_col_name_excel["phone_completed_by"]].trim();
                                line_list_table_new.setValue(jspreadsheet.getColumnNameFromId([col_name["phone_completed_by"], rNum]), phone_completed_by);

                                var sample_collector   = (data[i][letter_col_name_excel["sample_collector"]] == null || data[i][letter_col_name_excel["sample_collector"]] == undefined ) ? "" : data[i][letter_col_name_excel["sample_collector"]].trim();
                                line_list_table_new.setValue(jspreadsheet.getColumnNameFromId([col_name["sample_collector"], rNum]), sample_collector);

                                var phone_number_sample_collctor   = (data[i][letter_col_name_excel["phone_number_sample_collctor"]] == null || data[i][letter_col_name_excel["phone_number_sample_collctor"]] == undefined ) ? "" : data[i][letter_col_name_excel["phone_number_sample_collctor"]].trim();
                                line_list_table_new.setValue(jspreadsheet.getColumnNameFromId([col_name["phone_number_sample_collctor"], rNum]), phone_number_sample_collctor);

                                var clinical_symptom   = (data[i][letter_col_name_excel["clinical_symptom"]] == null || data[i][letter_col_name_excel["clinical_symptom"]] == undefined ) ? "" : data[i][letter_col_name_excel["clinical_symptom"]].trim();
                                if(clinical_symptom !== ""){
                                    line_list_table_new.setValue(jspreadsheet.getColumnNameFromId([col_name["clinical_symptom"], rNum]), data[i][letter_col_name_excel["clinical_symptom"]]);
                                }
                                

                                var health_facility   = (data[i][letter_col_name_excel["health_facility"]] == null || data[i][letter_col_name_excel["health_facility"]] == undefined ) ? "" : data[i][letter_col_name_excel["health_facility"]].trim();
                                line_list_table_new.setValue(jspreadsheet.getColumnNameFromId([col_name["health_facility"], rNum]), health_facility);

                                var test_name   = (data[i][letter_col_name_excel["test_name"]] == null || data[i][letter_col_name_excel["test_name"]] == undefined ) ? "" : data[i][letter_col_name_excel["test_name"]].trim();
                                var test_id     = "";
                                if( test_name !== "" ){
                                    
                                    test_name_index = test_name_array.findIndex(item => test_name.toLowerCase() === item.toLowerCase());
                                    if(test_name_index == '-1'){                                    
                                        line_list_table_new.setComments(letter_col_name["test_name"]+rowNumber,'"'+test_name+'" '+ msg["not_correct"]); // reset comment
                                    }else{
                                        line_list_table_new.setValue(jspreadsheet.getColumnNameFromId([col_name["test_name"], rNum]), test_name_array[test_name_index]);
                                        test_id =  line_list_table_new.getValueFromCoords(col_name["test_id"],rNum);
                                    }
                                }

                                var machine_name   = (data[i][letter_col_name_excel["machine_name"]] == null || data[i][letter_col_name_excel["machine_name"]] == undefined ) ? "" : data[i][letter_col_name_excel["machine_name"]].trim();
                                if( machine_name !== "" ){
                                    machine_name_index = machine_name_array.findIndex(item => machine_name.toLowerCase() === item.toLowerCase());
                                    if(machine_name_index == '-1'){
                                        line_list_table_new.setComments(letter_col_name["machine_name"]+rowNumber,'"'+machine_name+'" '+ msg["not_correct"]); // reset comment
                                    }else{
                                        line_list_table_new.setValue(jspreadsheet.getColumnNameFromId([col_name["machine_name"], rNum]), machine_name_array[machine_name_index]);
                                    }
                                }
                                
                                var test_result   = (data[i][letter_col_name_excel["test_result"]] == null || data[i][letter_col_name_excel["test_result"]] == undefined ) ? "" : data[i][letter_col_name_excel["test_result"]].trim();
                                if( test_result !== "" ){
                                    if(test_id == ""){
                                        line_list_table_new.setComments(letter_col_name["test_result"]+rowNumber, msg["select_test_name"]); // reset comment
                                    }else{
                                        var list_result_array   = getTestResultsArray(test_id);
                                        test_result_index       = list_result_array.findIndex(item => test_result.toLowerCase() === item.toLowerCase());

                                        if(test_result_index == '-1'){
                                            line_list_table_new.setComments(letter_col_name["test_result"]+rowNumber,'"'+test_result+'" '+ msg["not_correct"]); // reset comment
                                        }else{
                                            line_list_table_new.setValue(jspreadsheet.getColumnNameFromId([col_name["test_result"], rNum]), list_result_array[test_result_index]);
                                        }
                                    }
                                }
                                
                                var test_result_date   = (data[i][letter_col_name_excel["test_result_date"]] == null || data[i][letter_col_name_excel["test_result_date"]] == undefined ) ? "" : data[i][letter_col_name_excel["test_result_date"]];
                                if(test_result_date !== ""){
                                    
                                    var test_result_date_obj    = ExcelDateToJSDate(test_result_date)
                                    var dd              = String(test_result_date_obj.getDate()).padStart(2, '0');
                                    var mm              = String(test_result_date_obj.getMonth() + 1).padStart(2, '0');
                                    var yyyy            = test_result_date_obj.getFullYear();
                                    test_result_date      = yyyy+'-'+mm+'-'+dd;
                                    if( test_result_date_obj > currentDate){
                                        line_list_table_new.setComments(letter_col_name["test_result_date"]+rowNumber,'"'+test_result_date+'" '+ msg["not_greater_than_current_date"]); // reset comment                                       
                                    }else{
                                        line_list_table_new.setValue(jspreadsheet.getColumnNameFromId([col_name["test_result_date"], rNum]), test_result_date);
                                    }
                                }

                                var perform_by   = (data[i][letter_col_name_excel["perform_by"]] == null || data[i][letter_col_name_excel["perform_by"]] == undefined ) ? "" : data[i][letter_col_name_excel["perform_by"]].trim();
                                line_list_table_new.setValue(jspreadsheet.getColumnNameFromId([col_name["perform_by"], rNum]), perform_by);

                                var country   = (data[i][letter_col_name_excel["country"]] == null || data[i][letter_col_name_excel["country"]] == undefined ) ? "" : data[i][letter_col_name_excel["country"]].trim();
                                line_list_table_new.setValue(jspreadsheet.getColumnNameFromId([col_name["country"], rNum]), country);

                                var nationality   = (data[i][letter_col_name_excel["nationality"]] == null || data[i][letter_col_name_excel["nationality"]] == undefined ) ? "" : data[i][letter_col_name_excel["nationality"]].trim();
                                if( nationality !== "" ){
                                    nationality_index       = nationalities_array.findIndex(item => nationality.toLowerCase() === item.toLowerCase());
                                    if(nationality_index == '-1'){
                                        line_list_table_new.setComments(letter_col_name["nationality"]+rowNumber,'"'+nationality+'" '+ msg["not_correct"]); // reset comment
                                    }else{
                                        line_list_table_new.setValue(jspreadsheet.getColumnNameFromId([col_name["nationality"], rNum]), nationalities_array[nationality_index]);
                                    }
                                }

                                var arrival_date   = (data[i][letter_col_name_excel["arrival_date"]] == null || data[i][letter_col_name_excel["arrival_date"]] == undefined ) ? "" : data[i][letter_col_name_excel["arrival_date"]];
                                if(arrival_date !== ""){                                    
                                    var arrival_date_obj    = ExcelDateToJSDate(arrival_date)
                                    var dd              = String(arrival_date_obj.getDate()).padStart(2, '0');
                                    var mm              = String(arrival_date_obj.getMonth() + 1).padStart(2, '0');
                                    var yyyy            = arrival_date_obj.getFullYear();
                                    arrival_date        = yyyy+'-'+mm+'-'+dd;
                                    if( arrival_date_obj > currentDate){
                                        line_list_table_new.setComments(letter_col_name["arrival_date"]+rowNumber,'"'+arrival_date+'" '+ msg["not_greater_than_current_date"]); // reset comment                                       
                                    }else{
                                        line_list_table_new.setValue(jspreadsheet.getColumnNameFromId([col_name["arrival_date"], rNum]), arrival_date);
                                    }
                                }
                                
                                var passport_number   = (data[i][letter_col_name_excel["passport_number"]] == null || data[i][letter_col_name_excel["passport_number"]] == undefined ) ? "" : data[i][letter_col_name_excel["passport_number"]].trim();
                                line_list_table_new.setValue(jspreadsheet.getColumnNameFromId([col_name["passport_number"], rNum]), passport_number);

                                var flight_number   = (data[i][letter_col_name_excel["flight_number"]] == null || data[i][letter_col_name_excel["flight_number"]] == undefined ) ? "" : data[i][letter_col_name_excel["flight_number"]].trim();
                                line_list_table_new.setValue(jspreadsheet.getColumnNameFromId([col_name["flight_number"], rNum]), flight_number);
                                
                                var seat_number   = (data[i][letter_col_name_excel["seat_number"]] == null || data[i][letter_col_name_excel["seat_number"]] == undefined ) ? "" : data[i][letter_col_name_excel["seat_number"]].trim();
                                line_list_table_new.setValue(jspreadsheet.getColumnNameFromId([col_name["seat_number"], rNum]), seat_number);
                                
                                line_list_table_new.setValue(jspreadsheet.getColumnNameFromId([col_name["is_positive_covid"], rNum]), data[i][letter_col_name_excel["is_positive_covid"]]);
                                line_list_table_new.setValue(jspreadsheet.getColumnNameFromId([col_name["test_date"], rNum]), data[i][letter_col_name_excel["test_date"]]);

                                var test_date   = (data[i][letter_col_name_excel["test_date"]] == null || data[i][letter_col_name_excel["test_date"]] == undefined ) ? "" : data[i][letter_col_name_excel["test_date"]];                                
                                if(test_date !== ""){
                                    var test_date_obj   = ExcelDateToJSDate(test_date)
                                    var dd              = String(test_date_obj.getDate()).padStart(2, '0');
                                    var mm              = String(test_date_obj.getMonth() + 1).padStart(2, '0');
                                    var yyyy            = test_date_obj.getFullYear();
                                    test_date           = yyyy+'-'+mm+'-'+dd;
                                    if( test_date_obj > currentDate){
                                        line_list_table_new.setComments(letter_col_name["test_date"]+rowNumber,'"'+test_date+'" '+ msg["not_greater_than_current_date"]); // reset comment                                       
                                    }else{
                                        line_list_table_new.setValue(jspreadsheet.getColumnNameFromId([col_name["test_date"], rNum]), test_date);
                                    }
                                }

                                line_list_table_new.setValue(jspreadsheet.getColumnNameFromId([col_name["number_of_sample"], rNum]), data[i][letter_col_name_excel["number_of_sample"]]);

                                //03082021
                                var vaccination_status   = (data[i][letter_col_name_excel["vaccination_status"]] == null || data[i][letter_col_name_excel["vaccination_status"]] == undefined ) ? "" : data[i][letter_col_name_excel["vaccination_status"]];                                
                                if(vaccination_status !== ""){
                                    line_list_table_new.setValue(jspreadsheet.getColumnNameFromId([col_name["vaccination_status"], rNum]), vaccination_status);
                                    var first_vaccinated_date = (data[i][letter_col_name_excel["first_vaccinated_date"]] == null || data[i][letter_col_name_excel["first_vaccinated_date"]] == undefined ) ? "" : data[i][letter_col_name_excel["first_vaccinated_date"]];
                                    
                                    if(first_vaccinated_date !== ""){
                                        var first_vaccinated_date_obj   = ExcelDateToJSDate(first_vaccinated_date);
                                        var dd                          = String(first_vaccinated_date_obj.getDate()).padStart(2, '0');
                                        var mm                          = String(first_vaccinated_date_obj.getMonth() + 1).padStart(2, '0');
                                        var yyyy                        = first_vaccinated_date_obj.getFullYear();
                                        first_vaccinated_date           = yyyy+'-'+mm+'-'+dd;                                       
                                        if( first_vaccinated_date_obj > currentDate){
                                            line_list_table_new.setComments(letter_col_name["first_vaccinated_date"]+rowNumber,'"'+first_vaccinated_date+'" '+ msg["not_greater_than_current_date"]); // reset comment
                                        }else{
                                            line_list_table_new.setValue(jspreadsheet.getColumnNameFromId([col_name["first_vaccinated_date"], rNum]), first_vaccinated_date);
                                        }
                                    }
                                    
                                    var second_vaccinated_date = (data[i][letter_col_name_excel["second_vaccinated_date"]] == null || data[i][letter_col_name_excel["second_vaccinated_date"]] == undefined ) ? "" : data[i][letter_col_name_excel["second_vaccinated_date"]];
                                    
                                    if(second_vaccinated_date !== ""){
                                        var second_vaccinated_date_obj  = ExcelDateToJSDate(second_vaccinated_date);
                                        var dd                          = String(second_vaccinated_date_obj.getDate()).padStart(2, '0');
                                        var mm                          = String(second_vaccinated_date_obj.getMonth() + 1).padStart(2, '0');
                                        var yyyy                        = second_vaccinated_date_obj.getFullYear();
                                        second_vaccinated_date          = yyyy+'-'+mm+'-'+dd;
                                        
                                        if( second_vaccinated_date_obj > currentDate){
                                            line_list_table_new.setComments(letter_col_name["second_vaccinated_date"]+rowNumber,'"'+second_vaccinated_date+'" '+ msg["not_greater_than_current_date"]); // reset comment
                                        }else{                                            
                                            line_list_table_new.setValue(jspreadsheet.getColumnNameFromId([col_name["second_vaccinated_date"], rNum]), second_vaccinated_date);
                                        }
                                    }

                                    var vaccine_name = (data[i][letter_col_name_excel["vaccine_name"]] == null || data[i][letter_col_name_excel["vaccine_name"]] == undefined ) ? "" : data[i][letter_col_name_excel["vaccine_name"]];
                                    line_list_table_new.setValue(jspreadsheet.getColumnNameFromId([col_name["vaccine_name"], rNum]), vaccine_name);

                                    var occupation = (data[i][letter_col_name_excel["occupation"]] == null || data[i][letter_col_name_excel["occupation"]] == undefined ) ? "" : data[i][letter_col_name_excel["occupation"]];
                                    line_list_table_new.setValue(jspreadsheet.getColumnNameFromId([col_name["occupation"], rNum]), occupation);
                                }

                                rNum++;
                                rowNumber++;
                            //} End if
                            }
                        }
                        
                        setTimeout(() => {
                            myDialog.showProgress('hide'); 
                            $modal_upload_excel.modal("hide");
                            $btnSubmitPatientForm.removeAttr("disabled");
                            $btnSubmitExcelFile.removeAttr("disabled");
                            $btnSubmitExcelFile.html('Submit');
                            if(rNum == 0){
                                myDialog.showDialog('show', {text: mg["excel_none_data_again"], style: 'warning'});
                            }else{
                                if(rNum > 500){
                                    myDialog.showDialog('show', {text: msg["data_over_max_only_500_added"],style: 'warning'});
                                }else{
                                    myDialog.showDialog('show', {text: msg["data_inserted_successful"] ,style: 'success'});
                                }
                                
                            }
                        }, 800); 
                    }else{
                        myDialog.showProgress('hide');
                        setTimeout(() => {
                            myDialog.showDialog('show', {text: resText.msg ,style: 'warning'});
                            $btnSubmitExcelFile.removeAttr("disabled");
                        }, 500);
                    }
                }
            });
    //    }, 300);
                
    });
    // added 15-06-2021
    $(document).on('click','button.btnQrCode', function(evt) {
        evt.preventDefault();
        var patient_code = $(this).attr("data-patient_code");
        //console.log(patient_code);
        //$(this).attr('disabled', 'disabled'); // prevent multiple click    
       
        $('#tbl_qr_code tbody tr').each( (tr_idx,tr) => {
            if(patient_code == "-1"){
                $(tr).removeClass("no-print");
            }else if(patient_code !== $(tr).attr("data-patient_code")){
                $(tr).addClass("no-print");
            }else{
                $(tr).removeClass("no-print");
            }                      
        });  
        printJS({
            printable: "tbl_qr_code_wrapper",
            type: "html",            
            style: [
              "@page { size: auto; margin: 0mm;} @media print{#tbl_qr_code_wrapper {width:50mm; height: 23mm;} span{font-size:10px;} .no-print, .no-print *{display: none !important;} }"
            ],
            targetStyles: ["*"]
        });
    //    return false;
    })
})
 
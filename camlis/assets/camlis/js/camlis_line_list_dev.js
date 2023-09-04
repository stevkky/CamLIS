
function getProvinceCode(name_kh){
     var code = "";
    $.each(PROVINCES, function(key, row) {
        if(row.name_kh == name_kh){
            code = row.code;
            return false;
        }
    });
    return code;
 }
 function getDistrictCode(name_kh , province_code){
    var code = "";
   $.each(DISTRICTS, function(key, row) {
       if(row.name_kh == name_kh && row.province_code == province_code){
           code = row.code;
           return false;
       }
   });
   return code;
}
function getCommuneCode(name_kh, district_code){
    var code = "";
   $.each(COMMUNES, function(key, row) {
       if(row.name_kh == name_kh && row.district_code == district_code){
           code = row.code;
           return false;
       }
   });
   return code;
}
function getVillageCode(name_kh , commune_code){
   var code = "";
   $.each(VILLAGES, function(key, row) {
       if(row.name_kh == name_kh && row.commune_code == commune_code){
           code = row.code;
           return false;
       }
   });
   return code;
}

function getReason(reason){
    var code = "";    
    $.each(REASON_FOR_TESTING, function(key, value) {
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

function getClinicalSymptom(val){
    var res = val.split(";");
    var result = "";
    for(var i in res){
        $.each(CLINICAL_SYMPTOM, function(key, value) {
            if(value.name_kh == res[i]){
                result += value.ID+";";
                return false;
            }           
          });
    }
    return result;
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
$(function () {

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
    var $btnSubmitPatientForm           = $("#btnSubmitPatientForm");
    var $print_preview_covid_form_modal	= $("#print_preview_covid_form_modal");
    var $modal_patient_excel	        = $("#modal-read-patient-from-excel_dev");
    var $modal_error_line_list_1        = $("#modal_error_line_list");
    var $modal_upload_excel	            = $("#modal_upload_excel");

    $("select[name=clinical_symptom]").select2();
    
    $("select[name=sample_source]").on("change",  function (evt) {
        evt.preventDefault();

        var form = $(this).parents("form.frm-sample-entry");
        var requester = $("select[name=requester]");
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

    $modal_patient_excel.find('input:checkbox[name="is_contacted"]').on("click", function (evt) {     
        //console.log("here")
        if ($(this).is(":checked")) {
            $modal_patient_excel.find("div.contact_wrapper").removeClass("hidden");
        } else {
            $modal_patient_excel.find("div.contact_wrapper").addClass("hidden");
        }
    });
    $("#btnAddPatientsDev").on("click", function (evt) {
        var $modal_read_patient_from_excel = $("#modal-read-patient-from-excel_dev"); // added 19-03-2021
        $modal_read_patient_from_excel.modal("show");
    });
    var today = new Date();
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
    var yyyy = today.getFullYear();

    today = yyyy+'-'+mm+'-'+dd;
    
    //console.log(COUNTRIES);
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
    
    //console.log("Countries");
    //console.log(COUNTRIES);
    //console.log("Nationality");
    //console.log(NATIONALITIES);
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
    //console.log(app_language)
    
    // For Localization
    

    var province_code = "";
    var district_code = "";
    var commune_code  = "";

    
    var districtFilter = function(instance, cell, c, r, source) {
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
    
    var communeFilter = function(instance, cell, c, r, source) {
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
    
    var villageFilter = function(instance, cell, c, r, source) {
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
    
    var requesterFilter = function(instance, cell, c, r, source) {
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
    var line_list_table_dev = jspreadsheet(document.getElementById('spreadsheetdev'), {
        minDimensions: [ 24,500],
        defaultColWidth: 100,
        tableOverflow: true,
        tableHeight: "350px",
        columns: [
            { type:'text', title: label_patient_id ,width:140 ,maxlength:20},
            { type:'text', title: label_patient_name+'*' ,maxlength:60},
            { type:'numeric', title: label_patient_age+'*',width:40 , maxlength:3 },
            { type:'dropdown', title: label_sex+'*',width:50, source:["ប្រុស" , "ស្រី"] },
            { type:'text', title: label_patient_phone_number ,maxlength:100 },
            { type: 'autocomplete', title: label_province+'*', width:100, source:province_array },
            { type: 'autocomplete', title: label_district+'*', width:100, source:districts_array , filter: districtFilter},
            { type: 'autocomplete', title: label_commune+'*', width:100, source:communes_array , filter: communeFilter},
            { type: 'autocomplete', title: label_village+'*', width:100, source:villages_array ,filter: villageFilter},
            { type:'text', title: label_residence ,width:80 ,maxlength:100},
            { type: 'autocomplete', title: label_reason_for_testing , width:120, source:reason_for_testing_array },
            { type:'checkbox', title: label_yes, width:100 },
            { type:'text', title: label_contact_with,readOnly:true , maxlength:60 },
            { type:'checkbox', title: label_is_direct_contact },
            { type: 'text', title:label_sample_number, width:120 ,maxlength:11},
            { type: 'dropdown', title: label_sample_source+'*', width:100, source:sample_source_array },
            { type: 'dropdown', title: label_requester+'*', width:120, source:requester_array, filter: requesterFilter },
            { type: 'calendar', title: label_collect_dt+'*', width:170,options: { format:'YYYY-MM-DD' , time:true , readonly:true, validRange: [ '2021-01-01', today ] } },
            { type: 'calendar', title: label_receive_dt+'*', width:170,options: { format:'YYYY-MM-DD' , time:true , readonly:true , validRange: [ '2021-01-01', today ] } },
            { type: 'autocomplete', title: label_payment_type+'*', width:120, source:payment_type_array },
            { type: 'calendar', title:'ថ្ងៃចូលសម្រាកពេទ្យ', width:120,options: { format:'YYYY-MM-DD' , time:true , readonly:true} },
            { type:'text', title: label_clinical_history ,width:80 , maxlength: 60},
            { type:'checkbox', title: label_urgent ,width:50 },

            { type:'text', title: label_completed_by, width:160 , maxlength:50},
            { type:'text', title: label_phone ,maxlength:10 },
            { type:'text', title: label_sample_collector , maxlength:50 },
            { type:'text', title: label_phone , maxlength:10 },
            { type: 'dropdown', title: label_clinical_symtom, width:150, source:clinical_symptom_array , autocomplete:true, multiple:true},
            { type: 'dropdown', title: label_test_name, width:150, source:['SARS-CoV-2 (Method: real time RT-PCR by GeneXpert)', 'SARS-CoV-2 (Applied Biosystems 7500 Fast Real Time PCR)','SARS-CoV-2 Rapid Antigen Test','SARS-CoV-2 (BIOER Gene 9660 Real Time PCR Instruments)']},
            { type: 'dropdown', title: label_result, width:70, source:['Negative', 'Positive'], readOnly: true},
            { type: 'calendar', title: label_test_date, width:150 , readOnly: true, options: { format:'YYYY-MM-DD' , readonly:true , validRange: [ '2021-01-01', today ]}},
            { type: 'dropdown', title: label_performed_by, width:120, source:performer_array, readOnly: true},
            
            { type: 'autocomplete', title: label_country, width:80, source:country_array },        
            { type: 'autocomplete', title: label_nationality , width:80, source:nationalities_array },            
            { type: 'calendar', title: label_date_of_arrival, width:100,options: { format:'YYYY-MM-DD' , readonly:true } },
            { type:'text', title: label_passport_no, width:80 , maxlength:20},
            { type:'text', title: label_flight_number,maxlength:5 },
            { type:'text', title: label_seat_no , width:60, maxlength:5 },
            { type:'checkbox', title: label_yes,width:60},
            { type:'calendar', title: label_test_covid_date , readOnly:true, width:90,options: { format:'YYYY-MM-DD' , readonly:true , validRange: [ '2021-01-01', today ]} },
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
            { type:'dropdown', title: label_number_of_sample , width:120,source:[1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30]},
            { type:'text', title:'performer_by_id'},
        ],
        nestedHeaders:[
            [
                {
                    title: label_patient_info,
                    colspan: '11',
                },
                {
                    title: label_sample_info,
                    colspan: '7',
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
                /*
                {
                    title: label_if_contact,
                    colspan: '3'
                },
                */
                {
                    title: label_sample,
                    colspan: '4'
                },
                {
                    title: label_test_result,
                    colspan: '3'
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
                                //var phone               = patient.phone;
                                //phone                   = phone.trim();
                                var phone               = (patient.phone == undefined || patient.phone == null) ? "" : (patient.phone).trim();
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
                    province_code = line_list_table_dev.getValueFromCoords(col["province_code"],r);                    
                    code = getDistrictCode(value, province_code);
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["district_code"], r]), code);
                }
            }
            // commune column
            if (c == col["commune"]) {
                if(value !== ""){
                    // set null value to village                     
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["village"], r]), "");
                    district_code = line_list_table_dev.getValueFromCoords(col["district_code"],r);
                    code = getCommuneCode(value, district_code);                    
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["commune_code"], r]), code);
                }
            }
            // village column
            if (c == col["village"]) {
                if(value !== ""){
                    commune_code = line_list_table_dev.getValueFromCoords(col["commune_code"],r);
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
                //var c = line_list_table_dev.getCell(is_direct_contact_column);
                //console.log(c);
                if(value){
                    //console.log("true is right");
                   line_list_table_dev.setReadOnly(is_contacted_column,false);
                   line_list_table_dev.setReadOnly(is_direct_contact_column,false);
                }else{
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["is_contacted_with"], r]),"");
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["is_directed_contact"], r]),"");
                   line_list_table_dev.setReadOnly(is_contacted_column,true);
                   line_list_table_dev.setReadOnly(is_direct_contact_column,true);
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
                    sample_source_id = line_list_table_dev.getValueFromCoords(col["sample_source_id"],r);
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
                    line_list_table_dev.setReadOnly(test_result_col,false);
                    line_list_table_dev.setReadOnly(test_result_date_col,false);
                    line_list_table_dev.setReadOnly(perform_by_col,false);
                }else{
                    // reset value and set readonly
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["test_result"], r]), "");
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["test_result_date"], r]), "");
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["perform_by"], r]), "");

                    line_list_table_dev.setReadOnly(test_result_col,true);
                    line_list_table_dev.setReadOnly(test_result_date_col,true);
                    line_list_table_dev.setReadOnly(perform_by_col,true);
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
                   line_list_table_dev.setReadOnly(colummnName,false);
                }else{
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col["test_date"], r]), "");
                    line_list_table_dev.setReadOnly(colummnName,true);
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
    
    line_list_table_dev.hideColumn(col["payment_type"]); // hide payment type
    line_list_table_dev.hideColumn(col["admission_date"]); // hide admision column

    line_list_table_dev.hideColumn(col["is_contacted"]); 
    line_list_table_dev.hideColumn(col["is_contacted_with"]);
    line_list_table_dev.hideColumn(col["is_directed_contact"]);
    
    line_list_table_dev.hideColumn(col["sample_source"]);
    line_list_table_dev.hideColumn(col["requester"]);
    line_list_table_dev.hideColumn(col["collected_date"]);
    line_list_table_dev.hideColumn(col["received_date"]);
    line_list_table_dev.hideColumn(col["completed_by"]);
    line_list_table_dev.hideColumn(col["phone_completed_by"]);
    line_list_table_dev.hideColumn(col["sample_collector"]);
    line_list_table_dev.hideColumn(col["phone_number_sample_collctor"]);
    line_list_table_dev.hideColumn(col["test_name"]);

    line_list_table_dev.hideColumn(col["sex_id"]);
    line_list_table_dev.hideColumn(col["test_id"]);
    line_list_table_dev.hideColumn(col["country_id"]);
    line_list_table_dev.hideColumn(col["nationality_id"]);
    line_list_table_dev.hideColumn(col["performer_by_id"]);
    line_list_table_dev.hideColumn(col["province_code"]);
    line_list_table_dev.hideColumn(col["district_code"]);
    line_list_table_dev.hideColumn(col["commune_code"]);
    line_list_table_dev.hideColumn(col["village_code"]);
    line_list_table_dev.hideColumn(col["reason_for_testing_id"]);
    line_list_table_dev.hideColumn(col["sample_source_id"]);
    line_list_table_dev.hideColumn(col["requester_id"]);
    line_list_table_dev.hideColumn(col["clinical_symtop_id"]);

    $("#btnSaveListShorForm").on("click", function (evt) {
        $(this).addClass('disabled'); //prevent multiple click
        $modal_patient_excel.find('input:checkbox[name="is_contacted"]').val();
        var sample_source_id    = $modal_patient_excel.find('select[name=sample_source]').val();
        var sample_source       = $modal_patient_excel.find('select[name=sample_source] option:selected').text();
        var requester_id        = $modal_patient_excel.find('select[name=requester]').val();
        var requester           = $modal_patient_excel.find('select[name=requester] option:selected').text();
        var collected_date	    = $modal_patient_excel.find("input[name=collected_date]").data('DateTimePicker').date();
        var received_date	    = $modal_patient_excel.find("input[name=received_date]").data('DateTimePicker').date();
        var collected_time	    = moment($modal_patient_excel.find("input[name=collected_time]").val().trim(), 'HH:mm');
        var received_time	    = moment($modal_patient_excel.find("input[name=received_time]").val().trim(), 'HH:mm');
        //var reason_for_testing  = $modal_patient_excel.find('select[name=for_research]').val();
        var test_id             = $modal_patient_excel.find('select[name=test_name]').val();
        var test_name           = $modal_patient_excel.find('select[name=test_name] option:selected').text();
        
        var completed_by        = $modal_patient_excel.find('input[name=completed_by]').val();
        var phone_number        = $modal_patient_excel.find('input[name=phone_number]').val();
        var sample_collector    = $modal_patient_excel.find('input[name=sample_collector]').val();
        var phone_number_sample_collector    = $modal_patient_excel.find('input[name=phone_number_sample_collector]').val();
        
        var is_contacted        = $modal_patient_excel.find("#is_contacted").is(":checked") ? true : false;
        var contact_with        = $modal_patient_excel.find("input[name=contact_with]").val();
        var is_direct_contact   = $modal_patient_excel.find("input[name=is_direct_contact]:checked").val();

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

        //console.log(line_list_table_dev.getData());
        var line_list_data = line_list_table_dev.getData();
        // delete blank row before send to server
        var data            = [];
        var require_string  = "";
        var valid_check     = 0;
        var array_check     = [];
        
        for(var i in line_list_data){
            var name = line_list_data[i][col["patient_name"]];
            var name = name.trim();
            if(name !== "" || name.length > 0){

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
                //var test_name               = line_list_data[i][col["test_name"]];
                
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
                var residence      =  line_list_data[i][col["residence"]];
                // convert to string in order to trim();
                if(residence !== ""){
                    residence = residence.toString(); // void value in integer
                    if(residence.lenth > 100){
                        extraErrString += " - "+label_residence+': <span class="text-danger">'+msg["not_greater_than"]+' 100 '+msg["char"]+'</span><br />';
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
                    
                   
                    passport_number                                 = line_list_data[i][col["passport_number"]];
                    passport_number                                 = passport_number.toString();
                    line_list_data[i][col["passport_number"]]       = passport_number.trim();

                    flight_number                                   = line_list_data[i][col["flight_number"]];
                    flight_number                                   = flight_number.toString();
                    line_list_data[i][col["flight_number"]]         = flight_number.trim();

                    seat_number                                     = line_list_data[i][col["seat_number"]];
                    seat_number                                     = seat_number.toString();
                    line_list_data[i][col["seat_number"]]           = seat_number.trim();
                    

                    line_list_data[i][col["is_contacted"]]          = is_contacted;
                    line_list_data[i][col["is_contacted_with"]]     = contact_with.trim();
                    line_list_data[i][col["is_directed_contact"]]   = is_direct_contact;

                    line_list_data[i][col["collected_date"]]        = collected_date.format('YYYY-MM-DD')+' '+collected_time.format('HH:mm:ss'); // collected date
                    line_list_data[i][col["received_date"]]         = received_date.format('YYYY-MM-DD')+' '+received_time.format('HH:mm:ss'); // recieved date

                    line_list_data[i][col["completed_by"]]          = completed_by.trim(); // completed by
                    line_list_data[i][col["phone_completed_by"]]    = phone_number.trim(); // phone number of completed by
                    line_list_data[i][col["sample_collector"]]      = sample_collector.trim(); // sample collector
                    line_list_data[i][col["phone_number_sample_collctor"]] = phone_number_sample_collector.trim(); // phone number of sample collector

                    //line_list_data[i][42] = reason_for_testing; // reason_for_testing
                    line_list_data[i][col["sample_source_id"]]      = sample_source_id; // sample_source_id
                    line_list_data[i][col["requester_id"]]          = requester_id; // requester_id
                    line_list_data[i][col["test_name"]]             = test_name; // Test name
                    line_list_data[i][col["test_id"]]               = test_id; // save test id
                    
                    // trim 
                    line_list_data[i][col["patient_name"]]          = name.trim();                   

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
            $("table[name=tblErrorLineList] tbody").html(require_string);
            $modal_error_line_list_1.modal('show');
            $(this).removeClass('disabled'); //prevent multiple click
            return false;
        }
        if(data.length > 500){
            myDialog.showProgress('hide');
            myDialog.showDialog('show', {text: msg["not_greater_than_100_row"], style: 'warning'});
            $(this).removeClass('disabled'); //prevent multiple click
            return false;
        }
       // console.log(data);

    
        $.ajax({
            url: base_url + "/patient/add_line_list_short_form",
            type: "POST",
            data: { data: data},
            dataType: 'json',
            success: function (resText) {
            //    console.log(resText);
                var patients = resText.patients;
                var bodyResult = '';    
                var psample_ids = '';
                for(var i in patients) {
                    var btnPrint = "";
                    var n = 1;
                    var test_result_msg = "";
                    
                    if(patients[i].psample_id !== undefined){
                        btnPrint = '<button type="button" class="btnPrintCovidForm" data-psample_id="'+patients[i].psample_id+'">'+label_print+'</button>'
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
                    bodyResult += '<td>'+btnPrint+'&nbsp;</td>';
                    bodyResult += '</tr>';
                    n++;
                }
                setTimeout(function(){
                    myDialog.showProgress('hide');
                    $("table[name=tblResultLineListDev] tbody").html(bodyResult);
                    var res = psample_ids.substring(0, psample_ids.length - 1); // remove the last n
                    $("#printAll").attr("data-psample_id",res);
                    $("#modal_result_line_list_dev").modal("show");
                }, 1000);
            },
            error: function(xhr, status, error) {
            var err = eval("(" + xhr.responseText + ")");
                console.log(err.Message);
                console.log(xhr.responseText);
            }
        });
    });

    
    $(document).on('click','button.btnPrintCovidForm', function(evt) {
        evt.preventDefault();
     
        var patient_sample_id = $(this).attr("data-psample_id");
        //console.log(patient_sample_id);
        
        $print_preview_covid_form_modal.find(".modal-dialog").empty();
        $print_preview_covid_form_modal.data("patient_sample_id", patient_sample_id);
        $print_preview_covid_form_modal.find("#doPrinting").off("click").on("click", function (evt) {
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
               // console.log(resText);
                for (var i in resText) {
                    var $page = $("<div class='psample-result'></div>");
                    $page.attr("id", "presult-" + (parseInt(i) + 1));
                    $page.data("patient_sample_id", resText[i].patient_sample_id);
                    $page.html(resText[i].template);

                    //if (i == 0) $page.addClass("active");
                    //else $page.hide();

                    $print_preview_covid_form_modal.find(".modal-dialog").append($page);
                }
                
                $print_preview_covid_form_modal.find(".page-count").text(resText.length);
                $print_preview_covid_form_modal.find(".page-number").val(1);
                $print_preview_covid_form_modal.find(".page-number").autoNumeric({aPad: 0, vMin: 1, vMax: resText.length});

                setTimeout(function () {
                    myDialog.showProgress('hide');
                    $print_preview_covid_form_modal.modal("show");
                }, 400);
            },
            error: function () {
                myDialog.showProgress('hide');
                $print_preview_covid_form_modal.modal("show");
                $print_preview_covid_form_modal.find(".modal-dialog").empty();
            }
        });
    })
    // added 08-04-2021
    $("#btnOpenModalUpload").on("click", function (evt) {        
        $modal_upload_excel.modal("show");
    });
    $('input[type=file]').change(function () {
        var $msg = $("#theExcelFile_message");
        
        var val = $(this).val().toLowerCase(),
            regex = new RegExp("(.*?)\.(xlsx|xls)$");

        if (!(regex.test(val))) {
            $msg.html("Please select correct file format");
            $btnSubmitPatientForm.attr("disabled", true);
        }else{
            $msg.html("");
            $btnSubmitPatientForm.removeAttr("disabled");
        }   
    });
    
    $('#theUploadForm').submit(function(e){  
        e.preventDefault();
        $btnSubmitPatientForm.html('Please wait ...').attr('disabled', 'disabled'); // prevent multiple click
        myDialog.showProgress('show');
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
                    console.log(resText);
                    console.log(resText.status);
                    console.log(resText.msg);
                    
                    if(resText.status){
                        //console.log("true");
                        var filename = resText.filename;
                        var data    = resText.data;
                        var content = [];
                        var rNum = 0;
                        //console.log(data);
                        for(var i in data){
                            
                            // if patient code or name empty, we ignore it
                            if(data[i]["A"] !== null || data[i]["B"] !== null){
                                aCol = data[i]["A"];
                                bCol = data[i]["B"];
                                //console.log(aCol);
                                //console.log(bCol);
                                if(data[i]["A"] == null){
                                    var nameColumn = jspreadsheet.getColumnNameFromId([1, rNum]);
                                    line_list_table_dev.setValue(nameColumn, data[i]["B"]);
                                    var nameColumn = jspreadsheet.getColumnNameFromId([2, rNum]);
                                    line_list_table_dev.setValue(nameColumn, data[i]["C"]);
                                    var nameColumn = jspreadsheet.getColumnNameFromId([3, rNum]);
                                    line_list_table_dev.setValue(nameColumn, data[i]["D"]);
                                    var nameColumn = jspreadsheet.getColumnNameFromId([4, rNum]);
                                    line_list_table_dev.setValue(nameColumn, data[i]["E"]);
                                    var nameColumn = jspreadsheet.getColumnNameFromId([5, rNum]);
                                    line_list_table_dev.setValue(nameColumn, data[i]["F"]);
                                    var nameColumn = jspreadsheet.getColumnNameFromId([6, rNum]);
                                    line_list_table_dev.setValue(nameColumn, data[i]["G"]);
                                    var nameColumn = jspreadsheet.getColumnNameFromId([7, rNum]);
                                    line_list_table_dev.setValue(nameColumn, data[i]["H"]);
                                    var nameColumn = jspreadsheet.getColumnNameFromId([8, rNum]);
                                    line_list_table_dev.setValue(nameColumn, data[i]["I"]);
                                    var nameColumn = jspreadsheet.getColumnNameFromId([9, rNum]);
                                    line_list_table_dev.setValue(nameColumn, data[i]["J"]);
                                }else{
                                    var nameColumn = jspreadsheet.getColumnNameFromId([0, rNum]);
                                    line_list_table_dev.setValue(nameColumn, data[i]["A"]);
                                    var nameColumn = jspreadsheet.getColumnNameFromId([1, rNum]);
                                    line_list_table_dev.setValue(nameColumn, data[i]["B"]);
                                    var nameColumn = jspreadsheet.getColumnNameFromId([2, rNum]);
                                    line_list_table_dev.setValue(nameColumn, data[i]["C"]);
                                    var nameColumn = jspreadsheet.getColumnNameFromId([3, rNum]);
                                    line_list_table_dev.setValue(nameColumn, data[i]["D"]);
                                    var nameColumn = jspreadsheet.getColumnNameFromId([4, rNum]);
                                    line_list_table_dev.setValue(nameColumn, data[i]["E"]);
                                    var nameColumn = jspreadsheet.getColumnNameFromId([5, rNum]);
                                    line_list_table_dev.setValue(nameColumn, data[i]["F"]);
                                    var nameColumn = jspreadsheet.getColumnNameFromId([6, rNum]);
                                    line_list_table_dev.setValue(nameColumn, data[i]["G"]);
                                    var nameColumn = jspreadsheet.getColumnNameFromId([7, rNum]);
                                    line_list_table_dev.setValue(nameColumn, data[i]["H"]);
                                    var nameColumn = jspreadsheet.getColumnNameFromId([8, rNum]);
                                    line_list_table_dev.setValue(nameColumn, data[i]["I"]);
                                    var nameColumn = jspreadsheet.getColumnNameFromId([9, rNum]);
                                    line_list_table_dev.setValue(nameColumn, data[i]["J"]);
                                }
                                
                                var nameColumn = jspreadsheet.getColumnNameFromId([10, rNum]);
                                line_list_table_dev.setValue(nameColumn, data[i]["K"]);
                                var nameColumn = jspreadsheet.getColumnNameFromId([14, rNum]);
                                line_list_table_dev.setValue(nameColumn, data[i]["L"]);
                                var nameColumn = jspreadsheet.getColumnNameFromId([21, rNum]);
                                line_list_table_dev.setValue(nameColumn, data[i]["M"]);
                                var nameColumn = jspreadsheet.getColumnNameFromId([22, rNum]);
                                line_list_table_dev.setValue(nameColumn, data[i]["N"]);
                                var nameColumn = jspreadsheet.getColumnNameFromId([29, rNum]);
                                line_list_table_dev.setValue(nameColumn, data[i]["O"]);
                                var nameColumn = jspreadsheet.getColumnNameFromId([30, rNum]);
                                line_list_table_dev.setValue(nameColumn, data[i]["P"]);
                                var nameColumn = jspreadsheet.getColumnNameFromId([31, rNum]);
                                line_list_table_dev.setValue(nameColumn, data[i]["Q"]);
                                var nameColumn = jspreadsheet.getColumnNameFromId([32, rNum]);
                                line_list_table_dev.setValue(nameColumn, data[i]["R"]);
                                var nameColumn = jspreadsheet.getColumnNameFromId([33, rNum]);
                                line_list_table_dev.setValue(nameColumn, data[i]["S"]);
                                var nameColumn = jspreadsheet.getColumnNameFromId([34, rNum]);
                                line_list_table_dev.setValue(nameColumn, data[i]["T"]);
                                var nameColumn = jspreadsheet.getColumnNameFromId([35, rNum]);
                                line_list_table_dev.setValue(nameColumn, data[i]["U"]);
                                var nameColumn = jspreadsheet.getColumnNameFromId([36, rNum]);
                                line_list_table_dev.setValue(nameColumn, data[i]["V"]);
                                rNum++;
                            }
                        }
                        myDialog.showProgress('hide'); 
                        $modal_upload_excel.modal("hide");
                        $btnSubmitPatientForm.removeAttr("disabled");
                        setTimeout(() => {
                            if(rNum == 0){
                                myDialog.showDialog('show', {text: "ពុំមានទិន្នន័យដែលត្រូវបញ្ជូលនោះទេ សូមត្រួតពិនិត្យម្តងទៀត", style: 'warning'});
                            }else{
                                myDialog.showDialog('show', {text: "ទិន្នន័យចំនួន "+rNum+"ជួរ ត្រូវបានបញ្ជូលរួចរាល់"});
                            }
                        }, 300);
                    }
                }
            });
    //    }, 300);
                
    });
    
    
    $modal_patient_excel.find('select[name=test_name]').on("change",function(){
        //myDialog.showProgress('show');
        var text = $( "select[name=test_name] option:selected" ).text();
        var val = $(this).val();
        //console.log(text);
        
        if(val == '-1'){
            // reset value and set readonly            
            for(var r = 0 ; r < 500 ; r++){
                
               // line_list_table_dev.setColumnData(col["test_name"], "");

                line_list_table_dev.setReadOnly(jspreadsheet.getColumnNameFromId([col["test_result"], r]),true);
                line_list_table_dev.setReadOnly(jspreadsheet.getColumnNameFromId([col["test_result_date"], r]), true);
                line_list_table_dev.setReadOnly(jspreadsheet.getColumnNameFromId([col["perform_by"], r]),true);
            }
        }else{
            for(var r = 0 ; r < 100 ; r++){
                //set 
            //    line_list_table_dev.setColumnData(col["test_name"], text);                
                line_list_table_dev.setReadOnly(jspreadsheet.getColumnNameFromId([col["test_result"], r]),false);
                line_list_table_dev.setReadOnly(jspreadsheet.getColumnNameFromId([col["test_result_date"], r]), false);
                line_list_table_dev.setReadOnly(jspreadsheet.getColumnNameFromId([col["perform_by"], r]),false);
            }
        }
     //   myDialog.showProgress('hide');
    })
    
 })
 
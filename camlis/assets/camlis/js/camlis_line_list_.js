$(function () {
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
        console.log("Result "+result);
        console.log("sample_test_id "+sample_test_id);
        $.each(test_result, function(key, row) {
            console.log(row);
            if(row.organism_name == result && row.sample_test_id == sample_test_id){
                test_organism_id = row.test_organism_id;
                return false;
            }
        });
        return test_organism_id;
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
        test_result_id                  : 56
    }

    var maxRow                      = 500; 
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
    var machine_name_array          = [];
    var test_result_array           = [];

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
    getMachineName(497);
    getMachineName(479);
    getMachineName(505);   

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
        gender_array                = ["Male" , "Female"];
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
    $("#btnAddPatients").on("click", function (evt) {
        var $modal_read_patient_from_excel = $("#modal-read-patient-from-excel"); // added 19-03-2021
        $modal_read_patient_from_excel.modal("show");
    });
    var line_list_table_test = jspreadsheet(document.getElementById('spreadsheet1'), {
        minDimensions: [ 24, 100 ],
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

            { type: 'dropdown', title: label_test_name, width:150, source:['SARS-CoV-2 (Method: real time RT-PCR by GeneXpert)', 'SARS-CoV-2 (Applied Biosystems 7500 Fast Real Time PCR)','SARS-CoV-2 Rapid Antigen Test']},
            { type: 'dropdown', title: label_machine_name, width:150, source: machine_name_array, filter:machineNameFilter, readOnly: true},
            
            { type: 'dropdown', title: label_result, width:70,source:test_result_array, filter: testResultFilter, readOnly: true},
            { type: 'calendar', title: label_test_date, width:150 , readOnly: true, options: { format:'YYYY-MM-DD' , readonly:true , validRange: [ '2021-01-01', today ]}},
            { type: 'dropdown', title: label_performed_by, width:120, source:performer_array, readOnly: true},
            
           /* { type: 'autocomplete', title: label_country, width:80, source:country_array }, */
            { type: 'text', title: label_country, width:80, maxlength: 150 },
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
            { type:'text', title:'test_result_id'},
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
                                var is_contacted        = (patient.is_contacted == null || patient.is_contacted == undefined) ? false : true;
                                var contact_with        = (patient.contact_with == null || patient.contact_with == undefined) ? "" : patient.contact_with;
                                var is_direct_contact   = (patient.is_direct_contact == null || patient.is_direct_contact == undefined) ? false : true;
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
                                //var phone               = patient.phone;

                                //var name            = "name";
                                //var patient_code    = "patient code";
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
                    province_code = line_list_table_test.getValueFromCoords(col_name["province_code"],r);
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
                    district_code = line_list_table_test.getValueFromCoords(col_name["district_code"],r);
                    code = getCommuneCode_(value, district_code);                    
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["commune_code"], r]), code);
                }
            }
            // village column
            if (c == col_name["village"]) {
                //console.log("Village "+value);
                if(value !== ""){
                    commune_code = line_list_table_test.getValueFromCoords(col_name["commune_code"],r);
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
                   line_list_table_test.setReadOnly(is_contacted_column,false);
                   line_list_table_test.setReadOnly(is_direct_contact_column,false);
                }else{
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["is_contacted_with"], r]),"");
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["is_directed_contact"], r]),"");
                   line_list_table_test.setReadOnly(is_contacted_column,true);
                   line_list_table_test.setReadOnly(is_direct_contact_column,true);
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
                    sample_source_id = line_list_table_test.getValueFromCoords(col_name["sample_source_id"],r);
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
                    line_list_table_test.setReadOnly(test_result_col,false);
                    line_list_table_test.setReadOnly(test_result_date_col,false);
                    line_list_table_test.setReadOnly(perform_by_col,false);
                    line_list_table_test.setReadOnly(machine_name_col,false);
                }else{
                    // reset value and set readonly
                    instance.jexcel.setValue(test_result_col, "");
                    instance.jexcel.setValue(test_result_date_col, "");
                    instance.jexcel.setValue(perform_by_col, "");
                    instance.jexcel.setValue(machine_name_col, "");

                    line_list_table_test.setReadOnly(test_result_col,true);
                    line_list_table_test.setReadOnly(test_result_date_col,true);
                    line_list_table_test.setReadOnly(perform_by_col,true);
                    line_list_table_test.setReadOnly(machine_name_col,true);
                }
            }
            
            if(c == col_name["test_result"]){
                if(value !== ""){
                    //console.log("Test result "+ value);
                    var sample_test_id = line_list_table_test.getValueFromCoords(col_name["test_id"],r);
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
                   line_list_table_test.setReadOnly(colummnName,false);
                }else{
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["test_date"], r]), "");
                    line_list_table_test.setReadOnly(colummnName,true);
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
            ){
            var config = el.jexcel.options.columns[x].maxlength;
                cell.children[0].setAttribute('maxlength' , config); // set maxlength to column
            }
        }
    });
    
    line_list_table_test.hideColumn(col_name["payment_type"]); // hide payment type
    line_list_table_test.hideColumn(col_name["admission_date"]); // hide admision column
    line_list_table_test.hideColumn(col_name["is_urgent"]);
    line_list_table_test.hideColumn(col_name["sex_id"]);
    line_list_table_test.hideColumn(col_name["test_id"]);
    line_list_table_test.hideColumn(col_name["country_id"]);
    line_list_table_test.hideColumn(col_name["nationality_id"]);
    line_list_table_test.hideColumn(col_name["performer_by_id"]);
    line_list_table_test.hideColumn(col_name["province_code"]);
    line_list_table_test.hideColumn(col_name["district_code"]);
    line_list_table_test.hideColumn(col_name["commune_code"]);
    line_list_table_test.hideColumn(col_name["village_code"]);
    line_list_table_test.hideColumn(col_name["reason_for_testing_id"]);
    line_list_table_test.hideColumn(col_name["sample_source_id"]);
    line_list_table_test.hideColumn(col_name["requester_id"]);
    line_list_table_test.hideColumn(col_name["clinical_symtop_id"]);
    line_list_table_test.hideColumn(col_name["test_result_id"]);
    
    var $modal_list_error = $("#modal_error_line_list_new");

    $("#btnSaveList").on("click", function (evt) {
        $(this).addClass('disabled btn-progress'); //prevent multiple click
        myDialog.showProgress('show', { text : msg_loading });

        var line_list_data  = line_list_table_test.getData();
        var data            = [];
        var require_string  = "";
        var valid_check     = 0;
        var array_check     = [];
        var currentDate     = new Date();
       
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
                    //clear spreadsheet
                    // it does not provice option to clear it, so i just clear it manually
                    line_list_table_test.setData([]);
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
    
    // Excel Short Form    
    var $modal_excel_short_form     = $("#modal_excel_short_form"); // added 19-03-2021
    $("#btnExcelShortForm").on("click", function (evt) {
        $modal_excel_short_form.modal("show");
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

            { type: 'dropdown', title: label_test_name, width:150, source:['SARS-CoV-2 (Method: real time RT-PCR by GeneXpert)', 'SARS-CoV-2 (Applied Biosystems 7500 Fast Real Time PCR)','SARS-CoV-2 Rapid Antigen Test']},
            { type: 'dropdown', title: label_machine_name, width:150, source: machine_name_array, filter:machineNameFilterShortForm, readOnly: true},
            
            { type: 'dropdown', title: label_result, width:70,source:test_result_array, filter: testResultFilterShortForm, readOnly: true},
            { type: 'calendar', title: label_test_date, width:150 , readOnly: true, options: { format:'YYYY-MM-DD' , readonly:true , validRange: [ '2021-01-01', today ]}},
            { type: 'dropdown', title: label_performed_by, width:120, source:performer_array, readOnly: true},
            
           /* { type: 'autocomplete', title: label_country, width:80, source:country_array }, */
            { type: 'text', title: label_country, width:80, maxlength: 150 },
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
            { type:'text', title:'test_result_id'},
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
                }
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
                                console.log(patient);
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
                                var is_contacted        = (patient.is_contacted == null || patient.is_contacted == undefined) ? false : true;
                                var contact_with        = (patient.contact_with == null || patient.contact_with == undefined) ? "" : patient.contact_with;
                                var is_direct_contact   = (patient.is_direct_contact == null || patient.is_direct_contact == undefined) ? false : true;
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
                                //var phone               = patient.phone;

                                //var name            = "name";
                                //var patient_code    = "patient code";
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
                    line_list_data[i][col_name["test_id"]]               = test_id; // save test id
                    
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
            url: base_url + "/patient/add_line_list_full_form_test",
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
                    ("table[name=tblResultLineListNew] tbody").html(bodyResult);
                    var res = psample_ids.substring(0, psample_ids.length - 1); // remove the last n
                    $("#printAll").attr("data-psample_id",res);
                    $("#modal_result_line_list_new").modal("show");
                    line_list_table_new.setData([]);
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
        console.log(text);
        
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
    /*
    * 18-05-2021 
    * Add Result as line list 
    * avoid loading this for other user
    */
   /*
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
    */
})
 
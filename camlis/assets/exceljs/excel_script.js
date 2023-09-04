$(function () {
    $loader = $("#loading");
    var dtPickerOption = {
        widgetPositioning : {
            horizontal	: 'left',
            vertical	: 'bottom'
        },
        showClear		: true,
        format			: 'DD/MM/YYYY',
        useCurrent		: false,
        maxDate			: new Date(),
        locale			: app_language
    };
    var $result_modal = $("#resuldModal");
    $("input.start_date").datetimepicker(dtPickerOption);
    $("input.end_date").datetimepicker(dtPickerOption);

    var col_name = {
        no_by_day               : 0,
        cdc_case_no             : 1,
        laboratory_code         : 2,
        fullname                : 3,
        sex                     : 4,
        age                     : 5,
        nationality             : 6,
        phone                   : 7,
        date_of_sampling        : 8,
        date_of_result          : 9,
        f20_event               : 10,
        imported_country        : 11,
        date_of_onset           : 12,
        symptoms                : 13,
        positive_on             : 14,
        reason_for_testing      : 15,
        province                : 16,
        district                : 17,
        commune                 : 18,
        village                 : 19,
        province_of_detection   : 20,
        remark                  : 21,
        vaccination_status      : 22,
        first_vaccinated_date   : 23,
        second_vaccinated_date  : 24,
        vaccine_name            : 25,
        image                   : 26,
        sex_id                  : 27,
        nationality_id          : 28,
        reason_for_testing_id   : 29,
        province_code           : 30,
        district_code           : 31,
        commune_code            : 32,
        village_code            : 33,
        province_of_detection_code   : 34,
        vaccination_status_code : 35,
        vaccine_id              : 36,
        patient_exist           : 37,
    }

    var letter_col_name = {        
        no_by_day               : "A",
        cdc_case_no             : "B",
        laboratory_code         : "C",
        fullname                : "D",
        sex                     : "E",
        age                     : "F",
        nationality             : "G",
        phone                   : "H",
        date_of_sampling        : "I",
        date_of_result          : "J",
        f20_event               : "K",
        imported_country        : "L",
        date_of_onset           : "M",
        symptoms                : "N",
        positive_on             : "O",
        reason_for_testing      : "P",
        province                : "Q",
        district                : "R",
        commune                 : "S",
        village                 : "T",
        province_of_detection   : "U",
        remark                  : "V",
        vaccination_status      : "W",
        first_vaccinated_date   : "X",
        second_vaccinated_date  : "Y",
        vaccine_name            : "Z",
        image                   : "AA",
        sex_id                  : "AB",
        nationality_id          : "AC",
        reason_for_testing_id   : "AD",
        province_code           : "AE",
        district_code           : "AF",
        commune_code            : "AG",
        village_code            : "AH",
        province_of_detection_code   : "AI",
        vaccination_status_code : "AJ",
        vaccine_id              : "AK",
    }

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
    function getReasonValue_(id){
        var code = "";
        $.each(REASON_FOR_TESTING_ARR, function(key, value) {
            if(key == id){
                code = value;
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
    function getProvinceName_(id){
        var res = "";
        if(app_language == 'kh'){
            $.each(PROVINCES, function(key, row) {
                if(row.code == id){
                    res = row.name_kh;
                    return false;
                }
            });
        }else{
            $.each(PROVINCES, function(key, row) {
                if(row.code == id){
                    res = row.name_en;
                    return false;
                }
            });
        }
       return res;
    }
    var province_array              = [];
    var districts_array             = [];
    var communes_array              = [];
    var villages_array              = [];
    var nationalities_array         = [];
    var country_array               = [];
    var reason_for_testing_array    = [];
    var vaccination_status_array    = [];
    var vaccine_type_array          = [];
    
    var today   = new Date();
    var dd      = String(today.getDate()).padStart(2, '0');
    var mm      = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
    var yyyy    = today.getFullYear();
    today       = yyyy+'-'+mm+'-'+dd;
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
        gender_array    = ["ប្រុស" , "ស្រី"];
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
        gender_array   = ["Male" , "Female"];
    }
    $.each(NATIONALITIES, function(key, value) {
        nationalities_array.push(value.nationality_en);
    });
    $.each(COUNTRIES, function(key, value) {
        country_array.push(value.name_en);
    });
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
    $.each(REASON_FOR_TESTING_ARR, function(key, value) {
        reason_for_testing_array.push(value);
    }); 
    
    var excel_form = jspreadsheet(document.getElementById('spreadsheet'), {        
        minDimensions: [ 27, 500 ],
        defaultColWidth: 100,
        tableOverflow: true,
        tableHeight: "960px",
        columns: [            
            { type:'numeric', title: "No. by Day",width:100, maxlength:20},
            { type:'numeric', title: "CDC Case No",width:120, maxlength:20},
            { type: 'text', title: 'Laboratory_code', width: 170, maxlength:30},
            { type:'text', title: "Full name",width:120, maxlength:100},            
            { type:'dropdown', title: "Sex",width:60, source: gender_array},
            { type:'numeric', title: "Age",width:60 , maxlength:3},
            { type: 'autocomplete', title: "Nationality", width:120, source:nationalities_array },  
            { type:'text', title: "Phone",maxlength:20 , maxlength:120},
            { type:'calendar', title: "Date of Sampling",width:130, options: { format:'YYYY-MM-DD' , readonly:true , validRange: [ '2021-01-01', today ]}},
            { type:'calendar', title: "Date of Result",width:130, options: { format:'YYYY-MM-DD' , readonly:true , validRange: [ '2021-01-01', today ]}},
            { type:'dropdown', title: "F20 Event",width:100, source:['No','Yes']},
            { type:'text', title: "Imported Country",width:130 , maxlength:120},
            { type:'calendar', title: "Date of Onset",width:120, options: { format:'YYYY-MM-DD' , readonly:true , validRange: [ '2021-01-01', today ]}},
            { type:'dropdown', title: "Symptoms (306)",width: 120, source:["Yes","No","Unknown"]},
            { type:'dropdown', title: "Positive on",source:["1-Test","2-Test","3-Test","4-Test","5-Test"]},
            { type:'dropdown', title: "Reason for Testing",width:140, source:reason_for_testing_array},
            { type: 'autocomplete', title: 'Province', width:100, source:province_array },
            { type: 'autocomplete', title: 'Distict', width:100, source:districts_array , filter: districtFilter2},
            { type: 'autocomplete', title: 'Commune', width:100, source:communes_array , filter: communeFilter2},
            { type: 'autocomplete', title: 'Village', width:100, source:villages_array ,filter: villageFilter2},
            { type: 'autocomplete', title: 'Province of detection', width: 150, source:province_array },            
            { type: 'text', title: 'Remark' ,maxlength: 150 },
            { type:'dropdown', title:"Vaccination Status", width: 140, source:vaccination_status_array},
            { type:'calendar', title: "First injection date", width:140,options: { format:'YYYY-MM-DD' , readonly:true , validRange: [ '2021-01-01', today ]} },
            { type:'calendar', title: "Second injection date", width:150,options: { format:'YYYY-MM-DD' , readonly:true , validRange: [ '2021-01-01', today ]} },
            { type:'dropdown', title: "Vaccine name" , source: vaccine_type_array, width:120},
            { type:'image', title:'Image',  width:120},
            { type:'text', title:'sex_id'}, 
            { type:'text', title:'nationality_id'}, 
            { type:'text', title:'reason_for_testing_id'},
            { type:'text', title:'province_code'},
            { type:'text', title:'district_code'}, 
            { type:'text', title:'commune_code'},
            { type:'text', title:'village_code'},
            { type:'text', title:'province_of_detection_code'},            
            { type:'text', title: 'vaccination_status_code'},
            { type:'text', title: 'vaccine_id'},
            { type:'text', title: 'patient_exist'},
        ],
        onchange:function(instance,cell, c, r, value) {
            var rowNumber = parseInt(r) + 1;
            if( c == col_name['laboratory_code'] ){
                if(value !== ""){
                    var patient_code = value;
                    var check_patient = excel_form.getValueFromCoords(col_name["patient_exist"],r);
                    if(check_patient !== 1){
                        $.ajax({
                            url: base_url + 'exceljs/search/' + patient_code,
                            type: 'POST',
                            data: {pid: patient_code},
                            dataType: 'json',
                            success: function (resText) {
                                console.log(resText);
                                var patient = resText.patient;
                                if(patient){
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["patient_exist"], r]), 1);
                                    if(!resText.is_camlis_patient){
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
                                        var no_by_day           = (patient.no_by_day == undefined || patient.no_by_day == null) ? "" : patient.no_by_day;
                                        var case_no             = (patient.case_no == undefined || patient.case_no == null) ? "" : patient.case_no;
                                        var name                = patient.fullname;
                                        var age                 = (patient.age == undefined || patient.age == null) ? "" : patient.age;
                                        var nationality         = (patient.nationality_en == undefined || patient.nationality_en == null) ? "" : patient.nationality_en;
                                        var phone               = (patient.phone == undefined || patient.phone == null) ? "" : (patient.phone).trim();
                                        var date_of_sampling    = (patient.date_of_sampling == undefined || patient.date_of_sampling == null) ? "" : patient.date_of_sampling;
                                        var date_of_result      = (patient.date_of_result == undefined || patient.date_of_result == null) ? "" : patient.date_of_result;
                                        var f20_event           = (patient.f20_event == undefined || patient.f20_event == null) ? "" : patient.f20_event;
                                        var imported_country    = (patient.imported_country == undefined || patient.imported_country == null) ? "" : patient.imported_country;
                                        var date_of_onset       = (patient.date_of_onset == undefined || patient.date_of_onset == null) ? "" : patient.date_of_onset;
                                        var symptoms            = (patient.symptoms == undefined || patient.symptoms == null) ? "" : patient.symptoms;
                                        var reason_for_testing  = (patient.reason_for_testing == undefined || patient.reason_for_testing == null) ? "" : getReasonValue_(patient.reason_for_testing);
                                        var detection_province  = (patient.detection_province == undefined || patient.detection_province == null) ? "" : getProvinceName_(patient.detection_province);
                                        var remark              = (patient.remark == undefined || patient.remark == null) ? "" : patient.remark;
                                        var positive_on         = (patient.positive_on == undefined || patient.positive_on == null) ? "" : patient.positive_on;
                                        var image               = (patient.img_url == undefined || patient.img_url == null) ? "" : patient.img_url;
                                        
                                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["no_by_day"], r]), no_by_day);
                                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["cdc_case_no"], r]), case_no);
                                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["date_of_sampling"], r]), date_of_sampling);
                                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["date_of_result"], r]), date_of_result);
                                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["f20_event"], r]), f20_event);
                                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["imported_country"], r]), imported_country);
                                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["date_of_onset"], r]), date_of_onset);
                                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["symptoms"], r]), symptoms);
                                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["positive_on"], r]), positive_on);
                                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["remark"], r]), remark);
                                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["reason_for_testing"], r]), reason_for_testing);
                                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["province_of_detection"], r]), detection_province);
                                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["image"], r]), image);
                                        
                                        var vaccination_status_code  = (patient.vaccination_status == undefined || patient.vaccination_status == null) ? "" : patient.vaccination_status;
                                        var vaccine_type        = (patient.vaccine == undefined || patient.vaccine == null) ? "" : patient.vaccine;
                                        var first_injection_date     = (patient.first_injection_date == undefined || patient.first_injection_date == null) ? "" : patient.first_injection_date;
                                        var second_injection_date    = (patient.second_injection_date == undefined || patient.second_injection_date == null) ? "" : patient.second_injection_date;
    
                                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["fullname"], r]), name);
                                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["sex"], r]), sex);
                                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["age"], r]), age);
                                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["nationality"], r]), nationality);
                                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["phone"], r]), phone);
                                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["province"], r]), province);
                                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["commune"], r]), commune);
                                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["district"], r]), district);
                                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["village"], r]), village);
    
                                        if(vaccination_status_code !== ""){
                                            var status = getVaccinationStatusString(vaccination_status_code);
                                            instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["vaccination_status"], r]), status);
                                            var vaccine_name = getVaccineName(vaccine_type);
                                            instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["vaccine_name"], r]), vaccine_name);  
                                            instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["first_vaccinated_date"], r]), first_injection_date); 
                                            instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["second_vaccinated_date"], r]), second_injection_date);
                                        }
                                        excel_form.setComments(letter_col_name["laboratory_code"]+rowNumber, "Laboratory code already exist ...!"); // reset comment
                                    }else{
                                        // if camlis_patient
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
                                        //var age                 = calculateAge(dob);
                                        var age                 = 23;
                                        var name                = patient.name;                                
                                        var country             = (patient.country_name_en == undefined || patient.country_name_en == null) ? "" : patient.country_name_en;
                                        if(patient.country_name == undefined || patient.country_name == null){
                                            country = "";
                                        }else{
                                            country = patient.country_name;
                                        }
    
                                        var nationality         = (patient.nationality_en == undefined || patient.nationality_en == null) ? "" : patient.nationality_en;
                                        var phone               = (patient.phone == undefined || patient.phone == null) ? "" : (patient.phone).trim();
                                        var vaccination_status_code  = (patient.vaccination_status == undefined || patient.vaccination_status == null) ? "" : patient.vaccination_status;
                                        var vaccine_type            = (patient.vaccine_id == undefined || patient.vaccine_id == null) ? "" : patient.vaccine_id;
                                        var first_vaccinated_date     = (patient.first_vaccinated_date == undefined || patient.first_vaccinated_date == null) ? "" : patient.first_vaccinated_date;
                                        var second_vaccinated_date    = (patient.second_vaccinated_date == undefined || patient.second_vaccinated_date == null) ? "" : patient.second_vaccinated_date;

                                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["fullname"], r]), name);
                                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["sex"], r]), sex);
                                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["age"], r]), age);
                                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["nationality"], r]), nationality);
                                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["phone"], r]), phone);
                                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["province"], r]), province);
                                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["commune"], r]), commune);
                                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["district"], r]), district);
                                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["village"], r]), village);
                                        if(vaccination_status_code !== ""){
                                            var status = getVaccinationStatusString(vaccination_status_code);
                                            instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["vaccination_status"], r]), status);
                                            var vaccine_name = getVaccineName(vaccine_type);
                                            instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["vaccine_name"], r]), vaccine_name);  
                                            instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["first_vaccinated_date"], r]), first_vaccinated_date); 
                                            instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["second_vaccinated_date"], r]), second_vaccinated_date);
                                        }
                                    }
                                }else{
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["patient_exist"], r]), 0);
                                    excel_form.setComments(letter_col_name["laboratory_code"]+rowNumber, null); // reset comment
                                }
                            }
                        })
                    }
                }else{
                    var check_patient = excel_form.getValueFromCoords(col_name["patient_exist"],r);
                    if(check_patient == 1){
                        // Reset all columns
                        excel_form.setComments(letter_col_name["laboratory_code"]+rowNumber, null); // reset comment
                        excel_form.setComments(letter_col_name["phone"]+rowNumber, null); // reset comment
                        excel_form.setComments(letter_col_name["fullname"]+rowNumber, null); // reset comment
                        $.each(col_name, function(key, value) {
                            console.log(key+" "+value)
                            instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name[key], r]), null);
                        })
                    }
                }
            }
            if( c == col_name['phone']){
                if(value !== ""){
                    var check_patient = excel_form.getValueFromCoords(col_name["patient_exist"],r);
                    if(check_patient !== 1){
                        var phone = value.trim();
                        phone = phone.replace(/\s/g, ''); //remove space
                        $.ajax({
                            url: base_url + 'exceljs/search_by_phone/' + phone,
                            type: 'POST',
                            data: {phone: phone},
                            dataType: 'json',
                            success: function (resText) {
                                console.log(resText);
                                var patient = resText.patient;
                                if(patient){                
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["patient_exist"], r]), 1);                
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
                                    var no_by_day           = (patient.no_by_day == undefined || patient.no_by_day == null) ? "" : patient.no_by_day;
                                    var case_no             = (patient.case_no == undefined || patient.case_no == null) ? "" : patient.case_no;
                                    var laboratory_code     = (patient.patient_code == undefined || patient.patient_code == null) ? "" : patient.patient_code;
                                    var name                = patient.fullname;
                                    var age                 = (patient.age == undefined || patient.age == null) ? "" : patient.age;
                                    var nationality         = (patient.nationality_en == undefined || patient.nationality_en == null) ? "" : patient.nationality_en;
                                    var phone               = (patient.phone == undefined || patient.phone == null) ? "" : (patient.phone).trim();
                                    var date_of_sampling    = (patient.date_of_sampling == undefined || patient.date_of_sampling == null) ? "" : patient.date_of_sampling;
                                    var date_of_result      = (patient.date_of_result == undefined || patient.date_of_result == null) ? "" : patient.date_of_result;
                                    var f20_event           = (patient.f20_event == undefined || patient.f20_event == null) ? "" : patient.f20_event;
                                    var imported_country    = (patient.imported_country == undefined || patient.imported_country == null) ? "" : patient.imported_country;
                                    var date_of_onset       = (patient.date_of_onset == undefined || patient.date_of_onset == null) ? "" : patient.date_of_onset;
                                    var symptoms            = (patient.symptoms == undefined || patient.symptoms == null) ? "" : patient.symptoms;
                                    var reason_for_testing  = (patient.reason_for_testing == undefined || patient.reason_for_testing == null) ? "" : getReasonValue_(patient.reason_for_testing);
                                    var detection_province  = (patient.detection_province == undefined || patient.detection_province == null) ? "" : getProvinceName_(patient.detection_province);
                                    var remark              = (patient.remark == undefined || patient.remark == null) ? "" : patient.remark;
                                    var positive_on         = (patient.positive_on == undefined || patient.positive_on == null) ? "" : patient.positive_on;                                                                      
                                    var image               = (patient.img_url == undefined || patient.img_url == null) ? "" : patient.img_url;

                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["no_by_day"], r]), no_by_day);
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["cdc_case_no"], r]), case_no);
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["laboratory_code"], r]), laboratory_code);
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["date_of_sampling"], r]), date_of_sampling);
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["date_of_result"], r]), date_of_result);
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["f20_event"], r]), f20_event);
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["imported_country"], r]), imported_country);
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["date_of_onset"], r]), date_of_onset);
                                    
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["symptoms"], r]), symptoms);
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["positive_on"], r]), positive_on);
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["remark"], r]), remark);
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["reason_for_testing"], r]), reason_for_testing);
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["province_of_detection"], r]), detection_province);
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["image"], r]), image);
                                    
                                    var vaccination_status_code  = (patient.vaccination_status == undefined || patient.vaccination_status == null) ? "" : patient.vaccination_status;
                                    var vaccine_type        = (patient.vaccine == undefined || patient.vaccine == null) ? "" : patient.vaccine;
                                    var first_injection_date     = (patient.first_injection_date == undefined || patient.first_injection_date == null) ? "" : patient.first_injection_date;
                                    var second_injection_date    = (patient.second_injection_date == undefined || patient.second_injection_date == null) ? "" : patient.second_injection_date;
    
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["fullname"], r]), name);
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["sex"], r]), sex);
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["age"], r]), age);
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["nationality"], r]), nationality);
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["phone"], r]), phone);
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["province"], r]), province);
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["commune"], r]), commune);
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["district"], r]), district);
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["village"], r]), village);
    
                                    if(vaccination_status_code !== ""){
                                        var status = getVaccinationStatusString(vaccination_status_code);
                                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["vaccination_status"], r]), status);
                                        var vaccine_name = getVaccineName(vaccine_type);
                                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["vaccine_name"], r]), vaccine_name);  
                                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["first_vaccinated_date"], r]), first_injection_date); 
                                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["second_vaccinated_date"], r]), second_injection_date);
                                    }
                                    excel_form.setComments(letter_col_name["phone"]+rowNumber, "Phone already exists...!"); // reset comment
                                }else{
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["patient_exist"], r]), 0);
                                    excel_form.setComments(letter_col_name["phone"]+rowNumber, ""); // reset comment
                                }
                            }
                        })
                    }
                }else{
                    var check_patient = excel_form.getValueFromCoords(col_name["patient_exist"],r);
                    if(check_patient == 1){
                        // Reset all columns
                        excel_form.setComments(letter_col_name["fullname"]+rowNumber, null); // reset comment
                        excel_form.setComments(letter_col_name["laboratory_code"]+rowNumber, null); // reset comment
                        excel_form.setComments(letter_col_name["phone"]+rowNumber, null); // reset comment
                        $.each(col_name, function(key, value) {
                            console.log(key+" "+value)
                            instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name[key], r]), null);
                        })
                    }
                }
            }
            if( c == col_name['fullname']){
                if(value !== ""){
                    var check_patient = excel_form.getValueFromCoords(col_name["patient_exist"],r);
                    if(check_patient !== 1){
                        var name = value.trim();
                        $.ajax({
                            url: base_url + 'exceljs/search_by_name/' + name,
                            type: 'POST',
                            data: {name: name},
                            dataType: 'json',
                            success: function (resText) {
                                console.log(resText);
                                var patient = resText.patient;
                                if(patient){                                    
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["patient_exist"], r]), 1);                
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
                                    var no_by_day           = (patient.no_by_day == undefined || patient.no_by_day == null) ? "" : patient.no_by_day;
                                    var case_no             = (patient.case_no == undefined || patient.case_no == null) ? "" : patient.case_no;
                                    var laboratory_code     = (patient.patient_code == undefined || patient.patient_code == null) ? "" : patient.patient_code;
                                    var name                = patient.fullname;
                                    var age                 = (patient.age == undefined || patient.age == null) ? "" : patient.age;
                                    var nationality         = (patient.nationality_en == undefined || patient.nationality_en == null) ? "" : patient.nationality_en;
                                    var phone               = (patient.phone == undefined || patient.phone == null) ? "" : (patient.phone).trim();
                                    var date_of_sampling    = (patient.date_of_sampling == undefined || patient.date_of_sampling == null) ? "" : patient.date_of_sampling;
                                    var date_of_result      = (patient.date_of_result == undefined || patient.date_of_result == null) ? "" : patient.date_of_result;
                                    var f20_event           = (patient.f20_event == undefined || patient.f20_event == null) ? "" : patient.f20_event;
                                    var imported_country    = (patient.imported_country == undefined || patient.imported_country == null) ? "" : patient.imported_country;
                                    var date_of_onset       = (patient.date_of_onset == undefined || patient.date_of_onset == null) ? "" : patient.date_of_onset;
                                    var symptoms            = (patient.symptoms == undefined || patient.symptoms == null) ? "" : patient.symptoms;
                                    var reason_for_testing  = (patient.reason_for_testing == undefined || patient.reason_for_testing == null) ? "" : getReasonValue_(patient.reason_for_testing);
                                    var detection_province  = (patient.detection_province == undefined || patient.detection_province == null) ? "" : getProvinceName_(patient.detection_province);
                                    var remark              = (patient.remark == undefined || patient.remark == null) ? "" : patient.remark;
                                    var positive_on         = (patient.positive_on == undefined || patient.positive_on == null) ? "" : patient.positive_on;                                                                      
                                    var image               = (patient.img_url == undefined || patient.img_url == null) ? "" : patient.img_url;

                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["no_by_day"], r]), no_by_day);
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["cdc_case_no"], r]), case_no);
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["laboratory_code"], r]), laboratory_code);
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["date_of_sampling"], r]), date_of_sampling);
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["date_of_result"], r]), date_of_result);
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["f20_event"], r]), f20_event);
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["imported_country"], r]), imported_country);
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["date_of_onset"], r]), date_of_onset);
                                    
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["symptoms"], r]), symptoms);
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["positive_on"], r]), positive_on);
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["remark"], r]), remark);
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["reason_for_testing"], r]), reason_for_testing);
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["province_of_detection"], r]), detection_province);
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["image"], r]), image);
                                    
                                    var vaccination_status_code  = (patient.vaccination_status == undefined || patient.vaccination_status == null) ? "" : patient.vaccination_status;
                                    var vaccine_type        = (patient.vaccine == undefined || patient.vaccine == null) ? "" : patient.vaccine;
                                    var first_injection_date     = (patient.first_injection_date == undefined || patient.first_injection_date == null) ? "" : patient.first_injection_date;
                                    var second_injection_date    = (patient.second_injection_date == undefined || patient.second_injection_date == null) ? "" : patient.second_injection_date;
    
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["fullname"], r]), name);
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["sex"], r]), sex);
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["age"], r]), age);
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["nationality"], r]), nationality);
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["phone"], r]), phone);
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["province"], r]), province);
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["commune"], r]), commune);
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["district"], r]), district);
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["village"], r]), village);
    
                                    if(vaccination_status_code !== ""){
                                        var status = getVaccinationStatusString(vaccination_status_code);
                                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["vaccination_status"], r]), status);
                                        var vaccine_name = getVaccineName(vaccine_type);
                                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["vaccine_name"], r]), vaccine_name);  
                                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["first_vaccinated_date"], r]), first_injection_date); 
                                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["second_vaccinated_date"], r]), second_injection_date);
                                    }
                                    excel_form.setComments(letter_col_name["fullname"]+rowNumber, "Patient name already exist"); // reset comment
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["patient_exist"], r]), 1);
                                }else{
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["patient_exist"], r]), 0);
                                    excel_form.setComments(letter_col_name["fullname"]+rowNumber, ""); // reset comment
                                }
                            }
                        })
                    }
                }else{
                    var check_patient = excel_form.getValueFromCoords(col_name["patient_exist"],r);
                    if(check_patient == 1){
                        // Reset all columns
                        excel_form.setComments(letter_col_name["fullname"]+rowNumber, null); // reset comment
                        excel_form.setComments(letter_col_name["laboratory_code"]+rowNumber, null); // reset comment
                        excel_form.setComments(letter_col_name["phone"]+rowNumber, null); // reset comment
                        $.each(col_name, function(key, value) {
                            instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name[key], r]), null);
                        })
                    }
                }
            }
            if(c == col_name["sex"]){                
                if(value !== ""){                    
                    if(app_language == 'kh'){
                        sex = value == 'ប្រុស' ? 1 : 2;
                    }else{
                        sex = value == 'Male' ? 1 : 2;
                    }
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["sex_id"], r]),sex);
                }
            }
            if (c == col_name["province"]) {                
                if(value !== ""){
                    // set null value to district col
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["district"], r]), "");
                    code = getProvinceCode_(value);                    
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["province_code"], r]),code); // save province code in column 38
                    excel_form.setComments(letter_col_name["province"]+rowNumber,"");
                }               
            }
            // district column
            if (c == col_name["district"]) {
                if(value !== ""){
                    // set null value to commune 
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["commune"], r]), "");
                    province_code = excel_form.getValueFromCoords(col_name["province_code"],r);
                    code = getDistrictCode_(value, province_code);
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["district_code"], r]), code);
                    excel_form.setComments(letter_col_name["district"]+rowNumber,"");
                }
            }
            // commune column
            if (c == col_name["commune"]) {
                //console.log("Village "+value);
                if(value !== ""){
                    // set null value to village                     
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["village"], r]), "");
                    district_code = excel_form.getValueFromCoords(col_name["district_code"],r);
                    code = getCommuneCode_(value, district_code);                    
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["commune_code"], r]), code);
                    excel_form.setComments(letter_col_name["commune"]+rowNumber,"");
                }
            }
            // village column
            if (c == col_name["village"]) {                
                if(value !== ""){
                    commune_code = excel_form.getValueFromCoords(col_name["commune_code"],r);
                    code = getVillageCode_(value, commune_code);                    
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["village_code"], r]), code);
                    excel_form.setComments(letter_col_name["village"]+rowNumber,"");
                }
            }
            // reason for testing column
            if (c == col_name["reason_for_testing"]) {
                if(value !== ""){
                    code = getReason_(value);
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["reason_for_testing_id"], r]), code);
                    excel_form.setComments(letter_col_name["reason_for_testing"]+rowNumber,"");
                }
            }
            if(c == col_name["province_of_detection"]){
                if(value !== ""){
                    code = getProvinceCode_(value);
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["province_of_detection_code"], r]),code);
                }
            }
            if (c == col_name["reason_for_testing"]) {
                if(value !== ""){
                    code = getReason_(value);
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["reason_for_testing_id"], r]), code);
                    excel_form.setComments(letter_col_name["reason_for_testing"]+rowNumber,"");
                }
            }
            // nationality column
            if (c == col_name["nationality"]) {                
                if(value !== ""){                    
                    code = getNationality(value);
                    console.log("Nationality code: "+code);                    
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["nationality_id"], r]), code);
                    excel_form.setComments(letter_col_name["nationality"]+rowNumber,"");
                }
            }
            if (c == col_name["vaccination_status"]) {                
                if(value !== ""){
                    status = getVaccinationStatus(value);                    
                    if(status == 1){
                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["vaccine_name"], r]), "");
                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["first_vaccinated_date"], r]), "");
                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["second_vaccinated_date"], r]), "");                        
                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["vaccination_status_code"], r]), status);
                    }else{
                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["vaccination_status_code"], r]), status);                        
                    }
                }
            }
            if (c == col_name["vaccine_name"]) {                
                if(value !== ""){
                    status = getVaccineCode(value);                    
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["vaccine_id"], r]), status);
                }else{
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["vaccine_id"], r]), "");
                }
            }
        },
        oncreateeditor: function(el, cell, x, y) {
            if (x == col_name["no_by_day"] 
                || x == col_name["cdc_case_no"] 
                || x == col_name["laboratory_code"]
                || x == col_name["fullname"]
                || x == col_name["age"]
                || x == col_name["phone"]
                || x == col_name["imported_country"]
                || x == col_name["remark"]
            ){
                var config = el.jexcel.options.columns[x].maxlength;
                cell.children[0].setAttribute('maxlength' , config); // set maxlength to column
            }
        }
    });
    var hide_column = [
        col_name['sex_id'],
        col_name['nationality_id'],
        col_name['reason_for_testing_id'],
        col_name['province_code'],
        col_name['district_code'],
        col_name['commune_code'],
        col_name['village_code'],
        col_name['province_of_detection_code'],
        col_name['vaccination_status_code'],
        col_name['vaccine_id'],
        col_name['patient_exist']
    ];
    for(var i = 0 ; i < hide_column.length ; i++){
        excel_form.hideColumn(hide_column[i]); 
    }    
    $("#btnSave").on("click",function(evt){
        var line_list_data  = excel_form.getData();
        var data            = [];
        var valid_check     = 0;
        for(var i in line_list_data){
            var name = line_list_data[i][col_name["fullname"]];
            if(name.trim() !== ""){
                if(valid_check !== 1){
                    valid_check     = 1;
                }
                data.push(line_list_data[i]);
            }
        }
        if(valid_check == 0){
            alert("Please input data......!");
            return false;
        }
        
        $loader.css("display","block");
        $.ajax({
            url: base_url + "/exceljs/save",
            type: "POST",
            data: { data: data},
            dataType: 'json',
            success: function (resText) {
                console.log(resText);
                var bodyHtmlStr          = ''; 
                var n = 1;
                var results = resText.results;
                for(var i in results) {
                    bodyHtmlStr += '<tr>';
                    bodyHtmlStr += '<td>'+n+'</td>';
                    bodyHtmlStr += '<td>'+results[i].name+'</td>';
                    bodyHtmlStr += '<td>'+results[i].status+'</td>';
                    bodyHtmlStr += '<td>'+results[i].msg+'</td>';
                    bodyHtmlStr += '</tr>';
                    n++;
                }
                setTimeout(() => {
                    $loader.css("display","none");
                    $("table[name=table_result] tbody").html(bodyHtmlStr);
                    $result_modal.modal("show");
                }, 500);
                
            },
            error: function(xhr, status, error) {
                var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                    console.log(xhr.responseText);
                }
        })
    })

    $("#btnGetData").on("click",function(evt){
        evt.preventDefault();
        $loader.css("display","block");
        $(this).addClass('disabled'); //prevent multiple click
        var start_date	    = $("input[name=start_date]").data('DateTimePicker').date();
        var end_date	    = $("input[name=end_date]").data('DateTimePicker').date();        
        if (start_date !== null) {
            start_date = moment(start_date.format('YYYY-MM-DD'), 'YYYY-MM-DD');
        }
        if (end_date !== null) {
            end_date = moment(end_date.format('YYYY-MM-DD'), 'YYYY-MM-DD');
        }

        if (start_date > end_date) {
            alert("End date must be greater than or equal to start date ....!")
            $(this).removeClass('disabled'); //prevent multiple click
            $loader.css("display","none");
            return false;
        }
       $(this).removeClass('disabled'); //prevent multiple click
        $.ajax({
            url: base_url + "/exceljs/get_data",
            type: "POST",
            data: { 
                start_date: $("#start_date").val(), 
                end_date: $("#end_date").val()
            },
            dataType: 'json',
            success: function (resText) {
                setTimeout(() => {
                    $loader.css("display","none");
                    $("table[name=table_data] tbody").html(resText.htmlString);
                }, 500);
            }
        })
    })
 })

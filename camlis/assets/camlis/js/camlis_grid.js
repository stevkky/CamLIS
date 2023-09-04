var msg_loading = "សូមរង់ចាំ...";
var patient_code_col = 0; 
var patient_name_col = 1; 
var age_col          = 2;
var gender_col       = 3;
var phone_col        = 4;
var residence_col    = 5;
var province_col     = 6;
var district_col     = 7;
var commune_col      = 8;
var village_col      = 9;
var country_col      = 10;
var nationality_col  = 11;
var arrival_date_col = 12;
var passport_col	  	= 13;
var flight_number_col 	= 14;
var seat_number_col		= 15;
var sample_source_col	= 16;
var collected_date_col	= 17;
var number_of_sample_col	= 18;
var collector_name_col	= 19;
var phone_collector_name_col= 20;
var for_labo_col		= 21;
var gender_id_col    = 22;
var province_id_col  = 23;
var district_id_col  = 24;
var commune_id_col   = 25;
var village_id_col   = 26;
var country_id_col   = 27;
var nationality_id_col = 28;

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

$(function () {
    var $modal_error_line_list = $("#modal_error_line_list");
    var $print_preview_labo_form_modal	= $("#print_preview_labo_form_modal");
    var today = new Date();
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
    var yyyy = today.getFullYear();

    today = yyyy+'-'+mm+'-'+dd;
    
    //console.log(COUNTRIES);
    var province_array      = [];
    var districts_array     = [];
    var communes_array      = [];
    var villages_array      = [];
    var nationalities_array = [];
    var country_array       = [];  
    
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
        var province_code =  instance.jexcel.getValueFromCoords(province_id_col, r);        
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
        var district_code =  instance.jexcel.getValueFromCoords(district_id_col, r); // district_code
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

    var province_code = "";
    var district_code = "";
    var commune_code  = "";
    
    var line_list_table = jspreadsheet(document.getElementById('spreadsheet'), {
        minDimensions: [ 24, 120 ],
        defaultColWidth: 100,
        tableOverflow: true,
        tableHeight: "550px",
        columns: [
            { type:'text', title:'លេខសំគាល់អ្នកជំងឺ',width:140, maxlength:20 },
            { type:'text', title:'ឈ្មោះ*', maxlength:64 },
            { type:'numeric', title:'អាយុ*',width:40 },
            { type:'dropdown', title:'ភេទ*',width:40, source:["ប្រុស" , "ស្រី"] },
            { type:'text', title:'លេខទូរស័ព្ទ', maxlength: 10 },
            { type:'text', title:'កន្លែងស្នាក់នៅ',width:80 },
            { type: 'autocomplete', title:'ខេត្តក្រុង*', width:100, source:province_array },
            { type: 'autocomplete', title:'ស្រុក*', width:100, source:districts_array , filter: districtFilter},
            { type: 'autocomplete', title:'ឃុំ*', width:100, source:communes_array , filter: communeFilter},
            { type: 'autocomplete', title:'ភូមិ*', width:100, source:villages_array ,filter: villageFilter},
            { type: 'autocomplete', title:'មកពីប្រទេស', width:80, source:country_array },
            { type: 'autocomplete', title:'សញ្ជាតិ', width:80, source:nationalities_array },
            { type: 'calendar', title:'ថ្ងៃខែមកដល់', width:80,options: { format:'YYYY-MM-DD' , readonly:true } },
            { type:'text', title:'លិខិតឆ្លងដែន', width:80 , maxlength: 20},
            { type:'text', title:'លេខជើងហោះហើរ', maxlength:10 },
            { type:'text', title:'លេខកៅអី', width:60 ,maxlength: 5},
            { type: 'text', title:'ទីកន្លែងយកសំណាក', width:120 , maxlength:100},
            { type: 'calendar', title:'ថ្ងៃយកសំណាក*', width:100,options: { format:'YYYY-MM-DD' , readonly:true } },
            { type:'dropdown', title:'សំណាកលើកទី*',width:90,source:[1,2,3,4,5,6,7,8,9,10]},
            { type:'text', title:'ឈ្មោះអ្នកប្រមូល', maxlength:50 },
            { type:'text', title:'លេខទូរស័ព្ទ' , maxlength:10 },
            { type:'text', title:'យកទៅមន្ទីរពិសោធន៏',width: 120, maxlength: 100 },
            { type:'text', title:'sex'},
            { type:'text', title:'province_code'}, 
            { type:'text', title:'district_code'}, 
            { type:'text', title:'commune_code'},
            { type:'text', title:'village_code'},
            { type:'text', title:'country_id'}, 
            { type:'text', title:'nationality_id'}
        ],        
        onchange:function(instance,cell, c, r, value) {
            console.log("Column number = "+c);            
            if(c == patient_code_col){
                if(value !== ""){
                    var patient_code = value;                        
                    $.ajax({
                        url: base_url + 'rrt/search_patient/' + patient_code,
                        type: 'POST',
                        data: {pid: patient_code},
                        dataType: 'json',
                        success: function (resText) {

                            var patient = resText.patient;
                            var number_of_sample = resText.number_of_sample;
                            console.log(patient);
                            
                            var nSample = number_of_sample.number;
                            console.log(nSample);
                            if(patient){
                                var name                = patient.name;
                                var age                 = patient.age;
                                var sex                 = (patient.sex == 'M') ? "ប្រុស" : "ស្រី";
                                var phone               = patient.phone;
                                var residence           = patient.residence;
                                var province_kh         = patient.province_kh;
                                var province_id         = patient.province;
                                var district_kh         = patient.district_kh;
                                var district_id         = patient.district;
                                var commune_kh          = patient.commune_kh;
                                var commune_id          = patient.commune;
                                var village_kh          = patient.village_kh;
                                var village_id          = patient.village;
                                var country             = patient.country_name_en;
                                var country_id          = patient.country;
                                var nationality         = patient.nationality_en;
                                var nationality_id      = patient.nationality;
                                var arrival_date        = patient.date_arrival;
                                var passport_number     = (patient.passport_number == undefined || patient.passport_number == null) ? "" : patient.passport_number;
                                var flight_number       = (patient.flight_number == undefined || patient.flight_number == null) ? "" : patient.flight_number;
                                var seat_number         = (patient.seat_number == undefined || patient.seat_number == null) ? "" : patient.seat_number;
                                
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([patient_name_col, r]), name);
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([age_col, r]), age);
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([gender_col, r]), sex);
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([phone_col, r]), phone);
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([residence_col, r]), residence);
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([province_col, r]), province_kh);
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([district_col, r]), district_kh);
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([commune_col, r]), commune_kh);
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([village_col, r]), village_kh);
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([country_col, r]), country);
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([nationality_col, r]), nationality);
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([arrival_date_col, r]), arrival_date);
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([passport_col, r]), passport_number);
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([flight_number_col, r]), flight_number);
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([seat_number_col, r]), seat_number);
                                if(nSample > 0){
                                    var nb = nSample + 1; //
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([number_of_sample_col, r]), nb);
                                }
                            }
                        }
                    })
                }
            }
            if(c == gender_col){
                if(value !== ""){
                    // save id of sex in column 35
                    sex = value == 'ប្រុស' ? 1 : 2;
                    var columnName  = jspreadsheet.getColumnNameFromId([gender_id_col, r]);
                    instance.jexcel.setValue(columnName,sex);
                }
            }
            // province column
            if (c == province_col) {
                if(value !== ""){
                    // set null value to district 
                    var districtColumn = jspreadsheet.getColumnNameFromId([district_col, r]);
                    instance.jexcel.setValue(districtColumn, "");

                    code = getProvinceCode(value);
                    var columnName = jspreadsheet.getColumnNameFromId([province_id_col, r]);
                    instance.jexcel.setValue(columnName,code); 
                }               
            }
            // district column
            if (c == district_col) {
                if(value !== ""){
                    // set null value to commune 
                    var communeColumn = jspreadsheet.getColumnNameFromId([commune_col, r]);
                    instance.jexcel.setValue(communeColumn, "");

                    province_code = line_list_table.getValueFromCoords(province_id_col,r);                    
                    code = getDistrictCode(value, province_code);
                    var columnName = jspreadsheet.getColumnNameFromId([district_id_col, r]);
                    instance.jexcel.setValue(columnName, code);
                }
            }
            // commune column
            if (c == commune_col) {
                if(value !== ""){
                    // set null value to village 
                    var villageColumn = jspreadsheet.getColumnNameFromId([village_col, r]);
                    instance.jexcel.setValue(villageColumn, "");

                    district_code = line_list_table.getValueFromCoords(district_id_col,r);
                    code = getCommuneCode(value, district_code);
                    var columnName = jspreadsheet.getColumnNameFromId([commune_id_col, r]);
                    instance.jexcel.setValue(columnName, code);
                }
            }
            // village column
            if (c == village_col) {
                if(value !== ""){
                    commune_code = line_list_table.getValueFromCoords(commune_id_col,r);
                    code = getVillageCode(value, commune_code);
                    //console.log("get village code "+code);
                    var columnName = jspreadsheet.getColumnNameFromId([village_id_col, r]);
                    instance.jexcel.setValue(columnName, code);
                }
            }

            if (c == country_col) {
                if(value !== ""){
                   //console.log(value);
                   code = getCountry(value);
                   //console.log("Country code: "+code);
                   var columnName = jspreadsheet.getColumnNameFromId([country_id_col, r]);
                   instance.jexcel.setValue(columnName, code);
                }
            }
            // nationality column
            if (c == nationality_col) {
                if(value !== ""){                    
                    code = getNationality(value);
                    //console.log("Nationality code: "+code);
                    var columnName = jspreadsheet.getColumnNameFromId([nationality_id_col, r]);
                    instance.jexcel.setValue(columnName, code);
                }
            }
            
        },
        oncreateeditor: function(el, cell, x, y) {
            if (x == phone_col || x == seat_number_col || x == collector_name_col || x == patient_code_col 
                || x == patient_name_col || x == passport_col || x == flight_number_col || x == sample_source_col 
                || x == phone_collector_name_col || x == for_labo_col) {
            var config = el.jexcel.options.columns[x].maxlength;
            cell.children[0].setAttribute('maxlength' , config);
            }
        }
    });
    
    line_list_table.hideColumn(22);
    line_list_table.hideColumn(23); // 
    line_list_table.hideColumn(24); // 
    line_list_table.hideColumn(25); //
    line_list_table.hideColumn(26); // 
    line_list_table.hideColumn(27); // 
    line_list_table.hideColumn(28); //    
    

    $("#btnSaveList").on("click", function (evt) {
        $(this).addClass('disabled btn-progress'); //prevent multiple click
        myDialog.showProgress('show', { text : msg_loading });
        var line_list_data  = line_list_table.getData();
        var data            = [];
        var require_string  = "";
        var valid_check     = 0;
        var array_check     = [];        
        for(var i in line_list_data){
            var name = line_list_data[i][1];            
            if(name !== ""){
                if(valid_check !== 1){
                    valid_check     = 1;
                }

                require_string += "<tr>";
                // require patient_code
                var patient_code = line_list_data[i][patient_code_col];
                var check_patient_code = false;
                if(patient_code.length > 20 ){
                    require_string += '<td class="text-danger">មិនអាចធំជាង២០តួ</td>';
                }else{
                    require_string += '<td>'+patient_code+'</td>';
                    check_patient_code = true;
                }

                require_string += "<td>"+name+"</td>";
                var age         = line_list_data[i][2];
                var check_age   = false;
                if(age == ""){
                    require_string += '<td class="text-danger">មិនបានបំពេញ</td>';
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
                var gender       = line_list_data[i][gender_col];
                var check_gender = false;
                var gender_id    = line_list_data[i][gender_id_col];

                if(gender.length == 0 || gender_id.length == 0){
                    require_string += '<td class="text-danger">មិនបានជ្រើសរើស</td>';
                }else{
                    require_string += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                    check_gender = true;                    
                }
                var province        = line_list_data[i][province_col];
                var check_province = false;
                var province_id = line_list_data[i][province_id_col];
                if(province.length == 0 || province_id.length == 0){
                    if(province.length > 0){
                        require_string += '<td class="text-danger">ពុំត្រឹមត្រូវ</td>';
                    }else{
                        require_string += '<td class="text-danger">មិនបានជ្រើសរើស</td>';
                    }
                }else{
                    require_string += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                    check_province = true;
                }
                var district = line_list_data[i][district_col];
                var check_district = false;
                var district_id = line_list_data[i][district_id_col];

                if(district.length == 0 || district_id.length == 0){                    
                    if(district.length > 0){
                        require_string += '<td class="text-danger">ពុំត្រឹមត្រូវ</td>';
                    }else{
                        require_string += '<td class="text-danger">មិនបានជ្រើសរើស</td>';
                    }
                }else{
                    require_string += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                    check_district = true;
                }
                var commune         = line_list_data[i][commune_col];
                var check_commune   = false;
                var commune_id      = line_list_data[i][commune_id_col];
                if(commune.length == 0 || commune_id.length == 0){
                    if(commune.length > 0){
                        require_string += '<td class="text-danger">ពុំត្រឹមត្រូវ</td>';
                    }else{
                        require_string += '<td class="text-danger">មិនបានជ្រើសរើស</td>';
                    }
                }else{
                    require_string += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                    check_commune = true;
                }

                var village        = line_list_data[i][village_col];
                var check_village = false;
                var village_id = line_list_data[i][village_id_col];

                if(village.length == 0 || village_id.length == 0){
                    if(village.length > 0){
                        require_string += '<td class="text-danger">ពុំត្រឹមត្រូវ</td>';
                    }else{
                        require_string += '<td class="text-danger">មិនបានជ្រើសរើស</td>';
                    }
                }else{
                    require_string += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                    check_village = true;
                }
                var collected_date = line_list_data[i][collected_date_col];
                var check_collected_date = false;
                if(collected_date == ""){
                    require_string += '<td class="text-danger">មិនបានជ្រើសរើស</td>';
                }else{
                    require_string += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                    check_collected_date = true;
                }
                var number_of_sample = line_list_data[i][number_of_sample_col];
                var check_number_of_sample =false;
                if(number_of_sample == 0){
                    require_string += '<td class="text-danger">មិនបានជ្រើសរើស</td>';
                }else{
                    require_string += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                    check_number_of_sample = true;
                }

                if(!check_patient_code || !check_age || !check_gender || !check_province || !check_district || !check_commune || !check_village 
                    || !check_collected_date || !check_number_of_sample){
                        array_check.push(false);
                }else{
                    data.push(line_list_data[i]);
                    array_check.push(true);
                }                               
            }
        }
        if(valid_check == 0){
            myDialog.showProgress('hide');
            myDialog.showDialog('show', {text: "សូមបញ្ចូលទិន្នន័យជាមុនសិន...", style: 'warning'});
            $(this).removeClass('disabled'); //prevent multiple click
            return false;
        }
        
        if((array_check.indexOf(false) >= 0)){
            myDialog.showProgress('hide');
            $("table[name=tblErrorLineList] tbody").html(require_string);
            $modal_error_line_list.modal('show');
            $(this).removeClass('disabled'); //prevent multiple click
            return false;
        }
        if(data.length > 100){
            myDialog.showProgress('hide');
            myDialog.showDialog('show', {text: "ទិន្នន័យមិនអាចលើសពី100ជួរ...", style: 'warning'});
            $(this).removeClass('disabled'); //prevent multiple click
            return false;
        }
        console.log(data);
        
        $.ajax({
            url: base_url + "/rrt/add_line_list",
            type: "POST",
            data: { data: data},
            dataType: 'json',
            success: function (resText) {
                var patients = resText.patients;
                var n =1;
                var bodyResult = '';
                var psample_ids = '';
                for(var i in patients) {
                    var btnPrint = "";
                    var qr_code_img = ' ';
                    if(patients[i].psample_id !== undefined){
                        btnPrint = '<button type="button" class="btnPrintCovidForm" data-psample_id="'+patients[i].psample_id+'">បោះពុម្ភ</button>'
                        psample_ids += patients[i].psample_id+"n";
                    }
                    if(patients[i].qrcode !== undefined){
                        qr_code_img = '<img src="'+site_url + 'assets/plugins/qrcode/img/'+patients[i].qrcode+'" style="width:64px;" />';
                    }
                    bodyResult += '<tr>';
                    bodyResult += '<td>'+n+'</td>';
                    bodyResult += '<td>'+qr_code_img+'</td>';
                    bodyResult += '<td>'+patients[i].patient_code+'</td>';
                    bodyResult += '<td>'+patients[i].patient_name+'</td>';
                    bodyResult += '<td>'+patients[i].msg+'</td>';  
                    bodyResult += '<td>'+patients[i].sample_msg+'</td>';
                    bodyResult += '<td>'+btnPrint+'</td>';
                    bodyResult += '</tr>';
                    n++;
                }
                setTimeout(function(){
                    myDialog.showProgress('hide');
                    $("#saveToExcel").removeClass('disabled'); //prevent multiple click
                    $("table[name=tblResultLineList] tbody").html(bodyResult);
                    var res = psample_ids.substring(0, psample_ids.length - 1); // remove the last n
                    $("#printAll").attr("data-psample_id",res);
                    $("#saveToExcel").attr("data-psample_id",res);
                    $("#modal_result_line_list").modal("show");
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
        console.log(patient_sample_id);
        $print_preview_labo_form_modal.find(".modal-dialog").empty();
        $print_preview_labo_form_modal.data("patient_sample_id", patient_sample_id);
        $print_preview_labo_form_modal.find("#doPrinting").off("click").on("click", function (evt) {
            evt.preventDefault();
            console.log("Printing");
            printpage(base_url + "rrt/patient_covid_forms/print/" +patient_sample_id);
        });
        console.log("Here is....");
        //Show Progress
        myDialog.showProgress('show', { text : msg_loading });

        $.ajax({
            url: base_url + "rrt/patient_covid_forms/preview/" + patient_sample_id,
            type: 'POST',
            dataType: 'json',
            data: {
                department_optional_view: '',
                sample_optional_view: ''                
            },
            success: function (resText) {   
                console.log(1111);        
                console.log(resText);
                         
                for (var i in resText) {
                    var $page = $("<div class='psample-result'></div>");
                    $page.attr("id", "presult-" + (parseInt(i) + 1));
                    $page.data("patient_sample_id", resText[i].patient_sample_id);
                    $page.html(resText[i].template);

                    $print_preview_labo_form_modal.find(".modal-dialog").append($page);
                }
                
                $print_preview_labo_form_modal.find(".page-count").text(resText.length);
                $print_preview_labo_form_modal.find(".page-number").val(1);
                //$print_preview_labo_form_modal.find(".page-number").autoNumeric({aPad: 0, vMin: 1, vMax: resText.length});

                setTimeout(function () {
                    myDialog.showProgress('hide');
                    $print_preview_labo_form_modal.modal("show");
                }, 400);
            },
            error: function (xhr, status, error) {
                console.log(xhr.status);
                console.log(status);                
                myDialog.showProgress('hide');
                $print_preview_labo_form_modal.modal("show");
                $print_preview_labo_form_modal.find(".modal-dialog").empty();
            }
        });
    })
    $("#saveToExcel").on("click",function(evt){
        evt.preventDefault();  
        $(this).addClass("disabled");
        myDialog.showProgress('show');
        var patient_sample_id = $(this).attr("data-psample_id");
        if(patient_sample_id.length > 0){
            var url = base_url+'rrt/export_grid_result?psample_ids='+patient_sample_id;
		    location.href = encodeURI(url);
        }
        setTimeout(function(){
            myDialog.showProgress('hide');
        },1000);

    })
})

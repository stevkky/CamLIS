/** Add Modal Line List
     * added 19-03-2021
     */
 $(function () {
    var $btnSubmitPatientForm = $("#btnSubmitPatientForm");
    $("#btnAddPatients").on("click", function (evt) {
        var $modal_read_patient_from_excel = $("#modal-read-patient-from-excel"); // added 19-03-2021
        $modal_read_patient_from_excel.modal("show");
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
        $.ajax({
            url 			:base_url + '/patient/upload_file', 
            type:"post",
            data:new FormData(this), //this is formData
            processData:false,
            contentType:false,
            cache:false,
            async:false,			
            success	: function (data)
            {
                console.log(data);
            //    alert(data);
                
            }
        });
        
    });
    
    //console.log(COUNTRIES);
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
    var payment_type_array = [];
    
    
    $.each(PROVINCES, function(key, value) {
        province_array.push({id:value.code, name: value.name_kh});
    });
    $.each(DISTRICTS, function(key, value) {
        districts_array.push({id:value.code, name: value.name_kh});
    });
    $.each(COMMUNES, function(key, value) {
        communes_array.push({id:value.code, name: value.name_kh});
    });
    $.each(VILLAGES, function(key, value) {
        villages_array.push({id:value.code, name: value.name_kh});
    });
    $.each(NATIONALITIES, function(key, value) {
        nationalities_array.push({id:value.num_code, name: value.nationality_en});
    });
    $.each(COUNTRIES, function(key, value) {
        country_array.push({id:value.num_code, name: value.name_en});
    });
    $.each(REQUESTER, function(key, value) {
        requester_array.push({id:value.requester_id, name: value.requester_name});
    });

    //console.log("Reason for testing: "+REASON_FOR_TESTING);
    $.each(REASON_FOR_TESTING, function(key, value) {
      
        reason_for_testing_array.push({id:key, name: value});
    });
    //console.log("CLINICAL_SYMPTOM: "+CLINICAL_SYMPTOM);

    $.each(CLINICAL_SYMPTOM, function(key, value) {
      //  console.log(value.ID +" "+ value.name_kh);
        clinical_symptom_array.push({id:value.ID, name: value.name_kh});
    });
    $.each(SAMPLE_SOURCE, function(key, value) {
        sample_source_array.push({id:value.source_id, name: value.source_name});
    });
    $.each(PAYMENT_TYPE, function(key, value) {
        payment_type_array.push({id:value.id, name: value.name});
    });
    
    var districtFilter = function(instance, cell, c, r, source) {
        var value = instance.jexcel.getValueFromCoords(c - 1, r);
        var res = [];
        $.each(DISTRICTS, function(key, item) {
            if(value == item.province_code){
                res.push({id:item.code, name: item.name_kh});
            }
        });
        return res;
    }
    
    var communeFilter = function(instance, cell, c, r, source) {
        var value = instance.jexcel.getValueFromCoords(c - 1, r);
        var res = [];
        $.each(COMMUNES, function(key, item) {
            if(value == item.district_code){
                res.push({id:item.code, name: item.name_kh});
            }
        });
        return res;
    }
    
    var villageFilter = function(instance, cell, c, r, source) {
        var value = instance.jexcel.getValueFromCoords(c - 1, r);
        var res = [];
        $.each(VILLAGES, function(key, item) {
            if(value == item.commune_code){
                res.push({id:item.code, name: item.name_kh});
            }
        });
        return res;
    }
    var line_list_table = jspreadsheet(document.getElementById('spreadsheet'), {
        minDimensions: [ 24, 300 ],
        defaultColWidth: 100,
        tableOverflow: true,
        tableHeight: "450px",
    /*
        columns: [
            { type: 'text', title:'ឈ្មោះ*', width:120 },
            { type: 'text', title:'លេខសំគាល់អ្នកជំងឺ', width:120 },
            { type: 'dropdown', title:'ភេទ*', width:40, source:[ {id: 1 , name:"ប្រុស"} , {id: 2 , name:"ស្រី"}] },
            { type: 'number', title:'អាយុ*', width:40 },
            { type: 'autocomplete', title:'សញ្ជាតិ', width:100, source:nationalities_array },
            { type: 'text', title:'លេខទូរស័ព្ទ', width:100 },
            { type: 'text', title:'កន្លែងស្នាក់នៅ', width:120 },
            { type: 'autocomplete', title:'ខេត្តក្រុង*', width:120, source:province_array },
            { type: 'autocomplete', title:'ស្រុក*', width:120, source:districts_array , filter: districtFilter},
            { type: 'autocomplete', title:'ឃុំ*', width:120, source:communes_array , filter: communeFilter},
            { type: 'autocomplete', title:'ភូមិ*', width:120, source:villages_array ,filter: villageFilter},
            { type: 'autocomplete', title:'មកពីប្រទេស', width:120, source:country_array },
            { type: 'calendar', title:'ថ្ងៃខែមកដល់', width:100,options: { format:'YYYY-MM-DD' , readonly:true } },
            { type: 'text', title:'លេខលិខិតឆ្លងដែន', width:120 },
            { type: 'text', title:'ទីកន្លែងយកសំណាក', width:120 },
            { type: 'text', title:'ឈ្មោះអ្នកយកសំណាក', width:120 },
            { type: 'text', title:'លេខទូរស័ព្ទអ្នកយកសំណាក', width:200 },
            { type: 'text', title:'កន្លែងទទួលសំណាក', width:120 },
            { type: 'autocomplete', title:'ប្រភពសំណាក*', width:100, source:sample_source_array },
            { type: 'autocomplete', title:'អ្នកស្នើសុំ*', width:120, source:requester_array },
            { type: 'calendar', title:'ថ្ងៃប្រមូលសំណាក*', width:100,options: { format:'YYYY-MM-DD' , time:true , readonly:true } },
            { type: 'calendar', title:'ថ្ងៃទទួលសំណាក*', width:100,options: { format:'YYYY-MM-DD' , time:true , readonly:true } },
            { type: 'autocomplete', title:'ប្រភេទនៃការបង់ប្រាក់*', width:120, source:payment_type_array },
            { type: 'calendar', title:'ថ្ងៃចូលសម្រាកពេទ្យ', width:120,options: { format:'YYYY-MM-DD' , time:true , readonly:true} },
            { type: 'autocomplete', title:'បំណងនៃការធ្វើតេស្ត', width:120, source:reason_for_testing_array },
            { type: 'text', title:'ឈ្មោះអ្នកបំពេញទំរង់', width:120 },
            { type: 'text', title:'លេខទូរស័ព្ទអ្នកបំពេញទំរង់', width:170 },
            { type: 'dropdown', title:'រោគសញ្ញាគ្លីនិក', width:150, source:clinical_symptom_array , autocomplete:true, multiple:true}
        ],
    */
        columns: [
            { type:'text', title:'លេខសំគាល់អ្នកជំងឺ',width:160 },
            { type:'text', title:'ឈ្មោះ*' },
            { type:'number', title:'អាយុ*',width:40 },
            { type:'dropdown', title:'ភេទ*',width:40, source:[ {id: 1 , name:"ប្រុស"} , {id: 2 , name:"ស្រី"}] },
            { type:'text', title:'លេខទូរស័ព្ទ' },            
            { type: 'autocomplete', title:'ខេត្តក្រុង*', width:120, source:province_array },            
            { type: 'autocomplete', title:'ស្រុក*', width:120, source:districts_array , filter: districtFilter},        
            { type: 'autocomplete', title:'ឃុំ*', width:120, source:communes_array , filter: communeFilter},            
            { type: 'autocomplete', title:'ភូមិ*', width:120, source:villages_array ,filter: villageFilter},
            { type:'text', title:'កន្លែងស្នាក់នៅ' },            
            { type: 'autocomplete', title:'មកពីប្រទេស', width:120, source:country_array },        
            { type: 'autocomplete', title:'សញ្ជាតិ', width:100, source:nationalities_array },            
            { type: 'calendar', title:'ថ្ងៃខែមកដល់', width:100,options: { format:'YYYY-MM-DD' , readonly:true } },
            { type:'text', title:'លិខិតឆ្លងដែន', width:100},
            { type:'text', title:'លេខជើងហោះហើរ' },
            { type:'text', title:'លេខកៅអី', width:100 },
            { type:'checkbox', title:'ធ្លាប់',width:40},
            { type:'calendar', title:'ថ្ងៃតេស្ត', readOnly:true, width:100,options: { format:'YYYY-MM-DD' , readonly:true } },
            { type:'checkbox', title:'បាទ/ចាស', width:100 },
            { type:'text', title:'ឈ្មោះអ្នកជំងឺ',readOnly:true},
            { type:'checkbox', title:'ប៉ះពាល់ផ្ទាល់?', readOnly:true},
            { type: 'text', title:'លេខសំណាក', width:100 },
            { type: 'autocomplete', title:'ប្រភពសំណាក*', width:100, source:sample_source_array },
            { type: 'autocomplete', title:'អ្នកស្នើសុំ*', width:120, source:requester_array },            
            { type: 'calendar', title:'កាលបរិច្ឆេទប្រមូលសំណាក*', width:170,options: { format:'YYYY-MM-DD' , time:true , readonly:true } },
            { type: 'calendar', title:'កាលបរិច្ឆេទទទួលសំណាក*', width:170,options: { format:'YYYY-MM-DD' , time:true , readonly:true } },            
            { type: 'autocomplete', title:'ប្រភេទនៃការបង់ប្រាក់*', width:120, source:payment_type_array },            
            { type: 'calendar', title:'ថ្ងៃចូលសម្រាកពេទ្យ', width:120,options: { format:'YYYY-MM-DD' , time:true , readonly:true} },
            { type:'text', title:'រោគវិនិច្ឆ័យ' },
            { type:'checkbox', title:'បន្ទាន់' },            
            { type: 'autocomplete', title:'បំណងនៃការធ្វើតេស្ត', width:120, source:reason_for_testing_array },
            { type:'text', title:'ឈ្មោះអ្នកបំពេញទំរង់', width:160 },
            { type:'text', title:'លេខទូរស័ព្ទ' },
            { type:'text', title:'ឈ្មោះអ្នកប្រមូល' },
            { type:'text', title:'លេខទូរស័ព្ទ' },            
            { type: 'dropdown', title:'រោគសញ្ញាគ្លីនិក', width:150, source:clinical_symptom_array , autocomplete:true, multiple:true},
            { type: 'dropdown', title:'តេស្ត', width:150, source:[{id:479, name: 'SARS-CoV-2 (Method: real time RT-PCR by GeneXpert)'}, {id:497, name: 'SARS-CoV-2 (Applied Biosystems 7500 Fast Real Time PCR)'}]}
        ],
        nestedHeaders:[
            [
                {
                    title: 'ពត័មានអ្នកជំងឺ',
                    colspan: '21',
                },
                {
                    title: 'ពត័មានសំណាក',
                    colspan: '16',
                },
            ],
            [
                {
                    title: 'អ្នកជំងឺ',
                    colspan: '5',
                },
                {
                    title: 'អាស័យដ្ឋាន',
                    colspan: '5',
                },
                {
                    title: 'សំនួរទាក់ទងនឹងកូវដី',
                    colspan: '6'
                },
                {
                    title: 'ធ្លាប់កើតជំងឺកូវិដ-១៩ឬទេ?',
                    colspan: '2'
                },
                {
                    title: 'បើជាអ្នកប៉ះពាល់',
                    colspan: '3'
                },
                {
                    title: 'សំណាក',
                    colspan: '10',
                },
                {
                    title: 'សំនួរទាក់ទងនឹងកូវីដ',
                    colspan: '7'
                }
            ],
        ],
        onchange:function(instance,cell, c, r, value) {
            // patient_code
            if( c == 0){
                var rowNumber = r;
                var totalCol = 20;
                var i;
                var nCol = 1;                
                if(value !== ""){
                    console.log(value);
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
                           
                            if(patient){
                                
                                //console.log("patient_code "+patient.patient_code);
                                var dob = "";
                                var sex = (patient.sex == 'M') ? 1 : 2;
                                var dob = moment(patient.dob, 'YYYY-MM-DD');
                                var age = calculateAge(dob);
                                
                                var is_positive_covid   = (patient.is_positive_covid == null || patient.is_positive_covid == undefined) ? false : true;
                                var is_contacted        = (patient.is_contacted == null || patient.is_contacted == undefined) ? false : true;
                                var contact_with        = (patient.contact_with == null || patient.contact_with == undefined) ? "" : patient.contact_with;
                                var is_direct_contact   = (patient.is_direct_contact == null || patient.is_direct_contact == undefined) ? false : true;
                                var test_date           = (patient.test_date == undefined || patient.test_date == null) ? "" : patient.test_date;;
                                var name                = patient.name;
                                var residence           = (patient.residence == undefined || patient.residence == null) ? "" : patient.residence;
                                var date_arrival        = (patient.date_arrival == undefined || patient.date_arrival == null) ? "" : patient.date_arrival;
                                var country             = (patient.country == undefined || patient.country == null) ? "" : patient.country;
                                var nationality         = (patient.nationality == undefined || patient.nationality == null) ? "" : patient.nationality;
                                var passport_number     = (patient.passport_number == undefined || patient.passport_number == null) ? "" : patient.passport_number;
                                var flight_number       = (patient.flight_number == undefined || patient.flight_number == null) ? "" : patient.flight_number;
                                var seat_number         = (patient.seat_number == undefined || patient.seat_number == null) ? "" : patient.seat_number;

                                //var name            = "name";
                                //var patient_code    = "patient code";
                                var rowData = [                                                                        
                                    name,
                                    age.years,
                                    sex,
                                    patient.phone,
                                    patient.province,
                                    patient.district,
                                    patient.commune,
                                    patient.village,
                                    residence,
                                    country,
                                    nationality,
                                    date_arrival,
                                    passport_number,
                                    flight_number,
                                    seat_number,
                                    is_positive_covid,
                                    test_date,
                                    is_contacted,
                                    contact_with,
                                    is_direct_contact
                                ];
                                console.log(rowNumber);
                                console.log(rowData);
                                //ine_list_table.setRowData([1-7],["patient_name","45",2,"012270281",1,1,1]);
                                //line_list_table.setRowData(rowNumber,rowData);
                                for(i = 0 ; i < rowData.length;i++){
                                    var nameColumn = jspreadsheet.getColumnNameFromId([c+nCol, r]);
                                    instance.jexcel.setValue(nameColumn, rowData[i]);
                                    nCol++;
                                }


                                if(patient.is_pmrs_patient !== undefined && patient.is_pmrs_patient == true){
                                    console.log("set readonly to colummn");
                                    for(i = 9 ; i < totalCol;i++){
                                        var nameColumn = jspreadsheet.getColumnNameFromId([i, r]);
                                        line_list_table.setReadOnly(nameColumn,true)
                                    }
                                }else{
                                    
                                }
                            }else{
                                for(i = 0 ; i < totalCol;i++){
                                    var nameColumn = jspreadsheet.getColumnNameFromId([c+nCol, r]);
                                    instance.jexcel.setValue(nameColumn, "");
                                    nCol++;
                                }
                                for(i = 9 ; i < totalCol;i++){
                                    var nameColumn = jspreadsheet.getColumnNameFromId([i, r]);
                                    line_list_table.setReadOnly(nameColumn,false)
                                }
                            }
                        }
                    })
                }else{
                    for(i = 0 ; i < totalCol;i++){
                        var nameColumn = jspreadsheet.getColumnNameFromId([c+nCol, r]);
                        instance.jexcel.setValue(nameColumn, "");
                        nCol++;
                    }
                    for(i = 9 ; i < totalCol;i++){
                        var nameColumn = jspreadsheet.getColumnNameFromId([i, r]);
                        line_list_table.setReadOnly(nameColumn,false)
                    }
                }
            }

            if (c == 5) {
                var columnName = jspreadsheet.getColumnNameFromId([c + 1, r]);
               // instance.jexcel.setValue(columnName, '');
            }
            if (c == 6) {
                var columnName = jspreadsheet.getColumnNameFromId([c + 1, r]);
               // instance.jexcel.setValue(columnName, '');
            }
            if (c == 7) {
                var columnName = jspreadsheet.getColumnNameFromId([c + 1, r]);
               // instance.jexcel.setValue(columnName, '');
            }
            // if check is true, enable Test Date
            if (c == 16) {
                console.log(value);
                var colummnName = jspreadsheet.getColumnNameFromId([17, r]);
                if(value){

                   line_list_table.setReadOnly(colummnName,false);
                }else{
                    line_list_table.setReadOnly(colummnName,true);
                }
            }
            if (c == 18) {
                console.log(value);
                var is_contacted_column = jspreadsheet.getColumnNameFromId([19, r]);
                var is_direct_contact_olumn = jspreadsheet.getColumnNameFromId([20, r]);
                if(value){
                    console.log("true is right");
                   line_list_table.setReadOnly(is_contacted_column,false);
                   line_list_table.setReadOnly(is_direct_contact_olumn,false);
                }else{                    
                    line_list_table.setReadOnly(is_contacted_column,true);
                   line_list_table.setReadOnly(is_direct_contact_olumn,true);
                }
            }
        },
        
    });
    $("#btnSaveList").on("click", function (evt) {
        $(this).addClass('disabled btn-progress'); //prevent multiple click
        myDialog.showProgress('show', { text : msg_loading });

        console.log(line_list_table.getData());
        var line_list_data = line_list_table.getData();

        $.ajax({
            url: base_url + "/patient/add_line_list",
            type: "POST",
            data: { data: line_list_data},
            dataType: 'json',
            success: function (resText) {
                
                console.log(resText);
                
                var patients = resText.patients;
               
                var bodyResult = '';    
                
                for(var i in patients) {
                    
                    bodyResult += '<tr>';
                    bodyResult += '<td>'+patients[i].patient_code+'</td>';
                    bodyResult += '<td>'+patients[i].patient_name+'</td>';
                    bodyResult += '<td>'+patients[i].msg+'</td>';  
                    bodyResult += '<td>'+patients[i].sample_number+'</td>';
                    bodyResult += '<td>'+patients[i].sample_msg+'</td>';
                    bodyResult += '<td>'+patients[i].test_msg+'</td>';                    
                    bodyResult += '</tr>';
                }
                setTimeout(function(){
                    myDialog.showProgress('hide');
                    $("table[name=tblResultLineList] tbody").html(bodyResult);
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
 })
 
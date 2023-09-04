/*
    * 18-05-2021 
    * Add Result as line list 
    * avoid loading this for other user
    */
    var col_name = {
         sample_number   : 0,
         patient_code    : 1,
         patient_name    : 2,
         test_name       : 3,
         machine_name    : 4,
         test_result     : 5,
         test_date       : 6,
         perform_by      : 7,
         test_id         : 8,
         psample_id      : 9,
         patient_test_id : 10,
         test_result_id  : 11,
         performer_by_id : 12,
         sample_number_status : 13,
         is_test_assigned : 14,
         is_result_added : 15,
         province       : 16,
         phone          : 17
    }
    var performer_array             = [];
    var list_sample_number_array    = [];
    var test_result_array           = [];
    var machine_name_array          = [];
    var machine_name_arr            = [];
    var today   = new Date();
    var dd      = String(today.getDate()).padStart(2, '0');
    var mm      = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
    var yyyy    = today.getFullYear();
    today       = yyyy+'-'+mm+'-'+dd;

    $.each(PERFORMERS, function(key, value) {
        performer_array.push(value.performer_name);
    });
    //console.log(performer_array);
    var TEST_ARRAY = [
        {id:479, test_name: 'SARS-CoV-2 (Method: real time RT-PCR by GeneXpert)'}, 
        {id:497, test_name: 'SARS-CoV-2 (Applied Biosystems 7500 Fast Real Time PCR)'},
        {id:505, test_name: 'SARS-CoV-2 Rapid Antigen Test'},
        {id:509, test_name: 'SARS-CoV-2 (Method: real time RT-PCR by Cobas 6800)'}, //16082021
		{id:516, test_name: 'SARS-CoV-2 (BIOER Gene 9660 Real Time PCR Instruments)'}
    ];
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
    getTestResult(509);    
	getTestResult(516); 
    getMachineName(497);
    getMachineName(479);
    getMachineName(505);
    getMachineName(509); 
	getMachineName(516); 
   
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
    //console.log(machine_name_arr)
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
    //console.log(result_arr);
     var $modal_excel_result = $("#modal_excel_result");
     $("#btnModalAddResult").on("click",function(){
         $modal_excel_result.modal("show");
     })


     function getOrganismName(result_id, sample_test_id){
        //console.log(result_id+" and "+sample_test_id);
        var organism_name = "";
        var test_result = result_arr[sample_test_id];        
        $.each(test_result, function(key, row) {            
            if(row.test_organism_id == result_id && row.sample_test_id == sample_test_id){
                organism_name = row.organism_name;
                return false;
            }
        });
        return organism_name;
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
     function setCommentColumn(col , msg){
         line_list_table_result.setComments(col,msg); // reset comment        
     }
     var line_list_table_result = jspreadsheet(document.getElementById('spreadsheetResult'), {
         minDimensions: [ 7, 200 ],
         defaultColWidth: 120,
         tableOverflow: true,
         tableHeight: "500px",
         columns: [
             { type:'text', title: label_sample_number,width:140 ,maxlength:20},
             { type:'text', title: label_patient_id ,maxlength:20 , width:150},
             { type:'text', title: label_patient_name ,maxlength:60, width:150} ,
             { type: 'text', title: label_test_name, width:200 },
             { type: 'dropdown', title: label_machine_name, width:100, source:machine_name_array, filter:machineNameFilter},
             { type: 'dropdown', title: label_result+"*", width:115, source:test_result_array, filter: testResultFilter},
             { type: 'calendar', title: label_test_date+"*", width:130 , options: { format:'YYYY-MM-DD' , readonly:true , validRange: [ '2021-01-01', today ]}},
             { type: 'dropdown', title: label_performed_by+"*", width:120, source:performer_array},
             { type: 'text', title: "test_id", width:100},
             { type: 'text', title: "psample_id", width:100},
             { type: 'text', title: "patient_test_id", width:100},
             { type: 'text', title: "test_result_id", width:100},
             { type: 'text', title: "performer_by_id", width:100},
             { type: 'text', title: "sample_number_status", width:50},
             { type: 'text', title: "is_test_assigned", width:150},
             { type: 'text', title: "is_result_added", width:150},
             { type: 'text', title: "province", width:150}, // for qr-code
             { type: 'text', title: "phone", width:150}, // for qr-code
         ],
         allowComments:true,
         
         onchange:function(instance,cell, c, r, value) {
             // patient_code
             if( c == 0){
                 //console.log(value+" "+value.length);
                 var row_num = parseInt(r) + 1;
                 //console.log("row_num "+ row_num);
                 if(value !== ""){
                     var sample_number = value;
                     sample_number     = sample_number.toString();
                     sample_number     = sample_number.trim();
                     laboratory_id     = LABORATORY_SESSION.labID;
                     
                     //console.log(sample_number);
                     //console.log(laboratory_id);
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
                             //console.log(resText);
                             var patient_sample     = resText.patient_sample;
                             var patient_info       = resText.patient_info;
                             var sample_status      = resText.sample_status;
                             var sample_tests       = resText.sample_tests;
                             var is_covid_test      = resText.test_status;
                             var test_name          = "";
                             var test_result        = "";
                             var test_date          = "";
                             var performer_id       = "";
                             var performer_name     = "";
                             var sample_test_id     = "";
                             var psample_id         = "";   
                             var patient_test_id    = "";
                             var test_result_id     = "";
                             var machine_name       = "";
                             //console.log(patient_sample);
                             console.log(patient_info);
                             //console.log(sample_status);
                             //console.log(sample_tests);
                            
                             // sample is available
                             if(sample_status){
                                 // set patient info
                                setCommentColumn("A"+row_num, '');
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["sample_number_status"], r]), 1);
                                line_list_table_result.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["patient_code"], r]),false);
                                line_list_table_result.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["patient_name"], r]),false);
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["patient_code"], r]), patient_info.patient_code);
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["patient_name"], r]), patient_info.name);
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["province"], r]), patient_info.province_en);
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["phone"], r]), patient_info.phone);  
                                //check if test sar-cov-2                                
                                if(is_covid_test){
                                    setCommentColumn("D"+row_num, "");
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["is_test_assigned"], r]), 1);
                                    sample_test_id     = (sample_tests[0].sample_test_id !== "" || sample_tests[0].sample_test_id !== undefined) ? sample_tests[0].sample_test_id : "";
                                    
                                    //Chech which Testname of SARV-CoV2
                                    if(sample_test_id !== ""){
                                        test_name_id   = sample_test_id;
                                        $.each(TEST_ARRAY, function(key, value) {
                                            if(value.id == test_name_id){
                                                test_name = value.test_name;
                                                return false;
                                            }
                                        });
                                        line_list_table_result.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["test_name"], r]),false);
                                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["test_name"], r]), test_name);
                                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["test_id"], r]), test_name_id);
                                        // Check Test Result
                                        test_result_id = (sample_tests[0].result == null || sample_tests[0].result == undefined) ? "" : sample_tests[0].result;
                                        psample_id     = (sample_tests[0].patient_sample_id == null || sample_tests[0].patient_sample_id == undefined) ? "" : sample_tests[0].patient_sample_id;
                                        patient_test_id= (sample_tests[0].patient_test_id == null || sample_tests[0].patient_test_id == undefined) ? "" : sample_tests[0].patient_test_id;
                                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["patient_test_id"], r]), patient_test_id);
                                        instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["psample_id"], r]), psample_id);

                                        //Check whether Test result is available
                                        if(test_result_id !== ""){
                                            instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["is_result_added"], r]), 1);
                                            machine_name    = (sample_tests[0].machine_name == null || sample_tests[0].machine_name == undefined) ? "" : sample_tests[0].machine_name;
                                            test_result     = getOrganismName(test_result_id,test_name_id);
                                            test_date       = (sample_tests[0].test_date == null || sample_tests[0].test_date == undefined) ? "" : sample_tests[0].test_date;
                                            performer_name  = "";
                                            performer_id    = sample_tests[0].performer_id;                                           
                                            $.each(PERFORMERS, function(key, value) {
                                                if(value.ID == performer_id){
                                                    performer_name = value.performer_name;
                                                    return false;
                                                }
                                            });
                                            // Enables columns                                            
                                            line_list_table_result.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["test_result"], r]),false);
                                            line_list_table_result.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["test_date"], r]),false);
                                            line_list_table_result.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["perform_by"], r]),false);
                                            
                                            instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["machine_name"], r]), machine_name);
                                            instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["test_result"], r]), test_result);
                                            instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["test_date"], r]), test_date);
                                            instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["perform_by"], r]), performer_name);
                                            instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["test_result_id"], r]), test_result_id);
                                            instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["performer_by_id"], r]), performer_id);

                                            //line_list_table_result.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["test_result"], r]),true);
                                            //line_list_table_result.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["test_date"], r]),true);
                                            //line_list_table_result.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["perform_by"], r]),true);

                                        }else{                                            
                                            instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["is_result_added"], r]), 0);
                                            line_list_table_result.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["machine_name"], r]),false);
                                            line_list_table_result.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["test_result"], r]),false);
                                            line_list_table_result.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["test_date"], r]),false);
                                            line_list_table_result.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["perform_by"], r]),false);
                                        }
                                    }
                                }else{
                                    setCommentColumn("D"+row_num, resText.test_msg);
                                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["is_test_assigned"], r]), 0);
                                    line_list_table_result.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["machine_name"], r]),true);
                                    line_list_table_result.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["test_result"], r]),true);
                                    line_list_table_result.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["test_date"], r]),true);
                                    line_list_table_result.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["perform_by"], r]),true);
                                }
                             }else{

                                setCommentColumn("A"+row_num, resText.sample_msg);
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["sample_number_status"], r]), 0);
                                line_list_table_result.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["patient_code"], r]),false);
                                line_list_table_result.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["test_name"], r]),false);
                                line_list_table_result.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["patient_name"], r]),false);
                                line_list_table_result.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["test_result"], r]),false);
                                line_list_table_result.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["test_date"], r]),false);
                                line_list_table_result.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["perform_by"], r]),false);

                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["patient_name"], r]), "");
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["test_name"], r]), "");
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["patient_code"], r]), "");
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["test_result"], r]), "");
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["test_date"], r]), "");
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["perform_by"], r]), "");
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["test_id"], r]), "");
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["psample_id"], r]), "");
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["patient_test_id"], r]), "");
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["test_result_id"], r]), "");
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["performer_by_id"], r]), "");
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["province"], r]), "");
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["phone"], r]), "");
                                
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["is_test_assigned"], r]), "");
                                instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["is_result_added"], r]), "");     
                                
                                line_list_table_result.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["patient_code"], r]),true);
                                line_list_table_result.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["test_name"], r]),true);
                                line_list_table_result.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["patient_name"], r]),true);
                                line_list_table_result.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["test_result"], r]),true);
                                line_list_table_result.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["test_date"], r]),true);
                                line_list_table_result.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["perform_by"], r]),true);
                             }
                             
                         },error(xhr,status,error){
                             console.log(status);
                             console.log(error);
                         }
                     })
                 }else{
                     // set other column READ ONLY
                    setCommentColumn("A"+row_num,'');
                    setCommentColumn("D"+row_num,'');
                     //line_list_table_result.setRowData(r,["","","","","","","","","","","",""]); // loop unfinite
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["sample_number_status"], r]), "");
                    line_list_table_result.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["patient_code"], r]),false);
                    line_list_table_result.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["test_name"], r]),false);
                    line_list_table_result.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["patient_name"], r]),false);
                    line_list_table_result.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["test_result"], r]),false);
                    line_list_table_result.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["test_date"], r]),false);
                    line_list_table_result.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["perform_by"], r]),false);

                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["patient_name"], r]), "");
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["test_name"], r]), "");
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["patient_code"], r]), "");
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["test_result"], r]), "");
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["test_date"], r]), "");
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["perform_by"], r]), "");
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["test_id"], r]), "");
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["psample_id"], r]), "");
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["patient_test_id"], r]), "");
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["test_result_id"], r]), "");
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["performer_by_id"], r]), "");
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["province"], r]), "");
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["phone"], r]), "");
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["is_test_assigned"], r]), "");
                    instance.jexcel.setValue(jspreadsheet.getColumnNameFromId([col_name["is_result_added"], r]), "");     
                    
                    line_list_table_result.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["patient_code"], r]),true);
                    line_list_table_result.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["test_name"], r]),true);
                    line_list_table_result.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["patient_name"], r]),true);
                    line_list_table_result.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["test_result"], r]),true);
                    line_list_table_result.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["test_date"], r]),true);
                    line_list_table_result.setReadOnly(jspreadsheet.getColumnNameFromId([col_name["perform_by"], r]),true);
                    
                 }
             }
             if(c == col_name["test_result"]){
                if(value !== ""){
                    //console.log("Test result "+ value);
                    var sample_test_id = line_list_table_result.getValueFromCoords(col_name["test_id"],r);
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
         }
     })
     
     line_list_table_result.hideColumn(col_name["test_id"]);
     line_list_table_result.hideColumn(col_name["psample_id"]);
     line_list_table_result.hideColumn(col_name["patient_test_id"]);
     line_list_table_result.hideColumn(col_name["test_result_id"]);
     line_list_table_result.hideColumn(col_name["performer_by_id"]);
     line_list_table_result.hideColumn(col_name["sample_number_status"]);
     line_list_table_result.hideColumn(col_name["is_result_added"]);
     line_list_table_result.hideColumn(col_name["is_test_assigned"]);
     line_list_table_result.hideColumn(col_name["province"]);
     line_list_table_result.hideColumn(col_name["phone"]);
     
    /**
    * 04-06-2021
    * Get psample id and be able to edit
    */
     var $modal_excel_result = $("#modal_add_sample_result");
     var $modal_list_error = $("#modal_error_add_result");
     $("#tab-routine").on('click', '#btnGetPsample', function (evt) {
         //list_sample_number_array = $('input[type=checkbox]:checked').map(function(_, el) {
         //    return $(el).val();
        // }).get();
         //console.log(list_sample_number_array)
         if(list_sample_number_array.length > 0){
             var rNum = 1;
             for(var i in list_sample_number_array){
                 if(list_sample_number_array[i] !== 'on'){
                    line_list_table_result.setValue(jspreadsheet.getColumnNameFromId([col_name["sample_number"], i]), list_sample_number_array[i]);
                 }
                rNum++;
             }
         }
         $modal_excel_result.modal("show");
     });
     $("#tab-routine").on('click', 'input[type=checkbox]', function (evt) {
        var self = $(this);
         if (self.is(":checked")) {
             // add sample number in array             
             list_sample_number_array.push(self.val());
         }else{
            const index = list_sample_number_array.indexOf(self.val());
            if (index > -1) {
                list_sample_number_array.splice(index, 1);
            }
         }         
     })
     
     $("#selectAll").click(function() {
        $("input[type=checkbox]").prop("checked", $(this).prop("checked"));
      });
     /**---End----- */
     $("#btnSaveListResult").on("click",function(e){
        $(this).addClass('disabled btn-progress'); //prevent multiple click
        myDialog.showProgress('show', { text : msg_loading });
        
        var line_list_data  = line_list_table_result.getData();
        var data            = [];
        var valid_check     = 0;
        var array_check     = [];
        var currentDate     = new Date();
        var require_string  = "";
        var n               = 1;

        for(var i in line_list_data){
            var sample_number = line_list_data[i][col_name["sample_number"]];
            sample_number     = sample_number.toString(); // avoid value in integer
            if(sample_number.trim() !== ""){
                if(valid_check !== 1){
                    valid_check     = 1;
                }

                require_string += "<tr>";
                require_string += "<td>"+n+"</td>";
                var sample_number_status = line_list_data[i][col_name["sample_number_status"]];
                var check_sample_number       = false;
                // if Sample number is available
                if(sample_number_status == 0){
                    require_string += "<td>"+line_list_data[i][col_name["sample_number"]]+"</td>";
                    require_string += "<td colspan='7'><span class='text-danger'>"+msg["no_sample_found"]+"</span></td>";
                }else{
                    check_sample_number = true;
                    require_string += "<td>"+line_list_data[i][col_name["sample_number"]]+"</td>";
                    require_string += "<td>"+line_list_data[i][col_name["patient_code"]]+"</td>";
                    require_string += "<td>"+line_list_data[i][col_name["patient_name"]]+"</td>";

                    is_test_assigned = line_list_data[i][col_name["is_test_assigned"]];
                    is_result_added  = line_list_data[i][col_name["is_result_added"]];
                    // Check whether test covid assigned
                    var check_is_test_assigned = false;
                    if(is_test_assigned == 0){
                        require_string += "<td><span class='text-danger'>"+msg["no_test_sar_cov2_found"]+"</span></td>";
                    }else{
                        require_string  += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                        check_is_test_assigned = true;
                    }
                   
                    //Machine name
                    require_string  += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';

                    var test_result             = line_list_data[i][col_name["test_result"]];
                    var test_result_id          = line_list_data[i][col_name["test_result_id"]];
                    var check_test_result       = false;


                    if(test_result.length == 0 || test_result_id.length == 0){
                        require_string += '<td class="text-danger">'+msg["not_select"]+'</td>';
                    }else{
                        require_string  += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                        check_test_result = true;
                    }

                    var test_result_date        =  line_list_data[i][col_name["test_date"]];
                    var check_test_result_date  = false;
                    if(test_result_date !== ""){
                        tr_date = new Date(test_result_date);
                        if( tr_date > currentDate){
                            require_string += '<td class="text-danger">'+msg["not_greater_than_current_date"]+'</td>';
                        }else{
                            require_string  += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                            check_test_result_date       = true;
                        }
                    }else{
                        require_string += '<td class="text-danger">'+msg["not_select"]+'</td>';
                    }

                    var perform_by          = line_list_data[i][col_name["perform_by"]];
                    var perform_by_id       = line_list_data[i][col_name["performer_by_id"]];
                    var check_perform_by    = false;
                    if(perform_by.length == 0 || perform_by_id.length == 0){
                        require_string += '<td class="text-danger">'+msg["not_select"]+'</td>';
                    }else{
                        require_string  += '<td><i class="fa fa-check-circle" aria-hidden="true" style="color:blue;"></i></td>';
                        check_perform_by = true;
                    }               
                    require_string += "</tr>";
                    if(!check_test_result
                        || !check_test_result_date
                        || !check_perform_by  
                        || !check_sample_number
                        || !check_is_test_assigned
                        ){
                            array_check.push(false);                        
                    }else{
                        data.push(line_list_data[i]);
                        array_check.push(true);
                    }
                }
   
               n++; 
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
            $("table[name=tblErrorLineList] tbody").html(require_string);
            $modal_list_error.modal('show');
            $(this).removeClass('disabled'); //prevent multiple click
            return false;
        }

        $.ajax({
            url: base_url + "/patient_sample/add_test_result",
            type: "POST",
            data: { data: data},
            dataType: 'json',
            success: function (resText) {
                var patients        = resText.patients;
                var bodyResult      = '';    
                var psample_ids     = '';
                var n               = 1;
                var img_qr_code     = '';
                var btnPrintQrCode  = "";
                var htmlStringQrCode = "";
                for(var i in patients) {                    
                    var test_result_msg = "";
                    if(patients[i].test_result_msg !== undefined){
                        test_result_msg = patients[i].test_result_msg;
                    }
                    if(patients[i].qr_code !== undefined){
                        //qr_code = '<img src="'+base_url+'/assets/camlis/images/patient_qr_code/'+patients[i].pqr_code+'" style="width:32px;" />'
                        img_qr_code = '<img src="'+patients[i].qr_code+'" style="width:70px;" />';
                        btnPrintQrCode = '<button type="button" class="btnQrCode btn btn-sm btn-primary" data-patient_code="'+patients[i].patient_code+'" title="'+label_qr_code+'"><i class="fa fa-qrcode"></i></button>';
                    }
                    bodyResult += '<tr>';
                    bodyResult += '<td>'+n+'</td>';
                    bodyResult += '<td>'+patients[i].sample_number+'</td>';
                    bodyResult += '<td>'+patients[i].patient_code+'</td>';
                    bodyResult += '<td>'+patients[i].patient_name+'</td>';
                    bodyResult += '<td>'+patients[i].test_name+'</td>';
                    bodyResult += '<td><span>'+patients[i].test_result_msg+'</span></td>';
                    bodyResult += '<td>'+btnPrintQrCode+'</td>';
                    bodyResult += '</tr>';

                    htmlStringQrCode += '<tr data-patient_code="'+patients[i].patient_code+'">';
                    htmlStringQrCode += '<td style="text-align:center; vertical-align:middle;">'+img_qr_code+'</td>';
                    htmlStringQrCode += '<td style="text-align:center; vertical-align:middle;"><span>'+patients[i].patient_code+'</span></td>';
                    htmlStringQrCode += '</tr>';

                    n++;
                }
                setTimeout(function(){
                    myDialog.showProgress('hide');
                    $("table[name=tblResult] tbody").html(bodyResult);
                    var res = psample_ids.substring(0, psample_ids.length - 1); // remove the last n
                    $("table[name=tbl_qr_code] tbody").html(htmlStringQrCode); // 11 March 2021
                    $("#modal_test_results").modal("show");
                    line_list_table_result.setData([]); 
                }, 1000);

            },
            error: function(xhr, status, error) {
                var err = eval("(" + xhr.responseText + ")");
                console.log(err.Message);
                console.log(xhr.responseText);
            }
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
$(function() {
    $("input").iCheck({ checkboxClass: 'icheckbox_minimal', radioClass: 'iradio_minimal' });
    get_patient_type();
    
    var tb_sampleTest = $("#tb_sampleTest").DataTable({
        "info"       : false,
        "processing" : true,
        "serverSide" : true,
        "ajax"       : {
            "url"    : base_url+'test/view_lab_sample_test',
            "type"   : 'POST'
        },
        "language"   : dataTableOption.language
    });


    
    //Edit Sample Test
    $("#tb_sampleTest tbody").on("click", ".edit_stest", function(evt) {
        evt.preventDefault();
        $(this).blur();
        resetNewForm();
        
        //Set Old Value
        var data    = tb_sampleTest.row($(this).parents("tr")).data().DT_RowData;
        
        $("#department").val(data.lab_department_id);
        $("#department").trigger("change", [data.lab_sample_id]);
        $("#test").val(data.testID);
        $("#unit_sign").val(data.unit_sign);
        if (data.numeric_result == 1) {
            $("#field_type").val(3);
        } else if (data.field_type == 1) {
            $("#field_type").val(1);
        } else if (data.field_type == 2) {
            $("#field_type").val(2);
        } else {
            $("#field_type").val(4);
        }
        
        if (data.default_select == 1) {
            $("#is_default_selected").iCheck("check");
        } 
        
        //Get All Sample Test for Grouping
        get_all_sampleTest(data.testPID);
        
        //Get Normal Value
        $.ajax({
            url      : base_url + 'test/get_stest_normal_value',
            type     : 'POST',
            data     : { stest_id : $(this).attr("value") },
            dataType : 'json',
            success  : function(resText) {
                for(var i in resText) {
                    $("#new_ref_range").trigger("click", resText[i]);    
                }
            }
        });
        
        $("#frm_new_stest").find("#test_id").remove();
        $("#frm_new_stest").append($("<input type='hidden' name='stest_id' id='test_id' value='"+$(this).attr("value")+"'>"));
        $("#modal_new_test").find("#header").html("Edit Test");
        $("#modal_new_test").modal({backdrop : "static"});
    });
    
    //Remove Sample Test
    $("#tb_sampleTest tbody").on("click", ".remove_stest", function(evt) {
        evt.preventDefault();
        
        if (confirm("Are you sure you want to delete this test?")) {
            $.ajax({
                url      : base_url + 'test/delete_lab_sample_test',
                type     : 'POST',
                data     : { stest_id : $(this).attr("value") },
                dataType : 'json',
                success  : function(resText) {
                    tb_sampleTest.row( $(this).parents('tr') ).remove().draw();
                }
            });
        }
        
    });
    
    //Reload TB
    $("#modal_new_test").on("hidden.bs.modal", function() {
        tb_sampleTest.ajax.reload();    
        $("#frm_new_stest").find("#test_id").remove();
    });
    
    //Show Test Modal
    $("#addNew").on("click", function(evt) {
        evt.preventDefault();
        resetNewForm();
        $("#frm_new_stest").find("#test_id").remove();
        
        $("#modal_new_test").find("#header").html("New Test");

        get_all_sampleTest();
        
        $("#modal_new_test").modal({backdrop : "static"});
    });
    
    //Get Sample base on Department
    $("#department").on("change", function(evt, lab_sampleID) {
        evt.preventDefault();
        
        $.ajax({
            url      : base_url + 'sample/get_lab_sample',
            type     : 'POST',
            data     : { department : $(this).val() },
            dataType : 'json',
            success  : function(resText) {
                $sampleType = $("#frm_new_stest").find("select#sample");
                $sampleType.find("option").not("option[value=-1]").remove();
                
                for (var i in resText) {
                    var selected = "";
                    if (lab_sampleID != undefined && lab_sampleID == resText[i].lab_sample_id) {
                        selected = "selected";    
                    }
                    
                    $opt = $("<option value='" + resText[i].lab_sample_id + "'"+selected+">" + resText[i].sample_name + "</option>");
                    $sampleType.append($opt);
                }
            },
            error : function(resText) {
                
            }
        });
    });
    
    //New Ref. Range
    $("#new_ref_range").on("click", function(evt, prev_data) {
        evt.preventDefault();
        
        //Saved data
        var db_ptype        = -1;
        var db_min_val      = "";
        var db_max_val      = "";
        var db_nvalue_ID    = "";
        if (prev_data != undefined) {
            db_ptype        = prev_data.patient_type;
            db_min_val      = prev_data.min_value;
            db_max_val      = prev_data.max_value;
            db_nvalue_ID    = prev_data.ID;
        }
        
        var patient_type    = $("#frm_new_stest").data("patient_type");
        
        //Get Previous Patient Type in Ref. Range
        var exist_ptype = [];
        $("#tb_ref_range tbody select.ptype").each(function() {
            exist_ptype.push($(this).val());    
        });
        
        var ptype_option    = "";
        for(var i in patient_type) {
            if (exist_ptype.indexOf(patient_type[i].ID) < 0) {
                var selected = "";
                if (db_ptype == patient_type[i].ID)
                    selected = "selected";
                
                ptype_option   += "<option value='" + patient_type[i].ID + "'"+selected+">" + patient_type[i].type + "</option>"; 
            }
        }
        
        var tr = $("<tr> \
                      <td> \
                          <select name='patient_type' class='form-control ptype'><option value='-1'></option>"+ptype_option+"</select> \
                      </td> \
                      <td><input type='text' class='form-control min_value' name='min_value' onkeypress='return allowDouble(event, this)' value='"+db_min_val+"'></td> \
                      <td><input type='text' class='form-control max_value' name='max_value' onkeypress='return allowDouble(event, this)' value='"+db_max_val+"'></td> \
                      <td class='text-center' style='vertical-align:middle;'><a href='#' class='remove-ref-range text-red hint--left hint--error' data-hint='Remove'><i class='fa fa-trash'></i></a></td> \
                  </tr>");
        tr.data("nvalue_ID", db_nvalue_ID);
        $("#tb_ref_range tbody").append(tr);
    });
    
    //Remove Ref. Range
    $("#tb_ref_range tbody").on("click", ".remove-ref-range", function(evt) {
        evt.preventDefault();
        $tr = $(this).parents("tr");
        $tr.remove();
    });
    
    //Add Test 
    $("#btnNewTest").on("click", function(evt) {
        evt.preventDefault();
        var hasError = 0;
        if ($("#department").val() <= 0) {
            hasError += 1;
            $("[for=department]").attr("data-hint", "Please choose department!");
        } else {
            $("[for=department]").removeAttr("data-hint");
        }
        
        if ($("#sample").val() <= 0) {
            hasError += 1;
            $("[for=sample]").attr("data-hint", "Please choose sample!");
        } else {
            $("[for=sample]").removeAttr("data-hint");
        }
        
        if ($("#test").val() <= 0) {
            hasError += 1;
            $("[for=test]").attr("data-hint", "Please choose test!");
        } else {
            $("[for=test]").removeAttr("data-hint");
        }
        
        if ($("#field_type").val() <= 0) {
            hasError += 1;
            $("[for=field_type]").attr("data-hint", "Please choose Filed Type!");
        } else {
            $("[for=field_type]").removeAttr("data-hint");
        }
        
        $("#tb_ref_range tbody tr").each(function() {
            if ($(this).find("select.ptype").val() <= 0      || 
                $(this).find(".min_value").val().length == 0 || 
                $(this).find(".max_value").val().length == 0) 
            {
                hasError += 1;
                $(this).find("td:not(:last-child)").css("background", "#f2da27");
            } else {
                $(this).find("td").css("background", "white");
            }  
        });
        
        if (hasError > 0) {
            myDialog.showDialog('show', {text:'Please fill all required data!', status : 'Warning! ', style : 'warning'});
            return false;
        }
        
        var ref_range   = [];
        var stest_data  = new Object;
        $.each($("#frm_new_stest").serializeArray(), function(index, arr) {
            stest_data[arr.name]    = arr.value;        
        });
        
        $("#tb_ref_range tbody tr").each(function() {
            ref_range.push({
                ID           : $(this).data("nvalue_ID"),
                patient_type : $(this).find("select.ptype").val(),
                min_value    : $(this).find("input.min_value").val(),
                max_value    : $(this).find("input.max_value").val()
            });            
        }); 
        stest_data.ref_range    = ref_range;
        
        $.ajax({
            url      : base_url + 'test/save_lab_sample_test',
            type     : 'POST',
            data     : stest_data,
            dataType : 'json',
            success  : function(resText) {
                myDialog.showDialog('show', {
                    text    : 'Data has been saved!', 
                    status  : 'Success! ', 
                    style   : 'success',
                    onHidden  : function() {
                        resetNewForm();
                    }
                });
            }
        });
    });
});

function resetNewForm() {
    $("#modal_new_test").find("#header").html("New Test");
    $("#tb_ref_range tbody").empty();
    $("#frm_new_stest").find("label.hint--error").removeAttr("data-hint");
    $("#is_default_selected").iCheck("uncheck");
    $("#frm_new_stest").find("select").val(-1);
    $("#frm_new_stest").find("input[type=text]").val("");
}

function get_all_sampleTest(test_id) {
    //Get Group Header from previous assigned test
    $.ajax({
        url      : base_url + 'test/get_all_stest',
        type     : 'POST',
        dataType : 'json',
        success  : function(resText) {
            $group_by = $("#frm_new_stest").find("select#group_by");
            $group_by.find("option").not("option[value=-1]").remove();

            for (var i in resText) {
                var selected = "";
                if (test_id != undefined && test_id == resText[i].testID) {
                    selected = "selected";    
                }
                
                $opt = $("<option value='" + resText[i].testID + "'>" + resText[i].testName + "</option>");
                $group_by.append($opt);
            }
        }
    });
}

function get_patient_type() {
    //Get Patient Type 
    $.ajax({
        url      : base_url + 'patient/get_patient_type',
        type     : 'POST',
        dataType : 'json',
        success  : function(resText) {
            $("#frm_new_stest").data("patient_type", resText);    
        }
    });
}
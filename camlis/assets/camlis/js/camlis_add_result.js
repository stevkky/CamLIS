$(function() {
    //get last result
    var prev_result = [];
    $test_list      = $("#test_list_wrapper table#tb_test_result tbody");
    
    $.ajax({
        url      : base_url + "result/get_result",
        type     : 'POST',
        data     : { psample_ID : psample_id },
        async    : false, 
        dataType : 'json',
        success  : function(resText) {
            prev_result = resText;
        }
    });

    $.ajax({
        url      : base_url + 'sample/get_psample_details',
        type     : 'POST',
        data     : { patient_dob : patient_dob, patient_sample_id : psample_id },
        dataType : 'json',
        success  : function(resText) { 
            var performer = "";
            for(var i in resText.performer) {
                var row = resText.performer[i];
                performer += "<option value='" + row.ID + "'>" + row.performer_name + "</option>";
            }

            if (resText.details.length > 0) {
                for(var i in resText.details) {
                    var row = resText.details[i];

                    var input = "";
                    if (row.numeric_result == 1) {
                        input = "<input type='text' name='result' class='form-control result' onkeypress='return allowDouble(event, this)'>";
                    } else if (row.field_type == 1 || row.field_type == 2) {
                        input = "<input type='text' name='result' class='form-control result' onfocus='showPossibleResult(this)' readonly style='background:white;'>";
                    }
                    else {
                        input = "<input type='text' name='result' class='form-control result'>";
                    }

                    var ref_range = row.Ref_min_value + ' - ' + row.Ref_max_value;
                    ref_range     = ref_range.replace('null - null','');
                    ref_range     = ref_range.replace('null -', '< ');

                    $tr = $("<tr> \
                                <td style='width:40px'>" + (parseInt(i) + 1) + "</td> \
                                <td>" + row.testName + "</td> \
                                <td>" + input +"</td> \
                                <td style='width:100px;'>" + row.unit_sign + "</td> \
                                <td style='width:120px;'>" + ref_range +"</td> \
                                <td style='width:15%; display: table-cell;' class='hint--left hint--error hint--always' data-hint=''> \
                                    <input type='text' class='form-control test_dt' readonly style='background:white;'> \
                                </td> \
                                <td style='width:18%;'> \
                                    <select name='performer' class='form-control performer'> \
                                        <option value='-1'>Choose Performer</option>" + performer + " \
                                    </select> \
                                </td> \
                             </tr>"); 

                    //datepicker initial
                    var opt = {
                        format : 'dd/mm/yyyy',
                        autoclose   : true,
                        orientation : 'left auto',
                        clearBtn    : true
                        /*language    : 'kh'*/
                    }
                    if (i == 0) {
                        opt.orientation = 'left bottom';
                    }

                    //set data
                    $tr.find("td input.result").data("sample_test_id", row.lab_stest_id);
                    $tr.find("td input.result").data("field_type", row.field_type);
                    $tr.find("td input.result").data("pTest_id", row.pTest_id);
                    $tr.find("td input.test_dt").datepicker(opt);

                    //get last result 
                    var tmp = prev_result['r'+row.pTest_id];
                    if (tmp != undefined) {
                        //single value result
                        if (row.field_type == 0) {
                            var txtValue = [];
                            for(var i in tmp) {
                                $tr.find("td input.result").val(tmp[i].result);
                                $tr.find("td input.test_dt").datepicker('setDate', moment(tmp[0].testDate, 'YYYY-MM-DD').toDate());
                                $tr.find("td select.performer").val(tmp[i].performer);
                            }
                        } 
                        //multiple value result 
                        else {
                            var txtValue = [];
                            var data     = [];
                            var checker  = [];

                            var antibiotic_result = new Object;
                            for(var i in tmp) {
                                if (antibiotic_result['anti_'+tmp[i].resultID] == undefined) {
                                    antibiotic_result['anti_'+tmp[i].resultID] = [];
                                }

                                if (tmp[i].organism_antibioticID != null) {
                                    antibiotic_result['anti_'+tmp[i].resultID].push({
                                        orgAnti_id  : tmp[i].organism_antibioticID,
                                        sensitivity : tmp[i].sensitivity,
                                        test_zone   : tmp[i].test_zone
                                    });   
                                }
                            }

                            for(var i in tmp) {
                                if (checker.indexOf(tmp[i].result) < 0) {
                                    txtValue.push(tmp[i].result_name.trim());
                                    data.push({
                                        pResult     : tmp[i].result,
                                        qty         : tmp[i].qty_id,
                                        antibiotic  : antibiotic_result['anti_' + tmp[i].resultID]
                                    });
                                }
                                checker.push(tmp[i].result);
                            }

                            $tr.find("td input.result").val(txtValue.join(', '));
                            $tr.find("td input.result").data('pResult', data);
                            $tr.find("td input.test_dt").datepicker('setDate', moment(tmp[0].testDate, 'YYYY-MM-DD').toDate());
                            $tr.find("td select.performer").val(tmp[0].performer);
                        }       
                    }

                    $test_list.append($tr);
                }
            }    
        },
        error : function(resText) {

        }
    });
    
    /* ============================================================================================================*/
    
    //Save Result 
    $("#btnAddResult").on("click", function(evt) {
        evt.preventDefault();
        var data = [];
        
        $err = 0;
        $("#test_list_wrapper table#tb_test_result tbody tr").each(function(){
            $inputResult = $(this).find("td input.result");
            $inputDt     = $(this).find("td input.test_dt");
            $performer   = $(this).find("td select.performer");
            
            //require test data
            if ($inputDt.val().length == 0 && $inputResult.val().length != 0) {
                $inputDt.closest('td').attr('data-hint', 'Please chose test date!');
                $err += 1;
            } else {
                $inputDt.closest('td').attr('data-hint', '');
            }
            
            if ($inputResult.data("field_type") > 0) {
                data.push({
                    sampleTest_id : $inputResult.data("sample_test_id"),
                    field_type    : $inputResult.data("field_type"),
                    pTest_id      : $inputResult.data("pTest_id"),
                    value         : $inputResult.data("pResult"),
                    testDate      : $inputDt.val(),
                    performer     : $performer.val()
                });       
            } else {
                data.push({
                    sampleTest_id : $inputResult.data("sample_test_id"),
                    field_type    : $inputResult.data("field_type"),
                    pTest_id      : $inputResult.data("pTest_id"),
                    value         : $inputResult.val(),
                    testDate      : $inputDt.val(),
                    performer     : $performer.val()
                });    
                
            }
        });
        
        if ($err > 0) return false;
        
        //add result 
        $.ajax({
            url      : base_url +  "result/add_result",
            type     : 'POST',
            data     : { results : data },
            dataType : 'json',
            success  : function(resText) {
                myDialog.showDialog('show', {text:'Result has been saved!', status : 'Success! ', style : 'success'});
                $("#result_modal").modal("hide");
            }
        });
    });
    
/* ========================================================================================================= */

});


/* ========================================================================================================= */

/**
 * Show Possible Result (Organism)
 * @param {object} me Control that trigger the event
 */
function showPossibleResult(me) {
    var sampleTest_id = $(me).data("sample_test_id");
    var field_type    = $(me).data("field_type");
    var pTest_id      = $(me).data("pTest_id");
    
    //Previous result
    var prev_result      = $(me).data("pResult");
    var prev_antibiotic  = new Object();
    for(var i in prev_result) {
        if (prev_result[i].antibiotic != undefined) {
            prev_antibiotic["anti_" + prev_result[i].pResult] = prev_result[i].antibiotic;
        }
    }
    
    //clear antibiotic result
    $("#antibiotic_list tbody").empty();
    
    //fetch possible result (organism) of patient's test
    $.ajax({
        url      : base_url + 'organism/get_sample_test_organism',
        type     : 'POST',
        data     : { sampleTest_id : sampleTest_id },
        dataType : 'json',
        success  : function(resText) { 
            //Qunatity
            var qty = "<select class='form-control organism_qty'>\
                   <option value='-1'>Choose Quantity</option>";

            for(var i in resText.resultQty) {
                qty += "<option value='" + resText.resultQty[i].ID + "'>" + resText.resultQty[i].qty + "</option>";
            }

            qty += "</select>";
            
            /* ================================================================================== */

            var type = field_type == 1 ? "radio" : "checkbox";
            $context = $("#possible_result_modal .modal-body table#list tbody");
            $context.empty();
            
            for(var i in resText.possibleResult) {
                $tr = $("<tr value='" + resText.possibleResult[i].stest_organismID + "'> \
                             <td style='vertical-align:middle;'> \
                                 <label style='cursor:pointer;'> \
                                     <input type='"+type+"' name='posResult[]' value='" + resText.possibleResult[i].stest_organismID + "' data-name='" + resText.possibleResult[i].organism_name + "'> \
                                 </label>&nbsp;&nbsp;<b>" + resText.possibleResult[i].organism_name + "</b> \
                             </td> \
                             <td style='width:180px;'>" + qty + "</td> \
                         </tr>");
                
                //set previous antibiotic
                if (prev_antibiotic["anti_" + resText.possibleResult[i].stest_organismID] != undefined) {
                    $tr.data("antibiotic_result", prev_antibiotic["anti_" + resText.possibleResult[i].stest_organismID]);
                }
                
                //set style
                $tr.css("cursor", "pointer");
                
                //set event for each tr
                $tr.on("click", function(evt) {
                    var target = evt.target.tagName.toLowerCase();
                    if (target == "select" || target == "option") {
                        return false;    
                    }
                    
                    //set selected background
                    $(this).toggleClass("selected");
                    $(this).siblings("tr").removeClass("selected");
                    
                    //set selected antibiotic to organism
                    var selected            = getSelectedAntibiotic();
                    var sampleTest_organism = $("#antibiotic_list").data("sampleTest_organism");
                    if (sampleTest_organism != undefined) {
                        $context.find("tr[value=" + sampleTest_organism + "]").data("antibiotic_result", selected);
                        $("#antibiotic_list").removeData("sampleTest_organism");
                    }
                    
                    //get antibiotic
                    if ($(this).find(":checkbox, :radio").is(":checked") && $(this).hasClass("selected")) {
                        var anti_result = $(this).data("antibiotic_result"); //last selected result
                        geAntibiotic($(this).attr("value"), anti_result);
                        
                    } else {
                        $("#antibiotic_list tbody").empty();
                    }
                });
                
                $context.append($tr);
            }

            //set event for all checkbox or radioButton
            $context.find("input")
                    .iCheck({
                         checkboxClass: 'icheckbox_minimal',
                         radioClass: 'iradio_minimal'
                    })
                    .on("ifUnchecked", function() {
                        //remove selected background
                        $tr = $(this).parents("tr");
                        $tr.removeClass("selected");
                        $("#antibiotic_list").removeData("sampleTest_organism");
                
                        $tr.find("td select").val(-1);
                    })
                    .on("ifChecked", function() {
                        //set selected background
                        $tr = $(this).parents("tr");
                        $tr.addClass("selected");
                        $tr.siblings("tr").removeClass("selected");

                        //get antibiotic
                        var antibiotic_result = $tr.data("antibiotic_result");
                        geAntibiotic($(this).attr("value"), antibiotic_result);
                        
                    });

            //set previous result
            if (prev_result != undefined) {
                for(var i in prev_result) {
                    $tr = $context.find("tr[value=" + prev_result[i].pResult + "]");
                    if ($tr.length > 0) {
                        $tr.find("td input[value="+prev_result[i].pResult+"]").iCheck("check");
                        $tr.find("td select").val(prev_result[i].qty);
                    }
                }
            }
            
            //Show Organism Modal
            $("#possible_result_modal").modal({backdrop:'static'});
            
            //Assign Selected Organism
            $("#possible_result_modal .modal-footer #btnAddOrganism").off("click").on("click", function(evt) { 
                //set selected antibiotic
                var selected            = getSelectedAntibiotic();
                var sampleTest_organism = $("#antibiotic_list").data("sampleTest_organism"); 
                if (sampleTest_organism != undefined) {
                    $context.find("tr[value=" + sampleTest_organism + "]").data("antibiotic_result", selected);
                }
                $("#antibiotic_list").removeData("sampleTest_organism");
                /* ============================================================================= */
                
                var pResult     = [];
                var resultTxt   = [];
                $context.find("tr").each(function() {
                    if ($(this).find("td input").is(":checked")) {
                        var val        = $(this).find("td input:checked").val();
                        var txt        = $(this).find("td input:checked").attr("data-name");
                        var resQty     = $(this).find("td select.organism_qty").val();
                        var antibiotic = $(this).data("antibiotic_result");
                        
                        pResult.push({
                            pResult     : val,
                            qty         : resQty,
                            antibiotic  : antibiotic
                        });
                        resultTxt.push(txt);
                    }   
                });

                $(me).data("pResult", pResult);
                $(me).val(resultTxt.join(', '));;
                $("#possible_result_modal").modal("hide");
            });
        },
        error : function(resText) {

        }
    });
 
}

/**
 * Get Antibiotic of Organism
 * @param {number} stest_organismID Sample test organismID
 * @param {Array} antibiotic_result Previous selected antibiotic
 */
function geAntibiotic(stest_organismID, antibiotic_result) {
     $.ajax({
        url      : base_url + "antibiotic/get_organism_antibiotic",
        type     : 'POST',
        data     : { test_organismID : stest_organismID },
        dataType : 'json',
        success  : function(resText) {
            $list = $("#antibiotic_list tbody");
            $list.empty();
            
            if (resText.length > 0) {
                //set identity for antibiotic list for current selected sampelTest_organism
                $("#antibiotic_list").data("sampleTest_organism", stest_organismID);

                //set antibiotic list
                for (var i in resText) {
                    var tr = "<tr value=" + resText[i].orgAnti_id + "> \
                                  <td style='vertical-align:middle;'>" + resText[i].antibiotic_name + "</td> \
                                  <td> \
                                      <select class='form-control sensitivity'> \
                                          <option value='-1'></option> \
                                          <option value='1'>Sensitivity</option> \
                                          <option value='2'>Resistant</option> \
                                          <option value='3'>Intermidiate</option> \
                                      </select> \
                                  </td> \
                                  <td><input type='text' class='form-control test_zone'></td> \
                              </tr>";
                    $list.append(tr);
                }
                
                //set previous selected antibiotic
                for (var i in antibiotic_result) {
                    $tr = $list.find("tr[value=" + antibiotic_result[i].orgAnti_id + "]");
                    if ($tr.length > 0) {
                        $tr.find("td select.sensitivity").val(antibiotic_result[i].sensitivity)
                        $tr.find("td input.test_zone").val(antibiotic_result[i].test_zone);
                    }
                }
                
            } else {
                $("#antibiotic_list").removeData("sampleTest_organism");
            }
        }
    });
}

/**
 * Get Selected Antibiotic
 * @returns {Array} List of Selected Antibiotic
 */
function getSelectedAntibiotic() {
    $list       = $("#antibiotic_list tbody");
    $selected   = [];
    
    $list.find("tr").each(function() {
        var antibiotic_id  = $(this).attr("value");
        var sensitivity    = $(this).find("td select.sensitivity").val();
        var test_zone      = $(this).find("td input.test_zone").val();
        
        if (sensitivity != -1) {
            $selected.push({
                orgAnti_id    : antibiotic_id,
                sensitivity   : sensitivity,
                test_zone     : test_zone
            });
        }
    });
    
    return $selected;
}
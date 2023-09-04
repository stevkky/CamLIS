var isInit = true;
$.fn.dataTable.moment('DD-MM-YYYY HH:mm'); // sort date time // 18-03-2021
var extend_tbl; // 30092021
$(function() {
    var $tbl_sample_urgent  = $("#tb_sample_urgent");
    var $tbl_sample_routine = $("#tb_sample_routine");
    var print_button = "";
    var dtPickerOption = {
        widgetPositioning : {
            horizontal	: 'left',
            vertical	: 'bottom'
        },
        showClear		: true,
        format			: 'DD/MM/YYYY',
        useCurrent		: false,
        maxDate			: new Date(),
        locale			: app_lang == 'kh' ? 'km' : 'en'
    };
    var tb_sample_urgent = $tbl_sample_urgent.DataTable({
        "filter"		: true,
        "info"			: false,
        "bPaginate"		: true,
        "processing"	: true,
        "serverSide"	: true,
        "scrollX"       : true,
        "fixedColumns"  : {
            "leftColumns" : 0,
            "rightColumns": 2
        },
        
        "columnDefs": [{
            "targets": 5,
            "render": function (data, type, row, meta) {
                return moment(data, 'DD-MM-YYYY HH:mm').format('DD-MM-YYYY HH:mm');
            }
        }],
        "select" : true,
        
        "aaSorting": [[5, "desc"]],
        
        "ajax"			: {
            "url"	: base_url+'patient_sample/view_all_patient_sample',
            "type"	: 'POST',
            "data"  : function (d) {
                d.is_urgent = 1;
            }
        },
        "language"		: dataTableOption.language,
        "columns"       : [
            
            { "data" : "number", "width" : "18px", "searchable" : false},
            { "data" : "patient_code", "width" : "80px" },
            { "data" : "patient_name" },
            { "data" : "sample_number", "width" : "90px" },
            { "data" : "collected_date", "width" : "140px", "searchable": false, "className": "collected-date" },
            { "data" : "received_date", "width" : "140px", "searchable": false, "className": "received-date" },
            { "data" : "printedDate", "width" : "140px", "searchable": false, "className": "print-date" },
            { "data" : "sample_source" },
            { "data" : "micro","width" : "0px","searchable": false},
            { "data" : "psample_status", "searchable": false },
            { "data" : "action", "width" : "50px", "className": "text-right no-wrap", "orderable": false, "searchable": false },
            { "data" : "psample_id", "orderData" : -1, "visible" : false, "searchable": false }
        ],
        "lengthMenu": [[10, 20, 40, 60, 80, 100], [10, 20, 40, 60, 80, 100]],
        "pageLength": 20,
        "order": [[0, 'desc']],
        "dom": "<'row'<'col-sm-6' <'search-field-wrapper'>><'col-sm-6 text-right'l>> <'row'<'col-sm-12'tr>> <'row'<'col-sm-5'i><'col-sm-7'p>>",
        "createdRow": function (row, data, dataIndex) {
            var collected_date = moment(data.collected_date + ' ' + data.collected_time, 'YYYY-MM-DD HH:mm:ss');
            var received_date  = moment(data.received_date + ' ' + data.received_time, 'YYYY-MM-DD HH:mm:ss');
            $(row).find("td.collected-date").html(collected_date.isValid() ? collected_date.format('DD-MM-YYYY HH:mm') : '');
            $(row).find("td.received-date").html(received_date.isValid() ? received_date.format('DD-MM-YYYY HH:mm') : '');
        },
        "drawCallback" : function (settings) {
            if ((!settings.aoData || settings.aoData.length == 0) && isInit) {
                $('.nav-tabs a[href="#tab-routine"]').tab('show');
                isInit = false;
            }
            setTimeout( function () {
                $("[data-toggle='tooltip']").tooltip();
              }, 10)
        },
        
        "initComplete": function () {
            //search field
            var $search_wrapper = $("#tb_sample_urgent_wrapper").find(".search-field-wrapper");
            $search_wrapper.append("<div class='input-group'><input class='form-control' id='filter-tb-urgent' placeholder='"+ pageMessage.label_search +"'></div>");
            $search_wrapper.find(".input-group").append("<div class='input-group-btn'><button type='button' id='btn-filter-tb-urgent' class='btn btn-primary btn-flat'><i class='fa fa-search'></i> "+ pageMessage.label_search + "</button></div>")            
        },        
    });
    
    var tb_sample_routine = $tbl_sample_routine.DataTable({
        "filter"		: true,
        "info"			: false,
        "processing"	: true,
        "serverSide"	: true,
        "scrollX"       : true,
        "fixedColumns"  : {
            "leftColumns" : 0,
            "rightColumns": 2
        },
        
        "columnDefs": [{
            "targets":  6,
            
            "render": function (data, type, row, meta) {
              //  console.log(row.collected_date);
              //  var t = moment(row.collected_date+" "+row.collected_time, 'YYYY-MM-DD HH:mm').format('DD-MM-YYYY HH:mm');
              //  console.log(t);
                return moment(row.collected_date+" "+row.collected_time, 'YYYY-MM-DD HH:mm').format('DD-MM-YYYY HH:mm');
            }
        },{ "orderable": false, "targets": [0] }],
        "aaSorting": [[6, "desc"]],        
        "ajax"			: {
            "url"	: base_url+'patient_sample/view_all_patient_sample',
            "type"	: 'POST',
            "data"  : function (d) {
                //console.log(d);
                d.is_urgent   = 0;
                d.sample_progress = $("#routine-sample-type").val();
                if($("#collected_date").data("DateTimePicker") !== undefined){
                    collected_date      = ($("#collected_date").data("DateTimePicker").date() == null) ? "" : $("#collected_date").data("DateTimePicker").date().format('YYYY-MM-DD');
                }else{
                    collected_date ="";
                }
                d.collected_date    = collected_date;           
            }
        },        
        "language"		: dataTableOption.language,
        "columns"       : [
            { "data" : "sam_number","searchable" : false},
            { "data" : "number", "width" : "18px", "searchable" : false},
            { "data" : "patient_code", "width": "80px" },
            { "data" : "patient_name" },
            { "data" : "sample_number", "width": "90px" },
            { "data" : "collected_date", "width" : "140px", "searchable": false, "className": "collected-date" },
            { "data" : "received_date", "width" : "140px", "searchable": false, "className": "received-date" },
            { "data" : "printedDate", "width" : "140px", "searchable": false, "className": "print-date" },
            { "data" : "sample_source" },
            { "data" : "micro","width" : "0px","searchable": false},
            { "data" : "psample_status", "searchable": false },
            { "data" : "action", "width" : "50px", "className": "text-right no-wrap", "orderable": false, "searchable": false },
            { "data" : "psample_id", "orderData" : -1, "visible" : false, "searchable": false }
        ],
        "order": [[5, 'desc'],[4, 'desc']],
        "lengthMenu": [[10, 20, 40, 60, 80, 100], [10, 20, 40, 60, 80, 100]],
        "pageLength": 20,
        "dom": "<'row'<'col-sm-6' <'search-field-wrapper'>><'col-sm-6 text-right' <'#status-filter'>l>> <'row'<'col-sm-12'tr>> <'row'<'col-sm-5'i><'col-sm-7'p>>",
        "initComplete": function () {
            $("#status-filter").html($("#psample-status-wrapper").html());
            $("#psample-status-wrapper").remove();
            $("#routine-sample-type").select2({
                minimumResultsForSearch: -1,
                templateResult: function (state) {
                    if (!state.id) return state.text;
                    var formatState = "<span>";
                    formatState += "<span class='patient-sample-mark-color' style='background-color: "+ $(state.element).data("color") +"'></span>";
                    formatState += state.text;
                    formatState += "</span>";
                    return $(formatState);
                }
            });
            $("#status-filter").find(".select2-container").addClass("routine-sample-type-wrapper text-left");

            //search field
            var $search_wrapper = $("#tb_sample_routine_wrapper").find(".search-field-wrapper");
            $search_wrapper.append("<div class='input-group'><input class='form-control' id='filter-tb-routine' placeholder='"+ pageMessage.label_search +"'></div>");
            $search_wrapper.find(".input-group").append("<div class='input-group-btn'><button type='button' id='btn-filter-tb-routine' class='btn btn-primary btn-flat'><i class='fa fa-search'></i> "+ pageMessage.label_search + "</button></div>");
            //30092021
            $search_wrapper.append(" <div class='input-group'><input type='text' class='form-control dtpicker' placeholder='"+label_collected_date+"' name='collected_date' id='collected_date'><span class='input-group-addon'><i class='fa fa-calendar'></i></span></div> ");
            $("#collected_date").datetimepicker(dtPickerOption);

            // button open modal result
            if(can_edit_psample == 1){
                $search_wrapper.append(" <div class='input-group'> <button type='button' class='btn btn-primary' id='btnGetPsample' title='Edit result'><i class='fa fa-edit'></i></button></div>");
            }
        },
        "createdRow": function (row, data, dataIndex) {
            var collected_date = moment(data.collected_date + ' ' + data.collected_time, 'YYYY-MM-DD HH:mm:ss');
            var received_date  = moment(data.received_date + ' ' + data.received_time, 'YYYY-MM-DD HH:mm:ss');
            $(row).find("td.collected-date").html(collected_date.isValid() ? collected_date.format('DD-MM-YYYY HH:mm') : '');
            $(row).find("td.received-date").html(received_date.isValid() ? received_date.format('DD-MM-YYYY HH:mm') : '');
        }
    });
    extend_tbl = tb_sample_routine; // 30092021
    //Bootstrap Tooltip
    $('[data-toggle="tooltip"]').tooltip();
    /**
     * Filter Routine table
     */
    $("#tab-routine").on("change", "#routine-sample-type", function(evt) {
        tb_sample_routine.ajax.reload();
    });

    $("#tab-routine").on('keyup', '#filter-tb-routine', function (evt) {
        if (evt.keyCode === 13) {
            tb_sample_routine.search(this.value).draw();
        }
    } );

    $("#tab-routine").on('click', '#btn-filter-tb-routine', function (evt) {
        tb_sample_routine.search($("#filter-tb-routine").val()).draw();
    } );

    /**
     * Filter Urgent table
     */
    $("#tab-urgent").on('keyup', '#filter-tb-urgent', function (evt) {
        if (evt.keyCode === 13) {
            tb_sample_urgent.search(this.value).draw();
        }
    } );

    $("#tab-urgent").on('click', '#btn-filter-tb-urgent', function (evt) {
        tb_sample_urgent.search($("#filter-tb-urgent").val()).draw();
    });

    /**
     * Redraw table column
     */
    $("a[data-toggle=tab][href='#tab-routine']").on("shown.bs.tab", function () {
        tb_sample_routine.columns.adjust();
    });

    /**
     * Preview result
     */
    $tbl_sample_routine.on("click", ".preview-sample", function(evt) {
        evt.preventDefault();

        var data = tb_sample_routine.row($(this).closest("tr")).data();
        preview_psample_result(data.psample_id, tb_sample_routine);
    });

    $tbl_sample_urgent.on("click", ".preview-sample", function(evt) {
        evt.preventDefault();

        var data = tb_sample_urgent.row($(this).closest("tr")).data();
        preview_psample_result(data.psample_id, tb_sample_urgent);

    });

    /**
     * Delete Sample
     */
    $tbl_sample_routine.on("click", ".delete-sample", function(evt) {
        evt.preventDefault();

        var data = tb_sample_routine.row($(this).closest("tr")).data();
        delete_psample(data.psample_id, tb_sample_routine);

    });

    $tbl_sample_urgent.on("click", ".delete-sample", function(evt) {
        evt.preventDefault();

        var data = tb_sample_urgent.row($(this).closest("tr")).data();
        delete_psample(data.psample_id, tb_sample_urgent);
    });

    /**
     * Add Result
     */
    /*
    $tbl_sample_routine.on("click", ".add-result", function(evt) {
        evt.preventDefault();

        var data = tb_sample_routine.row($(this).closest("tr")).data();
    //    console.log(data);

    });*/

    /*$tbl_sample_urgent.on("click", ".add-result", function(evt) {
        evt.preventDefault();

        var data = tb_sample_routine.row($(this).closest("tr")).data();
        console.log(data);

    });*/

});

function preview_psample_result(patient_sample_id, datatable) {
    var $print_preview_modal = $("#print_preview_modal");

    $print_preview_modal.find(".modal-dialog").empty();
    $print_preview_modal.data("patient_sample_id", patient_sample_id);
    $print_preview_modal.find("#doPrinting").off("click").on("click", function (evt) {
        evt.preventDefault();
        printpage(base_url + "result/patient_sample_result/print/" + patient_sample_id);
        $.ajax({
            url: base_url + "patient_sample/update_printed_info",
            type: "POST",
            data: { patient_sample_id: patient_sample_id },
            dataType: 'json',
            success: function (result) {
                //if (result.status) datatable.ajax.reload();
            }
        });
    });

    //Show Progress
    myDialog.showProgress('show', { text : globalMessage.loading });

    $.ajax({
        url		: base_url + "result/patient_sample_result/preview/" + patient_sample_id,
        type	: 'POST',
        dataType: 'json',
        success	: function (resText) {
            //console.log(JSON.stringify(resText));

            for (var i in resText) {
                var $page = $("<div class='psample-result'></div>");
                $page.attr("id", "presult-" + (parseInt(i) + 1));
                $page.data("patient_sample_id", resText[i].patient_sample_id);
                $page.html(resText[i].template);
                if (i == 0) $page.addClass("active");
                else $page.hide();

                $print_preview_modal.find(".modal-dialog").append($page);
            }

            $print_preview_modal.find(".page-count").text(resText.length);
            $print_preview_modal.find(".page-number").val(1);
            $print_preview_modal.find(".page-number").autoNumeric({aPad: 0, vMin: 1, vMax: resText.length});
            /*
            * Check verify button
            * @desc check for verify the result
            * 0 : No result input
            * 1 : Input some result
            * 2 : all result are complete input 
            */
           if (parseInt($print_preview_modal.find('#verify').val()) === 0) {
                /*Set print label to variable print button*/
                print_button = $print_preview_modal.find('li.print a').html();
                /*remove the print button*/
                $print_preview_modal.find('li.print').remove();
            }
            /*
            * Check for the complete result
            */
            if ($print_preview_modal.find('#verify').val() === '2') {
                /*
                * Check for the reverify permission
                * 0 : no permission
                * 1 : have permssion
                */
                if (!$print_preview_modal.find('li.approve').attr('reverify')) {
                    // remove the approve button
                    $print_preview_modal.find('li.approve').remove();
                }
            }


            setTimeout(function() {
                myDialog.showProgress('hide');
                $print_preview_modal.modal("show");
            }, 400);
        },
        error		: function (jqXHR, exception) {            
            var msg = '';
            if (jqXHR.status === 0) {
                msg = 'Not connect.\n Verify Network.';
            } else if (jqXHR.status == 404) {
                msg = 'Requested page not found. [404]';
            } else if (jqXHR.status == 500) {
                msg = 'Internal Server Error [500].';
            } else if (exception === 'parsererror') {
                msg = 'Requested JSON parse failed.';
            } else if (exception === 'timeout') {
                msg = 'Time out error.';
            } else if (exception === 'abort') {
                msg = 'Ajax request aborted.';
            } else {
                msg = 'Uncaught Error.\n' + jqXHR.responseText;
            }
            console.log(msg);
            
           
        }
    });
}
/**
* Click approve button
*/
$("#print_preview_modal").on('click', '#approve', function(event) {
    event.preventDefault();
    /*show the processing dialog*/
    myDialog.showProgress('show', { text : globalMessage.loading });
    /*update the approve/verify the result*/
    $.ajax({
        url: base_url + 'patient_sample/approve_result',
        type: 'POST',
        dataType: 'json',
        data: {
            // get patient_sample_id from hidden text field in patient_sample_result.php
            patient_sample_id: $("#print_preview_modal").find('#patient_sample_id').val()
        },
        success: function (data) {
            if (data.message && data.verify >= 1) {
                /*check permission for print*/
                if (typeof print_button != 'undefined') {
                    /*change to print icon, text, id and add event click*/
                    $("#print_preview_modal").find('#approve').html(print_button).prop('id', 'printing').click(function(event) {
                        /*print the result*/
                        printpage(base_url + "result/patient_sample_result/print/" + $("#print_preview_modal").find('#patient_sample_id').val());
                        /*update printed info*/
                        $.ajax({
                            url: base_url + "patient_sample/update_printed_info",
                            type: 'POST',
                            dataType: 'json',
                            data: { patient_sample_id: $("#print_preview_modal").find('#patient_sample_id').val() },
                        });
                    });
                    /*Close the modal when user no permission to print*/
                    $('#print_preview_modal').modal('toggle');
                    /*close processing dialog*/
                    setTimeout(function() {myDialog.showProgress('hide');},400);
                    /*Terminate the function*/
                    return 0;
                }
            }
            if (data.message && data.verify <= 1) {
                /*Not complete result cannot print close modal*/
                $('#print_preview_modal').modal('toggle');
                /*close processing dialog*/
                setTimeout(function() { myDialog.showProgress('hide'); }, 400);
                /*Terminate the function*/
                return 0;
            }
            if (!data.message && data.verify === 1) {
                $print_preview_modal.find('li.approve').remove();
                /*Not complete result cannot print close modal*/
                //$('#print_preview_modal').modal('toggle');
                /*close processing dialog*/
                setTimeout(function() { myDialog.showProgress('hide'); }, 400);
                /*Terminate the function*/
                return 0;
            }
            /*close the processing dialog*/
            setTimeout(function() {myDialog.showProgress('hide');},400);
        }
    });
});
function delete_psample(patient_sample_id, table) {
    if (confirm(pageMessage.q_confirm_delete_patient_sample)) {
        if (patient_sample_id > 0) {

            //Show Progress
            myDialog.showProgress('show', {text: globalMessage.loading});

            $.ajax({
                url: base_url + 'patient_sample/delete',
                type: 'POST',
                data: {patient_sample_id: patient_sample_id},
                dataType: 'json',
                success: function (resText) {
                    myDialog.showProgress('hide');
                    if (resText.status === true) {
                        myDialog.showDialog('show', {text: globalMessage.delete_success, style: 'success'});
                        table.ajax.reload();
                    } else {
                        myDialog.showDialog('show', {text: globalMessage.delete_fail, style: 'warning'});
                    }
                },
                error: function () {
                    myDialog.showProgress('hide');
                    myDialog.showDialog('show', {text: globalMessage.delete_fail, style: 'warning'});
                }
            });
        } else {
            myDialog.showDialog('show', {text: globalMessage.delete_fail, style: 'warning'});
        }
    }
}
$(document).ready(function(){
    $(document).on('dp.change', 'input[name=collected_date].dtpicker', function(){        
        extend_tbl.ajax.reload();
    })
})
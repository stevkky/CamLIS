var isInit = true;
$(function() {

    var $tbl_sample_urgent  = $("#tb_sample_urgent");
    var $tbl_request_routine = $("#tb_request_routine");
    var print_button = "";
    // Table urgent
    var tb_sample_urgent = $tbl_sample_urgent.DataTable({
        "filter"        : true,
        "info"          : false,
        "bPaginate"     : true,
        "processing"    : true,
        "serverSide"    : true,
        "ajax"          : {
            "url"   : base_url+'request/view_all_patient_request',
            "type"  : 'POST',
            "data"  : function (d) {
                d.is_urgent = 1;
            }
        },
        "language"      : dataTableOption.language,
        "columns"       : [
            { "data" : "number", "width" : "18px", "searchable" : false},
            { "data" : "patient_code", "width" : "80px" },
            { "data" : "patient_name" },
            { "data" : "sample_number", "width" : "90px" },
            { "data" : "collected_date", "width" : "140px", "searchable": false, "className": "collected-date" },
            { "data" : "received_date", "width" : "140px", "searchable": false, "className": "received-date" },
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
            console.log(data)
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
        },
        "initComplete": function () {
            //search field
            var $search_wrapper = $("#tb_sample_urgent_wrapper").find(".search-field-wrapper");
            $search_wrapper.append("<div class='input-group'><input class='form-control' id='filter-tb-urgent' placeholder='"+ pageMessage.label_search +"'></div>");
            $search_wrapper.find(".input-group").append("<div class='input-group-btn'><button type='button' id='btn-filter-tb-urgent' class='btn btn-primary btn-flat'><i class='fa fa-search'></i> "+ pageMessage.label_search + "</button></div>")
        },
    });
    // Table routine
    var tb_request_routine = $tbl_request_routine.DataTable({
        "filter"        : true,
        "info"          : false,
        "processing"    : true,
        "serverSide"    : true,
        "ajax"          : {
            "url"   : base_url+'request/view_all_patient_request',
            "type"  : 'POST',
            "data"  : function (d) {
                d.is_urgent   = 0;
                d.sample_progress = $("#routine-request-type").val()
            }
        },
        "language"      : dataTableOption.language,
        "columns"       : [
            { "data" : "number", "width" : "18px", "searchable" : false},
            { "data" : "patient_code", "width": "80px" },
            { "data" : "patient_name" },
            { "data" : "sample_number", "width": "90px" },
            { "data" : "collected_date", "width" : "140px", "searchable": false, "className": "collected-date" },
            { "data" : "received_date", "width" : "140px", "searchable": false, "className": "received-date" },
            { "data" : "sample_source" },
            { "data" : "micro","width" : "0px","searchable": false},
            { "data" : "psample_status", "searchable": false },
            { "data" : "action", "width" : "50px", "className": "text-right no-wrap", "orderable": false, "searchable": false },
            { "data" : "psample_id", "orderData" : -1, "visible" : false, "searchable": false }
        ],
        "order": [[0, 'desc']],
        "lengthMenu": [[10, 20, 40, 60, 80, 100], [10, 20, 40, 60, 80, 100]],
        "pageLength": 20,
        "dom": "<'row'<'col-sm-6' <'search-field-wrapper'>><'col-sm-6 text-right' <'#status-filter'>l>> <'row'<'col-sm-12'tr>> <'row'<'col-sm-5'i><'col-sm-7'p>>",
        "initComplete": function () {
            $("#status-filter").html($("#psample-status-wrapper").html());
            $("#psample-status-wrapper").remove();
            $("#routine-request-type").select2({
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
            $("#status-filter").find(".select2-container").addClass("routine-request-type-wrapper text-left");

            //search field
            var $search_wrapper = $("#tb_request_routine_wrapper").find(".search-field-wrapper");
            $search_wrapper.append("<div class='input-group'><input class='form-control' id='filter-tb-routine' placeholder='"+ pageMessage.label_search +"'></div>");
            $search_wrapper.find(".input-group").append("<div class='input-group-btn'><button type='button' id='btn-filter-tb-routine' class='btn btn-primary btn-flat'><i class='fa fa-search'></i> "+ pageMessage.label_search + "</button></div>")
        },
        "createdRow": function (row, data, dataIndex) {
            var collected_date = moment(data.collected_date + ' ' + data.collected_time, 'YYYY-MM-DD HH:mm:ss');
            var received_date  = moment(data.received_date + ' ' + data.received_time, 'YYYY-MM-DD HH:mm:ss');
            $(row).find("td.collected-date").html(collected_date.isValid() ? collected_date.format('DD-MM-YYYY HH:mm') : '');
            $(row).find("td.received-date").html(received_date.isValid() ? received_date.format('DD-MM-YYYY HH:mm') : '');
        }
    });
    /**
    * @Desc: Converting khmer number to English
    * @param: khmer
    * @return: result
    */
    function convertion(khmer_unicode) {
        var english_digits = {
            '០': '0',
            '១': '1',
            '២': '2',
            '៣': '3',
            '៤': '4',
            '៥': '5',
            '៦': '6',
            '៧': '7',
            '៨': '8',
            '៩': '9',
            'ឥ': '-'
        };
        return khmer_unicode.replace(/[០១២៣៤៥៦៧៨៩ឥ]/g, function(s) { return english_digits[s];});
    }
    /**
    * Filter Routine table
    */
    $("#tab-routine").on("change", "#routine-request-type", function(evt) {
        tb_request_routine.ajax.reload();
    });
    
    $("#tab-routine").on('keyup', '#filter-tb-routine', function(event) {
        if (event.keyCode === 13) {
            var search = convertion($(this).val());
            // Change value to english number
            $(this).val(search);
            tb_request_routine.search(this.value).draw();
        }
    });

    $("#tab-routine").on('click', '#btn-filter-tb-routine', function(event) {
        var search = convertion($("#filter-tb-routine").val());
        // Change value to english number
        $("#filter-tb-routine").val(search);
        tb_request_routine.search($("#filter-tb-routine").val()).draw();
    });
    /**
    * Filter Urgent table
    */
    $("#tab-urgent").on('keyup', '#filter-tb-urgent', function(event) {
        if (event.keyCode === 13) {
            var search = convertion($(this).val());
           // Change value to english number
           $(this).val(search);
           tb_sample_urgent.search(this.value).draw();
        }
    });

    $("#tab-urgent").on('click', '#btn-filter-tb-urgent', function(event) {
        var search = convertion($("#filter-tb-urgent").val());
        // Change value to english number
        $("#filter-tb-urgent").val(search);
        tb_sample_urgent.search($("#filter-tb-urgent").val()).draw();
    });
    /**
    * Redraw table column
    */
    $("a[data-toggle=tab][href='#tab-routine']").on("shown.bs.tab", function () {
        tb_request_routine.columns.adjust();
    });

});
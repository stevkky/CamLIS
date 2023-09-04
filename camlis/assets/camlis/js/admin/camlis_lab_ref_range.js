$(function() {
    var $modalRefRange  = $("#modal-ref-range");
    var tableSampleTest = $("#tbl-sample-test").DataTable({
        "info"       : false,
        "scrollX"    : true,
        "autoWidth"  : false,
        "processing" : true,
        "serverSide" : true,
        "language"   : dataTableOption.language,
        "ajax"       : {
            "url"    : base_url+'test/view_all_std_sample_test',
            "type"   : 'POST',
            "data"   : function (d) {
                d.department_id = $(".filter-box").find("#filter-department").val();
                d.dep_sample_id = $(".filter-box").find("#filter-sample-type").val();
                d.field_type    = [3, 5];
                d.allow_remove  = 0;
            }
        },
        "columns"    : [
            { "data" : "number" },
            { "data" : "department_name" },
            { "data" : "sample_type" },
            { "data" : "test_name" },
            { "data" : "unit_sign", "searchable": false },
            { "data" : "field_type" },
            { "data" : "is_heading", "searchable": false },
            { "data" : "header", "searchable": false },
            { "data" : "action" }
        ],
        "columnDefs": [
            { "targets": -1, "orderable": false, "searchable": false, "width" : "50px", "className" : 'text-middle no-wrap text-center' },
            { "targets": '_all', "className": 'text-middle no-wrap' },
            { "targets": 0, "width": '18px', "searchable": false },
        ],
        "order": [[1, 'asc']],
        "lengthMenu": [[10, 20, 40, 60, 80, 100], [10, 20, 40, 60, 80, 100]],
        "pageLength": 20,
        "fixedColumns" : {
            "leftColumns" : 0,
            "rightColumns": 1
        }
    });

    //Initialize Select2
    $(".filter-box #filter-department, .filter-box #filter-sample-type").select2();

    /**
     * Show Ref Range Modal for Update
     */
    $("#tbl-sample-test").on("click", "a.edit", function(evt) {
        evt.preventDefault();
        $(this).blur();

        //Clear Data
        $modalRefRange.removeData("sample_test_id");
        $modalRefRange.find("#test-name, #department-name, #sample-name").text('');
        //remove ref.range
        $modalRefRange.find("#tbl-ref-range tbody tr[type!=_template]").remove();
        //Add Ref. Range
        $modalRefRange.find(".btn-new-ref-range").trigger("click");

        var data = tableSampleTest.row($(this).parents("tr")).data();
        $modalRefRange.removeData("sample_test_id");
        $modalRefRange.find("#department-name").text(data.department_name);
        $modalRefRange.find("#sample-name").text(data.sample_type);
        $modalRefRange.find("#test-name").text(data.test_name);

        //Show Progress
        myDialog.showProgress('show', { text : msg_loading });

        if (data.sample_test_id > 0) {
            $modalRefRange.data("sample_test_id", data.sample_test_id);

            $.ajax({
                url : base_url + "reference_range/get_lab_reference_range",
                type : 'POST',
                data : { sample_test_id : data.sample_test_id },
                dataType : 'json',
                success : function (resText) {
                    myDialog.showProgress('hide');
                    if (resText !== null) {
                        //3: Numeric, 5: Calculate Result Test => Set Ref. Ranges
                        if ((parseInt(data.field_type_id) === 3 || parseInt(resText.field_type_id) === 5) && resText !== undefined) {
                            $modalRefRange.find("#tbl-ref-range tbody tr[type!=_template]").remove();
                            if (resText.length > 0) {
                                for (var i in resText) {
                                    add_new_ref_range(resText[i]);
                                }
                            } else {
                                add_new_ref_range();
                            }
                        }

                        //show modal
                        $modalRefRange.modal({backdrop: 'static'});
                    }
                },
                error : function () {
                    myDialog.showProgress('hide');
                }
            });
        }
    });

    /**
     * Save/Update Reference range
     */
    $modalRefRange.find("#btnSave").on("click", function(evt) {
        evt.preventDefault();
        var sample_test_id = $modalRefRange.data("sample_test_id");
        var data           = { sample_test_id : sample_test_id, ref_ranges : [] };

        $modalRefRange.find("#tbl-ref-range tbody tr[type!=_template]").each(function () {
            var ptype = $(this).find("select.patient-type").val();
            if (Number(ptype) > 0) {
                data.ref_ranges.push({
                    sample_test_id : sample_test_id,
                    patient_type   : ptype,
                    min_value      : $(this).find("input.min-value").val().trim(),
                    max_value      : $(this).find("input.max-value").val().trim(),
                    range_sign     : $(this).find("select.range-sign").val().trim()
                });
            }
        });
        console.log(data);
        //Show Progress
        myDialog.showProgress('show', { text : msg_loading });

        $.ajax({
            url      : base_url + 'reference_range/set_lab_reference_range',
            type     : 'POST',
            data     : data,
            dataType : 'json',
            success  : function(resText) {
                myDialog.showProgress('hide');
                myDialog.showDialog('show', {text : resText.msg, style : resText.status ? 'success' : 'warning'});
                if (resText.status === true) {
                    $modalRefRange.modal("hide");
                }
            },
            error : function () {
                myDialog.showProgress('hide');
                myDialog.showDialog('show', {text : msg_update_fail, style : 'warning'});
            }
        });
    });

    /**
     * Add New Ref. Range
     */
    $modalRefRange.on("click", '.btn-new-ref-range', function (evt) {
        evt.preventDefault();
        add_new_ref_range();
    });

    /**
     * Delete Ref. Range
     */
    $modalRefRange.on("click", '.btn-remove-ref-range', function (evt) {
        evt.preventDefault();
        var num_rows = $modalRefRange.find("#tbl-ref-range tbody tr[type!=_template]").length;

        //Enabled Option in other Ref. Range
        if (num_rows > 1) {
            var val = $(this).closest("tr").find("select.patient-type").val();
            var other_ref_ranges = $(this).closest("tr").siblings(":not([type=_template])");
            other_ref_ranges.each(function () {
                var select = $(this).find("select.patient-type");
                select.find("option:not(:selected)[value!=-1]").each(function () {
                    if ($(this).attr("value") == val) {
                        $(this).prop("disabled", false);
                    }
                });
                select.select2();
            });

            //remove row
            $(this).closest("tr").remove();
        }

        //Show/Hide Delete/New Reg.Range
        num_rows = $modalRefRange.find("#tbl-ref-range tbody tr[type!=_template]").length;
        if (num_rows === 1) {
            $modalRefRange.find("#tbl-ref-range tbody tr:eq(1) .btn-remove-ref-range").hide();
        } else {
            $modalRefRange.find("#tbl-ref-range tbody tr .btn-remove-ref-range").show();
        }

        $modalRefRange.find("#tbl-ref-range tbody tr[type!=_template] .btn-new-ref-range").hide();
        $modalRefRange.find("#tbl-ref-range tbody tr:last-child .btn-new-ref-range").show();
    });

    /**=================================== Filter Test ====================================================**/
    /**
     * Filter By Department
     */
    $(".filter-box").find("#filter-department").on("change", function(evt) {
        evt.preventDefault();
        $sampleType = $(".filter-box").find("select#filter-sample-type");
        $sampleType.find("option").not("option[value=-1]").remove();
        var department_id = Number($(this).val());

        //reload table test
        tableSampleTest.ajax.reload();

        if (isNaN(department_id) || department_id == undefined || department_id == -1) {
            $sampleType.find("option").not("option[value=-1]").remove();
            return false;
        }

        $.ajax({
            url      : base_url + 'sample/get_std_department_sample',
            type     : 'POST',
            data     : { department_id : department_id },
            dataType : 'json',
            success  : function(resText) {
                for (var i in resText) {
                    $opt = $("<option value='" + resText[i].department_sample_id + "'>" + resText[i].sample_name + "</option>");
                    $sampleType.append($opt);
                }
            },
            error : function(resText) {
                $sampleType.find("option").not("option[value=-1]").remove();
            }
        });
    });

    /**
     * Filter By Sample Type
     */
    $(".filter-box").find("#filter-sample-type").on("change", function(evt) {
        tableSampleTest.ajax.reload();
    });
});

/**
 * Add New Ref. Range.
 * @param _data
 */
function add_new_ref_range(_data) {
    var $tbl_range_body = $("#modal-ref-range").find("#tbl-ref-range tbody");
    var row = $tbl_range_body.find("tr[type=_template]").clone();
    row.removeAttr('type');
    row.show();
    $tbl_range_body.append(row);

    //Initial Select2
    row.find("select.patient-type").select2();

    //Show/Hide Delete/New Reg.Range
    var count = $tbl_range_body.find("tr[type!=_template]").length;
    if (count == 1) {
        $tbl_range_body.find("tr:eq(1) .btn-remove-ref-range").hide();
    } else {
        $tbl_range_body.find("tr .btn-remove-ref-range").show();
    }
    $tbl_range_body.find("tr[type!=_template] .btn-new-ref-range").hide();
    $tbl_range_body.find("tr:last-child .btn-new-ref-range").show();

    //Disable Selected Option
    $tbl_range_body.find("tr[type!=_template]:not(:last-child)").each(function () {
        var val = $(this).find("select.patient-type").val();
        if (parseInt(val) > 0) {
            row.find("select.patient-type option[value=" + val + "]").prop("disabled", true);
        }
    });

    //Disabled Selected Option in other Ref. Range
    row.find("select.patient-type").on("change", function () {
        var val = $(this).val();
        var selected_types = [val];
        var other_ref_ranges = $(this).closest("tr").siblings("[type!=_template]");

        //Get all selected patient types
        other_ref_ranges.each(function () {
            var select = $(this).find("select.patient-type");
            selected_types.push(select.val());
        });

        other_ref_ranges.each(function () {
            var select  = $(this).find("select.patient-type");
            var is_updated  = false;
            select.find("option:not(:selected)[value!=-1]").each(function () {
                var opt_value = $(this).attr("value");
                if (selected_types.indexOf(opt_value) < 0) {
                    $(this).prop("disabled", false);
                    is_updated = true;
                }
                else if (opt_value == val) {
                    $(this).prop("disabled", true);
                    is_updated = true;
                }
            });
            if (is_updated) select.select2();
        });
    });

    //Set Data
    if (_data != undefined) {
        row.find("select.patient-type").val(_data.patient_type).trigger("change");
        row.find("input.min-value").val(_data.min_value);
        row.find("input.max-value").val(_data.max_value);
        row.find("select.range-sign").val(_data.range_sign.trim());
    }
}
$(function() {
    var $modal_test = $("#modal-test");
    var $modal_copy_data = $("#modal-copy-data");
    var tb_test = $("#tbl-test").DataTable({
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
                d.allow_remove  = 1;
            }
        },
        "columns"    : [
            { "data" : "number" },
            { "data" : "order" },
            { "data" : "department_name" },
            { "data" : "sample_type" },
            { "data" : "test_name" },
            { "data" : "unit_sign" },
            { "data" : "field_type" },
            { "data" : "is_heading" },
            { "data" : "header" },
            { "data" : "action" }
        ],
        "columnDefs": [
            { "targets": -1, "orderable": false, "searchable": false, "width" : "50px", "className" : 'text-middle no-wrap text-center' },
            { "targets": '_all', "className": 'text-middle no-wrap' },
            { "targets": 0, "width": '18px', "searchable": false },
            { "targets": 1, "width": '40px', "searchable": false },
            { "targets": 7, "width": '40px', "searchable": false },
            { "targets": 8, "width": '40px', "searchable": false },
        ],
        "order": [[1, 'asc']],
        "lengthMenu": [[10, 20, 40, 60, 80, 100], [10, 20, 40, 60, 80, 100]],
        "pageLength": 20,
        "fixedColumns" : {
            "leftColumns" : 0,
            "rightColumns": 1
        }
    });

    //Bootstrap Tooltip
    $('[data-toggle="tooltip"]').tooltip();

    //Initialize iCheck
    $("input").iCheck({ checkboxClass: 'icheckbox_minimal', radioClass: 'iradio_minimal' });

    //Initialize Select2
    $("#department, #sample-type, #test-group, .filter-box #filter-department, .filter-box #filter-sample-type").select2();
    $("#test-list, select#group-result-list").select2({ placeholder : label_choose });

    /**
     * Enable/Disable Edit Test Name
     */
    $modal_test.find("#test-list").on("change", function () {
        var val = $(this).val();
        if (val > 0) {
            $modal_test.find("#btn-edit-test-name").prop("disabled", false);
        } else {
            $modal_test.find("#btn-edit-test-name").prop("disabled", true);
        }
    });

    /**
     * Show Input for Entry New Test Name
     */
    $modal_test.find("#btn-new-test-name").on("click", function (evt) {
        evt.preventDefault();
        $modal_test.find("#select-test-wrapper").hide();
        $modal_test.find("#test-entry-wrapper").show();
        $modal_test.find("#test-entry-wrapper").find("#test-name").val('');
        $modal_test.find("#test-entry-wrapper").find("#test-name").focus();
        $modal_test.find("#test-entry-wrapper").find("#test-name").removeData("test_id");
        $modal_test.find("#test-list").val(null).trigger("change");
        $modal_test.find("#btn-delete-test-name").prop("disabled", true);
        $modal_test.find("#btnSave").prop("disabled", true);
    });

    /**
     * Show Input for Edit Test Name
     */
    $modal_test.find("#btn-edit-test-name").on("click", function (evt) {
        evt.preventDefault();
        var test_list   = $modal_test.find("#test-list");
        var test_id     = Number(test_list.val()); //Current Selected Test

        if (!isNaN(test_id) && test_id > 0) {
            $modal_test.find("#select-test-wrapper").hide();
            $modal_test.find("#test-entry-wrapper").show();

            $modal_test.find("#test-entry-wrapper").find("#test-name").val(test_list.find("option:selected").text());
            $modal_test.find("#test-entry-wrapper").find("#test-name").focus();
            $modal_test.find("#btn-delete-test-name").prop("disabled", false);
            $modal_test.find("#btnSave").prop("disabled", true);
            $modal_test.find("#test-list").val(null).trigger("change");

            //set test id
            $modal_test.find("#test-entry-wrapper").find("#test-name").removeData("test_id");
            $modal_test.find("#test-entry-wrapper").find("#test-name").data("test_id", test_id);
        }
    });

    /**
     * Show Test Name List (Hide Test Entry Form)
     */
    $modal_test.find("#btn-cancel-test-name").on("click", function (evt) {
        evt.preventDefault();
        var test_id = $modal_test.find("#test-entry-wrapper").find("#test-name").data("test_id");
        if (test_id != undefined && Number(test_id) > 0) {
            $modal_test.find("#test-list").val(test_id).trigger("change");
        } else {
            $modal_test.find("#test-list").val(null).trigger("change");
        }

        $modal_test.find("#select-test-wrapper").show();
        $modal_test.find("#test-entry-wrapper").hide();
        $modal_test.find("#test-entry-wrapper").find("#test-name").removeData("test_id");
        $modal_test.find("#btnSave").prop("disabled", false);
    });

    /**
     * Hide required data for Test Heading
     */
    $modal_test.find("#is-heading").on("ifToggled", function () {
        if ($(this).is(":checked")) {
            $modal_test.find("div.for-test").hide();
        } else {
            $modal_test.find("div.for-test").show();
            $modal_test.find("select#field-type").val(-1).trigger("change");
        }
    });

    /**
     * Get Sample base on Department
     */
    $modal_test.find("#department").on("change", function(evt, dep_sample_id, callback) {
        evt.preventDefault();
        $sampleType = $modal_test.find("select#sample-type");
        $sampleType.find("option").not("option[value=-1]").remove();
        var department_id = Number($(this).val());

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
                    var selected = "";
                    if (dep_sample_id != undefined && dep_sample_id == resText[i].department_sample_id) {
                        selected = "selected";
                    }

                    $opt = $("<option value='" + resText[i].department_sample_id + "'"+selected+">" + resText[i].sample_name + "</option>");
                    $sampleType.append($opt);
                }
                if (callback) callback();
            },
            error : function(resText) {
                $sampleType.find("option").not("option[value=-1]").remove();
            }
        });
    });

    /**
     * Get Test header
     */
    $modal_test.find("#sample-type").on("change", function (evt, header_id) {
        var dep_sample_id = $(this).val();
        $test_group = $modal_test.find("#test-group");
        $test_group.find("option").not("option[value=-1]").remove();

        if (isNaN(dep_sample_id) || dep_sample_id == undefined || dep_sample_id == -1) {
            $test_group.find("option").not("option[value=-1]").remove();
            return false;
        }

        $.ajax({
            url      : base_url + 'test/get_std_sample_test',
            type     : 'POST',
            data     : { dep_sample_id : dep_sample_id, is_heading : 1 },
            dataType : 'json',
            success  : function(resText) {
                for (var i in resText) {
                    var selected = "";
                    if (header_id != undefined && header_id == resText[i].sample_test_id) {
                        selected = "selected";
                    }
                    $opt = $("<option value='" + resText[i].sample_test_id + "' " + selected + ">" + resText[i].test_name + "</option>");
                    $test_group.append($opt);
                }
            },
            error : function(resText) {
                $test_group.find("option").not("option[value=-1]").remove();
            }
        });
    });

    /**
     * Show New Sample Test Modal
     */
    $("#addNew").on("click", function(evt) {
        evt.preventDefault();

        //Label
        $modal_test.find(".modal-title.new").show();
        $modal_test.find(".modal-title.edit").hide();
        $modal_test.find("#btnSave span.save").show();
        $modal_test.find("#btnSave span.edit").hide();

        //Clear Form
        clearTestForm();

        $modal_test.modal({ backdrop : 'static' });
    });

    /**
     * Save/Update Test Name
     */
    $modal_test.find("#btn-save-test-name").on("click", function (evt) {
        evt.preventDefault();
        var test_name   = $modal_test.find("#test-entry-wrapper #test-name").val().trim();
        var test_id     = Number($modal_test.find("#test-entry-wrapper").find("#test-name").data("test_id"));
        var data        = { test_name : test_name };
        var url         = base_url + "test/add_std_test_name";

        if (test_name.length == 0) {
            myDialog.showDialog('show', {text : msg_fill_required_data, style : 'warning'});
            return false;
        }

        if (!isNaN(test_id) && test_id > 0) {
            url = base_url + "test/update_std_test_name";
            data.test_id = test_id;
        }

        $.ajax({
            url      : url,
            type     : 'POST',
            data     : data,
            dataType : 'json',
            success  : function(resText) {
                myDialog.showDialog('show', {text : resText.msg, style : resText.status > 0 ? 'success' : 'warning'});
                //Add new test name to test list
                if (isNaN(test_id) && resText.status > 0 && resText.data && resText.data.test_id > 0) {
                    $modal_test.find("#btn-cancel-test-name").trigger("click");
                    $modal_test.find("#select-test-wrapper #test-list").append("<option value='" + resText.data.test_id + "'>" + resText.data.test_name + "</option>");
                    $modal_test.find("#select-test-wrapper #test-list").val(resText.data.test_id).trigger("change");
                } else if (test_id > 0 && resText.status > 0) {
                    $modal_test.find("#btn-cancel-test-name").trigger("click");
                    $modal_test.find("#select-test-wrapper #test-list").find("option[value="+test_id+"]").text(data.test_name);
                    $modal_test.find("#select-test-wrapper #test-list").select2();
                    $modal_test.find("#select-test-wrapper #test-list").val(test_id).trigger("change");
                }
            },
            error : function () {
                myDialog.showDialog('show', {text : test_id > 0 ? msg_update_fail : msg_save_fail, style : 'warning'});
            }
        });
    });

    /**
     * Delete Test Name
     */
    $modal_test.find("#btn-delete-test-name").on("click", function (evt) {
        evt.preventDefault();
        var test_id = Number($modal_test.find("#test-entry-wrapper").find("#test-name").data("test_id"));

        if (isNaN(test_id) || test_id <= 0) return false;

        if (confirm(msg_q_delete_test)) {
            $.ajax({
                url      : base_url + 'test/delete_std_test_name',
                type     : 'POST',
                data     : { test_id : test_id },
                dataType : 'json',
                success  : function(resText) {
                    myDialog.showDialog('show', { text : resText.msg, style : resText.status > 0 ? 'success' : 'warning' });
                    if (resText.status > 0) {
                        $modal_test.find("#btn-cancel-test-name").trigger("click");
                        $modal_test.find("#select-test-wrapper #test-list").find("option[value=" + test_id + "]").remove();
                        $modal_test.find("#select-test-wrapper #test-list").val(null).trigger("change");
                    }
                },
                error   : function () {
                    myDialog.showDialog('show', {text: msg_delete_fail, style : 'warning'});
                }
            });
        }
    });

    /**
     * Save/Update Sample Test
     */
    $modal_test.find("#btnSave").on("click", function(evt) {
        evt.preventDefault();
        var is_valid = true;
        var _data = {
            department_id    : Number($modal_test.find("#department").val()),
            dep_sample_id    : Number($modal_test.find("#sample-type").val()),
            test_id          : Number($modal_test.find("select#test-list").val()),
            //is_heading       : $modal_test.find("#is-heading").is(":checked") ? 1 : 0,
            is_heading       : $modal_test.find("#is-heading").is(":checked") ? true : false,
            default_selected : $modal_test.find("#is-default-selected").is(":checked") ? 1 : 0,
            group_by         : $modal_test.find("#test-group").val(),
            unit_sign        : $modal_test.find("#unit-sign").val(),
            field_type       : Number($modal_test.find("#field-type").val()),
            group_result     : $modal_test.find("input#group-result-name").val().trim(),
            test_order       : Number($modal_test.find("#test-order").val())
        };

        _data.organism_antibiotic = [];
        if ([1, 2].indexOf(Number(_data.field_type)) > -1) {
            $modal_test.find("#tbl-organism tbody tr.organism:first-child").trigger("click", [false, "update"]);
            $modal_test.find("#tbl-organism tbody tr.organism").find("input:checkbox.organism-value:checked").each(function () {
                var organism_id = $(this).val();
                var row = $(this).closest("tr.organism");
                if (Number(organism_id) > 0 ) {
                    _data.organism_antibiotic.push({
                        organism_id : Number(organism_id),
                        antibiotic : row.data("antibiotic") != undefined ? row.data("antibiotic") : []
                    });
                }
            });
        }

        if (!isNaN(_data.department_id) && _data.department_id <= 0) {
            is_valid = false;
        }
        if (!isNaN(_data.dep_sample_id) && _data.dep_sample_id <= 0) {
            is_valid = false;
        }
        if (isNaN(_data.test_id) || _data.test_id <= 0) {
            is_valid = false;
        }
        if (_data.is_heading == 0 && !isNaN(_data.field_type) &&_data.field_type <= 0) {
            is_valid = false;
        }

        if (!is_valid) {
            myDialog.showDialog('show', {text : msg_fill_required_data, style : 'warning'});
            return false;
        }

        _data.ref_ranges = [];
        if (Number(_data.field_type) == 3 || Number(_data.field_type) == 5) { //Numeric
            $modal_test.find("#tbl-ref-range tbody tr[type!=_template]").each(function () {
                var ptype = $(this).find("select.patient-type").val();
                if (Number(ptype) > 0) {
                    _data.ref_ranges.push({
                        patient_type : ptype,
                        min_value    : $(this).find("input.min-value").val(),
                        max_value    : $(this).find("input.max-value").val(),
                        range_sign   : $(this).find("select.range-sign").val()
                    });
                }
            });
        }

        var sample_test_id = $modal_test.data("sample_test_id");
        var url     = base_url + "test/add_std_sample_test";
        if (sample_test_id != undefined && Number(sample_test_id) > 0) {
            url = base_url + "test/update_std_sample_test";
            _data.sample_test_id = sample_test_id;
        }

        //Show Progress
        myDialog.showProgress('show', { text : msg_loading });

        console.log(_data);
        
        $.ajax({
            url      : url,
            type     : 'POST',
            data     : _data,
            dataType : 'json',
            success  : function(resText) {
                myDialog.showProgress('hide');
                myDialog.showDialog('show', {text : resText.msg, style : resText.status > 0 ? 'success' : 'warning'});
                if (resText.status > 0) {
                    tb_test.ajax.reload();
                    tb_test.columns.adjust();
                    $("#modal-test").modal("hide");
                }
            },
            error : function () {
                myDialog.showProgress('hide');
                myDialog.showDialog('show', {text : sample_test_id > 0 ? msg_update_fail : msg_save_fail, style : 'warning'});
            }
        });
    });

    /**
     * Show Test Modal for Update
     */
    $("#tbl-test").on("click", "a.edit", function(evt) {
        evt.preventDefault();
        $(this).blur();

        //Label
        $modal_test.find(".modal-title.new").hide();
        $modal_test.find(".modal-title.edit").show();
        $modal_test.find("#btnSave span.save").hide();
        $modal_test.find("#btnSave span.edit").show();

        var data = tb_test.row($(this).parents("tr")).data();
        $modal_test.removeData("sample_test_id");

        //Show Progress
        myDialog.showProgress('show', { text : msg_loading });

        if (data.sample_test_id > 0) {
            $modal_test.data("sample_test_id", data.sample_test_id);

            $.ajax({
                url : base_url + "test/get_std_sample_test_details",
                type : 'POST',
                data : { sample_test_id : data.sample_test_id },
                dataType : 'json',
                success : function (resText) {
                    myDialog.showProgress('hide');
                    if (resText != null) {
                        $modal_test.find("#department").val(resText.department_id).trigger("change", [resText.dep_sample_id, function () {
                            $modal_test.find("#sample-type").trigger("change", resText.testPID);
                        }]);
                        $modal_test.find("#test-list").val(resText.test_id).trigger("change");

                        //default selected
                        $modal_test.find("#is-default-selected").iCheck(resText.default_select == 1 ? 'check' : 'uncheck');

                        //is heading
                        //$modal_test.find("#is-heading").iCheck(resText.is_heading == 1 ? 'check' : 'uncheck');
                        $modal_test.find("#is-heading").iCheck(resText.is_heading == true ? 'check' : 'uncheck');
                        //field type
                        $modal_test.find("#field-type").val(resText.field_type).trigger("change");

                        //unit sign
                        $modal_test.find("#unit-sign").val(resText.unit_sign);

                        //Group Result
                        $modal_test.find("#group-result-name").val(resText.group_result);

                        //test order
                        $modal_test.find("#test-order").val(resText.test_order);

                        //3: Numeric, 5: Calculate Result Test => Set Ref. Ranges
                        resText.organism_antibiotic = Object.keys(resText.organism_antibiotic).map(function (key) { return resText.organism_antibiotic[key]; });
                        if ((resText.field_type == 3 || resText.field_type == 5) && resText.ref_ranges != undefined) {
                            $modal_test.find("#tbl-ref-range tbody tr[type!=_template]").remove();
                            if (resText.ref_ranges.length > 0) {
                                for (var i in resText.ref_ranges) {
                                    add_new_ref_range(resText.ref_ranges[i]);
                                }
                            } else {
                                add_new_ref_range();
                            }
                        }
                        //Single/Multiple Result Test => Set Possible Result
                        else if ([1, 2].indexOf(Number(resText.field_type)) > -1 && resText.organism_antibiotic != undefined && resText.organism_antibiotic.length > 0) {
                            for (var i in resText.organism_antibiotic) {
                                var organism_id = resText.organism_antibiotic[i].organism_id;
                                var checkbox = $modal_test.find("input:checkbox#org-" + organism_id);
                                if (checkbox.length > 0) {
                                    checkbox.iCheck('check');
                                    var antibiotic = resText.organism_antibiotic[i].antibiotic ? resText.organism_antibiotic[i].antibiotic : [];
                                    $modal_test.find("tr#organism-" + organism_id).data("antibiotic", antibiotic);
                                }
                            }
                        }

                        $modal_test.find("#antibiotic-wrapper").fadeOut(200);
                        $modal_test.find("ul#antibiotic-list li select.antibiotic").iCheck('uncheck');

                        //show modal
                        $modal_test.modal({backdrop: 'static'});
                    }
                },
                error : function () {
                    myDialog.showProgress('hide');
                }
            });
        }
    });

    /**
     * Delete Standard Sample Test
     */
    $("#tbl-test").on("click", ".remove", function(evt) {
        evt.preventDefault();

        var data = tb_test.row($(this).parents("tr")).data();
        console.log(data);
        if (confirm(msg_q_delete_test)) {
            //Show Progress
            myDialog.showProgress('show', { text : msg_loading });

            $.ajax({
                url      : base_url + 'test/delete_std_sample_test',
                type     : 'POST',
                data     : { sample_test_id : data.sample_test_id },
                dataType : 'json',
                success  : function(resText) {
                    myDialog.showProgress('hide');
                    myDialog.showDialog('show', { text : resText.msg, style : resText.status > 0 ? 'success' : 'warning' });
                    tb_test.ajax.reload();
                },
                error   : function () {
                    myDialog.showProgress('hide');
                    myDialog.showDialog('show', {text: msg_delete_fail, style : 'warning'});
                }
            });
        }
    });

    /**
     * Show Required Form Base on Field Type
     */
    $modal_test.find("select#field-type").on("change", function () {
        var val = parseInt($(this).val());

        $modal_test.find("#ref-ranges").hide();
        $modal_test.find("#possible-results").hide();

        if ([-1, 3,5].indexOf(val) > -1) {
            $modal_test.find("#ref-ranges").show();
        } else if ([1, 2].indexOf(val) > -1) {
            $modal_test.find("#possible-results").show();
        }

        //Set Default Unit Sign (Single/Multiple/Text)
        if ([1, 2, 4].indexOf(val) > -1) {
            $modal_test.find("input#unit-sign").val($(this).find("option:selected").text());
        } else {
            $modal_test.find("input#unit-sign").val('');
        }
    });

    /**
     * Add New Ref. Range
     */
    $modal_test.on("click", '.btn-new-ref-range', function (evt) {
        evt.preventDefault();
        add_new_ref_range();
    });

    /**
     * Delete Ref. Range
     */
    $modal_test.on("click", '.btn-remove-ref-range', function (evt) {
        evt.preventDefault();
        var num_rows = $modal_test.find("#tbl-ref-range tbody tr[type!=_template]").length;

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
        num_rows = $modal_test.find("#tbl-ref-range tbody tr[type!=_template]").length;
        if (num_rows == 1) {
            $modal_test.find("#tbl-ref-range tbody tr:eq(1) .btn-remove-ref-range").hide();
        } else {
            $modal_test.find("#tbl-ref-range tbody tr .btn-remove-ref-range").show();
        }

        $modal_test.find("#tbl-ref-range tbody tr[type!=_template] .btn-new-ref-range").hide();
        $modal_test.find("#tbl-ref-range tbody tr:last-child .btn-new-ref-range").show();
    });

    /**
     * Select Organism Row and add selected antibiotic data
     */
    $modal_test.find("#tbl-organism").on("click", "tbody tr.organism", function (evt, selectRow, action) {
        evt.preventDefault();
        selectRow	= selectRow == undefined ? false : selectRow;

        //set selected antibiotic to previous selected organism (row)
        var selected_antibiotic = getSelectedAntibiotic();
        var cur_row = $modal_test.find("#tbl-organism").data("cur_row");
        if (cur_row != undefined && selected_antibiotic.length > 0) {
            cur_row.data("antibiotic", selected_antibiotic);
        }

        //set data only (don't change state)
        if (action == "update") return false;

        //Change selected state
        if (selectRow) {
            $(this).addClass("selected");
        } else {
            $(this).toggleClass("selected");
        }
        $(this).siblings("tr.organism").removeClass("selected");

        var is_checked = $(this).find("input:checkbox.organism-value").is(":checked");
        if ($(this).hasClass("selected") && is_checked) {
            $modal_test.find("#antibiotic-wrapper").fadeIn(200);

            //set current selected organism row
            $modal_test.find("#tbl-organism").data("cur_row", $(this));

            //clear antibiotic
            $modal_test.find("ul#antibiotic-list").find("input[type=checkbox].antibiotic").iCheck("uncheck");

            //set previous antibiotic
            var antibiotic = $(this).data("antibiotic");
            if (antibiotic != undefined && antibiotic.length > 0) {
                $modal_test.find("#antibiotic-list").find("input:checkbox.antibiotic").iCheck('uncheck');
                for(var i in antibiotic) {
                    $modal_test.find("input#anti-" + antibiotic[i]).iCheck('check');
                }
            }

            if ($modal_test.find("#show-selected-antibiotic").is(":checked")) $modal_test.find("#show-selected-antibiotic").iCheck('uncheck').iCheck('check');
        } else {
            $modal_test.find("#antibiotic-wrapper").fadeOut(200);
            $modal_test.find("#tbl-organism").removeData("cur_row");
            $modal_test.find("ul#antibiotic-list").find("input[type=checkbox].antibiotic").iCheck("uncheck");
        }
    });

    $modal_test.on("shown.bs.modal", function () {
        $(this).find("#tbl-organism").find("tbody input:checkbox.organism-value").on("ifChanged", function () {
            var row = $(this).closest("tr.organism");
            if ($(this).is(":checked")) {
                row.trigger("click", [true]);
            } else {
                row.trigger("click");
            }
        });
    });

    $modal_test.on("hidden.bs.modal", function () {
        $(this).find("#tbl-organism").find("tbody input:checkbox.organism-value").off("ifChanged");
        clearTestForm();
    });

    /**
     * Filter Organism
     */
    $modal_test.find("#organsim-filter").on("keyup", function () {
        var val     = $(this).val();
        var regExp	= new RegExp(val, "i");
        var show_selected_only = $modal_test.find("input:checkbox#show-selected-organism").is(':checked');

        $modal_test.find("#tbl-organism").find("tbody tr.organism").each(function () {
            var is_check = $(this).find("input:checkbox.organism-value").is(':checked');
            var name = $(this).find("span.organism-name").text().trim();
            var name_matched = regExp.test(name);
            if ((name_matched && !show_selected_only) || (name_matched && show_selected_only && is_check)) {
                $(this).show();
            } else if ((!name_matched && !show_selected_only) || (!name_matched && show_selected_only && is_check)) {
                $(this).hide();
            }
        });
    });

    $modal_test.find("input:checkbox#show-selected-organism").on("ifChanged", function () {
        if ($(this).is(":checked")) {
            $modal_test.find("#tbl-organism tr.organism").each(function () {
                var is_checked = $(this).find("input:checkbox.organism-value").is(":checked");
                if (is_checked) $(this).show();
                else $(this).hide();
            });
        } else {
            $modal_test.find("#tbl-organism tr.organism").show();
        }

        if ($modal_test.find("#organsim-filter").val().trim().length > 0) $modal_test.find("#organsim-filter").trigger("keyup");
    });

    /**
     * Filter Antibiotic
     */
    $modal_test.find("#antibiotic-filter").on("keyup", function () {
        var val     = $(this).val();
        var regExp	= new RegExp(val, "i");
        var show_selected_only = $modal_test.find("input:checkbox#show-selected-antibiotic").is(':checked');

        $modal_test.find("ul#antibiotic-list li").each(function () {
            var is_check = $(this).find("input.antibiotic").is(':checked');
            var name = $(this).find("label.control-label span.antibiotic_name").text().trim();
            var name_matched = regExp.test(name);
            if ((name_matched && !show_selected_only) || (name_matched && show_selected_only && is_check)) {
                $(this).show();
            } else if ((!name_matched && !show_selected_only) || (!name_matched && show_selected_only && is_check)) {
                $(this).hide();
            }
        });
    });

    $modal_test.find("input:checkbox#show-selected-antibiotic").on("ifChanged", function () {
        if ($(this).is(":checked")) {
            $modal_test.find("#antibiotic-list li").hide();
            $modal_test.find("#antibiotic-list input.antibiotic:checked").each(function () {
                $(this).closest("li").show();
            });
        } else {
            $modal_test.find("#antibiotic-list li").show();
        }

        if ($modal_test.find("#antibiotic-filter").val().trim().length > 0) $modal_test.find("#antibiotic-filter").trigger("keyup");
    });

    /****************************************** Copy Data ******************************************/
    $modal_copy_data.find("select.sample-test").select2();
    $modal_copy_data.find("select.organism").select2();

    /**
     * Show Copy Modal
     */
    $("#copy-data").on("click", function (evt) {
        evt.preventDefault();
        $sample_test = $modal_copy_data.find("select.sample-test");
        $modal_copy_data.find("input[name=type][value=1]").iCheck('check');
        $sample_test.find("option[value!=-1]").remove();

        $.ajax({
            url      : base_url + 'test/get_std_sample_test',
            type     : 'POST',
            data     : {is_heading : 0, field_type : [1, 2] },
            dataType : 'json',
            success  : function(resText) {
                for (var i in resText) {
                    var selected = "";

                    if ([1, 2].indexOf(parseInt(resText[i].field_type)) > -1) {
                        $opt = $("<option value='" + resText[i].sample_test_id + "' " + selected + ">" + resText[i].department_name + "&nbsp; &rArr; &nbsp;" + resText[i].sample_name + "&nbsp; &rArr; &nbsp;" + resText[i].test_name + "</option>");
                        $sample_test.append($opt);
                    }
                }
            },
            error : function() {
                $sample_test.find("option[value!=-1]").remove();
            }
        });

        $modal_copy_data.modal({ backdrop : 'static' });
    });

    /**
     * Get Organism
     */
    $modal_copy_data.find("select.sample-test").on("change", function () {
        var val = Number($(this).val());
        var type = Number($modal_copy_data.find("input[type=radio][name=type]:checked").val());
        var $organism = $modal_copy_data.find("select.organism.copy-from");
        if ($(this).hasClass('copy-to')) $organism = $modal_copy_data.find("select.organism.copy-to");

        $organism.find("option[value!=-1]").remove();

        //Disabled Option
        if ($(this).hasClass("copy-from") && type == 1) {
            $modal_copy_data.find("select.sample-test.copy-to option").prop("disabled", false);
            if (val > 0) $modal_copy_data.find("select.sample-test.copy-to option[value=" + val + "]").prop("disabled", true);
            $modal_copy_data.find("select.sample-test.copy-to").select2();
        } else if ($(this).hasClass("copy-to") && type == 1) {
            $modal_copy_data.find("select.sample-test.copy-from option").prop("disabled", false);
            if (val > 0) $modal_copy_data.find("select.sample-test.copy-from option[value=" + val + "]").prop("disabled", true);
            $modal_copy_data.find("select.sample-test.copy-from").select2();
        }

        if (type == 2 && val > 0) {
            $.ajax({
                url: base_url + 'organism/get_sample_test_organism',
                type: 'POST',
                data: {sample_test_id: val},
                dataType: 'json',
                success: function (resText) {
                    for (var i in resText) {
                        var selected = "";
                        $opt = $("<option value='" + resText[i].test_organism_id + "' " + selected + ">" + resText[i].organism_name + "</option>");
                        $organism.append($opt);
                    }
                    $modal_copy_data.find("select.organism.copy-from").trigger("change");
                },
                error: function () {
                    $organism.find("option[value!=-1]").remove();
                }
            });
        }
    });

    /**
     * Diabled Selected Organism
     */
    $modal_copy_data.find("select.organism").on("change", function () {
        var val = $(this).val();
        //Disabled Option
        if ($(this).hasClass("copy-from")) {
            $modal_copy_data.find("select.organism.copy-to option").prop("disabled", false);
            if (val > 0) $modal_copy_data.find("select.organism.copy-to option[value=" + val + "]").prop("disabled", true);
            $modal_copy_data.find("select.organism.copy-to").select2();
        } else if ($(this).hasClass("copy-to")) {
            $modal_copy_data.find("select.organism.copy-from option").prop("disabled", false);
            if (val > 0) $modal_copy_data.find("select.organism.copy-from option[value=" + val + "]").prop("disabled", true);
            $modal_copy_data.find("select.organism.copy-from").select2();
        }
    });

    /**
     * Show Organism Select Box
     */
    $modal_copy_data.find("input[type=radio][name=type]").on("ifChecked", function () {
        var type = Number($(this).val());
        $modal_copy_data.find("select.organism").find("option[value!=-1]").remove();

        if (type == 1) {
            $modal_copy_data.find("select.sample-test.copy-from").trigger("change");
            $modal_copy_data.find("select.sample-test.copy-to").val(-1).trigger("change");
            $modal_copy_data.find("div.type-antibiotic").fadeOut();
        }
        else {
            $modal_copy_data.find("select.sample-test.copy-to").val(-1).trigger("change");
            $modal_copy_data.find("select.sample-test.copy-to option").prop("disabled", false);
            $modal_copy_data.find("select.sample-test.copy-from option").prop("disabled", false);
            $modal_copy_data.find("select.sample-test.copy-to, #modal-copy-data select.sample-test.copy-from").select2();
            $modal_copy_data.find("select.sample-test.copy-from").trigger("change");
            $modal_copy_data.find("div.type-antibiotic").fadeIn();
        }
    });

    /**
     * Copy Antibiotic/Organism
     */
    $modal_copy_data.find("#btnCopy").on("click", function (evt) {
        evt.preventDefault();
        var data                 = {};
        data.src_sample_test_id     = Number($modal_copy_data.find("select.sample-test.copy-from").val());
        data.target_sample_test_id  = Number($modal_copy_data.find("select.sample-test.copy-to").val());
        data.src_organism           = Number($modal_copy_data.find("select.organism.copy-from").val());
        data.target_organism        = Number($modal_copy_data.find("select.organism.copy-to").val());
        data.type                   = Number($modal_copy_data.find("input[type=radio][name=type]:checked").val());
        data.src_sample_test_id     = isNaN(data.src_sample_test_id) ? null : data.src_sample_test_id;
        data.target_sample_test_id  = isNaN(data.target_sample_test_id) ? null : data.target_sample_test_id;
        data.src_organism           = isNaN(data.src_organism) ? null : data.src_organism;
        data.target_organism        = isNaN(data.target_organism) ? null : data.target_organism;
        data.type                   = isNaN(data.type) ? null : data.type;

        var sample_test_condition = (data.src_sample_test_id <= 0 || data.target_sample_test_id <= 0);
        var organism_condition    = (data.src_organism <= 0 || data.target_organism <= 0);

        if ((data.type == 1 && sample_test_condition) || (data.type == 2 && (sample_test_condition || organism_condition) ) ) {
            myDialog.showDialog('show', { text : msg_fill_required_data, style : 'warning' });
            return false;
        }

        if ((data.type == 1 && data.src_sample_test_id == data.target_sample_test_id) || (data.type == 2 && data.src_organism == data.target_organism)) {
            myDialog.showDialog('show', { text : msg_diff_copy, style : 'warning' });
            return false;
        }

        var url = base_url + 'organism/copy_sample_test_organism';
        if (data.type == 2) {
            url = base_url + 'antibiotic/copy_sample_test_antibiotic';
        }

        //Show Progress
        myDialog.showProgress('show', { text : msg_loading });

        $.ajax({
            url     : url,
            type    : 'POST',
            data    : data,
            dataType: 'json',
            success : function (resText) {
                myDialog.showProgress('hide');
                myDialog.showDialog('show', { text : resText.msg, style : resText.status > 0 ? 'success' : 'warning' });
                if (resText.status > 0) {
                    $modal_copy_data.modal("hide");
                }
            },
            error   : function () {
                myDialog.showProgress('hide');
                myDialog.showDialog('show', { text : msg_copy_fail, style : 'warning' });
            }
        });
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
        tb_test.ajax.reload();

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
        tb_test.ajax.reload();
    });
});

/**
 * Get Selected Antibiotic
 * @returns {Array}
 */
function  getSelectedAntibiotic() {
    var antibiotic = [];
    $("#modal-test").find("ul#antibiotic-list li").each(function () {
        var checkbox = $(this).find("input[type=checkbox].antibiotic");

        if (checkbox.is(":checked")) {
            antibiotic.push(Number(checkbox.attr("value")));
        }
    });

    return antibiotic;
}

/**
 * Add New Ref. Range.
 * @param _data
 */
function add_new_ref_range(_data) {
    var $tbl_range_body = $("#modal-test").find("#tbl-ref-range tbody");
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
        row.find("select.range-sign").val(_data.range_sign);
    }
}

function clearTestForm() {
    //Clear Form
    var $modal_test = $("#modal-test");
    $modal_test.find("#test-name").val('');
    $modal_test.find("#test-list").val('').trigger("change");
    $modal_test.find("#group-result-name").val('');
    $modal_test.find("#unit-sign").val('');
    $modal_test.find("#test-order").val('');
    $modal_test.find("#field-type").val(-1);
    $modal_test.find("#is-heading").iCheck('uncheck');
    $modal_test.find("#is-default-selected").iCheck('uncheck');
    $modal_test.find("#test-group").val(-1);
    $modal_test.find("#department").val(-1).trigger("change");
    $modal_test.find("#sample-type").val(-1).trigger("change");
    $modal_test.find("#test-group option[value!=-1]").remove();

    //remove ref.range
    $modal_test.find("#tbl-ref-range tbody tr[type!=_template]").remove();
    //Add Ref. Range
    $modal_test.find(".btn-new-ref-range").trigger("click");

    //Clear Organism/Antibiotic
    $modal_test.find("#tbl-organism").removeData("cur_row");
    $modal_test.find("#tbl-organism tbody tr.organism").removeClass("selected");
    $modal_test.find("#tbl-organism tbody tr.organism").removeData("antibiotic");
    $modal_test.find("#tbl-organism tbody").find("input:checkbox.organism-value").iCheck("uncheck");
    $modal_test.find("#antibiotic-wrapper").hide();
    $modal_test.find("ul#antibiotic-list").find("input[type=checkbox].antibiotic").iCheck("uncheck");

    //Clear Filter
    $modal_test.find("#organsim-filter").val('');
    $modal_test.find("#antibiotic-filter").val('');
    if ($modal_test.find("#show-selected-organism").is(":checked")) {
        $modal_test.find("#show-selected-organism").iCheck('uncheck');
    } else {
        $modal_test.find("#tbl-organism tr.organism").show();
    }
    if ($modal_test.find("#show-selected-antibiotic").is(":checked")) {
        $modal_test.find("#show-selected-antibiotic").iCheck('uncheck');
    } else {
        $modal_test.find("ul#antibiotic-list li").show();
    }


    $modal_test.find("#btn-cancel-test-name").trigger("click");

    //clear data
    $modal_test.removeData("sample_test_id");
}
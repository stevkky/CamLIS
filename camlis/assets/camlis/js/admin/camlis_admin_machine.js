$(function() {
    var $modal_machine = $("#modal-machine");
    var $tbl_machine = $("#tbl-machine");
    //Bootstrap Tooltip
    $('[data-toggle="tooltip"]').tooltip();
    //Initialize iCheck
    $("input").iCheck({ checkboxClass: 'icheckbox_minimal', radioClass: 'iradio_minimal' });
    /*********** multiselect ******/
    var $option = {
        'buttonWidth': '100%',
        'buttonClass': 'form-control text-left custom-multiselect',
        'includeSelectAllOption': true,
        'enableFiltering': true,
        'filterPlaceholder': '',
        'selectAllText': 'All',
        'nonSelectedText': 'Choose machine',
        'nSelectedText': 'machines',
        'allSelectedText': 'All selected',
        'numberDisplayed': 1,
        'selectAllNumber': false,
        'templates': {
            ul: '<ul class="multiselect-container dropdown-menu custom-multiselect-container"></ul>',
            filter: '<li class="multiselect-item filter"><input class="form-control input-sm multiselect-search" type="text"></li>',
        }
    }
    /**
    * click on machine
    */
    $option.onChange = function (element, checked) {
        var selected = [];
        $modal_machine.find('#machine_name option:selected').each(function() {
            selected.push($(this).val());
        });
        (selected.length === 1) ? $modal_machine.find("#btn-edit-machine-name").prop("disabled", false) : $modal_machine.find("#btn-edit-machine-name").prop("disabled", true);

        $.ajax({
            url: base_url + 'machine_test/get_test_by_machine_lab',
            type: 'POST',
            dataType: 'json',
            data: {
                machine_id : JSON.stringify(selected),
                lab_id : LABORATORY_SESSION.labID,
            },
            success: function(response){
                $modal_machine.find('#sample_test').multiselect('deselectAll', false).multiselect('updateButtonText');
                response.forEach(function(data) {
                    $modal_machine.find('#sample_test option').each(function() {
                        (data.std_sample_test_id === $(this).val()) ? $modal_machine.find('#sample_test').multiselect('select', $(this).val()) : "";
                    });
                });
            },
            error: function(data){
                $modal_machine.find('#sample_test').multiselect('deselectAll', false).multiselect('updateButtonText');
            }
        });
    };

    $modal_machine.find("select.machine-name").multiselect($option);
    //clear the modal
    $modal_machine.on('hidden.bs.modal', function () {
        $modal_machine.find('#machine_name').multiselect('deselectAll', false).multiselect('updateButtonText');
        $modal_machine.find('#sample_test').multiselect('deselectAll', false).multiselect('updateButtonText');
    });
    /**
    * Show machine Modal
    */
    $("#machine-test").on("click", function (evt) {
        evt.preventDefault();
        myDialog.showProgress('show', { text : msg_loading });
        $sample_test = $modal_machine.find("select.sample-test");
        $.ajax({
            url : base_url + 'machine_test/get_all_tests',
            type : 'POST',
            data : {},
            dataType : 'json',
            success : function(response) {
                for (var i in response) {
                    $opt = $("<option value='"+response[i].ID +"'>"+response[i].department_name+"=>"+response[i].sample_name+"=>"+response[i].test_name+"</option>");
                    $sample_test.append($opt);
                }
                $option.nonSelectedText = "Choose test";
                $option.nSelectedText = "tests";
                $option.onChange = function (element, checked) {
                    $modal_machine.find('#sample_test').multiselect('updateButtonText');
                };
                $modal_machine.find("select.sample-test").multiselect($option);
                myDialog.showProgress('hide');
            },
            error : function() {
                $sample_test.find("option[value!=-1]").remove();
                myDialog.showProgress('hide');
            }
        });
        $modal_machine.modal({ backdrop : 'static' });
    });

    /**
    * Show Input for Entry New machine name
    */
    $modal_machine.find("#btn-new-machine-name").on("click", function (evt) {
        evt.preventDefault();
        $modal_machine.find("#select-test-wrapper").hide();
        $modal_machine.find("#test-entry-wrapper").show();

        $modal_machine.find("#test-entry-wrapper").find("#machine").val('');
        $modal_machine.find("#test-entry-wrapper").find("#machine").focus();
        $modal_machine.find("#test-entry-wrapper").find("#machine").removeData("test_id");

        $modal_machine.find("#test-list").val(null).trigger("change");
        $modal_machine.find("#btn-delete-test-name").prop("disabled", true);
        $modal_machine.find("#btnSave").prop("disabled", true);
    });
    /**
    * Cancel new machine
    */
    $modal_machine.find("#btn-cancel-machine-name").on("click", function (evt) {
        evt.preventDefault();
        var machine_id = $modal_machine.find("#test-entry-wrapper").find("#machine").data("machine_id");
        if (machine_id != undefined && Number(machine_id) > 0) {
            $modal_machine.find("#machine_name").val(machine_id).trigger("change");
        } else {
            $modal_machine.find("#machine_name").val(null).trigger("change");
        }
        $modal_machine.find("#select-test-wrapper").show();
        $modal_machine.find("#test-entry-wrapper").hide();
        $modal_machine.find("#test-entry-wrapper").find("#machine_name").removeData("machine_id");
        $modal_machine.find("#btnSave").prop("disabled", false);
    });
    
    /**
    * Edit Machine
    */
    $modal_machine.find("#btn-edit-machine-name").on("click", function (evt) {
        evt.preventDefault();
        var machine = $modal_machine.find("#machine_name");
        var machine_id = Number(machine.val());
        if (!isNaN(machine_id) && machine_id > 0) {
            $modal_machine.find("#select-test-wrapper").hide();
            $modal_machine.find("#test-entry-wrapper").show();
            $modal_machine.find("#test-entry-wrapper").find("#machine").val(machine.find("option:selected").text());
            $modal_machine.find("#test-entry-wrapper").find("#machine").focus();
            $modal_machine.find("#btn-delete-test-name").prop("disabled", false);
            $modal_machine.find("#btnSave").prop("disabled", true);
            $modal_machine.find("#machine_name").val(null).trigger("change");
            //set Machine id
            $modal_machine.find("#test-entry-wrapper").find("#machine").removeData("machine_id");
            $modal_machine.find("#test-entry-wrapper").find("#machine").data("machine_id", machine_id);
        }
    });
    /**
    * Save/Update Machine Name
    */
    $modal_machine.find("#btn-save-machine-name").on("click", function (evt) {
        var machine_name = $modal_machine.find("#test-entry-wrapper").find("#machine").val().trim();
        var machine_id = $modal_machine.find("#test-entry-wrapper").find("#machine").data("machine_id");
        var data = {machine_name: machine_name};
        var url  = base_url + 'machine/create';

        if (machine_name.length === 0) {
            myDialog.showDialog('show', {text : 'Please input machine name', style : 'warning'});
            return false;
        }
    
        if (!isNaN(machine_id) && machine_id > 0) {
            url = base_url + "machine/update";
            data.machine_id = machine_id;
        }

        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'json',
            data: data,
            success: function(response){
                myDialog.showDialog('show', {text : response.msg, style : response.status > 0 ? 'success' : 'warning'});
                if (isNaN(machine_id) && response.status > 0 && response.data && response.data.id > 0) {
                    $modal_machine.find("#btn-cancel-machine-name").trigger("click");
                    $modal_machine.find("#select-test-wrapper").find('#machine_name').append("<option value='" + response.data.id + "'>" + machine_name + "</option>");
                    $modal_machine.find("#select-test-wrapper").find('#machine_name').multiselect('rebuild').multiselect('select', response.data.id);
                } else if (machine_id > 0 && response.status > 0) {
                    $modal_machine.find("#btn-cancel-machine-name").trigger("click");
                    $modal_machine.find("#select-test-wrapper").find('#machine_name').find("option[value="+machine_id+"]").text(machine_name);
                    $modal_machine.find("#select-test-wrapper").find('#machine_name').multiselect('rebuild').multiselect('select', machine_id);
                }
            },
            error: function(){}
        });
    });
    /*
    * Save machine test
    */
    $modal_machine.find("#btnSave").on("click", function (evt) {
        evt.preventDefault();
        var machines = new Array();
        var sample_tests = new Array();

        $modal_machine.find('#machine_name option:selected').each(function() {
            machines.push($(this).val());
        });

        $modal_machine.find('#sample_test option:selected').each(function() {
            sample_tests.push($(this).val());
        });
        
        if (sample_tests.length > 0 && machines.length > 0) {
            myDialog.showProgress('show', { text : msg_loading });
            $.ajax({
                url: base_url + 'machine_test/create',
                type: 'POST',
                dataType: 'json',
                data: {
                    lab_id : LABORATORY_SESSION.labID,
                    machine_id : JSON.stringify(machines),
                    sample_tests : JSON.stringify(sample_tests)
                },
                success: function(response) {
                    myDialog.showProgress('hide');
                    myDialog.showDialog('show', {text : response.msg, style : response.status > 0 ? 'success' : 'warning'});
                },
                error: function(response) {
                    myDialog.showProgress('hide');
                }
            });
        }
    });
    /* Datatable for list all machine test*/
    $tbl_machine.DataTable({
        "info": false,
    });

    /**
    * Edit Machine test
    */
    $tbl_machine.on("click", "a.edit", function (evt) {
        evt.preventDefault();
        $("#machine-test").trigger('click');
        $modal_machine.find('#machine_name').multiselect('select', $(this).attr('id'), true).multiselect('updateButtonText');
    });

    /**
    * Delete Machine test
    */
    $tbl_machine.on("click", "a.remove", function (evt) {
        evt.preventDefault();
        if (confirm('Do you want to delete?')) {
            myDialog.showProgress('show', { text : msg_loading });
            $.ajax({
                url: base_url + 'machine_test/delete',
                type: 'POST',
                dataType: 'json',
                data: {id: $(this).attr('id')},
                success: function(response){
                    myDialog.showProgress('hide');
                    myDialog.showDialog('show', {text : response.msg, style : response.status > 0 ? 'success' : 'warning'});
                },
                error: function(response){
                    myDialog.showProgress('hide');
                }
            }); 
        }
    });

});

/*
* Global function
*/
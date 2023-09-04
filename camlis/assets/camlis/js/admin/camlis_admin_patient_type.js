$(function() {
    //Initialize DataTable
    var tbl_patient_type = $("#tbl-patient-type").DataTable({
        "info"       : false,
        "processing" : true,
        "serverSide" : true,
        "ajax"       : {
            "url"    : base_url+'patient/view_std_patient_type',
            "type"   : 'POST'
        },
        "language"   : dataTableOption.language,
        "columns"    : [
            { "data" : "number" },
            { "data" : "type" },
            { "data" : "min_age_format" },
            { "data" : "range_sign" },
            { "data" : "max_age_format" },
            { "data" : "gender_format" },
            { "data" : "action" }
        ],
        "columnDefs": [
            { "targets": "_all", "className" : "text-middle" },
            { "targets": -1, "orderable": false, "searchable": false }
        ],
        "order": [[0, 'asc']]
    });

    //Initialize iCheck
    $("input").iCheck({ checkboxClass: 'icheckbox_minimal', radioClass: 'iradio_minimal' });

    //Add New Department
    $("#addNew").on("click", function(evt) {
        evt.preventDefault();

        //Label
        $("#modal-patient-type #btnSave span.save").show();
        $("#modal-patient-type #btnSave span.edit").hide();
        $("#modal-patient-type").find('.modal-title.new').show();
        $("#modal-patient-type").find('.modal-title.edit').hide();

        //Clear Form
        clearForm();

        $("#modal-patient-type").modal({ backdrop : 'static' });
    });
    
    $("#btnSave").on("click", function(evt) {
        evt.preventDefault();

        var _data ={
            patient_type  : $("#modal-patient-type #patient-type-name").val(),
            min_age       : $("#modal-patient-type #min-age").val(),
            min_age_unit  : $("#modal-patient-type #min-age-unit").val(),
            max_age       : $("#modal-patient-type #max-age").val(),
            max_age_unit  : $("#modal-patient-type #max-age-unit").val(),
            equal_max_age : $("#modal-patient-type #is-equal-max-age").val(),
            gender        : $("#modal-patient-type input[type=radio][name=gender]:checked").attr("value")
        };
        var url     = base_url + "patient/add_std_patient_type";
        console.log(_data);
        if (_data.patient_type.trim().length == 0) {
            myDialog.showDialog('show', {text : msg_required_data, style : 'warning'});
            return false;
        }
        
        if (Number(_data.min_age) > 0 && Number(_data.max_age) > 0 && (parseInt(_data.min_age) * parseInt(_data.min_age_unit) > parseInt(_data.max_age) * parseInt(_data.max_age_unit)) ) {
            myDialog.showDialog('show', {text : msg_valid_age_range, style : 'warning'});
            return false;
        }

        var ID = $("#modal-patient-type").data("patient_type_id");
        if (ID > 0) {
            url = base_url + "patient/update_std_patient_type";
            _data.patient_type_id = ID;
        }

        $.ajax({
            url      : url,
            type     : 'POST',
            data     : _data,
            dataType : 'json',
            success  : function(resText) {
                myDialog.showDialog('show', {text:resText.msg, style : resText.status > 0 ? 'success' : 'warning'});
                if (resText.status > 0) {
                    tbl_patient_type.ajax.reload();
                    $("#modal-patient-type").modal("hide");
                }
            },
            error : function () {
                myDialog.showDialog('show', {text: ID > 0 ? msg_update_fail : msg_save_fail, style : 'warning'});
            }
        });
    });
    
    $("#tbl-patient-type").on("click", ".edit", function(evt) {
        evt.preventDefault();
        $(this).blur();

        //label
        $("#modal-patient-type #btnSave span.save").hide();
        $("#modal-patient-type #btnSave span.edit").show();
        $("#modal-patient-type").find('.modal-title.new').hide();
        $("#modal-patient-type").find('.modal-title.edit').show();
        
        var data = tbl_patient_type.row($(this).parents("tr")).data();
        $("#modal-patient-type #patient-type-name").val(data.type);
        $("#modal-patient-type #min-age").val(data.min_age);
        $("#modal-patient-type #min-age-unit").val(data.min_age_unit);
        $("#modal-patient-type #max-age").val(data.max_age);
        $("#modal-patient-type #max-age-unit").val(data.max_age_unit);
        var is_equal_value = data.is_equal == true ? 1 : 0; // added 18 Dec 2020
        $("#modal-patient-type #is-equal-max-age").val(is_equal_value);
        $("#modal-patient-type input[type=radio][name=gender][value=" + data.gender + "]").iCheck('check');
        console.log(data);
        $("#modal-patient-type").removeData("patient_type_id");
        $("#modal-patient-type").data("patient_type_id", data.ID);
        $("#modal-patient-type").modal({ backdrop : 'static' });
    });
    
    $("#tbl-patient-type").on("click", ".remove", function(evt) {
        evt.preventDefault();
        
        var data = tbl_patient_type.row($(this).parents("tr")).data();
        
        if (confirm(q_delete_patient_type)) {
            $.ajax({
                url      : base_url + 'patient/delete_std_patient_type',
                type     : 'POST',
                data     : { patient_type_id : data.ID },
                dataType : 'json',
                success  : function(resText) {
                    myDialog.showDialog('show', {text:resText.msg, style : resText.status > 0 ? 'success' : 'warning'});
                    tbl_patient_type.ajax.reload();
                },
                error : function () {
                    myDialog.showDialog('show', {text: msg_delete_fail, style : 'warning'});
                }
            });
        }
    });
});

function clearForm() {
    $("#modal-patient-type #patient-type-name").val('');
    $("#modal-patient-type #min-age").val('');
    $("#modal-patient-type #max-age").val('');
    $("#modal-patient-type #min-age-unit").val(1);
    $("#modal-patient-type #max-age-unit").val(1);
    $("#modal-patient-type #is-equal-max-age").val(0);
    $("#modal-patient-type #is-equal-min-age").val(1);
    $("#modal-patient-type input[type=radio][name=gender][value=3]").iCheck('check');
    $("#modal-patient-type").removeData("patient_type_id");
}
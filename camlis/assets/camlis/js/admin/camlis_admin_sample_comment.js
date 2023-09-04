$(function() {
    //Initialize DataTable
    var tbl_sample_comment = $("#tbl-sample-comment").DataTable({
        "info"       : false,
        "processing" : true,
        "serverSide" : true,
        "ajax"       : {
            "url"    : base_url+'comment/view_std_sample_comment',
            "type"   : 'POST'
        },
        "language"   : dataTableOption.language,
        "columns"    : [
            { "data" : "number" },
            { "data" : "department_name" },
            { "data" : "sample_type" },
            { "data" : "comment" },
            { "data" : "is_reject_comment" },
            { "data" : "action" }
        ],
        "columnDefs": [
            { "targets": -1, "orderable": false, "searchable": false },
            { "targets": -2, "className" : "text-center text-middle", "width" : "70px", "render": function ( data, type, full, meta ) {
                return data == 1 ? '<i class="fa fa-check-circle text-red"></i>' : '';
            }},
            { "targets": "_all", "className" : "text-middle" }
        ],
        "order": [[0, 'asc']]
    });

    //Init Select 2
    $("#modal-sample-comment #department, #modal-sample-comment #sample-type").select2();
    $(":checkbox").iCheck({ checkboxClass: 'icheckbox_minimal', radioClass: 'iradio_minimal' });

    /**
     * Filter comment
     */
    $("#show-reject-comment").on("ifChanged", function () {
        //tbl_sample_comment.column(4).search($(this).is(":checked") ? 1 : "").draw();
        tbl_sample_comment.column(4).search($(this).is(":checked") ? true : "").draw();
    });

    //Get Sample base on Department
    $("#modal-sample-comment #department").on("change", function(evt, dep_sample_id) {
        evt.preventDefault();
        $sampleType = $("#modal-sample-comment").find("select#sample-type");
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
                    if (dep_sample_id != undefined && dep_sample_id.length > 0 && dep_sample_id.indexOf(parseInt(resText[i].department_sample_id)) > -1) {
                        selected = "selected";
                    }

                    $opt = $("<option value='" + resText[i].department_sample_id + "'"+selected+">" + resText[i].sample_name + "</option>");
                    $sampleType.append($opt);
                }
            },
            error : function(resText) {
                $sampleType.find("option").not("option[value=-1]").remove();
            }
        });
    });

    //Add New Department
    $("#addNew").on("click", function(evt) {
        evt.preventDefault();

        //Label
        $("#modal-sample-comment #btnSave span.save").show();
        $("#modal-sample-comment #btnSave span.edit").hide();
        $("#modal-sample-comment").find('.modal-title.new').show();
        $("#modal-sample-comment").find('.modal-title.edit').hide();

        //Clear Form
        clearForm();

        $("#modal-sample-comment").modal({ backdrop : 'static' });
    });

    //Add/Update
    $("#btnSave").on("click", function(evt) {
        evt.preventDefault();

        var _data = {
            department_id     : $("#modal-sample-comment #department").val(),
            sample_type       : $("#modal-sample-comment #sample-type").val(),
            comment           : $("#modal-sample-comment #sample-comment").val(),
            is_reject_comment : $("#modal-sample-comment #is-reject-comment").is(":checked") ? 1 : 0
        };

        if (_data.comment.trim().length == 0 || Number(_data.department_id) <= 0) {
            myDialog.showDialog('show', {text : msg_required_data, style : 'warning'});
            return false;
        }

        if (_data.sample_type == null || (_data.sample_type != null && _data.sample_type.length == 0)) {
            _data.sample_type = [];
            $("#modal-sample-comment #sample-type option").each(function () {
                var val = Number($(this).val());
                if (!isNaN(val) && val > 0) {
                    _data.sample_type.push(val);
                }
            });
        }

        var url     = base_url + "comment/add_std_comment";
        var ID = $("#modal-sample-comment").data("comment_id");
        if (ID > 0) {
            url = base_url + "comment/update_std_comment";
            _data.comment_id = ID;
        }
        console.log(_data);
        $.ajax({
            url      : url,
            type     : 'POST',
            data     : _data,
            dataType : 'json',
            success  : function(resText) {
                myDialog.showDialog('show', {text:resText.msg, style : resText.status > 0 ? 'success' : 'warning'});
                if (resText.status > 0) {
                    tbl_sample_comment.ajax.reload();
                    $("#modal-sample-comment").modal("hide");
                }
            },
            error : function () {
                myDialog.showDialog('show', {text: ID > 0 ? msg_update_fail : msg_save_fail, style : 'warning'});
            }
        });
    });
    
    $("#tbl-sample-comment").on("click", ".edit", function(evt) {
        evt.preventDefault();
        $(this).blur();

        //label
        $("#modal-sample-comment #btnSave span.save").hide();
        $("#modal-sample-comment #btnSave span.edit").show();
        $("#modal-sample-comment").find('.modal-title.new').hide();
        $("#modal-sample-comment").find('.modal-title.edit').show();

        var all_data = tbl_sample_comment.rows().data().toArray();
        var row_data = tbl_sample_comment.row($(this).parents("tr")).data();
        var sample_type = [];
        
       // console.log(row_data)
        //console.log(all_data)
        for(var i in all_data) {
            console.log(row_data.department_id+" "+all_data[i].department_id);
            if (row_data.department_id == all_data[i].department_id) {
                sample_type.push(parseInt(all_data[i].dep_sample_id));
            }
        }

        $("#modal-sample-comment #department").val(row_data.department_id).trigger("change", [sample_type]);
        $("#modal-sample-comment #sample-comment").val(row_data.comment);
        $("#modal-sample-comment #is-reject-comment").iCheck(row_data.is_reject_comment == 1 ? 'check' : 'uncheck');

        $("#modal-sample-comment").removeData("comment_id");
        $("#modal-sample-comment").data("comment_id", row_data.comment_id);
        $("#modal-sample-comment").modal({ backdrop : 'static' });
    });
    
    $("#tbl-sample-comment").on("click", ".remove", function(evt) {
        evt.preventDefault();
        
        var data = tbl_sample_comment.row($(this).parents("tr")).data();
        
        if (confirm(q_delete_comment)) {
            $.ajax({
                url      : base_url + 'comment/delete_std_comment',
                type     : 'POST',
                data     : { comment_id : data.comment_id },
                dataType : 'json',
                success  : function(resText) {
                    myDialog.showDialog('show', {text:resText.msg, style : resText.status > 0 ? 'success' : 'warning'});
                    tbl_sample_comment.ajax.reload();
                },
                error : function () {
                    myDialog.showDialog('show', {text: msg_delete_fail, style : 'warning'});
                }
            });
        }
    });
});

function clearForm() {
    $("#modal-sample-comment #department").val(-1).trigger("change");
    $("#modal-sample-comment #sample-type").val(null).trigger("change");
    $("#modal-sample-comment #sample-comment").val('');
    $("#modal-sample-comment").removeData("comment_id");
}
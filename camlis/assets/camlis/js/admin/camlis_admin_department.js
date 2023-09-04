$(function() {
    //Initialize DataTable
    var tbl_department = $("#tbl-department").DataTable({
        "info"       : false,
        "processing" : true,
        "serverSide" : true,
        "ajax"       : {
            "url"    : base_url+'department/view_std_department',
            "type"   : 'POST'
        },
        "language"   : dataTableOption.language,
        "columns"    : [
            { "data" : "number" },
            { "data" : "department_name" },
            { "data" : "action" }
        ],
        "columnDefs": [
            { "targets": "_all", "className" : "text-middle" },
            { "targets": -1, "orderable": false, "searchable": false }
        ],
        "order": [[0, 'asc']],
        "lengthMenu": [[10, 20, 40, 60, 80, 100], [10, 20, 40, 60, 80, 100]],
        "pageLength": 20
    });

    //Add New Department
    $("#addNew").on("click", function(evt) {
        evt.preventDefault();

        $("#modal-department #btnSave span.save").show();
        $("#modal-department #btnSave span.update").hide();
        $("#modal-department").find('.modal-title.new').show();
        $("#modal-department").find('.modal-title.edit').hide();
        $("#department-name").val('');
        $("#modal-department").removeData("ID");
        $("#modal-department").modal({ backdrop : 'static' });
    });
    
    $("#btnSave").on("click", function(evt) {
        evt.preventDefault();

        var department_name  = $("#department-name").val();
        var url     = base_url + "department/add_std_department";

        if (department_name.trim().length == 0) {
            myDialog.showDialog('show', {text:msg_required_data, style : 'warning'});
            return false;
        }

        var ID = $("#modal-department").data("ID");
        if (ID > 0) url = base_url + "department/update_std_department";

        $.ajax({
            url      : url,
            type     : 'POST',
            data     : { department_name : department_name, ID : ID },
            dataType : 'json',
            success  : function(resText) {
                myDialog.showDialog('show', {text:resText.msg, style : resText.status > 0 ? 'success' : 'warning'});
                if (resText.status > 0) {
                    tbl_department.ajax.reload();
                    $("#modal-department").modal("hide");
                }
            },
            error : function () {
                myDialog.showDialog('show', {text: ID > 0 ? msg_update_fail : msg_save_fail, style : 'warning'});
            }
        });
    });
    
    $("#tbl-department").on("click", ".edit", function(evt) {
        evt.preventDefault();
        $(this).blur();

        //label
        $("#modal-department #btnSave span.save").hide();
        $("#modal-department #btnSave span.update").show();
        $("#modal-department").find('.modal-title.new').hide();
        $("#modal-department").find('.modal-title.edit').show();

        var data = tbl_department.row($(this).parents("tr")).data();
        $("#department-name").val(data.department_name);

        $("#modal-department").removeData("ID");
        $("#modal-department").data("ID", data.ID);
        $("#modal-department").modal({ backdrop : 'static' });
    });
    
    $("#tbl-department").on("click", ".remove", function(evt) {
        evt.preventDefault();
        
        var data = tbl_department.row($(this).parents("tr")).data();
        
        if (confirm(q_delete_department)) {
            $.ajax({
                url      : base_url + 'department/delete_std_department',
                type     : 'POST',
                data     : { ID : data.ID },
                dataType : 'json',
                success  : function(resText) {
                    myDialog.showDialog('show', {text:resText.msg, style : resText.status > 0 ? 'success' : 'warning'});
                    tbl_department.ajax.reload();
                },
                error : function () {
                    myDialog.showDialog('show', {text: msg_delete_fail, style : 'warning'});
                }
            });
        }
    });
});
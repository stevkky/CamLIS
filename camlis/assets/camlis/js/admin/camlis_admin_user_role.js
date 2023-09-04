$(function() {
    var $modal_user_role  = $("#modal-user-role");
    var $tbl_user_role    = $("#tbl-user-role");
    var tbl_user_role     = $tbl_user_role.DataTable({
        "info"       : false,
        "processing" : true,
        "serverSide" : true,
        "language"   : dataTableOption.language,
        "ajax"       : {
            "url"    : base_url+'user/view_all_user_roles',
            "type"   : 'POST'
        },
        "columns"    : [
            { "data" : "number" },
            { "data" : "name" },
            { "data" : "definition" },
            { "data" : "default_page" },
            { "data" : "action" }
        ],
        "columnDefs" : [
            { "orderable" : false, "searchable" : false, "width" : "60px", "className" : "text-center text-middle no-wrap", "targets" : -1 },
            { "width" : "18px", "searchable" : false, "targets" : 0 }
        ],
        "order": [[0, 'asc']],
        "lengthMenu": [[10, 20, 40, 60, 80, 100], [10, 20, 40, 60, 80, 100]],
        "pageLength": 20
    });

    //Init iCheck
    $("input").iCheck({ checkboxClass: 'icheckbox_minimal', radioClass: 'iradio_minimal' });

    $modal_user_role.on("hidden.bs.modal", function () {
        $(this).find("input[name=name], input[name=definition], input[name=default_page]").val('');
        $(this).find("input[name=name], input[name=definition]").prop('disabled', false);
        $(this).find(":checkbox").iCheck('uncheck');
        $(this).removeData("GROUP_ID");
    });

    /**
     * Save/Update User
     */
    $("#btnSave").on("click", function (evt) {
        evt.preventDefault();
        var data = {
            group_id: $modal_user_role.data("GROUP_ID"),
            name: $modal_user_role.find("input[name=name]").val().trim(),
            definition: $modal_user_role.find("input[name=definition]").val().trim(),
            default_page: $modal_user_role.find("input[name=default_page]").val().trim(),
            permissions: []
        };

        $modal_user_role.find(":checkbox[name=permission]:checked").each(function () {
            var perm_id = $(this).val();
            if (perm_id > 0) data.permissions.push(perm_id);
        });

        if (data.name.length === 0 || data.definition.length === 0 || data.default_page.length === 0) {
            myDialog.showDialog('show', { text : msg_required_data, style : 'warning' });
            return false;
        }

        var url = base_url + "user/add_user_group";
        if (data.group_id > 0) url = base_url + "user/update_user_group";

        myDialog.showProgress("show");
        $.ajax({
            url      : url,
            type     : "POST",
            data     : data,
            dataType : 'json',
            success  : function (resText) {
                myDialog.showProgress("hide");
                myDialog.showDialog('show', { text : resText.msg, style : resText.status ? 'success' : 'warning' });
                if (resText.status) {
                    tbl_user_role.ajax.reload();
                    $modal_user_role.modal("hide");
                }
            },
            error    : function () {
                myDialog.showProgress("hide");
                myDialog.showDialog('show', {text : msg_save_fail, style : 'warning'});
            }
        });
    });

    /**
     * Edit User
     */
    $tbl_user_role.on("click", "a.edit", function (evt) {
        evt.preventDefault();
        var data = tbl_user_role.row($(this).closest("tr")).data();
        $modal_user_role.find("input[name=name]").val(data.name);
        $modal_user_role.find("input[name=definition]").val(data.definition);
        $modal_user_role.find("input[name=default_page]").val(data.default_page);
        $modal_user_role.find("input[name=name], input[name=definition]").prop('disabled', data.is_predefined);
        for(var i in data.permissions) {
            $modal_user_role.find(":checkbox#perm-" + data.permissions[i]).iCheck('check');
        }
        $modal_user_role.data("GROUP_ID", data.id);
        $modal_user_role.modal({backdrop : 'static'});
    });

    /**
     * Delete User
     */
    $tbl_user_role.on("click", "a.remove", function (evt) {
        evt.preventDefault();
        var data = tbl_user_role.row($(this).closest("tr")).data();
        if (confirm(q_delete_user_role)) {
            $.ajax({
                url      : base_url + "user/delete_user_group/" + data.id,
                type     : "POST",
                dataType : 'json',
                success  : function (resText) {
                    myDialog.showDialog('show', {text : resText.msg, style : resText.status ? 'success' : 'warning' });
                    if (resText.status) tbl_user_role.ajax.reload();
                },
                error    : function () {
                    myDialog.showDialog('show', {text : msg_delete_fail, style : 'warning'});
                }
            })
        }
    })
});
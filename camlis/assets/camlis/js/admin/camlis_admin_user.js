$(function() {
    var $modal_user_form  = $("#modal-user-form");
    var $modal_laboratory = $("#modal-laboratory");
    var $modal_user_group = $("#modal-groups");
    var $tbl_user         = $("#tbl-user");
    var tbl_user          = $tbl_user.DataTable({
        "filter"		: true,
        "info"       : false,
        'paging'    : false,
        "bPaginate"	 : false,
        "processing" : true,
        "serverSide" : true,
        "language"   : dataTableOption.language,
        "ajax"       : {
            "url"    : base_url+'user/view_all_user',
            "type"   : 'POST',
            "data"   : function (d) {
                d.current_laboratory = $tbl_user.data("laboratory");
            }
        },
        "columns"    : [
            { "data" : "number" },
            { "data" : "fullname" },
            { "data" : "username" },
            { "data" : "email" },
            { "data" : "phone" },
            { "data" : "action" }
        ],
        "columnDefs" : [
            { "orderable" : false, "searchable" : false, "width" : "100px", "className" : "text-center text-middle no-wrap", "targets" : -1 },
            { "width" : "18px", "searchable" : false, "targets" : 0 }
        ],
        "order": [[0, 'asc']],
        "lengthMenu": [[10, 20, 40, 60, 80, 100], [10, 20, 40, 60, 80, 100]],
        "pageLength": 20
    });

    //Init iCheck
    $("input").iCheck({ checkboxClass: 'icheckbox_minimal', radioClass: 'iradio_minimal' });

    //Assign Laboratory
    $modal_laboratory.find("#btnAssign").on("click", function(evt) {
        evt.preventDefault();
        
        var laboratories = [];
        $modal_laboratory.find(".modal-body input[type=checkbox][name=lab]:checked").each(function() {
            laboratories.push($(this).attr("value"));
        });

        $.ajax({
            url      : base_url + "user/assign_user_laboratory",
            type     : 'POST',
            data     : { laboratories : laboratories, user_id :  $modal_laboratory.data("user_id") },
            dataType : 'json',
            success  : function(resText) {
                myDialog.showDialog('show', {text : resText.msg, status : '', style : resText.status > 0 ? 'success' : 'warning'});
                $("#modal-laboratory").modal("hide");
            },
            error   : function () {
                myDialog.showDialog('show', {text :  msg_save_fail, style : 'warning'});
                $("#modal-laboratory").modal("hide");
            }
        });
    });

    /**
     * Assign Lab to User
     */
    $tbl_user.on("click", ".assign_lab", function(evt) {
        evt.preventDefault();
        $(this).blur();
        $modal_laboratory.find(".modal-body input[type=checkbox][name=lab]").iCheck('uncheck');
        var data = tbl_user.row($(this).parents("tr")).data();
        
        $.ajax({
            url      : base_url + "user/get_user_laboratory",
            type     : 'POST',
            data     : { user_id :  data.user_id },
            dataType : 'json',
            success  : function(resText) {
                $modal_laboratory.find(".modal-body input[type=checkbox][name=lab]").each(function() {
                    if (resText.indexOf(parseInt($(this).val())) >= 0) {
                        $(this).iCheck('check');
                    }
                });
                $modal_laboratory.modal({ backdrop : 'static' });
            }
        });

        $modal_laboratory.removeData("user_id");
        $modal_laboratory.data("user_id", data.user_id);
    });

    /**
     * Open User Group Modal
     */
    $tbl_user.on("click", ".set_group", function(evt) {
        evt.preventDefault();
        $(this).blur();

        $modal_user_group.find(".modal-body input[type=checkbox][name=group]").iCheck('uncheck');
        var data = tbl_user.row($(this).parents("tr")).data();

        myDialog.showProgress("show");
        $.ajax({
            url      : base_url + "user/get_user_groups",
            type     : 'POST',
            data     : { user_id :  data.user_id },
            dataType : 'json',
            success  : function(resText) {
                console.log(JSON.stringify(resText));
                myDialog.showProgress("hide");

                var groups = [];
                for (var i in resText) {
                    groups.push(resText[i].group_id);
                }
                
                $modal_user_group.find(".modal-body input[type=checkbox][name=group]").each(function() {
                   /*
                    if (groups.indexOf($(this).val()) > -1) {
                        $(this).iCheck('check');  
                    }
                    */
                   // added 14 Dec 2020
                    if (groups.indexOf(parseInt($(this).val())) > -1) {
                        $(this).iCheck('check');  
                    }
                });
                $modal_user_group.modal({ backdrop : 'static' });
            },
            error    : function () {
                myDialog.showProgress("hide");
            }
        });

        $modal_user_group.removeData("user_id");
        $modal_user_group.data("user_id", data.user_id);
    });

    /**
     * Assign User to group
     */
    $modal_user_group.find("#btnSetGroup").on("click", function(evt) {
        evt.preventDefault();

        var groups = [];
        $modal_user_group.find(".modal-body input[type=checkbox][name=group]:checked").each(function() {
            groups.push($(this).attr("value"));
        });

        $.ajax({
            url      : base_url + "user/assign_user_group",
            type     : 'POST',
            data     : { groups : groups, user_id :  $modal_user_group.data("user_id") },
            dataType : 'json',
            success  : function(resText) {
                myDialog.showDialog('show', {text : resText.msg, style : resText.status > 0 ? 'success' : 'warning'});
                $modal_user_group.modal("hide");
            },
            error   : function () {
                myDialog.showDialog('show', {text :  msg_save_fail, style : 'warning'});
            }
        });
    });

    /**
     * Show New User Form
     */
    $("#btn-new-user").on("click", function (evt) {
        evt.preventDefault();
        $modal_user_form.find("input").val('');
        $modal_user_form.removeData("user_id");
        $modal_user_form.modal({backdrop : 'static'});
    })

    /**
     * Save/Update User
     */
    $("#btnSaveUser").on("click", function (evt) {
        evt.preventDefault();
        var data = {
            user_id  : $modal_user_form.data("user_id"),
            fullname : $modal_user_form.find("#fullname").val().trim(),
            username : $modal_user_form.find("#username").val().trim(),
            email    : $modal_user_form.find("#email").val().trim(),
            phone    : $modal_user_form.find("#phone").val().trim(),
            location : $modal_user_form.find("#location").val().trim(),
            province : $modal_user_form.find("select[name=province] option:selected").val(),
            password : $modal_user_form.find("#password").val().trim(),
            confirm_password : $modal_user_form.find("#confirm-password").val().trim(),
            laboratory : $tbl_user.data("laboratory")
        };

        if (data.fullname.length == 0 || data.username.length == 0 || ((data.password.length == 0 || data.confirm_password.length == 0) && data.user_id == undefined )) {
            myDialog.showDialog('show', { text : msg_required_data, style : 'warning' });
            return false;
        }

        if (data.password.length < 5 || data.password.length > 20) {
            myDialog.showDialog('show', { text : msg_password_criteria, style : 'warning' });
            return false;
        }
        if (data.province == '-1'){
            myDialog.showDialog('show', { text : msg_required_data, style : 'warning' });
            return false;
        }
        var match_password = data.password == data.confirm_password;
        if ((!match_password && data.user_id == undefined) || (data.user_id > 0 && (data.password.length > 0 || data.confirm_password.length > 0) && !match_password) ) {
            myDialog.showDialog('show', { text : msg_password_not_match, style : 'warning' });
            return false;
        }

        var url = base_url + "user/save";
        if (data.user_id > 0) url = base_url + "user/update";

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
                    tbl_user.ajax.reload();
                    $modal_user_form.modal("hide");
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
    $tbl_user.on("click", "a.edit", function (evt) {
        evt.preventDefault();
        var data = tbl_user.row($(this).closest("tr")).data();
        $modal_user_form.find("input").val('');
        $modal_user_form.find("#fullname").val(data.fullname);
        $modal_user_form.find("#username").val(data.username);
        $modal_user_form.find("#email").val(data.email);
        $modal_user_form.find("#phone").val(data.phone);
        
        $modal_user_form.data("user_id", data.user_id);
        //added 24-04-2021
        // get Location and province of user
        $.ajax({
            url      : base_url + "user/get_province",
            type     : "POST",
            data     : { user_id : data.user_id },
            dataType : 'json',
            success  : function (resText) {
               // console.log(resText);
                $modal_user_form.find("#location").val(resText.location);
                $modal_user_form.find("select[name=province]").val(resText.province_code);
            },
            error    : function () {
                myDialog.showDialog('show', {text : msg_delete_fail, style : 'warning'});
            }
        })
        //
        $modal_user_form.modal({backdrop : 'static'});
    });

    /**
     * Delete User
     */
    $tbl_user.on("click", "a.remove", function (evt) {
        evt.preventDefault();
        var data = tbl_user.row($(this).closest("tr")).data();
        if (confirm(q_delete_user)) {
            $.ajax({
                url      : base_url + "user/delete",
                type     : "POST",
                data     : { user_id : data.user_id },
                dataType : 'json',
                success  : function (resText) {
                    myDialog.showDialog('show', {text : resText.msg, style : resText.status ? 'success' : 'warning' });
                    if (resText.status) tbl_user.ajax.reload();
                },
                error    : function () {
                    myDialog.showDialog('show', {text : msg_delete_fail, style : 'warning'});
                }
            })
        }
    })
});
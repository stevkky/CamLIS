$(function() {
    //Initialize DataTable
    var tbl_payment_type = $("#tbl-payment-type").DataTable({
        "info"       : false,
        "processing" : true,
        "serverSide" : true,
        "ajax"       : {
            "url"    : base_url+'payment_type/view_lab_payment_type',
            "type"   : 'POST'
        },
        "language"   : dataTableOption.language,
        "columns"    : [
            { "data" : "number" },
            { "data" : "name" },
            { "data" : "action" }
        ],
        "columnDefs": [
            { "targets": -1, "orderable": false, "searchable": false, "className": "text-center" }
        ],
        "order": [[1, 'asc']]
    });

    $("input[name=payment_type]").iCheck({ checkboxClass: 'icheckbox_minimal', radioClass: 'iradio_minimal' });

    //Assign lab payment type
    $("#assign-payment-type").on("click", function(evt) {
        evt.preventDefault();
        $("#modal-payment-type").find(":checkbox[name=payment_type]").iCheck('uncheck');
        myDialog.showProgress('show');
        $.ajax({
            url: base_url + "payment_type/get_lab_payment_type",
            type: 'POST',
            dataType: 'json',
            success: function(resText) {
                myDialog.showProgress('hide');

                for(var i in resText) {
                    var $cb = $("#modal-payment-type").find(":checkbox#payment-type-" + resText[i].id);
                    if ($cb.length > 0) $cb.iCheck('check');
                }

                $("#modal-payment-type").modal({ backdrop : 'static' });
            },
            error: function () {
                myDialog.showProgress('hide');
            }
        });
    });
    
    $("#btnSave").on("click", function(evt) {
        evt.preventDefault();

        var assigned_payment_type = [];
        $("#modal-payment-type").find(":checkbox[name=payment_type]").each(function () {
            if ($(this).is(":checked") && $(this).val() > 0) {
                assigned_payment_type.push($(this).val());
            }
        });

        $.ajax({
            url      : base_url + "payment_type/assign_lab_payment_type",
            type     : 'POST',
            data     : { payment_type: assigned_payment_type },
            dataType : 'json',
            success  : function(resText) {
                myDialog.showDialog('show', {text:resText.msg, style : resText.status > 0 ? 'success' : 'warning'});
                if (resText.status) {
                    tbl_payment_type.ajax.reload();
                    $("#modal-payment-type").modal("hide");
                }
            },
            error : function () {
                myDialog.showDialog('show', {text: globalMessage.save_fail, style : 'warning'});
            }
        });
    });
    
    $("#tbl-payment-type").on("click", ".remove", function(evt) {
        evt.preventDefault();
        
        var data = tbl_payment_type.row($(this).parents("tr")).data();
        
        if (confirm(q_delete_payment_type)) {
            $.ajax({
                url      : base_url + 'payment_type/delete_lab_payment_type',
                type     : 'POST',
                data     : { id : data.lab_payment_type_id },
                dataType : 'json',
                success  : function(resText) {
                    myDialog.showDialog('show', {text:resText.msg, style : resText.status > 0 ? 'success' : 'warning'});
                    tbl_payment_type.ajax.reload();
                },
                error : function () {
                    myDialog.showDialog('show', {text: globalMessage.delete_fail, style : 'warning'});
                }
            });
        }
    });
});
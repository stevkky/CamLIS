$(function() {
    /**================================== Organism ==================================**/
    var $tbl_organism   = $("#tbl-organism");
    var $modal_organism = $("#modal-organism");
    //Initialize DataTable
    var tbl_organism = $tbl_organism.DataTable({
        "info"       : false,
        "processing" : true,
        "serverSide" : true,
        "ajax"       : {
            "url"    : base_url+'organism/view_std_organism',
            "type"   : 'POST'
        },
        "language"   : dataTableOption.language,
        "retrieve"   : true,
        "columns"    : [
            { "data" : "number" },
            { "data" : "order","searchable": false },
            { "data" : "organism_name" },
            { "data" : "value_text" },
            { "data" : "action" }
        ],
        "columnDefs": [
            { "targets": -1, "orderable": false, "searchable": false, "className" : "text-middle no-wrap", "width" : "60px" },
            { "targets": 0, "width" : "40px" },
            { "targets": 1, "width" : "40px" },
            { "targets": "_all", "className" : "text-middle" }
        ],
        "order": [[0, 'asc']],
        "lengthMenu": [[10, 20, 40, 60, 80, 100], [10, 20, 40, 60, 80, 100]],
        "pageLength": 20
    });

    //Init iCheck
    var $organism_value = $modal_organism.find("input:checkbox.organism-value");
    $organism_value.iCheck({ checkboxClass: 'icheckbox_minimal', radioClass: 'iradio_minimal' });
    $organism_value.on("ifChecked", function () {
        var name = $(this).attr("name");
        $modal_organism.find("input.organism-value[name!=" + name + "]").iCheck("uncheck");
    });

    //Add New Organism
    $("#new-organism").on("click", function(evt) {
        evt.preventDefault();

        $modal_organism.find("#btnSave span.save").show();
        $modal_organism.find("#btnSave span.update").hide();
        $modal_organism.find('.modal-title.new').show();
        $modal_organism.find('.modal-title.edit').hide();
        $modal_organism.find("#organism-name").val('');
        $modal_organism.find("#organism-order").val('');
        $organism_value.iCheck('uncheck');
        $modal_organism.removeData("ID");
        $modal_organism.modal({ backdrop : 'static' });
    });

    /**
     * Save/Update organism
     */
    $("#btnSaveOrganism").on("click", function(evt) {
        evt.preventDefault();

        var organism_name  = $modal_organism.find("#organism-name").val();
        var value          = $modal_organism.find("input:checkbox.organism-value:checked").val();
        var order          = $modal_organism.find("#organism-order").val();
        var url            = base_url + "organism/add_std_organism";

        if (organism_name.trim().length == 0) {
            myDialog.showDialog('show', {text:msg_required_data, style : 'warning'});
            return false;
        }

        var ID = $modal_organism.data("ID");
        if (ID > 0) url = base_url + "organism/update_std_organism";

        $.ajax({
            url      : url,
            type     : 'POST',
            data     : { organism_name : organism_name, order : order, organism_value : value, ID : ID },
            dataType : 'json',
            success  : function(resText) {
                myDialog.showDialog('show', {text:resText.msg, style : resText.status > 0 ? 'success' : 'warning'});
                if (resText.status > 0) {
                    tbl_organism.ajax.reload();
                    $modal_organism.modal("hide");
                }
            },
            error : function () {
                myDialog.showDialog('show', {text: ID > 0 ? msg_update_fail : msg_save_fail, style : 'warning'});
            }
        });
    });

    /**
     * Edit Organism
     */
    $tbl_organism.on("click", ".edit", function(evt) {
        evt.preventDefault();
        $(this).blur();

        //label
        $modal_organism.find("#btnSaveOrganism span.save").hide();
        $modal_organism.find("#btnSaveOrganism span.update").show();
        $modal_organism.find('.modal-title.new').hide();
        $modal_organism.find('.modal-title.edit').show();

        var data = tbl_organism.row($(this).parents("tr")).data();
        $modal_organism.find("#organism-name").val('');
        $organism_value.iCheck('uncheck');
        $modal_organism.find("#organism-name").val(data.organism_name);
        $modal_organism.find("#organism-order").val(data.order);
        $modal_organism.find("input:checkbox.organism-value[value=" + data.organism_value + "]").iCheck("check");

        $modal_organism.removeData("ID");
        $modal_organism.data("ID", data.ID);
        $modal_organism.modal({ backdrop : 'static' });
    });

    $tbl_organism.on("click", ".remove", function(evt) {
        evt.preventDefault();
        
        var data = tbl_organism.row($(this).parents("tr")).data();
        
        if (confirm(q_delete_organism)) {
            $.ajax({
                url      : base_url + 'organism/delete_std_organism',
                type     : 'POST',
                data     : { ID : data.ID },
                dataType : 'json',
                success  : function(resText) {
                    myDialog.showDialog('show', {text:resText.msg, style : resText.status > 0 ? 'success' : 'warning'});
                    tbl_organism.ajax.reload();
                },
                error : function () {
                    myDialog.showDialog('show', {text: msg_delete_fail, style : 'warning'});
                }
            });
        }
    });

    /**================================== Antibiotic ==================================**/
    var $tbl_antibiotic   = $("#tbl-antibiotic");
    var $modal_antibiotic = $("#modal-antibiotic");

    //Initialize DataTable
    var tbl_antibiotic = $tbl_antibiotic.DataTable({
        "info"       : false,
        "processing" : true,
        "serverSide" : true,
        "ajax"       : {
            "url"    : base_url+'antibiotic/view_std_antibiotic',
            "type"   : 'POST'
        },
        "language"   : dataTableOption.language,
        "retrieve"   : true,
        "columns"    : [
            { "data" : "number" },
            { "data" : "order", "searchable": false },
            { "data" : "antibiotic_name" },
            { "data" : "action" }
        ],
        "columnDefs": [
            { "targets": -1, "orderable": false, "searchable": false, "className" : "text-middle no-wrap", "width" : "60px" },
            { "targets": 0, "width" : "40px" },
            { "targets": 1, "width" : "40px" },
            { "targets": "_all", "className" : "text-middle" }
        ],
        "order": [[0, 'asc']],
        "lengthMenu": [[10, 20, 40, 60, 80, 100], [10, 20, 40, 60, 80, 100]],
        "pageLength": 20
    });

    //Add New Antibiotic
    $("#new-antibiotic").on("click", function(evt) {
        evt.preventDefault();

        $modal_antibiotic.find("#btnSave span.save").show();
        $modal_antibiotic.find("#btnSave span.update").hide();
        $modal_antibiotic.find('.modal-title.new').show();
        $modal_antibiotic.find('.modal-title.edit').hide();
        $modal_antibiotic.find("#antibiotic-name").val('');
        $modal_antibiotic.find("#antibiotic-order").val('');
        $modal_antibiotic.removeData("ID");
        $modal_antibiotic.modal({ backdrop : 'static' });
    });

    $("#btnSaveAntibiotic").on("click", function(evt) {
        evt.preventDefault();

        var antibiotic_name  = $modal_antibiotic.find("#antibiotic-name").val();
        var antibiotic_order = $modal_antibiotic.find("#antibiotic-order").val();
        //var gram_type_id  = $modal_antibiotic.find("#gram_type_id").val();
        var url     = base_url + "antibiotic/add_std_antibiotic";

        if (antibiotic_name.trim().length == 0) {
            myDialog.showDialog('show', {text:msg_required_data, style : 'warning'});
            return false;
        }

        var ID = $modal_antibiotic.data("ID");
        if (ID > 0) url = base_url + "antibiotic/update_std_antibiotic";

        $.ajax({
            url      : url,
            type     : 'POST',
            data     : { antibiotic_name : antibiotic_name, order : antibiotic_order, ID : ID },
            dataType : 'json',
            success  : function(resText) {
                myDialog.showDialog('show', {text:resText.msg, style : resText.status > 0 ? 'success' : 'warning'});
                if (resText.status > 0) {
                    tbl_antibiotic.ajax.reload();
                    $modal_antibiotic.modal("hide");
                }
            },
            error : function () {
                myDialog.showDialog('show', {text: ID > 0 ? msg_update_fail : msg_save_fail, style : 'warning'});
            }
        });
    });

    /**
     * Edit Antibiotic
     */
    $tbl_antibiotic.on("click", ".edit", function(evt) {
        evt.preventDefault();
        $(this).blur();

        //label
        $modal_antibiotic.find("#btnSaveAntibiotic span.save").hide();
        $modal_antibiotic.find("#btnSaveAntibiotic span.update").show();
        $modal_antibiotic.find('.modal-title.new').hide();
        $modal_antibiotic.find('.modal-title.edit').show();

        var data = tbl_antibiotic.row($(this).parents("tr")).data();
        $modal_antibiotic.find("#antibiotic-name").val(data.antibiotic_name);
        $modal_antibiotic.find("#antibiotic-order").val(data.order);
        //$modal_antibiotic.find("#gram_type_id").val(data.gram_type);

        $modal_antibiotic.removeData("ID");
        $modal_antibiotic.data("ID", data.ID);
        $modal_antibiotic.modal({ backdrop : 'static' });
    });

    $tbl_antibiotic.on("click", ".remove", function(evt) {
        evt.preventDefault();

        var data = tbl_antibiotic.row($(this).parents("tr")).data();

        if (confirm(q_delete_antibiotic)) {
            $.ajax({
                url      : base_url + 'antibiotic/delete_std_antibiotic',
                type     : 'POST',
                data     : { ID : data.ID },
                dataType : 'json',
                success  : function(resText) {
                    myDialog.showDialog('show', {text:resText.msg, style : resText.status > 0 ? 'success' : 'warning'});
                    tbl_antibiotic.ajax.reload();
                },
                error : function () {
                    myDialog.showDialog('show', {text: msg_delete_fail, style : 'warning'});
                }
            });
        }
    });

    /**================================== Quantity ==================================**/
    var $tbl_quantity   = $("#tbl-quantity");
    var $modal_quantity = $("#modal-quantity");

    //Initialize DataTable
    var tbl_quantity = $tbl_quantity.DataTable({
        "info"       : false,
        "processing" : true,
        "serverSide" : true,
        "ajax"       : {
            "url"    : base_url+'quantity/view_std_organism_quantity',
            "type"   : 'POST'
        },
        "language"   : dataTableOption.language,
        "retrieve"   : true,
        "columns"    : [
            { "data" : "number" },
            { "data" : "quantity" },
            { "data" : "action" }
        ],
        "columnDefs": [
            { "targets": -1, "orderable": false, "searchable": false, "className" : "text-middle no-wrap", "width" : "60px" },
            { "targets": 0, "width" : "50px" },
            { "targets": "_all", "className" : "text-middle" }
        ],
        "order": [[0, 'asc']]
    });

    //Add New quantity
    $("#new-quantity").on("click", function(evt) {
        evt.preventDefault();

        $modal_quantity.find("#btnSave span.save").show();
        $modal_quantity.find("#btnSave span.update").hide();
        $modal_quantity.find('.modal-title.new').show();
        $modal_quantity.find('.modal-title.edit').hide();
        $modal_quantity.find("#quantity-name").val('');
        $modal_quantity.removeData("ID");
        $modal_quantity.modal({ backdrop : 'static' });
    });

    $("#btnSaveQuantity").on("click", function(evt) {
        evt.preventDefault();

        var quantity  = $modal_quantity.find("#quantity-name").val();
        var url     = base_url + "quantity/add_std_organism_quantity";

        if (quantity.trim().length == 0) {
            myDialog.showDialog('show', {text:msg_required_data, style : 'warning'});
            return false;
        }

        var ID = $modal_quantity.data("ID");
        if (ID > 0) url = base_url + "quantity/update_std_organism_quantity";

        $.ajax({
            url      : url,
            type     : 'POST',
            data     : { quantity : quantity, ID : ID },
            dataType : 'json',
            success  : function(resText) {
                myDialog.showDialog('show', {text:resText.msg, style : resText.status > 0 ? 'success' : 'warning'});
                if (resText.status > 0) {
                    tbl_quantity.ajax.reload();
                    $modal_quantity.modal("hide");
                }
            },
            error : function () {
                myDialog.showDialog('show', {text: ID > 0 ? msg_update_fail : msg_save_fail, style : 'warning'});
            }
        });
    });

    $tbl_quantity.on("click", ".edit", function(evt) {
        evt.preventDefault();
        $(this).blur();

        //label
        $modal_quantity.find("#btnSaveQuantity span.save").hide();
        $modal_quantity.find("#btnSaveQuantity span.update").show();
        $modal_quantity.find('.modal-title.new').hide();
        $modal_quantity.find('.modal-title.edit').show();

        var data = tbl_quantity.row($(this).parents("tr")).data();
        $modal_quantity.find("#quantity-name").val(data.quantity);

        $modal_quantity.removeData("ID");
        $modal_quantity.data("ID", data.ID);
        $modal_quantity.modal({ backdrop : 'static' });
    });

    $tbl_quantity.on("click", ".remove", function(evt) {
        evt.preventDefault();

        var data = tbl_quantity.row($(this).parents("tr")).data();

        if (confirm(q_delete_quantity)) {
            $.ajax({
                url      : base_url + 'quantity/delete_std_organism_quantity',
                type     : 'POST',
                data     : { ID : data.ID },
                dataType : 'json',
                success  : function(resText) {
                    myDialog.showDialog('show', {text:resText.msg, style : resText.status > 0 ? 'success' : 'warning'});
                    tbl_quantity.ajax.reload();
                },
                error : function () {
                    myDialog.showDialog('show', {text: msg_delete_fail, style : 'warning'});
                }
            });
        }
    });

    /**
     * Tab Event
     */
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
    });
});
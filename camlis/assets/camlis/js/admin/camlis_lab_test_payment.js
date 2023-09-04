$(function() {
    //Initialize DataTable
    var tbl_test_payment = $("#tbl-test-payment").DataTable({
        "info"       : false,
        "processing" : true,
        "serverSide" : true,
        "ajax"       : {
            "url"    : base_url + 'test/view_lab_test_payment',
            "type"   : 'POST'
        },
        "language"   : dataTableOption.language,
        "columns"    : [
            {
                "className": 'details-control',
                "orderable": false,
                "data": null,
                "defaultContent": ''
            },
            { "data" : "number" },
            { "data" : "group_result" },
            { "data" : "action" }
        ],
        "columnDefs": [
            { "targets": -1, "orderable": false, "searchable": false, "className": "text-center" }
        ],
        "order": [[2, 'asc']]
    });

    $("input[name=payment_type]").iCheck({ checkboxClass: 'icheckbox_minimal', radioClass: 'iradio_minimal' });
    $("#payment-type, #group-result").select2();
    $("#price").autoNumeric({vMin: 0, aPad: 0});

    //Show test payment modal
    $("#new-test-payment").on("click", function(evt) {
        evt.preventDefault();
        var $modal = $("#modal-test-payment");
        var $table = $("#tbl-payments");

        $table.find("tr.test-payment").remove();
        $table.find("tbody").append(generateTestPaymentRow());
        removePaymentTypeOptionDuplication();

        $modal.removeData("group-result");
        $modal.find("#group-result").prop("disabled", false);

        myDialog.showProgress("show");
        $.ajax({
            url: base_url + "test/get_lab_test_payment",
            type: 'POST',
            dataType: 'json',
            success: function (result) {
                myDialog.showProgress("hide");
                var group_results = _.chain(result).pluck('group_result').uniq().value();
                for(var i in GROUP_RESULTS) {
                    if (group_results.indexOf(GROUP_RESULTS[i].group_result) === -1) {
                        var option = "<option value='" + GROUP_RESULTS[i].group_result + "'>" + GROUP_RESULTS[i].group_result + "</option>";
                        $modal.find("#group-result").append(option);
                    }
                }
                $modal.find("#group-result").select2("destroy");
                $modal.find("#group-result").select2();
                $modal.find("#group-result").val(null).trigger("change");
                $modal.modal({ backdrop : 'static' });
            },
            error: function () {
                myDialog.showProgress("hide");
            }
        });
    });

    /**
     * Add new test payment
     */
    $("#modal-test-payment").on("click", "button.add-payment", function (evt) {
        evt.preventDefault();
        var $table = $("#tbl-payments");
        $table.find("tbody").append(generateTestPaymentRow());
        removePaymentTypeOptionDuplication();

        $table.find("tr.test-payment").find(".add-payment, .remove-payment").hide();
        if ($table.find("tr.test-payment").length > 1) {
            $table.find("tr.test-payment").find(".remove-payment").show();
            $table.find("tr.test-payment").last().find(".add-payment").show();
        } else {
            $table.find("tr.test-payment").find(".remove-payment").hide();
        }
    });

    /**
     * Remove test payment
     */
    $("#modal-test-payment").on("click", "button.remove-payment", function (evt) {
        evt.preventDefault();
        var $table = $("#tbl-payments");
        if ($table.find("tr.test-payment").length === 1) {
            return false;
        }
        $(this).closest("tr.test-payment").remove();
        removePaymentTypeOptionDuplication();

        $table.find("tr.test-payment").last().find(".add-payment").show();
        if ($table.find("tr.test-payment").length === 1) {
            $table.find("tr.test-payment").find(".remove-payment").hide();
        }
    });

    /**
     * Regenerate payment type option when changed
     */
    $("#modal-test-payment").on("change", ".payment-type", function () {
        removePaymentTypeOptionDuplication();
    });
    
    $("#btnSave").on("click", function(evt) {
        evt.preventDefault();

        var $modal = $("#modal-test-payment");
        var group_result = $modal.data("group-result") || $("#group-result").val();
        var test_payments = [];
        $("#tbl-payments").find("tr.test-payment").each(function() {
            var payment_type_id = $(this).find(".payment-type").val();
            if (payment_type_id > 0) {
                test_payments.push({
                    group_result: group_result,
                    payment_type_id: payment_type_id,
                    price: $(this).find(".price").autoNumeric("get")
                });
            }
        });

        if (!group_result || test_payments.length === 0) {
            myDialog.showDialog('show', {text: msg_required_data, style: 'warning'});
            return false;
        }

        var url = group_result ? base_url + "test/update_lab_test_payment" : base_url + "test/add_lab_test_payment";


        console.log(group_result);
        console.log(test_payments);
        
        myDialog.showProgress("show");
        $.ajax({
            url      : url,
            type     : 'POST',
            data     : { group_result: group_result, test_payments: test_payments },
            dataType : 'json',
            success  : function(resText) {
                myDialog.showProgress("hide");
                myDialog.showDialog('show', {text:resText.msg, style : resText.status > 0 ? 'success' : 'warning'});
                if (resText.status) {
                    tbl_test_payment.ajax.reload();
                    $modal.modal("hide");
                }
            },
            error : function () {
                myDialog.showDialog('show', {text: globalMessage.save_fail, style : 'warning'});
            }
        });
    });

    $("#tbl-test-payment").on("click", ".edit", function (evt) {
        evt.preventDefault();
        var data = tbl_test_payment.row($(this).closest("tr")).data();
        var $modal = $("#modal-test-payment");

        $modal.find("#tbl-payments").find("tr.test-payment").remove();
        $modal.find("#group-result").prop("disabled", true);

        myDialog.showProgress("show");
        $.ajax({
            url: base_url + "test/get_lab_test_payment",
            type: 'POST',
            dataType: 'json',
            success: function (result) {
                myDialog.showProgress("hide");

                var group_results = _.chain(result).pluck('group_result').uniq().value();
                for(var i in GROUP_RESULTS) {
                    if (group_results.indexOf(GROUP_RESULTS[i].group_result) === -1 || data.group_result === GROUP_RESULTS[i].group_result) {
                        var option = "<option value='" + GROUP_RESULTS[i].group_result + "'>" + GROUP_RESULTS[i].group_result + "</option>";
                        $modal.find("#group-result").append(option);
                    }
                }
                $modal.find("#group-result").select2("destroy");
                $modal.find("#group-result").select2();
                $modal.find("#group-result").val(data.group_result).trigger("change");

                var payments = _.filter(result, function (d) {
                    return d.group_result === data.group_result;
                });
                if (payments) {
                    for(i in payments) {
                        $modal.find("#tbl-payments").find("tbody").append(generateTestPaymentRow());
                        var $payment_type = $modal.find("#tbl-payments").find("tr.test-payment").last().find(".payment-type");
                        var $price = $modal.find("#tbl-payments").find("tr.test-payment").last().find(".price");
                        $payment_type.html(generatePaymentTypeOption(payments[i].payment_type_id));
                        $price.autoNumeric("set", payments[i].price);
                    }
                } else {
                    $modal.find("#tbl-payments").find("tbody").append(generateTestPaymentRow());
                }

                removePaymentTypeOptionDuplication();
                $modal.data("group-result", data.group_result);
                $modal.modal({ backdrop : 'static' });
            },
            error: function () {
                myDialog.showProgress("hide");
            }
        });
    });
    
    $("#tbl-test-payment").on("click", ".remove", function(evt) {
        evt.preventDefault();
        $(this).blur();
        var data = tbl_test_payment.row($(this).parents("tr")).data();
        
        if (confirm(q_delete_test_payment)) {
            $.ajax({
                url      : base_url + 'test/delete_lab_test_payment',
                type     : 'POST',
                data     : { group_result: data.group_result },
                dataType : 'json',
                success  : function(resText) {
                    myDialog.showDialog('show', {text:resText.msg, style : resText.status > 0 ? 'success' : 'warning'});
                    tbl_test_payment.ajax.reload();
                },
                error : function () {
                    myDialog.showDialog('show', {text: globalMessage.delete_fail, style : 'warning'});
                }
            });
        }
    });

    // Add event listener for opening and closing details
    $("#tbl-test-payment tbody").on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = tbl_test_payment.row( tr );

        if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            // Open this row
            row.child( formatRowDetails(row.data()) ).show();
            tr.addClass('shown');
        }
    } );
});

function generatePaymentTypeOption(val, exclude) {
    val         = val || 0;
    exclude     = exclude || [];
    var options = "";
    for (var i in PAYMENT_TYPES) {
        var selected = '';
        var id       = PAYMENT_TYPES[i].id.toString();

        if (val.toString() === id) selected = 'selected';
        else if (exclude.indexOf(id) > -1) continue;

        options += "<option value='"+ id +"' "+ selected +">"+ PAYMENT_TYPES[i].name +"</option>";
    }
    return options;
}

function removePaymentTypeOptionDuplication() {
    var selectedOption = [];
    var $table = $("#tbl-payments");
    $table.find("tr.test-payment").each(function () {
        var val = $(this).find("select.payment-type").val() || 0;
        if (val > 0) selectedOption.push(val);
    });

    $table.find("tr.test-payment").each(function () {
        var val = $(this).find("select.payment-type").val() || 0;
        $(this).find("select.payment-type").select2("destroy");
        $(this).find("select.payment-type").html(generatePaymentTypeOption(val, selectedOption));
        $(this).find("select.payment-type").select2();
    });
}

function generateTestPaymentRow() {
    var tr = "<tr class='test-payment'>";
    tr += "<td><select name='payment_type' class='form-control payment-type'></select></td>";
    tr += "<td>";
    tr += "<div class='input-group'>";
    tr += "<input type='text' class='form-control price text-right' name='price' value='0' onfocus='this.select()'>";
    tr += "<span class='input-group-addon'>"+ label_riel +"</span>";
    tr += "</div>";
    tr += "</td>";
    tr += "<td><button class='btn btn-danger btn-sm remove-payment'><i class='fa fa-minus'></i></button></td>";
    tr += "<td><button class='btn btn-success btn-sm add-payment'><i class='fa fa-plus'></i></button></td>";
    tr += "</tr>";

    var $tr = $(tr);
    $tr.find("button.remove-payment").hide();
    $tr.find("select.payment-type").select2();
    $tr.find("input.price").autoNumeric({vMin: 0, aPad: 0});
    return $tr;
}

function formatRowDetails(rowData) {
    var div = $('<div/>')
        .addClass( 'loading' )
        .text( 'Loading...' );

    $.ajax( {
        url: base_url + "test/get_lab_test_payment",
        type: 'POST',
        data: {
            group_result: rowData.group_result
        },
        dataType: 'json',
        success: function ( json ) {
            var table = "<table class='table table-bordered table-striped'>";
            for(var i in json) {
                table += "<tr>";
                table += "<td style='width: 40%'>"+ json[i].payment_type_name +"</td>";
                table += "<td><span class='price'>"+ json[i].price +"</span>&nbsp;&nbsp;&nbsp;"+ label_riel +"</td>";
                table += "</tr>";
            }
            table += "</table>";
            div
                .html( table )
                .removeClass( 'loading' )
                .find(".price").autoNumeric({vMin: 0, aPad: 0})
        }
    } );

    return div;
}
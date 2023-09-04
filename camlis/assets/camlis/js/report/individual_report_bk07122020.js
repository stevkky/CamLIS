$(function () {
    var $preview_modal = $("#print_preview_modal");

    $("#patient-code").autocomplete({
        source: function (request, response) {
            $.ajax({
                url: base_url + 'patient/get_patients',
                type: 'POST',
                data: { patient_code : request.term, limit: 10, with_address: 0 },
                dataType: 'json',
                success: function (result) {
                    var data = $.map(result.patients, function (item) {
                       return { label: item.patient_code, value: item.patient_code, patient_name: item.name }
                    });
                    response(data);
                }
            })
        },
        select: function (event, ui) {
            $("#patient-name").val(ui.item.patient_name);
        }
    }).on("focus", function() {
        $(this).autocomplete("search");
    });

    $("#patient-name").autocomplete({
        source: function (request, response) {
            $.ajax({
                url: base_url + 'patient/get_patients',
                type: 'POST',
                data: { patient_name : request.term, limit: 10, with_address: 0 },
                dataType: 'json',
                success: function (result) {
                    var data = $.map(result.patients, function (item) {
                        return { label: item.name, value: item.name, patient_code: item.patient_code }
                    });
                    response(data);
                }
            })
        },
        select: function (event, ui) {
            $("#patient-code").val(ui.item.patient_code);
        }
    }).on("focus", function() {
        $(this).autocomplete("search");
    });

    $("#sample-number").autocomplete({
        minLength: 0,
        source: function (request, response) {
            var data = { sample_number : request.term, patient_code: $("#patient-code").val() };
            if (data.sample_number || data.patient_code) {
                if (!data.patient_code) data.limit = 10;
                $.ajax({
                    url: base_url + 'patient_sample/lookup_patient_sample',
                    type: 'POST',
                    data: data,
                    dataType: 'json',
                    success: function (result) {
                        var data = $.map(result.patient_samples, function (item) {
                            return {
                                label: item.sample_number,
                                value: item.sample_number,
                                patient_sample_id: item.patient_sample_id
                            }
                        });
                        response(data);
                    },
                    error: function () {
                        response([]);
                    }
                });
            } else {
                response([]);
            }
        }
    }).on("focus", function() {
        $(this).autocomplete("search");
    });

    $("#btnGenerate").on("click", function (evt) {
        var sample_number  = $("#sample-number").val();
        var patient_code   = $("#patient-code").val();

        $preview_modal.find(".modal-dialog").empty();

        $.ajax({
            url: base_url + 'patient_sample/lookup_patient_sample',
            type: 'POST',
            data: { sample_number : sample_number, patient_code: patient_code },
            dataType: 'json',
            success: function (result) {
                //Order result of current laboratory to first
                console.log("Result :"+ JSON.stringify(result));
                
                var patient_samples   = _.chain(result.patient_samples).sortBy(function(d) { return LABORATORY_SESSION.labID == d.labID ? -1 : d.labID }).groupBy('labID').value();
                var patient_sample_id = [];
                $.each(patient_samples, function (index, item) {
                    var ids = _.chain(item).sortBy('ID').pluck('ID').value();
                    patient_sample_id = patient_sample_id.concat(ids);
                });
                
                if (patient_sample_id.length === 0) {
                    $preview_modal.find(".page-count").text(0);
                    $preview_modal.find(".page-number").val(0);
                    $preview_modal.find(".page-number").autoNumeric({aPad: 0, vMin: 0, vMax: 0});
                    $preview_modal.modal("show");
                } else {
                    //Show Progress                    
                    myDialog.showProgress('show', {text: globalMessage.loading});
                    
                    // Check if sample has been assigned test, without Test it cause error Query 
                    //ADDED: 07 Dec 2020
                    // AUTHOR: Sopheak HEM
                    
                    is_assign_test = false;
                    $.ajax({
                        url: base_url + 'patient_sample/is_assigned_test',
                        type: 'POST',
                        data: { patient_sample_id : patient_sample_id[0] },
                        dataType: 'json',
                        success: function (result) {
                            console.log("Result "+JSON.stringify(result));
                            is_assign_test = result;
                        }
                    });
                    console.log(is_assign_test);
                    return;


                    $.ajax({
                        url: base_url + "result/patient_sample_result/preview/" + encodeURIComponent(patient_sample_id.join(',')),
                        type: 'POST',
                        dataType: 'json',
                        success: function (resText) {
                            for(var i in resText) {
                                var $page = $("<div class='psample-result'></div>");
                                $page.attr("id", "presult-" + (parseInt(i) + 1));
                                $page.data("patient_sample_id", resText[i].patient_sample_id);
                                $page.html(resText[i].template);
                                if (i == 0) $page.addClass("active");
                                else $page.hide();

                                $preview_modal.find(".modal-dialog").append($page);
                            }

                            $preview_modal.find(".page-count").text(resText.length);
                            $preview_modal.find(".page-number").val(1);
                            $preview_modal.find(".page-number").autoNumeric({aPad: 0, vMin: 1, vMax: resText.length});

                            setTimeout(function () {
                                myDialog.showProgress('hide');
                                $preview_modal.modal("show");
                            }, 400);

                            $preview_modal.find("#doPrinting").off("click").on("click", function (evt) {
                                evt.preventDefault();
                                var psample_id = $preview_modal.find(".modal-dialog .psample-result.active").data("patient_sample_id");
                                printpage(base_url + "result/patient_sample_result/print/" + psample_id);
                            });
                        }
                    });
                }
            },
            error: function() {
                myDialog.showProgress('hide');
                $preview_modal.modal("show");
                $preview_modal.find(".modal-dialog").empty();
            }
        });
    });

    $preview_modal.on("click", "button.next, button.previous", function (evt, customPageNumber) {
        var page_count  = parseInt($preview_modal.find(".page-count").text());
        var page_number = parseInt($preview_modal.find(".page-number").val());
        if ($(this).hasClass("next")) page_number += 1;
        else page_number -= 1;
        if (customPageNumber) page_number = customPageNumber;
        if (page_number > page_count || page_number < 1) return false;
        $preview_modal.find(".page-number").val(page_number);
        $preview_modal.find(".modal-dialog .psample-result").removeClass("active");
        $preview_modal.find(".modal-dialog .psample-result").hide();
        $preview_modal.find(".modal-dialog .psample-result#presult-" + page_number).show().addClass("active");
    });

    $preview_modal.on("keyup", ".page-number", function () {
        var val = $(this).val();
        $preview_modal.find("button.next").trigger("click", val);
    });
});
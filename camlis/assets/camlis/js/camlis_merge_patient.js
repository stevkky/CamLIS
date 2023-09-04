$(function () {
    //Merge patient
    $("#merge-patient").on("click", function(evt) {
        evt.preventDefault();
        var $modal = $("#modal-merge-patient");
        $modal.find("input[type=text]").val('');
        $modal.find(".patient-list").empty();
        $modal.find(".btn-merge-patient").prop("disabled", true);
        $modal.modal({backdrop: 'static'});
    });

    $("#modal-merge-patient").on("keyup", ".patient-code, .patient-name", function(evt) {
        evt.preventDefault();

        var isSource = $(this).hasClass("source");
        var type = isSource ? "source" : "destination";
        var data = { limit: 10, with_address: 0 };
        var patient_search = $(this).val();
        if ($(this).hasClass("patient-code")) {
            data.patient_code = patient_search;
        } else {
            data.patient_name = patient_search;
        }

        var $wrapper = isSource ? $("#patient-source-list") : $("#patient-destination-list");

        $wrapper.find("table.patient-list").empty();
        if (!data.patient_code && !data.patient_name) return false;
        myDialog.showProgress("show", { appendTo: $wrapper, text: '', size: '1x' });

        $.ajax({
            url: base_url + 'patient/get_patients',
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function (result) {
                $wrapper.find("table.patient-list").empty();
                for(var i in result.patients) {
                    var patient = result.patients[i];
                    var  tr  = "<tr class='patient'>";
                    tr += "<td class='text-center text-middle'><input type='radio' name='patient_"+ type +"' value='"+ patient.pid +"'></td>";
                    tr += "<td class='text-nowrap'>"+ patient.patient_code +"</td>";
                    tr += "<td>"+ patient.name +"</td>";
                    tr += "</tr>";

                    var $tr = $(tr);
                    $tr.data("patient", patient);
                    $wrapper.find("table.patient-list").append($tr);
                    $wrapper.find("table.patient-list").find(":radio").iCheck({ checkboxClass: 'icheckbox_minimal', radioClass: 'iradio_minimal' });

                }

                myDialog.showProgress("hide");
            }
        });
    });

    $("#modal-merge-patient").on("click", "button.swap-patient", function(evt) {
        evt.preventDefault();
        $("#modal-merge-patient").find(".patient-list").iCheck("destroy");
        var source_content = $("#patient-source-list").find("table.patient-list").clone(true);
        var destination_content = $("#patient-destination-list").find("table.patient-list").clone(true);
        var source_selection = $(":radio[name=patient_source]:checked").val() || '';
        var destination_selection = $(":radio[name=patient_destination]:checked").val() || '';

        $("#patient-source-list").empty().append(destination_content);
        $("#patient-source-list").find("table.patient-list").find(":radio").attr("name", "patient_source");
        $("#patient-destination-list").empty().append(source_content);
        $("#patient-destination-list").find("table.patient-list").find(":radio").attr("name", "patient_destination");

        $("#modal-merge-patient").find(".patient-list").iCheck({ checkboxClass: 'icheckbox_minimal', radioClass: 'iradio_minimal' });

        $("#patient-source-list").find("table.patient-list").find(":radio[value='"+ destination_selection +"']").iCheck("check");
        $("#patient-destination-list").find("table.patient-list").find(":radio[value='"+ source_selection +"']").iCheck("check");
    });

    $("#modal-merge-patient").on("ifChecked", ":radio", function(evt) {
        var source_data = $(":radio[name=patient_source]:checked").closest("tr").data("patient");
        var destination_data = $(":radio[name=patient_destination]:checked").closest("tr").data("patient");

        var valid = source_data && destination_data && source_data.pid !== destination_data.pid && !source_data.is_pmrs_patient;
        $("#modal-merge-patient").find(".btn-merge-patient").prop("disabled", !valid);
    });

    $("#modal-merge-patient .btn-merge-patient").on("click", function (evt) {
        evt.preventDefault();

        var source_data = $(":radio[name=patient_source]:checked").closest("tr").data("patient");
        var destination_data = $(":radio[name=patient_destination]:checked").closest("tr").data("patient");
        var valid = source_data && destination_data && source_data.pid !== destination_data.pid && !source_data.is_pmrs_patient;
        if (confirm(vsprintf(confirm_merge_patient, [source_data.patient_code, destination_data.patient_code]))) {
            if (!valid) {
                myDialog.showDialog('show', {text: globalMessage.save_fail, style : 'warning'});
                $("#modal-merge-patient").find(".btn-merge-patient").prop("disabled", true);
                return false;
            }

            myDialog.showProgress("show");
            $.ajax({
                url: base_url + "patient/merge",
                data: { patient_id_source: source_data.pid, patient_id_destination: destination_data.pid },
                type: 'POST',
                dataType: 'json',
                success: function(resText) {
                    myDialog.showProgress("hide");
                    myDialog.showDialog('show', {text:resText.msg, style : resText.status ? 'success' : 'warning'});
                    if (resText.status) {
                        var $modal = $("#modal-merge-patient");
                        $modal.find("input[type=text]").val('');
                        $modal.find(".patient-list").empty();
                        $modal.find(".btn-merge-patient").prop("disabled", true);
                    }
                },
                error: function() {
                    myDialog.showProgress("hide");
                    myDialog.showDialog('show', {text: globalMessage.save_fail, style : 'warning'});
                }
            })
        }
    });
});
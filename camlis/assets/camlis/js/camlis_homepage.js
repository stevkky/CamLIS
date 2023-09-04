$(function() {
    $(".filter-box #filter-laboratories").select2();

    $('#btn-enter-laboratory').on('click',function(){
        var labId = $('#filter-laboratories').val();
        //window.location.href = '<?php echo $this->app_language->site_url("laboratory/change") ?>/'+labId;
        window.location.href = 'laboratory/change/'+labId;
    });
});
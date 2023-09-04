<!-- Modal Reject Comment -->
<div class="modal fade modal-info" id="modal-reject-comment">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <i class="fa fa-comments-o" aria-hidden="true"></i>&nbsp;
                <b><?php echo _t('manage.list_comment'); ?></b>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <table class="table table-bordered table-striped" id="tbl-reject-comment" style="width:100%; !important">
                            <thead>
                            <th style="width:40px;"></th>
                            <th><?php echo _t('global.comment'); ?></th>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary add-reject-comment">
                    <i class="fa fa-check-square-o" aria-hidden="true"></i>&nbsp;<?php echo _t('global.choose'); ?>
                </button>
                <button type="button" class="btn btn-default" data-dismiss='modal'>
                    <?php echo _t('global.cancel'); ?>
                </button>
            </div>
        </div>
    </div>
</div>
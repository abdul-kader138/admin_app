<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('Add Service Types'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?php echo lang('enter_info'); ?></p>
                <?php
                $attrib = array('data-toggle' => 'validator', 'role' => 'form');
                echo form_open_multipart("quotes/add_type", $attrib)
                ?>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <?= lang("Service Type Category", "quref"); ?>
                            <?php echo form_input('service_type_category', '', 'class="form-control input-tip" id="service_type_category"'); ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div
                            class="fprom-group"><?php echo form_submit('add_service_type_category', $this->lang->line("submit"), 'id="add_service_type_category" class="btn btn-primary" style="padding: 6px 15px; margin:15px 0;"'); ?>
                            <button type="button" class="btn btn-danger" id="reset"><?= lang('reset') ?></div>
                    </div>
                </div>

            </div>
        </div>


    </div>
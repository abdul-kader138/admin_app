<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<style>
    hr.line {
        border-top: 1px solid darkslategray;
    }
</style>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('Add_Manpower_Requisition'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?php echo lang('enter_info'); ?></p>
                <?php
                $attrib = array('data-toggle' => 'validator', 'role' => 'form');
                echo form_open_multipart("document/add_baina", $attrib);

                ?>
                <div class="row">
                    <div class="col-lg-12">

                        <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang("Requisition_Date", "Requisition_Date") . " <b> *</b>"; ?>
                                <?php echo form_input('requisition_date', (isset($_POST['requisition_date']) ? $_POST['requisition_date'] : ""), 'class="form-control input-tip date" required="required" id="requisition_date"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("Position", "Position") . "<b> *</b>"; ?>
                                <?php $sst = array('Lead' => lang('Lead'), 'Process Owner' => lang('Process_Owner'), 'Associate' => lang('Associate'));
                                echo form_dropdown('position', $sst, (isset($_POST['position']) ? $_POST['position'] : ""), 'class="form-control input-tip" required="required" id="position" style="width:100%"'); ?>

                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("Company", "Company") . " <b> *</b>"; ?>
                                <?php
                                $bl[""] = "";
                                foreach ($companies as $company) {
                                    $bl[$company->id] = $company->name;
                                }
                                echo form_dropdown('company_id', $bl, (isset($_POST['company_id']) ? $_POST['company_id'] : $Settings->default_biller), 'id="company_id" data-placeholder="' . $this->lang->line("select") . ' ' . $this->lang->line("company") . '" required="required" class="form-control input-tip select" style="width:100%;"');
                                ?>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang("Department", "Department") . " <b> *</b>"; ?>
                                <?php echo form_input('department', (isset($_POST['department']) ? $_POST['department'] : ""), 'class="form-control input-tip" required="required" id="department" '); ?>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("Designation", "Designation") . " <b> *</b>"; ?>
                                <?php
                                $wh[''] = '';
                                foreach ($designations as $designation) {
                                    $wh[$designation->id] = $designation->name;
                                }
                                echo form_dropdown('designation_id', $wh, (isset($_POST['designation_id']) ? $_POST['designation_id'] : ""), 'id="designation_id" class="form-control input-tip select" data-placeholder="' . $this->lang->line("select") . ' ' . $this->lang->line("Designation") . '" required="required" style="width:100%;" ');
                                ?>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang("Workstation", "Workstation") . " <b> *</b>"; ?>
                                <?php echo form_input('workstation', (isset($_POST['workstation']) ? $_POST['workstation'] : ""), 'class="form-control input-tip" required="required" id="workstation" '); ?>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("Corporate/Business", "Corporate/Business") . "<b> *</b>"; ?>
                                <?php $sst = array('1' => lang('Corporate'), '2' => lang('Business'));
                                echo form_dropdown('organization_type', $sst, (isset($_POST['organization_type']) ? $_POST['organization_type'] : ""), 'class="form-control input-tip" required="required" id="organization_type" style="width:100%"'); ?>

                            </div>
                        </div>
                        <div class="col-sm-4" id="corporate_name_div">
                            <div class="form-group">
                                <?= lang("Corporate_Name", "Corporate_Name") . " <b> *</b>"; ?>
                                <?php echo form_input('corporate_name', (isset($_POST['corporate_name']) ? $_POST['corporate_name'] : ""), 'class="form-control input-tip" id="corporate_name" '); ?>
                            </div>
                        </div>
                        <div class="col-sm-4" id="business_name_div" style="display:none">
                            <div class="form-group">
                                <?= lang("Business_Name", "Business_Name") . " <b> *</b>"; ?>
                                <?php $sst = array('Feed' => lang('Feed'), 'Poultry' => lang('Poultry'), 'Plast Fiber' => lang('Plast_Fiber'), 'Plastic' => lang('Plastic'));
                                echo form_dropdown('organization_type', $sst, (isset($_POST['organization_type']) ? $_POST['organization_type'] : ""), 'class="form-control input-tip" required="required" id="organization_type" style="width:100%"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang("Number_Required", "Number_Required") . " <b> *</b>"; ?>
                                <?php echo form_input('number_required', (isset($_POST['number_required']) ? $_POST['number_required'] : ""), 'class="form-control input-tip" id="number_required" required="required"'); ?>
                            </div>
                        </div>

                        <div class="clearfix"></div>
                        <div class="col-sm-6">
                            <div class="combo">

                                <div class="control-group table-group">
                                    <label class="table-label"
                                           for="combo"><?= lang("Required_Experience") . " <b> *</b>"; ?></label>
                                    <div class="controls table-controls">
                                        <table id="prTable"
                                               class="table items table-striped table-bordered table-condensed table-hover">
                                            <thead>
                                            <tr>
                                                <th class="col-md-4 col-sm-4 col-xs-4"><?= lang('Minimum(Years)'); ?></th>
                                                <th class="col-md-4 col-sm-4 col-xs-4"><?= lang("Maximum(Years)"); ?></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <th> <?php echo form_input('exp_min', (isset($_POST['exp_min']) ? $_POST['exp_min'] : ""), 'class="form-control input-tip" id="exp_min" required="required"'); ?>
                                                </th>
                                                <th><?php echo form_input('exp_max', (isset($_POST['exp_max']) ? $_POST['exp_max'] : ""), 'class="form-control input-tip" id="exp_max" required="required"'); ?></th>
                                            </tr>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="combo">

                                <div class="control-group table-group">
                                    <label class="table-label"
                                           for="combo"><?= lang("Age_Limit") . " <b> *</b>"; ?></label>
                                    <div class="controls table-controls">
                                        <table id="prTable"
                                               class="table items table-striped table-bordered table-condensed table-hover">
                                            <thead>
                                            <tr>
                                                <th class="col-md-4 col-sm-4 col-xs-4"><?= lang('Minimum'); ?></th>
                                                <th class="col-md-4 col-sm-4 col-xs-4"><?= lang("Maximum"); ?></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <th> <?php echo form_input('age_min', (isset($_POST['age_min']) ? $_POST['age_min'] : ""), 'class="form-control input-tip" id="age_min" required="required"'); ?>
                                                </th>
                                                <th><?php echo form_input('age_max', (isset($_POST['age_max']) ? $_POST['age_max'] : ""), 'class="form-control input-tip" id="age_max" required="required"'); ?></th>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <hr class="line">
                        <div class="col-sm-12">
                            <h3 class="table-label"
                                for="combo"><b>Please choose the appropriate option for requirement:</b></H3>
                            <div class="form-check">
                                <input type="radio" class="form-check-input" id="ap"
                                       name="requirement" checked>
                                <label class="form-check-label"
                                       for="ap"><?= lang('Additional_Position') ?></label>
                            </div>
                            <div class="form-check">
                                <input type="radio" class="form-check-input" id="rr"
                                       name="requirement">
                                <label class="form-check-label"
                                       for="materialChecked"><?= lang('Replacement_Due_To_Resignation') ?></label>
                            </div>
                            <div class="form-check">
                                <input type="radio" class="form-check-input" id="rt"
                                       name="requirement" >
                                <label class="form-check-label"
                                       for="materialChecked"><?= lang('Replacement_Due_To_Termination') ?></label>
                            </div>
                            <div class="form-check">
                                <input type="radio" class="form-check-input" id="rp"
                                       name="requirement">
                                <label class="form-check-label"
                                       for="materialChecked"><?= lang('Replacement_Due_To_Promotion') ?></label>
                            </div>
                            <div class="form-check">
                                <input type="radio" class="form-check-input" id="rtr"
                                       name="requirement">
                                <label class="form-check-label"
                                       for="materialChecked"><?= lang('Replacement_Due_To_Transfer') ?></label>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <hr class="line">
                        <div class="row" id="bt">
                            <div class="col-md-12">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang("Other_info", "Other_info"); ?>
                                        <?php echo form_textarea('other_info', (isset($_POST['other_info']) ? $_POST['other_info'] : ""), 'class="form-control" id="other_info" style="margin-top: 10px; height: 100px;"'); ?>
                                    </div>
                                </div>

                            </div>

                        </div>

                        <div class="col-md-12">
                            <div
                                    class="fprom-group"><?php echo form_submit('add_sale', $this->lang->line("submit"), 'id="add_sale" class="btn btn-primary" style="padding: 6px 15px; margin:15px 0;"'); ?>
                                <button type="button" class="btn btn-danger" id="reset"><?= lang('reset') ?></div>
                        </div>
                    </div>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
<script>
    $('#organization_type').on('change', function () {
        if (this.value === '1') {
            $('#corporate_name_div').show();
            $('#business_name_div').hide();
        }
        if (this.value === '2') {
            $('#corporate_name_div').hide();
            $('#business_name_div').show();
        }
    });

</script>
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
                echo form_open("hrms/add_manpower_requisition", $attrib);

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
                                <?php $sst = array('Corporate' => lang('Corporate'), 'Business' => lang('Business'));
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
                                echo form_dropdown('business_name', $sst, (isset($_POST['business_name']) ? $_POST['business_name'] : ""), 'class="form-control input-tip"  id="business_name" style="width:100%"'); ?>
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
                        <div class="col-sm-6">
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
                        <div class="col-sm-6">
                            <div class="form-group">
                                <b>Is the position as per the approved manpower budget & approved manpower plan fot the year</b>
                                <?php
                                $opt = array(2019 => "2019", 2020 => "2020", 2021 => "2021", 2022 => "2022", 2023 => "2023", 2024 => "2024", 2025 => "2025", 2026 => "2026", 2027 => "2027", 2028 => "2028", 2029 => "2029", 2030 => "2030");
                                echo form_dropdown('mb_year', $opt, (isset($_POST['mb_year']) ? $_POST['mb_year'] : ''), 'id="mb_year" required="required" class="form-control input-tip select" style="width:100%;"');
                                ?>
                            </div>

                            <div class="form-group">
                                <b>Reason for additional position requisition</b>
                                <?php echo form_input('reason_ap', (isset($_POST['reason_ap']) ? $_POST['reason_ap'] : ""), 'class="form-control input-tip" id="reason_ap" required="required"'); ?>

                            </div>
                            <div class="form-group">
                                <b>Time limit within which the position is to be held</b>
                                <?php echo form_input('time_limit', (isset($_POST['time_limit']) ? $_POST['time_limit'] : ""), 'class="form-control input-tip" id="time_limit" required="required"'); ?>

                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <hr class="line">
                        <div class="col-sm-12">
                            <div class="combo">

                                <div class="control-group table-group">
                                    <label class="table-label"
                                           for="combo"><?= lang("In Case of New/Additional Position Requirement"); ?>
                                        <br><?= lang("Qualification Required:"); ?></label>
                                    <div class="controls table-controls">
                                        <table id="prTable"
                                               class="table items table-striped table-bordered table-condensed table-hover">
                                            <thead>
                                            <tr>
                                                <th class="col-md-3 col-sm-3 col-xs-3"><?= lang('Education'); ?></th>
                                                <th class="col-md-3 col-sm-3 col-xs-3"><?= lang("Skill"); ?></th>
                                                <th class="col-md-3 col-sm-3 col-xs-3"><?= lang("Minimum_Experience"); ?></th>
                                                <th class="col-md-3 col-sm-3 col-xs-3"><?= lang("Nature_Of_Experience"); ?></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <th> <?php echo form_input('education', (isset($_POST['education']) ? $_POST['education'] : ""), 'class="form-control input-tip" id="education"'); ?>
                                                </th>
                                                <th><?php echo form_input('skill', (isset($_POST['skill']) ? $_POST['skill'] : ""), 'class="form-control input-tip" id="skill"'); ?></th>
                                                <th><?php echo form_input('minimum_experience', (isset($_POST['minimum_experience']) ? $_POST['minimum_experience'] : ""), 'class="form-control input-tip" id="minimum_experience"'); ?></th>
                                                <th><?php echo form_input('nature_experience', (isset($_POST['nature_experience']) ? $_POST['nature_experience'] : ""), 'class="form-control input-tip" id="nature_experience"'); ?></th>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="row" id="bt">
                            <div class="col-md-12">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang("Areas_Of_Responsibility", "Areas_Of_Responsibility"); ?>
                                        <?php echo form_textarea('areas_of_responsibility', (isset($_POST['areas_of_responsibility']) ? $_POST['areas_of_responsibility'] : ""), 'class="form-control" id="areas_of_responsibility" required="required" style="margin-top: 10px; height: 100px;"'); ?>
                                    </div>
                                </div>

                            </div>

                        </div>

                        <div class="clearfix"></div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang("Reporting_to", "Reporting_to") . " <b> *</b>"; ?>
                                <?php echo form_input('reporting_to', (isset($_POST['reporting_to']) ? $_POST['reporting_to'] : ""), 'class="form-control input-tip" id="reporting_to" required="required" '); ?>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang("Mobile_No", "Mobile_No") . " <b> *</b>"; ?>
                                <?php echo form_input('mobile_no', (isset($_POST['mobile_no']) ? $_POST['mobile_no'] : ""), 'class="form-control input-tip" id="mobile_no" required="required" '); ?>
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
        if (this.value === 'Corporate') {
            $('#corporate_name_div').show();
            $('#business_name_div').hide();
        }
        if (this.value === 'Business') {
            $('#corporate_name_div').hide();
            $('#business_name_div').show();
        }
    });

</script>
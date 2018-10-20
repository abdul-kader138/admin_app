<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-users"></i><?= lang('add_guard'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <p class="introtext"><?php echo lang('add_guard'); ?></p>

                <?php $attrib = array('class' => 'form-horizontal', 'data-toggle' => 'validator', 'role' => 'form');
                echo form_open("guard/add_guard", $attrib);
                ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-5">
                            <div class="form-group">
                                <?php echo lang('employee_id', 'employee_id'); ?>
                                <div class="controls">
                                    <?php echo form_input('employee_id',  (isset($_POST['employee_id']) ? $_POST['employee_id'] : ''), 'class="form-control" id="employee_id" required="required"'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php echo lang('name', 'name'); ?>
                                <div class="controls">
                                    <?php echo form_input('name', (isset($_POST['name']) ? $_POST['name'] : ''), 'class="form-control" id="name" required="required" '); ?>
                                </div>
                            </div>


                            <div class="form-group">
                                <?php echo lang('mobile_number', 'mobile_number'); ?>
                                <div class="controls">
                                    <?php echo form_input('mobile_number',  (isset($_POST['mobile_number']) ? $_POST['mobile_number'] : ''), 'class="form-control" pattern="(?=\d)(?=[0-9]).{10,14}" id="mobile_number" '); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <?php echo lang('present_weight', 'present_weight'); ?>
                                <div class="controls">
                                    <?php echo form_input('present_weight', (isset($_POST['present_weight']) ? $_POST['present_weight'] : ''), 'class="form-control tip" id="present_weight" required="required" '); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <?php echo lang('height_feet', 'height_feet'); ?>
                                <div class="controls">
                                    <?php echo form_input('height_feet', (isset($_POST['height_feet']) ? $_POST['height_feet'] : ''), 'class="form-control tip" id="height_feet" required="required" '); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <?php echo lang('height_inch', 'height_inch'); ?>
                                <div class="controls">
                                    <?php echo form_input('height_inch', (isset($_POST['height_inch']) ? $_POST['height_inch'] : ''), 'class="form-control tip" id="height_inch" required="required" '); ?>
                                </div>
                            </div>


                        </div>
                        <div class="col-md-5 col-md-offset-1">
                            <div class="form-group">
                                <?= lang("designation", "designation"); ?>
                                <?php
                                $bl[""] = lang('select') . ' ' . lang('designation');
                                foreach ($designations as $designation) {
                                    $bl[$designation->id] = $designation->name;
                                }
                                echo form_dropdown('designation_id', $bl, (isset($_POST['designation_id']) ? $_POST['designation_id'] : ''), ' id="designation_id" required="required" class="form-control select" style="width:100%;"');
                                ?>
                            </div>
                            <div class="form-group">
                                <?= lang("company", "company"); ?>
                                <?php
                                $b2[""] = lang('select') . ' ' . lang('company');
                                foreach ($companies as $company) {
                                    $b2[$company->id] = $company->name;
                                }
                                echo form_dropdown('company_id', $b2, (isset($_POST['company_id']) ? $_POST['company_id'] : ''), 'id="company_id" required="required" class="form-control select" style="width:100%;"');
                                ?>
                            </div>

                            <div class="form-group">
                                <?php echo lang('dob', 'dob'); ?>
                                <div class="controls">
                                    <?php echo form_input('dob', (isset($_POST['dob']) ? $_POST['dob'] : ''), 'class="form-control date" id="dob"'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php echo lang('joining_date', 'joining_date'); ?>
                                <div class="controls">
                                    <?php echo form_input('joining_date', (isset($_POST['joining_date']) ? $_POST['joining_date'] : ''), 'class="form-control date" id="joining_date"'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <?= lang('status', 'status'); ?>
                                <?php
                                $opt = array(1 => lang('active'), 0 => lang('inactive'));
                                echo form_dropdown('active', $opt, (isset($_POST['active']) ? $_POST['active'] : ''), 'id="active" required="required" class="form-control select" style="width:100%;"');
                                ?>
                            </div>

                            <div class="clearfix"></div>

                        </div>
                    </div>
                </div>

                <p><?php echo form_submit('add_employee', lang('add_employee'), 'class="btn btn-primary"'); ?></p>

                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" charset="utf-8">
    $(document).ready(function () {
        $('.no').slideUp();
        $('#group').change(function (event) {
            var group = $(this).val();
            if (group == 1 || group == 2) {
                $('.no').slideUp();
            } else {
                $('.no').slideDown();
            }
        });
    });
</script>

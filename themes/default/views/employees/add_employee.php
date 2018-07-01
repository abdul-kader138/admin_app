<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-users"></i><?= lang('add_employee'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <p class="introtext"><?php echo lang('add_employee'); ?></p>

                <?php $attrib = array('class' => 'form-horizontal', 'data-toggle' => 'validator', 'role' => 'form');
                echo form_open("employees/add_employee", $attrib);
                ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-5">
                            <div class="form-group">
                                <?php echo lang('employee_id', 'employee_id'); ?>
                                <div class="controls">
                                    <?php echo form_input('employee_id', '', 'class="form-control" id="employee_id" required="required" pattern=".{3,10}"'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php echo lang('name', 'name'); ?>
                                <div class="controls">
                                    <?php echo form_input('name', '', 'class="form-control" id="name" required="required" pattern=".{3,10}"'); ?>
                                </div>
                            </div>


                            <div class="form-group">
                                <?php echo lang('mobile_no', 'mobile_no'); ?>
                                <div class="controls">
                                    <?php echo form_input('mobile_number', '', 'class="form-control" id="mobile_number" required="required"'); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <?php echo lang('email', 'email'); ?>
                                <div class="controls">
                                    <input type="email" id="email" name="email" class="form-control"
                                           required="required"/>
                                    <?php /* echo form_input('email', '', 'class="form-control" id="email" required="required"'); */ ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <?php echo lang('ceiling_limit', 'ceiling_limit'); ?>
                                <div class="controls">
                                    <?php echo form_input('ceiling_amount', '', 'class="form-control tip" id="ceiling_amount" required="required" '); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <?php echo lang('service_start_date', 'service_start_date'); ?>
                                <div class="controls">
                                    <?php echo form_input('service_start_date', '', 'class="form-control date" id="service_start_date" required="required" '); ?>
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
                                <?= lang("operator", "operator"); ?>
                                <?php
                                $b3[""] = lang('select') . ' ' . lang('operator');
                                foreach ($operators as $operator) {
                                    $b3[$operator->id] = $operator->name;
                                }
                                echo form_dropdown('operator_id', $b3, (isset($_POST['operator_id']) ? $_POST['operator_id'] : ''), 'id="operator_id" required="required" class="form-control select" style="width:100%;"');
                                ?>
                            </div>

                            <div class="form-group">
                                <?= lang("package", "package"); ?>
                                <?php
                                $b4[""] = lang('select') . ' ' . lang('package');
                                foreach ($packages as $package) {
                                    $b4[$package->id] = $package->name;
                                }
                                echo form_dropdown('package_id', $b4, (isset($_POST['package_id']) ? $_POST['package_id'] : ''), 'id="package_id" required="required" class="form-control select" style="width:100%;"');
                                ?>
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

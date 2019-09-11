<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-users"></i><?= lang('edit_employee'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <p class="introtext"><?php echo lang('edit_employee'); ?></p>

                <?php $attrib = array('class' => 'form-horizontal', 'data-toggle' => 'validator', 'role' => 'form');
                echo form_open("employees/edit_employee/" . $employee->id, $attrib);
                ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-5">
                            <div class="form-group">
                                <?php echo lang('employee_id', 'employee_id'); ?>
                                <div class="controls">
                                    <?php echo form_input('employee_id', $employee->employee_id, 'class="form-control" id="employee_id" required="required" readonly pattern=".{3,10}"'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php echo lang('name', 'name'); ?>
                                <div class="controls">
                                    <?php echo form_input('name', $employee->name, 'class="form-control" id="name" required="required" pattern=".{3,10}"'); ?>
                                </div>
                            </div>


                            <div class="form-group">
                                <?php echo lang('mobile_no', 'mobile_no'); ?>
                                <div class="controls">
                                    <?php echo form_input('mobile_number', $employee->mobile_number, 'class="form-control" pattern="(?=\d)(?=[0-9]).{10,14}" id="mobile_number" required="required"'); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <?php echo lang('email', 'email'); ?>
                                <div class="controls">
                                    <!--                                    <input type="email" id="email" name="email" class="form-control"-->
                                    <!--                                           required="required"/>-->
                                    <?php  echo form_input('email', $employee->email, 'class="form-control" id="email" '); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <?php echo lang('ceiling_amount', 'ceiling_amount'); ?>
                                <div class="controls">
                                    <?php echo form_input('ceiling_amount', $employee->ceiling_amount, 'class="form-control tip" id="ceiling_amount" required="required" '); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php echo lang('credit_limit', 'credit_limit'); ?>
                                <div class="controls">
                                    <?php echo form_input('credit_limit', $employee->credit_limit, 'class="form-control tip" id="credit_limit" required="required" '); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php echo lang('NID', 'NID'); ?>
                                <div class="controls">
                                    <?php echo form_input('nid', (isset($_POST['nid']) ? $_POST['nid'] : $employee->nid), 'class="form-control tip" id="nid" required="required" '); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <?= lang("Blood", "Blood"); ?>
                                <?php $sst23 = array('' => lang(''),'A+' => lang('A+'),'A-' => lang('A-'), 'B+' => lang('B+'), 'B-' => lang('B-'), 'AB+' => lang('AB+'),'AB-' => lang('AB-'),'O+' => lang('O+'),'O-' => lang('O-'));      echo form_dropdown('blood', $sst23, (isset($_POST['blood']) ? $_POST['blood'] : $employee->blood), 'class="form-control input-tip"  id="blood" style="width:100%"'); ?>
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
                                echo form_dropdown('designation_id', $bl, $employee->designation_id, ' id="designation_id" required="required" class="form-control select" style="width:100%;"');
                                ?>
                            </div>
                            <div class="form-group">
                                <?= lang("company", "company"); ?>
                                <?php
                                $b2[""] = lang('select') . ' ' . lang('company');
                                foreach ($companies as $company) {
                                    $b2[$company->id] = $company->name;
                                }
                                echo form_dropdown('company_id', $b2, $employee->company_id, 'id="company_id" required="required" class="form-control select" style="width:100%;"');
                                ?>
                            </div>
                            <div class="form-group">
                                <?= lang("operator", "operator"); ?>
                                <?php
                                $b3[""] = lang('select') . ' ' . lang('operator');
                                foreach ($operators as $operator) {
                                    $b3[$operator->id] = $operator->name;
                                }
                                echo form_dropdown('operator_id', $b3, $employee->operator_id, 'id="operator_id" required="required" class="form-control select" style="width:100%;"');
                                ?>
                            </div>

                            <div class="form-group">
                                <?= lang("package", "package"); ?>
                                <?php
                                $b4[""] = lang('select') . ' ' . lang('package');
                                foreach ($packages as $package) {
                                    $b4[$package->id] = $package->name;
                                }
                                echo form_dropdown('package_id', $b4, $employee->package_id, 'id="package_id" required="required" class="form-control select" style="width:100%;"');
                                ?>
                            </div>

                            <div class="form-group">
                                <?php echo lang('service_start_date_f', 'service_start_date_f'); ?>
                                <div class="controls">
                                    <?php echo form_input('service_start_date', $employee->service_start_date, 'class="form-control date" id="service_start_date" '); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <?= lang("Work_Station", "Work_Station") . " <b> *</b>"; ?>
                                <?php $sst = array('HeadOffice' => lang('HeadOffice'),'Feed' => lang('Feed'), 'Poultry' => lang('Poultry'), 'Plast Fiber' => lang('Plast_Fiber'), 'Plastic' => lang('Plastic'),'FootWear' => lang('FootWear'),'Consumer Food' => lang('Consumer_Food'),'Horticulture' => lang('Horticulture'),'Tea States' => lang('Tea_States'));      echo form_dropdown('work_station', $sst, (isset($_POST['work_station']) ? $_POST['work_station'] : $employee->work_station), 'class="form-control input-tip"  id="work_station" style="width:100%"'); ?>
                            </div>
                            <div class="form-group">
                                <?= lang('status', 'status'); ?>
                                <?php
                                $opt = array(1 => lang('active'), 0 => lang('inactive'));
                                echo form_dropdown('active', $opt,$employee->active, 'id="active" required="required" class="form-control select" style="width:100%;"');
                                ?>
                            </div>

                            <div class="clearfix"></div>

                        </div>
                    </div>
                </div>

                <p><?php echo form_submit('edit_employee', lang('edit_employee'), 'class="btn btn-primary"'); ?></p>

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

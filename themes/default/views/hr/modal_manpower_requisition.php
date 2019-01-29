<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                <i class="fa fa-2x">&times;</i>
            </button>
            <button type="button" class="btn btn-xs btn-default no-print pull-right" style="margin-right:15px;" onclick="window.print();">
                <i class="fa fa-print"></i> <?= lang('print'); ?>
            </button>
            <h2 class="modal-title" id="myModalLabel" style="color: #00A0C6; text-align:center">Manpower Requisition Details</h2>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-xs-12">
                    <div class="col-xs-6">
                        <div class="table-responsive">
                            <table class="table table-borderless table-striped ">
                                <tbody>
                                <tr>
                                    <td style="width: 30%"><b><?= lang("Requisition_date"); ?>:</b></td>
                                    <td style="width: 70%"><?= $document->requisition_date ?></td>
                                </tr>
                                <tr>
                                    <td style="width: 30%"><b><?= lang("Position"); ?>:</b></td>
                                    <td style="width: 70%"><?= $document->position; ?></td>
                                </tr>
                                <tr>
                                    <td style="width: 30%"><b><?= lang("Corporate/Business"); ?>:</b></td>
                                    <td style="width: 70%"><?= $document->organization_type;?></td>
                                </tr>
                                <?php if($document->organization_type == 'Corporate'){ ?>
                                <tr>
                                    <td style="width: 30%"><b><?= lang("Company_Name"); ?>:</b></td>
                                    <td style="width: 70%"><?= $document->corporate_name;?></td>
                                </tr>
                                <?php }?>
                                <?php if($document->organization_type == 'Business'){ ?>
                                    <tr>
                                        <td style="width: 30%"><b><?= lang("Business_Name"); ?>:</b></td>
                                        <td style="width: 70%"><?= $document->business_name;?></td>
                                    </tr>
                                <?php }?>
                                <tr>
                                    <td style="width: 30%"><b><?= lang("Department"); ?>:</b></td>
                                    <td style="width: 70%"><?= $document->department; ?></td>
                                </tr>

                                <tr>
                                    <td style="width: 30%"><b><?= lang("Company"); ?>:</b></td>
                                    <td style="width: 70%"><?= $companies->name; ?></td>
                                </tr>
                                <tr>
                                    <td style="width: 30%"><b><?= lang("Workstation"); ?>:</b></td>
                                    <td style="width: 70%"><?= $document->workstation; ?></td>
                                </tr>
                                <tr>
                                    <td style="width: 35%"><b><?= lang("Designation"); ?>:</b></td>
                                    <td style="width: 65%"><?= $designations->name; ?></td>
                                </tr>
                                <tr>
                                    <td style="width: 35%"><b><?= lang("Number_Required"); ?>:</b></td>
                                    <td style="width: 65%"><?= $document->number_required; ?></td>
                                </tr>
                                <tr>
                                    <td style="width: 35%"><b><?= lang("Reporting_to"); ?>:</b></td>
                                    <td style="width: 65%"><?= $document->reporting_to; ?></td>
                                </tr>

                                <tr>
                                    <td style="width: 30%"><b><?= lang("No_Of_Reportees"); ?>:</b></td>
                                    <td style="width: 70%"><?= $document->no_of_reportees;?></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <div class="table-responsive">
                            <table class="table table-borderless table-striped">
                                <tbody>
                                <tr>
                                    <td style="width: 50%"><b><?= lang("Required_Experience_(Min)"); ?>:</b></td>
                                    <td style="width: 50%"><?=$document->exp_min; ?></td>
                                </tr>
                                <tr>
                                    <td style="width: 50%" ><b><?= lang("Required_Experience_(Max)"); ?>:</b></td>
                                    <td style="width: 50%" ><?= $document->exp_max; ?></td>
                                </tr>
                                <tr>
                                    <td style="5width: 50%"><b><?= lang("Age_Limit_(Min)"); ?>:</b></td>
                                    <td style="width: 50%"><?= $document->age_min; ?></td>
                                </tr>
                                <tr>
                                    <td ><b><?= lang("Age_Limit_(Max)"); ?>:</b></td>
                                    <td ><?= $document->age_max;?></td>
                                </tr>
                                <tr>
                                    <td style="width: 50%"> <b><?= lang("Requirement_Reason"); ?>:</b></td>
                                    <?php if($document->ap =='1'){?> <td style="width: 50%">Additional Position</td> <?php }?>
                                    <?php if($document->rr =='1'){?> <td style="width: 50%">Replacement Due To Resignation</td> <?php }?>
                                    <?php if($document->rt =='1'){?> <td style="width: 50%">Replacement Due To Termination</td> <?php }?>
                                    <?php if($document->rp =='1'){?> <td style="width: 50%">Replacement Due To Promotion</td> <?php }?>
                                    <?php if($document->rtr =='1'){?> <td style="width: 50%">Replacement Due To Transfer</td> <?php }?>
                                </tr>
                                <tr>
                                    <td style="width: 50%"><b><?= lang("Approved_Manpower_Budget_&_Plan_Year"); ?>:</b></td>
                                    <td style="width: 50%"><?= $document->mb_year;?></td>
                                </tr>
                                <tr>
                                    <td style="width: 30%"><b><?= lang("Reason_For_Additional_Position_Requisition"); ?>:</b></td>
                                    <td style="width: 70%"><?= $document->reason_ap;?></td>
                                </tr>
                                <tr>
                                    <td style="width: 30%"><b><?= lang("Time_Limit_For_Filling_Position"); ?>:</b></td>
                                    <td style="width: 70%"><?= $document->case_no;?></td>
                                </tr>
                                <tr>
                                    <td style="width: 30%"><b><?= lang("Status"); ?>:</b></td>
                                    <td style="width: 70%"><?= $document->status;?></td>
                                </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-sm-12">
                    <div class="combo">

                        <div class="control-group table-group">
                            <label class="table-label" style="text-align: center;alignment: center"
                                   for="combo"><?= lang("Qualification Required:"); ?></label>
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
                                        <th style="font-weight: normal;"> <?php echo $document->education;?> </th>
                                        <th style="font-weight: normal;"> <?php echo $document->skill;?> </th>
                                        <th style="font-weight: normal;"> <?php echo $document->minimum_experience;?> </th>
                                        <th style="font-weight: normal;"> <?php echo $document->nature_experience;?> </th>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="clearfix"></div>
                <?php if($document->areas_of_responsibility){ ?>
                    <div class="col-xs-12">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tbody>
                                <tr>
                                    <td style="text-align: center;font-weight: bold;">Area Of Responsibilities</td>
                                </tr>
                                <tr>
                                    <td><?= $document->areas_of_responsibility;?></td>
                                </tr>
                                </tbody>
                            </table></div>
                    </div>
                    <div class="clearfix"></div>
                <?php }?>
                <?php if($document->other_info){ ?>
                    <div class="col-xs-12">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tbody>
                                <tr>
                                    <td style="text-align: center; font-weight: bold;">Other Information</td>
                                </tr>
                                <tr>
                                    <td><?= $document->other_info;?></td>
                                </tr>
                                </tbody>
                            </table></div>
                    </div>
                    <div class="clearfix"></div>
                <?php }?>

                <div class="col-xs-12">

                    <?= $product->details ? '<div class="panel panel-success"><div class="panel-heading">' . lang('product_details_for_invoice') . '</div><div class="panel-body">' . $product->details . '</div></div>' : ''; ?>
                    <?= $product->product_details ? '<div class="panel panel-primary"><div class="panel-heading">' . lang('product_details') . '</div><div class="panel-body">' . $product->product_details . '</div></div>' : ''; ?>

                </div>
            </div>
            <div class="buttons">
                <div class="btn-group btn-group-justified">
                    <div class="btn-group">
                        <a href="<?= site_url('document/pdf/' . $document->id) ?>" class="tip btn btn-primary" title="<?= lang('pdf') ?>">
                            <i class="fa fa-download"></i>
                            <span class="hidden-sm hidden-xs"><?= lang('pdf') ?></span>
                        </a>
                    </div>
                    <div class="btn-group">
                        <a href="<?= site_url('document/edit/' . $document->id) ?>" class="tip btn btn-warning tip" title="<?= lang('Edit_Document') ?>">
                            <i class="fa fa-edit"></i>
                            <span class="hidden-sm hidden-xs"><?= lang('edit') ?></span>
                        </a>
                    </div>
                    <div class="btn-group">
                        <a href="#" class="tip btn btn-danger bpo" title="<b><?= lang("Delete_Document") ?></b>"
                           data-content="<div style='width:150px;'><p><?= lang('r_u_sure') ?></p><a class='btn btn-danger' href='<?= site_url('document/delete/' . $document->id) ?>'><?= lang('i_m_sure') ?></a> <button class='btn bpo-close'><?= lang('no') ?></button></div>"
                           data-html="true" data-placement="top">
                            <i class="fa fa-trash-o"></i>
                            <span class="hidden-sm hidden-xs"><?= lang('delete') ?></span>
                        </a>
                    </div>
                </div>
            </div>
            <script type="text/javascript">
                $(document).ready(function () {
                    $('.tip').tooltip();
                });
            </script>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $('.change_img').click(function(event) {
            event.preventDefault();
            var img_src = $(this).attr('href');
            $('#pr-image').attr('src', img_src);
            return false;
        });
    });
</script>

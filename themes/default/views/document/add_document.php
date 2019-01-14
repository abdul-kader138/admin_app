<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('add_document'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?php echo lang('enter_info'); ?></p>
                <?php
                $attrib = array('data-toggle' => 'validator', 'role' => 'form');
                echo form_open_multipart("document/add", $attrib);

                ?>
                <div class="row">
                    <div class="col-lg-12">

                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("name", "name"); ?>
                                <?php echo form_input('name', (isset($_POST['name']) ? $_POST['name'] : ""), 'class="form-control input-tip" id="name" required="required"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("reference_no", "slref"); ?>
                                <?php echo form_input('reference_no', (isset($_POST['reference_no']) ? $_POST['reference_no'] : $slnumber), 'class="form-control input-tip" required="required" id="reference_no"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("company", "company"); ?>
                                <?php
                                $bl[""] = "";
                                foreach ($companies as $company) {
                                    $bl[$company->id] =  $company->name;
                                }
                                echo form_dropdown('company_id', $bl, (isset($_POST['company_id']) ? $_POST['company_id'] : $Settings->default_biller), 'id="company_id" data-placeholder="' . $this->lang->line("select") . ' ' . $this->lang->line("company") . '" required="required" class="form-control input-tip select" style="width:100%;"');
                                ?>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang("document_status", "document_status"); ?>
                                <?php $sst = array('new' => lang('new'),'updated' => lang('updated'), 'pending' => lang('pending'));
                                echo form_dropdown('status_id', $sst, '', 'class="form-control input-tip" required="required" id="status_id"'); ?>

                            </div>
                        </div>


                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("doc_type", "doc_type"); ?>
                                <?php
                                $wh[''] = '';
                                foreach ($doctypes as $doctype) {
                                    $wh[$doctype->id] = $doctype->name;
                                }
                                echo form_dropdown('doctype_id', $wh, (isset($_POST['doctype_id']) ? $_POST['doctype_id'] : ""), 'id="doctype_id" class="form-control input-tip select" data-placeholder="' . $this->lang->line("select") . ' ' . $this->lang->line("doc_type") . '" required="required" style="width:100%;" ');
                                ?>
                            </div>
                        </div>

                        <div class="clearfix"></div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <?= lang("document", "document") ?>
                                <input id="document" type="file" data-browse-label="<?= lang('browse'); ?>" name="document" data-show-upload="false"
                                       data-show-preview="false" class="form-control file">
                            </div>
                        </div>
                        <div class="clearfix"></div>


                        <div class="row" id="bt">
                            <div class="col-md-12">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang("other_info", "other_info"); ?>
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

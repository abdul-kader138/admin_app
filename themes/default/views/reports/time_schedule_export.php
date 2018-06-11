<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <div class="row modal-title">
                <p class="pull-left">Export: Time Schedule</p>
            </div>
        </div>
        <div class="modal-body">
            <form action="<?php echo site_url('reports/export_time_schedule'); ?>" method="get">
                <div class="form-group">
                    <label for="user" class="control-label">Employee</label>
                    <div class="row">
                        <div class="col-sm-12">
                            <select name="user" id="users" class="form-control">
                                <option value="">All</option>
                                <?php foreach($ts_users as $user): ?>
                                    <option value="<?php echo $user->id; ?>"><?php echo $user->first_name . ' ' . $user->last_name . ' - ' . $user->company; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="from" class="control-label">Date From</label>
                    <div class="row">
                        <div class="col-sm-12">
                            <input type="text" name="from" required="required" placeholder="YYYY-MM-DD" class="form-control" id="from" maxlength="10" />
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="to" class="control-label">Date To</label>
                    <div class="row">
                        <div class="col-sm-12">
                            <input type="text" name="to" required="required" placeholder="YYYY-MM-DD" class="form-control" id="to" maxlength="10" />
                        </div>
                    </div>
                </div>

                <input type="hidden" name="<?php echo $type; ?>" value="1" />

                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-12">
                            <button class="btn btn-success">Submit</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
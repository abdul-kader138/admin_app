<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <div class="row modal-title">
                <p class="pull-left">Time Schedule</p>
            </div>
        </div>
        <div class="modal-body">
            <div class="hidden alert alert-success" id=ts_message_ok>
                <p>Time schedule successfully save!</p>
            </div>

            <div class="hidden alert alert-danger" id=ts_message_error">
                <p>Couldn't save time schedule. Please try again.</p>
            </div>

            <form action="" method="post" role="form" onsubmit="return false;">
                <div class="form-group">
                    <label for="ts_itime_user" class="control-label">Employee</label>
                    <div class="row">
                        <div class="col-sm-12">
                            <select name="ts_user" id="ts_itime_user" class="form-control" disabled>
                                <?php foreach($ts_users as $user): ?>
                                    <option value="<?php echo $user->id; ?>"><?php echo $user->first_name . ' ' . $user->last_name . ' - ' . $user->company; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <?php if ($count_time_sched < 2): ?>
                <div class="form-group">
                    <label for="ts_itime_in" class="control-label">Time In</label>
                    <div class="row">
                        <div class="col-sm-12">
                            <input type="text" name="ts_timein" required="required" placeholder="HH:MM:SS" class="form-control" id="ts_itime_in" maxlength="8" value="<?php echo isset($obj_time_sched) && $obj_time_sched->time_in ? $obj_time_sched->time_in : '' ?>" />
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="ts_itime_out" class="control-label">Time Out</label>
                    <div class="row">
                        <div class="col-sm-12">
                            <input type="text" name="ts_timeout" required="required" placeholder="HH:MM:SS" class="form-control" id="ts_itime_out" maxlength="8" value="<?php echo isset($obj_time_sched) && $obj_time_sched->time_out ? $obj_time_sched->time_out : '' ?>" />
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <div class="form-group">
                    <div class="row-fluid">
                        <table class="table table-bordered table-condensed table-striped">
                            <thead>
                                <tr>
                                    <th>Time In</th>
                                    <th>Time Out</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php $arrTimeScheduleIDs = array(); ?>
                            <?php foreach($multi_time_sched as $key => $sched): ?>
                                <?php $arrTimeScheduleIDs[] = $sched->timeSheduleID; ?>
                                <tr>
                                    <td><input type="text" name="ts_timein" required="required" placeholder="HH:MM:SS" value="<?php echo $sched->timeIn; ?>" class="form-control" id="ts_itime_in_<?php echo $key; ?>" maxlength="8" /></td>
                                    <td><input type="text" name="ts_timeout" required="required" placeholder="HH:MM:SS" value="<?php echo $sched->timeOut; ?>" class="form-control" id="ts_itime_out_<?php echo $key; ?>" maxlength="8" /></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="ts_itime_date" class="control-label">Date</label>
                    <div class="row">
                        <div class="col-sm-12">
                            <input type="text" name="ts_date" required="required" disabled placeholder="YYYY-MM-DD" class="form-control" id="ts_itime_date" value="<?php echo isset($obj_time_sched) && $obj_time_sched->time_date ? date('Y-m-d',strtotime($obj_time_sched->time_date)) : '' ?>"/>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="ts_itime_status" class="control-label">Employee</label>
                    <div class="row">
                        <div class="col-sm-12">
                            <select name="ts_status" id="ts_itime_status" class="form-control">
                                <option value="FOR_REVIEW" <?php echo isset($obj_time_sched) && !empty($obj_time_sched) && $obj_time_sched->handle == 'FOR_REVIEW' ? 'selected' : '' ?>>For Review</option>
                                <option value="PAID" <?php echo isset($obj_time_sched) && !empty($obj_time_sched) && $obj_time_sched->handle == 'PAID' ? 'selected' : '' ?>>Paid</option>
                                <option value="NOT_PAID" <?php echo isset($obj_time_sched) && !empty($obj_time_sched) && $obj_time_sched->handle == 'NOT_PAID' ? 'selected' : '' ?>>Not Paid</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-12">
                            <button class="btn btn-success" id="btnsubmit">Submit</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script>            
    (function() {        
        $('#btnsubmit').click(function() {
            window.TimeSchedulerClass.save(<?php echo $id; ?>, <?php echo isset($arrTimeScheduleIDs) ? json_encode($arrTimeScheduleIDs) : json_encode(''); ?>);
            return false;
        });
    })();
</script>
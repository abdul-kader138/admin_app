<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <div class="row modal-title">
                <p class="pull-left">Time Scheduler</p>
                <p class="pull-right">Date : <?php echo date( 'F d, Y' ); ?></p>
            </div>
        </div>
        <div class="modal-body">

            <!-- STEP 1 -->
            <div class="jumbotron <?php echo $status ? 'hidden' : ''; ?>" id="step1">
                <div class="container text-info">
                    <legend class="text-info"><h2><i class="fa fa-lock"></i> Sign In</h2></legend>
                    <p class="alert alert-danger text-center hidden" id="time_scheduler_error">
                        <small>Error: Combination of username/password did not match. Please try again.</small>
                    </p>
                    <form action="" method="post" role="form">
                        <div class="form-group">
                            <label class="control-label" for="sched_username">Username</label>
                            <div class="row">
                                <div class="col-xs-12"><input type="text" name="username" class="form-control" id="sched_username" /></div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label" for="sched_password">Password</label>
                            <div class="row">
                                <div class="col-xs-12"><input type="password" name="password" class="form-control" id="sched_password" /></div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-xs-12">
                                    <button class="btn btn-primary" type="button" id="sched_login" name="login">Submit</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- STEP 2 -->
            <div class="jumbotron <?php echo $status ? '' : 'hidden'; ?>" id="step2">
                <div class="container text-info">
                    <legend class="text-info"><h2><i class="fa fa-time"></i> Work Time Record</h2></legend>

                    <form action="" method="post" role="form">
                        <div class="form-group">
                            <div class="row">
                                <span class="form-control-static">Hi, <span id="sched_fullname"></span>!</span><br/><br/>
                                <span class="form-control-static">The time now is <?php echo date('g:i:s A'); ?>.</span><br/><br/>
                                <span class="form-control-static">Your Time-in:
                                    <strong id="time-in-label"><?php echo $status ? date('g:i:s A',strtotime($status->time_in)) : 'N/A'; ?></strong>
                                 </span><br/>
                                <span class="form-control-static">Your Time-out:
                                    <strong id="time-out-label"><?php echo $status && $status->time_out != NULL ? date('g:i:s A',strtotime($status->time_out)) : 'N/A'; ?></strong>
                                </span><br/><br/>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-xs-12 text-center">
                                    <button class="btn btn-primary time-punch-btn time-punch-in" time-punch-type="time_in" type="button" <?php echo $status && $status->time_out == '' ? 'disabled' : ''; ?> name="time_in"><i class="fa fa-log-in"></i> Time-in</button>
                                    <button class="btn btn-primary time-punch-btn time-punch-out" time-punch-type="time_out" type="button" <?php echo $status && $status->time_out != '' ? 'disabled' : ''; ?> name="time_out"><i class="fa fa-log-out"></i> Time-out</button>
                                </div>
                            </div>
                        </div>

                        <hr/>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-xs-12">
                                    <button class="btn btn-default" type="button" id="sched_logout">Sign Out</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

</div>
<script src="<?= $assets ?>js/jquery-dateformat.min.js"></script>
<script>
    (function($) {
        if ($) {
            var refresh = function () {
                $.getJSON('<?php echo base_url(); ?>time_scheduler/ajax_refresh', function (response) {
                    var user = response.user;
                    var time_status = response.status;

                    $('#sched_fullname').text(user.first_name + ' ' + user.last_name);
                    $('#time-in-label').text(time_status ? $.format.date(time_status.time_date + ' ' + time_status.time_in, 'h:mm:ss a') : 'N/A');
                    $('#time-out-label').text(time_status && time_status.time_out != null ? $.format.date(time_status.time_date + ' ' + time_status.time_out, 'h:mm:ss a') : 'N/A');
                    $('.time-punch-in').attr('disabled', time_status && !time_status.time_out);
                    $('.time-punch-out').attr('disabled', time_status && time_status.time_out);
                });
            };

            $('#sched_login').click(function() {
                $.post('<?php echo base_url(); ?>time_scheduler/ajax_login', {
                    email: $('#sched_username').val(),
                    password: $('#sched_password').val(),
                    '<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'
                }, function(response) {
                    if (response == '0') {
                        $('#time_scheduler_error').removeClass('hidden');
                    } else {
                        $('#time_scheduler_error').addClass('hidden');
                        $('#step1').addClass('hidden');
                        $('#step2').removeClass('hidden');
                        refresh();
                    }
                });
            });
            
            $('#sched_logout').click(function() {
                $.post('<?php echo base_url(); ?>time_scheduler/ajax_logout', {
                    '<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'
                }, function() {
                    $('#step2').addClass('hidden');
                    $('#step1').removeClass('hidden');
                });
            });

            $('.time-punch-btn').click(function() {
                var time_type = $(this).attr('time-punch-type');

                $.post('<?php echo base_url(); ?>time_scheduler/ajax_punch_time', {
                    time_type : time_type,
                    '<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'
                }, function() {
                    refresh();
                });
            });

            refresh();
        }
    })(window.jQuery);
</script>

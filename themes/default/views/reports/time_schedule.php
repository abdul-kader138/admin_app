<script>
    (function () {
        window.TimeSchedulerClass = (function ($) {
            var self = this;
            var collection = {};

            self.refresh = function () {
                $('#ts_itime_user').val(self.model.userID);
                $('#ts_itime_in').val(self.model.timeIn);
                $('#ts_itime_out').val(self.model.timeOut);
                $('#ts_itime_date').val(self.model.timeDate);
                $('#ts_itime_status').val(self.model.timeStatus);

                if (self.model.userID) {
                    $('#ts_itime_user').attr('disabled', true);
                    $('#ts_itime_date').attr('disabled', true);
                } else {
                    $('#ts_itime_user').attr('disabled', false);
                    $('#ts_itime_date').attr('disabled', false);
                    $('#ts_itime_user').val($('#ts_itime_user > option:first-child').attr('value'));
                }
            };
            self.reset = function () {
                var objDate = new Date();

                self.model = {
                    userID: null,
                    timeIn: null,
                    timeOut: null,
                    timeDate: objDate.getFullYear() + '-' + (objDate.getMonth() + 1) + '-' + ("0" + objDate.getDate()).slice(-2),
                    timeStatus: 'FOR_REVIEW'
                };
            };

            self.model = {
                userID: null,
                timeIn: null,
                timeOut: null,
                timeDate: null,
                timeStatus: null
            };

            self.addToCollection = function (id, data) {
                collection[id] = $.extend(self.model, {
                    userID: data[1],
                    timeIn: data[5],
                    timeOut: data[6],
                    timeDate: data[4],
                    timeStatus: data[10] //handle
                });

                self.reset();
            };

            self.populate = function (id) {
                if (collection[id]) {
                    self.model = collection[id];
                }
            };

            self.remove = function (id) {
                delete collection[id];
            };

            self.save = function (id, bolMultiple, override) {
                if (bolMultiple) {

                    $.each(bolMultiple, function (key, value) {
                        self.model.timeIn = $('#ts_itime_in_' + key).val();
                        self.model.timeOut = $('#ts_itime_out_' + key).val();
                        self.model.timeStatus = $('#ts_itime_status_' + key).val();

                        self.save(value, false, true);
                    });

                } else {

                    if (!override) {
                        if (id) {
                            self.model.timeIn = $('#ts_itime_in').val();
                            self.model.timeOut = $('#ts_itime_out').val();
                        } else {
                            self.model = {
                                userID: $('#ts_itime_user').val(),
                                timeIn: $('#ts_itime_in').val(),
                                timeOut: $('#ts_itime_out').val(),
                                timeDate: $('#ts_itime_date').val()
                            };
                        }
                    } else {
                        //do nothing self.model already been set
                    }

                    self.model['timeStatus'] = $('#ts_itime_status').val();

                    $.post('<?php echo base_url() ?>time_scheduler/update_time_schedule', $.extend({
                        id: id,
                        '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
                    }, self.model), function (response) {
                        if (response == 1) {
                            $('#ts_message_ok').removeClass('hidden');
                            $('#ts_message_error').addClass('hidden');
                        } else {
                            $('#ts_message_error').removeClass('hidden');
                            $('#ts_message_ok').addClass('hidden');
                        }
                    });
                }

                return false;
            };

            self.getAll = function () {
                return collection;
            };

            return self;
        })(window.jQuery);

        var oTable;
        $(document).ready(function () {
            $(document).on('show.bs.modal', '#myModal', function () {
                setTimeout(function () {
                    window.TimeSchedulerClass.refresh();
                }, 1000);
            });
            $('#myModal').on('shown.bs.modal', function () {
                window.TimeSchedulerClass.refresh();
            });
            $('#myModal').on('hide.bs.modal', function () {
                window.TimeSchedulerClass.reset();
            });

            oTable = $('#PRData').dataTable({
                "aaSorting": [[4, "desc"]],                
                "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
                "iDisplayLength": <?= $Settings->rows_per_page ?>,
                'bProcessing': true,
                'bServerSide': true,
                'sAjaxSource': '<?= site_url('reports/get_time_schedule'); ?>',
                'fnRowCallback': function (nRow, aData) {
                    window.TimeSchedulerClass.addToCollection(aData[0], aData);

                    $(nRow).find('td:nth-child(1)').addClass('hide');
                    $(nRow).find('td:nth-child(2)').addClass('hide');
                    $(nRow).find('td:nth-child(11)').addClass('hide');

                    return nRow;
                },
                'fnServerData': function (sSource, aoData, fnCallback) {
                    aoData.push({
                        "name": "<?= $this->security->get_csrf_token_name() ?>",
                        "value": "<?= $this->security->get_csrf_hash() ?>",
                    }, {
                        "name": "userId",
                        "value": $('#user').val()
                    }, {
                        "name": "fromDateTime",
                        "value": $('#from_date').val()
                    }, {
                        "name": "toDateTime",
                        "value": $('#to_date').val()
                    });
                    $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
                },
                "aoColumns": [
                    null,
                    null,
                    null,
                    null,
                    null,
                    { "bSearchable": false },
                    { "bSearchable": false },
                    { "bSearchable": false },
                    null,
                    null,
                    null,
                    null
                ]
            }).fnSetFilteringDelay().dtFilter([
                {column_number: 2, filter_default_label: "[<?= lang('First Name'); ?>]", filter_type: "text", data: []},
                {column_number: 3, filter_default_label: "[<?= lang('Last Name'); ?>]", filter_type: "text", data: []},
                {column_number: 4, filter_default_label: "[<?= lang('Date'); ?>]", filter_type: "text", data: []},
                {column_number: 9, filter_default_label: "[<?= lang('Status'); ?>]", filter_type: "text", data: []}
            ], "footer");

            $("#filter").on("click", function () {
                oTable.fnDraw();  // In your case this would be 'tblOrders.fnDraw();'
            });

        });
    })();
</script>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-clock-o"></i><?= lang('Time Schedule Report'); ?></h2>
        <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#"><i class="icon fa fa-tasks tip" data-placement="left" title="<?= lang("actions") ?>"></i></a>
                    <ul class="dropdown-menu pull-right" class="tasks-menus" role="menu" aria-labelledby="dLabel">
                        <li><a data-toggle="modal" href="<?php echo site_url('reports/time_schedule/0/edit'); ?>" data-target="#myModal"><i class="fa fa-plus-circle"></i> <?= lang('Add Time Schedule') ?></a></li>
                        <li><a href="<?php echo site_url('reports/time_schedule/0/export'); ?>?export_type=excel" id="excel" data-target="#myModal" data-toggle="modal"><i class="fa fa-file-excel-o"></i> <?= lang('export_to_excel') ?></a></li>
                        <li><a href="<?php echo site_url('reports/time_schedule/0/export'); ?>?export_type=pdf" id="pdf" data-target="#myModal" data-toggle="modal"><i class="fa fa-file-pdf-o"></i> <?= lang('export_to_pdf') ?></a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?= lang('list_results'); ?></p>

                <div class="filter_div">
                    <form class="form-inline" id="filter_form" action="<?php echo site_url('reports/export_time_schedule/true'); ?>" method="get">
                        <div class="form-group">
                            <label class="sr-only" for="user">User</label>                            
                            <select name="user" id="user" class="form-control" style="width: 200px;">
                                <option value="">All</option>
                                <?php foreach ($ts_users as $user): ?>
                                    <option value="<?php echo $user->id; ?>"><?php echo $user->first_name . ' ' . $user->last_name . ' - ' . $user->company; ?></option>
                                <?php endforeach; ?>
                            </select>                            
                        </div>
                        <div class="form-group">
                            <label class="sr-only">From Date-Time</label>
                            <input type="text" name='from_date' id='from_date' class="form-control datetime" placeholder="From Date-Time">
                        </div>                        
                        <div class="form-group">
                            <label class="sr-only">To Date-Time</label>
                            <input type="text" name='to_date' id='to_date' class="form-control datetime" placeholder="To Date-Time">
                        </div>                        
                        <button type="button" class="btn btn-primary" id='filter'>Filter</button>
                        <button type="submit" class="btn btn-default" id='export_to_pdf'>Export To PDF</button>
                    </form>
                </div>

                <div class="table-responsive">
                    <table id="PRData" class="table table-bordered table-condensed table-hover table-striped">
                        <thead>
                            <tr class="primary">
                                <th class="hide"></th>
                                <th class="hide"></th>
                                <th><?= lang("First Name") ?></th>
                                <th><?= lang("Last Name") ?></th>
                                <th><?= lang("Date") ?></th>
                                <th><?= lang("Time In") ?></th>
                                <th><?= lang("Time Out") ?></th>
                                <th><?= lang("Total Time") ?></th>
                                <th><?= lang("IP Address") ?></th>
                                <th><?= lang("Status") ?></th>
                                <th class="hide"></th>
                                <th><?= lang("actions") ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="9" class="dataTables_empty"><?= lang('loading_data_from_server'); ?></td>
                            </tr>
                        </tbody>

                        <tfoot class="dtFilter">
                            <tr class="active">
                                <th class="hide"></th>
                                <th class="hide"></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th class="hide"></th>
                                <th><?= lang("actions") ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
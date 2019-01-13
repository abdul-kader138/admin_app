<link rel="stylesheet" type="text/css"
      href="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css"/>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
<link href="<?= $assets ?>elFinder/css/elfinder.min.css" rel="stylesheet"/>
<link href="<?= $assets ?>elFinder/themes/windows-10/css/theme.css" rel="stylesheet"/>

<script type="text/javascript" src="<?= $assets ?>elFinder/js/elfinder.min.js"></script>
<?php
//$languages = $this->db->where('name', config_item('default_language'))->get('tbl_languages')->row();
?>
<style>
    /*#elfinder{*/
        /*background-color: grey;*/
    /*}*/
    .ui-dialog{
        background-color: #dbdee0;
    }
</style>
<script type="text/javascript" charset="utf-8">

        $().ready(function () {
            window.setTimeout(function () {
                var locale = "<?= $languages->code;?>";
                var _locale = locale;
                if (locale == 'pt') {
                    _locale = 'pt_BR';
                }
                var elf = $('#elfinder').elfinder({
                    // lang: 'ru',             // language (OPTIONAL)
                    url: '<?= site_url()?>filemanager/elfinder_init',  // connector URL (REQUIRED)
                    lang: _locale,
                    height: 700,
                    uiOptions: {
                        // toolbar configuration

                        toolbar: [
                            ['back', 'forward'],
//                     ['mkdir'],
                            ['mkdir', 'upload'],
                            // ['mkdir', 'mkfile', 'upload'],
                            ['open', 'download', 'getfile'],
                            ['info'],
                            ['quicklook'],
                            // ['copy', 'cut', 'paste'],
                            ['copy'],
                            ['rm'],
                            ['duplicate', 'rename', 'edit', 'resize'],
                            ['extract', 'archive'],
                            ['search'],
                            ['view'],
                        ],
                    }

                }).elfinder('instance');
            }, 200);
        });
</script>

<!-- Element where elFinder will be created (REQUIRED) -->
<div class="panel panel-custom">
    <div class="panel-heading">
        <div class="panel-title"><?= lang('filemanager') ?></div>
    </div>
    <div class="">
        <div id="elfinder"></div>
    </div>

</div>
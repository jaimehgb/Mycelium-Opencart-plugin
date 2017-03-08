<?php echo $header; ?>
<?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <!-- This button was initially made to import settings from mycelium gear, if that feature is implemented sometime this could be enabled again -->
                <!--<button type="submit" form="form-mycelium-import" data-toggle="tooltip" title="<?php echo $button_copy_from; ?>" class="btn btn-success"><i class="fa fa-cloud-download"></i></button>-->
                <button type="submit" form="form-mycelium-account" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
                <a href="<?php echo $url_cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
            </div>
            <h1>
                <?php echo $heading_title; ?>
            </h1>
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <li>
                    <a href="<?php echo $breadcrumb['href']; ?>">
                        <?php echo $breadcrumb['text']; ?>
                    </a>
                </li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="container-fluid" id="mycelium-page">
        <?php if ($error_warning) { ?>
        <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i>
            <?php echo $error_warning; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>
        <?php if ($success) { ?>
        <div class="alert alert-success"><i class="fa fa-check-circle"></i>
            <?php echo $success; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>
        
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-pencil"></i>
                    <?php echo $text_edit; ?>
                </h3>
            </div>
            <div class="panel-body">
                
                <form action="<?php echo $url_action; ?>" method="post" enctype="multipart/form-data" id="form-mycelium-import" class="form-horizontal">
                    <input type="hidden" name="action" value="import"/>
                </form>
                
                <form action="<?php echo $url_action; ?>" method="post" enctype="multipart/form-data" id="form-mycelium-account" class="form-horizontal">
                    <input type="hidden" name="action" value="save">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a href="#tab-settings" data-toggle="tab">
                                <?php echo $tab_settings; ?>
                            </a>
                        </li>
                        <li>
                            <a href="#tab-log" data-toggle="tab">
                                <?php echo $tab_log ?>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab-settings">
                            <h3 class="col-sm-10 col-sm-offset-2">
                                <?php echo $text_general; ?>
                            </h3>
                            
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-gateway-name"><span data-toggle="tooltip" title="<?php echo $help_gateway_name; ?>"><?php echo $entry_gateway_name; ?></span></label>
                                <div class="col-sm-10">
                                    <input type="text" name="mycelium_gateway_name" class="form-control" value="<?php echo $mycelium_gateway_name; ?>" />
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-mycelium-id"><span data-toggle="tooltip" title="<?php echo $help_gateway_id; ?>"><?php echo $entry_gateway_id; ?></span></label>
                                <div class="col-sm-10">
                                    <input type="text" name="mycelium_gateway_id" class="form-control" value="<?php echo $mycelium_gateway_id; ?>" />
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-gateway-secret"><span data-toggle="tooltip" title="<?php echo $help_gateway_secret; ?>"><?php echo $entry_gateway_secret; ?></span></label>
                                <div class="col-sm-10">
                                    <input type="text" name="mycelium_gateway_secret" class="form-control" value="<?php echo $mycelium_gateway_secret; ?>" />
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-expiration-period"><span data-toggle="tooltip" title="<?php echo $help_expiration_period; ?>"><?php echo $entry_expiration_period; ?></span></label>
                                <div class="col-sm-10">
                                    <input type="text" name="mycelium_expiration_period" class="form-control" value="<?php echo $mycelium_expiration_period; ?>" />
                                </div>
                            </div>
                            <!-- Uncomment this once the we can successfully authenticate to mycelium admin endpoint -->
                            <!--
                            
                            
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-confirmations"><span data-toggle="tooltip" title="<?php echo $help_confirmations; ?>"><?php echo $entry_confirmations; ?></span></label>
                                <div class="col-sm-10">
                                    <div class="input-group"> <span class="input-group-addon"><i class="fa fa-bolt fa-fw"></i></span>
                                        <select name="mycelium_confirmations" id="input-confirmations" class="form-control">
                                            <option value="0"<?php if ($mycelium_confirmations == '0') { echo ' selected="selected"'; } ?>>0</option>
                                            <option value="1"<?php if ($mycelium_confirmations == '1') { echo ' selected="selected"'; } ?>>1</option>
                                            <option value="2"<?php if ($mycelium_confirmations == '2') { echo ' selected="selected"'; } ?>>2</option>
                                            <option value="3"<?php if ($mycelium_confirmations == '3') { echo ' selected="selected"'; } ?>>3</option>
                                            <option value="4"<?php if ($mycelium_confirmations == '4') { echo ' selected="selected"'; } ?>>4</option>
                                            <option value="5"<?php if ($mycelium_confirmations == '5') { echo ' selected="selected"'; } ?>>5</option>
                                            <option value="6"<?php if ($mycelium_confirmations == '6') { echo ' selected="selected"'; } ?>>6</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-xpub"><span data-toggle="tooltip" title="<?php echo $help_xpub; ?>"><?php echo $entry_xpub; ?></span></label>
                                <div class="col-sm-10">
                                    <input type="text" name="mycelium_gateway_xpub" class="form-control" value="<?php echo $mycelium_gateway_xpub; ?>">
                                </div>
                            </div>
                            -->
                                                        <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-shifty"><span data-toggle="tooltip" title="<?php echo $help_shifty; ?>"><?php echo $entry_shifty; ?></span></label>
                                <div class="col-sm-10">
                                    <select name="mycelium_shifty_enabled" id="input-shifty" class="form-control">
                                        <?php if ($mycelium_shifty_enabled) { ?>
                                        <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                        <option value="0"><?php echo $text_disabled; ?></option>
                                        <?php } else { ?>
                                        <option value="1"><?php echo $text_enabled; ?></option>
                                        <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-geo-zone"><?php echo $entry_geo_zone; ?></label>
                                <div class="col-sm-10">
                                    <div class="input-group"> <span class="input-group-addon"><i class="fa fa-globe fa-fw"></i></span>
                                        <select name="mycelium_geo_zone_id" id="input-geo-zone" class="form-control">
                                            <option value="0"><?php echo $text_all_zones; ?></option>
                                            <?php foreach ($geo_zones as $geo_zone) { ?>
                                            <?php if ($geo_zone['geo_zone_id'] == $mycelium_geo_zone_id) { ?>
                                            <option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
                                            <?php } else { ?>
                                            <option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
                                            <?php } ?>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
                                <div class="col-sm-10">
                                    <select name="mycelium_status" id="input-status" class="form-control">
                                        <?php if ($mycelium_status) { ?>
                                        <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                        <option value="0"><?php echo $text_disabled; ?></option>
                                        <?php } else { ?>
                                        <option value="1"><?php echo $text_enabled; ?></option>
                                        <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                        <?php } ?>
                                    </select>
                                    <?php if ($error_status) { ?>
                                    <div class="text-danger">
                                        <?php echo $error_status; ?>
                                    </div>
                                    <?php } ?>
                                </div>
                            </div>
                            
                            
                            
                            <br>
                            <h3 class="col-sm-10 col-sm-offset-2">
                                <?php echo $text_statuses; ?>
                            </h3>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?php echo $help_paid_status; ?>"><?php echo $entry_paid_status; ?></span></label>
                                <div class="col-sm-10">
                                    <select name="mycelium_paid_status" class="form-control">
                                        <?php foreach ($order_statuses as $order_status) { ?>
                                        <?php if ($order_status['order_status_id'] == $mycelium_paid_status) { ?>
                                        <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                        <?php } else { ?>
                                        <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                        <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?php echo $help_complete_status; ?>"><?php echo $entry_complete_status; ?></span></label>
                                <div class="col-sm-10">
                                    <select name="mycelium_complete_status" class="form-control">
                                        <?php foreach ($order_statuses as $order_status) { ?>
                                        <?php if ($order_status['order_status_id'] == $mycelium_complete_status) { ?>
                                        <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                        <?php } else { ?>
                                        <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                        <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <br>
                            <h3 class="col-sm-10 col-sm-offset-2<?php if ($error_notify_url || $error_return_url) { ?> text-danger<?php } ?>">
                                <?php echo $text_advanced; ?>
                            </h3>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-reuse-time"><span data-toggle="tooltip" title="<?php echo $help_reuse_time; ?>"><?php echo $entry_reuse_time; ?></span></label>
                                <div class="col-sm-10">
                                    <div class="input-group"> <span class="input-group-addon"><i class="fa fa-recycle" aria-hidden="true"></i></span>
                                        <input type="text" name="mycelium_reuse_time" id="input-reuse-time" value="<?php echo $mycelium_reuse_time; ?>" class="form-control" />
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-callback-url"><span data-toggle="tooltip" title="<?php echo $help_callback_url; ?>"><?php echo $entry_callback_url; ?></span></label>
                                <div class="col-sm-10">
                                    <div class="input-group"> <span class="input-group-addon"><i class="fa fa-link fa-fw"></i></span>
                                        <input type="url" name="mycelium_callback_url" id="input-callback-url" value="<?php echo $mycelium_callback_url; ?>" placeholder="<?php echo $default_notify_url; ?>" class="form-control" />
                                    </div>
                                    <?php if ($error_notify_url) { ?>
                                    <div class="text-danger">
                                        <?php echo $error_notify_url; ?>
                                    </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-return-url"><span data-toggle="tooltip" title="<?php echo $help_return_url; ?>"><?php echo $entry_return_url; ?></span></label>
                                <div class="col-sm-10">
                                    <div class="input-group"> <span class="input-group-addon"><i class="fa fa-link fa-fw"></i></span>
                                        <input type="url" name="mycelium_return_url" id="input-return-url" value="<?php echo $mycelium_return_url; ?>" placeholder="<?php echo $default_return_url; ?>" class="form-control" />
                                    </div>
                                    <?php if ($error_return_url) { ?>
                                    <div class="text-danger">
                                        <?php echo $error_return_url; ?>
                                    </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-back-url"><span data-toggle="tooltip" title="<?php echo $help_back_url; ?>"><?php echo $entry_back_url; ?></span></label>
                                <div class="col-sm-10">
                                    <div class="input-group"> <span class="input-group-addon"><i class="fa fa-link fa-fw"></i></span>
                                        <input type="url" name="mycelium_back_url" id="input-return-url" value="<?php echo $mycelium_back_url; ?>" placeholder="<?php echo $default_back_url; ?>" class="form-control" />
                                    </div>
                                    <?php if ($error_back_url) { ?>
                                    <div class="text-danger">
                                        <?php echo $error_back_url; ?>
                                    </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-debug"><span data-toggle="tooltip" title="<?php echo $help_debug; ?>"><?php echo $entry_debug; ?></span></label>
                                <div class="col-sm-10">
                                    <select name="mycelium_debug" id="input-debug" class="form-control">
                                        <?php if ($mycelium_debug) { ?>
                                        <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                        <option value="0"><?php echo $text_disabled; ?></option>
                                        <?php } else { ?>
                                        <option value="1"><?php echo $text_enabled; ?></option>
                                        <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="tab-log">
                            <p>
                                <pre id="mycelium_logs" class="form-control"><?php echo $log; ?></pre>
                            </p>
                            <div class="text-right"><a href="<?php echo $url_clear; ?>" class="btn btn-danger"><i class="fa fa-eraser"></i> <?php echo $button_clear; ?></a></div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<style>
    .btn-mycelium {
        color: #fff;
        background-color: #002855;
        border-color: #000F3C;
    }
    
    .btn-mycelium:hover,
    .btn-mycelium:focus,
    .btn-mycelium:active {
        color: #fff;
        background-color: #000F3C;
        border-color: #000023;
    }
    
    #mycelium_logs {
        overflow: scroll;
        white-space: nowrap;
        height: 15em;
    }
    
    .bp-log-date {
        font-size: 12px;
    }
    
    .bp-log-level {
        font-weight: bold;
    }
    
    .bp-log-locale {
        font-weight: bold;
    }
    
    .bp-log-locale>span {
        color: #888;
        font-weight: normal;
        font-style: italic;
    }
    
    .bp-log-locale>span>span {
        color: #c55;
    }
    
    .bp-log-error>.bp-log-level>span {
        color: #a94442;
    }
    
    .bp-log-warn>.bp-log-level>span {
        color: #aa6708;
    }
    
    .bp-log-info>.bp-log-level>span {
        color: #31708f;
    }
    
    .bp-log-trace>.bp-log-level>span {
        color: #777;
    }
    
    #mycelium_disconnect {
        border-radius: 3px;
    }
</style>

<?php echo $footer; ?>
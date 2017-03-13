<?php
$_['heading_title']          = 'Mycelium';

// Text
$_['text_payment']           = 'Payment';
$_['text_success']           = 'Success: You have modified the Mycelium payment module!';
$_['text_edit']              = 'Edit Mycelium';
$_['text_changes']           = 'There are unsaved changes.';
$_['text_general']           = 'General';
$_['text_statuses']          = 'Order Statuses';
$_['text_advanced']          = 'Advanced';
$_['text_all_geo_zones']     = 'All Geo Zones';
$_['text_yes']               = 'Yes';
$_['text_no']                = 'No';
$_['text_are_you_sure']      = 'Are you sure?';

// Tab
$_['tab_settings']           = 'Settings';
$_['tab_log']                = 'Log';

// Button
$_['button_copy_from']       = 'Import settings from Mycelium';
$_['button_continue']        = 'Continue';


// Entry
$_['entry_gateway_name']     = 'Gateway Name';
$_['entry_gateway_id']       = 'Gateway ID';
$_['entry_gateway_secret']   = 'Gateway Secret';
$_['entry_xpub']			 = 'XPUB';
$_['entry_confirmations']    = 'Number of confirmations required';
$_['entry_default_currency'] = 'Default Currency';
$_['entry_active']			 = 'Gateway is active';
$_['entry_callback_url']     = 'Callback URL';
$_['entry_expiration_period']= 'Orders expiration period';
$_['entry_return_url']       = 'Return URL';
$_['entry_back_url']         = 'Back/Cancel URL';
$_['entry_expiration_period']= 'Order expiration period';
$_['entry_sort_order']       = 'Sort Order';
$_['entry_geo_zone']         = 'Geo Zone';
$_['entry_status']           = 'Status';
$_['entry_paid_status']      = 'Paid Status';
$_['entry_complete_status']  = 'Complete Status';
$_['entry_debug']            = 'Debug Logging';
$_['entry_shifty']           = 'Enable Shifty button';
$_['entry_reuse_time']       = 'Address reuse interval';

// Help
$_['help_xpub']              = 'The xpub thing';
$_['help_expiration_period'] = 'The number of seconds the order will be payable (max 48h).';
$_['help_gateway_name']      = 'The name of the Mycelium Gateway you are going to use to handle payments.';
$_['help_gateway_id']        = 'The unique identifier of your Mycelium Gateway.';
$_['help_gateway_secret']    = 'The secret key of your Mycelium Gateway. This is needed to authenticate the requests to their API.';
$_['help_confirmations']     = 'The number of confirmations needed to receive the callback';
$_['help_callback_url']      = 'The URL where transaction notifications will be sent to.';
$_['help_back_url']          = 'The URL where a user will be redirected in case of cancellation, order expiration or error.';
$_['help_return_url']        = 'The URL where a user will be redirected at successful payments.';
$_['help_shifty']            = 'If enabled users will be able to pay invoices with altcoins through ShapeShift.';
$_['help_reuse_time']        = 'Mycelium orders use the same address more than once. Set the minimum amount of time to wait before using an previously used address again.';


$_['help_paid_status']       = 'A fully paid invoice awaiting confirmation';
$_['help_confirmed_status']  = 'A confirmed invoice with based on the number of confirmations set for your Gateway.';
$_['help_complete_status']   = 'A confirmed invoice with based on the number of confirmations set for your Gateway.';
$_['help_notify_url']        = 'Mycelium will post invoice status updates to this URL';
$_['help_return_url']        = 'User will be redirected here after a successful payment.';
$_['help_debug']             = 'Enabling debug will write sensitive data to a log file. You should always disable unless instructed otherwise';

// Success
$_['success_clear']           = 'Success: The Mycelium log has been cleared';

// Warning
$_['warning_permission']        = 'Warning: You do not have permission to modify the Mycelium payment module.';

// Error
$_['error_status']               = 'Some needed parameters are missing. Check that the Gateway Secret, and Gateway ID are set correctly.';
$_['error_notify_url']           = '`Notification URL` needs to be a valid URL';
$_['error_return_url']           = '`Return URL` needs to be a valid URL';
$_['error_back_url']             = '`Back URL` needs to be a valid URL';

// Log
$_['log_unable_to_connect']      = 'Unable to connect to Mycelium';

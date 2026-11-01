<?php

$GLOBALS['TL_LANG']['tl_calendar_events_payment_provider']['title'] = ['Title', 'A name for this provider, e.g. "Stripe (test)".'];
$GLOBALS['TL_LANG']['tl_calendar_events_payment_provider']['type'] = ['Provider', 'The payment service. Card details are entered on the provider\'s own page, never on this site.'];
$GLOBALS['TL_LANG']['tl_calendar_events_payment_provider']['typeRef'] = ['stripe' => 'Stripe', 'paypal' => 'PayPal', 'mollie' => 'Mollie', 'govukpay' => 'GOV.UK Pay'];
$GLOBALS['TL_LANG']['tl_calendar_events_payment_provider']['live'] = ['Live mode', 'Take real payments. Leave unticked to use the PayPal sandbox.'];
$GLOBALS['TL_LANG']['tl_calendar_events_payment_provider']['currency'] = ['Currency', 'Three-letter currency code, e.g. GBP.'];
$GLOBALS['TL_LANG']['tl_calendar_events_payment_provider']['secretKey'] = ['Secret key', 'The Stripe secret key (sk_test_… for test mode, sk_live_… for live payments).'];
$GLOBALS['TL_LANG']['tl_calendar_events_payment_provider']['clientId'] = ['Client ID', 'The client ID of the PayPal REST app.'];
$GLOBALS['TL_LANG']['tl_calendar_events_payment_provider']['clientSecret'] = ['Secret', 'The secret of the PayPal REST app.'];
$GLOBALS['TL_LANG']['tl_calendar_events_payment_provider']['apiKey'] = ['API key', 'The API key (test keys take test payments, live keys real ones).'];
$GLOBALS['TL_LANG']['tl_calendar_events_payment_provider']['title_legend'] = 'Provider';
$GLOBALS['TL_LANG']['tl_calendar_events_payment_provider']['config_legend'] = 'Account';
$GLOBALS['TL_LANG']['tl_calendar_events_payment_provider']['new'] = ['New provider', 'Set up a new payment provider'];
$GLOBALS['TL_LANG']['tl_calendar_events_payment_provider']['webhookUrl'] = 'Webhook URL';
$GLOBALS['TL_LANG']['tl_calendar_events_payment_provider']['published'] = ['Active', 'Untick to switch this provider off without deleting it. It is no longer offered on new bookings, but its settings stay and payments already made with it can still be refunded and updated.'];
$GLOBALS['TL_LANG']['tl_calendar_events_payment_provider']['publish_legend'] = 'Status';
$GLOBALS['TL_LANG']['tl_calendar_events_payment_provider']['inactive'] = 'inactive';
$GLOBALS['TL_LANG']['tl_calendar_events_payment_provider']['toggle'] = ['Active or inactive', 'Switch provider ID %s on or off'];
$GLOBALS['TL_LANG']['tl_calendar_events_payment_provider']['detailsMissing'] = 'details missing';
$GLOBALS['TL_LANG']['tl_calendar_events_payment_provider']['activeNeedsDetails'] = 'Enter the account details first (%s), then this provider can be made active.';
$GLOBALS['TL_LANG']['tl_calendar_events_payment_provider']['typeText'] = ['stripe' => 'Cards, Apple Pay and Google Pay', 'paypal' => 'PayPal checkout', 'mollie' => 'Cards and other methods through Mollie', 'govukpay' => 'The UK government payment service'];

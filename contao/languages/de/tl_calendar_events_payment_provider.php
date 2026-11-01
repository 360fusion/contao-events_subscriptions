<?php

$GLOBALS['TL_LANG']['tl_calendar_events_payment_provider']['title'] = ['Titel', 'Ein Name für diesen Anbieter, z. B. "Stripe (Test)".'];
$GLOBALS['TL_LANG']['tl_calendar_events_payment_provider']['type'] = ['Anbieter', 'Der Zahlungsdienst. Kartendaten werden auf der Seite des Anbieters eingegeben, nie auf dieser Website.'];
$GLOBALS['TL_LANG']['tl_calendar_events_payment_provider']['typeRef'] = ['stripe' => 'Stripe', 'paypal' => 'PayPal', 'mollie' => 'Mollie', 'govukpay' => 'GOV.UK Pay'];
$GLOBALS['TL_LANG']['tl_calendar_events_payment_provider']['live'] = ['Live-Modus', 'Echte Zahlungen annehmen. Nicht angekreuzt wird die PayPal-Sandbox verwendet.'];
$GLOBALS['TL_LANG']['tl_calendar_events_payment_provider']['currency'] = ['Währung', 'Dreistelliger Währungscode, z. B. EUR.'];
$GLOBALS['TL_LANG']['tl_calendar_events_payment_provider']['secretKey'] = ['Geheimer Schlüssel', 'Der geheime Stripe-Schlüssel (sk_test_… für Tests, sk_live_… für echte Zahlungen).'];
$GLOBALS['TL_LANG']['tl_calendar_events_payment_provider']['clientId'] = ['Client-ID', 'Die Client-ID der PayPal-REST-App.'];
$GLOBALS['TL_LANG']['tl_calendar_events_payment_provider']['clientSecret'] = ['Secret', 'Das Secret der PayPal-REST-App.'];
$GLOBALS['TL_LANG']['tl_calendar_events_payment_provider']['apiKey'] = ['API-Schlüssel', 'Der API-Schlüssel (Test-Schlüssel für Testzahlungen, Live-Schlüssel für echte).'];
$GLOBALS['TL_LANG']['tl_calendar_events_payment_provider']['title_legend'] = 'Anbieter';
$GLOBALS['TL_LANG']['tl_calendar_events_payment_provider']['config_legend'] = 'Konto';
$GLOBALS['TL_LANG']['tl_calendar_events_payment_provider']['new'] = ['Neuer Anbieter', 'Einen neuen Zahlungsanbieter einrichten'];
$GLOBALS['TL_LANG']['tl_calendar_events_payment_provider']['webhookUrl'] = 'Webhook-URL';
$GLOBALS['TL_LANG']['tl_calendar_events_payment_provider']['published'] = ['Aktiv', 'Abwählen, um diesen Anbieter auszuschalten, ohne ihn zu löschen. Er wird bei neuen Anmeldungen nicht mehr angeboten, seine Einstellungen bleiben und bereits damit getätigte Zahlungen können weiterhin erstattet und aktualisiert werden.'];
$GLOBALS['TL_LANG']['tl_calendar_events_payment_provider']['publish_legend'] = 'Status';
$GLOBALS['TL_LANG']['tl_calendar_events_payment_provider']['inactive'] = 'inaktiv';
$GLOBALS['TL_LANG']['tl_calendar_events_payment_provider']['toggle'] = ['Aktiv oder inaktiv', 'Anbieter ID %s ein- oder ausschalten'];
$GLOBALS['TL_LANG']['tl_calendar_events_payment_provider']['detailsMissing'] = 'Zugangsdaten fehlen';
$GLOBALS['TL_LANG']['tl_calendar_events_payment_provider']['activeNeedsDetails'] = 'Tragen Sie zuerst die Zugangsdaten ein (%s), dann kann dieser Anbieter aktiviert werden.';
$GLOBALS['TL_LANG']['tl_calendar_events_payment_provider']['typeText'] = ['stripe' => 'Karten, Apple Pay und Google Pay', 'paypal' => 'PayPal-Kasse', 'mollie' => 'Karten und weitere Zahlarten über Mollie', 'govukpay' => 'Der Zahlungsdienst der britischen Regierung'];

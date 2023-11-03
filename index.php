<?php

use ApiSaleLibrary\Services\SaleService;

require 'vendor/autoload.php';

$data = '{ "action":"SALE",
           "client_key":"-f8f5-11ea-976a-0242c0a85007",
           "order_id":"ORDER32325647",
           "order_amount":"1.99",
           "order_currency":"USD",
           "order_description":"Product",
           "card_number":"4111111111111111",
           "card_exp_month":"01",
           "card_exp_year":"2025",
           "card_cvv2":"123",
           "payer_first_name":"John",
           "payer_last_name":"Doe",
           "payer_address":"BigStreet",
           "payer_country":"US",
           "payer_state":"CA",
            "payer_city":"City",    
            "payer_zip":"123456",
            "payer_email":"kukarowwwww@gmail.com",
            "payer_phone":"199999999",
            "payer_ip":"157.90.182.5",
            "term_url_3ds":"http://client.site.com/return.php",
             "hash":"52e01c3b41ec432c63bab4df6ea96687"
     }';

$array = json_decode($data, true);
$initService = new SaleService('d0ec0beca8a3c30652746925d5380cf3', '5b6492f0-f8f5-11ea-976a-0242c0a85007');
$initService->setEndpoint('https://dev-api.rafinita.com/post');
$initService->setData($array);
print_r($initService->send());

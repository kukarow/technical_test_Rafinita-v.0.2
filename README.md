
>[!tip]
>###### Це тестове завдання (плюс у вкладенні документація до тестового завдання) Завдання: Реалізувати бібліотеку для взаємодії із зовнішнім API за наданою документацією. Функціональні вимоги: реалізувати тільки SALE запит Технічні вимоги: реалізувати інтерфейс для запиту реалізувати інтерфейс для відповіді за яким можна було б оцінити результат виконаного запиту Вимоги до якості коду: Код повинен проходити нижче перераховані інспекції: PHP_CodeSniffer (PSR12) PHP Mess Detector PHP Copy Paste Detector Public Key: 5b6492f0-f8f5-11ea-976a-0242c0a85007 Pass: d0ec0beca8a3c30652746925d5380cf3 (edited) API: [https://dev-api.rafinita.com/post](https://dev-api.rafinita.com/post)

---

**Структура проєкта -**
```bash
/sale-api-client
    /src
	    /Contracts
	    /Services
    /tests
    index.php
    phpcs.xml
    phpmd.xml
    composer.json
    README.md

```
**Зміст `composer.json`**
```json
{  
  "name": "username/api-sale-library",  
  "description": "Library for request to an external API",  
  "type": "library",  
  "require-dev": {  
    "squizlabs/php_codesniffer": "^3.7",  
    "phpmd/phpmd": "^2.14",  
    "sebastian/phpcpd": "^6.0",  
    "guzzlehttp/guzzle": "^7.0"  
  },  
  "autoload": {  
    "psr-4": {  
      "ApiSaleLibrary\\": "src/"  
    }  
  },  
  "scripts": {  
    "check-style": "./vendor/bin/phpcs",  
    "mess-detect": "./vendor/bin/phpmd src,tests text phpmd.xml",  
    "copy-paste-detect": "./vendor/bin/phpcpd src/ tests/",  
    "test": [  
      "@phpcs",  
      "@phpmd",  
      "@phpcpd"  
    ],  
    "phpcs": "phpcs --standard=phpcs.xml ./src",  
    "phpmd": "phpmd ./src text phpmd.xml",  
    "phpcpd": "phpcpd ./src"  
  }  
}
```

---

Зібраний тестовий варіант для тестування карток .
даний варіант передбачає, що з боку клієнтської частини дані, що відправляються, пройшли всі необхідні тести - перевірки , а з боку серверної частини необхідно згенерувати новий `hash` для картки , в залежності від  `payer_email`- (в картці клієнта), `card_number` - (в картці клієнта) та приватного ключа , також підміна `client_key` (в картці клієнта)

**Зверніть увагу , в цьому варіанті - поля картки клієнта `hash` та `client_key`
не додаються, а змінюються значення данних полей , так як вважаєтся що вони вже є.**  

**Підготовленні дані мають таку форму :**
```json
  { "action":"SALE",  
           "client_key":"-f8f5-11ea-976a-0242c0a85007",
           "order_id":"ORDER32325608", 
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
```
І**ніціалізація запиту на `SALE` -**

1. Підготовка отриманих данних від клієнта 
 ```php
 //Данні клієнта (скорочено)
 $data = '{ "action":"SALE"......}';
 //Декодування данних клієнта
 $array = json_decode($data, true);
```
2. Передача `private_key` та `public_key`
```php
$initService = new SaleService('private_key', 'public_key');
```
3. Передача `setEndpoint`
```php
$initService->setEndpoint('setEndpoint');
```
4. Передача декодованих даних отриманих раніше від клієнта
```php
$initService->setData($array);
```
5. Відправка запиту на `SALE`
```php
$initService->send();
```
**Приклад знаходиться в `index.php`**

---


**Відповідь -**
```php
/bin/php /technical_test_(Rafinita)v.0.1/index.php
ApiSaleLibrary\Services\ResponseService Object
(
    [statusCode:protected] => 200
    [body:protected] => Array
        (
            [action] => SALE
            [result] => SUCCESS
            [status] => SETTLED
            [order_id] => ORDER32325608
            [trans_id] => a017e8a8-79b1-11ee-8158-0242ac120005
            [trans_date] => 2023-11-02 18:57:09
            [descriptor] => Descriptor
            [amount] => 1.99
            [currency] => USD
        )

)

```

---


**Генерація `hesh` має вигляд під капотом -**
```php
protected function setHash(): string  
{  
    //generate hash  
    $hashInput = strtoupper(  
        strrev($this->clientData['payer_email']) .  
        $this->passwordKey .  
        strrev(substr($this->clientData['card_number'], 0, 6) . substr($this->clientData['card_number'], -4))  
    );  
  
    return md5($hashInput);  
}
```
**Заміна `client_key`**
```php
protected function setClientKey(): string  
{  
    //functionality may be expanded  
    return $this->publicKey;  
}
```
**Вигляд методу `send` для формування запиту(як приклад) -**
```php
public function send(): RafinitaResponseInterface  
{  
    $this->clientData['hash'] = $this->setHash();  
    $this->clientData['client_key'] = $this->setClientKey();  
    $headers = ['Content-Type' => 'application/x-www-form-urlencoded'];  
    $options = ['form_params' => $this->clientData];  
    try {  
        $client = new Client();  
        $request = new Request('POST', $this->apiEndpoint, $headers);  
        $res = $client->sendAsync($request, $options)->wait();  
  
        return new ResponseService($res->getStatusCode(), json_decode((string)$res->getBody(), true));  
    } catch (ClientException $e) {  
        return new ResponseService($e->getCode(), json_decode((string)$e->getResponse()->getBody(), true));  
    }  
}
```
**Потенційний вигляд невдалої `SALE` операції, помилки (дублікат) -**
```php
/bin/php /technical_test_(Rafinita)v.0.1/index.php
ApiSaleLibrary\Services\ResponseService Object
(
    [statusCode:protected] => 400
    [body:protected] => Array
        (
            [result] => ERROR
            [error_code] => 100000
            [error_message] => Request data is invalid.
            [errors] => Array
                (
                    [0] => Array
                        (
                            [error_code] => 100000
                            [error_message] => order_id: Duplicate payment.
                        )

                )

        )

)
```
**Потенційний вигляд вдалої `SALE` операції-**
```php
/bin/php /technical_test_(Rafinita)v.0.1/index.php
ApiSaleLibrary\Services\ResponseService Object
(
    [statusCode:protected] => 200
    [body:protected] => Array
        (
            [action] => SALE
            [result] => SUCCESS
            [status] => SETTLED
            [order_id] => ORDER32325647
            [trans_id] => de5684ee-7a47-11ee-bf92-0242ac120005
            [trans_date] => 2023-11-03 12:52:38
            [descriptor] => Descriptor
            [amount] => 1.99
            [currency] => USD
        )

)
```
**Додаткові джерела -** 
[https://docs.montypay.com/s2s_card#sale-request](https://docs.montypay.com/s2s_card#sale-request)

[https://docs.montypay.com/s2s_apm#sale-request](https://docs.montypay.com/s2s_apm#sale-request)


## About the project

I've written the code on the Ubuntu 22.04 platform, using local MySQL server, version 5.7, and local redis server. Locally I also have installed
PHP 8.2, so in order to test it, you will have to have this version of PHP locally installed. I've installed Symfony version 5, OpenApi and Doctrine 2.
I have one custom api resource ConversionRate which is not an entity, the remaining entities are not api resources. I have one data provider
for the api resource and two filter fields: base_currency and target_currencies
I was using Guzzle when I was making http requests. I also wrote some tests using PHP Unit 9.5.
The next step is to fill in the .env.local file, you have example files (.env and .env.test) with the needed variables.
Just make sure to insert your local values. I've used this api ``https://www.exchangerate-api.com`` to get the data.
In order to use it you will have to get an api key and store it in the .env.local file.

After that run these commands:

``composer install``
``php bin/console doctrine:database:create``
``php bin/console doctrine:migrations:migrate``
``symfony serve -d``

Then import some data with the console command that I wrote (run this):

``php bin/console app:currency:rates EUR EUR USD GBP -vvv``

If you want this command to run every day at 01:00 AM first execute ``crontab -e``

and put this expression in the crontab file
``0 1 * * * /usr/bin/php /home/bojan/Dev/currency_exchange_platform/bin/console app:currency:rates EUR USD GBP -vvv``

then visit this url

``http://localhost:8000/api/``

and try the endpoint with the requested query params. The url should look like this

``http://127.0.0.1:8000/api/exchange-rates?page=1&base_currency=EUR&target_currencies=EUR%2CUSD%2CGBP``


# Oschadpay

Oschadpay payment gateway integration.

## Installation

### Manually

* Create a free Oschadpay account https://oschadpay.com.ua/mportal/ 
* Copy the module folder into the ```<thelia_root>/local/modules/``` directory and make sure that the name of the folder is ```Oschadpay``` or create zip archive which contains folder ```Oschadpay/{all files from this repository}``` and install from administration side.
* Activate Oschadpay in your Thelia administration panel.

### Composer

Add it in your main Thelia composer.json file

```
composer config repositories.cloudipsp git https://github.com/cloudipsp/thelia.git
composer require cloudipsp/thelia
```

### How to use

To use this module, your first need to activate if in the Back-office, tab Modules,
then click on "Configure" on the Oschadpay module line. Enter your Merchant_id, secret_key and save.

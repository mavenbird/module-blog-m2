# How to install Blog for Magento 2

There are 2 different solutions to install Mavenbird extensions:

- Solution #1. Install via Composer (Recommend)
- Solution #2: Ready to paste (Not recommend)

## Important:
- We recommend you to duplicate your live store on a staging/test site and try installation on it in advanced.
- Back up Magento files and the store database.
- This extension requires [Mavenbird_Core](https://github.com/mavenbird/module-core) to be installed first.

You will get an error, if **Mavenbird_Core** is not installed.

## Solution #1. Install via Composer (Recommend)

Run the following command in Magento 2 root folder:

```
composer require mavenbird/magento-2-blog-extension
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy
```

## Solution #2: Ready to paste (Not recommend)

Please make sure that you've [installed Mavenbird_Core module](https://github.com/mavenbird/module-core#how-to-install--upgrade-mavenbird_core) already.

If you don't want to install via composer, you can use this way. 

- Download [the latest version here](https://github.com/mavenbird/magento-2-blog/archive/master.zip) 
- Extract `master.zip` file to `app/code/Mavenbird/Blog`; You should create a folder path `app/code/Mavenbird/Blog` if not exist.
- Go to Magento root folder and run upgrade command line to install `Mavenbird_Blog`:

```
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy
```

## FAQs

Q: I got this error message: 
```
Fatal error: Uncaught Error: Call to undefined method Mavenbird\\Blog\\Helper\\Data::isEnabled()
```
A: Your store installed Mavenbird Core old version, please [upgrade it](https://github.com/mavenbird/module-core#12-upgrade).

Q: I got an error message:

```
Mavenbird_Core has been already defined
```
A: Mavenbird Core need to be installed, [learn more](ttps://github.com/mavenbird/module-core#how-to-install--upgrade-mavenbird_core).


Q: Install Core Module.

A: Our Core module is updated frequently so make sure that you are using [the latest version](https://github.com/mavenbird/module-core) of it.


Other messages that indicate missing Core module are: 

```
- Mavenbird\Core\Helper\AbstractData does not exist.
- Class Mavenbird\<extension_name>\Helper\Data does not exist.
- Specified invalid parent id (Mavenbird_Core::menu)
- Call to undefined method Mavenbird\\PdfInvoice\\Helper\\Config::jsonEncode
```

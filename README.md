# wp-cli test command

Install and setup Unit Tests with wp-cli

## Usage

```bash
wp test setup theme [options]
```

* ```[--data=<wxr>]``` custom WXR file location/URL
* ```[--reset]``` reset WP database

* ```--url=<url>``` if reset, WP install url
* ```--title=<site-title>``` WP install title
* ```--admin_email=<email>``` WP admin email
* ```--admin_password=<password>``` WP admin password
* ```[--admin_name=<username>]``` WP admin username


## Installation

Composer installation as referenced in [Community Packages setup](https://github.com/wp-cli/wp-cli/wiki/Community-Packages).

0) [Install wp-cli](http://wp-cli.org#install)

1) Go to the directory containing the root `composer.json` file:

```bash
cd ~/.composer
```

2) Add the repository containing the desired package:

```bash
composer config repositories.unit_test vcs https://github.com/pixline/wp-cli-test-command
```

3) Install the package:

```bash
composer require pixline/wp-cli-test-command=dev-master
```

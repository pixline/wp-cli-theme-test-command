# wp-cli theme-test command

Install and configure Theme "unit tests" (sample data, required plugins and options) with wp-cli

## Usage

```
wp theme-test setup [options]

[--data=<url|path>]     URL/path to WXR data file

[--reset]               Reset and install a clean WordPress
  --url=<url>                 Blog URL  
  --title=<title>             Blog Title  
  --admin_name=<username>     Admin username
  --admin_email=<email>       Admin email address
  --admin_password=<password> Admin password

[--menus]               Add (optional) custom nav menu
                        (default dataset have them already)



## Installation

Composer installation as referenced in [wp-cli Community Packages setup](https://github.com/wp-cli/wp-cli/wiki/Community-Packages).

0) [Install wp-cli](http://wp-cli.org#install)

1) Go to the directory containing the root `composer.json` file:

```bash
cd ~/.composer
```

2) Add the repository containing the desired package:

```bash
composer config repositories.theme_test vcs https://github.com/pixline/wp-cli-test-command
```

3) Install the package:

```bash
composer require pixline/wp-cli-test-command=dev-master
```

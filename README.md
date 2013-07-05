# wp-cli test command

Install and configure Unit Tests (theme, plugins, core) with wp-cli

## Usage

```
wp test install theme [options]
--data=<url|path>       URL/path to WXR data file

--reset                 Reset and install a clean WordPress
  --url=""              Blog URL  
  --title=""            Blog Title  
  --admin_name=""       Admin username
  --admin_email=""      Admin email address
  --admin_password=""   Admin password

--menus                 Add custom nav menu (built-in in default WXR)
```

```
wp test install plugin <slug>
```

```
wp test install core [options]

--dbname=""   Test db name
--dbuser=""   Test db username
--dbpass=""   Test db password
```


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
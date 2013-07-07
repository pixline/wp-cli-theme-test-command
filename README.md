# wp-cli theme-test command

[![Build Status - master](https://travis-ci.org/pixline/wp-cli-theme-test-command.png?branch=master)](https://travis-ci.org/pixline/wp-cli-theme-test-command)

Install and configure Theme "unit tests" (sample data, required plugins and options) with wp-cli

http://codex.wordpress.org/Theme_Unit_Test

## Usage

```
wp theme-test setup [options]

[--data=<data>]      URL/path to custom WXR data file, or built-in:

                    ( unit-test* | wpcom-theme | wpcom-demo | wptest | skip )  

                    'unit-test' = Default Theme Unit Test datafile
                    'wpcom-theme' = Alternative wpcom datafile (1)
                    'wpcom-demo' = Alternative wpcom datafile (2)
                    'wptest' = manovotny/wptest datafile

[--plugin=<plugin>]  Install and activate required plugin set,
                     as featured in the "developer" WP plugin

                     ( theme* | vip | devel | all | skip )
                     
                     'theme' = Default plugin setup
                     'vip' = Default + wpcom VIP plugin setup
                     'devel' = Default + developer plugin setup
                     'all' = Default + VIP + developer

[--option=<option>]  Update blog options
                    ( default* | skip )

[--menus]            Add (optional) custom nav menus
```

It can be used to reset and reinstall WP test site programmatically, as in [this simple script](https://gist.github.com/pixline/5937737)


## Installation

### Default install: wp-cli + composer

Composer installation as referenced in [wp-cli Community Packages setup](https://github.com/wp-cli/wp-cli/wiki/Community-Packages).

0) Make sure to have [wp-cli](http://wp-cli.org#install) already installed.

1) Go to the directory containing the root `composer.json` file:

```bash
cd ~/.wp-cli
```

2) Add the repository containing the desired package:

```bash
composer config repositories.theme_test vcs https://github.com/pixline/wp-cli-theme-test-command
```

3) Install the package:

```bash
composer require pixline/wp-cli-theme-test-command=dev-master
```

### Alternative install: git + composer method

```bash
git clone https://github.com/pixline/wp-cli-theme-test-command
cd wp-cli-theme-test-command
composer install --dev --prefer-source

## append this line to .bash_profile or .zshrc.local
export PATH="/full/path/to/wp-cli-theme-test-command/vendor/bin:$PATH"

wp theme-test install
```


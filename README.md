# wp-cli theme-test command

[![Build Status - master](https://travis-ci.org/pixline/wp-cli-theme-test-command.png?branch=master)](https://travis-ci.org/pixline/wp-cli-theme-test-command)

Install and configure Theme "unit tests" (sample data, required plugins and options) with wp-cli

http://codex.wordpress.org/Theme_Unit_Test

## Usage

NOTE: This command can't deal (yet?) with global flags like ```--path```, please run it in the WordPress root folder only.

```
wp theme-test install [options]

Angle brackets groups possible values, default is marked with *

[--data=< unit-test* | wpcom-theme | wpcom-demo | wptest | skip >]

	URL/path to custom WXR data file, or built-in:

	'unit-test' = Default Theme Unit Test datafile
	'wpcom-theme' = Alternative wpcom datafile (1)
	'wpcom-demo' = Alternative wpcom datafile (2)
	'wptest' = manovotny/wptest datafile
	'skip' = Do not install new data

[--plugin=< theme* | vip | devel | all | skip >]  

	Plugin bundle to install. 
	Bundles are as suggested in the 'developer' plugin,
	'debug' bundle is an exclusive feature :-) 

	'theme' = Default plugin setup
	'vip' = Default + wpcom VIP plugin setup
	'devel' = Default + plugin developer setup
	'debug' = Default + debugger setup
	'all' = Default + VIP + developer + debugger!
	'skip' = Do not install/activate plugin bundles

[--option=< default* | skip >]
	

	Updates blog options to the test default values.

	'skip' = Do not update options
	'default' = Updates options to their default test value:

		- blogname                WordPress Theme Unit Test Site
		- posts_per_page          5
		- thread_comments         1
		- thread_comments_depth   3
		- page_comments           1
		- comments_per_page       5
		- medium_max_w            null
		- medium_max_h            null
		- large_max_w             null
		- large_max_h             null
		- permalink_structure     /%year%/%monthnum%/%day%/%postname%/

[--menus]            Add custom nav menus

```

Can be used to "sandbox" a *local* WordPress install programmatically, i.e. :

```bash
#!/bin/bash

wp db reset
wp core install --url=http://localhost --title="WP" --admin_name=test --admin_email=test@example.org --admin_password=test
wp theme-test install --data=unit-test --plugin=all --menus
```


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

### Developer install: git + composer method

```bash
git clone https://github.com/pixline/wp-cli-theme-test-command
cd wp-cli-theme-test-command
composer install --dev --prefer-source

## append this line to .bash_profile or .zshrc.local
export PATH="/full/path/to/wp-cli-theme-test-command/vendor/bin:$PATH"

wp theme-test install
```


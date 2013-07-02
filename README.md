# wp-cli test command

Install and setup Unit Tests with wp-cli

## Installation

Composer installation as referenced in [Community Packages setup](https://github.com/wp-cli/wp-cli/wiki/Community-Packages).

0) [Install wp-cli](http://wp-cli.org#install)

1) Go to the directory containing the root `composer.json` file:

```bash
cd ~/.composer
```

2) Add the repository containing the desired package:

```bash
composer config repositories.stat vcs https://github.com/danielbachhuber/wp-cli-stat-command
```

3) Install the package:

```bash
composer require danielbachhuber/wp-cli-stat-command=dev-master
```

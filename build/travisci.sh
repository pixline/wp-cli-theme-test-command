#!/bin/bash

wp core download --version=$WP_VERSION --path=$HOME/wordpress
wp core install --url=http://localhost --title="WP" --admin_name=test --admin_email=test@example.org --admin_password=test
wp theme-test install --menus

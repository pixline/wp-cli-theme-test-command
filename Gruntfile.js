module.exports = function (grunt) {

  "use strict";

  grunt.initConfig({

    pkg: grunt.file.readJSON('package.json'),

    phpcs: {
      options: {
        bin: 'phpcs -p -s -v',
        standard: 'WordPress'
      },
      main: {
        dir: './*.php'
      },
    },

    phplint: {
      main: [ 'wp-cli-test-command.php' ],
    },

    shell: {
      'phpmd': {
        command: '/usr/bin/phpmd wp-cli-test-command.php text codesize,design,naming,unusedcode',
        options: {
          stdout: true,
          failOnError: false
        }
      }
    }

  });


    grunt.loadNpmTasks('grunt-phpcs');
    grunt.loadNpmTasks('grunt-phplint');
    grunt.loadNpmTasks('grunt-shell');

    grunt.registerTask( 'lint' , [ 'phpcs', 'phplint', 'shell:phpmd' ] );
    grunt.registerTask( 'default' , [ 'lint' ] );

  };

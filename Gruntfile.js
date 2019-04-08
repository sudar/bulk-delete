/* global module, require */
module.exports = function( grunt ) {
	'use strict';

	// Load all grunt tasks
	require('matchdep').filterDev('grunt-*').forEach(grunt.loadNpmTasks);

	// Project configuration
	grunt.initConfig( {
		pkg:    grunt.file.readJSON( 'package.json' ),

		jshint: {
			browser: {
				files: {
					src: [ 'assets/js/src/*.js' ]
				},
				options: {
					jshintrc: '.jshintrc'
				}
			},
			grunt: {
				files:{
					src: [ 'Gruntfile.js' ]
				},
				options: {
					jshintrc: '.gruntjshintrc'
				}
			}
		},

		concat: {
			options: {
				stripBanners: true,
				banner: '/*! <%= pkg.title %> - v<%= pkg.version %> %>\n' +
					' * <%= pkg.homepage %>\n' +
					' * Copyright (c) <%= grunt.template.today("yyyy") %>;' +
					' * Licensed GPLv2+' +
					' */\n',
				sourceMap: true
			},
			css: {
				files: {
					'assets/css/bulk-delete.css': ['assets/css/src/**/*.css']
				}
			},
			scripts: {
				files: {
					'assets/js/bulk-delete.js': ['assets/js/src/**/*.js']
				}
			}
		},

		uglify: {
			all: {
				options: {
					banner: '/*! <%= pkg.title %> - v<%= pkg.version %> \n' +
						' * <%= pkg.homepage %>\n' +
						' * Copyright (c) <%= grunt.template.today("yyyy") %>;' +
						' * Licensed GPLv2+' +
						' */\n',
					mangle: {
						reserved: ['jQuery', 'BulkWP']
					}
				},
				files: {
					'assets/js/bulk-delete.min.js': ['assets/js/bulk-delete.js']
				}
			}
		},

		cssmin: {
			minify: {
				src: 'assets/css/bulk-delete.css',
				dest: 'assets/css/bulk-delete.min.css'
			}
		},

		watch:  {
			scripts: {
				files: ['assets/js/src/**/*.js'],
				tasks: ['jshint:browser', 'concat', 'uglify'],
				options: {
					debounceDelay: 500
				}
			},
			css: {
				files: ['assets/css/src/**/*.css'],
				tasks: ['concat', 'cssmin'],
				options: {
					debounceDelay: 500
				}
			},
			build: {
				files: ['**', '!dist/**'],
				tasks: ['build'],
				options: {
					debounceDelay: 500
				}
			}
		},

		clean   : {
			dist: ['dist/']
		},

		copy: {
			timepicker: {
				files: [{
					src : 'node_modules/jquery-ui-timepicker-addon/dist/jquery-ui-timepicker-addon.min.js',
					dest: 'assets/js/jquery-ui-timepicker-addon.min.js'
				},
				{
					src : 'node_modules/jquery-ui-timepicker-addon/dist/jquery-ui-timepicker-addon.min.css',
					dest: 'assets/css/jquery-ui-timepicker-addon.min.css'
				}]
			},
			select2: {
				files: [{
					src : 'node_modules/select2/dist/js/select2.min.js',
					dest: 'assets/js/select2.min.js'
				},
				{
					src : 'node_modules/select2/dist/css/select2.min.css',
					dest: 'assets/css/select2.min.css'
				}]
			},
			jquery_ui: {
				files: [
					{
						src : 'node_modules/jquery-ui-built-themes/smoothness/jquery-ui.min.css',
						dest: 'assets/css/jquery-ui-smoothness.min.css'
					},
					{
						expand: true,
						src: ['node_modules/jquery-ui-built-themes/smoothness/images/*'],
						dest: 'assets/css/images/',
						flatten: true,
						filter: 'isFile'
					},
				]
			},
			dist: {
				files : [
					{
						expand: true,
						src: [
							'assets/**',
							'!assets/css/src/**',
							'!assets/js/src/**',
							'!assets/vendor/**',
							'include/**',
							'languages/**',
							'vendor/sudar/wp-system-info/**',
							'bulk-delete.php',
							'HISTORY.md',
							'load-bulk-delete.php',
							'README.md'
						],
						dest: 'dist/'
					}
				]
			}
		}
	} );

	require('time-grunt')(grunt);

	grunt.registerTask('default', ['jshint:browser', 'concat', 'uglify', 'cssmin']);
	grunt.registerTask('vendor', ['copy:timepicker', 'copy:select2', 'copy:jquery_ui']);
	grunt.registerTask('build', [ 'default', 'vendor', 'clean', 'copy:dist']);

	grunt.util.linefeed = '\n';
};

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
					' */\n'
			},
			bulkdelete: {
				src: 'assets/js/src/bulk-delete.js',
				dest: 'assets/js/bulk-delete.js'
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
						except: ['jQuery']
					}
				},
				files: {
					'assets/js/bulk-delete.min.js': ['assets/js/bulk-delete.js']
				}
			}
		},

		watch:  {
			scripts: {
				files: ['assets/js/src/**/*.js'],
				tasks: ['jshint:browser', 'concat', 'uglify'],
				options: {
					debounceDelay: 500
				}
			}
		},

		copy: {
			timepicker: {
				files: [{
					src : 'assets/vendor/jqueryui-timepicker-addon/dist/jquery-ui-timepicker-addon.min.js',
					dest: 'assets/js/jquery-ui-timepicker-addon.min.js'
				},
				{
					src : 'assets/vendor/jqueryui-timepicker-addon/dist/jquery-ui-timepicker-addon.min.css',
					dest: 'assets/css/jquery-ui-timepicker-addon.min.css'
				}]
			},
			select2: {
				files: [{
					src : 'assets/vendor/select2/dist/js/select2.min.js',
					dest: 'assets/js/select2.min.js'
				},
				{
					src : 'assets/vendor/select2/dist/css/select2.min.css',
					dest: 'assets/css/select2.min.css'
				}]
			}
		}
	} );

	grunt.registerTask('default', ['jshint:browser', 'concat', 'uglify']);
	grunt.registerTask('build', ['copy']);

	grunt.util.linefeed = '\n';
};

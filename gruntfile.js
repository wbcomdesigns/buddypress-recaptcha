'use strict';
module.exports = function (grunt) {

	// load all grunt tasks matching the `grunt-*` pattern
	// Ref. https://npmjs.org/package/load-grunt-tasks
	require('load-grunt-tasks')(grunt);

	// Project configuration
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),

		// Check text domain
		checktextdomain: {
			options: {
				text_domain: ['buddypress-recaptcha'], // Specify allowed domain(s)
				keywords: [ // List keyword specifications
					'__:1,2d',
					'_e:1,2d',
					'_x:1,2c,3d',
					'esc_html__:1,2d',
					'esc_html_e:1,2d',
					'esc_html_x:1,2c,3d',
					'esc_attr__:1,2d',
					'esc_attr_e:1,2d',
					'esc_attr_x:1,2c,3d',
					'_ex:1,2c,3d',
					'_n:1,2,4d',
					'_nx:1,2,4c,5d',
					'_n_noop:1,2,3d',
					'_nx_noop:1,2,3c,4d'
				]
			},
			target: {
				files: [{
					src: [
						'*.php',
						'**/*.php',
						'!node_modules/**',
						'!options/framework/**',
						'!tests/**',
						'!bp-recaptcha-update-checker/**',
						'!vendor/**'
					], // all php
					expand: true
				}]
			}
		},

		// Task for CSS minification
		cssmin: {
			public: {
				files: [{
					expand: true,
					cwd: 'public/css/', // Source directory for public CSS files
					src: ['*.css', '!*.min.css'], // Minify all CSS files except already minified ones
					dest: 'public/css/min/', // Destination directory for minified CSS
					ext: '.min.css', // Extension for minified files
				}]
			},
			wbcom: {
				files: [{
					expand: true,
					cwd: 'admin/wbcom/assets/css/', // Source directory for admin CSS files
					src: ['*.css', '!*.min.css'], // Minify all admin CSS files except already minified ones
					dest: 'admin/wbcom/assets/css/min/', // Destination directory for minified admin CSS
					ext: '.min.css', // Extension for minified files
				}]
			}
		},

		// Task for JavaScript minification
		uglify: {
			public: {
				options: {
					mangle: false, // Prevents variable name mangling
				},
				files: [{
					expand: true,
					cwd: 'public/js/', // Source directory for public JS files
					src: ['*.js', '!*.min.js'], // Minify all JS files except already minified ones
					dest: 'public/js/min/', // Destination directory for minified JS
					ext: '.min.js', // Extension for minified files
				}]
			},
			wbcom: {
				options: {
					mangle: false, // Prevents variable name mangling
				},
				files: [{
					expand: true,
					cwd: 'admin/wbcom/assets/js/', // Source directory for admin JS files
					src: ['*.js', '!*.min.js'], // Minify all admin JS files except already minified ones
					dest: 'admin/wbcom/assets/js/min/', // Destination directory for minified admin JS
					ext: '.min.js', // Extension for minified files
				}]
			}
		},

		// Task for watching file changes
		watch: {
			css: {
				files: ['public/css/*.css'], // Watch for changes in public CSS files
				tasks: ['cssmin:public'], // Run public CSS minification task
			},
			adminCss: {
				files: ['admin/wbcom/assets/css/*.css'], // Watch for changes in admin CSS files
				tasks: ['cssmin:wbcom'], // Run admin CSS minification task
			},
			js: {
				files: ['public/js/*.js'], // Watch for changes in public JS files
				tasks: ['uglify:public'], // Run public JS minification task
			},
			adminJs: {
				files: ['admin/wbcom/assets/js/*.js'], // Watch for changes in admin JS files
				tasks: ['uglify:wbcom'], // Run admin JS minification task
			},
			php: {
				files: ['**/*.php'], // Watch for changes in PHP files
				tasks: ['checktextdomain'], // Run text domain check
			}
		},

		// Task for generating RTL CSS
		rtlcss: {
			myTask: {
				options: {
					// Disable source maps
					map: false,
					// RTL CSS options
					opts: {
						clean: false
					},
					// RTL CSS plugins
					plugins: [],
					// Save unmodified files
					saveUnmodified: true,
				},
				files: [
					{
						expand: true,
						cwd: 'public/css/', // Source directory for public CSS
						src: ['*.css', '!**/*.min.css'], // Source files
						dest: 'public/css/rtl/', // Destination directory for public RTL CSS
						flatten: true // Prevents creating subdirectories
					},
					{
						expand: true,
						cwd: 'admin/wbcom/assets/css/', // Source directory for admin CSS
						src: ['*.css', '!**/*.min.css'], // Source files
						dest: 'admin/wbcom/assets/css/rtl/', // Destination directory for admin RTL CSS
						flatten: true // Prevents creating subdirectories
					}
				]
			}
		},

		// Make POT files
		makepot: {
			target: {
				options: {
					cwd: '.', // Directory of files to internationalize.
					domainPath: 'languages/', // Where to save the POT file.
					exclude: ['node_modules/*', 'vendor/*', 'options/framework/*', 'bp-recaptcha-update-checker/*'], // List of files or directories to ignore.
					mainFile: 'recaptcha-for-buddypress.php', // Main project file.
					potFilename: 'buddypress-recaptcha.pot', // Name of the POT file.
					potHeaders: { // Headers to add to the generated POT file.
						poedit: true, // Includes common Poedit headers.
						'Last-Translator': 'Wbcom Designs',
						'Language-Team': 'Wbcom Designs',
						'report-msgid-bugs-to': 'https://wbcomdesigns.com/contact/',
						'x-poedit-keywordslist': true // Include a list of all possible gettext functions.
					},
					type: 'wp-plugin', // Type of project (wp-plugin or wp-theme).
					updateTimestamp: true // Whether the POT-Creation-Date should be updated without other changes.
				}
			}
		},

		// Clean task - removes previous build files
		clean: {
			pre: {
				src: ['dist/']
			},
			post: {
				src: ['dist/buddypress-recaptcha/']
			},
			maps: {
				src: ['**/*.map', '!node_modules/**']
			}
		},

		// Copy task - copies plugin files to dist folder
		copy: {
			dist: {
				src: [
					'**',
					'!.git/**',
					'!**/.git/**',
					'!**/*.md',
					'!README.md',
					'!CHANGELOG.md',
					'!docs',
					'!docs/**',
					'!customer-docs',
					'!customer-docs/**',
					'!developer-docs',
					'!developer-docs/**',
					'!bp-recaptcha-update-checker/**',
					'!tests/**',
					'!test/**',
					'!spec/**',
					'!build.sh',
					'!gruntfile.js',
					'!Gruntfile.js',
					'!package.json',
					'!package-lock.json',
					'!node_modules/**',
					'!src/**',
					'!assets/src/**',
					'!dist/**',
					'!.git/**',
					'!.github/**',
					'!.gitignore',
					'!.gitattributes',
					'!composer.json',
					'!composer.lock',
					'!yarn.lock',
					'!webpack.config.js',
					'!gulpfile.js',
					'!phpunit.xml',
					'!phpcs.xml',
					'!phpstan.neon',
					'!psalm.xml',
					'!.phpcs.xml',
					'!.phpunit.xml',
					'!.eslintrc*',
					'!.stylelintrc*',
					'!.editorconfig',
					'!**/*.map',
					'!**/*.log',
					'!**/*.tmp',
					'!**/*.temp',
					'!.DS_Store',
					'!Thumbs.db',
					'!**/*.bak',
					'!**/*.orig',
					'!**/*~',
					'!**/#*',
					'!**/*.zip'
				],
				dest: 'dist/buddypress-recaptcha/'
			}
		},

		// Compress task - creates zip file
		compress: {
			dist: {
				options: {
					archive: 'dist/buddypress-recaptcha-<%= pkg.version %>.zip',
					mode: 'zip'
				},
				files: [{
					expand: true,
					cwd: 'dist/',
					src: ['buddypress-recaptcha/**'],
					dest: '/'
				}]
			}
		}
	});

	// Register default task
	grunt.registerTask('default', ['checktextdomain', 'makepot']);

	// Build task - creates distribution zip file
	grunt.registerTask('build', [
		'clean:pre',    // Clean previous builds and old zip files
		'rtlcss',       // Generate RTL CSS first
		'clean:maps',   // Remove any .map files generated by rtlcss
		'cssmin',       // Minify all CSS
		'uglify',       // Minify all JS
		'makepot',      // Generate POT file
		'copy:dist',    // Copy plugin files to dist folder
		'compress:dist', // Create versioned zip file
		'clean:post'    // Clean up dist folder, keeping only the zip
	]);
};

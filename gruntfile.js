module.exports = function (grunt) {
   // Do grunt-related things in here

   grunt.loadNpmTasks('grunt-contrib-jshint');
   grunt.loadNpmTasks('grunt-contrib-uglify');
   grunt.loadNpmTasks('grunt-contrib-cssmin');
   grunt.loadNpmTasks('grunt-contrib-copy');
   grunt.loadNpmTasks('grunt-contrib-clean');
   grunt.loadNpmTasks('grunt-strip');
   grunt.loadNpmTasks('grunt-bump');
   grunt.loadNpmTasks('grunt-contrib-watch');
   grunt.loadNpmTasks('grunt-contrib-compress');

   grunt.initConfig({

      pkg: grunt.file.readJSON("package.json"),

      jshint: {
         files: ['gruntfile.js', 'js/*.js', '!js/*.min.js'],
         options: {
            globals: {
               console: true,
               module: true,
               jquery: true
            }
         }
      },

      uglify: {
         options: {
            // the banner is inserted at the top of the output
            banner: '/*! <%= pkg.name %>  (c) <%= pkg.author %> <%= grunt.template.today("yyyy") %>\n' +
                    ' * Version <%= pkg.version %> (<%= grunt.template.today("dd-mm-yyyy") %>) */\n',
            mangle: {
               except: ['jQuery']
            },
            report: 'min'
         },
         dev: {
            files: [
                 {
                    expand: true,     // Enable dynamic expansion.
                    cwd: 'src/js',      // Src matches are relative to this path.
                    src: ['*.js', '!*.min.js'], // Actual pattern(s) to match.
                    dest: 'src/js',   // Destination path prefix.
                    ext: '.min.js',   // Dest filepaths will have this extension.
                 },
            ],
            options: { beautify: true, mangle: false }
         },
         release: {
            files: [
                 {
                    expand: true,     // Enable dynamic expansion.
                    cwd: 'src/js',      // Src matches are relative to this path.
                    src: ['*.js', '!*.min.js'], // Actual pattern(s) to match.
                    dest: 'src/js',   // Destination path prefix.
                    ext: '.min.js',   // Dest filepaths will have this extension.
                 },
            ],
         }

      },
    
      cssmin: {
         options: {
            // the banner is inserted at the top of the output
            banner: '/*! <%= pkg.name %>  (c) <%= pkg.author %> <%= grunt.template.today("yyyy") %>\n' +
                    ' * Version <%= pkg.version %> (<%= grunt.template.today("dd-mm-yyyy") %>) */\n',
            report: 'min'
         },
         all: {
            expand: true,
            cwd: 'src/css',
            src: ['*.css', '!*.min.css'],
            dest: 'src/css',
            ext: '.min.css'
         }
         
      },
      
      watch: {
         options: {
            livereload: true
         },
         scripts: {
            files: ['src/js/*.js', '!src/js/*.min.js'],
            tasks: ['jshint', 'uglify:dev'],
         },
         css: {
            files: ['src/css/*.css', '!src/css/*.min.css'],
            tasks: ['cssmin'],
         },
      },
      
      strip : {
         main : {
            src : 'release/js/*.min.js',
            options : { inline : true }
         }
      },

      copy: {
         srcToRelease: {
            files: [{
               expand: true,
               cwd: 'src/',
               src: ['**/*.php',
                     '**/*.txt',
                     'css/*.min.css', 
                     'js/*.min.js', 
                     'assets/**',
                     'buttons/**', 
                     'lang/**', 
                     '**/*.pot',
                     '!**/_notes'],
               dest: 'release/'
            }]
         },
         zipStaging: {
            files: [{
               expand: true,
               cwd: 'release/',
               src: ['**/*.php',
                     '**/*.txt',
                     'css/*.min.css', 
                     'js/*.min.js', 
                     'buttons/**', 
                     'lang/**', 
                     '**/*.pot'],
               dest: '<%= pkg.name %>/'
            }]
         },
         svn: {
            files: [{
               expand: true,
               cwd: 'release/',
               src: ['**/*', '!assets/**'],
               dest: '../svn/crafty-social-buttons/trunk/'
            }]
         },
		test: {
			files: [{
				expand: true,
				cwd: '../svn/crafty-social-buttons/trunk',
				src: ['**/*', '!assets/**'],
				dest: 'D:/Dropbox/Websites/sarah.geek.nz/blog/wp-content/plugins/crafty-social-buttons/'
			}]
		},
         
         svnAssets: {
            files: [{
               expand: true,
               cwd: 'release/assets',
               src: ['**/*'],
               dest: '../svn/crafty-social-buttons/assets/'
            }]
         },
         docs: {
            files: [{
               expand: true,
               cwd: '../',
               src: ['master/zips/<%= pkg.name %>-<%= pkg.version %>.zip'],
               dest: '../gh-pages/downloads/',
               flatten: true,
               filter: 'isFile'
            }]
         },
         doc_buttons: {
            files: [{
               expand: true,
               cwd: 'src/buttons',
               src: ['**/*.png'],
               dest: '../gh-pages/buttons/',
               flatten: false,
               filter: 'isFile'
            }]
         }



      },
    
      clean: {
         release: ["release"],
         zipStaging: ["<%= pkg.name %>", "zips"],
         dreamweaverNotes: ["release/**/_notes"]
      },
      
      compress: {
         release: {
            options: { archive: 'zips/<%= pkg.name %>-<%= pkg.version %>.zip' },
            files: [
               {src: ['<%= pkg.name %>/**/*'], dest: ''} 
            ]
         }
      },
      bump: {
      options: {
         files: ['package.json'],
         updateConfigs: ['pkg'],
         commitMessage: 'Updated version number to %VERSION%',
         createTag: false,
         push: false,
         }
      }

   });

   grunt.registerTask('default', 
      ['jshint', 'uglify:dev', 'cssmin', 'watch']);
   
   grunt.registerTask('test', 
      ['copy:test']);
      
   grunt.registerTask('release', 
      [  'jshint', 
         'uglify:release', 
         'cssmin', 
         'clean:release',
         'copy:srcToRelease',
         'clean:dreamweaverNotes',
         'strip', 
         'clean:zipStaging',
         'copy:zipStaging', 
         'compress', 
         'copy:svn',
         'copy:svnAssets',
         'copy:docs',
         'copy:doc_buttons',
         'clean:release',
         'clean:zipStaging']);   
            
};
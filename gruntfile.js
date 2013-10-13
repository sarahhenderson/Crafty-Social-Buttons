module.exports = function (grunt) {
   // Do grunt-related things in here

   grunt.loadNpmTasks('grunt-contrib-jshint');
   grunt.loadNpmTasks('grunt-contrib-uglify');
   grunt.loadNpmTasks('grunt-contrib-cssmin');
   grunt.loadNpmTasks('grunt-contrib-copy');
   grunt.loadNpmTasks('grunt-contrib-clean');
   grunt.loadNpmTasks('grunt-contrib-compress');

   grunt.initConfig({
      pkg: grunt.file.readJSON("package.json"),

      jshint: {
         files: ['gruntfile.js', 'js/*.js'],
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
            banner: '/*! <%= pkg.name %> <%= grunt.template.today("dd-mm-yyyy") %> */\n',
            mangle: {
               except: ['jQuery']
            }
         },
         build: {
            files: [
                 {
                    expand: true,     // Enable dynamic expansion.
                    cwd: 'src/js',      // Src matches are relative to this path.
                    src: ['*.js', '!*.min.js'], // Actual pattern(s) to match.
                    dest: 'src/js',   // Destination path prefix.
                    ext: '.min.js',   // Dest filepaths will have this extension.
                 },
            ],
         },
         www: {
            files: [
                 {
                    expand: true,     // Enable dynamic expansion.
                    cwd: 'gh-pages/js',      // Src matches are relative to this path.
                    src: ['*.js', '!*.min.js'], // Actual pattern(s) to match.
                    dest: 'gh-pages/js',   // Destination path prefix.
                    ext: '.min.js',   // Dest filepaths will have this extension.
                 },
            ],
         }

      },
    
      cssmin: {
         build: {
            expand: true,
            cwd: 'src/css',
            src: ['*.css', '!*.min.css'],
            dest: 'src/css',
            ext: '.min.css'
         },
         www: {
            expand: true,
            cwd: 'gh-pages/css',
            src: ['*.css', '!*.min.css'],
            dest: 'gh-pages/css',
            ext: '.min.css'
         }
      },

      copy: {
         build: {
            files: [{
               expand: true,
               cwd: 'src/',
               src: ['**/*.min.css', '**/*.min.js', '**/*.php', '**/*.png', '**/*.jpg', '**/*.txt', '**/*.pot'],
               dest: 'svn/'
            }]
         }
      },
    
      clean: {
         build: ["svn/*"]
      }

   });

   grunt.registerTask('default', 
      ['jshint', 'uglify', 'cssmin', 'copy', 'compress']);
      
   grunt.registerTask('build', 
      ['jshint', 'uglify:build', 'cssmin:build', 'clean:build', 'copy:build', 'compress:build' ]);   
      
   grunt.registerTask('www', 
      ['jshint', 'uglify:www', 'cssmin:www']);
      
};
module.exports = function (grunt) {
   // Do grunt-related things in here

   grunt.initConfig({
      pkg: grunt.file.readJSON("package.json"),

      jshint: {
         // define the files to lint
         files: ['gruntfile.js', 'js/*.js'],
         // configure JSHint (documented at http://www.jshint.com/docs/)
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
            banner: '/*! <%= pkg.name %> <%= grunt.template.today("dd-mm-yyyy") %> /*\n',
            mangle: {
               except: ['jQuery']
            }

        },
        dev: {
            files: [
                 {
                    expand: true,     // Enable dynamic expansion.
                    cwd: 'crafty-social-buttons/js',      // Src matches are relative to this path.
                    src: ['*.js', '!*.min.js'], // Actual pattern(s) to match.
                    dest: 'crafty-social-buttons/js',   // Destination path prefix.
                    ext: '.min.js',   // Dest filepaths will have this extension.
                 },
            ],
         }
    },
    
    cssmin: {
        minify: {
            expand: true,
            cwd: 'crafty-social-buttons/css',
            src: ['*.css', '!*.min.css'],
            dest: 'crafty-social-buttons/css',
            ext: '.min.css'
        }
    },

    copy: {
        main: {
            files: [
              {
                 expand: true,
                 cwd: 'crafty-social-buttons/',
                 src: ['**/*.min.css', '**/*.min.js', '**/*.php', '**/*.png', '**/*.jpg', '**/.txt', '**/.pot'],
                 dest: 'svn/'
              }]
         }
      }


   });

   grunt.loadNpmTasks('grunt-contrib-jshint');
   grunt.loadNpmTasks('grunt-contrib-uglify');
   grunt.loadNpmTasks('grunt-contrib-cssmin');
   grunt.loadNpmTasks('grunt-contrib-copy');
   grunt.registerTask('default', ['jshint', 'uglify', 'copy']);
};
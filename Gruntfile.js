module.exports = function(grunt) {
 
    
  grunt.initConfig({
      config: {
        root: 'web',
        requirejs: '<%= config.root %>/js/requirejs',
        modules_js: '<%= config.root %>/js/modules',
        css: '<%= config.root %>/css',
      },
      
      copy: {
          requirejs: {
            files :[{
              expand: true,
              cwd: 'node_modules/requirejs',
              src: ['require.js'],
              dest: '<%= config.requirejs %>'
            }]
          },
          modules_js: {
              files: [{
                  expand: true,
                  cwd: 'node_modules/jquery/dist',
                  src: ['jquery.js'],
                  dest: '<%= config.modules_js %>'
              },{
                  expand: true,
                  cwd: 'node_modules/popper.js/dist',
                  src: ['popper.js'],
                  dest: '<%= config.modules_js %>'
              },{
                  expand: true,
                  cwd: 'node_modules/bootstrap/dist/js',
                  src: ['bootstrap.js'],
                  dest: '<%= config.modules_js %>'
              }
              ]
          },
      },
      sass: {
    	  main: {
	          files: [{
	        	  src: 'node_modules/bootstrap/scss/bootstrap.scss',
	        	  dest: '<%= config.css %>/bootstrap.css'
			  }]
    	  }
      },
      concat: {
    	  main: {
    		  src: ['<%= config.css %>/bootstrap.css', '<%= config.css %>/additional_style.css'],
    	      dest: '<%= config.css %>/main.css',
    	  }
      }

  });
  
  grunt.loadNpmTasks('grunt-contrib-copy');
  grunt.loadNpmTasks('grunt-contrib-sass');
  grunt.loadNpmTasks('grunt-contrib-concat');
  
  grunt.registerTask('dev_js', ['copy:requirejs', 'copy:modules_js']);
  grunt.registerTask('dev_css', ['sass:main', 'concat:main']);
  grunt.registerTask('dev', ['dev_js', 'dev_css']);
  grunt.registerTask('default', ['dev']);
};    
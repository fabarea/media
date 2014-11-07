module.exports = (grunt) ->
	grunt.initConfig
		pkg: grunt.file.readJSON("package.json")
		directory:
			components: "Resources/Public/WebComponents"
			build: "Resources/Public/Build"
			source: "Resources/Public/Source"
		js:
			sources: [
				"<%= directory.source %>/JavaScript/Initialize.js"
				"<%= directory.source %>/JavaScript/Media.js"
				"<%= directory.source %>/JavaScript/Media.EditStorage.js"
			]
		js_tce:
			sources: [
				"<%= directory.source %>/JavaScript/Encoder.js"
			]
		css:
			sources: [
				"<%= directory.source %>/StyleSheets/media.css"
				"<%= directory.source %>/StyleSheets/fineuploader.css"
			]
		css_tce:
			sources: [
				"<%= directory.source %>/StyleSheets/fineuploader.tce.css"
			]

	############################ Assets ############################

	##
	# Assets: clean up environment
	##
		clean:
			temporary:
				src: [".tmp"]


	##
	# Assets: copy some files to the distribution dir
	##
		copy:
			gif:
				files: [
					# includes files within path
					expand: true
					flatten: true
					src: "<%= directory.components %>/fine-uploader/_dist/jquery.fineuploader-*/*.gif"
					dest: "<%= directory.build %>"
					filter: "isFile"
				]

	############################ StyleSheets ############################

	##
	# StyleSheet: minification of CSS
	##
		cssmin:
			options: {}
			css:
				files: [
					src: "<%= directory.build %>/media.css"
					dest: "<%= directory.build %>/media.min.css"
				]
			css_tce:
				files: [
					src: "<%= directory.build %>/media_tce.css"
					dest: "<%= directory.build %>/media_tce.min.css"
				]

	############################ JavaScript ############################

	##
	# JavaScript: check javascript coding guide lines
	##
		jshint:
			files: [
				"<%= directory.source %>/JavaScript/*.js"
			]

			options:
			# options here to override JSHint defaults
				curly: true
				eqeqeq: true
				immed: true
				latedef: true
				newcap: true
				noarg: true
				sub: true
				undef: true
				boss: true
				eqnull: true
				browser: true
				loopfunc: true
				globals:
					jQuery: true
					console: true
					$: true
					Uri: true
					bootbox: true
					TYPO3: true
					Encoder: true
					vidiModuleUrl: true
					define: true
					alert: true
					Vidi: true
					Media: true

	##
	# JavaScript: minimize javascript
	##
		uglify:
			js:
				files: [
					src: "<%= directory.build %>/media.js"
					dest: "<%= directory.build %>/media.min.js"
				]
			js_tce:
				files: [
					src: "<%= directory.build %>/media_tce.js"
					dest: "<%= directory.build %>/media_tce.min.js"
				]

	########## concat css + js ############
		concat:
			options:
				separator: "\n\n"
			js:
				src: [
					"<%= directory.components %>/fine-uploader/_dist/jquery.fineuploader-*/jquery.fineuploader-[0-9].[0-9].[0-9].js"
					"<%= js.sources %>"
				]
				dest: "<%= directory.build %>/media.js"
			js_tce:
				src: [
					"<%= directory.source %>/JavaScript/JQuery/jquery.fineuploader.compatibility.js"
					"<%= directory.components %>/fine-uploader/_dist/jquery.fineuploader-*/jquery.fineuploader-[0-9].[0-9].[0-9].js"
					"<%= js_tce.sources %>"
				]
				dest: "<%= directory.build %>/media_tce.js"
			css:
				src: [
					"<%= css.sources %>"
				]
				dest: "<%= directory.build %>/media.css"
			css_tce:
				src: [
					"<%= css_tce.sources %>"
				]
				dest: "<%= directory.build %>/media_tce.css"

	########## Watcher ############
		watch:
			css:
				files: [
					"<%= directory.source %>/StyleSheets/**/*.scss"
				]
				tasks: ["build-css"]
			js:
				files: ["<%= jshint.files %>"]
				tasks: ["build-js"]


	########## Help ############
	grunt.registerTask "help", "Just display some helping output.", () ->
		grunt.log.writeln "Usage:"
		grunt.log.writeln ""
		grunt.log.writeln "- grunt watch        : watch your file and compile as you edit"
		grunt.log.writeln "- grunt build        : build your assets ready to be deployed"
		grunt.log.writeln "- grunt build-css    : only build your css files"
		grunt.log.writeln "- grunt build-js     : only build your js files"
		grunt.log.writeln "- grunt build-icons  : only build icons"
		grunt.log.writeln "- grunt clean        : clean behind you the temporary files"
		grunt.log.writeln ""
		grunt.log.writeln "Use grunt --help for a more verbose description of this grunt."
		return

	# Load Node module
	grunt.loadNpmTasks "grunt-contrib-uglify"
	grunt.loadNpmTasks "grunt-contrib-jshint"
	grunt.loadNpmTasks "grunt-contrib-watch"
	grunt.loadNpmTasks "grunt-contrib-concat"
	grunt.loadNpmTasks "grunt-contrib-sass";
	grunt.loadNpmTasks "grunt-contrib-cssmin"
	grunt.loadNpmTasks "grunt-contrib-copy"
	grunt.loadNpmTasks "grunt-contrib-clean"
	grunt.loadNpmTasks "grunt-string-replace"
	grunt.loadNpmTasks "grunt-imagine"
	grunt.loadNpmTasks "grunt-pngmin"

	# Alias tasks
	grunt.task.renameTask("string-replace", "replace")

	# Tasks
	grunt.registerTask "build", ["build-js", "build-css", "build-icons"]
	grunt.registerTask "build-js", ["jshint", "concat:js", "concat:js_tce", "uglify"]
	grunt.registerTask "build-css", ["concat:css", "concat:css_tce", "cssmin"]
	grunt.registerTask "build-icons", ["copy"]
	grunt.registerTask "default", ["help"]
	return
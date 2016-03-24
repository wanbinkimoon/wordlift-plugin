# Set the well-known $ reference to jQuery.
$ = jQuery

# Create the main AngularJS module, and set it dependent on controllers and directives.
angular.module('wordlift.editpost.widget', [
  'wordlift.ui.carousel'
  'wordlift.utils.directives'
  'wordlift.editpost.widget.providers.ConfigurationProvider',
  'wordlift.editpost.widget.controllers.EditPostWidgetController',
  'wordlift.editpost.widget.directives.wlClassificationBox',
  'wordlift.editpost.widget.directives.wlEntityForm',
  'wordlift.editpost.widget.directives.wlEntityTile',
  'wordlift.editpost.widget.directives.wlEntityInputBox',
  'wordlift.editpost.widget.services.AnalysisService',
  'wordlift.editpost.widget.services.EditorService',
  'wordlift.editpost.widget.services.RelatedPostDataRetrieverService'

])

.config((configurationProvider)->
  configurationProvider.setConfiguration window.wordlift
)

$(
  container = $("""
  	<div 
      id="wordlift-edit-post-wrapper" 
      ng-controller="EditPostWidgetController"
      ng-include="configuration.defaultAngularTemplatesPath + 'wordlift-editpost-widget.html'">
    </div>  
  """)
  .appendTo('#wordlift-edit-post-outer-wrapper')

  injector = angular.bootstrap $('#wordlift-edit-post-wrapper'), ['wordlift.editpost.widget']

# Add WordLift as a plugin of the TinyMCE editor.
  tinymce.PluginManager.add 'wordlift', (editor, url) ->

# This plugin has to be loaded only with the main WP "content" editor
    return unless editor.id is "content"

    # Register event depending on tinymce major version
    fireEvent = (editor, eventName, callback)->
      switch tinymce.majorVersion
        when '4' then editor.on eventName, callback
        when '3' then editor["on#{eventName}"].add callback

    # Hack wp.mce.views to prevent shorcodes rendering
    # starts before the analysis is properly embedded
    injector.invoke(['EditorService', '$rootScope', '$log', (EditorService, $rootScope, $log) ->

# wp.mce.views uses toViews() method from WP 3.8 to 4.1
# and setMarkers() method from WP 4.2 to 4.3 to replace
# available shortcodes with coresponding views markup
      for method in ['setMarkers', 'toViews']
        if wp.mce.views[method]?

          originalMethod = wp.mce.views[method]
          $log.warn "Override wp.mce.views method #{method}() to prevent shortcodes rendering"
          wp.mce.views[method] = (content)->
            return content

          $rootScope.$on "analysisEmbedded", (event) ->
            $log.info "Going to restore wp.mce.views method #{method}()"
            wp.mce.views[method] = originalMethod

          $rootScope.$on "analysisFailed", (event) ->
            $log.info "Going to restore wp.mce.views method #{method}()"
            wp.mce.views[method] = originalMethod

          break
    ])

    # Perform analysis once tinymce is loaded
    fireEvent(editor, "LoadContent", (e) ->
      injector.invoke(['AnalysisService', 'EditorService', '$rootScope', '$log'
        (AnalysisService, EditorService, $rootScope, $log) ->
          # execute the following commands in the angular js context.
          $rootScope.$apply(->
            # Get the html content of the editor.
            html = editor.getContent format: 'raw'
            # Get the text content from the Html.
            text = Traslator.create(html).getText()
            if text.match /[a-zA-Z0-9]+/
              # Disable tinymce editing
              EditorService.updateContentEditableStatus false
              AnalysisService.perform text
            else
              $log.warn "Blank content: nothing to do!"
          )
      ])
    )

    # Fires when the user changes node location using the mouse or keyboard in the TinyMCE editor.
    fireEvent(editor, "NodeChange", (e) ->
      injector.invoke(['AnalysisService', 'EditorService', '$rootScope', '$log',
        (AnalysisService, EditorService, $rootScope, $log) ->
          if AnalysisService._currentAnalysis
            $rootScope.$apply(->
              $rootScope.selectionStatus = EditorService.hasSelection()
            )
          true

      ])
    )

    # this event is raised when a textannotation is selected in the TinyMCE editor.
    fireEvent(editor, "Click", (e) ->
      injector.invoke(['AnalysisService', 'EditorService', '$rootScope', '$log',
        (AnalysisService, EditorService, $rootScope, $log) ->
          if AnalysisService._currentAnalysis
            $rootScope.$apply(->
              EditorService.selectAnnotation e.target.id
            )
          true

      ])
    )
)

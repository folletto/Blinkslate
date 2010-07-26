(function() {
	tinymce.create('com.intenseminimalism.ProLayout', {
		init: function(ed, url) {
		  /****************************************************************************************************
  		 * Initializes the plugin.
  		 *
  		 * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
  		 * @param {string} url Absolute URL to where the plugin is located.
  		 */
  		
			// ****** Button: Side
 		  ed.addButton('plside', {
 				title: 'Side box',
 				image: url + '/img/side.png',
 				onclick: function prolayoutSide() {
 				  if (ed.selection.getContent()) {
 				    ed.selection.setContent('<div class="side box">' + ed.selection.getContent() + '</div>');
 				  }
 				}
 			});

 		  // ****** Button: Hilight
 		  ed.addButton('plhilight', {
 				title: 'Hilight box',
 				image: url + '/img/hilight.png',
 				onclick: function prolayoutHilight() {
 				  if (ed.selection.getContent()) {
 				    ed.selection.setContent('<div class="hilight box">' + ed.selection.getContent() + '</div>');
			    }
 				}
 			});
		}
	});

	/****************************************************************************************************
	 * Register the plugin
	 */
	tinymce.PluginManager.add('prolayout', com.intenseminimalism.ProLayout);
})();

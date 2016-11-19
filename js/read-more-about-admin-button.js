( function() {
    tinymce.create( 'tinymce.plugins.read_more_about', {
 		init : function( ed, url ) {
 			url = url.slice( 0, -3 );
           	ed.addButton( 'read_more_about', {
            	title : 'Related Links',
            	cmd : 'read_more',
             	image : url + '/images/read-more.png'
          	} );
            ed.addCommand( 'read_more', function() {
            	var returnText = '[read-more title="" float=""]';
            	ed.execCommand( 'mceInsertContent', 0, returnText );
            } );
        },
        createControl : function( n, cm ) {
            return null;
        },
        getInfo : function() {
            return {
                longname : 'Read More About Plugin',
                author : 'Jacob Martella',
                authorurl : 'http://jacobmartella.com',
                infourl : 'http://www.jacobmartella.com/read-more-about/',
                version : "1.0"
            };
        }
    } );
    tinymce.PluginManager.add( 'read_more_about', tinymce.plugins.read_more_about );
} )();
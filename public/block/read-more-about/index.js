import './editor.scss';

import icon from './icon';
import SelectPost from './select';

const {
    __,
} = wp.i18n;
const {
    registerBlockType,
} = wp.blocks;
const {
    Button,
    IconButton,
    PanelBody,
    TextControl,
    ColorPalette,
    SelectControl
} = wp.components;
const {
    PanelColor,
    InspectorControls,
    URLInput,
    PanelColorSettings,
    AlignmentToolbar,
    BlockControls,
} = wp.editor;
const {
    Fragment,
} = wp.element;

/**
 * Register: Repeater Gutenberg Block.
 *
 * Registers a new block provided a unique name and an object defining its
 * behavior. Once registered, the block is made editor as an option to any
 * editor interface where blocks are implemented.
 *
 * @link https://wordpress.org/gutenberg/handbook/block-api/
 * @param  {string}   name     Block name.
 * @param  {Object}   settings Block settings.
 * @return {?WPBlock}          The block, if it has been successfully
 *                             registered; otherwise `undefined`.
 */
registerBlockType( 'read-more-about/read-more-about', {
    title: __( 'Read More About' ),
    icon: icon,
    category: 'common',
    attributes: {
        read_more_links: {
            type: 'array',
            default: [],
        },
        read_more_title: {
            type: 'string',
            default: 'Read More',
        },
        read_more_color_scheme: {
            type: 'string',
            default: 'light',
        },
        posts: {
            type: 'array',
            default: [],
        },
        getPost: {
            type: 'boolean',
            default: false,
        },
    },
    supports: {
        align: true,
    },
    keywords: [
        __( 'additional links' ),
        __( 'references' ),
        __( 'citations' ),
    ],
    edit: ( props ) => {

        if ( false === props.attributes.getPost ) {
            getPosts().then(function (options) {
                console.log(options);
                props.setAttributes({
                    getPost: true,
                    posts: options
                });
            });
        }

        function getPosts(){
            console.log('get team');
            var url = '/wp-json/wp/v2/posts?per_page=100';
            console.log(url);
            return fetch( url, {
                credentials: 'same-origin',
                method: 'get',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': read_more_about_globals.nonce
                }})
                .then( handleFetchErrors )
                .then( ( response ) => response.json() )
                .then( ( json ) => {
                    console.log(json);
                    var options = json.map( function(opt, i){
                        return {value: opt.id, label: opt.title.rendered}
                    });
                    return options;
                })
                .catch(function(e) {
                    console.log(e);
                });

        }

        function onSelectPost( option, index ){
            if( option === null ){
                console.log(null);
                getPosts().then( function( options ) {
                    console.log(options);
                    const read_more_links = [ ...props.attributes.read_more_links ];
                    read_more_links[ index ].in_link_post_id = option.value;
                    read_more_links[ index ].in_link_title = option.label;
                    props.setAttributes({
                        read_more_links,
                        getPost: false,
                        posts: options
                    });
                });
            } else {
                console.log('There is an option');
                getPosts().then( function( options ) {
                    console.log(options);
                    var url = '/wp-json/wp/v2/posts?per_page=100';
                    console.log(url);
                    return fetch( url, {
                        credentials: 'same-origin',
                        method: 'get',
                        headers: {
                            Accept: 'application/json',
                            'Content-Type': 'application/json',
                            'X-WP-Nonce': read_more_about_globals.nonce
                        }})
                        .then( handleFetchErrors )
                        .then( ( response ) => response.json() )
                        .then( ( json ) => {
                            const read_more_links = [ ...props.attributes.read_more_links ];
                            read_more_links[ index ].in_link_post_id = option.value;
                            read_more_links[ index ].in_link_title = option.label;
                            read_more_links[ index ].in_link = json[0].link;
                            read_more_links[ index ].in_link_photo = json[0].read_more_featured_image_src.full[0];
                            read_more_links[ index ].in_link_photo_alt = json[0].read_more_featured_image_src.alt;
                            props.setAttributes({
                                read_more_links,
                                getPosts: true,
                                posts: options
                            });
                        })
                        .catch(function(e) {
                            console.log(e);
                        });
                });
            }
        }

        function handleFetchErrors( response ) {
            if (!response.ok) {
                console.log('fetch error, status: ' + response.statusText);
            }
            return response;
        }

        const handleAddLocation = () => {
            const read_more_links = [ ...props.attributes.read_more_links ];
            read_more_links.push( {
                text: '',
                target: '',
            } );
            props.setAttributes( { read_more_links } );
        };

        const handleRemoveLocation = ( index ) => {
            const read_more_links = [ ...props.attributes.read_more_links ];
            read_more_links.splice( index, 1 );
            props.setAttributes( { read_more_links } );
        };

        const handleLinkTypeChange = ( text, index ) => {
            const read_more_links = [ ...props.attributes.read_more_links ];
            read_more_links[ index ].link_type = text;
            props.setAttributes( { read_more_links } );
        };

        const handleExLinkChange = ( text, index ) => {
            const read_more_links = [ ...props.attributes.read_more_links ];
            read_more_links[ index ].ex_link = text;
            props.setAttributes( { read_more_links } );
        };

        const handleExLinkTitleChange = ( target, index ) => {
            const read_more_links = [ ...props.attributes.read_more_links ];
            read_more_links[ index ].ex_link_title = target;
            props.setAttributes( { read_more_links } );
        };

        const handleColorSchemeChange = ( color ) => {
            props.setAttributes( { read_more_color_scheme: color } );
        };

        const handleTitleChange = ( title ) => {
            props.setAttributes( { read_more_title: title } );
        };

        let linkFields,
            linkDisplay;

        if ( props.attributes.read_more_links.length ) {
            linkFields = props.attributes.read_more_links.map( ( location, index ) => {
                var linkFields = '';
                if ( 'internal' === props.attributes.read_more_links[ index ].link_type ) {
                    var selectPostValue = { value: props.attributes.read_more_links[ index ].in_link_post_id, label: props.attributes.read_more_links[ index ].in_link_title };
                    linkFields = [
                        <SelectPost
                            onChange={ ( object ) => onSelectPost( object, index ) }
                            restUrl="/wp-json/wp/v2/posts?per_page=100&title="
                            initial_value={ selectPostValue }
                            nonce={ read_more_about_globals.nonce }
                        />
                    ];
                } else {
                    linkFields = [
                        <TextControl
                        className="grf__location-address"
                        placeholder=""
                        label="Link URL"
                        value={ props.attributes.read_more_links[ index ].ex_link }
                        onChange={ ( text ) => handleExLinkChange( text, index ) }
                        />,
                        <TextControl
                            className="grf__location-address"
                            placeholder=""
                            label="Link Text"
                            value={ props.attributes.read_more_links[ index ].ex_link_title }
                            onChange={ ( text ) => handleExLinkTitleChange( text, index ) }
                        />];
                }
                return <Fragment key={ index }>
                    <PanelBody>
                        <SelectControl
                            label={ __( 'Link Type', 'read-more-about' ) }
                            value={ props.attributes.read_more_links[ index ].link_type }
                            options={ [
                                { value: 'external', label: __( 'External', 'read-more-about' ) },
                                { value: 'internal', label: __( 'Internal', 'read-more-about' ) }
                            ] }
                            onChange={ ( text ) => handleLinkTypeChange( text, index ) }
                        />
                    </PanelBody>
                    { linkFields }
                    <IconButton
                        className="grf__remove-location-address"
                        icon="no-alt"
                        label="Delete location"
                        onClick={ () => handleRemoveLocation( index ) }
                    />
                </Fragment>;
            } );

            linkDisplay = props.attributes.read_more_links.map( ( highlight, index ) => {
                var links = '';
                if ( 'internal' === props.attributes.read_more_links[ index ].link_type ) {
                    var linkPhoto = '';

                    if ( '' !== props.attributes.read_more_links[ index ].in_link_photo && null !== props.attributes.read_more_links[ index ].in_link_photo && undefined !== props.attributes.read_more_links[ index ].in_link_photo ) {
                        linkPhoto = [ <div className={ 'photo' }><a href={ props.attributes.read_more_links[ index ].in_link } target={ '_blank' }><img src={ props.attributes.read_more_links[ index ].in_link_photo } alt={ props.attributes.read_more_links[ index ].in_link_photo_alt } /></a></div> ];
                    }

                    links = [
                        linkPhoto,
                        <p className={ 'story-title' }><a href={ props.attributes.read_more_links[ index ].in_link } target={ '_blank' }> { props.attributes.read_more_links[ index ].in_link_title } </a></p>
                    ];
                } else {
                    links = (
                        <p className={ 'story-title' }><a href={ props.attributes.read_more_links[ index ].ex_link } target={ '_blank' }> { props.attributes.read_more_links[ index ].ex_link_title } </a></p>
                    );
                }

                return <div className={ 'story' } key={ index }> { links } </div>;
            } );
        }

        return [
            <InspectorControls key="1">
                <PanelBody title={ __( 'Read More Title' ) }>
                    <TextControl
                        placeholder=""
                        value={ props.attributes.read_more_title }
                        onChange={ ( title ) => handleTitleChange( title ) }
                    />
                </PanelBody>
                <PanelBody title={ __( 'Highlights' ) }>
                    { linkFields }
                    <Button
                        isDefault
                        onClick={ handleAddLocation.bind( this ) }
                    >
                        { __( 'Add Link' ) }
                    </Button>
                </PanelBody>
                <PanelBody>
                    <SelectControl
                        label={ __( 'Color Scheme', 'read-more-about' ) }
                        value={ props.attributes.read_more_color_scheme }
                        options={ [
                            { value: 'light', label: __( 'Light', 'read-more-about' ) },
                            { value: 'dark', label: __( 'Dark', 'read-more-about' ) }
                        ] }
                        onChange={ ( color ) => handleColorSchemeChange( color ) }
                    />
                </PanelBody>
            </InspectorControls>,
            <div key="2" className={ props.className + ' ' + props.attributes.read_more_color_scheme }>
                <h2>{ props.attributes.read_more_title }</h2>
                { linkDisplay }
            </div>,
        ];
    },
    save: ( props ) => {

        const linkDisplay = props.attributes.read_more_links.map( ( highlight, index ) => {
            var links = '';
            if ( 'internal' === props.attributes.read_more_links[ index ].link_type ) {
                var linkPhoto = '';

                if ( '' !== props.attributes.read_more_links[ index ].in_link_photo && null !== props.attributes.read_more_links[ index ].in_link_photo && undefined !== props.attributes.read_more_links[ index ].in_link_photo ) {
                    linkPhoto = [ <div className={ 'photo' }><a href={ props.attributes.read_more_links[ index ].in_link } target={ '_blank' } rel="noopener noreferrer"><img src={ props.attributes.read_more_links[ index ].in_link_photo } alt={ props.attributes.read_more_links[ index ].in_link_photo_alt } /></a></div> ];
                }

                links = [
                    linkPhoto,
                    <p className={ 'story-title' }><a href={ props.attributes.read_more_links[ index ].in_link } target={ '_blank' } rel="noopener noreferrer"> { props.attributes.read_more_links[ index ].in_link_title } </a></p>
                ];
            } else {
                links = (
                    <p className={ 'story-title' }><a href={ props.attributes.read_more_links[ index ].ex_link } target={ '_blank' } rel="noopener noreferrer"> { props.attributes.read_more_links[ index ].ex_link_title } </a></p>
                );
            }

            return <div className={ 'story' } key={ index }> { links } </div>;
        } );

        return (
            <div className={ props.className + ' ' + props.attributes.read_more_color_scheme }>
                <h2>{ props.attributes.read_more_title }</h2>
                { linkDisplay }
            </div>
        );
    },
} );


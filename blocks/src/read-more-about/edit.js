import SelectPost from './select';
import axios from 'axios';

import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	InspectorControls,
	InnerBlocks,
} from '@wordpress/block-editor';
import {
	Button,
    IconButton,
    PanelBody,
    TextControl,
    ColorPalette,
    SelectControl,
    URLInput,
    PanelColorSettings,
    AlignmentToolbar,
    BlockControls,
	ComboboxControl,
} from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import {
	PanelColor,
	Fragment
} from '@wordpress/editor';
import {
	useState,
} from '@wordpress/element';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */
export default function Edit( props ) {
	if ( false === props.attributes.getPost ) {
		getPosts().then(function (options) {
			props.setAttributes({
				getPost: true,
				posts: options
			});
		});
	}

	function getPosts() {
		let options = [];
		const posts = wp.data.select( 'core' ).getEntityRecords( 'postType', 'post', { per_page: -1 } );
		if ( null === posts ) {
			return options;
		}
		posts.forEach( ( post ) => {
			options.push( { value: post.id, label: post.title.rendered } );
		} );
		return options;
	}

	const [ filteredOptions, setFilteredOptions ] = useState( getPosts() );

	function onSelectPost( option, index ){
		axios.get(`/wp-json/wp/v2/posts?include[]=${option}`)
		.then(response => {
			let post = response.data;
			const read_more_links = [ ...props.attributes.read_more_links ];
			read_more_links[ index ].in_link_post_id = option;
			read_more_links[ index ].in_link_title = post[0].title.rendered;
			read_more_links[ index ].in_link = post[0].link;
			read_more_links[ index ].in_link_photo = post[0].read_more_featured_image_src.full[0];
			read_more_links[ index ].in_link_photo_alt = post[0].read_more_featured_image_src.alt;
			props.setAttributes({
				read_more_links
			});
  		 })
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
			link_type: 'external'
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

	let linkFields = [];
	let linkDisplay = [];

	if ( props.attributes.read_more_links.length ) {
		linkFields = props.attributes.read_more_links.map( ( location, index ) => {
			var singleLinkFields = [];
			if ( 'internal' === props.attributes.read_more_links[ index ].link_type ) {
				let selectPostValue;
				if ( undefined === props.attributes.read_more_links[ index ].in_link_post_id ) {
					selectPostValue = '';
				} else {
					selectPostValue =props.attributes.read_more_links[ index ].in_link_post_id;
				}
				singleLinkFields = (
					<ComboboxControl
						label={ __( 'Select Post', '' ) }
						value={ selectPostValue }
						onChange={ ( object ) => onSelectPost( object, index ) }
						options={ getPosts() }
						onFilterValueChange={ ( inputValue ) =>
							setFilteredOptions(
								getPosts().filter( ( option ) =>
									option.label
										.toLowerCase()
										.startsWith( inputValue.toLowerCase() )
								)
							)
						}
					/>
				);
			} else {
				let linkURL;
				let linkText;
				if ( undefined === props.attributes.read_more_links[ index ].ex_link ) {
					linkURL = '';
				} else {
					linkURL = props.attributes.read_more_links[ index ].ex_link;
				}

				if ( undefined === props.attributes.read_more_links[ index ].ex_link_title ) {
					linkText = '';
				} else {
					linkText = props.attributes.read_more_links[ index ].ex_link_title;
				}

				singleLinkFields = [
					<TextControl
					className="grf__location-address"
					placeholder=""
					label="Link URL"
					value={ linkURL }
					onChange={ ( text ) => handleExLinkChange( text, index ) }
					/>,
					<TextControl
						className="grf__location-address"
						placeholder=""
						label="Link Text"
						value={ linkText }
						onChange={ ( text ) => handleExLinkTitleChange( text, index ) }
					/>
				];
			}

			return [
				<div key={ index }>
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
					{singleLinkFields}
					<IconButton
						className="grf__remove-location-address"
						icon="no-alt"
						label="Delete location"
						onClick={ () => handleRemoveLocation( index ) }
					/>
				</div>
			];
		} );

		linkDisplay = props.attributes.read_more_links.map( ( highlight, index ) => {
			var links = '';
			if ( 'internal' === props.attributes.read_more_links[ index ].link_type ) {
				var linkPhoto = '';

				if ( undefined !== props.attributes.read_more_links[ index ].in_link ) {
					if ( '' !== props.attributes.read_more_links[ index ].in_link_photo && null !== props.attributes.read_more_links[ index ].in_link_photo && undefined !== props.attributes.read_more_links[ index ].in_link_photo ) {
						linkPhoto = [ <div className={ 'photo' }><a href={ props.attributes.read_more_links[ index ].in_link } target={ '_blank' }><img src={ props.attributes.read_more_links[ index ].in_link_photo } alt={ props.attributes.read_more_links[ index ].in_link_photo_alt } /></a></div> ];
					}


					links = [
						linkPhoto,
						<p className={ 'story-title' }><a href={ props.attributes.read_more_links[ index ].in_link } target={ '_blank' }> { props.attributes.read_more_links[ index ].in_link_title } </a></p>
					];
				}
			} else {
				if ( undefined !== props.attributes.read_more_links[ index ].ex_link ) {
					links = (
						<p className={ 'story-title' }><a href={ props.attributes.read_more_links[ index ].ex_link } target={ '_blank' }> { props.attributes.read_more_links[ index ].ex_link_title } </a></p>
					);
				}
			}

			return <div className={ 'story' } key={ index }> { links } </div>;
		} );
	}

	const blockProps = useBlockProps( {
		className: 'wp-block-read-more-about-read-more-about ' + props.attributes.read_more_color_scheme,
		'data-id': 'special-h1-id'
	} );

	return [
		<InspectorControls>
			<PanelBody title={ __( 'Read More Title' ) }>
				<TextControl
					placeholder=""
					value={ props.attributes.read_more_title }
					onChange={ ( title ) => handleTitleChange( title ) }
				/>
			</PanelBody>
			<PanelBody title={ __( 'Links' ) }>
				{ linkFields }
				<Button
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
		<div { ...blockProps }>
			<h2 className={ 'title' }>{ props.attributes.read_more_title }</h2>
			{ linkDisplay }
		</div>,
	];
}

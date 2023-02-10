/**
 * External dependencies
 */
import classnames from 'classnames';

/**
 * WordPress dependencies
 */
import { useBlockProps } from '@wordpress/block-editor';

export default function save( props ) {
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

	const blockProps = useBlockProps.save( {
		className: 'wp-block-read-more-about-read-more-about ' + props.attributes.read_more_color_scheme,
	} );

	return (
		<div { ...blockProps }>
			<h2 className={ 'title' }>{ props.attributes.read_more_title }</h2>
			{ linkDisplay }
		</div>
	);
}

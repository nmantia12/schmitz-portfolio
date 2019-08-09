console.log('movie react running')

const { RichText, PlainText } = wp.editor
// const { PlainText } = wp.editor
const { registerBlockType } = wp.blocks
// const { Button } = wp.components

// Import our CSS files
// import './editor.scss';

registerBlockType('movie/main', {
	title: 'Movie',
	icon: 'heart',
	category: 'common',
	attributes: { 
		title: {
			source: 'text',
			selector: '.movie__title'
		},
		body: {
			type: 'array',
			source: 'children',
			selector: '.movie__body'
		}
	},
	edit({ attributes, className, setAttributes }) {

		return (
			<div className="container movie-container">
				<PlainText
					onChange={content => setAttributes({ title: content })}
					value={attributes.title}
					placeholder="Your card title"
					className="heading"
				/>
				<RichText
					onChange={content => setAttributes({ body: content })}
					value={attributes.body}
					multiline="p"
					placeholder="Your card text"
					formattingControls={['bold', 'italic', 'underline']}
					isSelected={attributes.isSelected}
				/>
			</div>
		)
	},

	save({ attributes }) {
		// const cardImage = (src, alt) => {
		// 	if (!src) return null

		// 	if (alt) {
		// 		return <img className="card__image" src={src} alt={alt} />
		// 	}

		// 	// No alt set, so let's hide it from screen readers
		// 	return <img className="card__image" src={src} alt="" aria-hidden="true" />
		// }

		return (
			<div className="card">
				{/* {cardImage(attributes.imageUrl, attributes.imageAlt)} */}
				<div className="card__content">
					<h3 className="movie__title">{attributes.title}</h3>
					<div className="movie__body">{attributes.body}</div>
				</div>
			</div>
		)
	}
})

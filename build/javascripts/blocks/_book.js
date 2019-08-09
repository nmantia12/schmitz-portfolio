console.log('book react running')

const { PlainText } = wp.editor
const { registerBlockType } = wp.blocks

registerBlockType('book/main', {
    title: 'Book',
    icon: 'heart',
    category: 'common',
    attributes: {
        title: {
            source: 'text',
            selector: '.card__title'
        }
    },
    edit({ attributes, className, setAttributes }) {

        return (
            <div className="container book-container">
                <PlainText
                    onChange={content => setAttributes({ title: content })}
                    value={attributes.title}
                    placeholder="Your card title"
                    className="heading"
                />
            </div>
        )
    },

    save({ attributes }) {
        return (
            <div className="card">
                <div className="card__content">
                    <h3 className="card__title">{attributes.title}</h3>
                </div>
            </div>
        )
    }
})

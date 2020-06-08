wp.domReady(() => {
	wp.blocks.unregisterBlockStyle("core/button", "default");
	wp.blocks.unregisterBlockStyle("core/button", "outline");
	wp.blocks.unregisterBlockStyle("core/button", "squared");

	var allowedBlocks = [
    "core/paragraph",
    "core/image",
    "core/html",
    "core/freeform",
    "core/heading",
    "core/list",
    "core/separator",
    "core/columns",
		"core/column",
		"core/list",
		"core/shortcode",
		'core/block',
		'core/group',
		"gravityforms/block"
	];

	wp.blocks.getBlockTypes().forEach(function (blockType) {
		if (allowedBlocks.indexOf(blockType.name) === -1 && blockType.category != 'paradowski') {
			wp.blocks.unregisterBlockType(blockType.name);
		}
	});
});


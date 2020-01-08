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
    "core/button",
    "core/separator",
    "core/columns",
    "core/column",
    "acf/large-quote",
    "acf/parallax-image",
    "acf/fact-circle",
    "acf/split-scroll",
    "acf/content-image-quote",
    "acf/infographic",
    "acf/video-modal",
    "acf/full-bg-img-content",
    "acf/image-slider",
    "acf/split-content"
  ];

	wp.blocks.getBlockTypes().forEach(function(blockType) {
		if (allowedBlocks.indexOf(blockType.name) === -1) {
			wp.blocks.unregisterBlockType(blockType.name);
		}
	});
});


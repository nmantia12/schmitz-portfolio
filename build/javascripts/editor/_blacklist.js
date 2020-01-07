wp.domReady(function() {
	const blocks_blacklist = [
    "core/verse",
    "core/video",
    "core/table",
    "core/quote",
    "core/pullquote",
    "core/preformatted",
    "core/nextpage",
    "core/more",
    "core/latestPosts",
    "core/latestComments",
    "core/html",
    "core/gallery",
    "core/freeform",
    "core/coverImage",
    "core/code",
    "core/categories",
    "core/audio",
    "core/archive"
  ];

	for (let i = 0; i < blocks_blacklist.length; i++) {
		const blockName = blocks_blacklist[i];
		wp.blocks.unregisterBlockType(blockName);
  }
});

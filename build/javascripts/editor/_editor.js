wp.domReady(() => {
  wp.blocks.unregisterBlockStyle("core/button", "default");
  wp.blocks.unregisterBlockStyle("core/button", "outline");
  wp.blocks.unregisterBlockStyle("core/button", "squared");
});

var allowedBlocks = [
  "core/paragraph",
  "core/image",
  "core/html",
  "core/freeform"
];

wp.blocks.getBlockTypes().forEach(function(blockType) {
  if (allowedBlocks.indexOf(blockType.name) === -1) {
    wp.blocks.unregisterBlockType(blockType.name);
  }
});

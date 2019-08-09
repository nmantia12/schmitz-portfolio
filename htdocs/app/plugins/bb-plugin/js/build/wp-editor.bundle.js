/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 34);
/******/ })
/************************************************************************/
/******/ ({

/***/ 34:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


__webpack_require__(35);

__webpack_require__(36);

/***/ }),

/***/ 35:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _toConsumableArray(arr) { if (Array.isArray(arr)) { for (var i = 0, arr2 = Array(arr.length); i < arr.length; i++) { arr2[i] = arr[i]; } return arr2; } else { return Array.from(arr); } }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

var _FLBuilderConfig = FLBuilderConfig,
    strings = _FLBuilderConfig.strings;
var _wp$blocks = wp.blocks,
    createBlock = _wp$blocks.createBlock,
    serialize = _wp$blocks.serialize;
var Button = wp.components.Button;
var compose = wp.compose.compose;
var _wp$data = wp.data,
    withDispatch = _wp$data.withDispatch,
    withSelect = _wp$data.withSelect;
var Component = wp.element.Component;
var addFilter = wp.hooks.addFilter;

/**
 * Convert to builder button component.
 */

var ConvertToBuilderButton = function (_Component) {
	_inherits(ConvertToBuilderButton, _Component);

	function ConvertToBuilderButton() {
		_classCallCheck(this, ConvertToBuilderButton);

		return _possibleConstructorReturn(this, (ConvertToBuilderButton.__proto__ || Object.getPrototypeOf(ConvertToBuilderButton)).apply(this, arguments));
	}

	_createClass(ConvertToBuilderButton, [{
		key: 'render',
		value: function render() {
			if (this.hasBuilderBlock()) {
				return null;
			}

			return React.createElement(
				'span',
				null,
				React.createElement(
					Button,
					{
						className: 'components-menu-item__button',
						onClick: this.convertToBuilder.bind(this)
					},
					strings.convert
				)
			);
		}
	}, {
		key: 'hasBuilderBlock',
		value: function hasBuilderBlock() {
			var getBlocks = this.props.getBlocks;

			var blocks = getBlocks();
			var builder = blocks.filter(function (block) {
				return 'fl-builder/layout' === block.name;
			});

			return !!builder.length;
		}
	}, {
		key: 'convertToBuilder',
		value: function convertToBuilder() {
			var _props = this.props,
			    getBlocks = _props.getBlocks,
			    insertBlock = _props.insertBlock,
			    removeBlocks = _props.removeBlocks;

			var blocks = getBlocks();
			var clientIds = blocks.map(function (block) {
				return block.clientId;
			});
			var content = serialize(blocks).replace(/<!--(.*?)-->/g, '');
			var block = createBlock('fl-builder/layout', { content: content });

			removeBlocks(clientIds);
			insertBlock(block, 0);
		}
	}]);

	return ConvertToBuilderButton;
}(Component);

/**
 * Connect the button to editor data.
 */


var ConvertToBuilderButtonConnected = compose(withDispatch(function (dispatch, ownProps) {
	var editor = dispatch('core/editor');
	return {
		insertBlock: editor.insertBlock,
		removeBlocks: editor.removeBlocks
	};
}), withSelect(function (select) {
	var editor = select('core/editor');
	return {
		getBlocks: editor.getBlocks
	};
}))(ConvertToBuilderButton);

/**
 * Register the button.
 */
addFilter('editPost.MoreMenu.tools', 'fl-builder/convert-to-builder/button', function (children) {
	return [].concat(_toConsumableArray(children), [React.createElement(ConvertToBuilderButtonConnected, { key: 'convert-to-builder' })]);
});

/***/ }),

/***/ 36:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

__webpack_require__(37);

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

var _FLBuilderConfig = FLBuilderConfig,
    builder = _FLBuilderConfig.builder,
    strings = _FLBuilderConfig.strings,
    urls = _FLBuilderConfig.urls;
var _wp$blocks = wp.blocks,
    registerBlockType = _wp$blocks.registerBlockType,
    rawHandler = _wp$blocks.rawHandler,
    serialize = _wp$blocks.serialize;
var _wp$components = wp.components,
    Button = _wp$components.Button,
    Placeholder = _wp$components.Placeholder,
    Spinner = _wp$components.Spinner;
var compose = wp.compose.compose;
var _wp$data = wp.data,
    subscribe = _wp$data.subscribe,
    withDispatch = _wp$data.withDispatch,
    withSelect = _wp$data.withSelect;
var _wp$element = wp.element,
    Component = _wp$element.Component,
    RawHTML = _wp$element.RawHTML;

/**
 * Edit Component
 */

var LayoutBlockEdit = function (_Component) {
	_inherits(LayoutBlockEdit, _Component);

	function LayoutBlockEdit() {
		_classCallCheck(this, LayoutBlockEdit);

		var _this = _possibleConstructorReturn(this, (LayoutBlockEdit.__proto__ || Object.getPrototypeOf(LayoutBlockEdit)).apply(this, arguments));

		_this.state = {
			launching: false
		};
		_this.unsubscribe = subscribe(_this.storeDidUpdate.bind(_this));
		return _this;
	}

	_createClass(LayoutBlockEdit, [{
		key: 'storeDidUpdate',
		value: function storeDidUpdate() {
			var isSavingPost = this.props.isSavingPost;
			var launching = this.state.launching;

			if (launching && !isSavingPost()) {
				this.unsubscribe();
				this.redirectToBuilder();
			}
		}
	}, {
		key: 'componentDidMount',
		value: function componentDidMount() {
			var getBlockCount = this.props.getBlockCount;

			if (1 === getBlockCount()) {
				this.toggleEditor('disable');
			}
		}
	}, {
		key: 'componentWillUnmount',
		value: function componentWillUnmount() {
			this.unsubscribe();
			this.toggleEditor('enable');
		}
	}, {
		key: 'render',
		value: function render() {
			var _props = this.props,
			    getBlockCount = _props.getBlockCount,
			    onReplace = _props.onReplace;
			var launching = this.state.launching;

			var label = void 0,
			    callback = void 0,
			    description = void 0;

			if (1 === getBlockCount()) {
				label = builder.access ? strings.launch : strings.view;
				callback = this.launchBuilder.bind(this);
			} else {
				label = strings.convert;
				callback = this.convertToBuilder.bind(this);
			}

			if (builder.enabled) {
				description = strings.active;
			} else {
				description = strings.description;
			}

			return React.createElement(
				Placeholder,
				{
					key: 'placeholder',
					instructions: description,
					icon: 'welcome-widgets-menus',
					label: strings.title,
					className: 'fl-builder-layout-launch-view'
				},
				launching && React.createElement(Spinner, null),
				!launching && React.createElement(
					Button,
					{
						isLarge: true,
						isPrimary: true,
						type: 'submit',
						onClick: callback
					},
					label
				),
				!launching && React.createElement(
					Button,
					{
						isLarge: true,
						type: 'submit',
						onClick: this.convertToBlocks.bind(this)
					},
					strings.editor
				)
			);
		}
	}, {
		key: 'toggleEditor',
		value: function toggleEditor() {
			var method = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 'enable';
			var classList = document.body.classList;

			var enabledClass = 'fl-builder-layout-enabled';

			if ('enable' === method) {
				if (classList.contains(enabledClass)) {
					classList.remove(enabledClass);
				}
			} else {
				if (!classList.contains(enabledClass)) {
					classList.add(enabledClass);
				}
			}
		}
	}, {
		key: 'redirectToBuilder',
		value: function redirectToBuilder() {
			window.location.href = builder.access ? urls.edit : urls.view;
		}
	}, {
		key: 'launchBuilder',
		value: function launchBuilder() {
			var savePost = this.props.savePost;

			this.setState({ launching: true });
			savePost();
		}
	}, {
		key: 'convertToBuilder',
		value: function convertToBuilder() {
			var _props2 = this.props,
			    clientId = _props2.clientId,
			    getBlocks = _props2.getBlocks,
			    setAttributes = _props2.setAttributes,
			    removeBlocks = _props2.removeBlocks;

			var blocks = getBlocks();
			var content = serialize(blocks);
			var clientIds = blocks.map(function (block) {
				return block.clientId;
			}).filter(function (id) {
				return id !== clientId;
			});

			setAttributes({ content: content.replace(/<!--(.*?)-->/g, '') });
			removeBlocks(clientIds);
			this.launchBuilder();
		}
	}, {
		key: 'convertToBlocks',
		value: function convertToBlocks() {
			var _props3 = this.props,
			    attributes = _props3.attributes,
			    clientId = _props3.clientId,
			    replaceBlocks = _props3.replaceBlocks,
			    onReplace = _props3.onReplace;


			if (attributes.content && !confirm(strings.warning)) {
				return;
			} else if (attributes.content) {
				replaceBlocks([clientId], rawHandler({
					HTML: attributes.content,
					mode: 'BLOCKS'
				}));
			} else {
				onReplace([]);
			}
		}
	}]);

	return LayoutBlockEdit;
}(Component);

/**
 * Connect the edit component to editor data.
 */


var LayoutBlockEditConnected = compose(withDispatch(function (dispatch, ownProps) {
	var editor = dispatch('core/editor');
	return {
		savePost: editor.savePost,
		removeBlocks: editor.removeBlocks,
		replaceBlocks: editor.replaceBlocks
	};
}), withSelect(function (select) {
	var editor = select('core/editor');
	return {
		getBlockCount: editor.getBlockCount,
		getBlocks: editor.getBlocks,
		isSavingPost: editor.isSavingPost
	};
}))(LayoutBlockEdit);

/**
 * Register the block.
 */
if (builder.access && builder.unrestricted || builder.enabled) {
	registerBlockType('fl-builder/layout', {
		title: strings.title,
		description: strings.description,
		icon: 'welcome-widgets-menus',
		category: 'layout',
		useOnce: true,
		supports: {
			customClassName: false,
			className: false,
			html: false
		},
		attributes: {
			content: {
				type: 'string',
				source: 'html'
			}
		},
		edit: LayoutBlockEditConnected,
		save: function save(_ref) {
			var attributes = _ref.attributes;

			return React.createElement(
				RawHTML,
				null,
				attributes.content
			);
		}
	});
}

/***/ }),

/***/ 37:
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ })

/******/ });
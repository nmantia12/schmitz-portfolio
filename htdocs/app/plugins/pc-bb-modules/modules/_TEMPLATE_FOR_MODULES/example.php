<?php
/**
 * @class PC_EXAMPLE_Module
 *
 *  Property Reference: http://kb.wpbeaverbuilder.com/article/124-custom-module-developer-guide#module-property-ref
 *  Method Reference: http://kb.wpbeaverbuilder.com/article/124-custom-module-developer-guide#module-method-ref
 */
class PC_EXAMPLE_Module extends FLBuilderModule {

	/**
	 * Constructor function for the module. You must pass the
	 * name, description, dir and url in an array to the parent class.
	 *
	 * @method __construct
	 */

	public function __construct() {
		parent::__construct([
			'name'         => __('Example', 'fl-builder'),
			'description'  => __('Just an example module.', 'fl-builder'),
			'category'     => __('Paradowski Modules', 'fl-builder'),
			'dir'          => PC_MODULES_DIR . 'modules/' . '_TEMPLATE_FOR_MODULES/',
            'url'          => PC_MODULES_URL . 'modules/' . '_TEMPLATE_FOR_MODULES/',
		]);

		/**
         * Use these methods to enqueue css and js already
         * registered or to register and enqueue your own.
         */
        // Already registered
        // $this->add_css('font-awesome');
        // $this->add_js('jquery-bxslider');

        // Register and enqueue your own
        // $this->add_css('example-lib', $this->url . 'css/example-lib.css');
        // $this->add_js('example-lib', $this->url . 'js/example-lib.js', array(), '', true);
	}

	/**
     * Use this method to work with settings data before
     * it is saved. You must return the settings object.
     *
     * @method update
     * @param $settings {object}
     */
    public function update($settings) {

        return $settings;
    }

    /**
     * This method will be called by the builder
     * right before the module is deleted.
     *
     * @method delete
     */
    public function delete() {

    }
}

/**
* Register the module and its form settings.
*/
FLBuilder::register_module('PC_EXAMPLE_Module', [
    'general' => [ // Tab
        'title'    => __('General', 'fl-builder'), // Tab title
        'sections' => [ // Tab Sections
            'general' => [ // Section
                'title'  => __('Section Title', 'fl-builder'), // Section Title
                'fields' => []
            ]
        ]
    ],
    'toggle'       => [ // Tab
        'title'         => __('Toggle', 'fl-builder'), // Tab title
        'sections'      => [ // Tab Sections
            'general'       => [ // Section
                'title'         => __('Toggle Example', 'fl-builder'), // Section Title
                'fields'        => [ // Section Fields
                    'toggle_me'     => [
                        'type'          => 'select',
                        'label'         => __('Toggle Me!', 'fl-builder'),
                        'default'       => 'option-1',
                        'options'       => [
                            'option-1'      => __('Option 1', 'fl-builder'),
                            'option-2'      => __('Option 2', 'fl-builder')
                        ],
                        'toggle'        => [
                            'option-1'      => [
                                'fields'        => ['toggle_text', 'toggle_text2'],
                                'sections'      => ['toggle_section']
                            ],
                            'option-2'      => []
                        ]
                    ],
                    'toggle_text'   => [
                        'type'          => 'text',
                        'label'         => __('Hide Me!', 'fl-builder'),
                        'default'       => '',
                        'description'   => 'I get hidden when you toggle the select above.'
                    ],
                    'toggle_text2'   => [
                        'type'          => 'text',
                        'label'         => __('Me Too!', 'fl-builder'),
                        'default'       => ''
                    ]
                ]
            ],
            'toggle_section' => [ // Section
                'title'         => __('Hide This Section!', 'fl-builder'), // Section Title
                'fields'        => [ // Section Fields
                    'some_text'     => [
                        'type'          => 'text',
                        'label'         => __('Text', 'fl-builder'),
                        'default'       => ''
                    ]
                ]
            ]
        ]
    ],
    'multiple'      => [ // Tab
        'title'         => __('Multiple', 'fl-builder'), // Tab title
        'sections'      => [ // Tab Sections
            'general'       => [ // Section
                'title'         => __('Multiple Example', 'fl-builder'), // Section Title
                'fields'        => [ // Section Fields
                    'test'          => [
                        'type'          => 'text',
                        'label'         => __('Multiple Test', 'fl-builder'),
                        'multiple'      => true // Doesn't work with editor or photo fields
                    ]
                ]
            ]
        ]
    ],
]);

?>

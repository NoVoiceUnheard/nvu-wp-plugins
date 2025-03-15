(function (wp) {
    var registerBlockType = wp.blocks.registerBlockType;
    var createElement = wp.element.createElement;
    var useState = wp.element.useState;
    var useEffect = wp.element.useEffect;
    var InspectorControls = wp.blockEditor.InspectorControls;
    var SelectControl = wp.components.SelectControl;
    var PanelBody = wp.components.PanelBody;
    var apiFetch = wp.apiFetch;
    var select = wp.data.select;

    registerBlockType('cf7/submission-block', {
        title: 'CF7 Submissions',
        icon: 'list-view',
        category: 'widgets',
        attributes: {
            postType: {
                type: 'string',
                default: ''
            }
        },

        edit: function (props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;

            var _useState = useState([]),
                postTypes = _useState[0],
                setPostTypes = _useState[1];

            useEffect(function () {
                apiFetch({ path: '/wp/v2/types' }).then(function (types) {
                    var formPostTypes = Object.keys(types).filter(function (type) {
                        return type.startsWith('cf7_');
                    });
                    setPostTypes(formPostTypes);
                });
            }, []);

            return createElement(
                'div',
                {},
                createElement(
                    InspectorControls,
                    {},
                    createElement(
                        PanelBody,
                        { title: 'Form Selector' },
                        createElement(SelectControl, {
                            label: 'Select Form',
                            value: attributes.postType,
                            options: [{ label: 'Select a form...', value: '' }].concat(
                                postTypes.map(function (type) {
                                    return { label: type.replace('cf7_', ''), value: type };
                                })
                            ),
                            onChange: function (value) {
                                setAttributes({ postType: value });
                            }
                        })
                    )
                ),
                createElement(
                    'p',
                    {},
                    attributes.postType
                        ? 'Displaying submissions for: ' + attributes.postType
                        : 'Select a form to display submissions.'
                )
            );
        },

        save: function () {
            return null; // Uses PHP render_callback
        }
    });
    registerBlockType("custom/tag-dropdown", {
        title: "Tag Dropdown",
        category: "widgets",
        icon: "filter",

        edit: function () {
            // Get all post tags
            let tags = select("core").getEntityRecords("taxonomy", "post_tag", { per_page: -1 });
            // Create a select dropdown
            let selectElement = createElement(
                "select",
                {
                    id: "tag-dropdown-editor"
                },
                [
                    createElement("option", { value: "" }, "Select a tag"),
                    ...(tags ? tags.map(tag => createElement("option", { value: tag.name }, tag.name)) : [])
                ]
            );

            return createElement("div", {}, selectElement);
        },

        save: function () {
            return null; // Rendered dynamically via PHP
        }
    });
})(window.wp);




(function () {
    var el = wp.element.createElement;
    var registerBlockType = wp.blocks.registerBlockType;

    registerBlockType("cf7/submission-block", {
        title: "CF7 Submissions",
        category: "widgets",
        attributes: {
            fields: {
                type: "array",
                default: [],
            },
        },
        edit: function (props) {
            var selectedFields = props.attributes.fields || [];

            function updateFields(event) {
                var field = event.target.value;
                if (event.target.checked) {
                    selectedFields.push(field);
                } else {
                    selectedFields = selectedFields.filter(f => f !== field);
                }
                props.setAttributes({ fields: selectedFields });
            }

            function fetchFields(callback) {
                fetch("/wp-json/cf7/v1/fields")
                    .then((response) => response.json())
                    .then(callback);
            }

            var fieldOptions = el("div", {}, "Loading fields...");

            fetchFields(function (fields) {
                fieldOptions = el(
                    "div",
                    {},
                    fields.map(function (field) {
                        return el("label", {},
                            el("input", {
                                type: "checkbox",
                                value: field,
                                checked: selectedFields.includes(field),
                                onChange: updateFields
                            }),
                            " " + field
                        );
                    })
                );
            });

            return el("div", {}, el("p", {}, "CF7 Submission Block - Configure fields in settings"), fieldOptions);
        },
        save: function () {
            return null; // Uses PHP render_callback
        }
    });
})();
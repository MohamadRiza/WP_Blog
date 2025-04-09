"use strict";
(function () {
    var _wp = wp,
        _wp$serverSideRender = _wp.serverSideRender,
        createElement = wp.element.createElement,
        ServerSideRender = _wp$serverSideRender === void 0 ? wp.components.ServerSideRender : _wp$serverSideRender,
        _ref = wp.blockEditor || wp.editor,
        InspectorControls = _ref.InspectorControls,
        _wp$components = wp.components,
        TextareaControl = _wp$components.TextareaControl,
        Button = _wp$components.Button,
        PanelBody = _wp$components.PanelBody,
        Placeholder = _wp$components.Placeholder,
        registerBlockType = wp.blocks.registerBlockType;

    var sbrIcon = createElement('svg', {
        width: 20,
        height: 20,
        viewBox: '0 0 16 17',
        className: 'dashicon'
    }, createElement('path', {
        fill: 'currentColor',
        d: 'M2.66683 1.83331H13.3335C14.0668 1.83331 14.6668 2.43331 14.6668 3.16665V11.1666C14.6668 11.9 14.0668 12.5 13.3335 12.5H10.2502L8.15756 15.0222C7.94521 15.2781 7.54681 15.2593 7.35955 14.9845L5.66683 12.5L2.58349 12.4212C1.88845 12.4049 1.3335 11.8368 1.3335 11.1416V3.16665C1.3335 2.43331 1.9335 1.83331 2.66683 1.83331ZM8.11539 3.77526C8.07255 3.67298 7.92763 3.67298 7.8848 3.77526L6.96671 5.96725C6.94868 6.01028 6.9082 6.03969 6.86171 6.04353L4.49329 6.23933C4.38278 6.24846 4.338 6.38628 4.42204 6.45863L6.22304 8.00915C6.2584 8.03959 6.27386 8.08718 6.26315 8.13258L5.71748 10.4456C5.69201 10.5535 5.80925 10.6387 5.90403 10.5811L7.9352 9.3474C7.97507 9.32318 8.02511 9.32318 8.06498 9.3474L10.0962 10.5811C10.1909 10.6387 10.3082 10.5535 10.2827 10.4456L9.73703 8.13258C9.72632 8.08718 9.74179 8.03959 9.77714 8.00915L11.5781 6.45863C11.6622 6.38628 11.6174 6.24846 11.5069 6.23933L9.13847 6.04353C9.09198 6.03969 9.0515 6.01028 9.03348 5.96725L8.11539 3.77526Z'
    }));

    registerBlockType('sbr/sbr-feed-block', {
        title: 'Reviews Feed',
        icon: sbrIcon,
        category: 'widgets',
        attributes: {
            noNewChanges: {
                type: 'boolean',
            },
            shortcodeSettings: {
                type: 'string',
            },
            executed: {
                type: 'boolean'
            }
        },
        edit: function edit(props) {
            var _props = props,
                setAttributes = _props.setAttributes,
                _props$attributes = _props.attributes,
                _props$attributes$sho = _props$attributes.shortcodeSettings,
                shortcodeSettings = _props$attributes$sho === void 0 ? sbr_block_editor.shortcodeSettings : _props$attributes$sho,
                _props$attributes$cli = _props$attributes.noNewChanges,
                noNewChanges = _props$attributes$cli === void 0 ? true : _props$attributes$cli,
                _props$attributes$exe = _props$attributes.executed,
                executed = _props$attributes$exe === void 0 ? false : _props$attributes$exe;

            props.attributes.shortcodeSettings = shortcodeSettings;

            function setState(shortcodeSettingsContent) {
                setAttributes({
                    noNewChanges: false,
                    shortcodeSettings: shortcodeSettingsContent
                });
            }

            function previewClick(content) {
                setAttributes({
                    noNewChanges: true,
                    executed: false,
                });
            }
            function afterRender() {
                // no way to run a script after AJAX call to get feed so we just try to execute it on a few intervals
                if (! executed
                    || typeof window.sbrGutenberg === 'undefined') {
                    window.sbr = true;
                    window.sbrGutenberg = true;
                    setTimeout(function() { if (typeof sbr_init !== 'undefined') {sbr_init();}},1000);
                    setTimeout(function() { if (typeof sbr_init !== 'undefined') {sbr_init();}},2000);
                    setTimeout(function() { if (typeof sbr_init !== 'undefined') {sbr_init();}},3000);
                    setTimeout(function() { if (typeof sbr_init !== 'undefined') {sbr_init();}},5000);
                    setTimeout(function() { if (typeof sbr_init !== 'undefined') {sbr_init();}},10000);
                }
                setAttributes({
                    executed: true,
                });
            }

            var jsx = [React.createElement(InspectorControls, {
                key: "sbr-gutenberg-setting-selector-inspector-controls"
            }, React.createElement(PanelBody, {
                title: sbr_block_editor.i18n.addSettings
            }, React.createElement(TextareaControl, {
                key: "sbr-gutenberg-settings",
                className: "sbr-gutenberg-settings",
                label: sbr_block_editor.i18n.shortcodeSettings,
                help: sbr_block_editor.i18n.example + ": 'feed=\"1\"",
                value: shortcodeSettings,
                onChange: setState
            }), React.createElement(Button, {
                key: "sbr-gutenberg-preview",
                className: "sbr-gutenberg-preview",
                onClick: previewClick,
                isDefault: true
            }, sbr_block_editor.i18n.preview)))];

            if (noNewChanges) {
                afterRender();
                jsx.push(React.createElement(ServerSideRender, {
                    key: "reviews-feed/reviews-feed",
                    block: "sbr/sbr-feed-block",
                    attributes: props.attributes,
                }));
            } else {
                props.attributes.noNewChanges = false;
                jsx.push(React.createElement(Placeholder, {
                    key: "sbr-gutenberg-setting-selector-select-wrap",
                    className: "sbr-gutenberg-setting-selector-select-wrap"
                }, React.createElement(Button, {
                    key: "sbr-gutenberg-preview",
                    className: "sbr-gutenberg-preview",
                    onClick: previewClick,
                    isDefault: true
                }, sbr_block_editor.i18n.preview)));
            }

            return jsx;
        },
        save: function save() {
            return null;
        }
    });
})();

jQuery(document).ready( function($) {

    $('.form-table input[type=hidden]').closest('table').hide();

    var textareaFilters = $('textarea#psn_option_placeholders_filters');
    textareaFilters.hide();
    var filters_val = textareaFilters.val();
    textareaFilters.closest('td').prepend('<div id="placeholders_filters_editor"></div>');

    var editorFilters = ace.edit("placeholders_filters_editor");

    editorFilters.setTheme("ace/theme/github");
    editorFilters.getSession().setMode("ace/mode/twig");
    editorFilters.getSession().setValue(filters_val);

    editorFilters.getSession().on('change', function() {
        textareaFilters.val(editorFilters.getSession().getValue());
    });
});
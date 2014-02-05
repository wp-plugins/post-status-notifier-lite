jQuery(document).ready( function($) {

    var premium = '';
    if (psn.is_premium == false) {
        premium = '<br><span class="premium_notice">' + psn_taxonomies.lang_premium_feature + '</span>';
    }
    $('#psn_form_rule li:eq(1)').append('<table class="form-table"><tbody><tr valign="top"><th scope="row"><label>'+ psn_taxonomies.lang_Categories + premium +'</label></th><td id="category_container"></td></tr></tbody></table>' +
        '<p class="hint" id="categories_hint" style="display: none;">'+ psn_taxonomies.lang_categories_help +'</p>' );

    $('#posttype').on('change', function (event) {

        var selected_posttype = $(this).val();
        var selected_posttype_label = $("#posttype option[value='"+ selected_posttype +"']").text();
        var category_container_el = 'category_container_' + selected_posttype;

        $('#category_container > div').hide();

        if ($('#category_container #' + category_container_el).length > 0) {
            $('#category_container #' + category_container_el).show();
            $('#categories_hint').show();
        } else {
            var output = '<div id="'+ category_container_el +'">';

            if (typeof psn_taxonomies[selected_posttype] != 'undefined') {

                var size = 10;
                if (psn_taxonomies[selected_posttype].length < 10) {
                    size = psn_taxonomies[selected_posttype].length;
                }
                var disabled = '';
                if (psn.is_premium == false) {
                    disabled = ' disabled';
                }

                var options_include = '';
                $.each(psn_taxonomies[selected_posttype], function( i, cat ) {
                    var selected = '';
                    if (typeof psn_taxonomies_selected != 'undefined' &&  typeof psn_taxonomies_selected.include != 'undefined' && $.inArray( cat.id, psn_taxonomies_selected.include ) > -1) {
                        var selected = ' selected="selected"';
                    }
                    options_include += '<option value="'+ cat.id +'"'+ selected + disabled +'>' + cat.name + '</option>';
                });

                var options_exclude = '';
                $.each(psn_taxonomies[selected_posttype], function( i, cat ) {
                    var selected = '';
                    if (typeof psn_taxonomies_selected != 'undefined' && typeof psn_taxonomies_selected.exclude != 'undefined' && $.inArray( cat.id, psn_taxonomies_selected.exclude ) > -1) {
                        var selected = ' selected="selected"';
                    }
                    options_exclude += '<option value="'+ cat.id +'"'+ selected + disabled +'>' + cat.name + '</option>';
                });

                var el_id_include = 'category_include_'+ selected_posttype;
                var el_id_exclude = 'category_exclude_'+ selected_posttype;

                output += '<div style="width: 100%">';
                output += '<div style="float: left; width: 45%;">';
                output += '<span class="cat_inc_ex_header">' + psn_taxonomies.lang_include_categories + '</span>:<br><a href="javascript:void(0)" onclick="psn_rule_form_select_all(\''+ el_id_include +'\');">'+ psn_taxonomies.lang_select_all +'</a>&nbsp;|&nbsp;<a href="javascript:void(0)" onclick="psn_rule_form_unselect_all(\''+ el_id_include +'\');">'+ psn_taxonomies.lang_remove_all +'</a><br>';
                output += '<select size="'+ size +'" name="'+ el_id_include +'[]" id="'+ el_id_include +'" multiple>' + options_include + '</select>';
                output += '</div>';
                output += '<div style="">';
                output += '<span class="cat_inc_ex_header">' + psn_taxonomies.lang_exclude_categories +  '</span>:<br><a href="javascript:void(0)" onclick="psn_rule_form_select_all(\''+ el_id_exclude +'\');">'+ psn_taxonomies.lang_select_all +'</a>&nbsp;|&nbsp;<a href="javascript:void(0)" onclick="psn_rule_form_unselect_all(\''+ el_id_exclude +'\');">'+ psn_taxonomies.lang_remove_all +'</a><br>';
                output += '<select size="'+ size +'" name="'+ el_id_exclude +'[]" id="'+ el_id_exclude +'" multiple>' + options_exclude + '</select>';
                output += '</div>';
                output += '<div style="clear: both;"></div>';
                output += '</div>';
                $('#categories_hint').show();

            } else {
                output += new String(psn_taxonomies.lang_no_categories).replace('%s', selected_posttype_label);
                $('#categories_hint').hide();
            }
            output += '</div>';

            $('#category_container').append(output);
        }

    });

    $( "#posttype" ).trigger( "change" );

    if ($('#to').length > 0 && psn.is_premium == true) {
        $('#recipient').on('change', function (event) {
            var selected_recipient = $(this).val();
            var li = $('#to').closest('li');
            var errors = li.next('.errors');
            if (selected_recipient == 'individual_email') {
                li.show();

                if (errors.length > 0) {
                    errors.show();
                    errors.next('.hint').show();
                } else {
                    li.next('.hint').show();
                }

            } else {
                li.hide();

                if (errors.length > 0) {
                    errors.hide();
                    errors.next('.hint').hide();
                } else {
                    li.next('.hint').hide();
                }
            }
        });
    }

    $( "#recipient" ).trigger( "change" );
});

function psn_rule_form_select_all(select_id) {
    if (psn.is_premium == false) {
        return;
    }
    jQuery('#' + select_id +' option').prop('selected',true);
}
function psn_rule_form_unselect_all(select_id) {
    if (psn.is_premium == false) {
        return;
    }
    jQuery('#' + select_id +' option').prop('selected',false);
}
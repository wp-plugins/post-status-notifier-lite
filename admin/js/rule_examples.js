
jQuery(document).ready( function($) {
    $('#example_pending_post').click(function(link) {
        $('#name').val(PsnExampleRule.ThePendingPost);

        $('#posttype').val('post');
        $('#status_before').val('draft');
        $('#status_after').val('pending');

        $('#notification_subject').val(PsnExampleRule.ThePendingPostSubject);
        $('#notification_body').val(PsnExampleRule.ThePendingPostBody);

        $('#recipient').val('admin');
        $('#cc').val('reviewer@yourdomain.com');
        return false;
    });
    $('#example_happy_author').click(function(link) {
        $('#name').val(PsnExampleRule.TheHappyAuthor);

        $('#posttype').val('post');
        $('#status_before').val('pending');
        $('#status_after').val('publish');

        $('#notification_subject').val(PsnExampleRule.TheHappyAuthorSubject);
        $('#notification_body').val(PsnExampleRule.TheHappyAuthorBody);

        $('#recipient').val('author');
        $('#cc').val('');
        return false;
    });

    $('#example_pedantic_admin').click(function(link) {
        $('#name').val(PsnExampleRule.ThePedanticAdmin);

        $('#posttype').val('post');
        $('#status_before').val('anything');
        $('#status_after').val('anything');

        $('#notification_subject').val(PsnExampleRule.ThePedanticAdminSubject);
        $('#notification_body').val(PsnExampleRule.ThePedanticAdminBody);

        $('#recipient').val('admin');
        $('#cc').val('');
        return false;
    });

});

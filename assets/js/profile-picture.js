jQuery(document).ready(function($){
    var mediaUploader;

    $('#upload_profile_picture_button').on('click', function(e) {
        e.preventDefault();

        if (mediaUploader) {
            mediaUploader.open();
            return;
        }

        mediaUploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Profile Picture',
            button: { text: 'Use this image' },
            multiple: false
        });

        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            $('#profile_picture').val(attachment.url);
        });

        mediaUploader.open();
    });



});

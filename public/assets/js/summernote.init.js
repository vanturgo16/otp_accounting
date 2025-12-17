
$(document).ready(function() {
    $('.summernote-editor').each(function() {
        $(this).summernote();
    });

    $('.summernote-editor-simple').summernote({
        height: 90,                // editor height
        placeholder: 'Type here...', 
        toolbar: [
            ['style', ['bold', 'italic', 'underline']],
            ['para', ['ul', 'ol', 'paragraph']],
        ]
    });
});
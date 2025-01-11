<script>
    var height = 250;
    var fields = @json($custom_fields);
    fields = fields.map(t => t.replaceAll('\{\{', '').replaceAll('\}\}', ''));
</script>

<script>
    let hint = {
        hint: {
            words: fields, // Assuming 'fields' is defined and populated elsewhere
            match: /\B\{\{(\w*)$/i,
            search: function(keyword, callback) {
                callback($.grep(this.words, function(item) {
                    return item.includes(keyword);
                }));
            },
            content: function(item) {
                return '\{\{' + item + '\}\}'; // Escaping curly braces to avoid Blade syntax
            }
        }
    };
    $('.summernote1').summernote({
        height: height, // Assuming 'height' is a valid variable
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'underline', 'clear', 'strikethrough', 'superscript',
                'subscript'
            ]],
            ['fontname', ['fontname', 'fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph', 'height']],
            ['table', ['table']],
            ['insert', ['Embeded', 'Link', 'picture',
                'hr'
            ]], // Added 'Embeded' and 'Link' buttons
            ['view', ['fullscreen', 'codeview', 'undo', 'redo']],
            // ['custom', ['MergeButton']]

        ],
        placeholder: '',
        buttons: {},
        ...hint
    });

    $('.summernote').summernote({
        toolbar: ['hint'],
        shortcuts: false,
        height: height,
        buttons: {},
        ...hint
    });
</script>

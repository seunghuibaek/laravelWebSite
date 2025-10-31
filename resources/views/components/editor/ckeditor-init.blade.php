@php
    $shouldUseEditor = isset($board) ? ($board->use_editor ?? false) : ($use_editor ?? true);
    $selector = $selector ?? '#content';
    $language = $language ?? (app()->getLocale() ?? 'en');
@endphp

@if($shouldUseEditor)
    <style>
        .ck-editor__editable { min-height: 320px; }
    </style>
    <!-- CKEditor 5 Classic (common include) -->
{{--    <script src="{{ asset('js/ckeditor.js') }}"></script>--}}
    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
    <script src="{{ asset('js/UploadAdapter.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var el = document.querySelector(@json($selector));
            if (el) {
                ClassicEditor.create(el, {
                    toolbar: [
                        'undo','redo','|','bold','italic','link','|',
                        'imageUpload','|','insertTable','blockQuote','|',
                        'toggleImageCaption','imageTextAlternative','|',
                        'resizeImage', 'imageStyle:inline','imageStyle:block','imageStyle:side'
                    ],
                    image: {
                        resizeUnit: '%',   // 'px'로 바꿀 수 있음
                        resizeOptions: [
                            { name: 'resizeImage:original', label: '원본', value: null },
                            { name: 'resizeImage:25', label: '25%', value: '25' },
                            { name: 'resizeImage:50', label: '50%', value: '50' },
                            { name: 'resizeImage:75', label: '75%', value: '75' }
                        ],
                        toolbar: [
                            'toggleImageCaption','imageTextAlternative','|',
                            'resizeImage','|',
                            'imageStyle:inline','imageStyle:block','imageStyle:side'
                        ]
                    },
                    extraPlugins: [CustomUploadAdapterPlugin],
                    language: @json($language)
                }).catch(function (error) {
                    console.error(error);
                });
            }
        });
    </script>
@endif

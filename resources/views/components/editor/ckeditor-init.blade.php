@php
    $shouldUseEditor = isset($board) ? ($board->use_editor ?? false) : ($use_editor ?? true);
    $selector = $selector ?? '#content';
    $language = $language ?? (app()->getLocale() ?? 'en');
@endphp

@if($shouldUseEditor)
    <!-- CKEditor 5 Classic (common include) -->
    <script src="{{ asset('js/ckeditor.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var el = document.querySelector(@json($selector));
            if (el) {
                ClassicEditor.create(el, {
                    language: @json($language)
                }).catch(function (error) {
                    console.error(error);
                });
            }
        });
    </script>
@endif

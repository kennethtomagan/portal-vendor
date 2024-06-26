<!DOCTYPE html>
<html lang="en">
    <head>

        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">

        <script crossorigin src="https://unpkg.com/react@18/umd/react.production.min.js"></script>
        <script crossorigin src="https://unpkg.com/react-dom@18/umd/react-dom.production.min.js"></script>

        <title>Plain Text Editor</title>

        @if(file_exists('/vendor/editor/js/manifest.js'))
            <script src="{{ mix('js/manifest.js', '/vendor/editor') }}"></script>
        @endif

        @if(file_exists('/vendor/editor/js/vendor.js'))
            <script src="{{ mix('js/vendor.js', '/vendor/editor') }}"></script>
        @endif

        <link href="{{ mix('css/tool.css', '/vendor/editor') }}" rel="stylesheet">


    </head>

    <body>
        <h1>Gutenberg attached to a plain textarea</h1>
        <p>The textarea can be converted into a Gutenberg editor. Any existing content will be converted into blocks. Multiple editors can be added, with a seperate edit history for each.</p>

        <div class="editors">
            <div class="editor-container cloner">
                <p>
                    <button onClick="toggleEditor( this );">Toggle Gutenberg</button>
                </p>
                <textarea rows="10"></textarea>
            </div>
            <div class="editor-container">
                <p>
                    <button onClick="toggleEditor( this );">Toggle Gutenberg</button>
                </p>
                <textarea rows="10"></textarea>
            </div>

            <textarea id="editor" rows="10"></textarea>

        </div>

        <button class="add-another" onClick="addEditor()">Add another editor</button>

        <script>
        function toggleEditor( button ) {
            const textarea = button.parentNode.parentNode.querySelector( 'textarea' );

            if ( textarea.style.display === 'none' ) {
                wp.detachEditor( textarea );
            } else {
                wp.attachEditor( textarea);
            }
        }

        function addEditor() {
            const editor = document.querySelector( '.cloner' );
            const clone = editor.cloneNode( true );

            clone.classList.remove( 'cloner' );

            editor.parentNode.appendChild( clone );
        }
    </script>

        <script src="{{ asset('/vendor/editor/js/tool.js') }}"></script>


    </body>





</html>

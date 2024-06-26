import IsolatedBlockEditor from '@automattic/isolated-block-editor';

const settings = {
    editor: {
        hasFixedToolbar: true,
        codeEditingEnabled: true,
        __experimentalFeatures: {
            color: {
                text: true,
                background: true,
            },
        },
    },
    iso: {
        footer: true,
        header: true,
        toolbar: {
            undo: true,
            inserter: true,
            inspector: true,
            navigation: true,
            documentInspector: true,
        },
        moreMenu: {
            editor: true,
            fullscreen: true,
            preview: true,
            inspector: true,
        },
        sidebar: {
            inspector: true,
        }
    },
};

if (document.getElementById('editor')) {

    console.log("hello World");

    ReactDOM.render(
        <IsolatedBlockEditor
            settings ={settings}
        >
            <EditorLoaded onLoaded={() => {}} onLoading={() => {}} />
            <DocumentSection>Extra Information</DocumentSection>

            <ToolbarSlot>
                <button>Beep!</button>
            </ToolbarSlot>
        </IsolatedBlockEditor>,
        document.getElementById('editor')
    );
}

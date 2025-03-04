function aqInsertTextToEditor(label, editorId) {
    if (window['jInsertEditorText'] === 'function') {
        jInsertEditorText(label, editorId);
    } else if (window['Joomla'] && Joomla.editors && Joomla.editors.instances && Joomla.editors.instances[editorId]) {
        var editor = Joomla.editors.instances[editorId];
        editor.replaceSelection(label);
    }
}
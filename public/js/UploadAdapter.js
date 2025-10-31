class UploadAdapter {
    constructor(loader) {
        this.loader = loader;
    }

    upload() {
        return this.loader.file.then(file => new Promise((resolve, reject) => {
            this._initRequest();
            this._initListeners(resolve, reject, file);
            this._sendRequest(file);
        }))
    }

    _initRequest() {
        const xhr = this.xhr = new XMLHttpRequest();
        // Use API route (no CSRF required)
        xhr.open('POST', '/api/upload-image', true);
        xhr.responseType = 'json';
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.setRequestHeader('Accept', 'application/json');
    }

    _initListeners(resolve, reject, file) {
        const xhr = this.xhr;
        const loader = this.loader;
        const genericErrorText = '파일을 업로드 할 수 없습니다.'

        xhr.addEventListener('error', () => reject(genericErrorText));
        xhr.addEventListener('abort', () => reject());
        xhr.addEventListener('load', () => {
            const response = xhr.response;
            if (!response || response.error) {
                return reject(response && response.error ? response.error.message : genericErrorText);
            }

            resolve({
                // CKEditor 5 expects an object with a `default` URL
                default: response.link // 업로드된 파일 주소
            });
        });
    }

    _sendRequest(file) {
        const data = new FormData();
        // Backend expects 'file' field name
        data.append('file', file);
        this.xhr.send(data);
    }
}

function CustomUploadAdapterPlugin(editor) {
    editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
        return new UploadAdapter(loader);
    }
}

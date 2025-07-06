// Open or create the IndexedDB database for audio samples
function openAudioDatabase() {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open('PianoSamplesDB', 1);

        request.onupgradeneeded = function(event) {
            const db = event.target.result;
            if (!db.objectStoreNames.contains('samples')) {
                db.createObjectStore('samples', { keyPath: 'key' });
            }
        };

        request.onsuccess = function(event) {
            resolve(event.target.result);
        };

        request.onerror = function(event) {
            reject('Error opening database');
        };
    });
}

// Add a sample to IndexedDB as ArrayBuffer
function addSampleToDatabase(key, arrayBuffer) {
    return openAudioDatabase().then((db) => {
        const transaction = db.transaction('samples', 'readwrite');
        const store = transaction.objectStore('samples');
        store.put({ key: key, audioBuffer: arrayBuffer });
    });
}

// Retrieve a sample as ArrayBuffer from IndexedDB
function getSampleFromDatabase(key) {
    return openAudioDatabase().then((db) => {
        return new Promise((resolve, reject) => {
            const transaction = db.transaction('samples', 'readonly');
            const store = transaction.objectStore('samples');
            const request = store.get(key);
            request.onsuccess = function(event) {
                resolve(event.target.result ? event.target.result.audioBuffer : null);
            };
            request.onerror = function() {
                reject('Error retrieving sample');
            };
        });
    });
}


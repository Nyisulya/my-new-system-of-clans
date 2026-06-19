// Import and configure the Firebase SDK compat versions (standard for service workers)
importScripts('https://www.gstatic.com/firebasejs/9.23.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/9.23.0/firebase-messaging-compat.js');

// Parse the configuration query parameters sent during service worker registration
const urlParams = new URLSearchParams(self.location.search);

const firebaseConfig = {
    apiKey: urlParams.get('apiKey'),
    authDomain: urlParams.get('authDomain'),
    projectId: urlParams.get('projectId'),
    storageBucket: urlParams.get('storageBucket'),
    messagingSenderId: urlParams.get('messagingSenderId'),
    appId: urlParams.get('appId')
};

// Initialize only if configuration is provided
if (firebaseConfig.apiKey) {
    firebase.initializeApp(firebaseConfig);
    const messaging = firebase.messaging();

    // Handle background notifications
    messaging.onBackgroundMessage((payload) => {
        console.log('[firebase-messaging-sw.js] Received background message: ', payload);

        const notificationTitle = payload.notification.title;
        const notificationOptions = {
            body: payload.notification.body,
            icon: payload.notification.icon || '/favicon.ico',
            data: payload.data
        };

        self.registration.showNotification(notificationTitle, notificationOptions);
    });
} else {
    console.warn('[firebase-messaging-sw.js] Firebase API key not found in registration query parameters.');
}

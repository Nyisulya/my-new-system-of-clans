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
    messaging.onBackgroundMessage(function(payload) {
        console.log('[firebase-messaging-sw.js] Received background message: ', payload);

        let title = 'Arifa Mpya';
        let body = 'Una ujumbe mpya kutoka kwenye mfumo.';
        let icon = '/favicon.png'; // Make sure this icon exists
        let data = {};

        if (payload && payload.notification) {
            if (payload.notification.title) title = payload.notification.title;
            if (payload.notification.body) body = payload.notification.body;
            if (payload.notification.icon) icon = payload.notification.icon;
        } else if (payload && payload.data) {
            if (payload.data.title) title = payload.data.title;
            if (payload.data.body) body = payload.data.body;
        }

        if (payload && payload.data) {
            data = payload.data;
        }

        const notificationOptions = {
            body: body
        };

        return self.registration.showNotification(title, notificationOptions);
    });
        
    // Handle clicking on the notification in the background
    self.addEventListener('notificationclick', function(event) {
        event.notification.close();
        
        // If there's a click_action in data, open it
        if (event.notification.data && event.notification.data.click_action) {
            event.waitUntil(
                clients.openWindow(event.notification.data.click_action)
            );
        } else {
            // Default open to root
            event.waitUntil(
                clients.openWindow('/')
            );
        }
    });
} else {
    console.warn('[firebase-messaging-sw.js] Firebase API key not found in registration query parameters.');
}

// ==========================================
// PWA Requirements
// ==========================================
// A fetch event listener is required for Chrome to recognize this as a valid PWA.
self.addEventListener('fetch', function(event) {
    // We don't need to cache anything manually for now, just let the request go to the network
    event.respondWith(fetch(event.request).catch(function() {
        return new Response('Network error occurred.');
    }));
});

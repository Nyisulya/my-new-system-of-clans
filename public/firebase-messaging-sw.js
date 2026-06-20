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
    
    // We intentionally DO NOT use messaging.onBackgroundMessage() for notification payloads.
    // The Firebase SDK will automatically display the notification when it detects a `notification` payload.
    // This prevents the "This site has been updated in the background" Chrome error.
    
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

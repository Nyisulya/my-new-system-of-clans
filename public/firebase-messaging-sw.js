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

        // Safely extract notification data from either 'notification' or 'data' payload
        const notificationTitle = (payload.notification && payload.notification.title) || 
                                  (payload.data && payload.data.title) || 
                                  'Tangazo Jipya';
        
        const notificationOptions = {
            body: (payload.notification && payload.notification.body) || 
                  (payload.data && payload.data.body) || 
                  'Una ujumbe mpya.',
            icon: (payload.notification && payload.notification.icon) || '/favicon.png',
            data: payload.data || {}
        };

        // Tell the browser to show the notification
        return self.registration.showNotification(notificationTitle, notificationOptions);
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

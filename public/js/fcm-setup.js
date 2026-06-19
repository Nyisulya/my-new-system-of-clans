// FCM Setup & Token Retrieval
function initFcm() {
    // 1. Check if window.fcmConfig is set and has variables
    const config = window.fcmConfig;
    if (!config || !config.apiKey || config.apiKey.trim() === '') {
        console.warn('FCM configurations not found or empty in window.fcmConfig. Please check your .env configuration.');
        return;
    }

    const firebaseConfig = {
        apiKey: config.apiKey,
        authDomain: config.authDomain,
        projectId: config.projectId,
        storageBucket: config.storageBucket,
        messagingSenderId: config.messagingSenderId,
        appId: config.appId
    };

    // 2. Initialize Firebase
    if (!firebase.apps.length) {
        firebase.initializeApp(firebaseConfig);
    }
    
    const messaging = firebase.messaging();

    // 3. Request Permission and get Device Token
    Notification.requestPermission().then((permission) => {
        if (permission === 'granted') {
            console.log('Notification permission granted.');

            messaging.getToken({ vapidKey: config.vapidKey || 'BGa8aqwWAGOk2meXwB66DZK5xpn-2yfId4P8hbj0j5Dlmqjv4G4xQR0GwDlrtVZzet312i-5VWjUlB3aA2WbS-s' })
                .then((currentToken) => {
                    if (currentToken) {
                        console.log('FCM Token generated: ', currentToken);
                        sendTokenToServer(currentToken);
                    } else {
                        console.log('No registration token available. Request permission to generate one.');
                    }
                }).catch((err) => {
                    console.error('An error occurred while retrieving token: ', err);
                });
        } else {
            console.warn('Unable to get permission to notify.');
        }
    });

    // 4. Handle foreground notifications (when user is active on the page)
    messaging.onMessage((payload) => {
        console.log('Message received in foreground: ', payload);
        
        // Show notification using Toastr if available, otherwise fallback to alert
        if (window.toastr) {
            toastr.info(payload.notification.body, payload.notification.title);
        } else {
            alert(`${payload.notification.title}: ${payload.notification.body}`);
        }
    });
}

// 5. Send FCM Token to Laravel backend using Axios
function sendTokenToServer(token) {
    axios.post('/fcm-token', {
        fcm_token: token
    })
    .then(response => {
        console.log('FCM Token successfully stored on server: ', response.data);
    })
    .catch(error => {
        console.error('Error storing FCM Token on server: ', error);
    });
}

// Automatically trigger FCM initialization once the script loads
if (typeof firebase !== 'undefined') {
    initFcm();
} else {
    console.error('Firebase SDK not loaded. Please make sure to import firebase-app and firebase-messaging CDN scripts first.');
}

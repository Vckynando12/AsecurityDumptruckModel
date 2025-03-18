// RFID Access Monitoring
database.ref('smartcab').on('value', (snapshot) => {
    const smartcabData = snapshot.val();
    if (smartcabData) {
        // Door Status Change
        if (smartcabData.servo_status) {
            this.addNotification(
                'door',
                'Door Status Changed',
                `Door is now ${smartcabData.servo_status}`
            );
        }

        // Access Attempt
        if (smartcabData.last_access) {
            const accessMessage = smartcabData.last_access === 'Terdaftar' 
                ? 'Authorized access granted'
                : 'Unauthorized access attempt';
            
            this.addNotification(
                'door',
                'Access Event',
                `${accessMessage} (ID: ${smartcabData.status_device || 'Unknown'})`
            );
        }
    }
});

initializeDeviceMonitoring() {
    // ESP8266 Status
    database.ref('logs/systemESP').on('value', (snapshot) => {
        const status = snapshot.val();
        if (status && status !== NOTIFICATION_CONSTANTS.STATUS.DEVICE.ESP_ONLINE) {
            this.addNotification(
                'device',
                'ESP8266 Status Change',
                `ESP8266: ${status}`
            );
        }
    });

    // Device Heartbeat Monitoring
    database.ref('device/lastActive').on('value', (snapshot) => {
        const lastActive = snapshot.val();
        if (lastActive) {
            const timeDiff = Date.now() - (lastActive * 1000);
            if (timeDiff > 120000) { // 2 minutes threshold
                this.addNotification(
                    'device',
                    'Device Connection Warning',
                    'ESP8266 device has not reported for over 2 minutes'
                );
            }
        }
    });

    // DHT11 Sensor Status
    database.ref('logs/dht').on('value', (snapshot) => {
        const dhtStatus = snapshot.val();
        if (dhtStatus && dhtStatus.status !== NOTIFICATION_CONSTANTS.STATUS.SENSOR.CONNECTED) {
            this.addNotification(
                'sensor',
                'DHT11 Sensor Issue',
                dhtStatus.message || 'Temperature sensor is not responding'
            );
        }
    });

    // Device Restart Monitoring
    database.ref('control/restartESP').on('value', (snapshot) => {
        const restartStatus = snapshot.val();
        if (restartStatus === true) {
            this.addNotification(
                'device',
                'Device Restart',
                'ESP8266 device is restarting'
            );
        }
    });
}

addNotification(type, title, message) {
    const notification = {
        type,
        title,
        message,
        timestamp: Date.now(),
        color: NOTIFICATION_CONSTANTS.COLORS[type.toUpperCase()] || NOTIFICATION_CONSTANTS.COLORS.DEVICE
    };

    this.notifications.unshift(notification);
    if (this.notifications.length > this.maxNotifications) {
        this.notifications.pop();
    }

    this.updateNotificationPanel();
    this.updateNotificationBadge();

    if (Notification.permission === "granted") {
        new Notification(title, { 
            body: message,
            icon: '/icons/' + type + '.png' // Pastikan menyediakan icon yang sesuai
        });
    }
}

// ... (metode lain tetap sama seperti sebelumnya)
} 
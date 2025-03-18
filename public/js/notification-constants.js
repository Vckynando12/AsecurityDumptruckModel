const NOTIFICATION_TYPES = {
    MOTION: 'motion',
    DOOR: 'door',
    DEVICE: 'device'
};

const NOTIFICATION_COLORS = {
    [NOTIFICATION_TYPES.MOTION]: 'bg-red-500',
    [NOTIFICATION_TYPES.DOOR]: 'bg-green-500',
    [NOTIFICATION_TYPES.DEVICE]: 'bg-blue-500'
};

const DEVICE_STATUSES = {
    ESP_ONLINE: 'Device online',
    WEMOS_ONLINE: 'Device Online',
    SENSOR_CONNECTED: 'connected',
    RFID_CONNECTED: 'Connected'
}; 
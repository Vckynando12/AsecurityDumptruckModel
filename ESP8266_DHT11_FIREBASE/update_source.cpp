#include <Wire.h>
#include <MPU6050.h>
#include <ESP8266WiFi.h>
#include <FirebaseESP8266.h>
#include <DHT.h>

// Konfigurasi WiFi
#define WIFI_SSID "smartcab"
#define WIFI_PASSWORD "123123123"

// Konfigurasi Firebase
#define FIREBASE_HOST "smartcab-8bb42-default-rtdb.firebaseio.com"
#define FIREBASE_AUTH "kiiQoFa6Ckp7bL2oRLbaTSGQth9z0PgN64Ybv8dw"

FirebaseData firebaseData;
FirebaseConfig firebaseConfig;
FirebaseAuth firebaseAuth;

MPU6050 mpu;
const int relayMPUPin = D6;    // Relay untuk MPU6050
const int relayKipasPin = D5;  // Relay untuk kipas
const int dhtPin = D3;         // DHT11 sensor
#define DHTTYPE DHT11
DHT dht(dhtPin, DHTTYPE);

float threshold = 0.05;
float baseAccelX, baseAccelY, baseAccelZ;
bool relayActive = false;
bool mpuEnabled = true;
unsigned long relayStartTime = 0;
unsigned long lastUpdateTime = 0;
unsigned long lastI2CCheckTime = 0;
const unsigned long restartInterval = 6 * 60 * 60 * 1000; // Restart otomatis 6 jam

// Tambahkan variabel global untuk timestamp
unsigned long lastHeartbeatTime = 0;
const unsigned long heartbeatInterval = 60000; // 60 detik = 1 menit

void setup() {
    Serial.begin(115200);
    Wire.begin(D2, D1);  // SDA = D2, SCL = D1
    
    // Tambahkan pinMode untuk relay
    pinMode(relayMPUPin, OUTPUT);
    pinMode(relayKipasPin, OUTPUT);
    
    // Matikan semua relay saat startup
    digitalWrite(relayMPUPin, HIGH);    // Relay MPU OFF
    digitalWrite(relayKipasPin, HIGH);  // Relay Kipas OFF
    
    // Koneksi ke WiFi dulu
    WiFi.begin(WIFI_SSID, WIFI_PASSWORD);
    Serial.print("Menghubungkan ke WiFi");
    while (WiFi.status() != WL_CONNECTED) {
        Serial.print(".");
        delay(1000);
    }
    Serial.println("\nTerhubung ke WiFi");

    // Konfigurasi Firebase
    firebaseConfig.host = FIREBASE_HOST;
    firebaseConfig.signer.tokens.legacy_token = FIREBASE_AUTH;

    Firebase.begin(&firebaseConfig, &firebaseAuth);
    Firebase.reconnectWiFi(true);

    // Update status device saat startup
    Firebase.setString(firebaseData, "/logs/systemESP", "Device online");

    // Inisialisasi sensor
    mpu.initialize();
    dht.begin();

    // Cek MPU6050
    if (!mpu.testConnection()) {
        Serial.println("MPU6050 tidak terhubung!");
        Firebase.setString(firebaseData, "/logs/mpu/status", "disconnected");
    } else {
        Firebase.setString(firebaseData, "/logs/mpu/status", "connected");
    }

    // Cek DHT11
    float testReading = dht.readTemperature();
    if (isnan(testReading)) {
        Serial.println("DHT11 tidak terhubung atau error!");
        Firebase.setString(firebaseData, "/logs/dht/status", "disconnected");
    } else {
        Firebase.setString(firebaseData, "/logs/dht/status", "connected");
    }

    // Kalibrasi MPU6050 jika terhubung
    if (mpu.testConnection()) {
        Serial.println("Kalibrasi awal...");
        calibrateSensor();
    }

    // Konfigurasi NTP
    configTime(7 * 3600, 0, "pool.ntp.org");
    Serial.println("Waiting for time sync");
    while (time(nullptr) < 1000000000) {
        Serial.print(".");
        delay(100);
    }
    Serial.println("\nTime synchronized");
}

void loop() {
    // Update lastActive dengan unix timestamp
    if (millis() - lastHeartbeatTime >= heartbeatInterval) {
        lastHeartbeatTime = millis();
        unsigned long epochTime = time(nullptr);
        if (Firebase.setInt(firebaseData, "/device/lastActive", epochTime)) {
            Serial.println("Heartbeat sent: " + String(epochTime));
            // Tambahkan update status Device online saat heartbeat berhasil
            Firebase.setString(firebaseData, "/logs/systemESP", "Device online");
            Serial.println("Device status updated: Online");
        } else {
            Serial.println("Failed to send heartbeat");
            // Jika gagal mengirim heartbeat, set status offline
            Firebase.setString(firebaseData, "/logs/systemESP", "Device offline");
        }
    }

    // Cek jika tidak ada perubahan dalam 70 detik
    if (millis() - lastHeartbeatTime > 70000) {
        Firebase.setString(firebaseData, "/logs/systemESP", "Device offline");
    }

    // Tambahkan pengecekan status restart di awal loop
    if (Firebase.getString(firebaseData, "/control/restartESP")) {
        String restartStatus = firebaseData.stringData();
        if (restartStatus == "true") {
            Serial.println("Perintah restart diterima dari Firebase");
            // Reset status restart di Firebase ke false
            Firebase.setString(firebaseData, "/control/restartESP", "false");
            // Kirim log sebelum restart
            Firebase.setString(firebaseData, "/logs/systemESP", "Device restarting by command...");
            delay(1000); // Tunggu sebentar agar data terkirim
            ESP.restart();
        }
    }

    // Cek auto-restart berdasarkan interval
    if (millis() > restartInterval) {
        Serial.println("Auto-restart setelah " + String(restartInterval/3600000) + " jam");
        Firebase.setString(firebaseData, "/logs/systemESP", "Device auto-restarting...");
        delay(1000);
        ESP.restart();
    }

    // **Cek status MPU6050 setiap 30 detik**
    if (millis() - lastI2CCheckTime >= 30000) {
        lastI2CCheckTime = millis();
        if (!mpu.testConnection()) {
            Serial.println("MPU6050 tidak merespons! Reset I2C...");
            Firebase.setString(firebaseData, "/logs/mpu/status", "error");

            Wire.begin(D2, D1);
            mpu.initialize();

            if (mpu.testConnection()) {
                Firebase.setString(firebaseData, "/logs/mpu/status", "connected");
            }
        }
    }

    // **Baca status MPU dari Firebase**
    if (Firebase.getString(firebaseData, "/security/status")) {
        String mpuStatus = firebaseData.stringData();
        Serial.print("Status MPU dari Firebase: ");
        Serial.println(mpuStatus);
        mpuEnabled = (mpuStatus == "on");
    } else {
        Serial.println("Gagal membaca data dari Firebase!");
        Firebase.setString(firebaseData, "/logs/error", firebaseData.errorReason());
    }

    // **Proses MPU6050**
    if (mpuEnabled) {
        if (millis() - lastUpdateTime >= 10) {
            lastUpdateTime = millis();

            int16_t ax, ay, az;
            mpu.getAcceleration(&ax, &ay, &az);

            float accelX = ax / 16384.0;
            float accelY = ay / 16384.0;
            float accelZ = az / 16384.0;

            float deltaAccel = sqrt(pow(accelX - baseAccelX, 2) + pow(accelY - baseAccelY, 2) + pow(accelZ - baseAccelZ, 2));

            if (deltaAccel > threshold && !relayActive) {
                Serial.println("Getaran terdeteksi! Relay ON.");
                digitalWrite(relayMPUPin, LOW);
                relayActive = true;
                relayStartTime = millis();
                Firebase.setString(firebaseData, "/security/motion", "detected");
            }

            if (relayActive && millis() - relayStartTime >= 8000) {
                Serial.println("Relay mati, kalibrasi ulang.");
                digitalWrite(relayMPUPin, HIGH);
                relayActive = false;
                calibrateSensor();
                Firebase.setString(firebaseData, "/security/motion", "clear");
            }
        }
    } else {
        digitalWrite(relayMPUPin, HIGH);  // Pastikan relay OFF saat disabled
        relayActive = false;
        Firebase.setString(firebaseData, "/security/motion", "disabled");
    }

    // **Baca suhu dan kelembaban dari DHT11**
    float suhu = dht.readTemperature();
    float humidity = dht.readHumidity();
    
    if (isnan(suhu) || isnan(humidity)) {
        Serial.println("Gagal membaca data dari DHT11!");
        Firebase.setString(firebaseData, "/logs/dht/status", "error");
    } else {
        Firebase.setString(firebaseData, "/logs/dht/status", "connected");
        
        Serial.print("Suhu: ");
        Serial.print(suhu);
        Serial.println(" °C");
        Serial.print("Kelembaban: ");
        Serial.print(humidity);
        Serial.println(" %");
        
        Firebase.setFloat(firebaseData, "/dht11/temperature", suhu);
        Firebase.setFloat(firebaseData, "/dht11/humidity", humidity);
    }

    // **Kendalikan relay kipas**
    if (suhu > 40) {
        Serial.println("Suhu tinggi! Kipas ON.");
        digitalWrite(relayKipasPin, LOW);
        Firebase.setString(firebaseData, "/security/fan", "ON");
    } else {
        digitalWrite(relayKipasPin, HIGH);
        Firebase.setString(firebaseData, "/security/fan", "OFF");
    }

    yield();
}

void calibrateSensor() {
    int16_t ax, ay, az;
    mpu.getAcceleration(&ax, &ay, &az);
    baseAccelX = ax / 16384.0;
    baseAccelY = ay / 16384.0;
    baseAccelZ = az / 16384.0;
    Serial.println("Kalibrasi selesai!");
}

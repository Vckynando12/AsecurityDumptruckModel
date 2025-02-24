#include <Wire.h>
#include <MPU6050.h>
#include <ESP8266WiFi.h>
#include <FirebaseESP8266.h>

// Koneksi WiFi
#define WIFI_SSID "KONTRAKAN OYI"
#define WIFI_PASSWORD "warkopoyi"

// Koneksi Firebase
#define FIREBASE_HOST "smartcab-8bb42-default-rtdb.firebaseio.com"
#define FIREBASE_AUTH "kiiQoFa6Ckp7bL2oRLbaTSGQth9z0PgN64Ybv8dw"

FirebaseData firebaseData;

MPU6050 mpu;
const int relayPin = D6;
float threshold = 0.1;
float baseAccelX, baseAccelY, baseAccelZ;
bool relayActive = false;
bool mpuEnabled = true; // Status MPU dari Firebase
unsigned long relayStartTime = 0;
unsigned long lastUpdateTime = 0;
unsigned long lastI2CCheckTime = 0;
const unsigned long restartInterval = 6 * 60 * 60 * 1000; // Restart otomatis 6 jam

void setup() {
    Serial.begin(115200);
    Wire.begin(4, 5);  // Sesuaikan SDA, SCL untuk ESP8266
    mpu.initialize();

    pinMode(relayPin, OUTPUT);
    digitalWrite(relayPin, LOW);

    // Koneksi ke WiFi
    WiFi.begin(WIFI_SSID, WIFI_PASSWORD);
    Serial.print("Menghubungkan ke WiFi");
    while (WiFi.status() != WL_CONNECTED) {
        Serial.print(".");
        delay(1000);
    }
    Serial.println("\nTerhubung ke WiFi");

    // Koneksi ke Firebase
    Firebase.begin(FIREBASE_HOST, FIREBASE_AUTH);
    Firebase.reconnectWiFi(true);

    if (!mpu.testConnection()) {
        Serial.println("MPU6050 tidak terhubung!");
        while (1);
    }

    Serial.println("Kalibrasi awal...");
    calibrateSensor();
}

void loop() {
    if (millis() > restartInterval) {
        Serial.println("Restart otomatis.");
        ESP.restart();
    }

    if (millis() - lastI2CCheckTime >= 30000) {
        lastI2CCheckTime = millis();
        if (!mpu.testConnection()) {
            Serial.println("MPU6050 tidak merespons! Reset I2C...");
            Wire.begin(4, 5);  // Reset I2C dengan SDA, SCL
            mpu.initialize();
            calibrateSensor();
        }
    }

    // **Membaca status MPU dari Firebase**
    if (Firebase.getBool(firebaseData, "/mpu/status")) {
        if (firebaseData.dataType() == "boolean") {
            mpuEnabled = firebaseData.boolData();
            Serial.print("Status MPU dari Firebase: ");
            Serial.println(mpuEnabled ? "AKTIF" : "NONAKTIF");
        }
    } else {
        Serial.println("Gagal membaca data dari Firebase!");
        Serial.println(firebaseData.errorReason());
    }

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
                digitalWrite(relayPin, HIGH);
                relayActive = true;
                relayStartTime = millis();

                // **Mengirim status relay ke Firebase**
                Firebase.setBool(firebaseData, "/relay/status", true);
            }

            if (relayActive && millis() - relayStartTime >= 2000) {
                Serial.println("Relay mati, kalibrasi ulang.");
                digitalWrite(relayPin, LOW);
                relayActive = false;
                calibrateSensor();

                // **Mengirim status relay ke Firebase**
                Firebase.setBool(firebaseData, "/relay/status", false);
            }
        }
    } else {
        // Jika MPU dinonaktifkan dari Firebase, pastikan relay juga mati
        digitalWrite(relayPin, LOW);
        relayActive = false;
        Firebase.setBool(firebaseData, "/relay/status", false);
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

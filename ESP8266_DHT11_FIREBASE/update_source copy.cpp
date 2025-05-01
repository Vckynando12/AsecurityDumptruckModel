#include <Wire.h>
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

// MPU6050 I2C address
#define MPU6050_ADDR 0x68

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
unsigned long firebaseRetryCount = 0;  // Menghitung percobaan koneksi ke Firebase

// Tambahkan variabel global untuk timestamp
unsigned long lastHeartbeatTime = 0;
const unsigned long heartbeatInterval = 60000; // 60 detik = 1 menit
unsigned long lastFirebaseRetryTime = 0;
const unsigned long firebaseRetryInterval = 5000; // 5 detik antara percobaan

// Fungsi untuk membaca data dari MPU6050
void readMPU6050(int16_t* ax, int16_t* ay, int16_t* az) {
    Wire.beginTransmission(MPU6050_ADDR);
    Wire.write(0x3B);  // starting with register 0x3B (ACCEL_XOUT_H)
    Wire.endTransmission(false);
    Wire.requestFrom(MPU6050_ADDR, 6, true);  // request a total of 6 registers
    
    *ax = Wire.read() << 8 | Wire.read();  // 0x3B (ACCEL_XOUT_H) & 0x3C (ACCEL_XOUT_L)
    *ay = Wire.read() << 8 | Wire.read();  // 0x3D (ACCEL_YOUT_H) & 0x3E (ACCEL_YOUT_L)
    *az = Wire.read() << 8 | Wire.read();  // 0x3F (ACCEL_ZOUT_H) & 0x40 (ACCEL_ZOUT_L)
}

// Fungsi untuk menginisialisasi MPU6050
bool initMPU6050() {
    Wire.beginTransmission(MPU6050_ADDR);
    Wire.write(0x6B);  // PWR_MGMT_1 register
    Wire.write(0);     // set to zero (wakes up the MPU-6050)
    return Wire.endTransmission(true) == 0;
}

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

    bool firebaseConnected = false;
    for (int i = 0; i < 3; i++) {  // Coba 3 kali
        if (Firebase.setString(firebaseData, "/logs/systemESP", "Device online")) {
            firebaseConnected = true;
            Serial.println("Firebase terhubung");
            break;
        } else {
            Serial.println("Gagal menghubungkan ke Firebase, mencoba lagi...");
            delay(1000);
        }
    }

    if (!firebaseConnected) {
        Serial.println("Tidak dapat terhubung ke Firebase. Sistem keamanan dimatikan.");
        mpuEnabled = false;  // Matikan keamanan jika Firebase tidak terhubung
    }

    // Inisialisasi sensor
    if (!initMPU6050()) {
        Serial.println("MPU6050 tidak terhubung!");
        if (firebaseConnected) {
            Firebase.setString(firebaseData, "/logs/mpu/status", "disconnected");
        }
        mpuEnabled = false;
    } else {
        if (firebaseConnected) {
            Firebase.setString(firebaseData, "/logs/mpu/status", "connected");
        }
        calibrateSensor();
    }

    dht.begin();

    // Cek DHT11
    float testReading = dht.readTemperature();
    if (isnan(testReading)) {
        Serial.println("DHT11 tidak terhubung atau error!");
        if (firebaseConnected) {
            Firebase.setString(firebaseData, "/logs/dht/status", "disconnected");
        }
    } else {
        if (firebaseConnected) {
            Firebase.setString(firebaseData, "/logs/dht/status", "connected");
        }
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
    // Cek koneksi WiFi
    if (WiFi.status() != WL_CONNECTED) {
        Serial.println("WiFi terputus, mencoba menyambung kembali...");
        WiFi.begin(WIFI_SSID, WIFI_PASSWORD);
        delay(5000);  // Tunggu 5 detik
        
        // Jika masih tidak terhubung, matikan keamanan
        if (WiFi.status() != WL_CONNECTED) {
            mpuEnabled = false;
            digitalWrite(relayMPUPin, HIGH);  // Pastikan relay OFF
        }
        return;  // Keluar dari loop dan coba lagi
    }

    // Update lastActive dengan unix timestamp
    if (millis() - lastHeartbeatTime >= heartbeatInterval) {
        lastHeartbeatTime = millis();
        unsigned long epochTime = time(nullptr);
        bool heartbeatSent = false;
        
        if (Firebase.setInt(firebaseData, "/device/lastActive", epochTime)) {
            Serial.println("Heartbeat sent: " + String(epochTime));
            // Tambahkan update status Device online saat heartbeat berhasil
            Firebase.setString(firebaseData, "/logs/systemESP", "Device online");
            Serial.println("Device status updated: Online");
            heartbeatSent = true;
            firebaseRetryCount = 0;  // Reset counter jika berhasil
        } else {
            Serial.println("Failed to send heartbeat: " + firebaseData.errorReason());
            // Jika gagal mengirim heartbeat, set status offline
            firebaseRetryCount++;
            
            if (firebaseRetryCount >= 3) {  // Setelah 3 kali gagal
                Serial.println("Firebase tidak dapat diakses setelah beberapa percobaan, mematikan sistem keamanan");
                mpuEnabled = false;  // Matikan keamanan
                digitalWrite(relayMPUPin, HIGH);  // Pastikan relay OFF
                Firebase.setString(firebaseData, "/security/status", "off");  // Coba set status off
                Firebase.setString(firebaseData, "/logs/systemESP", "Device offline");
            }
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
    } else {
        Serial.println("Gagal membaca status restart: " + firebaseData.errorReason());
        // Jika gagal membaca dari Firebase, tambahkan counter
        firebaseRetryCount++;
        
        if (firebaseRetryCount >= 3) {  // Setelah 3 kali gagal
            Serial.println("Tidak dapat mengakses Firebase, mematikan sistem keamanan");
            mpuEnabled = false;  // Matikan keamanan
            digitalWrite(relayMPUPin, HIGH);  // Pastikan relay OFF
            // Masih coba set status meski kemungkinan gagal
            Firebase.setString(firebaseData, "/security/status", "off");
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
        if (!initMPU6050()) {
            Serial.println("MPU6050 tidak merespons! Reset I2C...");
            Firebase.setString(firebaseData, "/logs/mpu/status", "error");

            Wire.begin(D2, D1);
            if (initMPU6050()) {
                Firebase.setString(firebaseData, "/logs/mpu/status", "connected");
            }
        }
    }

    // **Baca status MPU dari Firebase**
    bool readSuccess = false;
    if (Firebase.getString(firebaseData, "/security/status")) {
        String mpuStatus = firebaseData.stringData();
        Serial.print("Status MPU dari Firebase: ");
        Serial.println(mpuStatus);
        mpuEnabled = (mpuStatus == "on");
        readSuccess = true;
        firebaseRetryCount = 0;  // Reset counter jika berhasil
    } else {
        Serial.println("Gagal membaca data dari Firebase: " + firebaseData.errorReason());
        Firebase.setString(firebaseData, "/logs/error", firebaseData.errorReason());
        
        // Jika gagal memperoleh data dari Firebase, tambahkan counter
        firebaseRetryCount++;
        
        if (firebaseRetryCount >= 3) {  // Setelah 3 kali gagal
            // Cek jika waktu cukup berlalu untuk percobaan berikutnya
            if (millis() - lastFirebaseRetryTime >= firebaseRetryInterval) {
                lastFirebaseRetryTime = millis();
                
                Serial.println("Tidak dapat membaca status dari Firebase, mematikan sistem keamanan");
                mpuEnabled = false;  // Matikan keamanan
                digitalWrite(relayMPUPin, HIGH);  // Pastikan relay OFF
                
                // Masih coba set status meski kemungkinan gagal
                Firebase.setString(firebaseData, "/security/status", "off");
            }
        }
    }

    // **Proses MPU6050**
    if (mpuEnabled) {
        if (millis() - lastUpdateTime >= 10) {
            lastUpdateTime = millis();

            int16_t ax, ay, az;
            readMPU6050(&ax, &ay, &az);

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
    readMPU6050(&ax, &ay, &az);
    baseAccelX = ax / 16384.0;
    baseAccelY = ay / 16384.0;
    baseAccelZ = az / 16384.0;
    Serial.println("Kalibrasi selesai!");
}
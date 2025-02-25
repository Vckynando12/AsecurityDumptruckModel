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


v2
#include <ESP8266WiFi.h>
#include <FirebaseESP8266.h>
#include <SPI.h>
#include <MFRC522.h>
#include <Servo.h>

// WiFi Credentials
#define WIFI_SSID "KONTRAKAN OYI"
#define WIFI_PASSWORD "warkopoyi"

// Firebase Configuration
#define FIREBASE_HOST "smartcab-8bb42-default-rtdb.firebaseio.com"
#define FIREBASE_AUTH "kiiQoFa6Ckp7bL2oRLbaTSGQth9z0PgN64Ybv8dw"

FirebaseConfig config;
FirebaseAuth auth;
FirebaseData firebaseData;

// RFID Configuration
#define SS_PIN D4  // Pin SDA RFID
#define RST_PIN D3 // Pin RST RFID
MFRC522 mfrc522(SS_PIN, RST_PIN);

// Servo Configuration
Servo myServo;
#define SERVO_PIN D2  // Pin servo

// **ID kartu yang diizinkan**
String kartuTerdaftar = "53ed8434";
bool servoTerbuka = false;  // Status awal servo (tertutup)

void setup() {
  Serial.begin(115200);
  
  // Koneksi ke WiFi
  WiFi.begin(WIFI_SSID, WIFI_PASSWORD);
  Serial.print("Menghubungkan ke WiFi");
  while (WiFi.status() != WL_CONNECTED) {
    Serial.print(".");
    delay(500);
  }
  Serial.println("\nWiFi Terhubung!");

  // Konfigurasi Firebase
  config.host = FIREBASE_HOST;
  config.signer.tokens.legacy_token = FIREBASE_AUTH;
  Firebase.begin(&config, &auth);
  Firebase.reconnectWiFi(true);

  // Inisialisasi RFID
  SPI.begin();
  mfrc522.PCD_Init();
  
  // Inisialisasi Servo
  myServo.attach(SERVO_PIN);
  myServo.write(0); // Posisi awal servo terkunci
}

void loop() {
  // Cek apakah kartu RFID terdeteksi
  if (mfrc522.PICC_IsNewCardPresent() && mfrc522.PICC_ReadCardSerial()) {
    Serial.println("Kartu Terdeteksi!");

    // Membaca UID kartu
    String rfidUID = "";
    for (byte i = 0; i < mfrc522.uid.size; i++) {
      rfidUID += String(mfrc522.uid.uidByte[i], HEX);
    }

    Serial.print("UID: ");
    Serial.println(rfidUID);

    // **Cek apakah kartu terdaftar**
    if (rfidUID == kartuTerdaftar) {
      Serial.println("Kartu Terdaftar!");

      if (!servoTerbuka) {
        Serial.println("Membuka kunci...");
        myServo.write(90);
        servoTerbuka = true;
        Firebase.setString(firebaseData, "/smartcab/servo_status", "Terbuka");
      } else {
        Serial.println("Mengunci kunci...");
        myServo.write(0);
        servoTerbuka = false;
        Firebase.setString(firebaseData, "/smartcab/servo_status", "Terkunci");
      }

      // Kirim status ke Firebase
      Firebase.setString(firebaseData, "/smartcab/last_access", "Terdaftar");
      Firebase.setString(firebaseData, "/smartcab/status_device", rfidUID);
    } 
    else {
      Serial.println("Kartu Tidak Terdaftar! Mengunci servo...");
      
      // Paksa servo terkunci jika kartu tidak dikenal
      myServo.write(0);
      servoTerbuka = false;
      Firebase.setString(firebaseData, "/smartcab/servo_status", "Terkunci");
      Firebase.setString(firebaseData, "/smartcab/last_access", "Tidak Terdaftar");
      Firebase.setString(firebaseData, "/smartcab/status_device", rfidUID);
    }

    mfrc522.PICC_HaltA(); // Hentikan komunikasi RFID
    mfrc522.PCD_StopCrypto1();
  }

  delay(500);
}


v3
#include <ESP8266WiFi.h>
#include <FirebaseESP8266.h>
#include <SPI.h>
#include <MFRC522.h>
#include <Servo.h>

// WiFi Credentials
#define WIFI_SSID "KONTRAKAN OYI"
#define WIFI_PASSWORD "warkopoyi"

// Firebase Configuration
#define FIREBASE_HOST "smartcab-8bb42-default-rtdb.firebaseio.com"
#define FIREBASE_AUTH "kiiQoFa6Ckp7bL2oRLbaTSGQth9z0PgN64Ybv8dw"

FirebaseConfig config;
FirebaseAuth auth;
FirebaseData firebaseData;

// RFID Configuration
#define SS_PIN D4  // Pin SDA RFID
#define RST_PIN D3 // Pin RST RFID
MFRC522 mfrc522(SS_PIN, RST_PIN);

// Servo Configuration
Servo myServo;
#define SERVO_PIN D2  // Pin servo

// **ID kartu yang diizinkan**
String kartuTerdaftar = "53ed8434";
bool servoTerbuka = false;  // Status awal servo (tertutup)

void setup() {
  Serial.begin(115200);
  
  // Koneksi ke WiFi
  WiFi.begin(WIFI_SSID, WIFI_PASSWORD);
  Serial.print("Menghubungkan ke WiFi");
  while (WiFi.status() != WL_CONNECTED) {
    Serial.print(".");
    delay(500);
  }
  Serial.println("\nWiFi Terhubung!");

  // Konfigurasi Firebase
  config.host = FIREBASE_HOST;
  config.signer.tokens.legacy_token = FIREBASE_AUTH;
  Firebase.begin(&config, &auth);
  Firebase.reconnectWiFi(true);

  // Inisialisasi RFID
  SPI.begin();
  mfrc522.PCD_Init();
  
  // Inisialisasi Servo
   myServo.attach(SERVO_PIN, 500, 2500);  // Min pulse width = 500µs, Max pulse width = 2500µs
   myServo.write(0);
}

void loop() {
  // Cek apakah kartu RFID terdeteksi
  if (mfrc522.PICC_IsNewCardPresent() && mfrc522.PICC_ReadCardSerial()) {
    Serial.println("Kartu Terdeteksi!");

    // Membaca UID kartu
    String rfidUID = "";
    for (byte i = 0; i < mfrc522.uid.size; i++) {
      rfidUID += String(mfrc522.uid.uidByte[i], HEX);
    }

    Serial.print("UID: ");
    Serial.println(rfidUID);

    // **Cek apakah kartu terdaftar**
    if (rfidUID == kartuTerdaftar) {
      Serial.println("Kartu Terdaftar!");

      if (!servoTerbuka) {
        Serial.println("Membuka kunci...");
        myServo.write(180);
        servoTerbuka = true;
        Firebase.setString(firebaseData, "/smartcab/servo_status", "Terbuka");
      } else {
        Serial.println("Mengunci kunci...");
        myServo.write(0);
        servoTerbuka = false;
        Firebase.setString(firebaseData, "/smartcab/servo_status", "Terkunci");
      }

      // Kirim status ke Firebase
      Firebase.setString(firebaseData, "/smartcab/last_access", "Terdaftar");
      Firebase.setString(firebaseData, "/smartcab/status_device", rfidUID);
    } 
    else {
      Serial.println("Kartu Tidak Terdaftar! Mengunci servo...");
      
      // Paksa servo terkunci jika kartu tidak dikenal
      myServo.write(0);
      servoTerbuka = false;
      Firebase.setString(firebaseData, "/smartcab/servo_status", "Terkunci");
      Firebase.setString(firebaseData, "/smartcab/last_access", "Tidak Terdaftar");
      Firebase.setString(firebaseData, "/smartcab/status_device", rfidUID);
    }

    mfrc522.PICC_HaltA(); // Hentikan komunikasi RFID
    mfrc522.PCD_StopCrypto1();
  }

  delay(500);
}

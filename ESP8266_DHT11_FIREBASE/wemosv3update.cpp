#include <ESP8266WiFi.h>
#include <FirebaseESP8266.h>
#include <SPI.h>
#include <MFRC522.h>
#include <Servo.h>
#include <time.h>

// WiFi Credentials
#define WIFI_SSID "smartcab"
#define WIFI_PASSWORD "123123123"

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
String kartuTerdaftar[] = {"53ed8434", "3b4f7d9e", "5b85e19d", "cb31e19f", "5b58179f", "1b22e9f"};
bool servoTerbuka = false;  // Status awal servo (tertutup)

// Tambahkan variabel global untuk last active
unsigned long previousMillis = 0;
const long interval = 60000; // interval 1 menit dalam milliseconds

// Tambahkan di bagian global variables
unsigned long lastHeartbeatTime = 0;
const long heartbeatInterval = 60000; // 1 menit dalam milliseconds

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
  
  // Cek koneksi RFID dengan cara yang lebih aman
  byte v = mfrc522.PCD_ReadRegister(mfrc522.VersionReg);
  if (v == 0x00 || v == 0xFF) {
    Firebase.setString(firebaseData, "/logs/RFID/status", "Disconnected");
    Serial.println("RFID Connection Failed!");
  } else {
    Firebase.setString(firebaseData, "/logs/RFID/status", "Connected");
    Serial.println("RFID Connected!");
  }
  
  // Inisialisasi Servo
  myServo.attach(SERVO_PIN, 500, 2500);
  if (myServo.attached()) {
    Firebase.setString(firebaseData, "/logs/servo/status", "Connected");
    Serial.println("Servo Connected!");
  } else {
    Firebase.setString(firebaseData, "/logs/servo/status", "Disconnected");
    Serial.println("Servo Connection Failed!");
  }
  myServo.write(0);

  // Update path untuk restart control
  Firebase.setBool(firebaseData, "/control/restartWemos", false);
  
  // Konfigurasi NTP
  configTime(7 * 3600, 0, "pool.ntp.org"); // GMT+7
  
  // Kirim timestamp pertama kali
  unsigned long epochTime = time(nullptr);
  Firebase.setInt(firebaseData, "/device/lastActiveWemos", epochTime);
  
  // Update status device saat startup
  Firebase.setString(firebaseData, "/logs/systemWemos", "Device Online");
}

void loop() {
  // Update path untuk pengecekan restart
  if (Firebase.getBool(firebaseData, "/control/restartWemos")) {
    if (firebaseData.boolData() == true) {
      // Update status sebelum restart
      Firebase.setString(firebaseData, "/logs/systemWemos", "Device auto-restarting...");
      Firebase.setBool(firebaseData, "/control/restartWemos", false);
      delay(1000);
      ESP.restart();
    }
  }

  // Cek status RFID secara periodik dengan cara yang lebih aman
  byte v = mfrc522.PCD_ReadRegister(mfrc522.VersionReg);
  if (v == 0x00 || v == 0xFF) {
    if (Firebase.getString(firebaseData, "/logs/RFID/status") && 
        firebaseData.stringData() != "Disconnected") {
      Firebase.setString(firebaseData, "/logs/RFID/status", "Disconnected");
      Serial.println("RFID Disconnected!");
    }
  } else {
    if (Firebase.getString(firebaseData, "/logs/RFID/status") && 
        firebaseData.stringData() != "Connected") {
      Firebase.setString(firebaseData, "/logs/RFID/status", "Connected");
      Serial.println("RFID Connected!");
    }
  }

  // Cek status Servo
  if (!myServo.attached()) {
    Firebase.setString(firebaseData, "/logs/servo/status", "Disconnected");
    Serial.println("Servo Disconnected!");
  }

  // Update lastActive dengan unix timestamp
  if (millis() - lastHeartbeatTime >= heartbeatInterval) {
    lastHeartbeatTime = millis();
    unsigned long epochTime = time(nullptr);
    if (Firebase.setInt(firebaseData, "/device/lastActiveWemos", epochTime)) {
      Serial.println("Heartbeat sent: " + String(epochTime));
      // Tambahkan update status Device Online saat heartbeat berhasil
      Firebase.setString(firebaseData, "/logs/systemWemos", "Device Online");
      Serial.println("Device status updated: Online");
    } else {
      Serial.println("Failed to send heartbeat");
      // Jika gagal mengirim heartbeat, set status offline
      Firebase.setString(firebaseData, "/logs/systemWemos", "Device Offline");
    }
  }

  // Cek jika tidak ada perubahan dalam 70 detik
  if (millis() - lastHeartbeatTime > 70000) {
    Firebase.setString(firebaseData, "/logs/systemWemos", "Device Offline");
  }

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
    bool isCardRegistered = false;
    for (int i = 0; i < sizeof(kartuTerdaftar)/sizeof(kartuTerdaftar[0]); i++) {
      if (rfidUID == kartuTerdaftar[i]) {
        isCardRegistered = true;
        break;
      }
    }
    
    if (isCardRegistered) {
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

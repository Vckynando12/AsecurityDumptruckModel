#include <ESP8266WiFi.h>
#include <FirebaseESP8266.h>
#include <SPI.h>
#include <MFRC522.h>
#include <Servo.h>
#include <EEPROM.h>

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

// EEPROM Configuration
#define EEPROM_SIZE 512
#define MAX_CARDS 20
#define CARD_SIZE 8 // 8 bytes per card (4 byte UID in hex = 8 chars)
#define EEPROM_SIGNATURE 0xAA // Signature byte to check if EEPROM is initialized

// Status variables
bool servoTerbuka = false;  // Status awal servo (tertutup)
unsigned long previousMillis = 0;
const long interval = 60000; // interval 1 menit dalam milliseconds
unsigned long lastHeartbeatTime = 0;
const long heartbeatInterval = 60000; // 1 menit dalam milliseconds
unsigned long lastCommandCheck = 0;
const long commandCheckInterval = 1000; // 1 detik interval untuk cek command
unsigned long bootTime = 0; // Waktu saat device booting
unsigned long lastWiFiCheck = 0; // Waktu terakhir cek WiFi
const long wifiCheckInterval = 5000; // Interval cek koneksi WiFi (5 detik)

// Flag untuk status koneksi
bool isWiFiConnected = false;
bool isFirebaseConnected = false;

// Flag untuk indikasi ada command baru dari Firebase
bool pendingCommand = false;
String currentCommand = "";
String currentCardId = "";

void setup() {
  Serial.begin(115200);
  Serial.println("\n=== Smart Cabinet System Starting ===");
  
  // Catat waktu boot untuk referensi waktu lokal
  bootTime = millis();
  
  // Initialize EEPROM
  EEPROM.begin(EEPROM_SIZE);
  initializeEEPROM();
  
  // Mulai koneksi WiFi (non-blocking)
  WiFi.begin(WIFI_SSID, WIFI_PASSWORD);
  Serial.println("Memulai koneksi WiFi di background...");
  
  // Inisialisasi RFID
  SPI.begin();
  mfrc522.PCD_Init();
  
  // Cek koneksi RFID
  byte v = mfrc522.PCD_ReadRegister(mfrc522.VersionReg);
  if (v == 0x00 || v == 0xFF) {
    Serial.println("RFID Connection Failed!");
  } else {
    Serial.println("RFID Connected!");
  }
  
  // Inisialisasi Servo
  myServo.attach(SERVO_PIN, 500, 2500);
  if (myServo.attached()) {
    Serial.println("Servo Connected!");
  } else {
    Serial.println("Servo Connection Failed!");
  }
  myServo.write(0);
  
  Serial.println("Setup selesai. Sistem berjalan...");
}

void loop() {
  // Cek dan update status koneksi WiFi
  if (millis() - lastWiFiCheck >= wifiCheckInterval) {
    lastWiFiCheck = millis();
    checkWiFiConnection();
  }
  
  // Cek apakah kartu RFID terdeteksi - selalu berjalan terlepas dari WiFi
  checkRFID();
  
  // Fungsi-fungsi yang membutuhkan koneksi WiFi
  if (isWiFiConnected) {
    // Cek perintah restart
    checkRestartCommand();
    
    // Cek perintah dari Firebase dengan interval untuk menghindari blocking
    if (millis() - lastCommandCheck >= commandCheckInterval) {
      lastCommandCheck = millis();
      checkFirebaseCommands();
    }
    
    // Update status perangkat secara berkala
    updateDeviceStatus();
    
    // Heartbeat ke Firebase
    sendHeartbeat();
  }
  
  delay(100); // Kurangi delay untuk respon lebih cepat
}

// Fungsi untuk mengecek dan memperbarui status koneksi WiFi
void checkWiFiConnection() {
  if (WiFi.status() == WL_CONNECTED) {
    if (!isWiFiConnected) {
      isWiFiConnected = true;
      Serial.println("WiFi terhubung!");
      Serial.print("IP Address: ");
      Serial.println(WiFi.localIP());
      
      // Setup Firebase saat WiFi baru terhubung
      setupFirebase();
    }
  } else {
    if (isWiFiConnected) {
      isWiFiConnected = false;
      isFirebaseConnected = false;
      Serial.println("WiFi terputus!");
    }
  }
}

// Setup Firebase setelah WiFi terhubung
void setupFirebase() {
  config.host = FIREBASE_HOST;
  config.signer.tokens.legacy_token = FIREBASE_AUTH;
  Firebase.begin(&config, &auth);
  Firebase.reconnectWiFi(true);
  
  // Cek koneksi Firebase
  if (Firebase.ready()) {
    isFirebaseConnected = true;
    Serial.println("Firebase terhubung!");
    
    // Kirim timestamp lokal ke Firebase
    unsigned long localTime = millis();
    if (Firebase.setInt(firebaseData, "/device/lastActiveWemos", localTime)) {
      Serial.println("Initial timestamp sent: " + String(localTime));
    } else {
      Serial.println("Failed to send initial timestamp");
    }
    
    // Update status device saat terhubung
    Firebase.setString(firebaseData, "/logs/systemWemos", "Device Online");
    
    // Setup struktur data kartu di Firebase
    Firebase.setString(firebaseData, "/registered_cards/card", "");
    Firebase.setString(firebaseData, "/registered_cards/delete", "");
    Firebase.setString(firebaseData, "/registered_cards/status", "System connected");
    
    // Update status peripheral
    updatePeripheralStatus();
    
    // Upload daftar kartu ke Firebase
    uploadCardListToFirebase();
  } else {
    Serial.println("Gagal terhubung ke Firebase!");
  }
}

// Fungsi untuk update status peripheral
void updatePeripheralStatus() {
  // Cek status RFID
  byte v = mfrc522.PCD_ReadRegister(mfrc522.VersionReg);
  if (v == 0x00 || v == 0xFF) {
    Firebase.setString(firebaseData, "/logs/RFID/status", "Disconnected");
  } else {
    Firebase.setString(firebaseData, "/logs/RFID/status", "Connected");
  }
  
  // Cek status Servo
  if (myServo.attached()) {
    Firebase.setString(firebaseData, "/logs/servo/status", "Connected");
  } else {
    Firebase.setString(firebaseData, "/logs/servo/status", "Disconnected");
  }
}

// Fungsi untuk cek perintah restart
void checkRestartCommand() {
  if (Firebase.getBool(firebaseData, "/control/restartWemos")) {
    if (firebaseData.boolData() == true) {
      // Update status sebelum restart
      Firebase.setString(firebaseData, "/logs/systemWemos", "Device auto-restarting...");
      Firebase.setBool(firebaseData, "/control/restartWemos", false);
      delay(1000);
      ESP.restart();
    }
  }
}

// Fungsi untuk cek RFID
void checkRFID() {
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

    // Cek apakah kartu terdaftar dalam EEPROM
    bool isCardRegistered = isCardInEEPROM(rfidUID);
    
    if (isCardRegistered) {
      Serial.println("Kartu Terdaftar!");

      if (!servoTerbuka) {
        Serial.println("Membuka kunci...");
        myServo.write(180);
        servoTerbuka = true;
        
        // Update Firebase hanya jika terhubung
        if (isWiFiConnected && isFirebaseConnected) {
          Firebase.setString(firebaseData, "/smartcab/servo_status", "Terbuka");
        }
      } else {
        Serial.println("Mengunci kunci...");
        myServo.write(0);
        servoTerbuka = false;
        
        // Update Firebase hanya jika terhubung
        if (isWiFiConnected && isFirebaseConnected) {
          Firebase.setString(firebaseData, "/smartcab/servo_status", "Terkunci");
        }
      }

      // Kirim status ke Firebase jika terhubung
      if (isWiFiConnected && isFirebaseConnected) {
        Firebase.setString(firebaseData, "/smartcab/last_access", "Terdaftar");
        Firebase.setString(firebaseData, "/smartcab/status_device", rfidUID);
      }
    } 
    else {
      Serial.println("Kartu Tidak Terdaftar! Mengunci servo...");
      
      // Paksa servo terkunci jika kartu tidak dikenal
      myServo.write(0);
      servoTerbuka = false;
      
      // Update Firebase hanya jika terhubung
      if (isWiFiConnected && isFirebaseConnected) {
        Firebase.setString(firebaseData, "/smartcab/servo_status", "Terkunci");
        Firebase.setString(firebaseData, "/smartcab/last_access", "Tidak Terdaftar");
        Firebase.setString(firebaseData, "/smartcab/status_device", rfidUID);
      }
    }

    mfrc522.PICC_HaltA(); // Hentikan komunikasi RFID
    mfrc522.PCD_StopCrypto1();
  }
}

// Update status perangkat
void updateDeviceStatus() {
  // Cek status RFID secara periodik
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
}

// Kirim heartbeat ke Firebase
void sendHeartbeat() {
  // Update lastActive dengan timestamp lokal
  if (millis() - lastHeartbeatTime >= heartbeatInterval) {
    lastHeartbeatTime = millis();
    unsigned long localTime = millis();
    if (Firebase.setInt(firebaseData, "/device/lastActiveWemos", localTime)) {
      Serial.println("Heartbeat sent: " + String(localTime));
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
}

// Fungsi untuk inisialisasi EEPROM jika belum diinisialisasi
void initializeEEPROM() {
  // Cek signature byte
  if (EEPROM.read(0) != EEPROM_SIGNATURE) {
    Serial.println("Initializing EEPROM for first use...");
    // Set signature
    EEPROM.write(0, EEPROM_SIGNATURE);
    // Clear card slots
    for (int i = 1; i < EEPROM_SIZE; i++) {
      EEPROM.write(i, 0xFF);
    }
    EEPROM.commit();
    Serial.println("EEPROM initialized");
  }
}

// Fungsi untuk cek apakah kartu terdaftar di EEPROM
bool isCardInEEPROM(String cardId) {
  int startAddr = 1; // Skip signature byte
  
  for (int i = 0; i < MAX_CARDS; i++) {
    int addr = startAddr + (i * CARD_SIZE);
    if (EEPROM.read(addr) != 0xFF) { // Jika slot tidak kosong
      char storedCard[CARD_SIZE+1];
      for (int j = 0; j < CARD_SIZE; j++) {
        storedCard[j] = char(EEPROM.read(addr + j));
      }
      storedCard[CARD_SIZE] = '\0';
      
      if (cardId == String(storedCard)) {
        return true;
      }
    }
  }
  return false;
}

// Fungsi untuk menambahkan kartu ke EEPROM
bool addCardToEEPROM(String cardId) {
  if (cardId.length() != CARD_SIZE) {
    Serial.println("Card ID harus " + String(CARD_SIZE) + " karakter!");
    return false;
  }
  
  // Cek apakah kartu sudah terdaftar
  if (isCardInEEPROM(cardId)) {
    Serial.println("Kartu sudah terdaftar!");
    return false;
  }
  
  int startAddr = 1; // Skip signature byte
  bool cardAdded = false;
  
  // Cari slot kosong
  for (int i = 0; i < MAX_CARDS; i++) {
    int addr = startAddr + (i * CARD_SIZE);
    if (EEPROM.read(addr) == 0xFF) { // Slot kosong
      // Simpan card ID
      for (int j = 0; j < CARD_SIZE; j++) {
        EEPROM.write(addr + j, cardId[j]);
      }
      EEPROM.commit();
      cardAdded = true;
      Serial.println("Kartu berhasil ditambahkan!");
      break;
    }
  }
  
  if (!cardAdded) {
    Serial.println("EEPROM penuh! Hapus beberapa kartu terlebih dahulu.");
    return false;
  }
  
  return true;
}

// Fungsi untuk menghapus kartu dari EEPROM
bool removeCardFromEEPROM(String cardId) {
  int startAddr = 1; // Skip signature byte
  bool cardRemoved = false;
  
  for (int i = 0; i < MAX_CARDS; i++) {
    int addr = startAddr + (i * CARD_SIZE);
    if (EEPROM.read(addr) != 0xFF) { // Jika slot tidak kosong
      char storedCard[CARD_SIZE+1];
      for (int j = 0; j < CARD_SIZE; j++) {
        storedCard[j] = char(EEPROM.read(addr + j));
      }
      storedCard[CARD_SIZE] = '\0';
      
      if (cardId == String(storedCard)) {
        // Hapus card dengan mengisi 0xFF
        for (int j = 0; j < CARD_SIZE; j++) {
          EEPROM.write(addr + j, 0xFF);
        }
        EEPROM.commit();
        cardRemoved = true;
        Serial.println("Kartu berhasil dihapus!");
        break;
      }
    }
  }
  
  if (!cardRemoved) {
    Serial.println("Kartu tidak ditemukan!");
    return false;
  }
  
  return true;
}

// Fungsi untuk menghapus semua kartu dari EEPROM
void clearAllCards() {
  int startAddr = 1; // Skip signature byte
  
  for (int i = 0; i < MAX_CARDS * CARD_SIZE; i++) {
    EEPROM.write(startAddr + i, 0xFF);
  }
  EEPROM.commit();
  Serial.println("Semua kartu berhasil dihapus!");
}

// Fungsi untuk mendapatkan jumlah kartu yang tersimpan di EEPROM
int getCardCount() {
  int startAddr = 1; // Skip signature byte
  int count = 0;
  
  for (int i = 0; i < MAX_CARDS; i++) {
    int addr = startAddr + (i * CARD_SIZE);
    if (EEPROM.read(addr) != 0xFF) { // Jika slot tidak kosong
      count++;
    }
  }
  
  return count;
}

// Fungsi untuk upload daftar kartu ke Firebase
void uploadCardListToFirebase() {
  int startAddr = 1; // Skip signature byte
  int count = 0;
  
  // Clear existing list
  Firebase.deleteNode(firebaseData, "/registered_cards/list");
  
  // Add cards to list
  for (int i = 0; i < MAX_CARDS; i++) {
    int addr = startAddr + (i * CARD_SIZE);
    if (EEPROM.read(addr) != 0xFF) { // Jika slot tidak kosong
      char storedCard[CARD_SIZE+1];
      for (int j = 0; j < CARD_SIZE; j++) {
        storedCard[j] = char(EEPROM.read(addr + j));
      }
      storedCard[CARD_SIZE] = '\0';
      
      String cardKey = "card" + String(count + 1);
      Firebase.setString(firebaseData, "/registered_cards/list/" + cardKey, String(storedCard));
      count++;
    }
  }
  
  // Update total count
  Firebase.setInt(firebaseData, "/registered_cards/total", count);
  
  Serial.println("Daftar kartu berhasil diupload ke Firebase!");
}

// Fungsi untuk memeriksa perintah dari Firebase
void checkFirebaseCommands() {
  // Check for new card to add
  if (Firebase.getString(firebaseData, "/registered_cards/card")) {
    String cardId = firebaseData.stringData();
    
    if (cardId != "") {
      Serial.println("Perintah baru: tambah kartu " + cardId);
      
      if (addCardToEEPROM(cardId)) {
        Firebase.setString(firebaseData, "/registered_cards/status", "Kartu berhasil ditambahkan");
        uploadCardListToFirebase();
      } else {
        Firebase.setString(firebaseData, "/registered_cards/status", "Gagal menambahkan kartu");
      }
      
      // Reset command
      Firebase.setString(firebaseData, "/registered_cards/card", "");
    }
  }
  
  // Check for card to delete
  if (Firebase.getString(firebaseData, "/registered_cards/delete")) {
    String cardId = firebaseData.stringData();
    
    if (cardId != "") {
      Serial.println("Perintah baru: hapus kartu " + cardId);
      
      if (cardId == "all") {
        clearAllCards();
        Firebase.setString(firebaseData, "/registered_cards/status", "Semua kartu berhasil dihapus");
        uploadCardListToFirebase();
      } else {
        if (removeCardFromEEPROM(cardId)) {
          Firebase.setString(firebaseData, "/registered_cards/status", "Kartu berhasil dihapus");
          uploadCardListToFirebase();
        } else {
          Firebase.setString(firebaseData, "/registered_cards/status", "Kartu tidak ditemukan");
        }
      }
      
      // Reset command
      Firebase.setString(firebaseData, "/registered_cards/delete", "");
    }
  }
}

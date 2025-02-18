      #include <ESP8266WiFi.h>
#include <FirebaseESP8266.h>
#include <DHT.h>

// Koneksi WiFi
#define WIFI_SSID "KONTRAKAN OYI"
#define WIFI_PASSWORD "warkopoyi"

// Koneksi Firebase
#define FIREBASE_HOST "smartcab-8bb42-default-rtdb.firebaseio.com"
#define FIREBASE_AUTH "kiiQoFa6Ckp7bL2oRLbaTSGQth9z0PgN64Ybv8dw"

// Pin Konfigurasi
#define PIR_PIN D5        // Sensor PIR
#define RELAY_PIN D6      // Relay
#define DHTPIN D4         // Sensor DHT11
#define DHTTYPE DHT11

DHT dht(DHTPIN, DHTTYPE);
FirebaseData firebaseData;
FirebaseConfig config;
FirebaseAuth auth;

// Status Security System dari Firebase
bool securitySystem = false;

void setup() {
    Serial.begin(115200);
    delay(1000);

    // Koneksi ke WiFi
    WiFi.begin(WIFI_SSID, WIFI_PASSWORD);
    Serial.print("Connecting to Wi-Fi");
    while (WiFi.status() != WL_CONNECTED) {
        Serial.print(".");
        delay(500);
    }
    Serial.println("\nConnected to Wi-Fi");

    // Konfigurasi Firebase
    config.host = FIREBASE_HOST;
    config.signer.tokens.legacy_token = FIREBASE_AUTH;
    Firebase.begin(&config, &auth);
    Firebase.reconnectWiFi(true);

    // Inisialisasi sensor & relay
    pinMode(PIR_PIN, INPUT);
    pinMode(RELAY_PIN, OUTPUT);
    digitalWrite(RELAY_PIN, LOW);

    dht.begin();
}

void loop() {
    // 🔹 1. Ambil status Security System dari Firebase
    if (Firebase.getString(firebaseData, "/security/status")) {
        String status = firebaseData.stringData();
        securitySystem = (status == "on");  // Jika "on", aktifkan PIR
    } else {
        Serial.print("Failed to get security status: ");
        Serial.println(firebaseData.errorReason());
    }

    // 🔹 2. Cek PIR Sensor jika security system aktif
    if (securitySystem) {
        int motionDetected = digitalRead(PIR_PIN);
        if (motionDetected) {
            digitalWrite(RELAY_PIN, HIGH);
            Serial.println("Gerakan Terdeteksi! Relay Nyala");

            // Kirim status ke Firebase
            Firebase.setString(firebaseData, "/security/motion", "detected");
        } else {
            digitalWrite(RELAY_PIN, LOW);
            Firebase.setString(firebaseData, "/security/motion", "clear");
        }
    } else {
        digitalWrite(RELAY_PIN, LOW);
        Firebase.setString(firebaseData, "/security/motion", "disabled");
    }

    // 🔹 3. Baca dan kirim data suhu & kelembaban ke Firebase
    float temperature = dht.readTemperature();
    float humidity = dht.readHumidity();

    if (!isnan(temperature) && !isnan(humidity)) {
        Serial.print("Temperature: ");
        Serial.print(temperature);
        Serial.print(" °C, Humidity: ");
        Serial.print(humidity);
        Serial.println(" %");

        Firebase.setFloat(firebaseData, "/dht11/temperature", temperature);
        Firebase.setFloat(firebaseData, "/dht11/humidity", humidity);
    } else {
        Serial.println("Failed to read from DHT sensor!");
    }

    delay(2000);  // Update setiap 2 detik
}

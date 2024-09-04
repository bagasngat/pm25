#include <WiFi.h>
#include <WiFiClientSecure.h>
#include <HTTPClient.h>
#include <Wire.h>

const char* ssid = "OPPO A15";
const char* password = "987654321";

const char* serverName = "http://192.168.43.10/pm25/save.php";  //ubah menjadi IP komputer yang sedang run xampp
String apiKeyValue = "tPmAT5Ab3j7F9";

#define measurePin 34  // Connect dust sensor analog out
#define ledPower 19    // Connect dust sensor LED driver
#define PIN_FAN 4      // PWM fan speed
int samplingTime = 280;
int deltaTime = 40;
int sleepTime = 9680;
float voMeasured, calcVoltage, dustDensity, fanSpeed;

void setup() {
  Serial.begin(115200);

  pinMode(ledPower, OUTPUT);
  pinMode(PIN_FAN, OUTPUT);
  analogWrite(PIN_FAN, 0);

  WiFi.begin(ssid, password);
  Serial.println("Connecting");
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("");
  Serial.print("Connected to WiFi network with IP Address: ");
  Serial.println(WiFi.localIP());
}

void loop() {
  digitalWrite(ledPower, LOW);  // Turn on the LED
  delayMicroseconds(samplingTime);
  voMeasured = analogRead(measurePin);  // Read dust value
  delayMicroseconds(deltaTime);
  digitalWrite(ledPower, HIGH);  // Turn off the LED

  calcVoltage = voMeasured * (3.3 / 4095.0);  // Map 0-3.3V to 0-4095 integer values and recover voltage
  dustDensity = 170 * calcVoltage - 0.1;

  Serial.print("Dust Density: ");
  Serial.print(dustDensity);
  Serial.print(" ug/m3");  // Output in ug/m3

  /* DATA TABEL STATUS KUALITAS AIR. REFERENSI https://www.aqi.in/air-quality-monitor/sensible-monitor
  0 - 30. Bagus. (ug/m3) 
  31 - 60. Memuaskan. (ug/m3) 
  61 - 90. Tercemar Sedang. (ug/m3) 
  91 - 120. Buruk. (ug/m3) 
  121 - 250. Sangat Buruk. (ug/m3) 
  250+ Parah. (ug/m3)
  */

  String status = "";
  if (dustDensity >= 0.0 && dustDensity <= 30.0) {
    status = "Bagus";
    fanSpeed = 20;
  } else if (dustDensity > 30.0 && dustDensity <= 60.0) {
    status = "Cukup Bagus";
    fanSpeed = 70;
  } else if (dustDensity > 60.0 && dustDensity <= 90.0) {
    status = "Tercemar Sedang";
    fanSpeed = 120;
  } else if (dustDensity > 90.0 && dustDensity <= 120.0) {
    status = "Buruk";
    fanSpeed = 170;
  } else if (dustDensity > 120.0 && dustDensity <= 250.0) {
    status = "Sangat Buruk";
    fanSpeed = 220;
  } else if (dustDensity > 250.0) {
    status = "Sangat Parah";
    fanSpeed = 255;
  } else {
    status = "error";
    fanSpeed = 0;
  }

  analogWrite(PIN_FAN, fanSpeed);

  Serial.print(" - ");
  Serial.println(status);

  /* KIRIM DATA KE SERVER */
  if (WiFi.status() == WL_CONNECTED) {  //Check WiFi connection status
    WiFiClient client;
    HTTPClient http;
    http.begin(client, serverName);  // Your Domain name with URL path or IP address with path

    http.addHeader("Content-Type", "application/x-www-form-urlencoded");  // Specify content-type header

    /* Persiapan membuat HTTP POST request data */
    String httpRequestData = "api_key=" + apiKeyValue + "&sensor=" + dustDensity + "&fan=" + fanSpeed + "&status=" + status ;
    Serial.println(httpRequestData);

    //int z = random(0, 100);
    //String httpRequestData = "api_key=tPmAT5Ab3j7F9&sensor=" + String(z) + "&fan=off";
    //Serial.println(httpRequestData);

    int httpResponseCode = http.POST(httpRequestData);      // Send HTTP POST request

    if (httpResponseCode > 0) {
      Serial.print("HTTP Response code: ");
      Serial.println(httpResponseCode);
    } else {
      Serial.print("Error code: ");
      Serial.println(httpResponseCode);
    }
    http.end();

  } else {
    Serial.println("WiFi Disconnected");
  }

  delay(3000);
}
<?php
/**
 * SIOTIC API Smoke Test Suite
 * Verifies critical business logic and endpoint integrity.
 */

require_once __DIR__ . '/../src/conexiondb.php';

class SmokeTest {
    private $baseUrl = 'http://localhost:8080/api';
    private $token = null;

    public function run() {
        echo "Starting SIOTIC API Smoke Tests...\n";
        echo "--------------------------------------\n";

        try {
            $this->testAuthentication();
            $this->testDashboardMetrics();
            $this->testStockForecasting();
            $this->testTimelineIntegrity();
            
            echo "\n✅ ALL TESTS PASSED SUCCESSFULLY\n";
        } catch (Exception $e) {
            echo "\n❌ TEST FAILURE: " . $e->getMessage() . "\n";
            exit(1);
        }
    }

    private function request($method, $endpoint, $data = null) {
        $ch = curl_init($this->baseUrl . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        
        $headers = [];
        if ($this->token) {
            $headers[] = 'Authorization: Bearer ' . $this->token;
        }
        
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            $headers[] = 'Content-Type: application/json';
        }
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ['status' => $statusCode, 'data' => json_decode($response, true)];
    }

    private function testAuthentication() {
        echo "Testing Authentication... ";
        $res = $this->request('POST', '/auth/login', [
            'username' => 'admin',
            'password' => 'admin123'
        ]);

        if ($res['status'] !== 200 || !isset($res['data']['token'])) {
            throw new Exception("Authentication failed with status " . $res['status']);
        }
        $this->token = $res['data']['token'];
        echo "OK\n";
    }

    private function testDashboardMetrics() {
        echo "Testing Dashboard Metrics... ";
        $res = $this->request('GET', '/dashboard');
        if ($res['status'] !== 200 || !isset($res['data']['total_hardware'])) {
            throw new Exception("Dashboard metrics retrieval failed");
        }
        echo "OK\n";
    }

    private function testStockForecasting() {
        echo "Testing Stock Forecasting Logic... ";
        $res = $this->request('GET', '/analytics/forecast');
        if ($res['status'] !== 200 || !is_array($res['data'])) {
            throw new Exception("Forecasting engine failed");
        }
        
        // Verify projection data exists
        if (count($res['data']) > 0) {
            $first = $res['data'][0];
            if (!isset($first['days_left']) || !isset($first['velocity'])) {
                throw new Exception("Forecasting data payload incomplete");
            }
        }
        echo "OK\n";
    }

    private function testTimelineIntegrity() {
        echo "Testing Timeline/Audit Path... ";
        $res = $this->request('GET', '/timeline?type=hardware&id=1');
        if ($res['status'] !== 200) {
            throw new Exception("Timeline retrieval failed");
        }
        echo "OK\n";
    }
}

$testSuite = new SmokeTest();
$testSuite->run();

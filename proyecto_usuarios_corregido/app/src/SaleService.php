<?php

class SaleService {
    private string $apiUrl;

    public function __construct() {
        $this->apiUrl = 'http://localhost:8000/facturas/'; 
    }
 
    public function listar(): array {
        $ch = curl_init($this->apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        return $response ? json_decode($response, true) ?? [] : [];
    } 
 
    public function obtenerPorId(int $id): ?array {
        $ch = curl_init("{$this->apiUrl}{$id}");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200 && $response) {
            return json_decode($response, true);
        }
        return null;
    }
}
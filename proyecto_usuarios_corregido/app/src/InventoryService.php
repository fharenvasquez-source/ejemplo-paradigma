<?php

class InventoryService {
    private string $apiUrl;

    public function __construct() {
        $this->apiUrl = 'http://localhost:8000/productos/'; 
    }
  
    public function listar(): array {
        $ch = curl_init($this->apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        return $response ? json_decode($response, true) ?? [] : [];
    }
}
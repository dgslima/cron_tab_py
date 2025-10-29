<?php

class Address
{
    public $nome;
    public $cep;
    public $logradouro;
    public $numero;
    public $complemento;
    public $bairro;
    public $cidade;
    public $uf;

    function __construct($nome, $cep, $logradouro, $numero, $bairro, $complemento, $cidade, $uf)
    {
        if($nome === "SEM-VELORIO" || $nome === "SEM LABORATORIO"){
            $this->nome = null;
            // IF IT DOES NOT HAVE 8 DIGIT PAD_LEFT 0 UNTIL 8 DIGITS
            $this->cep = null;
            $this->logradouro = null;
            $this->numero = null;
            $this->bairro = null;
            $this->complemento = null;
            $this->cidade = null;
            $this->uf = null;
        } else {
            $this->nome = $nome;
            // IF IT DOES NOT HAVE 8 DIGIT PAD_LEFT 0 UNTIL 8 DIGITS
            $this->cep = str_pad(preg_replace('/\s+/', '', $cep), 8, '0', STR_PAD_LEFT);
            $this->logradouro = $logradouro;
            $this->numero = $numero;
            $this->bairro = $bairro;
            $this->complemento = $complemento;
            $this->cidade = $cidade;
            $this->uf = $uf;
        }
    }

    function toString()
    {
        if($this->nome === null){
            return null;
        }
        $formatted_cep = str_pad($this->cep, 8, '0', STR_PAD_LEFT);
        $formatted_cep = substr($formatted_cep, 0, 5) . '-' . substr($formatted_cep, 5);
        $address = strtoupper($this->logradouro) . ' - Nº ' . trim($this->numero);
        if (!empty($complemento)) {
            $address .= ' - ' . strtoupper($complemento);
        }
        $address .= ' - ' . strtoupper($this->bairro) . ' - ' . $formatted_cep;
        return $address;
    }

    function geocoding()
    {
        $formattedCEP = substr($this->cep, 0, 5) . '-' . substr($this->cep, 5);
        $numero = str_replace(' ', '', $this->numero);
        $query = "$numero $this->logradouro, $this->bairro, $this->cidade, $this->uf $formattedCEP, Brazil";
        $q = urlencode($query);
        $url = "https://geocode.search.hereapi.com/v1/geocode?q=$q&apiKey=aJOrzbvwdAB3Rc2SbC8ilup4wxJgtf3voDBuln2y598";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        $data = json_decode($response, true);
        return $data["items"][0]["position"];
    }
}
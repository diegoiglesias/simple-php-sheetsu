<?php

/**
 * Simple PHP Sheetsu class
 * API Documentation: https://sheetsu.com/docs/beta
 *
 * @author Diego Iglesias
 * @since 12.13.2015
 * @version 0.5
 * @license MIT https://opensource.org/licenses/MIT
 */

namespace SimpleSheetsu;

class Sheetsu
{
    /**
     * Sheetsu API Version
     */
    protected $apiVersion = '1.0';

    /**
     * Sheetsu API Endpoint
     */
    protected $apiEndpoint = 'https://sheetsu.com/apis';

    /**
     * Populated on instantiation, combo of apiEndpoint and apiVersion
     */
    protected $apiUrl;

    /*
     *  Sheetsu API id
     */
    protected $apiId;

    /**
     * Sheetsu API Key (optional)
     */
    protected $apiKey;

    /**
     * Sheetsu API Secret (optional)
     */
    protected $apiSecret;


    function __construct($config)
    {
        // CURL is required in order for this extension to work
        if (!function_exists('curl_init')) {
            throw new \Exception('CURL is required for simple-php-sheetsu');
        }

        if (is_array($config)) {

            if(isset($config['apiKey']) && isset($config['apiSecret'])){
                $this->setApiKey($config['apiKey']);
                $this->setApiSecret($config['apiSecret']);
            }

            $this->setApiId($config['apiId']);
            $this->setApiUrl($this->apiEndpoint.'/v'.$this->apiVersion.'/'.$this->apiId);

        } else {
            throw new \Exception('Error: __construct() - Configuration data is missing.');
        }
    }

    private function rest($method = 'GET', $arguments = null)
    {
        $curlObj = curl_init($this->apiUrl);

        if($this->apiKey && $this->apiSecret){
            curl_setopt($curlObj, CURLOPT_USERPWD, $this->apiKey . ":" . $this->apiSecret);
        }

        curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlObj, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        switch ($method) {
            case 'GET':
                break;
            case 'POST':
                curl_setopt($curlObj, CURLOPT_POST, true);
                curl_setopt($curlObj, CURLOPT_POSTFIELDS, json_encode($arguments, true));
                break;
            case 'PUT':
            case 'PATCH':
                curl_setopt($curlObj, CURLOPT_CUSTOMREQUEST, $method);
                curl_setopt($curlObj, CURLOPT_POSTFIELDS, json_encode($arguments, true));
                break;
            case 'DELETE':
            case 'DEL':
                curl_setopt($curlObj, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
            default:
                throw new \Exception('Invalid method specified');
                break;
        }

        $curl_response = curl_exec($curlObj);
        $http_status = curl_getinfo($curlObj, CURLINFO_HTTP_CODE);
        curl_close($curlObj);

        return [
            'http_status' => $http_status,
            'response' => json_decode($curl_response,true)
        ];
    }

    public function getSpreadsheet($column = null, $value = null, $fields = null)
    {
        if($column && $value){
            $this->apiUrl .= '/'.$column.'/'.$value;
        }

        if($fields){
            $this->apiUrl .= '?fields='.$fields;
        }

        $rest = $this->rest();

        if($rest['http_status'] == '200'){

            return $rest['response'];

        }else{

            throw new \Exception($rest['http_status'].' '.$rest['response']['error']);
        }
    }

    public function search($query, $fields = null)
    {
        $this->apiUrl .= '/search?';

        foreach($query as $key=>$value){
            $this->apiUrl .= $key.'='.$value.'&';
        }

        $this->apiUrl .= ($fields ? '&fields='.$fields : '/');

        $rest = $this->rest();

        if($rest['http_status'] == '200'){

            return $rest['response'];

        }else{

            throw new \Exception($rest['http_status'].' '.$rest['response']['error']);
        }

    }

    public function createRows($rows)
    {
        if(is_array($rows[0])){
            $rows = ['rows' => $rows];
        }

        $rest = $this->rest('POST', $rows);

        if($rest['http_status'] == '201'){

            return $rest['response'];

        }else{

            throw new \Exception($rest['http_status'].' '.$rest['response']['error']);
        }

    }

    public function updateRows($column, $value, $newRow, $overwrite = false)
    {
        $this->apiUrl .= '/'.$column.'/'.$value;

        if($overwrite == true){
            $method = 'PUT';
        }else{
            $method = 'PATCH';
        }

        $rest = $this->rest($method, $newRow);

        if($rest['http_status'] == '200'){

            return $rest['response'];

        }else{

            throw new \Exception($rest['http_status'].' '.$rest['response']['error']);
        }
    }

    public function deleteRows($column, $value)
    {
        $this->apiUrl .= '/'.$column.'/'.$value;

        $rest = $this->rest('DELETE');

        if($rest['http_status'] == '204'){

            return $rest['response'];

        }else{

            throw new \Exception($rest['http_status'].' '.$rest['response']['error']);
        }
    }

    /**
     * @return mixed
     */
    public function getApiEndpoint()
    {
        return $this->apiEndpoint;
    }

    /**
     * @param mixed $apiEndpoint
     */
    public function setApiEndpoint($apiEndpoint)
    {
        $this->apiEndpoint = $apiEndpoint;
    }

    /**
     * @return mixed
     */
    public function getApiUrl()
    {
        return $this->apiUrl;
    }

    /**
     * @param mixed $apiUrl
     */
    public function setApiUrl($apiUrl)
    {
        $this->apiUrl = $apiUrl;
    }

    /**
     * @return mixed
     */
    public function getApiId()
    {
        return $this->apiId;
    }

    /**
     * @param mixed $apiId
     */
    public function setApiId($apiId)
    {
        $this->apiId = $apiId;
    }

    /**
     * @return mixed
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @param mixed $apiKey
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @return mixed
     */
    public function getApiSecret()
    {
        return $this->apiSecret;
    }

    /**
     * @param mixed $apiSecret
     */
    public function setApiSecret($apiSecret)
    {
        $this->apiSecret = $apiSecret;
    }
}
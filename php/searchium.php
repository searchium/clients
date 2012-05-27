<?php

class SearchiumClient
{

    public $error;
    public $bucket;
    public $apikey;
    public $apiurl;
    public $searchurl;

    /**
     * @param string $bucket name of the bucket
     * @param string $apikey API Key string
     */
    function __construct($bucket, $apikey) {
        if ( ! ( is_callable('json_encode') && is_callable('json_decode') ) ) {
            throw new Exception('JSON serialization functions not found. Please install PECL extension or upgrade to PHP 5.2');
        } else if ( ! ( is_callable('curl_exec') && is_callable('curl_init') ) ) {
            throw new Exception('cURL functions not found. Please enable them or upgrade to PHP 5');
        }

        $this->bucket = $bucket;
        $this->apikey = $apikey;
        $this->apiurl = 'https://api.searchium.com/';
        $this->searchurl = 'http://s.searchium.com/' . $this->bucket . '/?q=';
    }

    /**
     * Saves the document received as (dict) parameter
     * @param array $doc document to save, can be array or object
     * @param string $id document ID if knwown
     * @return mixed false in case of error, string with the ID in case of success
     */
    public function save($doc, $id='') {
        $jsondoc = json_encode($doc);
        if (!$jsondoc) {
            $this->error = 'Could not encode document';
            return false;
        }

        $sig = $this->signature($jsondoc);
        $url = $this->apiurl . 'save/' . $this->bucket .'/'. $id . '?signature=' . $sig;
        $response = $this->send_request($url, $jsondoc);
        return $response['id'];
    }

    /**
     * Gets document from DB by ID
     * @param string $id document ID
     * @return mixed array/object document if found, false if not found
     */
    public function get($id) {
        $sig = $this->signature($id);
        $url = $this->apiurl . 'get/' . $this->bucket .'/'. $id . '?signature=' . $sig;
        $response = $this->send_request($url, $jsondoc);     
        return $response['doc'];
    }

    /**
     * Deletes document from DB by ID
     * @param string $id document ID
     * @return bool true if success, false if not found
     */
    public function delete($id) {
        $sig = $this->signature($id);
        $url = $this->apiurl . 'delete/' . $this->bucket .'/'. $id . '?signature=' . $sig;
        $response = $this->send_request($url, $jsondoc);
        return $response['ok'];
    }

    /**
     * Runs provided search query
     * @param string $query keyword(s) or field:keyword(s)
     * @param string $fields specific fields to retrieve (coma separated)
     * @return mixed array with results if success, false if error
     */
    public function search($query, $fields=false) {
        $url = $this->searchurl . $query . ( $fields ? '&fields='.$fields : '' );
        $response = $this->send_request($url, $jsondoc);
        return $response;     
    }

    /**
     * Calculates the signature for the petition
     * @param string $data JSON encoded document/s
     * @return string signature
     */
    protected function signature($data) {
        return sha1( $data . $this->apikey );
    }

    /**
     * Sends HTTP request to searchium API servers
     * @param string $url action url with signature
     * @param string $data JSON encoded document/s
     * @return mixed parsed response if success, false if error
     */
    protected function send_request($url, $data = false) {
        $options = array(
            CURLOPT_HEADER => 0,
            CURLOPT_URL => $url,
            CURLOPT_FRESH_CONNECT => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FORBID_REUSE => 1,
            CURLOPT_TIMEOUT => 10,
        );

        if ($data) {
            $options[CURLOPT_POST] = 1;
            $options[CURLOPT_POSTFIELDS] = $data;
        }

        try {
            $ch = curl_init();
            curl_setopt_array($ch, $options);
            if (!$result = curl_exec($ch)) {
                throw new Exception(curl_error($ch));
            }

            $response = json_decode($result, true);

            if ($response && $response['ok']) {
                $this->error = null;
                return $response; // Success
            } else if ($response['error']) {
                $this->error = $response['error'];
            } else {
                $this->error = 'Bad response from server';
            }

        } catch (Exception $e) {
            $this->error = $e->getMessage();   
        }

        curl_close($ch);
        return false;
    }   

}
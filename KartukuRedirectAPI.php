<?php

/**
 * Library for Payment using redirect API
 *
 * @author mfachri
 */
class KartukuRedirectAPI {

    private $merchantToken;
    private $secretKey;

    // set your merhcant token using this function
    public function setMerchantToken($value) {
        $this->merchantToken = $value;
    }

    // set your merhcant token using this function
    // keep your secret key safe, do not write it on source code
    public function setSecretKey($value) {
        $this->secretKey = $value;
    }

    public function getMerchantToken(){
        return $this->merchantToken;
    }
    
    // ipg url
    const IPG_DIRECT_BASE_URL_SANDBOX = "https://ipg-test.kartuku.com/";
    const IPG_DIRECT_BASE_URL_PRODUCTION = "https://ipg.kartuku.com/";

    public static $production = false;

    // constant
    const CC_PAYMENT_FORM_CONTEXT_PATH = "web/payment_form";
    const EMONEY_PAYMENT_FORM_CONTEXT_PATH = "emoney/pay";
    const REQ_SIGNATURE_EXCLUDES = array(
        'txnLang'
    );

    public function setProduction($val) {
        self::$production = $val;
    }

    public function isProduction() {
        return self::$production;
    }

    public function calculateReqSignature($paymentData) {
        foreach (self::REQ_SIGNATURE_EXCLUDES as $exclude) {
            unset($paymentData[$exclude]);
        }

        $hashStr = $this->concatenateParameter($paymentData);
        return hash_hmac('sha256', $hashStr, $this->secretKey);
    }
    
    public function checkRespSignature($responseData){
        if(!array_key_exists('respSignature', $responseData)){
            return false;
        }
        
        $respSignature = $responseData['respSignature'];
        unset($responseData['respSignature']);
        
        $hashStr = $this->concatenateParameter($responseData);
        $calculatedSignature = hash_hmac('sha256', $hashStr, $this->secretKey);
        return strtoupper($respSignature) == strtoupper($calculatedSignature);
    }
    
    public function getResponseData($post){
        $response = array();
        foreach ($post as $key => $value) {
            $response[$key] = htmlspecialchars($value);
        }
        return $response;
    }

    public function concatenateParameter($param) {
        ksort($param);

        $str = '';
        foreach ($param as $key => $value) {
            if (!$this->IsNullOrEmptyString($value)) {
                $str = $str . $key . '=' . $value . '&';
            }
        }
        return preg_replace('/^(.*)&$/', '$1', $str); // remove trailing '&'
    }

    private function IsNullOrEmptyString($var) {
        return (!isset($var) || trim($var) === '');
    }
    
    public function generateCcPaymentForm($paymentData, $formId){
        return $this->generateHiddenPaymentForm($paymentData, $formId, self::CC_PAYMENT_FORM_CONTEXT_PATH);
    }
    
    public function generateEmoneyPaymentForm($paymentData, $formId){
        return $this->generateHiddenPaymentForm($paymentData, $formId, self::EMONEY_PAYMENT_FORM_CONTEXT_PATH);
    }

    private function generateHiddenPaymentForm($paymentData, $formId, $target) {
        $reqSignature = $this->calculateReqSignature($paymentData);
        $paymentData['reqSignature'] = strtoupper($reqSignature);

        $form = '<form id="' . $formId . '" method="POST" action="' . $this->getIPGUrl() . $target . '">';
        foreach ($paymentData as $key => $value) {
            $form = $form . '<br><input type="hidden" name=' . $key . ' value="' . $value . '"/>';
        }
        $form = $form . '</form>';
        return $form;
    }

    public static function getIPGUrl() {
        return self::$production ? self::IPG_DIRECT_BASE_URL_PRODUCTION : self::IPG_DIRECT_BASE_URL_SANDBOX;
    }

}

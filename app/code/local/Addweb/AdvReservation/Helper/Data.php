<?php

/**
 * Class Addweb_AdvReservation_Helper_Data
 *
 * @author Vlasakh
 */

//include $_SERVER['DOCUMENT_ROOT']."/test/util.php";


/*class Mage_Addweb_Advreservation_Helper_Data extends Mage_Core_Helper_Abstract
{
}*/


class Addweb_AdvReservation_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getProductOptionsHtml(Addweb_AdvReservation_Model_Catalog_Product_Option $product)
    {
        return $product;
    }


    public function getSagePayUrl()
    {
        $url = Mage::getStoreConfig('addweb/sagepay_is_test_mode', 1) == 1 ?
            Mage::getStoreConfig('addweb/sagepay_test_url', 1)
            :
            Mage::getStoreConfig('addweb/sagepay_live_url', 1);

        return $url;
    }



    /**
     * Using for prepare crypt string
     * @param array $data
     * @param string $delimiter
     * @param bool $urlencoded
     * @return bool|string
     */
    public function arrayToQueryString(array $data, $delimiter = '&', $urlencoded = false)
    {
        $queryString = '';
        $delimiterLength = strlen($delimiter);

        // Parse each value pairs and concate to query string
        foreach ($data as $name => $value)
        {
            // Apply urlencode if it is required
            if ($urlencoded)
            {
                $value = urlencode($value);
            }
            $queryString .= $name . '=' . $value . $delimiter;
        }

        // remove the last delimiter
        return substr($queryString, 0, -1 * $delimiterLength);
    }



    /**
     * Convert string to data array.
     *
     * @param string  $data       Query string
     * @param string  $delimeter  Delimiter used in query string
     *
     * @return array
     */
    public function queryStringToArray($data, $delimeter = "&")
    {
        // Explode query by delimiter
        $pairs = explode($delimeter, $data);
        $queryArray = array();

        // Explode pairs by "="
        foreach ($pairs as $pair)
        {
            $keyValue = explode('=', $pair);

            // Use first value as key
            $key = array_shift($keyValue);

            // Implode others as value for $key
            $queryArray[$key] = implode('=', $keyValue);
        }
        return $queryArray;
    }




    /**
     * Crypt string for Sage Pay
     * @param $string
     * @param $key
     * @return string
     */
    public function encryptAes($string, $key)
    {
        // AES encryption, CBC blocking with PKCS5 padding then HEX encoding.
        // Add PKCS5 padding to the text to be encypted.
        $string = $this->addPKCS5Padding($string);

        // Perform encryption with PHP's MCRYPT module.
        $crypt = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $string, MCRYPT_MODE_CBC, $key);

        // Perform hex encoding and return.
        return "@" . strtoupper(bin2hex($crypt));
    }



    /**
     * Decode a returned string from SagePay.
     *
     * @param string $strIn         The encrypted String.
     * @param string $password      The encyption password used to encrypt the string.
     *
     * @return string The unecrypted string.
     * @throws SagepayApiException
     */
    public function decryptAes($strIn, $password)
    {
        // HEX decoding then AES decryption, CBC blocking with PKCS5 padding.
        // Use initialization vector (IV) set from $str_encryption_password.
        $strInitVector = $password;

        // Remove the first char which is @ to flag this is AES encrypted and HEX decoding.
        $hex = substr($strIn, 1);

        // Throw exception if string is malformed
        if (!preg_match('/^[0-9a-fA-F]+$/', $hex))
        {
            throw new Exception('Invalid encryption string');
        }
        $strIn = pack('H*', $hex);

        // Perform decryption with PHP's MCRYPT module.
        $string = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $password, $strIn, MCRYPT_MODE_CBC, $strInitVector);
        return $this->removePKCS5Padding($string);
    }



    /**
     * Remove PKCS5 Padding from a string.
     *
     * @param string $input The decrypted string.
     *
     * @return string String without the padding.
     * @throws SagepayApiException
     */
    private function removePKCS5Padding($input)
    {
        $blockSize = 16;
        $padChar = ord($input[strlen($input) - 1]);

        /* Check for PadChar is less then Block size */
        if ($padChar > $blockSize)
        {
            throw new Exception('Invalid encryption string');
        }
        /* Check by padding by character mask */
        if (strspn($input, chr($padChar), strlen($input) - $padChar) != $padChar)
        {
            throw new Exception('Invalid encryption string');
        }

        $unpadded = substr($input, 0, (-1) * $padChar);
        /* Chech result for printable characters */
        if (preg_match('/[[:^print:]]/', $unpadded))
        {
            throw new Exception('Invalid encryption string');
        }
        return $unpadded;
    }



    /**
     * PHP's mcrypt does not have built in PKCS5 Padding, so we use this.
     * @param string $input The input string.
     * @return string The string with padding.
     */
    private function addPKCS5Padding($input)
    {
        $blockSize = 16;
        $padd = "";

        // Pad input to an even block size boundary.
        $length = $blockSize - (strlen($input) % $blockSize);
        for ($i = 1; $i <= $length; $i++)
        {
            $padd .= chr($length);
        }

        return $input . $padd;
    }
}

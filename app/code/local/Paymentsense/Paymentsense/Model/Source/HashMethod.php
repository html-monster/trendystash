<?php

class Paymentsense_Paymentsense_Model_Source_HashMethod
{
	// public enum for the payment types
	const HASH_METHOD_MD5 = 'md5';
	const HASH_METHOD_SHA1 = 'sha1';
	const HASH_METHOD_HMACMD5 = 'hmacmd5';
	const HASH_METHOD_HMACSHA1 = 'hmacsha1';

	public function toOptionArray()
    {
        return array
        (
            array(
                'value' => self::HASH_METHOD_MD5,
                'label' => Mage::helper('Paymentsense')->__('MD5')
            ),
            array(
                'value' => self::HASH_METHOD_SHA1,
                'label' => Mage::helper('Paymentsense')->__('SHA1')
            ),
            array(
                'value' => self::HASH_METHOD_HMACMD5,
                'label' => Mage::helper('Paymentsense')->__('HMACMD5')
            ),
            array(
                'value' => self::HASH_METHOD_HMACSHA1,
                'label' => Mage::helper('Paymentsense')->__('HMACSHA1')
            )
        );
    }
}
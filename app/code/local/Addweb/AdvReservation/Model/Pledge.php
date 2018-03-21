<?php
/**
 * Created by Vlasakh
 * Date: 19.05.2017
 */

class Addweb_AdvReservation_Model_Pledge extends Mage_Core_Model_Abstract
{
    /**
     * Prepare info for pledge payment form
     * @param $order
     * @param $prodPledge
     * @return array
     */
    public function getSagePayPledgeFormData($order, $prodPledge)
    {
        $orderData = $order->getData();
        $CryptInfo = [
            'VendorTxCode' => "{$order->getRealOrderId()}-{$order->getId()}-{$prodPledge['id']}-".time(),
            'Amount' => $prodPledge['pledge'],
            'Currency' => 'GBP',
            'Description' => "{$order->getRealOrderId()} - {$prodPledge['name']} ({$prodPledge['fdate']} - {$prodPledge['tdate']})",
//            'SuccessURL' => 'http://rentabag.dev/test/test.php?success=1',
            'SuccessURL' => "https://{$_SERVER['SERVER_NAME']}/rent/pledge/payresult?order={$order->getId()}&id={$prodPledge['id']}&success=1",
            'FailureURL' => "https://{$_SERVER['SERVER_NAME']}/rent/pledge/payresult?order={$order->getId()}&id={$prodPledge['id']}&fail=1",
//            'FailureURL' => 'http://rentabag.dev/test/test.php?fail=1',
            'CustomerName' => "{$orderData['customer_firstname']} {$orderData['customer_lastname']}",
            'CustomerEMail' => $orderData['customer_email'],

            'VendorEMail' => '',
            'SendEMail' => '0',
            'eMailMessage' => '',
            'ApplyAVSCV2' => '0',
            'Apply3DSecure' => '0',
            'AllowGiftAid' => '0',
            'BillingAgreement' => '1',
            'VendorData' => $prodPledge['name'], //some my vendor data
            'BillingAddress2' => '-', //Frunse 55
//            'BillingPhone' => '44 (0)7933 000 000',
            'BillingState' => '',
            'DeliveryState' => '',
//            'DeliveryPhone' => '44 (0)7933 000 000',
//            'BasketXML' => '',

            'BillingSurname' => $orderData['customer_lastname'],
            'BillingFirstnames' => $orderData['customer_firstname'],
            'BillingAddress1' => '-',
            'BillingCity' => '-',
            'BillingPostCode' => '-', // W1A 1BL
            'BillingCountry' => 'GB',
            'DeliverySurname' => $orderData['customer_lastname'],
            'DeliveryFirstnames' => $orderData['customer_firstname'],
            'DeliveryAddress1' => '-',
            'DeliveryCity' => '-',
            'DeliveryPostCode' => '-',
            'DeliveryCountry' => 'GB',
        ];


        $pass = Mage::getStoreConfig('addweb/sagepay_is_test_mode', 1) == 1 ?
            Mage::getStoreConfig('addweb/test_pass', 1)
            :
            Mage::getStoreConfig('addweb/live_pass', 1)
            ;

        return [
            'data' => [
//                'FailureURL' => "http://{$_SERVER['SERVER_NAME']}{$_SERVER['REQUEST_URI']}?fail=1",
//                'VendorTxCode' => "{$order->getRealOrderId()}-{$prodPledge['id']}-PLEDGE-".time(),
//                'Amount' => $prodPledge['pledge'],

                'VPSProtocol' => '3.00',
                'TxType' => 'DEFERRED',
                'Vendor' => 'rentabag',
                'Crypt' => Mage::helper('advreservation')->encryptAes(Mage::helper('advreservation')->arrayToQueryString($CryptInfo), $pass),
            ]
        ];
    }



    /**
     * Get order products pledges and info
     * @param $order
     * @return mixed
     */
    public function getOrderProductsPledge($order)
    {
        $results = [];

        if (is_numeric($order)) $order = Mage::getModel('sales/order')->load($order);


        if( $order->getData()['status'] == 'processing' && $order->getData()['state'] == 'processing' )
        {
            // *** Get prod pledge payment info ***
            $resource = Mage::getSingleton('core/resource');
            $readConnection = $resource->getConnection('core_read');

            $query = "SELECT
                  id, mailletters, price, id_prod,
                  DATE_FORMAT(fdate, '%Y-%m-%d') fdate,
                  DATE_FORMAT(tdate, '%Y-%m-%d') tdate,
                  DATE_FORMAT(pldate, '%Y-%m-%d %H:%i') pldate
                FROM adv_rent
                WHERE id_order = {$order->getId()} 
                  -- AND mailletters < 4";
            $results = $readConnection->fetchAll($query);

            foreach ($results as $key => $val) {
                $rentInfo[$val['id_prod']] = $val;
            } // end foreach

//
//        // process orders product without pledge
//        $orders = Mage::getResourceModel('sales/order_collection')
//            ->addFieldToSelect('*')
////            ->addFieldToFilter('customer_id', Mage::getSingleton('customer/session')->getCustomer()->getId())
//            ->addFieldToFilter('entity_id', Mage::app()->getRequest()->getParams('o'));
//
//        $result = [];
//        $this->setOrders($orders);
//        foreach ($orders as $order)
//        {
//            $order_id = $order->getRealOrderId();
//            $order = Mage::getModel('sales/order')->load($order_id, 'increment_id');
            $order->getAllVisibleItems();
            $orderItems = $order->getItemsCollection()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('product_type', array('eq' => 'simple'))
                ->load();


            foreach ($orderItems as $Item) {
                $Item = Mage::getModel('catalog/product')->setStoreId($Item->getStoreId())->load($Item->getProductId());
                // check each product
                if (($id = $Item->getId()) && count($rentInfo[$id])) {
                    $datetime1 = new DateTime("now");
                    $datetime2 = (new DateTime())->createFromFormat('Y-m-d', $rentInfo[$id]['fdate']);
                    $interval = $datetime1->diff($datetime2);

                    $itemData = $Item->getData();


                    $result[$id] = [
                        'id' => $id,
                        'idOrder' => $order->getIncrementId(),
                        'name' => $Item->getName(),
                        'url' => $Item->getProductUrl(),
                        'img' => $Item->getImageUrl(),
                        'pledge' => number_format($itemData['pledge'], 2),
                        'tdate' => date('d F Y', strtotime($rentInfo[$id]['tdate'])),
                        'fdate' => date('d F Y', strtotime($rentInfo[$id]['fdate'])),
                        'pldate' => date('d F Y H:i', strtotime($rentInfo[$id]['pldate'])),
                        'price' => number_format($rentInfo[$id]['price'], 2),
                        'expired' => !($datetime1->getTimestamp() < $datetime2->getTimestamp()), // ? false : true,
                        'greaterT7' => $interval->y == 0 && $interval->m == 0 && $interval->d > 7, // greater the 7 days
                    ];
//                    if ($datetime1->getTimestamp() < $datetime2->getTimestamp()) {
//                    } // endif
                }
            }
        } // endif

        return $result;
    }



    /**
     * Decrypt result data
     * @param $cryptData
     * @return mixed
     */
    public function getPaymentResultInfo($cryptData)
    {
        $pass = Mage::getStoreConfig('addweb/sagepay_is_test_mode', 1) == 1 ?
            Mage::getStoreConfig('addweb/test_pass', 1)
            :
            Mage::getStoreConfig('addweb/live_pass', 1)
            ;

        return Mage::helper('advreservation')->queryStringToArray(Mage::helper('advreservation')->decryptAes($cryptData, $pass));
    }



    /**
     * Set pledge payment into DB
     * @param $paymentData array
     */
    public function setPledgePayment($paymentData)
    {
        list(, $idOrder, $idProd) = explode('-', $paymentData['VendorTxCode']);

        // save user success pledge payment
        $resource = Mage::getSingleton('core/resource');
        /** @var Magento_Db_Adapter_Pdo_Mysql */
        $readConnection = $resource->getConnection('core_write');

        $query = "UPDATE adv_rent SET price = " . str_replace(',', '', $paymentData['Amount']) . ", pldate = NOW() WHERE id_order = {$idOrder} AND id_prod = {$idProd}";
        $results = $readConnection->query($query);


        // mail manager about payment
        $order = Mage::getModel('sales/order')->load($idOrder);
        $orderData = $order->getData();
        $Item = Mage::getModel('catalog/product')->setStoreId(1)->load($idProd);


        $emailTemplate  = Mage::getModel('core/email_template')->loadByCode('manager_pledge');

        $emailTemplateVariables = array();
        $emailTemplateVariables['userName'] = "{$orderData['customer_firstname']} {$orderData['customer_lastname']}";
        $emailTemplateVariables['userEmail'] = $orderData['customer_email'];
        $emailTemplateVariables['orderNum'] = $order->getIncrementId();
        $emailTemplateVariables['prodName'] = $Item->getName();
        $emailTemplateVariables['payDate'] = date('H:i d F Y', time());
        $emailTemplateVariables['baseUrl'] = Mage::getBaseUrl();
        $emailTemplateVariables['idOrder'] = $order->getId();

        $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables, true);

        $data = $emailTemplate->getData();
        $emailTemplate->setSenderName($data['template_sender_name']);
        $emailTemplate->setSenderEmail($data['template_sender_email']);
        $emailTemplate->setTemplateSubject($data['template_subject']);


        $userName = $pass = Mage::getStoreConfig('addweb/manager_user', 1);
        $managerUserData = Mage::getModel('admin/user')->getCollection()->addFieldToFilter('username', $userName)->getData();

        $emailTemplate->send($managerUserData[0]['email'], "robot", $emailTemplateVariables);

        return [$idOrder, $idProd];
    }



    /**
     * Notify user about pledge payment need
     * @return array
     */
    public function notifyOrdersNoPlege()
    {
        // *** Get not payed rents ***
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');

        $query = "SELECT
                  id, mailletters, price, id_prod, id_order,
                  DATE_FORMAT(fdate, '%Y-%m-%d') fdate,
                  DATE_FORMAT(tdate, '%Y-%m-%d') tdate
                FROM adv_rent
                WHERE fdate >= CURDATE() 
                  AND fdate <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)
                  AND status < 2
                  AND mailletters < 4
                  AND (price < 1 OR price IS NULL)
                ORDER BY fdate ";
        $rents = $readConnection->fetchAll($query);

//        foreach ($results as $key => $val)
//        {
//            $rents[$val['id_prod']] = $val;
//        } // end foreach



        // process orders product without pledge
        $orders = Mage::getResourceModel('sales/order_collection')
            ->addFieldToSelect('*')
//            ->addFieldToFilter('customer_id', Mage::getSingleton('customer/session')->getCustomer()->getId())
            ->addFieldToFilter('state', array('in' => Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates()))
            ->addFieldToFilter('status', 'processing')
            ->setOrder('created_at', 'desc');

        $result = [];
        $this->setOrders($orders);
        foreach ($orders as $order)
        {
            $order_id = $order->getRealOrderId();
            $order = Mage::getModel('sales/order')->load($order_id, 'increment_id');

            if ($order->getData()['state'] != 'processing') continue;

            $email = $order->getCustomerEmail();
            $order->getAllVisibleItems();
            $orderItems = $order->getItemsCollection()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('product_type', array('eq' => 'simple'))
                ->load();

            $result[$order_id] = ['email' => $email, 'id' => $order->getId()];
            foreach ($orderItems as $Item)
            {
                $Item = Mage::getModel('catalog/product')->setStoreId($Item->getStoreId())->load($Item->getProductId());
                // check each product
                if ($id = $Item->getId())
                {
                    $mailletter = false;
                    if( (int)$Item->getData()['pledge'] > 0 && ($prod = $this->searchProduct($rents, $id, $order->getId())) )
                    {
//                        $timezone = new DateTimeZone("Europe/Kiev");
                        $datetime1 = new DateTime("now");
                        $datetime2 = (new DateTime())->createFromFormat('Y-m-d', $prod['fdate']);
                        $interval = $datetime1->diff($datetime2);

                        // check 7 days
                        if( $prod['mailletters'] == 0 && $interval->y == 0 && $interval->m == 0
                            && $interval->d <= 7 && $interval->d > 2 )
                        {
                            $mailletter = 1;
//			i. Вставляем данные пользователя
//			ii. Вставляем номер заказа
//			iii. Описываем товар и залоговую стоимость
//			iv. Пишем, что заказ на такоето число для получ. товара необходимо внести залог + ссылка на функционал оплаты залога
//			v. Данное уведомление было прислано вам в связи с тем, что у вас был оплачена оренда заказа, но не внесен залог. После внесения суммы залога данное письмо больше не будет приходить.Если вы хотите отказаться от этих уведомлений - также можно связаться с нами и отменить заказ, перейти на страницу контактов (ссылка)



                        // check 2 days
                        } elseif ($prod['mailletters'] < 2 && $interval->y == 0 && $interval->m == 0
                            && $interval->d == 2 )
                        {
                            $mailletter = 2;




                        // check 1 days
                        } elseif ($prod['mailletters'] < 3 && $interval->y == 0 && $interval->m == 0
                            && $interval->d == 1 )
                        {
                            $mailletter = 3;




                        // check 0 days
                        } elseif ($prod['mailletters'] < 4 && $interval->y == 0 && $interval->m == 0
                            && $interval->d == 0 )
                        {
                            $mailletter = 4;
                        }



                        if( $mailletter )
                        {
                            $itemData = $Item->getData();


                            $resource = Mage::getSingleton('core/resource');
                            /** @var Magento_Db_Adapter_Pdo_Mysql */
                            $readConnection = $resource->getConnection('core_write');

                            $query = "UPDATE adv_rent SET mailletters = {$mailletter} WHERE id_order = {$order->getId()} AND id_prod = {$prod['id_prod']}";
                            $results = $readConnection->query($query);


//                            $emailTemplate  = Mage::getModel('core/email_template')->loadDefault(1);
                            $emailTemplate  = Mage::getModel('core/email_template')->loadByCode($mailletter < 4 ? 'rent_notify' : 'rent_refuse');


                            $emailTemplateVariables = array();
                            $emailTemplateVariables['baseUrl'] = Mage::getBaseUrl();
                            $emailTemplateVariables['orderDate'] = date('d F Y', strtotime($order->getCreatedAt()));
                            $emailTemplateVariables['orderNum'] = $order->getIncrementId();
                            $emailTemplateVariables['goodsName'] = trim($itemData['name']);
                            $emailTemplateVariables['rentStart'] = date('d F Y', strtotime($prod['fdate']));
                            $emailTemplateVariables['pledge'] = number_format($itemData['pledge'], 2);
                            $emailTemplateVariables['orderId'] = $order->getId();
                            $emailTemplateVariables['prodId'] = $id;

                            $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables, true);

//                            $emailTemplate->send('Zotaper@yandex.ru', 'John Doe', $emailTemplateVariables);
                            $data = $emailTemplate->getData();
                            $emailTemplate->setSenderName($data['template_sender_name']);
                            $emailTemplate->setSenderEmail($data['template_sender_email']);
                            $emailTemplate->setTemplateSubject($data['template_subject']);

                            $emailTemplate->send($order->getCustomerEmail(), $order->getCustomerFirstname() . " " . $order->getCustomerLastname(), $emailTemplateVariables);
/*
                            $mail = Mage::getModel('core/email');
                            $mail->setToName('Test name');
                            $mail->setToEmail('Zotaper@yandex.ru');
                            $mail->setBody('Mail Text / Mail Content');
                            $mail->setSubject('Mail Subject test');
                            $mail->setFromEmail('autoMailer@rentbg.com');
                            $mail->setFromName("Msg to Show on Subject");
                            $mail->setType('html');// YOu can use Html or text as Mail format

                            try {
                                $mail->send();
//                                Mage::getSingleton('core/session')->addSuccess('Your request has been sent');
//                                $this->_redirect('');
                            } catch (Exception $e) {
//                                Mage::getSingleton('core/session')->addError('Unable to send.');
//                                $this->_redirect('');
                            }*/
                        } // endif
                    } // endif

                    $result[$order_id]['items'][] = [$Item->getId(), $Item->getName(), $Item->getPrice(), $Item->getProductUrl(), $Item->getImageUrl(), $Item->getData()['pledge'], date('d F Y', strtotime($prod['fdate'])), $mailletter, (int)$Item->getData()['pledge'] > 0 && ($prod = $this->searchProduct($rents, $id, $order->getId()))];
                }
            }
        }

        return $result;
    }



    private function searchProduct($inProducts, $inProdId, $inOrderId)
    {
        foreach ($inProducts as $key => $val)
        {
            if( $val['id_prod'] == $inProdId && $val['id_order'] == $inOrderId )
            {
                return $val;
            } // endif
        } // end foreach

        return false;
    }
}
<?php
/**
 * Created by Vlasakh
 * Date: 30.08.2017
 * Time: 05:52
 */


class Addweb_AdvReservation_Block_Order_Item_Renderer_Default extends Mage_Sales_Block_Order_Item_Renderer_Default
{
    /**
     * @author Vlasakh
     */
    public function getItemOptions()
    {
        $result = array();
        if ($options = $this->getItem()->getProductOptions()) {
            if (isset($options['options']))
            {
                // ---------------------------
                // proccess from_date and to_date options
                foreach ($options['options'] as $key => &$val)
                {
                    if( $val['option_type'] == 'from_date' )
                    {
                        list($val['value'],) = explode(" ", $val['value']); // trim time
                        list($val['print_value'],) = explode(" ", $val['print_value']);
                        list($val['option_value'],) = explode(" ", $val['option_value']);
                        $fromData = (new DateTime())->createFromFormat('d/m/Y', trim(explode(' ', $val['value'])[0]), new DateTimeZone("UTC"));
                    }

                    if( $val['option_type'] == 'to_date' )
                    {
                        list($val['value'],) = explode(" ", $val['value']);
                        list($val['print_value'],) = explode(" ", $val['print_value']);
                        list($val['option_value'],) = explode(" ", $val['option_value']);
                        $toDate = (new DateTime())->createFromFormat('d/m/Y', trim(explode(' ', $val['value'])[0]), new DateTimeZone("UTC"));
                    }
                } // end foreach

                if( $fromData && $toDate )
                {
                    $interval = $fromData->diff($toDate);
                    $interval->d++;
                    $options['options'][] = array (
                          'label' => 'Days',
                          'value' => $interval->d,
                          'print_value' => $interval->d.'d',
                          'option_id' => '101',
                          'option_type' => 'days',
                          'custom_view' => false,
                          'display' => false,
                        );
                } // endif
                // ---------------------------

                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (isset($options['attributes_info'])) {
                $result = array_merge($result, $options['attributes_info']);
            }
        }

//        $s1 = '$options='.var_export($options, 1)."\n";
//        0||$notpr||file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/11/file', "\n--------------------\n".date("H:i:s")."\n".$s1, 0);//FILE_APPEND

        return $result;
    }
}
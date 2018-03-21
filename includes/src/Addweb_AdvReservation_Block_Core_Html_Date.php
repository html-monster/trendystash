<?php
/*
* Date.php file add custom Class and $this->getCustomId() this is new class "options_from_date" "options_to_date".
* comment the calendar image code.
* add new calendar jQuery-ui for calendar and add javascript for previous date and selected to previous date disable.
*/

class Addweb_AdvReservation_Block_Core_Html_Date extends Mage_Core_Block_Html_Date
{
    protected function _toHtml()
    {
		//echo $this->getCustomId();
        $displayFormat = Varien_Date::convertZendToStrFtime($this->getFormat(), true, (bool)$this->getTime());
        $html  = '<input type="text" name="' . $this->getName() . '" ';
        $html .= 'value="' . $this->escapeHtml($this->getValue()) . '" class="' . $this->getClass() . ' ' . $this->getCustomId() . '" ' . $this->getExtraParams() . ' readonly="readonly"/> ';

        /*$html .= '<img src="' . $this->getImage() . '" alt="' . $this->helper('core')->__('Select Date') . '" class="v-middle" ';
        $html .= 'title="' . $this->helper('core')->__('Select Date') . '" id="' . $this->getId() . '_trig" />';*/

        $html .=
        '<script type="text/javascript">
        //<![CDATA[		
			// For Departure Date & Return Date Picker
			if(jQuery(".options_from_date")) {
				jQuery(".options_from_date").datepicker({minDate: 0, onSelect: function (selected) {
																  var dt = new Date(selected);
																  dt.setDate(dt.getDate() + 0);
																  jQuery(".options_to_date").datepicker("option", "minDate", dt);
																  jQuery(".options_to_date").datepicker("setDate", dt);
															   }
				  });
			}
			if(jQuery(".options_to_date")) {
				jQuery(".options_to_date").datepicker({minDate: 0, onSelect: function (selected) { 
											  var dt = new Date(selected);
											  dt.setDate(dt.getDate() - 0);
											  jQuery(".options_from_date").datepicker("option", "maxDate", dt);
										   }
				});
			}			
			';
        $html .= '
        //]]>
        </script>';


        return $html;
    }

    public function getEscapedValue($index=null) {

        if($this->getFormat() && $this->getValue()) {
            return strftime($this->getFormat(), strtotime($this->getValue()));
        }

        return htmlspecialchars($this->getValue());
    }

    public function getHtml()
    {
        return $this->toHtml();
    }

}		
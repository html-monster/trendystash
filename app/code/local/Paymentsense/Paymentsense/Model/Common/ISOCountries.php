<?php

if (!defined('COMPILER_INCLUDE_PATH')) {
    include_once ("ThePaymentGateway/TPG_Common.php");
} else {
    include_once ("Paymentsense_Paymentsense_Model_Common_ThePaymentGateway_TPG_Common.php");
}

class CSV_ISOCountries
{
	/**
	 * Get the list of ISO Countries
	 *
	 * @return ISOCountryList
	 */
	public static function getISOCountryList()
	{
		$iclISOCountryList = new CSV_ISOCountryList();
		
		$iclISOCountryList->add(826,"United Kingdom","GBR",3);
		$iclISOCountryList->add(840,"United States","USA",2);
		$iclISOCountryList->add(36,"Australia","AUS",1);
		$iclISOCountryList->add(124,"Canada","CAN",1);
		$iclISOCountryList->add(250,"France","FRA",1);
		$iclISOCountryList->add(276,"Germany","DEU",1);
		$iclISOCountryList->add(4,"Afghanistan","AFG",0);
		$iclISOCountryList->add(248,"�land Islands","ALA",0);	
		$iclISOCountryList->add(8,"Albania","ALB",0);
		$iclISOCountryList->add(12,"Algeria","DZA",0);
		$iclISOCountryList->add(16,"American Samoa","ASM",0);
		$iclISOCountryList->add(20,"Andorra","AND",0);
		$iclISOCountryList->add(24,"Angola","AGO",0);
		$iclISOCountryList->add(660,"Anguilla","AIA",0);
		$iclISOCountryList->add(10,"Antarctica","ATA",0);
		$iclISOCountryList->add(28,"Antigua and Barbuda","ATG",0);
		$iclISOCountryList->add(32,"Argentina","ARG",0);
		$iclISOCountryList->add(51,"Armenia","ARM",0);
		$iclISOCountryList->add(533,"Aruba","ABW",0);
		$iclISOCountryList->add(40,"Austria","AUT",0);
		$iclISOCountryList->add(31,"Azerbaijan","AZE",0);
		$iclISOCountryList->add(44,"Bahamas","BHS",0);
		$iclISOCountryList->add(48,"Bahrain","BHR",0);
		$iclISOCountryList->add(50,"Bangladesh","BGD",0);
		$iclISOCountryList->add(52,"Barbados","BRB",0);
		$iclISOCountryList->add(112,"Belarus","BLR",0);
		$iclISOCountryList->add(56,"Belgium","BEL",0);
		$iclISOCountryList->add(84,"Belize","BLZ",0);
		$iclISOCountryList->add(204,"Benin","BEN",0);
		$iclISOCountryList->add(60,"Bermuda","BMU",0);
		$iclISOCountryList->add(64,"Bhutan","BTN",0);
		$iclISOCountryList->add(68,"Bolivia","BOL",0);
		$iclISOCountryList->add(70,"Bosnia and Herzegovina","BIH",0);
		$iclISOCountryList->add(72,"Botswana","BWA",0);
		$iclISOCountryList->add(74,"Bouvet Island","BVT",0);
		$iclISOCountryList->add(76,"Brazil Federative","BRA",0);
		$iclISOCountryList->add(86,"British Indian Ocean Territory","IOT",0);
		$iclISOCountryList->add(96,"Brunei","BRN",0);
		$iclISOCountryList->add(100,"Bulgaria","BGR",0);
		$iclISOCountryList->add(854,"Burkina Faso","BFA",0);
		$iclISOCountryList->add(108,"Burundi","BDI",0);
		$iclISOCountryList->add(116,"Cambodia","KHM",0);
		$iclISOCountryList->add(120,"Cameroon","CMR",0);
		$iclISOCountryList->add(132,"Cape Verde","CPV",0);
		$iclISOCountryList->add(136,"Cayman Islands","CYM",0);
		$iclISOCountryList->add(140,"Central African Republic","CAF",0);
		$iclISOCountryList->add(148,"Chad","TCD",0);
		$iclISOCountryList->add(152,"Chile","CHL",0);
		$iclISOCountryList->add(156,"China","CHN",0);
		$iclISOCountryList->add(162,"Christmas Island","CXR",0);
		$iclISOCountryList->add(166,"Cocos (Keeling) Islands","CCK",0);
		$iclISOCountryList->add(170,"Colombia","COL",0);
		$iclISOCountryList->add(174,"Comoros","COM",0);
		$iclISOCountryList->add(180,"Congo","COD",0);
		$iclISOCountryList->add(178,"Congo","COG",0);
		$iclISOCountryList->add(184,"Cook Islands","COK",0);
		$iclISOCountryList->add(188,"Costa Rica","CRI",0);
		$iclISOCountryList->add(384,"C�te d'Ivoire","CIV",0);
		$iclISOCountryList->add(191,"Croatia","HRV",0);
		$iclISOCountryList->add(192,"Cuba","CUB",0);
		$iclISOCountryList->add(196,"Cyprus","CYP",0);
		$iclISOCountryList->add(203,"Czech Republic","CZE",0);
		$iclISOCountryList->add(208,"Denmark","DNK",0);
		$iclISOCountryList->add(262,"Djibouti","DJI",0);
		$iclISOCountryList->add(212,"Dominica","DMA",0);
		$iclISOCountryList->add(214,"Dominican Republic","DOM",0);
		$iclISOCountryList->add(626,"East Timor","TMP",0);
		$iclISOCountryList->add(218,"Ecuador","ECU",0);
		$iclISOCountryList->add(818,"Egypt","EGY",0);
		$iclISOCountryList->add(222,"El Salvador","SLV",0);
		$iclISOCountryList->add(226,"Equatorial Guinea","GNQ",0);
		$iclISOCountryList->add(232,"Eritrea","ERI",0);
		$iclISOCountryList->add(233,"Estonia","EST",0);
		$iclISOCountryList->add(231,"Ethiopia","ETH",0);
		$iclISOCountryList->add(238,"Falkland Islands (Malvinas)","FLK",0);
		$iclISOCountryList->add(234,"Faroe Islands","FRO",0);
		$iclISOCountryList->add(242,"Fiji","FJI",0);
		$iclISOCountryList->add(246,"Finland","FIN",0);
		$iclISOCountryList->add(254,"French Guiana","GUF",0);
		$iclISOCountryList->add(258,"French Polynesia","PYF",0);
		$iclISOCountryList->add(260,"French Southern Territories","ATF",0);
		$iclISOCountryList->add(266,"Gabon","GAB",0);
		$iclISOCountryList->add(270,"Gambia","GMB",0);
		$iclISOCountryList->add(268,"Georgia","GEO",0);
		$iclISOCountryList->add(288,"Ghana","GHA",0);
		$iclISOCountryList->add(292,"Gibraltar","GIB",0);
		$iclISOCountryList->add(300,"Greece","GRC",0);
		$iclISOCountryList->add(304,"Greenland","GRL",0);
		$iclISOCountryList->add(308,"Grenada","GRD",0);
		$iclISOCountryList->add(312,"Guadaloupe","GLP",0);
		$iclISOCountryList->add(316,"Guam","GUM",0);
		$iclISOCountryList->add(320,"Guatemala","GTM",0);
		$iclISOCountryList->add(831,"Guernsey","GGY",0);
		$iclISOCountryList->add(324,"Guinea","GIN",0);
		$iclISOCountryList->add(624,"Guinea-Bissau","GNB",0);
		$iclISOCountryList->add(328,"Guyana","GUY",0);
		$iclISOCountryList->add(332,"Haiti","HTI",0);
		$iclISOCountryList->add(334,"Heard Island and McDonald Islands","HMD",0);
		$iclISOCountryList->add(340,"Honduras","HND",0);
		$iclISOCountryList->add(344,"Hong Kong","HKG",0);
		$iclISOCountryList->add(348,"Hungary","HUN",0);
		$iclISOCountryList->add(352,"Iceland","ISL",0);
		$iclISOCountryList->add(356,"India","IND",0);
		$iclISOCountryList->add(360,"Indonesia","IDN",0);
		$iclISOCountryList->add(364,"Iran","IRN",0);
		$iclISOCountryList->add(368,"Iraq","IRQ",0);
		$iclISOCountryList->add(372,"Ireland","IRL",0);
		$iclISOCountryList->add(833,"Isle of Man","IMN",0);
		$iclISOCountryList->add(376,"Israel","ISR",0);
		$iclISOCountryList->add(380,"Italy","ITA",0);
		$iclISOCountryList->add(388,"Jamaica","JAM",0);
		$iclISOCountryList->add(392,"Japan","JPN",0);
		$iclISOCountryList->add(832,"Jersey","JEY",0);
		$iclISOCountryList->add(400,"Jordan","JOR",0);
		$iclISOCountryList->add(398,"Kazakhstan","KAZ",0);
		$iclISOCountryList->add(404,"Kenya","KEN",0);
		$iclISOCountryList->add(296,"Kiribati","KIR",0);
		$iclISOCountryList->add(410,"Korea","KOR",0);
		$iclISOCountryList->add(408,"Korea","PRK",0);
		$iclISOCountryList->add(414,"Kuwait","KWT",0);
		$iclISOCountryList->add(417,"Kyrgyzstan","KGZ",0);
		$iclISOCountryList->add(418,"Lao","LAO",0);
		$iclISOCountryList->add(428,"Latvia","LVA",0);
		$iclISOCountryList->add(422,"Lebanon","LBN",0);
		$iclISOCountryList->add(426,"Lesotho","LSO",0);
		$iclISOCountryList->add(430,"Liberia","LBR",0);
		$iclISOCountryList->add(434,"Libyan Arab Jamahiriya","LBY",0);
		$iclISOCountryList->add(438,"Liechtenstein","LIE",0);
		$iclISOCountryList->add(440,"Lithuania","LTU",0);
		$iclISOCountryList->add(442,"Luxembourg","LUX",0);
		$iclISOCountryList->add(446,"Macau","MAC",0);
		$iclISOCountryList->add(807,"Macedonia","MKD",0);
		$iclISOCountryList->add(450,"Madagascar","MDG",0);
		$iclISOCountryList->add(454,"Malawi","MWI",0);
		$iclISOCountryList->add(458,"Malaysia","MYS",0);
		$iclISOCountryList->add(462,"Maldives","MDV",0);
		$iclISOCountryList->add(466,"Mali","MLI",0);
		$iclISOCountryList->add(470,"Malta","MLT",0);
		$iclISOCountryList->add(584,"Marshall Islands","MHL",0);
		$iclISOCountryList->add(474,"Martinique","MTQ",0);
		$iclISOCountryList->add(478,"Mauritania Islamic","MRT",0);
		$iclISOCountryList->add(480,"Mauritius","MUS",0);
		$iclISOCountryList->add(175,"Mayotte","MYT",0);
		$iclISOCountryList->add(484,"Mexico","MEX",0);
		$iclISOCountryList->add(583,"Micronesia","FSM",0);
		$iclISOCountryList->add(498,"Moldova","MDA",0);
		$iclISOCountryList->add(492,"Monaco","MCO",0);
		$iclISOCountryList->add(496,"Mongolia","MNG",0);
		$iclISOCountryList->add(499,"Montenegro","MNE",0);	
		$iclISOCountryList->add(500,"Montserrat","MSR",0);
		$iclISOCountryList->add(504,"Morocco","MAR",0);
		$iclISOCountryList->add(508,"Mozambique","MOZ",0);
		$iclISOCountryList->add(104,"Myanmar","MMR",0);
		$iclISOCountryList->add(516,"Namibia","NAM",0);
		$iclISOCountryList->add(520,"Nauru","NRU",0);
		$iclISOCountryList->add(524,"Nepal","NPL",0);
		$iclISOCountryList->add(528,"Netherlands","NLD",0);
		$iclISOCountryList->add(530,"Netherlands Antilles","ANT",0);
		$iclISOCountryList->add(540,"New Caledonia","NCL",0);
		$iclISOCountryList->add(554,"New Zealand","NZL",0);
		$iclISOCountryList->add(558,"Nicaragua","NIC",0);
		$iclISOCountryList->add(562,"Niger","NER",0);
		$iclISOCountryList->add(566,"Nigeria","NGA",0);
		$iclISOCountryList->add(570,"Niue","NIU",0);
		$iclISOCountryList->add(574,"Norfolk Island","NFK",0);
		$iclISOCountryList->add(580,"Northern Mariana Islands","MNP",0);
		$iclISOCountryList->add(578,"Norway","NOR",0);
		$iclISOCountryList->add(512,"Oman","OMN",0);
		$iclISOCountryList->add(586,"Pakistan","PAK",0);
		$iclISOCountryList->add(585,"Palau","PLW",0);
		$iclISOCountryList->add(275,"Palestine","PSE",0);	
		$iclISOCountryList->add(591,"Panama","PAN",0);
		$iclISOCountryList->add(598,"Papua New Guinea","PNG",0);
		$iclISOCountryList->add(600,"Paraguay","PRY",0);
		$iclISOCountryList->add(604,"Peru","PER",0);
		$iclISOCountryList->add(608,"Philippines","PHL",0);
		$iclISOCountryList->add(612,"Pitcairn","PCN",0);
		$iclISOCountryList->add(616,"Poland","POL",0);
		$iclISOCountryList->add(620,"Portugal","PRT",0);
		$iclISOCountryList->add(630,"Puerto Rico","PRI",0);
		$iclISOCountryList->add(634,"Qatar","QAT",0);
		$iclISOCountryList->add(638,"R�union","REU",0);
		$iclISOCountryList->add(642,"Romania","ROM",0);
		$iclISOCountryList->add(643,"Russian Federation","RUS",0);
		$iclISOCountryList->add(646,"Rwanda","RWA",0);
		$iclISOCountryList->add(652,"Saint Barth�lemy","BLM",0);
		$iclISOCountryList->add(654,"Saint Helena","SHN",0);
		$iclISOCountryList->add(659,"Saint Kitts and Nevis","KNA",0);
		$iclISOCountryList->add(662,"Saint Lucia","LCA",0);
		$iclISOCountryList->add(663,"Saint Martin (French part)","MAF",0);
		$iclISOCountryList->add(666,"Saint Pierre and Miquelon","SPM",0);
		$iclISOCountryList->add(670,"Saint Vincent and the Grenadines","VCT",0);
		$iclISOCountryList->add(882,"Samoa","WSM",0);
		$iclISOCountryList->add(674,"San Marino","SMR",0);
		$iclISOCountryList->add(678,"S�o Tom� and Pr�ncipe Democratic","STP",0);
		$iclISOCountryList->add(682,"Saudi Arabia","SAU",0);
		$iclISOCountryList->add(686,"Senegal","SEN",0);
		$iclISOCountryList->add(688,"Serbia","SRB",0);
		$iclISOCountryList->add(690,"Seychelles","SYC",0);
		$iclISOCountryList->add(694,"Sierra Leone","SLE",0);
		$iclISOCountryList->add(702,"Singapore","SGP",0);
		$iclISOCountryList->add(703,"Slovakia","SVK",0);
		$iclISOCountryList->add(705,"Slovenia","SVN",0);
		$iclISOCountryList->add(90,"Solomon Islands","SLB",0);
		$iclISOCountryList->add(706,"Somalia","SOM",0);
		$iclISOCountryList->add(710,"South Africa","ZAF",0);
		$iclISOCountryList->add(239,"South Georgia and the South Sandwich Islands","SGS",0);
		$iclISOCountryList->add(724,"Spain","ESP",0);
		$iclISOCountryList->add(144,"Sri Lanka","LKA",0);
		$iclISOCountryList->add(736,"Sudan","SDN",0);
		$iclISOCountryList->add(740,"Suriname","SUR",0);
		$iclISOCountryList->add(744,"Svalbard and Jan Mayen","SJM",0);
		$iclISOCountryList->add(748,"Swaziland","SWZ",0);
		$iclISOCountryList->add(752,"Sweden","SWE",0);
		$iclISOCountryList->add(756,"Switzerland","CHE",0);
		$iclISOCountryList->add(760,"Syrian Arab Republic","SYR",0);
		$iclISOCountryList->add(158,"Taiwan,","TWN",0);
		$iclISOCountryList->add(762,"Tajikistan","TJK",0);
		$iclISOCountryList->add(834,"Tanzania","TZA",0);
		$iclISOCountryList->add(764,"Thailand","THA",0);
		$iclISOCountryList->add(768,"Togo","TGO",0);
		$iclISOCountryList->add(772,"Tokelau","TKL",0);
		$iclISOCountryList->add(776,"Tonga","TON",0);
		$iclISOCountryList->add(780,"Trinidad and Tobago","TTO",0);
		$iclISOCountryList->add(788,"Tunisia","TUN",0);
		$iclISOCountryList->add(792,"Turkey","TUR",0);
		$iclISOCountryList->add(795,"Turkmenistan","TKM",0);
		$iclISOCountryList->add(796,"Turks and Caicos Islands","TCA",0);
		$iclISOCountryList->add(798,"Tuvalu","TUV",0);
		$iclISOCountryList->add(800,"Uganda","UGA",0);
		$iclISOCountryList->add(804,"Ukraine","UKR",0);
		$iclISOCountryList->add(784,"United Arab Emirates","ARE",0);
		$iclISOCountryList->add(581,"United States Minor Outlying Islands","UMI",0);
		$iclISOCountryList->add(858,"Uruguay Eastern","URY",0);
		$iclISOCountryList->add(860,"Uzbekistan","UZB",0);
		$iclISOCountryList->add(548,"Vanuatu","VUT",0);
		$iclISOCountryList->add(336,"Vatican City State","VAT",0);
		$iclISOCountryList->add(862,"Venezuela","VEN",0);
		$iclISOCountryList->add(704,"Vietnam","VNM",0);
		$iclISOCountryList->add(92,"Virgin Islands, British","VGB",0);
		$iclISOCountryList->add(850,"Virgin Islands, U.S.","VIR",0);
		$iclISOCountryList->add(876,"Wallis and Futuna","WLF",0);
		$iclISOCountryList->add(732,"Western Sahara","ESH",0);
		$iclISOCountryList->add(887,"Yemen","YEM",0);
		$iclISOCountryList->add(894,"Zambia","ZMB",0);
		$iclISOCountryList->add(716,"Zimbabwe","ZWE",0);
		
		return $iclISOCountryList;
	}
}

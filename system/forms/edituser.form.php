<form id="edituser" name="edituser" method="post" action="<?php echo $this->core->conf['conf']['path'] . "/information/save/" . $item; ?>">
<input type="hidden" name="id" value="update-account">
<input type="hidden" name="studentid" id="studentid" value="<?php echo $id; ?>"/>

<p> Static information such as names and birth dates can not be changed unless a request for change is filed with the administrator.</p>

<?php
if($this->core->role > 100){

	echo'<div class="toolbar">';

		if($this->core->function == "save"){
			echo'<a href="'.$this->core->conf['conf']['path'].'/information/show/'. $id .'">BACK TO PROFILE</a>';
		}

		echo'<a href="'.$this->core->conf['conf']['path'].'/password/change/'. $id .'">Change Users Password</a>
		</div>';

}
?>

<script type="text/javascript">

jQuery(document).ready(function(){

	jQuery('select').ddslick({width:480, height:300,
	    onSelected: function(selectedData){
	        console.log(selectedData.selectedData.text);
	    }
	});



});

$('.emergencycontacts').repeater({
						btnAddClass: 'addemergencycontact',
						btnRemoveClass: 'deleteemergencycontact',
						groupClass: 'emergencycontact',
						minItems: 1,
						maxItems: 0,
						startingIndex: 0,
						reindexOnDelete: true,
						repeatMode: 'append',
						animation: null,
						animationSpeed: 600,
						animationEasing: 'swing',
						clearValues: true
						});

</script>

<div class="formElement">

<table width="768" border="0" cellpadding="5" cellspacing="0">
<tr>
	<td width="205" height="28" bgcolor="#EEEEEE"><strong>Information</strong></td>
	<td width="200" bgcolor="#EEEEEE"><strong>Input fields</strong></td>
</tr>

<?php
if($this->core->role == 1000 && $this->core->item != "personal" ||$this->core->role == 850 && $this->core->item != "personal" || $this->core->role == 104 && $this->core->item != "personal" || $this->core->role == 106 && $this->core->item != "personal"|| $this->core->role == 103 && $this->core->item != "personal"){

echo '<tr>
	<td><strong>Role</strong></td>
	<td>
		<select name="role" id="role">
			'. $select .'
		</select></td>
</tr>

<tr>
		<td><strong>Examination center</strong></td>
		<td>
			<select name="examcenter" id="examcenter">
				<option value="0">- Exam center -</option>
				<option value="Chipata Center">Chipata Center</option>
				<option value="Choma Center">Choma Center</option>
				<option value="Kabwe Center" selected="selected">Kabwe Center</option>
				<option value="Kasama Center">Kasama Center</option>
				<option value="Lusaka Center">Lusaka Center</option>
				<option value="Ndola Center">Ndola Center</option>
			</select>
		</td>
</tr>

<tr>
	<td><strong>Delivery method</strong></td>
	<td>
		<select name="method" id="method">
			<option value="'. $method .'">- '. $method .' -</option>
			<option value="Block">Block</option>
			<option value="Fulltime">Fulltime</option>
			<option value="Distance">Distance</option>
			<option value="Parttime">Part-time</option>
			<option value="Parallel">Parallel</option>
		</select></td>
</tr>
<tr>
	<td><strong>User status </strong></td>
	<td>
		<select name="status" id="status">
			<option value="'. $status .'">- '. $status .' -</option>
			<option value="Approved">Approved</option>
			<option value="Employed">Employed</option>
			<option value="Retired">Retired</option>
			<option value="Fired">Fired</option>
			<option value="Suspended">Suspended</option>
			<option value="Deceased">Deceased</option>
			<option value="Requesting">Requesting</option>
			<option value="Enrolled">Enrolled</option>
			<option value="Graduated">Graduated</option>
			<option value="Dismissed">Dismissed</option>
			<option value="Rejected">Rejected</option>
			<option value="Failed">Failed</option>
			<option value="New">New</option>
			<option value="Expelled">Expelled</option>

		</select></td>

</tr>';
}
?>

<?php
if(($this->core->role == 1000 && $this->core->item != "personal") || ($this->core->role == 104 && $this->core->item != "personal") || ($this->core->role == 106 && $this->core->item != "personal")|| ($this->core->role == 850 && $this->core->item != "personal")){

echo '<tr>
	<td><strong>Study</strong></td>
	<td>
		<select name="study" id="study">
			<option value="00">- CHANGE STUDY -</option>
			'. $selectstudy .'
		</select></td>

</tr>';



//echo '<tr>
	//<td><strong>Year of study</strong></td>
	//<td>
	///	<select name="year" id="year">
	//		<option value="0" selected>- CHANGE YEAR OF STUDY -</option>
	//		<option value="1" selected> Year 1 </option>
	//		<option value="2" selected> Year 2 </option>
	//		<option value="3" selected> Year 3 </option>
	////		<option value="4" selected> Year 4 </option>
	//	</select></td>

//</tr>';
}
?>
<tr>
	<td><strong>Surname </strong></td>
	<td>
		<input type="hidden" name="new" value="<?php echo $new; ?>" />
		<input type="text" name="surname" value="<?php echo $surname; ?>" /></td>

</tr>
<tr>
	<td height="19"><strong>First Name</strong></td>
	<td>
		<input type="text" name="firstname" value="<?php echo $firstname; ?>" /></td>

</tr>
<tr>
	<td><strong>Middle name</strong></td>
	<td>
		<input type="text" name="middlename" value="<?php echo $middlename; ?>" /></td>

</tr>
<tr>
	<td><strong>Sex (Gender)</strong></td>
	<td>
		<select name="sex" id="sex">
			<option selected="selected" value="<?php echo $gender; ?>">- <?php echo $gender; ?> -</option>
			<option value="Male">Male</option>
			<option value="Female">Female</option>
			<option value="Other">Other</option>
		</select></td>
</tr>


<tr>
	<td><strong>Date of Birth</strong></td>
	<td>
		<input type="date" id="name" name="dob" value="<?php echo $dob; ?>">
	</td>
</tr>

<tr>
	<td><strong>Government ID</strong></td>
	<td>
		<input type="text" name="nationalid" value="<?php echo $NID; ?>" />
	</td>
</tr>
<tr>
	<td><strong>Nationality</strong></td>
	<td>
		<select name="nationality">
			<option value="<?php echo $nationality; ?>" selected="selected">- <?php echo $nationality; ?> -</option>
			<option value="Other">Other</option>
			<option value="afghan">Afghan</option>
			<option value="albanian">Albanian</option>
			<option value="algerian">Algerian</option>
			<option value="american">American</option>
			<option value="andorran">Andorran</option>
			<option value="angolan">Angolan</option>
			<option value="antiguans">Antiguans</option>
			<option value="argentinean">Argentinean</option>
			<option value="armenian">Armenian</option>
			<option value="australian">Australian</option>
			<option value="austrian">Austrian</option>
			<option value="azerbaijani">Azerbaijani</option>
			<option value="bahamian">Bahamian</option>
			<option value="bahraini">Bahraini</option>
			<option value="bangladeshi">Bangladeshi</option>
			<option value="barbadian">Barbadian</option>
			<option value="barbudans">Barbudans</option>
			<option value="batswana">Batswana</option>
			<option value="belarusian">Belarusian</option>
			<option value="belgian">Belgian</option>
			<option value="belizean">Belizean</option>
			<option value="beninese">Beninese</option>
			<option value="bhutanese">Bhutanese</option>
			<option value="bolivian">Bolivian</option>
			<option value="bosnian">Bosnian</option>
			<option value="brazilian">Brazilian</option>
			<option value="british">British</option>
			<option value="bruneian">Bruneian</option>
			<option value="bulgarian">Bulgarian</option>
			<option value="burkinabe">Burkinabe</option>
			<option value="burmese">Burmese</option>
			<option value="burundian">Burundian</option>
			<option value="cambodian">Cambodian</option>
			<option value="cameroonian">Cameroonian</option>
			<option value="canadian">Canadian</option>
			<option value="cape verdean">Cape Verdean</option>
			<option value="central african">Central African</option>
			<option value="chadian">Chadian</option>
			<option value="chilean">Chilean</option>
			<option value="chinese">Chinese</option>
			<option value="colombian">Colombian</option>
			<option value="comoran">Comoran</option>
			<option value="congolese">Congolese</option>
			<option value="costa rican">Costa Rican</option>
			<option value="croatian">Croatian</option>
			<option value="cuban">Cuban</option>
			<option value="cypriot">Cypriot</option>
			<option value="czech">Czech</option>
			<option value="danish">Danish</option>
			<option value="djibouti">Djibouti</option>
			<option value="dominican">Dominican</option>
			<option value="dutch">Dutch</option>
			<option value="east timorese">East Timorese</option>
			<option value="ecuadorean">Ecuadorean</option>
			<option value="egyptian">Egyptian</option>
			<option value="emirian">Emirian</option>
			<option value="equatorial guinean">Equatorial Guinean</option>
			<option value="eritrean">Eritrean</option>
			<option value="estonian">Estonian</option>
			<option value="ethiopian">Ethiopian</option>
			<option value="fijian">Fijian</option>
			<option value="filipino">Filipino</option>
			<option value="finnish">Finnish</option>
			<option value="french">French</option>
			<option value="gabonese">Gabonese</option>
			<option value="gambian">Gambian</option>
			<option value="georgian">Georgian</option>
			<option value="german">German</option>
			<option value="ghanaian">Ghanaian</option>
			<option value="greek">Greek</option>
			<option value="grenadian">Grenadian</option>
			<option value="guatemalan">Guatemalan</option>
			<option value="guinea-bissauan">Guinea-Bissauan</option>
			<option value="guinean">Guinean</option>
			<option value="guyanese">Guyanese</option>
			<option value="haitian">Haitian</option>
			<option value="herzegovinian">Herzegovinian</option>
			<option value="honduran">Honduran</option>
			<option value="hungarian">Hungarian</option>
			<option value="icelander">Icelander</option>
			<option value="indian">Indian</option>
			<option value="indonesian">Indonesian</option>
			<option value="iranian">Iranian</option>
			<option value="iraqi">Iraqi</option>
			<option value="irish">Irish</option>
			<option value="israeli">Israeli</option>
			<option value="italian">Italian</option>
			<option value="ivorian">Ivorian</option>
			<option value="jamaican">Jamaican</option>
			<option value="japanese">Japanese</option>
			<option value="jordanian">Jordanian</option>
			<option value="kazakhstani">Kazakhstani</option>
			<option value="kenyan">Kenyan</option>
			<option value="kittian and nevisian">Kittian and Nevisian</option>
			<option value="kuwaiti">Kuwaiti</option>
			<option value="kyrgyz">Kyrgyz</option>
			<option value="laotian">Laotian</option>
			<option value="latvian">Latvian</option>
			<option value="lebanese">Lebanese</option>
			<option value="liberian">Liberian</option>
			<option value="libyan">Libyan</option>
			<option value="liechtensteiner">Liechtensteiner</option>
			<option value="lithuanian">Lithuanian</option>
			<option value="luxembourger">Luxembourger</option>
			<option value="macedonian">Macedonian</option>
			<option value="malagasy">Malagasy</option>
			<option value="malawian">Malawian</option>
			<option value="malaysian">Malaysian</option>
			<option value="maldivan">Maldivan</option>
			<option value="malian">Malian</option>
			<option value="maltese">Maltese</option>
			<option value="marshallese">Marshallese</option>
			<option value="mauritanian">Mauritanian</option>
			<option value="mauritian">Mauritian</option>
			<option value="mexican">Mexican</option>
			<option value="micronesian">Micronesian</option>
			<option value="moldovan">Moldovan</option>
			<option value="monacan">Monacan</option>
			<option value="mongolian">Mongolian</option>
			<option value="moroccan">Moroccan</option>
			<option value="mosotho">Mosotho</option>
			<option value="motswana">Motswana</option>
			<option value="mozambican">Mozambican</option>
			<option value="namibian">Namibian</option>
			<option value="nauruan">Nauruan</option>
			<option value="nepalese">Nepalese</option>
			<option value="new zealander">New Zealander</option>
			<option value="ni-vanuatu">Ni-Vanuatu</option>
			<option value="nicaraguan">Nicaraguan</option>
			<option value="nigerien">Nigerien</option>
			<option value="north korean">North Korean</option>
			<option value="northern irish">Northern Irish</option>
			<option value="norwegian">Norwegian</option>
			<option value="omani">Omani</option>
			<option value="pakistani">Pakistani</option>
			<option value="palauan">Palauan</option>
			<option value="panamanian">Panamanian</option>
			<option value="papua new guinean">Papua New Guinean</option>
			<option value="paraguayan">Paraguayan</option>
			<option value="peruvian">Peruvian</option>
			<option value="polish">Polish</option>
			<option value="portuguese">Portuguese</option>
			<option value="qatari">Qatari</option>
			<option value="romanian">Romanian</option>
			<option value="russian">Russian</option>
			<option value="rwandan">Rwandan</option>
			<option value="saint lucian">Saint Lucian</option>
			<option value="salvadoran">Salvadoran</option>
			<option value="samoan">Samoan</option>
			<option value="san marinese">San Marinese</option>
			<option value="sao tomean">Sao Tomean</option>
			<option value="saudi">Saudi</option>
			<option value="scottish">Scottish</option>
			<option value="senegalese">Senegalese</option>
			<option value="serbian">Serbian</option>
			<option value="seychellois">Seychellois</option>
			<option value="sierra leonean">Sierra Leonean</option>
			<option value="singaporean">Singaporean</option>
			<option value="slovakian">Slovakian</option>
			<option value="slovenian">Slovenian</option>
			<option value="solomon islander">Solomon Islander</option>
			<option value="somali">Somali</option>
			<option value="south african">South African</option>
			<option value="south korean">South Korean</option>
			<option value="spanish">Spanish</option>
			<option value="sri lankan">Sri Lankan</option>
			<option value="sudanese">Sudanese</option>
			<option value="surinamer">Surinamer</option>
			<option value="swazi">Swazi</option>
			<option value="swedish">Swedish</option>
			<option value="swiss">Swiss</option>
			<option value="syrian">Syrian</option>
			<option value="taiwanese">Taiwanese</option>
			<option value="tajik">Tajik</option>
			<option value="tanzanian">Tanzanian</option>
			<option value="thai">Thai</option>
			<option value="togolese">Togolese</option>
			<option value="tongan">Tongan</option>
			<option value="trinidadian or tobagonian">Trinidadian or Tobagonian</option>
			<option value="tunisian">Tunisian</option>
			<option value="turkish">Turkish</option>
			<option value="tuvaluan">Tuvaluan</option>
			<option value="ugandan">Ugandan</option>
			<option value="ukrainian">Ukrainian</option>
			<option value="uruguayan">Uruguayan</option>
			<option value="uzbekistani">Uzbekistani</option>
			<option value="venezuelan">Venezuelan</option>
			<option value="vietnamese">Vietnamese</option>
			<option value="welsh">Welsh</option>
			<option value="yemenite">Yemenite</option>
			<option value="zambian">Zambian</option>
			<option value="zimbabwean">Zimbabwean</option>
		</select></td>
</tr>
<tr>
	<td><strong>Streetname / Plot number</strong></td>
	<td>
		<input type="text" name="streetname" id="streetname" value="<?php echo $street; ?>"/></td>
</tr>
<tr>
	<td><strong>P.O Box</strong></td>
	<td>
		<input type="text" name="postalcode" id="postalcode" value="<?php echo $postal; ?>"/></td>
</tr>
<tr>
	<td><strong>City/Town</strong></td>
	<td>
		<input type="text" name="town" id="town" value="<?php echo $town; ?>"/></td>
</tr>
<tr>
<td><strong>Country</strong></td>
<td>
<select name="country" id="country">
<option selected="selected" value="<?php echo $country; ?>">- <?php echo $country; ?> -</option>
<option value="USA">USA</option>
<option value="UK">UK</option>
<option value="Albania">Albania</option>
<option value="Algeria">Algeria</option>
<option value="American Samoa">American Samoa</option>
<option value="Andorra">Andorra</option>
<option value="Angola">Angola</option>
<option value="Anguilla">Anguilla</option>
<option value="Antigua">Antigua</option>
<option value="Argentina">Argentina</option>
<option value="Armenia">Armenia</option>
<option value="Aruba">Aruba</option>
<option value="Australia">Australia</option>
<option value="Austria">Austria</option>
<option value="Azerbaijan">Azerbaijan</option>
<option value="Bahamas">Bahamas</option>
<option value="Bahrain">Bahrain</option>
<option value="Bangladesh">Bangladesh</option>
<option value="Barbados">Barbados</option>
<option value="Barbuda">Barbuda</option>
<option value="Belgium">Belgium</option>
<option value="Belize">Belize</option>
<option value="Benin">Benin</option>
<option value="Bermuda">Bermuda</option>
<option value="Bhutan">Bhutan</option>
<option value="Bolivia">Bolivia</option>
<option value="Bonaire">Bonaire</option>
<option value="Botswana">Botswana</option>
<option value="Brazil">Brazil</option>
<option value="Virgin islands">British Virgin isl.</option>
<option value="Brunei">Brunei</option>
<option value="Bulgaria">Bulgaria</option>
<option value="Burundi">Burundi</option>
<option value="Cambodia">Cambodia</option>
<option value="Cameroon">Cameroon</option>
<option value="Canada">Canada</option>
<option value="Cape Verde">Cape Verde</option>
<option value="Cayman isl">Cayman Islands</option>
<option value="Central African Rep">Central African Rep.</option>
<option value="Chad">Chad</option>
<option value="Channel isl">Channel Islands</option>
<option value="Chile">Chile</option>
<option value="China">China</option>
<option value="Colombia">Colombia</option>
<option value="Congo">Congo</option>
<option value="cook isl">Cook Islands</option>
<option value="Costa Rica">Costa Rica</option>
<option value="Croatia">Croatia</option>
<option value="Curacao">Curacao</option>
<option value="Cyprus">Cyprus</option>
<option value="Czech Republic">Czech Republic</option>
<option value="Denmark">Denmark</option>
<option value="Djibouti">Djibouti</option>
<option value="Dominica">Dominica</option>
<option value="Dominican Republic">Dominican Republic</option>
<option value="Ecuador">Ecuador</option>
<option value="Egypt">Egypt</option>
<option value="El Salvador">El Salvador</option>
<option value="Equatorial Guinea">Equatorial Guinea</option>
<option value="Eritrea">Eritrea</option>
<option value="Estonia">Estonia</option>
<option value="ethiopia">Ethiopia</option>
<option value="Faeroe isl">Faeroe Islands</option>
<option value="Fiji">Fiji</option>
<option value="Finland">Finland</option>
<option value="France">France</option>
<option value="French Guiana">French Guiana</option>
<option value="French Polynesia">French Polynesia</option>
<option value="Gabon">Gabon</option>
<option value="Gambia">Gambia</option>
<option value="Georgia">Georgia</option>
<option value="Gemany">Germany</option>
<option value="Ghana">Ghana</option>
<option value="Gibraltar">Gibraltar</option>
<option value="GB">Great Britain</option>
<option value="Greece">Greece</option>
<option value="Greenland">Greenland</option>
<option value="Grenada">Grenada</option>
<option value="Guadeloupe">Guadeloupe</option>
<option value="Guam">Guam</option>
<option value="Guatemala">Guatemala</option>
<option value="Guinea">Guinea</option>
<option value="Guinea Bissau">Guinea Bissau</option>
<option value="Guyana">Guyana</option>
<option value="Haiti">Haiti</option>
<option value="Honduras">Honduras</option>
<option value="Hong Kong">Hong Kong</option>
<option value="Hungary">Hungary</option>
<option value="Iceland">Iceland</option>
<option value="India">India</option>
<option value="Indonesia">Indonesia</option>
<option value="Irak">Irak</option>
<option value="Iran">Iran</option>
<option value="Ireland">Ireland</option>
<option value="Northern Ireland">Ireland, Northern</option>
<option value="Israel">Israel</option>
<option value="Italy">Italy</option>
<option value="Ivory Coast">Ivory Coast</option>
<option value="Jamaica">Jamaica</option>
<option value="Japan">Japan</option>
<option value="Jordan">Jordan</option>
<option value="Kazakhstan">Kazakhstan</option>
<option value="Kenya">Kenya</option>
<option value="Kuwait">Kuwait</option>
<option value="Kyrgyzstan">Kyrgyzstan</option>
<option value="Latvia">Latvia</option>
<option value="Lebanon">Lebanon</option>
<option value="Liberia">Liberia</option>
<option value="Liechtenstein">Liechtenstein</option>
<option value="Lithuania">Lithuania</option>
<option value="Luxembourg">Luxembourg</option>
<option value="Macau">Macau</option>
<option value="Macedonia">Macedonia</option>
<option value="Madagascar">Madagascar</option>
<option value="Malawi">Malawi</option>
<option value="Malaysia">Malaysia</option>
<option value="Maldives">Maldives</option>
<option value="Mali">Mali</option>
<option value="Malta">Malta</option>
<option value="Marshall islands">Marshall Islands</option>
<option value="Martinique">Martinique</option>
<option value="Mauritania">Mauritania</option>
<option value="Mauritius">Mauritius</option>
<option value="Mexico">Mexico</option>
<option value="Micronesia">Micronesia</option>
<option value="Moldova">Moldova</option>
<option value="Monaco">Monaco</option>
<option value="Mongolia">Mongolia</option>
<option value="Montserrat">Montserrat</option>
<option value="Morocco">Morocco</option>
<option value="Mozambique">Mozambique</option>
<option value="Myanmar">Myanmar/Burma</option>
<option value="Namibia">Namibia</option>
<option value="Nepal">Nepal</option>
<option value="Netherlands">Netherlands</option>
<option value="Netherlands Antilles">Netherlands Antilles</option>
<option value="New Caledonia">New Caledonia</option>
<option value="New Zealand">New Zealand</option>
<option value="Nicaragua">Nicaragua</option>
<option value="Niger">Niger</option>
<option value="Nigeria">Nigeria</option>
<option value="Norway">Norway</option>
<option value="Oman">Oman</option>
<option value="Palau">Palau</option>
<option value="Panama">Panama</option>
<option value="Papua New Guinea">Papua New Guinea</option>
<option value="Paraguay">Paraguay</option>
<option value="Peru">Peru</option>
<option value="Philippines">Philippines</option>
<option value="Poland">Poland</option>
<option value="Portugal">Portugal</option>
<option value="Puerto Rico">Puerto Rico</option>
<option value="Qatar">Qatar</option>
<option value="Reunion">Reunion</option>
<option value="Rwanda">Rwanda</option>
<option value="Saba">Saba</option>
<option value="Saipan">Saipan</option>
<option value="Saudi Arabia">Saudi Arabia</option>
<option value="Scotland">Scotland</option>
<option value="Senegal">Senegal</option>
<option value="seychelles">Seychelles</option>
<option value="Sierra Leone">Sierra Leone</option>
<option value="Singapore">Singapore</option>
<option value="Slovac Republic">Slovak Republic</option>
<option value="Slovenia">Slovenia</option>
<option value="South Africa">South Africa</option>
<option value="South Korea">South Korea</option>
<option value="Spain">Spain</option>
<option value="Sri Lanka">Sri Lanka</option>
<option value="Sudan">Sudan</option>
<option value="Suriname">Suriname</option>
<option value="Swaziland">Swaziland</option>
<option value="Sweden">Sweden</option>
<option value="Switzerland">Switzerland</option>
<option value="Syria">Syria</option>
<option value="Taiwan">Taiwan</option>
<option value="Tanzania">Tanzania</option>
<option value="Thailand">Thailand</option>
<option value="Togo">Togo</option>
<option value="Trinidad-Tobago">Trinidad-Tobago</option>
<option value="Tunesia">Tunisia</option>
<option value="Turkey">Turkey</option>
<option value="Turkmenistan">Turkmenistan</option>
<option value="United Arab Emirates">United Arab Emirates</option>
<option value="U.S. Virgin isl">U.S. Virgin Islands</option>
<option value="USA">U.S.A.</option>
<option value="Uganda">Uganda</option>
<option value="United Kingdom">United Kingdom</option>
<option value="Urugay">Uruguay</option>
<option value="Uzbekistan">Uzbekistan</option>
<option value="Vanuatu">Vanuatu</option>
<option value="Vatican City">Vatican City</option>
<option value="Venezuela">Venezuela</option>
<option value="Vietnam">Vietnam</option>
<option value="Wales">Wales</option>
<option value="Yemen">Yemen</option>
<option value="Zaire">Zaire</option>
<option value="Zambia">Zambia</option>
<option value="Zimbabwe">Zimbabwe</option>
</select></td>
</tr>
<tr>
	<td><strong>Phone Number</strong></td>
	<td>
		<input type="text" name="celphone" value="<?php echo $celphone; ?>"/></td>
</tr>
<tr>
	<td><strong>Dissability</strong></td>
	<td>
		<select name="dissability" id="dissability">
			<option value="No" selected="selected">No</option>
			<option value="Yes">Yes</option>
		</select>
		<select name="dissabilitytype" id="dissabilitytype">
			<option value="" selected="selected">No Dissability</option>
			<option value="blind/part.sight">Blind or partially sighted</option>
			<option value="deaf">Deaf</option>
			<option value="speech">Speech impairment</option>
			<option value="physical">Physical</option>
			<option value="mental">Mental</option>
		</select><br/>
		<input name="otherdisability" type="text" id="otherdisability" value="<?php echo $disability; ?>"/>
	</td>

</tr>
<tr>
	<td><strong>Private Email Address</strong></td>
	<td>
		<input type="text" name="email" id="email" value="<?php echo $email; ?>"/></td>
</tr>
<tr>
	<td><strong>Marital Status</strong></td>
	<td>
		<select name="mstatus" id="mstatus">
			<option value="<?php echo $relation; ?>" selected="selected">- <?php echo $relation; ?> -</option>
			<option value="Married">Married</option>
			<option value="Single">Single</option>
			<option value="Divorced">Divorced</option>
			<option value="Widowed">Widowed</option>
		</select></td>
</tr>
</table>

<p>&nbsp;</p>

		<?php

		$sql = "SELECT * FROM `emergency-contact` WHERE `emergency-contact`.`StudentID` = '" . $item . "'";
		$run = $this->core->database->doSelectQuery($sql);

		$n=0;
		echo '<div class="emergencycontacts formElement">
					<h2><strong>Emergency contact information</strong></h2>
					<p>In this section you can provide contact information that should be used in case of an emergency, in general this should be a family member or legal guardian. Use the add button to add as many emergency contacts as you wish.</p><div>
					<div id="contact-element">';
					
		while ($row = $run->fetch_assoc()) {



			$name = $row['FullName'];
			$relation = $row['Relationship'];
			$phone = $row['PhoneNumber'];
			$street = $row['Street'];
			$town = $row['Town'];
			$postalcode = $row['Postalcode'];

			
			

			echo'<div class="emergencycontact">
				<input type="hidden" name="contact[0][id]" data-pattern-name="contact[++][id]" data-pattern-id="contact_++_index"/>

					<table width="768" height="135" border="0" cellpadding="5" cellspacing="0">
						<tr>
							<td width="205" height="28" bgcolor="#EEEEEE"><strong>Information</strong></td>
							<td width="200" bgcolor="#EEEEEE"><strong>Input fields</strong></td>
							<td bgcolor="#EEEEEE">
								<div class="deleteemergencycontact">
									<a href="#">Remove section</a>
								</div>
							</td>
						</tr>
						<tr>
							<td height="19"><strong>Full Name</strong></td>
							<td>
								<input type="text" name="econtact[0][fullname]" value="' . $name . '" id="econtact_0_fullname" data-pattern-name="econtact[++][fullname]" data-pattern-id="contact_++_fullname"/></td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td height="19"><strong>Relationship</strong></td>
							<td>
								<select name="econtact[0][relationship]" id="econtact_0_relationship" data-pattern-name="econtact[++][relationship]" data-pattern-id="contact_++_relationship">
									<option value="' . $relation . '" selected="selected">- ' . $relation . ' -</option>
									<option value="Spouse" selected="selected">Spouse</option>
									<option value="Parent">Parent</option>
									<option value="Guardian">Legal Guardian</option>
									<option value="Uncle">Uncle</option>
									<option value="Son">Son</option>
									<option value="Daughter">Daughter</option>
									<option value="Grandparrent">Grandparrent</option>
									<option value="Aunt">Aunt</option>
									<option value="Cousing">Cousin</option>
									<option value="Sibling">Sibling</option>
								</select>

							</td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td height="22"><strong>Phone Number</strong></td>
							<td>
								<input type="text" name="econtact[0][phonenumber]" value="' . $phone . '" id="econtact_0_phonenumber" data-pattern-name="econtact[++][phonenumber]" data-pattern-id="contact_++_phonenumber"/></td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td height="22"><strong>Street</strong></td>
							<td>
								<input type="text" name="econtact[0][street]" value="' . $street . '" id="econtact_0_street" data-pattern-name="econtact[++][street]" data-pattern-id="contact_++_street"/></td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td height="22"><strong>Town</strong></td>
							<td><input type="text" name="econtact[0][town]" value="' . $town . '" id="econtact_0_town" data-pattern-name="econtact[++][town]" data-pattern-id="contact_++_town"/></td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td height="22"><strong>Postalcode</strong></td>
							<td><input type="text" name="econtact[0][postalcode]" value="' . $postalcode . '" id="econtact_0_postalcode" data-pattern-name="econtact[++][postalcode]" data-pattern-id="contact_++_postalcode"/></td>
							<td>&nbsp;</td>
						</tr>
					</table>
					</div>

					<div class="addemergencycontact">
						<a href="#"> <img src="'.  $this->core->fullTemplatePath .'/images/plus.png" width="16" height="16"/> Add another emergency contact </a>
					</div>

				</div>';

			if($n==0){
				echo"<script type=\"text/javascript\">
					$('.emergencycontacts').repeater({
					btnAddClass: 'addemergencycontact',
					btnRemoveClass: 'deleteemergencycontact',
					groupClass: 'emergencycontact',
					minItems: 1,
					maxItems: 0,
					startingIndex: 0,
					reindexOnDelete: true,
					repeatMode: 'append',
					animation: null,
					animationSpeed: 600,
					animationEasing: 'swing',
					clearValues: true
					});
				</script>";
			}
			$n++;

			

		}
		echo'</div></div>';
		/*if($n==0){
			

			echo'<div class="emergencycontact">
				<input type="hidden" name="contact[0][id]" data-pattern-name="contact[++][id]" data-pattern-id="contact_++_index"/>

					<table width="768" height="135" border="0" cellpadding="5" cellspacing="0">
						<tr>
							<td width="205" height="28" bgcolor="#EEEEEE"><strong>Information</strong></td>
							<td width="200" bgcolor="#EEEEEE"><strong>Input fields</strong></td>
							<td bgcolor="#EEEEEE">
								<div class="deleteemergencycontact">
									<a href="#">Remove section</a>
								</div>
							</td>
						</tr>
						<tr>
							<td height="19"><strong>Full Name</strong></td>
							<td>
								<input type="text" name="econtact[0][fullname]" value="" id="econtact_0_fullname" data-pattern-name="econtact[++][fullname]" data-pattern-id="contact_++_fullname"/></td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td height="19"><strong>Relationship</strong></td>
							<td>
								<select name="econtact[0][relationship]" id="econtact_0_relationship" data-pattern-name="econtact[++][relationship]" data-pattern-id="contact_++_relationship">
									<option value="Spouse" selected="selected">Spouse</option>
									<option value="Parent">Parent</option>
									<option value="Guardian">Legal Guardian</option>
									<option value="Uncle">Uncle</option>
									<option value="Son">Son</option>
									<option value="Daughter">Daughter</option>
									<option value="Grandparrent">Grandparrent</option>
									<option value="Aunt">Aunt</option>
									<option value="Cousing">Cousin</option>
									<option value="Sibling">Sibling</option>
								</select>

							</td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td height="22"><strong>Phone Number</strong></td>
							<td>
								<input type="text" name="econtact[0][phonenumber]" value="" id="econtact_0_phonenumber" data-pattern-name="econtact[++][phonenumber]" data-pattern-id="contact_++_phonenumber"/></td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td height="22"><strong>Street</strong></td>
							<td>
								<input type="text" name="econtact[0][street]" value="" id="econtact_0_street" data-pattern-name="econtact[++][street]" data-pattern-id="contact_++_street"/></td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td height="22"><strong>Town</strong></td>
							<td><input type="text" name="econtact[0][town]" value="" id="econtact_0_town" data-pattern-name="econtact[++][town]" data-pattern-id="contact_++_town"/></td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td height="22"><strong>Postalcode</strong></td>
							<td><input type="text" name="econtact[0][postalcode]" value="" id="econtact_0_postalcode" data-pattern-name="econtact[++][postalcode]" data-pattern-id="contact_++_postalcode"/></td>
							<td>&nbsp;</td>
						</tr>
					</table>
					</div>';
				echo'<div class="addemergencycontact">
						<a href="#"> <img src="'.  $this->core->fullTemplatePath .'/images/plus.png" width="16" height="16"/> Add emergency contact </a>
					</div>';
			}*/
		?>

<div style="padding-left: 30px;"> <input type="submit" name="button" id="button" value="Update account information">

</form>

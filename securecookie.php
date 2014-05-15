<?
/**
 * PHP Class: SecureCookie
 * @author Aikar <aikar@windower.net>
 * @desc Provides a Secure way of using cookies so the end user is not able to edit the cookies or even see the data it contains.
 * @notes You will not want to use large ammounts of stored data on 1 object, as each object is stored in only 1 cookie on the users system
 *                 If you use too many, the encrypted data will become too large to store in the cookie and will be corrupted or may not set at all.
 *                 Use multiple objects if you need alot of data stored.
 *
 * @example:
 * $C = new SecureCookie('mysecretword','SomeCookieID',time()+3600,'/','.mydomain.com');
 * $C->Set('test','hello');
 * echo 'value of test is: ' . $C->Get('test') . '<br />';
 *
 **/
 
class SecureCookie {
    var $_CookieObject;
    var $_CookieID;
    var $_Expire;
    var $_EncryptionPassword;
    var $_Path;
    var $_Domain;
    var $_Secure;
    /***
     * Create Object. 
     * EncryptionPassword: (required) The password to encrypt the cookie.
     *        - NOTE: Changing this password after a cookie has been set will make the cookie fail to be read.
     * CookieID: (required) A unique name for the cookie. This is the ACTUAL cookie name. Do not use the name of a cookie
     *                         already in use on your website.
     * expire, domain, path, secure: Standard Cookie Paramaters.
     *        - NOTE: This applies to all values in the object!
     *                       You will need multiple objects for different parameters.
     ***/
    function SecureCookie($EncryptionPassword,$CookieID,$expire=false,$path=false,$domain=false,$secure=false) {
        // Store all our passed parameters.
        $this->_Expire=$expire;
        $this->_EncryptionPassword=$EncryptionPassword;
        $this->_CookieID=$CookieID;
        $this->_Path=$path;
        $this->_Domain=$domain;
        $this->_Secure=$secure;
        // Does this cookie ID exists?
        if(isset($_COOKIE[$CookieID])) {        
            // Decrypt it.
            $obj=unserialize($this->_Decrypt($_COOKIE[$this->_CookieID],$this->_EncryptionPassword));
            // The best way to see if a successful decryption, check a stored value to see if the passwords match.
            // A failed decryption would corrupt it and return bad data.
            if($obj['____ENCRYPTIONPASSWORD'] == md5($this->_EncryptionPassword)) {
                // Its good! Lets use it.
                $this->_CookieObject=$obj;
            }else{
                // Failed! Developer may of changed the encryption password.
                // Open up with a blank object and set our verification field.
                $this->_CookieObject=array('____ENCRYPTIONPASSWORD' => md5($this->_EncryptionPassword));
            }
        }else{
            // Cookie doesn't exists, Open up with a blank object and set our verification field.
            $this->_CookieObject=array('____ENCRYPTIONPASSWORD' => md5($this->_EncryptionPassword));
        }
        // Cleanup obj.
        unset($obj);
    }
    // Alias: SetCookie()
    function Set($name,$value) {
        $this->SetCookie($name,$value);
    }
    // Alias: GetCookie()
    function Get($name,$default = null) {
        return $this->GetCookie($name,$default);    
    }
    // Alias: DeleteCookie()
    function Del($name) {
        $this->DeleteCookie($name);
    }
    /**
     * Sets the value of the cookie.
     **/
    function SetCookie($name,$value) {
        // Check to make sure not using invalid name.
        if($name != '____ENCRYPTIONPASSWORD') {
            // Make a copy of our object
            $obj=$this->_CookieObject;
            // Be sure the encryption password is in the object for password verifcation.
            $obj['____ENCRYPTIONPASSWORD'] = md5($this->_EncryptionPassword);
            // Set our new value
            $obj[$name]=$value;
            // Restore the new data to the object
            $this->_CookieObject=$obj;
            // Lets reuse $obj to store our encrypted object
            $obj=$this->_Encrypt(serialize($obj),$this->_EncryptionPassword);
            // Set the actual cookie with our encrypted data.
            setcookie($this->_CookieID,$obj,$this->_Expire,$this->_Path,$this->_Domain,$this->_Secure);
            // Set the cookie global so the data is usable on this page load.
            $_COOKIE[$this->_CookieID] = $obj;
            // Cleanup obj.
            unset($obj);
        }else{
            // See if your trying to intentionally break my script smile.gif Why else would you name it this!
            die('INVALID COOKIE NAME. YOU MAY NOT USE "____ENCRYPTIONPASSWORD" AS YOUR COOKIE NAME');
        }
    }
    /**
     * Retrieves the specified name from the object.
     **/
    function GetCookie($name,$default=null) {
        // Check to make sure not using invalid name.
        if($name != '____ENCRYPTIONPASSWORD') {
            // Make a copy of object
            $obj=$this->_CookieObject;
            // Return the value.
            return isset($obj[$name]) ? $obj[$name] : $default;
        }else{
            // See if your trying to intentionally break my script smile.gif Why else would you name it this!
            die('INVALID COOKIE NAME. YOU MAY NOT USE "____ENCRYPTIONPASSWORD" AS YOUR COOKIE NAME');
        }
    }
    /**
     * Deletes the specified name from the object.
     **/
    function DeleteCookie($name) {
        // Check to make sure not using invalid name.
        if($name != '____ENCRYPTIONPASSWORD') {
            // Make a copy of object.
            $obj=$this->_CookieObject;
            // Unset the value to delete it.
            unset($obj[$name]);
            // Restore our new data to the object.
            $this->_CookieObject=$obj;    
            // Lets reuse $obj to store our encrypted object
            $obj=$this->_Encrypt(serialize($obj),$this->_EncryptionPassword);
            // Set the actual cookie with our encrypted data.
            setcookie($this->_CookieID,$obj,$this->_Expire,$this->_Path,$this->_Domain,$this->_Secure);
            // Set the cookie global so the data is usable on this page load.
            $_COOKIE[$this->_CookieID] = $obj;
            // Cleanup obj.
            unset($obj);
        }else{
            // See if your trying to intentionally break my script smile.gif Why else would you name it this!
            die('INVALID COOKIE NAME. YOU MAY NOT USE "____ENCRYPTIONPASSWORD" AS YOUR COOKIE NAME');
        }
    }
    // Returns the Cookie Array
    function GetObject(){
        // Make a copy of the object
        $obj=$this->_CookieObject;
        // Get Rid of our encryption password value.
        unset($obj['____ENCRYPTIONPASSWORD']);
        // Return the array of values.
        return $obj;
    }
    // Standard Encryption Functions.
    function _Encrypt($string,$key) {
    	$result = '';
        for($i=0; $i<strlen($string); $i++) {
            $char = substr($string, $i, 1);
            $keychar = substr($key, ($i % strlen($key))-1, 1);
            $char = chr(ord($char)+ord($keychar));
            $result.=$char;
        }
        return base64_encode(gzdeflate($result,9));
    }
    function _Decrypt($string,$key) {
    	$result = '';
        $string = gzinflate(base64_decode($string));
        for($i=0; $i<strlen($string); $i++) {
            $char = substr($string, $i, 1);
            $keychar = substr($key, ($i % strlen($key))-1, 1);
            $char = chr(ord($char)-ord($keychar));
            $result.=$char;
        }
        return $result;
    }
 
}
 
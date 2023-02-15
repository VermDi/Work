<?php

namespace core;

use core\Tools;

class ToolsHelper
{

    public static function instance(){
        return new ToolsHelper;
    }

    /**
     * Генерация пароля
     * @param int $length
     * @param int $count
     * @param string $characters
     * @return string
     */
    public function randomPassword($length = 8, $count = 1, $characters = "lower_case,upper_case,numbers,special_symbols") {

// $length - the length of the generated password
// $count - number of passwords to be generated
// $characters - types of characters to be used in the password

// define variables used within the function
        $symbols = array();
        $passwords = array();
        $used_symbols = '';
        $pass = '';

// an array of different character types
        $symbols["lower_case"] = 'abcdefghijklmnopqrstuvwxyz';
        $symbols["upper_case"] = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $symbols["numbers"] = '1234567890';
        $symbols["special_symbols"] = '!?@#-_+<>';

        $characters = explode(",",$characters); // get characters types to be used for the passsword
        foreach ($characters as $key=>$value) {
            $used_symbols .= $symbols[$value]; // build asstring with all characters
        }
        $symbols_length = strlen($used_symbols) - 1; //strlen starts from 0 so to get number of characters deduct 1

        for ($p = 0; $p < $count; $p++) {
            $pass = '';
            for ($i = 0; $i < $length; $i++) {
                $n = rand(0, $symbols_length); // get a random character from the string with all characters
                $pass .= $used_symbols[$n]; // add the character to the password string
            }
            $passwords[] = $pass;
        }
        return implode('',$passwords); // return the generated password
    }

    /**
     * simple method to encrypt or decrypt a plain text string
     * initialization vector(IV) has to be the same when encrypting and decrypting
     *
     * @param string $action: can be 'encrypt' or 'decrypt'
     * @param string $string: string to encrypt or decrypt
     * @param string $key: your key
     *
     * @return string
     */
    public static function encrypt_decrypt($action, $string, $key = '!@#qwe$%^trS') {
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $secret_key = $key;
        $secret_iv = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
        // hash
        $key = hash('sha256', $secret_key);

        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        if ( $action == 'encrypt' ) {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        } else if( $action == 'decrypt' ) {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }
        return $output;
    }

    /**
     * Отправить почту
     * @param $email
     * @param $title
     * @param $message
     * @return bool
     * @throws \phpmailerException
     */
    public function sendMail($email, $title, $message){
        if(_MAIL_DRIVER_=='smtp'){
            $settings = [
                'server'         => _SMTP_HOST_,
                'port'           => _SMTP_PORT_,
                'user'           => _SMTP_LOGIN_,
                'password'       => _SMTP_PASSWORD_,
                'to_email'       => $email,
                'to_name'        => $email,
                'from_email'     => _SMTP_LOGIN_,
                'from_name'      => _SMTP_LOGIN_,
                'reply_to_email' => _SMTP_LOGIN_,
                'reply_to_name'  => _SMTP_LOGIN_,
                'title'          => $title,
                'message'        => $message,
            ];
            if ($result = Tools::sendSMTPmail($settings)) {
                return true;
            }
        }
        if(_MAIL_DRIVER_=='mail'){
            if (Tools::sendMail($email, $title, $message)) {
                return true;
            }
        }
        return false;
    }

}
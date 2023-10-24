<?php
/**
 * Klasa do autoryzacji jednorazowego dostępu do fragmentu serwisu
 * @author Grzegorz Petri
 * @since 0.2
 */

require("libs/DataBaseConn.php");
require("libs/Sensor.php");
class AuthBasic
{

    private $sensor;
    private $dbConn;

    public function __construct()
    {
        $this->sensor = new Sensor();
        $this->dbConn = new DataBaseConn();
    }

    public function genFingerprint($algo)
    {
        return $this->sensor->genFingerprint();
    }

    public function createCode($length = 6, $min = 1, $max = 999999)
    {
        return str_pad(mt_rand($min, $max), $length, '0', STR_PAD_LEFT);
    }


    #private function createAuthToken( $email, $id ){}
    public function compAuthCode($emlAuth, $idzAuth, $authCode)
    {
    }
    public function doAuthByEmail($person, $email)
    {
    }
    public function checkIfValidReqest($person, $email)
    {
    }
    private function checkIfValidReqest2f($emlAuth, $idzAuth)
    {
    }
    public function verifyQuickRegCode($codeNo)
    {
        $dbCode = "123456";

        if ($dbCode == $codeNo)
            return true;
        else
            return false;
    }
    /**
     * @desc Tworzy wpis w BD z numerem pozwalającym na uwierzytelnienie Requesta
     * Tworzony Token do uwierzytelnienia zapisując adres Email oraz ID użytkownika
     * Token musi zostać wysłany na pocztę użytkownika, stąd zwracany jest Obiekt informacyjny
     * @param string $email Adres email użytkownika do uwierzytelnienia
     * @param int $id	Numer ID użytkownika do uwierzytelnienia
     * @return array|false	Wygenerowany Token LUB Fałsz
     */
    public function createAuthToken($email, $id)
    {
        $authCode = $this->createCode();
        $authDate = date("Y-m-d H:i:s");
        $addrIp = $this->sensor->addrIp()['remote_addr'];
        $browserInfo = $this->sensor->browser();
        $opSys = $this->sensor->system()['name'];
        $browser = $browserInfo['name'] . ' ' . $browserInfo['version'];
        $fingerprint = $this->genFingerprint('sha512');

        $cont = array(
            'emlAuth' => $email,
            'authCode' => $authCode,
            'authDate' => $authDate,
            'addrIp' => $addrIp,
            'reqOs' => $opSys,
            'reqBrw' => $browser
        );

        $tbl = 'Auth';
        $data = array(
            'session_id' => '',
            'usrId' => $id,
            'addrIp' => $addrIp,
            'fingerprint' => $fingerprint,
            'dateTime' => $authDate,
            'content' => '0',
            'email' => $email,
            'authCode' => $authCode
        );

        // Wykorzystanie klasy DataBaseConn do zapisu danych
        $this->dbConn->insert($tbl, $data);

        // Zwrócenie odpowiedzi
        return $cont;
    }
}

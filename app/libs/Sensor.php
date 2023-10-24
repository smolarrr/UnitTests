<?php
require('./vendor/autoload.php');

class Sensor
{
    public function isLocal()
    {
        $remoteAddress = $_SERVER['REMOTE_ADDR'];

        // Sprawdzanie adresu IPv4 oraz IPv6
        $localAddresses = ['localhost', 'local', '127.0.0.1', '::1'];
        foreach ($localAddresses as $address) {
            if ($remoteAddress === $address) {
                return true;
            }
        }

        // Sprawdzanie przedziału 192.168.*
        if (substr($remoteAddress, 0, 8) === '192.168.') {
            return true;
        }

        return false;
    }

    public function addrIp()
    {
        $remoteAddr = $_SERVER['REMOTE_ADDR'];
        $xForwarded = isset($_SERVER['HTTP_X_FORWARDER_FOR']) ? $_SERVER['HTTP_X_FORWARDER_FOR'] : null;

        return ['remote_addr' => $remoteAddr, 'x_forwarded_for' => $xForwarded];
    }

    public function browser()
    {
        $result = new WhichBrowser\Parser($_SERVER['HTTP_USER_AGENT']);

        return [
            'name' => $result->browser->name,
            'version' => $result->browser->version ? $result->browser->version->toString() : 'unknown'
        ];
    }

    public function system()
    {
        $result = new WhichBrowser\Parser($_SERVER['HTTP_USER_AGENT']);

        return [
            'name' => $result->os->name
        ];
    }

    public function genFingerprint()
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        $ipHash = hash('sha512', $this->addrIp()['remote_addr']);

        return hash_hmac('sha512', $userAgent, $ipHash);
    }
}

?>
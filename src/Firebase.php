<?php namespace Pshift;

use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

/**
 * Description of PusherServices
 *
 * @author TuyenDD
 */
class Firebase {

    private $setting = [
        'type' => 'service_account',
        'auth_uri' => 'https://accounts.google.com/o/oauth2/auth',
        'token_uri' => 'https://oauth2.googleapis.com/token',
        'auth_provider_x509_cert_url' => 'https://www.googleapis.com/oauth2/v1/certs',
        'client_x509_cert_url' => 'https://www.googleapis.com/robot/v1/metadata/x509',
        'private_key' => "-----BEGIN PRIVATE KEY-----\nMIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCmxK19Ib7g5OH2\njrlRnQz85JHw32jHXoSHHH1LG+d2y8rfNrqaozoV46Ax05OXdVzoF5NPr9MD2idM\n9nHuI3xRWJrGGQMKI8GdFgnJdBDHsdR4MD89b8Wevq4S4+/okV1GTrQ4c2cQobI5\nqcNlO6IO6Hihr+x18IU32tR5enSve2tVaOd76CJLY1HlZmGbWlvp9YP+nJuRb5vv\nYKzHRcsmAisIvMCzy1I/Zb1nM3rypR7OMhxKde3ARrY9uY/boL+cs2h7WxQYGU/Z\nsTai5Cg2dBqZDrWtnwE7eqvzWZcofpQOtGgRbAZNAVAYyUIsY73w0NY/nZvtCUaX\nhTyGBllLAgMBAAECggEAUeAKqv4X2BMXKzUMH9kCqOBRFFii+Qra9viSPP9gps7L\n9cXJXKVZnaiJHB1bf0ckC2CTttJEP8ZpeBwgeEGcxdVB+5MJDn/ajRRmTqz4lgol\nHbLWQYPwGo1UKjsVLHG9wQhf670gsyYnua6ymy9pU3dnzj3wUkD5LqB1o4Ru6D3d\nY94fq8fPyYilpMRgdZY89HEBkdTmhBvbGiA6kgk00fR7cCJQUJWV9KvJdnRXmqQo\n+hy110e8q7t35Lcgjoc46UZh2WLYBUqvk8ot/Rd9Xz5SecIcn0K88Kv+SMH49uC1\nm4eZaXZcxR7kqX6GALIGhvaSNMHmG8OtuzDh6YtJHQKBgQDPQBC1OkJvW7Ngewv8\ndCIdl+rdvMd3MI6aMWbq9Gi2gBtb+K4FDC8z2Jwb0fLEtqJ512xDJcJGmMXU6E7H\ncE2juq7HXKpHVIHlTZbuGAbybM+j4gIpUUFX7OfKB7OEXkp3XIb0Xe6racxZAPRF\nwDHuFCMmXNuVgoUkgwv87/otLQKBgQDN/utdaJX6Bg2am7PJfLqp8NjSl2AsPkQK\nvq9hJM/bux1l91fUHAMiKrQFSES/zlmBzn0AB7lcrdNd563F96osAq03Pg6bK8Iu\nxXQ+Z2QNBn29BVO3Bhqkr1wHFzQsG7MAFDvu2WhTExM3Tkq0VhNef/VMBdg6dhow\nsFuYHPpbVwKBgQCQrIM9T2cS/2uPfTXXzGYITIVqtp5zSJUzdCsI9wal53Wx1T39\nTg0hXyNSlFOpGGkBLu1nTlN5HqpzPpvHw1Cfa/EYQEqpsodev7QNjv5CeszT9TBX\nEV7Q9xKzSH77dyr6eb/HlE7In/lDZFQg4NK6BJqo7AdpLgyteUGEFy2IHQKBgB1d\n7TH42gC14jSbY+sik/3sLwz+taA5FDwv6RLBS7y4lT1XILdFcxDo45cpMVvi0BHY\nXSum7j8PLOXjRHvHjgQOMaGBgFWQzHMchTDsTnAo774Fx2R9Q0kdEtKT9UUggclO\nGSk4502Il6G30KCa8NH1DgRThPr4oU01pyktYGerAoGABd+8ahpHe4PxD1lbXlEz\nG2usA+Ds6ls8H54DHCyQ3Y9nXloDkM0/tU8No4rKzkxVyl2V3I3gs8mU3LZ0MRLm\nnZMRpkyrRWAoTWAIjcVfnmK0dxZoHgbeQcnHTuE4amdG4EDMMsUCR+irC8hPiepD\nM49lti/fX1pwPmWCNmXiQ5A=\n-----END PRIVATE KEY-----\n"
    ];
    private $fb;

    public function __construct() {
        $this->setting = array_merge($this->setting, [
            'project_id' => env('FB_PROJECT_ID'),
            'client_id' => env('FB_CLIENT_ID'),
            'private_key_id' => env('FB_PRIVATE_KEY_ID')
        ]);
        
        $this->setting['client_email'] = 'firebase-adminsdk-f26sn@' . $this->setting['project_id'] . '.iam.gserviceaccount.com';
        $this->setting['client_x509_cert_url'] .= '/' . $this->setting['client_email'];
        try {
            $fb = (new Factory)
                    ->withServiceAccount(ServiceAccount::fromJson(json_encode($this->setting)))
                    ->withDatabaseUri('https://' . $this->setting['project_id'] . '.firebaseio.com/')
                    ->create();

            $this->fb = $fb->getDatabase();
        } catch (Exception $ex) {

        }
    }
    public function push($peference = 'clears/message', $val = []) {
        try {
            return $this->fb->getReference($peference)->push($val);
        } catch (Exception $ex) {
            $this->push($peference, $val);
        }
        
    }
    public function set($peference = 'clears/message', $val = '') {
        try {
            return $this->fb->getReference($peference)->set($val);
        } catch (Exception $ex) {
            $this->set($peference, $val);
        }
    }

    public function get($peference = 'clears/message') {
        try {
            return $this->fb->getReference($peference)->getValue();
        } catch (Exception $ex) {
            $this->get($peference);
        }        
    }
    public function initKyuyo() {
        $this->set('kyuyo', null);
        $this->set('shouyo', null);
        foreach(['upload', 'download'] AS $type) {
            foreach (['kyuyo', 'shouyo'] AS $root) {
                $kyuyoPath = $root . '/' . csrf_token() . '/' . $type;        
                $this->set($kyuyoPath . '/message', '');
                $this->set($kyuyoPath . '/pasento', 0);
            }
        }
    }
}

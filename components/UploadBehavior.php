<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace asimet\red\components;

use Yii;
use yii\base\Behavior;
use phpseclib\Net\SFTP;
use phpseclib\Crypt\RSA;

/**
 * Description of UploadBehavior
 *
 * @author gilles
 */
class UploadBehavior extends Behavior {

    public $error;
    public $filename;

    const PRIVATE_KEY = '-----BEGIN RSA PRIVATE KEY-----' . "\n"
            . 'Proc-Type: 4,ENCRYPTED' . "\n"
            . 'DEK-Info: AES-128-CBC,8D1CD714CC1CA496B6BDAAC20B5E4030' . "\n"
            . '' . "\n"
            . 'eZNKi+rJe/OfTgKnkmLGZQYfUhPXDFM0GCvq6eOUnkXoooPt7i+45+jpDWdZMPpZ' . "\n"
            . 'cFVl9CYCZzeRPzByb0+fb6YKjB/W/VwLkd4OBUWRZfkctjSmqQeSrpn4R6T3NAWn' . "\n"
            . 'Tye26nBvDCHBStUpkDaYtWQBEpo0+UhcquVd70OyimT2mNPPX3FY3LMNnSdxuoTa' . "\n"
            . 'qQszAw+YYdl/7Y/T2IQ7BPnzooaSz/pNxsxomghzJa6TBsrrJ/YPbsM4Jgcs85d9' . "\n"
            . 'WeJbtmLSSSU3pPBPl2fE62GWHpwEV+79oy8SNZDUOMReNVYr7uVwho0IctsE9mFn' . "\n"
            . 'ceS8i5f59N2x1vPkPD11Ntv4YA50AE9A4zWemT8zeIDhIcvA1mJ5fmimWbmSb36f' . "\n"
            . 'llewyAf0gDN481y2pzfczg6Fx5+URSexifDWVLH2aB2q+Z34ejQNqnUorG848b5D' . "\n"
            . 'f2h3yPcybWCDMxoNcKzKGWcVh6gLxVQtaKwUnO8fXXdJarBurKyWQn28m9e7bWTU' . "\n"
            . 'qwB16DFIhvNsX4Wp8wByqBxjVN4JzN98GC2OUnsFD6cE4lRg4ruGlvbKEWEHmWrk' . "\n"
            . 'z/fxKPwVhLARYGkWSQ2zGl0g+4s1enoKOjRlZw23dfz1v2Umyk6VJnDmFVmnr5Np' . "\n"
            . 'gKEDcSsmcTjtrADjQOwpifF79qb+cbBhhmD1zxCwshLggNKkjJGVlBplNEFWRFcT' . "\n"
            . 'LiFMbbxpnmL2RIUGg1zaNBwAOMoPNbBdBiyi4FnzcLgt11F/jBcIrwimUCjBw+W6' . "\n"
            . 'almKY4L95liRdgFLvYRnbWY3NIUyY7JLu6OpUC61vvy1SigoKdO+iARTU4iw1VsC' . "\n"
            . '3ILgVqRxN8frqFw0lVosehSanA4JYgS8f7FreJbSyl0nOF5dPWOp9fhgwr+3th9q' . "\n"
            . 'bFyklpWBLp1uTTQk4NUeusNm0IQu8Huk4HR0acgTcAPbpGjS/6OMOm1Kn1A91fWD' . "\n"
            . 'pwfshkONWNUxwL0sxEWfI+GBo/Pj8+ZXQL8A3dR4leuGbpxMAOUwCscuM7EtaWbU' . "\n"
            . 'nAtbX5un7U+o0sVY1n7bL3BWXWzObawXVC1QkLl3ecmLZBhlHnUbOLCZRdPlcUwK' . "\n"
            . 'hOHgzGtkCcj2k12F+G1XKsUmX7MJdqEnZtrcTy/pqznPeYfnYvCdh7kZRSOWy9/V' . "\n"
            . 'hc3kuvx+J6UPz60iY41K/B5Gjs/dRCDEqBiaKtBJWa9Mimkgv4j3NHc4hTpqdzOY' . "\n"
            . 'papo6itxXeGTDqgni0dnMSdo2lPaA7UR3QPrrItpEG2HlXTX9IMDMMZ1qCNpww3r' . "\n"
            . 'L7sezQGv4haTBIwntTyQQiiplfyD9VDOJ2fx0fvG8U/1dNC+ZgBB8aSAJqqQnWD+' . "\n"
            . 'S+0kMW0euYMSwFigQ0WEt7IuRzpRwy//zkjibD4hTHRr+kijd9xol31RG6XyKEw/' . "\n"
            . 'IdEDIxb0q4LYwneTv71vKSa8K7cILhuUfE+YRImmy7WwGcPXNQ4bO6GiIeH5L92u' . "\n"
            . '7tAgkoxaH9S+nrd6G1BP/yjq1576FXYMq/Ms6JS7R3KyVXhYipldUHXN+2Q9jqWA' . "\n"
            . 'Fn4eD6+4Xuob9rH1Pv+X+5p4cNwXKmsgi26tzSaix9dUYn2OWC8CTPkw54x8TW92' . "\n"
            . '-----END RSA PRIVATE KEY-----' . "\n";

    public function upload($file) {

        $ssh = new SFTP('archivos.cetasimet.cl');
        $key = new RSA();
        $key->setPassword('0662emeva$$');
        $key->loadKey(self::PRIVATE_KEY);

        if ($ssh->login('archive', $key)) {

            $extension = pathinfo($file->name, PATHINFO_EXTENSION);
            $this->filename = Yii::$app->security->generateRandomString() . ".{$extension}";
            $path = '/var/www/html/archives/' . $this->filename;

            $ssh->put($path, $file->tempName, SFTP::SOURCE_LOCAL_FILE);
        } else {
            $this->error = "Se ha producido un error al realizar la operación";
        }
    }

    public function deleteFile($filename) {

        $ssh = new SFTP('archivos.cetasimet.cl');
        $key = new RSA();
        $key->setPassword('0662emeva$$');
        $key->loadKey(self::PRIVATE_KEY);

        if ($ssh->login('archive', $key)) {
            $ssh->delete('/var/www/html/archives/' . $filename, false);
        } else {
            $this->error = "Se ha producido un error al realizar la operación";
        }
    }

}

<?php /** @noinspection AutoloadingIssuesInspection */

/**
 * Copyright (c) Ivan Klimchuk - All Rights Reserved
 * Unauthorized copying, changing, distributing this file, via any medium, is strictly prohibited.
 * Written by Ivan Klimchuk <ivan@klimchuk.com>, 2021
 */

declare(strict_types = 1);

require_once __DIR__ . '/helpers/xml.php';

use function alroniks\mspoplati\helpers\xml\xmlToArray;
use function alroniks\mspoplati\helpers\xml\arrayToXml;

/**
 * Class EncryptedVehicle
 */
class EncryptedVehicle extends xPDOObjectVehicle
{
    public const VERSION = '2.0.0';
    private const CIPHER = 'aes-256-cbc';

    public $class = self::class;

    /**
     * @param       $transport xPDOTransport
     * @param       $object
     * @param array $attributes
     *
     * @throws \Exception
     */
    public function put(&$transport, &$object, $attributes = []): void
    {
        parent::put($transport, $object, $attributes);

        if (defined('PKG_ENCODE_KEY')) {
            $this->payload['object_encrypted'] = $this->encode($this->payload['object'], PKG_ENCODE_KEY);
            unset($this->payload['object']);

            if (isset($this->payload['related_objects'])) {
                $this->payload['related_objects_encrypted'] = $this->encode(
                    $this->payload['related_objects'],
                    PKG_ENCODE_KEY
                );
                unset($this->payload['related_objects']);
            }
        }
    }

    /**
     * @param $transport xPDOTransport
     * @param $options
     *
     * @return bool
     * @throws \JsonException
     */
    public function install(&$transport, $options): bool
    {
        if (!$this->decodePayloads($transport)) {
            $transport->xpdo->log(xPDO::LOG_LEVEL_ERROR, 'Package can not be decrypted!');
            return false;
        }

        $transport->xpdo->log(xPDO::LOG_LEVEL_INFO, 'Package decrypted!');

        return parent::install($transport, $options);
    }

    /**
     * @param $transport xPDOTransport
     * @param $options
     *
     * @return bool
     * @throws \JsonException
     */
    public function uninstall(&$transport, $options): bool
    {
        if (!$this->decodePayloads($transport, 'uninstall')) {
            $transport->xpdo->log(xPDO::LOG_LEVEL_ERROR, 'Package can not be decrypted!');
            return false;
        }

        $transport->xpdo->log(xPDO::LOG_LEVEL_INFO, 'Package decrypted!');

        return parent::uninstall($transport, $options);
    }

    /**
     * @param array  $data
     * @param string $key
     *
     * @return string
     * @throws Exception
     */
    protected function encode(array $data, string $key): string
    {
        $iv = random_bytes(openssl_cipher_iv_length(self::CIPHER));

        $encrypted = openssl_encrypt(
            serialize($data),
            self::CIPHER,
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );

        return base64_encode($iv . $encrypted);
    }

    /**
     * @param string $string
     * @param string $key
     *
     * @return string
     */
    protected function decode(string $string, string $key): string
    {
        $ivLen = openssl_cipher_iv_length(self::CIPHER);
        $encoded = base64_decode($string);

        $iv = substr($encoded, 0, $ivLen);
        $cipher_raw = substr($encoded, $ivLen);

        return unserialize(openssl_decrypt($cipher_raw, self::CIPHER, $key, OPENSSL_RAW_DATA, $iv));
    }

    /**
     * @param        $transport xPDOTransport
     * @param string $action
     *
     * @return bool
     * @throws \JsonException
     */
    protected function decodePayloads(xPDOTransport $transport, string $action = 'install'): bool
    {
        $keysFound = count(array_intersect(['object_encrypted', 'related_objects_encrypted'], array_keys($this->payload)));

        if ($keysFound) {
            if (!$key = $this->getDecodeKey($transport, $action)) {
                return false;
            }
            if (isset($this->payload['object_encrypted'])) {
                $this->payload['object'] = $this->decode($this->payload['object_encrypted'], $key);
                unset($this->payload['object_encrypted']);
            }
            if (isset($this->payload['related_objects_encrypted'])) {
                $this->payload['related_objects'] = $this->decode($this->payload['related_objects_encrypted'], $key);
                unset($this->payload['related_objects_encrypted']);
            }
        }

        return true;
    }

    /**
     * @param        $transport xPDOTransport
     * @param string $action
     *
     * @return bool|string
     * @throws \JsonException
     */
    protected function getDecodeKey(xPDOTransport $transport, string $action)
    {
        $key = false;
        $endpoint = 'package/decode/' . $action;

        /** @var modTransportPackage $package */
        $package = $transport->xpdo->getObject(
            'transport.modTransportPackage',
            [
                'signature' => $transport->signature,
            ]
        );

        if ($package instanceof modTransportPackage) {
            /** @var modTransportProvider $provider */
            if ($provider = $package->getOne('Provider')) {
                $provider->xpdo->setOption('contentType', 'default');
                $params = [
                    'package' => $package->package_name,
                    'version' => $transport->version,
                    'username' => $provider->username,
                    'api_key' => $provider->api_key,
                    'vehicle_version' => self::VERSION,
                ];

                $response = $provider->request($endpoint, 'POST', $params);

                if ($response->isError()) {
                    $msg = $response->getError();
                    $transport->xpdo->log(xPDO::LOG_LEVEL_ERROR, $msg);
                } else {
                    $data = $response->toXml();
                    if ($data->key !== null) {
                        $key = $data->key;
                    } elseif ($data->message !== null) {
                        $transport->xpdo->log(xPDO::LOG_LEVEL_ERROR, $data->message);
                    }
                }
            } else {

                $credentials = file_get_contents(sprintf('%spkg/%s/.encryption', MODX_BASE_PATH, $package->package_name));

                $username = $secret = '';

                if ($credentials) {
                    [$username, $secret] = explode(':', $credentials);
                }

                // only for local installation during development
                $params = [
                    'api_key' => $secret,
                    'username' => $username,
                    'http_host' => 'any-site.docker',
                    'package' => $package->package_name,
                    'version' => $transport->version,
                    'vehicle_version' => self::VERSION,
                ];

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, sprintf('https://modstore.pro/extras/%s', $endpoint));
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/xml']);
                curl_setopt($ch, CURLOPT_POSTFIELDS, arrayToXml(['request' => $params]));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                $result = trim(curl_exec($ch));
                curl_close($ch);

                $answer = xmlToArray($result);

                $key = $answer['key'];
            }
        }

        return $key;
    }
}

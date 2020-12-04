<?php
namespace Ifaniqbal\ApiIts;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class ApiIts
{
    private $username;
    private $mahasiswa;
    private $mahasiswaHistoryStatus;
    private $mahasiswaTranskripMahasiswa;
    private $mahasiswaFrsKuliah;
    private $kodeProdi;
    private $prodiKurikulum;

    public function reset()
    {
        $this->username = null;
        $this->mahasiswa = null;
        $this->mahasiswaHistoryStatus = null;
        $this->mahasiswaTranskripMahasiswa = null;
        $this->mahasiswaFrsKuliah = null;
        $this->kodeProdi = null;
        $this->prodiKurikulum = null;
    }

    public function username($username)
    {
        if ($username == $this->username) {
            return $this;
        }

        $this->reset();
        $this->username = $username;
        return $this;
    }

    public function kodeProdi($kodeProdi)
    {
        if ($kodeProdi == $this->kodeProdi) {
            return $this;
        }

        $this->reset();
        $this->kodeProdi = $kodeProdi;
        return $this;
    }

    public function mahasiswa($username = null)
    {
        $username ? $this->username($username) : null;

        if (!$this->mahasiswa) {
            $result = $this->get(
                'https://api.its.ac.id:8443/akademik/1.5/mahasiswa/' . $this->username
            );

            $this->mahasiswa = $result ? $result[0] : null;
        }

        return $this->mahasiswa;
    }

    public function mahasiswaFrsKuliah($username = null, $tahun, $semester)
    {
        $username ? $this->username($username) : null;

        if (!$this->mahasiswaFrsKuliah) {
            $this->mahasiswaFrsKuliah = $this->get(
                'https://api.its.ac.id:8443/akademik/1.5/mahasiswa/' . $this->username . '/frs/kuliah',
                [
                    'query' => [
                        'tahun' => $tahun,
                        'semester' => $semester,
                    ],
                ]
            );
        }

        return $this->mahasiswaFrsKuliah;
    }

    public function mahasiswaHistoryStatus($username = null)
    {
        $username ? $this->username($username) : null;

        if (!$this->mahasiswaHistoryStatus) {
            $this->mahasiswaHistoryStatus = $this->get(
                'https://api.its.ac.id:8443/akademik/1.5/mahasiswa/' . $this->username . '/history-status'
            );
        }

        return $this->mahasiswaHistoryStatus;
    }

    public function mahasiswaTranskripMahasiswa($username = null)
    {
        $username ? $this->username($username) : null;

        if (!$this->mahasiswaTranskripMahasiswa) {
            $this->mahasiswaTranskripMahasiswa = $this->get(
                'https://api.its.ac.id:8443/akademik/1.5/mahasiswa/' . $this->username . '/transkrip-mahasiswa'
            );
        }

        return $this->mahasiswaTranskripMahasiswa;
    }

    public function prodiKurikulum($kodeProdi = null, $tahun)
    {
        $kodeProdi ? $this->kodeProdi($kodeProdi) : null;

        if (!$this->prodiKurikulum) {
            $this->prodiKurikulum = $this->get(
                'https://api.its.ac.id:8443/akademik/1.5/prodi/' . $kodeProdi . '/kurikulum',
                [
                    'query' => compact('tahun')
                ]
            );
        }

        return $this->prodiKurikulum;
    }

    public function accessToken()
    {
        if (Cache::has('api_its_access_token')) {
            return Cache::get('api_its_access_token');
        }

        $data = $this->post(
            Config::get('api-its.token_url'),
            [
                'form_params' => [
                    'grant_type' => 'client_credentials',
                    'client_id' => Config::get('api-its.client_id'),
                    'client_secret' => Config::get('api-its.client_secret'),
                ],
            ]
        );

        Cache::put('api_its_access_token', $data->access_token, $data->expires_in / 60 - 2);

        return $data->access_token;
    }

    public function post($url, $params = [])
    {
        $http = new Client;
        $response = null;
        $params['headers']['Content-Type'] = 'application/x-www-form-urlencoded';

        try {
            $response = $http->post($url, $params);

            $responseContent = $response->getBody()->getContents();
            $data = json_decode($responseContent);

            return $data;
        } catch (ServerException $e) {
            Log::error($e);
            Log::info('API ITS [post] Server Error: ', compact('url', 'params'));
        } catch (ClientException $e) {
            Log::error($e);
            Log::info('API ITS [post] Client Error: ', compact('url', 'params'));
        } catch (Exception $e) {
            Log::error($e);
            Log::info('API ITS [post] Error: ', compact('url', 'params'));
        }

        return null;
    }

    public function get($url, $params = [])
    {
        $http = new Client;
        $response = null;
        $params['headers']['Authorization'] = 'Basic ' . $this->accessToken();

        try {
            $response = $http->get($url, $params);

            $responseContent = $response->getBody()->getContents();
            $data = json_decode($responseContent);

            return $data;
        } catch (ServerException $e) {
            Log::error($e);
            Log::info('API ITS [get] Server Error: ', compact('url', 'params'));
        } catch (ClientException $e) {
            Log::error($e);
            Log::info('API ITS [get] Client Error: ', compact('url', 'params'));
        } catch (Exception $e) {
            Log::error($e);
            Log::info('API ITS [get] Error: ', compact('url', 'params'));
        }

        return null;
    }
}

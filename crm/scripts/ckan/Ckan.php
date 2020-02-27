<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Models;

class Ckan
{
    private $config = [];

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    function upload_resource($resource_id, $file)
    {
        echo "Uploading ".basename($file)."\n";

        $fields = [
            'id'     => $resource_id,
            'upload' => new \CURLFile($file, 'text/csv', 'data.csv')
        ];
        $request = curl_init($this->config['ckan_url'].'/api/3/action/resource_update');
        curl_setopt_array($request, [
            CURLOPT_POST           => true,
            CURLOPT_HEADER         => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS     => $fields,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER     => ["Authorization: {$this->config['api_key']}"]
        ]);
        $response = curl_exec($request);
        if ($response === false) {
            die(curl_error($request));
        }
        else {
            $json = json_decode($response);
            if (   !$json            // invalid response
                || !$json->success) {// Ckan reported an error
                die($response);
            }
        }
    }
}

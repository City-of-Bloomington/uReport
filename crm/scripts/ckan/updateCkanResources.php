<?php
/**
 * Exports Open311 data to CKAN
 *
 * The data will be in the format described by the 311 bulk request data standard
 * @see: http://govex.jhu.edu/untangling-311-request-data/
 * @see: https://docs.google.com/spreadsheets/d/1N9TSt6anpSJkZQv5ZhwtH4r4UX_QegerC4IRnzhkrzI/edit#gid=2065572358
 * @copyright 2019 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
include realpath(__DIR__.'/../../bootstrap.inc');

$db   = $DATABASES['default'];
$dsn  = sprintf("%s:host=%s;dbname=ureport", $db['driver'], $db['host'], $db['name']);
$pdo  = new \PDO($dsn, $db['user'], $db['pass']);

$config = include SITE_HOME.'/ckan/config.inc';
$tmp    = SITE_HOME.'/ckan';
foreach ($config['resource_categories'] as $resource_id => $ureport) {
    preg_match('/(^.*\.)([^\.]+)$/', $ureport['filename'], $matches);
    $ext  = $matches[2];
    $file = "$tmp/$ureport[filename]";
    $FILE = fopen($file, 'w');
    if (!$FILE) { die("Could not create $file\n"); }

    echo "Dumping data to $ureport[filename]\n";
    $result = doQuery($pdo, $ureport['category_id']);

    writeHeader($FILE, $ext);
    writeData  ($FILE, $ext, $result);
    writeFooter($FILE, $ext);
    fclose($FILE);
    uploadResource($resource_id,
                   $file,
                   $ext,
                   $config['ckan_url'].'/api/3/action/resource_update',
                   $config['api_key' ]);
    unlink($file);
}

function doQuery(\PDO $pdo, int $category_id=null)
{
    $sql = "select  t.id            as ticket_id,
                    t.enteredDate,
                    t.lastModified,
                    t.closedDate,
                    t.status,
                    m.name          as contactMethod,
                    c.name          as category,
                    t.description,
                    d.name          as department,
                    t.location, t.city, t.state, t.zip,
                    t.latitude,
                    t.longitude
            from tickets             t
            left join categories     c on t.category_id       = c.id
            left join people         p on t.assignedPerson_id = p.id
            left join departments    d on p.department_id     = d.id
            left join contactMethods m on t.contactMethod_id  = m.id
            where c.displayPermissionLevel = 'anonymous'";
    if ($category_id) {
        $sql.= " and t.category_id=$category_id";
    }
    return $pdo->query($sql);
}

function writeData($file, string $format, \PDOStatement $result)
{
    $c = 0;
    while($row = $result->fetch(\PDO::FETCH_ASSOC)) {
        $c++;
        $enteredDate  = new \DateTime($row['enteredDate']);
        $lastModified = new \DateTime($row['lastModified']);
        $closedDate   = $row['closedDate'] ? new \DateTime($row['closedDate']) : null;

        $location = '';
        if (!empty($row['location'])) {
            $l = [ $row['location'] ];
            if (!empty($row['city' ])) { $l[] = $row['city' ]; }
            if (!empty($row['state'])) { $l[] = $row['state']; }
            if (!empty($row['zip'  ])) { $l[] = $row['zip'  ]; }

            $location = implode(', ', $l);
        }

        $data = [
            'service_request_id' => $row['ticket_id'],
            'requested_datetime' => $enteredDate ->format('c'),
            'updated_datetime'   => $lastModified->format('c'),
            'closed_date'        => $closedDate ? $closedDate->format('c') : null,
            'status'             => $row['status'],
            'source'             => $row['contactMethod'],
            'service_name'       => $row['category'],
            'service_subtype'    => '',
            'description'        => $row['description'],
            'agency_responsible' => $row['department'],
            'address'            => $location,
            'lat'                => $row['latitude'],
            'long'               => $row['longitude']
        ];
        writeFileRecord($file, $format, $data, $c);
    }
}

function writeHeader($file, string $format)
{
    $fields = [
        'service_request_id',
        'requested_datetime',
        'updated_datetime',
        'closed_date',
        'status',
        'source',
        'service_name',
        'service_subtype',
        'description',
        'agency_responsible',
        'address',
        'lat',
        'long'
    ];
    switch ($format) {
        case 'xml':
            fwrite($file, "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<service_requests>\n");
        break;

        case 'json':
            fwrite($file, "[\n");
        break;

        case 'csv':
            fputcsv($file, $fields);
        break;
    }
}

function writeFileRecord($file, string $format, array $row, int $rowNumber)
{
    switch ($format) {
        case 'xml':
            fwrite($file, "\t<service_request>\n");
            foreach ($row as $k=>$v) {
                if ($v) {
                    $v = preg_replace('/[[:^print:]]/', '', $v);
                    $v = htmlspecialchars($v, ENT_XML1);
                }
                fwrite($file, "\t\t<$k>$v</$k>\n");
            }
            fwrite($file, "\t</service_request>\n");
        break;

        case 'json':
            if ($rowNumber != 1) {
                fwrite($file, ",\n");
            }
            fwrite($file, json_encode($row, JSON_PRETTY_PRINT));
        break;

        case 'csv':
            fputcsv($file, $row);
        break;
    }
}

function writeFooter($file, string $format)
{
    switch ($format) {
        case 'xml':
            fwrite($file, "</service_requests>");
        break;

        case 'json':
            fwrite($file, "]");
        break;

        case 'csv':
        break;
    }
}

function uploadResource(string $resource_id, string $file, string $format, string $url, string $api_key)
{
    $basename = basename($file);
    echo "Uploading $basename\n";

    $mime = [
        'csv'  => 'text/csv',
        'json' => 'application/json',
        'xml'  => 'application/xml'
    ];

    $fields = [
        'id'     => $resource_id,
        'upload' => new \CURLFile($file, $mime[$format], $basename)
    ];
    $request = curl_init($url);
    curl_setopt_array($request, [
        CURLOPT_POST           => true,
        CURLOPT_HEADER         => false,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 60,
        CURLOPT_POSTFIELDS     => $fields,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_HTTPHEADER     => ["Authorization: $api_key"]
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

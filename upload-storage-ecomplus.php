<?php
$store_id = 1182;
$headers  = array(
    "X-Store-ID: $store_id",
    'X-My-ID:  5f9c2472b2161709fa46a492',
    'X-Access-Token: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiI1ZjljMjQ3MmIyMTYxNzA5ZmE0NmE0OTIiLCJjb2QiOjM2MjUxNTU2LCJraWQiOm51bGwsImV4cCI6MTYyMjcyOTgwNTc2Nn0.IWVAiUa1ON_QpB_8fb484wu7s5fo89HWXiDt-curBCU/2',
);

function upload_to_storage_api($filename, $filepath, $ext)
{
    global $headers;
    global $store_id;

    if ($ext == 'jpg') {
        $ext = 'jpeg';
    }
    
    $body[] = implode("\r\n", array(
        "Content-Disposition: form-data; name=\"file\"; filename=\"{$filename}\"",
        "Content-Type: image/{$ext}",
        "",
        file_get_contents($filepath),
    ));

    do {
        $boundary = "---------------------" . md5(mt_rand() . microtime());
    } while (preg_grep("/{$boundary}/", $body));

    array_walk($body, function (&$part) use ($boundary) {
        $part = "--{$boundary}\r\n{$part}";
    });

    $body[] = "--{$boundary}--";
    $body[] = "";

    echo "\n$filepath\n";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_URL, "https://apx-storage.e-com.plus/$store_id/api/v1/upload.json");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, implode("\r\n", $body));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge(
        $headers,
        array("Content-Type: multipart/form-data; boundary={$boundary}")
    ));
    $result = curl_exec($ch);
    curl_close($ch);

    $response = json_decode($result, true);
    var_dump($response);

    sleep(1);
    return $response;
}

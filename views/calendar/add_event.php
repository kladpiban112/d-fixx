<?php
/** Error reporting */
error_reporting(0);
ini_set('display_errors', FALSE);
ini_set('display_startup_errors', FALSE);
error_reporting(E_ALL ^ E_NOTICE);  
require_once __DIR__ . '/vendor/autoload.php';
?>
<!DOCTYPE html>
<html>

<head>
    <title>Google Calendar API </title>
    <meta charset='utf-8' />
</head>

<body>
    <div class="card card-custom gutter-b example example-compact">
        <div class="card-header">
            <h3 class="card-title">

            </h3>
            <div class="card-toolbar">
                <div class="example-tools justify-content-center">
                    <a href="dashboard.php" class="btn btn-default btn-sm font-weight-bold mr-2" title=""><i
                            class="fa fa-chevron-left" title="" data-toggle="tooltip"></i></a>
                </div>
            </div>
        </div>



        <div class="card-body">

            <?php
echo "<h1>เพิ่มตารางนัดหมายเรียบร้อย</h1>";
echo "<br>";
echo "<a href='/./dashboard.php?act=&module=calendar&page=home'>ย้อนกลับ</a>";;
?>

        </div>
        <div class="card-footer">
            <div class="row">

            </div>
        </div>


    </div>

    <?php
define('APPLICATION_NAME', 'testcalendar');
define('CREDENTIALS_PATH', __DIR__ . '/.credentials/calendar.json');
define('CLIENT_SECRET_PATH', __DIR__ . '/client_secret.json');
// If modifying these scopes, delete your previously saved credentials
// at ~/.credentials/calendar-php-quickstart.json
define('SCOPES', implode(' ', array(
  Google_Service_Calendar::CALENDAR_EVENTS)
));
// https://developers.google.com/resources/api-libraries/documentation/calendar/v3/php/latest/class-Google_Service_Calendar.html 
/*if (php_sapi_name() != 'cli') {
  throw new Exception('This application must be run on the command line.');
}*/
  
/**
 * Returns an authorized API client.
 * @return Google_Client the authorized client object
 */
function getClient() {
  $client = new Google_Client();
  $client->setApplicationName(APPLICATION_NAME);
  $client->setScopes(SCOPES);
  $client->setAuthConfig(CLIENT_SECRET_PATH);
  $client->setAccessType('offline');
  $client->setApprovalPrompt('force');
    $guzzleClient = new \GuzzleHttp\Client(array( 'curl' => array( CURLOPT_SSL_VERIFYPEER => false, ), ));
    $client->setHttpClient($guzzleClient);  
  
  // Load previously authorized credentials from a file.
  $credentialsPath = expandHomeDirectory(CREDENTIALS_PATH);
  if (file_exists($credentialsPath)) {
    $accessToken = json_decode(file_get_contents($credentialsPath), true);
  } else {
    // Request authorization from the user.
    $authUrl = $client->createAuthUrl();
    printf("Open the following link in your browser:\n%s\n", $authUrl);
    print 'Enter verification code: ';
    $authCode = trim(fgets(STDIN));
  
    // Exchange authorization code for an access token.
    $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
  
    // Store the credentials to disk.
    if(!file_exists(dirname($credentialsPath))) {
      mkdir(dirname($credentialsPath), 0700, true);
    }
    file_put_contents($credentialsPath, json_encode($accessToken));
    printf("Credentials saved to %s\n", $credentialsPath);
  }
  $client->setAccessToken($accessToken);
  
  // Refresh the token if it's expired.
  if ($client->isAccessTokenExpired()) {
    $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
    $newAccessToken = $client->getAccessToken();
    $accessToken = array_merge($accessToken, $newAccessToken);
    file_put_contents($credentialsPath, json_encode($accessToken));
}
  return $client;
}
  
/**
 * Expands the home directory alias '~' to the full path.
 * @param string $path the path to expand.
 * @return string the expanded path.
 */
function expandHomeDirectory($path) {
  $homeDirectory = getenv('HOME');
  if (empty($homeDirectory)) {
    $homeDirectory = getenv('HOMEDRIVE') . getenv('HOMEPATH');
  }
  return str_replace('~', realpath($homeDirectory), $path);
}
  
// Get the API client and construct the service object.
$client = getClient();
$service = new Google_Service_Calendar($client);
 

/////////// ส่วนของการเพิ่ม Event
// ตัวแปรโครงสร้าง parameter สำหรับสร้าง event
$summary = $_POST['summary'];
$description = $_POST['description'];
$start = $_POST['start'];
$end = $_POST['end'];
// ตรวจสอบอย่างง่าย ว่ามีค่าส่งมาหรือไม่ มีค่าส่งมาจึงจะทำการเพิ่มข้อมูล
if($summary != "" && $description !="" && $start !="" && $end !=""){
    $event_data = array(
      'summary' => $summary, // หัวเรื่อง
      'description' => $description, // รายละเอียด
      'start' => array( // วันที่เวลาเริ่มต้น
        'date' => $start,
        'timeZone' => 'Asia/Bangkok',
      ),
      'end' => array( // วันที่เวลาสิ้นสุด
        'date' => $end,
        'timeZone' => 'Asia/Bangkok',
      ),
    );
    $event = new Google_Service_Calendar_Event($event_data); // สร้าง event object
 
    $calendarId = 'f2f89bc4a1f3490455f1da527e12958327802daf1e90b420e63e1feb41b00acf@group.calendar.google.com'; // calendar หลัก
    $event = $service->events->insert($calendarId, $event); // ทำคำสั่งเพิ่มข้อมูล
   
}

 
 
?>

</body>

</html>
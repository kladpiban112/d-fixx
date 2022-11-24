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
        <div class="card-header ribbon ribbon-right">

            <h3 class="card-title">
                <i class="fas fa-edit"></i>ปฏิทินงาน
            </h3>
            <div class="card-toolbar">

            </div>
        </div>

        <div class="form-group row text-center">

            <div class="col-lg-6  "><br />
                <iframe
                    src="https://calendar.google.com/calendar/embed?height=600&wkst=1&bgcolor=%23ffffff&ctz=Asia%2FBangkok&src=ZjJmODliYzRhMWYzNDkwNDU1ZjFkYTUyN2UxMjk1ODMyNzgwMmRhZjFlOTBiNDIwZTYzZTFmZWI0MWIwMGFjZkBncm91cC5jYWxlbmRhci5nb29nbGUuY29t&color=%23C0CA33"
                    style="border:solid 1px #777" width="800" height="600" frameborder="0" scrolling="no"></iframe>
                </iframe>

            </div>
            <div class="col-lg-6">
                <form action="dashboard.php?module=calendar&page=add_event" method="post"><br />
                    <h3 class="card-title">
                        <i class="fas fa-edit"></i> บันทึกตารางนัดหมาย
                    </h3>

                    <div class="form-group row">

                        <div class="col-lg-4">

                            <label>เริ่มงานวันที่</label>
                            <input type="date" class="form-control" name="start" maxlength="10" />
                        </div>
                        <div class="col-lg-4">
                            <label>สิ้นสุดวันที่</label>
                            <input type="date" class="form-control" name="end" maxlength="10" />
                        </div>

                    </div>

                    <div class="form-group row">
                        <div class="col-lg-5">
                            <label>หัวข้อ</label>
                            <input type="text" class="form-control" name="summary" maxlength="10" />
                        </div>
                        <div class="col-lg-5">
                            <label>รายละเอียด</label>
                            <input type="text" class="form-control" name="description" maxlength="10" />
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6">

                                <button type="submit" class="btn btn-primary mr-2"><i class="fa fa-save"></i>
                                    บันทึก</button>
                            </div>

                        </div>
                        <!--col-->
                    </div>

            </div>
        </div>


        <div class="card-footer">

            <div class="col-lg-6 text-right">
                <!--<button type="reset" class="btn btn-danger">Delete</button>-->
            </div>
        </div>
        </form>

    </div>

</body>

</html>


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
  
// Print the next 10 events on the user's calendar.
$calendarId = 'f2f89bc4a1f3490455f1da527e12958327802daf1e90b420e63e1feb41b00acf@group.calendar.google.com';
$optParams = array(
  'maxResults' => 10,
  'orderBy' => 'startTime',
  'singleEvents' => TRUE,
  'timeMin' => date('c'),
);
$results = $service->events->listEvents($calendarId, $optParams);
  

?>
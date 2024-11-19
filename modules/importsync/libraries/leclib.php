<?php if(count(get_included_files()) == 1) exit("No direct script access allowed");

define("LB_API_DEBUG", false);
define("LB_SHOW_UPDATE_PROGRESS", true);

define("LB_TEXT_CONNECTION_FAILED", 'Server is unavailable at the moment, please try again.');
define("LB_TEXT_INVALID_RESPONSE", 'Server returned an invalid response, please contact support.');
define("LB_TEXT_VERIFIED_RESPONSE", 'Verified! Thanks for purchasing.');
define("LB_TEXT_PREPARING_MAIN_DOWNLOAD", 'Preparing to download main update...');
define("LB_TEXT_MAIN_UPDATE_SIZE", 'Main Update size:');
define("LB_TEXT_DONT_REFRESH", '(Please do not refresh the page).');
define("LB_TEXT_DOWNLOADING_MAIN", 'Downloading main update...');
define("LB_TEXT_UPDATE_PERIOD_EXPIRED", 'Your update period has ended or your license is invalid, please contact support.');
define("LB_TEXT_UPDATE_PATH_ERROR", 'Folder does not have write permission or the update file path could not be resolved, please contact support.');
define("LB_TEXT_MAIN_UPDATE_DONE", 'Main update files downloaded and extracted.');
define("LB_TEXT_UPDATE_EXTRACTION_ERROR", 'Update zip extraction failed.');
define("LB_TEXT_PREPARING_SQL_DOWNLOAD", 'Preparing to download SQL update...');
define("LB_TEXT_SQL_UPDATE_SIZE", 'SQL Update size:');
define("LB_TEXT_DOWNLOADING_SQL", 'Downloading SQL update...');
define("LB_TEXT_SQL_UPDATE_DONE", 'SQL update files downloaded.');
define("LB_TEXT_UPDATE_WITH_SQL_IMPORT_FAILED", 'Application was successfully updated but automatic SQL importing failed, please import the downloaded SQL file in your database manually.');
define("LB_TEXT_UPDATE_WITH_SQL_IMPORT_DONE", 'Application was successfully updated and SQL file was automatically imported.');
define("LB_TEXT_UPDATE_WITH_SQL_DONE", 'Application was successfully updated, please import the downloaded SQL file in your database manually.');
define("LB_TEXT_UPDATE_WITHOUT_SQL_DONE", 'Application was successfully updated, there were no SQL updates.');

if(!LB_API_DEBUG){
    ini_set('display_errors', 0);
}

if((ini_get('max_execution_time')!=='0')&&(ini_get('max_execution_time'))<600){
    ini_set('max_execution_time', 600);
}
ini_set('memory_limit', '256M');

class ImportsyncLic{

    private $product_id;
    private $api_url;
    private $api_key;
    private $api_language;
    private $current_version;
    private $verify_type;
    private $verification_period;
    private $current_path;
    private $root_path;
    private $license_file;

    public function __construct(){
        $this->product_id = '2627A934';
        $this->api_url = $this->decrypt('lbsMMpYoQDbAc+azuI3NXzOn9MJmxJNbS5CaO5cst8dU3hw+R8RLAMsmMXbU12VR815p6I/cID4janlLirqmwF2W3NMWMXZvA30HM3gz++s=');
        $this->api_key = '302329B0QW3APE9J02T2';
        $this->api_language = 'english';
        $this->current_version = 'v1.0.0';
        $this->verify_type = 'envato';
        $this->verification_period = 3;
        $this->current_path = realpath(__DIR__);
        $this->root_path = realpath($this->current_path.'/..');
        $this->license_file = $this->current_path.'/.lic';
        $this->check_interval_file = $this->current_path.'/.licint';
    }

    /**
     * check local license_exist
     * @return bool
     */
    public function check_local_license_exist(){
        return is_file($this->license_file);
    }

    /**
     * get current version
     * @return string
     */
    public function get_current_version(){
        return $this->current_version;
    }

    /**
     * call api
     * @param  string $method
     * @param  string $url
     * @param  string $data
     * @return json
     */
    private function call_api($method, $url, $data = null){
        $curl = curl_init();
        switch ($method){
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);
                if($data)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                if($data)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            default:
                if($data)
                    $url = sprintf("%s?%s", $url, http_build_query($data));
        }
        $this_server_name = getenv('SERVER_NAME')?:
            $_SERVER['SERVER_NAME']?:
                getenv('HTTP_HOST')?:
                    $_SERVER['HTTP_HOST'];
        $this_http_or_https = ((
            (isset($_SERVER['HTTPS'])&&($_SERVER['HTTPS']=="on"))or
            (isset($_SERVER['HTTP_X_FORWARDED_PROTO'])and
                $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
        )?'https://':'http://');
        $this_url = $this_http_or_https.$this_server_name.$_SERVER['REQUEST_URI'];
        $this_ip = getenv('SERVER_ADDR')?:
            $_SERVER['SERVER_ADDR']?:
                $this->get_ip_from_third_party()?:
                    gethostbyname(gethostname());
        curl_setopt($curl, CURLOPT_HTTPHEADER,
            array('Content-Type: application/json',
                'LB-API-KEY: '.$this->api_key,
                'LB-URL: '.$this_url,
                'LB-IP: '.$this_ip,
                'LB-LANG: '.$this->api_language)
        );
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($curl);
        $recheck = false;
        if(!$result){
            $rs = array(
                'status' => FALSE,
                'message' => LB_TEXT_CONNECTION_FAILED
            );
            return json_encode($rs);
        }
        $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if($http_status != 200 && !$recheck){
            if(LB_API_DEBUG){
                $temp_decode = json_decode($result, true);
                $rs = array(
                    'status' => FALSE,
                    'message' => ((!empty($temp_decode['error']))?
                        $temp_decode['error']:
                        $temp_decode['message'])
                );
                return json_encode($rs);
            }else{
                $temp_decode = json_decode($result, true);
                $rs = array(
                    'status' => FALSE,
                    'message' => ((!empty($temp_decode['error']))?
                        $temp_decode['error']:
                        $temp_decode['message'])
                );
                return json_encode($rs);
            }
        }
        curl_close($curl);
        return $result;
    }

    /**
     * check connection
     * @return json
     */
    public function check_connection(){
        $get_data = $this->call_api(
            'POST',
            $this->api_url.'api/check_connection_ext'
        );
        $response = json_decode($get_data, true);
        return $response;
    }

    /**
     * get latest version
     * @return json
     */
    public function get_latest_version(){
        $data_array =  array(
            "product_id"  => $this->product_id
        );
        $get_data = $this->call_api(
            'POST',
            $this->api_url.'api/latest_version',
            json_encode($data_array)
        );
        $response = json_decode($get_data, true);
        return $response;
    }

    /**
     * activate license
     * @param  string  $license
     * @param  string  $client
     * @param  string  $create_lic
     * @return array
     */
    public function activate_license($license, $client, $create_lic = true){
        $data_array =  array(
            "product_id"  => $this->product_id,
            "license_code" => $license,
            "client_name" => $client,
            "verify_type" => $this->verify_type
        );

        $get_data = $this->call_api(
            'POST',
            $this->api_url.'validation/api/activate_license.php',
            json_encode($data_array)
        );
        $response = json_decode($get_data, true);
        if(!empty($create_lic)){
            if($response['status']){
                $licfile = trim($response['lic_response']);
                file_put_contents($this->license_file, $licfile, LOCK_EX);
            }else{
                chmod($this->license_file, 0777);
                if(is_writeable($this->license_file)){
                    unlink($this->license_file);
                }
            }
        }
        return $response;
    }

    /**
     * verify license
     * @param  boolean $time_based_check
     * @param  boolean $license
     * @param  boolean $client
     * @return array
     */
    public function verify_license($time_based_check = false, $license = false, $client = false){
        if(!empty($license)&&!empty($client)){
            $data_array =  array(
                "product_id"  => $this->product_id,
                "license_file" => null,
                "license_code" => $license,
                "client_name" => $client
            );
        }else{
            if(is_file($this->license_file)){
                $data_array =  array(
                    "product_id"  => $this->product_id,
                    "license_file" => file_get_contents($this->license_file),
                    "license_code" => null,
                    "client_name" => null
                );
            }else{
                $data_array =  array();
                return array('status' => FALSE, 'message' => LB_TEXT_INVALID_RESPONSE);
            }
        }

        $res = array('status' => TRUE, 'message' => LB_TEXT_VERIFIED_RESPONSE);
        if($time_based_check && $this->verification_period > 0){
            ob_start();
            if(session_status() == PHP_SESSION_NONE){
                session_start();
            }
            $type = (int) $this->verification_period;
            $today = date('d-m-Y');
            $last_verification = '00-00-0000';
            if(is_file($this->license_file)){
                $last_verification = base64_decode(file_get_contents($this->check_interval_file));
            }
            if($type == 1){
                $type_text = '1 day';
            }elseif($type == 3){
                $type_text = '3 days';
            }elseif($type == 7){
                $type_text = '1 week';
            }elseif($type == 30){
                $type_text = '1 month';
            }elseif($type == 90){
                $type_text = '3 months';
            }elseif($type == 365) {
                $type_text = '1 year';
            }else{
                $type_text = $type.' days';
            }
            if(strtotime($today) >= strtotime($last_verification)){
                $get_data = $this->call_api(
                    'POST',
                    $this->api_url.'validation/api/verify_license.php',
                    json_encode($data_array)
                );
                $res = json_decode($get_data, true);
                if($res['status']==true){
                    $tomo = date('d-m-Y', strtotime($today. ' + '.$type_text));
                    file_put_contents($this->check_interval_file,base64_encode($tomo), LOCK_EX);
                }
            }
            ob_end_clean();
        }else{
            $get_data = $this->call_api(
                'POST',
                $this->api_url.'validation/api/verify_license.php',
                json_encode($data_array)
            );
            $res = json_decode($get_data, true);
        }
        return $res;
    }

    /**
     * deactivate license
     * @param  boolean $license
     * @param  boolean $client
     * @return json
     */
    public function deactivate_license($license = false, $client = false){
        if(!empty($license)&&!empty($client)){
            $data_array =  array(
                "product_id"  => $this->product_id,
                "license_file" => null,
                "license_code" => $license,
                "client_name" => $client
            );
        }else{
            if(is_file($this->license_file)){
                $data_array =  array(
                    "product_id"  => $this->product_id,
                    "license_file" => file_get_contents($this->license_file),
                    "license_code" => null,
                    "client_name" => null
                );
            }else{
                $data_array =  array();
            }
        }
        $get_data = $this->call_api(
            'POST',
            $this->api_url.'validation/api/deactivate_license.php',
            json_encode($data_array)
        );
        $response = json_decode($get_data, true);
        if($response['status']){
            chmod($this->license_file, 0777);
            if(is_writeable($this->license_file)){
                unlink($this->license_file);
            }
        }
        return $response;
    }

    /**
     * get_ip_from_third_party
     * @return object
     */
    private function get_ip_from_third_party(){
        $curl = curl_init ();
        curl_setopt($curl, CURLOPT_URL, "http://ipecho.net/plain");
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    /**
     * decrypt
     * @param  string $data
     * @return string
     */
    public function decrypt($data) {
        $key = 'lenzcreative';
        $cipher = 'AES-128-CBC';

        $c = base64_decode($data);

        $ivlen = openssl_cipher_iv_length($cipher);
        $iv = substr($c, 0, $ivlen);
        $hmac = substr($c, $ivlen, $sha2len = 32);
        $ciphertext_raw = substr($c, $ivlen + $sha2len);

        $calcmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary = true);

        if (hash_equals($hmac, $calcmac)) {
            return openssl_decrypt($ciphertext_raw, $cipher, $key, $options = OPENSSL_RAW_DATA, $iv);
        }
    }
}
<?php // Code within app\Helpers\Helper.php

namespace App\Helpers;
use App;
use Aws\ElasticTranscoder\ElasticTranscoderClient;
use Aws\Sns;
use Aws\Sns\Exception\InvalidSnsMessageException;
use Aws\Sns\Message;
use Aws\Sns\MessageValidator;
use File;
use League\Flysystem\Exception;
use Log;
use App\Models\Video;
use Carbon\Carbon;

class AWSHelper
{

    public static function convertHLS_360($filename){
        
        try {
            //self::uploadToBucket($sourcefile,"",$filename,config('aws.bucket_input_video'));
        $client= App::make('aws')->createClient('ElasticTranscoder');

        // $actual_name = pathinfo($filename,PATHINFO_FILENAME);
        $actual_name=$filename["s3Name"];
        $outputHlsFolder = $filename["outputHlsFolder"];
        $createFromMontageServer=$filename["createFromMontageServer"];
        $filename=substr($filename['links3'], 1);

        $output_preset_480p_360=[
            'Key' => $actual_name . "_480p_360",
            'Rotate' => 'auto',
            'PresetId' => config('aws.preset_id.480p_360'),
            'SegmentDuration' => '3',
        ];
        $output_preset_720p_360=[
            'Key' => $actual_name . "_720p_360",
            'Rotate' => 'auto',
            'PresetId' => config('aws.preset_id.720p_360'),
            'SegmentDuration' => '3',
        ];
         $output_preset_1080p_360=[
            'Key' => $actual_name . "_1080p_360",
            'Rotate' => 'auto',
            'PresetId' => config('aws.preset_id.1080p_360'),
            'SegmentDuration' => '3',
        ];
        $output_preset_1440p_360=[
            'Key' => $actual_name . "_1440p_360",
            'Rotate' => 'auto',
            'PresetId' => config('aws.preset_id.1440p_360'),
            'SegmentDuration' => '3',
        ];
        $output_preset_2048p_360=[
            'Key' => $actual_name . "_2048p_360",
            'Rotate' => 'auto',
            'PresetId' => config('aws.preset_id.2048p_360'),
            'SegmentDuration' => '3',
        ];
        $playlist=array(
            array(
                'Name' => $actual_name,
                'Format' => 'HLSv3',
                'OutputKeys' => array(
                    $actual_name . "_480p_360",
                    $actual_name . "_720p_360",
                    $actual_name . "_1080p_360",
                    $actual_name . "_1440p_360",
                    $actual_name . "_2048p_360",
                ),
                // 'HlsContentProtection' => array(
                // 'Method' => 'aes-128',
                // 'LicenseAcquisitionUrl' => route('getkey',['id' => $video_id]),
                // 'KeyStoragePolicy' => 'NoStore',
                //'KeyStoragePolicy' => 'WithVariantPlaylists',
                // ),
                
            ),

        );
       
        $pipelineId = config('aws.pipeline_id');
        if($createFromMontageServer == 1)
        {
            $pipelineId = config('aws.pipeline_id_montage');
        }
        
        Log::info("Use PipelineId for 360: " . $pipelineId);
        $result=$client->createJob(array(
                    // PipelineId is required
                    'PipelineId' => $pipelineId,
                    // Input is required
                    'Input' => array(
                        'Key' => $filename,
                        'FrameRate' => 'auto',
                        'Resolution' => 'auto',
                        'AspectRatio' => 'auto',
                        'Interlaced' => 'auto',
                        'Container' => 'auto',
            
                    ),
                    'Outputs' => array(
                        $output_preset_480p_360,
                        $output_preset_720p_360,
                        $output_preset_1080p_360,
                        $output_preset_1440p_360,
                        $output_preset_2048p_360
                    ),
                    
                    'OutputKeyPrefix'=>$outputHlsFolder,
                    'Playlists'=>$playlist,
                ));
        $jobID= $result['Job']['Id'];
        /*Log::info("JOBID= ".$jobID);
        $result2 = $client->readJob(array(
            // Id is required
            'Id' => $jobID,
        ));
        Log::info($result['key']);
        Log::info($result2);
        $path = public_path("/key_storage");
            if (!is_dir($path)) {
                mkdir($path);
            }
        File::put($path . "/".$video_id.".key",$key);*/

        return $jobID;
        } catch (\Exception $e) {
            Log::info("Error HLS: ");
            Log::info($e);
        }
        

    }

    public static function checkJobStatus($jobId) {
        $client= App::make('aws')->createClient('ElasticTranscoder');
        $result = $client->readJob(['Id' => $jobId]);
        Log::info("Job status: " . $result['Job']['Status']);
        return $result['Job']['Status'];
    }

     public static function convertHLS_3D($filename, $isUpdate = "", $videoId = ""){
        
        try {
            //self::uploadToBucket($sourcefile,"",$filename,config('aws.bucket_input_video'));
        $client= App::make('aws')->createClient('ElasticTranscoder');

        // $actual_name = pathinfo($filename,PATHINFO_FILENAME);
        /*$actual_name=str_replace("/videos/","", $filename);
        $filename=config("aws.folderServer").str_replace("/videos/","videos/", $filename);*/
        
        $actual_name=$filename["s3Name"];
        $outputHlsFolder = $filename["outputHlsFolder"];
        $createFromMontageServer=$filename["createFromMontageServer"];
        $filename=substr($filename['links3'], 1);

        if($isUpdate != "" && $videoId != "" && $isUpdate==true)
        {
            $actual_name = $actual_name.$videoId;
        }
       
        
       

        $output_preset_600k=[
            'Key' => $actual_name . "_1m",
            'Rotate' => 'auto',
            'PresetId' => config('aws.preset_id.1m'),
            'SegmentDuration' => '3',
        ];
         $output_preset_1m=[
            'Key' => $actual_name . "_2m_480p",
            'Rotate' => 'auto',
            'PresetId' => config('aws.preset_id.2m_480p'),
            'SegmentDuration' => '3',
        ];
         $output_preset_2m=[
            'Key' => $actual_name . "_2m_720p",
            'Rotate' => 'auto',
            'PresetId' => config('aws.preset_id.2m_720p'),
            'SegmentDuration' => '3',
        ];
        
        
        $playlist=array(
            array(
                'Name' => $actual_name,
                'Format' => 'HLSv3',
                'OutputKeys' => array(
                    $actual_name . "_1m",
                    $actual_name . "_2m_480p",
                    $actual_name . "_2m_720p",
                ),
                // 'HlsContentProtection' => array(
                // 'Method' => 'aes-128',
                // 'LicenseAcquisitionUrl' => route('getkey',['id' => $video_id]),
                // 'KeyStoragePolicy' => 'NoStore',
                //'KeyStoragePolicy' => 'WithVariantPlaylists',
                // ),
                
            ),

        );

        $pipelineId = config('aws.pipeline_id');
        if($createFromMontageServer == 1)
        {
            $pipelineId = config('aws.pipeline_id_montage');
        }
        Log::info("Use PipelineId: " . $pipelineId);
        $result=$client->createJob(array(
                    // PipelineId is required
                    'PipelineId' => $pipelineId,
                    // Input is required
                    'Input' => array(
                        'Key' => $filename,
                        'FrameRate' => 'auto',
                        'Resolution' => 'auto',
                        'AspectRatio' => 'auto',
                        'Interlaced' => 'auto',
                        'Container' => 'auto',
            
                    ),
                    'Outputs' => array(
                        $output_preset_600k,
                        $output_preset_1m,
                        $output_preset_2m,
                    ),
                    
                    'OutputKeyPrefix'=>$outputHlsFolder,
                    'Playlists'=>$playlist,
                ));
        $jobID= $result['Job']['Id'];
        /*Log::info("JOBID= ".$jobID);
        $result2 = $client->readJob(array(
            // Id is required
            'Id' => $jobID,
        ));
        Log::info($result['key']);
        Log::info($result2);
        $path = public_path("/key_storage");
            if (!is_dir($path)) {
                mkdir($path);
            }
        File::put($path . "/".$video_id.".key",$key);*/

        return $jobID;
        } catch (\Exception $e) {
            Log::info("Error HLS: ");
            Log::info($e);
        }
        

    }
   
    public static function uploadToBucket($sourcefile, $folder, $filename,$bucket, $acl = "private")
    {
        $s3 = App::make('aws')->createClient('s3');
      

        $object = array(
            'Bucket'     => $bucket,
            'Key'        => $folder . $filename,
            'SourceFile' => $sourcefile,
            'ACL'       => $acl,
            'CacheControl' => 'public, max-age=31536000',
            "ContentType" => mime_content_type($sourcefile)
        );
        
        return $s3->putObject($object);
       
        
    }
    public static function uploadToS3($sourcefile, $folder, $filename, $bucket = "")
    {
        $s3 = App::make('aws')->createClient('s3');
        if($bucket == "")
        {
            $bucket = config('aws.bucket_upload_video');
            $folder = config("aws.folderServer").$folder;
        }
        $date = time();
        $actual_name = pathinfo($filename,PATHINFO_FILENAME);
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $actual_name = $actual_name.'_'.$date;
        $s3Name = $actual_name.".".$extension;

        $object = array(
            'Bucket'     => $bucket,
            'Key'        => $folder . $s3Name,
            'SourceFile' => $sourcefile,
            'ACL'       => 'public-read',
            'CacheControl' => 'public, max-age=31536000',
            'ContentType' => mime_content_type($sourcefile)
        );
        $link = "";
        if($s3->putObject($object))
        {
             $link = "/".$folder.$s3Name;
        }

        return ["links3" => $link, "s3Name" => $s3Name, "name" => $actual_name];
        
    }

    public static function uploadReportToS3($sourcefile, $folder, $filename, $bucket = "")
    {
        $s3 = App::make('aws')->createClient('s3');
        if($bucket == "")
        {
            $bucket = config('aws.bucket_admin_report');
            $folder = '';
        }
        $date = time();
        $actual_name = pathinfo($filename,PATHINFO_FILENAME);
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $actual_name = $actual_name;
        $s3Name = $actual_name.".".$extension;

        $object = array(
            'Bucket'     => $bucket,
            'Key'        => $folder . $s3Name,
            'SourceFile' => $sourcefile,
            'ACL'       => 'public-read',
            'ContentType' => 'application/octet-stream',
            'ContentDisposition' => 'attachment; filename=' . $s3Name
        );
        $link = "";
        if($s3->putObject($object))
        {
            $link = "/".$folder.$s3Name;
        }

        return ["links3" => $link, "s3Name" => $s3Name, "name" => $actual_name];

    }

    public static function getLinkPlay($links3)
    {
        $link = route('playvideo') . "?link=" . urlencode($links3);
        return $link;
    }

    /**
 * Get all the necessary details to directly upload a private file to S3
 * asynchronously with JavaScript using the Signature V4.
 *
 * @param string $s3Bucket your bucket's name on s3.
 * @param string $region   the bucket's location/region, see here for details: http://amzn.to/1FtPG6r
 * @param string $acl      the visibility/permissions of your file, see details: http://amzn.to/18s9Gv7
 *
 * @return array ['url', 'inputs'] the forms url to s3 and any inputs the form will need.
 */
public static function getS3Details() {

    // Options and Settings
    $awsKey = config("aws.credentials.key");
    $awsSecret = config("aws.credentials.secret");
    $s3Bucket = config("aws.bucket_upload_video");
    $region = config("aws.region");
    $acl = 'public-read';
    $algorithm = "AWS4-HMAC-SHA256";
    $service = "s3";
    $date = gmdate("Ymd\THis\Z");
    $shortDate = gmdate("Ymd");
    $requestType = "aws4_request";
    $expires = "86400"; // 24 Hours
    $successStatus = "201";
    $url = "//{$s3Bucket}.{$service}-{$region}.amazonaws.com";

    // Step 1: Generate the Scope
    $scope = [
        $awsKey,
        $shortDate,
        $region,
        $service,
        $requestType
    ];
    $credentials = implode('/', $scope);

    // Step 2: Making a Base64 Policy
    $policy = [
        'expiration' => gmdate('Y-m-d\TG:i:s\Z', strtotime('+6 hours')),
        'conditions' => [
            ['bucket' => $s3Bucket],
            ['acl' => $acl],
            ['starts-with', '$key', ''],
            ['starts-with', '$Content-Type', ''],
            ['success_action_status' => $successStatus],
            ['x-amz-credential' => $credentials],
            ['x-amz-algorithm' => $algorithm],
            ['x-amz-date' => $date],
            ['x-amz-expires' => $expires],
        ]
    ];
    $base64Policy = base64_encode(json_encode($policy));

    // Step 3: Signing your Request (Making a Signature)
    $dateKey = hash_hmac('sha256', $shortDate, 'AWS4' . $awsSecret, true);
    $dateRegionKey = hash_hmac('sha256', $region, $dateKey, true);
    $dateRegionServiceKey = hash_hmac('sha256', $service, $dateRegionKey, true);
    $signingKey = hash_hmac('sha256', $requestType, $dateRegionServiceKey, true);

    $signature = hash_hmac('sha256', $base64Policy, $signingKey);

    // Step 4: Build form inputs
    // This is the data that will get sent with the form to S3
    $inputs = [
        'Content-Type' => '',
        'acl' => $acl,
        'success_action_status' => $successStatus,
        'policy' => $base64Policy,
        'X-amz-credential' => $credentials,
        'X-amz-algorithm' => $algorithm,
        'X-amz-date' => $date,
        'X-amz-expires' => $expires,
        'X-amz-signature' => $signature
    ];

    return compact('url', 'inputs');
}
   public static function decryptKey($key)
   {
        $kmsClient = App::make('aws')->createClient('KMS');
        $result = $kmsClient->decrypt([
            'CiphertextBlob' => base64_decode($key),
            'EncryptionContext' => array(
                // Associative array of custom 'EncryptionContextKey' key names
                'service' => 'elastictranscoder.amazonaws.com',
                // ... repeated
            ),
        ]);
        return $result['Plaintext'];
   } 

   public static function readJob($jobId)
   {
        $client= App::make('aws')->createClient('ElasticTranscoder');
        $result = $client->readJob(array(
            'Id' => $jobId,
        ));
        $key= $result['Job']['Playlists'][0]['HlsContentProtection']['Key'];
        return $key;
   } 

   public static function getNotify()
   {
        Log::info("Get noify to set cache");
        $jobId = "";
        // Instantiate the Message and Validator
        $message = Message::fromRawPostData();
        $validator = new MessageValidator();

        // Validate the message and log errors if invalid.
        try {
           $validator->validate($message);
        } catch (InvalidSnsMessageException $e) {
           // Pretend we're not here if the message is invalid.
           http_response_code(404);
           Log::error("error get message from sns" . $e->getMessage());
           die();
        }
        Log::info("[getNotify] ".json_encode($message));
        // Check the type of the message and handle the subscription.
        if ($message['Type'] === 'SubscriptionConfirmation') {
           // Confirm the subscription by sending a GET request to the SubscribeURL
           $content = file_get_contents($message['SubscribeURL']);
           Log::info("[getNotify] get content confirm from sns: " . $content);
        }
        if ($message['Type'] === 'Notification') {
           // Do whatever you want with the message body and data.
           $resultString =  $message['MessageId'] . ': ' . $message['Message'] . "\n";
           Log::info("[getNotify] get message from sns: " . $resultString);
           $result = json_decode($message['Message'], true);
           if(isset($result['state']) && $result['state'] === "COMPLETED" && isset($result['outputKeyPrefix']))
           {
                Log::info("Set cache for hls");
                self::setCacheControlForS3($result['outputKeyPrefix']);
                $jobId = $result['jobId'];
           }
        }
        return $jobId;

   }

   public static function setCacheControlForS3($foldername)
   {
        Log::info("start setCacheControlForS3");
        $client = App::make('aws')->createClient('s3');
        $bucket = config('aws.bucket_upload_video');
        $objects = $client->getIterator('ListObjects', array(
            "Bucket" => $bucket,
            "Prefix" => $foldername
        )); 
        foreach ($objects as $object) {
            $key = $object['Key'];
            $client->copyObject(array(
                'Bucket' => $bucket,
                'ACL'       => 'public-read',
                'CacheControl' => 'public, max-age=31536000',
                'CopySource' => urlencode($bucket . '/' . $key),
                'Key' => $key,
                'MetadataDirective' => 'REPLACE'
            ));
        }
   }

   public static function getObjectFromAdminReportS3($filename){
       $client = App::make('aws')->createClient('s3');
       try{
           $result = $client->getObject([
               'Bucket' => "boomtv-admin-report", // REQUIRED
               'Key' => "$filename",
           ]);
       }
       catch (\Exception $exception){
           return null;
       }

       return $result;

   }

   public static function getFilesInFolderS3($folder, $bucket = "", $link = "", $max = 0)
   {
        if($max == 0)
        {
            $max = config("aws.padding_log");
        }
        $client = App::make('aws')->createClient('s3');
        $result = $client->listObjects([
            'Bucket' => $bucket, // REQUIRED
            'Prefix' => $folder.'/',
            'MaxKeys' => $max + 1
        ]);
        $data=[];
        if($link == "")
        {
            $link = config('aws.cloudfront');
        }
        foreach ($result['Contents'] as $key => $value) {
            if($value['Size']>0)
            {
                $data[$key]['link']=$link.'/'.$value['Key'];
                $data[$key]['name']=str_replace('mapzip/', '', $value['Key']);
                $data[$key]['lastModify']=$value['LastModified'];
                $data[$key]['size']=$value['Size'];
            }   
        }

        return $data;
   }

   public static function deleteVideoOns3($videoId)
   {
        Log::info("Delete video : " . $videoId);
        $s3 = App::make('aws')->createClient('s3');
        $result = array("status" => 1, "message" => "success");
        /*try
        {
            $video = Video::find($videoId);
            if($video != null)
            {
                $bucket = config('aws.bucket_upload_video');
              // delete thumbnail
                $thumb = $video->thumbnail;
                Log::info("Delete video thumb: " . $thumb);
                AWSHelper::deleteObj($s3, substr($thumb,1), $bucket);
              // delete source file
                $source = $video->links3;
                Log::info("Delete video source: " . $source);
                AWSHelper::deleteObj($s3, substr($source,1), $bucket);
              // delete hls file
                $name = pathinfo($video->links3);
                // generate hls folder
                $hlsFolder = config("aws.prefix_output_video").$video->user_id
                    ."/".$name["filename"];
                $path = $hlsFolder ."/";
                $exceptsRealFolder = config("aws.folders_s3_real");
                $exceptsBetaFolder = config("aws.folders_s3_beta");

                Log::info("Will deleting video folder hls : " . $path);
                //check folder hls
                if(is_numeric($video->user_id) && $video->user_id > 0
                    && $name["filename"] != null && $name["filename"] != ""
                    && !in_array($hlsFolder, $exceptsRealFolder)
                    && !in_array($hlsFolder, $exceptsBetaFolder)
                    && strpos($video->link_hls, $hlsFolder) !== false)
                {
                    Log::info("Start delete video folder hls: " . $path);
                    AWSHelper::deleteFolder($s3, $path, $bucket);
                }
                else
                {
                    Log::info("Have not folder hls for video");
                }
            }
            else
            {
                $result = array("status" => 0, "message" => "video deleted", "code" => 1);
            }
        }
        catch(\Exception $e)
        {
            Log::error("Delete video id ".$videoId." on s3 error: " . $e);
            $result = array("status" => 0, "message" => "delete error", "code"=>2);
        }*/
        return $result; 
      
   }
   public static function deleteObj($s3, $keyname, $bucket)
   {
        $result = $s3->deleteObject(array(
            'Bucket' => $bucket,
            'Key'    => $keyname
        ));   
    } 

    public static function deleteFolder($s3, $path, $bucket)
    {
        $result = $s3->listObjects([
            'Bucket' => $bucket, // REQUIRED
            'Prefix' => $path,
        ]);
        foreach ($result['Contents'] as $key => $value) 
        {
                AWSHelper::deleteObj($s3, $value['Key'], $bucket);
        }
    }  

    public static function getFilesInFolderForTime($bucket, $folder, $startDate, $endDate)
    {
        /*$cmd = "aws s3 ls s3://boomtv-content/public/assets/v1/ | awk '$0 = \"2017-04-20 14:05:54\" && $1 ~ /[*nodejs*]/'";*/

        $date = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        $length = $end->diffInDays($date);
        $dateStr = $date->toDateTimeString();
        $i = 1;
        $n = 30;
        for($i = 1; $i <= $length+1; $i++)
        {
            $datetimeStart = date('Y-m-d', strtotime($dateStr)) . " 00:00:00";
            $datetimeEnd = date('Y-m-d', strtotime($dateStr)) . " 23:59:59";
            echo "\n" . $datetimeStart . "\n";
            echo $datetimeEnd . "\n";
            $output = array();
            $cmd = "aws s3 ls s3://".$bucket.$folder." | awk '$0 >= \"".$datetimeStart."\" && $0 <= \"".$datetimeEnd."\" && /nodejs/'";
            echo $cmd . "\n";
            exec($cmd, $output, $return);
            print_r($output);
            AWSHelper::downloadFromListFile($bucket, $folder, $output, $datetimeStart);
            $date = Carbon::parse($dateStr);
            $date->addDay();   
            $dateStr = $date->toDateTimeString();
        }
        
    }
    public static function downloadFromListFile($bucket, $folder, $listFile, $datetime)
    {
        $path = storage_path('log_boom_bot');
        if (!is_dir($path)) {
            mkdir($path);
        }
        $time = date('Y-m-d', strtotime($datetime));
        $pathBB = storage_path('log_boom_bot')."/".$time."/";
        if (!is_dir($pathBB)) {
            mkdir($pathBB);
        }
        foreach ($listFile as $key => $file) {
            $fileEx = explode(" ", $file);
            $file = $fileEx[count($fileEx) - 1];
            $cmd = "aws s3 cp s3://".$bucket.$folder.$file." ".$pathBB;
            echo $cmd . "\n\r";
            exec($cmd);
            AWSHelper::writeBoomLog($pathBB, $pathBB.$file);
            //unlink($pathBB.$file);
        }
        $out_file_name = $pathBB."log.txt";
        AWSHelper::getReplay($out_file_name, $time);
        
    }
    public static function writeBoomLog($path, $file_name)
    {
        // Raising this value may increase performance
        $buffer_size = 4096; // read 4kb at a time
        $out_file_name = $path."log.txt";

        // Open our files (in binary mode)
        $file = gzopen($file_name, 'rb');
        $out_file = fopen($out_file_name, 'a+'); 

        // Keep repeating until the end of the input file
        while (!gzeof($file)) {
            // Read buffer-size bytes
            // Both fwrite and gzread and binary-safe
            fwrite($out_file, gzread($file, $buffer_size));
        }

        // Files are done, close files
        fclose($out_file);
        gzclose($file);

    }

    public static function getReplay($fileLog, $time)
    {
        $path = storage_path('log_boom_bot')."/";
        $content = "";
        $arrCsv = array();
        if (File::exists($fileLog))
        {
            $content = File::get($fileLog);
        }
        $pattern = '/[^\n]*#([\\w]+) replay finish received!(.*)/';
        $matches = array();
        preg_match_all($pattern, $content, $matches);
        
        $info = array();
        $domain_info = array();
        $backup_info = array();
        
        if(is_array($matches))
        {
            $users = array_count_values($matches[1]);
            $i = 0;
            $arrCsv[$i]["date"] = "Date";
            $arrCsv[$i]["username"] = "Username";
            $arrCsv[$i]["replay"] = "Replay";
            foreach ($users as $key => $value) {
                $i++;
                $arrCsv[$i]["date"] = $time;
                $arrCsv[$i]["username"] = $key;
                $arrCsv[$i]["replay"] = $value;
            }
        }
        $fp = fopen($path . 'log.csv', 'a');

        foreach ($arrCsv as $fields) {
            fputcsv($fp, $fields);
        }

        fclose($fp);
    }
}


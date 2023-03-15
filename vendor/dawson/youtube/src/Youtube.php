<?php namespace Dawson\Youtube;

use Carbon\Carbon;
use Dawson\Youtube\Contracts\Youtube as YoutubeContract;
use Google_Client;
use Illuminate\Support\Facades\DB;

use App\Models\SocialAccount;

use Log;

class Youtube implements YoutubeContract
{
    /** @var \Google_Client  */
    protected $client;

    /** @var \Google_Service_YouTube  */
    protected $youtube;

    private $videoId;

    private $thumbnailUrl;

    /**
     * Constructor accepts the Google Client object, whilst setting the configuration options.
     *
     * @param  \Google_Client  $client
     */
    public function __construct(Google_Client $client)
    {
        $this->client = $client;
        $this->client->setApplicationName(config('youtube.application_name'));
        $this->client->setClientId(config('youtube.client_id'));
        $this->client->setClientSecret(config('youtube.client_secret'));
        $this->client->setScopes(config('youtube.scopes'));
        $this->client->setAccessType(config('youtube.access_type'));
        $this->client->setApprovalPrompt(config('youtube.approval_prompt'));
        $this->client->setClassConfig('Google_Http_Request', 'disable_gzip', true);
        $this->client->setRedirectUri(config('youtube.routes.redirect_uri'));

		$this->youtube = new \Google_Service_YouTube($this->client);
    }

	public function saveAccessTokenToDB($accessToken) {
		echo '';
	}

	public function getLatestAccessTokenFromDB() {
		echo '';
	}

    /**
     * Upload the video to YouTube
     *
     * @param  string   $path           The path to the file you wish to upload.
     * @param  array    $data           An array of data.
     * @param  string   $privacyStatus  The status of the uploaded video, set to 'public' by default.
     *
     * @return self
     */
    public function upload($path, array $data, $privacyStatus = 'public')
    {
        $this->handleAccessToken();

        /* ------------------------------------
        #. Setup the Snippet
        ------------------------------------ */
        $snippet = new \Google_Service_YouTube_VideoSnippet();

        if (array_key_exists('title', $data))       $snippet->setTitle($data['title']);
        if (array_key_exists('description', $data)) $snippet->setDescription($data['description']);
        if (array_key_exists('tags', $data))        $snippet->setTags($data['tags']);
        if (array_key_exists('category_id', $data)) $snippet->setCategoryId($data['category_id']);

        /* ------------------------------------
        #. Set the Privacy Status
        ------------------------------------ */
        $status = new \Google_Service_YouTube_VideoStatus();
        $status->privacyStatus = $privacyStatus;

        /* ------------------------------------
        #. Set the Snippet & Status
        ------------------------------------ */
        $video = new \Google_Service_YouTube_Video();
        $video->setSnippet($snippet);
        $video->setStatus($status);

        /* ------------------------------------
        #. Set the Chunk Size
        ------------------------------------ */
        $chunkSize = 1 * 1024 * 1024;

        /* ------------------------------------
        #. Set the defer to true
        ------------------------------------ */
        $this->client->setDefer(true);

        /* ------------------------------------
        #. Build the request
        ------------------------------------ */
        $insert = $this->youtube->videos->insert('status,snippet', $video);

        /* ------------------------------------
        #. Upload
        ------------------------------------ */
        $media = new \Google_Http_MediaFileUpload(
            $this->client,
            $insert,
            'video/*',
            null,
            true,
            $chunkSize
        );

        /* ------------------------------------
        #. Set the Filesize
        ------------------------------------ */
        $media->setFileSize(filesize($path));

        /* ------------------------------------
        #. Read the file and upload in chunks
        ------------------------------------ */
        $status = false;
        $handle = fopen($path, "rb");

        while (!$status && !feof($handle)) {
            $chunk = fread($handle, $chunkSize);
            $status = $media->nextChunk($chunk);
        }

        fclose($handle);

        $this->client->setDefer(false);


        /* ------------------------------------
        #. Set the Uploaded Video ID
        ------------------------------------ */
        $this->videoId = $status['id'];

        return $this;
    }

    /**
     * Set a Custom Thumbnail for the Upload
     *
     * @param  string  $imagePath
     *
     * @return self
     */
    function withThumbnail($imagePath)
    {
        try {
            $videoId = $this->getVideoId();

            // Specify the size of each chunk of data, in bytes. Set a higher value for
            // reliable connection as fewer chunks lead to faster uploads. Set a lower
            // value for better recovery on less reliable connections.
            $chunkSizeBytes = 1 * 1024 * 1024;

            // Setting the defer flag to true tells the client to return a request which can be called
            // with ->execute(); instead of making the API call immediately.
            $this->client->setDefer(true);

            // Create a request for the API's thumbnails.set method to upload the image and associate
            // it with the appropriate video.
            $setRequest = $this->youtube->thumbnails->set($videoId);

            // Create a MediaFileUpload object for resumable uploads.
            $media = new \Google_Http_MediaFileUpload(
                $this->client,
                $setRequest,
                'image/png',
                null,
                true,
                $chunkSizeBytes
            );
            $media->setFileSize(filesize($imagePath));

            // Read the media file and upload it chunk by chunk.
            $status = false;
            $handle = fopen($imagePath, "rb");
            while (!$status && !feof($handle)) {
                $chunk  = fread($handle, $chunkSizeBytes);
                $status = $media->nextChunk($chunk);
            }
            fclose($handle);

            // If you want to make other calls after the file upload, set setDefer back to false
            $this->client->setDefer(false);
            $this->thumbnailUrl = $status['items'][0]['default']['url'];

        } catch (\Google_Service_Exception $e) {
            die($e->getMessage());
        } catch (\Google_Exception $e) {
            die($e->getMessage());
        }

        return $this;
    }

    /**
     * Return the Video ID
     *
     * @return string
     */
    function getVideoId()
    {
        return $this->videoId;
    }

    /**
     * Return the URL for the Custom Thumbnail
     *
     * @return string
     */
    function getThumbnailUrl()
    {
        return $this->thumbnailUrl;
    }

    /**
     * Delete a YouTube video by it's ID.
     *
     * @param  int  $id
     *
     * @return bool
     */
    public function delete($id)
    {
        $this->handleAccessToken();

        if ( ! $this->exists($id)) return false;

        $this->youtube->videos->delete($id);

        return true;
    }

    /**
     * Check if a YouTube video exists by it's ID.
     *
     * @param  int  $id
     *
     * @return bool
     */
    public function exists($id)
    {
        $this->handleAccessToken();

        $response = $this->youtube->videos->listVideos('status', ['id' => $id]);

        if (empty($response->items)) return false;

        return true;
    }

	/**
     * @param       $id
     * @param       $optionalParams
     * @param array $part
     *
     * @return \StdClass
     * @throws \Exception
     */
    public function getUserChannelById($social, $optionalParams = false, $part = ['id', 'snippet', 'contentDetails', 'statistics', 'invideoPromotion']) {
		$socialUpdate = $this->handleAccessToken($social);

		if($socialUpdate != null) {
			$params  = [
						'id'   => is_array($social->channel_id) ? implode(',', $social->channel_id) : $social->channel_id,
						'part' => implode(', ', $part),
						];

			if($optionalParams) {
				$params = array_merge($params, $optionalParams);
			}

			$apiData = $this->api_get($params);

			//echo "<pre>"; print_r($apiData);

			if(is_array($social->channel_id)) {
				return $this->decodeMultiple($apiData);
			}

			return $this->decodeSingle($apiData);
		}
    }
	
	public function getChannelBroadcastStatus($social) {
		$socialUpdate = $this->handleAccessToken($social);

		if($socialUpdate != null) {
			$broadcastsResponse = $this->youtube->liveBroadcasts->listLiveBroadcasts('id,snippet,contentDetails,status', array('broadcastStatus' => 'active', 'broadcastType' => 'all'));

			//echo "<pre>"; print_r($broadcastsResponse);

			if(empty($broadcastsResponse->items))
				return false;
			else
				return true;

		}
	}

	public function getChannelCurrentViewersList($social, $optionalParams = false, $part = ['id', 'snippet', 'contentDetails', 'statistics', 'liveStreamingDetails', 'viewCount']) {
		$socialUpdate = $this->handleAccessToken($social);

		if($socialUpdate != null) {
			try {
				$broadcastsResponse = $this->youtube->liveBroadcasts->listLiveBroadcasts('id,snippet,contentDetails,status', array('broadcastStatus' => 'active', 'broadcastType' => 'all'));

				if(isset($broadcastsResponse->items[0]['id'])) {
					$video_id = $broadcastsResponse->items[0]['id'];

					$params  = [
								'id'   => $video_id,
								'part' => implode(', ', $part),
								];

					if($optionalParams) {
						$params = array_merge($params, $optionalParams);
					}

					$response = $this->youtube->videos->listVideos($part, $params);

					return $response->items[0]['liveStreamingDetails']['concurrentViewers'];
				} else {
					return 1;
				}
			} catch(Google_Service_Exception $e) {
				echo sprintf('<p>A service error occurred: <code>%s</code></p>', htmlspecialchars($e->getMessage()));
			} catch(Google_Exception $e) {
				echo sprintf('<p>An client error occurred: <code>%s</code></p>', htmlspecialchars($e->getMessage()));
			}
		}
    }

	public function getChannelTotalViewers($social, $optionalParams = false, $part = ['id', 'snippet', 'contentDetails', 'statistics']) {
		$socialUpdate = $this->handleAccessToken($social);

		if($socialUpdate != null) {
			$params  = [
						'id'   => is_array($social->channel_id) ? implode(',', $social->channel_id) : $social->channel_id,
						'part' => implode(', ', $part),
						];

			if($optionalParams) {
				$params = array_merge($params, $optionalParams);
			}

			$response = $this->youtube->channels->listChannels('snippet', $params);

			//echo "<pre>"; print_r($response);

			return $response->items[0]['statistics']['viewCount'];
		}
    }
	
	public function getChannelModeratorList($social) {
		$socialUpdate = $this->handleAccessToken($social);

		if($socialUpdate != null) {
			try {
				$broadcastsResponse = $this->youtube->liveBroadcasts->listLiveBroadcasts('id,snippet,contentDetails,status', array('broadcastStatus' => 'active', 'broadcastType' => 'all'));

				//echo "<pre>"; print_r($broadcastsResponse);

				if(isset($broadcastsResponse->items[0]['id'])) {
					$liveChatId = $broadcastsResponse->items[0]['snippet']['liveChatId'];

					$response = $this->youtube->liveChatModerators->listLiveChatModerators($liveChatId, 'id,snippet');

					//echo "<pre>"; print_r($response);

					return $response->items;
				}
			} catch(Google_Service_Exception $e) {
				echo sprintf('<p>A service error occurred: <code>%s</code></p>', htmlspecialchars($e->getMessage()));
			} catch(Google_Exception $e) {
				echo sprintf('<p>An client error occurred: <code>%s</code></p>', htmlspecialchars($e->getMessage()));
			}
		}
	}
	
	/*public function getUserChannelVideoById($social, $optionalParams = false, $part = ['id', 'snippet', 'contentDetails']) {
		$socialUpdate = $this->handleAccessToken($social);

		if($socialUpdate != null) {
			//$API_URL = 'https://www.googleapis.com/youtube/v3/channels';

			$params  = [
						'id'   => is_array($social->channel_id) ? implode(',', $social->channel_id) : $social->channel_id,
						'part' => implode(', ', $part),
						];

			if($optionalParams) {
				$params = array_merge($params, $optionalParams);
			}

			$broadcastsResponse = $this->youtube->liveBroadcasts->listLiveBroadcasts('id,snippet,contentDetails,status', array('broadcastStatus' => 'active', 'broadcastType' => 'all'));

			echo "<pre>"; print_r($broadcastsResponse);

			//$this->youtube = new \Google_Service_YouTube($this->client);

			//$channels = $this->youtube->channels->listChannels("snippet", $params);

			//echo "<pre>"; print_r($channels);

			//$streamsResponse = $this->youtube->liveStreams->listLiveStreams('id,snippet,status', array('mine' => 'true'));

			//echo "<pre>"; print_r($streamsResponse);

			//$response = $this->youtube->channelSections->listChannelSections($part, $params);

			//echo "<pre>"; print_r($response);

			//var_dump($response); die;

			die;

			if(is_array($social->channel_id)) {
				return $this->decodeMultiple($apiData);
			}

			return $this->decodeSingle($apiData);
		}
    }*/
	
    /**
     * Handle the Access token.
     */
    private function handleAccessToken($social)
    {
		$accessToken = $social->access_token;
		$refreshAccessToken = $social->refresh_token;

		if(is_null($accessToken)) {
			throw new \Exception('An access token is required.');
		}

		if($this->client->isAccessTokenExpired()) {
			$this->client->refreshToken($refreshAccessToken);
			$response = $this->client->getAccessToken();

			$results = json_decode($response, true);

			return SocialAccount::updateAccessToken($social, $results);
        } else {
			$this->client->setAccessToken($accessToken);

			return $social;
		}
    }

	/**
	 * Using CURL to issue a GET request
	 *
	 * @param $url
	 * @param $params
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	public function api_get($params)
	{
		$this->youtube = new \Google_Service_YouTube($this->client);

		$channel = $this->youtube->channels->listChannels('snippet', $params);

		return $channel;
	}

	/**
	 * Decode the response from youtube, extract the single resource object.
	 * (Don't use this to decode the response containing list of objects)
	 *
	 * @param  string $apiData the api response from youtube
	 *
	 * @throws \Exception
	 * @return \StdClass  an Youtube resource object
	 */
	public function decodeSingle(&$apiData)
	{
		//$resObj = json_decode($apiData);

		$resObj = $apiData;

		if(isset($resObj->error)) {
			$msg = "Error " . $resObj->error->code . " " . $resObj->error->message;

			if(isset($resObj->error->errors[0])) {
				$msg .= " : " . $resObj->error->errors[0]->reason;
			}

			throw new \Exception($msg);
		} else {
			$itemsArray = $resObj->items;

			if(!is_array($itemsArray) || count($itemsArray) == 0) {
				return false;
			} else {
				return $itemsArray[0];
			}
		}
	}

	/**
	 * Decode the response from youtube, extract the multiple resource object.
	 *
	 * @param  string $apiData the api response from youtube
	 *
	 * @throws \Exception
	 * @return \StdClass  an Youtube resource object
	 */
	public function decodeMultiple(&$apiData)
	{
		$resObj = json_decode($apiData);

		if(isset($resObj->error)) {
			$msg = "Error " . $resObj->error->code . " " . $resObj->error->message;

			if(isset($resObj->error->errors[0])) {
				$msg .= " : " . $resObj->error->errors[0]->reason;
			}

			throw new \Exception($msg);
		} else {
			$itemsArray = $resObj->items;

			if(!is_array($itemsArray)) {
				return false;
			} else {
				return $itemsArray;
			}
		}
	}

	/**
	 * Decode the response from youtube, extract the list of resource objects
	 *
	 * @param  string $apiData response string from youtube
	 *
	 * @throws \Exception
	 * @return array Array of StdClass objects
	 */
	public function decodeList(&$apiData)
	{
		$resObj = json_decode($apiData);

		if (isset($resObj->error)) {
			$msg = "Error " . $resObj->error->code . " " . $resObj->error->message;

			if(isset($resObj->error->errors[0])) {
				$msg .= " : " . $resObj->error->errors[0]->reason;
			}

			throw new \Exception($msg);
		} else {
			$this->page_info = [
				'resultsPerPage' => $resObj->pageInfo->resultsPerPage,
				'totalResults'   => $resObj->pageInfo->totalResults,
				'kind'           => $resObj->kind,
				'etag'           => $resObj->etag,
				'prevPageToken'  => null,
				'nextPageToken'  => null,
			];

			if(isset($resObj->prevPageToken)) {
				$this->page_info['prevPageToken'] = $resObj->prevPageToken;
			}

			if(isset($resObj->nextPageToken)) {
				$this->page_info['nextPageToken'] = $resObj->nextPageToken;
			}

			$itemsArray = $resObj->items;

			if(!is_array($itemsArray) || count($itemsArray) == 0) {
				return false;
			} else {
				return $itemsArray;
			}
		}
	}
}
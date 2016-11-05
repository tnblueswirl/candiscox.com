<?php

/**
 * API client for yourls.org
 *
 * @author Šarūnas Dubinskas <s.dubinskas@evp.lt>
 */

if ( ! defined( 'ABSPATH' ) ) 
	die( 'These aren\'t the droids you\'re looking for...' );

if ( ! class_exists( 'SuextYourls' ) ) {

	class SuextYourls {

	    /**
	     * Available actions
	     */
	    const ACTION_SHORTURL = 'shorturl';
	    const ACTION_URL_STATS = 'url-stats';
	    const ACTION_EXPAND = 'expand';
	
	    /**
	     * @var string
	     */
	    protected $apiUrl;
	
	    /**
	     * @var null|string
	     */
	    protected $username;
	
	    /**
	     * @var null|string
	     */
	    protected $password;
	
	    /**
	     * @var null|string
	     */
	    protected $token;
	
	    /**
	     * @var string
	     */
	    private $lastResponse;
	
	    /**
	     * Class constructor
	     *
	     * @param string $apiUrl
	     * @param null|string $username
	     * @param null|string $password
	     * @param null|string $token
	     */
		public function __construct( $apiUrl, $username = null, $password = null, $token = null ) {
			$this->apiUrl = $apiUrl;
			$this->username = $username;
			$this->password = $password;
			$this->token = $token;
		}
	
	    /**
	     * Get short URL for a URL
	     *
	     * @param string $url
	     * @param null|string $keyword
	     *
	     * @return string
	     */
		public function shorten( $url, $keyword = null ) {
			$result = $this->call( self::ACTION_SHORTURL, 
				array( 'url' => $url, 'keyword' => $keyword ) );

			return isset( $result['shorturl'] ) ? 
				$result['shorturl'] : false;
		}
	
	    /**
	     * Get stats about short URL
	     *
	     * @param string $shortUrl
	     *
	     * @return array
	     */
		public function getUrlStats( $shortUrl ) {
			return $this->call( self::ACTION_URL_STATS, 
				array( 'shorturl' => $shortUrl ) );
		}
	
	    /**
	     * Get long URL of a short URL
	     *
	     * @param string $shortUrl
	     *
	     * @return string
	     */
		public function expand( $shortUrl ) {
			$result = $this->call( self::ACTION_EXPAND, 
				array( 'shorturl' => $shortUrl ) );

			return isset( $result['longurl'] ) ? 
				$result['longurl'] : false;
		}
	
	    /**
	     * Returns last raw response from API
	     *
	     * @return string
	     */
		public function getLastResponse() {
			return $this->lastResponse;
		}
	
	    /**
	     * Calls API action with specified params
	     *
	     * @param string $action
	     * @param array $params
	     *
	     * @return array
	     */
		protected function call( $action, $params = array() ) {

			$result = null;
		        $params['action'] = $action;
	
		        if ( $this->username ) {
				$params['username'] = $this->username;
				$params['password'] = $this->password;
			} else {
				$params['timestamp'] = time();
				$params['signature'] = md5( $this->token.$params['timestamp'] );
			}
			
			$params['format'] = 'json';
		
			$url = $this->apiUrl.(strpos($this->apiUrl, '?') === false ? '?' : '&').http_build_query($params);
		
		        $ch = curl_init();
	
		        curl_setopt( $ch, CURLOPT_URL, $url );
		        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, false );
		        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
		        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
		        curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
		        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		        curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Expect:' ) );
		
		        $response = $this->lastResponse = curl_exec( $ch );
	
			// fetch errors
			$errorNumber = curl_errno( $ch );
			$errorMessage = curl_error( $ch );
			$httpCode = (int) curl_getinfo( $ch, CURLINFO_HTTP_CODE );
	
			// close
			curl_close( $ch );

			try {
				if ( $response === false ) {
					throw new SucomException( 'YOURLS Error: '.$errorMessage, $errorNumber );
				} elseif ( $httpCode !== 200 ) {
					throw new SucomException( 'YOURLS Error: '.json_decode( $response, true ), $httpCode );
				} else {
					$result = json_decode( $response, true ); 
					if ( $result === null )
						throw new SucomException( 'YOURLS Error: JSON response decode error.', json_last_error() );
				}
			} catch ( SucomException $e ) {
				return $e->errorMessage();
			}
			
		        return $result;
		}
	}
}

?>

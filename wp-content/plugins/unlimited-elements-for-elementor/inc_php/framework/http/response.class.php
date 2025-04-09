<?php

class UEHttpResponse{

	private $status;
	private $headers;
	private $body;

	/**
	 * Create a new class instance.
	 *
	 * @param array $data
	 *
	 * @return void
	 */
	public function __construct($data){

		$this->status = UniteFunctionsUC::getVal($data, "status", 0);
		$this->headers = UniteFunctionsUC::getVal($data, "headers", array());
		$this->body = UniteFunctionsUC::getVal($data, "body");
	}

	/**
	 * Get the status code of the response.
	 *
	 * @return int
	 */
	public function status(){

		return $this->status;
	}

	/**
	 * Get the headers of the response.
	 *
	 * @return array
	 */
	public function headers(){

		return $this->headers;
	}

	/**
	 * Get the raw body of the response.
	 *
	 * @return string
	 */
	public function body(){

		return $this->body;
	}

	/**
	 * if the status is not 200
	 */
	public function isStatusOK(){
		
		if($this->status >= 200 && $this->status < 300)
			return(true);
		
		return(false);
	}
	
	/**
	 * get last response error. if available by status
	 */
	public function getError(){
		
		$isOK = $this->isStatusOK();
		
		if($isOK)
			return(null);
		
		$errorText = $this->getStatusText();
			
		return($errorText);
	}
	
	
	/**
	 * get status text
	 */
	public function getStatusText(){
	    
		$code = $this->status;
		
		$statusTexts = array(
	        100 => 'Continue',
	        101 => 'Switching Protocols',
	        102 => 'Processing',
	        103 => 'Early Hints',
	        200 => 'OK',
	        201 => 'Created',
	        202 => 'Accepted',
	        203 => 'Non-Authoritative Information',
	        204 => 'No Content',
	        205 => 'Reset Content',
	        206 => 'Partial Content',
	        207 => 'Multi-Status',
	        208 => 'Already Reported',
	        226 => 'IM Used',
	        300 => 'Multiple Choices',
	        301 => 'Moved Permanently',
	        302 => 'Found',
	        303 => 'See Other',
	        304 => 'Not Modified',
	        305 => 'Use Proxy',
	        306 => '(Unused)',
	        307 => 'Temporary Redirect',
	        308 => 'Permanent Redirect',
	        400 => 'Bad Request',
	        401 => 'Unauthorized',
	        402 => 'Payment Required',
	        403 => 'Forbidden',
	        404 => 'Not Found',
	        405 => 'Method Not Allowed',
	        406 => 'Not Acceptable',
	        407 => 'Proxy Authentication Required',
	        408 => 'Request Timeout',
	        409 => 'Conflict',
	        410 => 'Gone',
	        411 => 'Length Required',
	        412 => 'Precondition Failed',
	        413 => 'Payload Too Large',
	        414 => 'URI Too Long',
	        415 => 'Unsupported Media Type',
	        416 => 'Range Not Satisfiable',
	        417 => 'Expectation Failed',
	        418 => "I'm a Teapot",
	        421 => 'Misdirected Request',
	        422 => 'Unprocessable Entity',
	        423 => 'Locked',
	        424 => 'Failed Dependency',
	        425 => 'Too Early',
	        426 => 'Upgrade Required',
	        428 => 'Precondition Required',
	        429 => 'Too Many Requests',
	        431 => 'Request Header Fields Too Large',
	        451 => 'Unavailable For Legal Reasons',
	        500 => 'Internal Server Error',
	        501 => 'Not Implemented',
	        502 => 'Bad Gateway',
	        503 => 'Service Unavailable',
	        504 => 'Gateway Timeout',
	        505 => 'HTTP Version Not Supported',
	        506 => 'Variant Also Negotiates',
	        507 => 'Insufficient Storage',
	        508 => 'Loop Detected',
	        510 => 'Not Extended',
	        511 => 'Network Authentication Required'
	    );
		
	    if(isset($statusTexts[$code]))
	    	$text = $statusTexts[$code];
	    else
	    	 $text = 'Unknown Status';
	    
	    $text = "$code: ".$text; 
	    
	    return $text;
	}
	
	
	/**
	 * Get the JSON decoded body of the response.
	 *
	 * @return mixed
	 * @throws UEHttpResponseException
	 */
	public function json(){ 

		$data = json_decode($this->body(), true);

		if($data === null) {
			// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
			throw new UEHttpResponseException("Unable to parse the JSON body.", $this);
		}

		return $data;
	}

}

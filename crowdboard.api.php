<?php

/**
 * CrowdBoard API interface
 * @author Lime5
 * @version 0.1
 * 
 * Copyright (c) 2014, Lime5.  All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * - Redistributions of source code must retain the above copyright notice,
 *   this list of conditions and the following disclaimer.
 * - Redistributions in binary form must reproduce the above copyright
 *   notice, this list of conditions and the following disclaimer in the
 *   documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 */

namespace CrowdBoard;

class api {

	public $url = null;
	private $_user = null;
	private $_apikey = null;

	/**
	 * The constructor
	 * @param string $url The url of the resource
	 * @param string $user Your username
	 * @param string $apikey Your APIKey
	 */
	public function __construct($url, $user, $apikey) {
		$this->url = $url;
		$this->_user = $user;
		$this->_apikey = $apikey;
	}

	/**
	 * Initialize the cURL connection
	 * @return resource
	 */
	private function _get_curl() {
		$ch = curl_init($this->url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERPWD, "{$this->_user}:{$this->_apikey}");
		return $ch;
	}

	/**
	 * Close the cURL connection and returns the response
	 * @param resource $ch
	 * @return json
	 */
	private function _end_curl($ch) {
		$result = curl_exec($ch);
		$ch_error = curl_error($ch);
		curl_close($ch);

		if ($ch_error)
			return json_encode(array('error' => $ch_error));

		$json_result = json_decode($result);
		if (is_null($json_result))
			return json_encode(array('results' => $result));

		return $result;
	}
	
	/**
	 * Translate a two level array into an one level array
	 * for cURL post fields
	 * @todo Accept a multilevel array in input
	 * @param array $data
	 * @return array
	 */
	private function _parse_data($data){
		$returned = array();
		foreach($data as $key => $value)
			if(is_array($value)){
				foreach($value as $subkey => $subvalue)
					$returned["{$key}[{$subkey}]"] = $subvalue;
			}else
				$returned[$key] = $value;
			
		return $returned;
	}

	/**
	 * Create a resource
	 * @param array $data Contains the info of the new resource
	 * @return array
	 */
	function create($data) {
		$ch = $this->_get_curl();
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->_parse_data($data));
		return $this->_end_curl($ch);
	}

	/**
	 * Read the resource
	 * @return array
	 */
	function read() {
		$ch = $this->_get_curl();
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
		return $this->_end_curl($ch);
	}

	/**
	 * Update the resoruce
	 * @param array $data
	 * @return array
	 */
	function update($data) {
		$ch = $this->_get_curl();
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->_parse_data($data));
		return $this->_end_curl($ch);
	}

	/**
	 * Delete the resoruce
	 * @return array
	 */
	function delete() {
		$ch = $this->_get_curl();
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
		return $this->_end_curl($ch);
	}

}

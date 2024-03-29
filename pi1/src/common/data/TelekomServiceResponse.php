<?php
/*
 * This file is part of the Telekom PHP SDK
 * Copyright 2010 Deutsche Telekom AG
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * Base class of all Telekom response objects
 * @package common
 * @subpackage data
 */

/**
 * Base class of all Telekom response objects
 * @package common
 * @subpackage data
 */
class TelekomServiceResponse {
	private $status;
	
	/**
	 * Constructs a Telekom response object
	 * @param TelekomServiceStatusResponse $status Telekom status information
	 */
	function __construct($status) {
		$this->status = $status;
	}
	
	/**
	 * Retrieves the status information of the response object
	 * @return TelekomServiceStatusResponse Telekom status information
	 */
	public function getStatus() {
		return $this->status;
	}
}
?>
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
 * A security token getter. It is used by all Telekom service clients to obtain the
 * security token, which is to be used for authentication.
 * @package common
 * @subpackage client
 */

/**
 * A security token getter. It is used by all Telekom service clients to obtain the
 * security token, which is to be used for authentication.
 * @package common
 * @subpackage client
 */
interface SecurityTokenGetter {
	/**
	 * Obtains the security token, which is to be used for authentication.
	 * @return STSToken Security token, which is to be used for authentication.
	 */
	public function getToken();
}
?>
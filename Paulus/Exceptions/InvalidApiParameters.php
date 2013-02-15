<?php
/**
 * Paulus - A PHP micro-framework for creating RESTful API services
 *
 * @author		Trevor Suarez (Rican7)
 * @copyright	2013 Trevor Suarez
 * @link		https://github.com/Rican7/Paulus/
 * @license		https://github.com/Rican7/Paulus/blob/master/LICENSE
 * @version		0.9.0
 */

namespace Paulus\Exceptions;

use	\Paulus\Exceptions\Interfaces\ApiException,
	\Paulus\Exceptions\Traits\ApiExceptionBase,
	\InvalidArgumentException;

/**
 * InvalidApiParameters 
 *
 * InvalidApiParameters Exception 
 * 
 * @uses InvalidArgumentException
 * @uses ApiException
 * @package		Paulus\Exceptions
 */
class InvalidApiParameters extends InvalidArgumentException implements ApiException {
	// Use trait
	use ApiExceptionBase;

	// Define unique properties
	protected $code = 400;
	protected $slug = 'INVALID_API_PARAMETERS';
	protected $message = 'The posted data did not pass validation';

} // End class InvalidApiParameters

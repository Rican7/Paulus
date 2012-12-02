<?php

namespace Api;

// Tell PHP to look for class names in the following namespace
use	Api,
	\User,
	stdClass;

// Members class
class Sample extends Api {

	// Get sample data
	public function get_sample_data( $key_to_encrypt = null ) {
		// Let's create a sample data object
		$sample_data = new stdClass();

		// Only do key based stuff if we were given one
		if ( !is_null($key_to_encrypt) ) {
			$sample_data->posted_key = $key_to_encrypt;
			$sample_data->md5 = md5( $key_to_encrypt );
			$sample_data->sha1 = sha1( $key_to_encrypt );
		}

		$sample_data->timestamp = time();

		return $sample_data;
	}

	// Get sample user data
	public function get_sample_user_data( $user_id = null ) {
		// Let's create a sample data object
		$sample_data = new stdClass();

		// Pretend we only have one user
		if ( $user_id == 1 ) {
			// Let's build some fake/sample data
			$sample_data->user_id = $user_id;
			$sample_data->first_name = 'John';
			$sample_data->last_name = 'Smith';
			$sample_data->date_of_birth = (object) array(
				'string' => 'Feb 1, 1980',
				'int_style' => '2/1/1980',
				'timestamp' => strtotime('2/1/1980'),
			);
			$sample_data->gender = 'male';

			return $sample_data;
		}

		// If we got here, we didn't get any user data
		return null;
	}

	// Get sample DB user data
	public function get_sample_db_data() {
		// Get the first user from the DB
		$user = User::find(1);

		return (object) $user->name;
	}

} // End class Sample

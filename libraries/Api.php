<?php

// Api class
// Main class for structuring API logic
class Api {

	// Get sample DB data
	public function get_sample_data() {
		return User::find(1);
	}

} // End class Api

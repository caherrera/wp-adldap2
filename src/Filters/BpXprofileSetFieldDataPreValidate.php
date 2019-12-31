<?php

namespace WpAdldap2\Filters;

class BpXprofileSetFieldDataPreValidate {
	private function __construct() {
	}

	/**
	 * Filter the raw submitted profile field value.
	 *
	 * Use this filter to modify the values submitted by users before
	 * doing field-type-specific validation.
	 *
	 * @param mixed                  $value          Value passed to xprofile_set_field_data().
	 * @param BP_XProfile_Field      $field          Field object.
	 * @param BP_XProfile_Field_Type $field_type_obj Field type object.
	 *
	 * @return mixed
	 *
	 * @since 2.1.0
	 *
	 */
	static public function filter( $value, $field, $field_type_obj ) {
		switch ( $field->type ) {
			case 'datebox': {
				$value = date("Y-m-d 00:00:00", strtotime($value));break;
			}
		}

		return $value;
	}
}

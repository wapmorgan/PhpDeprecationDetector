<?php
namespace wapmorgan\PhpCodeFixer;

/**
 * Checks that value is present in column of two-dimensional array
 * @param array $haystack Array to search in
 * @param mixed $needle Value to search
 * @param string|int $column Column name
 * @param bool $strict
 * @return bool
 */
function in_array_column(array $haystack, $needle, $column, $strict = false) {
	if ($strict) {
		foreach ($haystack as $k => $elem) {
			if ($elem[$column] === $needle)
				return true;
		}
		return false;
	} else {
		foreach ($haystack as $k => $elem) {
			if ($elem[$column] == $needle)
				return true;
		}
		return false;
	}
}

/**
 * Returns index of value in specific column of two-dimensional array
 * @param array $haystack Array to search in
 * @param mixed $needle Value to search
 * @param string|int $column Column name
 * @param bool $strict
 * @return bool|mixed
 */
function array_search_column(array $haystack, $needle, $column, $strict = false) {
	if ($strict) {
		foreach ($haystack as $k => $elem) {
			if ($elem[$column] === $needle)
				return $k;
		}
		return false;
	} else {
		foreach ($haystack as $k => $elem) {
			if ($elem[$column] == $needle)
				return $k;
		}
		return false;
	}
}

/**
 * Removes all second-level value from array that don't have specific value in column
 * @param array $source Array to filter
 * @param mixed $needle Value to search
 * @param string|int $column Column name
 * @param bool $preserveIndexes
 * @return array
 */
function array_filter_by_column(array $source, $needle, $column, $preserveIndexes = false) {
	$filtered = array();
	if ($preserveIndexes) {
		foreach ($source as $i => $elem)
			if ($elem[$column] == $needle)
				$filtered[$i] = $elem;
	} else {
		foreach ($source as $elem)
			if ($elem[$column] == $needle)
				$filtered[] = $elem;
	}
	return $filtered;
}
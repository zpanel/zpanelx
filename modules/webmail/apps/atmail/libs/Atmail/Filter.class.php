<?php

class Filter
{
    /**
     * Cleans a string only leaving characters known to exist
     * in @Mail database table or field names
     *
     * @param string $string The string to clean
     * @return string The cleaned string
     */
    function cleanSqlFieldNames($string)
    {
        return preg_replace('/[^a-z,_0-9*.]+/i', '', $string);
    }


    /**
     * Check that a string matches (is equal to) a given
     * string or one of an array of strings
     *
     * @param string $string        The subject string
     * @param string|array $allowed Either a string or an array of
     *                              strings to check for a match with
     * @return string Returns $string if it matches $allowed otherwise
     *                returns an empty string
     */
    function stringMatch($string, $allowed, $caseSensitive=false)
    {
        if (is_array($allowed)) {
            foreach ($allowed as $match) {
                if (!$caseSensitive) {
                    if (strtolower($string) == strtolower($match))
                        return $string;
                } elseif ($string == $match) {
                    return $string;
                }
            }
        } elseif ($caseSensitive && $string == $allowed) {
            return $string;
        } elseif (!$caseSensitive && strtolower($string) == strtolower($allowed))
            return $string;

        return '';
    }
}
?>
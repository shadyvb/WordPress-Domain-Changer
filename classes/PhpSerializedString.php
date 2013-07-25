<?php
class PhpSerializedString {

    /**
     * Original string that was passed into the constructor.
     *
     * @var string
     */
    private $original_string = '';

    /**
     * The serialized string in its current state.
     *
     * @var string
     */
    private $string = '';


    /**
     * Class Constructor
     *
     * @param string;
     * @return void;
     */
    public function __construct($serialized_string) {
        $this->original_string  = $serialized_string;
        $this->string           = $this->original_string;
    }

    /**
     * Gets the current string's value.
     *
     * @return string
     */
    function getString() {
        return $this->string;
    }

    /**
     * Sets the current string's value.
     *
     * @return string
     */
    function setString($string) {
        return $this->string = $string;
    }

    /**
     * Gets the original string's value.
     *
     * @return string
     */
    function getOriginalString() {
        return $this->string;
    }

    /**
     * Magic method to work with the object like a string.
     *
     * @return string
     */
    function __toString() {
        return $this->getString();
    }

    /**
     * Replace $find with $replace in a string segment and still keep the integrity of the PHP serialized string.
     *
     * Example:
     *  ... s:13:"look a string"; ...
     *  serializedReplace('string', 'function', $serialized_string)
     *  ... s:15:"look a function"; ...
     *
     * @param string;
     * @param string;
     * @param string;
     * @return string;
     */
    public function replace($find, $replace) {
        $length_diff    = strlen($replace) - strlen($find);
        $find_escaped   = $this->preg_quote($find, '!');
        $encoded_string = $this->encodeDoubleQuotes($this->string);

        if(preg_match_all('!s:([0-9]+):"([^"]*?'.$find_escaped.'{1}.*?)";!', $encoded_string, $matches)) {
            $matches        = array_map(array($this,'decodeDoubleQuotes'), $matches);
            $match_count    = count($matches[0]);
            for($i=0;$i<$match_count;$i++) {
                $new_string     = str_replace($find, $replace, $matches[2][$i], $replace_count);
                $new_length     = ((int) $matches[1][$i]) + ($length_diff * $replace_count);
                $this->string   = str_replace($matches[0][$i], 's:' . $new_length . ':"' . $new_string . '"', $this->string);
            }
        }

        return $this->string;
    }

    /**
     * Enhanced version of preg_quote() that works properly in PHP < 5.3
     *
     * @param string;
     * @param mixed; string, null default
     * @return string;
     */
    public function preg_quote($string, $delimiter = null) {
        $string = preg_quote($string, $delimiter);
        if(phpversion() < 5.3) $string = str_replace('-', '\-', $string);
        return $string;
    }


    /**
     * Replaces any occurrence of " (double quote character) within the value
     * of a serialized string segment with [DOUBLE_QUOTE]. This allows for RegExp
     * to properly capture string segment values in $this->serializedStrReplace().
     *
     * Example:
     *  ... s:13:"look "a" string"; ...
     *  regExpSerializeEncode($serialized_string)
     *  ... s:13:"look [DOUBLE_QUOTE]a[DOUBLE_QUOTE] string"; ...
     *
     * @param string;
     * @return string;
     */
    public function encodeDoubleQuotes($string) {
        if(preg_match_all('!s:[0-9]+:"(.+?)";!', $string, $matches)) {
            foreach($matches[1] as $match) {
                $string = str_replace($match, str_replace('"', '[DOUBLE_QUOTE]', $match), $string);
            }
        }
        return $string;
    }

    /**
     * Undoes the changes that $this->regExpSerializeEncode() made to a string.
     *
     * @see $this->regExpSerializeEncode();
     * @param string;
     * @return string;
     */
    public function decodeDoubleQuotes($string) {
        return str_replace('[DOUBLE_QUOTE]', '"', $string);
    }
}
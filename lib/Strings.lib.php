<?php

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Provides translated language strings.
 */
class Strings {

    private $language = "en";
    private $strings = [];

    public function __construct($language = "en") {
        if (!preg_match("/[a-zA-Z\_\-]+/", $language)) {
            throw new Exception("Invalid language code $language");
        }

        $this->load("en");

        if (file_exists(__DIR__ . "/../langs/$language/")) {
            $this->language = $language;
            $this->load($language);
        } else {
            trigger_error("Language $language could not be found.", E_USER_WARNING);
        }
    }

    /**
     * Load all JSON files for the specified language.
     * @param string $language
     */
    private function load(string $language) {
        $files = glob(__DIR__ . "/../langs/$language/*.json");
        foreach ($files as $file) {
            $strings = json_decode(file_get_contents($file), true);
            foreach ($strings as $key => $val) {
                if (array_key_exists($key, $this->strings)) {
                    trigger_error("Language key \"$key\" is defined more than once.", E_USER_WARNING);
                }
                $this->strings[$key] = $val;
            }
        }
    }

    /**
     * Add language strings dynamically.
     * @param array $strings ["key" => "value", ...]
     */
    public function addStrings(array $strings) {
        foreach ($strings as $key => $val) {
            $this->strings[$key] = $val;
        }
    }

    /**
     * I18N string getter.  If the key isn't found, it outputs the key itself.
     * @param string $key
     * @param bool $echo True to echo the result, false to return it.  Default is true.
     * @return string
     */
    public function get(string $key, bool $echo = true): string {
        $str = $key;
        if (array_key_exists($key, $this->strings)) {
            $str = $this->strings[$key];
        } else {
            trigger_error("Language key \"$key\" does not exist in " . $this->language, E_USER_WARNING);
        }

        if ($echo) {
            echo $str;
        }
        return $str;
    }

    /**
     * I18N string getter (with builder).    If the key doesn't exist, outputs the key itself.
     * @param string $key
     * @param array $replace key-value array of replacements.
     * If the string value is "hello {abc}" and you give ["abc" => "123"], the
     * result will be "hello 123".
     * @param bool $echo True to echo the result, false to return it.  Default is true.
     * @return string
     */
    public function build(string $key, array $replace, bool $echo = true): string {
        $str = $key;
        if (array_key_exists($key, $this->strings)) {
            $str = $this->strings[$key];
        } else {
            trigger_error("Language key \"$key\" does not exist in " . $this->language, E_USER_WARNING);
        }

        foreach ($replace as $find => $repl) {
            $str = str_replace("{" . $find . "}", $repl, $str);
        }

        if ($echo) {
            echo $str;
        }
        return $str;
    }

    /**
     * Builds and returns a JSON key:value string for the supplied array of keys.
     * @param array $keys ["key1", "key2", ...]
     */
    public function getJSON(array $keys): string {
        $strings = [];
        foreach ($keys as $k) {
            $strings[$k] = $this->get($k, false);
        }
        return json_encode($strings);
    }

}

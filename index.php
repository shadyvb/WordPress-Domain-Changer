<?php
/**
 * Author: Daniel Doezema
 * Author URI: http://dan.doezema.com
 * Version: 0.2 (Beta)
 * Description: This script was developed to help ease migration of WordPress sites from one domain to another.
 *
 * Copyright (c) 2010, Daniel Doezema
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in the
 *     documentation and/or other materials provided with the distribution.
 *   * The names of the contributors and/or copyright holder may not be
 *     used to endorse or promote products derived from this software without
 *     specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL DANIEL DOEZEMA BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @copyright Copyright (c) 2010 Daniel Doezema. (http://dan.doezema.com)
 * @license http://dan.doezema.com/licenses/new-bsd New BSD License
 */

/* == Execute Bootstrap ======================================================= */

require_once dirname(__FILE__) . '/includes/bootstrap.php';

/* == START PROCEDURAL CODE ============================================== */

class IndexController extends Controller {

    public function root() {

    }

    public function sign_in() {

    }

    public function sign_in_form_handler() {

    }

    public function sign_in() {

    }

    public function sign_in_form_handler() {

    }

    public function form_handler() {

        $this->redirect(WPDC_URL);
    }
}

$WPDC = new WordPressDomainChanger();

if($WPDC->isPasswordDefault()) die('This script will remain disabled until the default password is changed.');

if($WPDC->isAuthenticationRequest()) {
    /**
     * Try and obstruct brute force attacks by making each login attempt
     * take 5 seconds.This is total security-through-obscurity and can be
     * worked around fairly easily, it's just one more step.
     */
    sleep(5);
    if($WPDC->isCorrectPassword($_POST['auth_password'])) {
        $WPDC->startAuthenticatedSession();
        die('<a href="'.basename(__FILE__).'">Click Here</a><script type="text/javascript">window.location = "'.basename(__FILE__).'";</script>');
    }
}

if($WPDC->isAuthenticatedSession()) {

    if(!$WPDC->getConfig()->exists()) {
        $WPDC->notices[] = 'Unable to find a "wp-config.php" file. Please ensure the <strong>wpdc</strong> is located in the root directory of your WordPress site.';
    }

    if(!$WPDC->getConfig()->db()) {
        $WPDC->notices[] = 'Unable to connect to this server\'s WordPress database using the settings from ' . $this->getConfig()->getPath() . '; Check that it\'s properly configured.';
    }

    try {
        if(isset($_POST['is_change_request'])) {
            // Clean up data & check for empty fields
            $POST = array();
            foreach($_POST as $key => $value) {
                $value = trim($value);
                if(strlen($value) <= 0) throw new Exception('One or more of the fields was blank -- all are required.');
                if(get_magic_quotes_gpc()) $value = stripslashes($value);
                $POST[$key] = $value;
            }

            // Check for "http://" in the new domain
            if(stripos($POST['new_domain'], 'http://') !== false) {
                // Let them correct this instead of assuming it's correct and removing the "http://".
                throw new Exception('The "New Domain" field must not contain "http://"');
            }

            // DB Connection
            $db = @new WordPressDatabase($POST['host'], $POST['username'], $POST['password'], $POST['database'], $db->getTablePrefix());
            if($db->getConnectError()) {
                throw new Exception('Unable to create a database connection using the provided settings.');
            }

            // Escape for Database
            $old_domain = $db->escape_string($_POST['old_domain']);
            $new_domain = $db->escape_string($_POST['new_domain']);

            /**
             * Handle Serialized Values
             *
             * Before we update the options we need to find any option_values that have the
             * old_domain stored within a serialized string.
             */
            if(!$result = $db->query('SELECT * FROM ' . $db->getTablePrefix() . 'options WHERE option_value REGEXP "s:[0-9]+:\".*'.$db->escape_string(WordPressDomainChanger::preg_quote($POST['old_domain'])).'.*\";"')) {
                throw new Exception($db->error);
            }
            $serialized_options = array();
            $options_to_exclude = '';
            if($result->num_rows > 0) {
                // Build dataset
                while(is_array($row = $result->fetch_assoc())) $serialized_options[] = $row;

                // Build Exclude SQL for general update of options later.
                foreach($serialized_options as $record) $options_to_exclude .= $record['option_id'].',';
                $options_to_exclude = ' WHERE option_id NOT IN('.rtrim($options_to_exclude, ',').')';

                // Update Serialized Options
                foreach($serialized_options as $record) {

                    $new_option_value = WordPressDomainChanger::serializedStrReplace($data['old_domain'], $data['new_domain'], $record['option_value']);
                    if(!$db->query('UPDATE ' . $db->getTablePrefix() . 'options SET option_value = "'.$db->escape_string($new_option_value).'" WHERE option_id='.(int)$record['option_id'].';')) {
                        throw new Exception($db->error);
                    }
                    $WPDC->actions[] = '[Serialize Replace] Old domain (' . $old_domain . ') replaced with new domain (' . $new_domain . ') in option_name="'.$record['option_name'].'"';
                }

            }

            // Update Options
            $result = $db->query('UPDATE ' . $db->getTablePrefix() . 'options SET option_value = REPLACE(option_value,"' . $old_domain . '","' . $new_domain . '")'.$options_to_exclude.';');
            if(!$result) throw new Exception($db->error);
            $WPDC->actions[] = 'Old domain (' . $old_domain . ') replaced with new domain (' . $new_domain . ') in ' . $db->getTablePrefix() . 'options.option_value';

            // Update Post Content
            $result = $db->query('UPDATE ' . $db->getTablePrefix() . 'posts SET post_content = REPLACE(post_content,"' . $old_domain . '","' . $new_domain . '");');
            if(!$result) throw new Exception($db->error);
            $WPDC->actions[] = 'Old domain (' . $old_domain . ') replaced with new domain (' . $new_domain . ') in ' . $db->getTablePrefix() . 'posts.post_content';

            // Update Post GUID
            $result = $db->query('UPDATE ' . $db->getTablePrefix() . 'posts SET guid = REPLACE(guid,"' . $old_domain . '","' . $new_domain . '");');
            if(!$result) throw new Exception($db->error);
            $WPDC->actions[] = 'Old domain (' . $old_domain . ') replaced with new domain (' . $new_domain . ') in ' . $db->getTablePrefix() . 'posts.guid';

            // Update post_meta
            $result = $result = $db->query('UPDATE ' . $db->getTablePrefix() . 'postmeta SET meta_value = REPLACE(meta_value,"' . $old_domain . '","' . $new_domain . '");');
            if(!$result) throw new Exception($db->error);
            $WPDC->actions[] = 'Old domain (' . $old_domain . ') replaced with new domain (' . $new_domain . ') in ' . $db->getTablePrefix() . 'postmeta.meta_value';

            // Update "upload_path"
            $upload_dir = dirname(__FILE__).'/wp-content/uploads';
            $result     = $db->query('UPDATE ' . $db->getTablePrefix() . 'options SET option_value = "'.$upload_dir.'" WHERE option_name="upload_path";');
            if(!$result) throw new Exception($db->error);
            $WPDC->actions[] = 'Option "upload_path" has been changed to "'.$upload_dir.'"';
        }
    } catch (Exception $exception) {
        $WPDC->errors[] = $exception->getMessage();
    }
}
?>

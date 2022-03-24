<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package     theme_cbe
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright   2022 Tresipunt
 */

namespace theme_cbe\cli;

global $CFG;

use auth_plugin_saml2;
use auth_saml2\event\cert_regenerated;
use coding_exception;
use moodle_exception;
use saml2_exception;

require_once($CFG->dirroot . '/my/lib.php');
require_once($CFG->dirroot . '/auth/saml2/auth.php');
require_once($CFG->dirroot . '/auth/saml2/setuplib.php');

defined('MOODLE_INTERNAL') || die();

class saml2 {

    /**
     * Execute
     *
     * @param string $sitename
     * @param string $contactname
     * @param string $contactemail
     * @throws \dml_exception
     */
    static public function execute(string $sitename, string $contactname, string $contactemail) {
        self::certificate($sitename, $contactname, $contactemail);
        cfg::set('auth_saml2', 'duallogin', 0);
        cfg::set('auth_saml2', 'autocreate', true);
    }

    /**
     * Certificate
     *
     */
    static protected function certificate(string $sitename, string $contactname, string $contactemail) {
        global $USER, $CFG;
        $dn = array(
            'commonName' => $sitename,
            'countryName' => 'ES',
            'emailAddress' => $contactemail,
            'localityName' => 'Barcelona',
            'organizationName' => $contactname,
            'stateOrProvinceName' => 'Catalunya',
            'organizationalUnitName' => $sitename,
        );
        $numberofdays = 200;

        $saml2auth = new auth_plugin_saml2();
        try {
            $error = create_certificates($saml2auth, $dn, $numberofdays);
            cli_writeln('SAML2: Create Certificate');
        } catch (moodle_exception $e) {
            cli_writeln('SAML2: Already Created!! - ' . $e->getMessage());
            return null;
        }

        if (!$error) {
            // Successfully regenerated cert so emit the cert_regenerated event.
            $eventdata = [
                'reason' => "regenerated in saml settings page",
                'userid' => $USER->id,
            ];
            try {
                cert_regenerated::create(['other' => $eventdata])->trigger();
                cli_writeln('SAML2: Regenerate Certificate');
            } catch (moodle_exception $e) {
                cli_writeln('SAML2: ERROR Regenerate - ' . $e->getMessage());
                return null;
            }
        }

        $certfiles = array($saml2auth->certpem, $saml2auth->certcrt);
        foreach ($certfiles as $certfile) {
            try {
                chmod($certfile, $CFG->filepermissions & 0440);
                cli_writeln('SAML2: CHMOD Certificate - ' . $certfile);
            } catch (moodle_exception $e) {
                cli_writeln('SAML2: ERROR Chmod - ' . $e->getMessage());
                return null;
            }
        }
        // Store the locked state in config.
        set_config('certs_locked', '1', 'auth_saml2');
        cli_writeln('SAML2: Lock Certificates');
    }

}

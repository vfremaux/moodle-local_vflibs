<?php
// This file is part of Moogwai - private project

/**
 * Generates a captcha challenge image, storing info in session to validate it.
 * Uses gd generator to make image.
 *
 * @package    core_auth
 */

require(__DIR__.'/../../../config.php');
require_once($CFG->dirroot.'/local_vflibs/simplecaptchalib.php');

simplecaptcha_generate($CFG->captchatype ?? 'glyphs');

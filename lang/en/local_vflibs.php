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
 * @package local_vflibs
 * @author valery.fremaux@gmail.com
 * @category local
 */

$string['pluginname'] = 'Extra common libraries for VF plugins';

$string['potcourses'] = 'Potential courses';
$string['nomatchingcourses'] = 'No courses matching';
$string['previouslyselectedcourses'] = 'Previously selected';
$string['courseselectorpreserveselected'] = 'Preserve selection';
$string['courseselectorautoselectunique'] = 'Auto select unique';
$string['courseselectorsearchanywhere'] = 'Search anywhere';
$string['configenablelocalpdf'] = 'Enable local pdf';
$string['configdocbaseurl'] = 'Documentation base url';
$string['configdoccustomerid'] = 'Documentation customer id';
$string['configdoccustomerpublickey'] = 'Documentation public key';
$string['helponblock'] = 'Help on the block';
$string['helponmodule'] = 'Help on the course module';

$string['configenablelocalpdf_desc'] = 'Enables the use of the locally modified tcpdf library. This needs protecting
the original lib/tcpdf lib from definition collision. Read the REDME.txt file for more information.';

$string['configdocbaseurl_desc'] = 'Base url for the remote documentation source';
$string['configdoccustomerid_desc'] = 'Customer key for document access authentication';
$string['configdoccustomerpublickey_desc'] = 'A PEM public key for encrypting the documentation access token. It is given by the documentation provider.';

$string['configdonutrenderercolors'] = 'JQPlot simple donut colors';
$string['configdonutrenderercolors_desc'] = 'Give an HTML color codes list, coma separated.';
$string['configjqplotshadows'] = 'JQPlot graph shadows';
$string['configjqplotshadows_desc'] = 'If set, will put shadows on shapes and plit generators.';
<?php
/**
 * EU cookie law (ECL)
 *
 * @package ECL
 * @author emanuele
 * @copyright 2012 emanuele, Simple Machines
 * @license http://www.simplemachines.org/about/smf/license.php BSD
 *
 * @version 0.1.0
 */

function template_ecl_warning_above ()
{
	global $context;

	echo '
	<div id="ecl_notification">
		', $context['ecl_main_notice'], '
	</div>';
}

function template_ecl_warning_below ()
{

}

function template_ecl_privacynotice_above ()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	echo '
		<div class="cat_bar">
			<h3 class="catbg">Privacy notice</h3>
		</div>
		<span class="upperframe"><span></span></span>
		<div class="roundframe">
			<p>', $context['ecl_privacynotice'], '</p>
		</div>
		<span class="lowerframe"><span></span></span>';
}
function template_ecl_privacynotice_below ()
{
}
function template_ecl_privacynotice ()
{
}

?>
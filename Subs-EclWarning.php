<?php
/**
 * EU cookie law (ECL)
 *
 * @package ECL
 * @author emanuele
 * @copyright 2012 emanuele, Simple Machines
 * @license http://www.simplemachines.org/about/smf/license.php BSD
 *
 * @version 0.1.1
 */

if (!defined('SMF'))
	die('Hacking attempt...');

function ecl_warning_add_action ($actionArray)
{
	global $modSettings, $txt, $context, $sourcedir;

	$actionArray['privacynotice'] = array('Subs.php', 'EclPrivacyNotice');

	$disabledActionsStrict = array(
		'coppa',
		'login',
		'login2',
		'register',
		'register2',
	);

	$disabledActionsRelaxed = array(
		'collapse',
		'deletemsg',
		'editpoll',
		'editpoll2',
		'mergetopics',
		'moderate',
		'movetopic',
		'movetopic2',
		'post',
		'post2',
		'quickmod',
		'quickmod2',
		'removepoll',
		'removetopic2',
		'restoretopic',
		'splittopics',
		'vote',
	);

	if (isset($_REQUEST['action']))
	{
		if (in_array($_REQUEST['action'], $disabledActionsRelaxed) || (!empty($modSettings['ecl_strict_interpretation']) && in_array($_REQUEST['action'], $disabledActionsStrict)))
		{
			// Did you accept to store cookies?
			if (!ecl_authorized_cookies())
			{
				$txt['ecl_action_disabled'] = str_replace(array('{ACCEPTCOOKIES}', '{ACTION}'), array($context['ecl_accept_cookies'], $txt['ecl_' . $_REQUEST['action']]), $txt['ecl_action_disabled']);
				fatal_lang_error('ecl_action_disabled', false);
			}
		}
	}

	if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'register')
	{
		if (empty($_POST['accept_agreement']))
		{
			$modSettings['requireAgreement'] = true;
			EclPrivacyNotice(true);
		}
		else
		{
			ecl_authorized_cookies(true);
			loadSession();
		}
	}
}

function ecl_warning_add_theme_elements ()
{
	global $context, $txt, $modSettings, $scripturl;

	if (!ecl_authorized_cookies() && !(isset($_REQUEST['wap']) || isset($_REQUEST['wap2']) || isset($_REQUEST['imode'])))
	{
		$context['html_headers'] .= '
	<style>
		#ecl_notification
		{
			color: #f96f00;
			background-color: white;
			border-bottom: solid 3px #f96f00;
			text-align: center;
			font-size: 12pt;
			padding: 8px;
			width: 100%;
			line-height: 25px;
			position: fixed;
			top: 0;
			left: 0;
			padding-left: 0;
			padding-right: 0;
		}
	</style>
	<script type="text/javascript"><!-- // --><![CDATA[
		cookies_allowed = ' . (ecl_authorized_cookies() ? '1' : '0') . ';
	// ]]></script>';

		$context['ecl_accept_cookies'] = $scripturl .'?'. http_build_query(array_merge($_GET, array('cookieaccept' => '')));
		call_integration_hook('integrate_fix_url', array(&$context['ecl_accept_cookies']));
		loadtemplate('EclWarning');
		$context['ecl_main_notice'] = str_replace('{ACCEPTCOOKIES}', $context['ecl_accept_cookies'], $txt['ecl_main_notice']) . (!empty($modSettings['ecl_strict_interpretation']) ? '' : '<br />' . str_replace('{ACCEPTCOOKIES}', $context['ecl_accept_cookies'], $txt['ecl_accept_how_to']));
		$context['template_layers'][] = 'ecl_warning';
	}
}

function ecl_authorized_cookies ($override_accept = false)
{
	global $cookiename, $modSettings;
	static $storeCookies;

	if (isset($storeCookies) && !$override_accept)
		return $storeCookies;

	if (isset($_SERVER['HTTP_X_MOZ']) && $_SERVER['HTTP_X_MOZ'] == 'prefetch' && isset($_GET['cookieaccept']))
		$storeCookies = false;
	elseif (isset($_COOKIE['ecl_auth']) || isset($_COOKIE[$cookiename]))
		$storeCookies = true;
	elseif (isset($_GET['cookieaccept']) || $override_accept)
	{
		setcookie('ecl_auth', 1, 0, '/');
		$storeCookies = true;
	}
	else
		$storeCookies = false;

	if (!$storeCookies && !empty($modSettings['ecl_strict_interpretation']))
		$modSettings['registration_method'] = 4;

	return $storeCookies;
}

function EclPrivacyNotice ($register = false)
{
	global $context, $user_info, $boarddir, $scripturl, $txt;

	loadTemplatE('EclWarning');
	if (!$register)
		$context['sub_template'] = 'ecl_privacynotice';
	$context['template_layers'][] = 'ecl_privacynotice';
	// Have we got a localized one?
	if (file_exists($boarddir . '/ecl_privacynotice.' . $user_info['language'] . '.txt'))
		$context['ecl_privacynotice'] = parse_bbc(str_replace('{ACCEPTCOOKIES}', $scripturl . '?cookieaccept', file_get_contents($boarddir . '/ecl_privacynotice.' . $user_info['language'] . '.txt')), true, 'ecl_privacynotice_' . $user_info['language']);
	elseif (file_exists($boarddir . '/ecl_privacynotice.txt'))
		$context['ecl_privacynotice'] = parse_bbc(str_replace('{ACCEPTCOOKIES}', $scripturl . '?cookieaccept', file_get_contents($boarddir . '/ecl_privacynotice.txt')), true, 'agreement');
	else
		$context['ecl_privacynotice'] = '';

	if (!$register)
		$context['ecl_privacynotice'] .= '<br /><br />' . $txt['ecl_accept_how_to'];

}

?>
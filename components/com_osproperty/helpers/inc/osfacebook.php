<?php
/*------------------------------------------------------------------------
# osfacebook.php - Ossolution Property
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2023 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
// No direct access.
defined('_JEXEC') or die;

class OSPFacebook
{
    /**
     * This function is used to load Config and return the Configuration Variable
     *
     */
    public static function fbConnect($app_id,$app_secret)
    {
		global $mainframe;
		require_once(JPATH_ROOT.'/components/com_osproperty/helpers/inc'.DIRECTORY_SEPARATOR.'Facebook'.DIRECTORY_SEPARATOR.'autoload.php' );
		$fb = new Facebook\Facebook(['app_id' => $app_id,'app_secret' => $app_secret,'default_graph_version' => 'v2.4']);	

		$helper = $fb->getRedirectLoginHelper();

		try {
		  $accessToken = $helper->getAccessToken();

		} catch(Facebook\Exceptions\FacebookResponseException $e) {
		  // When Graph returns an error
		  echo 'Graph returned an error: ' . $e->getMessage();
		  exit;
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
		  // When validation fails or other local issues
		  echo 'Facebook SDK returned an error: ' . $e->getMessage();
		  exit;
		}

		if ($accessToken){
			$response	= $fb->get('/me?fields=name,likes,accounts', $accessToken);
			$user		= $response->getGraphUser();
			$name		= $user['name'];
			$likes		= $user['likes'];
			$likes		= json_decode($likes);
			$accounts	= $user['accounts'];
			$accounts   = json_decode($accounts);
			?>
			<script type="text/javascript">
			var str = "";
			window.opener.document.getElementById('access_token').value = '<?php echo $accessToken;?>';
				
			var fb_params = window.opener.document.getElementById('fb_params');
			var fb_target = window.opener.document.getElementById('fb_target');
			var ogl = fb_target.getElementsByTagName('optgroup')
			for (var i=ogl.length-1;i>=0;i--) fb_target.removeChild(ogl[i])
			for (var option in fb_target){
				fb_target.remove(option);
			}
			var opt = document.createElement('option');
			opt.value = '';
			opt.innerHTML = '<?php echo $name;?>';
			fb_target.appendChild(opt);
			str += "@@:" + '<?php echo $name;?>';
			
			groupValues = '<?php echo likes;?>';
			if(groupValues){															
				var groupList = [];
				str += "||Pages@@";
				var optgroup = document.createElement('optgroup');
				optgroup.label = "Pages";
				<?php
				if(count($likes) > 0){
					foreach	($likes as $like){
						?>
						var opt = document.createElement('option');
						opt.value = '<?php echo $like->id;?>';
						opt.innerHTML = '<?php echo str_replace("'","\'",$like->name);?>';
						optgroup.appendChild(opt);
						str += "<?php echo $like->id;?>:<?php echo str_replace("'","\'",$like->name);?>{+}";
						<?php
					}
				}
				?>
				str = str.substring(0,str.length-3);
				fb_target.appendChild(optgroup);
			}

			groupValues = '<?php echo account;?>';
			if(groupValues){
				var groupList = [];
				str += "||Accounts@@";
				var optgroup = document.createElement('optgroup');
				optgroup.label = "Accounts";
				
				<?php
				if(count($accounts) > 0){
					foreach	($accounts as $account){
						?>
						var opt = document.createElement('option');
						opt.value = '<?php echo $account->id;?>';
						opt.innerHTML = '<?php echo str_replace("'","\'",$account->name);?>';
						optgroup.appendChild(opt);
						str += "<?php echo $account->id;?>:<?php echo str_replace("'","\'",$account->name);?>{+}";
						<?php
					}
				}
				?>
				str = str.substring(0,str.length-3);
				fb_target.appendChild(optgroup);
			}
			
			fb_params.innerHTML = str;
			window.close();
			</script>
			<?php
		}else{
			$fbpermissions = "publish_actions, user_likes, manage_pages";
			$permissions = explode(",", $fbpermissions);
			$fb_redirect_url = Uri::root()."administrator/index.php?option=com_osproperty&task=configuration_connectfb&app_id=".$app_id."&app_secret=".$app_secret;
			$facebook_login_url = $helper->getLoginUrl($fb_redirect_url, $permissions);
			OSPHelper::redirect($facebook_login_url);
		}
    }
}
?>
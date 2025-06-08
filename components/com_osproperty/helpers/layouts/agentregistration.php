<?php

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Captcha\Captcha;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Language\Text;

OSPHelperJquery::validateForm();
$session            = Factory::getSession();
$post	            = $session->get('post');

$rowFluidClass      = $bootstrapHelper->getClassMapping('row-fluid');
$span12Class        = $bootstrapHelper->getClassMapping('span12');
$controlGroupClass  = $bootstrapHelper->getClassMapping('control-group');
$controlLabelClass  = $bootstrapHelper->getClassMapping('control-label');
$controlsClass      = $bootstrapHelper->getClassMapping('controls');
$inputLargeClass	= $bootstrapHelper->getClassMapping('input-large');
$inputMediumClass	= $bootstrapHelper->getClassMapping('input-medium');
$inputSmallClass	= $bootstrapHelper->getClassMapping('input-small');
$inputMiniClass		= $bootstrapHelper->getClassMapping('input-mini');
?>
<div class="<?php echo $rowFluidClass; ?>" id="agentregisterpage">
	<div class="<?php echo $span12Class; ?>">
		<?php
		if (!$user->id && $configClass['show_agent_login_box'])
		{
			$actionUrl = Route::_('index.php?option=com_users&task=user.login');
			$validateLoginForm = 1;
			?>
			<div class="<?php echo $rowFluidClass; ?>">
				<div class="<?php echo $span12Class; ?>">
					<form class="form-horizontal" method="post" action="<?php echo $actionUrl ; ?>" name="company-login-form" id="company-login-form" autocomplete="off">
						<h3 class="os-heading agentaddress"><?php echo Text::_('OS_EXISTING_USER_LOGIN'); ?></h3>
						<div class="<?php echo $controlGroupClass; ?>">
							<label class="<?php echo $controlLabelClass; ?>" for="username">
								<?php echo  Text::_('OS_USERNAME') ?><span class="required">*</span>
							</label>
							<div class="<?php echo $controlsClass; ?>">
								<input type="text" name="username" class="<?php echo $inputLargeClass; ?> validate[required]" value=""/>
							</div>
						</div>
						<div class="<?php echo $controlGroupClass; ?>">
							<label class="<?php echo $controlLabelClass; ?>" for="password">
								<?php echo  Text::_('OS_PASSWORD') ?><span class="required">*</span>
							</label>
							<div class="<?php echo $controlsClass; ?>">
								<input type="password" name="password" class="<?php echo $inputLargeClass; ?> validate[required]" value="" />
							</div>
						</div>
						<div class="<?php echo $controlGroupClass; ?>">
							<div class="<?php echo $controlsClass; ?>">
								<input type="submit" value="<?php echo Text::_('OS_LOGIN'); ?>" class="button btn btn-primary" />
							</div>
						</div>
						<?php
						if (PluginHelper::isEnabled('system', 'remember'))
						{
						?>
							<input type="hidden" name="remember" value="1" />
						<?php
						}
						?>
						<input type="hidden" name="return" value="<?php echo base64_encode(Uri::getInstance()->toString()); ?>" />
						<?php echo HTMLHelper::_( 'form.token' ); ?>
					</form>
				</div>
			</div>
			<div class="clearfix"></div>
		<?php
		}
		?>
	</div>
</div>
<?php
if($post['name'] != ""){
	$name = $post['name'];
}elseif($user->name != ""){
	$name = $user->name;
}else{
	$name = "";
}

if($post['username'] != ""){
	$username = $post['username'];
}elseif($user->username != ""){
	$username = $user->username;
}else{
	$username = "";
}

if($post['email'] != ""){
	$email = $post['email'];
}elseif($user->email != ""){
	$email = $user->email;
}else{
	$email = "";
}
?>
<form method="POST" action="<?php echo Route::_('index.php?Itemid='.$itemid)?>" name="ftForm" enctype="multipart/form-data" class="form-horizontal" id="agent-register-form" autocomplete="off">
		<?php
		if(intval($user->id) == 0){
		?>
			<div class="<?php echo $rowFluidClass; ?>">
				<div class="<?php echo $span12Class; ?>">
					<h3 class="os-heading"><?php echo Text::_('OS_USER_INFORMATION')?></h3>
					<div class="clearfix"></div>
					<div class="<?php echo $controlGroupClass; ?>">
						<label class="<?php echo $controlLabelClass; ?>" ><?php echo Text::_('OS_NAME')?><span class="required" />*</span></label>
						<div class="<?php echo $controlsClass; ?>">
							<input type="text" name="name" value="<?php echo $name?>" size="20" class="<?php echo $inputLargeClass; ?> validate[required]" placeholder="<?php echo Text::_('OS_AGENT_NAME')?>" autocomplete="off"/>
						</div>
					</div>
					<?php
					if($configClass['use_email_as_agent_username'] == 0){
					?>
						<div class="<?php echo $controlGroupClass; ?>">
							<label class="<?php echo $controlLabelClass; ?>" ><?php echo Text::_('OS_USERNAME')?><span class="required" />*</span></label>
							<div class="<?php echo $controlsClass; ?>">
								<input type="text" name="username" id="username" size="20" class="<?php echo $inputLargeClass; ?> validate[required]" placeholder="<?php echo Text::_('OS_USERNAME')?>" value="<?php echo $username;?>" autocomplete="off"/>
							</div>
						</div>
					<?php } ?>
					<div class="<?php echo $controlGroupClass; ?>">
						<label class="<?php echo $controlLabelClass; ?>" ><?php echo Text::_('OS_EMAIL')?><span class="required" />*</span></label>
						<div class="<?php echo $controlsClass; ?>">
							<input type="text" name="email" id="email" size="20" class="<?php echo $inputLargeClass; ?> validate[required,custom[email]]" placeholder="<?php echo Text::_('OS_EMAIL')?>" value="<?php echo $email; ?>"autocomplete="off"  />
						</div>
					</div>
					
					<div class="<?php echo $controlGroupClass; ?>">
						<label class="<?php echo $controlLabelClass; ?>" ><?php echo Text::_('OS_PWD')?><span class="required" />*</span></label>
						<div class="<?php echo $controlsClass; ?>">
							<input type="password" name="password" id="password1" size="20" class="<?php echo $inputLargeClass; ?> validate[required]" autocomplete="off"/>
						</div>
					</div>
					
					<div class="<?php echo $controlGroupClass; ?>">
						<label class="<?php echo $controlLabelClass; ?>" ><?php echo Text::_('OS_VPWD')?><span class="required" />*</span></label>
						<div class="<?php echo $controlsClass; ?>">
							<input type="password" name="password2" id="password2" size="20" class="<?php echo $inputLargeClass; ?> validate[required,equals[password1]]" autocomplete="off"/>
						</div>
					</div>
				</div>
			</div>
		<?php
		}
		?>


<div class="<?php echo $rowFluidClass; ?>">
	<div class="<?php echo $bootstrapHelper->getClassMapping('span6'); ?>">
		<h3 class="os-heading"><?php echo Text::_('OS_OTHER_INFORMATION')?></h3>
	</div>
</div>
<div class="<?php echo $rowFluidClass; ?>" id="agentprofilepage">
    <div class="<?php echo $bootstrapHelper->getClassMapping('span6'); ?>">
		<div class="<?php echo $controlGroupClass; ?>">
			<div class="<?php echo $controlsClass; ?>">
				<?php echo OSPHelper::loadAgentTypeDropdown(0,$inputLargeClass,'onChange="javascript:updateCompanyDropdown()"');?>
			</div>
		</div>
		<?php
		if($lists['company_id_selected'] > 0){
			?>
			<input type="hidden" name="company_id" value="<?php echo $lists['company_id_selected'];?>" />
			<?php
		}elseif(count($companies) > 0){
			?>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlsClass; ?>">
					<?php echo $lists['company']?>
				</div>
			</div>
			<?php
		}
		if($configClass['show_agent_phone'] == 1){
		?>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlsClass; ?>">
					<input type="text" name="phone" value="<?php echo $post['phone']; ?>" id="phone" size="20" class="<?php echo $inputLargeClass; ?> validate[required]" placeholder="<?php echo Text::_('OS_PHONE')?>" />
				</div>
			</div>
		<?php
		}
		
		if($configClass['show_agent_mobile'] == 1){
		?>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlsClass; ?>">
					<input type="text" name="mobile" id="mobile" value="<?php echo $post['mobile']; ?>" size="20" class="<?php echo $inputLargeClass; ?>" placeholder="<?php echo Text::_('OS_MOBILE')?>" />
				</div>
			</div>
		<?php
		}
		if($configClass['show_agent_fax'] == 1){
		?>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlsClass; ?>">
					<input type="text" name="fax" id="fax" size="20" value="<?php echo $post['fax']; ?>" class="<?php echo $inputLargeClass; ?>" placeholder="<?php echo Text::_('OS_FAX')?>" />
				</div>
			</div>
		<?php
		}
		?>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlsClass; ?>">
					<input type="text" name="address" id="address" size="20" value="<?php echo $post['address']; ?>" class="<?php echo $inputLargeClass; ?> validate[required]" placeholder="<?php echo Text::_('OS_ADDRESS')?>" />
				</div>
			</div>
		<?php
		if(HelperOspropertyCommon::checkCountry()){
		?>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlsClass; ?>">
					<?php echo $lists['country'];?>
				</div>
			</div>
		<?php
		}else{
			echo $lists['country'];
		}
		if(OSPHelper::userOneState()){
			echo $lists['state'];
		}else{
		?>
		<div class="<?php echo $controlGroupClass; ?>">
			<div class="<?php echo $controlsClass; ?>" id="country_state">
				<?php echo $lists['state'];?>
			</div>
		</div>
		<?php } ?>
		<div class="<?php echo $controlGroupClass; ?>">
			<div class="<?php echo $controlsClass; ?>" id="city_div">
				<?php echo $lists['city'];?>
			</div>
		</div>
    </div>
    <div class="<?php echo $bootstrapHelper->getClassMapping('span6'); ?>">
        <?php
        if($configClass['show_agent_skype'] == 1){
            ?>
            <div class="<?php echo $controlGroupClass; ?>">
                <div class="<?php echo $controlsClass; ?>">
                    <input type="text" name="skype" id="skype" size="20" value="<?php echo $post['skype']; ?>" class="<?php echo $inputMediumClass; ?>" placeholder="<?php echo Text::_('Skype')?>" />
                </div>
            </div>
            <?php
        }
        if($configClass['show_agent_msn'] == 1){
            ?>
            <div class="<?php echo $controlGroupClass; ?>">
                <div class="<?php echo $controlsClass; ?>">
                    <input type="text" name="msn" id="msn" size="20" value="<?php echo $post['msn']; ?>" class="<?php echo $inputMediumClass; ?>" placeholder="<?php echo Text::_('Line Messages')?>" />
                </div>
            </div>
            <?php
        }
        if($configClass['show_agent_gplus'] == 1){
            ?>
            <div class="<?php echo $controlGroupClass; ?>">
                <div class="<?php echo $controlsClass; ?>">
                    <input type="text" name="gtalk" id="gtalk" size="20" value="<?php echo $post['gtalk']; ?>" class="<?php echo $inputMediumClass; ?>" placeholder="<?php echo Text::_('Gtalk')?>" />
                </div>
            </div>
            <?php
        }
        if($configClass['show_agent_facebook'] == 1){
            ?>
            <div class="<?php echo $controlGroupClass; ?>">
                <div class="<?php echo $controlsClass; ?>">
                    <input type="text" name="facebook" id="facebook" size="20" value="<?php echo $post['facebook']; ?>" class="<?php echo $inputMediumClass; ?>" placeholder="<?php echo Text::_('Facebook')?>" />
                </div>
            </div>
            <?php
        }
        if($configClass['show_agent_twitter'] == 1){
            ?>
            <div class="<?php echo $controlGroupClass; ?>">
                <div class="<?php echo $controlsClass; ?>">
                    <input type="text" name="aim" id="aim" size="20" value="<?php echo $post['aim']; ?>" class="<?php echo $inputMediumClass; ?>" placeholder="<?php echo Text::_('Twitter')?>" />
                </div>
            </div>
            <?php
        }
        if($configClass['show_agent_linkin'] == 1){
            ?>
            <div class="<?php echo $controlGroupClass; ?>">
                <div class="<?php echo $controlsClass; ?>">
                    <input type="text" name="yahoo" id="yahoo" size="20" value="<?php echo $post['yahoo']; ?>" class="<?php echo $inputMediumClass; ?>" placeholder="<?php echo Text::_('Linkin')?>" />
                </div>
            </div>
            <?php
        }
        if($configClass['show_license'] == 1){
            ?>
            <div class="<?php echo $controlGroupClass; ?>">
                <div class="<?php echo $controlsClass; ?>">
                    <input type="text" name="license" id="license" size="20" value="<?php echo $post['license']; ?>" class="<?php echo $inputMediumClass; ?>" placeholder="<?php echo Text::_('OS_LICENSE')?>" />
                </div>
            </div>
            <?php
        }
		?>
		<?php
		if($configClass['show_agent_image']==1)
		{
		?>
			<div class="<?php echo $controlGroupClass; ?>">
				<div class="<?php echo $controlsClass; ?>">
					<?php echo Text::_('OS_PHOTO')?>
					<Div class="clearfix"></Div>
					<input type="file" class="input-large form-control" name="photo" id="photo" onchange="javascript:checkUploadPhotoFiles('photo');" /> 
				</div>
			</div>
		<?php
		}
	?>
    </div>
</div>
<div class="<?php echo $rowFluidClass; ?>">
    <div class="<?php echo $span12Class; ?>">
    <?php
        if($configClass['captcha_agent_register'] == 1)
        {
            OSPHelper::generateCaptcha();
        }

        //HTMLHelper::_("behavior.modal","a.osmodal");
		OSPHelperJquery::colorbox('a.osmodal');
		if(($configClass['agent_term_condition'] == 1) && ($configClass['agent_article_id'] != ""))
		{
			$termLink = OSPHelper::getAssocArticleId($configClass['agent_article_id']);
		    ?>
			<div class="<?php echo $controlGroupClass; ?>">
				<label class="checkbox">
					<input type="checkbox" name="termcondition" id="termcondition" value="1" class="validate[required]" />
					&nbsp;
					<?php echo Text::_('OS_READ_TERM'); ?> 
					<a href="<?php echo $termLink; ?>" class="osmodal" rel="{handler: 'iframe', size: {x: 600, y: 450}}" title="<?php echo Text::_('OS_TERM_AND_CONDITION');?>"><?php echo Text::_('OS_TERM_AND_CONDITION');?></a>
				</label>
			</div>
			<?php 
		}
        if ($configClass['use_privacy_policy'])
        {
            if ($configClass['privacy_policy_article_id'] > 0)
            {
                $privacyArticleId = $configClass['privacy_policy_article_id'];

                if (Multilanguage::isEnabled())
                {
                    $associations = Associations::getAssociations('com_content', '#__content', 'com_content.item', $privacyArticleId);
                    $langCode     = Factory::getLanguage()->getTag();
                    if (isset($associations[$langCode]))
                    {
                        $privacyArticle = $associations[$langCode];
                    }
                }

                if (!isset($privacyArticle))
                {
                    $db    = Factory::getDbo();
                    $query = $db->getQuery(true);
                    $query->select('id, catid')
                        ->from('#__content')
                        ->where('id = ' . (int) $privacyArticleId);
                    $db->setQuery($query);
                    $privacyArticle = $db->loadObject();
                }

                JLoader::register('ContentHelperRoute', JPATH_ROOT . '/components/com_content/helpers/route.php');

                $link = Route::_(ContentHelperRoute::getArticleRoute($privacyArticle->id, $privacyArticle->catid).'&tmpl=component&format=html');
            }
            else
            {
                $link = '';
            }
            ?>
            <div class="<?php echo $controlGroupClass; ?>">
                <label class="<?php echo $controlLabelClass; ?>">
                    <?php
                    if ($link)
                    {
                        $extra = ' class="osmodal" ' ;
                        ?>
                        <a href="<?php echo $link; ?>" <?php echo $extra;?> class="eb-colorbox-privacy-policy"><?php echo Text::_('OS_PRIVACY_POLICY');?></a>
                        <?php
                    }
                    else
                    {
                        echo Text::_('OS_PRIVACY_POLICY');
                    }
                    ?>
                </label>
                <div class="<?php echo $controlsClass; ?>">
                    <input type="checkbox" name="agree_privacy_policy" id="agree_privacy_policy" value="1" data-errormessage="<?php echo Text::_('OS_AGREE_PRIVACY_POLICY_ERROR');?>" class="validate[required]"/>
                    <?php
                    $agreePrivacyPolicyMessage = Text::_('OS_AGREE_PRIVACY_POLICY_MESSAGE');
                    if (strlen($agreePrivacyPolicyMessage))
                    {
                        ?>
                        <div class="eb-privacy-policy-message alert alert-info"><?php echo $agreePrivacyPolicyMessage;?></div>
                        <?php
                    }
                    ?>
                </div>
            </div>
            <?php
        }
		?>
		<div class="clearfix"></div>
		<div class="btn-toolbar">
            <div class="btn-group">
                <input type="submit" class="btn btn-primary uk-button uk-button-primary btnSubmit" id="btn-submit" value="<?php echo Text::_('OS_SAVE');?>" />
                <input type="reset" class="btn btn-warning uk-button uk-button-primary btnSubmit" title="<?php echo Text::_('OS_RESET');?>" />
             </div>
        </div>
	</div>
</div>
<input type="hidden" name="option" value="com_osproperty" />
<input type="hidden" name="task" value="agent_completeregistration" />
<input type="hidden" name="MAX_FILE_SIZE" value="9000000000" />
<input type="hidden" name="id" value="0" />
<input type="hidden" name="gid" value="0" />
<input type="hidden" name="Itemid" value="<?php echo $itemid;?>" />
<input type="hidden" name="captcha_agent_register" id="captcha_agent_register" value="<?php echo $configClass['captcha_agent_register']?>" />
<input type="hidden" name="use_privacy_policy" id="use_privacy_policy" value="<?php echo $configClass['use_privacy_policy']?>" />
<input type="hidden" name="captcha_str" id="captcha_str" value="<?php echo $ResultStr?>" />
<input type="text" name="osp_my_own_website_name" value="" autocomplete="off" class="osp-invisible-to-visitors" />
<input type="hidden" name="<?php echo OSPHelper::getHashedFieldName(); ?>" value="<?php echo time(); ?>" />
</form>
<script type="text/javascript">
var live_site = '<?php echo Uri::root()?>';
function change_country_company(country_id,state_id,city_id){
	var live_site = '<?php echo Uri::root()?>';
	loadLocationInfoStateCity(country_id,state_id,city_id,'country','state',live_site);
}

function loadCity(state_id,city_id){
	var live_site = '<?php echo Uri::root()?>';
	loadLocationInfoCityAddProperty(state_id,city_id,'state',live_site);
}

function emailValid(emailvalue){
	var filter = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	if (!filter.test(emailvalue)) {
		return false;
	}else{
		return true;
	}
}

OSP.jQuery(function($){
	$(document).ready(function(){
		$("#agent-register-form").validationEngine('attach', {
			onValidationComplete: function(form, status){
				if (status == true) {
					form.on('submit', function(e) {
						e.preventDefault();
					});

					form.find('#btn-submit').prop('disabled', true);
					return true;
				}
				return false;
			}
		});
	})
});


function submitForm(){
	var form = document.ftForm;
	var name = form.name;
	var email = form.email;
	var address = form.address;
	var userid = <?php echo intval($user->id)?>;
	var captcha_agent_register = form.captcha_agent_register;
	var use_privacy_policy = form.use_privacy_policy;
	var allowSubmit = 0;
	<?php
	if($user->id > 0){
	?>
		if(address.value == ""){
			alert("<?php echo Text::_('OS_PLEASE_ENTER_ADDRESS')?>");
			address.focus();
			return false;		
		}else{
			form.submit();
		}
	<?php
	}else{
	?>
	var username = form.username;
	var password = form.password;
	var password2 = form.password2;
	
	if(name.value == ""){
		alert("<?php echo Text::_('OS_PLEASE_ENTER_NAME')?>");
		name.focus();
		return false;
	<?php
	if($configClass['use_email_as_agent_username'] == 0){
	?>
	}else if((username.value == "") && (userid == 0)){
		alert("<?php echo Text::_('OS_PLEASE_ENTER_UNAME')?>");
		username.focus();
		return false;
	<?php } ?>
	}else if((password.value == "") && (userid == 0)){
		alert("<?php echo Text::_('OS_PLEASE_ENTER_PWD')?>");
		password.focus();
		return false;
	}else if((password2.value == "") && (userid == 0)){
		alert("<?php echo Text::_('OS_PLEASE_ENTER_VPWD')?>");
		password2.focus();
		return false;
	}else if((password.value != password2.value) && (userid == 0)){
		alert("<?php echo Text::_('OS_PWDANDVPWDARETHESAME')?>");
		password.focus();
		return false;
	}else if(email.value == ""){
		alert("<?php echo Text::_('OS_PLEASE_ENTER_EMAIL')?>");
		email.focus();
		return false;
	}else if(! emailValid(email.value)){
		alert("<?php echo Text::_('OS_PLEASE_ENTER_VALID_EMAIL')?>");
		email.focus();
		return false;
	}else if(address.value == ""){
		alert("<?php echo Text::_('OS_PLEASE_ENTER_ADDRESS')?>");
		address.focus();
		return false;
    }else if ((use_privacy_policy.value == 1) && (document.getElementById('agree_privacy_policy').checked == false)) {
        alert(" <?php echo Text::_('OS_AGREE_PRIVACY_POLICY_ERROR')?>");
        document.getElementById('agree_privacy_policy').focus();
        return false;
	<?php
	if($configClass['agent_term_condition'] == 1){
		?>
	} else if(document.getElementById('termcondition').checked == false){
		alert(" <?php echo Text::_('OS_PLEASE_AGREE_WITH_OUT_TERM_AND_CONDITION')?>");
		document.getElementById('termcondition').focus();
		return false;
		<?php
	}
	?>
	}else{
		form.submit();
	}
	<?php
	}
	?>
}
</script>

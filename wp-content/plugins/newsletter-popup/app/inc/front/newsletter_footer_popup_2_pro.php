<?php if ( ! defined( 'ABSPATH' ) ) exit;

global $wpdb;
    
$hiddenstyle="";

if(is_user_logged_in()){
 global $user_ID;
 
 $user_info = get_userdata($user_ID);
 $usermail = $user_info->user_email;

  global $wpdb;
  $tbl = $wpdb->prefix.'mk_newsletter_local_records';

  
  $local_records = $wpdb->get_results('select nl_data from '.$tbl.' where nl_id="'.$newsletter->id.'" order by rid DESC',ARRAY_A);
    
    foreach ($local_records as $lvalue) {
      $userdetail=json_decode( $lvalue['nl_data'] ,true) ;
      if($usermail== $userdetail['EMAIL']){
        $hiddenstyle="hidden";
        break;
      }
    }

  }

$tbl = $wpdb->prefix.'mk_newsletter_data';
$newsletters = $wpdb->get_results("select * from ".$tbl." where status = 'publish'");
$action = isset($_GET['action']) ? $_GET['action'] : '';
$cookieDelete = false;
if(!empty($newsletters) && is_array($newsletters ))
{ 
 foreach($newsletters  as $newsletter) {
	$decodedData = json_decode($newsletter->data, true);
	if(!isset($_COOKIE['newsletter_'.$newsletter->id])) { 
	?>
<script>
<?php if($decodedData['wp_newsletter_popup_show_on_window_close'] == 'enable') {?>
window.onbeforeunload = function(e){ 
 return jQuery(".subscribebox_<?php echo $newsletter->id;?>").fadeIn();
}

<?php } ?>
   <?php if(isset($decodedData['wp_newsletter_popup_time']) && !empty($decodedData['wp_newsletter_popup_time'])) { ?>
	setTimeout(function(){ jQuery(".subscribebox_<?php echo $newsletter->id;?>").fadeIn(); }, <?php echo ($decodedData['wp_newsletter_popup_time'] * 1000);?>);
	<?php } else { ?>
	jQuery(window).load(function(e) {

     function setCookiePopup(c_name,value,exdays) {
    var exdate=new Date();
    exdate.setDate(exdate.getDate() + exdays);
    var expires = exdate.toUTCString();
    var isIE8 = (document.documentMode !== undefined);
    if (exdays == 0) {
        expires = (isIE8 == true) ? "" : "0";
    }
    var c_value=escape(value) + ((exdays==null) ? "" : "; expires="+expires);
    document.cookie=c_name + "=" + c_value;
}

function getCookiePopup(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) != -1) return c.substring(name.length,c.length);
    }
    return "";
}

 function deleteCookiePopup(name) {
  document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
}
  if (getCookiePopup('visited')) {
    // deleteCookiePopup('visited');
  } 
  else {
   
    setCookiePopup('visited','true',1); //999 days expiration
      <?php if($hiddenstyle!='hidden') { ?>  
      jQuery(".subscribebox_<?php echo $newsletter->id;?>").fadeIn();
      <?php } ?>
  }
    });
	<?php } ?>
	jQuery('.nlp_show_oc_<?php echo $newsletter->id;?>').live('click', function(e) {
		jQuery(".subscribebox_<?php echo $newsletter->id;?>").fadeIn();
	});

	jQuery(document).ready(function(){
		 jQuery(".close_<?php echo $newsletter->id;?>").click(function(e){
			 e.preventDefault();
			jQuery(".subscribebox_<?php echo $newsletter->id;?>").fadeOut();
		});
	});
</script>
<style>
/** Newsletter 2 **/
#main-div
{
	width:100%;
	margin: auto;
	padding: 25px;
	height: 100vh;
    left: 0;
    position: fixed;
    z-index: 9999;
    background: rgba(0, 0, 0, 0.8);
    padding: 0px 15px;
    box-sizing: border-box;
    -webkit-box-sizing: border-box;
	top: 0;
}
.webdesign-newsLetter
{
	width:<?php echo $decodedData['wp_newsletter_width']; ?>%;
	max-width: <?php echo $decodedData['wp_newsletter_maxwidth']; ?>px;
	padding: 0;
	border-radius: 1px;
	background-size:cover;
	margin:55px auto;
	margin-top: <?php echo $decodedData['wp_newsletter_mintopbottommargin'].''.'px';?> auto;
	position: relative;
	z-index:9999;
}

.webdesign-newsLetter .upper-div
{
	background:#f7f6ed;
	padding:5px;
}
.webdesign-newsLetter .lower-div
{
	width:100%;
	max-width:760px;
    padding: 50px 22px;
	border-radius: 1px;
	background:url(<?php echo plugins_url( 'images/popupbg2.jpg',__FILE__); ?>) no-repeat;
	background-size:cover;
	margin:1px auto;
	position: relative;
	z-index:9999;
	background-position: right bottom;
}
.webdesign-newsLetter .upper-div h1
{
	color: #404040;
	font-weight:bold;
	font-size: 48px;
	text-align:center;
	letter-spacing:2px;
	padding: 30px 0 10px;
	font-family: 'Playfair Display', serif;
	line-height: 47px;
	font-style: italic;
}
 .right-div .wordpress-text
{
	color: #2d4270;
	font-size: 68px;
	text-align:center;
	font-family: 'Oswald', sans-serif;
	text-transform:uppercase;
}
 .right-div .checklist-text
{
	color: #2d4270;
	font-size: 18px;
	text-align:center;
	font-family: 'Oswald', sans-serif;
	text-transform: capitalize;
	font-weight: 100;
}
.webdesign-newsLetter p
{
	text-align:center;
	line-height:25px;
	color: #333;
	margin:15px;
	font-family: 'Roboto', sans-serif;
	font-size: 18px;
}
.webdesign-newsLetter .subscribeThreebutton {
    font-family: 'Roboto', sans-serif;
    background: #7ab52d;
    color: #fff;
    padding: 24px;
    text-transform: uppercase;
    margin:5px;
    border: none;
    font-weight: 600;
    letter-spacing: 2px;
    max-width: 200px;
    width: 100%;
    margin:0px 10px;
}
.webdesign-newsLetter .email-textbox
{
	padding: 20px;
	width: 100%;
	max-width: 400px;
	margin: 0px;
	box-sizing: border-box;
	font-family: 'Oswald', sans-serif;
	font-size: 15px;
	color: #89b7bf !important;
	display:inline-block;
	vertical-align:top;
}
.webdesign-newsLetter .terms-box {
    width: 100%;
    text-align: center;
    color: #fff;
    margin: 10px 0;
}
.webdesign-newsLetter .privacy-text
{
	display: inline-block;
    width: 100%;
    text-align: center;
}
.webdesign-newsLetter form
{
    margin: 70px auto;
    padding: 25px 25px 10px;
    margin-bottom: 0;
}
input::-webkit-input-placeholder {
color: #89b7bf !important;
}
.webdesign-newsLetter .thanksText
{
	width:100%;
	text-align:center;
	margin:5px auto;
	display:inline-block;
}
.webdesign-newsLetter .close-button {
  position: absolute;
  right: -4px;
  top: 45px;
  width: 35px;
  height: 35px;
  border-radius: 100%;
  background: <?php if(isset($decodedData['wp_newsletter_close_bg_color']) && !empty($decodedData['wp_newsletter_close_bg_color'])){ echo $decodedData['wp_newsletter_close_bg_color'];}else{ echo "#7ab52d;";}?>;
  padding: 6px 0;
  z-index:99999;
 }
 
 .webdesign-newsLetter .close-button:hover {
    background: <?php if(isset($decodedData['wp_newsletter_close_hover']) && !empty($decodedData['wp_newsletter_close_hover'])){ echo $decodedData['wp_newsletter_close_hover'];}else{ echo "#05a396;";}?>;
    transition: .2s;
}

.webdesign-newsLetter .close-button:before, .close-button:after {
  position: absolute;
  left: 17px;
  content: ' ';
  height: 20px;
  width: 2px;
  background-color: #fff;
}

.webdesign-newsLetter .close-button:before {
  transform: rotate(45deg);
}
.webdesign-newsLetter .close-button:after {
  transform: rotate(-45deg);
}
.webdesign-newsLetter .ribbon{
 background:url(<?php echo $decodedData['wp_newsletter_ribbon']; ?>) no-repeat;
 position:absolute;
	top: -8px !important;
    left: -8px !important;
	z-index: 99999;
}
.error_container {
    text-align: center;
    color: #ff0000;
}
@media screen and (max-width:991px)
{
	.webdesign-newsLetter form{padding:25px 14px 10px !important;}
}
@media screen and (max-width:767px)
{
	.webdesign-newsLetter .email-textbox{margin:5px auto !important;  max-width:inherit !important;margin-bottom: 10px !important;}
	.webdesign-newsLetter .subscribeThreebutton{ margin:30px 0px; max-width:inherit;}
	.webdesign-newsLetter .lower-div{padding: 5px !important;}
	.webdesign-newsLetter form {padding: 5px !important;}
	.webdesign-newsLetter  .box-center{margin: 5px auto !important;}
	.webdesign-newsLetter .news-box{overflow-y:auto; max-height:80vh;}
	.webdesign-newsLetter .upper-div h1 {font-size: 24px !important; line-height: 30px;}
	.webdesign-newsLetter .close-button {z-index:99999;}
	
}
@media screen and (max-width:480px)
{
	.webdesign-newsLetter .email-textbox{ margin:5px auto !important;  max-width:inherit !important;}
	.webdesign-newsLetter .subscribeThreebutton{ margin:30px 0px; max-width:inherit;}
	.webdesign-newsLetter .lower-div {padding: 5px !important;}
	.webdesign-newsLetter form {padding: 5px !important;}
	.webdesign-newsLetter  .box-center{margin: 5px auto !important;}
	
}
@media screen and (max-width:374px)
{
	.webdesign-newsLetter .upper-div h1 {font-size: 20px !important; line-height: 20px;}
	.webdesign-newsLetter .lower-div {padding: 5px !important;}
	.webdesign-newsLetter form {padding: 5px !important;}
	.webdesign-newsLetter .email-textbox { margin: 5px 0px !important;}
}
</style>
<?php 
/*style of Close arrow btn*/
if($decodedData['wp_newsletter_closeposition']=="top-left-inside")
{?>
<style>
.webdesign-newsLetter .close-button {
    left: -5px !important;
    top: -3px  !important;
    background: none;
}
.webdesign-newsLetter .close-button:hover{  background: none;}
.webdesign-newsLetter .close-button:before, .close-button:after {
    background-color: #333;
}
</style>
<?php }else if($decodedData['wp_newsletter_closeposition']=="top-right-inside")
{
	?>
<style>
.webdesign-newsLetter .close-button {
    right: -2px !important;
    top: -2px  !important;
    background: none;
}
.webdesign-newsLetter .close-button:hover{background: none;}
.webdesign-newsLetter .close-button:before, .close-button:after {
    background-color: #333;
}
</style>
<?php }else if($decodedData['wp_newsletter_closeposition']=="top-left-outside")
{?>
<style>
.webdesign-newsLetter .close-button {
    left: -20px;
    top: -18px;
   /* background: #fff;
    border: none;*/
}
.webdesign-newsLetter .close-button:before, .close-button:after {
   top: 7px;
}
@media screen and (max-width:480px)
{
	.webdesign-newsLetter .close-button {left: -11px !important;top: -9px !important;}
}
</style>
<?php }else if($decodedData['wp_newsletter_closeposition']=="top-right-outside")
{?>
<style>
.webdesign-newsLetter .close-button { 
	right: 17px;
	top: 5px;
}
@media screen and (max-width:480px)
{
	.webdesign-newsLetter .close-button {right: -11px !important;top: -9px !important;}
}
</style>
<?php }?>
<?php $count = 0;?>
<div id="main-div" class="subscribebox_<?php echo $newsletter->id;?> " style="display: none;">
<div class="animated <?php echo $decodedData['wp_newsletter_animation'];?>">
<div class="webdesign-newsLetter">
    <div class="news-box">
	<?php
	/*For Ribbon icon*/
	if(isset($decodedData['wp_newsletter_ribbon_show'])&& $decodedData['wp_newsletter_ribbon_show'] == '1') {
		?>
	<div class="ribbon" style="<?php echo $decodedData['wp_newsletter_ribboncss'];?>"><img src=" <?php echo $decodedData['wp_newsletter_ribbon']; ?>" /></div>
    <?php }?>
	<a href="#" title="<?php if(isset($decodedData['wp_newsletter_closetip_show'])&& $decodedData['wp_newsletter_closetip_show'] == '1'){echo stripslashes($decodedData['wp_newsletter_closetip']);}?>" class="close-button close_<?php echo $newsletter->id;?> agree"></a>
		<div class="upper-div">
			<h1 style="color:<?php echo $decodedData['wp_newsletter_heading_color'];?>"><?php echo $decodedData['wp_newsletter_heading'];?></h1>
			<p style="color:<?php echo $decodedData['wp_newsletter_description_color'];?>"><?php echo $decodedData['wp_newsletter_description'];?></p>
		</div>
		<div class="lower-div" style="border-radius:<?php echo $decodedData['wp_newsletter_radius'].'px'; ?>; box-shadow:<?php echo $decodedData['wp_newsletter_bordershadow']; ?>;<?php if(!empty($decodedData['wp_newsletter_backgroundimage'])){ ?>background:url(<?php echo $decodedData['wp_newsletter_backgroundimage'];?>);background-repeat:<?php echo $decodedData['wp_newsletter_backgroundimagerepeat'];?>;background-position:<?php echo $decodedData['wp_newsletter_backgroundimageposition'];?>;<?php } else {?>background:<?php echo $decodedData['wp_newsletter_backgroung_color']; }?>;">
			<div class="error_container">                     
				 <span class="error" id="error_<?php echo $newsletter->id;?>"></span> 
				 <!--<span class="subscribe-box-afteractionmessage"></span>-->
            </div> 
			<form class="subscribe-box-form" name="nl_<?php echo $newsletter->id;?>"  id="nl_<?php echo $newsletter->id;?>">
				<!-- Email -->
                <?php if(isset($decodedData['wp_newsletter_showaction'])&&  $decodedData['wp_newsletter_showemail'] == '1') { 
	$count++;
	?>
				<input class="email-textbox  subscribe-box-email validate_<?php echo $newsletter->id;?> email_validate_<?php echo $newsletter->id;?> custom-input" name="<?php echo $decodedData['wp_newsletter_emailfieldname'];?>" placeholder="<?php echo $decodedData['wp_newsletter_email'];?>" type="text">
                	<?php } ?>
				<!-- Name -->
				<?php if(isset($decodedData['wp_newsletter_showname'])&& $decodedData['wp_newsletter_showname'] == '1'){
				$count++;	
					?>
				<input class="email-textbox subscribe-box-email validate_<?php echo $newsletter->id;?> custom-input" name="<?php echo $decodedData['wp_newsletter_namefieldname'];?>" placeholder="<?php echo $decodedData['wp_newsletter_name'];?>" type="text">
				<?php } ?>
				
				<!-- First Name -->
				<?php if(isset($decodedData['wp_newsletter_showfirstname'])&& $decodedData['wp_newsletter_showfirstname'] == '1') {$count++; ?>
				<input class="email-textbox subscribe-box-email validate_<?php echo $newsletter->id;?> custom-input" name="<?php echo $decodedData['wp_newsletter_firstnamefieldname'];?>" placeholder="<?php echo $decodedData['wp_newsletter_firstname'];?>" type="text">
				<?php } ?>
			
				<!-- Last Name -->
				<?php if(isset($decodedData['wp_newsletter_showlastname'])&& $decodedData['wp_newsletter_showlastname'] == '1') { $count++;?>
				<input class="email-textbox subscribe-box-email validate_<?php echo $newsletter->id;?> custom-input" name="<?php echo $decodedData['wp_newsletter_lastnamefieldname'];?>" placeholder="<?php echo $decodedData['wp_newsletter_lastname'];?>" type="text">
				<?php } ?>
				
				<!-- Phone -->
				<?php if(isset($decodedData['wp_newsletter_showphone'])&& $decodedData['wp_newsletter_showphone'] == '1') {$count++; ?>
				<input class="email-textbox subscribe-box-email validate_<?php echo $newsletter->id;?> custom-input" name="<?php echo $decodedData['wp_newsletter_phonefieldname'];?>" placeholder="<?php echo $decodedData['wp_newsletter_phone'];?>" type="text">
				<?php } ?>
			
				<!-- company -->
				<?php if(isset($decodedData['wp_newsletter_showcompany'])&& $decodedData['wp_newsletter_showcompany'] == '1') { $count++;?>
				<input class="email-textbox subscribe-box-email validate_<?php echo $newsletter->id;?> custom-input" name="<?php echo $decodedData['wp_newsletter_companyfieldname'];?>" placeholder="<?php echo $decodedData['wp_newsletter_company'];?>" type="text">
				<?php } ?>
			
				<!-- zip -->
				<?php if(isset($decodedData['wp_newsletter_showzip'])&& $decodedData['wp_newsletter_showzip'] == '1') {
					$count++;?>
				<input class="email-textbox subscribe-box-email validate_<?php echo $newsletter->id;?> custom-input" name="<?php echo $decodedData['wp_newsletter_zipfieldname'];?>" placeholder="<?php echo $decodedData['wp_newsletter_zip'];?>" type="text">
				<?php } ?>
				
				<!-- msg -->
				<?php if(isset($decodedData['wp_newsletter_showmessage'])&& $decodedData['wp_newsletter_showmessage'] == '1') { $count++;?>
				<textarea class="email-textbox subscribe-box-email validate_<?php echo $newsletter->id;?> custom-input" name="<?php echo $decodedData['wp_newsletter_messagefieldname'];?>" placeholder="<?php echo $decodedData['wp_newsletter_message'];?>"></textarea>
				<?php } ?>
				   <!-- terms -->
				<div class="terms-box">
				<?php if(isset($decodedData['wp_newsletter_showterms'])&& $decodedData['wp_newsletter_showterms'] == '1') { ?>
				<input type="checkbox" value="Yes" name="TERMS" <?php echo($decodedData['wp_newsletter_termsrequired'] == '1') ? 'required' : '';?>/ <?php if($decodedData['wp_newsletter_termsrequired'] == '1') {?>id="term_<?php echo $newsletter->id;?>"<?php }?>> <?php echo $decodedData['wp_newsletter_terms'];?>
				<span id="error_terms_<?php echo $newsletter->id;?>" class="error"></span>
				<?php }else {
					$wp_newsletter_showterms = 0;
					} ?>	
				<!--Subscribe Now Button--->
				<?php if(isset($decodedData['wp_newsletter_showaction'])&& $decodedData['wp_newsletter_showaction'] == '1'):?>
					<input class="subscribe-box-action subscribeThreebutton" name="subscribe-box-action" value="<?php echo $decodedData['wp_newsletter_action'];?>" id="subscribe-box-action-<?php echo $newsletter->id;?>" type="button">
					<div class="subscribe-box-afteractionmessage" style="display: none;"></div>
				<?php endif;?>
             	 <input type="hidden" value="<?php echo date("Y-m-d h:i:s");?>" name="TIME"/>
            <input type="hidden" name="nl_pop_page" value="<?php echo (is_home() ? 'blog' : $post->ID);?>" />
				</div>
                <!--Cancel Button-->
   <?php if(isset($decodedData['wp_newsletter_showcancel'])&& $decodedData['wp_newsletter_showcancel'] == '1') { ?>
   <div class="subscribe-box-cancel close_<?php echo $newsletter->id;?> thanksText"><a href="#" class="thanksText"><?php echo $decodedData['wp_newsletter_cancel'];?></a></div>
   <?php } ?>
			</form>
			<!--Privacy Text--->
			<?php if(!empty($decodedData['wp_newsletter_privacy_show']) && ($decodedData['wp_newsletter_privacy_show']==1) ):?>
				<div class="privacy-text" style="color:<?php echo $decodedData['wp_newsletter_privacy_color']."!important"; ?>"><?php echo $decodedData['wp_newsletter_privacy'];?></div>
			<?php endif; ?>	
			
		</div>
		<div class="clear"></div>
    </div>
    </div>
</div>
	
</div>


<?php

if($count == 1 && (isset($wp_newsletter_showterms)&& $wp_newsletter_showterms == '0'))
{
	?>
	<style>
	.webdesign-newsLetter .subscribeThreebutton {
    padding: 24px;    
	margin:5px; 
	max-width: 200px;
    margin:0px 10px;
}
.webdesign-newsLetter .email-textbox
{
	padding: 20px;
	max-width: 400px;
	margin: 0px;
	float:left;
}
.webdesign-newsLetter .terms-box {
    text-align: center;
    margin:0px;
}
.webdesign-newsLetter .privacy-text
{
	display: inline-block;
    text-align: center;
}
	</style>
<?php 
}
?>
<?php
if($count == 1 && (isset($decodedData['wp_newsletter_showterms'])&& $decodedData['wp_newsletter_showterms'] == '1')){
	?>
	<style>
	.webdesign-newsLetter .email-textbox {
    margin: 5px auto;
    max-width: 400px;
    padding: 20px;
    display: block;
    float: none;
}
.webdesign-newsLetter .terms-box {
    text-align: center;
    margin: 10px 0;
}
.webdesign-newsLetter .privacy-text
{
	display: inline-block;
    text-align: center;
}
.webdesign-newsLetter .subscribeThreebutton {
    padding: 24px;    
	margin:15px auto;
	display:block;
}
	</style>
	
<?php }elseif($count == 3 || $count == 5 || $count==7){?>
	<style>
	.webdesign-newsLetter .email-textbox{
    margin: 5px 5px;
	max-width:300px;
	padding:10px;
	float:none;
}
.webdesign-newsLetter .terms-box {
    margin: 10px 0 0;
    display: inline-block;
}
.webdesign-newsLetter .subscribeThreebutton {
    margin: 10px auto;
    display: block;
	padding:20px;
}
.webdesign-newsLetter .lower-div {
    padding: 15px 45px;
	background-position: center center;
}
.webdesign-newsLetter .upper-div h1 {
    font-size: 40px;
    padding: 10px 0;
    margin: 0;
}
.webdesign-newsLetter p {
    margin: 5px;
}
.webdesign-newsLetter .close-button {
    right: -13px;
    top: -15px;
}
.webdesign-newsLetter  .box-center
{
	float:none;
	display:block;
	margin:5px auto;
}
.webdesign-newsLetter form {
    margin: 5px auto;
    padding: 25px 20px 10px;
    margin-bottom: 0;
}
	</style>
	
<?php
}elseif($count == 2 || $count == 4 || $count==6 || $count==8)
{?>
<style>
.webdesign-newsLetter .email-textbox{
    margin: 5px 5px;
	max-width:300px;
	padding:10px;
	float:none;
}
.webdesign-newsLetter .terms-box {
    margin: 10px 0 0;
    display: inline-block;
}
.webdesign-newsLetter .subscribeThreebutton {
    margin: 10px auto;
    display: block;
	padding:20px;
}
.webdesign-newsLetter .lower-div {
    padding: 15px 45px;
	background-position: center center;
}
.webdesign-newsLetter .upper-div h1 {
    font-size: 40px;
    padding: 10px 0;
    margin: 0;
}
.webdesign-newsLetter p {
    margin: 5px;
}
.webdesign-newsLetter form {
    margin: 5px auto;
    padding: 25px 25px 10px;
    margin-bottom: 0;
}
.webdesign-newsLetter .close-button {
    right: -13px;
    top: -15px;
}
.webdesign-newsLetter form{padding: 10px 20px 10px;}

</style>
<?php 
}
?>
<script>
	jQuery(document).ready(function() {		
	    var count = 0;
		jQuery( ".custom-input" ).each(function( i ) {			
			count++;
		});
		if(parseInt(count) == 3 || parseInt(count)== 5 || parseInt(count)== 7){
			jQuery('.custom-input:last').addClass("box-center");
		}
		var ajax_url = "<?php echo admin_url('admin-ajax.php');?>";
		jQuery('#subscribe-box-action-<?php echo $newsletter->id;?>').click(function(e) {
			//alert('working');
			e.preventDefault();
			var go_ahead = true; 
			var required = false;
			jQuery(".validate_<?php echo $newsletter->id;?>").each(function() { 
			var val = jQuery(this).val();
			 if(val == '')
			 {
				go_ahead = false; 
				required = true;
			 }
			 else
			 {
				go_ahead = true; 
				required = false;
			 }
			});
			if(required)
			{
				jQuery('#error_<?php echo $newsletter->id;?>').text('<?php echo $decodedData['wp_newsletter_fieldmissingmessage'];?>');
			}
			else if(!ValidateEmail<?php echo $newsletter->id;?>(jQuery('.email_validate_<?php echo $newsletter->id;?>').val()))
			{
				jQuery('#error_<?php echo $newsletter->id;?>').text('<?php echo $decodedData['wp_newsletter_invalidemailmessage'];?>');
				go_ahead = false; 
			}
			else if(jQuery('#term_<?php echo $newsletter->id;?>').length)
			{
				if(!jQuery('#term_<?php echo $newsletter->id;?>').is(':checked'))
				{
					jQuery('#error_terms_<?php echo $newsletter->id;?>').html('<p><?php echo $decodedData['wp_newsletter_termsnotcheckedmessage'];?></p>');
					go_ahead = false; 
				}
			}
			else
			{
				jQuery('#error_terms_<?php echo $newsletter->id;?>').text('');
				jQuery('#error_<?php echo $newsletter->id;?>').text('');
				
			}
			if(go_ahead) 
			{
				var data = {
					'action': 'save_newsletter',
					'nl_id': '<?php echo $newsletter->id;?>',
					'nl_name': '<?php echo $newsletter->name;?>',
					'nl_data': jQuery("#nl_<?php echo $newsletter->id;?>").serialize()
				};
				// We can also pass the url value separately from ajaxurl for front end AJAX implementations
				jQuery.post(ajax_url, data, function(response) {
				var obj = jQuery.parseJSON(response);
				var action = obj.action;
				if(action == 'close')
				{
				   jQuery(".subscribebox_<?php echo $newsletter->id;?>").hide();
				}
				else if(action == 'redirect')
				{
					var redirect = obj.redirect;
					window.location.href = redirect;
				}
				else if(action == 'display')
				{
					var redirect = obj.redirect;
					jQuery('.subscribe-box-afteractionmessage').show().text(redirect);
					jQuery(".validate_<?php echo $newsletter->id;?>").each(function() { 
					var val = jQuery(this).val('');
					});
				 }
				}); 
			}
			});
	});
	function ValidateEmail<?php echo $newsletter->id;?>(email) {
			var expr = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
			return expr.test(email);
	};
	</script>
<?php
	}/*End of foreach loop*/
 }
 } /*End if */
?>
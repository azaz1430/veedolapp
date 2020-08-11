<?php include(plugin_dir_path( dirname(__FILE__) ) . 'functions/index.php'); ?>

<!doctype html>
<html <?php language_attributes(); ?> >
    <head>
        <meta charset="<?php bloginfo('charset'); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="profile" href="http://gmpg.org/xfn/11">
        <?php wp_head(); ?>
        <style>
          .mstore_input {
            margin-bottom: 10px;
            width:400px !important;
			  padding: .857em 1.214em !important;
				background-color: transparent;
				color: #818181 !important;
				line-height: 1.286em !important;
				outline: 0;
				border: 0;
				-webkit-appearance: none;
				border-radius: 1.571em !important;
				box-sizing: border-box;
				border-width: 1px;
				border-style: solid;
				border-color: #ddd;
				box-shadow: inset 0 1px 2px rgba(0,0,0,.07) !important;
				transition: 50ms border-color ease-in-out;
				font-family: "Open Sans",HelveticaNeue-Light,"Helvetica Neue Light","Helvetica Neue",Helvetica,Arial,"Lucida Grande",sans-serif;
			    touch-action: manipulation;
          }
          .mstore_button {
            position: relative;
            border: 0 none;
            border-radius: 3px !important;
            color: #fff !important;
            display: inline-block;
            font-family: 'Poppins','Open Sans', Helvetica, Arial, sans-serif;
            font-size: 12px;
            letter-spacing: 1px;
            line-height: 1.5;
            text-transform: uppercase;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            margin-bottom: 21px;
            margin-right: 10px;
            line-height: 1;
            padding: 12px 30px;
            background: #39c36e !important;
            -webkit-transition: all 0.21s ease;
            -moz-transition: all 0.21s ease;
            -o-transition: all 0.21s ease;
            transition: all 0.21s ease;
          }
          .mstore_title{
            font-size: 18px;
            font-weight: 500;
            margin-bottom: .5em;
            line-height: 1.1;
            display: block;
            margin-inline-start: 0px;
            margin-inline-end: 0px;
          }
          .mstore_list{
			margin: 0;
			padding: 0;
			border: 0;
			font-size: 100%;
			font: inherit;
			vertical-align: baseline;
			display: block;
			margin-block-start: 1em;
			margin-block-end: 1em;
			margin-inline-start: 0px;
			margin-inline-end: 0px;
			padding-inline-start: 40px;
            list-style: none;
          }
          .mstore_list li{
            list-style-type: square;
            font-size: 14px;
            font-weight: normal;
            margin-bottom: 6px;
            display: list-item;
            text-align: -webkit-match-parent;
          }
			.mstore_number_list li{
				list-style-type:decimal;
			}
			.mstore_link{
				margin-inline-start: 0px;
            	margin-inline-end: 0px;
				color: #0099ff;
				text-decoration: none;
				outline: 0;
				transition-property: border,background,color;
				transition-duration: .05s;
				transition-timing-function: ease-in-out;
				margin: 0;
				padding: 0;
				border: 0;
				font-size: 100%;
				font: inherit;
				vertical-align: baseline;
				margin-bottom: 20px;
				display: block;
			}
        </style>
    </head>
  <body>
<div class="wrap">
	<h1>MStore API Settings</h1>

  <br>

  <div class="thanks">
  <p style="font-size: 16px;">Thank you for installing Mstore API plugins.</p>
  <?php 
   $verified = get_option("mstore_purchase_code");
   if($verified){
     ?>
      <p style="font-size: 16px;color: green">Your website have been license and all the API features are unlocked. </p>
     <?php
   }
  ?>
	</div>
</div>
<?php
$verified = get_option("mstore_purchase_code");
if(!isset($verified) || $verified == false){
?>
  <form action="" enctype="multipart/form-data" method="post" style="margin-bottom:50px">
    <?php
    if (isset($_POST['but_verify'])) {     
      $verified = verifyPurchaseCode($_POST['code']);

      if ($verified !== true) {
        ?>
        <p style="font-size: 16px;color: red;"><?=$verified?></p>
        <?php
      }else{
        ?>
        <p style="font-size: 16px;color: green">Your website have been license and all the API features are unlocked. </p>
      <?php
      }
    }
    ?>
    <div class="form-group" style="margin-top:10px">
        <input name="code" placeholder="Purchase Code" type="text" class="mstore_input">
    </div>
    <div>
    <h4 class="mstore_title">What is purchase code?</h4>
    <ul class="mstore_list">
      <li>A purchase code is a license identifier which is issued with the item once a purchase has been made and included with your download.</li>
      <li>One purchase code is used for one website only.</li>
      <li>It's required to active to unlock the API use to connect with the app.</li>
    </ul>
    <h4 class="mstore_title">How can I get my purchase code? </h4>
    <ul class="mstore_list mstore_number_list">
      <li>Log into your Envato Market account.</li>
      <li>Hover the mouse over your username at the top of the screen.</li>
      <li>Click ‘Downloads’ from the drop-down menu.`</li>
      <li>Click ‘License certificate & purchase code’ (available as PDF or text file).</li>
    </ul>
    <a class="mstore_link" href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-">https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-</a>
    </div>
    <button type="submit" class="mstore_button" name='but_verify'>Verify</button>
  </form>
<?php
}else{
  checkCurrentPurchaseCode();
}

if(isset($verified) && $verified == true){
?>

<div class="thanks">
  <p style="font-size: 16px;">This setting help to speed up the mobile app performance,  upload the config.json from the common folder:</p>
</div>
  <form action="" enctype="multipart/form-data" method="post">
  
    <div class="form-group" style="margin-top:30px">
        <input id="fileToUpload" accept=".json" name="fileToUpload" type="file" class="form-control-file">
    </div>
    
    <p style="font-size: 14px; color: #1B9D0D; margin-top:10px">
    <?php
    if (isset($_POST['but_submit'])) {     
      wp_upload_bits($_FILES['fileToUpload']['name'], null, file_get_contents($_FILES['fileToUpload']['tmp_name'])); 
      $uploads_dir = str_replace('plugins/mstore-api/templates','uploads',dirname( __FILE__ ));
      $source      = $_FILES['fileToUpload']['tmp_name'];
      $destination = trailingslashit( $uploads_dir ) . '2000/01/config.json';
      if (!file_exists($uploads_dir."/2000/01")) {
        mkdir($uploads_dir."/2000/01", 0777, true);
      }
      move_uploaded_file($source, $destination);
      echo "The caching is active.";
    }else{
      if (file_exists($uploads_dir = str_replace('plugins/mstore-api/templates','uploads',dirname( __FILE__ ))."/2000/01/config.json")) {
        echo "The caching is active.";
      }
    }
    ?>
    </p>

    <button type="submit" class="mstore_button" name='but_submit'>Save</button>
    </form>
<?php
}
?>
  </body>
</html>
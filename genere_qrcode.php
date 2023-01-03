 <?php
	
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	
    include('phpqrcode/qrlib.php');
    
    // we need to be sure ours script does not output anything!!!
    // otherwise it will break up PNG binary!
    
    // ob_start("callback");
    
    // here DB request or some processing
    // $codeText = $param;
    
    // end of processing here
    // $debugLog = ob_get_contents();
    // ob_end_clean();
    
	if (!file_exists($pngAbsoluteFilePath)) {
        QRcode::png($urlvalidation, $pngAbsoluteFilePath, QR_ECLEVEL_M, 4 ,4);
    } 

?>
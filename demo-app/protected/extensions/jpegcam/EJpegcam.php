<?php
class EJpegcam extends CWidget
{
    /**
     * The url to call to save the jpg
     * = api_url parameter (See Jpegcam documentation)
     */
    public $apiUrl = '';

    /**
     * Jpeg Quality
     */
    public $jpegQuality = 90;

    /**
     * true = Play sound
     * false = No sound
     * string = Sound relative url
     */
    public $shutterSound = true;
    
    /**
     * Boolean. When enabled, this causes the image to be captured and uploaded 
     * without interrupting the video preview. Meaning, the snapshot is not "frozen", 
     * but instead the webcam video continues to be played. 
     */
    public $stealth = false;

    /**
     * Size of webcam
     */
    public $camWidth = 320;
    public $camHeight = 240;
    public $serverWidth = false;
    public $serverHeight = false;

    /**
     * Associative array of buttons to show having
     *   $key = one of 'configure, takesnapshot, freeze, upload, reset'
     *   $val = the string to show on the button
     */
    public $buttons = array();

    /**
     * JavaScript code to be executed before taking the picture. Eg:
     * "document.getElementById('upload_results').innerHTML = '<h1>Uploading...</h1>';"
     */
    public $onBeforeSnap = '';
    
    /**
     * JavaScript code to be executed before taking the picture. Eg:
     * "document.getElementById('upload_results').innerHTML = '<h1>Uploading...</h1>';"
     */
    public $onBeforeUpload = '';    
    

    /**
     * JavaScript code to be executed after receiving the response.
     * Assume 'msg' comes from the server. Eg:
     * 	if (msg == 'OK') {
     *      document.getElementById('upload_results').innerHTML = '<h1>OK!</h1>';
     *      webcam.reset();
     *  }
	 *	else alert("PHP Error: " + msg);
     */
    public $completionHandler = '';

    public function run()
    {
        $base_dir = dirname(__FILE__);
        $assets_dir = $base_dir.DIRECTORY_SEPARATOR.'assets';
        $assets_url = Yii::app()->getAssetManager()->publish($assets_dir).DIRECTORY_SEPARATOR;

        $cs = Yii::app()->getClientScript();
        $cs->registerScriptFile($assets_url.'webcam.js', CClientScript::POS_BEGIN);

        // Correcting url to adapt to assets directory
        $js2 = "webcam.swf_url = '$assets_url" . "webcam.swf';\n";
        $js2 .= "webcam.shutter_url = '$assets_url" . "shutter.mp3';\n";
        $cs->registerScript ('fdfd', $js2, CClientScript::POS_BEGIN);

        $html = <<<BLOCK
<script language="JavaScript">
    webcam.set_api_url( '%APIURL%' );
    webcam.set_quality( %JPEGQUALITY% ); // JPEG quality (1 - 100)
    webcam.set_shutter_sound( %SHUTTERSOUND% ); // play shutter click sound
    webcam.set_stealth( %STEALTH% );

    document.write( webcam.get_html(%GETHTMLARGS%) );

    webcam.set_hook( 'onComplete', 'my_completion_handler' );

    function take_snapshot() {
        %ONBEFORESNAP%
        webcam.snap();
    }

    function do_upload() {
        %ONBEFOREUPLOAD%
        webcam.upload();
    }

    function my_completion_handler(msg) {
        %COMPLETIONHANDLER%
    }
</script>

BLOCK;

        // str_replace ( mixed $needle , mixed $replace , mixed $haystack [, int &$count ] );

        // %APIURL% %JPEGQUALITY% %SHUTTERSOUND% %STEALTH% %GETHTMLARGS% %ONBEFORESNAP% %COMPLETIONHANDLER%

        $html = str_replace ( "%APIURL%", $this->apiUrl, $html);
        $html = str_replace ( "%JPEGQUALITY%", $this->jpegQuality, $html);
        $html = str_replace ( "%STEALTH%", $this->stealth, $html);
        
        if ( is_bool ( $this->shutterSound ) )
            $html = str_replace ( "%SHUTTERSOUND%", $this->shutterSound ? "true" : "false", $html);
        else
            $html = str_replace ( "%SHUTTERSOUND%", "true, $assets_url{$this->shutterSound}", $html);

        $getHtmlArgs = $this->camWidth . ", " . $this->camHeight;
        if ($this->serverWidth && $this->serverHeight)
            $getHtmlArgs .= ", " . $this->serverWidth . ", " . $this->serverHeight;
        $html = str_replace ( "%GETHTMLARGS%", $getHtmlArgs, $html);

        $html = str_replace ( "%ONBEFORESNAP%", $this->onBeforeSnap, $html);
        $html = str_replace ( "%ONBEFOREUPLOAD%", $this->onBeforeUpload, $html);
        $html = str_replace ( "%COMPLETIONHANDLER%", $this->completionHandler, $html);

        $form = "\n<form>\n";
        foreach ($this->buttons as $key => $val)
        {
            switch ( strtolower($key) ) { // 'configure, takesnapshot, freeze, upload, reset'
                case "configure":
                    $form .= "<input type=button value=\"$val\" onClick=\"webcam.configure()\">\n";
                    break;
                case "takesnapshot":
                    $form .= "<input type=button value=\"$val\" onClick=\"take_snapshot()\">\n";
                    break;
                case "freeze":
                    $form .= "<input type=button value=\"$val\" onClick=\"webcam.freeze()\">\n";
                    break;
                case "upload":
                    $form .= "<input type=button value=\"$val\" onClick=\"do_upload()\">\n";
                    break;
                case "reset":
                    $form .= "<input type=button value=\"$val\" onClick=\"webcam.reset()\">\n";
                    break;
                default:
                    throw new Exception ("Wrong argument: $key");
            }
        }
        $form .= "</form>\n";

        echo $html . $form;
    }

    public static function actions()
    {
        return array(
           // naming the action and pointing to the location
           // where the external action class is
           'saveJpg'=>'application.extensions.jpegcam.actions.saveJpg',
        );
    }

}
?>

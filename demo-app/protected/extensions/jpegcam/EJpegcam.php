<?php
class EJpegcam extends CWidget
{ public $filepath; //cancallare!
    /**
     * The url to call to save the jpg
     * = api_url parameter (See Jpegcam documentation)
     */
    public $apiUrl = '';
    
    /**
     * shutter_sound parameter
     * See Jpegcam documentation
     */
    public $shutterSound = false;
    
    /**
     * Size of webcam
     */
    public $camWidth = 320;
    public $camHeight = 240;
    
    /**
     * Show the "Configure" button:
     * - false -> doesn't show the button
     * - any string -> The text of the button
     */
    public $configureButton = "Configure...";
     
    /**
     * Text for the "Take Snapshot" button:
     */ 
    public $takeSnapshotButton = "Take Snapshot";
    
    /**
     * JavaScript code to be executed before taking the picture. Eg:
     * "document.getElementById('upload_results').innerHTML = '<h1>Uploading...</h1>';"
     */
    public $onBeforeSnap = '';
    
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
        
        // Corrección del url según la carpeta donde el asset está publicado
        $js2 = "webcam.swf_url = '$assets_url" . "webcam.swf'";
        $cs->registerScript ('fdfd', $js2, CClientScript::POS_BEGIN);
        
        $txt = "<script language=\"JavaScript\">";
        
        if ($this->apiUrl != '')
            $txt .= "webcam.set_api_url( '{$this->apiUrl}' );\n";
            
        $txt .= "webcam.set_quality( 90 );\n";
        $txt .= "webcam.set_shutter_sound( {$this->shutterSound} );\n";
        
        $txt .= "document.write( webcam.get_html({$this->camWidth}, {$this->camHeight}) );\n";
        
        $txt .= <<<BLOCK
                 webcam.set_hook( 'onComplete', 'my_completion_handler' );
		
                 function take_snapshot() {
BLOCK;

        $txt .= $this->onBeforeSnap;
        
        $txt .= <<<BLOCK
                    webcam.snap();
                }
		
		function my_completion_handler(msg) {
BLOCK;
        $txt .= $this->completionHandler;
        
        $txt .= "}";
        
        $txt .= "</script>\n"; // Finished setting webcam parameters
        
        //Starting buttons
        $txt .= "<form>\n";
        if ($this->configureButton)
            $txt .= "<input type=button value=\"{$this->configureButton}\" onClick=\"webcam.configure()\">\n";
		$txt .= "<input type=button value=\"{$this->takeSnapshotButton}\" onClick=\"take_snapshot()\">\n";
		$txt .= "</form>";
	
        
        
        $js = <<<BLOCK
	<script language="JavaScript">
		webcam.set_api_url( 'guardaJpg' );
		webcam.set_quality( 90 ); // JPEG quality (1 - 100)
		webcam.set_shutter_sound( false ); // play shutter click sound
	</script>
	
	<!-- Next, write the movie to the page at 320x240 -->
	<script language="JavaScript">
		document.write( webcam.get_html(320, 240) );
	</script>
	
	<!-- Some buttons for controlling things -->
	<br/><form>
		<input type=button value="Configure..." onClick="webcam.configure()">
		&nbsp;&nbsp;
		<input type=button value="Take Snapshot" onClick="take_snapshot()">
	</form>
	
	<!-- Code to handle the server response (see test.php) -->
	<script language="JavaScript">
		webcam.set_hook( 'onComplete', 'my_completion_handler' );
		
		function take_snapshot() {
			// take snapshot and upload to server
			document.getElementById('upload_results').innerHTML = '<h1>Uploading...</h1>';
			webcam.snap();
		}
		
		function my_completion_handler(msg) {
			// extract URL out of PHP output
			if (msg == 'OK') {
				document.getElementById('upload_results').innerHTML = '<h1>OK!</h1>';
				
				// reset camera for another shot
				webcam.reset();
			}
			else alert("PHP Error: " + msg);
		}
	</script>
	

    <div id="upload_results" style="background-color:#eee;"></div>
BLOCK;
        

    
    echo $txt;
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

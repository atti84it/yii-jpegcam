<?php
$this->breadcrumbs=array(
    'User'=>array('/user'),
    'New Photo',
);?>
<h1>Take a new photo</h1>

<?php $onBeforeSnap = "document.getElementById('upload_results').innerHTML = '<h1>Uploading...</h1>';";
      $completionHandler = <<<BLOCK
        if (msg == 'OK') {
            document.getElementById('upload_results').innerHTML = '<h1>OK! ...redirecting in 3 seconds</h1>';

            // reset camera for another shot
            webcam.reset();
            setTimeout(function(){window.location = "index.php?r=user/index";},3000);
        }
        else alert("PHP Error: " + msg);
BLOCK;
      $this->widget('application.extensions.jpegcam.EJpegcam', array(
            'apiUrl' => 'index.php?r=user/jpegcam.saveJpg',
            'shutterSound' => false,
            'stealth' => true,
            'buttons' => array(
                'configure' => 'Configure',
                'takesnapshot' => 'Take Snapshot!',
                //'freeze' => 'Capture',
                //'upload' => 'Upload',
                //'reset' => 'Reset'
            ),
            'onBeforeSnap' => $onBeforeSnap,
            'completionHandler' => $completionHandler
        )); ?>

<div id="upload_results" style="background-color:#eee; margin-top:10px"></div>

<p>After being redirected you may need to refresh the page pressing F5</p>

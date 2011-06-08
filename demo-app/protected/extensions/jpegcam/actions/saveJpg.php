<?php
class saveJpg extends CAction{
    /**
     * Stores the full path of the file to save.
     * It is set by the client controller.
     */
    public $filepath;
    
    public function run(){
        $filepath = $this->filepath;
        if ($filepath == null)
            throw new Exception ("Null filepath!");

        $contents = file_get_contents('php://input');
        $result = file_put_contents( $filepath, $contents);
        if (!$result) {
            print "ERROR: Failed to write data to $filename, check permissions\n";
            exit();
        }
        print "OK";
    }
}
?>

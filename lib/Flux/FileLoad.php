<?php


class FileLoad{

    protected $path;
    protected $errorCodeMessages = array(
        1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
        2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
        3 => 'The uploaded file was only partially uploaded',
        4 => 'No file was uploaded',
        6 => 'Missing a temporary folder',
        7 => 'Failed to write file to disk',
        8 => 'A PHP extension stopped the file upload'
    );

    public function load($file, $path){
        $this->path = $path;
        if($file->get('error')){
            return $this->errorCodeMessages[$file->get('error')];
        }
        if(is_uploaded_file($file->get('tmp_name'))) {
            if(move_uploaded_file($file->get('tmp_name'), $path)) {
                return true;
            }
        }
        return 'During the boot file error occurred';
    }

    public function delete(){
        unlink($this->path);
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 14/02/2019
 * Time: 9:15
 */

class Photo extends Dbobject
{
    protected static $db_table = "photos";
    protected static $db_table_fields = array('title','description','caption', 'filename', 'alternate_text','type',
'size');

    /**(LEGE) VARIABELEN binnen de code zullen opvullen**/
    public $id;
    public $title;
    public $description;
    public $caption;
    public $filename;
    public $alternate_text;
    public $type;
    public $size;
    public $returnpath;

    public $tmp_path;
    public $upload_directory = 'img';
    public $errors = array();
    public $upload_errors_array = array(
        UPLOAD_ERR_OK => "There is no error",
        UPLOAD_ERR_INI_SIZE => "The uploaded file exceeds the upload max_filesize from php.ini",
        UPLOAD_ERR_FORM_SIZE => "The upload file exceeds MAX_FILE_SIZE in php.ini voor html form",
        UPLOAD_ERR_NO_FILE => "No file uploaded",
        UPLOAD_ERR_PARTIAL => "The file was partially uploaded",
        UPLOAD_ERR_NO_TMP_DIR => "Missing a temporary folder",
        UPLOAD_ERR_CANT_WRITE => "Failed to write do disk",
        UPLOAD_ERR_EXTENSION => "A php extension stopped your upload"
    );

    /**METHODES**/
    /**Hier dragen we de super global variabelen $_FILES['opteladenfile'] over als een argument (parameter)**/

    public function set_file($file){
        if(empty($file) || !$file || !is_array($file)) {
            $this->errors[] = "No file uploaded";
            return false;
        }
        elseif($file['error'] != 0){
            $this->errors[] = $this->upload_errors_array[$file['error']];
            return false;/**/
        }else{
            $this->filename = basename($file['name']);
            $this->tmp_path = $file['tmp_name'];
            $this->type = $file['type'];
            $this->size=$file['size'];
        }
    }
    public function save(){
        if($this->id){
            $this->update();
        }else{
            if(!empty($this->errors)){
                return false;
            }
            if(empty($this->filename) || empty($this->tmp_path)){
                $this->errors[]= "File not available";
                return false;
            }
            $target_path = SITE_ROOT . DS . "admin" . DS . $this->upload_directory . DS . $this->filename;
            if (file_exists($target_path)){
                $this->errors[] = "File {$this->filename} exists!";
                return false;
            }
            if(move_uploaded_file($this->tmp_path, $target_path)){
                if($this->create()){
                    unset($this->tmp_path);
                    return true;
                }
            }
            else{
                $this->errors[]= "This folder has no write rights!";
                return false;
            }
        }
    }

    public function picture_path(){

        if($this->filename !== ''){
           return $this->returnpath = $this->upload_directory.DS.$this->filename;
        }else{
            return $this->returnpath = 'https://place-hold.it/400x400';
        }
      }
    public function delete_photo(){
        if($this->delete()){
            $target_path = SITE_ROOT.DS.'admin'.DS.$this->picture_path();
            return unlink($target_path) ? true : false;
        }else{
            return false;
        }
    }

}
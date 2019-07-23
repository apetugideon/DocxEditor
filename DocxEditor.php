<?php
/**
 * DocxEditor
 * 
 * @author    Apetu Gideon Oluwatoyin
 * @copyright 2019
 * @version   NA
 * @access    public
 * @formats   docx
**/
class DocxEditor {
	//
	private $target_file = "";
	private $zip 		 = null;
	public  $source_file = "";
	public  $xml 		 = null;
	public  $error       = "";
	
	
    function __construct() {}
	
	
	private function getAcopy() : void {
		$this->target_file = "t_" . $this->source_file;
		if (!copy($this->source_file, $this->target_file)) {
			$this->error = "Could not proceed !";
		}
	}
	
	
	private function init_action() : void {
		$this->zip = new ZipArchive();
		if ( $this->zip->open($this->target_file, ZipArchive::CREATE) === TRUE ) {
			$this->xml = $this->zip->getFromName('word/document.xml'); 
		} else {
			$this->error = "Could not Open file";
		}
	}
	
	
	private function replace_value(string $find, string $replace) : void {
		$this->xml = str_replace($find, $replace, $this->xml);
	}
  
  
	private function persist_data() : bool {
		if (!$this->zip->addFromString('word/document.xml', $this->xml)) return false;
		$this->zip->close();
		return true;
	}
  
  
    public function write_doc(array $to_replace=array()) : bool { 
		if (file_exists($this->source_file)) {
			$this->getAcopy();
		} else {
			$this->error = "No file to work with";
		}
		
		if (file_exists($this->target_file)) $this->init_action();
		if ($this->xml) {
			foreach($to_replace as $key=>$value) {
				$this->replace_value($key, $value);
			}
			return $this->persist_data();
		} else {
			return false;
		}
    } 
}

$docObj = new DocxEditor();
$docObj->source_file = "test.docx";

$to_replace = array(
	'find_one' => 'replace_with_two',
	'find_a' => 'replace_with_b'
);
$docObj->write_doc($to_replace);
?>

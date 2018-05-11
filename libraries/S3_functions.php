<?php

/**
 * Amazon S3 functions php class
 *
 * @version	: 1.0
 * @date	: 10052018
 */
class S3_functions {

	function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->load->library('s3');

		$this->CI->config->load('s3', TRUE);
		$s3_config = $this->CI->config->item('s3');
		$this->bucket_name = $s3_config['bucket_name'];
		$this->folder_name = $s3_config['folder_name'];
		$this->s3_url = $s3_config['s3_url'];
	}

	/**
	* function to upload files in specific path.
	* Input		: $file_path - full path of file, $folder - specific folder to upload the file to.
	* Returns	: object's full path in S3.
	*/
	function upload_file($file_path, $folder)
	{
		$file = pathinfo($file_path);
		$s3_file = $file['filename'].'-'.rand(1000,1).'.'.$file['extension'];
		$mime_type = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $file_path);

		$saved = $this->CI->s3->putObjectFile(
			$file_path,
			$this->bucket_name,
			$this->folder_name.$folder.$s3_file,
			S3::ACL_PUBLIC_READ,
			array(),
			$mime_type
		);
		if ($saved) {
			return $this->s3_url.$this->bucket_name.'/'.$this->folder_name.$folder_name.$s3_file;
		}
	}
	
	/**
	* function to retrieve files from the specific folder. This is useful to retrieve images.
	* Input		: $folder e.g.: 'files/images'
	* Returns	: array of objects with full path.
	*/
	function get_files($folder)
	{
		$result = [];
        $iterator = $this->CI->s3->getBucket(
			$this->bucket_name,
			$this->folder_name.$folder.'/',
			null,
			0,
			null,
			false
		);

        foreach ($iterator as $object) {
            $result[] = $this->s3_url.$this->bucket_name.'/'.$object["name"];
        }
        
        return $result;
	}
	
	/**
	* function to retrieve files from the specific folder. This is useful to retrieve images.
	* Input		: $file_uri e.g.: 'files/images/file.jpg'
	* Returns	: array of objects with full path.
	*/
	function delete_file($file_uri)
	{
        $result = $this->CI->s3->deleteObject(
			$this->bucket_name,
			$file_uri
		);
        
        return $result;
	}
}

<?php

/**
 * File Handler Class
 *
 * @author Paul T Sunny
 */
class FileHandle
{
    private $FILE;
    private $type;
    private $loc = __DIR__ . '/../fileserver/'; //TODO: replace with full path
    private $name;
    private $sizeCon = array(
        'KB' => 1024,
        'MB' => 1048576,
        'GB' => 1073741824,
        'TB' => 1099511627776,
    );
    /**
     * @var bool $ERRORS Have errors or not
     * @var mixed $ErMap $ErMap Contains the list of errors
     */
    public $ERRORS = false;
    public $ErMap = array();

    /**
     * Initialise the file handler class
     *
     * @param mixed $FILE the file
     * @param mixed $loc file location 'files/'
     * @param string $rename leave empty if file name is same
     * @param bool $noReplace true (don't replace file), false is default (replace the file)
     * @return bool success/failed
     *
     * @access public
     */
    function __construct($FILE, $loc, $rename = "", $noReplace = false)
    {
        if ($FILE['error'] != 0 || is_array($FILE['error'])) {
            $this->ERRORS = true;
            $this->ErMap[] = "Fueling failed. Rocket can't take off"; //File upload failed
            return false;
        }

        $this->FILE = $FILE;
        $this->type = $FILE['type'];
        $this->loc = $this->loc . $loc;
        if ($rename == "")
            $this->name = $FILE['name'];
        else
            $this->name = $rename . '.' . pathinfo($FILE['name'], PATHINFO_EXTENSION);

        if ($noReplace && $this->FileExistsCheck()) {
            $this->ERRORS = true;
            $this->ErMap[] = "You had found a duplicate galaxy. No parallel ones.";//Similar file exists in storage.
        }
        return true;
    }

    /**
     * Check if file exists
     *
     * @return bool true: File exists and viseversa
     */
    public function FileExistsCheck()
    {
        if (file_exists($this->loc . $this->name))
            return true;
        return false;
    }

    /**
     * Gets the file type
     *
     * @return string file type
     */
    public function get_filetype()
    {
        return $this->type;
    }
    /**
     * Sets the file size limit and adds to the error fields
     * @param string $minSize Minimun file size in bytes
     */
    public function set_file_size_limit($minSize)
    {
        if ($minSize != -1 && $this->get_size() > $minSize) {
            $this->ERRORS = true;
            $this->ErMap[] = "Don't have space for anymore fuel.";//File Size Limit Exceeded.
        }
    }

    /**
     * Get file size
     * @return string bytes
     */
    public function get_size()
    {
        return $this->FILE['size'];
    }

    /**
     * Displays the file size
     * @return string file size in 'Byte','KB','MB','GB','TB','PB'
     */
    public function display_filesize()
    {
        $filesize = $this->get_size();
        $decr = 1024;
        $step = 0;
        $prefix = array('Byte', 'KB', 'MB', 'GB', 'TB', 'PB');
        while (($filesize / $decr) > 0.9) {
            $filesize = $filesize / $decr;
            $step++;
        }
        return round($filesize, 2) . ' ' . $prefix[$step];
    }

    /**
     * Get the size conversion 5MB = 5 * getSizeMulti('MB')
     * @param string $type Values are ('Byte','KB','MB','GB','TB','PB')
     * @return string bytes in 'Byte','KB','MB','GB','TB','PB'
     */
    public function getSizeMulti($type)
    {
        try {
            return $this->sizeCon[$type];
        } catch (\Throwable $th) {
            return 1;
        }
    }

    /**
     * Sets allowed extensions
     * @param mixed $ext extensions can be image/png,...
     */
    public function setAllowedExt($ext)
    {
        if (!in_array($this->get_filetype(), $ext)) {
            $this->ERRORS = true;
            $this->ErMap[] = "Pluto is not a planet! So is this...";//File type is not allowed
        }
    }

    /**
     * Save to database
     * Also inserts the file upload information into the database
     * @param mixed $tbname default fileuploads db
     * @param mixed $user (client/user) table name
     * @param mixed $tid id of insert into $user table
     * @return bool true/false
     */
    public function saveToDB($data1, $fid, $path, $tbname = 'fileuploads')
    {
        if (method_exists(DB::class, "query")) {
            if (DB::insert($tbname, [
                'fid' => $fid,
                'at' => DB::sqlEval("NOW()"),
                'path' => $path,
                'data1' => $data1,
            ])) return true;
        }
        return false;
    }

    /**
     * Finally saves the file to the location
     *
     * @return bool true/false (false: Call $ErMap to get the errors)
     */
    public function saveFile()
    {
        if ($this->ERRORS)
            return false;
        try {
            $fileTmpPath = $this->FILE['tmp_name'];
            $uploadFileDir = $this->loc;
            if(!$this->folder_exist($uploadFileDir)){
                mkdir($uploadFileDir);
            }
            $dest_path = $uploadFileDir . $this->name;
            if (move_uploaded_file($fileTmpPath, $dest_path)){
                return true;
            }
        } catch (\Throwable $th) {
            $this->ERRORS = true;
            $this->ErMap[] = "Unable to compute. Rocket AI failure.";//Unable to save the file
            return false;
        }
    }
    /**
     * File saved location including file name
     *
     * @return string file saved location including file name
     */
    public function saveLoc()
    {
        $loc = str_replace(__DIR__, '', $this->loc);
        return $loc . $this->name;
    }
    /**
     * @OBSOLETE Moved to FileRemoveHandler Class
     * Delete a file in /files/ directory
     * @param loc string url of file
     * @param home boolean use home directory files/
     * public function deleteAfile(...){...}
     */

    /**
     * Set a new name for the file
     * @param mixed name New name of the file
     */
    public function setNewName($name){
        $this->name = $name;
    }
    /**
     * Checks if a folder exist and return canonicalized absolute pathname (sort version)
     * @param string $folder the path being checked.
     * @return mixed returns the canonicalized absolute pathname on success otherwise FALSE is returned
     */
    function folder_exist($folder)
    {
        // Get canonicalized absolute pathname
        $path = realpath($folder);

        // If it exist, check if it's a directory
        return ($path !== false AND is_dir($path)) ? $path : false;
    }
}

<?php
ini_set('max_execution_time', 0);
ini_set('display_errors', 'On');
error_reporting(E_ALL & ~E_NOTICE);
set_time_limit(0);
include_once("ebcdic.php");

class jdatelnet {
	/**
	 * telnet connection
	 *
	 * @var resource
	 */
	var $fp;
	/**
	 * IP Address of the jda telnet server
	 *
	 * @var unknown_type
	 */
	var $ip = '172.16.1.1';
	/**
	 * Message generated by the scripts. Can be used to return messages resulting from keystroke entries
	 *
	 * @var string
	 */
	var $message = '';
	/**
	 * Contains the data returned by the last write command with getresponse = true
	 *
	 * @var unknown_type
	 */
	var $stream = '';
	var $completeStream = '';
	var $completeKeys = '';
	# for testing purposes
	var $streamFile = "stream.log";
	var $streamCtr = 0;
	/**
	 * Set to True to output all streams returned by write() where getresponse = true
	 *
	 * @var bool
	 */

	var $screen = "";
   	var $pos = 0;
	var $screencol = 132;
	var $screenrow = 27;
	
	//preserve the sequence of keys from input
	var $keyststack = "";
	var $tablestack = "";
   	
   	var $timeout = 300; // set to 5 minutes
	var $debugLvl = 0;  // Debug Level 0 for production, debugLvl 1 for output
	var $showAfter = false;
   	
	/**
	 * Contsructor. Establishes telnet session
	 *
	 * @param string $ip
	 * @param int $id terminal id values from 1 to 5 default to 5
	 * @param int $time_out
	 * @return jdatelnet
	 */
	function jdatelnet($ip=null,$id=5,$time_out=30){
		# 5250 codes
		define("CLEAR_UNIT",0x40);
		define("WRITE_TO_DISPLAY",0x11);
		define("WRITE_STRUCTURED_FIELD",0xF3);
		define("WRITE_TO_DISPLAY_STRUCTURED_FIELD",0x15);
		define("READ_MDT_FIELDS",0x52);
		define("ESC_CODE",0x04);
		define("SAVE_SCREEN_OP",0x04);
		define("READ_SCREEN_OP",0x08);
		
		# Order Codes
		define("SOH",0x01);
		define("SBA",0x11);
		define("IC",0x13);
		define("RA",0x02);
		define("SF",0x1D);
		define("RU",0x04);
		define("SAVE_SCREEN",0x02);
		define("READ_SCREEN",0x62);
		
		
		define("SCR_ROWS",27);
		define("SCR_COLS",132);
		
		if(!is_null($ip)) $this->ip = $ip;
		$linkid = uniqid();
		$this->fp = stream_socket_client("tcp://".$this->ip.":23/$linkid",$errno,$errstr);	
		#$this->fp = fsockopen($this->ip,23, $errno,$errstr);			
		if(!$this->fp){
			// did not connect so do not do anything
			echo " did not connect $errno,$errstr";
			return $this->fp;
		}

		stream_set_timeout($this->fp, 10);
		## Initialize the Screen
		$this->screen = "";
		for($row = 0; $row<SCR_ROWS; $row++)
			for($col = 0; $col<SCR_COLS; $col++)
				$this->screen .=" ";
		
		# stream_set_timeout($this->fp,$time_out);
		# for testing purposes only
		$stamp = date("z_Hi");
		$this->streamFile = "stream_$stamp.log";
		$this->streamCtr = 0;
		$this->negotiate($id);
		
		sleep(3);

		# get the first screen from server.
		$this->getResponse();
		// clear the keystrokes logged during initialization
		$this->completeKeys = '';
		
		$this->keys = array(
			 'F1' => chr(0x31)
			,'F2' => chr(0x32)
			,'F3' => chr(0x33)
			,'F4' => chr(0x34)
			,'F5' => chr(0x35)
			,'F6' => chr(0x36)
			,'F7' => chr(0x37)
			,'F8' => chr(0x38)
			,'F9' => chr(0x39)
			,'F10' => chr(0x3A)
			,'F11' => chr(0x3B)
			,'F12' => chr(0x3C)
			,'F13' => chr(0x3D)
			,'F14' => chr(0x3E)
			,'F15' => chr(0x3F)
			,'F16' => chr(0x40)
			,'F17' => chr(0x41)
			,'F18' => chr(0x42)
			,'F19' => chr(0x43)
			,'F20' => chr(0x44)
			,'F21' => chr(0x45)
			,'F22' => chr(0x46)
			,'F23' => chr(0x47)
			,'F24' => chr(0x48)
			,'ROLLUP' => chr(0xF5)
			,'ROLLDOWN' => chr(0xF4)
			,'UP' => chr(27).'[A'
			,'DOWN' => chr(27).'[B'
			,'REFRESH' => chr(27).'5'
			,'DELETE' => chr(127)
			,'TAB' => chr(11)
			,'BACKTAB' => chr(27)."\t"
			,'ENTER' => chr(0xF1)
			,'FIELDEXIT' => chr(27).'x'
			);
			
		$this->keys = array_flip($this->keys);

		define('F1',chr(0x31));
		define('F2',chr(0x32));
		define('F3',chr(0x33));
		define('F4',chr(0x34));
		define('F5',chr(0x35));
		define('F6',chr(0x36));
		define('F7',chr(0x37));
		define('F8',chr(0x38));
		define('F9',chr(0x39));
		define('F10',chr(0x3A));
		define('F11',chr(0x3B));
		define('F12',chr(0x3C));
		define('F13',chr(0x3D));
		define('F14',chr(0x3E));
		define('F15',chr(0x3F));
		define('F16',chr(0x41));
		define('F17',chr(0x42));
		define('F18',chr(0x43));
		define('F19',chr(0x44));
		define('F20',chr(0x45));
		define('F21',chr(0x46));
		define('F22',chr(0x47));
		define('F23',chr(0x48));
		define('F24',chr(0x49));
		define('ROLLUP',chr(0xF5));
		define('ROLLDOWN',chr(0xF4));
		define('ROLLLEFT',chr(0xD9));
		define('ROLLRIGHT',chr(0xDA));
		define('REFRESH',chr(27).'5');
		define('DELETE',chr(127));
		define('TAB',"\t");
		define('ENTER',chr(0xF1));
		define('BACKTAB',chr(27)."\t");
		define('FIELDEXIT',chr(27)."x");
		
		
	}			

	
	function negotiate($id){
		define("DONEWENV","ff fd 27");
		define("DONEWTERM","ff fd 18");
		define("WILLNEWENV",chr(0xff).chr(0xfb).chr(0x27));
		define("WILLTERMTYPE",chr(0xff).chr(0xfb).chr(0x18));
		define("SUBNEWENV","ff fa 27");
		define("SUBTERMTYPE","ff fa 18 01 ff f0");
		define("DOEOR","ff fd 19");
		define("WILLEOR","ff fb 19");
		define("DOBIN","ff fd 00");
		define("WILLBIN","ff fb 00");
		
		 
		#ff fd 27 ff fd 18 waiting HS5
		#ff fa 27 01 03 49 42 4d 52 53 45 45 44 33 16 b2 a8 a1 6e ee 61 00 03 ff f0 waiting HS8
		#ff fa 18 01 ff f0

		$requisites = 0;
		while($requisites < 3){
			#echo "reading server\n";
			$server = fread($this->fp,8092);
			$server = my5250tohex($server); 
			#echo "$server\n";
			$response = "";
			if(preg_match("%".DONEWENV."%",$server,$server_parts)) $response.= WILLNEWENV;
			if(preg_match("%".DONEWTERM."%",$server,$server_parts)) {
				# delay term type after ENV SUB OPTION
			}
			if(preg_match("%".SUBNEWENV."%",$server,$server_parts)) {
				$id = $id % 6;
			//	$response .= hexto5250("fffa2700005445524d0149424d2d333437372d4643004445564e414d450152415641474".$id."fff0");	# tn5250		
				# Remove device name
				# when their is no device name jda will automatically create one	
				$response .= hexto5250("fffa2700005445524d0149424d2d333437372d4643004445564e414d45");	# tn5250
			
			
				$response.= WILLTERMTYPE;
			}
			if(preg_match("%".SUBTERMTYPE."%",$server,$server_parts)) {
				$response.= hexto5250("fffa180049424d2d333437372d4643fff0");
				$requisites++; # this is one of the 3 requisites
			}
			if(preg_match("%".DOEOR."%",$server,$server_parts)) {
				# HS8: Will End of Record
				$response .= chr(0xff).chr(0xfb).chr(0x19);
				# HS9: Do End of Record
				$response .=chr(0xff).chr(0xfd).chr(0x19);
				$requisites++; # this is one of the 3 requisites
			}
			if(preg_match("%".WILLEOR."%",$server,$server_parts)) {
				# OK good, no need to respond;
			}
			if(preg_match("%".DOBIN."%",$server,$server_parts)) {
				# HS10: Will Binary Transmission
				$response.=chr(0xff).chr(0xfb).chr(0x00);
				# HS11: Do Binary Transmission
				$response.=chr(0xff).chr(0xfd).chr(0x00);
				$requisites++; # this is one of the 3 requisites
			}
			if(preg_match("%".WILLBIN."%",$server,$server_parts)) {
				# ok no need to respond
			}
			#echo "writing ". my5250tohex($response)." requisites $requisites\n";
			$this->write($response);
		}
		
		# peek one more time
		
		#$peek = stream_socket_recvfrom($this->fp, 1500, STREAM_PEEK);
		#$peek = my5250tohex($peek);
		#echo "peeked $peek \n";
		#if(preg_match("%".SUBNEWENV."%",$peek,$peek_parts)) {
		#$server = stream_get_line($this->fp,8192, chr(0xff). chr(0xf0));
		#	$server = my5250tohex($server); 
		#	echo "$server\n";
		#}
		
		
	}
	/**
	 * login to jda application and specify library
	 *
	 * @param string $username
	 * @param string $password
	 * @param string $env
	 * @return bool
	 */
	function login($username,$password,$env=''){
		if($this->debugLvl > 1) echo "\nLogging in\n"; 
		// login
		$credentials[] = array($username,6,53);
		$credentials[] = array($password,7,53);
		$this->write5250($credentials,ENTER,true,3);
		if($this->debugLvl > 1) echo "\nLogged in\n";
	}
	

	/**
	 * Write a 5250 data stream
	 */
	function write5250($words,$aid=ENTER,$getresponse=false,$delayresponse=0){
		$aid_row_byte = chr(FLOOR($this->pos/SCR_COLS));
		$aid_col_byte = chr($this->pos % SCR_COLS);
		
		$order_code_sba = chr(0x11);
		$data_string = "";
		if(is_array($words)){
			foreach($words as $word){
				if(is_array($word)){
					$string = myasciitoebcdic($word[0]);
					if($this->debugLvl > 1) {
						echo "converted string ";
						print_r(urlencode($string));
						echo "\n data string ";
					}
					$row_byte = chr($word[1]);
					$col_byte = chr($word[2]);
					$data_string .= $order_code_sba; // Write to Display
						$data_string .= $row_byte;
						$data_string .= $col_byte;
						$data_string .= $string;
					$aid_row_byte = $row_byte;
					$aid_col_byte = chr($word[2] + strlen($string));
				}
			}
		}
	
		
		$header="";
		$length_byte_1=chr(0);
		$length_byte_2=chr(0);
		$record_type_byte_1 = chr(0x12);
		$record_type_byte_2 = chr(0xA0);
		$flags_byte_1 = chr(0x00);
		$flags_byte_2 = chr(0x00);
		$var_rec_length = chr(0x04);
		$sna_flags = chr(0x00);
		$reserved_field = chr(0x00);
		$operation_code = chr(0x03);
		$attention_identifier = $aid;
		
		$escape_code = chr(0x04);
		$end_of_record_byte_1 = chr(0xff);
		$end_of_record_byte_2 = chr(0xef);
		
		
		$binary_string = $record_type_byte_1 . $record_type_byte_2 . $flags_byte_1 . $flags_byte_2 . $var_rec_length .$sna_flags . $reserved_field ;
		$binary_string .= $operation_code . $aid_row_byte . $aid_col_byte . $attention_identifier;
		$binary_string .= $data_string;
		$binary_string .= $end_of_record_byte_1;
		$binary_string .= $end_of_record_byte_2;
		
		$length = strlen($binary_string);
		
		
		$length_byte_1 = chr(floor($length/256));
		$length_byte_2 = chr($length%256);
		
		$binary_string = $length_byte_1 . $length_byte_2 . $binary_string;
		$this->write($binary_string,$getresponse,$delayresponse);
		
	}
	
	function reply_wsf_query(){
		$reply = "004712a0000004000000000088003ad9708006000101000000000000000000000000000000000001f3f4f7f700f0f002000000615000ffffffff000000233100000000000000000000ffef";
		$binary_string = hexto5250($reply);
		$this->write($binary_string,true);
		
		if($this->debugLvl > 0) echo "\nreplied to wsf\n";
		
	}
	
	
	function reply_save_screen(){
		$header="";
		$length_byte_1=chr(0);
		$length_byte_2=chr(0);
		$record_type_byte_1 = chr(0x12);
		$record_type_byte_2 = chr(0xA0);
		$flags_byte_1 = chr(0x00);
		$flags_byte_2 = chr(0x00);
		$var_rec_length = chr(0x04);
		$sna_flags = chr(0x00);
		$reserved_field = chr(0x00);
		
		$operation_code = chr(0x04); # SAVE SCREEN
		$restore_order_code = chr(0x12);
		
		$escape_code = chr(0x04);
		$clear_unit = chr(0x40);
		$end_of_record_byte_1 = chr(0xff);
		$end_of_record_byte_2 = chr(0xef);
		
		
		$binary_string = $record_type_byte_1 . $record_type_byte_2 . $flags_byte_1 . $flags_byte_2 . $var_rec_length .$sna_flags . $reserved_field ;
		$binary_string .= $operation_code . $escape_code. $restore_order_code;
		$binary_string .= $escape_code . $clear_unit;
		$binary_string .= $end_of_record_byte_1;
		$binary_string .= $end_of_record_byte_2;
		
		$length = strlen($binary_string);
		
		
		$length_byte_1 = chr(floor($length/256));
		$length_byte_2 = chr($length%256);
		
		$binary_string = $length_byte_1 . $length_byte_2 . $binary_string;
		$this->write($binary_string,true);
		if($this->debugLvl > 0) echo "\nreplied to save\n";
	}
	
	function reply_read_screen(){
		$header="";
		$length_byte_1=chr(0);
		$length_byte_2=chr(0);
		$record_type_byte_1 = chr(0x12);
		$record_type_byte_2 = chr(0xA0);
		$flags_byte_1 = chr(0x00);
		$flags_byte_2 = chr(0x00);
		$var_rec_length = chr(0x04);
		$sna_flags = chr(0x00);
		$reserved_field = chr(0x00);
		
		$operation_code = chr(0x00); # NO OPERATION
		$restore_order_code = chr(0x12);
		
		$row_add_byte = chr(0x20);
		$col_add_byte = chr(0x22);
		$aid_help = chr(0xF3);
		$end_of_record_byte_1 = chr(0xff);
		$end_of_record_byte_2 = chr(0xef);
		
		$binary_string = $record_type_byte_1 . $record_type_byte_2 . $flags_byte_1 . $flags_byte_2 . $var_rec_length .$sna_flags . $reserved_field ;
		$binary_string .= $operation_code . $row_add_byte. $col_add_byte; # . $aid_help;
		$binary_string .= myasciitoebcdic($this->screen);
		$binary_string .= $end_of_record_byte_1;
		$binary_string .= $end_of_record_byte_2;
		
		$length = strlen($binary_string);
		
		
		$length_byte_1 = chr(floor($length/256));
		$length_byte_2 = chr($length%256);
		
		$binary_string = $length_byte_1 . $length_byte_2 . $binary_string;
		$this->write($binary_string,true);
		
		if($this->debugLvl > 0) echo "\nreplied to read\n";
	}
	/**
	 * Write to the telnet session. pass $getresponse=true if you want the results of the write operation to be written to jdatelnet::stream. 
	 *
	 * @param unknown_type $str
	 * @param unknown_type $getresponse
	 */
	function write($str,$getresponse=false,$delayresponse=0){
		$this->completeKeys .= $str;

		if(!$this->fp){
			// while the time away cuz no connection
			echo "no connection";
		}
		else{
			fwrite($this->fp,$str);
			
			if($getresponse && $delayresponse>0) sleep($delayresponse);
			
			if($getresponse) {
				$this->getResponse();
				if($this->debugLvl >= 1) $this->debugOutput($str);
			}
		}
	}
	
	/**
	 * getResponse processes the responses from the TN5250 connection
	 * 
	 * 
	 *
	 * 
	 */
	function getResponse(){
		if($this->debugLvl > 1) echo "\ngetting response \n";
		$start = time();
		$passctr = 0;
		if($this->fp)
		do{
			$data = "";
			# trying to read per packet instead of input by matching the EOR to FF EF
			if($this->debugLvl > 0) echo "finding ff ef\n";
			$data .= stream_get_line($this->fp,8192, chr(0xff). chr(0xef));
			$data .= chr(0xff). chr(0xef);
			if($this->debugLvl > 0) echo "found ff ef\n";	
			# process the data
			$length = strlen($data);
			if($this->debugLvl > 0) echo "\ngot response $length bytes\n";
			if($length > 0){
				$this->completeStream .= $this->stream = $data;
				$this->streamCtr++;
				# for testing purposes only
				#file_put_contents($this->streamFile,$this->streamCtr."\n",FILE_APPEND);
				#$meta = stream_get_meta_data($this->fp);
				#$metadata = print_r($meta,true);
				#file_put_contents($this->streamFile,$metadata,FILE_APPEND);
				#file_put_contents($this->streamFile,my5250tohex($this->stream)."\n",FILE_APPEND);
				#file_put_contents($this->streamFile,"--------------------------------\n",FILE_APPEND);
				$this->runCommands($data);
			}
			else{
				if($this->debugLvl > 1) echo "\ngot no response \n";
			}
			// check if more to read
			$read   = array($this->fp);
			$write  = NULL;
			$except = NULL;
			if (false === ($num_changed_streams = stream_select($read, $write, $except, 1))) {
				/* Error handling */
			} elseif ($num_changed_streams > 0) {
				/* At least on one of the streams something interesting happened */
				if($this->debugLvl > 0) echo "\nstream needs to be read\n";
				#$this->getResponse(); # this is recursing, could be a problem
			}
			# update meta to get the number of unread bytes.
			$meta = stream_get_meta_data($this->fp);
			# count the number of passes
			$passctr++;
			
		}while($meta['unread_bytes'] > 0);
		
	}
	
	function saveScreen(){
		$header="";
		$length_byte_1=chr(0);
		$length_byte_2=chr(0);
		$record_type_byte_1 = chr(0x12);
		$record_type_byte_2 = chr(0xA0);
		$flags_byte_1 = chr(0x00);
		$flags_byte_2 = chr(0x00);
		$var_rec_length = chr(0x04);
		$sna_flags = chr(0x00);
		$reserved_field = chr(0x00);
		
		$operation_code = chr(0x04);
		$order_code = chr(0x02);
		
		$escape_code = chr(0x04);
		$end_of_record_byte_1 = chr(0xff);
		$end_of_record_byte_2 = chr(0xef);
		
		
		$binary_string = $record_type_byte_1 . $record_type_byte_2 . $flags_byte_1 . $flags_byte_2 . $var_rec_length .$sna_flags . $reserved_field ;
		$binary_string .= $operation_code . $escape_code. $order_code;
		$binary_string .= $end_of_record_byte_1;
		$binary_string .= $end_of_record_byte_2;
		
		$length = strlen($binary_string);
		
		
		$length_byte_1 = chr(floor($length/256));
		$length_byte_2 = chr($length%256);
		
		$binary_string = $length_byte_1 . $length_byte_2 . $binary_string;
		$this->write($binary_string,false);
		

	}
	function screen_write($string){
		$ascii = myebcdictoascii($string);
		$length = strlen($string);
		for($i = 0;$i<$length;$i++){
			$this->screen[$this->pos++] = $ascii[$i];
		}
		return;
	}

	function screen_write_to_pos($char,$pos){
		$ascii = myebcdictoascii($char);
		for($i = $this->pos;$i<$pos;$i++){
			$this->screen[$i] = $ascii;
		}
		$this->pos = $i;
	}
	function set_pos($row,$col){
		$this->pos = ($row * SCR_COLS) + $col;
		if($this->debugLvl > 0) {
			echo "\n Set pos to $row,$col or".$this->pos."\n";
		}
	}
	function process_order_code(&$stream,&$idx){
		$wrow = 0;
		$wcol = 0;
		$not_done = true;
		$length = strlen($stream);
		do{
			// get order code
			$order_code = ord($stream[$idx++]);
			if($this->debugLvl > 1) echo "\n  processing order 0x".dechex($order_code)."\n";
			switch($order_code){
				case SOH:
					if($this->debugLvl > 1) echo "\n  processing order 0x".dechex($order_code)."\n";
					// get length
					$hdr_length = ord($stream[$idx++]);
					$hdr_variable = substr($stream,$idx,$hdr_length);
					//skip header bytes;
					$idx+= $hdr_length;
					break;
				case SBA:
					if($this->debugLvl > 1) echo "\n  processing order 0x".dechex($order_code)."\n";
					$wrow = ord($stream[$idx++]);
					$wcol = ord($stream[$idx++]);
					$this->pos = (SCR_COLS * $wrow) + $wcol;
					// Save to Keystack
					$this->keystack.= substr($stream,$idx-3,3);
					break;
				case IC:
					$wrow = ord($stream[$idx++]);
					$wcol = ord($stream[$idx++]);
					$this->set_pos($wrow,$wcol);
					if($this->debugLvl > 1) echo "\n  processing order 0x".dechex($order_code)."\n";
					break;
				case SF:
					if($this->debugLvl > 1) echo "\n  processing order 0x".dechex($order_code)."\n";
					$start = $idx-1;
					$field_format = ord($stream[$idx++]);
					$field_control = ord($stream[$idx++]);
					$field_attrib = ord($stream[$idx++]);
					$field_length = ord($stream[$idx])*256 + ord($stream[$idx+1]) ; $idx+=2;
					#$data = substr($stream,$idx,$field_length); $idx += $field_length;
					$end = $idx;
					#$this->screen_write($data);
					// Save to Table Stack
					$this->tablestack .= substr($stream,$start,$end-$start); 
					break;
				case RA:
					if($this->debugLvl > 1) echo "\n  processing order 0x".dechex($order_code)."\n";
					$end_row = ord($stream[$idx++]);
					$end_col = ord($stream[$idx++]);
					$char_byte_1 = $stream[$idx++];
					// write the same char up to end_row, and end_col
					$this->screen_write_to_pos($char_byte_1,($end_row*SCR_COLS) + $end_col);
					// save command to keystack
					$this->keystack .= substr($stream,$idx-4,4);
					break;
				case ESC_CODE: // if we find an escape code then we are done.
					if($this->debugLvl > 1) echo "\n  found escape 0x".dechex($order_code)."\n";
					$not_done = false;
					$idx--;
					break;
				case READ_SCREEN: // read the screen
					break;
				case SAVE_SCREEN: // save the screen
					if($this->debugLvl > 1) echo "\n  processing order 0x".dechex($order_code)."\n";
					//$this->clear_screen();
					break;
				default: // assumes this is data
					$this->screen_write($stream[$idx -1]);
					// save to keystack
					$this->keystack .= $stream[$idx -1];
					break;
			}
			// if we see the end of record then we are done.
			if(ord($stream[$idx]) == 0xff &&ord($stream[$idx+1]) == 0xfe){
					$not_done = false; # if idx < len of stream, then runCommands will continue processing the stream
					$idx += 2;
			}
		} while($not_done && $idx < $length);
		return $idx;
	}
	
	function runCommands(&$stream){
		$len = strlen($stream);
		if($this->debugLvl > 0) echo "\nrunCommand processing stream of $len bytes\n";
		$idx = 0;
		$not_done = true;
		do{
			$byte = ord($stream[$idx++]);
			if($this->debugLvl > 0) echo dechex($byte)." ";
			if($byte == 4){ // found the escape code
				if($this->debugLvl > 0) echo "\n\tfound escape code 0x".dechex($byte)."\n";
				// parse command
				// get the command code
				$command_code = ord($stream[$idx++]); // set cursor to next byte
				if($this->debugLvl > 0) echo "\n\tprocessing command code 0x" .dechex($command_code)."\n";
				switch($command_code){
					case CLEAR_UNIT:
						// run a command to clear the screen
						$this->clear_screen();
						break;
					case WRITE_TO_DISPLAY:
						$wtd_control_character_byte1 = ord($stream[$idx++]); // usually 0x00
						$wtd_control_character_byte2 = ord($stream[$idx++]); // a byte of flags
						$idx = $this->process_order_code($stream,$idx); # assignement not necessary because $idx passed by reference
						if($this->showAfter) print_r($this->screen);
						break;
					case READ_MDT_FIELDS:
						$wtd_CC_byte_1 = ord($stream[$idx++]);
						$wtd_CC_byte_2 = ord($stream[$idx++]);
						break;
					case WRITE_STRUCTURED_FIELD:
						$wsf_length_byte_1 = ord($stream[$idx++]);
						$wsf_length_byte_2 = ord($stream[$idx++]);
						$wsf_class = ord($stream[$idx++]);
						$wsf_type = ord($stream[$idx++]);
						$wsf_flags = ord($stream[$idx++]);
						$this->reply_wsf_query();
						break;
					case WRITE_TO_DISPLAY_STRUCTURED_FIELD:
						$wdsf_length_byte_1 = ord($stream[$idx++]);
						$wdsf_length_byte_2 = ord($stream[$idx++]);
						$wdsf_class = ord($stream[$idx++]);
						$wdsf_type = ord($stream[$idx++]);
						$wdsf_flag = ord($stream[$idx++]);
						$wdsf_flag_reserved_byte_1 = ord($stream[$idx++]);
						$wdsf_flag_reserved_byte_2 = ord($stream[$idx++]);
						$wdsf_window_depth = ord($stream[$idx++]);
						$wdsf_window_width = ord($stream[$idx++]);
						$wdsf_length = ord($stream[$idx++]);
						$wdsf_minor_type= ord($stream[$idx++]);
						$wdsf_flag_2 = ord($stream[$idx++]);
						$wdsf_border_mono_attrib = ord($stream[$idx++]); 
						$wdsf_border_color_attrib= ord($stream[$idx++]); 
						$wdsf_border_char_ul = ord($stream[$idx++]); 
						$wdsf_border_char_top = ord($stream[$idx++]); 
						$wdsf_border_char_ur = ord($stream[$idx++]); 
						$wdsf_border_char_left = ord($stream[$idx++]); 
						$wdsf_border_char_right = ord($stream[$idx++]); 
						$wdsf_border_char_ll = ord($stream[$idx++]); 
						$wdsf_border_char_bottom = ord($stream[$idx++]); 
						$wdsf_border_char_lr= ord($stream[$idx++]); 
						$wdsf_length_2 = ord($stream[$idx++]); 
						$wdsf_minor_unknown = ord($stream[$idx++]); 
						break;
					case READ_SCREEN_OP:
					case READ_SCREEN:
						$this->reply_read_screen();
						break;
					case SAVE_SCREEN_OP:
						$this->reply_save_screen();
						break;
					default:
						break;
				}
			}
		} while($not_done && $idx < $len);
	}

	function clear_screen(){
		$this->screen="";
		for($row = 0; $row<SCR_ROWS; $row++)
			for($col = 0; $col<SCR_COLS; $col++)
				$this->screen .=" ";
		$this->keystack = "";
		$this->tablestack = "";
		$this->pos = 0;
	}
	/**
	 * checks to see if $str is found in the stream. Sets jdatelnet::message if string is found and a 2nd paramater is passed.
	 *
	 * @param string $str
	 * @param message $message
	 * @return bool
	 */
	function streamCheck($str,$message=''){
		if(strpos($this->stream,$str)){
			$this->message = $message;
			return true;
		} else  {
			return false;
		}
	}
	
	/**
	 * checks to see if $str is found in the screen. Sets jdatelnet::message if string is found and a 2nd paramater is passed.
	 *
	 * @param string $str
	 * @param message $message
	 * @return bool
	 */
	function screenCheck($str,$message=''){
		
		if(strpos($this->screen,$str) > 0){
			$this->message = $message;
			return true;
		} else  {
			return false;
		}
	}
	/**
	 * checks to see if $str is found in the screen. Sets jdatelnet::message if string is found and a 2nd paramater is passed.
	 *
	 * @param string $str
	 * @param message $message
	 * @return bool
	 */
	function screenWait($str, $tries=3){
		$sockets = array("jda" => $this->fp);
		$write = array();
		$read = $except = $sockets;
		if($this->debugLvl > 0) echo "Waiting for $str ";
		$ctr = 0;
		do{
			echo ".";
			if($this->screenCheck($str,"Waiting for $str")){
				if($this->debugLvl > 0) echo "+\n";
				return true;
			} else  {
					if($this->debugLvl > 0) {
						echo "not found\n";
						echo $this->screen;
						echo "\n";
					}
					# sleep for 1 second then quickly check for a response
					sleep(1);
					stream_set_blocking($this->fp,0);
					$this->getResponse();
					stream_set_blocking($this->fp,1);
			}
			$ctr++;
		}
		while($ctr < $tries);
		return false;
	}
	
	/**
	 * private method: outputs the command and resulting stream/screen. called by write when jdatelnet::debug is true
	 *
	 * @param unknown_type $command
	 */
	function debugOutput($command){
		if(substr($command,0,1)==chr(27)){
			echo "<br>\n".date("h:i:s ")."Key Sent:[".$this->keys[$command]."] ";
		} else {
			echo "<br>\n".date("h:i:s ")."Key Sent:".$command;
		}
		echo "\r\n";
		echo $this->getScreen().chr(13).chr(10).chr(13).chr(10);
	}
	
	/**
	 * Closes the telnet connection
	 *
	 */
	function close(){
		fclose($this->fp);	
	}
	
	/**
	 * Enter description here...
	 *
	 * @param int $start
	 * @param int $length
	 * @return string
	 */
	function parsestream($start,$length){
		return trim(substr($this->stream,$start,$length));		
	}
	

	function getCursor(){
		$col = $this->pos % $this->screencol;
		return array('row'=>floor($this->pos/$this->screencol),'col'=>$col);
	}
	
	function maxChars($str,$max){
		if(strlen($str)>=$max) return substr($str,0,$max);
		else return $str.FIELDEXIT;
	}
	
	function screenWrite($str){
//		echo "POS:".$this->pos."	str:".$str."|"."	strlen:".strlen($str)."\r\n";
		$this->screen = substr_replace($this->screen,$str,$this->pos,strlen($str));
		$this->pos += strlen($str);
	}
	
	function getScreen($singleLine=false){
		if($singleLine==true) return $this->screen;
		else {
			$i = 0;
			$str = "";
			while($i<2000){
				$str .= substr($this->screen,$i,$this->screencol)."\n";
				$i += $this->screencol;
			}
			return $str;
		}
	}
	
	function getScreenRow($x){
		$rows = explode("\n",$this->getScreen());
		
		return $rows[$x];
	}
	
	function getScreenSection($row,$start,$len){
		return trim(substr($this->getScreenRow($row),$start,$len));
	}
	
	function writeItems($startRow,$endRow,$start,$length,$selectedCodes,$backtoStart){
		if(!is_array($selectedCodes)) return;
		$totalcounter = 0;

		while( count($selectedCodes)>0 && $totalcounter<6 ){ // loop for as long as there are still unmatched codes
			$totalcounter ++;
			if($notfirst){
				$this->write(PGDN,true); // scroll to next set
				// do we need to move the cursor back up?
				
			} else $notfirst = true; // set variable for next loop
			
			$rows = explode("\n",$this->getScreen());
			
			for($i=$startRow;$i<=$endRow;$i++){
				if(count($selectedCodes)==0) break;
				$code = trim(substr($rows[$i],$start,$length));
//				echo "CODE FOUND IS ".$code;
//				echo "\r\n\r\n";
//				$key = array_search($code, $selectedCodes);

				if(isset($selectedCodes[$code])){ // match is found. hit x key and remove code from array selectedCodes
//				echo $selectedCodes[$code];
//				echo "\r\n\r\n";
					$this->write($selectedCodes[$code]); 
					unset($selectedCodes[$code]);
//					array_splice($selectedCodes,$key,1);
					if($i==$endRow){ // do key strokes to move back up
						$this->write($backtoStart);
					}
				} elseif($i==$endRow) { // if last row and item is not to be selected... do key strokes to move back up
					for($j=$endRow;$j>$startRow;$j--){ // hit the up key as many times as there are rows
						$this->write(UP);
					}
				} else { // if items is not to be selected then move down to next item.
					$this->write(DOWN);
				}
				//print_r($selectedCodes);
			}
		}
	}

}

?>

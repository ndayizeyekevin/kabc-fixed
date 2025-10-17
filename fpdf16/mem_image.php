<?php
require('fpdf.php');
//Stream handler to read from global variables
class VariableStream
{
	var $varname;
	var $position;
	function stream_open($path, $mode, $options, &$opened_path)
	{
		$url = parse_url($path);
		$this->varname = $url['host'];
		if(!isset($GLOBALS[$this->varname]))
		{
			trigger_error('Global variable '.$this->varname.' does not exist', E_USER_WARNING);
			return false;
		}
		$this->position = 0;
		return true;
	}

	function stream_read($count)
	{
		$ret = substr($GLOBALS[$this->varname], $this->position, $count);
		$this->position += strlen($ret);
		return $ret;
	}

	function stream_eof()
	{
		return $this->position >= strlen($GLOBALS[$this->varname]);
	}

	function stream_tell()
	{
		return $this->position;
	}

	function stream_seek($offset, $whence)
	{
		if($whence==SEEK_SET)
		{
			$this->position = $offset;
			return true;
		}
		return false;
	}
	
	function stream_stat()
	{
		return array();
	}
}
class PDF_MemImage extends FPDF
{
	function PDF_MemImage($orientation='P', $unit='mm', $format='A4')
	{
		$this->FPDF($orientation, $unit, $format);
		//Register var stream protocol
		stream_wrapper_register('var', 'VariableStream');
	}

	function MemImage($data, $x=null, $y=null, $w=0, $h=0, $link='')
	{
		//Display the image contained in $data
		$v = 'img'.md5($data);
		$GLOBALS[$v] = $data;
		$a = getimagesize('var://'.$v);
		if(!$a)
			$this->Error('Invalid image data');
		$type = substr(strstr($a['mime'],'/'),1);
		$this->Image('var://'.$v, $x, $y, $w, $h, $type, $link);
		unset($GLOBALS[$v]);
	}
	function GDImage($im, $x=null, $y=null, $w=0, $h=0, $link='')
	{
		//Display the GD image associated to $im
		ob_start();
		imagepng($im);
		$data = ob_get_clean();
		$this->MemImage($data, $x, $y, $w, $h, $link);
	}
function Code39($xpos, $ypos, $code, $baseline=0.5, $height=7){
	$wide = $baseline;
	$narrow = $baseline / 3 ; 
	$gap = $narrow;
	$barChar['0'] = 'nnnwwnwnn';
	$barChar['1'] = 'wnnwnnnnw';
	$barChar['2'] = 'nnwwnnnnw';
	$barChar['3'] = 'wnwwnnnnn';
	$barChar['4'] = 'nnnwwnnnw';
	$barChar['5'] = 'wnnwwnnnn';
	$barChar['6'] = 'nnwwwnnnn';
	$barChar['7'] = 'nnnwnnwnw';
	$barChar['8'] = 'wnnwnnwnn';
	$barChar['9'] = 'nnwwnnwnn';
	$barChar['A'] = 'wnnnnwnnw';
	$barChar['B'] = 'nnwnnwnnw';
	$barChar['C'] = 'wnwnnwnnn';
	$barChar['D'] = 'nnnnwwnnw';
	$barChar['E'] = 'wnnnwwnnn';
	$barChar['F'] = 'nnwnwwnnn';
	$barChar['G'] = 'nnnnnwwnw';
	$barChar['H'] = 'wnnnnwwnn';
	$barChar['I'] = 'nnwnnwwnn';
	$barChar['J'] = 'nnnnwwwnn';
	$barChar['K'] = 'wnnnnnnww';
	$barChar['L'] = 'nnwnnnnww';
	$barChar['M'] = 'wnwnnnnwn';
	$barChar['N'] = 'nnnnwnnww';
	$barChar['O'] = 'wnnnwnnwn'; 
	$barChar['P'] = 'nnwnwnnwn';
	$barChar['Q'] = 'nnnnnnwww';
	$barChar['R'] = 'wnnnnnwwn';
	$barChar['S'] = 'nnwnnnwwn';
	$barChar['T'] = 'nnnnwnwwn';
	$barChar['U'] = 'wwnnnnnnw';
	$barChar['V'] = 'nwwnnnnnw';
	$barChar['W'] = 'wwwnnnnnn';
	$barChar['X'] = 'nwnnwnnnw';
	$barChar['Y'] = 'wwnnwnnnn';
	$barChar['Z'] = 'nwwnwnnnn';
	$barChar['-'] = 'nwnnnnwnw';
	$barChar['.'] = 'wwnnnnwnn';
	$barChar[' '] = 'nwwnnnwnn';
	$barChar['*'] = 'nwnnwnwnn';
	$barChar['$'] = 'nwnwnwnnn';
	$barChar['/'] = 'nwnwnnnwn';
	$barChar['+'] = 'nwnnnwnwn';
	$barChar['%'] = 'nnnwnwnwn';
	$this->SetFont('Arial','',10);
	//$this->Text($xpos, $ypos + $height + 4, $code);
	$this->SetFillColor(0);
	$code = '*'.strtoupper($code).'*';
	for($i=0; $i<strlen($code); $i++){
		$char = $code[$i];
		if(!isset($barChar[$char])){
			$this->Error('Invalid character in barcode: '.$char);
		}
		$seq = $barChar[$char];
		for($bar=0; $bar<9; $bar++){
			if($seq[$bar] == 'n'){
				$lineWidth = $narrow;
			}else{
				$lineWidth = $wide;
			}
			if($bar % 2 == 0){
				$this->Rect($xpos, $ypos, $lineWidth, $height, 'F');
			}
			$xpos += $lineWidth;
		}
		$xpos += $gap;
	}
}
}
?>

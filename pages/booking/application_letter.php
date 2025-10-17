<?php
ob_start(); 
require('../../fpdf16/fpdf.php');
$year=date('Y');
// It will be called downloaded.pdf

//print watermark
class PDF_Rotate extends FPDF
{
var $angle=0;
function Rotate($angle,$x=-1,$y=-1)
{
	if($x==-1)
		$x=$this->x;
	if($y==-1)
		$y=$this->y;
	if($this->angle!=0)
		$this->_out('Q');
	$this->angle=$angle;
	if($angle!=0)
	{
		$angle*=M_PI/180;
		$c=cos($angle);
		$s=sin($angle);
		$cx=$x*$this->k;
		$cy=($this->h-$y)*$this->k;
		$this->_out(sprintf('q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm',$c,$s,-$s,$c,$cx,$cy,-$cx,-$cy));
	}
}
function _endpage()
{
	if($this->angle!=0)
	{
		$this->angle=0;
		$this->_out('Q');
	}
	parent::_endpage();
}
}
//inherits watermark to pdf
class PDF extends PDF_Rotate
{
//barcode generation
function Code39($xpos, $ypos, $code, $baseline=0.5, $height=5){
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
	$this->Text($xpos, $ypos + $height + 4, $code);
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
//Page header
function Header()
{
 $this->Image('ougami.jpg',10,10,40);
		$this->SetFont('Arial','B',15);
    
         /********************************************/
		 $this->SetX(70);
		 $this->SetFont('Arial','',13);
		 $this->SetTextColor(196,33,38);
		 $this->Cell(120,5,'OUGAMI',0,0,'R');
		 $this->Ln(6);
		 $this->SetX(70);
		 $this->Cell(120,5,'',0,0,'R');
		 $this->Ln(6);
		 $this->SetTextColor(0,0,0);
		 $this->SetFont('Arial','',8);
		 $this->SetX(70);
		 $this->Cell(120,5,'B.P: 238 Bujumbura-Burundi',0,0,'R');
		 $this->Ln(5);
		 /************************************************/
		 $this->SetTextColor(0,0,0);
		 $this->SetFont('Arial','',8);
		 $this->SetX(70);
		 $this->Cell(120,5,'Tel: (+257) 31040404 / (+257) 76256533',0,0,'R');
		  /************************************************/
		 $this->Ln(5);
		 $this->SetTextColor(100,0,0);
		 $this->SetFont('Arial','',8);
		 $this->SetX(70);
		 $this->cell(120,5,'email:info@itecrwanda.com',0,0,'R');
		 $this->Ln(5);
		 $this->SetX(70);
		 $this->Cell(120,5,'www.ougami.com',0,0,'R');
		 $this->Ln(10);
	 /************************************************/
	
	
}
function RotatedText($x, $y, $txt, $angle)
{
	//Text rotated around its origin
	$this->Rotate($angle,$x,$y);
	$this->Text($x,$y,$txt);
	$this->Rotate(0);
}
//Page footer
function Footer()
{$this->SetY(-50);
	//$this->Image('fpdf16/regsign.gif',15,230,80);
	$this->SetTextColor(0,128,0);
   
    $this->Ln(5);
	$this->SetY(-26);
	$this->SetTextColor(255,0,0);
	$this->SetFont('Arial','I',8);	 
	$this->Cell(0,5,'(Due to the COVID-19 pandemic, all measures barrier have been put in place to avoid all risks of contamination.)',0,1,'C');
//$this->Image('fpdf16/regsign.gif',15,235,50);
    $this->SetTextColor(100,0,0);
    $this->SetY(-25);
	$this->Cell(0,10,'_______________________________________________________________________________________________________',0,0,'C');

}
var $B;
var $I;
var $U;
var $HREF;
function PDF($orientation='P',$unit='mm',$format='A4')
{
    //Call parent constructor
    $this->FPDF($orientation,$unit,$format);
    //Initialization
    $this->B=0;
    $this->I=0;
    $this->U=0;
    $this->HREF='';
}
function WriteHTML($html)
{
    //HTML parser
    $html=str_replace("\n",' ',$html);
    $a=preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
    foreach($a as $i=>$e)
    {
        if($i%2==0)
        {
            //Text
            if($this->HREF)
                $this->PutLink($this->HREF,$e);
            else
                $this->Write(5,$e);
        }
        else
        {
            //Tag
            if($e[0]=='/')
                $this->CloseTag(strtoupper(substr($e,1)));
            else
            {
                //Extract attributes
                $a2=explode(' ',$e);
                $tag=strtoupper(array_shift($a2));
                $attr=array();
                foreach($a2 as $v)
                {
                    if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))
                        $attr[strtoupper($a3[1])]=$a3[2];
                }
                $this->OpenTag($tag,$attr);
            }
        }
    }
}
function OpenTag($tag,$attr)
{
    //Opening tag
    if($tag=='B' || $tag=='I' || $tag=='U')
        $this->SetStyle($tag,true);
    if($tag=='A')
        $this->HREF=$attr['HREF'];
    if($tag=='BR')
        $this->Ln(5);
}
function CloseTag($tag)
{
    //Closing tag
    if($tag=='B' || $tag=='I' || $tag=='U')
        $this->SetStyle($tag,false);
    if($tag=='A')
        $this->HREF='';
}
function SetStyle($tag,$enable)
{
    //Modify style and select corresponding font
    $this->$tag+=($enable ? 1 : -1);
    $style='';
    foreach(array('B','I','U') as $s)
    {
        if($this->$s>0)
            $style.=$s;
    }
    $this->SetFont('',$style);
}
function PutLink($URL,$txt)
{
    //Put a hyperlink
    $this->SetTextColor(0,0,255);
    $this->SetStyle('U',true);
    $this->Write(5,$txt,$URL);
    $this->SetStyle('U',false);
    $this->SetTextColor(0);
}
}
//Instanciation of inherited class
$pdf=new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','',12);

$pdf->SetAuthor('https://www.ougami.com/');
$pdf->SetTitle('Confirmation Letter');


require_once ("../../inc/config.php");

if(isset($_GET['emailcnfrm']))
       {
	$query = "	SELECT * FROM tbl_reservation INNER JOIN guest ON tbl_reservation.guest_id=guest.guest_id 
		WHERE confirmation = '".$_GET['emailcnfrm']."'	";
	$statement = $conn->prepare($query);
	$statement->execute();
    
	$no_of_row = $statement->rowCount();
	
	if($no_of_row > 0)
	{
		$row = $statement->fetch();
		
    			$firstname = $row['firstname'];
                $lastname = $row['lastname'];
                $phone = $row['phone'];
                $email = $row['email'];
                $arrival = $row['arrival'];
                $departure = $row['departure'];
                $passport = $row['nid_passport'];
                $today = date("Y-m-d");
			}
       }
               
$dat=date('d/M/Y');
   $pdf->SetFont('Arial','',9);
   $pdf->Ln(10);
    $pdf->Cell(90,5,'Date: '.$dat.'','','1');
//   $pdf->Ln(5);
//   $pdf->SetFont('Arial','',9);
   $pdf->Ln(10);
    $pdf->Cell(90,5,'RE: Confirmation of reservation'.''.'','','1');
    $pdf->Ln(5);
   $pdf->Cell(90,5,'Passport No: '.$passport.',','','','l');
   $pdf->Ln(10);
//   $pdf->Ln(5);
   $pdf->Cell(90,5,'Dear Mr/Miss '.strtoupper($firstname).' '.strtoupper($lastname).',','','','l');
   $pdf->Ln(10);
    //$pdf->Cell(5);
	$html="
Thank you for choosing Saint Blaise Hotel. We pleased to confirm your reservation on date from ".$arrival. " to ".$departure."<br>
Thank you again for your reservation.<br><br>
	</b><br>
We remain at your disposal for any questions in case of need. 

<br><br> The Saint Blaise Hotel team welcomes you!";	
   
    $pdf->WriteHTML($html);

    $pdf->Output($n1.'.pdf','I');
    $pdf->Output();


exit;
ob_end_flush();
?>
<?php
namespace App;

/**
* класс для  формирования  ESC/POS команд
*/
class Printer{

    /**
     * ASCII null control character
     */
    const NUL = 0x00;

    /**
     * ASCII linefeed control character
     */
    const LF = 0x0a;

    /**
     * ASCII escape control character
     */
    const ESC = 0x1b;

    /**
     * ASCII form separator control character
     */
    const FS = 0x1c;

    /**
     * ASCII form feed control character
     */
    const FF = 0x0c;

    /**
     * ASCII group separator control character
     */
    const GS = 0x1d;

    /**
     * ASCII data link escape control character
     */
    const DLE = 0x10;

    /**
     * ASCII end of transmission control character
     */
    const EOT = 0x04;

 

    /**
     * Indicates JAN13 barcode when used with Printer::barcode
     */
    const BARCODE_EAN13 = 67;

 

    /**
     * Indicates CODE39 barcode when used with Printer::barcode
     */
    const BARCODE_CODE39 = 69;

 

    /**
     * Indicates CODE128 barcode when used with Printer::barcode
     */
    const BARCODE_CODE128 = 73;

    /**
     * Indicates that HRI (human-readable interpretation) text should not be
     * printed, when used with Printer::setBarcodeTextPosition
     */
    const BARCODE_TEXT_NONE = 0;

    /**
     * Indicates that HRI (human-readable interpretation) text should be printed
     * above a barcode, when used with Printer::setBarcodeTextPosition
     */
    const BARCODE_TEXT_ABOVE = 1;

    /**
     * Indicates that HRI (human-readable interpretation) text should be printed
     * below a barcode, when used with Printer::setBarcodeTextPosition
     */
    const BARCODE_TEXT_BELOW = 2;

    /**
     * Use the first color (usually black), when used with Printer::setColor
     */
    const COLOR_1 = 0;

    /**
     * Use the second color (usually red or blue), when used with Printer::setColor
     */
    const COLOR_2 = 1;

    /**
     * Make a full cut, when used with Printer::cut
     */
    const CUT_FULL = 65;

    /**
     * Make a partial cut, when used with Printer::cut
     */
    const CUT_PARTIAL = 66;

    /**
     * Use Font A, when used with Printer::setFont
     */
    const FONT_A = 0;

    /**
     * Use Font B, when used with Printer::setFont
     */
    const FONT_B = 1;

    /**
     * Use Font C, when used with Printer::setFont
     */
    const FONT_C = 2;

    /**
     * Use default (high density) image size, when used with Printer::graphics,
     * Printer::bitImage or Printer::bitImageColumnFormat
     */
    const IMG_DEFAULT = 0;

    /**
     * Use lower horizontal density for image printing, when used with Printer::graphics,
     * Printer::bitImage or Printer::bitImageColumnFormat
     */
    const IMG_DOUBLE_WIDTH = 1;

    /**
     * Use lower vertical density for image printing, when used with Printer::graphics,
     * Printer::bitImage or Printer::bitImageColumnFormat
     */
    const IMG_DOUBLE_HEIGHT = 2;

    /**
     * Align text to the left, when used with Printer::setJustification
     */
    const JUSTIFY_LEFT = 0;

    /**
     * Center text, when used with Printer::setJustification
     */
    const JUSTIFY_CENTER = 1;

    /**
     * Align text to the right, when used with Printer::setJustification
     */
    const JUSTIFY_RIGHT = 2;

    /**
     * Use Font A, when used with Printer::selectPrintMode
     */
    const MODE_FONT_A = 0;

    /**
     * Use Font B, when used with Printer::selectPrintMode
     */
    const MODE_FONT_B = 1;

    /**
     * Use text emphasis, when used with Printer::selectPrintMode
     */
    const MODE_EMPHASIZED = 8;

    /**
     * Use double height text, when used with Printer::selectPrintMode
     */
    const MODE_DOUBLE_HEIGHT = 16;

    /**
     * Use double width text, when used with Printer::selectPrintMode
     */
    const MODE_DOUBLE_WIDTH = 32;

    /**
     * Underline text, when used with Printer::selectPrintMode
     */
    const MODE_UNDERLINE = 128;

    /**
     * Indicates standard PDF417 code
     */
    const PDF417_STANDARD = 0;

    /**
     * Indicates truncated PDF417 code
     */
    const PDF417_TRUNCATED = 1;

    /**
     * Indicates error correction level L when used with Printer::qrCode
     */
    const QR_ECLEVEL_L = 0;

    /**
     * Indicates error correction level M when used with Printer::qrCode
     */
    const QR_ECLEVEL_M = 1;

    /**
     * Indicates error correction level Q when used with Printer::qrCode
     */
    const QR_ECLEVEL_Q = 2;

    /**
     * Indicates error correction level H when used with Printer::qrCode
     */
    const QR_ECLEVEL_H = 3;

    /**
     * Indicates QR model 1 when used with Printer::qrCode
     */
    const QR_MODEL_1 = 1;

    /**
     * Indicates QR model 2 when used with Printer::qrCode
     */
    const QR_MODEL_2 = 2;

    /**
     * Indicates micro QR code when used with Printer::qrCode
     */
    const QR_MICRO = 3;

    /**
     * Indicates a request for printer status when used with
     * Printer::getPrinterStatus (experimental)
     */
    const STATUS_PRINTER = 1;

    /**
     * Indicates a request for printer offline cause when used with
     * Printer::getPrinterStatus (experimental)
     */
    const STATUS_OFFLINE_CAUSE = 2;

    /**
     * Indicates a request for error cause when used with Printer::getPrinterStatus
     * (experimental)
     */
    const STATUS_ERROR_CAUSE = 3;

    /**
     * Indicates a request for error cause when used with Printer::getPrinterStatus
     * (experimental)
     */
    const STATUS_PAPER_ROLL = 4;

    /**
     * Indicates a request for ink A status when used with Printer::getPrinterStatus
     * (experimental)
     */
    const STATUS_INK_A = 7;

    /**
     * Indicates a request for ink B status when used with Printer::getPrinterStatus
     * (experimental)
     */
    const STATUS_INK_B = 6;

    /**
     * Indicates a request for peeler status when used with Printer::getPrinterStatus
     * (experimental)
     */
    const STATUS_PEELER = 8;

    /**
     * Indicates no underline when used with Printer::setUnderline
     */
    const UNDERLINE_NONE = 0;

    /**
     * Indicates single underline when used with Printer::setUnderline
     */
    const UNDERLINE_SINGLE = 1;

    /**
     * Indicates double underline when used with Printer::setUnderline
     */
    const UNDERLINE_DOUBLE = 2;
  private $buffer=[];
  
    function __construct( ) {
       
        $this->buffer[]= self::ESC;        
        $this->buffer[]= ord('@'); 
        
        $this->addBytes([27,116,17]); //866
    }
  
    private function encode($text){
        $text = \Normalizer::normalize($text);        
        if ($text === false) {
            throw new \Exception("Input must be UTF-8");
        }   
        
        $text = iconv('UTF-8','cp866',$text)  ;        
        
        return $text;
    }
    public function getBuffer(){
        return $this->buffer;        
    }
    public function addBytes(array $bytes){
        foreach($bytes as $b){
           $this->buffer[]= $b;   
        }

    }
    
 
    public function align($align )
    {
        $this->buffer[]= 0x1b; 
        $this->buffer[]= 0x61; 
        $this->buffer[]= $align; 
 
    } 
    public function newline( )
    {
        $this->buffer[]= self::LF; 
 
    } 
    public function cut($partial = false )
    {
        if($partial)
            $this->buffer[]= self::CUT_PARTIAL; 
        else     
            $this->buffer[]= self::CUT_FULL; 
 
    } 

    /**
    * открыть ящик .  Пин  2 или  5 
    * 
    * @param mixed $pin
    */
    public function cash($pin)
    {
        self::ESC . "p" . chr($pin_value) ;
        
        $this->buffer[]=self::ESC; 
        $this->buffer[]= 0x70; 

        $this->buffer[]= $pin == 2 ? 0:1; 
 
    } 
    
    /**
    * вывод текста
    * 
    * @param mixed $text
    * @param mixed $newline   перевод на  новую  строку
    */
    public function text($text,$newline=true )
    {
        $text = $this->encode($text)  ;
        
        $t = str_split($text) ;    
        foreach($t as $b) {
           $this->buffer[]= ord($b);     
        }
        
        
        if($newline) $this->newline( )  ;
    }      

    /**
    * вывод QR кода   
    * 
    * @param mixed $text
    * @param mixed $type     EAN13 Code128 Code39
    */
    public function QR($text,$size=12 )
    {
     //    $text = $this->encode($text)  ;
         $store_len = strlen($text) + 3;
         $store_pL = intval($store_len % 256);
         $store_pH = intval($store_len / 256);
  
         $model=[0x1d, 0x28, 0x6b, 0x04, 0x00, 0x31, 0x41,0x32,0 ] ;
         $size =[0x1d, 0x28, 0x6b, 0x03, 0x00, 0x31, 0x43,intval($size) ] ;
         $error=[0x1d, 0x28, 0x6b, 0x03, 0x00, 0x31, 0x45,0x31] ;
         $store=[0x1d, 0x28, 0x6b, $store_pL, $store_pH, 0x31, 0x50,0x30] ;
         $print=[0x1d, 0x28, 0x6b, 0x03, 0x00, 0x31, 0x51,0x30] ;
         
         $this->addBytes($model) ;
         $this->addBytes($size) ;
         $this->addBytes($error) ;
         $this->addBytes($store) ;
         
        $t = str_split($text) ;    
        foreach($t as $b) {
           $this->buffer[]= ord($b);     
        }
    

        $this->addBytes($print) ;

    
        
    } 


    /**
    * вывод QR кода   
    * 
    * @param mixed $text
    * @param mixed $type     EAN13 Code128 Code39
    */
    public function BarCode($text,int $type )
    {
        if($type == self::BARCODE_CODE128) {
            
            $text = "{B".$text;
            
            if (preg_match("/^\{[A-C][\\x00-\\x7F]+$/", $text) === 0) {
                throw new \Exception("invalid barcode {$text}");
            }  
            
            $this->addBytes( [self::GS , ord('k') , $type , (int)strlen($text) ] );
            
            $t = str_split($text) ;    
            foreach($t as $b) {
               $this->buffer[]= ord($b);     
            }         
            
            
        }
  
        if($type == self::BARCODE_CODE39) {
            
            $text =strtoupper($text);
            
            if (preg_match("/^([0-9A-Z \$\%\+\-\.\/]+|\*[0-9A-Z \$\%\+\-\.\/]+\*)$/", $text) === 0) {
                throw new \Exception("invalid barcode {$text}");
            }  
            
            $this->addBytes( [self::GS , ord('k') , $type , (int)strlen($text) ] );
            
            $t = str_split($text) ;    
            foreach($t as $b) {
               $this->buffer[]= ord($b);     
            }         
            
            
        }
  
        if($type == self::BARCODE_EAN13) {
            
             
            if (preg_match("/^[0-9]{12,13}$/", $text) === 0) {
                throw new \Exception("invalid barcode {$text}");
            }  
            
            $this->addBytes( [self::GS , ord('k') , $type , (int)strlen($text) ] );
            
            $t = str_split($text) ;    
            foreach($t as $b) {
               $this->buffer[]= ord($b);     
            }         
            
            
        }
  
  
  
  
    }       
}  
 
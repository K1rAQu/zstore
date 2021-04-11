<?php
  
namespace App\Pages\Service;

use App\Entity\Customer;
use App\Entity\Doc\Document;
use App\Entity\Item;
use App\Entity\Service;
use App\Helper as H;
use App\System;
use Zippy\Html\DataList\DataView;
use Zippy\Html\Form\AutocompleteTextInput;
use Zippy\Html\Form\Button;
use Zippy\Html\Form\Date;
use Zippy\Html\Form\DropDownChoice;
use Zippy\Html\Form\Form;
use Zippy\Html\Form\SubmitButton;
use Zippy\Html\Form\TextArea;
use Zippy\Html\Form\TextInput;
use Zippy\Html\Label;
use Zippy\Html\Link\ClickLink;
use Zippy\Html\Link\SubmitLink;

/**
 * АРМ кассира общепита
 */
class ARMFood extends \App\Pages\Base
{ 
      private $_pos ;
    
      public function __construct() {
        parent::__construct();


        if (false == \App\ACL::checkShowSer('ARMFood')) {
            return;
        }
        //обшие настройки
        $this->add(new Form('setupform'))->onSubmit($this, 'setupOnClick');
      

        $this->setupform->add(new DropDownChoice('pos', \App\Entity\Pos::findArray('pos_name', ''), 0));
        $this->setupform->add(new DropDownChoice('store', \App\Entity\Store::getList(), H::getDefStore()));
        $this->setupform->add(new DropDownChoice('pricetype', \App\Entity\Item::getPriceTypeList(), H::getDefPriceType()));
        $this->setupform->add(new DropDownChoice('nal', \App\Entity\MoneyFund::getList(false,false,1) , H::getDefMF()));
        $this->setupform->add(new DropDownChoice('beznal', \App\Entity\MoneyFund::getList(false,false,2) , H::getDefMF()));
      
        
      }
        
      public function setupOnClick($sender){
        $store =  $this->setupform->store->getValue() ;
        $nal =  $this->setupform->nal->getValue() ;
        $beznal =  $this->setupform->beznal->getValue() ;
        $pricetype =  $this->setupform->pricetype->getValue() ;
        $this->_pos = \App\Entity\Pos::load($this->setupform->pos->getValue());
    
        if($store==0  || $nal==0  || $beznal==0 || strlen($pricetype)==0 || $this->_pos==null) {
            $this->setError(H::l("notalldata")) ;
            return;
        }
     
      }
      
}
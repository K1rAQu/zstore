<?php

namespace App\Modules\Issue\Pages;

use \App\Application as App;
use \App\System;
use \ZCL\DB\DB as DB;
use \Zippy\Html\Label;
use \Zippy\Html\Panel;
use \Zippy\Html\Form\Form;
use \Zippy\Html\Form\Date;
use \Zippy\Html\Form\DropDownChoice;
use \Zippy\Html\Link\ClickLink;
use \App\Modules\Issue\Entity\Project;
use \App\Modules\Issue\Entity\Issue;
use \App\Entity\User;
use \Zippy\Html\DataList\ArrayDataSource;
use \Zippy\Html\DataList\DataView;

/**
 * страница статистики
 */
class Stat extends \App\Pages\Base {

    public $_list = array();

    public function __construct() {

        parent::__construct();

        $user = System::getUser();

        $allow = (strpos($user->modules, 'issue') !== false || $user->userlogin == 'admin');
        if (!$allow) {
            System::setErrorMsg('Нет права  доступа  к   модулю ');
            App::RedirectHome();
            return;
        }

        $this->add(new Form('filter'))->onSubmit($this, 'filterOnSubmit');
        $this->filter->add(new Date('from', strtotime("-1 month", time())));
        $this->filter->add(new Date('to', time()));
        $projects = Project::findArray('project_name', '', 'project_name');
        $this->filter->add(new DropDownChoice('searchproject', $projects, 0));
        $users = User::findArray('username', '', 'username');

        $user_id = 0;

        if ($user->username != 'admin') {
            $user_id = $user->user_id;
            $users = User::findArray('username', 'user_id=' . $user_id, 'username');
        }

        $this->filter->add(new DropDownChoice('searchemp', $users, $user_id));


        $this->add(new DataView('list', new ArrayDataSource($this, '_list'), $this, 'listOnRow'));
        $this->add(new Label('total'))->setVisible(false);
        ;
    }

    public function filterOnSubmit($sender) {


        $searchproject = $this->filter->searchproject->getValue();
        $searchemp = $this->filter->searchemp->getValue();
        $from = $this->filter->from->getDate();
        $to = $this->filter->to->getDate(true);
        $where = "";
        if ($searchproject > 0)
            $where .= " and project_id = " . $searchproject;
        if ($searchemp > 0)
            $where .= " and user_id = " . $searchemp;

        $total = 0;
        $this->_list = array();
        $conn = DB::getConnect();
        $sql = "select sum(duration) as amount ,username,project_name from  issue_time_view
                where  date(createdon) >= " . $conn->DBDate($from) . " and  date(createdon) <= " . $conn->DBDate($to) . "   
                {$where}
                group by   username,project_name  
                having amount >0
                order  by  username,project_name ";

        $res = $conn->Execute($sql);
        foreach ($res as $v) {
            $item = new \App\DataItem();


            $item->project_name = $v['project_name'];
            $item->username = $v['username'];
            $item->amount = $v['amount'];
            $this->_list[] = $item;
            $total += $item->amount;
        }


        $this->list->Reload();

        $this->total->setVisible($total > 0);
        $this->total->setText($total);
    }

    public function listOnRow($row) {
        $item = $row->getDataItem();
        $row->add(new Label('project_name', $item->project_name));
        $row->add(new Label('username', $item->username));
        $row->add(new Label('amount', $item->amount));
    }

}

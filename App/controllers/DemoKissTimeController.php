<?php

namespace controllers;

class DemoKissTimeController extends KissBaseController {

    public function dateTime() {

        $dateTime = date('d-m-Y H:i:s');
        $dbTime = $this->dbTime();

        return array('phpTime' => $dateTime,
                     'dbTime'  => $dbTime);
    }

    protected function dbTime() {
        $model = new \levitarmouse\kiss_orm\GenericEntity();
        $query = 'select now() as dbtime';

        $dbTime = $model->getMapper()->select($query);

        return $dbTime[0]['dbtime'];
    }
}

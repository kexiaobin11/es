<?php
namespace app\common\model;
use think\Model;

class Bill extends Model
{
    public function getIncome()
    {
        $incomeId = $this->getData('income_id');
        $Income = Income::get($incomeId);
        // var_dump($Income);
        // die();
        return $Income;
    }
}
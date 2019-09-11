<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/10
 * Time: 14:52
 */

namespace Transport\Model;

use Think\Model;

class CountriesModel extends Model
{

    protected $tableName = 'ebay_countries';


    /**
     * @param $where
     * @return int
     */
    public function getCount($where)
    {
        $countries = $this -> alias('a') -> join('inner join ebay_countries_alias as b on a.id = b.pid')
            -> where($where)
            -> field('a.id')
            -> group('a.id')
            -> select();
        return count($countries);
    }


    /**
     * 保存国家主表信息
     * @param $countryInfo
     */
    public function saveCountryInfo($countryInfo)
    {
        if ($countryInfo['name'] && $countryInfo['char_code']) {
            $this -> add($countryInfo);
        }
    }

    public function getCountryCodeByCountryname($country){
        //各种乱七八糟的可能的国家
        $map['a.char_code']=$country;
        $map['a.name']=$country;
        $map['b.alias']=$country;
        $map['_logic']='or';
        $rr=$this->alias('a')->join('ebay_countries_alias b on a.id=b.pid')
            ->where($map)->field('a.char_code')->find();
        if(empty($rr)){
            return '';
        }
       return $rr['char_code'];
    }
}
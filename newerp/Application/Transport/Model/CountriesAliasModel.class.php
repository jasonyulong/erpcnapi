<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/10
 * Time: 14:55
 */

namespace Transport\Model;

use Think\Model;

/**
 * 国家别名
 * Class CountriesAliasModel
 * @package Transport\Model
 */
class CountriesAliasModel extends Model
{

    protected $tableName = 'ebay_countries_alias';

    /**
     * @param $data
     */
    public function multiAddAlias($data)
    {
        foreach ($data as $value) {
            $this -> data($value) -> add();
        }
    }


    /**
     * 根据国家的一个别名来获取这个国家的二字码:
     * @param string $alias : 国家的一个别名
     * @return string
     */
    public function getCharCodeByAlias($alias)
    {
        $targetCharCode = $this -> alias('a')
            -> join('inner join ebay_countries as b on a.pid = b.id')
            -> field('b.char_code')
            -> where(['a.alias' => $alias])
            -> find();

        return $targetCharCode['char_code'];
    }


    /**
     * 根据国家的名称，获取国家的所有的别名; 如果是多个国家的话可以传递数组，
     * 返回的结果将以传递的国家名称为键，所有别名数组为值的结构返回
     * @param $country
     * @return array
     */
    public function getCountryAlias($country)
    {
        if (!is_array($country)) {
            $pid = $this -> where(['alias' => trim($country)]) -> getField('pid');
            $aliases = $this -> where(['pid' => $pid]) -> getField('alias', true);
            return [$country => $aliases];
        }

        $resultArr = [];
        foreach ($country as $item) {
            $result = $this -> alias('a')
                -> join('inner join '. $this -> tableName.' as b on a.pid = b.pid')
                -> where('a.`alias` = "'.$item.'"')
                -> field('b.alias')
                -> select();
            $resultArr[$item] = array_column($result, 'alias');
        }

        return $resultArr;
    }

}
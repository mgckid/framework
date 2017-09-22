<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/18
 * Time: 0:43
 */

namespace houduanniu\base;


use Exceptions\Http\Server\InternalServerErrorException;
use idiorm\orm\ORM;
use Overtrue\Validation\Factory;
use Overtrue\Validation\Translator;
use houduanniu\base\Application;

class Model
{

    /**
     * @var string  模型表名
     */
    protected $tableName = '';
    /**
     * @var string  模型主键
     */
    protected $pk = 'id';

    /**
     * orm 选择数据库并配置
     * @access public
     * @author furong
     * @param string $dbName 数据库标示名称
     * @return string
     * @since  2017年6月20日 10:20:06
     * @abstract
     * @throws \Exception
     */
    public static function selectDb($dbName = 'default')
    {
        $config = Application::config()->get('DB');
        if (!array_key_exists($dbName, $config)) {
            throw new InternalServerErrorException($dbName . '数据库配置不存在');
        }
        if (!in_array($dbName, ORM::get_connection_names())) {
            $dbConfig = $config[$dbName];
            ORM::configure($dbConfig, NULL, $dbName);
        }
        return $dbName;
    }

    /**
     * orm 选择表
     * @access public
     * @author furong
     * @param $table_name 表名
     * @param string $db_name 数据库名
     * @return ORM
     * @since 2017年6月20日 10:11:04
     * @abstract
     */
    public function for_table($table_name, $db_name = 'default')
    {
        return ORM::for_table($table_name, self::selectDb($db_name));
    }

    /**
     * 获取数据库 pdo连接 对象
     * @access public
     * @author furong
     * @param string $db_name
     * @return \PDO
     * @since 2017年6月20日 10:25:57
     * @abstract
     */
    public function get_db($db_name = 'default')
    {
        return ORM::get_db(self::selectDb($db_name));
    }

    /**
     * 开启事务
     * @access public
     * @author furong
     * @param string $db_name
     * @return \PDO
     * @since 2017年6月20日 10:30:35
     * @abstract
     */
    public function beginTransaction($db_name = 'default')
    {
        return $this->get_db($db_name)->beginTransaction();
    }

    /**
     * 事务回滚
     * @access public
     * @author furong
     * @param string $db_name
     * @return \PDO
     * @since 2017年6月20日 10:30:35
     * @abstract
     */
    public function rollBack($db_name = 'default')
    {
        return $this->get_db($db_name)->rollBack();
    }

    /**
     * 事务提交
     * @access public
     * @author furong
     * @param string $db_name
     * @return \PDO
     * @since 2017年6月20日 10:30:35
     * @abstract
     */
    public function commit($db_name = 'default')
    {
        return $this->get_db($db_name)->commit();
    }

    /**
     * 执行原生sql
     * @access public
     * @author furong
     * @param $query
     * @param array $parameters
     * @param string $db_name
     * @return ORM
     * @since 2017年6月20日 10:50:45
     * @abstract
     */
    public function raw_execute($query, $parameters = array(), $db_name = 'default')
    {
        return ORM::raw_execute($query, $parameters = array(), self::selectDb($db_name));
    }

    /**
     * 获取最后一次执行的sql语句
     *
     * @access public
     * @author furong
     * @param string $db_name
     * @return string
     * @since 2017年6月20日 10:53:24
     * @abstract
     */
    public function get_last_query($db_name = 'default')
    {
        return ORM::get_last_query(self::selectDb($db_name));
    }

    public function get_query_log($db_name = 'default')
    {
        return ORM::get_query_log(self::selectDb($db_name));
    }

    /**
     * 获取最后一次执行的sql语句(get_last_query 别名方法)
     *
     * @access public
     * @author furong
     * @param string $db_name
     * @return string
     * @since 2017年6月20日 10:54:45
     * @abstract
     */
    public function _sql($db_name = 'default')
    {
        return $this->get_last_query($db_name);
    }

    /**
     * 模型快捷方法
     * @access public
     * @author furong
     * @param string $db_name
     * @return ORM
     * @since 2017年6月20日 11:38:23
     * @abstract
     * @throws \Exception
     */
    public function orm($db_name = 'default')
    {
        if (empty($this->tableName)) {
            throw new InternalServerErrorException('请设置模型表名');
        }
        if (empty($this->pk)) {
            throw new InternalServerErrorException('请设置模型主键');
        }
        return $this->for_table($this->tableName, $db_name)->use_id_column($this->pk);
    }

    /**
     * 模型验证对象 Laravel Validation 简化无依赖版 验证器对象
     * @access public
     * @author furong
     * @return Factory
     * @since 2017年6月20日 12:09:03
     * @abstract
     */
    public function validate()
    {
        return  Application::validation();
    }

    public function setMessage($msg)
    {
        return Application::getInstance()->setMessage($msg);
    }

    public function getMessage()
    {
        return Application::getInstance()->getMessage();
    }

    public function getInfo($key)
    {
        return Application::getInstance()->getInfo($key);
    }

    public function setInfo($key, $value)
    {
        Application::getInstance()->setInfo($key, $value);
    }

    public function getTableName()
    {
        return $this->tableName;
    }


}
<?php
namespace ImiApp\Module\Member\Model\Base;

use Imi\Model\Model;
use Imi\Model\Annotation\DDL;
use Imi\Model\Annotation\Table;
use Imi\Model\Annotation\Column;
use Imi\Model\Annotation\Entity;

/**
 * MemberBase
 * @Entity
 * @Table(name="tb_member", id={"id"})
 * @DDL("CREATE TABLE `tb_member` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(16) NOT NULL COMMENT '用户名',
  `password` varchar(255) NOT NULL COMMENT '密码',
  `register_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '注册时间',
  `last_login_time` datetime NOT NULL COMMENT '最后登录时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `username` (`username`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8")
 * @property int $id 
 * @property string $username 用户名
 * @property string $password 密码
 * @property string $registerTime 注册时间
 * @property string $lastLoginTime 最后登录时间
 */
abstract class MemberBase extends Model
{
    /**
     * id
     * @Column(name="id", type="int", length=10, accuracy=0, nullable=false, default="", isPrimaryKey=true, primaryKeyIndex=0, isAutoIncrement=true)
     * @var int
     */
    protected $id;

    /**
     * 获取 id
     *
     * @return int
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * 赋值 id
     * @param int $id id
     * @return static
     */ 
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * 用户名
     * username
     * @Column(name="username", type="varchar", length=16, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     * @var string
     */
    protected $username;

    /**
     * 获取 username - 用户名
     *
     * @return string
     */ 
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * 赋值 username - 用户名
     * @param string $username username
     * @return static
     */ 
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * 密码
     * password
     * @Column(name="password", type="varchar", length=255, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     * @var string
     */
    protected $password;

    /**
     * 获取 password - 密码
     *
     * @return string
     */ 
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * 赋值 password - 密码
     * @param string $password password
     * @return static
     */ 
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * 注册时间
     * register_time
     * @Column(name="register_time", type="timestamp", length=0, accuracy=0, nullable=false, default="CURRENT_TIMESTAMP", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     * @var string
     */
    protected $registerTime;

    /**
     * 获取 registerTime - 注册时间
     *
     * @return string
     */ 
    public function getRegisterTime()
    {
        return $this->registerTime;
    }

    /**
     * 赋值 registerTime - 注册时间
     * @param string $registerTime register_time
     * @return static
     */ 
    public function setRegisterTime($registerTime)
    {
        $this->registerTime = $registerTime;
        return $this;
    }

    /**
     * 最后登录时间
     * last_login_time
     * @Column(name="last_login_time", type="datetime", length=0, accuracy=0, nullable=false, default="", isPrimaryKey=false, primaryKeyIndex=-1, isAutoIncrement=false)
     * @var string
     */
    protected $lastLoginTime;

    /**
     * 获取 lastLoginTime - 最后登录时间
     *
     * @return string
     */ 
    public function getLastLoginTime()
    {
        return $this->lastLoginTime;
    }

    /**
     * 赋值 lastLoginTime - 最后登录时间
     * @param string $lastLoginTime last_login_time
     * @return static
     */ 
    public function setLastLoginTime($lastLoginTime)
    {
        $this->lastLoginTime = $lastLoginTime;
        return $this;
    }

}

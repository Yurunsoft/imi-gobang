<?php
namespace ImiApp\Module\Member\Service;

use Imi\Bean\Annotation\Bean;
use Imi\Server\Session\Session;
use ImiApp\Exception\BusinessException;
use ImiApp\Module\Member\Model\Member;
use ImiApp\Exception\NotFoundException;

/**
 * @Bean("MemberService")
 */
class MemberService
{
    /**
     * 使用用户ID获取记录
     *
     * @param integer $id
     * @return \ImiApp\Module\Member\Model\Member
     */
    public function get(int $id): Member
    {
        $record = Member::find($id);
        if(!$record)
        {
            throw new NotFoundException(sprintf('未找到用户 %s', $id));
        }
        return $record;
    }

    /**
     * 使用用户名获取记录
     *
     * @param string $username
     * @return \ImiApp\Module\Member\Model\Member
     */
    public function getByUsername(string $username): Member
    {
        $record = Member::find([
            'username'  =>  $username,
        ]);
        if(!$record)
        {
            throw new NotFoundException(sprintf('未找到用户 %s', $username));
        }
        return $record;
    }
  
    /**
     * 注册
     *
     * @param string $username
     * @param string $password
     * @return void
     */
    public function register(string $username, string $password)
    {
        try {
            $this->getByUsername($username);
            throw new BusinessException('该用户已被注册');
        } catch(NotFoundException $ne) {

        }
        $record = Member::newInstance();
        $record->username = $username;
        $record->password = password_hash($password, PASSWORD_BCRYPT);
        $record->save();
    }

    /**
     * 登录
     *
     * @param string $username
     * @param string $password
     * @return void
     */
    public function login(string $username, string $password)
    {
        $record = $this->getByUsername($username);
        if(password_verify($password, $record->password))
        {
            $record->lastLoginTime = date('Y-m-d H:i:s');
            $record->save();
            Session::set('memberId', $record->id);
        }
        else
        {
            throw new BusinessException('登录失败');
        }
    }

}

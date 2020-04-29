<?php
namespace ImiApp\Module\Member\Model;

use Imi\Bean\Annotation\Inherit;
use Imi\Model\Annotation\Serializables;
use ImiApp\Module\Member\Model\Base\MemberBase;

/**
 * Member
 * @Inherit
 * @Serializables(mode="deny", fields={"password"})
 */
class Member extends MemberBase
{

}

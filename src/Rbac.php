<?php
namespace Tussock\Hornbill;

// 权限
class Rbac{
    /**
     * @var
     * pdo
     */
    protected $pdo;

    /**
     * @var Permissions|null
     * 权限
     */
    public $Permissions = null;

    /**
     * @var User|null
     * user
     */
    public $User = null;

    /**
     * @var User|null
     * 用户
     */
    public $Users = null;

    /**
     * @var Roles
     * 角色
     */
    public $Roles;

    /**
     * @param $pdo
     * 构造函数
     */
    public function __construct($pdo){
        $this->pdo = $pdo;
        $this->Permissions = new Permissions($pdo);
        $this->Users = $this->User = new User($pdo);
        $this->Roles = new Roles($pdo);
    }

    /**
     * @return mixed|User|null
     * 用户
     */
    public function Users()
    {
        return $this->Users;
    }

    /**
     * @return Roles
     * 角色
     */
    public function Roles(){
        return $this->Roles;
    }

    /**
     * @return mixed|Permissions|null
     * 权限
     */
    public function permissions()
    {
        return $this->Permissions;
    }

    /**
     * @param $permissionId
     * @param $userId
     * @return void
     * 检查权限
     */
    public function check($permissionId,$userId)
    {
        return $this->User->check($permissionId, $userId);
    }
}
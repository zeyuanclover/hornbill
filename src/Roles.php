<?php

namespace Tussock\Hornbill;

use PDO;

class Roles
{
    /**
     * @var
     * pdo
     */
    public $pdo;

    /**
     * @param $pdo
     * 构造函数
     */
    public function __construct($pdo){
        $this->pdo = $pdo;
    }

    /**
     * @param $roleID
     * @param $permissionID
     * @return void
     * role permissions
     */
    public function assign($roleID,$permissionID)
    {
        $this->pdo->insert('auth_rolepermissions',[
            'RoleID'=>$roleID,
            'PermissionID'=>$permissionID,
            'AssignmentDate'=>time()
        ],false);
    }
}
<?php

namespace Tussock\Hornbill;

class User
{
    /**
     * @var
     * pdo
     */
    protected $pdo;

    /**
     * @param $pdo
     * 构造函数
     */
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @param $permissionId
     * @param $userId
     * @return void
     * 检查权限
     */
    public function check($permissionId,$userId)
    {
        // 根据用户id找到roleid
        $currentPermissionDetails = $this->pdo->database('user')->where('ID',$permissionId)->getOne('auth_permissions');
        $roles = $this->pdo->database('user')->where("UserID", $userId)->get('auth_userroles');
        foreach ($roles as $role) {
            $roleDetails = $this->pdo->database('user')->where('ID', $role['RoleID'])->getOne('auth_roles');
            if($roleDetails['IsActiveSwitch']==1){
                // 根据roleid 查找permissios
                $permissions = $this->pdo->database('user')->where('RoleID',$role['RoleID'])->get('auth_rolepermissions');
                if($currentPermissionDetails['IsActiveSwitch']==1){
                    foreach ($permissions as $permission) {
                        if ($permission['PermissionID'] == $permissionId) {
                            return true;
                        }else{
                            $permissionDetails = $this->pdo->database('user')->where('ID',$permission['PermissionID'])->getOne('auth_permissions');
                            if (($currentPermissionDetails['Lft'] >= $permissionDetails['Lft']) && $currentPermissionDetails['Rght'] <= $permissionDetails['Rght']) {
                                return true;
                            }
                        }
                    }
                }
            }
        }
        return false;
    }

    /**
     * @param $roleID
     * @param $userID
     * @return void
     */
    public function assign($roleID,$userID)
    {
        $this->pdo->insert('auth_userroles',[
            'UserID'=>$roleID,
            'RoleID'=>$userID,
            'AssignmentDate'=>time()
        ],false);
    }
}
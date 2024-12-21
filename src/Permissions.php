<?php

namespace Tussock\Hornbill;

class Permissions
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
     * @return void
     * 通过路径获得id
     */
    public function pathId($path)
    {
        $path = trim($path, '/');
        $pathArr = explode('/', $path);
        $node = $this->recursiveGetId($pathArr);
        if(isset($node['ID'])){
            return $node['ID'];
        }
        return null;
    }

    /**
     * @param $pathID
     * @return string
     * 根据id获得路径
     */
    public function getPath($pathID)
    {
        $rs = $this->recursiveGetPath($pathID);
        if ($rs){
            return '/'.implode('/',$rs);
        }else{
            return null;
        }
    }

    /**
     * @param $permissionsID
     * @return false
     * 根据permission 获取role
     */
    public function roles($permissionsID)
    {
        // 自己
        $rolesArr = [];
        $roles = $this->pdo->database('user')->where('PermissionID',$permissionsID)->get('auth_rolepermissions');
        foreach ($roles as $role){
            $rolesArr[] = $role['RoleID'];
        }

        return array_unique($rolesArr);

        // 获得所有下级
        $childrenArr = [];
        $permissionDetail = $this->pdo->database('user')->where('ID',$permissionsID)->getOne('auth_permissions');
        $children = $this->pdo->database('user')->where('Lft',$permissionDetail['Lft'],'>=')->where('Rght',$permissionDetail['Rght'],'<=')->get('auth_permissions');
        foreach ($children as $child) {
            $childrenArr[] = $child['ID'];
        }

        $roles = $this->pdo->database('user')->where('PermissionID',$childrenArr,'in')->get('auth_rolepermissions');
        foreach ($roles as $role){
            $rolesArr[] = $role['RoleID'];
        }

        return array_unique($rolesArr);
    }

    /**
     * @param $id
     * @return array
     * 根据id获取路径
     */
    public function recursiveGetPath($id)
    {
        static $paths = [];
        $node =  $this->pdo->database('user')->where('ID',$id)->getValue('auth_permissions','Title,Parent');
        if($node['Parent']>0){
            $this->recursiveGetPath($node['Parent']);
        }
        $paths[] = $node['Title'];
        return $paths;
    }

    /**
     * @param $pathArr
     * @return mixed
     * 递归获取id
     */
    public function recursiveGetId($pathArr,$pid=0){
        $path = array_shift($pathArr);
        if ($pid){
            $node = $this->pdo->database('user')->where('Parent',$pid)->where('Title',$path)->getOne('auth_permissions');
        }else{
            $node = $this->pdo->database('user')->where('Title',$path)->getOne('auth_permissions');
        }

        $this->pdo->database('user')->reset();
        if (count($pathArr)>0) {
            return $this->recursiveGetId($pathArr,$node['ID']);
        }
        return $node;
    }
}
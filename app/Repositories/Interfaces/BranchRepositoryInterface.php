<?php
namespace App\Repositories\Interfaces;

interface BranchRepositoryInterface
{
    public function all();
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function saveuser(array $data,$id);
    public function addUserToBranchByBranchId(array $data,$id);
    public function finduser($id);
    public function updateuser(array $data,$id);
    public function toggleStatus(array $data);
    public function getBranch($data);
    public function allactive();



    // New methods for branch staff
    public function findBranchStaff($id);
    public function addbranchStaff(array $data);
     public function findBranchStaffuser($id);
    public function updatebranchStaff(array $data,$d);
    public function deleteBranchStaff($id);
    public function toggleUserStatus(array $data);



    // New method for branch shifts
    public function BranchShiftlist($id);
    public function findBranchShift($id);
    public function BranchShiftCreate(array $data);
    public function BranchShiftUpdate(array $data);
    public function BranchShiftDelete($id);



    //Station
     public function createStation(array $data);
     public function getBranchStationData($branchId = null,$stationId=null);
     public function findStation($id);



}


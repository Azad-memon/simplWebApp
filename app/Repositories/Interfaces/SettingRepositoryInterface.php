<?php
namespace App\Repositories\Interfaces;

interface SettingRepositoryInterface
{
    public function Loyaltylist();
    public function Loyaltyfind($id);
    public function Loyaltycreate(array $data);
    public function Loyaltyupdate($id, array $data);
    public function PaymentMethodList();
    public function PaymentMethodActiveList();
    public function PaymentMethodfind($id);
    public function PaymentMethodcreate(array $data);
    public function PaymentMethodupdate($id, array $data);


}


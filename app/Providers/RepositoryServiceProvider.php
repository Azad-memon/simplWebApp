<?php
namespace App\Providers;


use App\Repositories\CmsPageRepository;
use App\Repositories\Interfaces\CartRepositoryInterface;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Repositories\Interfaces\CmsPageRepositoryInterface;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Repositories\Interfaces\SettingRepositoryInterface;
use App\Repositories\OrderRepository;
use App\Repositories\SettingRepository;
use Illuminate\Support\ServiceProvider;
use App\Repositories\Interfaces\LanguageRepositoryInterface;
use App\Repositories\Interfaces\LanguageTranslationRepositoryInterface;
use App\Repositories\Interfaces\BranchRepositoryInterface;
use App\Repositories\Interfaces\ConstraintRepositoryInterface;
use App\Repositories\Interfaces\UsersRepositoryInterface;
use App\Repositories\Interfaces\IngredientRepositoryInterface;
use App\Repositories\LanguageRepository;
use App\Repositories\LanguageTranslationRepository;
use App\Repositories\BranchRepository;
use App\Repositories\ConstraintRepository;
use App\Repositories\UserRepository;
use App\Repositories\IngredientRepository;
use App\Repositories\ProductRepository;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use App\Repositories\Interfaces\ProductVariantRepositoryInterface;
use App\Repositories\ProductVariantRepository;
use App\Repositories\Interfaces\DealRepositoryInterface;
use App\Repositories\Interfaces\CouponRepositoryInterface;
use App\Repositories\DealRepository;

use App\Repositories\Interfaces\CustomerRepositoryInterface;
use App\Repositories\CustomerRepository;
use App\Repositories\Interfaces\CustomerAddressRepositoryInterface;
use App\Repositories\CustomerAddressRepository;
use App\Repositories\Interfaces\AddonsRepositoryInterface;
use App\Repositories\AddonsRepository;
use App\Repositories\CartRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\CouponRepository;





class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {

        $this->app->bind(LanguageRepositoryInterface::class, LanguageRepository::class);
        $this->app->bind(LanguageTranslationRepositoryInterface::class, LanguageTranslationRepository::class);
        $this->app->bind(BranchRepositoryInterface::class, BranchRepository::class);
        $this->app->bind(ConstraintRepositoryInterface::class, ConstraintRepository::class);
        $this->app->bind(UsersRepositoryInterface::class, UserRepository::class);
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(IngredientRepositoryInterface::class, IngredientRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(ProductVariantRepositoryInterface::class, ProductVariantRepository::class);
        $this->app->bind(DealRepositoryInterface::class, DealRepository::class);

        $this->app->bind(CustomerRepositoryInterface::class, CustomerRepository::class);
        $this->app->bind(CustomerAddressRepositoryInterface::class, CustomerAddressRepository::class);
        $this->app->bind(AddonsRepositoryInterface::class, AddonsRepository::class);
        $this->app->bind(CartRepositoryInterface::class, CartRepository::class);
         $this->app->bind(CouponRepositoryInterface::class, CouponRepository::class);
         $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
         $this->app->bind(CmsPageRepositoryInterface::class, CmsPageRepository::class);
         $this->app->bind(SettingRepositoryInterface::class, SettingRepository::class);




    }
}

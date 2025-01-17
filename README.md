# Intro
Xây dựng lớp truy vấn cho phép query nhanh chóng, tiện lợi


#Install
```bash
    composer require at-core/core-repo
```
Truy cập AppServiceProvider khai báo
```php
  public function register()
    {
        $this->app->bind(BaseRepositoryInterface::class, BaseRepository::class);
        .... các repository đăng ký khác
        $this->app->bind(PaymentInvoicesRepositoryInterface::class, PaymentInvoiceRepository::class);
    }
```
## Example
Controller 
```php
<?php

namespace Isvn\Admin\Http\Controllers\Api;

use App\Services\ResponseService;
use AtCore\CoreRepo\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Isvn\Admin\Services\PaymentInvoiceService;

class ApiAdmPaymentController extends Controller
{
    protected $paymentInvoiceService;

    public function __construct(PaymentInvoiceService $paymentInvoiceService)
    {
        $this->paymentInvoiceService = $paymentInvoiceService;
    }

    public function index(Request $request)
    {
        $paginate = $this->paymentInvoiceService->paginate($request->all());
        list($data, $meta) = ResponseHelper::getLengthAwarePaginatorData($paginate);
        
        return ResponseService::success([
            'payments'          => $data,
            'meta'              => $meta
        ]);
    }
}

```

Service
```php
<?php
/**
 * Created By PhpStorm
 * Code By : trungphuna
 * Date: 1/9/25
 */

namespace Isvn\Admin\Services;


use App\Core\Repositories\Contracts\PaymentInvoicesRepositoryInterface;
use Isvn\Admin\Entities\PaymentInvoice;

class PaymentInvoiceService
{
    protected $paymentRepository;

    public function __construct(PaymentInvoicesRepositoryInterface $paymentInvoicesRepository)
    {
        $this->paymentRepository = $paymentInvoicesRepository;
    }

    public function paginate($params)
    {
        return $this->paymentRepository->paginate($params);
    }
}
```
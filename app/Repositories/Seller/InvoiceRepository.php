<?php

namespace App\Repositories\Seller;

use App\Models\Invoice;
use Illuminate\Container\Container as Application;
use Prettus\Repository\Eloquent\BaseRepository;

class InvoiceRepository extends BaseRepository
{

    public function __construct(Application $app)
    {
        parent::__construct($app);
    }


    public function storeInvoice($requestData)
    {
        return $this->model->create([
            'vendor_id' => (int)$requestData['vendor_id'],
            'product_id' => (int)$requestData['product_id'],
            'user_id' => $requestData['user_id'],
            'invoice_number' => $requestData['invoice_number'] ?? null,
            'status' => $requestData['status'] ?? 'free',
            'type' => $requestData['type'],
            'price' => $requestData['price'] ?? 0.0000,
            'quantity' => $requestData['quantity'],
        ]);
    }


    public function deleteInvoice($id)
    {
        return $this->model->where('id', $id)->delete();
    }

    public function model(): string
    {
        return Invoice::class;
    }
}

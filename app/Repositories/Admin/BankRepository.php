<?php
namespace App\Repositories\Admin;

use App\Models\Bank;

class BankRepository
{
    public function __construct(public BankTranslationRepository $bankTranslationRepository){}
    public function getAllBank()
    {
        return $this->getModel()::with('translations','countries')
            ->orderByDesc('id')
            ->get();
    }
    public function store($request)
    {
        $bank = $this->getModel()::create($request);
        if ($bank)
        {
            $this->bankTranslationRepository->storeOrUpdate($request, $bank->id);
            // Sync countries with the bank
            $bank->countries()->sync($request['country_id']);
        }
        return $bank;
    }
    public function update($data_request, $bank_id)
    {
        $bank = $this->getModelById($bank_id);
        $bank->update($data_request);
        $this->bankTranslationRepository->storeOrUpdate($data_request, $bank->id);
        // Sync countries with the bank
        $bank->countries()->sync($data_request['country_id']);
        return $bank;
    }
    public function getModelById($id){
        return $this->getModel()::find($id);
    }
    private function getModel() : String
    {
        return Bank::class;
    }
}

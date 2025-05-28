<?php

namespace Modules\Invoice\Interfaces;

interface InvoiceServiceInterface
{
    public function create(array $data);
    public function update(array $data, int $id);
    public function delete(int $id);
    public function get(int $id);
    public function getAll();
}

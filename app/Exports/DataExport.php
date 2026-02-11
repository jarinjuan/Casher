<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class DataExport implements WithMultipleSheets
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function sheets(): array
    {
        $sheets = [];

        if (isset($this->data['transactions']) && ! empty($this->data['transactions'])) {
            $sheets[] = new TransactionSheet($this->data['transactions']);
        }

        if (isset($this->data['categories']) && ! empty($this->data['categories'])) {
            $sheets[] = new CategorySheet($this->data['categories']);
        }

        if (isset($this->data['investments']) && ! empty($this->data['investments'])) {
            $sheets[] = new InvestmentSheet($this->data['investments']);
        }

        return $sheets;
    }
}

class TransactionSheet implements FromArray, WithHeadings
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        return $this->data;
    }

    public function headings(): array
    {
        return ['Title', 'Amount', 'Type', 'Note', 'Category', 'Currency', 'Created At'];
    }

    public function title(): string
    {
        return 'Transactions';
    }
}

class CategorySheet implements FromArray, WithHeadings
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        return $this->data;
    }

    public function headings(): array
    {
        return ['Name', 'Monthly Budget', 'Budget Currency'];
    }

    public function title(): string
    {
        return 'Categories';
    }
}

class InvestmentSheet implements FromArray, WithHeadings
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        return $this->data;
    }

    public function headings(): array
    {
        return ['Type', 'Name', 'Symbol', 'External ID', 'Quantity', 'Average Price', 'Currency'];
    }

    public function title(): string
    {
        return 'Investments';
    }
}

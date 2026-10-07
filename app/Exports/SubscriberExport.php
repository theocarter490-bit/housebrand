<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SubscriberExport implements FromCollection, WithHeadings
{
    protected $subscribers;

    public function __construct(Collection $subscribers)
    {
        $this->subscribers = $subscribers;
    }

    public function collection(): Collection
    {
        return $this->subscribers->map(function ($subscriber) {
            return [
                'emails' => $subscriber->email,
            ];
        });
    }

    public function headings(): array
    {
        return ['emails'];
    }
}

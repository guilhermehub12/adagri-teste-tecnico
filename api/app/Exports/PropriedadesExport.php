<?php

namespace App\Exports;

use App\Models\Propriedade;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PropriedadesExport implements FromCollection, WithHeadings, WithMapping
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Propriedade::with('produtor');

        // Aplicar filtro de município
        if (!empty($this->filters['municipio'])) {
            $query->where('municipio', $this->filters['municipio']);
        }

        // Aplicar filtro de produtor
        if (!empty($this->filters['produtor_id'])) {
            $query->where('produtor_id', $this->filters['produtor_id']);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nome',
            'Município',
            'UF',
            'Inscrição Estadual',
            'Área Total (ha)',
            'Produtor',
        ];
    }

    public function map($propriedade): array
    {
        return [
            $propriedade->id,
            $propriedade->nome,
            $propriedade->municipio,
            $propriedade->uf,
            $propriedade->inscricao_estadual ?? 'N/A',
            $propriedade->area_total,
            $propriedade->produtor->nome ?? 'N/A',
        ];
    }
}

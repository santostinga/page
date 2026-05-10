<?php
/**
 * Planos Sizo Software — preços por parcela mensal (licença anual ÷ 12).
 * Contactos e textos legais/comerciais.
 */
return [
    'contacto' => [
        'email' => 'info@sizotech.net',
        /** Formato internacional Moçambique */
        'telefone_display' => '+258 84 025 5253',
        'telefone_href' => 'tel:+258840255253',
        /** Ligações «Contacto» e número clicável abrem o WhatsApp Web / app */
        'whatsapp_url' => 'https://wa.me/258840255253?' . http_build_query([
            'text' => 'Olá! Gostaria de mais informações sobre o Sizo Software.',
        ]),
    ],

    'nota_licenca' => 'A licença de uso é contratada por período anual. O valor total é dividido em 12 prestações mensais iguais — os valores indicados são o montante de cada parcela mensal (MT/mês).',

    /**
     * Planos: LITE = 1 módulo à escolha | STANDARD = 2 módulos | PRO = os 3 módulos.
     */
    'planos' => [
        [
            'id' => 'lite',
            'nome' => 'LITE',
            'titulo_card' => 'Um módulo à escolha',
            'tipo' => 'LITE',
            'preco_mt' => '1 999,00',
            'preco_periodo' => '/ mês',
            'destaque' => false,
            'ativo' => true,
            'resumo' => 'Seleccione apenas um eixo: produtos & stock, serviços em catálogo, ou serviços livres — consoante o perfil da sua empresa.',
            'bullets' => [
                'Escolhe 1 entre: Produtos · Serviços (catálogo) · Serviço livre',
                'Funcionalidades completas dentro do módulo seleccionado',
            ],
        ],
        [
            'id' => 'standard',
            'nome' => 'STANDARD',
            'titulo_card' => 'Dois módulos combinados',
            'tipo' => 'STANDARD',
            'preco_mt' => '2 499,00',
            'preco_periodo' => '/ mês',
            'destaque' => false,
            'ativo' => true,
            'resumo' => 'Combine dois dos três módulos para alinhar stock, catálogo de serviços e vendas livres à sua operação.',
            'bullets' => [
                'Até 2 módulos activos à sua escolha (entre os 3 disponíveis)',
                'Indicado para empresas com operações cruzadas',
            ],
        ],
        [
            'id' => 'pro',
            'nome' => 'PRO',
            'titulo_card' => 'Suite completa',
            'tipo' => 'PRO',
            'preco_mt' => '3 499,00',
            'preco_periodo' => '/ mês',
            'destaque' => true,
            'ativo' => true,
            'resumo' => 'Acesso integral: produtos, serviços em catálogo e serviços livres — máxima flexibilidade num único plano.',
            'bullets' => [
                'Os 3 módulos incluídos: Produtos + Serviços (catálogo) + Serviço livre',
                'Para organizações que querem cobrir todas as linhas de negócio',
            ],
        ],
    ],

    /** Linhas da tabela comparativa (colunas fixas LITE | STANDARD | PRO) */
    'comparacao' => [
        [
            'criterio' => 'Parcela mensal (÷ 12 meses)',
            'lite' => '1 999,00 MT',
            'standard' => '2 499,00 MT',
            'pro' => '3 499,00 MT',
            'destaque' => true,
        ],
        [
            'criterio' => 'Quantos módulos pode activar',
            'lite' => '1 (escolhe qual)',
            'standard' => '2 (combinação à escolha)',
            'pro' => '3 (todos)',
            'destaque' => false,
        ],
        [
            'criterio' => 'Produtos & inventário',
            'lite' => 'Se escolher como único módulo',
            'standard' => 'Sim, se integrar na sua combinação',
            'pro' => 'Incluído',
            'destaque' => false,
        ],
        [
            'criterio' => 'Serviços (catálogo)',
            'lite' => 'Se escolher como único módulo',
            'standard' => 'Sim, se integrar na sua combinação',
            'pro' => 'Incluído',
            'destaque' => false,
        ],
        [
            'criterio' => 'Serviço livre',
            'lite' => 'Se escolher como único módulo',
            'standard' => 'Sim, se integrar na sua combinação',
            'pro' => 'Incluído',
            'destaque' => false,
        ],
    ],
];

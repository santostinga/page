<?php
/**
 * Planos Sizo Software · preços por parcela mensal (licença anual ÷ 12).
 */
return [
    'contacto' => [
        'email' => 'info@sizotech.net',
        'telefone_display' => '+258 84 025 5253',
        'telefone_href' => 'tel:+258840255253',
        'whatsapp_url' => 'https://wa.me/258840255253?' . http_build_query([
            'text' => 'Olá! Gostaria de mais informações sobre o Sizo Software.',
        ]),
        'app_url' => 'https://app.sizotech.net',
    ],

    'nota_licenca' => 'A licença é anual. O valor total é dividido em 12 prestações mensais iguais. Os preços abaixo são a parcela mensal (MT/mês).',

    'planos' => [
        [
            'id' => 'starter',
            'nome' => 'Starter',
            'titulo_card' => 'Um módulo à escolha',
            'tipo' => 'LITE',
            'preco_mt' => '1 999,00',
            'preco_periodo' => '/ mês',
            'destaque' => false,
            'ativo' => true,
            'resumo' => 'Ideal para começar: active um eixo (produtos e stock, serviços em catálogo ou serviço livre).',
            'bullets' => [
                '1 módulo à escolha (Produtos, Serviços ou Serviço livre)',
                'Facturação e documentos essenciais',
                'Utilizadores e permissões',
                'Suporte por e-mail / WhatsApp',
            ],
        ],
        [
            'id' => 'business',
            'nome' => 'Business',
            'titulo_card' => 'Dois módulos combinados',
            'tipo' => 'STANDARD',
            'preco_mt' => '2 499,00',
            'preco_periodo' => '/ mês',
            'destaque' => true,
            'ativo' => true,
            'resumo' => 'Para empresas com operação cruzada: combine dois módulos e cubra stock e serviços no mesmo plano.',
            'bullets' => [
                'Até 2 módulos activos em simultâneo',
                'POS, inventário e relatórios',
                'Automação e fecho de caixa',
                'Multiempresa e multiutilizador',
            ],
        ],
        [
            'id' => 'enterprise',
            'nome' => 'Enterprise',
            'titulo_card' => 'Suite completa',
            'tipo' => 'PRO',
            'preco_mt' => '3 499,00',
            'preco_periodo' => '/ mês',
            'destaque' => false,
            'ativo' => true,
            'resumo' => 'Acesso integral: produtos, serviços em catálogo e serviço livre. Máxima flexibilidade.',
            'bullets' => [
                'Os 3 módulos incluídos',
                'Todas as funcionalidades do produto',
                'Prioridade no onboarding',
                'Para organizações em crescimento',
            ],
        ],
    ],

    'comparacao' => [
        [
            'criterio' => 'Parcela mensal (÷ 12 meses)',
            'lite' => '1 999,00 MT',
            'standard' => '2 499,00 MT',
            'pro' => '3 499,00 MT',
            'destaque' => true,
        ],
        [
            'criterio' => 'Módulos activos',
            'lite' => '1 (escolhe qual)',
            'standard' => '2 (combinação)',
            'pro' => '3 (todos)',
            'destaque' => false,
        ],
        [
            'criterio' => 'Produtos & inventário',
            'lite' => 'Se escolher o módulo',
            'standard' => 'Na sua combinação',
            'pro' => 'Incluído',
            'destaque' => false,
        ],
        [
            'criterio' => 'Serviços (catálogo)',
            'lite' => 'Se escolher o módulo',
            'standard' => 'Na sua combinação',
            'pro' => 'Incluído',
            'destaque' => false,
        ],
        [
            'criterio' => 'Serviço livre',
            'lite' => 'Se escolher o módulo',
            'standard' => 'Na sua combinação',
            'pro' => 'Incluído',
            'destaque' => false,
        ],
    ],
];

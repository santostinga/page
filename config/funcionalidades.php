<?php
/**
 * Funcionalidades do produto · cartões + visão geral + checklist.
 */
return [
    'lead' => [
        'titulo' => 'Tudo o que precisa para gerir o negócio',
        'texto' => 'Módulos pensados para o dia-a-dia: vendas, stock, documentos, caixa e equipa numa só plataforma.',
    ],

    /** Cartões da grelha (abre modal) */
    'cartoes' => [
        [
            'id' => 'dashboard',
            'titulo' => 'Dashboard',
            'descricao' => 'KPI de vendas, documentos e despesas num só ecrã.',
            'screenshot' => 'dashboard.png',
            'detalhe' => 'Acompanhe vendas do mês, documentos emitidos, contactos activos e a evolução de facturação versus despesas, com acesso rápido a nova factura e venda rápida.',
        ],
        [
            'id' => 'facturacao',
            'titulo' => 'Facturação',
            'descricao' => 'Cotações, facturas, recibos e notas de crédito.',
            'screenshot' => 'facturacao.png',
            'detalhe' => 'Gerencie documentos comerciais com pesquisa, filtros e emissão em segundos. Converta cotações em facturas e acompanhe o estado de cada documento.',
        ],
        [
            'id' => 'inventario',
            'titulo' => 'Inventário',
            'descricao' => 'Stock, entradas, saídas e alertas de mínimo.',
            'screenshot' => 'inventario.png',
            'detalhe' => 'Controle quantidades por produto, movimentos e stock baixo. Registe entradas e saídas e mantenha o inventário alinhado com as vendas.',
        ],
        [
            'id' => 'clientes',
            'titulo' => 'Clientes',
            'descricao' => 'Fichas, extratos e histórico comercial.',
            'screenshot' => 'cadastros.png',
            'detalhe' => 'Organize clientes e contactos no mesmo fluxo de cadastros. Consulte extratos, documentos e saldos por cliente.',
        ],
        [
            'id' => 'fornecedores',
            'titulo' => 'Fornecedores',
            'descricao' => 'Cadastro e relação com compras e stock.',
            'screenshot' => 'cadastros.png',
            'detalhe' => 'Mantenha fornecedores organizados junto de categorias, marcas e produtos. Base limpa para operações e relatórios.',
        ],
        [
            'id' => 'servicos',
            'titulo' => 'Serviços',
            'descricao' => 'Catálogo de serviços com preços e IVA.',
            'screenshot' => 'venda-rapida.png',
            'detalhe' => 'Venda serviços com a mesma fluidez dos produtos: preços claros, categorias e integração directa na facturação e POS.',
        ],
        [
            'id' => 'operacoes',
            'titulo' => 'Operações',
            'descricao' => 'Ordens de serviço, agendamentos e guias.',
            'screenshot' => 'operacoes.png',
            'detalhe' => 'Gerencie ordens de serviço, levantamentos, agendamentos, guias de entrega e de transporte num único módulo operacional.',
        ],
        [
            'id' => 'automacao',
            'titulo' => 'Automação',
            'descricao' => 'Facturação recorrente e lembretes.',
            'screenshot' => 'automacao.png',
            'detalhe' => 'Automatize facturação recorrente, lembretes e cobranças. Veja próximas execuções e o histórico de envios com estado de sucesso.',
        ],
        [
            'id' => 'relatorios',
            'titulo' => 'Relatórios',
            'descricao' => 'Vendas, stock, clientes e finanças.',
            'screenshot' => 'relatorios.png',
            'detalhe' => 'Hub de relatórios: resumo geral, extratos, stock, vendas por período ou produto, e-mails enviados e registo de actividade.',
        ],
        [
            'id' => 'caixa',
            'titulo' => 'Caixa',
            'descricao' => 'Turnos, movimentos e fecho de caixa.',
            'screenshot' => 'fecho-caixa.png',
            'detalhe' => 'Abra e feche turnos, veja espécie esperada, filtre pagamentos e imprima movimentos. Controlo diário da tesouraria.',
        ],
        [
            'id' => 'permissoes',
            'titulo' => 'Permissões',
            'descricao' => 'Papéis por módulo, com controlo fino.',
            'screenshot' => 'permissoes.png',
            'detalhe' => 'Crie papéis de sistema ou personalizados e atribua permissões por módulo. Ideal para equipas com funções distintas.',
        ],
        [
            'id' => 'utilizadores',
            'titulo' => 'Utilizadores',
            'descricao' => 'Convites, cargos e estados da equipa.',
            'screenshot' => 'utilizadores.png',
            'detalhe' => 'Convide utilizadores por e-mail, defina cargos e active ou desactive contas. Controlo claro de quem acede ao sistema.',
        ],
    ],

    /** Checklist "Tudo o que o Sizo faz" */
    'checklist' => [
        ['titulo' => 'Dashboard', 'texto' => 'KPI de vendas, documentos e despesas num único painel.'],
        ['titulo' => 'Facturação', 'texto' => 'Cotações, facturas, recibos, NC e conversão de documentos.'],
        ['titulo' => 'Inventário', 'texto' => 'Stock actual, mínimos, entradas, saídas e ajustes.'],
        ['titulo' => 'POS', 'texto' => 'Venda rápida no balcão com artigos, IVA e totais.'],
        ['titulo' => 'Caixa', 'texto' => 'Abertura e fecho de turno, movimentos e espécie esperada.'],
        ['titulo' => 'Clientes', 'texto' => 'Fichas, extratos, saldos e histórico comercial.'],
        ['titulo' => 'Fornecedores', 'texto' => 'Cadastro ligado a compras, stock e operações.'],
        ['titulo' => 'Serviços', 'texto' => 'Catálogo de serviços com preços e impostos.'],
        ['titulo' => 'Relatórios', 'texto' => 'Vendas, stock, clientes, finanças e actividade.'],
        ['titulo' => 'Permissões', 'texto' => 'Papéis por módulo com controlo fino de acesso.'],
        ['titulo' => 'Multiutilizador', 'texto' => 'Convites, cargos e equipa a trabalhar em simultâneo.'],
        ['titulo' => 'Multiempresa', 'texto' => 'Várias empresas no mesmo ecossistema, dados isolados.'],
        ['titulo' => 'WhatsApp', 'texto' => 'Comunicação e alertas alinhados à operação.'],
        ['titulo' => 'Email', 'texto' => 'Envio de documentos e notificações por SMTP ou OAuth.'],
        ['titulo' => 'Código de Barras', 'texto' => 'Leitura e identificação rápida de artigos.'],
        ['titulo' => 'IVA', 'texto' => 'Cálculo e configuração de impostos nas vendas.'],
        ['titulo' => 'Facturação recorrente', 'texto' => 'Facturas automáticas, lembretes e cobranças.'],
        ['titulo' => 'API', 'texto' => 'Integrações e extensões para o seu ecossistema.'],
        ['titulo' => 'Exportação PDF', 'texto' => 'Documentos e relatórios prontos a imprimir ou enviar.'],
        ['titulo' => 'Gestão de Stock', 'texto' => 'Movimentação completa e alertas de stock baixo.'],
        ['titulo' => 'Tesouraria', 'texto' => 'Controlo de entradas, saídas e meios de pagamento.'],
        ['titulo' => 'Campanhas', 'texto' => 'Promoções, descontos e regras comerciais.'],
    ],

    /** Benefícios */
    'beneficios' => [
        ['titulo' => 'Interface moderna', 'texto' => 'UI limpa, rápida de aprender e agradável de usar no dia-a-dia.'],
        ['titulo' => 'Sistema rápido', 'texto' => 'Navegação fluida e respostas imediatas onde importa.'],
        ['titulo' => 'Cloud', 'texto' => 'Aceda no browser, sem instalar servidores locais.'],
        ['titulo' => 'Seguro', 'texto' => 'HTTPS, permissões por papel e isolamento por empresa.'],
        ['titulo' => 'Backups automáticos', 'texto' => 'Protecção contínua dos dados da sua operação.'],
        ['titulo' => 'Escalável', 'texto' => 'Cresça utilizadores, filiais e volume sem mudar de ferramenta.'],
        ['titulo' => 'Atualizações constantes', 'texto' => 'Novas melhorias chegam à cloud sem interrupções.'],
        ['titulo' => 'Acesso em qualquer lugar', 'texto' => 'Escritório, loja ou remoto: o mesmo sistema.'],
    ],
];

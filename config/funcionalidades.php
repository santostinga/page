<?php
/**
 * SizoTech ERP — funcionalidades da área tenant (empresa).
 * Estrutura alinhada ao menu e rotas da aplicação.
 */
return [
    'lead' => [
        'titulo' => 'Funcionalidades da área empresa',
        'texto' => 'Lista derivada das rotas da app, menu lateral e relatórios (área tenant). Exclui administração da plataforma. Alguns ecrãs dependem de permissões e do plano da empresa.',
    ],
    'pilares' => [
        [
            'titulo' => 'Acesso, shell e dashboard',
            'subtitulo' => 'Tenant isolado, navegação e painel inicial',
            'art' => 'shell',
            'gradient' => 'from-slate-700 via-blue-700 to-indigo-800',
            'itens' => [
                'Login por subdomínio — contexto isolado por empresa.',
                'Shell com barra lateral recolhível, cabeçalho e área principal.',
                'Navegação SPA (mudança de vista sem recarregar o documento), título e menu activo.',
                'Modo claro / escuro com tema persistente.',
                'Sessão com aviso de inactividade e overlay de expiração.',
                'Logótipo na lateral; onboarding dispensável; notificações com contador e marcar como lida.',
                'Dashboard inicial com métricas e gráficos (ex.: receitas vs despesas, estado dos documentos).',
            ],
        ],
        [
            'titulo' => 'Perfil do utilizador',
            'subtitulo' => 'Conta pessoal',
            'art' => 'profile',
            'gradient' => 'from-violet-600 to-purple-700',
            'itens' => [
                'Ver e editar dados pessoais e nome de exibição.',
                'Alterar palavra-passe.',
                'Avatar com upload e remoção.',
                'Auditoria ao actualizar o perfil quando aplicável.',
            ],
        ],
        [
            'titulo' => 'Utilizadores e permissões',
            'subtitulo' => 'Equipa e papéis',
            'art' => 'team',
            'gradient' => 'from-indigo-600 to-blue-800',
            'itens' => [
                'Utilizadores: listagem paginada, criar (convite por e-mail), editar, activar/desactivar, eliminar, reenviar convite.',
                'Gestão de avatares conforme permissão.',
                'Papéis: sistema e personalizados; criar, editar e eliminar (personalizados); atribuir permissões por módulo (.view / .manage).',
                'Interface de selecção de permissões com agrupamentos.',
            ],
        ],
        [
            'titulo' => 'Catálogo: categorias, marcas, produtos e serviços',
            'subtitulo' => 'Sujeito a plano e permissões',
            'art' => 'catalog',
            'gradient' => 'from-emerald-600 to-teal-700',
            'itens' => [
                'Categorias e marcas: listagens, criar/editar, activar/desactivar; eliminação de marca restrita a perfis elevados.',
                'Produtos: listagem com filtros, dados completos (IVA, preços…), variantes (criar, editar, defeito, eliminar), activação.',
                'Serviços: listagem e gestão (plano pago + permissão).',
            ],
        ],
        [
            'titulo' => 'Pessoas / contactos',
            'subtitulo' => 'Clientes, fornecedores e perfis',
            'art' => 'people',
            'gradient' => 'from-amber-600 to-orange-700',
            'itens' => [
                'Fichas de pessoas com tipos definidos no sistema.',
                'Criar, editar, activar/desactivar; eliminação com regras por perfil.',
                'Extrato e movimentos por cliente ligados à facturação.',
            ],
        ],
        [
            'titulo' => 'Facturação, venda rápida e levantamentos',
            'subtitulo' => 'Documentos comerciais e operações',
            'art' => 'billing',
            'gradient' => 'from-blue-700 to-slate-900',
            'itens' => [
                'Documentos por tipo (orçamento, factura, venda, recibo, ND…): listagem, criar com linhas, impostos e descontos.',
                'Detalhe, PDF / impressão, registo de pagamentos, reembolso / ND quando aplicável.',
                'Sugestões na criação de facturas; levantamento após pagamento quando activo; termos em orçamentos.',
                'Venda rápida: ecrã dedicado (permissão + plano + definição da empresa).',
                'Levantamentos (pickups): fluxo de entrega quando «levantamento após pagamento» e permissões aplicáveis.',
            ],
        ],
        [
            'titulo' => 'Despesas e inventário',
            'subtitulo' => 'Custos e stock',
            'art' => 'finance',
            'gradient' => 'from-teal-700 to-emerald-900',
            'itens' => [
                'Despesas por categoria: criar, editar, eliminar/restaurar (soft delete), categorias próprias.',
                'Inventário: saldos e movimentos por produto; registar entrada, saída e ajuste conforme UI e plano.',
            ],
        ],
        [
            'titulo' => 'Relatórios, exportações e auditoria',
            'subtitulo' => 'Inteligência e dados',
            'art' => 'reports',
            'gradient' => 'from-rose-600 to-pink-800',
            'itens' => [
                'Hub de relatórios por cartões (filtrados por plano e módulos).',
                'Exemplos: resumo geral; catálogo; stock actual e stock baixo; movimentação de stock; vendas por período, produto ou cliente; lucro/margem; pagamentos recebidos e pendentes; serviços realizados; dívidas; extrato de cliente; comparativo entre períodos; despesas por categoria; export CSV de vendas; registo de actividade (admin).',
                'Rotas de exportação e catálogo (JSON, PDF, envio por e-mail).',
                'Listagem de auditoria para perfis autorizados.',
            ],
        ],
        [
            'titulo' => 'Definições da empresa e guia',
            'subtitulo' => 'Configuração e ajuda',
            'art' => 'settings',
            'gradient' => 'from-slate-600 to-slate-900',
            'itens' => [
                'Definições: dados da empresa, IVA, numeração, e-mail, levantamento, venda rápida, termos em orçamentos, logótipo.',
                'Guia / ajuda interna (permissão help.view).',
            ],
        ],
    ],
    'nota_transversal' => 'Restrições transversais: permissões por papel (ex.: modulo.view / modulo.manage); plano de subscrição pode ocultar categorias, marcas, produtos, serviços ou relatórios; papel de operador de levantamento só quando a empresa activa levantamento após pagamento.',
];

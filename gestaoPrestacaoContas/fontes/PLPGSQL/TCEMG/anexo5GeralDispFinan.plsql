/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
/*
* Script de função PLPGSQL
*
* Titulo do arquivo RGF Anexo 5 - Tabela de Dados Gerais
* Data de Criação : 09/06/2008 


* @author Analista Gelson
* @author Desenvolvedor eduardoschitz 

* @package URBEM
* @subpackage 

$Id:$

*/

CREATE OR REPLACE FUNCTION tcemg.fn_rgf_anexo5_geral_disp_finan (  varchar, varchar , varchar , varchar , varchar ) RETURNS RECORD AS $$ 
DECLARE
    stExercicio alias for $1;
    dtInicial   alias for $2;
    dtFim       alias for $3;
    stEntidades alias for $4;
    stRPPS      alias for $5;
    reRegistro RECORD;
    stSql   varchar := '';
BEGIN

stSql := '
SELECT 
      stn.pl_saldo_contas (   ''' || stExercicio || '''
                            , ''' || dtInicial || '''
                            , ''' || dtFim || '''
                            , '' plano_conta.cod_estrutural like ''''1.1.1.1.1%''''''
                            , ''' || stEntidades || '''
                            , ''' || stRPPS || '''
    ) as caixa 
';

IF stRPPS = 'true' THEN
    stSql := stSql || '

        , stn.pl_saldo_contas (   ''' || stExercicio || '''
                                    , ''' || dtInicial || '''
                                    , ''' || dtFim || '''
                                    , '' plano_conta.cod_estrutural like ''''1.1.1.1.1.06'''' AND recurso_direto.tipo = ''''L'''' ''
                                    , ''' || stEntidades || '''
                                    , ''' || stRPPS || '''
        ) as conta_movimento 
    
        , stn.pl_saldo_contas (   ''' || stExercicio || '''
                                , ''' || dtInicial || '''
                                , ''' || dtFim || '''
                                , '' plano_conta.cod_estrutural like ''''1.1.1.1.1.06'''' AND recurso_direto.tipo = ''''V'''' ''
                                , ''' || stEntidades || '''
                                , ''' || stRPPS || '''
        ) as contas_vinculadas
    ';
ELSE 
    stSql := stSql || '
    
        , stn.pl_saldo_contas (   ''' || stExercicio || '''
                                , ''' || dtInicial || '''
                                , ''' || dtFim || '''
                                , '' plano_conta.cod_estrutural like ''''1.1.1.1.1.19%'''' AND recurso_direto.tipo = ''''L'''' ''
                                , ''' || stEntidades || '''
                                , ''' || stRPPS || '''
        ) as conta_movimento 
    
        , stn.pl_saldo_contas (   ''' || stExercicio || '''
                                , ''' || dtInicial || '''
                                , ''' || dtFim || '''
                                , '' plano_conta.cod_estrutural like ''''1.1.1.1.1.19%'''' AND recurso_direto.tipo = ''''V'''' ''
                                , ''' || stEntidades || '''
                                , ''' || stRPPS || '''
        ) as contas_vinculadas
    ';
END IF;

stSql := stSql || '
    , (
        SELECT
                (
                    SELECT stn.pl_saldo_contas (  ''' || stExercicio || ''',
                                                  ''' || dtInicial || ''',
                                                  ''' || dtFim || ''',
                                                  '' plano_conta.cod_estrutural like ''''1.1.1.1.1.50%'''' '',
                                                  ''' || stEntidades || ''',
                                                  ''' || stRPPS || '''
                                                )
                )
                +
                (
                    SELECT stn.pl_saldo_contas (  ''' || stExercicio || ''',
                                                  ''' || dtInicial || ''',
                                                  ''' || dtFim || ''',
                                                  '' plano_conta.cod_estrutural like ''''1.1.4%'''' '',
                                                  ''' || stEntidades || ''',
                                                  ''' || stRPPS || '''
                                                )
                )
    ) as aplicacoes_financeiras


    , stn.pl_saldo_contas (   ''' || stExercicio || '''
                            , ''' || dtInicial || '''
                            , ''' || dtFim || '''
                            , '' plano_conta.cod_estrutural like ''''1.9.3.2.9%'''' ''
                            , ''' || stEntidades || '''
                            , ''' || stRPPS || '''
    ) as outras_disponibilidades_financeiras

    , stn.pl_saldo_contas (   ''' || stExercicio || '''
                            , ''' || dtInicial || '''
                            , ''' || dtFim || '''
                            , '' plano_conta.cod_estrutural like ''''2.1.1%'''' ''
                            , ''' || stEntidades || '''
                            , ''' || stRPPS || '''
    ) as depositos

    , stn.pl_saldo_contas (   ''' || stExercicio || '''
                            , ''' || dtInicial || '''
                            , ''' || dtFim || '''
                            , '' ( plano_conta.cod_estrutural like ''''2.1.2.1.1.01%'''' 
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.1.03.01%''''
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.2.01%'''' 
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.2.03.01%''''
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.3.01.00.01%'''' 
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.3.02.00.01%'''' 
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.3.03.00.01%'''' 
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.3.04.00.01%'''' 
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.3.16.00.01%'''' 
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.3.99.01.01%'''' 
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.3.99.02.01%'''' 
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.3.99.03.01%'''' 
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.5.09.00.01%'''' 
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.7.05.01.01%'''' 
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.7.05.02.01%'''' 
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.7.05.03.01%'''' 
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.9.08.01%'''' 
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.9.08.03%'''' 
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.9.99.00.01%'''' 
                            )''
                            , ''' || stEntidades || '''
                            , ''' || stRPPS || '''
    ) as rpp_exercicio

    , stn.pl_saldo_contas (   ''' || stExercicio || '''
                            , ''' || dtInicial || '''
                            , ''' || dtFim || '''
                            , '' ( plano_conta.cod_estrutural like ''''2.1.2.1.1.02%'''' 
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.1.03.02%'''' 
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.2.02%'''' 
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.2.03.02%''''
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.3.01.00.02%'''' 
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.3.01.00.03%'''' 
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.3.01.00.04%'''' 
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.3.02.00.02%'''' 
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.3.03.00.02%'''' 
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.3.03.00.03%'''' 
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.3.03.00.04%'''' 
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.3.04.00.02%'''' 
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.3.04.00.03%'''' 
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.3.04.00.04%'''' 
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.3.16.00.02%'''' 
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.3.99.01.02%'''' 
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.3.99.02.02%'''' 
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.3.99.03.02%'''' 
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.5.09.00.02%''''
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.5.09.00.03%''''
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.5.09.00.04%''''
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.7.04%''''
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.7.05.01.02%''''
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.7.05.01.03%''''
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.7.05.01.04%''''
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.7.05.02.02%''''
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.7.05.02.03%''''
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.7.05.02.04%''''
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.7.05.03.02%''''
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.7.05.03.03%''''
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.7.05.03.04%''''
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.9.08.02%''''
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.9.08.04%''''
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.9.99.00.02%''''
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.9.99.00.03%''''
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.9.99.00.04%''''
                                OR plano_conta.cod_estrutural like ''''2.1.2.1.9.99.00.20%''''
                            )''
                            , ''' || stEntidades || '''
                            , ''' || stRPPS || '''
    ) as rpp_exercicios_anteriores
    ';

    IF stRPPS = 'true' THEN
        stSql := stSql || '
            , (
                SELECT (
                        COALESCE(SUM(liquidados_nao_pagos_exercicios_anteriores),0.00) +
                        COALESCE(SUM(liquidados_nao_pagos),0.00) +
                        COALESCE(SUM(empenhados_nao_liquidados_exercicios_anteriores),0.00) +
                        COALESCE(SUM(obrigacoes_financeiras),0.00)
                       ) AS valor
                  FROM (
                        SELECT
                                sum(tb.total_processados_exercicios_anteriores) + sum(tb.total_processados_exercicio_anterior) AS liquidados_nao_pagos_exercicios_anteriores,
                                sum(tb.liquidados_nao_pagos) AS liquidados_nao_pagos,
                                sum(tb.total_nao_processados_exercicios_anteriores) + sum(tb.total_nao_processados_exercicio_anterior) AS empenhados_nao_liquidados_exercicios_anteriores,
                                sum(tb2.consignacoes) AS obrigacoes_financeiras
                          FROM (
                                SELECT 
                                        cod_recurso
                                      , entidade
                                      , tipo
                                      , SUM(total_processados_exercicios_anteriores) AS total_processados_exercicios_anteriores
                                      , SUM(total_processados_exercicio_anterior) AS total_processados_exercicio_anterior
                                      , SUM(total_nao_processados_exercicios_anteriores) AS total_nao_processados_exercicios_anteriores
                                      , SUM(total_nao_processados_exercicio_anterior) AS total_nao_processados_exercicio_anterior
                                      , SUM(liquidados_nao_pagos) AS liquidados_nao_pagos
                                      , SUM(empenhados_nao_liquidados) AS empenhados_nao_liquidados
                                      , SUM(empenhados_nao_liquidados_cancelados) AS empenhados_nao_liquidados_cancelados
                                      , SUM(caixa_liquida) AS caixa_liquida
                                 FROM stn.fn_rgf_anexo6novo_recurso('''|| stExercicio ||''',''' || stEntidades || ''',''' || dtFim || ''') AS stn_fn
                                        (  cod_recurso                                  integer
                                         , tipo                                         varchar
                                         , entidade                                     integer
                                         , total_processados_exercicios_anteriores      numeric
                                         , total_processados_exercicio_anterior         numeric
                                         , total_nao_processados_exercicios_anteriores  numeric
                                         , total_nao_processados_exercicio_anterior     numeric
                                         , liquidados_nao_pagos                         numeric
                                         , empenhados_nao_liquidados                    numeric
                                         , empenhados_nao_liquidados_cancelados         numeric
                                         , caixa_liquida                                numeric )
                                  GROUP BY cod_recurso, entidade, tipo
                                ) AS tb
                     INNER JOIN orcamento.recurso_direto AS rd
                             ON rd.cod_recurso = tb.cod_recurso
                            AND rd.exercicio = '''|| stExercicio ||'''
                     INNER JOIN stn.pl_recurso_descricao('''|| stExercicio ||''',''' || dtInicial || ''',''' || dtFim || ''','' '',''' || stEntidades || ''',''true'') AS tb2
                                    ( tipo_recurso                  char(1)
                                     , cod_recurso                  integer
                                     , exercicio                    varchar
                                     , nom_recurso                  varchar
                                     , positivo                     numeric
                                     , negativo                     numeric
                                     , saldo                        numeric
                                     , a_pagar_exercicio            numeric
                                     , a_pagar_exercicio_anteriores numeric
                                     , valor_consignacao_positivo   numeric
                                     , valor_consignacao_negativo   numeric
                                     , consignacoes                 numeric
                                     , caixa                        numeric       
                                    )
                             ON tb2.cod_recurso = tb.cod_recurso
                            AND tb2.exercicio = '''|| stExercicio ||'''
                          WHERE rd.tipo = ''V''
                          GROUP BY  tb.cod_recurso, tb.tipo
                    ) AS retorno
            ) AS obrigacoes_financeiras
        ';
    ELSE
        stSql := stSql || '
    , (
        SELECT (
                COALESCE(SUM(liquidados_nao_pagos_exercicios_anteriores),0.00) +
                COALESCE(SUM(liquidados_nao_pagos),0.00) +
                COALESCE(SUM(empenhados_nao_liquidados_exercicios_anteriores),0.00) +
                COALESCE(SUM(obrigacoes_financeiras),0.00)
               ) AS valor

        FROM (
                SELECT sum(tb.total_processados_exercicios_anteriores) + sum(tb.total_processados_exercicio_anterior) AS liquidados_nao_pagos_exercicios_anteriores,
                       sum(tb.liquidados_nao_pagos) AS liquidados_nao_pagos,
                       sum(tb.total_nao_processados_exercicios_anteriores) + sum(tb.total_nao_processados_exercicio_anterior) AS empenhados_nao_liquidados_exercicios_anteriores,
                       sum(tb2.consignacoes) AS obrigacoes_financeiras
                  FROM (
                         SELECT 
                                cod_recurso
                                , entidade
                                , tipo
                                , SUM(total_processados_exercicios_anteriores) AS total_processados_exercicios_anteriores
                                , SUM(total_processados_exercicio_anterior) AS total_processados_exercicio_anterior
                                , SUM(total_nao_processados_exercicios_anteriores) AS total_nao_processados_exercicios_anteriores
                                , SUM(total_nao_processados_exercicio_anterior) AS total_nao_processados_exercicio_anterior
                                , SUM(liquidados_nao_pagos) AS liquidados_nao_pagos
                                , SUM(empenhados_nao_liquidados) AS empenhados_nao_liquidados
                                , SUM(empenhados_nao_liquidados_cancelados) AS empenhados_nao_liquidados_cancelados
                                , SUM(caixa_liquida) AS caixa_liquida
                           FROM stn.fn_rgf_anexo6novo_recurso('''|| stExercicio ||''',''' || stEntidades || ''',''' || dtFim || ''') AS stn_fn
                                    (  cod_recurso             integer
                                     , tipo              varchar
                                     , entidade              integer
                                     , total_processados_exercicios_anteriores    numeric
                                     , total_processados_exercicio_anterior       numeric
                                     , total_nao_processados_exercicios_anteriores numeric
                                     , total_nao_processados_exercicio_anterior    numeric
                                     , liquidados_nao_pagos           numeric
                                     , empenhados_nao_liquidados         numeric
                                     , empenhados_nao_liquidados_cancelados       numeric
                                     , caixa_liquida            numeric )
                                     GROUP BY cod_recurso
                                     , entidade
                                     , tipo
                        ) AS tb
             INNER JOIN orcamento.recurso_direto AS rd
                     ON rd.cod_recurso = tb.cod_recurso
                    AND rd.exercicio = '''|| stExercicio ||'''

             INNER JOIN stn.pl_recurso_descricao('''|| stExercicio ||''',''' || dtInicial || ''',''' || dtFim || ''','' '',''' || stEntidades || ''',''false'') AS tb2
                            (  tipo_recurso                 char(1)
                             , cod_recurso                  integer
                             , exercicio                    varchar
                             , nom_recurso                  varchar
                             , positivo                     numeric
                             , negativo                     numeric
                             , saldo                        numeric
                             , a_pagar_exercicio            numeric
                             , a_pagar_exercicio_anteriores numeric
                             , valor_consignacao_positivo   numeric
                             , valor_consignacao_negativo   numeric
                             , consignacoes                 numeric
                             , caixa                        numeric )
                     ON tb2.cod_recurso = tb.cod_recurso
                    AND tb2.exercicio   = '''|| stExercicio ||'''
                  WHERE rd.tipo = ''V''
                    AND tb.entidade NOT IN ((SELECT valor::integer FROM administracao.configuracao WHERE configuracao.parametro = ''cod_entidade_rpps'' AND configuracao.exercicio = '''|| stExercicio ||'''))
                  GROUP BY tb.cod_recurso, tb.tipo

            UNION

            SELECT sum(tb.total_processados_exercicios_anteriores) + sum(tb.total_processados_exercicio_anterior) AS liquidados_nao_pagos_exercicios_anteriores,
                   sum(tb.liquidados_nao_pagos) AS liquidados_nao_pagos,
                   sum(tb.total_nao_processados_exercicios_anteriores) + sum(tb.total_nao_processados_exercicio_anterior) AS empenhados_nao_liquidados_exercicios_anteriores,
                   sum(tb2.consignacoes) AS obrigacoes_financeiras

              FROM (
                    SELECT 
                            cod_recurso
                            , entidade
                            , tipo
                            , SUM(total_processados_exercicios_anteriores) AS total_processados_exercicios_anteriores
                            , SUM(total_processados_exercicio_anterior) AS total_processados_exercicio_anterior
                            , SUM(total_nao_processados_exercicios_anteriores) AS total_nao_processados_exercicios_anteriores
                            , SUM(total_nao_processados_exercicio_anterior) AS total_nao_processados_exercicio_anterior
                            , SUM(liquidados_nao_pagos) AS liquidados_nao_pagos
                            , SUM(empenhados_nao_liquidados) AS empenhados_nao_liquidados
                            , SUM(empenhados_nao_liquidados_cancelados) AS empenhados_nao_liquidados_cancelados
                            , SUM(caixa_liquida) AS caixa_liquida
                      FROM stn.fn_rgf_anexo6novo_recurso('''|| stExercicio ||''',''' || stEntidades || ''',''' || dtFim || ''') AS stn_fn
                                (  cod_recurso                                  integer
                                 , tipo                                         varchar
                                 , entidade                                     integer
                                 , total_processados_exercicios_anteriores      numeric
                                 , total_processados_exercicio_anterior         numeric
                                 , total_nao_processados_exercicios_anteriores  numeric
                                 , total_nao_processados_exercicio_anterior     numeric
                                 , liquidados_nao_pagos                         numeric
                                 , empenhados_nao_liquidados                    numeric
                                 , empenhados_nao_liquidados_cancelados         numeric
                                 , caixa_liquida                                numeric )
                      GROUP BY cod_recurso, entidade, tipo
                    ) AS tb

         INNER JOIN orcamento.recurso_direto AS rd
                 ON rd.cod_recurso = tb.cod_recurso
                AND rd.exercicio = '''|| stExercicio ||'''

         INNER JOIN stn.pl_recurso_descricao('''|| stExercicio ||''',''' || dtInicial || ''',''' || dtFim || ''','' '',''' || stEntidades || ''',''false'') AS tb2
                        (  tipo_recurso                 char(1)
                         , cod_recurso                  integer
                         , exercicio                    varchar
                         , nom_recurso                  varchar
                         , positivo                     numeric
                         , negativo                     numeric
                         , saldo                        numeric
                         , a_pagar_exercicio            numeric
                         , a_pagar_exercicio_anteriores numeric
                         , valor_consignacao_positivo   numeric
                         , valor_consignacao_negativo   numeric
                         , consignacoes                 numeric
                         , caixa                        numeric )
                  ON tb2.cod_recurso = tb.cod_recurso
                 AND tb2.exercicio   = '''|| stExercicio ||'''
                 AND tb2.tipo_recurso = ''L''
               WHERE rd.tipo = ''L''
                 AND tb.entidade NOT IN ((SELECT valor::integer FROM administracao.configuracao WHERE configuracao.parametro = ''cod_entidade_rpps'' AND configuracao.exercicio = '''|| stExercicio ||'''))
               GROUP BY tb.cod_recurso, tb.tipo
            ) AS retorno
    ) AS obrigacoes_financeiras
    ';
    END IF;

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN reRegistro;
    END LOOP;
    
end;
$$ LANGUAGE 'plpgsql';

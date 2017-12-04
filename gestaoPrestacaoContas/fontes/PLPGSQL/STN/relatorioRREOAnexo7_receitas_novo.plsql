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
/**
    * Script de função PLPGSQL - Relatório STN - Anexo7 - Receitas
    *
    * URBEM Soluções de Gestão Pública Ltda
    * www.urbem.cnm.org.br
    *
    * $Revision: 15947 $
    * $Name$
    * $Author: eduardoschitz $
    * $Date: 2008-05-20 16:15:54 -0300 (Ter, 20 Mai 2008) $
    *
    * $Id: $
    *
    * Casos de uso:
    * 
*/

--select * from stn.fn_rreo_anexo7('2006',1,'1,2');

CREATE OR REPLACE FUNCTION stn.fn_rreo_anexo7_receitas_novo(varchar,varchar,varchar,varchar) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio                     ALIAS FOR $1;
    stCodEntidades                  ALIAS FOR $2;
    dtInicio                        ALIAS FOR $3;
    dtFim                           ALIAS FOR $4;
    dtInicioExercicio               VARCHAR := '01/01/'||stExercicio;
    dtInicioExercicioAnterior       VARCHAR;
    dtInicioAnterior                VARCHAR;
    dtFinalAnterior                 VARCHAR;
    stExercicioAnterior             VARCHAR   := '';
    stSql                           VARCHAR   := '';
    stSQLsubgrupo                   VARCHAR   := '';
    stSQLaux                        VARCHAR   := '';
    stSQLaux2                       VARCHAR   := '';
    reRegistro                      RECORD;
    reReg                           RECORD;
    reRegsubgrupo                   RECORD;
    arDatas                         VARCHAR[];
    
    stCodEstruturalDedutora         VARCHAR := '';
    
    stExAtualMascRedDedutora        VARCHAR := '';
    stExAnteriorMascRedDedutora     VARCHAR := '';
    
    
BEGIN

    IF ( CAST(stExercicio AS INTEGER) < 2008 ) THEN
        stExAtualMascRedDedutora := '4.9';
        stExAnteriorMascRedDedutora := '4.9';
    ELSEIF ( CAST(stExercicio AS INTEGER) = 2008 ) THEN 
        stExAtualMascRedDedutora := '9.1';
        stExAnteriorMascRedDedutora := '4.9';
    ELSE
        stExAtualMascRedDedutora := '9.1';
        stExAnteriorMascRedDedutora := '9.1';
    END IF;
    
    stExercicioAnterior := cast((cast(stExercicio as integer) - 1) as varchar);
    
    dtInicioExercicioAnterior := '01/01/' || stExercicioAnterior;
    
    dtInicioAnterior := SUBSTRING(dtInicio,0,6) || stExercicioAnterior;
    dtFinalAnterior := SUBSTRING(dtFim,0,6) || stExercicioAnterior;

    stSql := '
    CREATE TEMPORARY TABLE tmp_valor AS (
        SELECT
              conta_receita.cod_estrutural as cod_estrutural
            , lote.dt_lote       as data
            , valor_lancamento.vl_lancamento   as valor
            , valor_lancamento.oid             as primeira
        FROM
            contabilidade.valor_lancamento    ,
            orcamento.conta_receita           ,
            orcamento.receita                 ,
            contabilidade.lancamento_receita  ,
            contabilidade.lancamento          ,
            contabilidade.lote                
        WHERE
              (receita.exercicio = ' || stExercicio  || '::VARCHAR OR
              receita.exercicio = ' || stExercicioAnterior || '::VARCHAR )
    ';  
            if ( stCodEntidades != '' ) then
               stSql := stSql || '  AND receita.cod_entidade    IN (' || stCodEntidades || ') ';
            end if;
            
    stSql := stSql || '
    
            AND conta_receita.cod_conta       = receita.cod_conta
            AND conta_receita.exercicio       = receita.exercicio
    
            -- join lancamento receita
            AND lancamento_receita.cod_receita      = receita.cod_receita
            AND lancamento_receita.exercicio        = receita.exercicio
            AND lancamento_receita.estorno          = true
            -- tipo de lancamento receita deve ser = A , de arrecadação
            AND lancamento_receita.tipo             = ''A''
    
            -- join nas tabelas lancamento_receita e lancamento
            AND lancamento.cod_lote        = lancamento_receita.cod_lote
            AND lancamento.sequencia       = lancamento_receita.sequencia
            AND lancamento.exercicio       = lancamento_receita.exercicio
            AND lancamento.cod_entidade    = lancamento_receita.cod_entidade
            AND lancamento.tipo            = lancamento_receita.tipo
    
            -- join nas tabelas lancamento e valor_lancamento
            AND valor_lancamento.exercicio        = lancamento.exercicio
            AND valor_lancamento.sequencia        = lancamento.sequencia
            AND valor_lancamento.cod_entidade     = lancamento.cod_entidade
            AND valor_lancamento.cod_lote         = lancamento.cod_lote
            AND valor_lancamento.tipo             = lancamento.tipo
            -- na tabela valor lancamento  tipo_valor deve ser credito
            AND valor_lancamento.tipo_valor       = ''D''
    
            AND lote.cod_lote       = lancamento.cod_lote
            AND lote.cod_entidade   = lancamento.cod_entidade
            AND lote.exercicio      = lancamento.exercicio
            AND lote.tipo           = lancamento.tipo
    
        UNION
    
        SELECT
              conta_receita.cod_estrutural as cod_estrutural
            , lote.dt_lote       as data
            , valor_lancamento.vl_lancamento   as valor
            , valor_lancamento.oid             as segunda
        FROM
            contabilidade.valor_lancamento     ,
            orcamento.conta_receita            ,
            orcamento.receita                  ,
            contabilidade.lancamento_receita   ,
            contabilidade.lancamento           ,
            contabilidade.lote                
    
        WHERE
               (receita.exercicio = ' || stExercicio  || '::VARCHAR OR
               receita.exercicio = ' || stExercicioAnterior || '::VARCHAR )
    ';
            if ( stCodEntidades != '' ) then
                stSql := stSql || ' AND receita.cod_entidade    IN(' || stCodEntidades || ') ';
            end if;
    
    stSql := stSql || '
    
            AND conta_receita.cod_conta       = receita.cod_conta
            AND conta_receita.exercicio       = receita.exercicio
    
            -- join lancamento receita
            AND lancamento_receita.cod_receita      = receita.cod_receita
            AND lancamento_receita.exercicio        = receita.exercicio
            AND lancamento_receita.estorno          = false
            -- tipo de lancamento receita deve ser = A , de arrecadação
            AND lancamento_receita.tipo             = ''A''
    
            -- join nas tabelas lancamento_receita e lancamento
            AND lancamento.cod_lote        = lancamento_receita.cod_lote
            AND lancamento.sequencia       = lancamento_receita.sequencia
            AND lancamento.exercicio       = lancamento_receita.exercicio
            AND lancamento.cod_entidade    = lancamento_receita.cod_entidade
            AND lancamento.tipo            = lancamento_receita.tipo
    
            -- join nas tabelas lancamento e valor_lancamento
            AND valor_lancamento.exercicio        = lancamento.exercicio
            AND valor_lancamento.sequencia        = lancamento.sequencia
            AND valor_lancamento.cod_entidade     = lancamento.cod_entidade
            AND valor_lancamento.cod_lote         = lancamento.cod_lote
            AND valor_lancamento.tipo             = lancamento.tipo
            -- na tabela valor lancamento  tipo_valor deve ser credito
            AND valor_lancamento.tipo_valor       = ''C''
    
            -- Data Inicial e Data Final, antes iguala codigo do lote
            AND lote.cod_lote       = lancamento.cod_lote
            AND lote.cod_entidade   = lancamento.cod_entidade
            AND lote.exercicio      = lancamento.exercicio
            AND lote.tipo           = lancamento.tipo )
    ';

    EXECUTE stSql;

    stSql := '
    CREATE TEMPORARY TABLE tmp_valores_birt AS (

    SELECT
        1 as ordem,
        1 as grupo,
        0 as subgrupo,
        0 as item,
        cast(''1.0.0.0.00.00.00.00.00'' as varchar) as cod_estrutural,
        1 as nivel,
        cast(''RECEITAS PRIMÁRIAS CORRENTES (I)'' as varchar) as nom_conta,
        0.00 as previsao_atualizada,
        0.00 as no_bimestre,
        0.00 as ate_bimestre,
        0.00 as ate_bimestre_exercicio_anterior

    UNION

    SELECT
        2 as ordem,
        1 as grupo,
        1 as subgrupo,
        0 as item,
        cast(''1.1.0.0.00.00.00.00.00'' as varchar) as cod_estrutural,
        2 as nivel,
        cast(''Receitas Tributárias'' as varchar) as nom_conta,
        cast(sum(coalesce(previsao_inicial,0.00)) as numeric(14,2)) as previsao_atualizada,
        cast(sum(coalesce(no_bimestre,0.00))*-1 as numeric(14,2)) as no_bimestre,
        cast(sum(coalesce(ate_bimestre,0.00))*-1 as numeric(14,2)) as ate_bimestre,
        cast(sum(coalesce(ate_bimestre_exercicio_anterior,0.00))*-1 as numeric(14,2)) as ate_bimestre_exercicio_anterior
    FROM(
        SELECT
            publico.fn_nivel(conta_receita.cod_estrutural) as nivel,
            conta_receita.cod_estrutural,
            conta_receita.descricao,
            orcamento.fn_receita_valor_previsto( ''' || stExercicio || '''
                                                ,publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                ,''' || stCodEntidades || '''
            ) as previsao_inicial,
            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                     ,''' || dtInicio || '''
                                                     ,''' || dtFim || '''
            ) as no_bimestre,
            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                     ,''' || dtInicioExercicio || '''
                                                     ,''' || dtFim || '''
            ) as ate_bimestre,
            -- orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
            --                                          ,''' || dtInicioExercicioAnterior || '''
            --                                          ,''' || dtFinalAnterior || '''
            -- ) as ate_bimestre_exercicio_anterior
            orcamento.fn_somatorio_balancete_receita_exercicio_menor_2012( conta_receita.cod_estrutural
                                                         ,''' || dtInicioExercicioAnterior || '''
                                                         ,''' || dtFinalAnterior || '''
                                                         ,''' || stExercicioAnterior || '''
            ) as ate_bimestre_exercicio_anterior
        FROM
            --contabilidade.plano_conta   ,
            orcamento.conta_receita     
        WHERE
            -- Ligação entre contabilidade.plano_conta e orcamento.conta_receita
            --plano_conta.cod_estrutural   = ''4.''||conta_receita.cod_estrutural  AND
            --plano_conta.exercicio        = conta_receita.exercicio             
            -- Filtros
                conta_receita.cod_estrutural = ''1.1.0.0.00.00.00.00.00''  
            AND publico.fn_nivel(conta_receita.cod_estrutural) = 2
            AND conta_receita.exercicio = ''' || stExercicio || '''
    
        ORDER BY
            conta_receita.cod_estrutural
    ) as tbl

    UNION

    SELECT
        3 as ordem,
        1 as grupo,
        2 as subgrupo,
        0 as item,
        cast(''1.2.0.0.00.00.00.00.00'' as varchar) as cod_estrutural,
        2 as nivel,
        cast(''Receitas de Contribuições'' as varchar) as nom_conta,
        0.00 as previsao_atualizada,
        0.00 as no_bimestre,
        0.00 as ate_bimestre,
        0.00 as ate_bimestre_exercicio_anterior

    UNION

    SELECT
        4 as ordem,
        1 as grupo,
        2 as subgrupo,
        1 as item,
        cast(''1.2.1.0.00.00.00.00.00'' as varchar) as cod_estrutural,
        3 as nivel,
        cast(''Receitas Previdenciárias'' as varchar) as nom_conta,
        cast(sum(coalesce(previsao_inicial,0.00)) as numeric(14,2)) as previsao_atualizada,
        cast(sum(coalesce(no_bimestre,0.00))*-1 as numeric(14,2)) as no_bimestre,
        cast(sum(coalesce(ate_bimestre,0.00))*-1 as numeric(14,2)) as ate_bimestre,
        cast(sum(coalesce(ate_bimestre_exercicio_anterior,0.00))*-1 as numeric(14,2)) as ate_bimestre_exercicio_anterior
    FROM(
        SELECT
            publico.fn_nivel(conta_receita.cod_estrutural) as nivel,
            conta_receita.cod_estrutural,
            conta_receita.descricao,
            orcamento.fn_receita_valor_previsto( ''' || stExercicio || '''
                                                ,publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                ,''' || stCodEntidades || '''
            ) as previsao_inicial,
            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                     ,''' || dtInicio || '''
                                                     ,''' || dtFim || '''
            ) as no_bimestre,
            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                     ,''' || dtInicioExercicio || '''
                                                     ,''' || dtFim || '''
            ) as ate_bimestre,
            -- orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
            --                                          ,''' || dtInicioExercicioAnterior || '''
            --                                          ,''' || dtFinalAnterior || '''
            -- ) as ate_bimestre_exercicio_anterior
            orcamento.fn_somatorio_balancete_receita_exercicio_menor_2012( conta_receita.cod_estrutural
                                                         ,''' || dtInicioExercicioAnterior || '''
                                                         ,''' || dtFinalAnterior || '''
                                                         ,''' || stExercicioAnterior || '''
            ) as ate_bimestre_exercicio_anterior
        FROM
            --contabilidade.plano_conta   as plano_conta,
            orcamento.conta_receita     
        WHERE
            -- Ligação entre contabilidade.plano_conta e orcamento.conta_receita
            --plano_conta.cod_estrutural   = ''4.''||conta_receita.cod_estrutural  AND
            --plano_conta.exercicio        = conta_receita.exercicio             
            -- Filtros
                (   conta_receita.cod_estrutural = ''1.2.1.0.01.00.00.00.00''
                OR  conta_receita.cod_estrutural = ''1.2.1.0.29.00.00.00.00''
            )
            AND publico.fn_nivel(conta_receita.cod_estrutural) = 5
            AND conta_receita.exercicio = ''' || stExercicio || '''
    
        ORDER BY
            conta_receita.cod_estrutural
    ) as tbl    

    UNION

    SELECT
        5 as ordem,
        1 as grupo,
        2 as subgrupo,
        2 as item,
        cast(''1.2.2.0.00.00.00.00.00'' as varchar) as cod_estrutural,
        3 as nivel,
        cast(''Outras Receitas de Contribuições'' as varchar) as nom_conta,
        cast(sum(coalesce(previsao_inicial,0.00)) as numeric(14,2)) as previsao_atualizada,
        cast(sum(coalesce(no_bimestre,0.00))*-1 as numeric(14,2)) as no_bimestre,
        cast(sum(coalesce(ate_bimestre,0.00))*-1 as numeric(14,2)) as ate_bimestre,
        cast(sum(coalesce(ate_bimestre_exercicio_anterior,0.00))*-1 as numeric(14,2)) as ate_bimestre_exercicio_anterior
    FROM(
        SELECT
            publico.fn_nivel(conta_receita.cod_estrutural) as nivel,
            conta_receita.cod_estrutural,
            conta_receita.descricao,
            orcamento.fn_receita_valor_previsto( ''' || stExercicio || '''
                                                ,publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                ,''' || stCodEntidades || '''
            ) as previsao_inicial,
            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                     ,''' || dtInicio || '''
                                                     ,''' || dtFim || '''
            ) as no_bimestre,
            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                     ,''' || dtInicioExercicio || '''
                                                     ,''' || dtFim || '''
            ) as ate_bimestre,
            -- orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
            --                                          ,''' || dtInicioExercicioAnterior || '''
            --                                          ,''' || dtFinalAnterior || '''
            -- ) as ate_bimestre_exercicio_anterior
            orcamento.fn_somatorio_balancete_receita_exercicio_menor_2012( conta_receita.cod_estrutural
                                                         ,''' || dtInicioExercicioAnterior || '''
                                                         ,''' || dtFinalAnterior || '''
                                                         ,''' || stExercicioAnterior || '''
            ) as ate_bimestre_exercicio_anterior
        FROM
            --contabilidade.plano_conta   ,
            orcamento.conta_receita   
        WHERE
            -- Ligação entre contabilidade.plano_conta e orcamento.conta_receita
            --plano_conta.cod_estrutural   = ''4.''||conta_receita.cod_estrutural  AND
            --plano_conta.exercicio        = conta_receita.exercicio             
            -- Filtros
                (   conta_receita.cod_estrutural = ''1.2.2.0.00.00.00.00.00''
                OR  conta_receita.cod_estrutural = ''1.2.1.0.99.00.00.00.00''
                OR  conta_receita.cod_estrutural = ''7.2.0.0.00.00.00.00.00''
            )
            AND publico.fn_nivel(conta_receita.cod_estrutural) <= 5
            AND conta_receita.exercicio = ''' || stExercicio || '''
    
        ORDER BY
            conta_receita.cod_estrutural
    ) as tbl    

    UNION

    SELECT
        6 as ordem,
        1 as grupo,
        3 as subgrupo,
        0 as item,
        cast(''1.3.0.0.00.00.00.00.00'' as varchar) as cod_estrutural,
        2 as nivel,
        cast(''Receita Patrimonial Líquida'' as varchar) as nom_conta,
        0.00 as previsao_atualizada,
        0.00 as no_bimestre,
        0.00 as ate_bimestre,
        0.00 as ate_bimestre_exercicio_anterior

    UNION

    SELECT
        7 as ordem,
        1 as grupo,
        3 as subgrupo,
        1 as item,
        cast(''1.3.1.0.00.00.00.00.00'' as varchar) as cod_estrutural,
        3 as nivel,
        cast(''Receita Patrimonial'' as varchar) as nom_conta,
        cast(sum(coalesce(previsao_inicial,0.00)) as numeric(14,2)) as previsao_atualizada,
        cast(sum(coalesce(no_bimestre,0.00))*-1 as numeric(14,2)) as no_bimestre,
        cast(sum(coalesce(ate_bimestre,0.00))*-1 as numeric(14,2)) as ate_bimestre,
        cast(sum(coalesce(ate_bimestre_exercicio_anterior,0.00))*-1 as numeric(14,2)) as ate_bimestre_exercicio_anterior
    FROM(
        SELECT
            publico.fn_nivel(conta_receita.cod_estrutural) as nivel,
            conta_receita.cod_estrutural,
            conta_receita.descricao,
            orcamento.fn_receita_valor_previsto( ''' || stExercicio || '''
                                                ,publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                ,''' || stCodEntidades || '''
            ) as previsao_inicial,
            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                     ,''' || dtInicio || '''
                                                     ,''' || dtFim || '''
            ) as no_bimestre,
            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                     ,''' || dtInicioExercicio || '''
                                                     ,''' || dtFim || '''
            ) as ate_bimestre,
            -- orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
            --                                          ,''' || dtInicioExercicioAnterior || '''
            --                                          ,''' || dtFinalAnterior || '''
            -- ) as ate_bimestre_exercicio_anterior
            orcamento.fn_somatorio_balancete_receita_exercicio_menor_2012( conta_receita.cod_estrutural
                                                         ,''' || dtInicioExercicioAnterior || '''
                                                         ,''' || dtFinalAnterior || '''
                                                         ,''' || stExercicioAnterior || '''
            ) as ate_bimestre_exercicio_anterior
        FROM
            --contabilidade.plano_conta   ,
            orcamento.conta_receita  
        WHERE
            -- Ligação entre contabilidade.plano_conta e orcamento.conta_receita
            --plano_conta.cod_estrutural   = ''4.''||conta_receita.cod_estrutural  AND
            --plano_conta.exercicio        = conta_receita.exercicio             
            -- Filtros
                (   conta_receita.cod_estrutural = ''1.3.1.0.00.00.00.00.00''
                OR  conta_receita.cod_estrutural = ''1.3.2.0.00.00.00.00.00''
                OR  conta_receita.cod_estrutural = ''1.3.3.0.00.00.00.00.00''
                OR  conta_receita.cod_estrutural = ''7.3.0.0.00.00.00.00.00''
            )
            AND publico.fn_nivel(conta_receita.cod_estrutural) <= 3
            AND conta_receita.exercicio = ''' || stExercicio || '''
    
        ORDER BY
            conta_receita.cod_estrutural
    )  as tbl

    UNION 

    SELECT
        8 as ordem,
        1 as grupo,
        3 as subgrupo,
        2 as item,
        cast(''1.3.2.5.00.00.00.00.00'' as varchar) as cod_estrutural,
        3 as nivel,
        cast(''(-) Aplicações Financeiras'' as varchar) as nom_conta,
        cast(coalesce(sum(previsao_inicial),0.00) * -1 as numeric(14,2)) as previsao_atualizada,
        cast(coalesce(sum(no_bimestre),0.00) as numeric(14,2)) as no_bimestre,
        cast(coalesce(sum(ate_bimestre),0.00) as numeric(14,2)) as ate_bimestre,
        cast(coalesce(sum(ate_bimestre_exercicio_anterior),0.00) as numeric(14,2)) as ate_bimestre_exercicio_anterior
    FROM(
        SELECT
            publico.fn_nivel(conta_receita.cod_estrutural) as nivel,
            conta_receita.cod_estrutural,
            conta_receita.descricao,
            orcamento.fn_receita_valor_previsto( ''' || stExercicio || '''
                                                ,publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                ,''' || stCodEntidades || '''
            ) as previsao_inicial,
            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                     ,''' || dtInicio || '''
                                                     ,''' || dtFim || '''
            ) as no_bimestre,
            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                     ,''' || dtInicioExercicio || '''
                                                     ,''' || dtFim || '''
            ) as ate_bimestre,
            -- orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
            --                                          ,''' || dtInicioExercicioAnterior || '''
            --                                          ,''' || dtFinalAnterior || '''
            -- ) as ate_bimestre_exercicio_anterior
            orcamento.fn_somatorio_balancete_receita_exercicio_menor_2012( conta_receita.cod_estrutural
                                                         ,''' || dtInicioExercicioAnterior || '''
                                                         ,''' || dtFinalAnterior || '''
                                                         ,''' || stExercicioAnterior || '''
            ) as ate_bimestre_exercicio_anterior
        FROM
            --contabilidade.plano_conta   ,
            orcamento.conta_receita  
        WHERE
            -- Ligação entre contabilidade.plano_conta e orcamento.conta_receita
            --plano_conta.cod_estrutural   = ''4.''||conta_receita.cod_estrutural  AND
            --plano_conta.exercicio        = conta_receita.exercicio             
            -- Filtros
            -- AND plano_conta.cod_estrutural = ''4.1.3.4.0.00.00.00.00.00''
            -- 1.3.2.5.00.00.00.00.00
                conta_receita.cod_estrutural = ''1.3.2.5.00.00.00.00.00''
            AND publico.fn_nivel(conta_receita.cod_estrutural) = 4
            AND conta_receita.exercicio = ''' || stExercicio || '''
    
        ORDER BY
            conta_receita.cod_estrutural
    )  as tbl
 
    
    UNION 

    SELECT
        12 as ordem,
        1 as grupo,
        7 as subgrupo,
        0 as item,
        cast(''1.7.0.0.00.00.00.00.00'' as varchar) as cod_estrutural,
        2 as nivel,
        cast(''Transferências Correntes'' as varchar) as nom_conta,
        0.00 as previsao_atualizada,
        0.00 as no_bimestre,
        0.00 as ate_bimestre,
        0.00 as ate_bimestre_exercicio_anterior
        

    UNION 

    SELECT
        13 as ordem,
        1 as grupo,
        7 as subgrupo,
        1 as item,
        cast (''1.7.6.0.00.00.00.00.00'' as varchar) as cod_estrutural,
        3 as nivel,
        cast(''Convênios'' as varchar) as nom_conta,
        cast(sum(coalesce(previsao_inicial,0.00)) as numeric(14,2)) as previsao_atualizada,
        cast(sum(coalesce(no_bimestre,0.00))*-1 as numeric(14,2)) as no_bimestre,
        cast(sum(coalesce(ate_bimestre,0.00))*-1 as numeric(14,2)) as ate_bimestre,
        cast(sum(coalesce(ate_bimestre_exercicio_anterior,0.00))*-1 as numeric(14,2)) as ate_bimestre_exercicio_anterior
    FROM(
        SELECT
            publico.fn_nivel(conta_receita.cod_estrutural) as nivel,
            conta_receita.cod_estrutural,
            conta_receita.descricao,
            orcamento.fn_receita_valor_previsto( ''' || stExercicio || '''
                                                ,publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                ,''' || stCodEntidades || '''
            ) as previsao_inicial,
            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                     ,''' || dtInicio || '''
                                                     ,''' || dtFim || '''
            ) as no_bimestre,
            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                     ,''' || dtInicioExercicio || '''
                                                     ,''' || dtFim || '''
            ) as ate_bimestre,
            -- orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
            --                                          ,''' || dtInicioExercicioAnterior || '''
            --                                          ,''' || dtFinalAnterior || '''
            -- ) as ate_bimestre_exercicio_anterior
            orcamento.fn_somatorio_balancete_receita_exercicio_menor_2012( conta_receita.cod_estrutural
                                                         ,''' || dtInicioExercicioAnterior || '''
                                                         ,''' || dtFinalAnterior || '''
                                                         ,''' || stExercicioAnterior || '''
            ) as ate_bimestre_exercicio_anterior
        FROM
            --contabilidade.plano_conta   ,
            orcamento.conta_receita  
        WHERE
            -- Ligação entre contabilidade.plano_conta e orcamento.conta_receita
            --plano_conta.cod_estrutural   = ''4.''||conta_receita.cod_estrutural  AND
            --plano_conta.exercicio        = conta_receita.exercicio             
            -- Filtros
                conta_receita.cod_estrutural = ''1.7.6.0.00.00.00.00.00''
            AND publico.fn_nivel(conta_receita.cod_estrutural) = 3
            AND conta_receita.exercicio = ''' || stExercicio || '''
    
        ORDER BY
            conta_receita.cod_estrutural
    )  as tbl

    UNION 

    SELECT 
        ordem,
        grupo,
        subgrupo,
        item,
        cod_estrutural,
        nivel,
        nom_conta,
        cast(sum(previsao_atualizada) as numeric(14,2)) as previsao_atualizada,
        cast(sum(no_bimestre) as numeric(14,2)) as no_bimestre,
        cast(sum(ate_bimestre) as numeric(14,2)) as ate_bimestre,
        cast(sum(ate_bimestre_exercicio_anterior) as numeric(14,2)) as ate_bimestre_exercicio_anterior
    FROM(
        SELECT
            14 as ordem,
            1 as grupo,
            7 as subgrupo,
            2 as item,
            cast (''1.7.2.0.00.00.00.00.00'' as varchar) as cod_estrutural,
            3 as nivel,
            cast(''Outras Transferências Correntes'' as varchar) as nom_conta,
            cast(sum(coalesce(previsao_inicial,0.00)) as numeric(14,2)) as previsao_atualizada,
            cast(sum(coalesce(no_bimestre,0.00))*-1 as numeric(14,2)) as no_bimestre,
            cast(sum(coalesce(ate_bimestre,0.00))*-1 as numeric(14,2)) as ate_bimestre,
            cast(sum(coalesce(ate_bimestre_exercicio_anterior,0.00))*-1 as numeric(14,2)) as ate_bimestre_exercicio_anterior
        FROM(
            SELECT
                publico.fn_nivel(conta_receita.cod_estrutural) as nivel,
                conta_receita.cod_estrutural,
                conta_receita.descricao,
                orcamento.fn_receita_valor_previsto( ''' || stExercicio || '''
                                                    ,publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                    ,''' || stCodEntidades || '''
                ) as previsao_inicial,
                orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                         ,''' || dtInicio || '''
                                                         ,''' || dtFim || '''
                ) as no_bimestre,
                orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                         ,''' || dtInicioExercicio || '''
                                                         ,''' || dtFim || '''
                ) as ate_bimestre,
                -- orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                --                                          ,''' || dtInicioExercicioAnterior || '''
                --                                          ,''' || dtFinalAnterior || '''
                -- ) as ate_bimestre_exercicio_anterior
                orcamento.fn_somatorio_balancete_receita_exercicio_menor_2012( conta_receita.cod_estrutural
                                                         ,''' || dtInicioExercicioAnterior || '''
                                                         ,''' || dtFinalAnterior || '''
                                                         ,''' || stExercicioAnterior || '''
                ) as ate_bimestre_exercicio_anterior
            FROM
--                contabilidade.plano_conta   ,
                orcamento.conta_receita  
            WHERE
                -- Ligação entre contabilidade.plano_conta e orcamento.conta_receita
                --plano_conta.cod_estrutural   = ''4.''||conta_receita.cod_estrutural  AND 
                --plano_conta.exercicio        = conta_receita.exercicio             
                -- Filtros
                    (   conta_receita.cod_estrutural = ''1.7.2.0.00.00.00.00.00''
                    OR  conta_receita.cod_estrutural = ''1.7.3.0.00.00.00.00.00''
                    OR  conta_receita.cod_estrutural = ''1.7.4.0.00.00.00.00.00''
                    OR  conta_receita.cod_estrutural = ''1.7.5.0.00.00.00.00.00''
                --    OR  conta_receita.cod_estrutural = ''1.7.6.0.00.00.00.00.00''
                    ) 
                AND publico.fn_nivel(conta_receita.cod_estrutural) = 3
                AND conta_receita.exercicio = ''' || stExercicio || '''
        
            ORDER BY
                conta_receita.cod_estrutural
        )  as tbl

    UNION ALL
    
    -- --------------------------------------
    -- Contas Dedutoras
    -- --------------------------------------

    SELECT
        14 as ordem,
        1 as grupo,
        7 as subgrupo,
        2 as item,
        cast (''1.7.2.0.00.00.00.00.00'' as varchar) as cod_estrutural, 
        3 as nivel,
        cast(''Outras Transferências Correntes'' as varchar) as nom_conta, 
        cast(sum(coalesce(previsao_inicial,0.00)) as numeric(14,2)) as previsao_atualizada, 
        cast(sum(coalesce(no_bimestre,0.00)) as numeric(14,2))* -1 as no_bimestre_dedutora, 
        cast(sum(coalesce(ate_bimestre,0.00)) as numeric(14,2))* -1 as ate_bimestre_dedutora, 
        cast(sum(coalesce((
            SELECT
                orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural), ''' || dtInicioExercicioAnterior || ''' , ''' || dtFinalAnterior || ''' ) AS ate_bimestre_exercicio_anterior 
            FROM
                --contabilidade.plano_conta, 
                orcamento.conta_receita 
            WHERE conta_receita.cod_estrutural   like ''1.7.2%'' 
              --
              --AND plano_conta.exercicio = conta_receita.exercicio 
              AND publico.fn_nivel(conta_receita.cod_estrutural) = 2 
              AND conta_receita.exercicio = ''' || stExercicioAnterior || '''
                
            
            ORDER BY 
                conta_receita.cod_estrutural
        ),0.00))* -1 as numeric(14,2)) as ate_bimestre_exercicio_anterior_dedutora 
    FROM (
        SELECT
            publico.fn_nivel(conta_receita.cod_estrutural) as nivel, 
            conta_receita.cod_estrutural, 
            conta_receita.descricao, 
            orcamento.fn_receita_valor_previsto( ''' || stExercicio || ''' , publico.fn_mascarareduzida(conta_receita.cod_estrutural) , ''' || stCodEntidades || ''' ) AS previsao_inicial,
            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural) , ''' || dtInicio || ''' , ''' || dtFim || ''' ) AS no_bimestre, 
            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural) , ''' || dtInicioExercicio || ''' , ''' || dtFim || ''' ) AS ate_bimestre
        FROM
            --contabilidade.plano_conta, 
            orcamento.conta_receita 
        WHERE 
            /*-- Ligação entre contabilidade.plano_conta e orcamento.conta_receita
            plano_conta.cod_estrutural   = conta_receita.cod_estrutural  AND 
            plano_conta.exercicio        = conta_receita.exercicio             
            -- Filtros
            AND plano_conta.cod_estrutural like ''' || stExAtualMascRedDedutora || '%''''
            AND publico.fn_nivel(plano_conta.cod_estrutural) = 2
            AND plano_conta.exercicio = '' || stExercicio || ''*/
            
            
              conta_receita.cod_estrutural   like ''1.7.2%'' 
          
          AND publico.fn_nivel(conta_receita.cod_estrutural) = 2 
          AND conta_receita.exercicio = ''' || stExercicio || '''
            
        
        ORDER BY 
            conta_receita.cod_estrutural
    ) AS tbl 
) AS tabela 
GROUP BY
    ordem,
    grupo,
    subgrupo,
    item,
    cod_estrutural,
    nivel,
    nom_conta
    
    -- --------------------------------------
    -- Fim Contas Dedutoras
    -- --------------------------------------
    
';

-- stExAtualMascRedDedutora
-- stExAnteriorMascRedDedutora


stSql := stSql || '

UNION

    SELECT
        15 as ordem,
        1 as grupo,
        8 as subgrupo,
        0 as item,
        cast(''1.8.0.0.00.00.00.00.00'' as varchar) as cod_estrutural,
        2 as nivel,
        cast(''Demais Receitas Correntes'' as varchar) as nom_conta,
        0.00 as previsao_atualizada,
        0.00 as no_bimestre,
        0.00 as ate_bimestre,
        0.00 as ate_bimestre_exercicio_anterior

UNION

    SELECT
        16 as ordem,
        1 as grupo,
        8 as subgrupo,
        1 as item,
        cast(''1.8.1.0.00.00.00.00.00'' as varchar) as cod_estrutural,
        3 as nivel,
        cast(''Dívida Ativa'' as varchar) as nom_conta,
        cast(sum(coalesce(previsao_inicial,0.00)) as numeric(14,2)) as previsao_atualizada,
        cast(sum(coalesce(no_bimestre,0.00))*-1 as numeric(14,2)) as no_bimestre,
        cast(sum(coalesce(ate_bimestre,0.00))*-1 as numeric(14,2)) as ate_bimestre,
        cast(sum(coalesce(ate_bimestre_exercicio_anterior,0.00))*-1 as numeric(14,2)) as ate_bimestre_exercicio_anterior
    FROM(
        SELECT
            publico.fn_nivel(conta_receita.cod_estrutural) as nivel,
            conta_receita.cod_estrutural,
            conta_receita.descricao,
            orcamento.fn_receita_valor_previsto( ''' || stExercicio || '''
                                                ,publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                ,''' || stCodEntidades || '''
            ) as previsao_inicial,
            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                     ,''' || dtInicio || '''
                                                     ,''' || dtFim || '''
            ) as no_bimestre,
            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                     ,''' || dtInicioExercicio || '''
                                                     ,''' || dtFim || '''
            ) as ate_bimestre,
            -- orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
            --                                          ,''' || dtInicioExercicioAnterior || '''
            --                                          ,''' || dtFinalAnterior || '''
            -- ) as ate_bimestre_exercicio_anterior
            orcamento.fn_somatorio_balancete_receita_exercicio_menor_2012( conta_receita.cod_estrutural
                                                         ,''' || dtInicioExercicioAnterior || '''
                                                         ,''' || dtFinalAnterior || '''
                                                         ,''' || stExercicioAnterior || '''
            ) as ate_bimestre_exercicio_anterior
        FROM
            --contabilidade.plano_conta   ,
            orcamento.conta_receita  
        WHERE
            -- Ligação entre contabilidade.plano_conta e orcamento.conta_receita
            --plano_conta.cod_estrutural   = ''4.''||conta_receita.cod_estrutural AND 
            --plano_conta.exercicio        = conta_receita.exercicio 
            -- Filtros
                conta_receita.cod_estrutural = ''1.9.3.0.00.00.00.00.00''
            AND publico.fn_nivel(conta_receita.cod_estrutural) = 3 
            AND conta_receita.exercicio = ''' || stExercicio || ''' 
    
        ORDER BY
            conta_receita.cod_estrutural
    )  as tbl

UNION

    SELECT
        17 as ordem,
        1 as grupo,
        8 as subgrupo,
        2 as item,
        cast(''1.8.2.0.00.00.00.00.00'' as varchar) as cod_estrutural,
        3 as nivel,
        cast(''Diversas Receitas Correntes'' as varchar) as nom_conta,
        cast(sum(coalesce(previsao_inicial,0.00)) as numeric(14,2)) as previsao_atualizada,
        cast(sum(coalesce(no_bimestre,0.00))*-1 as numeric(14,2)) as no_bimestre,
        cast(sum(coalesce(ate_bimestre,0.00))*-1 as numeric(14,2)) as ate_bimestre,
        cast(sum(coalesce(ate_bimestre_exercicio_anterior,0.00))*-1 as numeric(14,2)) as ate_bimestre_exercicio_anterior
    FROM(
        SELECT
            publico.fn_nivel(conta_receita.cod_estrutural) as nivel,
            conta_receita.cod_estrutural,
            conta_receita.descricao,
            orcamento.fn_receita_valor_previsto( ''' || stExercicio || '''
                                                ,publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                ,''' || stCodEntidades || '''
            ) as previsao_inicial,
            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                     ,''' || dtInicio || '''
                                                     ,''' || dtFim || '''
            ) as no_bimestre,
            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                     ,''' || dtInicioExercicio || '''
                                                     ,''' || dtFim || '''
            ) as ate_bimestre,
            -- orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
            --                                          ,''' || dtInicioExercicioAnterior || '''
            --                                          ,''' || dtFinalAnterior || '''
            -- ) as ate_bimestre_exercicio_anterior
            orcamento.fn_somatorio_balancete_receita_exercicio_menor_2012( conta_receita.cod_estrutural
                                                         ,''' || dtInicioExercicioAnterior || '''
                                                         ,''' || dtFinalAnterior || '''
                                                         ,''' || stExercicioAnterior || '''
            ) as ate_bimestre_exercicio_anterior
        FROM
            --contabilidade.plano_conta   ,
            orcamento.conta_receita  
        WHERE
            -- Ligação entre contabilidade.plano_conta e orcamento.conta_receita
            --plano_conta.cod_estrutural   = ''4.''||conta_receita.cod_estrutural  AND
            --plano_conta.exercicio        = conta_receita.exercicio             
            -- Filtros
                (   conta_receita.cod_estrutural = ''1.9.1.0.00.00.00.00.00''
                OR  conta_receita.cod_estrutural = ''1.9.2.0.00.00.00.00.00''
                OR  conta_receita.cod_estrutural = ''1.9.9.0.00.00.00.00.00''
                OR  conta_receita.cod_estrutural = ''7.9.0.0.00.00.00.00.00''
            )
            AND publico.fn_nivel(conta_receita.cod_estrutural) <= 3
            AND conta_receita.exercicio = ''' || stExercicio || '''
    
        ORDER BY
            conta_receita.cod_estrutural
    )  as tbl

UNION

    SELECT
        18 as ordem,
        1 as grupo,
        9 as subgrupo,
        1 as item,
        cast (''1.6.0.0.00.00.00.00.00'' as varchar) as cod_estrutural,
        2 as nivel,
        cast(''Receitas de Serviços'' as varchar) as nom_conta,
        cast(sum(coalesce(previsao_inicial,0.00)) as numeric(14,2)) as previsao_atualizada,
        cast(sum(coalesce(no_bimestre,0.00))*-1 as numeric(14,2)) as no_bimestre,
        cast(sum(coalesce(ate_bimestre,0.00))*-1 as numeric(14,2)) as ate_bimestre,
        cast(sum(coalesce(ate_bimestre_exercicio_anterior,0.00))*-1 as numeric(14,2)) as ate_bimestre_exercicio_anterior
    FROM(
        SELECT
            publico.fn_nivel(conta_receita.cod_estrutural) as nivel,
            conta_receita.cod_estrutural,
            conta_receita.descricao,
            orcamento.fn_receita_valor_previsto( ''' || stExercicio || '''
                                                ,publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                ,''' || stCodEntidades || '''
            ) as previsao_inicial,
            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                     ,''' || dtInicio || '''
                                                     ,''' || dtFim || '''
            ) as no_bimestre,
            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                     ,''' || dtInicioExercicio || '''
                                                     ,''' || dtFim || '''
            ) as ate_bimestre,
            -- orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
            --                                          ,''' || dtInicioExercicioAnterior || '''
            --                                          ,''' || dtFinalAnterior || '''
            -- ) as ate_bimestre_exercicio_anterior
            orcamento.fn_somatorio_balancete_receita_exercicio_menor_2012( conta_receita.cod_estrutural
                                                         ,''' || dtInicioExercicioAnterior || '''
                                                         ,''' || dtFinalAnterior || '''
                                                         ,''' || stExercicioAnterior || '''
            ) as ate_bimestre_exercicio_anterior
        FROM
            --contabilidade.plano_conta   ,
            orcamento.conta_receita  
        WHERE
            -- Ligação entre contabilidade.plano_conta e orcamento.conta_receita
            --plano_conta.cod_estrutural   = ''4.''||conta_receita.cod_estrutural  AND
            --plano_conta.exercicio        = conta_receita.exercicio             
            -- Filtros
                conta_receita.cod_estrutural   like ''1.6%'' 
            AND publico.fn_nivel(conta_receita.cod_estrutural) = 2
            AND conta_receita.exercicio = ''' || stExercicio || '''
    
        ORDER BY
            conta_receita.cod_estrutural
    )  as tbl    
        

UNION

    SELECT
        19 as ordem,
        2 as grupo,
        0 as subgrupo,
        0 as item,
        cast(''2.0.0.0.00.00.00.00.00'' as varchar) as cod_estrutural,
        1 as nivel,
        cast(''RECEITAS DE CAPITAL (II)'' as varchar) as nom_conta,
        0.00 as previsao_atualizada,
        0.00 as no_bimestre,
        0.00 as ate_bimestre,
        0.00 as ate_bimestre_exercicio_anterior

UNION

    SELECT
        20 as ordem,
        2 as grupo,
        1 as subgrupo,
        0 as item,
        cast(''2.1.0.0.00.00.00.00.00'' as varchar) as cod_estrutural,
        2 as nivel,
        cast(''Operações de Crédito (III)'' as varchar) as nom_conta,
        cast(sum(coalesce(previsao_inicial,0.00)) as numeric(14,2)) as previsao_atualizada,
        cast(sum(coalesce(no_bimestre,0.00))*-1 as numeric(14,2)) as no_bimestre,
        cast(sum(coalesce(ate_bimestre,0.00))*-1 as numeric(14,2)) as ate_bimestre,
        cast(sum(coalesce(ate_bimestre_exercicio_anterior,0.00))*-1 as numeric(14,2)) as ate_bimestre_exercicio_anterior
    FROM(
        SELECT
            publico.fn_nivel(conta_receita.cod_estrutural) as nivel,
            conta_receita.cod_estrutural,
            conta_receita.descricao,
            orcamento.fn_receita_valor_previsto( ''' || stExercicio || '''
                                                ,publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                ,''' || stCodEntidades || '''
            ) as previsao_inicial,
            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                     ,''' || dtInicio || '''
                                                     ,''' || dtFim || '''
            ) as no_bimestre,
            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                     ,''' || dtInicioExercicio || '''
                                                     ,''' || dtFim || '''
            ) as ate_bimestre,
            -- orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
            --                                          ,''' || dtInicioExercicioAnterior || '''
            --                                          ,''' || dtFinalAnterior || '''
            -- ) as ate_bimestre_exercicio_anterior
            orcamento.fn_somatorio_balancete_receita_exercicio_menor_2012( conta_receita.cod_estrutural
                                                         ,''' || dtInicioExercicioAnterior || '''
                                                         ,''' || dtFinalAnterior || '''
                                                         ,''' || stExercicioAnterior || '''
            ) as ate_bimestre_exercicio_anterior
        FROM
            --contabilidade.plano_conta   ,
            orcamento.conta_receita     
        WHERE
            -- Ligação entre contabilidade.plano_conta e orcamento.conta_receita
            --plano_conta.cod_estrutural   = ''4.''||conta_receita.cod_estrutural  AND
            --plano_conta.exercicio        = conta_receita.exercicio
            -- Filtros
                (conta_receita.cod_estrutural = ''2.1.0.0.00.00.00.00.00''
            )
            AND publico.fn_nivel(conta_receita.cod_estrutural) = 2
            AND conta_receita.exercicio = ''' || stExercicio || '''

        ORDER BY
            conta_receita.cod_estrutural
    ) as tbl

UNION

    SELECT
        21 as ordem,
        2 as grupo,
        2 as subgrupo,
        0 as item,
        cast(''2.3.0.0.00.00.00.00.00'' as varchar) as cod_estrutural,
        2 as nivel,
        cast(''Amortização de Empréstimos (IV)'' as varchar) as nom_conta,
        cast(sum(coalesce(previsao_inicial,0.00)) as numeric(14,2)) as previsao_atualizada,
        cast(sum(coalesce(no_bimestre,0.00))*-1 as numeric(14,2)) as no_bimestre,
        cast(sum(coalesce(ate_bimestre,0.00))*-1 as numeric(14,2)) as ate_bimestre,
        cast(sum(coalesce(ate_bimestre_exercicio_anterior,0.00))*-1 as numeric(14,2)) as ate_bimestre_exercicio_anterior
    FROM(
        SELECT
            publico.fn_nivel(conta_receita.cod_estrutural) as nivel,
            conta_receita.cod_estrutural,
            conta_receita.descricao,
            orcamento.fn_receita_valor_previsto( ''' || stExercicio || '''
                                                ,publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                ,''' || stCodEntidades || '''
            ) as previsao_inicial,
            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                     ,''' || dtInicio || '''
                                                     ,''' || dtFim || '''
            ) as no_bimestre,
            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                     ,''' || dtInicioExercicio || '''
                                                     ,''' || dtFim || '''
            ) as ate_bimestre,
            -- orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
            --                                          ,''' || dtInicioExercicioAnterior || '''
            --                                          ,''' || dtFinalAnterior || '''
            -- ) as ate_bimestre_exercicio_anterior
            orcamento.fn_somatorio_balancete_receita_exercicio_menor_2012( conta_receita.cod_estrutural
                                                         ,''' || dtInicioExercicioAnterior || '''
                                                         ,''' || dtFinalAnterior || '''
                                                         ,''' || stExercicioAnterior || '''
            ) as ate_bimestre_exercicio_anterior
        FROM
            --contabilidade.plano_conta   ,
            orcamento.conta_receita   
        WHERE
            -- Ligação entre contabilidade.plano_conta e orcamento.conta_receita
            --plano_conta.cod_estrutural   = ''4.''||conta_receita.cod_estrutural  AND
            --plano_conta.exercicio        = conta_receita.exercicio             
            -- Filtros
                (  conta_receita.cod_estrutural = ''2.3.0.0.00.00.00.00.00''
                OR conta_receita.cod_estrutural = ''8.3.0.0.00.00.00.00.00''
            )
            AND publico.fn_nivel(conta_receita.cod_estrutural) = 2
            AND conta_receita.exercicio = ''' || stExercicio || '''
    
        ORDER BY
            conta_receita.cod_estrutural
    ) as tbl

UNION

    SELECT
        22 as ordem,
        2 as grupo,
        3 as subgrupo,
        0 as item,
        cast(''2.2.0.0.00.00.00.00.00'' as varchar) as cod_estrutural,
        2 as nivel,
        cast(''Alienação de Bens (V)'' as varchar) as nom_conta,
        cast(sum(coalesce(previsao_inicial,0.00)) as numeric(14,2)) as previsao_atualizada,
        cast(sum(coalesce(no_bimestre,0.00))*-1 as numeric(14,2)) as no_bimestre,
        cast(sum(coalesce(ate_bimestre,0.00))*-1 as numeric(14,2)) as ate_bimestre,
        cast(sum(coalesce(ate_bimestre_exercicio_anterior,0.00))*-1 as numeric(14,2)) as ate_bimestre_exercicio_anterior
    FROM(
        SELECT
            publico.fn_nivel(conta_receita.cod_estrutural) as nivel,
            conta_receita.cod_estrutural,
            conta_receita.descricao,
            orcamento.fn_receita_valor_previsto( ''' || stExercicio || '''
                                                ,publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                ,''' || stCodEntidades || '''
            ) as previsao_inicial,
            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                     ,''' || dtInicio || '''
                                                     ,''' || dtFim || '''
            ) as no_bimestre,
            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                     ,''' || dtInicioExercicio || '''
                                                     ,''' || dtFim || '''
            ) as ate_bimestre,
            -- orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
            --                                          ,''' || dtInicioExercicioAnterior || '''
            --                                          ,''' || dtFinalAnterior || '''
            -- ) as ate_bimestre_exercicio_anterior
            orcamento.fn_somatorio_balancete_receita_exercicio_menor_2012( conta_receita.cod_estrutural
                                                         ,''' || dtInicioExercicioAnterior || '''
                                                         ,''' || dtFinalAnterior || '''
                                                         ,''' || stExercicioAnterior || '''
            ) as ate_bimestre_exercicio_anterior
        FROM
            --contabilidade.plano_conta   as plano_conta,
            orcamento.conta_receita     
        WHERE
            -- Ligação entre contabilidade.plano_conta e orcamento.conta_receita
            --plano_conta.cod_estrutural   = ''4.''||conta_receita.cod_estrutural  AND
            --plano_conta.exercicio        = conta_receita.exercicio             
            -- Filtros
                (  conta_receita.cod_estrutural = ''2.2.0.0.00.00.00.00.00''
                OR conta_receita.cod_estrutural = ''8.2.0.0.00.00.00.00.00''
            )
            AND publico.fn_nivel(conta_receita.cod_estrutural) = 2        
            AND conta_receita.exercicio = ''' || stExercicio || '''
    
        ORDER BY
            conta_receita.cod_estrutural
    ) as tbl    

UNION

    SELECT
        23 as ordem,
        2 as grupo,
        4 as subgrupo,
        0 as item,
        cast(''2.4.0.0.00.00.00.00.00'' as varchar) as cod_estrutural,
        2 as nivel,
        cast(''Transferências de Capital'' as varchar) as nom_conta,
        0.00 as previsao_atualizada,
        0.00 as no_bimestre,
        0.00 as ate_bimestre,
        0.00 as ate_bimestre_exercicio_anterior

UNION

    SELECT
        24 as ordem,
        2 as grupo,
        4 as subgrupo,
        1 as item,
        cast(''2.4.1.0.00.00.00.00.00'' as varchar) as cod_estrutural,
        3 as nivel,
        cast(''Convênios'' as varchar) as nom_conta,
        cast(sum(coalesce(previsao_inicial,0.00)) as numeric(14,2)) as previsao_atualizada,
        cast(sum(coalesce(no_bimestre,0.00))*-1 as numeric(14,2)) as no_bimestre,
        cast(sum(coalesce(ate_bimestre,0.00))*-1 as numeric(14,2)) as ate_bimestre,
        cast(sum(coalesce(ate_bimestre_exercicio_anterior,0.00))*-1 as numeric(14,2)) as ate_bimestre_exercicio_anterior
    FROM(
        SELECT
            publico.fn_nivel(conta_receita.cod_estrutural) as nivel,
            conta_receita.cod_estrutural,
            conta_receita.descricao,
            orcamento.fn_receita_valor_previsto( ''' || stExercicio || '''
                                                ,publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                ,''' || stCodEntidades || '''
            ) as previsao_inicial,
            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                     ,''' || dtInicio || '''
                                                     ,''' || dtFim || '''
            ) as no_bimestre,
            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                     ,''' || dtInicioExercicio || '''
                                                     ,''' || dtFim || '''
            ) as ate_bimestre,
            -- orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
            --                                          ,''' || dtInicioExercicioAnterior || '''
            --                                          ,''' || dtFinalAnterior || '''
            -- ) as ate_bimestre_exercicio_anterior
            orcamento.fn_somatorio_balancete_receita_exercicio_menor_2012( conta_receita.cod_estrutural
                                                         ,''' || dtInicioExercicioAnterior || '''
                                                         ,''' || dtFinalAnterior || '''
                                                         ,''' || stExercicioAnterior || '''
            ) as ate_bimestre_exercicio_anterior
        FROM
            --contabilidade.plano_conta   ,
            orcamento.conta_receita  
        WHERE
            -- Ligação entre contabilidade.plano_conta e orcamento.conta_receita
            --plano_conta.cod_estrutural   = ''4.''||conta_receita.cod_estrutural  AND
            --plano_conta.exercicio        = conta_receita.exercicio             
            -- Filtros
                conta_receita.cod_estrutural = ''2.4.7.0.00.00.00.00.00''
            AND publico.fn_nivel(conta_receita.cod_estrutural) = 3
            AND conta_receita.exercicio = ''' || stExercicio || '''
    
        ORDER BY
            conta_receita.cod_estrutural
    )  as tbl

    UNION 

    SELECT
        25 as ordem,
        2 as grupo,
        4 as subgrupo,
        2 as item,
        cast(''2.4.2.0.00.00.00.00.00'' as varchar) as cod_estrutural,
        3 as nivel,
        cast(''Outras Transferências de Capital'' as varchar) as nom_conta,
        cast(sum(coalesce(previsao_inicial,0.00)) as numeric(14,2)) as previsao_atualizada,
        cast(sum(coalesce(no_bimestre,0.00))*-1 as numeric(14,2)) as no_bimestre,
        cast(sum(coalesce(ate_bimestre,0.00))*-1 as numeric(14,2)) as ate_bimestre,
        cast(sum(coalesce(ate_bimestre_exercicio_anterior,0.00))*-1 as numeric(14,2)) as ate_bimestre_exercicio_anterior
    FROM( 
        SELECT
            publico.fn_nivel(conta_receita.cod_estrutural) as nivel, 
            conta_receita.cod_estrutural, 
            conta_receita.descricao, 
            orcamento.fn_receita_valor_previsto( ''' || stExercicio || '''
                                                ,publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                ,''' || stCodEntidades || '''
            ) as previsao_inicial,
            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                     ,''' || dtInicio || '''
                                                     ,''' || dtFim || '''
            ) as no_bimestre,
            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                     ,''' || dtInicioExercicio || '''
                                                     ,''' || dtFim || '''
            ) as ate_bimestre,
            -- orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
            --                                          ,''' || dtInicioExercicioAnterior || '''
            --                                          ,''' || dtFinalAnterior || '''
            -- ) as ate_bimestre_exercicio_anterior
            orcamento.fn_somatorio_balancete_receita_exercicio_menor_2012( conta_receita.cod_estrutural
                                                         ,''' || dtInicioExercicioAnterior || '''
                                                         ,''' || dtFinalAnterior || '''
                                                         ,''' || stExercicioAnterior || '''
            ) as ate_bimestre_exercicio_anterior
        FROM
            --contabilidade.plano_conta, 
            orcamento.conta_receita  
        WHERE
            -- Ligação entre contabilidade.plano_conta e orcamento.conta_receita
            --plano_conta.cod_estrutural   = ''4.''||conta_receita.cod_estrutural  AND
            --plano_conta.exercicio        = conta_receita.exercicio             
            -- Filtros
                (   conta_receita.cod_estrutural = ''2.4.2.0.00.00.00.00.00''
                OR  conta_receita.cod_estrutural = ''2.4.3.0.00.00.00.00.00''
                OR  conta_receita.cod_estrutural = ''2.4.4.0.00.00.00.00.00''
                OR  conta_receita.cod_estrutural = ''2.4.5.0.00.00.00.00.00''
                OR  conta_receita.cod_estrutural = ''2.4.6.0.00.00.00.00.00'' 
            )
            AND publico.fn_nivel(conta_receita.cod_estrutural) = 3
            AND conta_receita.exercicio = ''' || stExercicio || '''
    
        ORDER BY
            conta_receita.cod_estrutural 
    )  as tbl
 
    UNION

    SELECT
        26 as ordem,
        2 as grupo,
        5 as subgrupo,
        0 as item,
        cast(''2.5.0.0.00.00.00.00.00'' as varchar) as cod_estrutural,
        2 as nivel,
        cast(''Outras Receitas de Capital'' as varchar) as nom_conta,
        cast(sum(coalesce(previsao_inicial,0.00)) as numeric(14,2)) as previsao_atualizada,
        cast(sum(coalesce(no_bimestre,0.00))*-1 as numeric(14,2)) as no_bimestre,
        cast(sum(coalesce(ate_bimestre,0.00))*-1 as numeric(14,2)) as ate_bimestre,
        cast(sum(coalesce(ate_bimestre_exercicio_anterior,0.00))*-1 as numeric(14,2)) as ate_bimestre_exercicio_anterior
    FROM(
        SELECT
            publico.fn_nivel(conta_receita.cod_estrutural) as nivel,
            conta_receita.cod_estrutural,
            conta_receita.descricao,
            orcamento.fn_receita_valor_previsto( ''' || stExercicio || '''
                                                ,publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                ,''' || stCodEntidades || '''
            ) as previsao_inicial,
            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                     ,''' || dtInicio || '''
                                                     ,''' || dtFim || '''
            ) as no_bimestre,
            orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
                                                     ,''' || dtInicioExercicio || '''
                                                     ,''' || dtFim || '''
            ) as ate_bimestre,
            -- orcamento.fn_somatorio_balancete_receita( publico.fn_mascarareduzida(conta_receita.cod_estrutural)
            --                                          ,''' || dtInicioExercicioAnterior || '''
            --                                          ,''' || dtFinalAnterior || '''
            -- ) as ate_bimestre_exercicio_anterior
            orcamento.fn_somatorio_balancete_receita_exercicio_menor_2012( conta_receita.cod_estrutural
                                                         ,''' || dtInicioExercicioAnterior || '''
                                                         ,''' || dtFinalAnterior || '''
                                                         ,''' || stExercicioAnterior || '''
            ) as ate_bimestre_exercicio_anterior
        FROM
            --contabilidade.plano_conta   ,
            orcamento.conta_receita  
        WHERE
            -- Ligação entre contabilidade.plano_conta e orcamento.conta_receita
            --plano_conta.cod_estrutural   = ''4.''||conta_receita.cod_estrutural  AND
            --plano_conta.exercicio        = conta_receita.exercicio             
            -- Filtros
                (  conta_receita.cod_estrutural = ''2.5.0.0.00.00.00.00.00''
                OR conta_receita.cod_estrutural = ''8.5.0.0.00.00.00.00.00''
            )
            AND publico.fn_nivel(conta_receita.cod_estrutural) = 2
            AND conta_receita.exercicio = ''' || stExercicio || '''
    
        ORDER BY
            conta_receita.cod_estrutural
    )  as tbl

    UNION

    SELECT
        27 as ordem,
        3 as grupo,
        0 as subgrupo,
        0 as item,
        cast(''3.0.0.0.00.00.00.00.00'' as varchar) as cod_estrutural,
        1 as nivel,
        cast(''RECEITAS PRIMÁRIAS DE CAPITAL (VI) = (II - III - IV - V)'' as varchar) as nom_conta,
        0.00 as previsao_atualizada,
        0.00 as no_bimestre,
        0.00 as ate_bimestre,
        0.00 as ate_bimestre_exercicio_anterior

    )
    ';


    -- Criação da Tabela Temporaria tmp_valores_birt
    
    EXECUTE stSql;
    

    -- Calcular totais do nivel pai
    
    stSql := 'SELECT DISTINCT grupo FROM tmp_valores_birt ';
    
    FOR reReg IN EXECUTE stSql
    LOOP

        stSQLsubgrupo := 'SELECT DISTINCT subgrupo FROM tmp_valores_birt WHERE grupo = ' || reReg.grupo || ' ';

        FOR reRegSubgrupo IN EXECUTE stSQLsubgrupo
        LOOP

            stSQLaux2 := '
            UPDATE tmp_valores_birt SET
                previsao_atualizada = ( SELECT SUM(previsao_atualizada) FROM tmp_valores_birt WHERE grupo = ' || reReg.grupo || ' AND subgrupo = ' || reRegSubgrupo.subgrupo || ' ) ,
                no_bimestre = ( SELECT SUM(no_bimestre) FROM tmp_valores_birt WHERE grupo = ' || reReg.grupo || ' AND subgrupo = ' || reRegSubgrupo.subgrupo || ' ) ,
                ate_bimestre  = ( SELECT SUM(ate_bimestre) FROM tmp_valores_birt WHERE grupo = ' || reReg.grupo || ' AND subgrupo = ' || reRegSubgrupo.subgrupo || ' ) ,
                ate_bimestre_exercicio_anterior = ( SELECT SUM (ate_bimestre_exercicio_anterior) FROM tmp_valores_birt WHERE grupo = ' || reReg.grupo || ' AND subgrupo = ' || reRegSubgrupo.subgrupo || ' )
            WHERE grupo = ' || reReg.grupo || ' AND subgrupo = ' || reRegSubgrupo.subgrupo || ' AND item = 0
            ';

            EXECUTE stSQLaux2;

        END LOOP;

        
        stSQLaux := '
        UPDATE tmp_valores_birt SET
            previsao_atualizada = (SELECT COALESCE(SUM(previsao_atualizada), 0.00) FROM tmp_valores_birt WHERE grupo = ' || reReg.grupo || ' AND nivel = 2),
            no_bimestre = (SELECT COALESCE(SUM(no_bimestre), 0.00) FROM tmp_valores_birt WHERE grupo = ' || reReg.grupo || ' AND nivel = 2),
            ate_bimestre = (SELECT COALESCE(SUM(ate_bimestre), 0.00) FROM tmp_valores_birt WHERE grupo = ' || reReg.grupo || ' AND nivel = 2),
            ate_bimestre_exercicio_anterior = (SELECT COALESCE(SUM(ate_bimestre_exercicio_anterior), 0.00) FROM tmp_valores_birt WHERE grupo = ' || reReg.grupo || ' AND nivel = 2) 
        WHERE
            grupo = ' || reReg.grupo || ' AND nivel = 1
        ';
            
        EXECUTE stSQLaux;
        
    END LOOP;

    stSql := '    
    UPDATE tmp_valores_birt SET 
        previsao_atualizada = ((SELECT coalesce(sum(previsao_atualizada), 0.00) FROM tmp_valores_birt where cod_estrutural = ''4.2.0.0.0.00.00.00.00.00'') 
                  - (SELECT coalesce(sum(previsao_atualizada), 0.00) FROM tmp_valores_birt where cod_estrutural = ''4.2.1.0.0.00.00.00.00.00'') 
                  - (SELECT coalesce(sum(previsao_atualizada), 0.00) FROM tmp_valores_birt where cod_estrutural = ''4.2.3.0.0.00.00.00.00.00'') 
                  - (SELECT coalesce(sum(previsao_atualizada), 0.00) FROM tmp_valores_birt where cod_estrutural = ''4.2.2.0.0.00.00.00.00.00'')),
        no_bimestre = ((SELECT coalesce(sum(no_bimestre), 0.00) FROM tmp_valores_birt where cod_estrutural = ''4.2.0.0.0.00.00.00.00.00'')
                     - (SELECT coalesce(sum(no_bimestre), 0.00) FROM tmp_valores_birt where cod_estrutural = ''4.2.1.0.0.00.00.00.00.00'')
                     - (SELECT coalesce(sum(no_bimestre), 0.00) FROM tmp_valores_birt where cod_estrutural = ''4.2.3.0.0.00.00.00.00.00'')
                     - (SELECT coalesce(sum(no_bimestre), 0.00) FROM tmp_valores_birt where cod_estrutural = ''4.2.2.0.0.00.00.00.00.00'')),
        ate_bimestre = ((SELECT coalesce(sum(ate_bimestre), 0.00) FROM tmp_valores_birt where cod_estrutural = ''4.2.0.0.0.00.00.00.00.00'') 
                      - (SELECT coalesce(sum(ate_bimestre), 0.00) FROM tmp_valores_birt where cod_estrutural = ''4.2.1.0.0.00.00.00.00.00'') 
                      - (SELECT coalesce(sum(ate_bimestre), 0.00) FROM tmp_valores_birt where cod_estrutural = ''4.2.3.0.0.00.00.00.00.00'') 
                      - (SELECT coalesce(sum(ate_bimestre), 0.00) FROM tmp_valores_birt where cod_estrutural = ''4.2.2.0.0.00.00.00.00.00'')), 
        ate_bimestre_exercicio_anterior = ((SELECT coalesce(sum(ate_bimestre_exercicio_anterior), 0.00) FROM tmp_valores_birt where cod_estrutural = ''4.2.0.0.0.00.00.00.00.00'') 
                      - (SELECT coalesce(sum(ate_bimestre_exercicio_anterior), 0.00) FROM tmp_valores_birt where cod_estrutural = ''4.2.1.0.0.00.00.00.00.00'') 
                      - (SELECT coalesce(sum(ate_bimestre_exercicio_anterior), 0.00) FROM tmp_valores_birt where cod_estrutural = ''4.2.3.0.0.00.00.00.00.00'') 
                      - (SELECT coalesce(sum(ate_bimestre_exercicio_anterior), 0.00) FROM tmp_valores_birt where cod_estrutural = ''4.2.2.0.0.00.00.00.00.00'')  ) 
    WHERE cod_estrutural = ''4.3.0.0.0.00.00.00.00.00'';
    ';
    EXECUTE stSql;


    stSql := '
    SELECT
        ordem,
        grupo,
        cod_estrutural,
        nivel,
        nom_conta,
        previsao_atualizada,
        no_bimestre,
        ate_bimestre,
        ate_bimestre_exercicio_anterior
    FROM
        tmp_valores_birt
    ORDER BY
        ordem';
    
    
/*
    stSql := ''
    SELECT
        ordem, 
        grupo, 
        cod_estrutural, 
        nivel, 
        nom_conta, 
        COALESCE(SUM(previsao_atualizada), 0.00) AS previsao_atualizada, 
        COALESCE(SUM(no_bimestre), 0.00) AS no_bimestre, 
        COALESCE(SUM(ate_bimestre), 0.00) AS ate_bimestre, 
        COALESCE(SUM(ate_bimestre_exercicio_anterior), 0.00) AS ate_bimestre_exercicio_anterior 
    FROM 
        tmp_valores_birt
    GROUP BY
        ordem, 
        grupo, 
        cod_estrutural, 
        nivel, 
        nom_conta 
    ORDER BY
        ordem'';
*/    
    
    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN next reRegistro;
    END LOOP;

    DROP TABLE tmp_valor;
    DROP TABLE tmp_valores_birt;

    RETURN;
END;
$$ language 'plpgsql';

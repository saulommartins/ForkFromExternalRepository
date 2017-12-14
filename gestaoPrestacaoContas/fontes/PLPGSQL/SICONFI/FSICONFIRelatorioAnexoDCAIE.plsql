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
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Revision: 27052 $
* $Name$
* $Author: cako $
* $Date: 2007-12-05 15:12:56 -0200 (Qua, 05 Dez 2007) $
*
* Casos de uso: uc-02.01.23
*/

CREATE OR REPLACE FUNCTION siconfi.relatorio_siconfi_anexo_dca_ie(varchar,varchar,varchar,varchar,varchar) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio             ALIAS FOR $1;
    stFiltro                ALIAS FOR $2;
    stDataInicial           ALIAS FOR $3;
    stDataFinal             ALIAS FOR $4;
    stCodEntidades          ALIAS FOR $5;
    stSql               VARCHAR   := '';
    dataInicio          VARCHAR   := '';
    dataFim             VARCHAR   := '';    
    reRegistro          RECORD;

BEGIN
    stSql := 'CREATE TEMPORARY TABLE tmp_empenhado AS (
        SELECT
            SUM(coalesce(ipe.vl_total,0.00)) as valor            
            ,od.cod_funcao
            ,od.cod_subfuncao
        FROM
            orcamento.despesa           as od,
            orcamento.conta_despesa     as cd,
            empenho.pre_empenho_despesa as ped,
            empenho.empenho             as e,
            empenho.pre_empenho         as pe,
            empenho.item_pre_empenho    as ipe
        WHERE
                cd.cod_conta               = ped.cod_conta
            AND cd.exercicio               = ped.exercicio

            And od.cod_despesa              = ped.cod_despesa
            AND od.exercicio                = ped.exercicio

            And pe.exercicio               = ped.exercicio
            And pe.cod_pre_empenho         = ped.cod_pre_empenho

            And e.cod_entidade             IN (' || stCodEntidades || ')
            And e.exercicio                = ' || quote_literal(stExercicio) || '

            AND e.exercicio                = pe.exercicio
            AND e.cod_pre_empenho          = pe.cod_pre_empenho

            AND pe.exercicio               = ipe.exercicio
            AND pe.cod_pre_empenho         = ipe.cod_pre_empenho

            
            
            ' || stFiltro || '
        
        GROUP BY cod_funcao
                ,cod_subfuncao

        )';
        EXECUTE stSql;

    stSql := 'CREATE TEMPORARY TABLE tmp_anulado AS (
            SELECT  
                    SUM(EEAI.vl_anulado) as valor
                    , od.cod_funcao
                    , od.cod_subfuncao
                FROM orcamento.despesa           as OD,
                    orcamento.conta_despesa     as OCD,
                    empenho.pre_empenho_despesa as EPED,
                    empenho.pre_empenho         as EPE,
                    empenho.item_pre_empenho    as EIPE,
                    empenho.empenho_anulado_item as EEAI

               WHERE    OCD.cod_conta            = EPED.cod_conta
                    AND OCD.exercicio            = EPED.exercicio
                    And EPED.exercicio           = EPE.exercicio
                    And EPED.cod_pre_empenho     = EPE.cod_pre_empenho
                    And EPE.exercicio            = EIPE.exercicio
                    And EPE.cod_pre_empenho      = EIPE.cod_pre_empenho
                    And EIPE.exercicio           = EEAI.exercicio
                    And EIPE.cod_pre_empenho     = EEAI.cod_pre_empenho
                    And EIPE.num_item            = EEAI.num_item
                    And EEAI.exercicio           =' || quote_literal(stExercicio) ||'
                    And EEAI.cod_entidade        IN ('||stCodEntidades||')
                    And OD.cod_despesa           = EPED.cod_despesa
                    AND OD.exercicio             = EPED.exercicio
                
                ' || stFiltro || ' 
            
            GROUP BY cod_funcao
                    ,cod_subfuncao
        )';
        
        EXECUTE stSql;

    stSql := 'CREATE TEMPORARY TABLE tmp_pago AS (
        SELECT
             SUM(ENLP.vl_pago) as valor
            , od.cod_funcao
            , od.cod_subfuncao
        FROM
            orcamento.despesa               as OD,
            orcamento.conta_despesa         as OCD,
            empenho.pre_empenho_despesa     as EPED,
            empenho.empenho                 as EE,
            empenho.pre_empenho             as EPE,
            empenho.nota_liquidacao         as ENL,
            empenho.nota_liquidacao_paga    as ENLP

        WHERE
                OCD.cod_conta            = EPED.cod_conta
            AND OCD.exercicio            = EPED.exercicio

            AND OD.cod_despesa           = EPED.cod_despesa
            AND OD.exercicio             = EPED.exercicio

            And EPED.cod_pre_empenho     = EPE.cod_pre_empenho
            And EPED.exercicio           = EPE.exercicio

            And EPE.exercicio            = EE.exercicio
            And EPE.cod_pre_empenho      = EE.cod_pre_empenho

            And EE.exercicio             ='|| quote_literal(stExercicio) ||'
            And EE.cod_entidade          IN ('||stCodEntidades||')

            And EE.cod_empenho           = ENL.cod_empenho
            And EE.exercicio             = ENL.exercicio_empenho
            And EE.cod_entidade          = ENL.cod_entidade

            And ENL.cod_nota             = ENLP.cod_nota
            And ENL.cod_entidade         = ENLP.cod_entidade
            And ENL.exercicio            = ENLP.exercicio
            
            AND (ENLP.timestamp::DATE >= to_date('''||stDataInicial||''',''dd/mm/yyyy'') AND ENLP.timestamp::DATE <= to_date('''||stDataFinal||''',''dd/mm/yyyy''))
            
            ' || stFiltro || ' 
        
        GROUP BY cod_funcao
                ,cod_subfuncao

        )';
        EXECUTE stSql;

    stSql := 'CREATE TEMPORARY TABLE tmp_estornado AS (
        SELECT
            SUM(ENLPA.vl_anulado) as valor            
            , od.cod_funcao
            , od.cod_subfuncao
        FROM
            orcamento.despesa                    as OD,
            orcamento.conta_despesa              as OCD,
            empenho.pre_empenho_despesa          as EPED,
            empenho.empenho                      as EE,
            empenho.pre_empenho                  as EPE,
            empenho.nota_liquidacao              as ENL,
            empenho.nota_liquidacao_paga         as ENLP,
            empenho.nota_liquidacao_paga_anulada as ENLPA
        WHERE
                OCD.cod_conta            = EPED.cod_conta
            AND OCD.exercicio            = EPED.exercicio
            
            And OD.cod_despesa           = EPED.cod_despesa
            AND OD.exercicio             = EPED.exercicio
            
            And EPED.exercicio           = EPE.exercicio
            And EPED.cod_pre_empenho     = EPE.cod_pre_empenho

            And EPE.exercicio            = EE.exercicio
            And EPE.cod_pre_empenho      = EE.cod_pre_empenho

            And EE.cod_entidade          IN ('||stCodEntidades||')
            And EE.exercicio             = '|| quote_literal(stExercicio) ||'

            And EE.cod_empenho           = ENL.cod_empenho
            And EE.exercicio             = ENL.exercicio_empenho
            And EE.cod_entidade          = ENL.cod_entidade

            And ENL.exercicio            = ENLP.exercicio
            And ENL.cod_nota             = ENLP.cod_nota
            And ENL.cod_entidade         = ENLP.cod_entidade

            And ENLP.cod_entidade        = ENLPA.cod_entidade
            And ENLP.cod_nota            = ENLPA.cod_nota
            And ENLP.exercicio           = ENLPA.exercicio
            And ENLP.timestamp           = ENLPA.timestamp
            
            AND (ENLPA.timestamp_anulada::DATE >= to_date('''||stDataInicial||''',''dd/mm/yyyy'') AND ENLPA.timestamp_anulada::DATE <= to_date('''||stDataFinal||''',''dd/mm/yyyy''))
            
            ' || stFiltro || '

            GROUP BY cod_funcao
                    ,cod_subfuncao
        )';
    EXECUTE stSql;
    
    stSql := 'CREATE TEMPORARY TABLE tmp_liquidado AS (
                SELECT                    
                    SUM(nli.vl_total) as valor                    
                    ,od.cod_funcao
                    ,od.cod_subfuncao
                FROM
                    orcamento.despesa             as od,
                    orcamento.conta_despesa       as cd,
                    empenho.pre_empenho_despesa   as ped,
                    empenho.pre_empenho           as pe,
                    empenho.empenho               as e,
                    empenho.nota_liquidacao_item  as nli,
                    empenho.nota_liquidacao       as nl
                WHERE
                        cd.cod_conta        = ped.cod_conta
                    AND cd.exercicio        = ped.exercicio

                    And od.cod_despesa      = ped.cod_despesa
                    AND od.exercicio        = ped.exercicio

                    And pe.exercicio        = ped.exercicio
                    And pe.cod_pre_empenho  = ped.cod_pre_empenho

                    And e.cod_entidade      IN (' || stCodEntidades || ')
                    And e.exercicio         = ' || quote_literal(stExercicio) || '

                    AND e.exercicio         = pe.exercicio
                    AND e.cod_pre_empenho   = pe.cod_pre_empenho

                    AND e.exercicio    = nl.exercicio_empenho
                    AND e.cod_entidade = nl.cod_entidade
                    AND e.cod_empenho  = nl.cod_empenho

                    AND nl.exercicio    = nli.exercicio
                    AND nl.cod_nota     = nli.cod_nota
                    AND nl.cod_entidade = nli.cod_entidade
                    And nl.exercicio    = ' || quote_literal(stExercicio) || '
                    
                    ' || stFiltro || ' 
                GROUP BY cod_funcao
                        ,cod_subfuncao
        )';
        EXECUTE stSql;

    stSql := 'CREATE TEMPORARY TABLE tmp_liquidado_estornado AS (
        SELECT            
            SUM(ENLIA.vl_anulado) as valor            
            , od.cod_funcao
            , od.cod_subfuncao
            
        from orcamento.despesa                    as OD,
             orcamento.conta_despesa              as OCD,
             empenho.pre_empenho_despesa          as EPED,
             empenho.pre_empenho                  as EPE,
             empenho.empenho                      as EE,
             empenho.nota_liquidacao              as ENL,
             empenho.nota_liquidacao_item         as ENLI,
             empenho.nota_liquidacao_item_anulado as ENLIA

        Where OCD.cod_conta               = EPED.cod_conta
          AND OCD.exercicio               = EPED.exercicio
          And EPE.cod_pre_empenho         = EE.cod_pre_empenho
          And EPE.exercicio               = EE.exercicio

          And EE.exercicio                = ENL.exercicio_empenho
          And EE.cod_entidade             = ENL.cod_entidade
          And EE.cod_empenho              = ENL.cod_empenho
          And EE.cod_entidade             IN ('||stCodEntidades||')
          And EE.exercicio                = '|| quote_literal(stExercicio) || '

          And ENL.exercicio               = ENLI.exercicio
          And ENL.cod_nota                = ENLI.cod_nota
          And ENL.cod_entidade            = ENLI.cod_entidade
          And ENLI.exercicio           = ENLIA.exercicio
          And ENLI.cod_pre_empenho     = ENLIA.cod_pre_empenho
          And ENLI.num_item            = ENLIA.num_item
          And ENLI.cod_entidade        = ENLIA.cod_entidade
          And ENLI.exercicio_item      = ENLIA.exercicio_item
          And ENLI.cod_nota            = ENLIA.cod_nota
          And OD.cod_despesa           = EPED.cod_despesa
          AND OD.exercicio             = EPED.exercicio
          And OD.cod_entidade          IN ('||stCodEntidades||')
          And EPED.exercicio           = EPE.exercicio
          And EPED.cod_pre_empenho     = EPE.cod_pre_empenho
          
          ' || stFiltro || '

          GROUP BY cod_funcao
                    ,cod_subfuncao
        )';
    EXECUTE stSql;

stSql := ' CREATE TEMPORARY TABLE tmp_retorno AS (
    SELECT DISTINCT
        cod_funcao
        , nom_funcao
        , cod_subfuncao        
        , nom_subfuncao
        , COALESCE(despesas_empenhadas,0.00) as despesas_empenhadas
        , COALESCE(despesas_liquididas,0.00) as despesas_liquididas
        , COALESCE(despesas_pagas,0.00)      as despesas_pagas
        , COALESCE((despesas_empenhadas - despesas_liquididas),0.00) as restos_nao_processados
        , COALESCE((despesas_liquididas - despesas_pagas),0.00) as restos_processados
    FROM (
        SELECT              
             od.cod_funcao
            , funcao.descricao as nom_funcao
            , od.cod_subfuncao
            , subfuncao.descricao as nom_subfuncao
            
            
            ,( 
                (SELECT valor from tmp_empenhado where cod_funcao = od.cod_funcao and cod_subfuncao = od.cod_subfuncao) --empenhados
                -
                COALESCE((SELECT valor from tmp_anulado where cod_funcao = od.cod_funcao and cod_subfuncao = od.cod_subfuncao),0) --anuladas
            )as despesas_empenhadas
            
            
            ,(  
                (SELECT valor from tmp_liquidado where cod_funcao = od.cod_funcao and cod_subfuncao = od.cod_subfuncao) --liquidados
                - 
                COALESCE((SELECT valor from tmp_liquidado_estornado where cod_funcao = od.cod_funcao and cod_subfuncao = od.cod_subfuncao),0) --liquidadas_estornadas
            )as despesas_liquididas
            
            
            , ( 
                (SELECT valor from tmp_pago where cod_funcao = od.cod_funcao and cod_subfuncao = od.cod_subfuncao)  --pagas
                -
                COALESCE((SELECT valor from tmp_estornado where cod_funcao = od.cod_funcao and cod_subfuncao = od.cod_subfuncao), 0)  --estornado
            )as despesas_pagas
            
            
        FROM orcamento.conta_despesa  ocd
        
        INNER JOIN orcamento.despesa  od
             ON od.exercicio          = ocd.exercicio       
            AND od.cod_conta          = ocd.cod_conta       

        INNER JOIN orcamento.funcao
             ON funcao.exercicio  = od.exercicio
            AND funcao.cod_funcao = od.cod_funcao

        INNER JOIN orcamento.subfuncao
             ON subfuncao.exercicio  = od.exercicio
            AND subfuncao.cod_subfuncao = od.cod_subfuncao

        WHERE od.cod_entidade       IN ('||stCodEntidades||') 
            AND od.exercicio          = '''||stExercicio||'''
        
    ) as tbl
)
';
EXECUTE stSql;

    --Adicionando Somatorio de cada funcao
    stSql := 'INSERT INTO tmp_retorno
                SELECT  DISTINCT
                        cod_funcao
                        ,nom_funcao
                        ,0 as cod_subfuncao
                        ,'''' as nom_subfuncao
                        , SUM(despesas_empenhadas)
                        , SUM(despesas_liquididas)
                        , SUM(despesas_pagas)
                        , SUM(restos_nao_processados)
                        , SUM(restos_processados)
                FROM tmp_retorno
                GROUP BY cod_funcao,nom_funcao ';
    EXECUTE stSql;

    --select para trazer no formato certo para o relatorio, 
    --ordenado por cod_funcao e cod_subfuncao e adicionado o NIVEL para identacao das colunas no relatorio em html
    stSql := '  SELECT 
                    CASE WHEN cod_subfuncao = 0 THEN
                            1
                        ELSE
                            3
                    END as nivel
                    ,CASE WHEN cod_subfuncao = 0 THEN 
                        LPAD(cod_funcao::varchar,2,''0'')||''.''||LPAD(cod_subfuncao::varchar,3,''0'')||'' - ''||nom_funcao
                    ELSE
                        LPAD(cod_funcao::varchar,2,''0'')||''.''||LPAD(cod_subfuncao::varchar,3,''0'')||'' - ''||nom_subfuncao
                    END as descricao
                    , despesas_empenhadas
                    , despesas_liquididas
                    , despesas_pagas
                    , restos_nao_processados
                    , restos_processados
                FROM tmp_retorno
                order by cod_funcao, cod_subfuncao
            ';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN next reRegistro;
    END LOOP;

    DROP TABLE tmp_estornado;
    DROP TABLE tmp_liquidado_estornado;
    DROP TABLE tmp_empenhado;
    DROP TABLE tmp_anulado;
    DROP TABLE tmp_pago;
    DROP TABLE tmp_liquidado;
    DROP TABLE tmp_retorno;

END;
$$ language 'plpgsql';

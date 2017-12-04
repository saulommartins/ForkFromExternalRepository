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
* $Id: $
*
*/

CREATE OR REPLACE FUNCTION empenho.fn_relacao_empenho(varchar,varchar,varchar,varchar,varchar,varchar, varchar, varchar,varchar
                                                      ,varchar,varchar,varchar,varchar,varchar,varchar,varchar,varchar,varchar
                                                      , varchar, varchar,varchar
                                                    ) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio                    ALIAS FOR $1;
    stDtInicial                    ALIAS FOR $2;
    stDtFinal                      ALIAS FOR $3;
    stCodEntidades                 ALIAS FOR $4;
    stCodOrgao                     ALIAS FOR $5;
    stCodUnidade                   ALIAS FOR $6;
    stCodPao                       ALIAS FOR $7;
    stCodRecurso                   ALIAS FOR $8;
    stCodElementoDispensa          ALIAS FOR $9;
    stDestinacaoRecurso            ALIAS FOR $10;
    inCodDetalhamento              ALIAS FOR $11;
    stCodElementoDispensaMasc      ALIAS FOR $12;
    stCodHistorico                 ALIAS FOR $13;
    stOrdenacao                    ALIAS FOR $14;
    inCodFuncao                    ALIAS FOR $15;
    inCodSubFuncao                 ALIAS FOR $16;
    inCodPrograma                  ALIAS FOR $17;
    inCodDotacao                   ALIAS FOR $18;
    inCodTipo                      ALIAS FOR $19;
    inCodCategoria                 ALIAS FOR $20;
    inCodFornecedor                ALIAS FOR $21;
    
    stSql               VARCHAR   := '';
    reRegistro          RECORD;

BEGIN

    stSql := '
        CREATE TEMPORARY TABLE tmp_empenhos AS
        SELECT entidade
            , exercicio
            , stData
            , empenho
            , cgm
            , razao_social
            , funcao_programatica
            , recurso
            , despesa 
            , coalesce(valor_empenhado, 0.00) - coalesce(valor_empenhado_anulado, 0.00) as vl_empenhado
            , coalesce(valor_liquidado, 0.00) - coalesce(valor_liquidado_anulado, 0.00) as vl_liquidado
            , (coalesce(valor_empenhado, 0.00) - coalesce(valor_empenhado_anulado, 0.00)) - (coalesce(valor_liquidado, 0.00) - coalesce(valor_liquidado_anulado, 0.00))  as vl_a_liquidar
        FROM (
            SELECT
                  empenho.cod_entidade as entidade
                , empenho.exercicio as exercicio
                , to_char(empenho.dt_empenho,''dd/mm/yyyy'') as stData
                , cast(empenho.cod_entidade || '' - '' || empenho.cod_empenho || ''/'' || empenho.exercicio as varchar) as empenho
                , pre_empenho.cgm_beneficiario as cgm
                , sw_cgm.nom_cgm as razao_social
                , cast(ped_d_cd.funcao_programatica as varchar) as funcao_programatica
                , ped_d_cd.nom_recurso as recurso
                , ped_d_cd.cod_estrutural as despesa
                , sum(coalesce(empenhado.vl_total, 0.00)) as valor_empenhado
                , sum(coalesce(empenho_anulado.vl_anulado, 0.00)) as valor_empenhado_anulado
                , sum(coalesce(nota_liquidacao.vl_total, 0.00)) as valor_liquidado
                , sum(coalesce(nota_liquidacao.vl_anulado, 0.00)) as valor_liquidado_anulado
            FROM
                empenho.empenho
                JOIN empenho.pre_empenho
                ON (    empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                    AND empenho.exercicio = pre_empenho.exercicio )
                LEFT JOIN( SELECT sum(coalesce(item_pre_empenho.vl_total, 0.00)) as vl_total
                                , cod_pre_empenho
                                , exercicio
                             FROM empenho.item_pre_empenho
                         GROUP BY item_pre_empenho.cod_pre_empenho
                                , item_pre_empenho.exercicio             ) as empenhado
                       ON(     empenhado.cod_pre_empenho = pre_empenho.cod_pre_empenho
                           AND empenhado.exercicio = pre_empenho.exercicio )

                LEFT JOIN( SELECT sum(coalesce(nli.vl_total, 0.00)) as vl_total
                                , sum(coalesce(nlia.vl_anulado, 0.00)) as vl_anulado
                                , ipe.cod_pre_empenho
                                , ipe.exercicio
                                , nl.exercicio_empenho
                                , nl.cod_entidade
                                , nl.cod_empenho
                             FROM
                                  empenho.nota_liquidacao      as nl
                                , empenho.nota_liquidacao_item as nli
                                  LEFT JOIN empenho.nota_liquidacao_item_anulado as nlia
                                         ON (     nlia.exercicio       = nli.exercicio
                                              AND nlia.cod_nota        = nli.cod_nota
                                              AND nlia.num_item        = nli.num_item
                                              AND nlia.exercicio_item  = nli.exercicio_item
                                              AND nlia.cod_pre_empenho = nli.cod_pre_empenho
                                              AND nlia.cod_entidade    = nli.cod_entidade
                                              AND to_date(to_char(nlia.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') <= to_date('''|| stDtFinal || ''',''dd/mm/yyyy'') )

                                , empenho.item_pre_empenho     as ipe
                                WHERE nli.cod_nota     = nl.cod_nota
                              AND nli.cod_entidade     = nl.cod_entidade
                              AND nli.exercicio        = nl.exercicio
                              AND nli.exercicio_item   = ipe.exercicio
                              AND nli.cod_pre_empenho  = ipe.cod_pre_empenho
                              AND nli.num_item         = ipe.num_item
                              AND nl.dt_liquidacao <= to_date(''' || stDtFinal || ''',''dd/mm/yyyy'')
                         GROUP BY
                                  ipe.cod_pre_empenho
                                , ipe.exercicio
                                , nl.exercicio_empenho
                                , nl.cod_entidade
                                , nl.cod_empenho                                                ) as nota_liquidacao
                       ON(     nota_liquidacao.cod_pre_empenho = pre_empenho.cod_pre_empenho
                           AND nota_liquidacao.exercicio   = pre_empenho.exercicio
                           AND nota_liquidacao.exercicio_empenho = empenho.exercicio
                           AND nota_liquidacao.cod_entidade = empenho.cod_entidade
                           AND nota_liquidacao.cod_empenho = empenho.cod_empenho            )

                JOIN sw_cgm
                ON ( sw_cgm.numcgm = pre_empenho.cgm_beneficiario )

                JOIN empenho.historico
                ON (    historico.cod_historico = pre_empenho.cod_historico
                    AND historico.exercicio     = pre_empenho.exercicio )

                JOIN empenho.tipo_empenho
                ON ( tipo_empenho.cod_tipo = pre_empenho.cod_tipo )

                JOIN empenho.categoria_empenho
                ON ( categoria_empenho.cod_categoria = empenho.cod_categoria )

                LEFT OUTER JOIN (
                    SELECT
                        pre_empenho_despesa.exercicio,
                        pre_empenho_despesa.cod_pre_empenho,
                        lpad(despesa.num_orgao::VARCHAR,2,''0'') || ''.'' || lpad(despesa.num_unidade::VARCHAR,2,''0'') || ''.'' || lpad(despesa.cod_funcao::VARCHAR,2,''0'') || ''.'' || lpad(despesa.cod_subfuncao::VARCHAR,3,''0'') || ''.'' || lpad(ppa.programa.num_programa::VARCHAR,4,''0'') || ''.'' || lpad(ppa.acao.num_acao::VARCHAR,4,''0'') as funcao_programatica,
                        despesa.num_pao,
                        despesa.num_orgao,
                        despesa.num_unidade,
                        despesa.cod_recurso,
                        despesa.cod_despesa,
                        rec.nom_recurso,
                        despesa.cod_conta,
                        conta_despesa.cod_estrutural,
                        rec.masc_recurso_red,
                        rec.cod_detalhamento,
                        ppa.acao.num_acao,
                        programa.num_programa
                    FROM
                        empenho.pre_empenho_despesa,
                        orcamento.despesa
                        JOIN orcamento.recurso(''' || stExercicio || ''') as rec
                        ON ( rec.cod_recurso = despesa.cod_recurso
                            AND rec.exercicio = despesa.exercicio )
                          JOIN orcamento.programa_ppa_programa
                            ON programa_ppa_programa.cod_programa = despesa.cod_programa
                           AND programa_ppa_programa.exercicio   = despesa.exercicio
                          JOIN ppa.programa
                            ON ppa.programa.cod_programa = programa_ppa_programa.cod_programa_ppa
                          JOIN orcamento.pao_ppa_acao
                            ON pao_ppa_acao.num_pao = despesa.num_pao
                           AND pao_ppa_acao.exercicio = despesa.exercicio
                          JOIN ppa.acao 
                            ON ppa.acao.cod_acao = pao_ppa_acao.cod_acao
                        ,orcamento.conta_despesa
                    WHERE
                        pre_empenho_despesa.exercicio      =  ''' || stExercicio || ''' AND
                        pre_empenho_despesa.cod_despesa    = despesa.cod_despesa and
                        pre_empenho_despesa.exercicio      = despesa.exercicio   and
                        pre_empenho_despesa.cod_conta      = conta_despesa.cod_conta  and
                        pre_empenho_despesa.exercicio      = conta_despesa.exercicio
                ) as ped_d_cd
                ON (    pre_empenho.exercicio       = ped_d_cd.exercicio
                    AND pre_empenho.cod_pre_empenho = ped_d_cd.cod_pre_empenho )

                LEFT JOIN(
                            SELECT
                                   sum(coalesce(eai.vl_anulado, 0.00)) as vl_anulado
                                 , ea.exercicio
                                 , ea.cod_entidade
                                 , ea.cod_empenho
                              FROM
                                   empenho.empenho_anulado_item as eai
                                 , empenho.empenho_anulado as ea
                             WHERE eai.exercicio    = ea.exercicio
                               AND eai.cod_entidade = ea.cod_entidade
                               AND eai.cod_empenho  = ea.cod_empenho
                               AND eai.timestamp    = ea.timestamp
                               AND to_date(to_char(eai.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') <= to_date(''' || stDtFinal || ''',''dd/mm/yyyy'')

                          GROUP BY ea.exercicio
                                 , ea.cod_entidade
                                 , ea.cod_empenho                            ) as empenho_anulado
                       ON(     empenho_anulado.exercicio    = empenho.exercicio
                           AND empenho_anulado.cod_entidade = empenho.cod_entidade
                           AND empenho_anulado.cod_empenho  = empenho.cod_empenho  )

            WHERE
                    empenho.exercicio         = ''' || stExercicio || '''
                AND empenho.cod_entidade      IN (' || stCodEntidades || ')          ';

                if (stCodHistorico is not null and TRIM(stCodHistorico)<>'') then
                    stSql := stSql || ' and historico.cod_historico = ' || stCodHistorico || ' ';
                end if;

                stSql := stSql || '
                    AND empenho.dt_empenho BETWEEN to_date(''' || stDtInicial || ''',''dd/mm/yyyy'') AND to_date(''' || stDtFinal || ''',''dd/mm/yyyy'') ';

                if (stCodOrgao is not null and stCodOrgao<>'') then
                    stSql := stSql || ' AND ped_d_cd.num_orgao = '|| stCodOrgao ||' ';
                end if;

                if (stCodUnidade is not null and stCodUnidade<>'') then
                    stSql := stSql || ' AND ped_d_cd.num_unidade = '|| stCodUnidade ||'  ';
                end if;

                if (stCodPao is not null and stCodPao<>'') then
                    --stSql := stSql || ' AND ped_d_cd.num_pao = '|| stCodPao ||' ';
                    stSql := stSql || ' AND ped_d_cd.num_acao = '|| stCodPao ||' ';
                    
                end if;
                
                IF (inCodPrograma IS NOT NULL AND inCodPrograma <> '') THEN
                    stSql := stSql || ' AND ped_d_cd.num_programa = '|| inCodPrograma || ' ';
                END IF;
                if (stCodRecurso is not null and stCodRecurso<>'') then
                    stSql := stSql || ' AND ped_d_cd.cod_recurso = '|| stCodRecurso ||' ';
                end if;
                
                if (stDestinacaoRecurso is not null and stDestinacaoRecurso <> '') then
                    stSql := stSql || ' AND ped_d_cd.masc_recurso_red like '''|| stDestinacaoRecurso || '''%''' ||''' ';
                end if;
                
                if (inCodDotacao is not null and inCodDotacao <>'') then
                    stSql := stSql || ' AND ped_d_cd.cod_despesa = ' || inCodDotacao || ' ';
                end if;
                
                if (inCodDetalhamento is not null and inCodDetalhamento <> '') then 
                    stSql := stSql || ' AND ped_d_cd.cod_detalhamento = '|| inCodDetalhamento ||' ';
                end if;

                if (stCodElementoDispensa is not null and stCodElementoDispensa<>'') then
                    --stSql := stSql || ' AND ped_d_cd.cod_estrutural like publico.fn_mascarareduzida(''' || stCodElementoDispensaMasc || ''')|| ''%'' ';
                    stSql := stSql || ' AND ped_d_cd.cod_estrutural like publico.fn_mascarareduzida(''' || stCodElementoDispensa || ''')|| ''%'' ';
                end if;

                if (inCodTipo is not null and inCodTipo<>'') then
                    stSql := stSql || ' AND tipo_empenho.cod_tipo = ' || inCodTipo || ' ';
                end if;

                if (inCodCategoria is not null and inCodCategoria<>'') then
                    stSql := stSql || ' AND categoria_empenho.cod_categoria = ' || inCodCategoria || ' ';
                end if;

                if (inCodFornecedor is not null and inCodFornecedor<>'') then
                    stSql := stSql || ' AND sw_cgm.numcgm = ' || inCodFornecedor || ' ';
                end if;

            stSql := stSql || ' 
                GROUP BY 
                      empenho.dt_empenho
                    , empenho.cod_pre_empenho
                    , empenho.cod_entidade
                    , empenho.cod_empenho 
                    , empenho.exercicio 
                    , pre_empenho.cgm_beneficiario
                    , sw_cgm.nom_cgm
                    , ped_d_cd.cod_estrutural 
                    , ped_d_cd.nom_recurso 
                    , ped_d_cd.funcao_programatica
                ORDER BY 
                      empenho.dt_empenho
                    , empenho.cod_entidade 
                    , empenho.cod_empenho 
                    , empenho.exercicio
                    , empenho.cod_pre_empenho
                    , pre_empenho.cgm_beneficiario
                    , sw_cgm.nom_cgm 
             ) as tbl 
             ';


    RAISE NOTICE 'SQL %', stSql;

    EXECUTE stSql;
        
    stSql := '
        SELECT entidade
            , exercicio
            , stData
            , empenho
            , cgm
            , razao_social
            , funcao_programatica
            , recurso
            , despesa
            , vl_empenhado
            , vl_liquidado
            , vl_a_liquidar
        FROM tmp_empenhos
       WHERE vl_empenhado <> ''0.00'' ';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN next reRegistro;
    END LOOP;

    DROP TABLE tmp_empenhos;

    RETURN;
END;
$$ language 'plpgsql';

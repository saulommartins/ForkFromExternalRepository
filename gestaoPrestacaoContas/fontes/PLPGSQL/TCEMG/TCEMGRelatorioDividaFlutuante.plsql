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
* $Revision: 27033 $
* $Name$
* $Author: cako $
* $Date: 2007-12-04 12:08:26 -0200 (Ter, 04 Dez 2007) $
*
* Casos de uso: uc-02.03.09
*/

/*
$Log$
Revision 1.7  2006/07/05 20:37:38  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION tcemg.relatorio_divida_flutuante_restos_pagar(varchar,varchar,varchar,varchar,varchar,varchar, varchar, varchar,varchar,varchar, varchar, varchar) RETURNS SETOF RECORD AS $$ 
DECLARE
    stExercicio             ALIAS FOR $1;
    stFiltro                ALIAS FOR $2;
    stDtInicial             ALIAS FOR $3;
    stDtFinal               ALIAS FOR $4;
    stCodEntidades          ALIAS FOR $5;
    stCodOrgao              ALIAS FOR $6;
    stCodUnidade            ALIAS FOR $7;
    stCodRecurso            ALIAS FOR $8;
    stDestinacaoRecurso     ALIAS FOR $9;
    stDetalhamento          ALIAS FOR $10;
    stCodElementoDispensa   ALIAS FOR $11;
    stSituacao              ALIAS FOR $12;
    stSql               VARCHAR   := '';
    stSqlExercicio      VARCHAR   := '';
    stExercicioAtual    VARCHAR   := '';
    reRegistro          RECORD;
    reReg               RECORD;

BEGIN

    stExercicioAtual := to_char(to_date(stDtInicial, 'dd/mm/yyyy'), 'yyyy');

    IF (LENGTH(stExercicio) <> 4) THEN    
        stSqlExercicio := 'SELECT DISTINCT exercicio FROM empenho.empenho WHERE empenho.exercicio <> ' || quote_literal(stExercicioAtual) || ' ';
    ELSE
        stSqlExercicio := 'SELECT DISTINCT exercicio FROM empenho.empenho WHERE empenho.exercicio = ' || quote_literal(stExercicio) || ' ';
    END IF;

    CREATE TEMPORARY TABLE tmp_empenhos (
        entidade        INTEGER,
        empenho         integer,
        exercicio       CHAR(4),
        cgm             INTEGER,
        razao_social    VARCHAR,
        cod_nota        INTEGER,
        valor           NUMERIC(14,2),
        data            TEXT
    );

    FOR reReg IN EXECUTE stSqlExercicio
    LOOP

    stSql := ' INSERT INTO tmp_empenhos
                SELECT entidade
                     , empenho
                     , exercicio
                     , cgm
                     , razao_social
                     , cod_nota
                     , valor
                     , data 
                  FROM ( SELECT e.cod_entidade as entidade
                              , e.cod_empenho as empenho
                              , e.exercicio as exercicio
                              , pe.cgm_beneficiario as cgm
                              , cgm.nom_cgm as razao_social
            ';

        if (stSituacao = '1') then
            stSql := stSql || ', to_char(ea.timestamp::date,''dd/mm/yyyy'') as data 
                                , 0 as cod_nota, sum(eai.vl_anulado) as valor';
        end if;

        if (stSituacao = '2') then
                stSql := stSql || ', to_char(nl.dt_liquidacao,''dd/mm/yyyy'') as data 
                                    , nli.cod_nota as cod_nota, sum(nli.vl_total) as valor';
        end if;

        if (stSituacao = '3') then
                stSql := stSql || ', to_char(nlia.timestamp::date,''dd/mm/yyyy'') as data 
                                    , nlia.cod_nota as cod_nota, sum(nlia.vl_anulado) as valor';
        end if;

    stSql := stSql || '
        FROM empenho.empenho     as e';

            if (stSituacao = '1') then
                stSql := stSql || '
                   , empenho.empenho_anulado ea
                   , empenho.empenho_anulado_item eai
                ';
            end if;

            if (stSituacao = '2') then
                stSql := stSql || '
                , empenho.nota_liquidacao nl
                , empenho.nota_liquidacao_item nli
                ';
            end if;

            if (stSituacao = '3') then
                stSql := stSql || '
                , empenho.nota_liquidacao nl
                , empenho.nota_liquidacao_item nli
                , empenho.nota_liquidacao_item_anulado nlia
                ';
            end if;

            stSql := stSql || '
                , sw_cgm              as cgm
                , empenho.pre_empenho as pe
  LEFT OUTER JOIN empenho.restos_pre_empenho as rpe
                ON pe.exercicio = rpe.exercicio
                AND pe.cod_pre_empenho = rpe.cod_pre_empenho
  LEFT OUTER JOIN ( SELECT ped.exercicio
                         , ped.cod_pre_empenho
                         , d.num_orgao
                         , d.num_unidade
                         , d.cod_recurso
                         , r.masc_recurso_red
                         , r.cod_detalhamento
                         , cd.cod_estrutural
                      FROM empenho.pre_empenho_despesa as ped
                         , orcamento.despesa as d
                      JOIN orcamento.recurso(''' || reReg.exercicio || ''') as r
                        ON ( r.cod_recurso = d.cod_recurso AND r.exercicio = d.exercicio ) 
                         , orcamento.conta_despesa as cd
                     WHERE ped.cod_despesa = d.cod_despesa 
                       AND ped.exercicio = d.exercicio 
                       AND ped.cod_conta = cd.cod_conta 
                       AND ped.exercicio = cd.exercicio
                  ) as ped_d_cd 
               ON pe.exercicio = ped_d_cd.exercicio 
              AND pe.cod_pre_empenho = ped_d_cd.cod_pre_empenho
            WHERE e.exercicio         = ' || quote_literal(reReg.exercicio) || '
              AND e.exercicio         = pe.exercicio
              AND e.cod_pre_empenho   = pe.cod_pre_empenho
              AND e.cod_entidade      IN (' || stCodEntidades || ')
              AND pe.cgm_beneficiario = cgm.numcgm ';

            if (stSituacao = '1') then
                stSql := stSql || '
                    AND to_date(to_char(ea.timestamp::date,''dd/mm/yyyy''),''dd/mm/yyyy'') BETWEEN to_date(''' || stDtInicial || ''',''dd/mm/yyyy'') AND to_date(''' || stDtFinal || ''',''dd/mm/yyyy'')

                    AND ea.exercicio = e.exercicio
                    AND ea.cod_empenho = e.cod_empenho
                    AND ea.cod_entidade = e.cod_entidade

                    AND ea.exercicio = eai.exercicio
                    AND ea.cod_entidade = eai.cod_entidade
                    AND ea.cod_empenho = eai.cod_empenho
                    AND ea.timestamp = eai.timestamp
                ';
            end if;

            if (stSituacao = '2') then
                stSql := stSql || '
                    --Ligação EMPENHO : NOTA LIQUIDAÇÃO
                    AND e.exercicio = nl.exercicio_empenho
                    AND e.cod_entidade = nl.cod_entidade
                    AND e.cod_empenho = nl.cod_empenho

                    --Ligação NOTA LIQUIDAÇÃO : NOTA LIQUIDAÇÃO ITEM
                    AND nl.dt_liquidacao BETWEEN to_date(''' || stDtInicial || ''',''dd/mm/yyyy'') AND to_date(''' || stDtFinal || ''',''dd/mm/yyyy'')
                    AND nl.exercicio = nli.exercicio
                    AND nl.cod_nota = nli.cod_nota
                    AND nl.cod_entidade = nli.cod_entidade
                ';
            end if;

            if (stSituacao = '3') then
                stSql := stSql || '
                    --Ligação EMPENHO : NOTA LIQUIDAÇÃO
                    AND e.exercicio = nl.exercicio_empenho
                    AND e.cod_entidade = nl.cod_entidade
                    AND e.cod_empenho = nl.cod_empenho

                    --Ligação NOTA LIQUIDAÇÃO : NOTA LIQUIDAÇÃO ITEM
                    AND to_date(to_char(nlia.timestamp::date,''dd/mm/yyyy''),''dd/mm/yyyy'') BETWEEN to_date(''' || stDtInicial || ''',''dd/mm/yyyy'') AND to_date(''' || stDtFinal || ''',''dd/mm/yyyy'')

                    AND nl.exercicio = nli.exercicio
                    AND nl.cod_nota = nli.cod_nota
                    AND nl.cod_entidade = nli.cod_entidade

                    --Ligação NOTA LIQUIDAÇÃO ITEM : NOTA LIQUIDAÇÃO ITEM ANULADO
                    AND nli.exercicio = nlia.exercicio
                    AND nli.cod_nota = nlia.cod_nota
                    AND nli.cod_entidade = nlia.cod_entidade
                    AND nli.num_item = nlia.num_item
                    AND nli.cod_pre_empenho = nlia.cod_pre_empenho
                    AND nli.exercicio_item = nlia.exercicio_item
                ';
            end if;

            if (stCodOrgao is not null and stCodOrgao <> '') then
                stSql := stSql || ' AND CASE WHEN pe.implantado = true THEN rpe.num_orgao = '|| stCodOrgao ||' ELSE ped_d_cd.num_orgao = '|| stCodOrgao ||' END ';
            end if;

            if (stCodUnidade is not null and stCodUnidade <> '') then
                stSql := stSql || ' AND CASE WHEN pe.implantado = true THEN rpe.num_unidade = '|| stCodUnidade ||' ELSE ped_d_cd.num_unidade = '|| stCodUnidade ||' END ';
            end if;

            if (stCodRecurso is not null and stCodRecurso <> '') then
                stSql := stSql || ' AND CASE WHEN pe.implantado = true THEN rpe.recurso = '|| stCodRecurso ||' ELSE ped_d_cd.cod_recurso = '|| stCodRecurso ||' END ';
            end if;

            if (stDestinacaoRecurso is not null and stDestinacaoRecurso <> '') then
                stSql := stSql || ' AND ped_d_cd.masc_recurso_red = '''|| stDestinacaoRecurso ||''' ';
            end if;

            if (stDetalhamento is not null and stDetalhamento <> '') then
                stSql := stSql || ' AND ped_d_cd.cod_detalhamento = '|| stDetalhamento;
            end if;

            if (stCodElementoDispensa is not null and stCodElementoDispensa <> '') then
                stSql := stSql || ' AND CASE WHEN pe.implantado = true THEN rpe.cod_estrutural like rtrim(''' || stCodElementoDispensa || ''',''0'') || ''%'' ELSE ped_d_cd.cod_estrutural like publico.fn_mascarareduzida(empenho.fn_mascara_restos(''9.9.9.9.99.99.99.99.99'',''' || stCodElementoDispensa || ''')) || ''%'' END ';
            end if;

            stSql := stSql || ' GROUP BY ';
            if (stSituacao = '1') then
                stSql := stSql || 'ea.timestamp, ea.timestamp,';
            end if;

            if (stSituacao = '2') then
                    stSql := stSql || 'nl.dt_liquidacao, nli.cod_nota,';
            end if;

            if (stSituacao = '3') then
                    stSql := stSql || 'nlia.timestamp, nlia.cod_nota,';
            end if;

            stSql := stSql || ' e.cod_entidade, e.cod_empenho , e.exercicio ,pe.cgm_beneficiario, cgm.nom_cgm  ORDER BY ';

            if (stSituacao = '1') then
                stSql := stSql || 'ea.timestamp,';
            end if;

            if (stSituacao = '2') then
                stSql := stSql || 'nl.dt_liquidacao,';
            end if;

            if (stSituacao = '3') then
                stSql := stSql || 'nlia.timestamp,';
            end if;

            stSql := stSql || 'e.cod_entidade , e.cod_empenho , e.exercicio, ';

            if (stSituacao = '1') then
                stSql := stSql || ' ea.timestamp,';
            end if;

            if (stSituacao = '2') then
                stSql := stSql || ' nli.cod_nota,';
            end if;

            if (stSituacao = '3') then
                stSql := stSql || ' nlia.cod_nota,';
            end if;

        stSql := stSql || 'pe.cgm_beneficiario, cgm.nom_cgm ) as tmp WHERE tmp.valor > 0  ';

        EXECUTE stSql;

    END LOOP;

    stSql := ' SELECT entidade
                     , empenho
                     , exercicio
                     , cgm
                     , razao_social
                     , cod_nota
                     , valor
                     , data
                  FROM tmp_empenhos ';


    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN next reRegistro;
    END LOOP;

    DROP TABLE tmp_empenhos;

    RETURN;
END;
$$ LANGUAGE 'plpgsql';

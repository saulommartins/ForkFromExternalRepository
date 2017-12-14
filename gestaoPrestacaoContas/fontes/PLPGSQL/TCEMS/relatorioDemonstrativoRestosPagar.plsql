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
--                                                                           EXERCICIO           DTINICIAL          DTFINAL           ENTIDADES       ORDENACAO
CREATE OR REPLACE FUNCTION tcems.fn_relatorio_demonstrativo_restos_pagar( character varying, character varying, character varying, character varying, character varying ) RETURNS SETOF RECORD AS $$
DECLARE
        stExercicio         ALIAS FOR $1;
        stDtInicial         ALIAS FOR $2;
        stDtFinal           ALIAS FOR $3;
        stEntidades         ALIAS FOR $4;
        stCodOrdenacao      ALIAS FOR $5;

        stDataFinalAnterior VARCHAR;
        reRecord            RECORD;
        stSql               VARCHAR := ''; 

BEGIN

        stDataFinalAnterior := '31/12/'||(stExercicio::integer - 1)::varchar;

        stSql := '
           SELECT
                   funcional as funcional,
                   elemento as elemento,
                   valor_liquidado::numeric(14,2) as valor_liquidado,
                   valor_anulado_periodo::numeric(14,2) as cancelamento_periodo,
                   valor_empenhado::numeric(14,2) as valor_empenhado,
                   numero_empenho as numero_empenho,
                   fonte_classificacao_orcamentaria as fonte_classificacao_orcamentaria,
                   tipo_empenho as tipo_empenho,
                   descricao as descricao,
                   exercicio as exercicio_empenho,
                   (valor_empenhado - valor_anulado - valor_liquidado)::numeric(14,2) as valor_nao_liquidado,
                   codigo::varchar as codigo,
                   unidade_gestora::varchar as unidade_gestora
           FROM
                   (SELECT
                           publico.fn_mascara_dinamica( ''99.999.99.999.9999.9999'', (ped_d_cd.num_orgao ||''.''||  ped_d_cd.num_unidade  ||''.''|| ped_d_cd.cod_funcao   ||''.''|| ped_d_cd.cod_subfuncao||''.''|| ped_d_cd.cod_programa ||''.''|| ped_d_cd.num_pao ) )::varchar as funcional,
                           REPLACE(ped_d_cd.cod_estrutural,''.'', '''')::varchar as elemento,

                           (empenho.fn_empenho_liquidado( e.exercicio ,e.cod_empenho , e.cod_entidade, '''||stDtInicial||''', '''||stDtFinal||''' ) - empenho.fn_empenho_estorno_liquidacao( e.exercicio ,e.cod_empenho ,e.cod_entidade , '''||stDtInicial||''', '''||stDtFinal||'''  )) as valor_liquidado,
                           empenho.fn_empenho_anulado( e.exercicio ,e.cod_empenho , e.cod_entidade, '''||stDtInicial||''', '''||stDtFinal||''' ) as valor_anulado_periodo,
                           e.cod_empenho::varchar AS numero_empenho,
                           empenho.fn_empenho_empenhado(e.exercicio ,e.cod_empenho, e.cod_entidade, NULL, '''||stDataFinalAnterior||''') as valor_empenhado,
                           LPAD(ped_d_cd.cod_recurso,2,0)::varchar as fonte_classificacao_orcamentaria,
                           CASE WHEN pe.cod_tipo = 1 THEN
                                   ''O''::varchar
                                WHEN pe.cod_tipo = 2 THEN
                                   ''G''::varchar
                                WHEN pe.cod_tipo = 3 THEN
                                   ''E''::varchar
                           ELSE
                              ''''::varchar
                           END AS tipo_empenho,
                           REPLACE(ped_d_cd.descricao, ''\\n'', '''')::varchar AS descricao,
                           e.exercicio::varchar AS exercicio,
                           LPAD(ped_d_cd.num_orgao,2,0)::varchar||''.''||LPAD(ped_d_cd.num_unidade,3,0)::varchar AS codigo,
                           ped_d_cd.nom_orgao||''/''||ped_d_cd.nom_unidade AS unidade_gestora,
                           (empenho.fn_empenho_pago( e.exercicio ,e.cod_empenho , e.cod_entidade, NULL, '''||stDtFinal||''' ) - empenho.fn_empenho_estornado( e.exercicio ,e.cod_empenho , e.cod_entidade, NULL, '''||stDtFinal||''' )) AS valor_pago,
                           empenho.fn_empenho_anulado( e.exercicio ,e.cod_empenho , e.cod_entidade, NULL, '''||stDtFinal||''' ) as valor_anulado
           
                     FROM
                           empenho.empenho AS e,
                           empenho.pre_empenho AS pe
                           LEFT OUTER JOIN empenho.restos_pre_empenho AS rpe ON
                               pe.exercicio        = rpe.exercicio AND
                               pe.cod_pre_empenho  = rpe.cod_pre_empenho
                           LEFT  JOIN (
                               SELECT
                                   ped.exercicio,
                                   ped.cod_pre_empenho,
                                   d.num_orgao,
                                   d.num_unidade,
                                   u.nom_unidade,
                                   o.nom_orgao,
                                   d.cod_recurso,
                                   d.cod_programa,
                                   d.num_pao,
                                   cd.cod_estrutural,
                                   cd.descricao,
                                   d.cod_funcao,
                                   d.cod_subfuncao,
                                   rec.masc_recurso_red,
                                   rec.cod_detalhamento
                               FROM
                                   empenho.pre_empenho_despesa AS ped,
                                   orcamento.despesa           AS d
                                   JOIN orcamento.unidade      AS u
                                   ON  d.num_unidade   = u.num_unidade
                                   AND d.num_orgao     = u.num_orgao
                                   AND d.exercicio     = u.exercicio
                                   JOIN orcamento.orgao        AS o
                                   ON  u.num_orgao     = o.num_orgao
                                   AND u.exercicio     = o.exercicio
                                   JOIN orcamento.recurso('''||(stExercicio::integer - 1)::varchar||''') AS rec
                                   ON ( rec.exercicio = d.exercicio
                                        AND rec.cod_recurso = d.cod_recurso ),
                                   orcamento.conta_despesa     AS cd
                               WHERE
                                   ped.cod_despesa = d.cod_despesa AND
                                   ped.exercicio   = d.exercicio   AND
                                   ped.cod_conta   = cd.cod_conta  AND
                                   ped.exercicio   = cd.exercicio
                           ) AS ped_d_cd ON
                           pe.exercicio       = ped_d_cd.exercicio AND
                           pe.cod_pre_empenho = ped_d_cd.cod_pre_empenho
                           WHERE e.exercicio = pe.exercicio
                           AND e.cod_pre_empenho = pe.cod_pre_empenho
                           AND e.dt_empenho <= to_date('''||stDataFinalAnterior||''', ''DD/MM/YYYY'')
                           ';
        
        IF stEntidades <> '' THEN
                stSql := stSql || ' AND e.cod_entidade in ('||stEntidades||') ';
        END IF;

        stSql := stSql || '
                           GROUP BY ped_d_cd.num_orgao
                                  , ped_d_cd.num_unidade
                                  , ped_d_cd.cod_funcao
                                  , ped_d_cd.cod_subfuncao
                                  , ped_d_cd.cod_programa
                                  , ped_d_cd.num_pao
                                  , ped_d_cd.cod_estrutural
                                  , e.exercicio
                                  , e.cod_empenho
                                  , e.cod_entidade
                                  , e.dt_vencimento
                                  , ped_d_cd.cod_recurso
                                  , pe.cod_tipo
                                  , ped_d_cd.descricao
                                  , ped_d_cd.nom_orgao
                                  , ped_d_cd.nom_unidade
           ';
        IF stCodOrdenacao = 1 THEN
                stSql := stSql || ' ORDER BY e.cod_empenho ';
        END IF;

        IF stCodOrdenacao = 2 THEN
                stSql := stSql || ' ORDER BY e.dt_vencimento ';
        END IF;

        IF stCodOrdenacao = 3 THEN
                stSql := stSql || ' ORDER BY ped_d_cd.cod_recurso ';
        END IF;

        stSql := stSql || '
                ) AS tabela
           WHERE valor_empenhado <> 0.00
           AND valor_empenhado > (valor_pago + valor_anulado)
        ';

        FOR reRecord IN EXECUTE stSql
        LOOP
                RETURN NEXT reRecord;
        END LOOP;

        RETURN;
END;

$$ language 'plpgsql';

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
CREATE OR REPLACE FUNCTION orcamento.fn_relatorioSIOPSResumoRecursos( VARCHAR,VARCHAR,VARCHAR,VARCHAR,VARCHAR ) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio     ALIAS FOR $1;
    stDataInicial   ALIAS FOR $2;
    stDataFinal     ALIAS FOR $3;
    stCodEntidades  ALIAS FOR $4;
    stCodOrgao      ALIAS FOR $5;

    stCondicao      VARCHAR := '';
    reRegistro      RECORD ;
    stSql           VARCHAR := '';

BEGIN

    stSql := '
            SELECT exercicio::VARCHAR
                 , cod_recurso
                 , cod_fonte
                 , nom_recurso
                 , SUM(vl_empenhado)::NUMERIC AS vl_empenhado
                 , SUM(vl_liquidado)::NUMERIC AS vl_liquidado
                 , SUM(vl_pago)::NUMERIC AS vl_pago
              FROM (
                        SELECT recurso.*
                             , COALESCE((SELECT * FROM orcamento.fn_recurso_despesa_empenhada( recurso.cod_recurso, '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(stDataInicial) || ',' || quote_literal(stDataFinal) ||', '|| quote_literal(stCodOrgao) ||' )), ' || quote_literal('0.00') || ') AS vl_empenhado
                             , COALESCE((SELECT * FROM orcamento.fn_recurso_despesa_liquidada( recurso.cod_recurso, '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(stDataInicial) || ',' || quote_literal(stDataFinal) ||', '|| quote_literal(stCodOrgao) ||' )), ' || quote_literal('0.00') || ') AS vl_liquidado
                             , COALESCE((SELECT * FROM orcamento.fn_recurso_despesa_paga( recurso.cod_recurso,      '|| quote_literal(stExercicio) ||', '|| quote_literal(stCodEntidades) ||', '|| quote_literal(stDataInicial) || ',' || quote_literal(stDataFinal) ||', '|| quote_literal(stCodOrgao) ||' )), ' || quote_literal('0.00') || ') AS vl_pago
                          FROM orcamento.recurso
                    INNER JOIN orcamento.despesa
                            ON recurso.cod_recurso = despesa.cod_recurso
                           AND recurso.exercicio = despesa.exercicio
                    INNER JOIN orcamento.conta_despesa
                            ON despesa.cod_conta = conta_despesa.cod_conta
                           AND despesa.exercicio = conta_despesa.exercicio
                         WHERE recurso.exercicio = '|| quote_literal(stExercicio) ||'
                           AND despesa.num_orgao IN ('||stCodOrgao||')
                           AND despesa.cod_entidade IN ('||stCodEntidades||')
                      GROUP BY recurso.exercicio
                             , recurso.cod_recurso
                             , recurso.cod_fonte
                             , recurso.nom_recurso
                        ) as tabela
              WHERE ( vl_empenhado       <> ' || quote_literal('0.00') || ' 
                 OR   vl_liquidado       <> ' || quote_literal('0.00') || '
                 OR   vl_pago            <> ' || quote_literal('0.00') || ' )
            GROUP BY exercicio
                   , cod_recurso
                   , cod_fonte
                   , nom_recurso
            ORDER BY cod_recurso
                   , cod_fonte
    ';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN next reRegistro;
    END LOOP;

    RETURN;

END;

$$language 'plpgsql';

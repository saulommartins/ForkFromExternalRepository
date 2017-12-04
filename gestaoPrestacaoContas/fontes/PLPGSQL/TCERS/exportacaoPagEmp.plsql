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
* $Revision: 59612 $
* $Name$
* $Author: gelson $
* $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $
*
* Casos de uso: uc-02.08.08
* Casos de uso: uc-02.08.08
*/

/*
$Log$
Revision 1.1  2007/09/26 14:43:57  gris
-- O módulo TCE-SC  funcionalidades deverá ir para a gestãp estação de contas.

Revision 1.9  2006/07/18 13:03:52  cako
Bug #6149#

Revision 1.8  2006/07/05 20:37:45  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION tcerj.fn_exportacao_pagemp(varchar,varchar,varchar,varchar) RETURNS SETOF RECORD AS '
DECLARE
    stExercicio                 ALIAS FOR $1;
    stCodEntidades              ALIAS FOR $2;
    stDtInicial                 ALIAS FOR $3;
    stDtFinal                   ALIAS FOR $4;
    stSql               VARCHAR   := '''';
    reRegistro          RECORD;

BEGIN

stSql := ''CREATE TEMPORARY TABLE tmp_pago AS (
    SELECT
        p.cod_entidade as cod_entidade,
        p.cod_nota as cod_nota,
        p.exercicio_liquidacao as exercicio_liquidacao,
        p.timestamp as timestamp,
        pc.cod_estrutural as cod_estrutural,
        pb.cod_banco as cod_banco,
        pb.cod_agencia as cod_agencia,
        pb.conta_corrente as conta_corrente
    FROM
        contabilidade.pagamento p,
        contabilidade.lancamento_empenho le,
        contabilidade.conta_credito cc,
        contabilidade.plano_analitica pa,
        contabilidade.plano_banco pb,
        contabilidade.plano_conta pc
    WHERE
        --Ligação PAGAMENTO : LANCAMENTO EMPENHO
            p.cod_entidade      IN ('' || stCodEntidades || '')
        AND p.cod_lote = le.cod_lote
        AND p.tipo = le.tipo
        AND p.sequencia = le.sequencia
        AND p.exercicio = le.exercicio
        AND p.cod_entidade = le.cod_entidade
        AND le.estorno = false

        --Ligação LANCAMENTO EMPENHO : CONTA_CREDITO
        AND le.cod_lote = cc.cod_lote
        AND le.tipo = cc.tipo
        AND le.exercicio = cc.exercicio
        AND le.cod_entidade = cc.cod_entidade

        --Ligação CONTA_CREDITO : PLANO ANALITICA
        AND cc.cod_plano = pa.cod_plano
        AND cc.exercicio = pa.exercicio
        AND cc.sequencia = 2

       --Ligação PLANO ANALITICA : PLANO CONTA
        AND pa.cod_conta = pc.cod_conta
        AND pa.exercicio = pc.exercicio

       --Ligação PLANO ANALITICA : PLANO BANCO
        AND pa.cod_plano = pb.cod_plano
        AND pa.exercicio = pb.exercicio
)'';
EXECUTE stSql;


stSql := ''

    SELECT *
    FROM (
        SELECT
            ped_d_cd2.num_unidade    as num_unidade,
            e.exercicio             as exercicio,
            e.cod_entidade          as entidade,
            e.cod_empenho           as empenho,
            to_char(nlpa.timestamp_anulada,''''dd/mm/yyyy'''') as stData ,
            to_char(nlpa.timestamp_anulada,''''yyyy'''') as dtAno ,
            (sum(nlpa.vl_anulado)*-1)        as valor,
            replace(tmp.cod_estrutural,''''.'''','''''''') as cod_estrutural,
            tmp.cod_banco           as cod_banco,
            tmp.cod_agencia         as cod_agencia,
            tmp.conta_corrente      as conta_corrente,
            to_char(nlpa.timestamp_anulada,''''yyyymm'''') as dtCompetencia ,
            ped_d_cd2.num_orgao    as num_orgao
        FROM
            empenho.empenho                 as e
          , empenho.nota_liquidacao         as nl
          , empenho.nota_liquidacao_paga    as nlp
            LEFT OUTER JOIN
                tmp_pago                    as tmp
            ON
                --Ligação NOTA LIQUIDAÇÃO PAGA : PAGAMENTO
                    nlp.cod_entidade = tmp.cod_entidade
                AND nlp.cod_nota = tmp.cod_nota
                AND nlp.exercicio = tmp.exercicio_liquidacao
                AND nlp.timestamp = tmp.timestamp
          , empenho.pre_empenho             as pe
            LEFT OUTER JOIN (
                SELECT
                    ped.exercicio,
                    ped.cod_pre_empenho,
                    d.num_orgao,d.num_unidade,
                    cd.cod_estrutural
                FROM
                    empenho.pre_empenho_despesa as ped,
                    orcamento.despesa           as d,
                    orcamento.conta_despesa     as cd
                WHERE
                    ped.cod_despesa = d.cod_despesa and
                    ped.exercicio = d.exercicio     and
                    ped.cod_conta = cd.cod_conta    and
                    d.exercicio = cd.exercicio
            ) as ped_d_cd2 ON (
                    pe.exercicio = ped_d_cd2.exercicio AND
                    pe.cod_pre_empenho = ped_d_cd2.cod_pre_empenho )

           , empenho.nota_liquidacao_paga_anulada as nlpa

        WHERE
                e.exercicio         = '''''' || stExercicio || ''''''
            AND e.exercicio         = pe.exercicio
            AND e.cod_pre_empenho   = pe.cod_pre_empenho
            AND e.cod_entidade      IN ('' || stCodEntidades || '')

            --Ligação EMPENHO : NOTA LIQUIDAÇÃO
            AND e.exercicio = nl.exercicio_empenho
            AND e.cod_entidade = nl.cod_entidade
            AND e.cod_empenho = nl.cod_empenho

            --Ligação NOTA LIQUIDAÇÃO : NOTA LIQUIDAÇÃO PAGA
            AND nl.exercicio = nlp.exercicio
            AND nl.cod_nota = nlp.cod_nota
            AND nl.cod_entidade = nlp.cod_entidade

            --Ligação NOTA LIQUIDAÇÃO PAGA : NOTA LIQUIDACAO PAGA ANULADA (ESTORNO)
            AND nlp.cod_entidade = nlpa.cod_entidade
            AND nlp.cod_nota = nlpa.cod_nota
            AND nlp.exercicio = nlpa.exercicio
            AND nlp.timestamp = nlpa.timestamp            
            AND to_date(to_char(nlpa.timestamp_anulada,''''dd/mm/yyyy''''),''''dd/mm/yyyy'''') BETWEEN to_date('''''' || stDtInicial || '''''',''''dd/mm/yyyy'''') AND to_date('''''' || stDtFinal || '''''',''''dd/mm/yyyy'''')

        GROUP BY
            ped_d_cd2.num_unidade,
            e.exercicio,
            e.cod_entidade,
            e.cod_empenho,
            to_char(nlpa.timestamp_anulada,''''dd/mm/yyyy''''),
            to_char(nlpa.timestamp_anulada,''''yyyy''''),
            replace(tmp.cod_estrutural,''''.'''',''''''''),
            tmp.cod_banco,
            tmp.cod_agencia,
            tmp.conta_corrente,
            to_char(nlpa.timestamp_anulada,''''yyyymm''''),
            ped_d_cd2.num_orgao

        UNION

        SELECT
            ped_d_cd.num_unidade    as num_unidade,
            e.exercicio             as exercicio,
            e.cod_entidade          as entidade,
            e.cod_empenho           as empenho,
            to_char(nlp.timestamp,''''dd/mm/yyyy'''') as stData ,
            to_char(nlp.timestamp,''''yyyy'''') as dtAno ,
            sum(nlp.vl_pago)        as valor,
            replace(tmp.cod_estrutural,''''.'''','''''''') as cod_estrutural,
            tmp.cod_banco           as cod_banco,
            tmp.cod_agencia         as cod_agencia,
            tmp.conta_corrente      as conta_corrente,
            to_char(nlp.timestamp,''''yyyymm'''') as dtCompetencia ,
            ped_d_cd.num_orgao    as num_orgao

        FROM
            empenho.empenho                 as e
          , empenho.nota_liquidacao         as nl
          , empenho.nota_liquidacao_paga    as nlp
            LEFT OUTER JOIN
                tmp_pago                    as tmp
            ON
                --Ligação NOTA LIQUIDAÇÃO PAGA : PAGAMENTO
                    nlp.cod_entidade = tmp.cod_entidade
                AND nlp.cod_nota = tmp.cod_nota
                AND nlp.exercicio = tmp.exercicio_liquidacao
                AND nlp.timestamp = tmp.timestamp
          , empenho.pre_empenho             as pe
            LEFT OUTER JOIN (
                SELECT
                    ped.exercicio, 
                    ped.cod_pre_empenho, 
                    d.num_orgao,d.num_unidade, 
                    cd.cod_estrutural
                FROM
                    empenho.pre_empenho_despesa as ped, 
                    orcamento.despesa           as d,
                    orcamento.conta_despesa     as cd
                WHERE
                    ped.cod_despesa = d.cod_despesa and 
                    ped.exercicio = d.exercicio     and 
                    ped.cod_conta = cd.cod_conta    and 
                    d.exercicio = cd.exercicio
            ) as ped_d_cd ON (
                    pe.exercicio = ped_d_cd.exercicio AND 
                    pe.cod_pre_empenho = ped_d_cd.cod_pre_empenho )

        WHERE
                e.exercicio         = '''''' || stExercicio || ''''''
            AND e.exercicio         = pe.exercicio
            AND e.cod_pre_empenho   = pe.cod_pre_empenho
            AND e.cod_entidade      IN ('' || stCodEntidades || '')

            --Ligação EMPENHO : NOTA LIQUIDAÇÃO
            AND e.exercicio = nl.exercicio_empenho
            AND e.cod_entidade = nl.cod_entidade
            AND e.cod_empenho = nl.cod_empenho

            --Ligação NOTA LIQUIDAÇÃO : NOTA LIQUIDAÇÃO PAGA
            AND nl.exercicio = nlp.exercicio
            AND nl.cod_nota = nlp.cod_nota
            AND nl.cod_entidade = nlp.cod_entidade
            AND to_date(to_char(nlp.timestamp,''''dd/mm/yyyy''''),''''dd/mm/yyyy'''') BETWEEN to_date('''''' || stDtInicial || '''''',''''dd/mm/yyyy'''') AND to_date('''''' || stDtFinal || '''''',''''dd/mm/yyyy'''')

        GROUP BY
            ped_d_cd.num_unidade,
            e.exercicio,
            e.cod_entidade,
            e.cod_empenho,
            to_char(nlp.timestamp,''''dd/mm/yyyy''''),
            to_char(nlp.timestamp,''''yyyy''''),
            replace(tmp.cod_estrutural,''''.'''',''''''''),
            tmp.cod_banco,
            tmp.cod_agencia,
            tmp.conta_corrente,
            to_char(nlp.timestamp,''''yyyymm''''),
            ped_d_cd.num_orgao
 
) as tabela
  ORDER BY
            num_unidade,
            exercicio,
            entidade,
            empenho,
            stData,
            dtAno,
            cod_estrutural,
            cod_banco,
            cod_agencia,
            conta_corrente,
            dtCompetencia,
            num_orgao ''; 

    FOR reRegistro IN EXECUTE stSql
    LOOP

        RETURN next reRegistro;
    END LOOP;

    DROP TABLE tmp_pago;

    RETURN;
END;
'language 'plpgsql';

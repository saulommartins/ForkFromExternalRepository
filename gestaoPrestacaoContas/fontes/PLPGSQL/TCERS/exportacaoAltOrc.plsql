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
* Casos de uso: uc-02.08.08, uc-02.08.13
*/

/*
$Log$
Revision 1.1  2007/09/26 14:43:57  gris
-- O módulo TCE-SC  funcionalidades deverá ir para a gestãp estação de contas.

Revision 1.12  2006/07/05 20:37:44  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION tcerj.fn_exportacao_altorc(varchar,varchar,varchar,varchar) RETURNS SETOF RECORD AS '
DECLARE
    stExercicio             ALIAS FOR $1;
    stCodEntidades          ALIAS FOR $2;
    stDtInicial             ALIAS FOR $3;
    stDtFinal               ALIAS FOR $4;

    stSql                   VARCHAR   := '''';
    reRegistro              RECORD;
BEGIN

CREATE TEMPORARY TABLE tmp_suplementada AS (
    SELECT
        s.cod_suplementacao,
        s.exercicio,
        s.cod_tipo,
        s.cod_norma,
        ss.cod_despesa,
        ss.valor as valor_suplementado,
        0 as valor_reduzido,
        cast(''S'' as varchar) as tipo
    FROM
        orcamento.suplementacao as s,
        orcamento.suplementacao_suplementada  as ss
    WHERE
        s.exercicio = ss.exercicio AND
        s.cod_suplementacao = ss.cod_suplementacao AND
        s.exercicio = stExercicio
);


INSERT INTO tmp_suplementada (
    SELECT
        s.cod_suplementacao,
        s.exercicio,
        s.cod_tipo,
        s.cod_norma,
        sr.cod_despesa,
        0 as valor_suplementado,
        sr.valor as valor_reduzido,
        cast(''R'' as varchar) as tipo
    FROM
        orcamento.suplementacao         as s,
        orcamento.suplementacao_reducao as sr
    WHERE
        s.exercicio = sr.exercicio AND
        s.cod_suplementacao = sr.cod_suplementacao AND
        s.exercicio = stExercicio
);


stSql := ''
    SELECT
        d.num_pao           as num_pao,
        orcamento.fn_consulta_tipo_pao(cast('' || stExercicio || '' as varchar),d.num_pao),
        n.num_norma         as num_norma,
        n.exercicio         as exercicio_norma,
        d.num_unidade       as unidade,
        cast(substr(replace(ocd.cod_estrutural,''''.'''',''''''''),1,8) as integer),
        d.cod_recurso       as cod_recurso,
        d.cod_funcao        as cod_funcao,
        tmp.exercicio       as exercicio,
        d.cod_subfuncao     as cod_subfuncao,
        d.cod_programa      as cod_programa,
        to_char(n.dt_publicacao,''''dd/mm/yyyy'''')     as data_fundamento,
        d.num_orgao         as num_orgao,
        to_char(n.dt_publicacao,''''dd/mm/yyyy'''')     as data_publicacao,
        ta.cod_tipo_alteracao   as cod_tipo_alteracao,
        tf.cod_fundamento_legal as cod_fundamento_legal,
        to_char(lo.dt_lote,''''dd/mm/yyyy'''')          as dt_lote,
        CASE WHEN sum(tmp.valor_suplementado - tmp.valor_reduzido) < 0 THEN
            sum(tmp.valor_suplementado - tmp.valor_reduzido) * -1
        ELSE
            sum(tmp.valor_suplementado - tmp.valor_reduzido)
        END AS valor,
        CASE WHEN trim(n.descricao)='''''''' THEN
            ''''LEI AUTORIZATIVA''''
           ELSE
            substr(n.descricao,100)
        END as texto_da_lei,
        CASE WHEN n.cod_tipo_norma=1 THEN
            tcers.fn_retorno_atributo_normas(n.cod_tipo_norma,n.cod_norma,''''Número da Lei'''')
           ELSE
            CAST(n.num_norma as varchar)
        END as numero_da_lei,
        CASE WHEN n.cod_tipo_norma=1 THEN 
            tcers.fn_retorno_atributo_normas(n.cod_tipo_norma,n.cod_norma,''''Data da Lei'''')
           ELSE
            to_char(n.dt_publicacao,''''dd/mm/yyyy'''')
        END as data_da_lei
        
    FROM
        tmp_suplementada as tmp,
        orcamento.despesa as d
            LEFT OUTER JOIN
                orcamento.conta_despesa as ocd ON
                    ocd.exercicio = d.exercicio AND
                    ocd.cod_conta = d.cod_conta,
        normas.norma as n,
        normas.tipo_norma as tn,
        tcerj.fundamento as tf,
        contabilidade.tipo_transferencia as tt,
        tcerj.tipo_alteracao as ta,
        contabilidade.transferencia_despesa as td,
        contabilidade.lancamento_transferencia as lt,
        contabilidade.lancamento as l,
        contabilidade.lote as lo

    WHERE
        d.cod_entidade      IN ('' || stCodEntidades || '') AND
        d.exercicio         =   '' || stExercicio || '' AND

        tmp.exercicio       = d.exercicio AND
        tmp.cod_despesa     = d.cod_despesa AND

        tmp.cod_norma       = n.cod_norma AND

        n.cod_tipo_norma    = tn.cod_tipo_norma AND

        tn.cod_tipo_norma   = tf.cod_tipo_norma AND

        tmp.exercicio       = tt.exercicio AND
        tmp.cod_tipo        = tt.cod_tipo AND

        tt.cod_tipo         = ta.cod_tipo AND
        tt.exercicio        = ta.exercicio AND
        tmp.tipo            = ta.tipo     AND


        td.cod_suplementacao    = tmp.cod_suplementacao AND
        td.exercicio            = tmp.exercicio AND

        td.exercicio        = lt.exercicio AND
        td.cod_entidade     = lt.cod_entidade AND
        td.cod_tipo         = lt.cod_tipo AND
        td.sequencia        = lt.sequencia AND
        td.tipo             = lt.tipo AND
        td.cod_lote         = lt.cod_lote AND

        lt.exercicio        = l.exercicio AND
        lt.cod_entidade     = l.cod_entidade AND
        lt.sequencia        = l.sequencia AND
        lt.tipo             = l.tipo AND
        lt.cod_lote         = l.cod_lote AND

        l.exercicio         = lo.exercicio AND
        l.cod_lote          = lo.cod_lote AND
        l.cod_entidade      = lo.cod_entidade AND
        l.tipo              = lo.tipo AND

        lo.dt_lote          BETWEEN to_date(''''''|| stDtInicial ||'''''',''''dd/mm/yyyy'''') AND to_date(''''''|| stDtFinal ||'''''',''''dd/mm/yyyy'''')

    GROUP BY
        d.num_pao,
        orcamento.fn_consulta_tipo_pao(cast('' || stExercicio || '' as varchar),d.num_pao),
        n.num_norma,
        n.exercicio,
        d.num_unidade,
        cast(substr(replace(ocd.cod_estrutural,''''.'''',''''''''),1,8) as integer),
        d.cod_recurso,
        d.cod_funcao,
        tmp.exercicio,
        d.cod_subfuncao,
        d.cod_programa,
        n.dt_publicacao,
        d.num_orgao,
        n.dt_publicacao,
        ta.cod_tipo_alteracao,
        tf.cod_fundamento_legal,
        lo.dt_lote,
        n.cod_tipo_norma,
        n.descricao,
        n.num_norma,
        n.cod_norma,
        n.dt_publicacao
        
'';


    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN next reRegistro;
    END LOOP;
    DROP TABLE tmp_suplementada;
    RETURN;
END;

'language 'plpgsql';


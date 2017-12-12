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
*/

/*
$Log$
Revision 1.1  2007/09/26 14:43:57  gris
-- O módulo TCE-SC  funcionalidades deverá ir para a gestãp estação de contas.

Revision 1.10  2006/07/05 20:37:45  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION tcerj.fn_exportacao_movconta(varchar,varchar,varchar,varchar) RETURNS SETOF RECORD AS '
DECLARE
    stExercicio             ALIAS FOR $1;
    stCodEntidades          ALIAS FOR $2;
    stDtInicial             ALIAS FOR $3;
    stDtFinal               ALIAS FOR $4;
    stSql                   VARCHAR   := '''';
    reRegistro              RECORD;
    stCredito               VARCHAR   := '''';
    stDebito                VARCHAR   := '''';
BEGIN
stSql := ''
SELECT  exercicio,
        cod_estrutural,
        credito,
        debito
 FROM(
    SELECT
        pc.exercicio            as exercicio,
        replace(pc.cod_estrutural,''''.'''','''''''') as cod_estrutural,
        coalesce(sum(credito.valor),0.00) as credito,
        coalesce(sum(debito.valor),0.00)  as debito,
        coalesce(sum(credito_implantado.valor),0.00) as credito_implantado,
        coalesce(sum(debito_implantado.valor),0.00)  as debito_implantado
    FROM
        contabilidade.plano_conta as pc,
        contabilidade.plano_analitica as pa
            LEFT OUTER JOIN (
                SELECT
                    cc.cod_plano,
                    cc.exercicio,
                    sum(vl.vl_lancamento) as valor
                FROM
                    contabilidade.conta_credito cc,
                    contabilidade.valor_lancamento vl,
                    contabilidade.lancamento as l,
                    contabilidade.lote as lo
                WHERE
                    cc.cod_lote     = vl.cod_lote AND
                    cc.tipo         = vl.tipo AND
                    cc.sequencia    = vl.sequencia AND
                    cc.exercicio    = vl.exercicio AND
                    cc.tipo_valor   = vl.tipo_valor AND
                    cc.cod_entidade = vl.cod_entidade AND
                    cc.cod_entidade IN ('' || stCodEntidades || '') AND

                    vl.cod_lote     = l.cod_lote AND
                    vl.tipo         = l.tipo AND
                    vl.sequencia    = l.sequencia AND
                    vl.exercicio    = l.exercicio AND
                    vl.cod_entidade = l.cod_entidade AND
                    vl.tipo_valor   = ''''C'''' AND
                    vl.tipo         <> ''''I'''' AND

                    l.exercicio     = lo.exercicio AND
                    l.cod_lote      = lo.cod_lote AND
                    l.tipo          = lo.tipo AND
                    l.cod_entidade  = lo.cod_entidade AND
                    lo.dt_lote       BETWEEN to_date(''''''|| stDtInicial ||'''''',''''dd/mm/yyyy'''') AND to_date(''''''|| stDtFinal ||'''''',''''dd/mm/yyyy'''')

                GROUP BY
                    cc.cod_plano,
                    cc.exercicio
            ) as credito ON (
                pa.cod_plano = credito.cod_plano AND
                pa.exercicio = credito.exercicio
            )
            LEFT OUTER JOIN (
                SELECT
                    cd.cod_plano,
                    cd.exercicio,
                    sum(vl.vl_lancamento) as valor
                FROM
                    contabilidade.conta_debito cd,
                    contabilidade.valor_lancamento vl,
                    contabilidade.lancamento as l,
                    contabilidade.lote as lo
                WHERE
                    cd.cod_lote     = vl.cod_lote AND
                    cd.tipo         = vl.tipo AND
                    cd.sequencia    = vl.sequencia AND
                    cd.exercicio    = vl.exercicio AND
                    cd.tipo_valor   = vl.tipo_valor AND
                    cd.cod_entidade = vl.cod_entidade AND
                    cd.cod_entidade IN ('' || stCodEntidades || '') AND

                    vl.cod_lote     = l.cod_lote AND
                    vl.tipo         = l.tipo AND
                    vl.sequencia    = l.sequencia AND
                    vl.exercicio    = l.exercicio AND
                    vl.cod_entidade = l.cod_entidade AND
                    vl.tipo_valor   = ''''D'''' AND
                    vl.tipo         <> ''''I'''' AND

                    l.exercicio     = lo.exercicio AND
                    l.cod_lote      = lo.cod_lote AND
                    l.tipo          = lo.tipo AND
                    l.cod_entidade  = lo.cod_entidade AND
                    lo.dt_lote       BETWEEN to_date(''''''|| stDtInicial ||'''''',''''dd/mm/yyyy'''') AND to_date(''''''|| stDtFinal ||'''''',''''dd/mm/yyyy'''')

                GROUP BY
                    cd.cod_plano,
                    cd.exercicio
            ) as debito ON (
                pa.cod_plano = debito.cod_plano AND
                pa.exercicio = debito.exercicio
            )
             LEFT OUTER JOIN (
                SELECT
                    cc.cod_plano,
                    cc.exercicio,
                    sum(vl.vl_lancamento) as valor
                FROM
                    contabilidade.conta_credito cc,
                    contabilidade.valor_lancamento vl,
                    contabilidade.lancamento as l,
                    contabilidade.lote as lo
                WHERE
                    cc.cod_lote     = vl.cod_lote AND
                    cc.tipo         = vl.tipo AND
                    cc.sequencia    = vl.sequencia AND
                    cc.exercicio    = vl.exercicio AND
                    cc.tipo_valor   = vl.tipo_valor AND
                    cc.cod_entidade = vl.cod_entidade AND
                    cc.cod_entidade IN ('' || stCodEntidades || '') AND

                    vl.cod_lote     = l.cod_lote AND
                    vl.tipo         = l.tipo AND
                    vl.sequencia    = l.sequencia AND
                    vl.exercicio    = l.exercicio AND
                    vl.cod_entidade = l.cod_entidade AND
                    vl.tipo_valor   = ''''C'''' AND
                    vl.tipo         = ''''I'''' AND

                    l.exercicio     = lo.exercicio AND
                    l.cod_lote      = lo.cod_lote AND
                    l.tipo          = lo.tipo AND
                    l.cod_entidade  = lo.cod_entidade AND
                    lo.dt_lote       BETWEEN to_date(''''''|| stDtInicial ||'''''',''''dd/mm/yyyy'''') AND to_date(''''''|| stDtFinal ||'''''',''''dd/mm/yyyy'''')

                GROUP BY
                    cc.cod_plano,
                    cc.exercicio
            ) as credito_implantado ON (
                pa.cod_plano = credito_implantado.cod_plano AND
                pa.exercicio = credito_implantado.exercicio
            )
            LEFT OUTER JOIN (
                SELECT
                    cd.cod_plano,
                    cd.exercicio,
                    sum(vl.vl_lancamento) as valor
                FROM
                    contabilidade.conta_debito cd,
                    contabilidade.valor_lancamento vl,
                    contabilidade.lancamento as l,
                    contabilidade.lote as lo
                WHERE
                    cd.cod_lote     = vl.cod_lote AND
                    cd.tipo         = vl.tipo AND
                    cd.sequencia    = vl.sequencia AND
                    cd.exercicio    = vl.exercicio AND
                    cd.tipo_valor   = vl.tipo_valor AND
                    cd.cod_entidade = vl.cod_entidade AND
                    cd.cod_entidade IN ('' || stCodEntidades || '') AND

                    vl.cod_lote     = l.cod_lote AND
                    vl.tipo         = l.tipo AND
                    vl.sequencia    = l.sequencia AND
                    vl.exercicio    = l.exercicio AND
                    vl.cod_entidade = l.cod_entidade AND
                    vl.tipo_valor   = ''''D'''' AND
                    vl.tipo         = ''''I'''' AND

                    l.exercicio     = lo.exercicio AND
                    l.cod_lote      = lo.cod_lote AND
                    l.tipo          = lo.tipo AND
                    l.cod_entidade  = lo.cod_entidade AND
                    lo.dt_lote       BETWEEN to_date(''''''|| stDtInicial ||'''''',''''dd/mm/yyyy'''') AND to_date(''''''|| stDtFinal ||'''''',''''dd/mm/yyyy'''')

                GROUP BY
                    cd.cod_plano,
                    cd.exercicio
            ) as debito_implantado ON (
                pa.cod_plano = debito_implantado.cod_plano AND
                pa.exercicio = debito_implantado.exercicio
           )
          
    WHERE
        pc.exercicio    =   '''''' || stExercicio || '''''' AND
        pc.cod_conta    = pa.cod_conta AND
        pc.exercicio    = pa.exercicio        
    GROUP BY
        pc.exercicio,
        replace(pc.cod_estrutural,''''.'''','''''''')

    ORDER BY
        pc.exercicio,
        replace(pc.cod_estrutural,''''.'''','''''''')
 ) as tbl
 WHERE
 (credito_implantado    <> 0.00 OR debito_implantado    <> 0.00) 
 OR  (credito               <> 0.00 OR debito               <> 0.00)

'';


FOR reRegistro IN EXECUTE stSql
    LOOP
        reRegistro.credito := replace(reRegistro.credito,''-'','''');
        reRegistro.credito := replace(reRegistro.credito,''.'','''');

        reRegistro.debito := replace(reRegistro.debito,''-'','''');
        reRegistro.debito := replace(reRegistro.debito,''.'','''');
/*
        stCredito := cast( reRegistro.credito as varchar );
        IF ( substr( stCredito,length(stCredito)-2,1 ) = ''.'' ) THEN
            reRegistro.credito := replace(reRegistro.credito,''.'','''');
        ELSE
            reRegistro.credito := reRegistro.credito || 00;
        END IF;
        reRegistro.credito := replace(reRegistro.credito,''-'','''');
        reRegistro.credito := replace(reRegistro.credito,''.'','''');

        stDebito := cast( reRegistro.debito as varchar );
        IF ( substr( stDebito,length(stDebito)-2,1 ) = ''.'' ) THEN
            reRegistro.debito := replace(reRegistro.debito,''.'','''');
        ELSE
            reRegistro.debito := reRegistro.debito || 00;
        END IF;
        reRegistro.debito := replace(reRegistro.debito,''-'','''');
        reRegistro.debito := replace(reRegistro.debito,''.'','''');
  */
        RETURN next reRegistro;
    END LOOP;

    RETURN;
END;

'language 'plpgsql';


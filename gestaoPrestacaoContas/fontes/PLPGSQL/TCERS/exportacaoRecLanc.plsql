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

Revision 1.8  2006/07/05 20:37:45  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION tcerj.fn_exportacao_reclanc(varchar,varchar,varchar,varchar) RETURNS SETOF RECORD AS '
DECLARE
    stExercicio             ALIAS FOR $1;
    stCodEntidades          ALIAS FOR $2;
    stDtInicial             ALIAS FOR $3;
    stDtFinal               ALIAS FOR $4;

    stSql                   VARCHAR   := '''';
    reRegistro              RECORD;
BEGIN
stSql := ''
    SELECT
        cast(substr(replace(cr.cod_estrutural,''''.'''',''''''''),1,8) as integer) as cod_estrutural,
        CASE WHEN lr.estorno = true THEN
            2
        ELSE
            1
        END as estorno,
        sum(vl.vl_lancamento) AS valor
    FROM
        orcamento.conta_receita as cr,
        orcamento.receita as r,
        contabilidade.lancamento_receita as lr,
        contabilidade.lancamento as l,
        contabilidade.lote as lo,
        contabilidade.valor_lancamento as vl
    WHERE
        r.cod_entidade  IN ('' || stCodEntidades || '') AND
        r.exercicio     =   '''''' || stExercicio || '''''' AND

        cr.exercicio    = r.exercicio AND
        cr.cod_conta    = r.cod_conta AND

        r.exercicio     = lr.exercicio AND
        r.cod_receita   = lr.cod_receita AND

        lr.exercicio    = l.exercicio AND
        lr.sequencia    = l.sequencia AND
        lr.cod_lote     = l.cod_lote AND
        lr.tipo         = l.tipo AND
        lr.cod_entidade = l.cod_entidade AND
        --lr.estorno      = false AND
        lr.tipo         = ''''A'''' AND

        l.exercicio     = lo.exercicio AND
        l.cod_lote      = lo.cod_lote AND
        l.tipo          = lo.tipo AND
        l.cod_entidade  = lo.cod_entidade AND
        lo.dt_lote       BETWEEN to_date(''''''|| stDtInicial ||'''''',''''dd/mm/yyyy'''') AND
                                 to_date(''''''|| stDtFinal ||'''''',''''dd/mm/yyyy'''') AND

        l.exercicio     = vl.exercicio AND
        l.cod_lote      = vl.cod_lote AND
        l.tipo          = vl.tipo AND
        l.sequencia     = vl.sequencia AND
        l.cod_entidade  = vl.cod_entidade AND
        vl.tipo_valor   = ''''C''''

    GROUP BY
        cast(substr(replace(cr.cod_estrutural,''''.'''',''''''''),1,8) as integer),
        lr.estorno
    ORDER BY
        cast(substr(replace(cr.cod_estrutural,''''.'''',''''''''),1,8) as integer),
        lr.estorno
'';


FOR reRegistro IN EXECUTE stSql
    LOOP
        IF reRegistro.valor <> 0.00 THEN
            reRegistro.valor := reRegistro.valor * -1;
            RETURN next reRegistro;
        END IF;
    END LOOP;

    RETURN;
END;

'language 'plpgsql';


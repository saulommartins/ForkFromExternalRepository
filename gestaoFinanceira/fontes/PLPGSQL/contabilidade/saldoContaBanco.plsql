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
* $Revision: 12203 $
* $Name$
* $Author: cleisson $
* $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $
*
* Casos de uso: uc-02.04.05 uc-02.04.09
*/

/*
$Log$
Revision 1.6  2006/07/05 20:37:31  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION contabilidade.fn_saldo_conta_banco(VARCHAR,INTEGER) RETURNS NUMERIC AS '

DECLARE
    stExercicio    ALIAS FOR $1;
    inCodPlano     ALIAS FOR $2;
    stSql          VARCHAR := '''';
    nuVlDebito     NUMERIC := 0.00;
    nuVlCredito    NUMERIC := 0.00;
    reRecord       RECORD;

BEGIN

        SELECT coalesce(sum( vl_lancamento ), 0.00 )
               INTO nuVlDebito
        FROM contabilidade.plano_banco      AS CPB
            ,contabilidade.plano_analitica  AS CPA
            ,contabilidade.conta_debito     AS CCD
            ,contabilidade.valor_lancamento AS CVL
          -- Join com plano_analitica
        WHERE CPB.exercicio    = CPA.exercicio
          AND CPB.cod_plano    = CPA.cod_plano
          -- Join com conta_debito
          AND CPA.exercicio    = CCD.exercicio
          AND CPA.cod_plano    = CCD.cod_plano
          -- Join com valor_lacamento
          AND CCD.exercicio    = CVL.exercicio
          AND CCD.cod_entidade = CVL.cod_entidade
          AND CCD.tipo         = CVL.tipo
          AND CCD.tipo_valor   = CVL.tipo_valor
          AND CCD.cod_lote     = CVL.cod_lote
          AND CCD.sequencia    = CVL.sequencia
          -- Filtros
          AND CPA.exercicio    = stExercicio
          AND CPB.cod_plano    = inCodPlano
          ;


        SELECT coalesce(sum( vl_lancamento ), 0.00 )
               INTO nuVlCredito
        FROM contabilidade.plano_banco      AS CPB
            ,contabilidade.plano_analitica  AS CPA
            ,contabilidade.conta_credito    AS CCC
            ,contabilidade.valor_lancamento AS CVL
          -- Join com plano_analitica
        WHERE CPB.exercicio    = CPA.exercicio
          AND CPB.cod_plano    = CPA.cod_plano
          -- Join com conta_debito
          AND CPA.exercicio    = CCC.exercicio
          AND CPA.cod_plano    = CCC.cod_plano
          -- Join com valor_lacamento
          AND CCC.exercicio    = CVL.exercicio
          AND CCC.cod_entidade = CVL.cod_entidade
          AND CCC.tipo         = CVL.tipo
          AND CCC.tipo_valor   = CVL.tipo_valor
          AND CCC.cod_lote     = CVL.cod_lote
          AND CCC.sequencia    = CVL.sequencia
          -- Filtros
          AND CPA.exercicio    = stExercicio
          AND CPB.cod_plano    = inCodPlano
          ;


    RETURN nuVlDebito + nuVlCredito;

END;
'LANGUAGE 'plpgsql';

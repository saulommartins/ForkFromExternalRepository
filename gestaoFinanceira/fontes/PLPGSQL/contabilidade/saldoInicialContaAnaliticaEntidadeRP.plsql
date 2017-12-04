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
* $Revision: 62406 $
* $Name$
* $Author: franver $
* $Date: 2015-05-05 11:43:16 -0300 (Tue, 05 May 2015) $
*
* Casos de uso: uc-02.02.31
*/
CREATE OR REPLACE FUNCTION contabilidade.fn_saldo_inicial_conta_analitica_entidade_rp(VARCHAR,INTEGER,INTEGER) RETURNS NUMERIC AS $$
DECLARE
    stExercicio    ALIAS FOR $1;
    inCodPlano     ALIAS FOR $2;
    inCodEntidade  ALIAS FOR $3;
    stSql          VARCHAR := '';
    nuVlDebito     NUMERIC := 0.00;
    nuVlCredito    NUMERIC := 0.00;
    reRecord       RECORD;

BEGIN
    -------------------------------
    ---- CONSULTA VALOR DEBITO ----
    -------------------------------
          SELECT COALESCE(SUM(vl_lancamento ), 0.00 ) INTO nuVlDebito
            FROM contabilidade.plano_analitica  AS CPA
              -- Join com conta_debito  
      INNER JOIN contabilidade.conta_debito     AS CCD
              ON CPA.exercicio    = CCD.exercicio
             AND CPA.cod_plano    = CCD.cod_plano
              -- Join com valor_lacamento
      INNER JOIN contabilidade.valor_lancamento AS CVL
              ON CCD.exercicio    = CVL.exercicio
             AND CCD.cod_entidade = CVL.cod_entidade
             AND CCD.tipo         = CVL.tipo
             AND CCD.tipo_valor   = CVL.tipo_valor
             AND CCD.cod_lote     = CVL.cod_lote
             AND CCD.sequencia    = CVL.sequencia
              -- Join com lacamento
      INNER JOIN contabilidade.lancamento AS CL
              ON CVL.exercicio    = CL.exercicio
             AND CVL.cod_entidade = CL.cod_entidade
             AND CVL.tipo         = CL.tipo
             AND CVL.cod_lote     = CL.cod_lote
             AND CVL.sequencia    = CL.sequencia
              -- Join com lote
      INNER JOIN contabilidade.lote
              ON CL.exercicio    = lote.exercicio
             AND CL.cod_entidade = lote.cod_entidade
             AND CL.tipo         = lote.tipo
             AND CL.cod_lote     = lote.cod_lote
              -- Filtros
           WHERE CPA.exercicio    = stExercicio
             AND CPA.cod_plano    = inCodPlano
             AND CVL.cod_entidade = inCodEntidade
             AND lote.dt_lote = TO_DATE('01/01/'||stExercicio,'dd/mm/yyyy');
    --------------------------------
    ---- CONSULTA VALOR cREDITO ----
    --------------------------------
          SELECT coalesce(sum( vl_lancamento ), 0.00 ) INTO nuVlCredito
            FROM contabilidade.plano_analitica  AS CPA
              -- Join com conta_debito
      INNER JOIN contabilidade.conta_credito    AS CCC
              ON CPA.exercicio    = CCC.exercicio
             AND CPA.cod_plano    = CCC.cod_plano
              -- Join com valor_lacamento
      INNER JOIN contabilidade.valor_lancamento AS CVL
              ON CCC.exercicio    = CVL.exercicio
             AND CCC.cod_entidade = CVL.cod_entidade
             AND CCC.tipo         = CVL.tipo
             AND CCC.tipo_valor   = CVL.tipo_valor
             AND CCC.cod_lote     = CVL.cod_lote
             AND CCC.sequencia    = CVL.sequencia
              -- Join com lacamento
      INNER JOIN contabilidade.lancamento AS CL
              ON CVL.exercicio    = CL.exercicio
             AND CVL.cod_entidade = CL.cod_entidade
             AND CVL.tipo         = CL.tipo
             AND CVL.cod_lote     = CL.cod_lote
             AND CVL.sequencia    = CL.sequencia
              -- Join com lote
      INNER JOIN contabilidade.lote
              ON CL.exercicio    = lote.exercicio
             AND CL.cod_entidade = lote.cod_entidade
             AND CL.tipo         = lote.tipo
             AND CL.cod_lote     = lote.cod_lote
              -- Filtros
           WHERE CPA.exercicio    = stExercicio
             AND CPA.cod_plano    = inCodPlano
             AND CVL.cod_entidade = inCodEntidade
             AND lote.dt_lote = TO_DATE('01/01/'||stExercicio,'dd/mm/yyyy');

    RETURN nuVlDebito + nuVlCredito;

END;
$$ LANGUAGE 'plpgsql';

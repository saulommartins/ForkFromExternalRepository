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
* Casos de uso: uc-02.02.04, uc-02.04.07
*/

/*
$Log$
Revision 1.7  2006/07/05 20:37:31  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION contabilidade.fn_recupera_conta_lancamento(VARCHAR,INTEGER,INTEGER,VARCHAR,INTEGER,VARCHAR) RETURNS INTEGER AS $$
DECLARE
    stExercicio     ALIAS FOR $1;
    inCodEntidade   ALIAS FOR $2;
    inCodLote       ALIAS FOR $3;
    stTipo          ALIAS FOR $4;
    inSequencia     ALIAS FOR $5;
    stTipoValor     ALIAS FOR $6;
    inCodPlano      INTEGER;

BEGIN

      SELECT CASE WHEN CVL.tipo_valor = 'D'
                  THEN CCD.cod_plano
                  ELSE CCC.cod_plano
              END AS cod_plano
             INTO inCodPlano
        FROM contabilidade.valor_lancamento  AS CVL
   -- Join com conta_debito
   LEFT JOIN contabilidade.conta_debito AS CCD
          ON CVL.exercicio    = CCD.exercicio
         AND CVL.cod_entidade = CCD.cod_entidade
         AND CVL.tipo         = CCD.tipo
         AND CVL.cod_lote     = CCD.cod_lote
         AND CVL.sequencia    = CCD.sequencia
         AND CVL.tipo_valor   = CCD.tipo_valor
    -- join com conta_credito
   LEFT JOIN contabilidade.conta_credito AS CCC
          ON CVL.exercicio    = CCC.exercicio
         AND CVL.cod_entidade = CCC.cod_entidade
         AND CVL.tipo         = CCC.tipo
         AND CVL.cod_lote     = CCC.cod_lote
         AND CVL.sequencia    = CCC.sequencia
         AND CVL.tipo_valor   = CCC.tipo_valor
    -- Filtros
       WHERE CVL.exercicio    = stExercicio
         AND CVL.cod_entidade = inCodEntidade
         AND CVL.tipo         = stTipo
         AND CVL.cod_lote     = inCodLote
         AND CVL.sequencia    = inSequencia
         AND CVL.tipo_valor   = stTipoValor
    ;

    RETURN inCodPlano;

END;
$$ LANGUAGE 'plpgsql';

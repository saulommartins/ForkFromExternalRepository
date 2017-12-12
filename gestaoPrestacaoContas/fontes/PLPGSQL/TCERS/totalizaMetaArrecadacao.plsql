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
* Casos de uso: uc-02.00.00
* Casos de uso: uc-02.00.00
*/

/*
$Log$
Revision 1.1  2007/09/26 14:43:57  gris
-- O módulo TCE-SC  funcionalidades deverá ir para a gestãp estação de contas.

Revision 1.6  2007/07/30 18:28:33  tonismar
alteração de uc-00.00.00 para uc-02.00.00

Revision 1.5  2006/07/05 20:37:45  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION tcers.totaliza_meta_arrecadacao(VARCHAR,VARCHAR,INTEGER) RETURNS NUMERIC AS $$

DECLARE
    stCodEstrutural     ALIAS FOR $1;
    stExercicio         ALIAS FOR $2;
    inBimestre          ALIAS FOR $3;
    stParametro         VARCHAR;
    stPeriodo           VARCHAR;
    stSql               VARCHAR;
    stUnidadeMedidaMetas VARCHAR;
    nuTotal             NUMERIC;
    crCursor            REFCURSOR;

BEGIN
    IF stExercicio::INTEGER < 2014 THEN
        stUnidadeMedidaMetas := 'unidade_medida_metas';
    ELSE
        stUnidadeMedidaMetas := 'unidade_medida_metas_receita';
    END IF;
    
    
    SELECT valor
      INTO stParametro
      FROM administracao.configuracao
     WHERE parametro = stUnidadeMedidaMetas
       AND exercicio = stExercicio;

    IF stParametro = '1' THEN

      IF inBimestre = 1 THEN
        stPeriodo = '1,2';
      ELSIF inBimestre = 2 THEN
        stPeriodo = '3,4';
      ELSIF inBimestre = 3 THEN
        stPeriodo = '5,6';
      ELSIF inBimestre = 4 THEN
        stPeriodo = '7,8';
      ELSIF inBimestre = 5 THEN
        stPeriodo = '9,10';
      ELSIF inBimestre = 6 THEN
        stPeriodo = '11,12';
      END IF;

    ELSIF stParametro = '2' THEN
      IF inBimestre = 1 THEN
        stPeriodo = '1';
      ELSIF inBimestre = 2 THEN
        stPeriodo = '2';
      ELSIF inBimestre = 3 THEN
        stPeriodo = '3';
      ELSIF inBimestre = 4 THEN
        stPeriodo = '4';
      ELSIF inBimestre = 5 THEN
        stPeriodo = '5';
      ELSIF inBimestre = 6 THEN
        stPeriodo = '6';
      END IF;

    END IF;

    stSql := '
      SELECT  coalesce(sum(vl_periodo),0.00)
        FROM  tmp_valor_metas
       WHERE  cod_estrutural like ''' || stCodEstrutural || '%''
         AND  periodo IN ('|| quote_literal(stPeriodo) ||') ';

    OPEN crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO nuTotal;
    CLOSE crCursor;

    RETURN nuTotal;

END;

$$ LANGUAGE 'plpgsql';

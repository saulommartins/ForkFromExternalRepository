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

CREATE OR REPLACE FUNCTION tcers.totaliza_receita_realizada(VARCHAR,VARCHAR,INTEGER) RETURNS NUMERIC AS $$

DECLARE
    stCodEstrutural     ALIAS FOR $1;
    stExercicio         ALIAS FOR $2;
    inMes               ALIAS FOR $3;
    nuTotal             NUMERIC;

BEGIN

    SELECT      coalesce(sum(vl.vl_lancamento),0.00)
        INTO    nuTotal
    FROM    orcamento.conta_receita             as cr
            ,orcamento.receita                  as re
            ,contabilidade.lancamento_receita   as lr
            ,contabilidade.lancamento           as la
            ,contabilidade.valor_lancamento     as vl
            ,contabilidade.lote                 as lo
    WHERE   cr.exercicio                = re.exercicio
    AND     cr.cod_conta                = re.cod_conta

    AND     re.exercicio                = lr.exercicio
    AND     re.cod_receita              = lr.cod_receita

    AND     lr.exercicio                = la.exercicio
    AND     lr.cod_lote                 = la.cod_lote
    AND     lr.tipo                     = la.tipo
    AND     lr.sequencia                = la.sequencia
    AND     lr.cod_entidade             = la.cod_entidade

    AND     la.exercicio                = vl.exercicio
    AND     la.cod_lote                 = vl.cod_lote
    AND     la.tipo                     = vl.tipo
    AND     la.sequencia                = vl.sequencia
    AND     la.cod_entidade             = vl.cod_entidade

    AND     la.exercicio                = lo.exercicio
    AND     la.cod_lote                 = lo.cod_lote
    AND     la.tipo                     = lo.tipo
    AND     la.cod_entidade             = lo.cod_entidade

    AND     cr.exercicio                = stExercicio
    AND     cr.cod_estrutural           = stCodEstrutural
    AND     to_number(to_char(lo.dt_lote,'mm'),'99999')  = inMes
    AND     lo.tipo                     = 'A'
    AND     vl.tipo_valor               = 'D'
    ;
    RETURN nuTotal;

END;
$$ LANGUAGE 'plpgsql';



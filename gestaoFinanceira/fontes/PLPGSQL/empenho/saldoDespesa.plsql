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
* $Revision: 12710 $
* $Name$
* $Author: andre.almeida $
* $Date: 2006-07-14 14:58:46 -0300 (Sex, 14 Jul 2006) $
*
* Casos de uso: uc-02.03.03
*/

/*
$Log$
Revision 1.7  2006/07/14 17:58:35  andre.almeida
Bug #6556#

Alterado scripts de NOT IN para NOT EXISTS.

Revision 1.6  2006/07/05 20:37:38  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION empenho.fn_saldo_despesa(VARCHAR,INTEGER) RETURNS NUMERIC AS '

DECLARE
    stExercicio         ALIAS FOR $1;
    inCodEmpenho        ALIAS FOR $2;
    nuTotal             NUMERIC;

BEGIN

    SELECT      coalesce(sum(vl_original),0.00)
        INTO    nuTotal
    FROM     empenho.pre_empenho           as pe
            ,empenho.pre_empenho_despesa   as pd
            ,empenho.empenho               as em
            ,orcamento.despesa             as de
    WHERE   pe.cod_pre_empenho  = em.cod_pre_empenho
    AND     pe.exercicio        = em.exercicio
    AND     pe.exercicio        = pd.exercicio
    AND     pe.cod_pre_empenho  = pd.cod_pre_empenho
    AND     pd.cod_despesa      = de.cod_despesa
    AND     pd.exercicio        = de.exercicio
    AND     pe.exercicio        = stExercicio
    AND NOT EXISTS ( SELECT 1
                       FROM empenho.empenho_anulado e_ea
                      WHERE e_ea.cod_empenho  = em.cod_empenho
                        AND e_ea.exercicio    = em.exercicio
                        AND e_ea.cod_entidade = em.cod_entidade
                        AND exercicio = stExercicio
            )
    AND     em.cod_empenho = inCodEmpenho
    ;
    RETURN nuTotal;

END;
'LANGUAGE 'plpgsql';

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

Revision 1.7  2007/07/30 18:28:33  tonismar
alteração de uc-00.00.00 para uc-02.00.00

Revision 1.6  2006/07/05 20:37:45  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION tcers.fn_exportacao_empenho_total_anulado(varchar,integer,integer,varchar) RETURNS NUMERIC(14,2)  AS '
DECLARE
    stExercicio      ALIAS FOR $1           ;
    inCodEmpenho     ALIAS FOR $2           ;
    inCodEntidade    ALIAS FOR $3           ;
    stExercicioAtual ALIAS FOR $4           ;
    nuSoma           NUMERIC(14,2)   := 0.00;

BEGIN
    SELECT  coalesce(Sum(eai.vl_anulado),0.00)
    INTO    nuSoma
    FROM    empenho.empenho_anulado_item    as eai,
            empenho.item_pre_empenho        as ipe,
            empenho.pre_empenho             as pre,
            empenho.empenho                 as ee
    WHERE   ee.exercicio        =   stExercicio
        AND ee.cod_empenho      =   inCodEmpenho
        AND ee.cod_entidade     =   inCodEntidade
        -- Ligando a pre empenho
        AND pre.exercicio       =   ee.exercicio
        AND pre.cod_pre_empenho =   ee.cod_pre_empenho
        -- Ligando a item_pre_empenho
        AND ipe.exercicio       =   pre.exercicio
        AND ipe.cod_pre_empenho =   pre.cod_pre_empenho
        -- Enfim chega a empenho_anulado_item
        AND eai.exercicio       =   ipe.exercicio
        AND eai.cod_pre_empenho =   ipe.cod_pre_empenho
        AND eai.num_item        =   ipe.num_item
        AND to_date(eai.timestamp,''yyyy-mm-dd'') <= to_date(''31/12/''||to_number(stExercicioAtual,''9999'')-1,''dd/mm/yyyy'')
 ;

/*      -- Empenho Anulado
        AND eea.exercicio       =   ee.exercicio
        AND eea.cod_empenho     =   ee.cod_empenho
        AND eea.cod_entidade    =   ee.cod_entidade
      -- Empenho anulado item
        AND eai.exercicio       =   eea.exercicio
        AND eai.cod_empenho     =   eea.cod_empenho
        AND eai.cod_entidade    =   eea.cod_entidade
        AND eai."timestamp"     =   eea."timestamp";
*/
    RETURN nuSoma;
END;
' LANGUAGE 'plpgsql';

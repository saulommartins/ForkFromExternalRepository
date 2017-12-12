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

CREATE OR REPLACE FUNCTION tcers.fn_exportacao_liquidacao_total_liquidado(varchar,integer,integer) RETURNS NUMERIC(14,2)  AS '
DECLARE
    stExercicio     ALIAS FOR $1            ;
    inCodNota       ALIAS FOR $2            ;
    inCodEntidade   ALIAS FOR $3            ;
    nuSoma          NUMERIC(14,2)   := 0.00 ;

BEGIN
    SELECT  coalesce(Sum(eli.vl_total),0.00)
    INTO    nuSoma
    FROM    empenho.nota_liquidacao_item    as eli,
            empenho.nota_liquidacao         as enl

    WHERE   enl.exercicio       =   stExercicio
        AND enl.cod_nota        =   inCodNota
        AND enl.cod_entidade    =   inCodEntidade
        -- Liga a nota_liquidacao_item
        AND eli.exercicio       =   enl.exercicio
        AND eli.cod_nota        =   enl.cod_nota
        AND eli.cod_entidade    =   enl.cod_entidade;

    RETURN nuSoma;
END;
' LANGUAGE 'plpgsql';

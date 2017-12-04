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
* $Revision: 19000 $
* $Name$
* $Author: cleisson $
* $Date: 2006-12-27 19:23:46 -0200 (Qua, 27 Dez 2006) $
*
* Casos de uso: uc-02.02.31
*/

/*
$Log$
Revision 1.1  2006/12/27 21:23:46  cleisson
UC 02.02.31

*/

CREATE OR REPLACE FUNCTION contabilidade.fn_busca_conta_lancamento_rp(VARCHAR,INTEGER,INTEGER) RETURNS INTEGER AS '
DECLARE
    stExercicio     ALIAS FOR $1;
    inEntidade      ALIAS FOR $2;
    inTipoConta     ALIAS FOR $3;

    stSql           VARCHAR   := '''';
    inCodPlano      INTEGER;

BEGIN

    SELECT 
        cod_plano INTO inCodPlano
    FROM
        contabilidade.conta_lancamento_rp
    WHERE
        exercicio       = stExercicio AND
        cod_entidade    = inEntidade  AND
        cod_tipo_conta  = inTipoConta
    ; 

RETURN inCodPlano;

END;
'language 'plpgsql';

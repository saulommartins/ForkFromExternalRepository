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
* $Revision: 22269 $
* $Name$
* $Author: cako $
* $Date: 2007-04-30 18:39:35 -0300 (Seg, 30 Abr 2007) $
*
* Casos de uso: uc-02.04.10
*/

/*
$Log$
Revision 1.9  2007/04/30 21:39:35  cako
Bug #9103#

Revision 1.8  2006/07/05 20:38:11  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION tesouraria.fn_create_tmp_banco(INTEGER, VARCHAR, VARCHAR, VARCHAR, VARCHAR) RETURNS SETOF RECORD AS '
DECLARE
    inCodPlano           ALIAS FOR $1;
    stExercicio          ALIAS FOR $2;
    stEntidade           ALIAS FOR $3;
    stDtInicial          ALIAS FOR $4;
    stDtFinal            ALIAS FOR $5;
    stSql                VARCHAR   := '''';

    reRegistro           RECORD;

BEGIN

stSql := ''
        SELECT  
            PC.nom_conta,
            PC.cod_estrutural,
            (select publico.fn_nivel(PC.cod_estrutural)) as nivel,
            PB.cod_banco,
            PB.cod_agencia,
            PB.conta_corrente,
            MA.nom_agencia,
            MB.nom_banco
        FROM
            contabilidade.plano_conta       as PC,
            contabilidade.plano_analitica   as PA,
            contabilidade.plano_banco       as PB
            LEFT JOIN monetario.agencia as MA ON(
                PB.cod_banco    = MA.cod_banco      AND
                PB.cod_agencia  = MA.cod_agencia       
            )
            LEFT JOIN monetario.banco as MB ON(
                MA.cod_banco    = MB.cod_banco           
            )
        WHERE   
            PC.cod_conta    = PA.cod_conta      AND
            PC.exercicio    = PA.exercicio      AND

            PA.cod_plano    = PB.cod_plano      AND
            PA.exercicio    = PB.exercicio      AND

            PA.cod_plano = '' || inCodPlano || ''   AND
            PB.exercicio = '''''' || stExercicio || '''''' '';


    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN next reRegistro;
    END LOOP;

    RETURN;

END;
' LANGUAGE 'plpgsql';

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
* Casos de uso: uc-02.04.10
*/

/*
$Log$
Revision 1.2  2006/07/05 20:38:11  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION tesouraria.retorna_OPs(VARCHAR,INTEGER,INTEGER) RETURNS VARCHAR as '
DECLARE
    stExercicio         ALIAS FOR $1;
    inCodBordero        ALIAS FOR $2;
    inCodEntidade       ALIAS FOR $3;
    stSaida             VARCHAR   := '''';
    stSql               VARCHAR   := '''';
    reRegistro          RECORD;
BEGIN
    stSql := ''
                SELECT
                         tp.cod_ordem
                        ,tp.exercicio
                FROM
                        tesouraria.transacoes_pagamento    as tp
                WHERE
                        tp.exercicio            = '''''' || stExercicio     || ''''''
                AND     tp.cod_bordero          = '' || inCodBordero    || ''
                AND     tp.cod_entidade         = '' || inCodEntidade   || ''
            '';
    FOR reRegistro IN EXECUTE stSql
    LOOP
        stSaida := stSaida || reRegistro.cod_ordem || ''/'';
    END LOOP;

    stSaida := substr(stSaida,0,length(stSaida));

    RETURN stSaida;
END;
'LANGUAGE 'plpgsql';

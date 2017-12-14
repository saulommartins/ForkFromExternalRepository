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
* $Id: mes.plsql 61082 2014-12-04 17:25:32Z franver $
* $Revision: 61082 $
* $Author: franver $
* $Date: 2014-12-04 15:25:32 -0200 (Thu, 04 Dec 2014) $
*
*/
CREATE OR REPLACE FUNCTION publico.mes(INTEGER,INTEGER) RETURNS VARCHAR[] AS $$
DECLARE
    inExercicioPar ALIAS FOR $1;
    inMesPar       ALIAS FOR $2;
    inExercicio    INTEGER;
    inMes          INTEGER;
    stDataInicial  VARCHAR := '';
    stDataFinal    VARCHAR := '';
    arDatas        VARCHAR[];
    reRegistro     RECORD;
BEGIN
    IF LENGTH(inExercicioPar::VARCHAR) = 4 THEN
        inExercicio := inExercicioPar;
        inMes       := inMesPar;
    ELSE
        inExercicio := inMesPar;
        inMes       := inExercicioPar;
    END IF;

    arDatas[0] := TO_CHAR((TO_DATE('01/'||inMes||'/'||inExercicio,'dd/mm/yyyy')),'dd/mm/yyyy');
    arDatas[1] := TO_CHAR((TO_DATE('01/'||inMes+1||'/'||inExercicio,'dd/mm/yyyy')-1),'dd/mm/yyyy');
    
    RETURN arDatas;
END;

$$ language 'plpgsql';

CREATE OR REPLACE FUNCTION publico.mes(VARCHAR,INTEGER) RETURNS VARCHAR[] AS $$
DECLARE
    inExercicioPar ALIAS FOR $1;
    inMesPar       ALIAS FOR $2;
BEGIN
    RETURN publico.mes( CAST(inExercicioPar as INTEGER), inMesPar );
END;

$$ language 'plpgsql';
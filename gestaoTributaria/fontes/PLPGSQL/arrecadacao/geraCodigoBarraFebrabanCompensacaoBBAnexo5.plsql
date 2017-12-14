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
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: geraCodigoBarraFebraban.plsql 29203 2008-04-15 14:45:04Z fabio $
*
* Caso de uso: uc-05.03.11
* Caso de uso: uc-05.03.19
*
*/

/*
$Log$
Revision 1.1  2007/07/16 16:05:55  dibueno
Melhorias na gerção de carnê pra gráfica



*/


CREATE OR REPLACE FUNCTION arrecadacao.geraCodigoBarraFebrabanCompensacaoBBAnexo5 (date, varchar, varchar, integer, integer)
                                                                /*  1    2     3    4   5  */
RETURNS VARCHAR AS '
DECLARE

    dtVencimento    ALIAS FOR $1;

    stValor         ALIAS FOR $2;
    stNumeracao     ALIAS FOR $3;

    inTipoMoeda     ALIAS FOR $4;
    inCodConvenio   ALIAS FOR $5;

    inFatorVencimento integer;
    stValor2        varchar := '''';

    stRetorno       varchar := '''';
    stBarraFebraban varchar := '''';
    stLinhaFebraban varchar := '''';
    stGeral         varchar := '''';
    stCodFebraban2  varchar := '''';
    dtVencimento2   varchar := '''';
    stNumeracao2    varchar := '''';

    stCampo1        varchar;
    stCampo2        varchar;
    stCampo3        varchar;
    stDV            varchar;

BEGIN

/**
*   FUNCIONAMENTO
*       Utilizando
*/

    SELECT dtVencimento-''1997-10-07''::date INTO inFatorVencimento;
    stValor2 := substring( stValor from 0 for (char_length(stValor)-2) ) || substring( stValor from (char_length(stValor)-1) for 2 );
    stValor2 := lpad( stValor2, 10, ''0'' );

    stCodFebraban2 := lpad( inCodConvenio::varchar, 6, ''0'' );

    stNumeracao2 := lpad( stNumeracao, 17, ''0'' );

    SELECT * INTO stGeral FROM arrecadacao.geraCodigoBarraFebrabanCompensacao_modulo11 ( ''001''||inTipoMoeda||inFatorVencimento||stValor2||stCodFebraban2||stNumeracao2||''21'' );

    stBarraFebraban:= ''001''||inTipoMoeda||stGeral||inFatorVencimento||stValor2||stCodFebraban2||stNumeracao2||''21'';

    stCampo1 := ''001''||inTipoMoeda||substring ( stBarraFebraban from 20 for 1 )||''.''||substring ( stBarraFebraban from 21 for 4 );
    SELECT * INTO stDV FROM arrecadacao.geraCodigoBarraFebrabanCompensacao_modulo10 (''001''||inTipoMoeda||substring ( stBarraFebraban from 20 for 5 ) );
    stCampo1 := stCampo1||stDV;


    stCampo2 := substring ( stBarraFebraban from 25 for 5 )||''.''||substring ( stBarraFebraban from 30 for 5 );
    SELECT * INTO stDV FROM arrecadacao.geraCodigoBarraFebrabanCompensacao_modulo10 ( substring ( stBarraFebraban from 25 for 10 ) );
    stCampo2 := stCampo2||stDV;

    stCampo3 = substring ( stBarraFebraban from 35 for 5 )||''.''||substring ( stBarraFebraban from 40 for 5 );
    SELECT * INTO stDV FROM arrecadacao.geraCodigoBarraFebrabanCompensacao_modulo10 ( substring ( stBarraFebraban from 35 for 10 ) );
    stCampo3 := stCampo3||stDV;

    stLinhaFebraban :=  stCampo1||'' ''||stCampo2||'' ''||stCampo3||'' ''||stGeral||'' ''||inFatorVencimento||stValor2;

    stRetorno :=  stBarraFebraban||'' § ''||stLinhaFebraban ;
    return stRetorno;

END;
' LANGUAGE 'plpgsql';

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
* $Id: geraCodigoBarraFebraban_modulo10.plsql 29203 2008-04-15 14:45:04Z fabio $
*
* Caso de uso: uc-05.03.11
* Caso de uso: uc-05.03.19
*
*/

CREATE OR REPLACE FUNCTION arrecadacao.geraCodigoBarraFebrabanCompensacao_modulo10 ( varchar )
RETURNS INTEGER AS '
DECLARE

    stCodigo        ALIAS FOR $1;

    inSoma          integer := 0;
    stAcc           varchar := '''';
    inResto         integer := 0;
    inDac           integer := 0;

    inTamanho       integer := 0;
    inCont          integer := 1;
    inX             integer;

    stRetorno       varchar := '''';
    stNovoCodigo    varchar := '''';
    boDois          boolean := true;

BEGIN
    inTamanho = char_length(stCodigo);
    inCont := inTamanho;

    while ( inCont >= 1 ) loop
        if ( boDois ) then
            stAcc := stAcc || (substring(stCodigo from inCont for 1 ))::int * 2;
            boDois := false;
        else
            stAcc := stAcc || (substring(stCodigo from inCont for 1 ))::int * 1;
            boDois := true;
        end if;

        inCont := inCont - 1;
    end loop;

    inTamanho := char_length ( stAcc );
    inCont := 1;
    while ( inCont <= inTamanho ) LOOP
        inSoma := inSoma + (substring( stAcc from inCont for 1 ))::int;
        inCont := inCont + 1;
    END LOOP;

    if ( inSoma < 10 ) then
        inDac := 10 - inSoma;
    else 
        stAcc := inSoma::varchar;
        inTamanho := char_length ( stAcc ) - 1;
        stNovoCodigo := substring( stAcc from 1 for inTamanho );
        inResto := stNovoCodigo::int;
        inResto := inResto + 1;
        inX := 0;
        stAcc := inResto::varchar;
        while ( inX < inTamanho - (inTamanho-1) ) loop
            stAcc = stAcc||''0'';
            inX := inX + 1;
        end loop;
        inResto := stAcc::int;
        inDac := inResto - inSoma;
    end if;

    if ( inDac = 10 ) then
        inDac := 0;
    end if;

    return inDac;
END;
' LANGUAGE 'plpgsql';

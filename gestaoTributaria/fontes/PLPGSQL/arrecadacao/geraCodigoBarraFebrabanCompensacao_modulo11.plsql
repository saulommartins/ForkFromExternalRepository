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

CREATE OR REPLACE FUNCTION arrecadacao.geraCodigoBarraFebrabanCompensacao_modulo11 ( varchar )
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

BEGIN



    inTamanho := char_length ( stCodigo );
    inCont := 2;
    inX := inTamanho;
    while ( inX >= 1 ) LOOP
        inSoma := inSoma + ((substring( stCodigo from inX for 1 ))::int * inCont);

        inCont := inCont + 1;
        if (inCont > 9) then
            inCont = 2;
        end if;

        inX := inX - 1;
    end loop;

    inDac := inSoma % 11;

    inDac := 11 - inDac;
    if ( ( inDac = 0 ) OR ( inDac = 10 ) OR ( inDac = 11 ) ) then
        return 1;
    else
        return inDac;
    end if;

END;
' LANGUAGE 'plpgsql';

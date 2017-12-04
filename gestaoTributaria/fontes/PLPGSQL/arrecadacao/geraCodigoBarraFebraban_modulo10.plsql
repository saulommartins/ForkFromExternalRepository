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
* $Id: geraCodigoBarraFebraban_modulo10.plsql 59612 2014-09-02 12:00:51Z gelson $
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


CREATE OR REPLACE FUNCTION arrecadacao.geraCodigoBarraFebraban_modulo10 ( varchar )
RETURNS INTEGER AS $$
DECLARE

    stCodigo        ALIAS FOR $1;

    inSoma          integer := 0;
    stAcc           varchar := '';
    inResto         integer := 0;
    inDac           integer := 0;

    inTamanho       integer := 0;
    inCont          integer := 1;

    stRetorno       varchar := '';
    stNovoCodigo    varchar := '';

BEGIN

/**
*   FUNCIONAMENTO
*       Utilizando
*/

        stNovoCodigo := '0'||stCodigo;
        inTamanho := char_length(stNovoCodigo) + 1;

        while ( inCont < inTamanho ) LOOP
            if ( inCont = 1 ) THEN
                stAcc := 0;
            ELSE
                if( inCont % 2 = 0 ) THEN
                    stAcc := stAcc || (substring(stNovoCodigo from inCont for 1 ))::int * 2;
                else
                    stAcc := stAcc || (substring(stNovoCodigo from inCont for 1 ))::int * 1;
                END IF;
            END IF;
            inCont := inCont + 1;
            
        END LOOP;

        -----############################

        stNovoCodigo := stAcc::varchar;
        inTamanho := char_length ( stNovoCodigo ) + 1;
        inCont := 1;


        while ( inCont < inTamanho ) LOOP
            inSoma := inSoma + (substring( stNovoCodigo from inCont for 1 ))::int;
            inCont := inCont + 1;
        END LOOP;

        inResto := inSoma % 10;
        IF ( inResto = 0 ) THEN
            inDac := 0;
        ELSE
            inDac := 10 - inResto;
        END IF;

    return inDac;

END;
$$ LANGUAGE 'plpgsql';

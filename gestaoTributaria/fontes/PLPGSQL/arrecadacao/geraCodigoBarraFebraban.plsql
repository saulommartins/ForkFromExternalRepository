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
* $Id: geraCodigoBarraFebraban.plsql 59612 2014-09-02 12:00:51Z gelson $
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


CREATE OR REPLACE FUNCTION arrecadacao.geraCodigoBarraFebraban (varchar, varchar, varchar, integer, integer)
                                                                /*  1    2     3    4   5  */
RETURNS VARCHAR AS $$
DECLARE

    dtVencimento    ALIAS FOR $1;

    stValor         ALIAS FOR $2;
    stNumeracao     ALIAS FOR $3;

    inTipoMoeda     ALIAS FOR $4;
    inCodFebraban   ALIAS FOR $5;

    stValor2        varchar := '';

    stRetorno       varchar := '';
    stBarraFebraban varchar := '';
    stLinhaFebraban varchar := '';
    stGeral         varchar := '';
    stCodFebraban2  varchar := '';
    dtVencimento2   varchar := '';
    stNumeracao2    varchar := '';

BEGIN

/**
*   FUNCIONAMENTO
*       Utilizando
*/

--
    stValor2 := substring( stValor from 0 for (char_length(stValor)-2) ) || substring( stValor from (char_length(stValor)-1) for 2 );
    stValor2 := lpad( stValor2, 11, '0' );

    stCodFebraban2 := lpad( inCodFebraban::varchar, 4, (0::varchar) );

    dtVencimento2 := substring( dtVencimento from 0 for 5 ) || substring( dtVencimento from 6 for 2 )|| substring( dtVencimento from 9 for 2 );

    stNumeracao2 := lpad( stNumeracao, 17, (0::varchar) );

    select * INTO stGeral FROM arrecadacao.geraCodigoBarraFebraban_modulo10 (
        '81'||inTipoMoeda||stValor2||stCodFebraban2||dtVencimento2||stNumeracao2
    );


    stBarraFebraban:= '81'||inTipoMoeda||stGeral||stValor2||stCodFebraban2||dtVencimento2||stNumeracao2;


    stLinhaFebraban :=  substring ( stBarraFebraban from 1 for 11 )||' '||
                        arrecadacao.geraCodigoBarraFebraban_modulo10 ( substring (stBarraFebraban from 1 for 11) )
                        ||' '||substring( stBarraFebraban from 12 for 11 )||' '||
                        arrecadacao.geraCodigoBarraFebraban_modulo10 ( substring (stBarraFebraban from 12 for 11) )
                        ||' '||substring( stBarraFebraban from 23 for 11 )||' '||
                        arrecadacao.geraCodigoBarraFebraban_modulo10 ( substring (stBarraFebraban from 23 for 11) )
                        ||' '||substring( stBarraFebraban from 34 for 11 )||' '||
                        arrecadacao.geraCodigoBarraFebraban_modulo10 ( substring (stBarraFebraban from 34 for 11) );



    stRetorno :=  stBarraFebraban||' § '||stLinhaFebraban ;
    return stRetorno;

END;
$$ LANGUAGE 'plpgsql';

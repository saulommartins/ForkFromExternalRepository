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
* $Id: numeracaoFebraban.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Casos d uso: uc-05.03.00
*/

/*
$Log$
Revision 1.5  2006/12/13 16:56:57  dibueno
Alterações referente ao uso de 17 caracteres

Revision 1.4  2006/11/23 11:23:31  dibueno
Alterações no procedimento de casas da numeracao

Revision 1.3  2006/11/22 18:17:50  dibueno
Alteração nas casas da numeracao

Revision 1.2  2006/11/14 15:01:18  fabio
correção da tag de caso de uso

Revision 1.1  2006/11/13 11:28:33  dibueno
*** empty log message ***

*/

CREATE OR REPLACE FUNCTION numeracaoFebraban(VARCHAR,VARCHAR) RETURNS VARCHAR AS $$
DECLARE
    stCarteira      VARCHAR;
    stConvenio      VARCHAR;
    stNumeracao     VARCHAR;
    stNumAnterior   VARCHAR;
    inCodConvenio   INTEGER;
    inNumCasas      INTEGER;
    inCont          INTEGER;
    stMascara       VARCHAR;
    reRecord        RECORD;
BEGIN
    stCarteira      := LPAD( $1, 2 , '0' );
    inCodConvenio   := $2::int;
    stConvenio := recuperaNumConvenio(inCodConvenio);

    inNumCasas := 17 - ( char_length( stConvenio ));
    inCont := 0;
    stMascara := '';
    while ( inCont < inNumCasas ) loop
        stMascara := stMascara ||'9';
        inCont := incont + 1;
    end loop;

    stNumAnterior := ultimaNumeracaoFebraban( inCodConvenio, char_length( stConvenio )::int )::varchar;

    IF stNumAnterior > '0' THEN
        stNumeracao := stConvenio||lpad((to_number(stNumAnterior,stMascara)+1)::varchar, inNumCasas, '0');
    ELSE
        stNumeracao := stConvenio||lpad((to_number('1',stMascara))::varchar, inNumCasas, '0');
    END IF;

    --return inNumCasas;
    return stNumeracao;
END;

$$ LANGUAGE 'plpgsql';

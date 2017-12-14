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
* $Id: buscaLocalizacaoPrimeiroNivel.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.02.12
*              uc-05.03.11
*/

/*
$Log$
Revision 1.3  2007/01/23 18:39:52  dibueno
Alteralçao no esquema da PL busca_primeiro_nivel

Revision 1.2  2007/01/23 11:01:01  fabio
correção da tag de caso de uso

Revision 1.1  2007/01/16 16:35:57  dibueno
*** empty log message ***

Revision 1.3  2007/01/12 19:02:14  dibueno
Melhorias para emissão de carne para gráfica

Revision 1.2  2007/01/12 13:03:35  dibueno
Alteração do caracter separador para '§'

Revision 1.1  2006/10/20 17:39:36  dibueno
*** empty log message ***

Revision 1.3  2006/09/15 10:19:52  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION imobiliario.fn_busca_localizacao_primeiro_nivel( VARCHAR )  RETURNS varchar AS '
DECLARE
    stCodigo        ALIAS FOR $1;
    stRetorno       VARCHAR;
    stRetornoAux    VARCHAR;
	reRecord       record;
	stSql 			VARCHAR;
    valorAtual      VARCHAR;
    stPrimeiroNivel VARCHAR;
    stChavePrimeiroNivel VARCHAR;

    inCont      INTEGER;
    inMaximo    INTEGER;
    inTamanho   INTEGER;
    inContTamanho   INTEGER;
BEGIN

    stRetorno := '''';

    SELECT
        split_part( stCodigo , ''.'', 1 )::varchar
    INTO stPrimeiroNivel;

    inMaximo := 10;
    inCont := 2;
    while ( inCont < inMaximo ) loop

        SELECT
            split_part( stCodigo , ''.'', inCont )::varchar
        INTO valorAtual;

        inTamanho := length ( valorAtual );

        IF ( valorAtual != '''') THEN
            inContTamanho := 0;
            stRetornoAux := '''';
            WHILE ( inContTamanho < inTamanho ) LOOP
                stRetornoAux := stRetornoAux||''0'';
                inContTamanho := inContTamanho + 1;
            END LOOP;
            stRetorno := stRetorno||''.''||stRetornoAux;
        END IF;


        inCont := inCont + 1;
    END LOOP;

    stChavePrimeiroNivel := stPrimeiroNivel||stRetorno;

    SELECT nom_localizacao from imobiliario.localizacao
    into stRetorno
    WHERE codigo_composto = stChavePrimeiroNivel;

    RETURN stRetorno; 
END;
' LANGUAGE 'plpgsql';

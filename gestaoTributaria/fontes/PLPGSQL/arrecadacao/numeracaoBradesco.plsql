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
* $Id: numeracaoBradesco.plsql 63292 2015-08-13 13:57:29Z arthur $
*
* Casos d uso: uc-05.03.11
*/
CREATE OR REPLACE FUNCTION numeracaoBradesco(VARCHAR,VARCHAR) RETURNS VARCHAR AS $$
DECLARE
    stCarteira      VARCHAR;
    stConvenio      VARCHAR;
    stNumeracao     VARCHAR;
    stNumAnterior   INTEGER;
    inCodConvenio   INTEGER;
    inNumCasas      INTEGER;
    inCont          INTEGER;
    stMascara       VARCHAR;
    reRecord        RECORD;
BEGIN
    stCarteira      := LPAD( $1, 2 , '0' );
    inCodConvenio   := $2::INTEGER;
    stConvenio := recuperaNumConvenio(inCodConvenio);

    inNumCasas := 17 - ( char_length( stConvenio ));
    inCont := 0;
    stMascara := '';
    while ( inCont < inNumCasas ) loop
        stMascara := stMascara||'9';
        inCont := incont + 1;
    end loop;
    
    stNumAnterior := ultimaNumeracao(inCodConvenio, char_length( stConvenio )::INTEGER)::INTEGER;

    IF stNumAnterior > 0 THEN
        stNumeracao := stConvenio || lpad((to_number(stNumAnterior::VARCHAR,stMascara::VARCHAR)+1)::VARCHAR, inNumCasas,'0');
    ELSE
        stNumeracao := stConvenio || lpad(to_number('1',stMascara::VARCHAR)::VARCHAR, inNumCasas,'0');
    END IF;

    return stNumeracao;
END;

$$ LANGUAGE 'plpgsql';
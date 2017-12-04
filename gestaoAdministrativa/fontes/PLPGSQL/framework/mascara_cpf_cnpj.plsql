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

CREATE OR REPLACE FUNCTION publico.mascara_cpf_cnpj ( VARCHAR
                                                    , VARCHAR ) RETURNS VARCHAR AS $$
DECLARE
    stValor         ALIAS FOR $1;
    stTipo          ALIAS FOR $2;
    stValorMasc     VARCHAR := '';
    stMascara       VARCHAR := '';

BEGIN

    IF ( UPPER(stTipo) = 'CPF' )
    THEN
       stValorMasc := LPAD(stValor,11,'0');
       stMascara := SUBSTR( stValorMasc,1,3 ) || '.' || SUBSTR( stValorMasc,4,3 ) || '.' || SUBSTR( stValorMasc,7,3 ) || '-' || SUBSTR( stValorMasc, 10,2 ) ;
    ELSEIF ( UPPER(stTipo) = 'CNPJ' )
    THEN
       stValorMasc := LPAD(stValor,14,'0');
       stMascara := SUBSTR( stValorMasc,1,2 ) || '.' || SUBSTR( stValorMasc,3,3 ) || '.' || SUBSTR( stValorMasc,6,3 ) || '/' || SUBSTR( stValorMasc, 9,4 ) || '-' || SUBSTR( stValorMasc,13,2) ;
    END IF;


RETURN stMascara;

END;

$$ LANGUAGE 'plpgsql';

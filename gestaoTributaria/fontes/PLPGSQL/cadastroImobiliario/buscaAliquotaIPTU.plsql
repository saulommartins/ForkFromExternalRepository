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
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: $
*
* Caso de uso: uc-05.01.03
*/

CREATE OR REPLACE FUNCTION imobiliario.buscaAliquotaIPTU(INTEGER)  RETURNS VARCHAR AS $$
DECLARE
    inIM                        ALIAS FOR $1;
    stRetorno                   VARCHAR;
    boEdificacao                BOOLEAN;

BEGIN

    SELECT
        arrecadacao.verificaEdificacaoImovel(inIM)
    INTO
        boEdificacao;

    if ( boEdificacao = true ) then
        SELECT
            ARRECADACAO.FN_BUSCA_TABELA_CONVERSAO( 1 , 2008 , 'Predial' , '' , '' , '' )
        INTO
            stRetorno;
    else
        SELECT
            ARRECADACAO.FN_BUSCA_TABELA_CONVERSAO( 1 , 2008 , 'Territorial' , '' , '' , '' )
        INTO
            stRetorno;
    end if;

    RETURN stRetorno;
END;
$$ LANGUAGE 'plpgsql';

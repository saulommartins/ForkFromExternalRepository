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
* $Id: ultimaNumeracaoConsolidacao.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.19
* Caso de uso: uc-05.03.11
*/

/*
$Log$
Revision 1.6  2006/12/13 16:52:10  dibueno
caso de uso

Revision 1.5  2006/12/13 12:44:40  dibueno
Alterações referente ao uso de 17 caracteres

Revision 1.4  2006/11/23 11:28:50  dibueno
Alterações no procedimento de casas da numeracao

Revision 1.3  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION ultimaNumeracaoConsolidacao( integer, integer ) RETURNS VARCHAR AS '
DECLARE
    stTmp varchar;
    inCodConvenio ALIAS FOR $1;
    inNumCasas  ALIAS FOR $2;
BEGIN

            SELECT
                substring( lpad((max(numeracao_consolidacao)::int), 17,''0'') from (inNumCasas+1) for char_length( max(numeracao_consolidacao)))
            INTO stTmp
            FROM arrecadacao.carne_consolidacao;
            --WHERE cod_convenio = inCodConvenio;

    return stTmp;
END;

' LANGUAGE 'plpgsql';

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
* $Id: consultaCarnePagoReemissoes.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-5.3.11
* Caso de uso: uc-05.03.11
*/

/*
$Log$
Revision 1.5  2007/03/12 21:25:18  dibueno
*** empty log message ***

Revision 1.4  2007/02/05 11:06:40  dibueno
Melhorias da consulta da arrecadacao

Revision 1.3  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION arrecadacao.consultaCarnePagoReemissao( INTEGER ) RETURNS VARCHAR AS '
DECLARE
    stCodParcela      ALIAS FOR $1;
    stNumeracaoPaga  varchar := null;
    stRetorno        varchar := null;

BEGIN

    SELECT
        coalesce ( apag.numeracao, null )
    INTO
        stNumeracaoPaga
    FROM  (
        SELECT
            numeracao
        FROM                                                                         
        arrecadacao.fn_lista_reemissoes( stCodParcela ) as                   
        (   cod_parcela integer,                                                 
            numeracao   varchar,                                                 
            vencimento  varchar,
            data_pagamento date,
            ocorrencia_pagamento integer
        )
    ) as carnes
    INNER JOIN arrecadacao.pagamento as apag
    ON carnes.numeracao = apag.numeracao
    ORDER BY carnes.numeracao DESC
    LIMIT 1
    ;
  
    IF stNumeracaoPaga IS NULL THEN
        stRetorno := null;
    ELSE
        stRetorno := stNumeracaoPaga;
    END IF;

    RETURN stRetorno;

END;
' LANGUAGE 'plpgsql';

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
* $Id: fn_busca_soma_pagamento_calculo.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.10
*/

/*
$Log$
Revision 1.1  2007/02/09 09:54:11  dibueno
*** empty log message ***

*/

CREATE OR REPLACE FUNCTION arrecadacao.fn_busca_soma_pagamento_calculo(varchar,integer,integer) returns numeric as '
declare
    stNumeracao             ALIAS FOR $1;
    inCodConvenio           ALIAS FOR $2;
    inOcorrenciaPagamento   ALIAS FOR $3;
    stSql                   VARCHAR;
    nuRetorno               NUMERIC := 0.00;
    reRecord                RECORD;
begin

    SELECT
        sum(valor)
    INTO
        nuRetorno
    FROM
        arrecadacao.pagamento_calculo as apagc
    WHERE
        apagc.numeracao = stNumeracao
        AND apagc.cod_convenio = inCodConvenio
        AND apagc.ocorrencia_pagamento = inOcorrenciaPagamento
    ;

    IF ( nuRetorno < 0.00 ) THEN
        nuRetorno := 0.00;
    END IF;

   return nuRetorno::numeric(14,2);
end;
'language 'plpgsql';

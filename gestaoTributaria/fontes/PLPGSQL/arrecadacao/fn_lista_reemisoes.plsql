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
* $Id: fn_lista_reemisoes.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.00
*/

/*
$Log$
Revision 1.5  2007/03/12 21:25:18  dibueno
*** empty log message ***

Revision 1.4  2007/02/05 11:06:56  dibueno
Melhorias da consulta da arrecadacao

Revision 1.3  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION arrecadacao.fn_lista_reemissoes (integer) returns SETOF RECORD AS '
DECLARE
   inCodParcela         ALIAS FOR $1;
   stSql                VARCHAR;
   reRecord1            RECORD;
BEGIN

    stSql := ''
               SELECT c.cod_parcela
                    , c.numeracao
                    , r.vencimento
                    , ( select data_pagamento from arrecadacao.pagamento
                        where numeracao = c.numeracao
                        order by ocorrencia_pagamento desc
                        limit 1
                    ) as data_pagamento
                    , ( select ocorrencia_pagamento from arrecadacao.pagamento
                        where numeracao = c.numeracao
                        order by ocorrencia_pagamento desc
                        limit 1
                    )::integer as ocorrencia_pagamento
                 from (    select (to_char(vencimento,''''dd/mm/YYYY''''))::varchar as vencimento
                                , timestamp
                             from arrecadacao.parcela_reemissao 
                            where cod_parcela= ''||inCodParcela||''
                         order by timestamp desc
                      ) as r
                    , (    select cod_parcela
                                , numeracao
                                , timestamp 
                             from arrecadacao.carne 
                            where cod_parcela=''||inCodParcela||''
                         order by timestamp desc
                                , numeracao desc
                           offset 1
                      ) as c
               UNION 
               SELECT p.cod_parcela
                    , c.numeracao
                    , p.vencimento
                    , ( select data_pagamento from arrecadacao.pagamento
                        where numeracao = c.numeracao
                        order by ocorrencia_pagamento desc
                        limit 1
                    ) as data_pagamento
                    , ( select ocorrencia_pagamento from arrecadacao.pagamento
                        where numeracao = c.numeracao
                        order by ocorrencia_pagamento desc
                        limit 1
                    )::integer as ocorrencia_pagamento
                 from ( select (to_char(vencimento,''''dd/mm/YYYY''''))::varchar as vencimento
                                , cod_parcela
                             from arrecadacao.parcela
                            where cod_parcela=''||inCodParcela||''
                      ) as p
                    , (    select cod_parcela
                                , numeracao
                                , timestamp
                             from arrecadacao.carne
                            where cod_parcela=''||inCodParcela||''
                         order by timestamp desc
                                , numeracao desc
                            limit 1
                           offset 0
                      ) as c

        '';


    FOR reRecord1 IN EXECUTE stSql LOOP
      return next reRecord1;
    END LOOP;

    return;
END;
' LANGUAGE 'plpgsql';


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
* Verifica Pagamento de Parcela Unica de Lancamento
* Recebe qualquer parcela do lancamento 
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: verificaPagUnica.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-5.3.19
* Caso de uso: uc-05.03.19
*/

/*
$Log$
Revision 1.2  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION arrecadacao.verificaPagUnica( integer , date )  RETURNS boolean AS '
DECLARE
    inCodParcela    ALIAS FOR $1;
    dtDataBase      ALIAS FOR $2;    
    inCodLancamento integer;
    stNumeracao     varchar;
BEGIN
    -- lancamento da parcela
        select cod_lancamento 
          into inCodLancamento 
          from arrecadacao.parcela 
         where cod_parcela=inCodParcela;

    -- recupera todas as parcelas unicas, e verifica se estao pagas
    select numeracao    
      into stNumeracao
      from arrecadacao.pagamento 
      JOIN arrecadacao.tipo_pagamento
        ON tipo_pagamento.cod_tipo  = pagamento.cod_tipo
       AND tipo_pagamento.pagamento = true
     where numeracao in (   select numeracao 
                              from arrecadacao.carne 
                             where cod_parcela in (     select cod_parcela 
                                                          from arrecadacao.parcela 
                                                         where nr_parcela = 0 
                                                           and cod_lancamento = inCodLancamento
                                                  )
                        )
       and data_pagamento < dtDataBase;
    if stNumeracao is not null then
        return true;
    else
        return false;
    end if ;
END;
' LANGUAGE 'plpgsql';

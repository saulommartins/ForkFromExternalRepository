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
* script de funcao PLSQL
* 
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br

* $Revision: 23095 $
* $Name$
* $Autor: MArcia $
* Date: 2006/04/20 10:50:00 $
*
* Caso de uso: uc-04.05.13
*
* Objetivo:  recebe a base de ir liquida e verifica na tabela
* vigente para a competencia atual. Retorna a aliquota que devera ser aplicada.
* Assume sempre o codigo da tabela como 1.
*/




CREATE OR REPLACE FUNCTION pega1PercDescontoFaixaDescontoIrrf(numeric) RETURNS numeric as '

DECLARE
    nuValorBase                      ALIAS FOR $1;
    stTimestampTabela                VARCHAR;

    inCodTabela                      INTEGER := 1;
    nuPercDescontoIrrf               NUMERIC := 0.00;

stEntidade VARCHAR := recuperarBufferTexto(''stEntidade'');
 BEGIN


    stTimestampTabela := pega1TimestampTabelaIrrf();

    nuPercDescontoIrrf := selectIntoNumeric(''
        SELECT aliquota
        FROM  folhapagamento''||stEntidade||''.faixa_desconto_irrf
        WHERE cod_tabela = ''||inCodTabela||''
          AND timestamp = ''''''||stTimestampTabela||''''''
          AND ''||nuValorBase||'' between vl_inicial AND vl_final''
        ) ;

    IF nuPercDescontoIrrf is null THEN
       nuPercDescontoIrrf := 0.00;
    END IF;


    RETURN nuPercDescontoIrrf;
END;
' LANGUAGE 'plpgsql';




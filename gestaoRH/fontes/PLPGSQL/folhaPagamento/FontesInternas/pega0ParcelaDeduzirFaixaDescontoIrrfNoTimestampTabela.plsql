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
* Date: 2006/01/03 10:50:00 $
*
* Caso de uso: uc-04.05.48
*
* Objetivo: Recebe o exato timestamp ja identificado pela funcao
* pega0TimestampTabelaIrrfNaData e o valor da base e retorna a parcela a deduzir 
* que sera aplicado sobre esta base para gerar o desconto de irrf.
* Assume sempre o codigo da tabela como 1.
*/



CREATE OR REPLACE FUNCTION pega0ParcelaDeduzirFaixaDescontoIrrfNoTimestampTabela(varchar,numeric) RETURNS numeric as '

DECLARE
    stTimestamp                      ALIAS FOR $1;
    nuValorBase                      ALIAS FOR $2;

    inCodTabela                      INTEGER := 1;
    nuParcelaDeduzir                 NUMERIC := 0.00;

stEntidade VARCHAR := recuperarBufferTexto(''stEntidade'');
 BEGIN


    nuParcelaDeduzir := selectIntoNumeric(''
        SELECT parcela_deduzir
        FROM  folhapagamento''||stEntidade||''.faixa_desconto_irrf
        WHERE cod_tabela = ''||inCodTabela||''
          AND timestamp = to_date(stTimestamp, ''''yyyy-mm-dd 99:99:99'''')
          AND ''||nuValorBase||'' between vl_inicial AND vl_final''
        ) ;

    IF nuParcelaDeduzir is null THEN
       nuParcelaDeduzir := 0.00;
    END IF;


    RETURN nuParcelaDeduzir;
END;
' LANGUAGE 'plpgsql';


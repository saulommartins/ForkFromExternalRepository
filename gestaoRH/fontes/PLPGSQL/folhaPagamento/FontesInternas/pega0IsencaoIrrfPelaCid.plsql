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
* Date: 2006/01/25 10:50:00 $
*
* Caso de uso: uc-04.05.13
*
* Objetivo: A partir da cid do servidor e feita uma busca, pelo 
* timestamp da tabela_irrf ( funcao pega0TimestampTabelaIrrfNaData(stDataFinalCompetencia) )
* A tabela de irrf default e a "1", unica ate o momento.

*/
--drop function pega0IsencaoIrrfPelaCid(integer,varchar);


CREATE OR REPLACE FUNCTION pega0IsencaoIrrfPelaCid(integer,varchar) RETURNS boolean as '

DECLARE
    inCodCid                ALIAS FOR $1;
    stTimestamp             ALIAS FOR $2;

    inCodTabela             INTEGER := 1;
    inCodCidIrrf            INTEGER;
    boRetorno               BOOLEAN := TRUE;
stEntidade VARCHAR := recuperarBufferTexto(''stEntidade'');
 BEGIN


   inCodCidIrrf := selectIntoInteger (''
        SELECT cod_cid
          FROM folhapagamento''||stEntidade||''.tabela_irrf_cid
          WHERE timestamp = ''''''||stTimestamp||''''''
            AND cod_cid = ''||inCodCid||''
            AND cod_tabela = ''||inCodTabela
                          );

   IF inCodCidIrrf is null THEN
      boRetorno := FALSE ;
   END IF;

   RETURN boRetorno;

END;
' LANGUAGE 'plpgsql';


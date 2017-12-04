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
* Date: 2006/01/04 10:50:00 $
*
* Caso de uso: uc-04.05.48
*
* Objetivo: 

*/




CREATE OR REPLACE FUNCTION pega0EventoInformDeDeducaoIrrfDependNoTimestampTab(varchar) RETURNS integer as '

DECLARE
    stTimestamp              ALIAS FOR $1;
    inCodTipo                INTEGER := 1;
    inCodTabela              INTEGER := 1;
    inCodEvento              INTEGER;
stEntidade VARCHAR := recuperarBufferTexto(''stEntidade'');
 BEGIN

    inCodEvento := selectIntoInteger(''
           SELECT cod_evento
             FROM folhapagamento''||stEntidade||''.tabela_irrf_evento
             WHERE cod_tipo = ''||inCodTipo||''
               AND cod_tabela = ''||inCodTabela||''
               AND timestamp = ''''''||stTimestamp||''''''
                              '');

    RETURN inCodEvento;
END;
' LANGUAGE 'plpgsql';


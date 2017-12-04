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
--/**
--    * Função PLSQL
--    * Data de Criação: 00/00/0000
--
--
--    * @author Projetista: Vandré Miguel Ramos
--
--    * @package URBEM
--    * @subpackage
--
--    $Revision: 23101 $
--    $Author: souzadl $
--    $Date: 2007-06-06 10:17:40 -0300 (Qua, 06 Jun 2007) $
--
--    * Casos de uso: uc-04.05.24
--*/


CREATE OR REPLACE FUNCTION  inserirEventoAutomaticoDescontoAdiantamento(INTEGER,VARCHAR) RETURNS BOOLEAN as $$

DECLARE
    inCodTipo                   ALIAS FOR $1;
    stDesdobramento             ALIAS FOR $2;

    inCodContrato               INTEGER;
    inCodPeriodoMovimentacao    INTEGER;
    inCodEvento                 INTEGER;
    stEntidade VARCHAR := recuperarBufferTexto('stEntidade');
BEGIN
    inCodContrato              := recuperarBufferInteiro('inCodContrato');
    inCodPeriodoMovimentacao   := recuperarBufferInteiro('inCodPeriodoMovimentacao');
    inCodEvento := selectIntoInteger(' SELECT cod_evento
                               FROM folhapagamento'||stEntidade||'.decimo_evento
                                  , (SELECT MAX(timestamp) as timestamp
                                       FROM folhapagamento'||stEntidade||'.decimo_evento) as max_timestamp
                              WHERE decimo_evento.timestamp  = max_timestamp.timestamp
                                AND decimo_evento.cod_tipo   = '||inCodTipo);

    return insertRegistroEventoAutomaticoDecimo(inCodContrato,inCodPeriodoMovimentacao,inCodEvento,stDesdobramento);
END;
$$LANGUAGE 'plpgsql';


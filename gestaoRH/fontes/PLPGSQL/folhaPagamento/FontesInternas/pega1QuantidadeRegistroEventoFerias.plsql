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
--*
-- script de funcao PLSQL
-- 
-- URBEM Soluções de Gestão Pública Ltda
-- www.urbem.cnm.org.br
--
-- $Revision: 23095 $
-- $Name$
-- $Autor: Marcia $
-- Date: 2006/04/18 10:50:00 $
--
-- Caso de uso: uc-04.05.48
--
-- Objetivo: recuperar a quantidade do registro de um evento, no instante do calculo
--  para o contrato e periodo de movimentacao atual.
--/
--


CREATE OR REPLACE FUNCTION pega1QuantidadeRegistroEvento() RETURNS numeric as '
DECLARE
    inCodContrato              INTEGER;
    inCodEvento                INTEGER;
    inCodPeriodoMovimentacao   INTEGER;
    nuValorQtd                 NUMERIC := 0.00;

stEntidade VARCHAR := recuperarBufferTexto(''stEntidade'');
 BEGIN

    inCodContrato := recuperarBufferInteiro(''inCodContrato'');
    inCodEvento := recuperarBufferInteiroPilha(''inCodEvento'');
    inCodPeriodoMovimentacao := recuperarBufferInteiro(''inCodPeriodoMovimentacao'');

    nuValorQtd := pega0QuantidadeRegistroEventoDoContratoNoPeriodo(inCodContrato, inCodEvento, inCodPeriodoMovimentacao );

    RETURN nuValorQtd;
END;
'LANGUAGE 'plpgsql';

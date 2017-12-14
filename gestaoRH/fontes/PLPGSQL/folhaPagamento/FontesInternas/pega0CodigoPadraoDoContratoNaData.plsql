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
--
-- script de funcao PLSQL
-- 
-- URBEM Soluções de Gestão Pública Ltda
-- www.urbem.cnm.org.br
--
-- $Revision: 23095 $
-- $Name$
-- $Autor: Marcia $
-- Date: 2005/12/13 $
--
-- Caso de uso: uc-04.05.48
--
-- Objetivo: Recebe o codigo do contrato e a data de referencia 
-- retornando o codigo do padrao .
--
--


CREATE OR REPLACE FUNCTION pega0CodigoPadraoDoContratoNaData(integer,varchar) RETURNS Integer as '

DECLARE
    inCodContrato             ALIAS FOR $1;
    stTimestamp               ALIAS FOR $2;

    inCodPadrao               INTEGER;

stEntidade VARCHAR := recuperarBufferTexto(''stEntidade'');
 BEGIN

     inCodPadrao := selectIntoInteger
      ( ''SELECT cod_padrao
          FROM pessoal''||stEntidade||''.contrato_servidor_padrao
        	  WHERE cod_contrato = ''||inCodContrato ||''
          AND timestamp <= ''''''||stTimestamp||''''''
          ORDER BY timestamp desc 
          LIMIT 1 '') ;

    RETURN inCodPadrao  ;

END;

' LANGUAGE 'plpgsql';



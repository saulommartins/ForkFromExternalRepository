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
-- $Revision: 26696 $
-- $Name$
-- $Autor: Marcia $
-- Date: 2005/11/18 10:50:00 $
--
-- Caso de uso: uc-04.05.48
--
-- Objetivo: Recebe o codigo do contrato e data e retorna a sub divisao em relacao a data informada
--/



CREATE OR REPLACE FUNCTION pega0SubDivisaoDoContratoNaData(integer,varchar) RETURNS integer as '

DECLARE
    inCodContratoParametro  ALIAS FOR $1;
    stTimestamp             ALIAS FOR $2;
    inCodSubDivisao         INTEGER := 0;
    inCodContrato           INTEGER;
stEntidade VARCHAR := recuperarBufferTexto(''stEntidade'');
 BEGIN
    inCodContrato := recuperaContratoServidorPensionista(inCodContratoParametro);

    inCodSubDivisao := selectIntoInteger(''
        SELECT cod_sub_divisao 
          FROM pessoal''||stEntidade||''.contrato_servidor_sub_divisao_funcao as cssdf
          WHERE cssdf.cod_contrato = ''||inCodContrato||''
          ORDER BY cssdf.timestamp desc 
           LIMIT 1''
               ); 

    RETURN inCodSubDivisao;
END;
' LANGUAGE 'plpgsql';


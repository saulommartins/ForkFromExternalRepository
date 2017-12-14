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
-- $Revision: 26696 $
-- $Name$
-- $Autor: Diego $
-- Date: 2005/12/12 10:50:00 $
--
-- Caso de uso: uc-04.05.48
--
-- Objetivo: Recebe o codigo do contrato e a data e retorna 
-- a especializacao da funcao do contrato
--



CREATE OR REPLACE FUNCTION pega0EspecialidadeDoContratoNaData(integer,varchar) RETURNS integer as $$

DECLARE
    inCodContratoParametro  ALIAS FOR $1;
    stTimestamp             ALIAS FOR $2;
    dtTimestamp             DATE;
    inCodEspecialidade      INTEGER := 0;
    inCodContrato           INTEGER;
stEntidade VARCHAR := recuperarBufferTexto('stEntidade');
 BEGIN
    inCodContrato := recuperaContratoServidorPensionista(inCodContratoParametro);


    dtTimestamp = to_date(stTimestamp, 'yyyy-mm-dd');

    inCodEspecialidade := selectIntoInteger('        
         SELECT cod_especialidade
           FROM (SELECT contrato_servidor_funcao.timestamp
                      , cod_contrato 
                   FROM pessoal'||stEntidade||'.contrato_servidor_funcao
                  WHERE contrato_servidor_funcao.vigencia <= '''||dtTimestamp||'''
                    AND contrato_servidor_funcao.cod_contrato = '||inCodContrato||'
               ORDER BY contrato_servidor_funcao.timestamp desc
                  LIMIT 1) as funcao, 
                (SELECT contrato_servidor_especialidade_funcao.timestamp
                      , cod_contrato
                      , cod_especialidade
                   FROM pessoal'||stEntidade||'.contrato_servidor_especialidade_funcao
                --  WHERE contrato_servidor_especialidade_funcao.vigencia <= dtTimestamp
                  WHERE contrato_servidor_especialidade_funcao.cod_contrato = '||inCodContrato||'
               ORDER BY contrato_servidor_especialidade_funcao.timestamp desc
                  LIMIT 1) as especialidade
         WHERE funcao.cod_contrato = especialidade.cod_contrato
           AND funcao.timestamp = especialidade.timestamp');
    IF inCodEspecialidade IS NULL THEN
        inCodEspecialidade := 0;
    END IF;        
    RETURN inCodEspecialidade;

END;
$$ LANGUAGE 'plpgsql';


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
-- Date: 2006/05/26 10:50:00 $
--
-- Caso de uso: uc-04.05.15
-- Caso de uso: uc-04.05.48
--
-- Objetivo: 
--
--



CREATE OR REPLACE FUNCTION pega1IdadeLimiteSalarioFamiliaEstatutario() RETURNS integer as '

DECLARE
    stDataFinalCompetencia      VARCHAR;
    dtTimestamp                 DATE;
    inIdadeLimite               INTEGER := 0;
    inCodRegimePrevidenciario   INTEGER := 0;
stEntidade VARCHAR := recuperarBufferTexto(''stEntidade'');
 BEGIN


    stDataFinalCompetencia := ''2006-04-30 23:59:59'';

    dtTimestamp = to_date( stDataFinalCompetencia, ''yyyy-mm-dd'' );

    -- direciona para o regime_previdenciario RPPS - vinculado ao estatuto 
    -- tabela regimes 1 = clt e 2 = estatutario - tabelas fixas.
    inCodRegimePrevidenciario := 2;

    inIdadeLimite := selectIntoInteger(''
         SELECT idade_limite 
           FROM folhapagamento''||stEntidade||''.salario_familia
          WHERE cod_regime_previdencia = ''||inCodRegimePrevidenciario||''
            AND vigencia <= ''''''||dtTimestamp||''''''
       ORDER BY timestamp desc 
          LIMIT 1''
               ); 
    IF inIdadeLimite is null THEN
       inIdadeLimite := 0;
    END IF;

    RETURN inIdadeLimite;
END;
' LANGUAGE 'plpgsql';


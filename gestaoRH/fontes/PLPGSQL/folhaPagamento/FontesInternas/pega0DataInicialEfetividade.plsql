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
-- $Autor:  Marcia$
-- Date: 2005/10/04 10:50:00 $
--
-- Caso de uso: uc-04.05.48
--
-- Objetivo: Nao recebe parametros mas retorna o timestamp do final da efetividade ativa
--



CREATE OR REPLACE FUNCTION pega0DataInicialEfetividade() RETURNS varchar as '

DECLARE
    stDataInicialEfetividade           VARCHAR :='''';
stEntidade VARCHAR := recuperarBufferTexto(''stEntidade'');
 BEGIN


   stDataInicialEfetividade := selectIntoVarchar(''select
     to_char(FPM.dt_inicial, ''''yyyy-mm-dd'''') as dt_inicial                 
   FROM                                                               
       folhapagamento''||stEntidade||''.periodo_movimentacao FPM,                       
       folhapagamento''||stEntidade||''.periodo_movimentacao_situacao FPMS,             
       (SELECT                                                        
           MAX(timestamp) as timestamp                                
       FROM folhapagamento''||stEntidade||''.periodo_movimentacao_situacao              
       WHERE situacao = ''''a'''') as MAX_TIMESTAMP                         
   WHERE FPM.cod_periodo_movimentacao = FPMS.cod_periodo_movimentacao 
   AND   FPMS.timestamp               = MAX_TIMESTAMP.timestamp       
   AND   FPMS.situacao                = ''''a'''' '');                           


    RETURN stDataInicialEfetividade;
END;
' LANGUAGE 'plpgsql';


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
-- $Revision: 27691 $
-- $Name$
-- $Autor: Marcia $
-- Date: 2005/10/04 10:50:00 $
--
-- Caso de uso: uc-04.05.48
--
-- Objetivo: Recebe o codigo do contrato e retorna o codigo do servidor.
--/
--


CREATE OR REPLACE FUNCTION pega0QuantidadeQuinquenios(DATE,INTEGER) RETURNS integer as '

DECLARE
    dtLei                       ALIAS FOR $1;
    inCodContrato               ALIAS FOR $2;
    dtAdmissaoPosse             DATE;
    dtCompetencia               DATE;
    inResultado                 INTEGER;
    stAdmissaoPosse             VARCHAR;
stEntidade VARCHAR := recuperarBufferTexto(''stEntidade'');
    stExercicio                 VARCHAR := recuperarBufferTexto(''stExercicioSistema'');
 BEGIN

    stAdmissaoPosse := selectIntoVarchar(''SELECT configuracao.valor
                                   FROM administracao.configuracao 
                                  WHERE cod_modulo = 22
                                    AND parametro = ''''dtContagemInicial''''||stEntidade||
                                    AND exercicio = ''||stExercicio);
    IF stAdmissaoPosse = ''dtPosse'' THEN
        dtAdmissaoPosse := selectIntoVarchar(''SELECT contrato_servidor_nomeacao_posse.dt_posse
                                       FROM pessoal''||stEntidade||''.contrato_servidor_nomeacao_posse
                                          , (SELECT cod_contrato
                                                  ,  max(timestamp) as timestamp
                                               FROM pessoal''||stEntidade||''.contrato_servidor_nomeacao_posse
                                             GROUP BY cod_contrato) as max_contrato_servidor_nomeacao_posse
                                      WHERE contrato_servidor_nomeacao_posse.cod_contrato = max_contrato_servidor_nomeacao_posse.cod_contrato
                                        AND contrato_servidor_nomeacao_posse.timestamp = max_contrato_servidor_nomeacao_posse.timestamp
                                        AND contrato_servidor_nomeacao_posse.cod_contrato = ''||inCodContrato);
    ELSE
        dtAdmissaoPosse := selectIntoVarchar(''SELECT contrato_servidor_nomeacao_posse.dt_nomeacao
                                       FROM pessoal''||stEntidade||''.contrato_servidor_nomeacao_posse
                                          , (SELECT cod_contrato
                                                  ,  max(timestamp) as timestamp
                                               FROM pessoal''||stEntidade||''.contrato_servidor_nomeacao_posse
                                             GROUP BY cod_contrato) as max_contrato_servidor_nomeacao_posse
                                      WHERE contrato_servidor_nomeacao_posse.cod_contrato = max_contrato_servidor_nomeacao_posse.cod_contrato
                                        AND contrato_servidor_nomeacao_posse.timestamp = max_contrato_servidor_nomeacao_posse.timestamp
                                        AND contrato_servidor_nomeacao_posse.cod_contrato = ''||inCodContrato);
    END IF;
    
    dtCompetencia := selectIntoVarchar(''SELECT dt_final
                                 FROM folhapagamento''||stEntidade||''.periodo_movimentacao 
                             ORDER BY cod_periodo_movimentacao DESC 
                                LIMIT 1'');
    IF dtAdmissaoPosse < dtLei THEN
        SELECT INTO inResultado (SELECT TO_DATE(dtLei,''yyyy-mm-dd'')-TO_DATE(dtCompetencia,''yyyy-mm-dd''));
    ELSE
        SELECT INTO inResultado (SELECT TO_DATE(dtAdmissaoPosse,''yyyy-mm-dd'')-TO_DATE(dtCompetencia,''yyyy-mm-dd''));        
    END IF;
    inResultado := inResultado/(365*5);

    RETURN inResultado;
END;
' LANGUAGE 'plpgsql';


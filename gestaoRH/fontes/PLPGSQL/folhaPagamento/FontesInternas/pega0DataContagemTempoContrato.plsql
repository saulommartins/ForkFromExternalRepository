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
--    * Data de Criação: 02/04/2008
--
--
--    * @author Desenvolvedor: Diego Lemos de Souza
--
--    * Casos de uso: uc-04.00.00
--
--    $Id: pega0DataContagemTempoContrato.sql 31697 2008-08-04 19:33:31Z souzadl $ 
--*/
drop function pega0DataContagemTempoContrato();
CREATE OR REPLACE FUNCTION pega0DataContagemTempoContrato() RETURNS VARCHAR as $$
DECLARE
    stSql                       VARCHAR;
    stSql2                      VARCHAR;
    dtPosse                     DATE;
    dtNomeacao                  DATE;
    dtAdmissao                  DATE;
    dtContagemTempo             VARCHAR;
    crCursor                    REFCURSOR;
    stEntidade               VARCHAR := recuperarBufferTexto('stEntidade');
    inCodContrato               INTEGER := recuperarBufferInteiro('inCodContrato');
    stExercicio                 VARCHAR := recuperarBufferTexto('stExercicioSistema');
BEGIN
    stSql2 := 'SELECT contrato_servidor_nomeacao_posse.dt_posse
                    , contrato_servidor_nomeacao_posse.dt_nomeacao
                    , contrato_servidor_nomeacao_posse.dt_admissao
                 FROM pessoal'|| stEntidade ||'.contrato_servidor_nomeacao_posse
                    , (  SELECT cod_contrato
                              , max(timestamp) as timestamp
                           FROM pessoal'|| stEntidade ||'.contrato_servidor_nomeacao_posse
                       GROUP BY cod_contrato) as max_contrato_servidor_nomeacao_posse
                WHERE contrato_servidor_nomeacao_posse.cod_contrato = max_contrato_servidor_nomeacao_posse.cod_contrato
                  AND contrato_servidor_nomeacao_posse.timestamp = max_contrato_servidor_nomeacao_posse.timestamp
                  AND contrato_servidor_nomeacao_posse.cod_contrato = '|| inCodContrato;                
    OPEN crCursor FOR EXECUTE stSql2;
        FETCH crCursor INTO dtPosse,dtNomeacao,dtAdmissao;
    CLOSE crCursor;                              
    stSql := 'SELECT valor 
                FROM administracao.configuracao 
               WHERE parametro = ' || quote_literal('dtContagemInicial'|| stEntidade) ||'  AND exercicio = '|| quote_literal(stExercicio) ||' ';
    IF selectIntoVarchar(stSql) = 'dtPosse' THEN
        dtContagemTempo := dtPosse;
    END IF;
    IF selectIntoVarchar(stSql) = 'dtNomeacao' THEN
        dtContagemTempo := dtNomeacao;
    END IF;
    IF selectIntoVarchar(stSql) = 'dtAdmissao' THEN
        dtContagemTempo := dtAdmissao;
    END IF;          
    
    RETURN dtContagemTempo;
END;
$$ LANGUAGE 'plpgsql';
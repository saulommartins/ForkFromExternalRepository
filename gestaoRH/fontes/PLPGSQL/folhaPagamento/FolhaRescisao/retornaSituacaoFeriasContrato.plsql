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
/**
    * Função PLSQL
    * Data de Criação: 18/10/2006

    * @author Analista: Vandré Miguel Ramos

    * @package URBEM
    * @subpackage

    $Id: retornaSituacaoFeriasContrato.plsql 65944 2016-07-01 21:18:24Z michel $

    * Casos de uso: uc-04.05.18
*/

CREATE OR REPLACE FUNCTION retornaSituacaoFeriasContrato(integer,integer) RETURNS VARCHAR as $$

DECLARE

   inCodContrato                ALIAS FOR $1;
   inCodPeriodoMovimentacao     ALIAS FOR $2;

   stRetorno                    VARCHAR := '';
   stSql                        VARCHAR := '';
   stDataFinalCompetencia       VARCHAR := '';
   dtDataFinalCompetencia       DATE;
   dtFinalAquisitivo            VARCHAR;
   dtFinalAquisitivoTemp        DATE; 
   dtInicialAquisitivoTemp      DATE; 
   inAnoFinal                   INTEGER;
   stDiaMesFinal                VARCHAR;
   stDataRescisao               VARCHAR;   
   stContagemInicial            VARCHAR;   
   stExercicioAtual             VARCHAR;
   crCursor                     REFCURSOR; 
   stEntidade                   VARCHAR;
BEGIN
    stEntidade := recuperarBufferTexto('stEntidade');
    stDataRescisao  := recuperarBufferTexto('stDataRescisao');

    stSql := 'SELECT dt_final_aquisitivo
                      , dt_inicial_aquisitivo
                  FROM pessoal'||stEntidade||'.ferias
                 WHERE cod_contrato = '||inCodContrato||'
              ORDER BY cod_ferias 
                  DESC LIMIT 1';
    OPEN crCursor FOR EXECUTE stSql;
        FETCH crCursor INTO dtFinalAquisitivoTemp,dtInicialAquisitivoTemp;
    CLOSE crCursor;   

    IF( dtFinalAquisitivoTemp IS NULL ) THEN
        stExercicioAtual := recuperarBufferTexto('stExercicioAtual');
        stContagemInicial := selectIntoVarchar
                    ('SELECT valor 
                       FROM administracao.configuracao 
                      WHERE parametro = ''dtContagemInicial'||stEntidade||'''
                        AND exercicio = '|| quote_literal(stExercicioAtual) ||'
                        AND cod_modulo = 22');
        IF stContagemInicial = 'dtPosse' THEN
            dtFinalAquisitivoTemp := selectIntoVarchar('SELECT dt_posse
                                                 FROM pessoal'||stEntidade||'.contrato_servidor_nomeacao_posse
                                                    , (SELECT cod_contrato
                                                            , max(timestamp) as timestamp
                                                         FROM pessoal'||stEntidade||'.contrato_servidor_nomeacao_posse
                                                       GROUP BY cod_contrato) as max_contrato_servidor_nomeacao_posse
                                                WHERE contrato_servidor_nomeacao_posse.cod_contrato = max_contrato_servidor_nomeacao_posse.cod_contrato
                                                  AND contrato_servidor_nomeacao_posse.timestamp = max_contrato_servidor_nomeacao_posse.timestamp
                                                  AND contrato_servidor_nomeacao_posse.cod_contrato ='|| inCodContrato);
        END IF;
        IF stContagemInicial = 'dtAdmissao' THEN
            dtFinalAquisitivoTemp := selectIntoVarchar('SELECT dt_admissao
                                                 FROM pessoal'||stEntidade||'.contrato_servidor_nomeacao_posse
                                                    , (SELECT cod_contrato
                                                            , max(timestamp) as timestamp
                                                         FROM pessoal'||stEntidade||'.contrato_servidor_nomeacao_posse
                                                       GROUP BY cod_contrato) as max_contrato_servidor_nomeacao_posse
                                                WHERE contrato_servidor_nomeacao_posse.cod_contrato = max_contrato_servidor_nomeacao_posse.cod_contrato
                                                  AND contrato_servidor_nomeacao_posse.timestamp = max_contrato_servidor_nomeacao_posse.timestamp
                                                  AND contrato_servidor_nomeacao_posse.cod_contrato = '||inCodContrato);
        END IF;
        IF stContagemInicial = 'dtNomeacao' THEN
            dtFinalAquisitivoTemp := selectIntoVarchar('SELECT dt_nomeacao
                                                 FROM pessoal'||stEntidade||'.contrato_servidor_nomeacao_posse
                                                    , (SELECT cod_contrato
                                                            , max(timestamp) as timestamp
                                                         FROM pessoal'||stEntidade||'.contrato_servidor_nomeacao_posse
                                                       GROUP BY cod_contrato) as max_contrato_servidor_nomeacao_posse
                                                WHERE contrato_servidor_nomeacao_posse.cod_contrato = max_contrato_servidor_nomeacao_posse.cod_contrato
                                                  AND contrato_servidor_nomeacao_posse.timestamp = max_contrato_servidor_nomeacao_posse.timestamp
                                                  AND contrato_servidor_nomeacao_posse.cod_contrato = '||inCodContrato);
        END IF;
    END IF;

    IF dtFinalAquisitivoTemp >= stDataRescisao::date THEN
        stRetorno := '';
    ELSE
        inAnoFinal        := SUBSTR(dtFinalAquisitivoTemp::varchar,0,5);
        stDiaMesFinal     := SUBSTR(dtFinalAquisitivoTemp::varchar,6,5);
        dtFinalAquisitivo := inAnoFinal||'-'||stDiaMesFinal;

        SELECT ((to_date(dtFinalAquisitivo, 'yyyy-mm-dd') + interval '1 year')::DATE)-1 INTO dtFinalAquisitivo;

        IF (dtFinalAquisitivo::date > stDataRescisao::date ) THEN
        stRetorno := 'A';
        ELSE
        stRetorno := 'V'; 
        END IF;
    END IF;

RETURN stRetorno;
END;
$$LANGUAGE 'plpgsql';



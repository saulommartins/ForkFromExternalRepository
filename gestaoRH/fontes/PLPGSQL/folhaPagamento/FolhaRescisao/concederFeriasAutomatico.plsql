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

/*
    $Id: concederFeriasAutomatico.plsql 66317 2016-08-09 13:50:58Z michel $
*/

CREATE OR REPLACE FUNCTION concederFeriasAutomatico(integer,integer,varchar) RETURNS BOOLEAN as $$

DECLARE

   inCodContrato            ALIAS FOR $1;
   inCodPeriodoMovimentacao ALIAS FOR $2;
   stTipoFerias             ALIAS FOR $3;


   stDataFinalCompetencia   VARCHAR;
   dtDataFinalCompetencia   DATE;
   dtFinalAquisitivo        DATE;
   dtInicialAquisitivo      DATE; 

   dtFinalAquisitivoReg        DATE;
   dtInicialAquisitivoReg      DATE;

   inAnoInicial             INTEGER;
   stDiaMesInicial          VARCHAR;

   inAnoFinal               INTEGER;
   stDiaMesFinal            VARCHAR;

   inAnoCompetencia         INTEGER;
   stMesCompetencia         VARCHAR;

   stDataRescisao           VARCHAR;

   inCodFerias              INTEGER;

   boRetorno                BOOLEAN := TRUE;
   stSql                    VARCHAR := '';
   reRegistro               RECORD;
   stContagemInicial        VARCHAR;
   dtContagemInicial        VARCHAR;
   stExercicioAtual         VARCHAR;

   stEntidade VARCHAR := recuperarBufferTexto('stEntidade');
BEGIN
    stExercicioAtual := recuperarBufferTexto('stExercicioAtual');
    stDataRescisao := recuperarBufferTexto('stDataRescisao');

    inCodFerias := selectIntoInteger('SELECT MAX(cod_ferias)+1 as cod_ferias
                              FROM pessoal'||stEntidade||'.ferias');
    IF inCodFerias IS NULL THEN
        inCodFerias := 1;
    END IF;

    dtInicialAquisitivoReg := selectIntoVarchar('Select dt_inicial_aquisitivo
           FROM   pessoal'||stEntidade||'.ferias
                 ,pessoal'||stEntidade||'.lancamento_ferias
          WHERE cod_contrato = '||inCodContrato||'
       ORDER BY ferias.cod_ferias 
           DESC LIMIT 1 ');

    dtFinalAquisitivoReg := selectIntoVarchar('SELECT dt_final_aquisitivo
           FROM   pessoal'||stEntidade||'.ferias
                 ,pessoal'||stEntidade||'.lancamento_ferias
          WHERE cod_contrato = '||inCodContrato||'
       ORDER BY ferias.cod_ferias
           DESC LIMIT 1 ');

    -- VALIDAÇÃO PARA CASOS EM QUE NÃO FORAM PAGAS AS FÉRIAS EM NENHUM MOMENTO
        IF (dtInicialAquisitivoReg IS NULL) THEN
          stSql := 'SELECT valor 
                      FROM administracao.configuracao 
                     WHERE parametro = ''dtContagemInicial'||stEntidade||'''
                       AND exercicio = '|| quote_literal(stExercicioAtual);

          stContagemInicial := selectIntoVarchar(stSql); 

        IF (stContagemInicial = 'dtPosse') THEN
          dtContagemInicial := selectIntoVarchar('SELECT dt_posse 
                                           FROM pessoal'||stEntidade||'.contrato_servidor_nomeacao_posse
                                          WHERE cod_contrato = '||inCodContrato||'
                                            AND timestamp    = (SELECT MAX(timestamp)
                                                                  FROM pessoal'||stEntidade||'.contrato_servidor_nomeacao_posse
                                                                 WHERE cod_contrato = '||inCodContrato||')');
        ELSIF (stContagemInicial = 'dtNomeacao') THEN
               dtContagemInicial := selectIntoVarchar('SELECT dt_nomeacao 
                                                FROM pessoal'||stEntidade||'.contrato_servidor_nomeacao_posse
                                               WHERE cod_contrato = '||inCodContrato||'
                                                 AND timestamp    = (SELECT MAX(timestamp)
                                                                       FROM pessoal'||stEntidade||'.contrato_servidor_nomeacao_posse
                                                                      WHERE cod_contrato = '||inCodContrato||' )');
        ELSIF (stContagemInicial = 'dtAdmissao') THEN
               dtContagemInicial := selectIntoVarchar('SELECT dt_admissao 
                                                FROM pessoal'||stEntidade||'.contrato_servidor_nomeacao_posse
                                               WHERE cod_contrato = '||inCodContrato||'
                                                 AND timestamp    = (SELECT MAX(timestamp)
                                                                            FROM pessoal'||stEntidade||'.contrato_servidor_nomeacao_posse
                                                                           WHERE cod_contrato = '||inCodContrato||' )');
        END IF;

        inAnoInicial             := SUBSTR(dtContagemInicial,0,5);
        stDiaMesInicial          := SUBSTR(dtContagemInicial,6,5);
        dtInicialAquisitivo      := (inAnoInicial)||'-'||stDiaMesInicial;
         IF(stTipoFerias = 'V') THEN
             dtFinalAquisitivo      := cast(dtInicialAquisitivo as date) - cast( 1 as integer);
             dtFinalAquisitivo      := to_date((to_date(dtFinalAquisitivo::varchar,'yyyy-mm-dd') + interval '1 year')::varchar,'yyyy-mm-dd');
         ELSE
             dtFinalAquisitivo       := stDataRescisao;
         END IF;
      ELSE
        inAnoInicial             := SUBSTR(dtInicialAquisitivoReg::varchar,0,5);
        stDiaMesInicial          := SUBSTR(dtInicialAquisitivoReg::varchar,6,5);
        dtInicialAquisitivo      := (inAnoInicial+1)||'-'||stDiaMesInicial;
         -- SE EXISTIR FÉRIAS VENCIDAS LANÇA NOVO PERÍODO DE MOVIMENTACAO 'V'(VENCIDA) SE NÃO 'A'(A VENCER)
         -- SE NÃO LANÇA PERIODO FINAL IGUAL A DATA DA RESCISAO

         IF(stTipoFerias = 'V') THEN
             dtFinalAquisitivo      := to_date((to_date(dtFinalAquisitivoReg::varchar,'yyyy-mm-dd') + interval '1 year')::varchar,'yyyy-mm-dd');

             inAnoFinal             := SUBSTR(dtFinalAquisitivo::varchar,0,5);
             stDiaMesFinal          := SUBSTR(dtFinalAquisitivo::varchar,6,5);

             -- SE ANO BISSEXTO E MES FINAL É FEVEREIRO E DIA 28, ADICIONA 1 DIA 
             IF ( ((inAnoFinal % 4) = 0 AND (inAnoFinal % 100) <> 0) OR (inAnoFinal % 400) = 0 ) THEN
                IF ( stDiaMesFinal = '02-28' ) THEN
                    dtFinalAquisitivo := dtFinalAquisitivo + 1;
                END IF;
            END IF;
         ELSE
             dtFinalAquisitivo       := stDataRescisao;
         END IF;
      END IF;


    IF ((dtFinalAquisitivo != dtFinalAquisitivoReg) OR (dtFinalAquisitivoReg IS NULL))  THEN
        stSql := 'INSERT INTO pessoal'||stEntidade||'.ferias( cod_ferias
                                            ,cod_contrato
                                            ,cod_forma
                                            ,dias_ferias
                                            ,dias_abono
                                            ,faltas
                                            ,dt_inicial_aquisitivo
                                            ,dt_final_aquisitivo
                                            ,rescisao) 
                                     VALUES(  '||inCodFerias||'
                                            , '||inCodContrato||'
                                            ,1
                                            ,30
                                            ,0
                                            ,0
                                            ,'|| quote_literal(dtInicialAquisitivo) ||'
                                            ,'|| quote_literal(dtFinalAquisitivo) ||'
                                            ,TRUE)';

        EXECUTE stSql;

        stDataFinalCompetencia  := pega0DataFinalCompetenciaDoPeriodoMovimento(  inCodPeriodoMovimentacao );
        inAnoCompetencia        := SUBSTR(stDataFinalCompetencia,0,5);
        stMesCompetencia        := SUBSTR(stDataFinalCompetencia,6,2);



        stsql := 'INSERT INTO pessoal'||stEntidade||'.lancamento_ferias(  cod_ferias
                                                       , dt_inicio       
                                                       , dt_fim          
                                                       , dt_retorno      
                                                       , mes_competencia 
                                                       , ano_competencia 
                                                       , pagar_13        
                                                       , cod_tipo)        
                                                VALUES( '||incodferias||'
                                                       ,'|| quote_literal(stDataRescisao) ||'
                                                       ,'|| quote_literal(stDataRescisao) ||'
                                                       ,'|| quote_literal(stDataRescisao) ||'
                                                       ,'|| quote_literal(stMesCompetencia) ||'
                                                       ,'||inAnoCompetencia||'
                                                       ,FALSE
                                                       ,5)';

       EXECUTE stsql; 
    END IF;
    RETURN TRUE;
END;
$$LANGUAGE 'plpgsql';
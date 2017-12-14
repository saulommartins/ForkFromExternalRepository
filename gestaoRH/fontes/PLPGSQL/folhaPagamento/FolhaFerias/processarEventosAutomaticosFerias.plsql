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
--    * Data de Criação: 17/08/2006
--
--
--    * @author Analista: Vandré Miguel Ramos
--    * @author Desenvolvedor: Diego Lemos de Souza
--
--    * @package URBEM
--    * @subpackage
--
--    $Revision: 28747 $
--    $Name$
--    $Author: souzadl $
--    $Date: 2008-03-26 09:51:14 -0300 (Qua, 26 Mar 2008) $
--
--    * Casos de uso: uc-04.05.19
--*/

CREATE OR REPLACE FUNCTION processarEventosAutomaticosFerias() RETURNS BOOLEAN as $$

DECLARE
    inCodContrato                       INTEGER;
    inCodContratoAux                    INTEGER;
    inCodPeriodoMovimentacao            INTEGER;
    inContadorDependentes               INTEGER;
    inDiasServidor                      INTEGER;
    inQtdDependentesPensaoAlimenticia   INTEGER;
    inCodEvento                         INTEGER;
    boRetorno                           BOOLEAN;
    dtVigencia                          VARCHAR :='';
    stSql                               VARCHAR :='';
    stSqlAux                            VARCHAR :='';
    stDataFinalCompetencia              VARCHAR :='';
    stNatureza                          VARCHAR :='';
    stDataAtributoIpe                   VARCHAR;
    stMatAtributoIpe                    VARCHAR;
    reRegistro                          RECORD;
    reConfiguracao                      RECORD;
    crCursor                            REFCURSOR;
    stEntidade VARCHAR := recuperarBufferTexto('stEntidade');
BEGIN
    inCodContrato              := recuperarBufferInteiro('inCodContrato');
    inCodPeriodoMovimentacao   := recuperarBufferInteiro('inCodPeriodoMovimentacao');
    stDataFinalCompetencia     := substr(recuperarBufferTexto('stDataFinalCompetencia'),1,10);
    stSqlAux :='( SELECT ultimo_registro_evento_ferias.cod_registro
                   FROM folhapagamento'||stEntidade||'.registro_evento_ferias
                      , folhapagamento'||stEntidade||'.ultimo_registro_evento_ferias
                      , folhapagamento'||stEntidade||'.evento
                  WHERE registro_evento_ferias.cod_registro = ultimo_registro_evento_ferias.cod_registro
                    AND registro_evento_ferias.cod_evento = ultimo_registro_evento_ferias.cod_evento
                    AND registro_evento_ferias.timestamp = ultimo_registro_evento_ferias.timestamp
                    AND registro_evento_ferias.desdobramento = ultimo_registro_evento_ferias.desdobramento
                    AND registro_evento_ferias.cod_evento = evento.cod_evento
                    AND evento.evento_sistema = true
                    AND registro_evento_ferias.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                    AND registro_evento_ferias.cod_contrato = '||inCodContrato||' )';

    stSql := 'DELETE FROM folhapagamento'||stEntidade||'.registro_evento_ferias_parcela
               WHERE cod_registro IN '||stSqlAux;
    EXECUTE stSql;

    stSql := 'DELETE FROM folhapagamento'||stEntidade||'.evento_ferias_calculado_dependente
               WHERE cod_registro IN '||stSqlAux;
    EXECUTE stSql;

    stSql := 'DELETE FROM folhapagamento'||stEntidade||'.evento_ferias_calculado
               WHERE cod_registro IN '||stSqlAux;
    EXECUTE stSql;

    stSql := 'DELETE FROM folhapagamento'||stEntidade||'.log_erro_calculo_ferias
               WHERE cod_registro IN '||stSqlAux;
    EXECUTE stSql;

    stSql := 'DELETE FROM folhapagamento'||stEntidade||'.ultimo_registro_evento_ferias
               WHERE cod_registro IN '||stSqlAux;
    EXECUTE stSql;

    dtVigencia := selectIntoVarchar(' SELECT vigencia
                              FROM folhapagamento'||stEntidade||'.tabela_irrf
                                 , (SELECT cod_tabela
                                         , max(timestamp) as timestamp
                                      FROM folhapagamento'||stEntidade||'.tabela_irrf
                                     WHERE vigencia <= '''||stDataFinalCompetencia||'''
                                     GROUP BY cod_tabela) as max_tabela_irrf
                             WHERE tabela_irrf.cod_tabela = max_tabela_irrf.cod_tabela
                               AND tabela_irrf.timestamp  = max_tabela_irrf.timestamp');
    dtVigencia := criarBufferTexto('dtVigenciaIrrf',dtVigencia);

    --Sql que verifica se o servidor possui dependentes
    inContadorDependentes := selectIntoInteger(' SELECT count(servidor.cod_servidor) as contador
                                         FROM pessoal'||stEntidade||'.servidor
                                            , pessoal'||stEntidade||'.servidor_contrato_servidor
                                            , pessoal'||stEntidade||'.servidor_dependente
                                        WHERE servidor_contrato_servidor.cod_contrato = '||inCodContrato||'
                                          AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                                          AND servidor.cod_servidor                   = servidor_dependente.cod_servidor');
    --Se possuir dependentes faz a inclusão de registro de evento
    IF inContadorDependentes > 0 THEN
        boRetorno := inserirEventosAutomaticosFerias(1);
    END IF;


    --Sql que verifica se o servidor se enquadra como inativo ou pensionista acima de 65 anos
    IF retornaDataAniversarioCom(65,inCodContrato) <= to_date(stDataFinalCompetencia::text,'yyyy/mm/dd') THEN
        boRetorno := inserirEventosAutomaticosFerias(2);
    END IF;

    --Inclusão dos registro de eventos referentes aos tipos 3,4,5,6 e 7
    boRetorno := inserirEventosAutomaticosFerias(3);
    boRetorno := inserirEventosAutomaticosFerias(4);
    boRetorno := inserirEventosAutomaticosFerias(5);
    boRetorno := inserirEventosAutomaticosFerias(6);
    boRetorno := inserirEventosAutomaticosFerias(7);

    ----Código para inclusão de registro de eventos FGTS
    dtVigencia := selectIntoVarchar(' SELECT MAX(vigencia)
                              FROM folhapagamento'||stEntidade||'.fgts
                             WHERE vigencia <= '''||stDataFinalCompetencia||''' ');
    stSql := 'SELECT cod_evento
                    , cod_tipo
                    , fgts_categoria.aliquota_deposito
                    , fgts_categoria.aliquota_contribuicao
                 FROM pessoal'||stEntidade||'.contrato_servidor
                    , folhapagamento'||stEntidade||'.fgts_categoria
                    , folhapagamento'||stEntidade||'.fgts
                    , (  SELECT max(timestamp) as timestamp
                              , cod_fgts
                           FROM folhapagamento'||stEntidade||'.fgts
                          WHERE fgts.vigencia <= '''||dtVigencia||'''
                       GROUP BY cod_fgts) as max_fgts
                    , folhapagamento'||stEntidade||'.fgts_evento
                WHERE contrato_servidor.cod_categoria = fgts_categoria.cod_categoria
                  AND fgts_categoria.cod_fgts = fgts.cod_fgts
                  AND fgts_categoria.timestamp= fgts.timestamp
                  AND fgts.cod_fgts = fgts_evento.cod_fgts
                  AND fgts.timestamp= fgts_evento.timestamp
                  AND fgts.cod_fgts = max_fgts.cod_fgts
                  AND fgts.timestamp= max_fgts.timestamp
                  AND contrato_servidor.cod_contrato = '||inCodContrato||' ';
    FOR reRegistro IN  EXECUTE stSql
    LOOP

        IF (reRegistro.aliquota_deposito     > 0 AND reRegistro.cod_tipo = 1) or
           (reRegistro.aliquota_contribuicao > 0 AND reRegistro.cod_tipo = 2) or
           (reRegistro.cod_tipo = 3                                         ) THEN
            boRetorno := insertRegistroEventoAutomaticoFerias(inCodContrato,inCodPeriodoMovimentacao,reRegistro.cod_evento,'F');
            boRetorno := insertRegistroEventoAutomaticoFerias(inCodContrato,inCodPeriodoMovimentacao,reRegistro.cod_evento,'A');
            boRetorno := insertRegistroEventoAutomaticoFerias(inCodContrato,inCodPeriodoMovimentacao,reRegistro.cod_evento,'D');
        END IF;
    END LOOP;


      --Código para inclusão de registro de eventos Previdência
      stSql := '    SELECT MAX(vigencia)
                    FROM folhapagamento'||stEntidade||'.previdencia_previdencia
              INNER JOIN (
                        SELECT contrato_servidor_previdencia.cod_contrato
                             , contrato_servidor_previdencia.cod_previdencia
                          FROM pessoal'||stEntidade||'.contrato_servidor_previdencia
                         WHERE contrato_servidor_previdencia.timestamp = ( SELECT timestamp
                                                                             FROM pessoal'||stEntidade||'.contrato_servidor_previdencia as contrato_servidor_previdencia_interna
                                                                            WHERE contrato_servidor_previdencia_interna.cod_contrato = contrato_servidor_previdencia.cod_contrato
                                                                              AND contrato_servidor_previdencia_interna.cod_previdencia = contrato_servidor_previdencia.cod_previdencia
                                                                              AND contrato_servidor_previdencia.bo_excluido = false
                                                                         ORDER BY timestamp desc
                                                                            LIMIT 1
                                                                         )

                        UNION
                        SELECT contrato_pensionista_previdencia.cod_contrato
                             , contrato_pensionista_previdencia.cod_previdencia
                          FROM pessoal'||stEntidade||'.contrato_pensionista_previdencia
                         WHERE contrato_pensionista_previdencia.timestamp = ( SELECT timestamp
                                                                                FROM pessoal'||stEntidade||'.contrato_pensionista_previdencia as contrato_pensionista_previdencia_interna
                                                                               WHERE contrato_pensionista_previdencia_interna.cod_contrato = contrato_pensionista_previdencia.cod_contrato
                                                                            ORDER BY timestamp desc
                                                                               LIMIT 1
                                                                             )
                        ) as previdencia_contrato on folhapagamento'||stEntidade||'.previdencia_previdencia.cod_previdencia = previdencia_contrato.cod_previdencia
              WHERE previdencia_contrato.cod_contrato = '||inCodContrato||'
                AND vigencia <= '''||stDataFinalCompetencia||'''';
    dtVigencia := selectIntoVarchar(stSql);
    --VERIFICAÇÃO DE EXISTENCIA DE PREVIDENCIAS CADASTRADAS NO CONTRATO DO SERVIDOR
    IF dtVigencia IS NOT NULL THEN
        dtVigencia := criarBufferTexto('dtVigenciaPrevidencia',dtVigencia);

        stSql := 'SELECT cod_evento
                        , cod_tipo
                     FROM pessoal'||stEntidade||'.contrato_servidor_previdencia
                        , folhapagamento'||stEntidade||'.previdencia_previdencia
                        , (  SELECT max(timestamp) as timestamp
                                  , cod_previdencia
                               FROM folhapagamento'||stEntidade||'.previdencia_previdencia
                              WHERE previdencia_previdencia.vigencia = '''||dtVigencia||'''
                           GROUP BY cod_previdencia) as max_previdencia_previdencia
                        , (  SELECT max(timestamp) as timestamp
                                  , cod_contrato
                               FROM pessoal'||stEntidade||'.contrato_servidor_previdencia
                           GROUP BY cod_contrato) as max_contrato_servidor_previdencia
                        , folhapagamento'||stEntidade||'.previdencia_evento
                    WHERE contrato_servidor_previdencia.cod_previdencia = previdencia_previdencia.cod_previdencia
                      AND contrato_servidor_previdencia.cod_contrato = max_contrato_servidor_previdencia.cod_contrato
                      AND contrato_servidor_previdencia.timestamp    = max_contrato_servidor_previdencia.timestamp
                      AND contrato_servidor_previdencia.bo_excluido  = false
                      AND previdencia_previdencia.cod_previdencia    = max_previdencia_previdencia.cod_previdencia
                      AND previdencia_previdencia.timestamp          = max_previdencia_previdencia.timestamp
                      AND previdencia_previdencia.cod_previdencia = previdencia_evento.cod_previdencia
                      AND previdencia_previdencia.timestamp       = previdencia_evento.timestamp
                      AND previdencia_evento.cod_tipo != 3
                      AND contrato_servidor_previdencia.cod_contrato = '||inCodContrato||' ';
        FOR reRegistro IN  EXECUTE stSql
        LOOP
            stNatureza := pega0NaturezaEvento(reRegistro.cod_evento);
            boRetorno := insertRegistroEventoAutomaticoFerias(inCodContrato,inCodPeriodoMovimentacao,reRegistro.cod_evento,'F');
            IF NOT(stNatureza = 'D') THEN
                boRetorno := insertRegistroEventoAutomaticoFerias(inCodContrato,inCodPeriodoMovimentacao,reRegistro.cod_evento,'A');
            END IF;
            boRetorno := insertRegistroEventoAutomaticoFerias(inCodContrato,inCodPeriodoMovimentacao,reRegistro.cod_evento,'D');
        END LOOP;
    ELSE
        dtVigencia := criarBufferTexto('dtVigenciaPrevidencia','NULL');
    END IF;

    --   INÍCIO PENSÃO     --
    --Código para inclusão de registro de eventos Pensão
    inQtdDependentesPensaoAlimenticia = pega0QtdDependentesPensaoAlimenticia( inCodContrato, stDataFinalCompetencia );
    inQtdDependentesPensaoAlimenticia = criarBufferInteiro('inQtdDependentesPensaoAlimenticia',inQtdDependentesPensaoAlimenticia);
    IF inQtdDependentesPensaoAlimenticia > 0 THEN
    
        --Verifica se existe alguma pensao com incidência na folha de férias
        --cod_incidencia igual a 2, 3, 4, 5
        stSql := ' SELECT count(pensao.cod_pensao) as contador 
                     FROM pessoal'||stEntidade||'.pensao_incidencia
               INNER JOIN pessoal'||stEntidade||'.pensao 
                       ON pensao.cod_pensao = pensao_incidencia.cod_pensao
                      AND pensao.timestamp  = pensao_incidencia.timestamp 
               INNER JOIN ( SELECT MAX(pensao.timestamp) as timestamp, cod_pensao
	                          FROM pessoal'||stEntidade||'.pensao
	                      GROUP BY cod_pensao ) as max_pensao	      	   
	                   ON max_pensao.cod_pensao = pensao.cod_pensao
                      AND max_pensao.timestamp  = pensao.timestamp	
               INNER JOIN pessoal'||stEntidade||'.servidor_contrato_servidor
	                   ON servidor_contrato_servidor.cod_servidor = pensao.cod_servidor
                    WHERE servidor_contrato_servidor.cod_contrato = '||inCodContrato||'
                      AND NOT EXISTS (SELECT *
		                                FROM pessoal'||stEntidade||'.pensao_excluida
		                               WHERE pensao_excluida.cod_pensao = pensao.cod_pensao
		                                 AND pensao_excluida.timestamp  = pensao.timestamp)      
                      AND pensao.dt_inclusao <= '''||stDataFinalCompetencia||'''
                      AND ( pensao.dt_limite IS NULL OR pensao.dt_limite >= '''||stDataFinalCompetencia||''' )
                      AND pensao_incidencia.cod_incidencia IN (2, 3, 4, 5)';

        IF selectIntoInteger(stSql) > 0 THEN    
            inCodEvento                       = pega2CodEventoDescontoPensaoAlimenticia();
            boRetorno := insertRegistroEventoAutomaticoFerias(inCodContrato,inCodPeriodoMovimentacao,inCodEvento,'F');
            boRetorno := insertRegistroEventoAutomaticoFerias(inCodContrato,inCodPeriodoMovimentacao,inCodEvento,'A');
            boRetorno := insertRegistroEventoAutomaticoFerias(inCodContrato,inCodPeriodoMovimentacao,inCodEvento,'D');
        END IF;
            
    END IF;
    --   FIM PENSÃO        --

    --********************************** cod_tipo = 1 ***********************************************
    --CÓDIGO PARA INCLUSÃO DE REGISTRO DE EVENTOS AUTOMÁTICOS DA CONFIGURAÇÃO DE FÉRIAS
    --DEVE-SE VERIFICAR SE AS FÉRIAS DIZEM RESPEITO AO MES DE PAGAMENTO OU NÃO.
    stSql := ' SELECT  cod_contrato
                  FROM pessoal'||stEntidade||'.ferias                 as ferias
                  JOIN pessoal'||stEntidade||'.lancamento_ferias      as lancamento_ferias
                    ON ferias.cod_ferias = lancamento_ferias.cod_ferias

                  JOIN pessoal'||stEntidade||'.forma_pagamento_ferias  as forma_pagamento_ferias
                    ON forma_pagamento_ferias.cod_forma = ferias.cod_forma

                 WHERE cod_contrato = '||inCodContrato||'
                   AND (SELECT SUBSTR(dt_final::varchar,1,7) as dt_final
                          FROM folhapagamento'||stEntidade||'.periodo_movimentacao
                         WHERE cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||')
                       BETWEEN SUBSTR(lancamento_ferias.dt_inicio::varchar,1,7)
                           AND SUBSTR(lancamento_ferias.dt_fim::varchar,1,7)
                   AND (SELECT SUBSTR(dt_final::varchar,6,2) as dt_final
                          FROM folhapagamento'||stEntidade||'.periodo_movimentacao
                         WHERE cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||') != lancamento_ferias.mes_competencia
			       AND NOT EXISTS (SELECT cod_contrato  
                                     FROM pessoal'||stEntidade||'.lancamento_ferias as lancamento_ferias2
                                     JOIN pessoal'||stEntidade||'.ferias as ferias2
                                       ON ferias2.cod_ferias = lancamento_ferias2.cod_ferias
                                    WHERE lancamento_ferias2.mes_competencia = (SELECT SUBSTR(dt_final::varchar,6,2) as dt_final
									                                              FROM folhapagamento.periodo_movimentacao
                                                                                 WHERE cod_periodo_movimentacao = 354)
                                      AND ferias2.cod_contrato = ferias.cod_contrato
                                      AND lancamento_ferias2.cod_ferias != lancamento_ferias.cod_ferias) 
             ';		 

    OPEN crCursor FOR EXECUTE stSql;
         FETCH crCursor INTO inCodContratoAux ;
    CLOSE crCursor;

    IF inCodContratoAux IS NOT NULL THEN
       stSql := ' SELECT cod_evento
                    FROM  folhapagamento'||stEntidade||'.ferias_evento
                         ,(SELECT  max(timestamp) as timestamp
                                  ,cod_tipo
                             FROM  folhapagamento'||stEntidade||'.ferias_evento
                             GROUP by cod_tipo) as max_timestamp
                   WHERE max_timestamp.cod_tipo  = ferias_evento.cod_tipo
                     AND max_timestamp.timestamp = ferias_evento.timestamp
                     AND ferias_evento.cod_tipo  = 1 ';

       FOR reRegistro IN  EXECUTE stSql
       LOOP
               boRetorno := insertRegistroEventoAutomaticoFerias(inCodContrato,inCodPeriodoMovimentacao,reRegistro.cod_evento,'F');
               boRetorno := insertRegistroEventoAutomaticoFerias(inCodContrato,inCodPeriodoMovimentacao,reRegistro.cod_evento,'A');
               boRetorno := insertRegistroEventoAutomaticoFerias(inCodContrato,inCodPeriodoMovimentacao,reRegistro.cod_evento,'D');
       END LOOP;
    END IF;
    --*********************************** cod_tipo = 2 ****************************************************
	
   	
       stSql := ' SELECT cod_evento
                    FROM  folhapagamento'||stEntidade||'.ferias_evento
                         ,(SELECT  max(timestamp) as timestamp
                                  ,cod_tipo
                             FROM  folhapagamento'||stEntidade||'.ferias_evento
                             GROUP by cod_tipo) as max_timestamp
                   WHERE max_timestamp.cod_tipo  = ferias_evento.cod_tipo
                     AND max_timestamp.timestamp = ferias_evento.timestamp
                     AND ferias_evento.cod_tipo  = 2 ';

       FOR reRegistro IN  EXECUTE stSql
       LOOP
               boRetorno := insertRegistroEventoAutomaticoFerias(inCodContrato,inCodPeriodoMovimentacao,reRegistro.cod_evento,'F');
               boRetorno := insertRegistroEventoAutomaticoFerias(inCodContrato,inCodPeriodoMovimentacao,reRegistro.cod_evento,'A');
               boRetorno := insertRegistroEventoAutomaticoFerias(inCodContrato,inCodPeriodoMovimentacao,reRegistro.cod_evento,'D');
       END LOOP;
    --********************************************************************************************

    --EVENTOS DE DESCONTO EXTERNO
    stSql := 'SELECT *
                 FROM folhapagamento'||stEntidade||'.configuracao_eventos_desconto_externo';
    FOR reRegistro IN  EXECUTE stSql
    LOOP
        boRetorno := insertRegistroEventoAutomaticoFerias(inCodContrato,inCodPeriodoMovimentacao,reRegistro.evento_desconto_previdencia,'F');
        boRetorno := insertRegistroEventoAutomaticoFerias(inCodContrato,inCodPeriodoMovimentacao,reRegistro.evento_base_previdencia,'F');
    END LOOP;
    --EVENTOS DE DESCONTO EXTERNO

    --EVENTOS AUTOMÁTICOS DE IPERS
    stSql := '
    SELECT configuracao_ipe.*
      FROM folhapagamento'||stEntidade||'.configuracao_ipe
         , (SELECT max(cod_configuracao) as cod_configuracao
                 , vigencia
              FROM folhapagamento'||stEntidade||'.configuracao_ipe
            GROUP BY vigencia) as max_configuracao_ipe
      WHERE configuracao_ipe.cod_configuracao = max_configuracao_ipe.cod_configuracao
        AND configuracao_ipe.vigencia = max_configuracao_ipe.vigencia
        AND configuracao_ipe.vigencia <= (SELECT dt_final
                                            FROM folhapagamento'||stEntidade||'.periodo_movimentacao
                                           WHERE cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||')
    ORDER BY configuracao_ipe.vigencia desc
    LIMIT 1';
    OPEN crCursor FOR EXECUTE stSql;
        FETCH crCursor INTO reConfiguracao;
    CLOSE crCursor;
    IF reConfiguracao.cod_configuracao IS NOT NULL THEN
        stSql := '
          SELECT atributo_contrato_servidor_valor.valor
            FROM pessoal'||stEntidade||'.atributo_contrato_servidor_valor
                , (SELECT cod_contrato
                        , cod_atributo
                        , max(timestamp) as timestamp
                    FROM pessoal'||stEntidade||'.atributo_contrato_servidor_valor
                    GROUP BY cod_contrato
                        , cod_atributo) as max_atributo_contrato_servidor_valor
          WHERE atributo_contrato_servidor_valor.cod_contrato = max_atributo_contrato_servidor_valor.cod_contrato
            AND atributo_contrato_servidor_valor.cod_atributo = max_atributo_contrato_servidor_valor.cod_atributo
            AND atributo_contrato_servidor_valor.timestamp = max_atributo_contrato_servidor_valor.timestamp
            AND atributo_contrato_servidor_valor.cod_contrato = '||inCodContrato||'
            AND atributo_contrato_servidor_valor.cod_atributo = '||reConfiguracao.cod_atributo_data||'
            AND atributo_contrato_servidor_valor.cod_modulo = '||reConfiguracao.cod_modulo_data||'
            AND atributo_contrato_servidor_valor.cod_cadastro = '||reConfiguracao.cod_cadastro_data;
        stDataAtributoIpe := selectIntoVarchar(stSql);
        stDataAtributoIpe := replace(stDataAtributoIpe, ' ', '');

        stSql := '
         SELECT atributo_contrato_servidor_valor.valor
           FROM pessoal'||stEntidade||'.atributo_contrato_servidor_valor
                , (SELECT cod_contrato
                        , cod_atributo
                        , max(timestamp) as timestamp
                    FROM pessoal'||stEntidade||'.atributo_contrato_servidor_valor
                    GROUP BY cod_contrato
                        , cod_atributo) as max_atributo_contrato_servidor_valor
          WHERE atributo_contrato_servidor_valor.cod_contrato = max_atributo_contrato_servidor_valor.cod_contrato
            AND atributo_contrato_servidor_valor.cod_atributo = max_atributo_contrato_servidor_valor.cod_atributo
            AND atributo_contrato_servidor_valor.timestamp = max_atributo_contrato_servidor_valor.timestamp
            AND atributo_contrato_servidor_valor.cod_contrato = '||inCodContrato||'
            AND atributo_contrato_servidor_valor.cod_atributo = '||reConfiguracao.cod_atributo_mat||'
            AND atributo_contrato_servidor_valor.cod_modulo = '||reConfiguracao.cod_modulo_mat||'
            AND atributo_contrato_servidor_valor.cod_cadastro = '||reConfiguracao.cod_cadastro_mat;
        stMatAtributoIpe := selectIntoVarchar(stSql);
        stMatAtributoIpe := replace(stMatAtributoIpe, ' ', '');

        IF stDataAtributoIpe IS NOT NULL
        AND stDataAtributoIpe != ''
        AND stMatAtributoIpe IS NOT NULL
        AND stMatAtributoIpe != ''
        THEN
           boRetorno := insertRegistroEventoAutomaticoFerias(inCodContrato,inCodPeriodoMovimentacao,reConfiguracao.cod_evento_automatico,'F');
           boRetorno := insertRegistroEventoAutomaticoFerias(inCodContrato,inCodPeriodoMovimentacao,reConfiguracao.cod_evento_automatico,'A');
           boRetorno := insertRegistroEventoAutomaticoFerias(inCodContrato,inCodPeriodoMovimentacao,reConfiguracao.cod_evento_automatico,'D');
           boRetorno := insertRegistroEventoAutomaticoFerias(inCodContrato,inCodPeriodoMovimentacao,reConfiguracao.cod_evento_base,'F');
           boRetorno := insertRegistroEventoAutomaticoFerias(inCodContrato,inCodPeriodoMovimentacao,reConfiguracao.cod_evento_base,'A');
           boRetorno := insertRegistroEventoAutomaticoFerias(inCodContrato,inCodPeriodoMovimentacao,reConfiguracao.cod_evento_base,'D');
        END IF;
    END IF;
    --EVENTOS AUTOMÁTICOS DE IPERS
    RETURN boRetorno;
END;
$$LANGUAGE 'plpgsql';

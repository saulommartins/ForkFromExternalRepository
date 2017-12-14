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
-- /**
--     * PLpgsql recuperaValoresAcumuladosCalculoRescisao
--     * Data de Criação: 24/06/2008
--     
--     
--     * @author Analista: Dagiane Vieira  
--     * @author Desenvolvedor: Diego Lemos de Souza
--     
--     * @ignore
--        
--     * Casos de uso: uc-04.05.65
--     
--     $Id:$    
-- */
-- Ticket #13872

CREATE OR REPLACE FUNCTION recuperaValoresAcumuladosCalculoSalarioFamilia(integer,integer,integer,varchar,varchar) RETURNS SETOF colunasValoresAcumuladosRescisao AS $$
DECLARE
    inCodContrato                       ALIAS FOR $1;
    inCodPeriodoMovimentacao            ALIAS FOR $2;
    inNumCGM                            ALIAS FOR $3;
    stNatureza                          ALIAS FOR $4;
    stEntidade                          ALIAS FOR $5;      
    rwValoresAcumulados                 colunasValoresAcumuladosRescisao%ROWTYPE;
    stFolhas                            VARCHAR:='';
    stSql                               VARCHAR;
    stSqlSalario                        VARCHAR;
    stSqlComplementar                   VARCHAR;
    stSqlRescisao                       VARCHAR;        
    stSqlAux                            VARCHAR;
    reEventos                           RECORD;
    reEvento                            RECORD;
    nuValorEvento                       NUMERIC:=0;
    nuAux                               NUMERIC:=0;
    crCursor                            REFCURSOR;
    inCodRegimePrevidencia              INTEGER;
BEGIN   

    --Consulta para busca de valores de salário
    stSqlSalario := 'SELECT sum(evento_calculado.valor) as valor
                       FROM folhapagamento'|| stEntidade ||'.evento_calculado
                          , folhapagamento'|| stEntidade ||'.registro_evento_periodo
                          , pessoal'|| stEntidade ||'.servidor_contrato_servidor
                          , pessoal'|| stEntidade ||'.servidor
                          , folhapagamento'|| stEntidade ||'.evento
                      WHERE registro_evento_periodo.cod_registro = evento_calculado.cod_registro
                        AND registro_evento_periodo.cod_contrato = servidor_contrato_servidor.cod_contrato
                        AND evento_calculado.cod_evento = evento.cod_evento
                        AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                        AND registro_evento_periodo.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                        AND servidor.numcgm = '|| inNumCGM;
                        
    --Consulta para busca de valores de complementar
    stSqlComplementar := 'SELECT sum(evento_complementar_calculado.valor) as valor
                       FROM folhapagamento'|| stEntidade ||'.evento_complementar_calculado
                          , folhapagamento'|| stEntidade ||'.registro_evento_complementar
                          , pessoal'|| stEntidade ||'.servidor_contrato_servidor
                          , pessoal'|| stEntidade ||'.servidor
                          , folhapagamento'|| stEntidade ||'.evento
                      WHERE registro_evento_complementar.cod_registro = evento_complementar_calculado.cod_registro
                        AND registro_evento_complementar.cod_evento = evento_complementar_calculado.cod_evento
                        AND registro_evento_complementar.cod_configuracao = evento_complementar_calculado.cod_configuracao
                        AND registro_evento_complementar.timestamp = evento_complementar_calculado.timestamp_registro
                        AND registro_evento_complementar.cod_contrato = servidor_contrato_servidor.cod_contrato
                        AND evento_complementar_calculado.cod_evento = evento.cod_evento
                        AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                        AND registro_evento_complementar.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                        AND servidor.numcgm = '|| inNumCGM; 
                  
    --Consulta para busca de valores de rescisão
    stSqlRescisao := 'SELECT sum(evento_rescisao_calculado.valor) as valor
                       FROM folhapagamento'|| stEntidade ||'.evento_rescisao_calculado
                          , folhapagamento'|| stEntidade ||'.registro_evento_rescisao
                          , pessoal'|| stEntidade ||'.servidor_contrato_servidor
                          , pessoal'|| stEntidade ||'.servidor
                          , folhapagamento'|| stEntidade ||'.evento
                      WHERE registro_evento_rescisao.cod_registro = evento_rescisao_calculado.cod_registro
                        AND registro_evento_rescisao.cod_evento = evento_rescisao_calculado.cod_evento
                        AND registro_evento_rescisao.desdobramento = evento_rescisao_calculado.desdobramento
                        AND registro_evento_rescisao.timestamp = evento_rescisao_calculado.timestamp_registro
                        AND registro_evento_rescisao.cod_contrato = servidor_contrato_servidor.cod_contrato
                        AND evento_rescisao_calculado.cod_evento = evento.cod_evento
                        AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                        AND registro_evento_rescisao.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                        AND servidor.numcgm = '|| inNumCGM;                             

                  
    --######################################################
    --Busca de valores acumulados de Base de Salário Família    
    --######################################################  
    inCodRegimePrevidencia := selectIntoInteger ('SELECT previdencia.cod_regime_previdencia
                                              FROM folhapagamento'|| stEntidade ||'.previdencia_previdencia
                                                 , (  SELECT cod_previdencia
                                                           , max(timestamp) as timestamp
                                                        FROM folhapagamento'|| stEntidade ||'.previdencia_previdencia
                                                    GROUP BY cod_previdencia) as max_previdencia_previdencia
                                                 , folhapagamento'|| stEntidade ||'.previdencia
                                                 , (SELECT contrato_servidor_previdencia.cod_contrato
                                                         , contrato_servidor_previdencia.cod_previdencia
                                                      FROM pessoal'|| stEntidade ||'.contrato_servidor_previdencia
                                                         , (  SELECT max(timestamp) as timestamp
                                                                  , cod_contrato
                                                               FROM pessoal'|| stEntidade ||'.contrato_servidor_previdencia
                                                           GROUP BY cod_contrato) as max_contrato_servidor_previdencia
                                                     WHERE contrato_servidor_previdencia.cod_contrato = max_contrato_servidor_previdencia.cod_contrato
                                                       AND contrato_servidor_previdencia.timestamp    = max_contrato_servidor_previdencia.timestamp
                                                       AND contrato_servidor_previdencia.bo_excluido = false
                                                     UNION
                                                    SELECT contrato_pensionista_previdencia.cod_contrato
                                                         , contrato_pensionista_previdencia.cod_previdencia
                                                      FROM pessoal'|| stEntidade ||'.contrato_pensionista_previdencia
                                                         , (  SELECT max(timestamp) as timestamp
                                                                  , cod_contrato
                                                               FROM pessoal'|| stEntidade ||'.contrato_pensionista_previdencia
                                                           GROUP BY cod_contrato) as max_contrato_pensionista_previdencia
                                                     WHERE contrato_pensionista_previdencia.cod_contrato = max_contrato_pensionista_previdencia.cod_contrato
                                                       AND contrato_pensionista_previdencia.timestamp    = max_contrato_pensionista_previdencia.timestamp) as servidor_pensionista_previdencia
                                             WHERE servidor_pensionista_previdencia.cod_contrato = '|| inCodContrato ||'
                                               AND servidor_pensionista_previdencia.cod_previdencia = previdencia_previdencia.cod_previdencia
                                               AND previdencia_previdencia.cod_previdencia = max_previdencia_previdencia.cod_previdencia
                                               AND previdencia_previdencia.timestamp = max_previdencia_previdencia.timestamp
                                               AND previdencia_previdencia.tipo_previdencia = ''o''
                                               AND previdencia_previdencia.cod_previdencia = previdencia.cod_previdencia');

    IF inCodRegimePrevidencia IS NOT NULL THEN
  
        stSql := '    SELECT salario_familia_evento.cod_evento
                        FROM folhapagamento'|| stEntidade ||'.salario_familia_evento
                INNER JOIN (  SELECT cod_tipo
                                    , cod_regime_previdencia
                                    , max(timestamp) as timestamp
                                FROM folhapagamento'|| stEntidade ||'.salario_familia_evento
                            GROUP BY cod_tipo
                                    , cod_regime_previdencia) as max_salario_familia_evento
                        ON salario_familia_evento.cod_tipo = max_salario_familia_evento.cod_tipo
                        AND salario_familia_evento.cod_regime_previdencia = max_salario_familia_evento.cod_regime_previdencia
                        AND salario_familia_evento.timestamp = max_salario_familia_evento.timestamp
                    WHERE salario_familia_evento.cod_tipo = 2
                        AND salario_familia_evento.cod_regime_previdencia = '|| inCodRegimePrevidencia;
    
        FOR reEventos IN EXECUTE stSql LOOP
            nuValorEvento := 0;
            
            --Folha Salário
            stSqlAux := stSqlSalario ||' AND evento_calculado.cod_evento = '|| reEventos.cod_evento;
            stSqlAux := stSqlAux     ||' AND evento.natureza = '|| quote_literal(stNatureza) ||' ';
            nuAux := selectIntoNumeric(stSqlAux);
    
            IF nuAux IS NOT NULL THEN
                nuValorEvento := nuValorEvento + nuAux;
            END IF;
    
            --Folha Complementar
            stSqlAux := stSqlComplementar ||' AND evento_complementar_calculado.cod_evento = '|| reEventos.cod_evento;
            stSqlAux := stSqlAux          ||' AND evento_complementar_calculado.cod_configuracao = 1';
            stSqlAux := stSqlAux          ||' AND evento.natureza = '|| quote_literal(stNatureza) ||' ';
            nuAux := selectIntoNumeric(stSqlAux);
    
            IF nuAux IS NOT NULL THEN
                nuValorEvento := nuValorEvento + nuAux;
            END IF;
    
            --Folha Rescisão
            stSqlAux := stSqlRescisao ||' AND evento_rescisao_calculado.cod_evento = '|| reEventos.cod_evento;
            stSqlAux := stSqlAux      ||' AND evento.natureza = '|| quote_literal(stNatureza) ||' ';
            stSqlAux := stSqlAux      ||' AND registro_evento_rescisao.desdobramento = '|| quote_literal('S')||' ';
            nuAux := selectIntoNumeric(stSqlAux);
    
            IF nuAux IS NOT NULL THEN
                nuValorEvento := nuValorEvento + nuAux;
            END IF;
    
            stSqlAux := 'SELECT * 
                        FROM folhapagamento'|| stEntidade ||'.evento 
                        WHERE cod_evento = '|| reEventos.cod_evento;
    
            OPEN crCursor FOR EXECUTE stSqlAux;
                FETCH crCursor INTO reEvento;
            CLOSE crCursor; 
    
            IF nuValorEvento != 0 THEN
                rwValoresAcumulados.codigo      := reEvento.codigo;
                rwValoresAcumulados.descricao   := trim(reEvento.descricao);
                rwValoresAcumulados.valor       := nuValorEvento;
                rwValoresAcumulados.folhas      := stFolhas;
                RETURN NEXT rwValoresAcumulados;  
            END IF;
        END LOOP;
    END IF;   
END
$$ language 'plpgsql';

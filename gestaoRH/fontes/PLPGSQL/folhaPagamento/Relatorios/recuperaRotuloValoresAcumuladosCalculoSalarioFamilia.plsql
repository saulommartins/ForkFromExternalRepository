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
--     * PLpgsql recuperaRotuloValoresAcumuladosCalculoRescisao
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
CREATE OR REPLACE FUNCTION recuperaRotuloValoresAcumuladosCalculoSalarioFamilia(integer,integer,integer,varchar,varchar) returns varchar as $$
DECLARE
    inCodContrato               ALIAS FOR $1;
    inCodPeriodoMovimentacao    ALIAS FOR $2;
    inNumCGM                    ALIAS FOR $3;
    stNatureza                  ALIAS FOR $4;  
    stEntidade                  ALIAS FOR $5;
    inCodRegimePrevidencia      INTEGER; 
    stSql                       VARCHAR;
    stCodEventos                VARCHAR:='';
    stRetorno                   VARCHAR:='';
    stSituacao                  VARCHAR:='';    
    reRegistros                 RECORD;
BEGIN    
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
    
        FOR reRegistros IN EXECUTE stSql LOOP
            stCodEventos := stCodEventos || reRegistros.cod_evento  ||',';
        END LOOP;
    
        IF trim(stCodEventos) != '' THEN
            stCodEventos := substr(stCodEventos,1,char_length(stCodEventos)-1);
    
            --Salário
            stSql := 'SELECT contrato.*
                        FROM pessoal'|| stEntidade ||'.contrato
                        , pessoal'|| stEntidade ||'.servidor_contrato_servidor
                        , pessoal'|| stEntidade ||'.servidor
                    WHERE contrato.cod_contrato = servidor_contrato_servidor.cod_contrato
                        AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                        AND contrato.cod_contrato IN (SELECT registro_evento_periodo.cod_contrato
                                                        FROM folhapagamento'|| stEntidade ||'.registro_evento_periodo
                                                            , folhapagamento'|| stEntidade ||'.evento_calculado
                                                            , folhapagamento'|| stEntidade ||'.evento
                                                        WHERE registro_evento_periodo.cod_registro = evento_calculado.cod_registro
                                                        AND evento_calculado.cod_evento = evento.cod_evento
                                                        AND evento.natureza = '|| quote_literal(stNatureza) ||'
                                                        AND registro_evento_periodo.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                                                        AND evento_calculado.cod_evento IN ('|| stCodEventos ||')
                                                    GROUP BY registro_evento_periodo.cod_contrato)
                        AND servidor.numcgm = '|| inNumCGM;                  
    
            FOR reRegistros IN EXECUTE stSql LOOP
                stRetorno := stRetorno || reRegistros.registro  ||'(S)/';
            END LOOP;
            
                
            --Rescisão
            stSql := 'SELECT contrato.*
                        FROM pessoal'|| stEntidade ||'.contrato
                        , pessoal'|| stEntidade ||'.servidor_contrato_servidor
                        , pessoal'|| stEntidade ||'.servidor
                    WHERE contrato.cod_contrato = servidor_contrato_servidor.cod_contrato
                        AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                        AND contrato.cod_contrato IN (SELECT registro_evento_rescisao.cod_contrato
                                                        FROM folhapagamento'|| stEntidade ||'.registro_evento_rescisao
                                                            , folhapagamento'|| stEntidade ||'.evento_rescisao_calculado
                                                            , folhapagamento'|| stEntidade ||'.evento
                                                        WHERE registro_evento_rescisao.cod_registro = evento_rescisao_calculado.cod_registro
                                                        AND registro_evento_rescisao.cod_evento = evento_rescisao_calculado.cod_evento
                                                        AND registro_evento_rescisao.desdobramento = evento_rescisao_calculado.desdobramento
                                                        AND registro_evento_rescisao.timestamp = evento_rescisao_calculado.timestamp_registro
                                                        AND evento_rescisao_calculado.cod_evento = evento.cod_evento
                                                        AND evento.natureza = '|| quote_literal(stNatureza) ||'
                                                        AND registro_evento_rescisao.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                                                        AND evento_rescisao_calculado.cod_evento IN ('|| stCodEventos ||')
                                                    GROUP BY registro_evento_rescisao.cod_contrato)
                        AND servidor.numcgm = '|| inNumCGM;
            IF stNatureza = 'D' THEN
                stSql := stSql || ' AND contrato.cod_contrato != '|| inCodContrato;          
            END IF;                   
    
            FOR reRegistros IN EXECUTE stSql LOOP
                stRetorno := stRetorno || reRegistros.registro  ||'(R)/';
            END LOOP;    
            
            --Complementar
            stSql := 'SELECT contrato.*
                        FROM pessoal'|| stEntidade ||'.contrato
                        , pessoal'|| stEntidade ||'.servidor_contrato_servidor
                        , pessoal'|| stEntidade ||'.servidor
                    WHERE contrato.cod_contrato = servidor_contrato_servidor.cod_contrato
                        AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                        AND contrato.cod_contrato IN (SELECT registro_evento_complementar.cod_contrato
                                                        FROM folhapagamento'|| stEntidade ||'.registro_evento_complementar
                                                            , folhapagamento'|| stEntidade ||'.evento_complementar_calculado
                                                            , folhapagamento'|| stEntidade ||'.evento
                                                        WHERE registro_evento_complementar.cod_registro = evento_complementar_calculado.cod_registro
                                                        AND registro_evento_complementar.cod_evento = evento_complementar_calculado.cod_evento
                                                        AND registro_evento_complementar.cod_configuracao = evento_complementar_calculado.cod_configuracao
                                                        AND registro_evento_complementar.timestamp = evento_complementar_calculado.timestamp_registro
                                                        AND evento_complementar_calculado.cod_evento = evento.cod_evento
                                                        AND registro_evento_complementar.cod_configuracao = 1
                                                        AND evento.natureza = '|| quote_literal(stNatureza) ||'
                                                        AND registro_evento_complementar.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                                                        AND evento_complementar_calculado.cod_evento IN ('|| stCodEventos ||')
                                                    GROUP BY registro_evento_complementar.cod_contrato)
                        AND servidor.numcgm = '|| inNumCGM;                    
    
            FOR reRegistros IN EXECUTE stSql LOOP
                stRetorno := stRetorno || reRegistros.registro  ||'(C)/';
            END LOOP;    
        END IF;
        
        IF char_length(stRetorno) > 1 THEN
            stRetorno := substr(stRetorno,1,char_length(stRetorno)-1);
        END IF;
    END IF;

    RETURN stRetorno;    
END
$$ language 'plpgsql';


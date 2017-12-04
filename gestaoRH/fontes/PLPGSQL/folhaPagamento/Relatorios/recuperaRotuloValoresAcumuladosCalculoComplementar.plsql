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
CREATE OR REPLACE FUNCTION recuperaRotuloValoresAcumuladosCalculoComplementar(integer,integer,integer,varchar,varchar) returns varchar as $$
DECLARE     
    inCodContrato                       ALIAS FOR $1;
    inCodPeriodoMovimentacao            ALIAS FOR $2;
    inNumCGM                            ALIAS FOR $3;
    stNatureza                          ALIAS FOR $4;  
    stEntidade                          ALIAS FOR $5;        
    stSql                       VARCHAR;
    stCodEventos                VARCHAR:='';
    stRetorno                   VARCHAR:='';
    stSituacao                  VARCHAR:='';  
    reRegistros                 RECORD;
BEGIN    
    stCodEventos := recuperaListaEventosAcumulados(inCodContrato,stEntidade);
    --Salário
    stSql := 'SELECT folha_situacao.situacao
                FROM folhapagamento'|| stEntidade ||'.folha_situacao
                   , (SELECT cod_periodo_movimentacao
                           , max(timestamp) as timestamp
                        FROM folhapagamento'|| stEntidade ||'.folha_situacao
                      GROUP BY cod_periodo_movimentacao) as max_folha_situacao
               WHERE folha_situacao.cod_periodo_movimentacao = max_folha_situacao.cod_periodo_movimentacao
                 AND folha_situacao.timestamp = max_folha_situacao.timestamp
                 AND folha_situacao.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                 AND folha_situacao.situacao = ''f'' ';  
    stSituacao := selectIntoVarchar(stSql);    
    IF stSituacao = 'f' THEN                    
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
    END IF;
    
    --Férias
    stSql := 'SELECT contrato.*
                FROM pessoal'|| stEntidade ||'.contrato
                   , pessoal'|| stEntidade ||'.servidor_contrato_servidor
                   , pessoal'|| stEntidade ||'.servidor
               WHERE contrato.cod_contrato = servidor_contrato_servidor.cod_contrato
                 AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                 AND contrato.cod_contrato IN (SELECT registro_evento_ferias.cod_contrato
                                                 FROM folhapagamento'|| stEntidade ||'.registro_evento_ferias
                                                    , folhapagamento'|| stEntidade ||'.evento_ferias_calculado
                                                    , folhapagamento'|| stEntidade ||'.evento
                                                WHERE registro_evento_ferias.cod_registro = evento_ferias_calculado.cod_registro
                                                  AND registro_evento_ferias.cod_evento = evento_ferias_calculado.cod_evento
                                                  AND registro_evento_ferias.desdobramento = evento_ferias_calculado.desdobramento
                                                  AND registro_evento_ferias.timestamp = evento_ferias_calculado.timestamp_registro
                                                  AND evento_ferias_calculado.cod_evento = evento.cod_evento
                                                  AND evento.natureza = '|| quote_literal(stNatureza) ||'
                                                  AND registro_evento_ferias.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                                                  AND evento_ferias_calculado.cod_evento IN ('|| stCodEventos ||')
                                             GROUP BY registro_evento_ferias.cod_contrato)
                 AND servidor.numcgm = '|| inNumCGM;                
    FOR reRegistros IN EXECUTE stSql LOOP
        stRetorno := stRetorno || reRegistros.registro  ||'(F)/';
    END LOOP;
    
    --Décimo
    stSql := 'SELECT contrato.*
                FROM pessoal'|| stEntidade ||'.contrato
                   , pessoal'|| stEntidade ||'.servidor_contrato_servidor
                   , pessoal'|| stEntidade ||'.servidor
               WHERE contrato.cod_contrato = servidor_contrato_servidor.cod_contrato
                 AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                 AND contrato.cod_contrato IN (SELECT registro_evento_decimo.cod_contrato
                                                 FROM folhapagamento'|| stEntidade ||'.registro_evento_decimo
                                                    , folhapagamento'|| stEntidade ||'.evento_decimo_calculado
                                                    , folhapagamento'|| stEntidade ||'.evento
                                                WHERE registro_evento_decimo.cod_registro = evento_decimo_calculado.cod_registro
                                                  AND registro_evento_decimo.cod_evento = evento_decimo_calculado.cod_evento
                                                  AND registro_evento_decimo.desdobramento = evento_decimo_calculado.desdobramento
                                                  AND registro_evento_decimo.timestamp = evento_decimo_calculado.timestamp_registro
                                                  AND evento_decimo_calculado.cod_evento = evento.cod_evento
                                                  AND evento.natureza = '|| quote_literal(stNatureza) ||'
                                                  AND registro_evento_decimo.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                                                  AND evento_decimo_calculado.cod_evento IN ('|| stCodEventos ||')
                                             GROUP BY registro_evento_decimo.cod_contrato)
                 AND servidor.numcgm = '|| inNumCGM;                
    FOR reRegistros IN EXECUTE stSql LOOP
        stRetorno := stRetorno || reRegistros.registro  ||'(D)/';
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
                                                  AND evento.natureza = '|| quote_literal(stNatureza) ||'
                                                  AND registro_evento_complementar.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                                                  AND evento_complementar_calculado.cod_evento IN ('|| stCodEventos ||')
                                             GROUP BY registro_evento_complementar.cod_contrato)
                 AND servidor.numcgm = '|| inNumCGM;   
    IF stNatureza = 'D'THEN
        stSql := stSql || ' AND contrato.cod_contrato != '|| inCodContrato;          
    END IF;                                          
    FOR reRegistros IN EXECUTE stSql LOOP
        stRetorno := stRetorno || reRegistros.registro  ||'(C)/';
    END LOOP;        
    
    IF char_length(stRetorno) > 1 THEN
        stRetorno := substr(stRetorno,1,char_length(stRetorno)-1);
    END IF;
    return stRetorno;    
end
$$ language 'plpgsql';


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
CREATE OR REPLACE FUNCTION concederFerias(VARCHAR,VARCHAR,INTEGER,BOOLEAN,VARCHAR,VARCHAR,VARCHAR,INTEGER,INTEGER) RETURNS SETOF colunasConcederFerias AS $$
DECLARE
    stTipoFiltro                ALIAS FOR $1;
    stValoresFiltro             ALIAS FOR $2;
    inCodPeriodoMovimentacao    ALIAS FOR $3;
    boFeriasVencidas            ALIAS FOR $4;
    stEntidade                  ALIAS FOR $5;
    stExercicio                 ALIAS FOR $6;    
    stAcao                      ALIAS FOR $7; 
    inCodLote                   ALIAS FOR $8; 
    inCodRegime                 ALIAS FOR $9; 
    stSql                       VARCHAR;
    stContagemTempo             VARCHAR;
    stSqlAux                    VARCHAR;
    reRegistro                  RECORD;
    reFerias                    RECORD;
    nuRetorno                   NUMERIC;
    dtInicial                   DATE;
    dtVencimentoFerias          DATE;
    dtCompetencia               DATE;
    inDataInicioDif             INTEGER;
    inDataFinalDif              INTEGER;
    inContador                  INTEGER:=0;
    inQntCodForma3              INTEGER;
    inQntCodForma4              INTEGER;
    inCodFormaAnterior          INTEGER;
    boRestaDiasFerias           BOOLEAN:=false;
    rwConcederFerias            colunasConcederFerias%ROWTYPE;
BEGIN 
    dtCompetencia := selectIntoVarchar('SELECT dt_final FROM folhapagamento'||stEntidade||'.periodo_movimentacao WHERE cod_periodo_movimentacao = '||inCodPeriodoMovimentacao);

    stSql := 'SELECT * 
                FROM recuperarContratoServidor(''cgm,l,oo,f,rf,anp'', '||quote_literal(stEntidade)||', '||inCodPeriodoMovimentacao||', '||quote_literal(stTipoFiltro)||', '||quote_literal(stValoresFiltro)||', '||quote_literal(stExercicio)||') as conceder_ferias';
    IF stAcao != 'incluir' THEN
        --Filtro para diminuir o tempo de processamento
        --no caso de ação igual a consultar/excluir
        stSql := stSql || ' WHERE EXISTS (SELECT 1
                                            FROM pessoal'||stEntidade||'.ferias';
        IF inCodLote != 0 THEN
            stSql := stSql ||' INNER JOIN pessoal'||stEntidade||'.lote_ferias_lote
                                  ON lote_ferias_lote.cod_ferias = ferias.cod_ferias
                                 AND lote_ferias_lote.cod_lote = '||inCodLote;
        END IF;
        stSql := stSql || '
                                           WHERE ferias.cod_contrato = conceder_ferias.cod_contrato)';
                                           
    ELSE
        --Filtro para retornar apenas contratos que possuam pelo menos registro
        --de evento no período anterior ao atual.
        stSql := stSql || ' WHERE EXISTS (SELECT 1
                                            FROM folhapagamento'||stEntidade||'.registro_evento_periodo
                                      INNER JOIN folhapagamento'||stEntidade||'.ultimo_registro_evento
                                              ON ultimo_registro_evento.cod_registro = registro_evento_periodo.cod_registro
                                           WHERE registro_evento_periodo.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                                             AND registro_evento_periodo.cod_contrato = conceder_ferias.cod_contrato)';        
                                             
        IF inCodRegime != 0 AND 
           inCodRegime IS NOT NULL THEN
            stSql := stSql ||' AND conceder_ferias.cod_regime_funcao = '||inCodRegime;
        END IF;
                                             
    END IF;

    FOR reRegistro IN EXECUTE stSql LOOP
        stSql := 'SELECT ferias.*
                       , lancamento_ferias.mes_competencia
                       , lancamento_ferias.ano_competencia
                       , lancamento_ferias.dt_inicio
                       , lancamento_ferias.dt_fim
                    FROM pessoal'||stEntidade||'.ferias
              INNER JOIN pessoal'||stEntidade||'.lancamento_ferias
                      ON lancamento_ferias.cod_ferias = ferias.cod_ferias';
        IF inCodLote != 0 THEN
            stSql := stSql ||' INNER JOIN pessoal'||stEntidade||'.lote_ferias_lote
                                  ON lote_ferias_lote.cod_ferias = ferias.cod_ferias
                                 AND lote_ferias_lote.cod_lote = '||inCodLote;
        END IF;
        stSql := stSql ||'
                   WHERE ferias.cod_contrato = '||reRegistro.cod_contrato||'
                ORDER BY ferias.cod_contrato
                       , ferias.dt_inicial_aquisitivo
                       , ferias.dt_final_aquisitivo';
        
        -- Instancia a variável para zerar a data a cada contrato
        dtInicial := null;
        
        

        -- LOOP das férias pagas
        FOR reFerias IN EXECUTE stSql LOOP
        
            IF stAcao = 'consultar' OR stAcao = 'excluir' THEN
                rwConcederFerias.cod_ferias             := reFerias.cod_ferias;
                rwConcederFerias.numcgm                 := reRegistro.numcgm;
                rwConcederFerias.nom_cgm                := reRegistro.nom_cgm;
                rwConcederFerias.nom_cgm                := reRegistro.nom_cgm;    
                rwConcederFerias.registro               := reRegistro.registro;   
                rwConcederFerias.cod_contrato           := reRegistro.cod_contrato;
                rwConcederFerias.desc_local             := reRegistro.desc_local; 
                rwConcederFerias.desc_orgao             := reRegistro.desc_orgao; 
                rwConcederFerias.orgao                  := reRegistro.orgao; 
                rwConcederFerias.dt_posse               := reRegistro.dt_posse;   
                rwConcederFerias.dt_admissao            := reRegistro.dt_admissao;
                rwConcederFerias.dt_nomeacao            := reRegistro.dt_nomeacao;
                rwConcederFerias.desc_funcao            := reRegistro.desc_funcao;
                rwConcederFerias.cod_regime_funcao      := reRegistro.cod_regime_funcao;
                rwConcederFerias.desc_regime_funcao     := reRegistro.desc_regime_funcao;
                rwConcederFerias.cod_funcao             := reRegistro.cod_funcao;
                rwConcederFerias.cod_local              := reRegistro.cod_local; 
                rwConcederFerias.cod_orgao              := reRegistro.cod_orgao; 
                rwConcederFerias.bo_cadastradas         := TRUE;            
                rwConcederFerias.situacao               := to_char(reFerias.dt_inicio,'dd/mm/yyyy') ||' a '||to_char(reFerias.dt_fim,'dd/mm/yyyy');
                rwConcederFerias.dt_inicial_aquisitivo  := reFerias.dt_inicial_aquisitivo;
                rwConcederFerias.dt_final_aquisitivo    := reFerias.dt_final_aquisitivo;
                rwConcederFerias.mes_competencia        := reFerias.mes_competencia;
                rwConcederFerias.ano_competencia        := reFerias.ano_competencia;
                rwConcederFerias.dt_inicio              := reFerias.dt_inicio;
                rwConcederFerias.dt_fim                 := reFerias.dt_fim;
                RETURN NEXT rwConcederFerias;
            END IF;

            IF ( dtInicial IS NOT NULL ) THEN                    
                IF (dtInicial != reFerias.dt_inicial_aquisitivo) OR (reFerias.cod_forma IN (3,4)) THEN
                    inDataInicioDif := CAST(SUBSTR(dtInicial::varchar,1,4) AS INTEGER);
                    inDataFinalDif := CAST(SUBSTR(reFerias.dt_inicial_aquisitivo::varchar,1,4) AS INTEGER);
                    inContador := inDataFinalDif - inDataInicioDif;

                    --Conta quantos periodos de 10 dias(cod_forma 3) ou de 15 dias(cod_forma 4) foram gozados e se necessita retirar periodo restante
                    --Calcula a quantidade de vezes que o cada forma de ferias se repete 
                    --visto que a logica antes implementava não possibilitava somar o total dos dias gozados por cada forma
                    --Divide pela quantidade que cada forma exige pegando o resto para validacao
                    --cod_forma 3 =  3 periodos de 10 dias tirados separados
                    --cod_forma 4 =  2 periodos de 15 dias tirados separados
                    stSqlAux := 'SELECT SUM(dias_ferias)
                                    FROM pessoal'||stEntidade||'.ferias
                                WHERE cod_contrato = '||reRegistro.cod_contrato||'
                                  AND cod_forma = 3
                                  AND dt_inicial_aquisitivo = '''||reFerias.dt_inicial_aquisitivo||'''
                                  AND dt_final_aquisitivo = '''||reFerias.dt_final_aquisitivo||'''
                                ';
                    EXECUTE stSqlAux INTO inQntCodForma3;

                    stSqlAux := 'SELECT SUM(dias_ferias)
                                FROM pessoal'||stEntidade||'.ferias
                                WHERE cod_contrato = '||reRegistro.cod_contrato||'
                                  AND cod_forma = 4
                                  AND dt_inicial_aquisitivo = '''||reFerias.dt_inicial_aquisitivo||'''
                                  AND dt_final_aquisitivo = '''||reFerias.dt_final_aquisitivo||'''
                                ';
                    EXECUTE stSqlAux INTO inQntCodForma4;
                    
                    --Nao demonstrar se ja foi tirado os 30 dias de ferias
                    IF (inQntCodForma3 = 30) OR (inQntCodForma4 = 30) THEN
                        boRestaDiasFerias := false;
                    ELSE
                        boRestaDiasFerias := true;
                    END IF;

                    IF (inContador = 0) AND (boRestaDiasFerias = true) AND (reFerias.cod_forma <> inCodFormaAnterior ) THEN
                        inContador := 1;
                    END IF;
    
                    FOR i IN 1..inContador LOOP
                        IF recuperarSituacaoDoContrato(reRegistro.cod_contrato,inCodPeriodoMovimentacao,stEntidade) = 'A' AND stAcao = 'incluir' THEN
                            dtVencimentoFerias := dtInicial::date + '1 year'::interval - '1 day'::interval;
                            rwConcederFerias.numcgm                 := reRegistro.numcgm;
                            rwConcederFerias.nom_cgm                := reRegistro.nom_cgm;
                            rwConcederFerias.nom_cgm                := reRegistro.nom_cgm;    
                            rwConcederFerias.registro               := reRegistro.registro;   
                            rwConcederFerias.cod_contrato           := reRegistro.cod_contrato;
                            rwConcederFerias.desc_local             := reRegistro.desc_local; 
                            rwConcederFerias.desc_orgao             := reRegistro.desc_orgao; 
                            rwConcederFerias.orgao                  := reRegistro.orgao; 
                            rwConcederFerias.dt_posse               := reRegistro.dt_posse;   
                            rwConcederFerias.dt_admissao            := reRegistro.dt_admissao;
                            rwConcederFerias.dt_nomeacao            := reRegistro.dt_nomeacao;
                            rwConcederFerias.desc_funcao            := reRegistro.desc_funcao;
                            rwConcederFerias.cod_regime_funcao      := reRegistro.cod_regime_funcao;
                            rwConcederFerias.desc_regime_funcao     := reRegistro.desc_regime_funcao;
                            rwConcederFerias.cod_funcao             := reRegistro.cod_funcao;
                            rwConcederFerias.cod_local              := reRegistro.cod_local; 
                            rwConcederFerias.cod_orgao              := reRegistro.cod_orgao; 
                            rwConcederFerias.bo_cadastradas         := FALSE;
                            
                            IF dtVencimentoFerias > dtCompetencia THEN
                                rwConcederFerias.situacao               := 'A Vencer';
                            ELSE
                                rwConcederFerias.situacao               := 'Vencida';
                            END IF;
                            
                            rwConcederFerias.dt_inicial_aquisitivo  := dtInicial;
                            rwConcederFerias.dt_final_aquisitivo    := dtVencimentoFerias;
                            
                            IF boFeriasVencidas IS TRUE THEN
                                IF rwConcederFerias.situacao = 'Vencida' THEN
                                    RETURN NEXT rwConcederFerias;
                                END IF;
                            ELSE
                                RETURN NEXT rwConcederFerias;
                            END IF;
                            
                        END IF;
                        
                        dtInicial := dtVencimentoFerias+1;
                    END LOOP;
                END IF;
                
            END IF;
            
            dtInicial := reFerias.dt_final_aquisitivo+1;
            inCodFormaAnterior := reFerias.cod_forma;

        END LOOP;

        IF dtInicial IS NULL THEN
            stSql := 'SELECT valor 
                        FROM administracao.configuracao 
                       WHERE parametro = ''dtContagemInicial'||stEntidade||''' AND exercicio = '||quote_literal(stExercicio)||'';
            stContagemTempo := selectIntoVarchar(stSql);
            
            IF selectIntoVarchar(stSql) = 'dtPosse' THEN
                dtInicial := reRegistro.dt_posse;
            END IF;
            
            IF selectIntoVarchar(stSql) = 'dtNomeacao' THEN
                dtInicial := reRegistro.dt_nomeacao;
            END IF;
            
            IF selectIntoVarchar(stSql) = 'dtAdmissao' THEN
                dtInicial := reRegistro.dt_admissao;
            END IF;
            
        END IF;
        
        IF recuperarSituacaoDoContrato(reRegistro.cod_contrato,inCodPeriodoMovimentacao,stEntidade) = 'A' AND stAcao = 'incluir' THEN
            --
            --dtVencimentoFerias = dtInicial + 1 ano (bissexto ou nao) - 1 dia (pois o primeiro dia da dtInicial tambem e incluido para o calculo)
            --
            
            dtVencimentoFerias := dtInicial::date + '1 year'::interval - '1 day'::interval;
            rwConcederFerias.numcgm                 := reRegistro.numcgm;
            rwConcederFerias.nom_cgm                := reRegistro.nom_cgm;
            rwConcederFerias.nom_cgm                := reRegistro.nom_cgm;    
            rwConcederFerias.registro               := reRegistro.registro;   
            rwConcederFerias.cod_contrato           := reRegistro.cod_contrato;
            rwConcederFerias.desc_local             := reRegistro.desc_local; 
            rwConcederFerias.desc_orgao             := reRegistro.desc_orgao; 
            rwConcederFerias.orgao                  := reRegistro.orgao; 
            rwConcederFerias.dt_posse               := reRegistro.dt_posse;   
            rwConcederFerias.dt_admissao            := reRegistro.dt_admissao;
            rwConcederFerias.dt_nomeacao            := reRegistro.dt_nomeacao;
            rwConcederFerias.desc_funcao            := reRegistro.desc_funcao;
            rwConcederFerias.cod_regime_funcao      := reRegistro.cod_regime_funcao;
            rwConcederFerias.desc_regime_funcao     := reRegistro.desc_regime_funcao;
            rwConcederFerias.cod_funcao             := reRegistro.cod_funcao;
            rwConcederFerias.cod_local              := reRegistro.cod_local; 
            rwConcederFerias.cod_orgao              := reRegistro.cod_orgao; 
            rwConcederFerias.bo_cadastradas         := FALSE;
            
            IF dtVencimentoFerias > dtCompetencia THEN
                rwConcederFerias.situacao               := 'A Vencer';
            ELSE
                rwConcederFerias.situacao               := 'Vencida';
            END IF;
            
            rwConcederFerias.dt_inicial_aquisitivo  := dtInicial;
            rwConcederFerias.dt_final_aquisitivo    := dtVencimentoFerias;
            
            IF boFeriasVencidas IS TRUE THEN
                IF rwConcederFerias.situacao = 'Vencida' THEN
                    RETURN NEXT rwConcederFerias;
                END IF;
            ELSE
                RETURN NEXT rwConcederFerias;
            END IF;
            
            dtInicial := dtVencimentoFerias + 1;
            
        END IF;
        
    END LOOP;
END 
$$ LANGUAGE 'plpgsql';

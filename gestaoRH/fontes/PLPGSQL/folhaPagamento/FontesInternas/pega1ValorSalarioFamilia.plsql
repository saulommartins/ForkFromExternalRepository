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
* script de funcao PLSQL
* 
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br

* $Revision: 23095 $
* $Name$
* $Autor: Marcia $
* Date: 2006/05/17 10:50:00 $
*
* Caso de uso: uc-04.05.15
* Caso de uso: uc-04.05.48
*
* Objetivo: a partir dos buffers relativos a competencia e ao codigo da 
* previdencia oficial se obtem o codigo do regime previdenciario qeu leva as 
* faixas de pagamento para a avaliacao. 
*/
CREATE OR REPLACE FUNCTION pega1ValorSalarioFamilia(numeric) RETURNS NUMERIC AS $$
DECLARE
    nuValorBase                 ALIAS FOR $1;
    nuValorSalarioFamilia       NUMERIC := 0.00;
    nuValorTemp                 NUMERIC := 0.00;
    nuValorBaseAcumulado        NUMERIC := 0.00;
    inCodRegimePrevidencia      INTEGER := 0;
    inDiasTrabalhados           INTEGER := 0;
    inDiasNaoTrabalhados        INTEGER := 0;
    inCodContrato               INTEGER := 0;
    inCodContratoAux            INTEGER := 0;
    inCodPrevidencia            INTEGER := 0;    
    inCodPeriodoMovimentacao    INTEGER := 0;    
    inCodEventoBase             INTEGER := 0;    
    inCodEventoProvento         INTEGER := 0;    
    inNumCgm                    INTEGER := 0;
    inCodRegistro               INTEGER := 0;
    inTotalDiasMes              INTEGER := 0;
    stDataFinalCompetencia      VARCHAR := '';
    stEntidade                  VARCHAR := '';
    stSql                       VARCHAR := '';
    stTipoFolha                 VARCHAR := '';
    stSituacaoDaFolha           VARCHAR := '';
    dtAdmissao                  DATE;
    dtRescisao                  DATE;
    dtFinalCompetencia          DATE;
    boRetorno                   BOOLEAN;
    boGeraValor                 BOOLEAN := TRUE;
    boZerarValorSalarioFamilia  BOOLEAN := FALSE;
    reRegistro                  RECORD;
    reRegistroRescisao          RECORD;
BEGIN
    --Ticket #13869
    stEntidade := recuperarBufferTexto('stEntidade');
    stDataFinalCompetencia := recuperarBufferTexto('stDataFinalCompetencia');    
    inCodRegimePrevidencia := pega1RegimePrevidenciarioPrevidenciaOficial();
    inCodContrato := recuperarBufferInteiro('inCodContrato');
    inCodPrevidencia := recuperarBufferInteiro('inCodPrevidenciaOficial');
    inCodPeriodoMovimentacao := recuperarBufferInteiro('inCodPeriodoMovimentacao');
    inNumCgm := recuperarBufferInteiro('inNumCgm');
    dtFinalCompetencia := to_date(stDataFinalCompetencia,'yyyy-mm-dd');

    IF inCodRegimePrevidencia IS NULL THEN
        --Erro quando código do regime da previdencia FOR nulo não deve gerar valor de salário família
        boGeraValor := FALSE;
    END IF;

    IF boGeraValor IS TRUE THEN
        --Evento de salário família
        stSql := '    SELECT salario_familia_evento.cod_evento
                        FROM folhapagamento'||stEntidade||'.salario_familia_evento
                  INNER JOIN (  SELECT cod_tipo
                                     , cod_regime_previdencia
                                     , max(timestamp) as timestamp
                                  FROM folhapagamento'||stEntidade||'.salario_familia_evento
                              GROUP BY cod_tipo
                                     , cod_regime_previdencia) as max_salario_familia_evento
                          ON salario_familia_evento.cod_tipo = max_salario_familia_evento.cod_tipo
                         AND salario_familia_evento.cod_regime_previdencia = max_salario_familia_evento.cod_regime_previdencia
                         AND salario_familia_evento.timestamp = max_salario_familia_evento.timestamp
                       WHERE salario_familia_evento.cod_tipo = 1';
        inCodEventoProvento := selectIntoInteger(stSql);

        stSql := '    SELECT salario_familia_evento.cod_evento
                        FROM folhapagamento'||stEntidade||'.salario_familia_evento
                  INNER JOIN (  SELECT cod_tipo
                                     , cod_regime_previdencia
                                     , max(timestamp) as timestamp
                                  FROM folhapagamento'||stEntidade||'.salario_familia_evento
                              GROUP BY cod_tipo
                                     , cod_regime_previdencia) as max_salario_familia_evento
                          ON salario_familia_evento.cod_tipo = max_salario_familia_evento.cod_tipo
                         AND salario_familia_evento.cod_regime_previdencia = max_salario_familia_evento.cod_regime_previdencia
                         AND salario_familia_evento.timestamp = max_salario_familia_evento.timestamp
                       WHERE salario_familia_evento.cod_tipo = 2';
        inCodEventoBase := selectIntoInteger(stSql);

        --PROCESSAMENTO DA FOLHA SALÁRIO
        --VERIFICAÇÃO DOS CONTRATOS DE MESMO SERVIDOR NA FOLHA SALÁRIO
        --CASO EXISTA MAIS DE UM CONTRATO PARA O MESMO SERVIDOR, TODOS
        --OS CONTRATOS DEVEM ESTAR CALCULADOS PARA QUE SEJA SOMADO O VALOR DAS BASES DE SALÁRIO FAMÍLIA
        stSql := '    SELECT contrato.*
                        FROM pessoal'||stEntidade||'.contrato
                  INNER JOIN pessoal'||stEntidade||'.servidor_contrato_servidor
                          ON contrato.cod_contrato = servidor_contrato_servidor.cod_contrato
                  INNER JOIN pessoal'||stEntidade||'.servidor
                          ON servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                       WHERE EXISTS (    SELECT 1
                                           FROM pessoal'||stEntidade||'.contrato_servidor_previdencia
                                     INNER JOIN (  SELECT cod_contrato
                                                        , max(timestamp) as timestamp
                                                     FROM pessoal'||stEntidade||'.contrato_servidor_previdencia
                                                 GROUP BY cod_contrato) as max_contrato_servidor_previdencia
                                             ON contrato_servidor_previdencia.cod_contrato = max_contrato_servidor_previdencia.cod_contrato
                                            AND contrato_servidor_previdencia.timestamp = max_contrato_servidor_previdencia.timestamp
                                     INNER JOIN folhapagamento'||stEntidade||'.previdencia
                                             ON contrato_servidor_previdencia.cod_previdencia = previdencia.cod_previdencia
                                            AND previdencia.cod_regime_previdencia = '||inCodRegimePrevidencia||'
                                          WHERE contrato_servidor_previdencia.cod_contrato = contrato.cod_contrato
                                            AND contrato_servidor_previdencia.cod_previdencia = '||inCodPrevidencia||')
                         AND servidor.numcgm = '||inNumCgm||'
                         AND contrato.cod_contrato != '||inCodContrato||'
                         AND NOT EXISTS (SELECT 1
                                           FROM pessoal'||stEntidade||'.contrato_servidor_caso_causa
                                          WHERE contrato_servidor_caso_causa.cod_contrato = contrato.cod_contrato)
                         AND EXISTS (    SELECT 1
                                           FROM folhapagamento'||stEntidade||'.registro_evento_periodo
                                     INNER JOIN folhapagamento'||stEntidade||'.ultimo_registro_evento
                                             ON registro_evento_periodo.cod_registro = ultimo_registro_evento.cod_registro
                                          WHERE registro_evento_periodo.cod_contrato = contrato.cod_contrato
                                            AND registro_evento_periodo.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||')';

        FOR reRegistro IN EXECUTE stSql LOOP 
            stSql := '    SELECT evento_calculado.valor
                            FROM folhapagamento'||stEntidade||'.registro_evento_periodo
                      INNER JOIN folhapagamento'||stEntidade||'.evento_calculado
                              ON registro_evento_periodo.cod_registro = evento_calculado.cod_registro
                           WHERE registro_evento_periodo.cod_contrato = '||reRegistro.cod_contrato||'
                             AND registro_evento_periodo.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                             AND evento_calculado.cod_evento = '||inCodEventoBase;
            nuValorTemp := selectIntoNumeric(stSql);
            IF nuValorTemp IS NULL THEN
                --Erro nuValorTemp FOR nulo, nesse caso existe um contrato do servidor que não foi calculado
                --por tanto não deve calcular salario família, pois o salário família só deverá ser calculado
                --no momento em que a última matrícula do servidor estiver sendo calculada. Isso é feito
                --para que seja obtido o valor correto do base de calculo do salário família, o qual deve ser
                --acumulado de todas as matrículas
                boGeraValor := FALSE;                        
            ELSE
                nuValorBaseAcumulado := nuValorBaseAcumulado + nuValorTemp;
                inCodContratoAux := reRegistro.cod_contrato;
            END IF;
        END LOOP;

        --PROCESSAMENTO DA FOLHA COMPLEMENTAR
        --VERIFICAÇÃO DOS CONTRATOS DE MESMO SERVIDOR NA FOLHA COMPLEMENTAR
        --CASO EXISTA MAIS DE UM CONTRATO PARA O MESMO SERVIDOR, TODOS
        --OS CONTRATOS DEVEM ESTAR CALCULADOS PARA QUE SEJA SOMADO O VALOR DAS BASES DE SALÁRIO FAMÍLIA
        stSql := '    SELECT contrato.*
                        FROM pessoal'||stEntidade||'.contrato
                  INNER JOIN pessoal'||stEntidade||'.servidor_contrato_servidor
                          ON contrato.cod_contrato = servidor_contrato_servidor.cod_contrato
                  INNER JOIN pessoal'||stEntidade||'.servidor
                          ON servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                       WHERE EXISTS (    SELECT 1
                                           FROM pessoal'||stEntidade||'.contrato_servidor_previdencia
                                     INNER JOIN (  SELECT cod_contrato
                                                        , max(timestamp) as timestamp
                                                     FROM pessoal'||stEntidade||'.contrato_servidor_previdencia
                                                 GROUP BY cod_contrato) as max_contrato_servidor_previdencia
                                             ON contrato_servidor_previdencia.cod_contrato = max_contrato_servidor_previdencia.cod_contrato
                                            AND contrato_servidor_previdencia.timestamp = max_contrato_servidor_previdencia.timestamp
                                     INNER JOIN folhapagamento'||stEntidade||'.previdencia
                                             ON contrato_servidor_previdencia.cod_previdencia = previdencia.cod_previdencia
                                            AND previdencia.cod_regime_previdencia = '||inCodRegimePrevidencia||'
                                          WHERE contrato_servidor_previdencia.cod_contrato = contrato.cod_contrato
                                            AND contrato_servidor_previdencia.cod_previdencia = '||inCodPrevidencia||')
                         AND servidor.numcgm = '||inNumCgm||'
                         AND contrato.cod_contrato != '||inCodContrato||'
                         AND EXISTS (    SELECT 1
                                           FROM folhapagamento'||stEntidade||'.registro_evento_complementar
                                     INNER JOIN folhapagamento'||stEntidade||'.ultimo_registro_evento_complementar 
                                             ON registro_evento_complementar.cod_registro = ultimo_registro_evento_complementar.cod_registro
                                            AND registro_evento_complementar.cod_evento = ultimo_registro_evento_complementar.cod_evento
                                            AND registro_evento_complementar.cod_configuracao = ultimo_registro_evento_complementar.cod_configuracao
                                            AND registro_evento_complementar.timestamp = ultimo_registro_evento_complementar.timestamp
                                          WHERE registro_evento_complementar.cod_contrato = contrato.cod_contrato
                                            AND registro_evento_complementar.cod_configuracao = 1
                                            AND registro_evento_complementar.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||')';

        FOR reRegistro IN EXECUTE stSql LOOP    
            stSql := '    SELECT evento_complementar_calculado.valor
                            FROM folhapagamento'||stEntidade||'.registro_evento_complementar
                      INNER JOIN folhapagamento'||stEntidade||'.evento_complementar_calculado
                              ON registro_evento_complementar.cod_registro = evento_complementar_calculado.cod_registro
                             AND registro_evento_complementar.cod_evento = evento_complementar_calculado.cod_evento
                             AND registro_evento_complementar.cod_configuracao = evento_complementar_calculado.cod_configuracao
                             AND registro_evento_complementar.timestamp = evento_complementar_calculado.timestamp_registro                             
                           WHERE  registro_evento_complementar.cod_configuracao = 1
                             AND registro_evento_complementar.cod_contrato = '||reRegistro.cod_contrato||'
                             AND registro_evento_complementar.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                             AND evento_complementar_calculado.cod_evento = '||inCodEventoBase;
            nuValorTemp := selectIntoNumeric(stSql);
            IF nuValorTemp IS NULL THEN
                --Erro nuValorTemp FOR nulo, nesse caso existe um contrato do servidor que não foi calculado
                --por tanto não deve calcular salario família, pois o salário família só deverá ser calculado
                --no momento em que a última matrícula do servidor estiver sendo calculada. Isso é feito
                --para que seja obtido o valor correto do base de calculo do salário família, o qual deve ser
                --acumulado de todas as matrículas
                boGeraValor := FALSE;                        
            ELSE
                nuValorBaseAcumulado := nuValorBaseAcumulado + nuValorTemp;
            END IF;
        END LOOP;

        --PROCESSAMENTO DA FOLHA RESCISAO
        --VERIFICAÇÃO DOS CONTRATOS DE MESMO SERVIDOR NA FOLHA RESCISÃO
        --CASO EXISTA MAIS DE UM CONTRATO PARA O MESMO SERVIDOR, TODOS
        --OS CONTRATOS DEVEM ESTAR CALCULADOS PARA QUE SEJA SOMADO O VALOR DAS BASES DE SALÁRIO FAMÍLIA
        stSql := '    SELECT contrato.*
                        FROM pessoal'||stEntidade||'.contrato
                  INNER JOIN pessoal'||stEntidade||'.servidor_contrato_servidor
                          ON contrato.cod_contrato = servidor_contrato_servidor.cod_contrato
                  INNER JOIN pessoal'||stEntidade||'.servidor
                          ON servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                       WHERE EXISTS (    SELECT 1
                                           FROM pessoal'||stEntidade||'.contrato_servidor_previdencia
                                     INNER JOIN (  SELECT cod_contrato
                                                        , max(timestamp) as timestamp
                                                     FROM pessoal'||stEntidade||'.contrato_servidor_previdencia
                                                 GROUP BY cod_contrato) as max_contrato_servidor_previdencia
                                             ON contrato_servidor_previdencia.cod_contrato = max_contrato_servidor_previdencia.cod_contrato
                                            AND contrato_servidor_previdencia.timestamp = max_contrato_servidor_previdencia.timestamp
                                     INNER JOIN folhapagamento'||stEntidade||'.previdencia
                                             ON contrato_servidor_previdencia.cod_previdencia = previdencia.cod_previdencia
                                            AND previdencia.cod_regime_previdencia = '||inCodRegimePrevidencia||'
                                          WHERE contrato_servidor_previdencia.cod_contrato = contrato.cod_contrato
                                            AND contrato_servidor_previdencia.cod_previdencia = '||inCodPrevidencia||')
                         AND servidor.numcgm = '||inNumCgm||'
                         AND contrato.cod_contrato != '||inCodContrato||'
                         AND EXISTS (    SELECT 1
                                           FROM folhapagamento'||stEntidade||'.registro_evento_rescisao
                                     INNER JOIN folhapagamento'||stEntidade||'.ultimo_registro_evento_rescisao
                                             ON registro_evento_rescisao.cod_registro = ultimo_registro_evento_rescisao.cod_registro
                                            AND registro_evento_rescisao.cod_evento = ultimo_registro_evento_rescisao.cod_evento
                                            AND registro_evento_rescisao.desdobramento = ultimo_registro_evento_rescisao.desdobramento
                                            AND registro_evento_rescisao.timestamp = ultimo_registro_evento_rescisao.timestamp
                                          WHERE registro_evento_rescisao.cod_contrato = contrato.cod_contrato
                                            AND registro_evento_rescisao.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||')';

        FOR reRegistro IN EXECUTE stSql LOOP
            stSql := '    SELECT evento_rescisao_calculado.valor
                            FROM folhapagamento'||stEntidade||'.registro_evento_rescisao
                      INNER JOIN folhapagamento'||stEntidade||'.evento_rescisao_calculado
                              ON registro_evento_rescisao.cod_registro = evento_rescisao_calculado.cod_registro
                             AND registro_evento_rescisao.cod_evento = evento_rescisao_calculado.cod_evento
                             AND registro_evento_rescisao.desdobramento = evento_rescisao_calculado.desdobramento
                             AND registro_evento_rescisao.timestamp = evento_rescisao_calculado.timestamp_registro
                           WHERE registro_evento_rescisao.cod_contrato = '||reRegistro.cod_contrato||'
                             AND registro_evento_rescisao.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                             AND evento_rescisao_calculado.cod_evento = '||inCodEventoBase||'
                             AND evento_rescisao_calculado.desdobramento = '||quote_literal('S')||' ';
            nuValorTemp := selectIntoNumeric(stSql);
            IF nuValorTemp IS NULL THEN
                --Erro nuValorTemp FOR nulo, nesse caso existe um contrato do servidor que não foi calculado
                --por tanto não deve calcular salario família, pois o salário família só deverá ser calculado
                --no momento em que a última matrícula do servidor estiver sendo calculada. Isso é feito
                --para que seja obtido o valor correto do base de calculo do salário família, o qual deve ser
                --acumulado de todas as matrículas
                --boGeraValor := FALSE;                        
            ELSE
                nuValorBaseAcumulado := nuValorBaseAcumulado + nuValorTemp;
            END IF;
            
            --Procura do evento de provento em algum contrato do servidor na folha rescisão
            --caso exista, o salário família já foi pago na folha rescisão enão deve ser
            --inserido novamente na folha de salário
            stSql := '    SELECT evento_rescisao_calculado.valor
                            FROM folhapagamento'||stEntidade||'.registro_evento_rescisao
                      INNER JOIN folhapagamento'||stEntidade||'.evento_rescisao_calculado
                              ON registro_evento_rescisao.cod_registro = evento_rescisao_calculado.cod_registro
                             AND registro_evento_rescisao.cod_evento = evento_rescisao_calculado.cod_evento
                             AND registro_evento_rescisao.desdobramento = evento_rescisao_calculado.desdobramento
                             AND registro_evento_rescisao.timestamp = evento_rescisao_calculado.timestamp_registro
                           WHERE registro_evento_rescisao.cod_contrato = '||reRegistro.cod_contrato||'
                             AND registro_evento_rescisao.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                             AND evento_rescisao_calculado.cod_evento = '||inCodEventoProvento||'
                             AND evento_rescisao_calculado.desdobramento = '||quote_literal('S')||' ';
                             
            nuValorTemp := selectIntoNumeric(stSql);
            
            IF nuValorTemp IS NOT NULL THEN
                stSql  := 'SELECT contrato_servidor_caso_causa.dt_rescisao
                             FROM pessoal'||stEntidade||'.contrato_servidor_caso_causa
                            WHERE contrato_servidor_caso_causa.cod_contrato = '||reRegistro.cod_contrato;               
                
                FOR reRegistroRescisao IN EXECUTE stSql LOOP    
                    IF to_char(reRegistroRescisao.dt_rescisao,'yyyy-mm') = to_char(dtFinalCompetencia,'yyyy-mm') THEN
                        inDiasTrabalhados := to_char(reRegistroRescisao.dt_rescisao,'dd');               
                        inTotalDiasMes := to_char(last_day(dtFinalCompetencia),'dd')::integer;
                  
                        IF inTotalDiasMes = inDiasTrabalhados THEN
                            inDiasTrabalhados = 30;
                        END IF;                
                      
                        IF inDiasTrabalhados = 30 THEN
                            boGeraValor := FALSE;
                        END IF;
                    END IF;
                END LOOP;
            END IF;
        END LOOP;
    END IF;

    IF boGeraValor IS TRUE THEN
        --Para o caso de afastamentos doença ou acidente da previdencia, 
        --deve pagar integralmente o valor no início do afastamento. No mês de retorno 
        --do afastamento não deve calcular o salario familia.
        stSql := '    SELECT TRUE AS retorno
                        FROM pessoal'||stEntidade||'.assentamento_gerado_contrato_servidor
                  INNER JOIN pessoal'||stEntidade||'.assentamento_gerado
                          ON assentamento_gerado_contrato_servidor.cod_assentamento_gerado = assentamento_gerado.cod_assentamento_gerado
                  INNER JOIN (  SELECT cod_assentamento_gerado
                                     , max(timestamp) as timestamp
                                  FROM pessoal'||stEntidade||'.assentamento_gerado
                              GROUP BY cod_assentamento_gerado) as max_assentamento_gerado
                          ON assentamento_gerado.cod_assentamento_gerado = max_assentamento_gerado.cod_assentamento_gerado
                         AND assentamento_gerado.timestamp = max_assentamento_gerado.timestamp
                  INNER JOIN pessoal'||stEntidade||'.assentamento_assentamento
                          ON assentamento_gerado.cod_assentamento = assentamento_assentamento.cod_assentamento
                         AND (assentamento_assentamento.cod_motivo = 5 OR assentamento_assentamento.cod_motivo = 6)
                  INNER JOIN pessoal'||stEntidade||'.contrato_servidor_previdencia
                          ON assentamento_gerado_contrato_servidor.cod_contrato = contrato_servidor_previdencia.cod_contrato
                         AND contrato_servidor_previdencia.bo_excluido IS FALSE
                         AND contrato_servidor_previdencia.cod_previdencia = '||inCodPrevidencia||'
                  INNER JOIN (  SELECT cod_contrato
                                     , max(timestamp) as timestamp
                                  FROM pessoal'||stEntidade||'.contrato_servidor_previdencia
                              GROUP BY cod_contrato) as max_contrato_servidor_previdencia
                          ON contrato_servidor_previdencia.cod_contrato = max_contrato_servidor_previdencia.cod_contrato
                         AND contrato_servidor_previdencia.timestamp = max_contrato_servidor_previdencia.timestamp
                  INNER JOIN folhapagamento'||stEntidade||'.previdencia
                          ON contrato_servidor_previdencia.cod_previdencia = previdencia.cod_previdencia
                         AND previdencia.cod_regime_previdencia = '||inCodRegimePrevidencia||'
                       WHERE NOT EXISTS (SELECT 1
                                           FROM pessoal'||stEntidade||'.assentamento_gerado_excluido
                                          WHERE assentamento_gerado.cod_assentamento_gerado = assentamento_gerado_excluido.cod_assentamento_gerado
                                            AND assentamento_gerado.timestamp = assentamento_gerado_excluido.timestamp)
                         AND to_char(assentamento_gerado.periodo_final,'||quote_literal('yyyy-mm')||') = '''||to_char(dtFinalCompetencia,'yyyy-mm')||'''
                         AND assentamento_gerado_contrato_servidor.cod_contrato = '||inCodContrato;
        boRetorno := selectIntoBoolean(stSql);
        IF boRetorno IS TRUE THEN
            --Erro quando boRetorno FOR TRUE, não deve gerar valor de salário família
            --motivo do servidor possuir um assentamento do tipo doença ou acidente com
            --data de retorno no mesmo mês da competência.
            boGeraValor := FALSE;        
        END IF;
    END IF;

    IF boGeraValor IS TRUE THEN
        dtFinalCompetencia := to_date(stDataFinalCompetencia,'yyyy-mm-dd');
        nuValorBaseAcumulado := nuValorBaseAcumulado + nuValorBase;
                
        stSql := '         SELECT vl_pagamento
                             FROM folhapagamento'||stEntidade||'.salario_familia as sf
                  LEFT OUTER JOIN (SELECT vl_pagamento
                                        , cod_regime_previdencia
                                        , timestamp
                                     FROM folhapagamento'||stEntidade||'.faixa_pagamento_salario_familia 
                                    WHERE '||nuValorBaseAcumulado||' between vl_inicial AND vl_final) as fpsf
                               ON sf.cod_regime_previdencia = fpsf.cod_regime_previdencia
                              AND sf.timestamp = fpsf.timestamp
                            WHERE sf.cod_regime_previdencia  = '||inCodRegimePrevidencia||'
                              AND sf.vigencia <= '''||dtFinalCompetencia||'''
                         ORDER BY sf.timestamp desc
                          LIMIT 1 ';
        nuValorSalarioFamilia := selectIntoNumeric(stSql);
        
        IF nuValorSalarioFamilia IS NULL THEN
            nuValorSalarioFamilia := 0.00;
        ELSE
            --No caso de nuValorBase = 0.00, o contrato que está sendo calculodo não gerou
            --base de salário família, por isso não pode ser pago salário família nesse
            --contrato. Porem existe valores acumulados que dever gerar salário família
            --em um outro contrato.
            IF nuValorBase = 0.00 THEN
                boZerarValorSalarioFamilia = true;
                inCodContratoAux := criarBufferInteiro('inCodContrato',inCodContratoAux);
            END IF;


            --Conforme verificado e descrito nas regras para cálculo do salário familia (www.mpas.gov.br), 
            --para os casos de admissão ou rescisão do servidor, o sistema deve proporcionalizar o valor 
            --do salário familia em relação aos dias trabalhados.

            --O valor da cota para o segurado empregado será proporcional nos meses de admissão e demissão.
            --Calculo de salário no mês de admissão
            dtAdmissao := pega0DataAdmissao();
            IF to_char(dtAdmissao,'yyyy-mm') = to_char(dtFinalCompetencia,'yyyy-mm') THEN            
                inDiasTrabalhados := dtFinalCompetencia - dtAdmissao + 1;         
                inDiasNaoTrabalhados := to_char(dtFinalCompetencia::date,'dd')::integer - inDiasTrabalhados;
                inDiasTrabalhados := 30 - inDiasNaoTrabalhados;
                nuValorSalarioFamilia := (nuValorSalarioFamilia/30)*inDiasTrabalhados;
            END IF;
            
            --Calculo de salário no mês de rescisão
            dtRescisao := pega0DataRescisao();    
            IF to_char(dtRescisao,'yyyy-mm') = to_char(dtFinalCompetencia,'yyyy-mm') THEN
                inDiasTrabalhados := to_char(dtRescisao,'dd');               
                inTotalDiasMes := to_char(last_day(dtFinalCompetencia),'dd')::integer;
                
                IF inTotalDiasMes = inDiasTrabalhados THEN
                    inDiasTrabalhados = 30;
                END IF;
                
                nuValorSalarioFamilia := (nuValorSalarioFamilia/30)*inDiasTrabalhados;
            END IF;

            --Recupera o tipo de folha que está sendo calculada
            stTipoFolha := recuperarBufferTexto('stTipoFolha');

            
            --Folha Rescisão
            --No caso da folha de rescisão gerar salário família, deverá ser verificado se a folha salário está fechada. 
            --No caso da folha salário estar FECHADA e algum contrato calculado do servidor possuir salário família
            --não inserir o evento de salário família no folha de rescisão
            --No caso da folha salário estar ABERTA e algum contrato calculado do servidor possuir salário família
            --excluir esse evento calculo do contrato em questão e inserir o salário família na rescisão normalmente
            IF stTipoFolha = 'R' THEN
                stSituacaoDaFolha := pega0SituacaoDaFolhaSalario();

                --Procura o evento de salário família na folha salário
                stSql := '    SELECT registro_evento_periodo.cod_registro
                                FROM folhapagamento'||stEntidade||'.registro_evento_periodo
                          INNER JOIN folhapagamento'||stEntidade||'.evento_calculado
                                  ON registro_evento_periodo.cod_registro = evento_calculado.cod_registro
                          INNER JOIN pessoal'||stEntidade||'.servidor_contrato_servidor
                                  ON registro_evento_periodo.cod_contrato = servidor_contrato_servidor.cod_contrato
                          INNER JOIN pessoal'||stEntidade||'.servidor
                                  ON servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                               WHERE servidor.numcgm = '||inNumCgm||'
                                 AND registro_evento_periodo.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                                 AND evento_calculado.cod_evento = '||inCodEventoProvento;
                inCodRegistro = selectIntoInteger(stSql);

                IF inCodRegistro IS NOT NULL THEN
                   IF stSituacaoDaFolha = 'a' THEN
                        --Folha Salário Aberta
                        stSql := 'DELETE FROM folhapagamento'||stEntidade||'.evento_calculado WHERE cod_registro = '||inCodRegistro;
                        EXECUTE stSql;
                   ELSE
                        --Folha Salário Fechada
                        nuValorSalarioFamilia := 0.00;
                        boZerarValorSalarioFamilia := false;
                   END IF;
                END IF;
            END IF;

            IF boZerarValorSalarioFamilia AND inCodContratoAux IS NOT NULL THEN
                --Insert evento de salário família em outro contrato do servidor (inCodContratoAux)
                stSql := 'INSERT INTO folhapagamento'||stEntidade||'.evento_calculado (
                          SELECT ultimo_registro_evento.cod_evento
                               , ultimo_registro_evento.cod_registro
                               , ultimo_registro_evento.timestamp
                               , '||nuValorSalarioFamilia||' as valor
                               , 0.00 as quantidade
                            FROM folhapagamento'||stEntidade||'.registro_evento_periodo
                      INNER JOIN folhapagamento'||stEntidade||'.ultimo_registro_evento
                              ON registro_evento_periodo.cod_registro = ultimo_registro_evento.cod_registro
                           WHERE registro_evento_periodo.cod_contrato = '||inCodContratoAux||'
                             AND registro_evento_periodo.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                             AND ultimo_registro_evento.cod_evento = '||inCodEventoProvento||')';
                EXECUTE stSql;

                nuValorSalarioFamilia := 0.00;
                inCodContrato := criarBufferInteiro('inCodContrato',inCodContrato);
            END IF;
        END IF;
    END IF;
    
    RETURN nuValorSalarioFamilia;
END;
$$ LANGUAGE 'plpgsql';

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
--    * Data de Criação: 13/04/2006
--
--
--    * @author Analista: Vandré Miguel Ramos
--    * @author Desenvolvedor: Diego Lemos de Souza
--
--    * @package URBEM
--    * @subpackage
--
--    $Revision: 25703 $
--    $Name$
--    $Author: souzadl $
--    $Date: 2007-09-28 13:09:20 -0300 (Sex, 28 Set 2007) $
--
--    * Casos de uso: uc-04.05.09
--*/

CREATE OR REPLACE FUNCTION processarValorBaseDeducao(INTEGER) RETURNS BOOLEAN as $$

DECLARE
    inCodContrato               ALIAS FOR $1;
    inCodPeriodoMovimentacao    INTEGER;
    inCodPrevidencia            INTEGER;
    inCodEvento                 INTEGER;
    inCodEventoCPensao          INTEGER;
    inCodEventoSPensao          INTEGER;
    inCodEvento65Anos           INTEGER;
    inCodRegistro               INTEGER;
    inCountTemp                 INTEGER;
    inNumContratos              INTEGER := 0;
    inNumCgm                    INTEGER;
    stTimestampRegistro         VARCHAR := '';
    stSql                       VARCHAR := '';
    stSqlSemPensao              VARCHAR := '';
    stSqlComPensao              VARCHAR := '';
    dtVigencia                  VARCHAR := '';
    stSituacaoComplementar      VARCHAR := '';
    crCursor                    REFCURSOR;
    nuValorTemp                 NUMERIC := 0.00;
    nuValor                     NUMERIC := 0.00;
    nuValorPensao               NUMERIC := 0.00;
    nuValorDeducaoDependente    NUMERIC := 0.00;
    nuValorDescontoExternoAnterior NUMERIC := 0.00;
    boRetorno                   BOOLEAN := TRUE;
    stEntidade                  VARCHAR;
BEGIN
    inCodPrevidencia         := recuperarBufferInteiro('inCodPrevidenciaOficial');    
    --If para verificar a existencia de um buffer para o cod_previdencia
    IF inCodPrevidencia IS NOT NULL AND inCodPrevidencia != 0 THEN
        stEntidade            := recuperarBufferTexto('stEntidade');
        inCodPeriodoMovimentacao := recuperarBufferInteiro('inCodPeriodoMovimentacao');    
        dtVigencia               := recuperarBufferTexto('dtVigenciaPrevidencia');
        inNumCgm                 := recuperarBufferInteiro('inNumCgm');
        nuValorTemp := selectIntoNumeric('SELECT evento_calculado.valor
                                    FROM folhapagamento'||stEntidade||'.previdencia_evento
                                        , (  SELECT cod_previdencia
                                                , max(timestamp) as timestamp
                                                FROM folhapagamento'||stEntidade||'.previdencia_previdencia
                                            WHERE vigencia = '''||dtVigencia||'''
                                            GROUP BY cod_previdencia) as max_previdencia_previdencia
                                        , folhapagamento'||stEntidade||'.evento
                                        , folhapagamento'||stEntidade||'.registro_evento_periodo
                                        , folhapagamento'||stEntidade||'.ultimo_registro_evento
                                        , folhapagamento'||stEntidade||'.evento_calculado
                                    WHERE previdencia_evento.cod_tipo = 1
                                    AND previdencia_evento.cod_previdencia = '||inCodPrevidencia||'
                                    AND registro_evento_periodo.cod_contrato = '||inCodContrato||'
                                    AND registro_evento_periodo.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                                    AND previdencia_evento.cod_previdencia = max_previdencia_previdencia.cod_previdencia
                                    AND previdencia_evento.timestamp = max_previdencia_previdencia.timestamp
                                    AND previdencia_evento.cod_evento = evento.cod_evento
                                    AND evento.cod_evento = ultimo_registro_evento.cod_evento
                                    AND ultimo_registro_evento.cod_registro = registro_evento_periodo.cod_registro
                                    AND ultimo_registro_evento.cod_evento = evento_calculado.cod_evento
                                    AND ultimo_registro_evento.cod_registro = evento_calculado.cod_registro
                                    AND ultimo_registro_evento.timestamp = evento_calculado.timestamp_registro');

        IF nuValorTemp IS NOT NULL THEN
            nuValor := nuValor + nuValorTemp;
        END IF;
        --Busca código do Evento de Desconto IRRF para Salário/Férias/13o.Salário
        inCodEventoSPensao := selectIntoInteger('SELECT tabela_irrf_evento.cod_evento
                                                   FROM folhapagamento'||stEntidade||'.tabela_irrf_evento
                                                  WHERE tabela_irrf_evento.cod_tipo = 3
                                               ORDER BY timestamp desc LIMIT 1');
                                               
        --Busca código do Evento de Desconto IRRF Salário/Férias/13o.Salário c/Dedução de Pensão Alimentícia
        inCodEventoCPensao := selectIntoInteger('SELECT tabela_irrf_evento.cod_evento
                                                   FROM folhapagamento'||stEntidade||'.tabela_irrf_evento
                                                  WHERE tabela_irrf_evento.cod_tipo = 6
                                               ORDER BY timestamp desc LIMIT 1');                                                   
        IF inCodEventoSPensao is not null AND inCodEventoCPensao is not null THEN 
            stSql := 'SELECT complementar_situacao.situacao
                        FROM folhapagamento'||stEntidade||'.complementar_situacao
                        , (SELECT cod_periodo_movimentacao
                                , cod_complementar
                                , max(timestamp) as timestamp
                                FROM folhapagamento'||stEntidade||'.complementar_situacao
                            GROUP BY cod_periodo_movimentacao
                                , cod_complementar) as max_complementar_situacao                
                        WHERE complementar_situacao.cod_complementar = max_complementar_situacao.cod_complementar
                        AND complementar_situacao.cod_periodo_movimentacao = max_complementar_situacao.cod_periodo_movimentacao
                        AND complementar_situacao.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao;
            stSituacaoComplementar := selectIntoVarchar(stSql);                        
        END IF;        
        
        --Verificação da necessidade de incluir a dedução de dependente na soma da base de dedução
        --Foi criada a tabela deducao_dependente onde é armazenada a informação que diz se já
        --foi utilizada a dedução em alguma matrícula em alguma folha. Caso exista um registro
        --que corresponda ao cgm e periodo de movimentação em calculo quer disser que já
        --foi utilizada a dedução de dependente e por tanto não deve ser inserida novamente na soma de base.
        nuValor := nuValor + processarValorDeducaoDependente();
                                     
        --Evento Informativo de isenção (inativos/pensionistas) acima de 65 anos
        --Busca que identifica se o servidor é um servidor com mais de 65 anos
        --Busca código do Evento Informativo de isenção (inativos/pensionistas) acima de 65 anos
        inCodEvento65Anos := selectIntoInteger('SELECT tabela_irrf_evento.cod_evento
                                                   FROM folhapagamento'||stEntidade||'.tabela_irrf_evento
                                                  WHERE tabela_irrf_evento.cod_tipo = 2
                                               ORDER BY timestamp desc LIMIT 1');   
        IF inCodEvento65Anos is not null THEN
            IF stSituacaoComplementar = 'f' THEN
                --Verifica se exise algum contrato calculado em folha complementar
                --que possua o evento de pensionista/inativo acima de 65 anos
                stSql := 'SELECT count(1) as contador
                            FROM folhapagamento'||stEntidade||'.registro_evento_complementar
                               , pessoal'||stEntidade||'.servidor_contrato_servidor
                               , pessoal'||stEntidade||'.servidor
                           WHERE registro_evento_complementar.cod_contrato = servidor_contrato_servidor.cod_contrato
                             AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                             AND registro_evento_complementar.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                             AND EXISTS (SELECT 1
                                           FROM folhapagamento'||stEntidade||'.evento_complementar_calculado
                                          WHERE registro_evento_complementar.cod_registro = evento_complementar_calculado.cod_registro
                                            AND registro_evento_complementar.cod_evento = evento_complementar_calculado.cod_evento
                                            AND registro_evento_complementar.cod_configuracao = evento_complementar_calculado.cod_configuracao
                                            AND registro_evento_complementar.timestamp = evento_complementar_calculado.timestamp_registro
                                            AND evento_complementar_calculado.cod_evento = '||inCodEvento65Anos||')
                             AND servidor.numcgm = '||inNumCgm;                        
                inCountTemp := selectIntoInteger(stSql);
                IF inCountTemp is not null THEN
                    inNumContratos := inNumContratos + inCountTemp;
                END IF;
            END IF;   
            --Verifica se exise algum contrato calculado em folha salárop
            --que possua o evento de pensionista/inativo acima de 65 anos
            stSql := 'SELECT count(1) as contador
                        FROM folhapagamento'||stEntidade||'.registro_evento_periodo
                           , pessoal'||stEntidade||'.servidor_contrato_servidor
                           , pessoal'||stEntidade||'.servidor
                       WHERE registro_evento_periodo.cod_contrato = servidor_contrato_servidor.cod_contrato
                         AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                         AND registro_evento_periodo.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                         AND EXISTS (SELECT 1
                                       FROM folhapagamento'||stEntidade||'.evento_calculado
                                      WHERE registro_evento_periodo.cod_registro = evento_calculado.cod_registro
                                        AND evento_calculado.cod_evento = '||inCodEvento65Anos||')
                         AND servidor.numcgm = '||inNumCgm;
            inCountTemp := selectIntoInteger(stSql);
            IF inCountTemp is not null THEN
                inNumContratos := inNumContratos + inCountTemp;
            END IF; 
            --Verifica se exise algum contrato calculado em folha férias
            --que possua o evento de pensionista/inativo acima de 65 anos
            stSql := 'SELECT count(1) as contador
                        FROM folhapagamento'||stEntidade||'.registro_evento_ferias
                           , pessoal'||stEntidade||'.servidor_contrato_servidor
                           , pessoal'||stEntidade||'.servidor
                       WHERE registro_evento_ferias.cod_contrato = servidor_contrato_servidor.cod_contrato
                         AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                         AND registro_evento_ferias.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                         AND EXISTS (SELECT 1
                                       FROM folhapagamento'||stEntidade||'.evento_ferias_calculado
                                      WHERE registro_evento_ferias.cod_registro = evento_ferias_calculado.cod_registro
                                        AND registro_evento_ferias.cod_evento = evento_ferias_calculado.cod_evento
                                        AND registro_evento_ferias.desdobramento = evento_ferias_calculado.desdobramento
                                        AND registro_evento_ferias.timestamp = evento_ferias_calculado.timestamp_registro
                                        AND evento_ferias_calculado.cod_evento = '||inCodEvento65Anos||')
                         AND servidor.numcgm = '||inNumCgm;                                                                               
            inCountTemp := selectIntoInteger(stSql);
            IF inCountTemp is not null THEN
                inNumContratos := inNumContratos + inCountTemp;
            END IF;   
            --Verifica se exise algum contrato calculado em folha rescisão
            --que possua o evento de pensionista/inativo acima de 65 anos
            stSql := 'SELECT count(1) as contador
                        FROM folhapagamento'||stEntidade||'.registro_evento_rescisao
                           , pessoal'||stEntidade||'.servidor_contrato_servidor
                           , pessoal'||stEntidade||'.servidor
                       WHERE registro_evento_rescisao.cod_contrato = servidor_contrato_servidor.cod_contrato
                         AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                         AND registro_evento_rescisao.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                         AND EXISTS (SELECT 1
                                       FROM folhapagamento'||stEntidade||'.evento_rescisao_calculado
                                      WHERE registro_evento_rescisao.cod_registro = evento_rescisao_calculado.cod_registro
                                        AND registro_evento_rescisao.cod_evento = evento_rescisao_calculado.cod_evento
                                        AND registro_evento_rescisao.desdobramento = evento_rescisao_calculado.desdobramento
                                        AND registro_evento_rescisao.timestamp = evento_rescisao_calculado.timestamp_registro
                                        AND registro_evento_rescisao.desdobramento = '|| quote_literal('S')||'
                                        AND evento_rescisao_calculado.cod_evento = '||inCodEvento65Anos||')
                         AND servidor.numcgm = '||inNumCgm;                                                                                                   
                    
            inCountTemp := selectIntoInteger(stSql);
            IF inCountTemp is not null THEN
                inNumContratos := inNumContratos + inCountTemp;
            END IF;                                                     
            IF inNumContratos = 1 THEN
                --Busca valor calculado dos eventos da folha salário
                stSql := 'SELECT COALESCE(sum(evento_calculado.valor),0.00) as valor
                            FROM folhapagamento'||stEntidade||'.registro_evento_periodo
                               , folhapagamento'||stEntidade||'.evento_calculado
                           WHERE registro_evento_periodo.cod_registro = evento_calculado.cod_registro
                             AND registro_evento_periodo.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                             AND evento_calculado.cod_evento = '||inCodEvento65Anos||'                                          
                             AND registro_evento_periodo.cod_contrato = '||inCodContrato;
                nuValorTemp := selectIntoNumeric(stSql);
                IF nuValorTemp is not null THEN
                    nuValor := nuValor + nuValorTemp;
                END IF;
            END IF;  
        END IF;
        --Busca o valor calculado do evento de pensão
        --Evento de Desconto de Pensão Alimentícia
        stSql := 'SELECT evento_calculado.valor
                    FROM folhapagamento'||stEntidade||'.pensao_evento
                       , (  SELECT cod_configuracao_pensao
                                   , max(timestamp) as timestamp
                               FROM folhapagamento'||stEntidade||'.pensao_funcao_padrao
                           GROUP BY cod_configuracao_pensao) as max_pensao_funcao_padrao
                       , folhapagamento'||stEntidade||'.evento
                       , folhapagamento'||stEntidade||'.registro_evento_periodo
                       , folhapagamento'||stEntidade||'.ultimo_registro_evento
                       , folhapagamento'||stEntidade||'.evento_calculado
                   WHERE pensao_evento.cod_tipo = 1
                      AND registro_evento_periodo.cod_contrato = '||inCodContrato||'
                      AND registro_evento_periodo.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                      AND pensao_evento.cod_configuracao_pensao = max_pensao_funcao_padrao.cod_configuracao_pensao
                      AND pensao_evento.timestamp       = max_pensao_funcao_padrao.timestamp
                      AND pensao_evento.cod_evento = evento.cod_evento
                      AND evento.cod_evento = ultimo_registro_evento.cod_evento
                      AND ultimo_registro_evento.cod_registro = registro_evento_periodo.cod_registro
                      AND ultimo_registro_evento.cod_evento = evento_calculado.cod_evento
                      AND ultimo_registro_evento.cod_registro = evento_calculado.cod_registro
                      AND ultimo_registro_evento.timestamp = evento_calculado.timestamp_registro';
        nuValorPensao := selectIntoNumeric(stSql);
        IF nuValorPensao IS NULL THEN
            nuValorPensao := 0.00;
        END IF;                
        
        
        ---------------------AJUSTE COM DESCONTOS EXTERNOS----------------------           
        ---Assim como no dependente, o desconto externo só pode ser utilizado uma vez
        ---por isso é verificado se o nuValorDescontoExternoAnterior FOR igual a 0 ou null o desconto externo não foi utilizado
        IF stSituacaoComplementar = 'f' THEN
            stSql := 'SELECT evento_complementar_calculado.valor
                        FROM folhapagamento'||stEntidade||'.registro_evento_complementar
                           , folhapagamento'||stEntidade||'.ultimo_registro_evento_complementar 
                           , folhapagamento'||stEntidade||'.evento_complementar_calculado
                           , pessoal'||stEntidade||'.servidor_contrato_servidor
                           , pessoal'||stEntidade||'.servidor
                           , folhapagamento'||stEntidade||'.configuracao_eventos_desconto_externo
                       WHERE ultimo_registro_evento_complementar.cod_registro = evento_complementar_calculado.cod_registro
                         AND ultimo_registro_evento_complementar.cod_evento   = evento_complementar_calculado.cod_evento
                         AND ultimo_registro_evento_complementar.cod_configuracao = evento_complementar_calculado.cod_configuracao
                         AND ultimo_registro_evento_complementar.timestamp    = evento_complementar_calculado.timestamp_registro
                         AND ultimo_registro_evento_complementar.cod_registro     = registro_evento_complementar.cod_registro
                         AND ultimo_registro_evento_complementar.cod_evento       = registro_evento_complementar.cod_evento
                         AND ultimo_registro_evento_complementar.cod_configuracao = registro_evento_complementar.cod_configuracao
                         AND ultimo_registro_evento_complementar.timestamp        = registro_evento_complementar.timestamp
                         AND registro_evento_complementar.cod_contrato = servidor_contrato_servidor.cod_contrato
                         AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                         AND evento_complementar_calculado.cod_evento = configuracao_eventos_desconto_externo.evento_desconto_previdencia
                         AND servidor.numcgm = '||inNumCgm||'
                         AND registro_evento_complementar.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao;
            nuValorDescontoExternoAnterior := selectIntoNumeric(stSql);
        END IF;
        
        IF nuValorDescontoExternoAnterior IS NULL or nuValorDescontoExternoAnterior = 0 THEN 
            stSql := ' SELECT desconto_externo_previdencia_valor.valor_previdencia
                         FROM folhapagamento'||stEntidade||'.desconto_externo_previdencia                                    
                    LEFT JOIN (SELECT desconto_externo_previdencia_valor.*
                                 FROM folhapagamento'||stEntidade||'.desconto_externo_previdencia_valor
                                    , (   SELECT cod_contrato
                                               , max(timestamp_valor) as timestamp_valor
                                            FROM folhapagamento'||stEntidade||'.desconto_externo_previdencia_valor
                                        GROUP BY cod_contrato) as max_desconto_externo_previdencia_valor
                                WHERE desconto_externo_previdencia_valor.cod_contrato = max_desconto_externo_previdencia_valor.cod_contrato
                                  AND desconto_externo_previdencia_valor.timestamp_valor = max_desconto_externo_previdencia_valor.timestamp_valor) AS desconto_externo_previdencia_valor
                           ON desconto_externo_previdencia_valor.cod_contrato = desconto_externo_previdencia.cod_contrato
                          AND desconto_externo_previdencia_valor.timestamp = desconto_externo_previdencia_valor.timestamp      
                            , (  SELECT cod_contrato
                                      , max(timestamp) as timestamp
                                   FROM folhapagamento'||stEntidade||'.desconto_externo_previdencia
                                  WHERE vigencia <= '||quote_literal(pega0DataFinalCompetenciaDoPeriodoMovimento(inCodPeriodoMovimentacao))||'
                               GROUP BY cod_contrato) as max_desconto_externo_previdencia
                        WHERE desconto_externo_previdencia.cod_contrato = max_desconto_externo_previdencia.cod_contrato
                          AND desconto_externo_previdencia.timestamp = max_desconto_externo_previdencia.timestamp
                          AND NOT EXISTS (SELECT 1
                                            FROM folhapagamento'||stEntidade||'.desconto_externo_previdencia_anulado
                                           WHERE desconto_externo_previdencia.cod_contrato = desconto_externo_previdencia_anulado.cod_contrato
                                             AND desconto_externo_previdencia.timestamp = desconto_externo_previdencia_anulado.timestamp)
                          AND desconto_externo_previdencia.cod_contrato = '||inCodContrato;
            
            nuValorDescontoExternoAnterior :=  selectIntoNumeric(stSql);            
            IF nuValorDescontoExternoAnterior IS NOT NULL THEN
                nuValor := nuValor + nuValorDescontoExternoAnterior;
            END IF;
        END IF;
        ---------------------AJUSTE COM DESCONTOS EXTERNOS----------------------              
            
        --Busca dados para atualizar os valores nos eventos
        --Evento de Base de Dedução sem Pensão Alimentícia
        --Evento de Base de Dedução com Pensão Alimentícia
        --Base Dedução IRRF s/Pensão
        stSqlSemPensao := 'SELECT evento_calculado.cod_evento
                                , evento_calculado.cod_registro
                                , evento_calculado.timestamp_registro
                            FROM folhapagamento'||stEntidade||'.tabela_irrf_evento
                                , (  SELECT cod_tabela
                                        , max(timestamp) as timestamp
                                        FROM folhapagamento'||stEntidade||'.tabela_irrf_evento
                                    GROUP BY cod_tabela) as max_tabela_irrf_evento
                                , folhapagamento'||stEntidade||'.evento
                                , folhapagamento'||stEntidade||'.registro_evento_periodo
                                , folhapagamento'||stEntidade||'.ultimo_registro_evento
                                , folhapagamento'||stEntidade||'.evento_calculado
                            WHERE tabela_irrf_evento.cod_tipo = 4
                            AND registro_evento_periodo.cod_contrato = '||inCodContrato||'
                            AND registro_evento_periodo.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                            AND tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela
                            AND tabela_irrf_evento.timestamp  = max_tabela_irrf_evento.timestamp
                            AND tabela_irrf_evento.cod_evento = evento.cod_evento
                            AND evento.cod_evento = ultimo_registro_evento.cod_evento
                            AND ultimo_registro_evento.cod_registro = registro_evento_periodo.cod_registro
                            AND ultimo_registro_evento.cod_evento = evento_calculado.cod_evento
                            AND ultimo_registro_evento.cod_registro = evento_calculado.cod_registro
                            AND ultimo_registro_evento.timestamp = evento_calculado.timestamp_registro';
        --Base Dedução IRRF c/Pensão
        stSqlComPensao := 'SELECT evento_calculado.cod_evento
                                , evento_calculado.cod_registro
                                , evento_calculado.timestamp_registro
                            FROM folhapagamento'||stEntidade||'.tabela_irrf_evento
                                , (  SELECT cod_tabela
                                        , max(timestamp) as timestamp
                                        FROM folhapagamento'||stEntidade||'.tabela_irrf_evento
                                    GROUP BY cod_tabela) as max_tabela_irrf_evento
                                , folhapagamento'||stEntidade||'.evento
                                , folhapagamento'||stEntidade||'.registro_evento_periodo
                                , folhapagamento'||stEntidade||'.ultimo_registro_evento
                                , folhapagamento'||stEntidade||'.evento_calculado
                            WHERE tabela_irrf_evento.cod_tipo = 5
                            AND registro_evento_periodo.cod_contrato = '||inCodContrato||'
                            AND registro_evento_periodo.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                            AND tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela
                            AND tabela_irrf_evento.timestamp  = max_tabela_irrf_evento.timestamp
                            AND tabela_irrf_evento.cod_evento = evento.cod_evento
                            AND evento.cod_evento = ultimo_registro_evento.cod_evento
                            AND ultimo_registro_evento.cod_registro = registro_evento_periodo.cod_registro
                            AND ultimo_registro_evento.cod_evento = evento_calculado.cod_evento
                            AND ultimo_registro_evento.cod_registro = evento_calculado.cod_registro
                            AND ultimo_registro_evento.timestamp = evento_calculado.timestamp_registro';
    
        --Atualiza Base Dedução IRRF s/Pensão
        OPEN crCursor FOR EXECUTE stSqlSemPensao;
            FETCH crCursor INTO inCodEvento,inCodRegistro,stTimestampRegistro;
        CLOSE crCursor;
        IF inCodEvento IS NOT NULL THEN
            inCodEvento         := criarBufferInteiro('inCodEventoSPensao',inCodEvento);
            inCodRegistro       := criarBufferInteiro('inCodRegistroSPensao',inCodRegistro);
            stTimestampRegistro := criarBufferTexto('stTimestampRegistroSPensao',stTimestampRegistro);
        ELSE
            inCodEvento         := criarBufferInteiro('inCodEventoSPensao',0);
            inCodRegistro       := criarBufferInteiro('inCodRegistroSPensao',0);
            stTimestampRegistro := criarBufferTexto('stTimestampRegistroSPensao','');
        END IF;
        IF inCodEvento != 0 THEN
            stSql := 'UPDATE folhapagamento'||stEntidade||'.evento_calculado SET valor = '||nuValor||'
            WHERE cod_evento       = '||inCodEvento||'
            AND cod_registro       = '||inCodRegistro||'
            AND timestamp_registro = '||quote_literal(stTimestampRegistro)||'';
            EXECUTE stSql;
        END IF;
    
        --Atualiza Base Dedução IRRF c/Pensão
        OPEN crCursor FOR EXECUTE stSqlComPensao;
            FETCH crCursor INTO inCodEvento,inCodRegistro,stTimestampRegistro;
        CLOSE crCursor;
        IF inCodEvento IS NOT NULL THEN
            inCodEvento         := criarBufferInteiro('inCodEventoCPensao',inCodEvento);
            inCodRegistro       := criarBufferInteiro('inCodRegistroCPensao',inCodRegistro);
            stTimestampRegistro := criarBufferTexto('stTimestampRegistroCPensao',stTimestampRegistro);
        ELSE
            inCodEvento         := criarBufferInteiro('inCodEventoCPensao',0);
            inCodRegistro       := criarBufferInteiro('inCodRegistroCPensao',0);
            stTimestampRegistro := criarBufferTexto('stTimestampRegistroCPensao','');
        END IF;
        IF inCodEvento != 0 THEN
            stSql := 'UPDATE folhapagamento'||stEntidade||'.evento_calculado SET valor = ('||nuValor+nuValorPensao||')
            WHERE cod_evento       = '||inCodEvento||'
            AND cod_registro       = '||inCodRegistro||'
            AND timestamp_registro = '||quote_literal(stTimestampRegistro)||' ';
            EXECUTE stSql;
        END IF;                    
    END IF;
    --If para verificar a existencia de um buffer para o cod_previdencia
    RETURN boRetorno; 
END;
$$ LANGUAGE 'plpgsql';

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
* Data de Criação: 12/04/2007


* @author Analista: Dagiane
* @author Desenvolvedor: Diego Lemos de Souza

* @package URBEM
* @subpackage

$Revision: 27124 $
$Name$
$Author: souzadl $
$Date: 2007-12-12 14:35:58 -0200 (Qua, 12 Dez 2007) $

* Casos de uso: uc-04.05.18
*/

CREATE OR REPLACE FUNCTION processarAjustePrevidenciaRescisao() RETURNS BOOLEAN as $$
DECLARE
    stSql                            VARCHAR := '';
    reRegistro                       RECORD;
    reBases                          RECORD;
    reFaixaDesconto                  RECORD;
    reDescontoExterno                RECORD;
    boRetorno                        BOOLEAN := TRUE;
    boAjustar                        BOOLEAN := FALSE;
    stCodigoEvento                   VARCHAR := '';
    stNatureza                       VARCHAR := '';
    dtVigencia                       VARCHAR := '';
    stTimestamp                      VARCHAR := '';
    stSituacaoFolhaSalario           VARCHAR := '';
    arDesdobramento                  VARCHAR[];
    stTimestampDesconto              TIMESTAMP;
    stTimestampDescontoDecimo        TIMESTAMP;
    stTimestampDescontoSaldoAviso    TIMESTAMP;
    inCodContrato                    INTEGER;
    inCodEvento                      INTEGER;
    inCodPeriodoMovimentacao         INTEGER;
    inCodPrevidencia                 INTEGER;
    inCodComplementar                INTEGER;
    inNumCgm                         INTEGER;
    inCodRegistroDesconto            INTEGER;
    inCodRegistroDescontoDecimo      INTEGER;
    inCodRegistroDescontoSaldoAviso  INTEGER;
    inCodEventoDesconto              INTEGER;
    inCodEventoDescontoDecimo        INTEGER;
    inCodEventoDescontoSaldoAviso    INTEGER;
    inCountFolhaRescisao             INTEGER;
    inCountFolhaComplementar         INTEGER;
    inCountFolhaDecimo               INTEGER;
    inCountFolhaSalario              INTEGER;
    inCountFolhaFerias               INTEGER;
    nuTotalDescontoCalculo           NUMERIC := 0.00;
    nuPercentualDesconto             NUMERIC := 0.00;
    nuSomaBase                       NUMERIC := 0.00;
    nuSomaBaseDecimo                 NUMERIC := 0.00;
    nuSomaBaseSaldoAviso             NUMERIC := 0.00;
    nuSomaDesconto                   NUMERIC := 0.00;
    nuSomaDescontoDecimo             NUMERIC := 0.00;
    nuSomaDescontoSaldoAviso         NUMERIC := 0.00;
    nuSomaDescontoExterno            NUMERIC := 0.00;
    nuBaseRescisaoDesdobramentoSaldoAviso   NUMERIC := 0.00;
    nuBaseRescisaoDesdobramentoDecimo       NUMERIC := 0.00;
    crCursor                         REFCURSOR;
    stEntidade                       VARCHAR;        
BEGIN
    stEntidade       := recuperarBufferTexto('stEntidade');
    inCodPrevidencia := recuperarBufferInteiro('inCodPrevidenciaOficial');
    dtVigencia       := recuperarBufferTexto('dtVigenciaPrevidencia');
    IF inCodPrevidencia > 0 THEN        
        stTimestamp                 := pega1TimestampTabelaPrevidencia();
        inNumCgm                    := recuperarBufferInteiro('inNumCgm');
        inCodContrato               := recuperarBufferInteiro('inCodContrato');
        inCodPeriodoMovimentacao    := recuperarBufferInteiro('inCodPeriodoMovimentacao');
        inCountFolhaRescisao := selectIntoInteger('SELECT count(*) as contador
                                        FROM (SELECT registro_evento_rescisao.cod_contrato
                                                FROM folhapagamento'|| stEntidade ||'.registro_evento_rescisao
                                                    , folhapagamento'|| stEntidade ||'.ultimo_registro_evento_rescisao
                                                    , folhapagamento'|| stEntidade ||'.evento_rescisao_calculado
                                                    , folhapagamento'|| stEntidade ||'.periodo_movimentacao
                                                    , pessoal'|| stEntidade ||'.servidor_contrato_servidor
                                                    , pessoal'|| stEntidade ||'.servidor
                                                WHERE registro_evento_rescisao.cod_registro     = ultimo_registro_evento_rescisao.cod_registro
                                                AND registro_evento_rescisao.timestamp        = ultimo_registro_evento_rescisao.timestamp
                                                AND registro_evento_rescisao.cod_evento       = ultimo_registro_evento_rescisao.cod_evento
                                                AND registro_evento_rescisao.desdobramento    = ultimo_registro_evento_rescisao.desdobramento
                                                AND registro_evento_rescisao.cod_registro     = evento_rescisao_calculado.cod_registro
                                                AND registro_evento_rescisao.timestamp        = evento_rescisao_calculado.timestamp_registro
                                                AND registro_evento_rescisao.cod_evento       = evento_rescisao_calculado.cod_evento
                                                AND registro_evento_rescisao.desdobramento    = evento_rescisao_calculado.desdobramento
                                                AND registro_evento_rescisao.cod_contrato = servidor_contrato_servidor.cod_contrato
                                                AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                                                AND registro_evento_rescisao.cod_periodo_movimentacao = periodo_movimentacao.cod_periodo_movimentacao
                                                AND servidor.numcgm = '|| inNumCgm ||'
                                                AND registro_evento_rescisao.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                                            GROUP BY registro_evento_rescisao.cod_contrato) as rescisao');
        inCountFolhaComplementar := selectIntoInteger('SELECT count(*)
                                                FROM (SELECT registro_evento_complementar.cod_contrato
                                                        , registro_evento_complementar.cod_complementar
                                                        FROM folhapagamento'|| stEntidade ||'.registro_evento_complementar
                                                        , folhapagamento'|| stEntidade ||'.ultimo_registro_evento_complementar
                                                        , folhapagamento'|| stEntidade ||'.evento_complementar_calculado
                                                        , pessoal'|| stEntidade ||'.servidor_contrato_servidor
                                                        , pessoal'|| stEntidade ||'.servidor
                                                        , folhapagamento'|| stEntidade ||'.complementar_situacao
                                                        , (SELECT cod_periodo_movimentacao
                                                                , cod_complementar
                                                                ,  max(timestamp) as timestamp
                                                                FROM folhapagamento'|| stEntidade ||'.complementar_situacao
                                                            GROUP BY cod_periodo_movimentacao
                                                                    , cod_complementar) as max_complementar_situacao
                                                    WHERE registro_evento_complementar.cod_registro     = ultimo_registro_evento_complementar.cod_registro
                                                        AND registro_evento_complementar.timestamp        = ultimo_registro_evento_complementar.timestamp
                                                        AND registro_evento_complementar.cod_evento       = ultimo_registro_evento_complementar.cod_evento
                                                        AND registro_evento_complementar.cod_configuracao = ultimo_registro_evento_complementar.cod_configuracao
                                                        AND registro_evento_complementar.cod_registro     = evento_complementar_calculado.cod_registro
                                                        AND registro_evento_complementar.timestamp        = evento_complementar_calculado.timestamp_registro
                                                        AND registro_evento_complementar.cod_evento       = evento_complementar_calculado.cod_evento
                                                        AND registro_evento_complementar.cod_configuracao = evento_complementar_calculado.cod_configuracao
                                                        AND registro_evento_complementar.cod_contrato = servidor_contrato_servidor.cod_contrato
                                                        AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                                                        AND registro_evento_complementar.cod_complementar = complementar_situacao.cod_complementar
                                                        AND registro_evento_complementar.cod_periodo_movimentacao = complementar_situacao.cod_periodo_movimentacao
                                                        AND complementar_situacao.cod_complementar = max_complementar_situacao.cod_complementar
                                                        AND complementar_situacao.cod_periodo_movimentacao = max_complementar_situacao.cod_periodo_movimentacao
                                                        AND complementar_situacao.timestamp = max_complementar_situacao.timestamp
                                                        AND complementar_situacao.situacao = ''f''
                                                        AND servidor.numcgm = '|| inNumCgm ||'
                                                        AND registro_evento_complementar.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                                                    GROUP BY registro_evento_complementar.cod_contrato
                                                        , registro_evento_complementar.cod_complementar) as complementar');
        inCountFolhaDecimo := selectIntoInteger('SELECT count(*) as contador
                                        FROM (SELECT registro_evento_decimo.cod_contrato
                                                FROM folhapagamento'|| stEntidade ||'.registro_evento_decimo
                                                    , folhapagamento'|| stEntidade ||'.ultimo_registro_evento_decimo
                                                    , folhapagamento'|| stEntidade ||'.evento_decimo_calculado
                                                    , folhapagamento'|| stEntidade ||'.periodo_movimentacao
                                                    , pessoal'|| stEntidade ||'.servidor_contrato_servidor
                                                    , pessoal'|| stEntidade ||'.servidor
                                                WHERE registro_evento_decimo.cod_registro     = ultimo_registro_evento_decimo.cod_registro
                                                AND registro_evento_decimo.timestamp        = ultimo_registro_evento_decimo.timestamp
                                                AND registro_evento_decimo.cod_evento       = ultimo_registro_evento_decimo.cod_evento
                                                AND registro_evento_decimo.desdobramento    = ultimo_registro_evento_decimo.desdobramento
                                                AND registro_evento_decimo.cod_registro     = evento_decimo_calculado.cod_registro
                                                AND registro_evento_decimo.timestamp        = evento_decimo_calculado.timestamp_registro
                                                AND registro_evento_decimo.cod_evento       = evento_decimo_calculado.cod_evento
                                                AND registro_evento_decimo.desdobramento    = evento_decimo_calculado.desdobramento
                                                AND registro_evento_decimo.cod_contrato = servidor_contrato_servidor.cod_contrato
                                                AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                                                AND registro_evento_decimo.cod_periodo_movimentacao = periodo_movimentacao.cod_periodo_movimentacao
                                                AND servidor.numcgm = '|| inNumCgm ||'
                                                AND registro_evento_decimo.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                                            GROUP BY registro_evento_decimo.cod_contrato) as decimo');
        inCountFolhaSalario := selectIntoInteger('SELECT count(*) as contador
                                        FROM (SELECT registro_evento_periodo.cod_contrato
                                                FROM folhapagamento'|| stEntidade ||'.evento_calculado
                                                    , folhapagamento'|| stEntidade ||'.registro_evento
                                                    , folhapagamento'|| stEntidade ||'.registro_evento_periodo
                                                    , folhapagamento'|| stEntidade ||'.folha_situacao
                                                    , (  SELECT cod_periodo_movimentacao
                                                                , max(timestamp) as timestamp
                                                            FROM folhapagamento'|| stEntidade ||'.folha_situacao
                                                        GROUP BY cod_periodo_movimentacao) as max_folha_situacao
                                                    , pessoal'|| stEntidade ||'.servidor_contrato_servidor
                                                    , pessoal'|| stEntidade ||'.servidor
                                                WHERE evento_calculado.cod_evento = registro_evento.cod_evento
                                                    AND evento_calculado.cod_registro = registro_evento.cod_registro
                                                    AND evento_calculado.timestamp_registro = registro_evento.timestamp
                                                    AND registro_evento.cod_registro = registro_evento_periodo.cod_registro
                                                    AND registro_evento_periodo.cod_contrato = servidor_contrato_servidor.cod_contrato
                                                    AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                                                    AND registro_evento_periodo.cod_periodo_movimentacao = folha_situacao.cod_periodo_movimentacao
                                                    AND folha_situacao.cod_periodo_movimentacao = max_folha_situacao.cod_periodo_movimentacao
                                                    AND folha_situacao.timestamp = max_folha_situacao.timestamp
                                                    AND folha_situacao.situacao = ''f''
                                                    AND servidor.numcgm = '|| inNumCgm ||'
                                                    AND registro_evento_periodo.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                                                GROUP BY registro_evento_periodo.cod_contrato) as salario');
        inCountFolhaFerias := selectIntoInteger('SELECT COUNT(*) AS contador
                                        FROM (SELECT registro_evento_ferias.cod_contrato
                                                FROM folhapagamento'|| stEntidade ||'.registro_evento_ferias
                                                    , folhapagamento'|| stEntidade ||'.ultimo_registro_evento_ferias
                                                    , folhapagamento'|| stEntidade ||'.evento_ferias_calculado
                                                    , folhapagamento'|| stEntidade ||'.periodo_movimentacao
                                                    , pessoal'|| stEntidade ||'.servidor_contrato_servidor
                                                    , pessoal'|| stEntidade ||'.servidor
                                                    , pessoal'|| stEntidade ||'.ferias
                                                    , pessoal'|| stEntidade ||'.lancamento_ferias
                                                WHERE registro_evento_ferias.cod_registro     = ultimo_registro_evento_ferias.cod_registro
                                                AND registro_evento_ferias.timestamp        = ultimo_registro_evento_ferias.timestamp
                                                AND registro_evento_ferias.cod_evento       = ultimo_registro_evento_ferias.cod_evento
                                                AND registro_evento_ferias.desdobramento    = ultimo_registro_evento_ferias.desdobramento
                                                AND registro_evento_ferias.cod_registro     = evento_ferias_calculado.cod_registro
                                                AND registro_evento_ferias.timestamp        = evento_ferias_calculado.timestamp_registro
                                                AND registro_evento_ferias.cod_evento       = evento_ferias_calculado.cod_evento
                                                AND registro_evento_ferias.desdobramento    = evento_ferias_calculado.desdobramento
                                                AND registro_evento_ferias.cod_contrato = servidor_contrato_servidor.cod_contrato
                                                AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                                                AND registro_evento_ferias.cod_periodo_movimentacao = periodo_movimentacao.cod_periodo_movimentacao
                                                AND registro_evento_ferias.cod_contrato = ferias.cod_contrato
                                                AND ferias.cod_ferias = lancamento_ferias.cod_ferias
                                                AND lancamento_ferias.cod_tipo = 1
                                                AND servidor.numcgm = '|| inNumCgm ||'
                                                AND registro_evento_ferias.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                                            GROUP BY registro_evento_ferias.cod_contrato) AS ferias');

        IF inCountFolhaRescisao >= 1 THEN
            --Código para ajustes da previdência
                    
            --BUSCA CÓDIGO E TIMESTAMP DA PREVIDENCIA
            --PARA BUSCAR OS EVENTOS VINCULADOS A ESSA PREVIDENCIA
            stSql := 'SELECT *
                        FROM folhapagamento'|| stEntidade ||'.tipo_evento_previdencia';
            FOR reRegistro IN EXECUTE stSql
            LOOP
                --Consulta que busca os eventos da previdencia
                stSql := 'SELECT evento.cod_evento
                                , evento.natureza
                            FROM folhapagamento'|| stEntidade ||'.previdencia_evento 
                                , folhapagamento'|| stEntidade ||'.evento
                            WHERE cod_tipo = '|| reRegistro.cod_tipo ||'
                            AND cod_previdencia = '|| inCodPrevidencia ||'
                            AND timestamp       = '|| quote_literal(stTimestamp) ||'
                            AND previdencia_evento.cod_evento = evento.cod_evento';
                OPEN crCursor FOR EXECUTE stSql;
                    FETCH crCursor INTO inCodEvento,stNatureza;
                CLOSE crCursor;
              
                IF inCountFolhaRescisao >= 1 AND NOT (inCountFolhaRescisao = 1 AND inCountFolhaComplementar = 0 AND inCountFolhaDecimo = 0 AND inCountFolhaSalario = 0 AND inCountFolhaFerias = 0) THEN
                    inCodComplementar           := pega0UltimaComplementar('f',inCodPeriodoMovimentacao);                
                    --Loop para buscar os demais valores das outras complementar maiores que 1
                    
                    FOR inIndex IN 1 .. inCodComplementar
                    LOOP
                        stSql := 'SELECT evento_complementar_calculado.valor
                                        , registro_evento_complementar.cod_registro
                                        , registro_evento_complementar.cod_configuracao
                                        , registro_evento_complementar.timestamp
                                        , registro_evento_complementar.cod_complementar
                                        , registro_evento_complementar.cod_contrato
                                        , registro_evento_complementar.cod_evento
                                    FROM folhapagamento'|| stEntidade ||'.registro_evento_complementar                                         
                                        , folhapagamento'|| stEntidade ||'.ultimo_registro_evento_complementar 
                                        , folhapagamento'|| stEntidade ||'.evento_complementar_calculado
                                        , pessoal'|| stEntidade ||'.servidor_contrato_servidor
                                        , pessoal'|| stEntidade ||'.servidor
                                        , folhapagamento'|| stEntidade ||'.complementar_situacao
                                        , (SELECT cod_periodo_movimentacao
                                                , cod_complementar
                                                ,  max(timestamp) as timestamp
                                            FROM folhapagamento'|| stEntidade ||'.complementar_situacao
                                        GROUP BY cod_periodo_movimentacao
                                                , cod_complementar) as max_complementar_situacao
                                    WHERE registro_evento_complementar.cod_evento       = '|| inCodEvento ||'
                                    AND registro_evento_complementar.cod_complementar = '|| inIndex ||'
                                    AND registro_evento_complementar.cod_configuracao != 3
                                    AND registro_evento_complementar.cod_registro     = ultimo_registro_evento_complementar.cod_registro
                                    AND registro_evento_complementar.timestamp        = ultimo_registro_evento_complementar.timestamp
                                    AND registro_evento_complementar.cod_evento       = ultimo_registro_evento_complementar.cod_evento
                                    AND registro_evento_complementar.cod_configuracao = ultimo_registro_evento_complementar.cod_configuracao
                                    AND registro_evento_complementar.cod_registro     = evento_complementar_calculado.cod_registro
                                    AND registro_evento_complementar.timestamp        = evento_complementar_calculado.timestamp_registro
                                    AND registro_evento_complementar.cod_evento       = evento_complementar_calculado.cod_evento
                                    AND registro_evento_complementar.cod_configuracao = evento_complementar_calculado.cod_configuracao
                                    AND registro_evento_complementar.cod_complementar = complementar_situacao.cod_complementar
                                    AND registro_evento_complementar.cod_periodo_movimentacao = complementar_situacao.cod_periodo_movimentacao
                                    AND complementar_situacao.cod_complementar = max_complementar_situacao.cod_complementar
                                    AND complementar_situacao.cod_periodo_movimentacao = max_complementar_situacao.cod_periodo_movimentacao
                                    AND complementar_situacao.timestamp = max_complementar_situacao.timestamp
                                    AND complementar_situacao.situacao = ''f''
                                    AND registro_evento_complementar.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                                    AND registro_evento_complementar.cod_contrato = servidor_contrato_servidor.cod_contrato
                                    AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                                    AND servidor.numcgm = '|| inNumCgm ||' ';
                        FOR reBases IN EXECUTE stSql
                        LOOP
                            IF stNatureza = 'B' THEN                        
                                IF reBases.cod_configuracao = 1 THEN
                                    nuSomaBaseSaldoAviso := nuSomaBaseSaldoAviso + reBases.valor;
                                END IF;
                                IF reBases.cod_configuracao = 3 THEN
                                    nuSomaBaseDecimo := nuSomaBaseDecimo + reBases.valor;
                                END IF;
                            END IF;
                            IF stNatureza = 'D' THEN
                                IF reBases.cod_configuracao = 1 THEN
                                    nuSomaDescontoSaldoAviso := nuSomaDescontoSaldoAviso + reBases.valor;
                                END IF;
                                IF reBases.cod_configuracao = 3 THEN
                                    nuSomaDescontoDecimo := nuSomaDescontoDecimo + reBases.valor;
                                END IF;
                            END IF;
                        END LOOP;
                    END LOOP;

                    -------------------INÍCIO DO AJUSTE COM O DÉCIMO--------------------
                    stSql := 'SELECT evento_decimo_calculado.valor
                                FROM folhapagamento'|| stEntidade ||'.registro_evento_decimo
                                    , folhapagamento'|| stEntidade ||'.ultimo_registro_evento_decimo
                                    , folhapagamento'|| stEntidade ||'.evento_decimo_calculado
                                    , folhapagamento'|| stEntidade ||'.periodo_movimentacao
                                    , pessoal'|| stEntidade ||'.servidor_contrato_servidor
                                    , pessoal'|| stEntidade ||'.servidor
                                WHERE registro_evento_decimo.cod_registro     = ultimo_registro_evento_decimo.cod_registro
                                AND registro_evento_decimo.timestamp        = ultimo_registro_evento_decimo.timestamp
                                AND registro_evento_decimo.cod_evento       = ultimo_registro_evento_decimo.cod_evento
                                AND registro_evento_decimo.desdobramento    = ultimo_registro_evento_decimo.desdobramento
                                AND registro_evento_decimo.cod_registro     = evento_decimo_calculado.cod_registro
                                AND registro_evento_decimo.timestamp        = evento_decimo_calculado.timestamp_registro
                                AND registro_evento_decimo.cod_evento       = evento_decimo_calculado.cod_evento
                                AND registro_evento_decimo.desdobramento    = evento_decimo_calculado.desdobramento
                                AND (registro_evento_decimo.desdobramento = ''D'' OR registro_evento_decimo.desdobramento = ''C'')
                                AND registro_evento_decimo.cod_contrato = servidor_contrato_servidor.cod_contrato
                                AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                                AND registro_evento_decimo.cod_periodo_movimentacao = periodo_movimentacao.cod_periodo_movimentacao
                                AND servidor.numcgm = '|| inNumCgm ||'
                                AND registro_evento_decimo.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                                AND registro_evento_decimo.cod_evento = '|| inCodEvento ||' ';
                    FOR reBases IN EXECUTE stSql
                    LOOP
                        IF stNatureza = 'B' THEN
                            nuSomaBaseDecimo := nuSomaBaseDecimo + reBases.valor;
                        END IF;
                        IF stNatureza = 'D' THEN
                            nuSomaDescontoDecimo := nuSomaDescontoDecimo + reBases.valor;
                        END IF;
                    END LOOP;
                    -------------------INÍCIO DO AJUSTE COM O DÉCIMO--------------------
                    
                    -------------------INÍCIO DO AJUSTE COM O SALÁRIO--------------------
                    stSituacaoFolhaSalario      := pega0SituacaoDaFolhaSalario();
                    IF  stSituacaoFolhaSalario = 'f'  THEN
                        --Consulta que busca o valor da folha principal a ser somado com os demais valores
                        --verificando se a folha já foi calculado e se está fechada
                        stSql := 'SELECT evento_calculado.valor
                                        , registro_evento.cod_registro
                                        , registro_evento.timestamp
                                    FROM folhapagamento'|| stEntidade ||'.registro_evento
                                        , folhapagamento'|| stEntidade ||'.evento_calculado
                                        , folhapagamento'|| stEntidade ||'.registro_evento_periodo
                                        , folhapagamento'|| stEntidade ||'.contrato_servidor_periodo
                                        , folhapagamento'|| stEntidade ||'.periodo_movimentacao
                                        , pessoal'|| stEntidade ||'.servidor_contrato_servidor
                                        , pessoal'|| stEntidade ||'.servidor
                                    WHERE registro_evento.cod_evento       = '|| inCodEvento ||'
                                    AND registro_evento.cod_registro     = registro_evento_periodo.cod_registro
                                    AND registro_evento.cod_registro     = evento_calculado.cod_registro
                                    AND registro_evento.timestamp        = evento_calculado.timestamp_registro
                                    AND registro_evento.cod_evento       = evento_calculado.cod_evento
                                    AND registro_evento_periodo.cod_contrato             = contrato_servidor_periodo.cod_contrato
                                    AND registro_evento_periodo.cod_periodo_movimentacao = contrato_servidor_periodo.cod_periodo_movimentacao
                                    AND contrato_servidor_periodo.cod_periodo_movimentacao = periodo_movimentacao.cod_periodo_movimentacao
                                    AND periodo_movimentacao.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                                    AND registro_evento_periodo.cod_contrato = servidor_contrato_servidor.cod_contrato
                                    AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                                    AND servidor.numcgm = '|| inNumCgm ||' ';
                        FOR reBases IN EXECUTE stSql
                        LOOP
                            IF stNatureza = 'B' THEN
                                nuSomaBaseSaldoAviso := nuSomaBaseSaldoAviso + reBases.valor;
                            END IF;
                            IF stNatureza = 'D' THEN
                                nuSomaDescontoSaldoAviso := nuSomaDescontoSaldoAviso + reBases.valor;
                            END IF;
                        END LOOP;
                    END IF;
                    -------------------INÍCIO DO AJUSTE COM O SALÁRIO--------------------
                    -------------------INICIO DO AJUSTE COM O FÉRIAS--------------------------
                    stSql := 'SELECT COALESCE(evento_ferias_calculado.valor,0.00) AS valor
                                FROM folhapagamento'|| stEntidade ||'.registro_evento_ferias
                                    , folhapagamento'|| stEntidade ||'.ultimo_registro_evento_ferias
                                    , folhapagamento'|| stEntidade ||'.evento_ferias_calculado
                                    , folhapagamento'|| stEntidade ||'.periodo_movimentacao
                                    , pessoal'|| stEntidade ||'.servidor_contrato_servidor
                                    , pessoal'|| stEntidade ||'.servidor
                                    , pessoal'|| stEntidade ||'.ferias
                                    , pessoal'|| stEntidade ||'.lancamento_ferias
                                WHERE registro_evento_ferias.cod_registro     = ultimo_registro_evento_ferias.cod_registro
                                AND registro_evento_ferias.timestamp        = ultimo_registro_evento_ferias.timestamp
                                AND registro_evento_ferias.cod_evento       = ultimo_registro_evento_ferias.cod_evento
                                AND registro_evento_ferias.desdobramento    = ultimo_registro_evento_ferias.desdobramento
                                AND registro_evento_ferias.cod_registro     = evento_ferias_calculado.cod_registro
                                AND registro_evento_ferias.timestamp        = evento_ferias_calculado.timestamp_registro
                                AND registro_evento_ferias.cod_evento       = evento_ferias_calculado.cod_evento
                                AND registro_evento_ferias.desdobramento    = evento_ferias_calculado.desdobramento
                                AND registro_evento_ferias.cod_contrato = servidor_contrato_servidor.cod_contrato
                                AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                                AND registro_evento_ferias.cod_periodo_movimentacao = periodo_movimentacao.cod_periodo_movimentacao
                                AND registro_evento_ferias.cod_contrato = ferias.cod_contrato
                                AND ferias.cod_ferias = lancamento_ferias.cod_ferias
                                AND lancamento_ferias.cod_tipo = 1
                                AND servidor.numcgm = '|| inNumCgm ||'
                                AND registro_evento_ferias.cod_evento = '|| inCodEvento ||'
                                AND registro_evento_ferias.cod_periodo_movimentacao ='||  inCodPeriodoMovimentacao;
                    FOR reBases IN EXECUTE stSql
                    LOOP
                        IF stNatureza = 'B' THEN
                            nuSomaBaseSaldoAviso := nuSomaBaseSaldoAviso + reBases.valor;
                        END IF;
                        IF stNatureza = 'D' THEN
                            nuSomaDescontoSaldoAviso := nuSomaDescontoSaldoAviso + reBases.valor;
                        END IF;
                    END LOOP;
                    -------------------FIM DO AJUSTE COM O FÉRIAS-----------------------------
                END IF;
                -------------------INÍCIO DO AJUSTE COM O RESCISÃO--------------------
                stSql := 'SELECT evento_rescisao_calculado.valor
                                , evento_rescisao_calculado.cod_registro
                                , evento_rescisao_calculado.cod_evento
                                , evento_rescisao_calculado.timestamp_registro
                                , evento_rescisao_calculado.desdobramento
                                , registro_evento_rescisao.cod_contrato
                            FROM folhapagamento'|| stEntidade ||'.registro_evento_rescisao
                                , folhapagamento'|| stEntidade ||'.ultimo_registro_evento_rescisao
                                , folhapagamento'|| stEntidade ||'.evento_rescisao_calculado
                                , folhapagamento'|| stEntidade ||'.periodo_movimentacao
                                , pessoal'|| stEntidade ||'.servidor_contrato_servidor
                                , pessoal'|| stEntidade ||'.servidor
                            WHERE registro_evento_rescisao.cod_registro     = ultimo_registro_evento_rescisao.cod_registro
                            AND registro_evento_rescisao.timestamp        = ultimo_registro_evento_rescisao.timestamp
                            AND registro_evento_rescisao.cod_evento       = ultimo_registro_evento_rescisao.cod_evento
                            AND registro_evento_rescisao.desdobramento    = ultimo_registro_evento_rescisao.desdobramento
                            AND registro_evento_rescisao.cod_registro     = evento_rescisao_calculado.cod_registro
                            AND registro_evento_rescisao.timestamp        = evento_rescisao_calculado.timestamp_registro
                            AND registro_evento_rescisao.cod_evento       = evento_rescisao_calculado.cod_evento
                            AND registro_evento_rescisao.desdobramento    = evento_rescisao_calculado.desdobramento
                            AND registro_evento_rescisao.cod_contrato = servidor_contrato_servidor.cod_contrato
                            AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                            AND registro_evento_rescisao.cod_periodo_movimentacao = periodo_movimentacao.cod_periodo_movimentacao
                            AND servidor.numcgm = '|| inNumCgm ||'
                            AND registro_evento_rescisao.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                            AND registro_evento_rescisao.cod_evento = '|| inCodEvento ||' ';
                --Na rescisão deverá haver um somador de base e desconto para cada tipo de desdobramento
                --com isso de acordo com o desdobramento haverá a soma com uma base de uma folha
                --Desdobramento:
                --S (Saldo Salário)         - Folha Salário
                --A (Aviso Prévio)          - Folha Rescisão 
                --V (Férias Vencidas)       - Folha Férias
                --P (Férias Proporcionais)  - Folha Férias
                --D (13° Salário)           - Folha Décimo
                FOR reBases IN EXECUTE stSql
                LOOP
                    IF stNatureza = 'B' THEN                        
                        IF reBases.desdobramento = 'S' OR reBases.desdobramento = 'A' THEN
                            nuSomaBaseSaldoAviso := nuSomaBaseSaldoAviso + reBases.valor;
                        END IF;
                        IF reBases.desdobramento = 'D' THEN
                            nuSomaBaseDecimo := nuSomaBaseDecimo + reBases.valor;
                        END IF;
                    END IF;
                    IF stNatureza = 'D' AND NOT ( reBases.cod_contrato = inCodContrato ) THEN
                        IF reBases.desdobramento = 'S' OR reBases.desdobramento = 'A' THEN
                            nuSomaDescontoSaldoAviso := nuSomaDescontoSaldoAviso + reBases.valor;
                        END IF;
                        IF reBases.desdobramento = 'D' THEN
                            nuSomaDescontoDecimo := nuSomaDescontoDecimo + reBases.valor;
                        END IF;
                    END IF;
                    IF stNatureza = 'D' AND reBases.cod_contrato = inCodContrato THEN
                        IF reBases.desdobramento = 'S' THEN
                            inCodRegistroDescontoSaldoAviso     = reBases.cod_registro;
                            inCodEventoDescontoSaldoAviso       = reBases.cod_evento;
                            stTimestampDescontoSaldoAviso       = reBases.timestamp_registro;
                        END IF;
                        IF reBases.desdobramento = 'D' THEN
                            inCodRegistroDescontoDecimo     = reBases.cod_registro;
                            inCodEventoDescontoDecimo       = reBases.cod_evento;
                            stTimestampDescontoDecimo       = reBases.timestamp_registro;
                        END IF;
                    END IF;
                END LOOP;
                -------------------FIM DO AJUSTE COM O RESCISÃO-----------------------
            END LOOP;
        END IF;        
        ---------------------AJUSTE COM DESCONTOS EXTERNOS----------------------
        stSql := ' SELECT registro_evento_rescisao.cod_contrato
                    FROM folhapagamento'|| stEntidade ||'.registro_evento_rescisao
                        , folhapagamento'|| stEntidade ||'.evento_rescisao_calculado
                        , pessoal'|| stEntidade ||'.servidor_contrato_servidor
                        , pessoal'|| stEntidade ||'.servidor
                    WHERE registro_evento_rescisao.cod_registro = evento_rescisao_calculado.cod_registro
                    AND registro_evento_rescisao.cod_evento = evento_rescisao_calculado.cod_evento
                    AND registro_evento_rescisao.desdobramento = evento_rescisao_calculado.desdobramento
                    AND registro_evento_rescisao.timestamp = evento_rescisao_calculado.timestamp_registro
                    AND registro_evento_rescisao.cod_contrato = servidor_contrato_servidor.cod_contrato
                    AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                    AND registro_evento_rescisao.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                    AND numcgm = '|| inNumCgm ||'
                GROUP BY registro_evento_rescisao.cod_contrato';                
        
        FOR reRegistro IN EXECUTE stSql
        LOOP
            stSql := '     SELECT desconto_externo_previdencia.vl_base_previdencia as base
                                , desconto_externo_previdencia_valor.valor_previdencia as desconto
                            FROM folhapagamento'|| stEntidade ||'.desconto_externo_previdencia                                    
                        LEFT JOIN (SELECT desconto_externo_previdencia_valor.*
                                    FROM folhapagamento'|| stEntidade ||'.desconto_externo_previdencia_valor
                                        , (   SELECT cod_contrato
                                                , max(timestamp_valor) as timestamp_valor
                                                FROM folhapagamento'|| stEntidade ||'.desconto_externo_previdencia_valor
                                            GROUP BY cod_contrato) as max_desconto_externo_previdencia_valor
                                    WHERE desconto_externo_previdencia_valor.cod_contrato = max_desconto_externo_previdencia_valor.cod_contrato
                                    AND desconto_externo_previdencia_valor.timestamp_valor = max_desconto_externo_previdencia_valor.timestamp_valor) AS desconto_externo_previdencia_valor
                            ON desconto_externo_previdencia_valor.cod_contrato = desconto_externo_previdencia.cod_contrato
                            AND desconto_externo_previdencia_valor.timestamp = desconto_externo_previdencia_valor.timestamp      
                                , (  SELECT cod_contrato
                                        , max(timestamp) as timestamp
                                    FROM folhapagamento'|| stEntidade ||'.desconto_externo_previdencia
                                    WHERE vigencia <= '|| quote_literal(pega0DataFinalCompetenciaDoPeriodoMovimento(inCodPeriodoMovimentacao)) ||'
                                GROUP BY cod_contrato) as max_desconto_externo_previdencia
                            WHERE desconto_externo_previdencia.cod_contrato = max_desconto_externo_previdencia.cod_contrato
                            AND desconto_externo_previdencia.timestamp = max_desconto_externo_previdencia.timestamp
                            AND NOT EXISTS (SELECT 1
                                                FROM folhapagamento'|| stEntidade ||'.desconto_externo_previdencia_anulado
                                            WHERE desconto_externo_previdencia.cod_contrato = desconto_externo_previdencia_anulado.cod_contrato
                                                AND desconto_externo_previdencia.timestamp = desconto_externo_previdencia_anulado.timestamp)
                            AND desconto_externo_previdencia.cod_contrato = '|| reRegistro.cod_contrato;
            OPEN crCursor FOR EXECUTE stSql;
                FETCH crCursor INTO reDescontoExterno;
            CLOSE crCursor;
            IF reDescontoExterno.base IS NOT NULL THEN
                nuSomaBaseSaldoAviso     := nuSomaBaseSaldoAviso + reDescontoExterno.base;
            END IF;
            IF reDescontoExterno.desconto IS NOT NULL THEN
                nuSomaDescontoExterno := nuSomaDescontoExterno + reDescontoExterno.desconto;
            END IF;
        END LOOP;
        ---------------------AJUSTE COM DESCONTOS EXTERNOS----------------------      
       
        IF (nuSomaBaseSaldoAviso+nuSomaBaseDecimo) > 0 THEN
            stSql := 'SELECT evento_rescisao_calculado.valor
                            , evento_rescisao_calculado.cod_registro
                            , evento_rescisao_calculado.cod_evento
                            , evento_rescisao_calculado.timestamp_registro
                            , evento_rescisao_calculado.desdobramento
                            , registro_evento_rescisao.cod_contrato
                        FROM folhapagamento'|| stEntidade ||'.registro_evento_rescisao
                            , folhapagamento'|| stEntidade ||'.ultimo_registro_evento_rescisao
                            , folhapagamento'|| stEntidade ||'.evento_rescisao_calculado
                            , folhapagamento'|| stEntidade ||'.periodo_movimentacao
                            , pessoal'|| stEntidade ||'.servidor_contrato_servidor
                            , pessoal'|| stEntidade ||'.servidor
                        WHERE registro_evento_rescisao.cod_registro     = ultimo_registro_evento_rescisao.cod_registro
                        AND registro_evento_rescisao.timestamp        = ultimo_registro_evento_rescisao.timestamp
                        AND registro_evento_rescisao.cod_evento       = ultimo_registro_evento_rescisao.cod_evento
                        AND registro_evento_rescisao.desdobramento    = ultimo_registro_evento_rescisao.desdobramento
                        AND registro_evento_rescisao.cod_registro     = evento_rescisao_calculado.cod_registro
                        AND registro_evento_rescisao.timestamp        = evento_rescisao_calculado.timestamp_registro
                        AND registro_evento_rescisao.cod_evento       = evento_rescisao_calculado.cod_evento
                        AND registro_evento_rescisao.desdobramento    = evento_rescisao_calculado.desdobramento
                        AND registro_evento_rescisao.cod_contrato = servidor_contrato_servidor.cod_contrato
                        AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                        AND registro_evento_rescisao.cod_periodo_movimentacao = periodo_movimentacao.cod_periodo_movimentacao
                        AND servidor.numcgm = '|| inNumCgm ||'
                        AND registro_evento_rescisao.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                        AND registro_evento_rescisao.cod_evento = '|| inCodEvento ||' ';
            --Na rescisão deverá haver um somador de base e desconto para cada tipo de desdobramento
            --com isso de acordo com o desdobramento haverá a soma com uma base de uma folha
            --Desdobramento:
            --S (Saldo Salário)         - Folha Salário
            --A (Aviso Prévio)          - Folha Rescisão 
            --V (Férias Vencidas)       - Folha Férias
            --P (Férias Proporcionais)  - Folha Férias
            --D (13° Salário)           - Folha Décimo
            
            FOR reBases IN EXECUTE stSql
            LOOP
                IF stNatureza = 'D' AND reBases.cod_contrato = inCodContrato THEN
                    IF reBases.desdobramento = 'S' THEN
                        inCodRegistroDescontoSaldoAviso := reBases.cod_registro;
                        inCodEventoDescontoSaldoAviso   := reBases.cod_evento;
                        stTimestampDescontoSaldoAviso   := reBases.timestamp_registro;
                    END IF;
                    IF reBases.desdobramento = 'D' THEN
                        inCodRegistroDescontoDecimo := reBases.cod_registro;
                        inCodEventoDescontoDecimo   := reBases.cod_evento;
                        stTimestampDescontoDecimo   := reBases.timestamp_registro;
                    END IF;
                ELSE 
                    IF stNatureza = 'B'  THEN
                        IF reBases.desdobramento = 'S' THEN
                            nuBaseRescisaoDesdobramentoSaldoAviso  := nuBaseRescisaoDesdobramentoSaldoAviso + reBases.valor; 
                        END IF;
                        IF reBases.desdobramento = 'D' THEN
                            nuBaseRescisaoDesdobramentoDecimo  := nuBaseRescisaoDesdobramentoDecimo + reBases.valor;
                        END IF;
                    END IF;
                END IF;
            END LOOP;

            arDesdobramento := string_to_array('S#D','#');
            
            FOR inIndex IN 1 .. 2
            LOOP            
                boAjustar := FALSE;
                IF arDesdobramento[inIndex] = 'S' THEN
                    nuSomaBase              := nuSomaBaseSaldoAviso;
                    nuSomaDesconto          := nuSomaDescontoSaldoAviso+nuSomaDescontoExterno;
                    inCodRegistroDesconto   := inCodRegistroDescontoSaldoAviso;
                    inCodEventoDesconto     := inCodEventoDescontoSaldoAviso;
                    stTimestampDesconto     := stTimestampDescontoSaldoAviso;
                    IF nuBaseRescisaoDesdobramentoSaldoAviso > 0 THEN 
                        boAjustar := TRUE;        
                    END IF;
                END IF;
                IF arDesdobramento[inIndex] = 'D' THEN
                    nuSomaBase              := nuSomaBaseDecimo;
                    nuSomaDesconto          := nuSomaDescontoDecimo;
                    inCodRegistroDesconto   := inCodRegistroDescontoDecimo;
                    inCodEventoDesconto     := inCodEventoDescontoDecimo;
                    stTimestampDesconto     := stTimestampDescontoDecimo;
                    IF nuBaseRescisaoDesdobramentoDecimo > 0 THEN 
                        boAjustar := TRUE;        
                    END IF;
                END IF;            
                     
                IF nuSomaBase > 0 AND boAjustar = TRUE THEN
                    --Percentual de desconto baseado na faixa de desconto da tabela folhapagamento'|| stEntidade ||'.faixa_desconto
                    nuPercentualDesconto := selectIntoNumeric('SELECT percentual_desconto 
                                                        FROM folhapagamento'|| stEntidade ||'.faixa_desconto
                                                        , folhapagamento'|| stEntidade ||'.previdencia_previdencia
                                                    WHERE valor_inicial <= '|| nuSomaBase ||'
                                                        AND valor_final   >= '|| nuSomaBase ||'
                                                        AND faixa_desconto.cod_previdencia = '|| inCodPrevidencia ||'
                                                        AND faixa_desconto.timestamp_previdencia = '|| quote_literal(stTimestamp) ||'
                                                        AND previdencia_previdencia.timestamp       = faixa_desconto.timestamp_previdencia
                                                        AND previdencia_previdencia.cod_previdencia = faixa_desconto.cod_previdencia
                                                        AND previdencia_previdencia.vigencia        = '|| quote_literal(dtVigencia) ||' ');                   
                    IF nuPercentualDesconto IS NULL THEN
                        stSql := ' SELECT COALESCE(percentual_desconto,0.00) as percentual_desconto
                                    FROM folhapagamento'|| stEntidade ||'.faixa_desconto
                                        , folhapagamento'|| stEntidade ||'.previdencia_previdencia
                                    WHERE valor_final <= '|| nuSomaBase ||'
                                    AND valor_inicial > 0.00
                                    AND faixa_desconto.cod_previdencia = '|| inCodPrevidencia ||'
                                    AND faixa_desconto.timestamp_previdencia = '|| quote_literal(stTimestamp) ||'
                                    AND previdencia_previdencia.timestamp       = faixa_desconto.timestamp_previdencia
                                    AND previdencia_previdencia.cod_previdencia = faixa_desconto.cod_previdencia
                                    AND previdencia_previdencia.vigencia        = '|| quote_literal(dtVigencia) ||'
                                ORDER BY valor_final DESC
                                    LIMIT 1';
                        nuPercentualDesconto := selectIntoNumeric(stSql);
    
                        nuSomaBase := selectIntoNumeric('SELECT COALESCE(valor_final,0.00) as valor_final
                                            FROM folhapagamento'|| stEntidade ||'.faixa_desconto
                                                , folhapagamento'|| stEntidade ||'.previdencia_previdencia
                                            WHERE valor_final <= '|| nuSomaBase ||'
                                                AND valor_inicial > 0.00
                                                AND faixa_desconto.cod_previdencia = '|| inCodPrevidencia ||'
                                                AND faixa_desconto.timestamp_previdencia = '|| quote_literal(stTimestamp) ||'
                                                AND previdencia_previdencia.timestamp       = faixa_desconto.timestamp_previdencia
                                                AND previdencia_previdencia.cod_previdencia = faixa_desconto.cod_previdencia
                                                AND previdencia_previdencia.vigencia        = '|| quote_literal(dtVigencia) ||'
                                        ORDER BY valor_final DESC
                                            LIMIT 1');
                    END IF;
                    nuTotalDescontoCalculo := nuSomaBase * nuPercentualDesconto / 100;
                    nuTotalDescontoCalculo := nuTotalDescontoCalculo - nuSomaDesconto;
                    nuTotalDescontoCalculo := truncarNumerico(nuTotalDescontoCalculo,2);
                                        
                    stSql := 'UPDATE folhapagamento'|| stEntidade ||'.evento_rescisao_calculado SET valor = '|| nuTotalDescontoCalculo ||',
                                                                            quantidade = '|| nuPercentualDesconto ||'
                                WHERE cod_evento         = '|| inCodEventoDesconto ||'
                                AND cod_registro       = '|| inCodRegistroDesconto ||'
                                AND desdobramento      = '|| quote_literal(arDesdobramento[inIndex]) ||'
                                AND timestamp_registro = '|| quote_literal(stTimestampDesconto) ||' ';
                    EXECUTE stSql;
                END IF;
            END LOOP;
        END IF;    
    END IF;
    RETURN TRUE;
END;
$$ LANGUAGE 'plpgsql';

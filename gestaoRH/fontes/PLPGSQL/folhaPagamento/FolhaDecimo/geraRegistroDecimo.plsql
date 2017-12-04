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
--    * Data de Criação: 00/00/0000
--
--
--    * @author Projetista: Vandré Miguel Ramos
--
--    * @package URBEM
--    * @subpackage
--
--    $Revision: 28805 $
--    $Author: souzadl $
--    $Date: 2008-03-27 09:19:31 -0300 (Qui, 27 Mar 2008) $
--
--    * Casos de uso: uc-04.05.24
--*/

CREATE OR REPLACE FUNCTION geraRegistroDecimo(integer,integer,varchar,varchar) RETURNS BOOLEAN as $$

DECLARE

inCodContrato                    ALIAS FOR $1;
inCodPeriodoMovimentacao         ALIAS FOR $2;
stDesdobramento                  ALIAS FOR $3;
stEntidadeParametro           ALIAS FOR $4;
stEntidade                    VARCHAR := '';
stDesdobramentoRescisao          VARCHAR := '';
inCodPeriodoMovimentacaoInicial  INTEGER := 0;
inCodPeriodoMovimentacaoTemp     INTEGER;
inCodContrato0                   INTEGER := 0;
stSql                            VARCHAR :='';
crCursor                         REFCURSOR;
reRegistro                       RECORD;
reRegistro1                      RECORD;
boRetorno                        BOOLEAN := TRUE;
dtInicialPeriodo                 DATE;
dtFinalPeriodo                   DATE;
stSituacao                       VARCHAR := 'f';
stDataFinalCompetencia           VARCHAR := '';
stAnoAdiantamento                VARCHAR := '';
inCodEvento                      INTEGER := 0;
InCodSubDivisao                  INTEGER := 1;
inCodFuncao                      INTEGER := 1;
inCodEspecialidade               INTEGER := 1;
inNrRegistros                    INTEGER := 0;
inContador                       INTEGER := 0;
stFormula                        VARCHAR := '';
stExecutaFormula                 VARCHAR := '';
nuExecutaFormula                 NUMERIC := 0;
nuValor                          NUMERIC := 0;
nuQuantidade                     NUMERIC := 0;
nuPercentualAdiantamento         NUMERIC := 0;
boGerarApenasFixo                VARCHAR := 'f';
boGravouRegistro                 BOOLEAN;
inControleExecucaoRescisaoDecimo INTEGER :=0; 
boGerandoRescisao                VARCHAR := 'f'; 


BEGIN
    stEntidade := criarBufferEntidade(stEntidadeParametro);
    inCodPeriodoMovimentacaoTemp := criarBufferInteiro('inCodPeriodoMovimentacao',inCodPeriodoMovimentacao);
    stDataFinalCompetencia := pega0DataFinalCompetenciaDoPeriodoMovimento(inCodPeriodoMovimentacao);


    inControleExecucaoRescisaoDecimo := countBufferTexto('stDataFinalCompetencia');
    IF inControleExecucaoRescisaoDecimo < 1 THEN
        stDataFinalCompetencia  := criarBufferTexto(  'stDataFinalCompetencia',  stDataFinalCompetencia );
        inCodContrato0          := criarBufferInteiro( 'inCodContrato' , inCodContrato );
    ELSE
        boGerandoRescisao         := 't'; 
    END IF;


    inCodSubDivisao        := pega0SubDivisaoDoContratoNaData( inCodContrato, stDataFinalCompetencia );
    inCodFuncao            := pega0FuncaoDoContratoNaData( inCodContrato, stDataFinalCompetencia );
    inCodEspecialidade     := pega0EspecialidadeDoContratoNaData( inCodContrato, stDataFinalCompetencia );

    inContador := selectIntoInteger('
             SELECT COUNT(periodo_movimentacao.cod_periodo_movimentacao) as t
               FROM   folhapagamento'||stEntidade||'.periodo_movimentacao
                     ,folhapagamento'||stEntidade||'.contrato_servidor_periodo
                     ,folhapagamento'||stEntidade||'.registro_evento_decimo
                     ,folhapagamento'||stEntidade||'.ultimo_registro_evento_decimo
              WHERE periodo_movimentacao.cod_periodo_movimentacao   = contrato_servidor_periodo.cod_periodo_movimentacao
                AND periodo_movimentacao.cod_periodo_movimentacao   = '||inCodPeriodoMovimentacao||'
                AND contrato_servidor_periodo.cod_contrato          = '||inCodContrato||'
                AND registro_evento_decimo.cod_periodo_movimentacao = contrato_servidor_periodo.cod_periodo_movimentacao
                AND registro_evento_decimo.cod_contrato             = contrato_servidor_periodo.cod_contrato
                AND registro_evento_decimo.desdobramento            = '''||stDesdobramento||'''
                AND registro_evento_decimo.cod_evento               = ultimo_registro_evento_decimo.cod_evento
                AND registro_evento_decimo.cod_registro             = ultimo_registro_evento_decimo.cod_registro
                AND registro_evento_decimo.desdobramento            = ultimo_registro_evento_decimo.desdobramento
                AND registro_evento_decimo.timestamp                = ultimo_registro_evento_decimo.timestamp');

    --VERIFICA SE EXISTE O REGISTRO PARA O CONTRATO DO DESDOBRAMENTO NA COMPETÊNCIA
    -- CASO SIM CANCELA A EXECUÇÃO
    IF inContador > 0 THEN
       RETURN FALSE;
    END IF;


    --FUNÇÃO PARA RECUPERAR A SITUAÇÃO DO ULTIMO PERIODO A SER CALCULADO
    stSql := 'SELECT dt_inicial,dt_final,situacao 
                 FROM folhapagamento'||stEntidade||'.periodo_movimentacao 
                 JOIN folhapagamento'||stEntidade||'.periodo_movimentacao_situacao
                  ON  (periodo_movimentacao.cod_periodo_movimentacao = periodo_movimentacao_situacao.cod_periodo_movimentacao
                       AND periodo_movimentacao_situacao.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||') 
                ORDER BY periodo_movimentacao_situacao.timestamp DESC LIMIT 1 ';

    OPEN crCursor FOR EXECUTE stSql;
         FETCH crCursor INTO dtInicialPeriodo,dtFinalPeriodo,stSituacao;
    CLOSE crCursor;

    --CONDIÇÕES ESPECIAIS QUANDO O MÊS DE COMPETÊNCIA FOR DEZEMBRO
    IF( SUBSTR(to_char(dtFinalPeriodo,'yyyy-mm-dd'),6,2) = '12' ) THEN
        --PARA O DESDOBRAMENTO  SALDO DE DÉCIMO  E PERIODO ESTIVER ABERTO UTILIZAR A COMPETÊNCIA ANTERIOR
        IF ( stDesdobramento = 'D' AND stSituacao = 'a') THEN
            stSql := 'SELECT dt_inicial,dt_final,situacao 
                         FROM folhapagamento'||stEntidade||'.periodo_movimentacao 
                         JOIN folhapagamento'||stEntidade||'.periodo_movimentacao_situacao
                          ON  (periodo_movimentacao.cod_periodo_movimentacao = periodo_movimentacao_situacao.cod_periodo_movimentacao
                               AND periodo_movimentacao_situacao.cod_periodo_movimentacao = ('||inCodPeriodoMovimentacao||' - 1))
                        ORDER BY periodo_movimentacao_situacao.timestamp DESC LIMIT 1 ';
     
            OPEN crCursor FOR EXECUTE stSql;
                 FETCH crCursor INTO dtInicialPeriodo,dtFinalPeriodo,stSituacao;
            CLOSE crCursor;
        END IF;
        --SE MÊS FINAL IGUAL A DEZEMBRO E DESDOBRAMENTO "ADIANTAMENTO" NÃO SERÁ EXECUTADA A GERAÇÃO DE REGISTROS
        IF(stDesdobramento = 'A') THEN
           RETURN FALSE;
        END IF;
        --SE MÊS FINAL IGUAL A DEZEMBRO E DESDOBRAMENTO "COMPLEMENTAÇÃO" E FOLHA SALÁRIO FECHADA NÃO SERÁ EXECUTADA A GERAÇÃO DE REGISTROS
        IF(stDesdobramento = 'C' AND stSituacao = 'f') THEN
           RETURN FALSE;
        END IF;
    ELSE
        --SE MÊS FINAL DIFERENTE DE DEZEMBRO E DESDOBRAMENTO "COMPLEMENTAÇÃO" NÃO SERÁ EXECUTADA A GERAÇÃO DE REGISTROS
        IF(stDesdobramento = 'C' ) THEN
            RETURN FALSE;
        END IF;
    END IF;


    --CONDIÇÃO ESPECIAL PARA DESDOBRAMENTO = ADIANTAMENTO
    IF stDesdobramento = 'A' THEN
        stSql := 'SELECT  percentual,
                         CASE WHEN vantagens_fixas = ''true''         
                              THEN ''t''                               
                              ELSE ''f''
                         END as bovantagem     
                   FROM folhapagamento'||stEntidade||'.configuracao_adiantamento
                  WHERE cod_contrato             = '||inCodContrato||'
                    AND cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                    AND desdobramento            = '''||stDesdobramento||'''' ;      

        OPEN crCursor FOR EXECUTE stSql;
            FETCH crCursor INTO nuPercentualAdiantamento,boGerarApenasFixo;
        CLOSE crCursor;
    END IF;

    nuPercentualAdiantamento := criarBufferNumerico( 'nuPercentualAdiantamento' , nuPercentualAdiantamento );
    boGerarApenasFixo        := criarBufferTexto('boGerarApenasFixo', boGerarApenasFixo );

    -- FUNÇÃO PARA RECUPERAR O PERIODO_MOVIMENTACAO DA COMPETÊNCIA DE JANEIRO DO ANO DE CÁLCULO
    stSql := 'SELECT periodo_movimentacao.cod_periodo_movimentacao 
                 FROM folhapagamento'||stEntidade||'.periodo_movimentacao 
                 JOIN folhapagamento'||stEntidade||'.periodo_movimentacao_situacao
                  ON  (periodo_movimentacao.cod_periodo_movimentacao = periodo_movimentacao_situacao.cod_periodo_movimentacao)
             WHERE  to_char(dt_inicial,''yyyy-mm'') = to_char(to_date('''||dtFinalPeriodo||''',''yyyy''),''yyyy'')||''-01''
                ORDER BY periodo_movimentacao_situacao.timestamp DESC LIMIT 1 ';

    OPEN crCursor FOR EXECUTE stSql;
         FETCH crCursor INTO inCodPeriodoMovimentacaoInicial;
    CLOSE crCursor;


    -- CRIA TABELA TEMPORÁRIA QUE SERÁ UTILIZADA NO CÁLCULO DAS MÉDIAS
    CREATE TEMPORARY TABLE tmp_registro_evento_13 
             (cod_evento               INTEGER,
              valor                    NUMERIC(14,2),
              quantidade               NUMERIC(14,2),
              cod_periodo_movimentacao INTEGER,
              natureza                 VARCHAR,
              fixado                   VARCHAR,
              unidade_quantitativa     NUMERIC(14,2),
              lido_de                  VARCHAR
             );

    -- LEITURA E INSERÇÃO DO REGISTRO DE EVENTOS ATUAL - (PONTO FIXO)
    stSql := ' INSERT INTO tmp_registro_evento_13 
                 SELECT registro_evento.cod_evento
                      , COALESCE(registro_evento.valor,0.00) as valor
                      , COALESCE(registro_evento.quantidade,0.00) as quantidade
                      , registro_evento_periodo.cod_periodo_movimentacao
                      , evento.natureza
                      , evento.fixado
                      , evento_evento.unidade_quantitativa
                      , ''fixo_atual'' as lido_de
                   FROM folhapagamento'||stEntidade||'.ultimo_registro_evento
             INNER JOIN folhapagamento'||stEntidade||'.registro_evento_periodo
                     ON registro_evento_periodo.cod_registro = ultimo_registro_evento.cod_registro
                    AND registro_evento_periodo.cod_contrato = '||inCodContrato||'
                    AND registro_evento_periodo.cod_periodo_movimentacao = '||incodPeriodoMovimentacao||'
             INNER JOIN folhapagamento'||stEntidade||'.registro_evento
                     ON registro_evento.cod_registro  = ultimo_registro_evento.cod_registro
                    AND registro_evento.proporcional = false
             INNER JOIN folhapagamento'||stEntidade||'.evento
                     ON evento.cod_evento = registro_evento.cod_evento
                    AND evento.natureza IN ( ''P'',''D'' )     
                    AND evento.tipo = ''F''    
             INNER JOIN ( SELECT COALESCE(unidade_quantitativa,0) as unidade_quantitativa
                               , evento_evento.cod_evento   
                               , evento_evento.timestamp    
                            FROM folhapagamento'||stEntidade||'.evento_evento
                               , (  SELECT cod_evento
                                         , max(timestamp) as timestamp
                                      FROM folhapagamento'||stEntidade||'.evento_evento
                                  GROUP BY cod_evento) as max_evento_evento
                             WHERE max_evento_evento.cod_evento = evento_evento.cod_evento
                               AND max_evento_evento.timestamp  = evento_evento.timestamp) as evento_evento
                     ON evento_evento.cod_evento = evento.cod_evento
             INNER JOIN folhapagamento'||stEntidade||'.configuracao_evento_caso
                     ON configuracao_evento_caso.cod_evento = evento_evento.cod_evento
                    AND configuracao_evento_caso.timestamp  = evento_evento.timestamp
                    AND configuracao_evento_caso.cod_configuracao = 3
             INNER JOIN folhapagamento'||stEntidade||'.tipo_evento_configuracao_media
                     ON tipo_evento_configuracao_media.cod_configuracao = 3
                    AND tipo_evento_configuracao_media.timestamp = configuracao_evento_caso.timestamp 
                    AND tipo_evento_configuracao_media.cod_evento = configuracao_evento_caso.cod_evento
                    AND tipo_evento_configuracao_media.cod_caso = configuracao_evento_caso.cod_caso
               ORDER BY registro_evento.cod_evento ';
    EXECUTE stSql;




    -- VERIFICA BASEADO NA CONSULTA ANTERIOR SE DEVE GERAR APENAS PONTO FIXO
    IF boGerarApenasFixo = 'f' THEN
       -- INSERE INFORMAÇÕES DA FOLHA SALÁRIO NA TABELA TEMPORÁRIO DA COMPETÊNCIA DE JANEIRO ATÉ A COMPETÊNCIA DE CALCULO
       IF inCodPeriodoMovimentacaoInicial IS NOT NULL THEN
       stSql := ' INSERT INTO tmp_registro_evento_13 
                     SELECT evento_calculado.cod_evento
                          , COALESCE(evento_calculado.valor,0.00) as valor
                          , COALESCE(evento_calculado.quantidade,0.00) as quantidade
                          , registro_evento_periodo.cod_periodo_movimentacao
                          , evento.natureza
                          , evento.fixado
                          , evento_evento.unidade_quantitativa
                          , ''evento_calculado'' as lido_de
                       FROM folhapagamento'||stEntidade||'.evento_calculado
                 INNER JOIN folhapagamento'||stEntidade||'.registro_evento_periodo
                         ON registro_evento_periodo.cod_registro = evento_calculado.cod_registro
                        AND registro_evento_periodo.cod_contrato = '||incodContrato||'
                        AND registro_evento_periodo.cod_periodo_movimentacao BETWEEN '||inCodPeriodoMovimentacaoInicial||' AND '||inCodPeriodoMovimentacao||'
                 INNER JOIN folhapagamento'||stEntidade||'.evento
                         ON evento.cod_evento = evento_calculado.cod_evento
                        AND evento.natureza IN ( ''P'',''D'' )         
                 INNER JOIN ( SELECT COALESCE(unidade_quantitativa,0) as unidade_quantitativa
                                   , evento_evento.cod_evento   
                                   , evento_evento.timestamp    
                                FROM folhapagamento'||stEntidade||'.evento_evento
                                   , (  SELECT cod_evento
                                             , max(timestamp) as timestamp
                                          FROM folhapagamento'||stEntidade||'.evento_evento
                                      GROUP BY cod_evento) as max_evento_evento
                                 WHERE max_evento_evento.cod_evento = evento_evento.cod_evento
                                   AND max_evento_evento.timestamp  = evento_evento.timestamp) as evento_evento
                         ON evento_evento.cod_evento = evento.cod_evento
                 INNER JOIN folhapagamento'||stEntidade||'.configuracao_evento_caso
                         ON configuracao_evento_caso.cod_evento = evento_evento.cod_evento
                        AND configuracao_evento_caso.timestamp  = evento_evento.timestamp
                        AND configuracao_evento_caso.cod_configuracao = 3
                 INNER JOIN folhapagamento'||stEntidade||'.tipo_evento_configuracao_media
                         ON tipo_evento_configuracao_media.cod_configuracao = 3
                        AND tipo_evento_configuracao_media.timestamp = configuracao_evento_caso.timestamp 
                        AND tipo_evento_configuracao_media.cod_evento = configuracao_evento_caso.cod_evento
                        AND tipo_evento_configuracao_media.cod_caso = configuracao_evento_caso.cod_caso
                      WHERE evento_calculado.valor > 0 
                   ORDER BY evento_calculado.cod_evento';
       EXECUTE stSql;
       END IF;

       -- INSERE INFORMAÇÕES DA FOLHA COMPLEMENTAR NA TABELA TEMPORÁRIO DA COMPETÊNCIA DE JANEIRO ATÉ A COMPETÊNCIA DE CALCULO
       IF inCodPeriodoMovimentacaoInicial IS NOT NULL THEN
       stSql := ' INSERT INTO tmp_registro_evento_13 
                     SELECT evento_complementar_calculado.cod_evento
                          , COALESCE(evento_complementar_calculado.valor,0.00) as valor
                          , COALESCE(evento_complementar_calculado.quantidade,0.00) as quantidade
                          , registro_evento_complementar.cod_periodo_movimentacao
                          , evento.natureza
                          , evento.fixado
                          , evento_evento.unidade_quantitativa
                          , ''evento_complementar_calculado'' as lido_de
                       FROM folhapagamento'||stEntidade||'.evento_complementar_calculado
                 INNER JOIN folhapagamento'||stEntidade||'.registro_evento_complementar
                         ON registro_evento_complementar.cod_registro = evento_complementar_calculado.cod_registro
                        AND registro_evento_complementar.cod_evento = evento_complementar_calculado.cod_evento
                        AND registro_evento_complementar.cod_configuracao = evento_complementar_calculado.cod_configuracao
                        AND registro_evento_complementar.timestamp = evento_complementar_calculado.timestamp_registro
                        AND registro_evento_complementar.cod_contrato = '||inCodContrato||'
                        AND registro_evento_complementar.cod_periodo_movimentacao BETWEEN '||inCodPeriodoMovimentacaoInicial||' AND '||inCodPeriodoMovimentacao||'
                 INNER JOIN folhapagamento'||stEntidade||'.evento
                         ON evento.cod_evento = evento_complementar_calculado.cod_evento
                        AND evento.natureza IN ( ''P'',''D'' )         
                 INNER JOIN ( SELECT COALESCE(unidade_quantitativa,0) as unidade_quantitativa
                                   , evento_evento.cod_evento   
                                   , evento_evento.timestamp    
                                FROM folhapagamento'||stEntidade||'.evento_evento
                                   , (  SELECT cod_evento
                                             , max(timestamp) as timestamp
                                          FROM folhapagamento'||stEntidade||'.evento_evento
                                      GROUP BY cod_evento) as max_evento_evento
                                 WHERE max_evento_evento.cod_evento = evento_evento.cod_evento
                                   AND max_evento_evento.timestamp  = evento_evento.timestamp) as evento_evento
                         ON evento_evento.cod_evento = evento.cod_evento
                 INNER JOIN folhapagamento'||stEntidade||'.configuracao_evento_caso
                         ON configuracao_evento_caso.cod_evento = evento_evento.cod_evento
                        AND configuracao_evento_caso.timestamp  = evento_evento.timestamp
                        AND configuracao_evento_caso.cod_configuracao = 3
                 INNER JOIN folhapagamento'||stEntidade||'.tipo_evento_configuracao_media
                         ON tipo_evento_configuracao_media.cod_configuracao = 3
                        AND tipo_evento_configuracao_media.timestamp = configuracao_evento_caso.timestamp 
                        AND tipo_evento_configuracao_media.cod_evento = configuracao_evento_caso.cod_evento
                        AND tipo_evento_configuracao_media.cod_caso = configuracao_evento_caso.cod_caso
                      WHERE evento_complementar_calculado.valor > 0 
                   ORDER BY evento_complementar_calculado.cod_evento';
       EXECUTE stSql;
       END IF;
    ELSE 
       -- INSERE INFORMAÇÕES DA FOLHA SALÁRIO NA TABELA TEMPORÁRIO DA COMPETÊNCIA DE JANEIRO ATÉ A COMPETÊNCIA DE CALCULO
       -- APENAS PONTO FIXO
       IF inCodPeriodoMovimentacaoInicial IS NOT NULL THEN
       stSql := ' INSERT INTO tmp_registro_evento_13 
                     SELECT evento_calculado.cod_evento
                          , COALESCE(evento_calculado.valor,0.00) as valor
                          , COALESCE(evento_calculado.quantidade,0.00) as quantidade
                          , registro_evento_periodo.cod_periodo_movimentacao
                          , evento.natureza
                          , evento.fixado
                          , evento_evento.unidade_quantitativa
                          , ''evento_calculado'' as lido_de
                       FROM folhapagamento'||stEntidade||'.evento_calculado
                 INNER JOIN folhapagamento'||stEntidade||'.registro_evento_periodo
                         ON registro_evento_periodo.cod_registro = evento_calculado.cod_registro
                        AND registro_evento_periodo.cod_contrato = '||incodContrato||'
                        AND registro_evento_periodo.cod_periodo_movimentacao BETWEEN '||inCodPeriodoMovimentacaoInicial||' AND '||inCodPeriodoMovimentacao||'
                 INNER JOIN folhapagamento'||stEntidade||'.evento
                         ON evento.cod_evento = evento_calculado.cod_evento
                        AND evento.natureza IN ( ''P'',''D'' )     
                        AND evento.tipo = ''F''    
                 INNER JOIN ( SELECT COALESCE(unidade_quantitativa,0) as unidade_quantitativa
                                   , evento_evento.cod_evento   
                                   , evento_evento.timestamp    
                                FROM folhapagamento'||stEntidade||'.evento_evento
                                   , (  SELECT cod_evento
                                             , max(timestamp) as timestamp
                                          FROM folhapagamento'||stEntidade||'.evento_evento
                                      GROUP BY cod_evento) as max_evento_evento
                                 WHERE max_evento_evento.cod_evento = evento_evento.cod_evento
                                   AND max_evento_evento.timestamp  = evento_evento.timestamp) as evento_evento
                         ON evento_evento.cod_evento = evento.cod_evento
                 INNER JOIN folhapagamento'||stEntidade||'.configuracao_evento_caso
                         ON configuracao_evento_caso.cod_evento = evento_evento.cod_evento
                        AND configuracao_evento_caso.timestamp  = evento_evento.timestamp
                        AND configuracao_evento_caso.cod_configuracao = 3
                 INNER JOIN folhapagamento'||stEntidade||'.tipo_evento_configuracao_media
                         ON tipo_evento_configuracao_media.cod_configuracao = 3
                        AND tipo_evento_configuracao_media.timestamp = configuracao_evento_caso.timestamp 
                        AND tipo_evento_configuracao_media.cod_evento = configuracao_evento_caso.cod_evento
                        AND tipo_evento_configuracao_media.cod_caso = configuracao_evento_caso.cod_caso
                      WHERE evento_calculado.valor > 0 
                   ORDER BY evento_calculado.cod_evento';
       EXECUTE stSql;
       END IF;

       -- INSERE INFORMAÇÕES DA FOLHA COMPLEMENTAR NA TABELA TEMPORÁRIO DA COMPETÊNCIA DE JANEIRO ATÉ A COMPETÊNCIA DE CALCULO
       -- APENAS PONTO FIXO
       IF inCodPeriodoMovimentacaoInicial IS NOT NULL THEN
       stSql := ' INSERT INTO tmp_registro_evento_13 
                     SELECT evento_complementar_calculado.cod_evento
                          , COALESCE(evento_complementar_calculado.valor,0.00) as valor
                          , COALESCE(evento_complementar_calculado.quantidade,0.00) as quantidade
                          , registro_evento_complementar.cod_periodo_movimentacao
                          , evento.natureza
                          , evento.fixado
                          , evento_evento.unidade_quantitativa
                          , ''evento_complementar_calculado'' as lido_de
                       FROM folhapagamento'||stEntidade||'.evento_complementar_calculado
                 INNER JOIN folhapagamento'||stEntidade||'.registro_evento_complementar
                         ON registro_evento_complementar.cod_registro = evento_complementar_calculado.cod_registro
                        AND registro_evento_complementar.cod_evento = evento_complementar_calculado.cod_evento
                        AND registro_evento_complementar.cod_configuracao = evento_complementar_calculado.cod_configuracao
                        AND registro_evento_complementar.timestamp = evento_complementar_calculado.timestamp_registro
                        AND registro_evento_complementar.cod_contrato = '||inCodContrato||'
                        AND registro_evento_complementar.cod_periodo_movimentacao BETWEEN '||inCodPeriodoMovimentacaoInicial||' AND '||inCodPeriodoMovimentacao||'
                 INNER JOIN folhapagamento'||stEntidade||'.evento
                         ON evento.cod_evento = evento_complementar_calculado.cod_evento
                        AND evento.natureza IN ( ''P'',''D'' )        
                        AND evento.tipo = ''F'' 
                 INNER JOIN ( SELECT COALESCE(unidade_quantitativa,0) as unidade_quantitativa
                                   , evento_evento.cod_evento   
                                   , evento_evento.timestamp    
                                FROM folhapagamento'||stEntidade||'.evento_evento
                                   , (  SELECT cod_evento
                                             , max(timestamp) as timestamp
                                          FROM folhapagamento'||stEntidade||'.evento_evento
                                      GROUP BY cod_evento) as max_evento_evento
                                 WHERE max_evento_evento.cod_evento = evento_evento.cod_evento
                                   AND max_evento_evento.timestamp  = evento_evento.timestamp) as evento_evento
                         ON evento_evento.cod_evento = evento.cod_evento
                 INNER JOIN folhapagamento'||stEntidade||'.configuracao_evento_caso
                         ON configuracao_evento_caso.cod_evento = evento_evento.cod_evento
                        AND configuracao_evento_caso.timestamp  = evento_evento.timestamp
                        AND configuracao_evento_caso.cod_configuracao = 3
                 INNER JOIN folhapagamento'||stEntidade||'.tipo_evento_configuracao_media
                         ON tipo_evento_configuracao_media.cod_configuracao = 3
                        AND tipo_evento_configuracao_media.timestamp = configuracao_evento_caso.timestamp 
                        AND tipo_evento_configuracao_media.cod_evento = configuracao_evento_caso.cod_evento
                        AND tipo_evento_configuracao_media.cod_caso = configuracao_evento_caso.cod_caso
                      WHERE evento_complementar_calculado.valor > 0 
                   ORDER BY evento_complementar_calculado.cod_evento';
       EXECUTE stSql;
       END IF;
    END IF;

    stSql := 'SELECT count(cod_evento) FROM tmp_registro_evento_13';

    OPEN crCursor FOR EXECUTE stSql;
         FETCH crCursor INTO inNrRegistros ;
    CLOSE crCursor;


  IF inNrRegistros > 0 THEN

     -- CRIA TABELA TEMPORÁRIA ONDE SERÃO AGRUPADOS OS EVENTOS PARA O CÁLCULO DA MÉDIAS
     CREATE TEMPORARY TABLE tmp_registro_evento_13_medias 
           (cod_evento               INTEGER,
            codigo                   VARCHAR,
            descricao                VARCHAR,
            unidade_quantitativa     NUMERIC(14,2),
            fixado                   VARCHAR,
            formula                  VARCHAR,
            valor                    NUMERIC(14,2),
            quantidade               NUMERIC(14,2),
            avos                     INTEGER,
            nr_ocorrencias           INTEGER
           );

     stSql := ' INSERT INTO tmp_registro_evento_13_medias 
              SELECT  distinct tmp_registro_evento_13.cod_evento as cod_evento
                     , fpe.codigo                                    as codigo
                     , fpe.descricao                                 as descricao
                     , COALESCE(fpee.unidade_quantitativa,0)         as unidade_quantitativa
                     , fpe.fixado                                    as fixado
                     , ''0.0.0''                                     as formula
                     , 0.00                                          as valor
                     , 0.00                                          as quantidade
                     , 0                                             as avos
                     , 0                                             as nr_ocorrencias
                FROM tmp_registro_evento_13

                LEFT OUTER JOIN folhapagamento'||stEntidade||'.evento  as fpe 
                  ON fpe.cod_evento = tmp_registro_evento_13.cod_evento

                LEFT OUTER JOIN( SELECT max_evento.cod_evento, 
                                        max_evento.timestamp, 
                                        COALESCE(unidade_quantitativa,0) as unidade_quantitativa
                                   FROM folhapagamento'||stEntidade||'.evento_evento,
                                        (SELECT cod_evento, max(timestamp) as timestamp
                                           FROM folhapagamento'||stEntidade||'.evento_evento
                                       GROUP BY cod_evento) as max_evento
                                  WHERE max_evento.cod_evento = evento_evento.cod_evento
                                    AND max_evento.timestamp  = evento_evento.timestamp
                                  ORDER BY evento_evento.cod_evento, evento_evento.timestamp desc) as fpee
                  ON fpee.cod_evento = fpe.cod_evento
            ORDER BY tmp_registro_evento_13.cod_evento';

     EXECUTE stSql;

     stSql := 'SELECT COUNT(cod_evento) 
                  FROM tmp_registro_evento_13_medias
                 WHERE formula IS NOT NULL';
                
     OPEN crCursor FOR EXECUTE stSql;
          FETCH crCursor INTO inNrRegistros;
     CLOSE crCursor;

     --SE POSSUI MAIS DE UM REGISTRO CONTINUA PROCESSO
     IF inNrRegistros IS NOT NULL THEN

        stSql := 'SELECT * FROM tmp_registro_evento_13_medias
                  WHERE formula is not null  ';
        -- SE EXISTE AO MENOS UM REGISTRO NA LISTA COM FÓRMULA PARA CALCULO
        FOR reRegistro1 IN  EXECUTE stSql LOOP

           inCodEvento := reRegistro1.cod_evento;
           inCodEvento := criarBufferInteiro( 'incodevento', reRegistro1.cod_evento );
           -- BUSCA A FORMULA DE MEDIA PARA O EVENTO - FOI UTILIZADA A MESMA FORMULA PASSANDO A CONFIGURAÇÃO DO DÉCIMO
           stFormula := pegaFormulaMediaFerias(inCodEvento,3,inCodSubDivisao,inCodFuncao,inCodEspecialidade);
           -- executa formula
           IF stFormula IS NOT NULL THEN
               stExecutaFormula := executaGCNumerico( stFormula );

               nuExecutaFormula := to_number( stExecutaFormula ,'99999999999.99' );
               IF nuExecutaFormula != 0 THEN 
                  IF reRegistro1.fixado = 'V' THEN
                       nuValor      := arredondar(nuExecutaFormula,2);
                       nuQuantidade := 0;
                  ELSE
                       nuQuantidade := arredondar(nuExecutaFormula,2);
                       nuValor      := 0;
                  END IF;


                  -- INSERI REGISTRO DE EVENTO DE 13º  - ADIANTAMENTO

                  IF stDesdobramento  = 'A' THEN
                      IF boGerandoRescisao = 'f' THEN
                         boGravouRegistro := gravaRegistroEventoDecimo( inCodContrato,inCodPeriodoMovimentacao,inCodEvento, nuValor, nuQuantidade, stDesdobramento );
                      ELSE
                         stDesdobramentoRescisao := 'D';
                         boGravouRegistro := gravaRegistroEventoRescisao( inCodContrato,inCodPeriodoMovimentacao,inCodEvento, nuValor, nuQuantidade, stDesdobramentoRescisao );
                      END IF;
                  END IF;
                  -- INSERI REGISTRO DE EVENTO DE 13º  - DÉCIMO 
                  IF stDesdobramento = 'D'  THEN
                      IF boGerandoRescisao = 'f' THEN
                         boGravouRegistro := gravaRegistroEventoDecimo( inCodContrato,inCodPeriodoMovimentacao,inCodEvento, nuValor, nuQuantidade, stDesdobramento );
                      ELSE
                         stDesdobramentoRescisao := 'D';
                         boGravouRegistro := gravaRegistroEventoRescisao( inCodContrato,inCodPeriodoMovimentacao,inCodEvento, nuValor, nuQuantidade, stDesdobramentoRescisao );
                      END IF;

                  END IF;
                  -- INSERI REGISTRO DE EVENTO DE 13º  - COMPLEMENTAÇÃO
                  IF stDesdobramento = 'C' THEN
                      IF boGerandoRescisao = 'f' THEN
                         boGravouRegistro := gravaRegistroEventoDecimo( inCodContrato,inCodPeriodoMovimentacao,inCodEvento, nuValor, nuQuantidade, stDesdobramento );
                      ELSE
                         stDesdobramentoRescisao := 'D';
                         boGravouRegistro := gravaRegistroEventoRescisao( inCodContrato,inCodPeriodoMovimentacao,inCodEvento, nuValor, nuQuantidade, stDesdobramentoRescisao );
                      END IF;


                  END IF;
               END IF;
           END IF;
        END LOOP;
        -- VERIFICAÇÃO PARA EFETUAR OU NÃO O REGISTRO DE EVENTO DE DESCONTO DE ADIANTAMENTO DE DÉCIMO
        --IF (stDesdobramento = 'D') THEN
        --   stAnoAdiantamento := SUBSTR(dtFinalPeriodo,1,4);
        --   boRetorno := verificaAdiantamento(stAnoAdiantamento);
        --   IF boRetorno is TRUE THEN
        --     boRetorno := inserirEventoAutomaticoDescontoAdiantamento(1,stDesdobramento);
        --   END IF;
        --END IF;

     END IF;
  END IF;

 IF inControleExecucaoRescisaoDecimo < 1 THEN
    boRetorno := deletarTemporariasDoCalculo(true);
 END IF; 

RETURN TRUE; 
END;
$$LANGUAGE 'plpgsql';

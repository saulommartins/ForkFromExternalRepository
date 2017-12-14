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

--/*
--
-- script de funcao PLSQL
--
-- URBEM Solugues de Gestco Pzblica Ltda
-- www.urbem.cnm.org.br
--
-- $Id: geraRegistroFerias.plsql 66263 2016-08-03 21:43:52Z michel $
--
-- Caso de uso: uc-04.04.22
-- Caso de uso: uc-04.05.53
--
-- Objetivo: efetua o levantamento necessario para a geracao do registro
-- de ferias conforme a indicacao do tipo de media de cada evento
--
--*/

CREATE OR REPLACE FUNCTION geraRegistroFerias(integer,integer,varchar,varchar) RETURNS BOOLEAN as $$

DECLARE

inCodContrato                       ALIAS FOR $1;
inCodPeriodoMovimentacaoParametro   ALIAS FOR $2;
stExercicioAtual                    ALIAS FOR $3;
stEntidadeParametro                 ALIAS FOR $4;
stEntidade                          VARCHAR := '';

inCodContrato0                   INTEGER := 0;
stSql                            VARCHAR := '';
stSql1                           VARCHAR := '';
stSql2                           VARCHAR := '';
crCursor                         REFCURSOR;
reRegistro                       RECORD;
reRegistro1                      RECORD;
reRegistroRescisao               RECORD;
boRetorno                        BOOLEAN := TRUE;
stRetorno                        VARCHAR := '';
mensagemerro                     VARCHAR := 'erro';

dtInicialPeriodo                 DATE;
dtFinalPeriodo                   DATE;
dtInicialAquisitivo              DATE;
dtFinalAquisitivo                DATE;

inDiasPeriodo                    INTEGER := 0;
inDiasAquisitivoPeriodoInicial   INTEGER := 0;
inCodPeriodoInicialLeitura       INTEGER := 0;
inCodPeriodoMovimentacao         INTEGER := 0;

stSituacao                       VARCHAR := 'f';

stDataFinalCompetencia           VARCHAR := '';

inCodEvento                      INTEGER := 0;
stCodigo                         VARCHAR:= '';
stDescricao                      VARCHAR := '';
nuUnidadeQuantitativa            NUMERIC := 0.00;

inCodRegime                      INTEGER := 1;
InCodSubDivisao                  INTEGER := 1;
inCodFuncao                      INTEGER := 1;
inCodEspecialidade               INTEGER := 1;

inNrRegistros                    INTEGER := 0;

stFormula                        VARCHAR := '';
stExecutaFormula                 VARCHAR := '';
nuExecutaFormula                 NUMERIC := 0;
nuValor                          NUMERIC := 0;
nuQuantidade                     NUMERIC := 0;
nuValorFormula                   NUMERIC := 0;
nuQuantidadeFormula              NUMERIC := 0;
boGravouRegistro                 BOOLEAN;

inAvos                           INTEGER := 12;
inCodForma                       INTEGER ;
inDiasGozoFeriasContrato         INTEGER ;
inDiasAbonoContrato              INTEGER ;
stAnoPagamento                   VARCHAR ;
stMesPAgamento                   VARCHAR ;
stInicio                         VARCHAR := '';
stFim                            VARCHAR := '';
inDiasGozo                       INTEGER := 0;
inDiasAbono                      INTEGER := 0;
inContador                       INTEGER := 0;

nuDiasAbono                      NUMERIC := 0.00;
nuProporcaoForma                 NUMERIC := 0.00;
nuProporcaoDivisao               NUMERIC := 0.00;

inGeraGozo                       INTEGER;
inGeraAbono                      INTEGER;
inGeraAdiantamento               INTEGER;

stDesdobramento                  VARCHAR := 'F';

nuTotalValor                     NUMERIC := 0.00;
nuTotalQuantidade                NUMERIC := 0.00;
boNaoGerarDesconto               BOOLEAN := FALSE;

inControleExecucaoRescisaoFerias INTEGER := 0;
boGerandoRescisao                VARCHAR :='f';
stDataRescisao                   VARCHAR :='';
stDataComparacao                 VARCHAR :='';
stContagemInicial                VARCHAR :='';
inGeraRegistroRescisao           INTEGER;

inQtdDiasAnoCompetencia          INTEGER := 0;

BEGIN
    stEntidade := criarBufferEntidade(stEntidadeParametro);
    -- futuramente, quando a chamada desta funcao FOR tambem para a 
    -- geracao de diferenca de ferias ( regeracao das ferias quando estas tiverem 
    -- gozo de ferias em mes diferente do cadastramento das ferias ) 
    inControleExecucaoRescisaoFerias := recuperarBufferInteiro('inControleExecucaoRescisaoFerias');

    inContador := countBufferInteiro('inCodPeriodoMovimentacao');
    IF inContador = 0 THEN
        inCodPeriodoMovimentacao := criarBufferInteiro('inCodPeriodoMovimentacao',inCodPeriodoMovimentacaoParametro);
    ELSE
        inCodPeriodoMovimentacao := inCodPeriodoMovimentacaoParametro;
    END IF;
    
    stDataFinalCompetencia := pega0DataFinalCompetenciaDoPeriodoMovimento(inCodPeriodoMovimentacao);

    IF inControleExecucaoRescisaoFerias = 1 THEN
        boGerandoRescisao       := 't';
    ELSE
       stDataFinalCompetencia  := criarBufferTexto(  'stDataFinalCompetencia',  stDataFinalCompetencia );
       inCodContrato0          := criarBufferInteiro( 'inCodContrato' , inCodContrato );
    END IF;
    boGerandoRescisao := criarBufferTexto('boGerandoRescisao',boGerandoRescisao); 

    inCodSubDivisao    := pega0SubDivisaoDoContratoNaData   ( inCodContrato, stDataFinalCompetencia );
    inCodFuncao        := pega0FuncaoDoContratoNaData       ( inCodContrato, stDataFinalCompetencia );
    inCodEspecialidade := pega0EspecialidadeDoContratoNaData( inCodContrato, stDataFinalCompetencia );

    -- *******************busca os dados do cadastro de ferias
    -- busca os dados do cadastro das ferias e retorna o periodo aquisitivo
    -- e demais dados necessarios ao desdobramento e gravacao dos registros 
    -- as situacoes onde deve haver calculo de ferias sao diversas : pagamento 
    -- todo adiantado de ferias, mes com dias de gozo de ferias, considerando o inicio do gozo, 
    -- mes com dias de gozo considerando a data final de gozo.
    inGeraRegistroRescisao := countBufferInteiro('inGeraRegistroRescisao') ;
    IF inGeraRegistroRescisao = 1 THEN
        stDataComparacao := recuperarBufferTexto('stDataRescisao');
    ELSE
        stDataComparacao := stDataFinalCompetencia;
    END IF;

    stSql := ' SELECT ferias.dt_inicial_aquisitivo
                    , ferias.dt_final_aquisitivo
                    , ferias.cod_forma
                    , ferias.dias_ferias
                    , ferias.dias_abono
                    , to_char(lancamento_ferias.dt_inicio,''yyyy-mm-dd'') as dt_inicio
                    , to_char(lancamento_ferias.dt_fim,''yyyy-mm-dd'')    as dt_fim
                    , forma_pagamento_ferias.dias     
                    , forma_pagamento_ferias.abono     
                    , lancamento_ferias.ano_competencia
                    , lancamento_ferias.mes_competencia
                    , lancamento_ferias.cod_ferias
                 FROM pessoal'||stEntidade||'.ferias                 as ferias

          INNER JOIN pessoal'||stEntidade||'.lancamento_ferias       as lancamento_ferias
                  ON ferias.cod_ferias = lancamento_ferias.cod_ferias

          INNER JOIN pessoal'||stEntidade||'.forma_pagamento_ferias  as forma_pagamento_ferias   
                  ON forma_pagamento_ferias.cod_forma = ferias.cod_forma

               WHERE cod_contrato = '||inCodContrato||'
                 AND ( 
                      (     substr('|| quote_literal(stDataComparacao) ||',1,4) = lancamento_ferias.ano_competencia
                        AND substr('|| quote_literal(stDataComparacao) ||',6,2) = lancamento_ferias.mes_competencia
                      )
                      OR
                      ( substr('|| quote_literal(stDataComparacao) ||',1,7) = substr(lancamento_ferias.dt_inicio::varchar,1,7)
                      )
                      OR
                      ( substr('|| quote_literal(stDataComparacao) ||',1,7) = substr(lancamento_ferias.dt_fim::varchar,1,7)
                      )
                      OR 
                      ( substr('|| quote_literal(stDataComparacao) ||',1,7) 
                         BETWEEN substr(lancamento_ferias.dt_inicio::varchar,1,7)
                             AND substr(lancamento_ferias.dt_fim::varchar,1,7)
                      )
                     )
            ORDER BY dt_inicial_aquisitivo desc
              LIMIT 1
             ';
             -- testar - verificar ainda o caso de gozo de ferias que inicia em janeiro
             --  e termina em março. Fevereiro precisa de tratamento especial. 

    OPEN crCursor FOR EXECUTE stSql;
         FETCH crCursor INTO dtInicialAquisitivo
                           , dtFinalAquisitivo
                           , inCodForma
                           , inDiasGozoFeriasContrato 
                           , inDiasAbonoContrato
                           , stInicio
                           , stFim
                           , inDiasGozo
                           , inDiasAbono
                           , stAnoPagamento
                           , stMesPagamento
         ;
    CLOSE crCursor;
    stAnoPagamento := criarBufferTexto  ('stAnoPagamento',stAnoPagamento);
    stMesPagamento := criarBufferTexto  ('stMesPagamento',stMesPagamento);
    inDiasAbono    := criarBufferInteiro('inDiasAbono'   ,inDiasAbono);
    stInicio       := criarBufferTexto  ('stInicio'      ,stInicio);
    stFim          := criarBufferTexto  ('stFim'         ,stFim);

    inQtdDiasAnoCompetencia := 365;
    IF dtInicialAquisitivo IS NOT NULL THEN
        inQtdDiasAnoCompetencia := selectIntoInteger('SELECT ( ((('''||dtInicialAquisitivo||'''::date) + INTERVAL ''1 year'')::date) - '''||dtInicialAquisitivo||'''::date )');
    END IF;

    IF (dtFinalAquisitivo-dtInicialAquisitivo+1) < inQtdDiasAnoCompetencia THEN
        IF inDiasAbono > 0 THEN
            inDiasAbono := inDiasGozoFeriasContrato * inDiasAbono / (inDiasGozo+inDiasAbono);
        END IF;
        inDiasGozo := inDiasGozoFeriasContrato;
    END IF;

    IF dtInicialAquisitivo IS NULL THEN
        stContagemInicial := selectIntoVarchar
                    ('SELECT valor
                        FROM administracao.configuracao
                       WHERE parametro = ''dtContagemInicial'||stEntidade||'
                         AND exercicio = '|| quote_literal(stExercicioAtual) ||'
                         AND cod_modulo = 22');
        IF stContagemInicial = 'dtPosse' THEN
            dtInicialAquisitivo := selectIntoVarchar('SELECT dt_posse
                                                        FROM pessoal'||stEntidade||'.contrato_servidor_nomeacao_posse
                                                           , (SELECT cod_contrato
                                                                   , max(timestamp) as timestamp
                                                                FROM pessoal'||stEntidade||'.contrato_servidor_nomeacao_posse
                                                            GROUP BY cod_contrato
                                                             ) AS max_contrato_servidor_nomeacao_posse
                                                       WHERE contrato_servidor_nomeacao_posse.cod_contrato = max_contrato_servidor_nomeacao_posse.cod_contrato
                                                         AND contrato_servidor_nomeacao_posse.timestamp = max_contrato_servidor_nomeacao_posse.timestamp
                                                         AND contrato_servidor_nomeacao_posse.cod_contrato = '||inCodContrato);
        END IF;
        IF stContagemInicial = 'dtAdmissao' THEN
            dtInicialAquisitivo := selectIntoVarchar('SELECT dt_admissao
                                                        FROM pessoal'||stEntidade||'.contrato_servidor_nomeacao_posse
                                                           , (SELECT cod_contrato
                                                                   , max(timestamp) as timestamp
                                                                FROM pessoal'||stEntidade||'.contrato_servidor_nomeacao_posse
                                                            GROUP BY cod_contrato
                                                             ) AS max_contrato_servidor_nomeacao_posse
                                                       WHERE contrato_servidor_nomeacao_posse.cod_contrato = max_contrato_servidor_nomeacao_posse.cod_contrato
                                                         AND contrato_servidor_nomeacao_posse.timestamp = max_contrato_servidor_nomeacao_posse.timestamp
                                                         AND contrato_servidor_nomeacao_posse.cod_contrato = '||inCodContrato);
        END IF;
        IF stContagemInicial = 'dtNomeacao' THEN
            dtInicialAquisitivo := selectIntoVarchar('SELECT dt_nomeacao
                                                        FROM pessoal'||stEntidade||'.contrato_servidor_nomeacao_posse
                                                           , (SELECT cod_contrato
                                                                   , max(timestamp) as timestamp
                                                                FROM pessoal'||stEntidade||'.contrato_servidor_nomeacao_posse
                                                            GROUP BY cod_contrato
                                                             ) AS max_contrato_servidor_nomeacao_posse
                                                       WHERE contrato_servidor_nomeacao_posse.cod_contrato = max_contrato_servidor_nomeacao_posse.cod_contrato
                                                         AND contrato_servidor_nomeacao_posse.timestamp = max_contrato_servidor_nomeacao_posse.timestamp
                                                         AND contrato_servidor_nomeacao_posse.cod_contrato = '||inCodContrato);
        END IF;
    END IF;

    -- o sql ja verifica se deve ou nao gerar o registro de ferias.
    -- caso nao coincida o mes e ano ou o periodo de gozo cai fora da funcao.
    IF dtInicialAquisitivo IS NOT NULL THEN
        -- necessario verificar a proporcionalizacao do registro de eventos para este contrato
        -- ou seja, proporcionalizar o ponto de salario para o nr. de dias de gozo de ferias
        -- dentro desta competencia . Ver isto apos a geracao da variavei inGeraGozo

        stSql := 'SELECT *
                    FROM folhapagamento'||stEntidade||'.periodo_movimentacao as pm
              INNER JOIN folhapagamento'||stEntidade||'.periodo_movimentacao_situacao as pms
                      ON pms.cod_periodo_movimentacao = pm.cod_periodo_movimentacao
                   WHERE '|| quote_literal(dtInicialAquisitivo) ||' between dt_inicial AND dt_final';

        IF boGerandoRescisao = 'f' THEN
            stSql := stSql || '   AND pms.situacao = '|| quote_literal(stSituacao);
        END IF;
        -- busca o periodo de movimentacao para inicio de avaliacao

        OPEN crCursor FOR EXECUTE stSql;
             FETCH crCursor INTO inCodPeriodoInicialLeitura, dtInicialPeriodo,dtFinalPeriodo ;
        CLOSE crCursor;
        IF inCodPeriodoInicialLeitura IS NOT NULL THEN
            inDiasPeriodo := ( dtFinalPeriodo - dtInicialPeriodo) +1 ;

            -- gera o nr. de dias de ferias levando em conta o periodo de efetividade
            -- e a data inicial do periodo aquisitivo.
            inDiasAquisitivoPeriodoInicial := ((dtFinalPeriodo - dtInicialAquisitivo )+1 );

            -- tratamento especial para mes de fevereiro
            IF inDiasAquisitivoPeriodoInicial < 15 OR ( inDiasPeriodo < 30 AND inDiasAquisitivoPeriodoInicial = 15 ) THEN
               -- ignora o periodo de movimentacao inicial e comeca no proximo

               inCodPeriodoInicialLeitura := inCodPeriodoInicialLeitura + 1;

               -- aqui estou apenas passando para o proximo periodo mas nao estou 
               -- sequer testando se este proximo existe ou se esta pendente de 
               -- fechamento ainda.

            END if;
    
            -- verifica o nr. de avos ( item acessorio - ainda nao utilizado )
            IF ( inCodPeriodoInicialLeitura + 11) > ( inCodPeriodoMovimentacao - 1 ) THEN
                inAvos := ( inCodPeriodoMovimentacao - inCodPeriodoInicialLeitura );
            END IF;
            -- tem que ver o caso de periodo aquisitivo que ultrapassa o mes atual
            inAvos := criarBufferInteiro( 'inAvos', inAvos );  

            --*****************************************************
            -- criacao de temporario que ira conter a lista dos eventos dos meses de 
            -- leitura (periodo aquisitivo) e mais o "ponto fixo" do servidor, do
            -- periodo atual.
            --
            -- inicialmente, cria a tabela com a leitura das calculadas em salario
            -- do periodo aquisitivo pois representam o maior volume

            CREATE TEMPORARY TABLE tmp_registro_evento_ferias 
                     (cod_evento               INTEGER,
                      valor                    NUMERIC(14,2),
                      quantidade               NUMERIC(14,2),
                      cod_periodo_movimentacao INTEGER,
                      natureza                 VARCHAR,
                      fixado                   VARCHAR,
                      unidade_quantitativa     NUMERIC(14,2),
                      proporcao_abono          BOOLEAN,
                      parcela                  INTEGER,
                      lido_de                  VARCHAR
                     );

            -- Nota para futuro - observar que nos casos em que a quantidade do registro
            -- do evento FOR alterada em relacao a gravacao da quantidade no calculado.
            -- Para a execucao destas media seria necessario pegar a qtd do registro.

            -- salario
            stSql := ' INSERT INTO tmp_registro_evento_ferias 
                          SELECT
                                 fpec.cod_evento                 as cod_evento
                               , COALESCE(fpec.valor,0.00)       as valor
                               , COALESCE(fpec.quantidade,0.00)  as quantidade
                               , fprepe.cod_periodo_movimentacao as cod_periodo_movimentacao
                               , fpe.natureza                    as natureza
                               , fpe.fixado                      as fixado
                               , fpee.unidade_quantitativa       as unidade_quantitativa
                               , fpcec.proporcao_abono           as proporcao_abono
                               , (SELECT parcela FROM folhapagamento'||stEntidade||'.registro_evento_parcela WHERE fprepe.cod_registro     = registro_evento_parcela.cod_registro) as parcela
                               , ''evento_calculado''        as lido_de

                            FROM folhapagamento'||stEntidade||'.registro_evento_periodo   as fprepe

                      INNER JOIN folhapagamento'||stEntidade||'.evento_calculado           as fpec
                              ON fpec.cod_registro = fprepe.cod_registro

                      INNER JOIN folhapagamento'||stEntidade||'.evento                    as fpe
                              ON fpe.cod_evento = fpec.cod_evento
                             AND fpe.natureza IN ( ''P'',''D'' )

                      INNER JOIN ( SELECT distinct ON (cod_evento) cod_evento 
                                        , timestamp
                                        , COALESCE(unidade_quantitativa,0) as unidade_quantitativa
                                     FROM folhapagamento'||stEntidade||'.evento_evento
                                 ORDER BY evento_evento.cod_evento, evento_evento.timestamp desc
                                 ) as fpee
                              ON fpee.cod_evento = fpe.cod_evento

                      INNER JOIN folhapagamento'||stEntidade||'.configuracao_evento_caso   as fpcec
                              ON fpcec.cod_evento = fpee.cod_evento
                             AND fpcec.timestamp  = fpee.timestamp
                             AND fpcec.cod_configuracao = 2

                      INNER JOIN folhapagamento'||stEntidade||'.tipo_evento_configuracao_media as fptecm
                              ON fptecm.cod_configuracao = 2
                             AND fptecm.timestamp = fpcec.timestamp 
                             AND fptecm.cod_evento = fpcec.cod_evento
                             AND fptecm.cod_caso = fpcec.cod_caso

                      INNER JOIN ( SELECT cod_periodo_movimentacao
                                        , max(timestamp) as timestamp 
                                     FROM folhapagamento'||stEntidade||'.periodo_movimentacao_situacao
                                 GROUP BY 1 
                                 ) as fppms
                              ON fppms.cod_periodo_movimentacao = fprepe.cod_periodo_movimentacao
                             AND ( SELECT situacao 
                                     FROM folhapagamento'||stEntidade||'.periodo_movimentacao_situacao as fppms_
                                    WHERE fppms_.cod_periodo_movimentacao = fppms.cod_periodo_movimentacao
                                      AND fppms_.timestamp = fppms.timestamp
                                 ) = ''f''

                           WHERE fprepe.cod_periodo_movimentacao 
                                   between '||inCodPeriodoInicialLeitura||' 
                                       AND '||inCodPeriodoInicialLeitura + 11||'

                             AND fprepe.cod_registro = fpec.cod_registro
                             AND fprepe.cod_contrato = '||inCodContrato||'

                             AND fpec.valor > 0
                        ORDER BY fpec.cod_evento';

            EXECUTE stSql;

            -- complementar
            stSql := ' INSERT INTO tmp_registro_evento_ferias 
                          SELECT
                                 fpecc.cod_evento                 as cod_evento
                               , COALESCE(fpecc.valor,0.00)       as valor
                               , COALESCE(fpecc.quantidade,0.00)  as quantidade
                               , fprec.cod_periodo_movimentacao   as cod_periodo_movimentacao
                               , fpe.natureza                     as natureza
                               , fpe.fixado                       as fixado
                               , fpee.unidade_quantitativa        as unidade_quantitativa
                               , fpcec.proporcao_abono            as proporcao_abono
                               , registro_evento_complementar_parcela.parcela 
                               , ''evento_calculado_complementar''        as lido_de

                            FROM folhapagamento'||stEntidade||'.registro_evento_complementar   as fprec

                       LEFT JOIN folhapagamento'||stEntidade||'.registro_evento_complementar_parcela
                              ON fprec.cod_registro     = registro_evento_complementar_parcela.cod_registro
                             AND fprec.cod_evento       = registro_evento_complementar_parcela.cod_evento
                             AND fprec.cod_configuracao = registro_evento_complementar_parcela.cod_configuracao
                             AND fprec.timestamp        = registro_evento_complementar_parcela.timestamp

                      INNER JOIN folhapagamento'||stEntidade||'.evento_complementar_calculado     as fpecc
                              ON fpecc.cod_registro = fprec.cod_registro

                      INNER JOIN folhapagamento'||stEntidade||'.evento                    as fpe
                              ON fpe.cod_evento = fpecc.cod_evento
                             AND fpe.natureza IN ( ''P'',''D'' )

                      INNER JOIN ( SELECT distinct ON (cod_evento) cod_evento 
                                        , timestamp
                                        , COALESCE(unidade_quantitativa,0) as unidade_quantitativa
                                     FROM folhapagamento'||stEntidade||'.evento_evento
                                 ORDER BY evento_evento.cod_evento, evento_evento.timestamp desc
                                 ) as fpee
                              ON fpee.cod_evento = fpe.cod_evento

                      INNER JOIN folhapagamento'||stEntidade||'.configuracao_evento_caso   as fpcec
                              ON fpcec.cod_evento = fpee.cod_evento
                             AND fpcec.timestamp  = fpee.timestamp
                             AND fpcec.cod_configuracao = 2

                      INNER JOIN folhapagamento'||stEntidade||'.tipo_evento_configuracao_media as fptecm
                              ON fptecm.cod_configuracao = 2
                             AND fptecm.timestamp = fpcec.timestamp 
                             AND fptecm.cod_evento = fpcec.cod_evento
                             AND fptecm.cod_caso = fpcec.cod_caso

                      INNER JOIN ( SELECT cod_periodo_movimentacao
                                        , max(timestamp) as timestamp 
                                     FROM folhapagamento'||stEntidade||'.periodo_movimentacao_situacao
                                 GROUP BY 1 
                                 ) as fppms
                              ON fppms.cod_periodo_movimentacao = fprec.cod_periodo_movimentacao
                             AND ( SELECT situacao 
                                     FROM folhapagamento'||stEntidade||'.periodo_movimentacao_situacao as fppms_
                                    WHERE fppms_.cod_periodo_movimentacao = fppms.cod_periodo_movimentacao
                                      AND fppms_.timestamp = fppms.timestamp
                                 ) = ''f''

                           WHERE fprec.cod_periodo_movimentacao 
                                    between '||inCodPeriodoInicialLeitura||' 
                                        AND '||inCodPeriodoInicialLeitura + 11||'
                             AND fprec.cod_registro             = fpecc.cod_registro
                             AND fprec.cod_contrato             = '||incodContrato||'

                             AND fpecc.valor > 0 
                        ORDER BY fpecc.cod_evento';
            EXECUTE stSql;

            -- leitura e insercao do "ponto fixo" na tabela temporaria
            stSql := ' INSERT INTO tmp_registro_evento_ferias 
                          SELECT
                                 registro_evento.cod_evento
                               , COALESCE(registro_evento.valor,0.00)       as valor
                               , COALESCE(registro_evento.quantidade,0.00)  as quantidade
                               , registro_evento_periodo.cod_periodo_movimentacao
                               , evento.natureza
                               , evento.fixado
                               , COALESCE(evento_evento.unidade_quantitativa,0) as unidade_quantitativa
                               , configuracao_evento_caso.proporcao_abono
                               , registro_evento_parcela.parcela
                               , ''fixo_atual'' as lido_de

                            FROM folhapagamento'||stEntidade||'.registro_evento_periodo

                      INNER JOIN folhapagamento'||stEntidade||'.ultimo_registro_evento
                              ON registro_evento_periodo.cod_registro = ultimo_registro_evento.cod_registro

                      INNER JOIN folhapagamento'||stEntidade||'.registro_evento
                              ON ultimo_registro_evento.cod_registro = registro_evento.cod_registro
                             AND ultimo_registro_evento.timestamp    = registro_evento.timestamp

                      INNER JOIN folhapagamento'||stEntidade||'.evento
                              ON evento.cod_evento = registro_evento.cod_evento
                             AND evento.natureza IN ( ''P'',''D'' )

                      INNER JOIN folhapagamento'||stEntidade||'.evento_evento
                              ON evento_evento.cod_evento = evento.cod_evento

                      INNER JOIN (  SELECT cod_evento
                                         , max(timestamp) as timestamp
                                      FROM folhapagamento'||stEntidade||'.evento_evento
                                  GROUP BY cod_evento
                                 ) as max_evento_evento
                              ON evento_evento.cod_evento = max_evento_evento.cod_evento
                             AND evento_evento.timestamp = max_evento_evento.timestamp

                      INNER JOIN folhapagamento'||stEntidade||'.configuracao_evento_caso
                              ON configuracao_evento_caso.cod_evento = evento_evento.cod_evento
                             AND configuracao_evento_caso.timestamp  = evento_evento.timestamp
                             AND configuracao_evento_caso.cod_configuracao = 2

                      INNER JOIN folhapagamento'||stEntidade||'.tipo_evento_configuracao_media
                              ON tipo_evento_configuracao_media.cod_configuracao = 2
                             AND tipo_evento_configuracao_media.timestamp = configuracao_evento_caso.timestamp
                             AND tipo_evento_configuracao_media.cod_evento = configuracao_evento_caso.cod_evento
                             AND tipo_evento_configuracao_media.cod_caso = configuracao_evento_caso.cod_caso

                       LEFT JOIN folhapagamento'||stEntidade||'.registro_evento_parcela
                              ON registro_evento.cod_registro = registro_evento_parcela.cod_registro

                           WHERE registro_evento_periodo.cod_contrato             = '||inCodContrato||'
                             AND registro_evento_periodo.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                             AND registro_evento.proporcional = FALSE
                             AND (evento.tipo = ''F'' OR registro_evento_parcela.parcela is not null)
                        ORDER BY registro_evento.cod_evento';
            EXECUTE stSql;

            -- ***************** cria tabela distinct eventos 
            -- so executa o resto se existir dados no temporario
            stSql := 'SELECT count(cod_evento) FROM tmp_registro_evento_ferias';

            OPEN crCursor FOR EXECUTE stSql;
                 FETCH crCursor INTO inNrRegistros ;
            CLOSE crCursor;
            IF inNrRegistros > 0 THEN
                -- organiza tabela com distinct e verifica tipo de avaliacao pra media 
                -- em uma nova tabela temporaria . 

                CREATE TEMPORARY TABLE tmp_registro_evento_ferias_medias 
                      (cod_evento               INTEGER,
                       codigo                   VARCHAR,
                       descricao                VARCHAR,
                       unidade_quantitativa     NUMERIC(14,2),
                       fixado                   VARCHAR,
                       formula                  VARCHAR,
                       valor                    NUMERIC(14,2),
                       quantidade               NUMERIC(14,2),
                       parcela                  INTEGER,
                       avos                     INTEGER,
                       nr_ocorrencias           INTEGER,
                       natureza                 CHAR(1),
                       evento_sistema           BOOLEAN 
                      );

                --testes
                --     inCodSubDivisao    := 1;
                --     inCodFuncao        := 1;
                --     inCodEspecialidade := 1;
                --

                stSql := ' INSERT INTO tmp_registro_evento_ferias_medias 
                          SELECT
                                 distinct tmp_registro_evento_ferias.cod_evento as cod_evento
                               , fpe.codigo                                     as codigo
                               , fpe.descricao                                  as descricao
                               , COALESCE(fpee.unidade_quantitativa,0)          as unidade_quantitativa
                               , fpe.fixado                                     as fixado
                               , ''0.0.0''                                      as formula
                               , 0.00                                           as valor
                               , 0.00                                           as quantidade
                               , COALESCE(parcela,0)                            as parcela
                               , 0                                              as avos
                               , 0                                              as nr_ocorrencias
                               , fpe.natureza                                   as natureza
                               , fpe.evento_sistema                             as evento_sistema

                            FROM tmp_registro_evento_ferias

                       LEFT JOIN folhapagamento'||stEntidade||'.evento  as fpe 
                              ON fpe.cod_evento = tmp_registro_evento_ferias.cod_evento

                       LEFT JOIN ( SELECT distinct ON (cod_evento) cod_evento
                                        , unidade_quantitativa 
                                     FROM folhapagamento'||stEntidade||'.evento_evento
                                 ORDER BY folhapagamento'||stEntidade||'.evento_evento.cod_evento, folhapagamento'||stEntidade||'.evento_evento.timestamp desc
                                 ) as fpee
                              ON fpee.cod_evento = fpe.cod_evento

                           WHERE tmp_registro_evento_ferias.cod_periodo_movimentacao = ( SELECT MAX(eventos_ferias.cod_periodo_movimentacao) AS cod_periodo_movimentacao
                                                                                           FROM tmp_registro_evento_ferias AS eventos_ferias
                                                                                          WHERE eventos_ferias.cod_evento = fpe.cod_evento
                                                                                       )

                        ORDER BY tmp_registro_evento_ferias.cod_evento';
                EXECUTE stSql;

                --*************************************************
                -- so executa o resto se existir dados no temporario

                -- executa as formulas de cada um dos eventos, faz o desdobramento 
                -- necessario e grava o registro

                stSql := 'SELECT count(cod_evento)
                            FROM tmp_registro_evento_ferias_medias
                           WHERE formula is not null';

                OPEN crCursor FOR EXECUTE stSql;
                     FETCH crCursor INTO inNrRegistros;
                CLOSE crCursor;

                IF inNrRegistros IS NOT NULL THEN
                    -- ****************desdobramento*******************************************
                    -- aqui ja indica que existem eventos para este contrato 
                    -- parte que gera a relacao de proporcionalidade para definir o desdobramento

                    -- tratar desdobramento
                    -- , pois as medias obtem o valor/quantidade 
                    -- do todo, tendo que avaliar a proporcao entre F (ferias gozo)
                    -- A(abono) e D(adiantamneto pagamento gozo ferias)

                    --       , inCodForma
                    --       , inDiasGozoFeriasContrato
                    --       , inDiasAbonoContrato 
                    --       , stInicio
                    --       , stFim
                    --     , inDiasGozo
                    --     , inDiasAbono
                    --     , stAnoPagamento
                    --     , stMesPagamento

                    -- abono sempre e gerado pois precisa ser calculado no mes do pagamento
                    -- e recalculado no mes de gozo, se FOR o caso, para verificar a existencia 
                    -- de diferenca em relacao a ferias
                    -- ha controversias neste caso  pode ser necessario nao recalcular o abono.
                    inGeraAbono := inDiasAbonoContrato;

                    -- avaliacoes do mes de pagamento
                    IF ( stAnoPagamento = substr(stDataFinalCompetencia::varchar,1,4) AND stMesPagamento = substr(stDataFinalCompetencia::varchar,6,2) ) THEN

                        -- se o pagamento FOR todo adiantado, nao havendo gozo neste mes.
                        IF stInicio > stDataFinalCompetencia THEN
                           -- verifica quantos dias, de cada tipo devem ser gerados 
                           -- para esta situacao 
                           inGeraGozo         := 0;
                           inGeraAdiantamento := inDiasGozoFeriasContrato;

                           -- o inicio do gozo esta dentro do mes de pagamento
                        ELSE
                           -- gozo de ferias todo dentro do mes
                           IF stFim <= stDataFinalCompetencia THEN
                              inGeraGozo         := inDiasGozoFeriasContrato;
                              inGeraAdiantamento := 0;

                           ELSE 
                               -- se inicia no mes e continua no proximo, o gozo de ferias
                               inGeraGozo         := ( to_number(substr(stDataFinalCompetencia,9,2),'99') - to_number(substr(stInicio,9,2),'99') + 1 );
                               inGeraAdiantamento := inDiasGozoFeriasContrato - inGeraGozo;

                               -- ainda nao esta sendo tratado o caso de ferias 
                               -- em fevereiro, caso inicie em janeiro e termine 
                               -- em marco.

                           END IF;
                        END IF;

                    ELSE
                        -- esta fora do mes de pagamento mas ha necessidade de 
                        -- regeracao das ferias em funcao das datas de gozo de 
                        -- ferias, tanto considerando o inicio do gozo como o 
                        -- final do gozo.

                        inGeraAdiantamento := 0;
                        --inGeraAbono        := inDiasAbono;

                        -- se no ano/mes atual houve o inicio do gozo de ferias
                        IF substr(stInicio::varchar,1,7) = substr(stDataFinalCompetencia::varchar,1,7) THEN 

                           -- inicio e fim no mesmo mes.
                           IF substr(stFim::varchar,1,7) = substr(stInicio::varchar,1,7) THEN 
                              inGeraGozo := to_number(substr(stFim,9,2),'99') - to_number(substr(stInicio::varchar,9,2),'99') + 1;
                           ELSE
                              inGeraGozo := to_number(substr(stDataFinalCompetencia,9,2),'99') - to_number(substr(stInicio,9,2),'99') + 1 ;
                           END IF;

                        ELSE

                           -- caso especial de inicio de ferias no mes anterior e termino no proximo (fevereiro)
                           IF (    substr(stFim::varchar,1,7) > substr(stDataFinalCompetencia::varchar,1,7) 
                               AND substr(stInicio::varchar,1,7) < substr(stDataFinalCompetencia::varchar,1,7) 
                              ) THEN 

                              inGeraGozo := to_number(substr(stDataFinalCompetencia,9,2),'99');

                           ELSE

                              -- se nao FOR o mes de inicio de gozo, verifica entao,
                              -- se o final esta neste mes.
                              IF substr(stFim::varchar,1,7) = substr(stDataFinalCompetencia::varchar,1,7) THEN 
                                  inGeraGozo  := to_number( substr(stFim,9,2),'99' );
                                  boNaoGerarDesconto := TRUE;
                              END IF;
                           END IF;
                        END IF;
                        -- ainda a veificar:
                        -- necessario verificar os pagamentos de ferias efetuados 
                        -- em meses anteriores pois aqui precisam ser descontados
                        -- para a geracao das diferencas de ferias.

                        -- calculaNrDiasAnoMes( ano, mes ) 
                        --   ( calculaNrDiasAnoMes( 
                        --     to_number(substr(stInicio,1,4),9999)
                        --   , to_number(substr(stInicio,6,2),99)
                        --                     )
                    END IF;

                    -- *********************** fim definicao desdobramento

                    inDiasGozo := criarBufferInteiro('inDiasGozo',inDiasGozo);

                    --***************************************************
                    -- Para os casos de gozo de ferias ( existindo na legislacao)
                    -- que compreenda menos de 30 dias ou que tenha mais que 30 dias 
                    -- sera necessario fazer a proporcao ja que os valores/quantidades
                    -- obtidos das funcoes de medias correspondem sempre a 30 dias.
                    -- Esta relacao deve ser com a forma de pagamento e nao em relacao aos 
                    -- dias de ferias do contrato.
                    nuProporcaoForma := 1;

                    --CRIAR IF PARA NAO ENTRAR NESSA PROPORCAO CASO FOR cod_forma 3 ou 4
                    --Deve ser armazenada no banco a quantidade do registro do evento como 30 sempre
                    IF inCodForma = 3 THEN
                        nuProporcaoDivisao := 10;
                    ELSEIF inCodForma = 4 THEN 
                        nuProporcaoDivisao := 15;
                    ELSE 
                        nuProporcaoDivisao := 30;
                    END IF;

                    IF inDiasGozo + inDiasAbono != 30 AND (dtFinalAquisitivo-dtInicialAquisitivo+1) >= inQtdDiasAnoCompetencia THEN
                       nuProporcaoForma := ((inDiasGozo + inDiasAbono)/nuProporcaoDivisao) ;
                    END IF;
                    --**************************** fim proporcao forma

                    --Exclui da tabela temporia evento de desconto que não são automáticos
                    IF boNaoGerarDesconto IS TRUE THEN
                       stSql1 := ' DELETE 
                                     FROM tmp_registro_evento_ferias_medias
                                    WHERE evento_sistema = false
                                      AND natureza =''D''  ';
                       EXECUTE stSql1;
                    END IF;
                    --*******************************************************************

                    -- executa e grava o resultado das formulas
                    stSql1 := 'SELECT *
                                 FROM tmp_registro_evento_ferias_medias
                                WHERE formula is not null ';
                    --  *****************************************************************
                    FOR reRegistro1 IN  EXECUTE stSql1 LOOP
                       inCodEvento := reRegistro1.cod_evento;
                       inCodEvento := criarBufferInteiro( 'incodevento', reRegistro1.cod_evento );
                       -- busca a formula de media para o evento em questao
                       stFormula := pegaFormulaMediaFerias(inCodEvento,2,inCodSubDivisao,inCodFuncao,inCodEspecialidade);
                       -- executa formula
                       IF stFormula IS NOT NULL THEN
                           -- como toda a avalicao da media se faz em periodos de competencia 
                           -- ou seja, de 30 dias, este resultado sera sempre para 30 dias. 
                           -- Caso o nr. de dias total seja diferente de 30 este devera ser tratado.
                           stExecutaFormula := executaGCNumerico( stFormula );
                           nuExecutaFormula := to_number( stExecutaFormula , '99999999999.99' );
                            --No caso de férias com período aquisitivo inferior a 12 meses
                            --essa linha vai fazer a proporção do retorna da função que foi executava
                            --com base em um pagamento de férias completo, ou seja, 30 dias
                            IF (dtFinalAquisitivo-dtInicialAquisitivo+1) < inQtdDiasAnoCompetencia THEN
                                nuExecutaFormula := nuExecutaFormula*inDiasGozo/30;
                            END IF;

                           IF nuExecutaFormula != 0 THEN 
                              -- multiplica pelo fator de proporcionalizacao da forma 
                              -- pois pode haver casos de ferias 45 dias ou entao que 
                              -- no total tenha apenas 20 dias, por exemplo.

                              nuExecutaFormula := arredondar( nuExecutaFormula * nuProporcaoForma,2);
                                IF reRegistro1.parcela IS NOT NULL AND reRegistro1.parcela > 0 THEN
                                    nuQuantidadeFormula := nuExecutaFormula;
                                    IF nuQuantidadeFormula = 0 THEN
                                        inGeraAbono := 0;
                                        inGeraGozo  := 0;
                                        inGeraAdiantamento := 0;
                                    ELSE
                                        stSql := ' SELECT valor
                                                     FROM tmp_registro_evento_ferias 
                                                    WHERE cod_evento = '||inCodEvento||'
                                                      AND lido_de = ''fixo_atual''';
                                        nuValorFormula := selectIntoNumeric(stSql);
                                    END IF;
                                    
                                ELSE

                                    IF reRegistro1.fixado = 'V' THEN
                                        nuValorFormula := nuExecutaFormula;
                                        -- por enquanto precisa ser zerado, posteriormente
                                        -- ler as quantidade deste caso, fazer a media, tambem
                                        -- e gravar.
                                        nuQuantidadeFormula := 0;
                                        -- so para teste
                                        -- depois avaliar se deve ser gravado no tmp..medias
                                        -- ou se gravar diretamente no registro_evento_ferias 
                                        stSql2 := ' UPDATE tmp_registro_evento_ferias_medias 
                                                       SET valor = '||nuExecutaFormula||'
                                                     WHERE cod_evento = '||reRegistro1.cod_evento||'
                                                '; 
                                        EXECUTE stSql2;
                                        -- fim so para teste
                                    ELSE

                                        nuQuantidadeFormula := nuExecutaFormula;
                                        -- para eventos fixados por quantidade nunca gravar valor.
                                        nuValorFormula := 0;
                                        -- so para testes
                                        stSql2 := ' UPDATE tmp_registro_evento_ferias_medias 
                                                       SET quantidade = '||nuExecutaFormula||'
                                                     WHERE cod_evento = '||reRegistro1.cod_evento||'
                                                '; 
                                        EXECUTE stSql2;
                                        -- fim so para teste

                                    END IF;
                                END IF;

                              IF boGerandoRescisao  = 'f' THEN
                                 --*****************************************
                                 -- desdobra os valores/quantidades e grava                 
                                 nuTotalValor := 0;
                                 nuTotalQuantidade := 0;

                                 --Validação necessário para não proporcionalizar eventos de desconto
                                 IF reRegistro1.natureza != 'D' THEN
                                    inGeraAbono := inDiasAbono;
                                 END IF;
                                 IF inGeraAbono > 0 AND reRegistro1.parcela <= 0 THEN
                                     -- faz a relacao do valor ja tratado para o nr. de dias 
                                     -- da forma de ferias com o nr.real de dias de abono e com 
                                     -- o total de dias relativo a forma de ferias.
                                     IF pega0ProporcaoAbonoFerias() IS TRUE THEN   
                                         nuValor := arredondar( (nuValorFormula*inGeraAbono)/(inDiasGozo+inDiasAbono),2 ); 
                                         nuQuantidade := arredondar( (nuQuantidadeFormula*inGeraAbono)/(inDiasGozo+inDiasAbono),2 ); 
                                     ELSE
                                         nuValor      := nuValorFormula;
                                         nuQuantidade := nuQuantidadeFormula;
                                     END IF;
                                     stDesdobramento := 'A';
                                     nuTotalValor := nuValor;
                                     nuTotalQuantidade := nuQuantidade;
                                     -- gravando o registro 
                                     boGravouRegistro := gravaRegistroEventoFerias( inCodContrato,inCodPeriodoMovimentacao,inCodEvento, nuValor, nuQuantidade, stDesdobramento,reRegistro1.parcela );
                                 END IF;

                                 --Validação necessário para não proporcionalizar eventos de desconto
                                 IF reRegistro1.natureza != 'D' THEN
                                    inGeraAdiantamento := inDiasGozo - inGeraGozo;
                                 END IF;

                                 IF inGeraAdiantamento > 0 THEN
                                     IF pega0ProporcaoAbonoFerias() IS TRUE THEN
                                         nuValor := arredondar( (nuValorFormula*inGeraAdiantamento)/(inDiasGozo+inDiasAbono),2 ); 
                                         nuQuantidade := arredondar( (nuQuantidadeFormula*inGeraAdiantamento)/(inDiasGozo+inDiasAbono),2 ); 
                                     ELSE
                                         nuValor := nuValorFormula;
                                         nuQuantidade := nuQuantidadeFormula;
                                        IF reRegistro1.parcela IS NOT NULL AND reRegistro1.parcela > 0 THEN
                                            IF inGeraAdiantamento <= inGeraGozo OR inGeraAdiantamento <= inGeraAbono THEN
                                                nuValor      := 0;
                                                nuQuantidade := 0;
                                            END IF;
                                        END IF;
                                     END IF;
                                     stDesdobramento := 'D';
                                     nuTotalValor := nuTotalValor + nuValor;
                                     nuTotalQuantidade := nuTotalValor + nuQuantidade;
                                     -- gravando o registro 
                                     boGravouRegistro := gravaRegistroEventoFerias( inCodContrato,inCodPeriodoMovimentacao,inCodEvento, nuValor, nuQuantidade, stDesdobramento,reRegistro1.parcela );
                                 END IF;

                                 IF inGeraGozo > 0 THEN
                                     IF pega0ProporcaoAbonoFerias() IS TRUE THEN
                                         nuValor := arredondar( (nuValorFormula*inGeraGozo)/(inDiasGozo+inDiasAbono),2 ); 
                                         nuQuantidade := arredondar( (nuQuantidadeFormula*inGeraGozo)/(inDiasGozo+inDiasAbono),2 ); 
                                     ELSE
                                         nuValor := nuValorFormula;
                                         nuQuantidade := nuQuantidadeFormula;
                                        IF reRegistro1.parcela IS NOT NULL AND reRegistro1.parcela > 0 THEN
                                            IF inGeraGozo < inGeraAdiantamento OR inGeraGozo < inGeraAbono THEN
                                                nuValor      := 0;
                                                nuQuantidade := 0;
                                            END IF;
                                        END IF;
                                     END IF;
                                     stDesdobramento := 'F';
                                     nuTotalValor := nuTotalValor + nuValor;
                                     nuTotalQuantidade := nuTotalValor + nuQuantidade;
                                     -- ajustar pois pode dar diferenca em funcao dos arredondamentos
                                     -- necessaria verificacao em cada um dos casos pois pode ter adiantamento
                                     -- e nao ter gozo.
                                     --IF nuExecutaFormula != (nuTotalValor + nuTotalQuantidade) THEN 
                                     --    IF nuValor > 0 THEN
                                     --    END IF; 
                                     --END IF;

                                     -- gravando o registro 
                                     boGravouRegistro := gravaRegistroEventoFerias( inCodContrato,inCodPeriodoMovimentacao,inCodEvento, nuValor, nuQuantidade, stDesdobramento,reRegistro1.parcela );

                                 END IF;
                               ELSE
                                     stDesdobramento  := recuperarBufferTexto('stSituacaFerias');
                                    stSql := '    SELECT registro_evento_rescisao.*
                                                    FROM folhapagamento'||stEntidade||'.registro_evento_rescisao
                                              INNER JOIN folhapagamento'||stEntidade||'.ultimo_registro_evento_rescisao
                                                      ON registro_evento_rescisao.cod_registro = ultimo_registro_evento_rescisao.cod_registro
                                                     AND registro_evento_rescisao.cod_evento = ultimo_registro_evento_rescisao.cod_evento
                                                     AND registro_evento_rescisao.desdobramento = ultimo_registro_evento_rescisao.desdobramento
                                                     AND registro_evento_rescisao.timestamp = ultimo_registro_evento_rescisao.timestamp
                                                     AND registro_evento_rescisao.cod_contrato = '||inCodContrato||'
                                                     AND registro_evento_rescisao.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                                                     AND registro_evento_rescisao.cod_evento = '||inCodEvento||'
                                                     AND registro_evento_rescisao.desdobramento = '|| quote_literal(stDesdobramento);

                                    OPEN crCursor FOR EXECUTE stSql;
                                        FETCH crCursor INTO reRegistroRescisao;
                                    CLOSE crCursor; 
                                    IF reRegistroRescisao.cod_registro IS NOT NULL THEN
                                        nuValorFormula := nuValorFormula + reRegistroRescisao.valor;
                                        nuQuantidadeFormula := nuQuantidadeFormula + reRegistroRescisao.quantidade;
                                    END IF;
                                     boGravouRegistro := gravaRegistroEventoRescisao( inCodContrato,inCodPeriodoMovimentacao,inCodEvento, nuValorFormula, nuQuantidadeFormula, stDesdobramento);
                               END IF;
                           END IF;
                       END IF;
                    END LOOP;
                END IF;
                DROP TABLE tmp_registro_evento_ferias_medias;

            END IF;
            DROP TABLE tmp_registro_evento_ferias;
            boRetorno := removerBufferInteiro('inAvos');

        END IF;
    END IF;
    IF inControleExecucaoRescisaoFerias < 1 THEN
        boRetorno := deletarTemporariasDoCalculo(true);
    END IF;
RETURN TRUE; 
END;
$$ LANGUAGE 'plpgsql';

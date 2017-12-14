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
-- $Revision: 23133 $
-- $Name$
-- $Autor: Marcia $
-- Date: 2006/06/01 10:50:00 $
--
-- Caso de uso: uc-04.04.22
-- Caso de uso: uc-04.05.53
--
-- Objetivo: efetua o levantamento necessario para a geracao do registro
-- de ferias conforme a indicacao do tipo de media de cada evento
--
--*/

CREATE OR REPLACE FUNCTION verificaferias01() RETURNS BOOLEAN as $$

DECLARE

stSql                            VARCHAR := '';
stSql1                           VARCHAR := '';
stSql2                           VARCHAR := '';
crCursor                         REFCURSOR;
reRegistro                       RECORD;
reRegistro1                      RECORD;
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

stSituacao                       VARCHAR := 'f';

inCodContrato                    INTEGER := 1;
inCodPeriodoMovimentacao         INTEGER := 17;
stDataFinalCompetencia           VARCHAR := '2006-07-31';

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

nuDiasAbono                      NUMERIC := 0.00;
nuProporcaoForma                 NUMERIC := 0.00;

inGeraGozo                       INTEGER;
inGeraAbono                      INTEGER;
inGeraAdiantamento               INTEGER;

stDesdobramento                  VARCHAR := 'F';

nuTotalValor                     NUMERIC := 0.00;
nuTotalQuantidade                NUMERIC := 0.00;
stEntidade VARCHAR := recuperarBufferTexto('stEntidade');


BEGIN


-- futuramente, quando a chamada desta funcao FOR tambem para a 
-- geracao de diferenca de ferias ( regeracao das ferias quando estas tiverem 
-- gozo de ferias em mes diferente do cadastramento das ferias )  


-- para a chamada desta funcao, estes buffers deverao estar criados.
--   inCodContrato :=  recuperarBufferInteiro(''inCodContrato'');
--   inCodPeriodoMovimentacao   := recuperarBufferInteiro(''inCodPeriodoMovimentacao'');
--   stDataFinalCompetencia := recuperaBufferTexto( ''stDataFinalCompetencia'');

-- nem entrar nesta funcao se o servidor nao tiver registros no folhapagamento'||stEntidade||'.contrato_servidor_periodo
    
-- se ja houver algum registro do contrato no periodo na tabela de 
-- folhapagamento'||stEntidade||'.registro_evento_ferias nao deve permitir a geracao 
-- das ferias e nem cadastro de ferias para este periodo. 
--  Nem deve entrar nesta funcao.!!???

--inCodSubDivisao            := recuperarBufferInteiro(''inCodSubDivisao'');
--inCodFuncao                := recuperarBufferInteiro(''inCodFuncao'');
--inCodEspecialidade         := recuperarBufferInteiro(''inCodEspecialidade'');
--
-- ou 
--
-- inCodSubDivisao := pega0SubDivisaoDoContratoNaData( inCodContrato, stDataFinalCompetencia );
-- inCodFuncao := pega0FuncaoDoContratoNaData( inCodContrato, stDataFinalCompetencia );
-- inCodEspecialidade := pega0EspecialidadeDoContratoNaData( inCodContrato, stDataFinalCompetencia );

-- inCodSubDivisao := criarBufferInteiro(''inCodSubDivisao'',inCodSubDivisao);
-- inCodFuncao := criarBufferInteiro(''inCodFuncao'',inCodFuncao);
-- inCodEspecialidade := criarBufferInteiro(''inCodEspecialidade'',inCodEspecialidade);



-- *******************busca os dados do cadastro de ferias
-- busca os dados do cadastro das ferias e retorna o periodo aquisitivo
-- e demais dados necessarios ao desdobramento e gravacao dos registros 
-- as situacoes onde deve haver calculo de ferias sao diversas : pagamento 
-- todo adiantado de ferias, mes com dias de gozo de ferias, considerando o inicio do gozo, 
-- mes com dias de gozo considerando a data final de gozo. 

stSql := 'SELECT  ferias.dt_inicial_aquisitivo
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

             FROM pessoal'||stEntidade||'.ferias                 as ferias

            JOIN pessoal'||stEntidade||'.lancamento_ferias       as lancamento_ferias
              ON ferias.cod_ferias = lancamento_ferias.cod_ferias

            JOIN pessoal'||stEntidade||'.forma_pagamento_ferias  as forma_pagamento_ferias   
              ON forma_pagamento_ferias.cod_forma = ferias.cod_forma

           WHERE cod_contrato = '||inCodContrato||'
             AND ( 
                  ( substr('|| quote_literal(stDataFinalCompetencia) ||',1,4) = lancamento_ferias.ano_competencia
                    AND substr('|| quote_literal(stDataFinalCompetencia) ||',6,2) = lancamento_ferias.mes_competencia
                  )
                  or
                  ( substr('|| quote_literal(stDataFinalCompetencia) ||',1,7) = substr(lancamento_ferias.dt_inicio::varchar,1,7)
                  )
                  or
                  ( substr('|| quote_literal(stDataFinalCompetencia) ||',1,7) = substr(lancamento_ferias.dt_fim::varchar,1,7)
                  )
                  or 
                  ( substr('|| quote_literal(stDataFinalCompetencia) ||',1,7) 
                     between substr(lancamento_ferias.dt_inicio::varchar,1,7)
                         AND substr(lancamento_ferias.dt_fim::varchar,1,7)
                  )
                 )
           ORDER BY dt_inicial_aquisitivo desc
           LIMIT 1';
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



-- o sql ja verifica se deve ou nao gerar o registro de ferias.
-- caso nao coincida o mes e ano ou o periodo de gozo cai fora da funcao.
IF dtInicialAquisitivo IS NOT NULL THEN

  -- necessario verificar a proporcionalizacao do registro de eventos para este contrato
  -- ou seja, proporcionalizar o ponto de salario para o nr. de dias de gozo de ferias
  -- dentro desta competencia . Ver isto apos a geracao da variavei inGeraGozo


  stSql := 'SELECT *
              FROM folhapagamento'||stEntidade||'.periodo_movimentacao as pm
              JOIN  folhapagamento'||stEntidade||'.periodo_movimentacao_situacao as pms
                ON pms.cod_periodo_movimentacao = pm.cod_periodo_movimentacao
               AND pms.situacao = '|| quote_literal(stSituacao) ||'
             WHERE '|| quote_literal(dtInicialAquisitivo) ||'
           BETWEEN dt_inicial AND dt_final';



  -- busca o periodo de movimentacao para inicio de avaliacao
    
  OPEN crCursor FOR EXECUTE stSql;
       FETCH crCursor INTO inCodPeriodoInicialLeitura, dtInicialPeriodo,dtFinalPeriodo ;
  CLOSE crCursor;

  inDiasPeriodo := ( dtFinalPeriodo - dtInicialPeriodo) +1 ;


  -- gera o nr. de dias de ferias levando em conta o periodo de efetividade
  -- e a data inicial do periodo aquisitivo.
  inDiasAquisitivoPeriodoInicial := ((dtFinalPeriodo - dtInicialAquisitivo )+1 );


  -- tratamento especial para mes de fevereiro
  IF inDiasAquisitivoPeriodoInicial < 15 
        or ( inDiasPeriodo < 30 AND inDiasAquisitivoPeriodoInicial = 15 )  
  THEN
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
            lido_de                  VARCHAR
           );
            

  
  -- Nota para futuro - observar que nos casos em que a quantidade do registro
  -- do evento FOR alterada em relacao a gravacao da quantidade no calculado.
  -- Para a execucao destas media seria necessario pegar a qtd do registro.
  
  -- salario
  stSql := ' INSERT INTO tmp_registro_evento_ferias 
                SELECT
                      fpec.cod_evento                 as cod_evento
                     ,COALESCE(fpec.valor,0.00)       as valor
                     ,COALESCE(fpec.quantidade,0.00)  as quantidade
                     ,fprepe.cod_periodo_movimentacao as cod_periodo_movimentacao
                     ,fpe.natureza                    as natureza
                     ,fpe.fixado                      as fixado
                     ,fpee.unidade_quantitativa       as unidade_quantitativa
                     ,''evento_calculado''              as lido_de

                FROM                                                     
                      folhapagamento'||stEntidade||'.registro_evento_periodo   as fprepe             

                JOIN folhapagamento'||stEntidade||'.evento_calculado           as fpec
                  ON  fpec.cod_registro = fprepe.cod_registro

                JOIN  folhapagamento'||stEntidade||'.evento                    as fpe
                  ON  fpe.cod_evento = fpec.cod_evento
                 AND  fpe.natureza IN ( ''P'',''D'' )         

                JOIN  ( SELECT distinct ON (cod_evento) cod_evento 
                              ,timestamp
                              ,COALESCE(unidade_quantitativa,0) as unidade_quantitativa
                          FROM folhapagamento'||stEntidade||'.evento_evento
                         ORDER BY evento_evento.cod_evento, evento_evento.timestamp desc
                         ) as fpee
                  ON  fpee.cod_evento = fpe.cod_evento

                JOIN folhapagamento'||stEntidade||'.configuracao_evento_caso   as fpcec
                  ON fpcec.cod_evento = fpee.cod_evento
                 AND fpcec.timestamp  = fpee.timestamp
                 AND fpcec.cod_configuracao = 2
          
                JOIN folhapagamento'||stEntidade||'.tipo_evento_configuracao_media as fptecm
                  ON fptecm.cod_configuracao = 2
                 AND fptecm.timestamp = fpcec.timestamp 
                 AND fptecm.cod_evento = fpcec.cod_evento
                 AND fptecm.cod_caso = fpcec.cod_caso

                JOIN ( SELECT 
                         cod_periodo_movimentacao
                        ,max(timestamp) 
                       FROM folhapagamento'||stEntidade||'.periodo_movimentacao_situacao
                      GROUP BY 1 
                     ) as fppms
                  ON fppms.cod_periodo_movimentacao = fprepe.cod_periodo_movimentacao
                 AND ( SELECT situacao 
                        FROM folhapagamento'||stEntidade||'.periodo_movimentacao_situacao as fppms_
                       WHERE fppms_.cod_periodo_movimentacao = fppms.cod_periodo_movimentacao
                         AND fppms_.timestamp = fppms.timestamp
                     ) = ''f''

               WHERE  fprepe.cod_periodo_movimentacao 
                          between '||inCodPeriodoInicialLeitura||'
                              AND '||inCodPeriodoInicialLeitura + 11||'
                 
                 AND  fprepe.cod_registro = fpec.cod_registro
                 AND  fprepe.cod_contrato = '||incodContrato||'
                 AND  fpec.valor > 0 
            ORDER BY  fpec.cod_evento';

  EXECUTE stSql;


  -- complementar
  stSql := ' INSERT INTO tmp_registro_evento_ferias 
                SELECT
                      fpecc.cod_evento                 as cod_evento
                     ,COALESCE(fpecc.valor,0.00)       as valor
                     ,COALESCE(fpecc.quantidade,0.00)  as quantidade
                     ,fprec.cod_periodo_movimentacao   as cod_periodo_movimentacao
                     ,fpe.natureza                     as natureza
                     ,fpe.fixado                       as fixado
                     ,fpee.unidade_quantitativa        as unidade_quantitativa
                     ,''evento_calculado_complementar''        as lido_de

                FROM                                                     
                      folhapagamento'||stEntidade||'.registro_evento_complementar   as fprec             

                JOIN folhapagamento'||stEntidade||'.evento_complementar_calculado     as fpecc
                  ON  fpecc.cod_registro = fprec.cod_registro

                JOIN  folhapagamento'||stEntidade||'.evento                    as fpe
                  ON  fpe.cod_evento = fpecc.cod_evento
                 AND  fpe.natureza IN ( ''P'',''D'' )         

                JOIN  ( SELECT distinct ON (cod_evento) cod_evento 
                              ,timestamp
                              ,COALESCE(unidade_quantitativa,0) as unidade_quantitativa
                          FROM folhapagamento'||stEntidade||'.evento_evento
                         ORDER BY evento_evento.cod_evento, evento_evento.timestamp desc
                         ) as fpee
                  ON  fpee.cod_evento = fpe.cod_evento

                JOIN folhapagamento'||stEntidade||'.configuracao_evento_caso   as fpcec
                  ON fpcec.cod_evento = fpee.cod_evento
                 AND fpcec.timestamp  = fpee.timestamp
                 AND fpcec.cod_configuracao = 2
          
               JOIN folhapagamento'||stEntidade||'.tipo_evento_configuracao_media as fptecm
                 ON fptecm.cod_configuracao = 2
                AND fptecm.timestamp = fpcec.timestamp 
                AND fptecm.cod_evento = fpcec.cod_evento
                AND fptecm.cod_caso = fpcec.cod_caso

                JOIN ( SELECT 
                         cod_periodo_movimentacao
                        ,max(timestamp) 
                       FROM folhapagamento'||stEntidade||'.periodo_movimentacao_situacao
                    --  WHERE situacao = ''f''
                      GROUP BY 1 
                     ) as fppms
                  ON fppms.cod_periodo_movimentacao = fprec.cod_periodo_movimentacao
                 AND ( SELECT situacao 
                        FROM folhapagamento'||stEntidade||'.periodo_movimentacao_situacao as fppms_
                       WHERE fppms_.cod_periodo_movimentacao = fppms.cod_periodo_movimentacao
                         AND fppms_.timestamp = fppms.timestamp
                     ) = ''f''

               WHERE  fprec.cod_periodo_movimentacao 
                          between ''||inCodPeriodoInicialLeitura||'' 
                              AND ''||inCodPeriodoInicialLeitura + 11||''
                 AND  fprec.cod_registro             = fpecc.cod_registro
                 AND  fprec.cod_contrato             = ''||incodContrato||''

                 AND  fpecc.valor > 0 
            ORDER BY  fpecc.cod_evento'';




  EXECUTE stSql;




  -- leitura e insercao do "ponto fixo" na tabela temporaria
  stSql := '' INSERT INTO tmp_registro_evento_ferias 
                SELECT
                      fpre.cod_evento                 as cod_evento
                     ,COALESCE(fpre.valor,0.00)       as valor
                     ,COALESCE(fpre.quantidade,0.00)  as quantidade
                     ,fprepe.cod_periodo_movimentacao as cod_periodo_movimentacao
                     ,fpe.natureza                 as natureza
                     ,fpe.fixado                      as fixado
                     ,fpee.unidade_quantitativa       as unidade_quantitativa
                     ,''fixo_atual''              as lido_de

                FROM                                                     
                      folhapagamento'||stEntidade||'.registro_evento_periodo       as fprepe             
                JOIN  folhapagamento'||stEntidade||'.ultimo_registro_evento        as fpure
                  ON  fprepe.cod_registro = fpure.cod_registro
          
                JOIN  folhapagamento'||stEntidade||'.registro_evento               as fpre                      
                  ON  fpure.cod_registro = fpre.cod_registro
                 AND  fpure.timestamp    = fpre.timestamp

                JOIN  folhapagamento'||stEntidade||'.evento                        as fpe
                  ON  fpe.cod_evento = fpre.cod_evento
                 AND  fpe.natureza IN ( ''P'',''D'' )         

                JOIN  ( SELECT distinct ON (cod_evento) cod_evento 
                              ,timestamp
                              ,COALESCE(unidade_quantitativa,0) as unidade_quantitativa
                          FROM folhapagamento'||stEntidade||'.evento_evento
                         ORDER BY evento_evento.cod_evento, evento_evento.timestamp desc
                         ) as fpee
                  ON  fpee.cod_evento = fpe.cod_evento

                JOIN folhapagamento'||stEntidade||'.configuracao_evento_caso   as fpcec
                  ON fpcec.cod_evento = fpee.cod_evento
                 AND fpcec.timestamp  = fpee.timestamp
                 AND fpcec.cod_configuracao = 2
          
               JOIN folhapagamento'||stEntidade||'.tipo_evento_configuracao_media as fptecm
                 ON fptecm.cod_configuracao = 2
                AND fptecm.timestamp = fpcec.timestamp 
                AND fptecm.cod_evento = fpcec.cod_evento
                AND fptecm.cod_caso = fpcec.cod_caso

           LEFT JOIN  folhapagamento'||stEntidade||'.registro_evento_parcela       as fprep    
                  ON  fpre.cod_registro     = fprep.cod_registro           
                 AND  fpre.timestamp        = fprep.timestamp            


               WHERE  fprepe.cod_periodo_movimentacao = '||incodPeriodoMovimentacao||'
                 AND  fprepe.cod_registro             = fpre.cod_registro
                 AND  fprepe.cod_contrato             = '||incodContrato||'
                 AND  fpre.proporcional               = FALSE
            ORDER BY  fpre.cod_evento
            ';




  EXECUTE stSql;

--*****************************
-- teste
  stSql := ' INSERT INTO tmp_registro_evento_ferias VALUES (1,0,200,10,''P'',''Q'',200,''fixo atual'') ';
  EXECUTE stSql;

  stSql := ' INSERT INTO tmp_registro_evento_ferias VALUES (1,0,200,13,''P'',''Q'',200,''evento_calculado'') ';
  EXECUTE stSql;

  stSql := ' INSERT INTO tmp_registro_evento_ferias VALUES (1,0,10,14,''P'',''Q'',200,''evento_calculado_complementar'') ';
  EXECUTE stSql;
  stSql := ' INSERT INTO tmp_registro_evento_ferias VALUES (1,0,99,14,''P'',''Q'',200,''evento_calculado'') ';
  EXECUTE stSql;

  stSql := ' INSERT INTO tmp_registro_evento_ferias VALUES (1,0,110,15,''P'',''Q'',200,''evento_calculado'') ';
  EXECUTE stSql;

  stSql := ' INSERT INTO tmp_registro_evento_ferias VALUES (1,0,50,16,''P'',''Q'',200,''evento_calculado'') ';
  EXECUTE stSql;


-- ***************** cria tabela distinct eventos 
  -- so executa o resto se existir dados no temporario
  SELECT INTO inNrRegistros(SELECT count(cod_evento)
                                  FROM tmp_registro_evento_ferias
                           );


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
            avos                     INTEGER,
            nr_ocorrencias           INTEGER
           );



--testes
     inCodSubDivisao    := 1;
     inCodFuncao        := 1;
     inCodEspecialidade := 1;
--


     stSql := ' INSERT INTO tmp_registro_evento_ferias_medias 
              SELECT  distinct tmp_registro_evento_ferias.cod_evento as cod_evento
                     , fpe.codigo                                    as codigo
                     , fpe.descricao                                 as descricao
                     , COALESCE(fpee.unidade_quantitativa,0)         as unidade_quantitativa
                     , fpe.fixado                                    as fixado
                     , ''0.0.0''                                     as formula
                     , 0.00                                          as valor
                     , 0.00                                          as quantidade
                     , 0                                             as avos
                     , 0                                             as nr_ocorrencias
                FROM tmp_registro_evento_ferias

                LEFT OUTER JOIN folhapagamento'||stEntidade||'.evento  as fpe 
                  ON fpe.cod_evento = tmp_registro_evento_ferias.cod_evento

                LEFT OUTER JOIN 
                     ( SELECT distinct ON (cod_evento) cod_evento
                        , unidade_quantitativa 
                        FROM folhapagamento'||stEntidade||'.evento_evento
                          ORDER BY folhapagamento'||stEntidade||'.evento_evento.cod_evento, folhapagamento'||stEntidade||'.evento_evento.timestamp desc
                         ) as fpee
                  ON fpee.cod_evento = fpe.cod_evento

              ORDER BY tmp_registro_evento_ferias.cod_evento';

     EXECUTE stSql;

     --*************************************************
     -- so executa o resto se existir dados no temporario

     -- executa as formulas de cada um dos eventos, faz o desdobramento 
     -- necessario e grava o registro

     SELECT INTO inNrRegistros(SELECT count(cod_evento)
                                  FROM tmp_registro_evento_ferias_medias
                                 WHERE formula is not null
                           );


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
        IF (     stAnoPagamento = substr(stDataFinalCompetencia::varchar,1,4) 
             AND stMesPagamento = substr(stDataFinalCompetencia::varchar,6,2) 
           ) THEN


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
                   inGeraGozo := (  to_number(substr(stDataFinalCompetencia::varchar,9,2),99)
                                    - to_number(substr(stInicio::varchar,9,2),99) + 1
                                 );
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
            IF substr(stInicio,1,7) = substr(stDataFinalCompetencia::varchar,1,7) THEN 

               -- inicio e fim no mesmo mes.
               IF substr(stFim,1,7) = substr(stInicio::varchar,1,7) THEN 
                  inGeraGozo := to_number(substr(stFim::varchar,9,2),99) - to_number(substr(stInicio::varchar,9,2),99) + 1;
               ELSE
                  inGeraGozo := to_number(substr(stDataFinalCompetencia::varchar,9,2),99) - to_number(substr(stInicio::varchar,9,2),99) + 1 ;
               END IF;





            ELSE

               -- caso especial de inicio de ferias no mes anterior e termino no proximo (fevereiro)
               IF (    substr(stFim::varchar,1,7) > substr(stDataFinalCompetencia::varchar,1,7) 
                   AND substr(stInicio::varchar,1,7) < substr(stDataFinalCompetencia::varchar,1,7) 
                  ) THEN 

                  inGeraGozo := to_number(substr(stDataFinalCompetencia::varchar,9,2),99);




               ELSE
                  -- se nao FOR o mes de inicio de gozo, verifica entao,
                  -- se o final esta neste mes.
                  IF substr(stFim::varchar,1,7) = substr(stDataFinalCompetencia::varchar,1,7) THEN 
                      inGeraGozo  := to_number( substr(stFim::varchar,6,2),99 );





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


        --***************************************************
        -- Para os casos de gozo de ferias ( existindo na legislacao)
        -- que compreenda menos de 30 dias ou que tenha mais que 30 dias 
        -- sera necessario fazer a proporcao ja que os valores/quantidades
        -- obtidos das funcoes de medias correspondem sempre a 30 dias.
        -- Esta relacao deve ser com a forma de pagamento e nao em relacao aos 
        -- dias de ferias do contrato.
        nuProporcaoForma := 1; 
        IF inDiasGozo + inDiasAbono != 30 THEN
           nuProporcaoForma := ((inDiasGozo + inDiasAbono)/30) ;
        END IF;
       --**************************** fim proporcao forma


        --*****************************************
        -- executa e grava o resultado das formulas
        stSql1 := 'SELECT * FROM tmp_registro_evento_ferias_medias WHERE formula is not null';



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

               IF nuExecutaFormula != 0 THEN 



                  -- multiplica pelo fator de proporcionalizacao da forma 
                  -- pois pode haver casos de ferias 45 dias ou entao que 
                  -- no total tenha apenas 20 dias, por exemplo.
                  nuExecutaFormula := arredondar( nuExecutaFormula * nuProporcaoForma,2);

                 
                  IF reRegistro1.fixado = 'V' THEN
                       nuValorFormula := nuExecutaFormula;


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

                       -- so para testes
                       stSql2 := ' UPDATE tmp_registro_evento_ferias_medias 
                                SET quantidade = '||nuExecutaFormula||'
                                WHERE cod_evento = '||reRegistro1.cod_evento||'
                             '; 
                       EXECUTE stSql2;
                       -- fim so para teste

                  END IF;

                  

                  --*****************************************
                  -- desdobra os valores/quantidades e grava                 
                  nuTotalValor := 0;
                  nuTotalQuantidade := 0;



                  IF inGeraAbono > 0 THEN
                      -- faz a relacao do valor ja tratado para o nr. de dias 
                      -- da forma de ferias com o nr.real de dias de abono e com 
                      -- o total de dias relativo a forma de ferias.
                      nuValor := arredondar( (nuValorFormula*inGeraAbono)/(inDiasGozo+inDiasAbono),2 ); 
                      nuQuantidade := arredondar( (nuQuantidadeFormula*inGeraAbono)/(inDiasGozo+inDiasAbono),2 ); 
                      stDesdobramento := 'A';

                      nuTotalValor := nuValor;
                      nuTotalQuantidade := nuQuantidade;

                      -- gravando o registro 
                      boGravouRegistro := gravaRegistroEventoFerias( inCodContrato,inCodPeriodoMovimentacao,inCodEvento, nuValor, nuQuantidade, stDesdobramento );



                  END IF;
                  IF inGeraAdiantamento > 0 THEN
                      nuValor := arredondar( (nuValorFormula*inGeraAdiantamento)/(inDiasGozo+inDiasAbono),2 ); 
                      nuQuantidade := arredondar( (nuQuantidadeFormula*inGeraAdiantamento)/(inDiasGozo+inDiasAbono),2 ); 
                      stDesdobramento := 'D';

                      nuTotalValor := nuTotalValor + nuValor;
                      nuTotalQuantidade := nuTotalValor + nuQuantidade;

                      -- gravando o registro 
                      boGravouRegistro := gravaRegistroEventoFerias( inCodContrato,inCodPeriodoMovimentacao,inCodEvento, nuValor, nuQuantidade, stDesdobramento );



                  END IF;
                  IF inGeraGozo > 0 THEN
                      nuValor := arredondar( (nuValorFormula*inGeraGozo)/(inDiasGozo+inDiasAbono),2 ); 
                      nuQuantidade := arredondar( (nuQuantidadeFormula*inGeraGozo)/(inDiasGozo+inDiasAbono),2 ); 
                      stDesdobramento := 'F';

                      nuTotalValor := nuTotalValor + nuValor;
                      nuTotalQuantidade := nuTotalValor + nuQuantidade;
                       
                      -- ajustar pois pode dar diferenca em funcao dos arredondamentos
                      -- necessaria verificacao em cada um dos casos pois pode ter adiantamento
                      -- e nao ter gozo.
                      IF nuExecutaFormula != (nuTotalValor + nuTotalQuantidade) THEN 
                          IF nuValor > 0 THEN
                          ELSE
                          END IF; 
                      END IF;

                      -- gravando o registro 
                      boGravouRegistro := gravaRegistroEventoFerias( inCodContrato,inCodPeriodoMovimentacao,inCodEvento, nuValor, nuQuantidade, stDesdobramento );



                  END IF;
                                        



               END IF;


           END IF;
           boGravouRegistro := removerBufferInteiro('inCodEvento') ;
        END LOOP;



     END IF;



  END IF;

  boGravouRegistro := removerBufferInteiro('inAvos');
END IF;
RETURN TRUE; 
--EXCEPTION    
--    WHEN others THEN 
--
--
--        RETURN FALSE;
END;
$$ LANGUAGE 'plpgsql';
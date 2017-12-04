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
--    * Data de Criação: 27/11/2006
--
--
--    * @author Desenvolvedor: Diego Lemos de Souza
--
--    * Casos de uso: uc-04.05.30
--
--    $Id: contraCheque.plsql 65718 2016-06-10 17:15:02Z evandro $
--*/
--DROP FUNCTION contraCheque(INTEGER,INTEGER,VARCHAR,INTEGER,INTEGER,VARCHAR,VARCHAR,INTEGER);
DROP TYPE colunasContraCheque CASCADE;
CREATE TYPE colunasContraCheque AS (
    registro                    INTEGER,
    cod_contrato                INTEGER,
    cod_contrato_servidor       INTEGER,
    numcgm                      INTEGER,
    nom_cgm                     VARCHAR(200),
    funcao                      VARCHAR,
    funcao_especialidade        VARCHAR,
    cbo                         VARCHAR,
    dt_admissao                 VARCHAR,
    dt_posse                    VARCHAR,    
    cod_periodo_movimentacao    INTEGER,
    orgao                       VARCHAR,
    local                       VARCHAR,
    mes_nascimento              VARCHAR,
    inOffSet                    INTEGER,
    boContinua                  BOOLEAN,
    boQuebraPagina              BOOLEAN,
    servidor_pis_pasep          VARCHAR,                                                               
    cpf                         VARCHAR,   
    rg                          VARCHAR,                                                                        
    nr_conta                    VARCHAR,                                        
    num_agencia                 VARCHAR,                                        
    nom_agencia                 VARCHAR,                                        
    num_banco                   VARCHAR,                                        
    nom_banco                   VARCHAR     
);

CREATE OR REPLACE FUNCTION contraCheque(INTEGER,INTEGER,VARCHAR,INTEGER,INTEGER,VARCHAR,VARCHAR,INTEGER,BOOLEAN,VARCHAR,VARCHAR) RETURNS SETOF colunasContraCheque AS $$

DECLARE
    inCodPeriodoMovimentacao                 ALIAS FOR $1;
    inQuantEventoPagina                      ALIAS FOR $2;
    stOrdem                                  ALIAS FOR $3;
    inFolha                                  ALIAS FOR $4;
    inCodComplementar                        ALIAS FOR $5;
    stDesdobramento                          ALIAS FOR $6;
    stFiltro                                 ALIAS FOR $7;
    inContratoReemissao                      ALIAS FOR $8;
    boDuplicar                               ALIAS FOR $9;
    stEntidade                               ALIAS FOR $10;
    stSituacao                               ALIAS FOR $11;
    stSql                                    VARCHAR:='';
    stSqlAux                                 VARCHAR:='';
    stSqlEventos                             VARCHAR:='';
    stExercicio                              VARCHAR;
    stTimestampFechamentoPeriodoMovimentacao VARCHAR;
    inContador                               INTEGER;
    inResto                                  INTEGER;
    inOffSet                                 INTEGER := 0;
    reRegistro                               RECORD;
    --reInformacoes                            RECORD;
    boContinua                               BOOLEAN;
    boQuebraPagina                           BOOLEAN;
    boProcessar                              BOOLEAN;
    boRetornaDadosConta                      BOOLEAN;
    rwContraCheque                           colunasContraCheque%ROWTYPE;
    crCursor                                 REFCURSOR;
BEGIN
    SELECT max(valor) as exercicio
      into stExercicio
      FROM administracao.configuracao 
     WHERE parametro = 'ano_exercicio';   
     
    stTimestampFechamentoPeriodoMovimentacao := ultimoTimestampPeriodoMovimentacao(inCodPeriodoMovimentacao, stEntidade);     

    stSql :=' SELECT contrato.registro
               , contrato.cod_contrato
               , sw_cgm.numcgm
               , sw_cgm.nom_cgm
               , (SELECT servidor_pis_pasep FROM sw_cgm_pessoa_fisica WHERE numcgm = sw_cgm.numcgm) as servidor_pis_pasep
               , (SELECT rg FROM sw_cgm_pessoa_fisica WHERE numcgm = sw_cgm.numcgm) as rg
               , (SELECT publico.mascara_cpf_cnpj(cpf, ''cpf'') FROM sw_cgm_pessoa_fisica WHERE numcgm = sw_cgm.numcgm) as cpf
               , cadastros.*
               , (SELECT max(cod_periodo_movimentacao) as cod_periodo_movimentacao FROM folhapagamento'||stEntidade||'.periodo_movimentacao) as cod_periodo_movimentacao
               , recuperaDescricaoOrgao(cadastros.cod_orgao,('''||stExercicio||'-01-01'')::date) as descricao_lotacao
               , (SELECT orgao FROM organograma.vw_orgao_nivel WHERE cod_orgao = cadastros.cod_orgao ) AS orgao
               , cadastros.descricao_local as local
               , to_char(sw_cgm_pessoa_fisica.dt_nascimento,''mm'') as mes_nascimento
            FROM (SELECT contrato_servidor.cod_contrato
                       , contrato_servidor.cod_contrato as cod_contrato_servidor
                       , cargo.descricao as descricao_funcao
                       , funcao.cbo_codigo as cbo
                       , funcao.descricao ||''/''|| funcao.cbo_codigo as funcao_cbo
                       , contrato_servidor_local.descricao_local
                       , contrato_servidor_local.cod_local
                       , contrato_servidor_funcao.cod_cargo as cod_funcao
                       , contrato_servidor_orgao.cod_orgao
                       , servidor.numcgm
                       , contrato_servidor.ativo
                       , 1 as cadastro 
                       , to_char(contrato_servidor_nomeacao_posse.dt_admissao,''dd/mm/yyyy'') as dt_admissao
                       , to_char(contrato_servidor_nomeacao_posse.dt_posse,''dd/mm/yyyy'') as dt_posse
                       , contrato_servidor_conta_salario.nr_conta
                       , contrato_servidor_conta_salario.num_agencia
                       , contrato_servidor_conta_salario.nom_agencia
                       , contrato_servidor_conta_salario.num_banco
                       , contrato_servidor_conta_salario.nom_banco
                    FROM pessoal'||stEntidade||'.contrato_servidor

               LEFT JOIN (     SELECT contrato_servidor_conta_salario_historico.*
                                    , agencia.*
                                    , banco.*
                                 FROM pessoal'||stEntidade||'.contrato_servidor_conta_salario_historico
                           INNER JOIN ( SELECT conta_salario_interno.cod_contrato
                                             , MAX(conta_salario_interno.timestamp) as timestamp
                                          FROM pessoal'||stEntidade||'.contrato_servidor_conta_salario_historico as conta_salario_interno
                                          WHERE timestamp <= '''||stTimestampFechamentoPeriodoMovimentacao||'''
                                       GROUP BY conta_salario_interno.cod_contrato
                                      ) as max_contrato_servidor_conta_salario_historico
                                   ON contrato_servidor_conta_salario_historico.cod_contrato = max_contrato_servidor_conta_salario_historico.cod_contrato
                                  AND contrato_servidor_conta_salario_historico.timestamp = max_contrato_servidor_conta_salario_historico.timestamp
                          INNER JOIN monetario.agencia
                                  ON contrato_servidor_conta_salario_historico.cod_banco = agencia.cod_banco
                                 AND contrato_servidor_conta_salario_historico.cod_agencia = agencia.cod_agencia
                          INNER JOIN monetario.banco
                                  ON agencia.cod_banco = banco.cod_banco
                         ) as contrato_servidor_conta_salario
                      ON contrato_servidor_conta_salario.cod_contrato = contrato_servidor.cod_contrato

               LEFT JOIN (SELECT local.descricao as descricao_local
                               , contrato_servidor_local.cod_contrato
                               , contrato_servidor_local.cod_local
                            FROM ( SELECT cod_contrato, cod_local, timestamp FROM pessoal'||stEntidade||'.contrato_servidor_local
                                   UNION
                                   SELECT cod_contrato, cod_local, timestamp FROM pessoal'||stEntidade||'.contrato_servidor_local_historico
                                 ) as contrato_servidor_local
                               , (          SELECT cod_contrato
                                                 , max(timestamp) as timestamp
                                              FROM ( SELECT cod_contrato, timestamp FROM pessoal'||stEntidade||'.contrato_servidor_local
                                                     UNION
                                                     SELECT cod_contrato, timestamp FROM pessoal'||stEntidade||'.contrato_servidor_local_historico
                                                   ) as contrato_servidor_local
                                             WHERE timestamp <= '''||stTimestampFechamentoPeriodoMovimentacao||'''
                                          GROUP BY cod_contrato
                                  ) as max_contrato_servidor_local
                               , organograma.local
                           WHERE contrato_servidor_local.cod_contrato = max_contrato_servidor_local.cod_contrato
                             AND contrato_servidor_local.timestamp = max_contrato_servidor_local.timestamp
                             AND contrato_servidor_local.cod_local = local.cod_local) as contrato_servidor_local
                      ON contrato_servidor.cod_contrato = contrato_servidor_local.cod_contrato
                       , pessoal'||stEntidade||'.servidor_contrato_servidor
                       , pessoal'||stEntidade||'.servidor
                       , pessoal'||stEntidade||'.contrato_servidor_nomeacao_posse
                       , (  SELECT cod_contrato
                                 , max(timestamp) as timestamp
                              FROM pessoal'||stEntidade||'.contrato_servidor_nomeacao_posse
                          GROUP BY cod_contrato) as max_contrato_servidor_nomeacao_posse
                       , pessoal'||stEntidade||'.contrato_servidor_funcao
                       , (  SELECT cod_contrato
                                 , max(timestamp) as timestamp
                              FROM pessoal'||stEntidade||'.contrato_servidor_funcao
                             WHERE to_char(contrato_servidor_funcao.vigencia, ''yyyymm'') <= 
                                   (SELECT to_char(dt_final, ''yyyymm'') 
                                      FROM folhapagamento'||stEntidade||'.periodo_movimentacao
                                     WHERE cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||')                              
                          GROUP BY cod_contrato) as max_contrato_servidor_funcao
                       , pessoal'||stEntidade||'.cargo
                       
                       , (   SELECT cargo.cod_cargo
                                 , cbo.codigo as cbo_codigo
                                 , cargo.descricao
                              FROM pessoal'||stEntidade||'.cargo
                                 , pessoal'||stEntidade||'.cbo_cargo
                                 , (  SELECT cod_cargo
                                           , max(timestamp) as timestamp
                                        FROM pessoal'||stEntidade||'.cbo_cargo
                                    GROUP BY cod_cargo) as max_cbo_cargo
                                 , pessoal'||stEntidade||'.cbo
                             WHERE cargo.cod_cargo = cbo_cargo.cod_cargo
                               AND cbo_cargo.cod_cargo = max_cbo_cargo.cod_cargo 
                               AND cbo_cargo.timestamp = max_cbo_cargo.timestamp
                               AND cbo_cargo.cod_cbo = cbo.cod_cbo

                            UNION

                            SELECT cargo.cod_cargo
                                 , cbo.codigo as cbo_codigo
                                 , cargo.descricao
                              FROM pessoal'||stEntidade||'.cargo
                                 , pessoal'||stEntidade||'.especialidade
                                 , pessoal'||stEntidade||'.cbo_especialidade
                                 , (  SELECT cod_especialidade
                                           , max(timestamp) as timestamp
                                        FROM pessoal'||stEntidade||'.cbo_especialidade
                                    GROUP BY cod_especialidade) as max_cod_especialidade
                                 , pessoal'||stEntidade||'.cbo
                             WHERE cargo.cod_cargo = especialidade.cod_cargo
                               AND especialidade.cod_especialidade = cbo_especialidade.cod_especialidade
                               AND cbo_especialidade.cod_especialidade = max_cod_especialidade.cod_especialidade 
                               AND cbo_especialidade.timestamp = max_cod_especialidade.timestamp
                               AND cbo_especialidade.cod_cbo = cbo.cod_cbo) as funcao

                       , pessoal'||stEntidade||'.contrato_servidor_orgao
                       , (  SELECT cod_contrato
                                 , max(timestamp) as timestamp
                              FROM pessoal'||stEntidade||'.contrato_servidor_orgao
                             WHERE timestamp <= '''||stTimestampFechamentoPeriodoMovimentacao||'''
                          GROUP BY cod_contrato) as max_contrato_servidor_orgao
                   WHERE servidor_contrato_servidor.cod_contrato = contrato_servidor.cod_contrato
                     AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                     AND contrato_servidor.cod_contrato = contrato_servidor_funcao.cod_contrato
                     AND contrato_servidor_funcao.cod_contrato = max_contrato_servidor_funcao.cod_contrato
                     AND contrato_servidor_funcao.timestamp = max_contrato_servidor_funcao.timestamp                     
                     AND contrato_servidor.cod_contrato = contrato_servidor_nomeacao_posse.cod_contrato
                     AND contrato_servidor_nomeacao_posse.cod_contrato = max_contrato_servidor_nomeacao_posse.cod_contrato
                     AND contrato_servidor_nomeacao_posse.timestamp = max_contrato_servidor_nomeacao_posse.timestamp                                          
                     AND contrato_servidor_funcao.cod_cargo = cargo.cod_cargo
                     AND cargo.cod_cargo = funcao.cod_cargo
                     AND contrato_servidor.cod_contrato = contrato_servidor_orgao.cod_contrato
                     AND contrato_servidor_orgao.cod_contrato = max_contrato_servidor_orgao.cod_contrato
                     AND contrato_servidor_orgao.timestamp = max_contrato_servidor_orgao.timestamp
                   UNION
                  SELECT contrato_pensionista.cod_contrato
                       , contrato_pensionista.cod_contrato_cedente as cod_contrato_servidor
                       , cargo.descricao as descricao_funcao
                       , funcao.cbo_codigo as cbo
                       , funcao.descricao ||''/''|| funcao.cbo_codigo as funcao_cbo
                       , contrato_servidor_local.descricao_local
                       , contrato_servidor_local.cod_local
                       , contrato_servidor_funcao.cod_cargo as cod_funcao
                       , contrato_pensionista_orgao.cod_orgao
                       , pensionista.numcgm
                       , true as ativo
                       , 2 as cadastro
                       , '''' as dt_admissao
                       , '''' as dt_posse
                       , contrato_pensionista_conta_salario.nr_conta
                       , contrato_pensionista_conta_salario.num_agencia
                       , contrato_pensionista_conta_salario.nom_agencia
                       , contrato_pensionista_conta_salario.num_banco
                       , contrato_pensionista_conta_salario.nom_banco   
                    FROM pessoal'||stEntidade||'.contrato_pensionista
                               
               LEFT JOIN (     SELECT contrato_pensionista_conta_salario.*
                                    , agencia.*
                                    , banco.*
                                 FROM pessoal'||stEntidade||'.contrato_pensionista_conta_salario
                           INNER JOIN ( SELECT conta_salario_interno.cod_contrato
                                             , MAX(conta_salario_interno.timestamp) as timestamp
                                          FROM pessoal'||stEntidade||'.contrato_pensionista_conta_salario as conta_salario_interno
                                         WHERE timestamp <= '''||stTimestampFechamentoPeriodoMovimentacao||'''
                                      GROUP BY conta_salario_interno.cod_contrato
                                      ) as max_contrato_pensionista_conta_salario
                                   ON contrato_pensionista_conta_salario.cod_contrato = max_contrato_pensionista_conta_salario.cod_contrato
                                  AND contrato_pensionista_conta_salario.timestamp = max_contrato_pensionista_conta_salario.timestamp
                           INNER JOIN monetario.agencia
                                   ON contrato_pensionista_conta_salario.cod_banco = agencia.cod_banco
                                  AND contrato_pensionista_conta_salario.cod_agencia = agencia.cod_agencia
                           INNER JOIN monetario.banco
                                   ON agencia.cod_banco = banco.cod_banco
                         ) as contrato_pensionista_conta_salario
                      ON contrato_pensionista_conta_salario.cod_contrato = contrato_pensionista.cod_contrato

               LEFT JOIN (SELECT local.descricao as descricao_local
                               , contrato_servidor_local.cod_contrato
                               , contrato_servidor_local.cod_local
                            FROM ( SELECT cod_contrato, cod_local, timestamp FROM pessoal'||stEntidade||'.contrato_servidor_local
                                   UNION
                                   SELECT cod_contrato, cod_local, timestamp FROM pessoal'||stEntidade||'.contrato_servidor_local_historico
                                 ) as contrato_servidor_local
                               , (          SELECT cod_contrato
                                                 , max(timestamp) as timestamp
                                              FROM ( SELECT cod_contrato, timestamp FROM pessoal'||stEntidade||'.contrato_servidor_local
                                                     UNION
                                                     SELECT cod_contrato, timestamp FROM pessoal'||stEntidade||'.contrato_servidor_local_historico
                                                   ) as contrato_servidor_local
                                             WHERE timestamp <= '''||stTimestampFechamentoPeriodoMovimentacao||'''
                                          GROUP BY cod_contrato
                                  ) as max_contrato_servidor_local
                               , organograma.local
                           WHERE contrato_servidor_local.cod_contrato = max_contrato_servidor_local.cod_contrato
                             AND contrato_servidor_local.timestamp = max_contrato_servidor_local.timestamp
                             AND contrato_servidor_local.cod_local = local.cod_local) as contrato_servidor_local
                      ON contrato_pensionista.cod_contrato_cedente = contrato_servidor_local.cod_contrato
                       , pessoal'||stEntidade||'.pensionista
                       , pessoal'||stEntidade||'.contrato_pensionista_orgao
                       , (  SELECT cod_contrato
                                 , max(timestamp) as timestamp
                              FROM pessoal'||stEntidade||'.contrato_pensionista_orgao
                             WHERE timestamp <= '''||stTimestampFechamentoPeriodoMovimentacao||'''
                          GROUP BY cod_contrato) as max_contrato_pensionista_orgao
                       , pessoal'||stEntidade||'.contrato_servidor_funcao
                       , (  SELECT cod_contrato
                                 , max(timestamp) as timestamp
                              FROM pessoal'||stEntidade||'.contrato_servidor_funcao
                             WHERE to_char(contrato_servidor_funcao.vigencia, ''yyyymm'') <= 
                                   (SELECT to_char(dt_final, ''yyyymm'')
                                      FROM folhapagamento'||stEntidade||'.periodo_movimentacao
                                     WHERE cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||')
                          GROUP BY cod_contrato) as max_contrato_servidor_funcao
                       , pessoal'||stEntidade||'.cargo
                       
                       , (   SELECT cargo.cod_cargo
                                 , cbo.codigo as cbo_codigo
                                 , cargo.descricao
                              FROM pessoal'||stEntidade||'.cargo
                                 , pessoal'||stEntidade||'.cbo_cargo
                                 , (  SELECT cod_cargo
                                           , max(timestamp) as timestamp
                                        FROM pessoal'||stEntidade||'.cbo_cargo
                                    GROUP BY cod_cargo) as max_cbo_cargo
                                 , pessoal'||stEntidade||'.cbo
                             WHERE cargo.cod_cargo = cbo_cargo.cod_cargo
                               AND cbo_cargo.cod_cargo = max_cbo_cargo.cod_cargo 
                               AND cbo_cargo.timestamp = max_cbo_cargo.timestamp
                               AND cbo_cargo.cod_cbo = cbo.cod_cbo

                            UNION

                            SELECT cargo.cod_cargo
                                 , cbo.codigo as cbo_codigo
                                 , cargo.descricao
                              FROM pessoal'||stEntidade||'.cargo
                                 , pessoal'||stEntidade||'.especialidade
                                 , pessoal'||stEntidade||'.cbo_especialidade
                                 , (  SELECT cod_especialidade
                                           , max(timestamp) as timestamp
                                        FROM pessoal'||stEntidade||'.cbo_especialidade
                                    GROUP BY cod_especialidade) as max_cod_especialidade
                                 , pessoal'||stEntidade||'.cbo
                             WHERE cargo.cod_cargo = especialidade.cod_cargo
                               AND especialidade.cod_especialidade = cbo_especialidade.cod_especialidade
                               AND cbo_especialidade.cod_especialidade = max_cod_especialidade.cod_especialidade 
                               AND cbo_especialidade.timestamp = max_cod_especialidade.timestamp
                               AND cbo_especialidade.cod_cbo = cbo.cod_cbo) as funcao

                   WHERE contrato_pensionista.cod_pensionista = pensionista.cod_pensionista
                     AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente
                     AND contrato_pensionista.cod_contrato = contrato_pensionista_orgao.cod_contrato
                     AND contrato_pensionista_orgao.cod_contrato = max_contrato_pensionista_orgao.cod_contrato
                     AND contrato_pensionista_orgao.timestamp = max_contrato_pensionista_orgao.timestamp
                     AND contrato_pensionista.cod_contrato_cedente = contrato_servidor_funcao.cod_contrato
                     AND contrato_servidor_funcao.cod_contrato = max_contrato_servidor_funcao.cod_contrato
                     AND contrato_servidor_funcao.timestamp = max_contrato_servidor_funcao.timestamp
                     AND contrato_servidor_funcao.cod_cargo = cargo.cod_cargo
                     AND cargo.cod_cargo = funcao.cod_cargo) as cadastros
               , pessoal'||stEntidade||'.contrato
               , sw_cgm
               , sw_cgm_pessoa_fisica
           WHERE contrato.cod_contrato = cadastros.cod_contrato
             AND cadastros.numcgm = sw_cgm.numcgm
             AND sw_cgm.numcgm = sw_cgm_pessoa_fisica.numcgm'||stFiltro;
             
    IF stSituacao = 'ativos' THEN
        stSql := stSql || ' AND recuperarSituacaoDoContrato(contrato.cod_contrato, '||inCodPeriodoMovimentacao||', '''||stEntidade||''') = ''A''';
    END IF;
    
    IF stSituacao = 'rescindidos' THEN
        stSql := stSql || ' AND recuperarSituacaoDoContrato(contrato.cod_contrato, '||inCodPeriodoMovimentacao||', '''||stEntidade||''') = ''R''';
    END IF;
    
    IF stSituacao = 'aposentados' THEN
        stSql := stSql || ' AND recuperarSituacaoDoContrato(contrato.cod_contrato, '||inCodPeriodoMovimentacao||', '''||stEntidade||''') = ''P''';
    END IF;
    
    IF stSituacao = 'pensionistas' THEN
        stSql := stSql || ' AND recuperarSituacaoDoContrato(contrato.cod_contrato, '||inCodPeriodoMovimentacao||', '''||stEntidade||''') = ''E''';
    END IF;
    
    IF inFolha = 0 THEN
        stSql := stSql || ' AND contrato.cod_contrato IN (SELECT registro_evento_complementar.cod_contrato
                                                            FROM folhapagamento'||stEntidade||'.evento_complementar_calculado
                                                               , folhapagamento'||stEntidade||'.registro_evento
                                                               , folhapagamento'||stEntidade||'.registro_evento_complementar
                                                               , pessoal'||stEntidade||'.contrato
                                                           WHERE evento_complementar_calculado.cod_evento         = registro_evento_complementar.cod_evento
                                                             AND evento_complementar_calculado.cod_registro       = registro_evento_complementar.cod_registro
                                                             AND evento_complementar_calculado.timestamp_registro = registro_evento_complementar.timestamp
                                                             AND evento_complementar_calculado.cod_configuracao   = registro_evento_complementar.cod_configuracao
                                                             AND registro_evento_complementar.cod_contrato = contrato.cod_contrato
                                                             AND registro_evento_complementar.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                                                             AND registro_evento_complementar.cod_complementar = '||inCodComplementar||'
                                                           GROUP BY registro_evento_complementar.cod_contrato)'; 

    END IF;
    IF inFolha = 1 THEN
        stSql := stSql || ' AND contrato.cod_contrato IN (SELECT registro_evento_periodo.cod_contrato
                                                            FROM folhapagamento'||stEntidade||'.evento_calculado
                                                               , folhapagamento'||stEntidade||'.registro_evento
                                                               , folhapagamento'||stEntidade||'.registro_evento_periodo
                                                               , pessoal'||stEntidade||'.contrato
                                                           WHERE evento_calculado.cod_evento = registro_evento.cod_evento
                                                             AND evento_calculado.cod_registro = registro_evento.cod_registro
                                                             AND evento_calculado.timestamp_registro = registro_evento.timestamp
                                                             AND registro_evento.cod_registro = registro_evento_periodo.cod_registro
                                                             AND registro_evento_periodo.cod_contrato = contrato.cod_contrato
                                                             AND registro_evento_periodo.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                                                           GROUP BY registro_evento_periodo.cod_contrato)'; 
    END IF;
    IF inFolha = 2 THEN
        stSql := stSql || ' AND contrato.cod_contrato IN (SELECT registro_evento_ferias.cod_contrato
                                                            FROM folhapagamento'||stEntidade||'.evento_ferias_calculado
                                                               , folhapagamento'||stEntidade||'.registro_evento_ferias
                                                               , pessoal'||stEntidade||'.contrato
                                                           WHERE evento_ferias_calculado.cod_evento         = registro_evento_ferias.cod_evento
                                                             AND evento_ferias_calculado.cod_registro       = registro_evento_ferias.cod_registro
                                                             AND evento_ferias_calculado.desdobramento      = registro_evento_ferias.desdobramento
                                                             AND evento_ferias_calculado.timestamp_registro = registro_evento_ferias.timestamp
                                                             AND registro_evento_ferias.cod_contrato = contrato.cod_contrato
                                                             AND registro_evento_ferias.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao;

        IF stDesdobramento <> '' THEN                                                             
            stSql := stSql || '                              AND registro_evento_ferias.desdobramento = '||stDesdobramento||'''';
        END IF;
        
        stSql := stSql || '                                GROUP BY registro_evento_ferias.cod_contrato)';
    END IF;
    IF inFolha = 3 THEN
        stSql := stSql || ' AND contrato.cod_contrato IN (SELECT registro_evento_decimo.cod_contrato
                                                            FROM folhapagamento'||stEntidade||'.evento_decimo_calculado
                                                               , folhapagamento'||stEntidade||'.registro_evento_decimo
                                                               , pessoal'||stEntidade||'.contrato
                                                           WHERE evento_decimo_calculado.cod_evento         = registro_evento_decimo.cod_evento
                                                             AND evento_decimo_calculado.cod_registro       = registro_evento_decimo.cod_registro
                                                             AND evento_decimo_calculado.desdobramento      = registro_evento_decimo.desdobramento
                                                             AND evento_decimo_calculado.timestamp_registro = registro_evento_decimo.timestamp
                                                             AND registro_evento_decimo.cod_contrato = contrato.cod_contrato
                                                             AND registro_evento_decimo.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao;

        IF stDesdobramento <> '' THEN                                                             
            stSql := stSql || '                              AND registro_evento_decimo.desdobramento = '''||stDesdobramento||'''';
        END IF;
        
        stSql := stSql || '                                GROUP BY registro_evento_decimo.cod_contrato)';
    END IF;
    IF inFolha = 4 THEN
        stSql := stSql || ' AND contrato.cod_contrato IN (SELECT registro_evento_rescisao.cod_contrato
                                                            FROM folhapagamento'||stEntidade||'.evento_rescisao_calculado
                                                               , folhapagamento'||stEntidade||'.registro_evento_rescisao
                                                               , pessoal'||stEntidade||'.contrato
                                                           WHERE evento_rescisao_calculado.cod_evento         = registro_evento_rescisao.cod_evento
                                                             AND evento_rescisao_calculado.cod_registro       = registro_evento_rescisao.cod_registro
                                                             AND evento_rescisao_calculado.desdobramento      = registro_evento_rescisao.desdobramento
                                                             AND evento_rescisao_calculado.timestamp_registro = registro_evento_rescisao.timestamp
                                                             AND registro_evento_rescisao.cod_contrato = contrato.cod_contrato
                                                             AND registro_evento_rescisao.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao;

        IF stDesdobramento <> '' THEN                                                             
            stSql := stSql || '                              AND registro_evento_rescisao.desdobramento = '||stDesdobramento||'''';
        END IF;
        
        stSql := stSql || '                                GROUP BY registro_evento_rescisao.cod_contrato)';
    END IF;
    stSql := stSql || ' ORDER BY '||stOrdem;
    
    
    boProcessar := true;
    IF inContratoReemissao != 0 THEN
        boProcessar := false;
    END IF;
    FOR reRegistro IN  EXECUTE stSql
    LOOP
        boRetornaDadosConta := false;

        IF boProcessar is false AND reRegistro.registro = inContratoReemissao THEN
            boProcessar := true;
        END IF;

        IF boProcessar is true THEN        
            IF inFolha = 0 THEN
                inContador := selectIntoInteger('SELECT count(1) as contador
                                          FROM folhapagamento'||stEntidade||'.evento_complementar_calculado
                                             , folhapagamento'||stEntidade||'.registro_evento_complementar
                                             , pessoal'||stEntidade||'.contrato
                                             , folhapagamento'||stEntidade||'.evento
                                         WHERE evento_complementar_calculado.cod_evento         = registro_evento_complementar.cod_evento
                                           AND evento_complementar_calculado.cod_registro       = registro_evento_complementar.cod_registro
                                           AND evento_complementar_calculado.cod_configuracao   = registro_evento_complementar.cod_configuracao
                                           AND evento_complementar_calculado.timestamp_registro = registro_evento_complementar.timestamp
                                           AND registro_evento_complementar.cod_contrato = contrato.cod_contrato
                                           AND registro_evento_complementar.cod_evento = evento.cod_evento
                                           AND contrato.cod_contrato = '||reRegistro.cod_contrato||'
                                           AND registro_evento_complementar.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                                           AND registro_evento_complementar.cod_complementar = '||inCodComplementar||'
                                           AND evento.natureza IN (''P'',''D'')');
            END IF;
            IF inFolha = 1 THEN
                inContador := selectIntoInteger('SELECT count(1) as contador
                                          FROM folhapagamento'||stEntidade||'.evento_calculado
                                             , folhapagamento'||stEntidade||'.registro_evento
                                             , folhapagamento'||stEntidade||'.registro_evento_periodo
                                             , pessoal'||stEntidade||'.contrato
                                             , folhapagamento'||stEntidade||'.evento
                                         WHERE evento_calculado.cod_evento = registro_evento.cod_evento
                                           AND evento_calculado.cod_registro = registro_evento.cod_registro
                                           AND evento_calculado.timestamp_registro = registro_evento.timestamp
                                           AND registro_evento.cod_registro = registro_evento_periodo.cod_registro
                                           AND registro_evento_periodo.cod_contrato = contrato.cod_contrato
                                           AND registro_evento.cod_evento = evento.cod_evento
                                           AND contrato.cod_contrato = '||reRegistro.cod_contrato||'
                                           AND registro_evento_periodo.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                                           AND evento.natureza IN (''P'',''D'')');        
            END IF;
            IF inFolha = 2 THEN
            
                stSqlAux :=            'SELECT count(1) as contador
                                          FROM folhapagamento'||stEntidade||'.evento_ferias_calculado
                                             , folhapagamento'||stEntidade||'.registro_evento_ferias
                                             , pessoal'||stEntidade||'.contrato
                                             , folhapagamento'||stEntidade||'.evento
                                         WHERE evento_ferias_calculado.cod_evento         = registro_evento_ferias.cod_evento
                                           AND evento_ferias_calculado.cod_registro       = registro_evento_ferias.cod_registro
                                           AND evento_ferias_calculado.desdobramento      = registro_evento_ferias.desdobramento
                                           AND evento_ferias_calculado.timestamp_registro = registro_evento_ferias.timestamp
                                           AND registro_evento_ferias.cod_contrato = contrato.cod_contrato
                                           AND registro_evento_ferias.cod_evento = evento.cod_evento
                                           AND contrato.cod_contrato = '||reRegistro.cod_contrato||'
                                           AND registro_evento_ferias.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao;
                                           
                IF stDesdobramento <> '' THEN
                    stSqlAux := stSqlAux || ' AND registro_evento_ferias.desdobramento = '''||stDesdobramento||'''';
                END IF;                                           

                stSqlAux := stSqlAux || ' AND evento.natureza IN (''P'',''D'')';
            
                inContador := selectIntoInteger(stSqlAux);
                
            END IF;
            IF inFolha = 3 THEN
            
                stSqlAux :=            'SELECT count(1) as contador
                                          FROM folhapagamento'||stEntidade||'.evento_decimo_calculado
                                             , folhapagamento'||stEntidade||'.registro_evento_decimo
                                             , pessoal'||stEntidade||'.contrato
                                             , folhapagamento'||stEntidade||'.evento
                                         WHERE evento_decimo_calculado.cod_evento         = registro_evento_decimo.cod_evento
                                           AND evento_decimo_calculado.cod_registro       = registro_evento_decimo.cod_registro
                                           AND evento_decimo_calculado.desdobramento      = registro_evento_decimo.desdobramento
                                           AND evento_decimo_calculado.timestamp_registro = registro_evento_decimo.timestamp
                                           AND registro_evento_decimo.cod_contrato = contrato.cod_contrato
                                           AND registro_evento_decimo.cod_evento = evento.cod_evento
                                           AND contrato.cod_contrato = '||reRegistro.cod_contrato||'
                                           AND registro_evento_decimo.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao;
                                           
                IF stDesdobramento <> '' THEN
                    stSqlAux := stSqlAux || ' AND registro_evento_decimo.desdobramento = '''||stDesdobramento||'''';
                END IF;                                           

                stSqlAux := stSqlAux || ' AND evento.natureza IN (''P'',''D'')';
                                           
                inContador := selectIntoInteger(stSqlAux);
                
            END IF;
            IF inFolha = 4 THEN
            
                stSqlAux :=            'SELECT count(1) as contador
                                          FROM folhapagamento'||stEntidade||'.evento_rescisao_calculado
                                             , folhapagamento'||stEntidade||'.registro_evento_rescisao
                                             , pessoal'||stEntidade||'.contrato
                                             , folhapagamento'||stEntidade||'.evento
                                         WHERE evento_rescisao_calculado.cod_evento         = registro_evento_rescisao.cod_evento
                                           AND evento_rescisao_calculado.cod_registro       = registro_evento_rescisao.cod_registro
                                           AND evento_rescisao_calculado.desdobramento      = registro_evento_rescisao.desdobramento
                                           AND evento_rescisao_calculado.timestamp_registro = registro_evento_rescisao.timestamp
                                           AND registro_evento_rescisao.cod_contrato = contrato.cod_contrato
                                           AND registro_evento_rescisao.cod_evento = evento.cod_evento
                                           AND contrato.cod_contrato = '||reRegistro.cod_contrato||'
                                           AND registro_evento_rescisao.cod_periodo_movimentacao = '||inCodPeriodoMovimentacao;

                IF stDesdobramento <> '' THEN
                    stSqlAux := stSqlAux || ' AND registro_evento_rescisao.desdobramento = '''||stDesdobramento||'''';
                END IF;                                           

                stSqlAux := stSqlAux || ' AND evento.natureza IN (''P'',''D'')';
                                           
                inContador := selectIntoInteger(stSqlAux);

            END IF;
            inResto := inContador % inQuantEventoPagina;
            inContador := inContador / inQuantEventoPagina;
            IF inResto > 0 THEN
                inContador := inContador + 1; 
            END IF;
            inOffSet := 0;        
         
            IF reRegistro.cadastro = '1' THEN -- Servidores
                -- Verifica no histórico se foi pago em crédito em banco ou foi pago em outra forma de pagamento
                -- Caso tenha sido pago em outra forma, retornar vazio os dados da conta salário
                stSql := '    SELECT contrato_servidor_forma_pagamento.cod_forma_pagamento
                                FROM pessoal'||stEntidade||'.contrato_servidor_forma_pagamento
                          INNER JOIN (  SELECT contrato_servidor_forma_pagamento.cod_contrato
                                             , max(timestamp) as timestamp
                                          FROM pessoal'||stEntidade||'.contrato_servidor_forma_pagamento
                                         WHERE timestamp <= '''||stTimestampFechamentoPeriodoMovimentacao||'''
                                      GROUP BY contrato_servidor_forma_pagamento.cod_contrato
                                     ) as max_contrato_servidor_forma_pagamento
                                  ON contrato_servidor_forma_pagamento.cod_contrato = max_contrato_servidor_forma_pagamento.cod_contrato
                              AND contrato_servidor_forma_pagamento.timestamp = max_contrato_servidor_forma_pagamento.timestamp
                            WHERE contrato_servidor_forma_pagamento.cod_contrato = '||reRegistro.cod_contrato;

                IF (selectIntoInteger(stSql) = 3) THEN
                    boRetornaDadosConta := true;
                END IF;
            END IF;

  
            FOR i IN 1..inContador LOOP
                IF i % 2 = 0 THEN
                    boQuebraPagina := true;
                ELSE
                    boQuebraPagina := false;
                END IF;
                IF i = inContador THEN
                    boContinua := false;
                    boQuebraPagina := true;
                ELSE 
                    boContinua := true;
                END IF;


                --Fonte para a duplicação do contra-cheque
                IF boDuplicar IS TRUE THEN
                    rwContraCheque.registro                   := reRegistro.registro; 
                    rwContraCheque.cod_contrato               := reRegistro.cod_contrato;
                    rwContraCheque.cod_contrato_servidor      := reRegistro.cod_contrato_servidor;
                    rwContraCheque.numcgm                     := reRegistro.numcgm; 
                    rwContraCheque.nom_cgm                    := reRegistro.nom_cgm;
                    rwContraCheque.cbo                        := reRegistro.cbo; 
                    rwContraCheque.dt_admissao                := reRegistro.dt_admissao; 
                    rwContraCheque.dt_posse                   := reRegistro.dt_posse; 
                    rwContraCheque.funcao                     := reRegistro.descricao_funcao; 
                    rwContraCheque.funcao_especialidade       := reRegistro.descricao_funcao; 
                    rwContraCheque.cod_periodo_movimentacao   := reRegistro.cod_periodo_movimentacao; 
                    rwContraCheque.orgao                      := reRegistro.descricao_lotacao; 
                    rwContraCheque.local                      := reRegistro.local;
                    rwContraCheque.mes_nascimento             := reRegistro.mes_nascimento;
                    rwContraCheque.inOffSet                   := inOffSet;
                    rwContraCheque.boContinua                 := boContinua;
                    rwContraCheque.boQuebraPagina             := boQuebraPagina;
                    rwContraCheque.servidor_pis_pasep         := reRegistro.servidor_pis_pasep;                                                               
                    rwContraCheque.cpf                        := reRegistro.cpf;                                                                                                 
                    rwContraCheque.rg                         := reRegistro.rg;                                                                                                  
                    
                    IF (boRetornaDadosConta = TRUE AND reRegistro.cadastro = '1') OR reRegistro.cadastro = '2' THEN
                        rwContraCheque.nr_conta                   := reRegistro.nr_conta;                                                            
                        rwContraCheque.num_agencia                := reRegistro.num_agencia;                                                         
                        rwContraCheque.nom_agencia                := reRegistro.nom_agencia;                                                         
                        rwContraCheque.num_banco                  := reRegistro.num_banco;                                                           
                        rwContraCheque.nom_banco                  := reRegistro.nom_banco;                                                                           
                    END IF;

                    RETURN NEXT rwContraCheque;
                END IF;

                rwContraCheque.registro                   := reRegistro.registro; 
                rwContraCheque.cod_contrato               := reRegistro.cod_contrato;
                rwContraCheque.cod_contrato_servidor      := reRegistro.cod_contrato_servidor;
                rwContraCheque.numcgm                     := reRegistro.numcgm; 
                rwContraCheque.nom_cgm                    := reRegistro.nom_cgm;
                rwContraCheque.cbo                        := reRegistro.cbo; 
                rwContraCheque.dt_admissao                := reRegistro.dt_admissao;
                rwContraCheque.dt_posse                   := reRegistro.dt_posse; 
                rwContraCheque.funcao                     := reRegistro.descricao_funcao; 
                rwContraCheque.funcao_especialidade       := reRegistro.descricao_funcao; 
                rwContraCheque.cod_periodo_movimentacao   := reRegistro.cod_periodo_movimentacao; 
                rwContraCheque.orgao                      := reRegistro.descricao_lotacao; 
                rwContraCheque.local                      := reRegistro.local;
                rwContraCheque.mes_nascimento             := reRegistro.mes_nascimento;
                rwContraCheque.inOffSet                   := inOffSet;
                rwContraCheque.boContinua                 := boContinua;
                rwContraCheque.boQuebraPagina             := boQuebraPagina;
                rwContraCheque.servidor_pis_pasep         := reRegistro.servidor_pis_pasep;                                                               
                rwContraCheque.cpf                        := reRegistro.cpf;                                                                                                 
                rwContraCheque.rg                         := reRegistro.rg;    
                                                                                              
                IF (boRetornaDadosConta = TRUE AND reRegistro.cadastro = '1') OR reRegistro.cadastro = '2' THEN
                    rwContraCheque.nr_conta                   := reRegistro.nr_conta;                                                            
                    rwContraCheque.num_agencia                := reRegistro.num_agencia;                                                         
                    rwContraCheque.nom_agencia                := reRegistro.nom_agencia;                                                         
                    rwContraCheque.num_banco                  := reRegistro.num_banco;                                                           
                    rwContraCheque.nom_banco                  := reRegistro.nom_banco;                                                                           
                END IF;

                inOffSet := inOffSet + inQuantEventoPagina;
                RETURN NEXT rwContraCheque;
            END LOOP;
        END IF;
    END LOOP;
    RETURN;
END;
$$LANGUAGE 'plpgsql';

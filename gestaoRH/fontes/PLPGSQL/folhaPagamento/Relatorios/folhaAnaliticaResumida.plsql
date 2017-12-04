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
--    * @author Analista: Diego Lemos de Souza
--    * @author Desenvolvedor: Diego Lemos de Souza
--
--    * @package URBEM
--    * @subpackage
--
--    $Revision: 28047 $
--    $Name$
--    $Author: alex $
--    $Date: 2008-02-14 12:54:21 -0200 (Qui, 14 Fev 2008) $
--
--    * Casos de uso: uc-04.05.50
--*/

--DROP FUNCTION folhaAnaliticaResumida(INTEGER,INTEGER,INTEGER,VARCHAR,VARCHAR,INTEGER,VARCHAR);
--DROP FUNCTION folhaSintetica(INTEGER,INTEGER,INTEGER,VARCHAR,VARCHAR,INTEGER,VARCHAR);
--DROP FUNCTION folhaAnalitica(INTEGER,INTEGER,INTEGER,VARCHAR,VARCHAR,INTEGER,VARCHAR);
--DROP FUNCTION eventosCalculadosFolhaAnaliticaResumida(INTEGER,INTEGER,INTEGER,INTEGER,VARCHAR);
--DROP FUNCTION eventosCalculadosFolhaAnalitica(INTEGER,INTEGER,INTEGER,INTEGER,VARCHAR,VARCHAR,VARCHAR);
--DROP FUNCTION eventosCalculadosComplementarFolhaAnalitica(INTEGER,INTEGER,INTEGER);

DROP TYPE colunasAnaliticaResumida CASCADE;
CREATE TYPE colunasAnaliticaResumida AS (
    codigoP                     VARCHAR, 
    descricaoP                  VARCHAR, 
    quantidadeP                 NUMERIC(14,2), 
    valorP                      NUMERIC(14,2), 
    desdobramentoP              VARCHAR, 

    codigoD                     VARCHAR, 
    descricaoD                  VARCHAR, 
    quantidadeD                 NUMERIC(14,2), 
    valorD                      NUMERIC(14,2), 
    desdobramentoD              VARCHAR, 

    codigoB                     VARCHAR, 
    descricaoB                  VARCHAR, 
    quantidadeB                 NUMERIC(14,2), 
    valorB                      NUMERIC(14,2), 
    desdobramentoB              VARCHAR, 

    parcela                     INTEGER,
    registro                    INTEGER,
    cod_contrato                INTEGER,
    numcgm                      INTEGER,
    nom_cgm                     VARCHAR(200),
    descricao_regime_funcao     VARCHAR,
    descricao_funcao            VARCHAR,
    descricao_padrao            VARCHAR,
    orgao                       VARCHAR,
    descricao_lotacao           VARCHAR,
    descricao_local             VARCHAR,
    dt_nomeacao                 VARCHAR,
    dt_admissao                 VARCHAR,
    dt_posse                    VARCHAR,
    hr_mensais                  VARCHAR,
    valor                       VARCHAR,
    num_banco                   VARCHAR,
    descricao_banco             VARCHAR,
    dt_contagem_inicial         VARCHAR
);

DROP TYPE colunasSintetica CASCADE;
CREATE TYPE colunasSintetica AS (
    registro                    INTEGER,
    cod_contrato                INTEGER,
    numcgm                      INTEGER,
    nom_cgm                     VARCHAR(200),
    orgao                       VARCHAR,
    descricao_lotacao           VARCHAR,
    proventos                   NUMERIC(15,2),
    descontos                   NUMERIC(15,2),
    previdencia                 NUMERIC(15,2),
    irrf                        NUMERIC(15,2),
    liquido                     NUMERIC(15,2),
    num_banco                   VARCHAR,
    descricao_banco             VARCHAR,
    dt_contagem_inicial         VARCHAR
);

DROP TYPE colunasAnalitica CASCADE;
CREATE TYPE colunasAnalitica AS (
    registro                    INTEGER,
    cod_contrato                INTEGER,
    numcgm                      INTEGER,
    nom_cgm                     VARCHAR(200),
    orgao                       VARCHAR,
    descricao_lotacao           VARCHAR,
    descricao_regime            VARCHAR,
    descricao_sub_divisao       VARCHAR,
    descricao_cargo             VARCHAR,
    descricao_especialidade     VARCHAR,
    descricao_regime_funcao     VARCHAR,
    descricao_sub_divisao_funcao    VARCHAR,
    descricao_funcao                VARCHAR,
    descricao_especialidade_funcao  VARCHAR,    
    descricao_local             VARCHAR,    
    horas_mensais               VARCHAR,    
    situacao                    VARCHAR,    
    previdencia_oficial         VARCHAR,    
    descricao_padrao            VARCHAR,    
    descricao_progressao        VARCHAR,    
    admissao_posse              VARCHAR,    
    multiplos                   VARCHAR,
    valor                       VARCHAR,
    num_banco                   VARCHAR,
    descricao_banco             VARCHAR,
    dt_contagem_inicial         VARCHAR
);

DROP TYPE colunasEventosCalculadosComplementaresAnalitica CASCADE;
CREATE TYPE colunasEventosCalculadosComplementaresAnalitica AS (
    base_previdencia            NUMERIC(15,2),
    desc_inss                   NUMERIC(15,2),
    base_irrf                   NUMERIC(15,2),
    desc_irrf                   NUMERIC(15,2),
    base_fgts                   NUMERIC(15,2),
    valor_recolhido_fgts        NUMERIC(15,2),
    valor_contribuicao          NUMERIC(15,2)

);

DROP TYPE colunasEventosCalculadosAnaliticaResumida CASCADE;
CREATE TYPE colunasEventosCalculadosAnaliticaResumida AS (
    codigoD                     VARCHAR,
    descricaoD                  VARCHAR,
    quantidadeD                 NUMERIC(15,2),
    valorD                      NUMERIC(15,2),
    desdobramentoD              VARCHAR,
    codigoP                     VARCHAR,
    descricaoP                  VARCHAR,
    quantidadeP                 NUMERIC(15,2),
    valorP                      NUMERIC(15,2),
    desdobramentoP              VARCHAR,
    codigoB                     VARCHAR,
    descricaoB                  VARCHAR,
    quantidadeB                 NUMERIC(15,2),
    valorB                      NUMERIC(15,2),
    desdobramentoB              VARCHAR
);

DROP TYPE colunasEventosCalculadosAnalitica CASCADE;
CREATE TYPE colunasEventosCalculadosAnalitica AS (
    codigoD                     VARCHAR,
    descricaoD                  VARCHAR,
    quantidadeD                 NUMERIC(15,2),
    valorD                      NUMERIC(15,2),
    desdobramentoD              VARCHAR,
    codigoE                     VARCHAR,
    descricaoE                  VARCHAR,
    quantidadeE                 NUMERIC(15,2),
    valorE                      NUMERIC(15,2),
    desdobramentoE              VARCHAR
);

CREATE OR REPLACE FUNCTION montaConsultaFolhasAnaliticaResumida(INTEGER,INTEGER,VARCHAR,VARCHAR,INTEGER,VARCHAR,VARCHAR,VARCHAR,INTEGER) RETURNS VARCHAR AS $$
DECLARE
    inCodConfiguracao           ALIAS FOR $1;
    inCodPeriodoMovimentacao    ALIAS FOR $2;
    stFiltro                    ALIAS FOR $3;
    stOrdenacao                 ALIAS FOR $4;
    inCodAtributo               ALIAS FOR $5;
    stValorAtributo             ALIAS FOR $6;
    stEntidade                  ALIAS FOR $7;
    stExercicio                 ALIAS FOR $8;
    inCodComplementar           ALIAS FOR $9;
    stSql                       VARCHAR := '';
    stDataContagemInicial       VARCHAR := '';
    stSelectServidorAtributo    VARCHAR := '';
    stFromServidorAtributo      VARCHAR := '';
    stSelectPensionistaAtributo VARCHAR := '';
    stFromPensionistaAtributo   VARCHAR := '';
    stDataFinalCompetencia      VARCHAR := '';
    boCriaTabelas               BOOLEAN;
BEGIN

    stDataFinalCompetencia := selectIntoVarchar('SELECT to_char(dt_final,'||quote_literal('dd/mm/yyyy')||')
                                                   FROM folhapagamento'|| stEntidade ||'.periodo_movimentacao
                                                  WHERE cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'');

    SELECT valor
      INTO stDataContagemInicial
      FROM administracao.configuracao
     WHERE cod_modulo = 22
       AND exercicio = stExercicio
       AND parametro = 'dtContagemInicial';

    /**************************************************
    * Monta os filtros caso pesquisa por atributos
    ***************************************************/
    IF stValorAtributo = '''''' THEN
        stSelectServidorAtributo := '   , ( CASE WHEN atributo_dinamico.cod_tipo = 3 OR
                                                      atributo_dinamico.cod_tipo = 4
                                                THEN ( SELECT atributo_valor_padrao.valor_padrao
                                                         FROM administracao.atributo_valor_padrao
                                                        WHERE atributo_valor_padrao.cod_cadastro = ultimo_atributo_contrato_servidor_valor.cod_cadastro
                                                          AND atributo_valor_padrao.cod_modulo   = ultimo_atributo_contrato_servidor_valor.cod_modulo
                                                          AND atributo_valor_padrao.cod_atributo = ultimo_atributo_contrato_servidor_valor.cod_atributo
                                                          AND atributo_valor_padrao.cod_valor::VARCHAR    = ultimo_atributo_contrato_servidor_valor.valor)
                                                ELSE ultimo_atributo_contrato_servidor_valor.valor
                                            END ) AS valor_label
                                        , ultimo_atributo_contrato_servidor_valor.valor
                                        , ultimo_atributo_contrato_servidor_valor.cod_atributo';
        stFromServidorAtributo   := ' INNER JOIN ultimo_atributo_contrato_servidor_valor('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||') as ultimo_atributo_contrato_servidor_valor
                                              ON ultimo_atributo_contrato_servidor_valor.cod_contrato = contrato_servidor.cod_contrato
                                             AND ultimo_atributo_contrato_servidor_valor.cod_atributo = '|| inCodAtributo ||'                                             
                                      INNER JOIN administracao.atributo_dinamico
                                              ON atributo_dinamico.cod_atributo = ultimo_atributo_contrato_servidor_valor.cod_atributo
                                             AND atributo_dinamico.cod_cadastro = ultimo_atributo_contrato_servidor_valor.cod_cadastro
                                             AND atributo_dinamico.cod_modulo = ultimo_atributo_contrato_servidor_valor.cod_modulo
                                    ';

        stSelectPensionistaAtributo := ',  ( CASE WHEN atributo_dinamico.cod_tipo = 3 OR
                                                        atributo_dinamico.cod_tipo = 4
                                                THEN (  SELECT atributo_valor_padrao.valor_padrao
                                                          FROM administracao.atributo_valor_padrao
                                                         WHERE atributo_valor_padrao.cod_cadastro = ultimo_atributo_contrato_pensionista.cod_cadastro
                                                           AND atributo_valor_padrao.cod_modulo   = ultimo_atributo_contrato_pensionista.cod_modulo
                                                           AND atributo_valor_padrao.cod_atributo = ultimo_atributo_contrato_pensionista.cod_atributo
                                                           AND atributo_valor_padrao.cod_valor::VARCHAR    = ultimo_atributo_contrato_pensionista.valor)
                                                ELSE ultimo_atributo_contrato_pensionista.valor
                                            END) AS valor_label
                                       , ultimo_atributo_contrato_pensionista.valor
                                       , ultimo_atributo_contrato_pensionista.cod_atributo';
        stFromPensionistaAtributo   := ' INNER JOIN ultimo_atributo_contrato_pensionista('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||') as ultimo_atributo_contrato_pensionista
                                                 ON ultimo_atributo_contrato_pensionista.cod_contrato = contrato_pensionista.cod_contrato
                                                AND ultimo_atributo_contrato_pensionista.cod_atributo = '|| inCodAtributo ||'                                                
                                         INNER JOIN administracao.atributo_dinamico
                                                 ON atributo_dinamico.cod_atributo = ultimo_atributo_contrato_pensionista.cod_atributo
                                                AND atributo_dinamico.cod_cadastro = ultimo_atributo_contrato_pensionista.cod_cadastro
                                                AND atributo_dinamico.cod_modulo = ultimo_atributo_contrato_pensionista.cod_modulo
                                       ';

    ELSEIF TRIM(stValorAtributo) != '' AND stValorAtributo IS NOT NULL THEN
        stSelectServidorAtributo := '   , ( CASE WHEN atributo_dinamico.cod_tipo = 3 OR
                                                      atributo_dinamico.cod_tipo = 4
                                                THEN ( SELECT atributo_valor_padrao.valor_padrao
                                                         FROM administracao.atributo_valor_padrao
                                                        WHERE atributo_valor_padrao.cod_cadastro = ultimo_atributo_contrato_servidor_valor.cod_cadastro
                                                          AND atributo_valor_padrao.cod_modulo   = ultimo_atributo_contrato_servidor_valor.cod_modulo
                                                          AND atributo_valor_padrao.cod_atributo = ultimo_atributo_contrato_servidor_valor.cod_atributo
                                                          AND atributo_valor_padrao.cod_valor::VARCHAR    = ultimo_atributo_contrato_servidor_valor.valor)
                                                ELSE ultimo_atributo_contrato_servidor_valor.valor
                                            END ) AS valor_label
                                        , ultimo_atributo_contrato_servidor_valor.valor
                                        , ultimo_atributo_contrato_servidor_valor.cod_atributo';
        stFromServidorAtributo   := ' INNER JOIN ultimo_atributo_contrato_servidor_valor('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||') as ultimo_atributo_contrato_servidor_valor
                                              ON ultimo_atributo_contrato_servidor_valor.cod_contrato = contrato_servidor.cod_contrato
                                             AND ultimo_atributo_contrato_servidor_valor.cod_atributo = '|| inCodAtributo ||'
                                             AND ultimo_atributo_contrato_servidor_valor.valor IN ('|| stValorAtributo ||')
                                      INNER JOIN administracao.atributo_dinamico
                                              ON atributo_dinamico.cod_atributo = ultimo_atributo_contrato_servidor_valor.cod_atributo
                                             AND atributo_dinamico.cod_cadastro = ultimo_atributo_contrato_servidor_valor.cod_cadastro
                                             AND atributo_dinamico.cod_modulo = ultimo_atributo_contrato_servidor_valor.cod_modulo
                                    ';

        stSelectPensionistaAtributo := ',  ( CASE WHEN atributo_dinamico.cod_tipo = 3 OR
                                                        atributo_dinamico.cod_tipo = 4
                                                THEN (  SELECT atributo_valor_padrao.valor_padrao
                                                          FROM administracao.atributo_valor_padrao
                                                         WHERE atributo_valor_padrao.cod_cadastro = ultimo_atributo_contrato_pensionista.cod_cadastro
                                                           AND atributo_valor_padrao.cod_modulo   = ultimo_atributo_contrato_pensionista.cod_modulo
                                                           AND atributo_valor_padrao.cod_atributo = ultimo_atributo_contrato_pensionista.cod_atributo
                                                           AND atributo_valor_padrao.cod_valor::VARCHAR    = ultimo_atributo_contrato_pensionista.valor)
                                                ELSE ultimo_atributo_contrato_pensionista.valor
                                            END) AS valor_label
                                       , ultimo_atributo_contrato_pensionista.valor
                                       , ultimo_atributo_contrato_pensionista.cod_atributo';
        stFromPensionistaAtributo   := ' INNER JOIN ultimo_atributo_contrato_pensionista('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||') as ultimo_atributo_contrato_pensionista
                                                 ON ultimo_atributo_contrato_pensionista.cod_contrato = contrato_pensionista.cod_contrato
                                                AND ultimo_atributo_contrato_pensionista.cod_atributo = '|| inCodAtributo ||'
                                                AND ultimo_atributo_contrato_pensionista.valor IN ('|| stValorAtributo ||')
                                         INNER JOIN administracao.atributo_dinamico
                                                 ON atributo_dinamico.cod_atributo = ultimo_atributo_contrato_pensionista.cod_atributo
                                                AND atributo_dinamico.cod_cadastro = ultimo_atributo_contrato_pensionista.cod_cadastro
                                                AND atributo_dinamico.cod_modulo = ultimo_atributo_contrato_pensionista.cod_modulo
                                       ';
    END IF;

    boCriaTabelas := criaTabelasTemporariasFolhaAnaliticaResumida(inCodConfiguracao, inCodPeriodoMovimentacao, inCodComplementar, stEntidade);
    /**************************************************
    * Monta a consulta de retorno da PL
    ***************************************************/
    stSql := '
            SELECT contrato.registro
                 , servidor_pensionista.*
              FROM pessoal'|| stEntidade ||'.contrato
                 , (
                    -- Inicio consulta servidores (ativos, aposentados e rescindidos)
                        SELECT contrato_servidor.cod_contrato
                             , contrato_servidor.cod_cargo
                             , recuperarSituacaoDoContrato(contrato_servidor.cod_contrato,'|| inCodPeriodoMovimentacao ||','|| quote_literal(stEntidade) ||') as situacao
                             , sw_cgm.nom_cgm
                             , sw_cgm.numcgm
                             , agencia.cod_agencia
                             , banco.cod_banco
                             , ultimo_contrato_servidor_conta_salario.nr_conta
                             , banco.num_banco
                             , banco.nom_banco as descricao_banco
                             , agencia.num_agencia
                             , agencia.nom_agencia
                             , ultimo_contrato_servidor_orgao.cod_orgao
                             , recuperaDescricaoOrgao(ultimo_contrato_servidor_orgao.cod_orgao, to_date('|| quote_literal(stDataFinalCompetencia) ||', ''dd/mm/yyyy'')) as descricao_lotacao
                             , ( SELECT orgao
                                   FROM organograma.vw_orgao_nivel
                                  WHERE cod_orgao = ultimo_contrato_servidor_orgao.cod_orgao) as orgao
                             , local.cod_local
                             , local.descricao as descricao_local
                             , to_char(ultimo_contrato_servidor_nomeacao_posse.dt_nomeacao,''dd/mm/yyyy'') as dt_nomeacao
                             , to_char(ultimo_contrato_servidor_nomeacao_posse.dt_posse,''dd/mm/yyyy'') as dt_posse
                             , to_char(ultimo_contrato_servidor_nomeacao_posse.dt_admissao,''dd/mm/yyyy'') as dt_admissao
                             , to_char(ultimo_contrato_servidor_nomeacao_posse.dt_inicio_contagem,''dd/mm/yyyy'') as dt_contagem_inicial
                             , to_char(ultimo_contrato_servidor_nomeacao_posse.dt_admissao,''dd/mm/yyyy'')||''-''|| to_char(ultimo_contrato_servidor_nomeacao_posse.dt_posse,''dd/mm/yyyy'') as admissao_posse
                             , (SELECT descricao FROM pessoal'|| stEntidade ||'.cargo WHERE cod_cargo = ultimo_contrato_servidor_funcao.cod_cargo) as descricao_funcao
                             , ultimo_contrato_servidor_funcao.cod_cargo as cod_funcao
                             , (SELECT descricao FROM pessoal'|| stEntidade ||'.cargo WHERE cod_cargo = contrato_servidor.cod_cargo) as descricao_cargo
                             , (SELECT descricao FROM pessoal'|| stEntidade ||'.sub_divisao WHERE cod_sub_divisao = contrato_servidor.cod_sub_divisao) as descricao_sub_divisao
                             , (SELECT descricao FROM pessoal'|| stEntidade ||'.regime WHERE cod_regime = contrato_servidor.cod_regime) as descricao_regime
                             , (SELECT descricao FROM pessoal'|| stEntidade ||'.sub_divisao WHERE cod_sub_divisao = ultimo_contrato_servidor_sub_divisao_funcao.cod_sub_divisao_funcao) as descricao_sub_divisao_funcao
                             , (SELECT cod_sub_divisao_funcao FROM pessoal'|| stEntidade ||'.sub_divisao WHERE cod_sub_divisao = ultimo_contrato_servidor_sub_divisao_funcao.cod_sub_divisao_funcao) as cod_sub_divisao_funcao
                             , ultimo_contrato_servidor_padrao.cod_padrao
                             , (SELECT descricao FROM folhapagamento'|| stEntidade ||'.padrao WHERE padrao.cod_padrao = ultimo_contrato_servidor_padrao.cod_padrao) as descricao_padrao
                             , contrato_servidor.cod_sub_divisao
                             , contrato_servidor.cod_regime
                             , (SELECT descricao FROM pessoal'|| stEntidade ||'.regime WHERE cod_regime = ultimo_contrato_servidor_regime_funcao.cod_regime_funcao) as descricao_regime_funcao
                             , (SELECT cod_regime FROM pessoal'|| stEntidade ||'.regime WHERE cod_regime = ultimo_contrato_servidor_regime_funcao.cod_regime_funcao) as cod_regime_funcao
                             , (SELECT descricao FROM pessoal'|| stEntidade ||'.especialidade WHERE especialidade.cod_especialidade = ultimo_contrato_servidor_especialidade_funcao.cod_especialidade_funcao) as descricao_especialidade_funcao
                             , ultimo_contrato_servidor_especialidade_funcao.cod_especialidade_funcao
                             , contrato_servidor_especialidade_cargo.cod_especialidade as cod_especialidade_cargo
                             , especialidade.descricao as descricao_especialidade
                             , ultimo_contrato_servidor_salario.horas_mensais
                             '|| stSelectServidorAtributo ||'
                             , tabelaEventos.valorP AS valorP
                             , tabelaEventos.quantidadeP AS quantidadeP
                             , tabelaEventos.codigoP AS codigoP
                             , tabelaEventos.cod_eventoP AS cod_eventoP
                             , tabelaEventos.descricaoP as descricaoP
                             , tabelaEventos.naturezaP AS naturezaP
                             , tabelaEventos.desdobramentoP AS desdobramentoP

                             , tabelaEventos.valorD AS valorD
                             , tabelaEventos.quantidadeD AS quantidadeD
                             , tabelaEventos.codigoD AS codigoD
                             , tabelaEventos.cod_eventoD AS cod_eventoD
                             , tabelaEventos.descricaoD AS descricaoD
                             , tabelaEventos.naturezaD AS naturezaD
                             , tabelaEventos.desdobramentoD AS desdobramentoD

                             , tabelaEventos.valorB AS valorB
                             , tabelaEventos.quantidadeB AS quantidadeB
                             , tabelaEventos.codigoB AS codigoB
                             , tabelaEventos.cod_eventoB AS cod_eventoB
                             , tabelaEventos.descricaoB AS descricaoB
                             , tabelaEventos.naturezaB AS naturezaB
                             , tabelaEventos.desdobramentoB AS desdobramentoB
                             , tabelaEventos.parcela

                          FROM pessoal'|| stEntidade ||'.contrato_servidor
                          JOIN eventos_tmp AS tabelaEventos
                            ON contrato_servidor.cod_contrato = tabelaEventos.cod_contrato

                    INNER JOIN pessoal'|| stEntidade ||'.servidor_contrato_servidor
                            ON contrato_servidor.cod_contrato = servidor_contrato_servidor.cod_contrato
                    INNER JOIN pessoal'|| stEntidade ||'.servidor
                            ON servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                    INNER JOIN sw_cgm
                            ON servidor.numcgm = sw_cgm.numcgm
                    INNER JOIN ultimo_contrato_servidor_orgao('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||') as ultimo_contrato_servidor_orgao
                            ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_orgao.cod_contrato
                    INNER JOIN ultimo_contrato_servidor_nomeacao_posse('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||') as ultimo_contrato_servidor_nomeacao_posse
                            ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_nomeacao_posse.cod_contrato
                    INNER JOIN ultimo_contrato_servidor_funcao('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||') as ultimo_contrato_servidor_funcao
                            ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_funcao.cod_contrato
                    INNER JOIN ultimo_contrato_servidor_regime_funcao('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||') as ultimo_contrato_servidor_regime_funcao
                            ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_regime_funcao.cod_contrato
                    INNER JOIN ultimo_contrato_servidor_padrao('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||') as ultimo_contrato_servidor_padrao
                            ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_padrao.cod_contrato
                    INNER JOIN ultimo_contrato_servidor_salario('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||') as ultimo_contrato_servidor_salario
                            ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_salario.cod_contrato
                    INNER JOIN ultimo_contrato_servidor_sub_divisao_funcao('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||') as ultimo_contrato_servidor_sub_divisao_funcao
                            ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_sub_divisao_funcao.cod_contrato
                    INNER JOIN evento_calculado_tmp AS registro_evento_calculado
                            ON registro_evento_calculado.cod_contrato = contrato_servidor.cod_contrato
                            '|| stFromServidorAtributo ||'
                     LEFT JOIN pessoal'|| stEntidade ||'.contrato_servidor_especialidade_cargo
                            ON contrato_servidor.cod_contrato = contrato_servidor_especialidade_cargo.cod_contrato
                     LEFT JOIN pessoal'|| stEntidade ||'.especialidade
                            ON especialidade.cod_especialidade = contrato_servidor_especialidade_cargo.cod_especialidade
                     LEFT JOIN ultimo_contrato_servidor_local('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||') as ultimo_contrato_servidor_local
                            ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_local.cod_contrato
                     LEFT JOIN organograma.local
                            ON local.cod_local = ultimo_contrato_servidor_local.cod_local
                     LEFT JOIN ultimo_contrato_servidor_especialidade_funcao('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||') as ultimo_contrato_servidor_especialidade_funcao
                            ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_especialidade_funcao.cod_contrato
                     LEFT JOIN ultimo_contrato_servidor_conta_salario('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||') as ultimo_contrato_servidor_conta_salario
                            ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_conta_salario.cod_contrato
                     LEFT JOIN monetario.banco
                            ON ultimo_contrato_servidor_conta_salario.cod_banco = banco.cod_banco
                     LEFT JOIN monetario.agencia
                            ON ultimo_contrato_servidor_conta_salario.cod_banco = agencia.cod_banco
                           AND ultimo_contrato_servidor_conta_salario.cod_agencia = agencia.cod_agencia
                     -- Fim consulta servidores (ativos, aposentados e rescindidos)

                        UNION

                    -- Inicio consulta pensionista
                        SELECT contrato_pensionista.cod_contrato
                             , contrato_servidor.cod_cargo
                             , recuperarSituacaoDoContrato(contrato_pensionista.cod_contrato,'|| inCodPeriodoMovimentacao ||','|| quote_literal(stEntidade) ||') as situacao
                             , sw_cgm.nom_cgm
                             , sw_cgm.numcgm
                             , agencia.cod_agencia
                             , banco.cod_banco
                             , ultimo_contrato_pensionista_conta_salario.nr_conta
                             , banco.num_banco
                             , banco.nom_banco as descricao_banco
                             , agencia.num_agencia
                             , agencia.nom_agencia
                             , ultimo_contrato_pensionista_orgao.cod_orgao
                             , recuperaDescricaoOrgao(ultimo_contrato_pensionista_orgao.cod_orgao, to_date('|| quote_literal(stDataFinalCompetencia) ||', ''dd/mm/yyyy'')) as descricao_lotacao
                             , ( SELECT orgao
                                   FROM organograma.vw_orgao_nivel
                                  WHERE cod_orgao = ultimo_contrato_pensionista_orgao.cod_orgao) as orgao
                             , local.cod_local
                             , local.descricao as descricao_local
                             , to_char(ultimo_contrato_pensionista_nomeacao_posse.dt_nomeacao, ''dd/mm/yyyy'') as dt_nomeacao
                             , to_char(ultimo_contrato_pensionista_nomeacao_posse.dt_posse, ''dd/mm/yyyy'') as dt_posse
                             , to_char(ultimo_contrato_pensionista_nomeacao_posse.dt_admissao, ''dd/mm/yyyy'') as dt_admissao
                             , to_char(ultimo_contrato_pensionista_nomeacao_posse.dt_inicio_contagem, ''dd/mm/yyyy'') as dt_contagem_inicial
                             , to_char(ultimo_contrato_pensionista_nomeacao_posse.dt_admissao,''dd/mm/yyyy'')||''-''|| to_char(ultimo_contrato_pensionista_nomeacao_posse.dt_posse,''dd/mm/yyyy'') as admissao_posse
                             , (SELECT descricao FROM pessoal'|| stEntidade ||'.cargo WHERE cod_cargo = ultimo_contrato_pensionista_funcao.cod_cargo) as descricao_funcao
			     , ultimo_contrato_pensionista_funcao.cod_cargo as cod_funcao
                             , (SELECT descricao FROM pessoal'|| stEntidade ||'.cargo WHERE cod_cargo = contrato_servidor.cod_cargo) as descricao_cargo
                             , (SELECT descricao FROM pessoal'|| stEntidade ||'.sub_divisao WHERE cod_sub_divisao = contrato_servidor.cod_sub_divisao) as descricao_sub_divisao
                             , (SELECT descricao FROM pessoal'|| stEntidade ||'.regime WHERE cod_regime = contrato_servidor.cod_regime) as descricao_regime
                             , (SELECT descricao FROM pessoal'|| stEntidade ||'.sub_divisao WHERE cod_sub_divisao = ultimo_contrato_pensionista_sub_divisao_funcao.cod_sub_divisao_funcao) as descricao_sub_divisao_funcao
                             , (SELECT cod_sub_divisao_funcao FROM pessoal'|| stEntidade ||'.sub_divisao WHERE cod_sub_divisao = ultimo_contrato_pensionista_sub_divisao_funcao.cod_sub_divisao_funcao) as cod_sub_divisao_funcao
                             , ultimo_contrato_pensionista_padrao.cod_padrao
                             , (SELECT descricao FROM folhapagamento'|| stEntidade ||'.padrao WHERE padrao.cod_padrao = ultimo_contrato_pensionista_padrao.cod_padrao) as descricao_padrao
                             , contrato_servidor.cod_sub_divisao
                             , contrato_servidor.cod_regime
                             , (SELECT descricao FROM pessoal'|| stEntidade ||'.regime WHERE cod_regime = ultimo_contrato_pensionista_regime_funcao.cod_regime_funcao) as descricao_regime_funcao
                             , (SELECT cod_regime FROM pessoal'|| stEntidade ||'.regime WHERE cod_regime = ultimo_contrato_pensionista_regime_funcao.cod_regime_funcao) as cod_regime_funcao
                             , (SELECT descricao FROM pessoal'|| stEntidade ||'.especialidade WHERE especialidade.cod_especialidade = ultimo_contrato_pensionista_especialidade_funcao.cod_especialidade_funcao) as descricao_especialidade_funcao
                             , ultimo_contrato_pensionista_especialidade_funcao.cod_especialidade_funcao
                             , contrato_pensionista_especialidade_cargo.cod_especialidade as cod_especialidade_cargo
                             , especialidade.descricao as descricao_especialidade
                             , ultimo_contrato_pensionista_salario.horas_mensais
                             '|| stSelectPensionistaAtributo ||'
                             , tabelaEventos.valorP AS valorP
                             , tabelaEventos.quantidadeP AS quantidadeP
                             , tabelaEventos.codigoP AS codigoP
                             , tabelaEventos.cod_eventoP AS cod_eventoP
                             , tabelaEventos.descricaoP as descricaoP
                             , tabelaEventos.naturezaP AS naturezaP
                             , tabelaEventos.desdobramentoP AS desdobramentoP

                             , tabelaEventos.valorD AS valorD
                             , tabelaEventos.quantidadeD AS quantidadeD
                             , tabelaEventos.codigoD AS codigoD
                             , tabelaEventos.cod_eventoD AS cod_eventoD
                             , tabelaEventos.descricaoD AS descricaoD
                             , tabelaEventos.naturezaD AS naturezaD
                             , tabelaEventos.desdobramentoD AS desdobramentoD

                             , tabelaEventos.valorB AS valorB
                             , tabelaEventos.quantidadeB AS quantidadeB
                             , tabelaEventos.codigoB AS codigoB
                             , tabelaEventos.cod_eventoB AS cod_eventoB
                             , tabelaEventos.descricaoB AS descricaoB
                             , tabelaEventos.naturezaB AS naturezaB
                             , tabelaEventos.desdobramentoB AS desdobramentoB
                             , tabelaEventos.parcela

                          FROM pessoal'|| stEntidade ||'.contrato_pensionista
                          JOIN eventos_tmp AS tabelaEventos
                            ON contrato_pensionista.cod_contrato = tabelaEventos.cod_contrato
                    INNER JOIN pessoal'|| stEntidade ||'.pensionista
                            ON contrato_pensionista.cod_pensionista = pensionista.cod_pensionista
                           AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente
                    INNER JOIN sw_cgm
                            ON sw_cgm.numcgm = pensionista.numcgm
                    INNER JOIN ultimo_contrato_pensionista_orgao('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||') as ultimo_contrato_pensionista_orgao
                            ON contrato_pensionista.cod_contrato = ultimo_contrato_pensionista_orgao.cod_contrato
                    INNER JOIN ultimo_contrato_servidor_nomeacao_posse('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||') as ultimo_contrato_pensionista_nomeacao_posse
                            ON pensionista.cod_contrato_cedente = ultimo_contrato_pensionista_nomeacao_posse.cod_contrato
                    INNER JOIN pessoal'|| stEntidade ||'.contrato_servidor
                            ON pensionista.cod_contrato_cedente = contrato_servidor.cod_contrato
                    INNER JOIN ultimo_contrato_servidor_funcao('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||') as ultimo_contrato_pensionista_funcao
                            ON pensionista.cod_contrato_cedente = ultimo_contrato_pensionista_funcao.cod_contrato
                    INNER JOIN ultimo_contrato_servidor_regime_funcao('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||') as ultimo_contrato_pensionista_regime_funcao
                            ON pensionista.cod_contrato_cedente = ultimo_contrato_pensionista_regime_funcao.cod_contrato
                    INNER JOIN ultimo_contrato_servidor_padrao('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||') as ultimo_contrato_pensionista_padrao
                            ON pensionista.cod_contrato_cedente = ultimo_contrato_pensionista_padrao.cod_contrato
                    INNER JOIN ultimo_contrato_servidor_salario('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||') as ultimo_contrato_pensionista_salario
                            ON pensionista.cod_contrato_cedente = ultimo_contrato_pensionista_salario.cod_contrato
                    INNER JOIN ultimo_contrato_servidor_sub_divisao_funcao('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||') as ultimo_contrato_pensionista_sub_divisao_funcao
                            ON pensionista.cod_contrato_cedente = ultimo_contrato_pensionista_sub_divisao_funcao.cod_contrato
                    INNER JOIN evento_calculado_tmp AS registro_evento_calculado
                            ON registro_evento_calculado.cod_contrato = contrato_pensionista.cod_contrato
                            '|| stFromPensionistaAtributo ||'
                     LEFT JOIN pessoal'|| stEntidade ||'.contrato_servidor_especialidade_cargo as contrato_pensionista_especialidade_cargo
                            ON pensionista.cod_contrato_cedente = contrato_pensionista_especialidade_cargo.cod_contrato
                     LEFT JOIN pessoal'|| stEntidade ||'.especialidade
                            ON especialidade.cod_especialidade = contrato_pensionista_especialidade_cargo.cod_especialidade
                     LEFT JOIN ultimo_contrato_servidor_local('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||') as ultimo_contrato_pensionista_local
                            ON pensionista.cod_contrato_cedente = ultimo_contrato_pensionista_local.cod_contrato
                     LEFT JOIN organograma.local
                            ON local.cod_local = ultimo_contrato_pensionista_local.cod_local
                     LEFT JOIN ultimo_contrato_servidor_especialidade_funcao('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||') as ultimo_contrato_pensionista_especialidade_funcao
                            ON pensionista.cod_contrato_cedente = ultimo_contrato_pensionista_especialidade_funcao.cod_contrato
                     LEFT JOIN ultimo_contrato_pensionista_conta_salario('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||') as ultimo_contrato_pensionista_conta_salario
                            ON contrato_pensionista.cod_contrato = ultimo_contrato_pensionista_conta_salario.cod_contrato
                     LEFT JOIN monetario.banco
                            ON ultimo_contrato_pensionista_conta_salario.cod_banco = banco.cod_banco
                     LEFT JOIN monetario.agencia
                            ON ultimo_contrato_pensionista_conta_salario.cod_banco = agencia.cod_banco
                           AND ultimo_contrato_pensionista_conta_salario.cod_agencia = agencia.cod_agencia
                    -- Fim consulta pensionista
                   ) as servidor_pensionista
            WHERE contrato.cod_contrato = servidor_pensionista.cod_contrato
                  '|| stFiltro ||'
         ORDER BY '|| stOrdenacao ||' , cod_contrato, codigoP, codigoD, codigoB
             ';

    RETURN stSql;
END;
$$LANGUAGE 'plpgsql';

CREATE OR REPLACE FUNCTION montaConsultaFolhas(INTEGER,INTEGER,VARCHAR,VARCHAR,INTEGER,VARCHAR,VARCHAR,VARCHAR,INTEGER) RETURNS VARCHAR AS $$
DECLARE
    inCodConfiguracao           ALIAS FOR $1;
    inCodPeriodoMovimentacao    ALIAS FOR $2;
    stFiltro                    ALIAS FOR $3;
    stOrdenacao                 ALIAS FOR $4;
    inCodAtributo               ALIAS FOR $5;
    stValorAtributo             ALIAS FOR $6;
    stEntidade                  ALIAS FOR $7;
    stExercicio                 ALIAS FOR $8;
    inCodComplementar           ALIAS FOR $9;
    stSql                       VARCHAR := '';
    stDataContagemInicial       VARCHAR := '';
    stSelectServidorAtributo    VARCHAR := '';
    stFromServidorAtributo      VARCHAR := '';
    stSelectPensionistaAtributo VARCHAR := '';
    stFromPensionistaAtributo   VARCHAR := '';
    stDataFinalCompetencia      VARCHAR := '';
    stFiltroCalculoServidor     VARCHAR := '';
BEGIN

    stDataFinalCompetencia := selectIntoVarchar('SELECT to_char(dt_final,'||quote_literal('dd/mm/yyyy')||')
                                                   FROM folhapagamento'|| stEntidade ||'.periodo_movimentacao 
                                                  WHERE cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||' ');

    SELECT valor
      INTO stDataContagemInicial
      FROM administracao.configuracao
     WHERE cod_modulo = 22
       AND exercicio = stExercicio
       AND parametro = 'dtContagemInicial';

    /**************************************************
    * Monta os filtros caso pesquisa por atributos
    ***************************************************/
    IF TRIM(stValorAtributo) != '' AND stValorAtributo IS NOT NULL THEN        
        stSelectServidorAtributo := '   , ( CASE WHEN ultimo_atributo_contrato_servidor_valor.cod_tipo = 3 OR                                             
                                                      ultimo_atributo_contrato_servidor_valor.cod_tipo = 4 
                                                THEN ( SELECT atributo_valor_padrao.valor_padrao
                                                         FROM administracao.atributo_valor_padrao
                                                        WHERE atributo_valor_padrao.cod_cadastro = ultimo_atributo_contrato_servidor_valor.cod_cadastro
                                                          AND atributo_valor_padrao.cod_modulo   = ultimo_atributo_contrato_servidor_valor.cod_modulo
                                                          AND atributo_valor_padrao.cod_atributo = ultimo_atributo_contrato_servidor_valor.cod_atributo
                                                          AND atributo_valor_padrao.cod_valor    = ultimo_atributo_contrato_servidor_valor.valor)
                                                ELSE ultimo_atributo_contrato_servidor_valor.valor 
                                            END ) AS valor_label
                                        , ultimo_atributo_contrato_servidor_valor.valor
                                        , ultimo_atributo_contrato_servidor_valor.cod_atributo';
        stFromServidorAtributo   := ' INNER JOIN ultimo_atributo_contrato_servidor_valor('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||') as ultimo_atributo_contrato_servidor_valor
                                              ON ultimo_atributo_contrato_servidor_valor.cod_contrato = contrato_servidor.cod_contrato       
                                             AND ultimo_atributo_contrato_servidor_valor.cod_atributo = '|| inCodAtributo ||'
                                             AND ultimo_atributo_contrato_servidor_valor.valor IN ('|| stValorAtributo ||')
                                    ';
                                    
        stSelectPensionistaAtributo := ',  ( CASE WHEN ultimo_atributo_contrato_pensionista.cod_tipo = 3 OR
                                                        ultimo_atributo_contrato_pensionista.cod_tipo = 4 
                                                THEN (  SELECT atributo_valor_padrao.valor_padrao
                                                          FROM administracao.atributo_valor_padrao
                                                         WHERE atributo_valor_padrao.cod_cadastro = ultimo_atributo_contrato_pensionista.cod_cadastro
                                                           AND atributo_valor_padrao.cod_modulo   = ultimo_atributo_contrato_pensionista.cod_modulo
                                                           AND atributo_valor_padrao.cod_atributo = ultimo_atributo_contrato_pensionista.cod_atributo
                                                           AND atributo_valor_padrao.cod_valor    = ultimo_atributo_contrato_pensionista.valor)
                                                ELSE ultimo_atributo_contrato_pensionista.valor 
                                            END) AS valor_label
                                       , ultimo_atributo_contrato_pensionista.valor
                                       , ultimo_atributo_contrato_pensionista.cod_atributo';
        stFromPensionistaAtributo   := ' INNER JOIN ultimo_atributo_contrato_pensionista('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||') as ultimo_atributo_contrato_pensionista
                                                 ON ultimo_atributo_contrato_pensionista.cod_contrato = contrato_pensionista.cod_contrato       
                                                AND atributo_contrato_pensionista.cod_atributo = '|| inCodAtributo ||'
                                                AND atributo_contrato_pensionista.valor IN ('|| stValorAtributo ||') 
                                       ';
    END IF;
    
    stFiltroCalculoServidor := possuiCalculo(inCodConfiguracao,inCodPeriodoMovimentacao,inCodComplementar,stEntidade);
    /**************************************************
    * Monta a consulta de retorno da PL
    ***************************************************/
    stSql := '
            SELECT *
              FROM pessoal'|| stEntidade ||'.contrato
                 , (
                    -- Inicio consulta servidores (ativos, aposentados e rescindidos)
                        SELECT contrato_servidor.cod_contrato
		             , contrato_servidor.cod_cargo		 	
                             , recuperarSituacaoDoContrato(contrato_servidor.cod_contrato,'|| inCodPeriodoMovimentacao ||','|| quote_literal(stEntidade) ||') as situacao
                             , sw_cgm.nom_cgm
                             , sw_cgm.numcgm
                             , agencia.cod_agencia
                             , banco.cod_banco  
                             , ultimo_contrato_servidor_conta_salario.nr_conta   
                             , banco.num_banco  
                             , banco.nom_banco as descricao_banco
                             , agencia.num_agencia
                             , agencia.nom_agencia
                             , ultimo_contrato_servidor_orgao.cod_orgao 
                             , recuperaDescricaoOrgao(ultimo_contrato_servidor_orgao.cod_orgao, to_date('|| quote_literal(stDataFinalCompetencia) ||', ''dd/mm/yyyy'')) as descricao_lotacao
                             , ( SELECT orgao
                                   FROM organograma.vw_orgao_nivel 
                                  WHERE cod_orgao = ultimo_contrato_servidor_orgao.cod_orgao) as orgao
                             , local.cod_local 
                             , local.descricao as descricao_local
                             , to_char(ultimo_contrato_servidor_nomeacao_posse.dt_nomeacao,''dd/mm/yyyy'') as dt_nomeacao      
                             , to_char(ultimo_contrato_servidor_nomeacao_posse.dt_posse,''dd/mm/yyyy'') as dt_posse         
                             , to_char(ultimo_contrato_servidor_nomeacao_posse.dt_admissao,''dd/mm/yyyy'') as dt_admissao       
                             , to_char(ultimo_contrato_servidor_nomeacao_posse.dt_inicio_contagem,''dd/mm/yyyy'') as dt_contagem_inicial     
                             , to_char(ultimo_contrato_servidor_nomeacao_posse.dt_admissao,''dd/mm/yyyy'')||''-''|| to_char(ultimo_contrato_servidor_nomeacao_posse.dt_posse,''dd/mm/yyyy'') as admissao_posse
                             , (SELECT descricao FROM pessoal'|| stEntidade ||'.cargo WHERE cod_cargo = ultimo_contrato_servidor_funcao.cod_cargo) as descricao_funcao
			     , ultimo_contrato_servidor_funcao.cod_cargo as cod_funcao
                             , (SELECT descricao FROM pessoal'|| stEntidade ||'.cargo WHERE cod_cargo = contrato_servidor.cod_cargo) as descricao_cargo
                             , (SELECT descricao FROM pessoal'|| stEntidade ||'.sub_divisao WHERE cod_sub_divisao = contrato_servidor.cod_sub_divisao) as descricao_sub_divisao
                             , (SELECT cod_sub_divisao_funcao FROM pessoal'|| stEntidade ||'.sub_divisao WHERE cod_sub_divisao = ultimo_contrato_servidor_sub_divisao_funcao.cod_sub_divisao_funcao) as cod_sub_divisao_funcao
                             , (SELECT descricao FROM pessoal'|| stEntidade ||'.regime WHERE cod_regime = contrato_servidor.cod_regime) as descricao_regime
			     , (SELECT cod_regime FROM pessoal'|| stEntidade ||'.regime WHERE cod_regime = ultimo_contrato_servidor_regime_funcao.cod_regime_funcao) as cod_regime_funcao
                             , (SELECT descricao FROM pessoal'|| stEntidade ||'.sub_divisao WHERE cod_sub_divisao = ultimo_contrato_servidor_sub_divisao_funcao.cod_sub_divisao_funcao) as descricao_sub_divisao_funcao
                             , ultimo_contrato_servidor_padrao.cod_padrao 
                             , (SELECT descricao FROM folhapagamento'|| stEntidade ||'.padrao WHERE padrao.cod_padrao = ultimo_contrato_servidor_padrao.cod_padrao) as descricao_padrao
                             , contrato_servidor.cod_sub_divisao
                             , contrato_servidor.cod_regime
                             , (SELECT descricao FROM pessoal'|| stEntidade ||'.regime WHERE cod_regime = ultimo_contrato_servidor_regime_funcao.cod_regime_funcao) as descricao_regime_funcao
                             , (SELECT descricao FROM pessoal'|| stEntidade ||'.especialidade WHERE especialidade.cod_especialidade = ultimo_contrato_servidor_especialidade_funcao.cod_especialidade_funcao) as descricao_especialidade_funcao
                             , ultimo_contrato_servidor_especialidade_funcao.cod_especialidade_funcao
                             , contrato_servidor_especialidade_cargo.cod_especialidade as cod_especialidade_cargo
                             , especialidade.descricao as descricao_especialidade
                             , ultimo_contrato_servidor_salario.horas_mensais
                             '|| stSelectServidorAtributo ||'
                          FROM pessoal'|| stEntidade ||'.contrato_servidor   
                    INNER JOIN pessoal'|| stEntidade ||'.servidor_contrato_servidor
                            ON contrato_servidor.cod_contrato = servidor_contrato_servidor.cod_contrato
                    INNER JOIN pessoal'|| stEntidade ||'.servidor 
                            ON servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                    INNER JOIN sw_cgm
                            ON servidor.numcgm = sw_cgm.numcgm
                    INNER JOIN ultimo_contrato_servidor_orgao('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||') as ultimo_contrato_servidor_orgao
                            ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_orgao.cod_contrato
                    INNER JOIN ultimo_contrato_servidor_nomeacao_posse('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||') as ultimo_contrato_servidor_nomeacao_posse
                            ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_nomeacao_posse.cod_contrato
                    INNER JOIN ultimo_contrato_servidor_funcao('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||') as ultimo_contrato_servidor_funcao
                            ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_funcao.cod_contrato 
                    INNER JOIN ultimo_contrato_servidor_regime_funcao('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||') as ultimo_contrato_servidor_regime_funcao
                            ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_regime_funcao.cod_contrato
                    INNER JOIN ultimo_contrato_servidor_padrao('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||') as ultimo_contrato_servidor_padrao
                            ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_padrao.cod_contrato
                    INNER JOIN ultimo_contrato_servidor_salario('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||') as ultimo_contrato_servidor_salario
                            ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_salario.cod_contrato
                    INNER JOIN ultimo_contrato_servidor_sub_divisao_funcao('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||') as ultimo_contrato_servidor_sub_divisao_funcao
                            ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_sub_divisao_funcao.cod_contrato
                    INNER JOIN '|| stFiltroCalculoServidor ||' AS registro_evento_calculado
                            ON registro_evento_calculado.cod_contrato = contrato_servidor.cod_contrato
                            '|| stFromServidorAtributo ||'
                     LEFT JOIN pessoal'|| stEntidade ||'.contrato_servidor_especialidade_cargo
                            ON contrato_servidor.cod_contrato = contrato_servidor_especialidade_cargo.cod_contrato
                     LEFT JOIN pessoal'|| stEntidade ||'.especialidade
                            ON especialidade.cod_especialidade = contrato_servidor_especialidade_cargo.cod_especialidade
                     LEFT JOIN ultimo_contrato_servidor_local('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||') as ultimo_contrato_servidor_local
                            ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_local.cod_contrato
                     LEFT JOIN organograma.local
                            ON local.cod_local = ultimo_contrato_servidor_local.cod_local
                     LEFT JOIN ultimo_contrato_servidor_especialidade_funcao('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||') as ultimo_contrato_servidor_especialidade_funcao
                            ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_especialidade_funcao.cod_contrato
                     LEFT JOIN ultimo_contrato_servidor_conta_salario('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||') as ultimo_contrato_servidor_conta_salario
                            ON contrato_servidor.cod_contrato = ultimo_contrato_servidor_conta_salario.cod_contrato
                     LEFT JOIN monetario.banco
                            ON ultimo_contrato_servidor_conta_salario.cod_banco = banco.cod_banco
                     LEFT JOIN monetario.agencia
                            ON ultimo_contrato_servidor_conta_salario.cod_banco = agencia.cod_banco
                           AND ultimo_contrato_servidor_conta_salario.cod_agencia = agencia.cod_agencia
                     -- Fim consulta servidores (ativos, aposentados e rescindidos)

                        UNION

                    -- Inicio consulta pensionista
                        SELECT contrato_pensionista.cod_contrato
			     , contrato_servidor.cod_cargo
                             , recuperarSituacaoDoContrato(contrato_pensionista.cod_contrato,'|| inCodPeriodoMovimentacao ||','|| quote_literal(stEntidade) ||') as situacao
                             , sw_cgm.nom_cgm
                             , sw_cgm.numcgm
                             , agencia.cod_agencia
                             , banco.cod_banco  
                             , ultimo_contrato_pensionista_conta_salario.nr_conta   
                             , banco.num_banco  
                             , banco.nom_banco as descricao_banco 
                             , agencia.num_agencia
                             , agencia.nom_agencia 
                             , ultimo_contrato_pensionista_orgao.cod_orgao 
                             , recuperaDescricaoOrgao(ultimo_contrato_pensionista_orgao.cod_orgao, to_date('|| quote_literal(stDataFinalCompetencia) ||', ''dd/mm/yyyy'')) as descricao_lotacao
                             , ( SELECT orgao
                                   FROM organograma.vw_orgao_nivel 
                                  WHERE cod_orgao = ultimo_contrato_pensionista_orgao.cod_orgao) as orgao
                             , local.cod_local   
                             , local.descricao as descricao_local  
                             , to_char(ultimo_contrato_pensionista_nomeacao_posse.dt_nomeacao, ''dd/mm/yyyy'') as dt_nomeacao      
                             , to_char(ultimo_contrato_pensionista_nomeacao_posse.dt_posse, ''dd/mm/yyyy'') as dt_posse         
                             , to_char(ultimo_contrato_pensionista_nomeacao_posse.dt_admissao, ''dd/mm/yyyy'') as dt_admissao      
                             , to_char(ultimo_contrato_pensionista_nomeacao_posse.dt_inicio_contagem, ''dd/mm/yyyy'') as dt_contagem_inicial
                             , to_char(ultimo_contrato_pensionista_nomeacao_posse.dt_admissao,''dd/mm/yyyy'')||''-''|| to_char(ultimo_contrato_pensionista_nomeacao_posse.dt_posse,''dd/mm/yyyy'') as admissao_posse
                             , (SELECT descricao FROM pessoal'|| stEntidade ||'.cargo WHERE cod_cargo = ultimo_contrato_pensionista_funcao.cod_cargo) as descricao_funcao
			     , ultimo_contrato_pensionista_funcao.cod_cargo as cod_funcao
                             , (SELECT descricao FROM pessoal'|| stEntidade ||'.cargo WHERE cod_cargo = contrato_servidor.cod_cargo) as descricao_cargo
                             , (SELECT descricao FROM pessoal'|| stEntidade ||'.sub_divisao WHERE cod_sub_divisao = contrato_servidor.cod_sub_divisao) as descricao_sub_divisao
                             , (SELECT cod_sub_divisao_funcao FROM pessoal'|| stEntidade ||'.sub_divisao WHERE cod_sub_divisao = ultimo_contrato_pensionista_sub_divisao_funcao.cod_sub_divisao_funcao) as cod_sub_divisao_funcao
                             , (SELECT descricao FROM pessoal'|| stEntidade ||'.regime WHERE cod_regime = contrato_servidor.cod_regime) as descricao_regime
			     , (SELECT cod_regime FROM pessoal'|| stEntidade ||'.regime WHERE cod_regime = ultimo_contrato_pensionista_regime_funcao.cod_regime_funcao) as cod_regime_funcao
                             , (SELECT descricao FROM pessoal'|| stEntidade ||'.sub_divisao WHERE cod_sub_divisao = ultimo_contrato_pensionista_sub_divisao_funcao.cod_sub_divisao_funcao) as descricao_sub_divisao_funcao
                             , ultimo_contrato_pensionista_padrao.cod_padrao 
                             , (SELECT descricao FROM folhapagamento'|| stEntidade ||'.padrao WHERE padrao.cod_padrao = ultimo_contrato_pensionista_padrao.cod_padrao) as descricao_padrao
                             , contrato_servidor.cod_sub_divisao
                             , contrato_servidor.cod_regime
                             , (SELECT descricao FROM pessoal'|| stEntidade ||'.regime WHERE cod_regime = ultimo_contrato_pensionista_regime_funcao.cod_regime_funcao) as descricao_regime_funcao
                             , (SELECT descricao FROM pessoal'|| stEntidade ||'.especialidade WHERE especialidade.cod_especialidade = ultimo_contrato_pensionista_especialidade_funcao.cod_especialidade_funcao) as descricao_especialidade_funcao
                             , ultimo_contrato_pensionista_especialidade_funcao.cod_especialidade_funcao
                             , contrato_pensionista_especialidade_cargo.cod_especialidade as cod_especialidade_cargo
                             , especialidade.descricao as descricao_especialidade
                             , ultimo_contrato_pensionista_salario.horas_mensais
                             '|| stSelectPensionistaAtributo ||'
                          FROM pessoal'|| stEntidade ||'.contrato_pensionista
                    INNER JOIN pessoal'|| stEntidade ||'.pensionista
                            ON contrato_pensionista.cod_pensionista = pensionista.cod_pensionista
                           AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente
                    INNER JOIN sw_cgm
                            ON sw_cgm.numcgm = pensionista.numcgm
                    INNER JOIN ultimo_contrato_pensionista_orgao('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||') as ultimo_contrato_pensionista_orgao
                            ON contrato_pensionista.cod_contrato = ultimo_contrato_pensionista_orgao.cod_contrato
                    INNER JOIN ultimo_contrato_servidor_nomeacao_posse('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||') as ultimo_contrato_pensionista_nomeacao_posse
                            ON pensionista.cod_contrato_cedente = ultimo_contrato_pensionista_nomeacao_posse.cod_contrato
                    INNER JOIN pessoal'|| stEntidade ||'.contrato_servidor   
                            ON pensionista.cod_contrato_cedente = contrato_servidor.cod_contrato                    
                    INNER JOIN ultimo_contrato_servidor_funcao('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||') as ultimo_contrato_pensionista_funcao
                            ON pensionista.cod_contrato_cedente = ultimo_contrato_pensionista_funcao.cod_contrato 
                    INNER JOIN ultimo_contrato_servidor_regime_funcao('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||') as ultimo_contrato_pensionista_regime_funcao
                            ON pensionista.cod_contrato_cedente = ultimo_contrato_pensionista_regime_funcao.cod_contrato
                    INNER JOIN ultimo_contrato_servidor_padrao('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||') as ultimo_contrato_pensionista_padrao
                            ON pensionista.cod_contrato_cedente = ultimo_contrato_pensionista_padrao.cod_contrato
                    INNER JOIN ultimo_contrato_servidor_salario('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||') as ultimo_contrato_pensionista_salario
                            ON pensionista.cod_contrato_cedente = ultimo_contrato_pensionista_salario.cod_contrato
                    INNER JOIN ultimo_contrato_servidor_sub_divisao_funcao('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||') as ultimo_contrato_pensionista_sub_divisao_funcao
                            ON pensionista.cod_contrato_cedente = ultimo_contrato_pensionista_sub_divisao_funcao.cod_contrato
                    INNER JOIN '|| stFiltroCalculoServidor ||' AS registro_evento_calculado
                            ON registro_evento_calculado.cod_contrato = contrato_pensionista.cod_contrato
                            '|| stFromPensionistaAtributo ||'
                     LEFT JOIN pessoal'|| stEntidade ||'.contrato_servidor_especialidade_cargo as contrato_pensionista_especialidade_cargo
                            ON pensionista.cod_contrato_cedente = contrato_pensionista_especialidade_cargo.cod_contrato
                     LEFT JOIN pessoal'|| stEntidade ||'.especialidade
                            ON especialidade.cod_especialidade = contrato_pensionista_especialidade_cargo.cod_especialidade
                     LEFT JOIN ultimo_contrato_servidor_local('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||') as ultimo_contrato_pensionista_local
                            ON pensionista.cod_contrato_cedente = ultimo_contrato_pensionista_local.cod_contrato
                     LEFT JOIN organograma.local
                            ON local.cod_local = ultimo_contrato_pensionista_local.cod_local
                     LEFT JOIN ultimo_contrato_servidor_especialidade_funcao('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||') as ultimo_contrato_pensionista_especialidade_funcao
                            ON pensionista.cod_contrato_cedente = ultimo_contrato_pensionista_especialidade_funcao.cod_contrato
                     LEFT JOIN ultimo_contrato_pensionista_conta_salario('|| quote_literal(stEntidade) ||', '|| inCodPeriodoMovimentacao ||') as ultimo_contrato_pensionista_conta_salario
                            ON contrato_pensionista.cod_contrato = ultimo_contrato_pensionista_conta_salario.cod_contrato
                     LEFT JOIN monetario.banco
                            ON ultimo_contrato_pensionista_conta_salario.cod_banco = banco.cod_banco
                     LEFT JOIN monetario.agencia
                            ON ultimo_contrato_pensionista_conta_salario.cod_banco = agencia.cod_banco
                           AND ultimo_contrato_pensionista_conta_salario.cod_agencia = agencia.cod_agencia
                    -- Fim consulta pensionista
                   ) as servidor_pensionista 
            WHERE contrato.cod_contrato = servidor_pensionista.cod_contrato
                  '|| stFiltro ||'
         ORDER BY '|| stOrdenacao ||'       
             ';

    RETURN stSql;
END;
$$LANGUAGE 'plpgsql';

CREATE OR REPLACE FUNCTION folhaAnalitica(INTEGER,INTEGER,INTEGER,VARCHAR,VARCHAR,INTEGER,VARCHAR,VARCHAR,VARCHAR) RETURNS SETOF colunasAnalitica AS $$
DECLARE
    inCodConfiguracao           ALIAS FOR $1;
    inCodPeriodoMovimentacao    ALIAS FOR $2;  
    inCodComplementar           ALIAS FOR $3;  
    stFiltro                    ALIAS FOR $4;
    stOrdenacao                 ALIAS FOR $5;
    inCodAtributo               ALIAS FOR $6;
    stValorAtributo             ALIAS FOR $7;
    stEntidade                  ALIAS FOR $8;
    stExercicio                 ALIAS FOR $9;
    stSql                       VARCHAR:='';
    stDescricaoPrevidencia      VARCHAR:='';
    stDescricaoProgressao       VARCHAR:='';    
    reRegistro                  RECORD;
    reEvento                    RECORD;
    nuProventos                 NUMERIC:=0.00;
    nuDesdontos                 NUMERIC:=0.00;
    nuBasePrevidencia           NUMERIC:=0.00;
    nuBaseIrrf                  NUMERIC:=0.00;
    nuBaseFgts                  NUMERIC:=0.00;
    inCodEventoPrevidencia      INTEGER;
    inCodEventoIRRF4            INTEGER;
    inCodEventoIRRF5            INTEGER;
    inCountContrato             INTEGER;
    inDiferenca                 INTEGER;
    dtInicial                   DATE;
    dtFinal                     DATE;
    arDataInicial               VARCHAR[];
    arDataFinal                 VARCHAR[];
    rwAnalitica                 colunasAnalitica%ROWTYPE;    
BEGIN
    stSql := montaConsultaFolhas(inCodConfiguracao,inCodPeriodoMovimentacao,stFiltro,stOrdenacao,inCodAtributo,stValorAtributo,stEntidade,stExercicio,inCodComplementar);
    
    FOR reRegistro IN  EXECUTE stSql
    LOOP
        stDescricaoPrevidencia := selectIntoVarchar('SELECT previdencia_previdencia.descricao
          FROM folhapagamento'|| stEntidade ||'.previdencia_previdencia
             , (  SELECT cod_previdencia
                       , max(timestamp) as timestamp
                    FROM folhapagamento'|| stEntidade ||'.previdencia_previdencia
                GROUP BY cod_previdencia) as max_previdencia_previdencia
             , folhapagamento'|| stEntidade ||'.previdencia
             , pessoal'|| stEntidade ||'.contrato_servidor_previdencia
             , (  SELECT cod_contrato
                       , max(timestamp) as timestamp
                    FROM pessoal'|| stEntidade ||'.contrato_servidor_previdencia
                GROUP BY cod_contrato) as max_contrato_servidor_previdencia
         WHERE previdencia_previdencia.cod_previdencia = previdencia.cod_previdencia
           AND previdencia_previdencia.cod_previdencia = max_previdencia_previdencia.cod_previdencia
           AND previdencia_previdencia.timestamp       = max_previdencia_previdencia.timestamp
           AND previdencia.cod_previdencia             = contrato_servidor_previdencia.cod_previdencia
           AND contrato_servidor_previdencia.cod_contrato = max_contrato_servidor_previdencia.cod_contrato
           AND contrato_servidor_previdencia.timestamp    = max_contrato_servidor_previdencia.timestamp
           AND previdencia_previdencia.tipo_previdencia = ''o''
           AND contrato_servidor_previdencia.cod_contrato = '|| reRegistro.cod_contrato); 

        dtInicial := selectIntoVarchar('SELECT dt_inicio_progressao
          FROM pessoal'|| stEntidade ||'.contrato_servidor_inicio_progressao
             , (SELECT cod_contrato
                     , max(timestamp) as timestamp
                  FROM pessoal'|| stEntidade ||'.contrato_servidor_inicio_progressao
                GROUP BY cod_contrato) as max_contrato_servidor_inicio_progressao
         WHERE contrato_servidor_inicio_progressao.cod_contrato = max_contrato_servidor_inicio_progressao.cod_contrato
           AND contrato_servidor_inicio_progressao.timestamp = max_contrato_servidor_inicio_progressao.timestamp
           AND contrato_servidor_inicio_progressao.cod_contrato = '|| reRegistro.cod_contrato);

        SELECT INTO dtFinal(SELECT to_date(now()::text,'yyyy-mm-dd'));
        
        arDataInicial := string_to_array(dtInicial::text,'-');
        arDataFinal   := string_to_array(dtFinal::text,'-');
        inDiferenca := 0;
        IF arDataFinal[2] > arDataInicial[2] THEN
            inDiferenca := to_number(arDataFinal[1],'9999') - to_number(arDataInicial[1],'9999');
        ELSEIF arDataFinal[2] = arDataInicial[2] THEN
            IF arDataFinal[3] >= arDataInicial[3] THEN
                inDiferenca := to_number(arDataFinal[1],'9999') - to_number(arDataInicial[1],'9999');
            ELSEIF arDataFinal[3] < arDataInicial[3] THEN
                inDiferenca := to_number(arDataFinal[1],'9999') - to_number(arDataInicial[1],'9999') - 1;
            END IF;
        ELSEIF arDataFinal[2] < arDataInicial[2] THEN
            inDiferenca := to_number(arDataFinal[1],'9999') - to_number(arDataInicial[1],'9999') - 1;
        END IF;                       
        
        stDescricaoProgressao := selectIntoVarchar('SELECT nivel_padrao_nivel.descricao
          FROM folhapagamento'|| stEntidade ||'.nivel_padrao_nivel
             , (SELECT cod_padrao
                     , max(timestamp) as timestamp
                  FROM folhapagamento'|| stEntidade ||'.nivel_padrao_nivel
                GROUP BY cod_padrao) as max_nivel_padrao_nivel
          WHERE nivel_padrao_nivel.cod_padrao = max_nivel_padrao_nivel.cod_padrao
            AND nivel_padrao_nivel.timestamp = max_nivel_padrao_nivel.timestamp
            AND nivel_padrao_nivel.cod_padrao = '|| reRegistro.cod_padrao ||'
            AND nivel_padrao_nivel.qtdmeses <= '|| inDiferenca ||'
        ORDER BY nivel_padrao_nivel.cod_nivel_padrao desc LIMIT 1');
        
        inCountContrato := selectIntoInteger('SELECT count(servidor_contrato_servidor.cod_contrato) as contador
          FROM pessoal'|| stEntidade ||'.servidor
             , pessoal'|| stEntidade ||'.servidor_contrato_servidor
         WHERE servidor.cod_servidor = servidor_contrato_servidor.cod_servidor
           AND servidor.numcgm = '|| reRegistro.numcgm);                                           
           
        rwAnalitica.registro                               := reRegistro.registro                      ;   
        rwAnalitica.cod_contrato                           := reRegistro.cod_contrato                  ;  
        rwAnalitica.numcgm                                 := reRegistro.numcgm                        ;  
        rwAnalitica.nom_cgm                                := reRegistro.nom_cgm                       ;  
        rwAnalitica.orgao                                  := reRegistro.orgao                         ;  
        rwAnalitica.descricao_lotacao                      := reRegistro.descricao_lotacao             ;  
        rwAnalitica.descricao_regime                       := reRegistro.descricao_regime              ;  
        rwAnalitica.descricao_sub_divisao                  := reRegistro.descricao_sub_divisao         ;  
        rwAnalitica.descricao_cargo                        := reRegistro.descricao_cargo               ;  
        rwAnalitica.descricao_especialidade                := reRegistro.descricao_especialidade       ;  
        rwAnalitica.descricao_regime_funcao                := reRegistro.descricao_regime_funcao       ;  
        rwAnalitica.descricao_sub_divisao_funcao           := reRegistro.descricao_sub_divisao_funcao  ;  
        rwAnalitica.descricao_funcao                       := reRegistro.descricao_funcao              ;  
        rwAnalitica.descricao_especialidade_funcao         := reRegistro.descricao_especialidade_funcao;  
        rwAnalitica.descricao_local                        := reRegistro.descricao_local               ;  
        rwAnalitica.horas_mensais                          := reRegistro.horas_mensais                 ;  
        rwAnalitica.situacao                               := reRegistro.situacao                      ;  
        rwAnalitica.previdencia_oficial                    := stDescricaoPrevidencia                   ;  
        rwAnalitica.descricao_padrao                       := recuperaProgressao(reRegistro.cod_contrato,inCodPeriodoMovimentacao,reRegistro.cod_padrao,stEntidade,reRegistro.descricao_padrao)              ;  
        rwAnalitica.descricao_progressao                   := stDescricaoProgressao                    ;  
        rwAnalitica.admissao_posse                         := reRegistro.admissao_posse                ;
        rwAnalitica.dt_contagem_inicial                    := reRegistro.dt_contagem_inicial           ;
        rwAnalitica.num_banco                              := reRegistro.num_banco                     ; 
        rwAnalitica.descricao_banco                        := reRegistro.descricao_banco               ; 
        IF stValorAtributo != '' THEN
            rwAnalitica.valor                              := reRegistro.valor                         ;
        END IF;
                    
        IF inCountContrato = 1 THEN
            rwAnalitica.multiplos                          := 'Não'                                    ;
        ELSE
            rwAnalitica.multiplos                          := 'Sim'                                    ; 
        END IF;
        RETURN NEXT rwAnalitica; 
    END LOOP;    
    RETURN;
END;
$$LANGUAGE 'plpgsql';

CREATE OR REPLACE FUNCTION recuperaProgressao(INTEGER,INTEGER,INTEGER,VARCHAR,VARCHAR) RETURNS VARCHAR AS $$
DECLARE
    inCodContrato               ALIAS FOR $1;
    inCodPeriodoMovimentacao    ALIAS FOR $2;
    inCodPadrao                 ALIAS FOR $3;
    stEntidade               ALIAS FOR $4;
    stPadrao                    ALIAS FOR $5;
    stSql                       VARCHAR;
    dtInicioProgressao          VARCHAR;
    dtFinalPeriodoMovimentacao  VARCHAR;
    stProgressao                VARCHAR;
    inNumMeses                  INTEGER;
BEGIN

    stSql := 'SELECT contrato_servidor_inicio_progressao.dt_inicio_progressao
                FROM pessoal'|| stEntidade ||'.contrato_servidor_inicio_progressao
                   , (SELECT cod_contrato
                           , max(timestamp) as timestamp
                        FROM pessoal'|| stEntidade ||'.contrato_servidor_inicio_progressao
                      GROUP BY cod_contrato) as max_contrato_servidor_inicio_progressao
               WHERE contrato_servidor_inicio_progressao.cod_contrato = max_contrato_servidor_inicio_progressao.cod_contrato
                 AND contrato_servidor_inicio_progressao.timestamp = max_contrato_servidor_inicio_progressao.timestamp
                 AND contrato_servidor_inicio_progressao.cod_contrato = '|| inCodContrato;
    dtInicioProgressao := selectIntoVarchar(stSql);
    
    stSql := 'SELECT dt_final 
                FROM folhapagamento'|| stEntidade ||'.periodo_movimentacao 
               WHERE cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao;
    dtFinalPeriodoMovimentacao := selectIntoVarchar(stSql);
    
    inNumMeses := calculaNrMesesParaProgressao(dtInicioProgressao,dtFinalPeriodoMovimentacao);
    
    stSql := 'SELECT nivel_padrao_nivel.descricao||''-''|| to_real(nivel_padrao_nivel.valor)
                FROM folhapagamento'|| stEntidade ||'.nivel_padrao_nivel
                   , (SELECT padrao_padrao.cod_padrao
                           , max(padrao_padrao.timestamp) as timestamp
                        FROM folhapagamento'|| stEntidade ||'.nivel_padrao_nivel
                  INNER JOIN folhapagamento'|| stEntidade ||'.padrao_padrao
                          ON padrao_padrao.cod_padrao = nivel_padrao_nivel.cod_padrao
                      GROUP BY padrao_padrao.cod_padrao) as max
               WHERE nivel_padrao_nivel.cod_padrao = '|| inCodPadrao ||'
                 AND nivel_padrao_nivel.cod_padrao = max.cod_padrao
                 AND nivel_padrao_nivel.timestamp = max.timestamp
                 AND nivel_padrao_nivel.qtdmeses <= '|| inNumMeses ||'
              ORDER BY nivel_padrao_nivel.qtdmeses desc
              LIMIT 1';    
    stProgressao := selectIntoVarchar(stSql);

    IF stProgressao IS NOT NULL THEN
        RETURN stProgressao;
    ELSE
        stSql := 'SELECT to_real(valor) as valor
                    FROM folhapagamento'|| stEntidade ||'.padrao_padrao  
                       , (SELECT cod_padrao
                               , max(timestamp) as timestamp
                            FROM folhapagamento'|| stEntidade ||'.padrao_padrao
                          GROUP BY cod_padrao) as max_padrao_padrao
                   WHERE padrao_padrao.cod_padrao = max_padrao_padrao.cod_padrao
                     AND padrao_padrao.timestamp = max_padrao_padrao.timestamp
                     AND padrao_padrao.cod_padrao = '|| inCodPadrao;
        
        RETURN stPadrao ||'-'|| selectIntoVarchar(stSql);
    END IF;
END;
$$LANGUAGE 'plpgsql'; 


CREATE OR REPLACE FUNCTION folhaSintetica(INTEGER,INTEGER,INTEGER,VARCHAR,VARCHAR,INTEGER,VARCHAR,VARCHAR,VARCHAR) RETURNS SETOF colunasSintetica AS $$
DECLARE
    inCodConfiguracao           ALIAS FOR $1;
    inCodPeriodoMovimentacao    ALIAS FOR $2;
    inCodComplementar           ALIAS FOR $3;
    stFiltro                    ALIAS FOR $4;
    stOrdenacao                 ALIAS FOR $5;
    inCodAtributo               ALIAS FOR $6;
    stValorAtributo             ALIAS FOR $7;
    stEntidade                  ALIAS FOR $8;
    stExercicio                 ALIAS FOR $9;
    stSql                       VARCHAR:='';
    reRegistro                  RECORD;
    reEvento                    RECORD;
    nuProventos                 NUMERIC:=0.00;
    nuDesdontos                 NUMERIC:=0.00;
    nuPrevidencia               NUMERIC:=0.00;
    nuIRRF                      NUMERIC:=0.00;
    inCodEventoPrevidencia      INTEGER;
    inCodEventoIRRF3            INTEGER;
    inCodEventoIRRF6            INTEGER;
    rwSintetica                 colunasSintetica%ROWTYPE;    
BEGIN
    stSql := montaConsultaFolhas(inCodConfiguracao,inCodPeriodoMovimentacao,stFiltro,stOrdenacao,inCodAtributo,stValorAtributo,stEntidade,stExercicio,inCodComplementar);
    FOR reRegistro IN  EXECUTE stSql
    LOOP
        nuProventos    :=0.00;
        nuDesdontos    :=0.00;
        nuPrevidencia  :=0.00;
        nuIRRF         :=0.00;        
        rwSintetica.registro                   := reRegistro.registro; 
        rwSintetica.cod_contrato               := reRegistro.cod_contrato; 
        rwSintetica.numcgm                     := reRegistro.numcgm; 
        rwSintetica.nom_cgm                    := reRegistro.nom_cgm;
        rwSintetica.orgao                      := reRegistro.orgao;
        rwSintetica.descricao_lotacao          := reRegistro.descricao_lotacao; 
        FOR reEvento IN  EXECUTE montaeventosCalculadosFolhaAnaliticaResumida(inCodConfiguracao,reRegistro.cod_contrato,inCodPeriodoMovimentacao,inCodComplementar,'codigo',quote_literal('P,D,B,I'),stEntidade)
        LOOP
        
        inCodEventoPrevidencia := selectIntoInteger('SELECT previdencia_evento.cod_evento
                                              FROM folhapagamento'|| stEntidade ||'.previdencia_evento
                                                 , folhapagamento'|| stEntidade ||'.previdencia_previdencia
                                                 , (  SELECT cod_previdencia
                                                           , max(timestamp) as timestamp
                                                        FROM folhapagamento'|| stEntidade ||'.previdencia_previdencia
                                                    GROUP BY cod_previdencia) as max_previdencia_previdencia
                                                 , folhapagamento'|| stEntidade ||'.previdencia
                                                 , pessoal'|| stEntidade ||'.contrato_servidor_previdencia
                                                 , (  SELECT cod_contrato
                                                           , max(timestamp) as timestamp
                                                        FROM pessoal'|| stEntidade ||'.contrato_servidor_previdencia
                                                    GROUP BY cod_contrato) as max_contrato_servidor_previdencia
                                             WHERE previdencia_evento.cod_previdencia = previdencia_previdencia.cod_previdencia
                                               AND previdencia_evento.timestamp       = previdencia_previdencia.timestamp
                                               AND previdencia_previdencia.cod_previdencia = previdencia.cod_previdencia
                                               AND previdencia_previdencia.cod_previdencia = max_previdencia_previdencia.cod_previdencia
                                               AND previdencia_previdencia.timestamp       = max_previdencia_previdencia.timestamp
                                               AND previdencia.cod_previdencia             = contrato_servidor_previdencia.cod_previdencia
                                               AND contrato_servidor_previdencia.cod_contrato = max_contrato_servidor_previdencia.cod_contrato
                                               AND contrato_servidor_previdencia.timestamp    = max_contrato_servidor_previdencia.timestamp
                                               AND previdencia_previdencia.tipo_previdencia = ''o''
                                               AND previdencia_evento.cod_tipo = 1
                                               AND contrato_servidor_previdencia.cod_contrato = '|| reRegistro.cod_contrato);            
            IF inCodEventoPrevidencia = reEvento.cod_evento THEN
                nuPrevidencia := reEvento.valor;
            END IF;   
            inCodEventoIRRF3 := selectIntoInteger  ('SELECT cod_evento
                                              FROM folhapagamento'|| stEntidade ||'.tabela_irrf_evento
                                                 , folhapagamento'|| stEntidade ||'.tabela_irrf
                                                 , (SELECT cod_tabela
                                                         , max(timestamp) as timestamp
                                                      FROM folhapagamento'|| stEntidade ||'.tabela_irrf
                                                    GROUP BY cod_tabela) as max_tabela_irrf
                                             WHERE tabela_irrf_evento.cod_tabela = tabela_irrf.cod_tabela
                                               AND tabela_irrf_evento.timestamp  = tabela_irrf.timestamp
                                               AND tabela_irrf.cod_tabela = max_tabela_irrf.cod_tabela
                                               AND tabela_irrf.timestamp  = max_tabela_irrf.timestamp
                                               AND tabela_irrf_evento.cod_tipo = 3');
            inCodEventoIRRF6 := selectIntoInteger  ('SELECT cod_evento
                                              FROM folhapagamento'|| stEntidade ||'.tabela_irrf_evento
                                                 , folhapagamento'|| stEntidade ||'.tabela_irrf
                                                 , (SELECT cod_tabela
                                                         , max(timestamp) as timestamp
                                                      FROM folhapagamento'|| stEntidade ||'.tabela_irrf
                                                    GROUP BY cod_tabela) as max_tabela_irrf
                                             WHERE tabela_irrf_evento.cod_tabela = tabela_irrf.cod_tabela
                                               AND tabela_irrf_evento.timestamp  = tabela_irrf.timestamp
                                               AND tabela_irrf.cod_tabela = max_tabela_irrf.cod_tabela
                                               AND tabela_irrf.timestamp  = max_tabela_irrf.timestamp
                                               AND tabela_irrf_evento.cod_tipo = 6');  
            IF inCodEventoIRRF3 = reEvento.cod_evento OR inCodEventoIRRF6 = reEvento.cod_evento THEN
                nuIRRF = reEvento.valor;
            END IF;
            IF reEvento.natureza = 'P' THEN
                nuProventos := nuProventos + reEvento.valor;
            END IF;
            IF reEvento.natureza = 'D' THEN
                nuDesdontos := nuDesdontos + reEvento.valor;
            END IF;                
        END LOOP;            
        rwSintetica.proventos                  := nuProventos; 
        rwSintetica.descontos                  := nuDesdontos; 
        rwSintetica.previdencia                := nuPrevidencia; 
        rwSintetica.irrf                       := nuIRRF; 
        rwSintetica.liquido                    := nuProventos-nuDesdontos; 
        rwSintetica.num_banco                  := reRegistro.num_banco; 
        rwSintetica.descricao_banco            := reRegistro.descricao_banco;
        rwSintetica.dt_contagem_inicial        := reRegistro.dt_contagem_inicial;
        RETURN NEXT rwSintetica;        
    END LOOP;
    RETURN;
END;
$$LANGUAGE 'plpgsql';


CREATE OR REPLACE FUNCTION folhaAnaliticaResumida(INTEGER,INTEGER,INTEGER,VARCHAR,VARCHAR,INTEGER,VARCHAR,VARCHAR,VARCHAR) RETURNS SETOF colunasAnaliticaResumida AS $$
DECLARE
    inCodConfiguracao           ALIAS FOR $1;
    inCodPeriodoMovimentacao    ALIAS FOR $2;
    inCodComplementar           ALIAS FOR $3;
    stFiltro                    ALIAS FOR $4;
    stOrdenacao                 ALIAS FOR $5;
    inCodAtributo               ALIAS FOR $6;
    stValorAtributo             ALIAS FOR $7;
    stEntidade                  ALIAS FOR $8;
    stExercicio                 ALIAS FOR $9;
    stSql                       VARCHAR:='';
    stSqlEvento                 VARCHAR:='';
    stCodigo                    VARCHAR:='';
    reRegistro                  RECORD;
    reRegistroEvento            RECORD;
    rwAnaliticaResumida         colunasAnaliticaResumida%ROWTYPE;
BEGIN   
    stSql := montaConsultaFolhasAnaliticaResumida(inCodConfiguracao,inCodPeriodoMovimentacao,stFiltro,stOrdenacao,inCodAtributo,stValorAtributo,stEntidade,stExercicio,inCodComplementar);

    FOR reRegistro IN  EXECUTE stSql
    LOOP
        rwAnaliticaResumida.codigoP                    := reRegistro.codigoP;
        rwAnaliticaResumida.descricaoP                 := reRegistro.descricaoP;
        rwAnaliticaResumida.quantidadeP                := reRegistro.quantidadeP;
        rwAnaliticaResumida.valorP                     := reRegistro.valorP;
        rwAnaliticaResumida.desdobramentoP             := reRegistro.desdobramentoP;

        rwAnaliticaResumida.codigoD                    := reRegistro.codigoD;
        rwAnaliticaResumida.descricaoD                 := reRegistro.descricaoD;
        rwAnaliticaResumida.quantidadeD                := reRegistro.quantidadeD;
        rwAnaliticaResumida.valorD                     := reRegistro.valorD;
        rwAnaliticaResumida.desdobramentoD             := reRegistro.desdobramentoD;

        rwAnaliticaResumida.codigoB                    := reRegistro.codigoB;
        rwAnaliticaResumida.descricaoB                 := reRegistro.descricaoB;
        rwAnaliticaResumida.quantidadeB                := reRegistro.quantidadeB;
        rwAnaliticaResumida.valorB                     := reRegistro.valorB;
        rwAnaliticaResumida.desdobramentoB             := reRegistro.desdobramentoB;

        rwAnaliticaResumida.parcela                    := reRegistro.parcela;
        rwAnaliticaResumida.registro                   := reRegistro.registro; 
        rwAnaliticaResumida.cod_contrato               := reRegistro.cod_contrato; 
        rwAnaliticaResumida.numcgm                     := reRegistro.numcgm; 
        rwAnaliticaResumida.nom_cgm                    := reRegistro.nom_cgm;
        rwAnaliticaResumida.descricao_regime_funcao    := reRegistro.descricao_regime_funcao; 
        rwAnaliticaResumida.descricao_funcao           := reRegistro.descricao_funcao; 
        rwAnaliticaResumida.descricao_padrao           := recuperaProgressao(reRegistro.cod_contrato,inCodPeriodoMovimentacao,reRegistro.cod_padrao,stEntidade,reRegistro.descricao_padrao); 
        rwAnaliticaResumida.orgao                      := reRegistro.orgao;
        rwAnaliticaResumida.descricao_lotacao          := reRegistro.descricao_lotacao; 
        rwAnaliticaResumida.descricao_local            := reRegistro.descricao_local; 
        rwAnaliticaResumida.dt_nomeacao                := reRegistro.dt_nomeacao; 
        rwAnaliticaResumida.dt_admissao                := reRegistro.dt_admissao;
        rwAnaliticaResumida.dt_posse                   := reRegistro.dt_posse; 
        rwAnaliticaResumida.hr_mensais                 := reRegistro.horas_mensais;
        rwAnaliticaResumida.num_banco                  := reRegistro.num_banco;
        rwAnaliticaResumida.descricao_banco            := reRegistro.descricao_banco;
        rwAnaliticaResumida.dt_contagem_inicial        := reRegistro.dt_contagem_inicial;
        IF stValorAtributo != '' THEN
            rwAnaliticaResumida.valor                  := reRegistro.valor; 
        END IF;
        RETURN NEXT rwAnaliticaResumida;
    END LOOP;

    DROP INDEX idx_evento_calculado_tmp;
    DROP TABLE evento_calculado_tmp;
    DROP TABLE eventos_tmp;

    RETURN;
END;
$$LANGUAGE 'plpgsql';

CREATE OR REPLACE FUNCTION possuiCalculo(INTEGER,INTEGER,INTEGER,VARCHAR) RETURNS VARCHAR AS $$
DECLARE
    inCodConfiguracao           ALIAS FOR $1;
    inCodPeriodoMovimentacao    ALIAS FOR $2;
    inCodComplementar           ALIAS FOR $3;
    stEntidade                  ALIAS FOR $4;
    stSql                       VARCHAR;
BEGIN
    --Salário
    IF inCodConfiguracao = 1 THEN
        stSql := '( SELECT DISTINCT registro_evento_periodo.cod_contrato
                      FROM folhapagamento'|| stEntidade ||'.registro_evento_periodo
                      JOIN folhapagamento'|| stEntidade ||'.evento_calculado
                        ON evento_calculado.cod_registro = registro_evento_periodo.cod_registro
                       AND registro_evento_periodo.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||') ';
    END IF;
    --Férias
    IF inCodConfiguracao = 2 THEN
        stSql := '( SELECT registro_evento_ferias.cod_contrato
                      FROM folhapagamento'|| stEntidade ||'.registro_evento_ferias
                      JOIN folhapagamento'|| stEntidade ||'.evento_ferias_calculado
                        ON registro_evento_ferias.cod_registro  = evento_ferias_calculado.cod_registro
                       AND registro_evento_ferias.cod_evento    = evento_ferias_calculado.cod_evento
                       AND registro_evento_ferias.desdobramento = evento_ferias_calculado.desdobramento
                       AND registro_evento_ferias.timestamp     = evento_ferias_calculado.timestamp_registro
                     WHERE registro_evento_ferias.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||' ) ';
    END IF;
    --Décimo
    IF inCodConfiguracao = 3 THEN
        stSql := '( SELECT registro_evento_decimo.cod_contrato 
                      FROM folhapagamento'|| stEntidade ||'.registro_evento_decimo
                      JOIN folhapagamento'|| stEntidade ||'.evento_decimo_calculado
                        ON registro_evento_decimo.cod_registro  = evento_decimo_calculado.cod_registro
                       AND registro_evento_decimo.cod_evento    = evento_decimo_calculado.cod_evento
                       AND registro_evento_decimo.desdobramento = evento_decimo_calculado.desdobramento
                       AND registro_evento_decimo.timestamp     = evento_decimo_calculado.timestamp_registro
                     WHERE registro_evento_decimo.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||' ) ';
    END IF;
    --Rescisão
    IF inCodConfiguracao = 4 THEN
        stSql := '( SELECT registro_evento_rescisao.cod_contrato
                      FROM folhapagamento'|| stEntidade ||'.registro_evento_rescisao
                      JOIN folhapagamento'|| stEntidade ||'.evento_rescisao_calculado
                        ON registro_evento_rescisao.cod_registro  = evento_rescisao_calculado.cod_registro
                       AND registro_evento_rescisao.cod_evento    = evento_rescisao_calculado.cod_evento
                       AND registro_evento_rescisao.desdobramento = evento_rescisao_calculado.desdobramento
                       AND registro_evento_rescisao.timestamp     = evento_rescisao_calculado.timestamp_registro
                     WHERE registro_evento_rescisao.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||' )';
    END IF;
    --Complementar
    IF inCodConfiguracao = 0 THEN
        stSql := '( SELECT registro_evento_complementar.cod_contrato
                      FROM folhapagamento'|| stEntidade ||'.registro_evento_complementar
                      JOIN folhapagamento'|| stEntidade ||'.evento_complementar_calculado
                        ON registro_evento_complementar.cod_registro     = evento_complementar_calculado.cod_registro
                       AND registro_evento_complementar.cod_evento       = evento_complementar_calculado.cod_evento
                       AND registro_evento_complementar.cod_configuracao = evento_complementar_calculado.cod_configuracao
                       AND registro_evento_complementar.timestamp        = evento_complementar_calculado.timestamp_registro
                     WHERE registro_evento_complementar.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                       AND registro_evento_complementar.cod_complementar = '|| inCodComplementar ||' ) ';
    END IF;

    RETURN stSql;
END;
$$LANGUAGE 'plpgsql';

CREATE OR REPLACE FUNCTION montaeventosCalculadosFolhaAnaliticaResumida(INTEGER,INTEGER,INTEGER,INTEGER,VARCHAR,VARCHAR,VARCHAR) RETURNS VARCHAR AS $$
DECLARE
    inCodConfiguracao                       ALIAS FOR $1;
    inCodContrato                           ALIAS FOR $2;
    inCodPeriodoMovimentacao                ALIAS FOR $3;
    inCodComplementar                       ALIAS FOR $4;
    stOrdenacao                             ALIAS FOR $5;
    stNatureza                              ALIAS FOR $6;
    stEntidade                              ALIAS FOR $7;
    stSql                                   VARCHAR := '';
BEGIN
    IF inCodConfiguracao = 1 THEN
        stSql := '
        SELECT evento_calculado.valor
             , evento_calculado.quantidade
             , evento.codigo
             , evento.cod_evento
             , evento.descricao
             , evento.natureza
             , CASE WHEN evento_calculado.desdobramento IS NULL THEN ''''
               ELSE evento_calculado.desdobramento END AS desdobramento
          FROM folhapagamento'|| stEntidade ||'.registro_evento_periodo
             , folhapagamento'|| stEntidade ||'.evento_calculado
             , folhapagamento'|| stEntidade ||'.evento
             , folhapagamento'|| stEntidade ||'.sequencia_calculo_evento
             , folhapagamento'|| stEntidade ||'.sequencia_calculo
         WHERE registro_evento_periodo.cod_registro             = evento_calculado.cod_registro
           AND evento_calculado.cod_evento                      = evento.cod_evento
           AND evento.cod_evento                                = sequencia_calculo_evento.cod_evento
           AND sequencia_calculo_evento.cod_sequencia           = sequencia_calculo.cod_sequencia
           AND registro_evento_periodo.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
           AND registro_evento_periodo.cod_contrato             = '|| inCodContrato ||'
           AND evento.natureza = ANY(STRING_TO_ARRAY('|| stNatureza ||', '',''))
         ORDER BY '|| stOrdenacao ||'
        ';
    END IF;

    IF inCodConfiguracao = 2 THEN
        stSql := '
        SELECT evento_ferias_calculado.valor
             , evento_ferias_calculado.quantidade
             , evento.codigo
             , evento.cod_evento
             , evento.descricao
             , evento.natureza
             , evento_ferias_calculado.desdobramento
          FROM folhapagamento'|| stEntidade ||'.registro_evento_ferias
             , folhapagamento'|| stEntidade ||'.evento_ferias_calculado
             , folhapagamento'|| stEntidade ||'.evento
             , folhapagamento'|| stEntidade ||'.sequencia_calculo_evento
             , folhapagamento'|| stEntidade ||'.sequencia_calculo
         WHERE registro_evento_ferias.cod_registro             = evento_ferias_calculado.cod_registro
           AND registro_evento_ferias.desdobramento            = evento_ferias_calculado.desdobramento
           AND registro_evento_ferias.timestamp                = evento_ferias_calculado.timestamp_registro
   	       AND registro_evento_ferias.cod_evento               = evento_ferias_calculado.cod_evento
           AND evento_ferias_calculado.cod_evento              = evento.cod_evento
           AND evento.cod_evento                               = sequencia_calculo_evento.cod_evento
           AND sequencia_calculo_evento.cod_sequencia          = sequencia_calculo.cod_sequencia
           AND registro_evento_ferias.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
           AND registro_evento_ferias.cod_contrato             = '|| inCodContrato ||'
           AND evento.natureza = ANY(STRING_TO_ARRAY('|| stNatureza ||', '',''))
         ORDER BY '|| stOrdenacao ||'
        ';
    END IF;
    
    IF inCodConfiguracao = 3 THEN
        stSql := '
        SELECT evento_decimo_calculado.valor
             , evento_decimo_calculado.quantidade
             , evento.codigo
             , evento.cod_evento
             , evento.descricao
             , evento.natureza
             , evento_decimo_calculado.desdobramento
          FROM folhapagamento'|| stEntidade ||'.registro_evento_decimo
             , folhapagamento'|| stEntidade ||'.evento_decimo_calculado
             , folhapagamento'|| stEntidade ||'.evento
             , folhapagamento'|| stEntidade ||'.sequencia_calculo_evento
             , folhapagamento'|| stEntidade ||'.sequencia_calculo
         WHERE registro_evento_decimo.cod_registro             = evento_decimo_calculado.cod_registro
           AND registro_evento_decimo.cod_evento               = evento_decimo_calculado.cod_evento
           AND registro_evento_decimo.desdobramento            = evento_decimo_calculado.desdobramento
           AND registro_evento_decimo.timestamp                = evento_decimo_calculado.timestamp_registro
           AND evento_decimo_calculado.cod_evento              = evento.cod_evento
           AND evento.cod_evento                               = sequencia_calculo_evento.cod_evento
           AND sequencia_calculo_evento.cod_sequencia          = sequencia_calculo.cod_sequencia
           AND registro_evento_decimo.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
           AND registro_evento_decimo.cod_contrato             = '|| inCodContrato ||'
           AND evento.natureza = ANY(STRING_TO_ARRAY('|| stNatureza ||', '',''))
         ORDER BY '|| stOrdenacao ||'
        ';
    END IF;   
    
    IF inCodConfiguracao = 4 THEN
        stSql := '
        SELECT evento_rescisao_calculado.valor
             , evento_rescisao_calculado.quantidade
             , evento.codigo
             , evento.cod_evento
             , evento.descricao
             , evento.natureza
             , evento_rescisao_calculado.desdobramento
          FROM folhapagamento'|| stEntidade ||'.registro_evento_rescisao
             , folhapagamento'|| stEntidade ||'.evento_rescisao_calculado
             , folhapagamento'|| stEntidade ||'.evento
             , folhapagamento'|| stEntidade ||'.sequencia_calculo_evento
             , folhapagamento'|| stEntidade ||'.sequencia_calculo
         WHERE registro_evento_rescisao.cod_registro             = evento_rescisao_calculado.cod_registro
           AND registro_evento_rescisao.cod_evento               = evento_rescisao_calculado.cod_evento
           AND registro_evento_rescisao.desdobramento            = evento_rescisao_calculado.desdobramento
           AND registro_evento_rescisao.timestamp                = evento_rescisao_calculado.timestamp_registro
           AND evento_rescisao_calculado.cod_evento              = evento.cod_evento
           AND evento.cod_evento                                 = sequencia_calculo_evento.cod_evento
           AND sequencia_calculo_evento.cod_sequencia            = sequencia_calculo.cod_sequencia
           AND registro_evento_rescisao.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
           AND registro_evento_rescisao.cod_contrato             = '|| inCodContrato ||'
           AND evento.natureza = ANY(STRING_TO_ARRAY('|| stNatureza ||', '',''))
         ORDER BY '|| stOrdenacao ||'
        ';
    END IF; 

    IF inCodConfiguracao = 0 THEN
        stSql := '
        SELECT evento_complementar_calculado.valor
             , evento_complementar_calculado.quantidade
             , evento.codigo
             , evento.cod_evento
             , evento.descricao
             , evento.natureza
             , CASE WHEN evento_complementar_calculado.desdobramento IS NULL THEN ''''
               ELSE evento_complementar_calculado.desdobramento END AS desdobramento
          FROM folhapagamento'|| stEntidade ||'.registro_evento_complementar
             , folhapagamento'|| stEntidade ||'.evento_complementar_calculado
             , folhapagamento'|| stEntidade ||'.evento
             , folhapagamento'|| stEntidade ||'.sequencia_calculo_evento
             , folhapagamento'|| stEntidade ||'.sequencia_calculo
         WHERE registro_evento_complementar.cod_registro             = evento_complementar_calculado.cod_registro
           AND registro_evento_complementar.cod_evento               = evento_complementar_calculado.cod_evento
           AND registro_evento_complementar.cod_configuracao         = evento_complementar_calculado.cod_configuracao
           AND registro_evento_complementar.timestamp                = evento_complementar_calculado.timestamp_registro
           AND evento_complementar_calculado.cod_evento              = evento.cod_evento
           AND evento.cod_evento                                     = sequencia_calculo_evento.cod_evento
           AND sequencia_calculo_evento.cod_sequencia                = sequencia_calculo.cod_sequencia
           AND registro_evento_complementar.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
           AND registro_evento_complementar.cod_complementar         = '|| inCodComplementar ||'
           AND registro_evento_complementar.cod_contrato             = '|| inCodContrato ||'
           AND evento.natureza = ANY(STRING_TO_ARRAY('|| stNatureza ||', '',''))
         ORDER BY '|| stOrdenacao ||'
        ';
    END IF;       
    RETURN stSql;
END;
$$LANGUAGE 'plpgsql';

CREATE OR REPLACE FUNCTION criaTabelasTemporariasFolhaAnaliticaResumida(INTEGER,INTEGER,INTEGER,VARCHAR) RETURNS BOOLEAN AS $$
DECLARE
    inCodConfiguracao                       ALIAS FOR $1;
    inCodPeriodoMovimentacao                ALIAS FOR $2;
    inCodComplementar                       ALIAS FOR $3;
    stEntidade                              ALIAS FOR $4;
    stSql                                   VARCHAR := '';
    stFrom                                  VARCHAR := '';
BEGIN

    IF inCodConfiguracao = 1 THEN
        stFrom := '
                 FROM folhapagamento'|| stEntidade ||'.registro_evento_periodo AS tabela_registro_evento
                 JOIN folhapagamento'|| stEntidade ||'.registro_evento
                   ON registro_evento.cod_registro = tabela_registro_evento.cod_registro
                 JOIN folhapagamento'|| stEntidade ||'.ultimo_registro_evento AS tabela_ultimo_registro_evento
                   ON tabela_ultimo_registro_evento.cod_registro     = registro_evento.cod_registro
                  AND tabela_ultimo_registro_evento.cod_evento       = registro_evento.cod_evento
                  AND tabela_ultimo_registro_evento.timestamp        = registro_evento.timestamp
                 JOIN folhapagamento'|| stEntidade ||'.evento_calculado AS tabela_evento_calculado
                   ON tabela_evento_calculado.timestamp_registro = tabela_ultimo_registro_evento.timestamp
                  AND tabela_evento_calculado.cod_registro       = tabela_ultimo_registro_evento.cod_registro
                  AND tabela_evento_calculado.cod_evento         = tabela_ultimo_registro_evento.cod_evento
            LEFT JOIN folhapagamento'|| stEntidade ||'.registro_evento_parcela AS tabela_parcela
                   ON tabela_evento_calculado.timestamp_registro = tabela_parcela.timestamp
                  AND tabela_evento_calculado.cod_registro       = tabela_parcela.cod_registro
                  AND tabela_evento_calculado.cod_evento         = tabela_parcela.cod_evento
        ';
    ELSEIF inCodConfiguracao = 2 THEN
        stFrom := '
                 FROM folhapagamento'|| stEntidade ||'.registro_evento_ferias AS tabela_registro_evento
                 JOIN folhapagamento'|| stEntidade ||'.ultimo_registro_evento_ferias AS tabela_ultimo_registro_evento
                   ON tabela_ultimo_registro_evento.cod_registro      = tabela_registro_evento.cod_registro
                  AND tabela_ultimo_registro_evento.cod_evento        = tabela_registro_evento.cod_evento
                  AND tabela_ultimo_registro_evento.timestamp         = tabela_registro_evento.timestamp
                  AND tabela_ultimo_registro_evento.desdobramento = tabela_registro_evento.desdobramento
                 JOIN folhapagamento'|| stEntidade ||'.evento_ferias_calculado AS tabela_evento_calculado
                   ON tabela_evento_calculado.timestamp_registro = tabela_ultimo_registro_evento.timestamp
                  AND tabela_evento_calculado.cod_registro       = tabela_ultimo_registro_evento.cod_registro
                  AND tabela_evento_calculado.cod_evento         = tabela_ultimo_registro_evento.cod_evento
                  AND tabela_evento_calculado.desdobramento  = tabela_ultimo_registro_evento.desdobramento
            LEFT JOIN folhapagamento'|| stEntidade ||'.registro_evento_ferias_parcela AS tabela_parcela
                   ON tabela_evento_calculado.timestamp_registro = tabela_parcela.timestamp
                  AND tabela_evento_calculado.cod_registro       = tabela_parcela.cod_registro
                  AND tabela_evento_calculado.cod_evento         = tabela_parcela.cod_evento
        ';
    ELSEIF inCodConfiguracao = 3 THEN
        stFrom := '
                 FROM folhapagamento'|| stEntidade ||'.registro_evento_decimo AS tabela_registro_evento
                 JOIN folhapagamento'|| stEntidade ||'.ultimo_registro_evento_decimo AS tabela_ultimo_registro_evento
                   ON tabela_ultimo_registro_evento.cod_registro  = tabela_registro_evento.cod_registro
                  AND tabela_ultimo_registro_evento.cod_evento    = tabela_registro_evento.cod_evento
                  AND tabela_ultimo_registro_evento.timestamp     = tabela_registro_evento.timestamp
                  AND tabela_ultimo_registro_evento.desdobramento = tabela_registro_evento.desdobramento
                 JOIN folhapagamento'|| stEntidade ||'.evento_decimo_calculado AS tabela_evento_calculado
                   ON tabela_evento_calculado.timestamp_registro = tabela_ultimo_registro_evento.timestamp
                  AND tabela_evento_calculado.cod_registro       = tabela_ultimo_registro_evento.cod_registro
                  AND tabela_evento_calculado.cod_evento         = tabela_ultimo_registro_evento.cod_evento
                  AND tabela_evento_calculado.desdobramento      = tabela_ultimo_registro_evento.desdobramento
            LEFT JOIN folhapagamento'|| stEntidade ||'.registro_evento_decimo_parcela AS tabela_parcela
                   ON tabela_evento_calculado.timestamp_registro = tabela_parcela.timestamp
                  AND tabela_evento_calculado.cod_registro       = tabela_parcela.cod_registro
                  AND tabela_evento_calculado.cod_evento         = tabela_parcela.cod_evento
        ';

    ELSEIF inCodConfiguracao = 4 THEN
        stFrom := '
                 FROM folhapagamento'|| stEntidade ||'.registro_evento_rescisao AS tabela_registro_evento
                 JOIN folhapagamento'|| stEntidade ||'.ultimo_registro_evento_rescisao AS tabela_ultimo_registro_evento
                   ON tabela_ultimo_registro_evento.cod_registro  = tabela_registro_evento.cod_registro
                  AND tabela_ultimo_registro_evento.cod_evento    = tabela_registro_evento.cod_evento
                  AND tabela_ultimo_registro_evento.timestamp     = tabela_registro_evento.timestamp
                  AND tabela_ultimo_registro_evento.desdobramento = tabela_registro_evento.desdobramento
                 JOIN folhapagamento'|| stEntidade ||'.evento_rescisao_calculado AS tabela_evento_calculado
                   ON tabela_evento_calculado.timestamp_registro = tabela_ultimo_registro_evento.timestamp
                  AND tabela_evento_calculado.cod_registro       = tabela_ultimo_registro_evento.cod_registro
                  AND tabela_evento_calculado.cod_evento         = tabela_ultimo_registro_evento.cod_evento
                  AND tabela_evento_calculado.desdobramento      = tabela_ultimo_registro_evento.desdobramento
            LEFT JOIN folhapagamento'|| stEntidade ||'.registro_evento_rescisao_parcela AS tabela_parcela
                   ON tabela_evento_calculado.timestamp_registro = tabela_parcela.timestamp
                  AND tabela_evento_calculado.cod_registro       = tabela_parcela.cod_registro
                  AND tabela_evento_calculado.cod_evento         = tabela_parcela.cod_evento
        ';

    ELSEIF inCodConfiguracao = 0 THEN
        stFrom := '
                 FROM folhapagamento'|| stEntidade ||'.registro_evento_complementar AS tabela_registro_evento
                 JOIN folhapagamento'|| stEntidade ||'.ultimo_registro_evento_complementar AS tabela_ultimo_registro_evento
                   ON tabela_ultimo_registro_evento.cod_registro     = tabela_registro_evento.cod_registro
                  AND tabela_ultimo_registro_evento.cod_evento       = tabela_registro_evento.cod_evento
                  AND tabela_ultimo_registro_evento.timestamp        = tabela_registro_evento.timestamp
                  AND tabela_ultimo_registro_evento.cod_configuracao = tabela_registro_evento.cod_configuracao
                 JOIN folhapagamento'|| stEntidade ||'.evento_complementar_calculado AS tabela_evento_calculado
                   ON tabela_evento_calculado.timestamp_registro = tabela_ultimo_registro_evento.timestamp
                  AND tabela_evento_calculado.cod_registro       = tabela_ultimo_registro_evento.cod_registro
                  AND tabela_evento_calculado.cod_evento         = tabela_ultimo_registro_evento.cod_evento
                  AND tabela_evento_calculado.cod_configuracao   = tabela_ultimo_registro_evento.cod_configuracao
            LEFT JOIN folhapagamento'|| stEntidade ||'.registro_evento_complementar_parcela AS tabela_parcela
                   ON tabela_evento_calculado.timestamp_registro = tabela_parcela.timestamp
                  AND tabela_evento_calculado.cod_registro       = tabela_parcela.cod_registro
                  AND tabela_evento_calculado.cod_evento         = tabela_parcela.cod_evento
        ';
    END IF;

    stSql := '
    CREATE TEMP TABLE evento_calculado_tmp AS SELECT DISTINCT tabela_registro_evento.cod_contrato 
                                                '|| stFrom ||'
                                                WHERE tabela_registro_evento.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||' ';

    EXECUTE stSql;

    CREATE INDEX idx_evento_calculado_tmp ON evento_calculado_tmp(cod_contrato);

    stSql := ' CREATE TEMP TABLE eventos_tmp AS 
               SELECT tabela_evento_calculado.valor AS valorP
                    , tabela_evento_calculado.quantidade AS quantidadeP
                    , evento.codigo AS codigoP
                    , evento.cod_evento AS cod_eventoP
                    , evento.descricao AS descricaoP
                    , evento.natureza AS naturezaP
                    , tabela_evento_calculado.desdobramento AS desdobramentoP
                    , null AS valorD
                    , null AS quantidadeD
                    , null AS codigoD
                    , null AS cod_eventoD
                    , null AS descricaoD
                    , null AS naturezaD
                    , null AS desdobramentoD
                    , 0    AS valorB
                    , 0    AS quantidadeB
                    , null AS codigoB
                    , 0    AS cod_eventoB
                    , null AS descricaoB
                    , null AS naturezaB
                    , null AS desdobramentoB
                    , tabela_registro_evento.cod_contrato
                    , tabela_parcela.parcela
                    '|| stFrom ||'
                 JOIN folhapagamento'|| stEntidade ||'.evento
                   ON evento.cod_evento = tabela_evento_calculado.cod_evento
                WHERE tabela_registro_evento.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                  AND evento.natureza IN (''P'') ';

    IF inCodConfiguracao = 0 THEN
        stSql := stSql || ' AND tabela_registro_evento.cod_complementar = '|| inCodComplementar;
    END IF;

    stSql := stSql || '
                UNION

               SELECT null AS valorP
                    , null AS quantidadeP
                    , null AS codigoP
                    , null AS cod_eventoP
                    , null AS descricaoP
                    , null AS naturezaP
                    , null AS desdobramentoP
                    , tabela_evento_calculado.valor AS valorD
                    , tabela_evento_calculado.quantidade AS quantidadeD
                    , evento.codigo AS codigoD
                    , evento.cod_evento AS cod_eventoD
                    , evento.descricao AS descricaoD
                    , evento.natureza AS naturezaD
                    , tabela_evento_calculado.desdobramento AS desdobramentoD
                    , 0    AS valorB
                    , 0    AS quantidadeB
                    , null AS codigoB
                    , 0    AS cod_eventoB
                    , null AS descricaoB
                    , null AS naturezaB
                    , null AS desdobramentoB
                    , tabela_registro_evento.cod_contrato
                    , tabela_parcela.parcela
                    '|| stFrom ||'
                 JOIN folhapagamento'|| stEntidade ||'.evento
                   ON evento.cod_evento = tabela_evento_calculado.cod_evento
                WHERE tabela_registro_evento.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                  AND evento.natureza IN (''D'') ';

    IF inCodConfiguracao = 0 THEN
        stSql := stSql || ' AND tabela_registro_evento.cod_complementar = '|| inCodComplementar;
    END IF;

    stSql := stSql || '
                UNION

               SELECT null AS valorP
                    , null AS quantidadeP
                    , null AS codigoP
                    , null AS cod_eventoP
                    , null AS descricaoP
                    , null AS naturezaP
                    , null AS desdobramentoP
                    , null AS valorD
                    , null AS quantidadeD
                    , null AS codigoD
                    , null AS cod_eventoD
                    , null AS descricaoD
                    , null AS naturezaD
                    , null AS desdobramentoD
                    , tabela_evento_calculado.valor AS valorB
                    , tabela_evento_calculado.quantidade AS quantidadeB
                    , evento.codigo AS codigoB
                    , evento.cod_evento AS cod_eventoB
                    , evento.descricao AS descricaoB
                    , evento.natureza AS naturezaB
                    , tabela_evento_calculado.desdobramento AS desdobramentoB
                    , tabela_registro_evento.cod_contrato
                    , tabela_parcela.parcela
                    '|| stFrom ||'
                 JOIN folhapagamento'|| stEntidade ||'.evento
                   ON evento.cod_evento = tabela_evento_calculado.cod_evento
                WHERE tabela_registro_evento.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                  AND evento.natureza IN (''B'', ''I'') ';

    IF inCodConfiguracao = 0 THEN
        stSql := stSql || ' AND tabela_registro_evento.cod_complementar = '|| inCodComplementar;
    END IF;

    EXECUTE stSql;

    RETURN true;
END;
$$LANGUAGE 'plpgsql';

CREATE OR REPLACE FUNCTION eventosCalculadosFolhaAnaliticaResumida(INTEGER,INTEGER,INTEGER,INTEGER,VARCHAR,VARCHAR) RETURNS SETOF colunasEventosCalculadosAnaliticaResumida AS $$
DECLARE
    inCodConfiguracao                       ALIAS FOR $1;
    inCodContrato                           ALIAS FOR $2;
    inCodPeriodoMovimentacao                ALIAS FOR $3;
    inCodComplementar                       ALIAS FOR $4;
    stOrdenacao                             ALIAS FOR $5;
    stEntidade                           ALIAS FOR $6;
    stSql                                   VARCHAR := '';
    stInsert                                VARCHAR := '';
    stUpdate                                VARCHAR := '';
    reRegistro                              RECORD;
    inIndice                                INTEGER;
    inVerifica                              INTEGER;
    inIdentificador                         INTEGER;
    rwEventosCalculadosAnaliticaResumida    colunasEventosCalculadosAnaliticaResumida%ROWTYPE;
BEGIN
    --verifica se a sequence tmp_eventos_calculados
    IF ((SELECT 1 FROM pg_catalog.pg_statio_user_sequences WHERE relname='tmp_eventos_calculados') IS NOT NULL) THEN
        SELECT NEXTVAL('folhapagamento.tmp_eventos_calculados')
          INTO inIdentificador;
    ELSE
        CREATE SEQUENCE folhapagamento.tmp_eventos_calculados START 1;
        SELECT NEXTVAL('folhapagamento.tmp_eventos_calculados')
          INTO inIdentificador;
    END IF;

    stSql := '
    CREATE TEMPORARY TABLE tmp_eventos_calculados_'|| inIdentificador ||'
        (indice         integer,
         codigoP        character(5),
         descricaoP     character(80),
         quantidadeP    numeric(15,2),
         valorP         numeric(15,2),
         desdobramentoP character(1),
         codigoD        character(5),
         descricaoD     character(80),
         quantidadeD    numeric(15,2),
         valorD         numeric(15,2),
         desdobramentoD character(1),
         codigoB        character(5),
         descricaoB     character(80),
         quantidadeB    numeric(15,2),
         valorB         numeric(15,2),
         desdobramentoB character(1)
        );
    ';

    EXECUTE stSql;

    stSql := montaeventosCalculadosFolhaAnaliticaResumida(inCodConfiguracao,inCodContrato,inCodPeriodoMovimentacao,inCodComplementar,stOrdenacao,quote_literal('P'),stEntidade); 
    inIndice := 1;
    FOR reRegistro IN  EXECUTE stSql
    LOOP
        stInsert := 'INSERT INTO tmp_eventos_calculados_'|| inIdentificador ||' (indice,codigoP,descricaoP,quantidadeP,valorP,desdobramentoP) 
                                                 VALUES ('|| inIndice ||','|| quote_literal(reRegistro.codigo) ||','|| quote_literal(reRegistro.descricao) ||','|| reRegistro.quantidade ||','|| reRegistro.valor ||','|| quote_literal(reRegistro.desdobramento) ||')';
        EXECUTE stInsert;
        inIndice := inIndice + 1;
    END LOOP;

    stSql := montaeventosCalculadosFolhaAnaliticaResumida(inCodConfiguracao,inCodContrato,inCodPeriodoMovimentacao,inCodComplementar,stOrdenacao,quote_literal('D'),stEntidade); 
    inIndice := 1;
    FOR reRegistro IN  EXECUTE stSql
    LOOP
        inVerifica := selectIntoInteger('SELECT 1 FROM tmp_eventos_calculados_'|| inIdentificador ||' WHERE indice = '|| inIndice ||' ');
        IF inVerifica = 1 THEN
            stUpdate := 'UPDATE tmp_eventos_calculados_'|| inIdentificador ||' SET codigoD = '|| quote_literal(reRegistro.codigo) ||',
                                                           descricaoD = '|| quote_literal(reRegistro.descricao) ||',
                                                           quantidadeD = '|| reRegistro.quantidade ||',
                                                           valorD = '|| reRegistro.valor ||',
                                                           desdobramentoD = '|| quote_literal(reRegistro.desdobramento) ||'
                                                WHERE indice = '|| inIndice;
            EXECUTE stUpDATE;
        ELSE        
            stInsert := 'INSERT INTO tmp_eventos_calculados_'|| inIdentificador ||' (indice,codigoD,descricaoD,quantidadeD,valorD,desdobramentoD) 
                                                     VALUES ('|| inIndice ||','|| quote_literal(reRegistro.codigo) ||','|| quote_literal(reRegistro.descricao) ||','|| reRegistro.quantidade ||','|| reRegistro.valor ||','|| quote_literal(reRegistro.desdobramento) ||')';
            EXECUTE stInsert;
        END IF;
        inIndice := inIndice + 1;
    END LOOP;

    stSql := montaeventosCalculadosFolhaAnaliticaResumida(inCodConfiguracao,inCodContrato,inCodPeriodoMovimentacao,inCodComplementar,stOrdenacao,quote_literal('B,I'),stEntidade); 
    inIndice := 1;
    FOR reRegistro IN  EXECUTE stSql
    LOOP
        inVerifica := selectIntoInteger('SELECT 1 FROM tmp_eventos_calculados_'|| inIdentificador ||' WHERE indice = '|| inIndice ||' ');
        IF inVerifica = 1 THEN
            stUpdate := 'UPDATE tmp_eventos_calculados_'|| inIdentificador ||' SET codigoB = '|| quote_literal(reRegistro.codigo) ||',
                                                           descricaoB = '|| quote_literal(reRegistro.descricao) ||',
                                                           quantidadeB = '|| reRegistro.quantidade ||',
                                                           valorB = '|| reRegistro.valor ||',
                                                           desdobramentoB = '|| quote_literal(reRegistro.desdobramento) ||'
                                                WHERE indice = '|| inIndice;
            EXECUTE stUpDATE;
        ELSE        
            stInsert := 'INSERT INTO tmp_eventos_calculados_'|| inIdentificador ||' (indice,codigoB,descricaoB,quantidadeB,valorB,desdobramentoB) 
                                                     VALUES ('|| inIndice ||','|| quote_literal(reRegistro.codigo) ||','|| quote_literal(reRegistro.descricao) ||','|| reRegistro.quantidade ||','|| reRegistro.valor ||','|| quote_literal(reRegistro.desdobramento) ||')';
            EXECUTE stInsert;
        END IF;
        inIndice := inIndice + 1;
    END LOOP;


    stSql := 'SELECT * FROM tmp_eventos_calculados_'|| inIdentificador ||' ORDER BY indice';
    FOR reRegistro IN  EXECUTE stSql
    LOOP
        rwEventosCalculadosAnaliticaResumida.codigoP                 := reRegistro.codigoP;
        rwEventosCalculadosAnaliticaResumida.descricaoP              := reRegistro.descricaoP;
        rwEventosCalculadosAnaliticaResumida.quantidadeP             := reRegistro.quantidadeP;
        rwEventosCalculadosAnaliticaResumida.valorP                  := reRegistro.valorP;
        rwEventosCalculadosAnaliticaResumida.desdobramentoP          := reRegistro.desdobramentoP;

        rwEventosCalculadosAnaliticaResumida.codigoD                 := reRegistro.codigoD;
        rwEventosCalculadosAnaliticaResumida.descricaoD              := reRegistro.descricaoD;
        rwEventosCalculadosAnaliticaResumida.quantidadeD             := reRegistro.quantidadeD;
        rwEventosCalculadosAnaliticaResumida.valorD                  := reRegistro.valorD;
        rwEventosCalculadosAnaliticaResumida.desdobramentoD          := reRegistro.desdobramentoD;

        rwEventosCalculadosAnaliticaResumida.codigoB                 := reRegistro.codigoB;
        rwEventosCalculadosAnaliticaResumida.descricaoB              := reRegistro.descricaoB;
        rwEventosCalculadosAnaliticaResumida.quantidadeB             := reRegistro.quantidadeB;
        rwEventosCalculadosAnaliticaResumida.valorB                  := reRegistro.valorB;
        rwEventosCalculadosAnaliticaResumida.desdobramentoB          := reRegistro.desdobramentoB;
        RETURN NEXT rwEventosCalculadosAnaliticaResumida;
    END LOOP;

    EXECUTE 'DROP TABLE tmp_eventos_calculados_'|| inIdentificador;
END;
$$LANGUAGE 'plpgsql';


CREATE OR REPLACE FUNCTION eventosCalculadosFolhaAnalitica(INTEGER,INTEGER,INTEGER,INTEGER,VARCHAR,VARCHAR,VARCHAR,VARCHAR) RETURNS SETOF colunasEventosCalculadosAnalitica AS $$
DECLARE
    inCodConfiguracao                       ALIAS FOR $1;
    inCodContrato                           ALIAS FOR $2;
    inCodPeriodoMovimentacao                ALIAS FOR $3;
    inCodComplementar                       ALIAS FOR $4;
    stOrdenacao                             ALIAS FOR $5;
    stNaturezaE                             ALIAS FOR $6;
    stNaturezaD                             ALIAS FOR $7;
    stEntidade                           ALIAS FOR $8;
    stNaturezaETemp                         VARCHAR := '';
    stNaturezaDTemp                         VARCHAR := '';    
    stSql                                   VARCHAR := '';
    stInsert                                VARCHAR := '';
    stUpdate                                VARCHAR := '';
    reRegistro                              RECORD;
    inIndice                                INTEGER;
    inVerifica                              INTEGER;
    rwEventosCalculadosAnalitica            colunasEventosCalculadosAnalitica%ROWTYPE;
BEGIN
    CREATE TEMPORARY TABLE tmp_eventos_calculados
        (indice         integer,
         codigoE        character(5),
         descricaoE     character(80),
         quantidadeE    numeric(15,2),
         valorE         numeric(15,2),
         desdobramentoE character(1),
         codigoD        character(5),
         descricaoD     character(80),
         quantidadeD    numeric(15,2),
         valorD         numeric(15,2),
         desdobramentoD character(1)
        );

    stNaturezaETemp := ' '|| quote_literal(stNaturezaE) ||' ';
    stSql := montaeventosCalculadosFolhaAnaliticaResumida(inCodConfiguracao,inCodContrato,inCodPeriodoMovimentacao,inCodComplementar,stOrdenacao,stNaturezaETemp,stEntidade); 
    inIndice := 1;
    FOR reRegistro IN  EXECUTE stSql
    LOOP
        stInsert := 'INSERT INTO tmp_eventos_calculados (indice,codigoE,descricaoE,quantidadeE,valorE,desdobramentoE) 
                                                 VALUES ('|| inIndice ||','|| quote_literal(reRegistro.codigo) ||','|| quote_literal(reRegistro.descricao) ||','|| reRegistro.quantidade ||','|| reRegistro.valor ||','|| quote_literal(reRegistro.desdobramento) ||')';
        EXECUTE stInsert;
        inIndice := inIndice + 1;
    END LOOP;

    stNaturezaDTemp := ' '|| quote_literal(stNaturezaD) ||' ';
    stSql := montaeventosCalculadosFolhaAnaliticaResumida(inCodConfiguracao,inCodContrato,inCodPeriodoMovimentacao,inCodComplementar,stOrdenacao,stNaturezaDTemp,stEntidade); 
    inIndice := 1;
    FOR reRegistro IN  EXECUTE stSql
    LOOP
        SELECT INTO inVerifica (SELECT 1 FROM tmp_eventos_calculados WHERE indice = inIndice);
        IF inVerifica = 1 THEN
            stUpdate := 'UPDATE tmp_eventos_calculados SET codigoD = '|| quote_literal(reRegistro.codigo) ||',
                                                           descricaoD = '|| quote_literal(reRegistro.descricao) ||',
                                                           quantidadeD = '|| reRegistro.quantidade ||',
                                                           valorD = '|| reRegistro.valor ||',
                                                           desdobramentoD = '|| quote_literal(reRegistro.desdobramento) ||'
                                                WHERE indice = '|| inIndice;
            EXECUTE stUpDATE;
        ELSE        
            stInsert := 'INSERT INTO tmp_eventos_calculados (indice,codigoD,descricaoD,quantidadeD,valorD,desdobramentoD) 
                                                     VALUES ('|| inIndice ||','|| quote_literal(reRegistro.codigo) ||','|| quote_literal(reRegistro.descricao) ||','|| reRegistro.quantidade ||','|| reRegistro.valor ||','|| quote_literal(reRegistro.desdobramento) ||')';
            EXECUTE stInsert;
        END IF;
        inIndice := inIndice + 1;
    END LOOP;

    stSql := 'SELECT * FROM tmp_eventos_calculados ORDER BY indice';
    FOR reRegistro IN  EXECUTE stSql
    LOOP
        rwEventosCalculadosAnalitica.codigoE                 := reRegistro.codigoE;
        rwEventosCalculadosAnalitica.descricaoE              := reRegistro.descricaoE;
        rwEventosCalculadosAnalitica.quantidadeE             := reRegistro.quantidadeE;
        rwEventosCalculadosAnalitica.valorE                  := reRegistro.valorE;
        rwEventosCalculadosAnalitica.desdobramentoE          := reRegistro.desdobramentoE;

        rwEventosCalculadosAnalitica.codigoD                 := reRegistro.codigoD;
        rwEventosCalculadosAnalitica.descricaoD              := reRegistro.descricaoD;
        rwEventosCalculadosAnalitica.quantidadeD             := reRegistro.quantidadeD;
        rwEventosCalculadosAnalitica.valorD                  := reRegistro.valorD;
        rwEventosCalculadosAnalitica.desdobramentoD          := reRegistro.desdobramentoD;
        RETURN NEXT rwEventosCalculadosAnalitica;
    END LOOP;

    DROP TABLE tmp_eventos_calculados;
END;
$$LANGUAGE 'plpgsql';

/*
CREATE OR REPLACE FUNCTION eventosCalculadosFolhaAnalitica(INTEGER,INTEGER,INTEGER,VARCHAR,VARCHAR,VARCHAR) RETURNS SETOF colunasEventosCalculadosAnalitica AS $$
DECLARE
    inCodConfiguracao                       ALIAS FOR $1;
    inCodContrato                           ALIAS FOR $2;
    inCodPeriodoMovimentacao                ALIAS FOR $3;
    stOrdenacao                             ALIAS FOR $4;
    stNaturezaE                             ALIAS FOR $5;
    stNaturezaD                             ALIAS FOR $6;
    stNaturezaETemp                         VARCHAR := '';
    stNaturezaDTemp                         VARCHAR := '';    
    stSql                                   VARCHAR := '';
    stInsert                                VARCHAR := '';
    stUpdate                                VARCHAR := '';
    reRegistro                              RECORD;
    inIndice                                INTEGER;
    inVerifica                              INTEGER;
    rwEventosCalculadosAnalitica            colunasEventosCalculadosAnalitica%ROWTYPE;
BEGIN
    CREATE TEMPORARY TABLE tmp_eventos_calculados
        (indice         integer,
         codigoE        character(5),
         descricaoE     character(80),
         quantidadeE    numeric(15,2),
         valorE         numeric(15,2),
         desdobramentoE character(1),
         codigoD        character(5),
         descricaoD     character(80),
         quantidadeD    numeric(15,2),
         valorD         numeric(15,2),
         desdobramentoD character(1)
        );

    stNaturezaETemp := ' '|| quote_literal(stNaturezaE) ||' ';
    stSql := montaeventosCalculadosFolhaAnaliticaResumida(inCodConfiguracao,inCodContrato,inCodPeriodoMovimentacao,inCodComplementar,stOrdenacao,stNaturezaETemp,stEntidade); 
    inIndice := 1;
    FOR reRegistro IN  EXECUTE stSql
    LOOP
        stInsert := 'INSERT INTO tmp_eventos_calculados (indice,codigoE,descricaoE,quantidadeE,valorE,desdobramentoE) 
                                                 VALUES ('|| inIndice ||','|| quote_literal(reRegistro.codigo) ||','|| quote_literal(reRegistro.descricao) ||','|| reRegistro.quantidade ||','|| reRegistro.valor ||','|| quote_literal(reRegistro.desdobramento) ||')';
        EXECUTE stInsert;
        inIndice := inIndice + 1;
    END LOOP;

    stNaturezaDTemp := ' '|| quote_literal(stNaturezaD) ||' ';
    stSql := montaeventosCalculadosFolhaAnaliticaResumida(inCodConfiguracao,inCodContrato,inCodPeriodoMovimentacao,inCodComplementar,stOrdenacao,stNaturezaDTemp,stEntidade); 
    inIndice := 1;
    FOR reRegistro IN  EXECUTE stSql
    LOOP
        SELECT INTO inVerifica (SELECT 1 FROM tmp_eventos_calculados WHERE indice = inIndice);
        IF inVerifica = 1 THEN
            stUpdate := 'UPDATE tmp_eventos_calculados SET codigoD = '|| quote_literal(reRegistro.codigo) ||'\',
                                                           descricaoD = '|| quote_literal(reRegistro.descricao) ||',
                                                           quantidadeD = '|| reRegistro.quantidade ||',
                                                           valorD = '|| reRegistro.valor ||',
                                                           desdobramentoD = '|| quote_literal(reRegistro.desdobramento) ||'
                                                WHERE indice = '|| inIndice;
            EXECUTE stUpDATE;
        ELSE        
            stInsert := 'INSERT INTO tmp_eventos_calculados (indice,codigoD,descricaoD,quantidadeD,valorD,desdobramentoD) 
                                                     VALUES ('|| inIndice ||','|| quote_literal(reRegistro.codigo) ||','|| quote_literal(reRegistro.descricao) ||','|| reRegistro.quantidade ||','|| reRegistro.valor ||','|| quote_literal(reRegistro.desdobramento) ||')';
            EXECUTE stInsert;
        END IF;
        inIndice := inIndice + 1;
    END LOOP;

    stSql := 'SELECT * FROM tmp_eventos_calculados ORDER BY indice';
    FOR reRegistro IN  EXECUTE stSql
    LOOP
        rwEventosCalculadosAnalitica.codigoE                 := reRegistro.codigoE;
        rwEventosCalculadosAnalitica.descricaoE              := reRegistro.descricaoE;
        rwEventosCalculadosAnalitica.quantidadeE             := reRegistro.quantidadeE;
        rwEventosCalculadosAnalitica.valorE                  := reRegistro.valorE;
        rwEventosCalculadosAnalitica.desdobramentoE          := reRegistro.desdobramentoE;

        rwEventosCalculadosAnalitica.codigoD                 := reRegistro.codigoD;
        rwEventosCalculadosAnalitica.descricaoD              := reRegistro.descricaoD;
        rwEventosCalculadosAnalitica.quantidadeD             := reRegistro.quantidadeD;
        rwEventosCalculadosAnalitica.valorD                  := reRegistro.valorD;
        rwEventosCalculadosAnalitica.desdobramentoD          := reRegistro.desdobramentoD;
        RETURN NEXT rwEventosCalculadosAnalitica;
    END LOOP;

    DROP TABLE tmp_eventos_calculados;
END;
$$LANGUAGE 'plpgsql';
*/


CREATE OR REPLACE FUNCTION eventosCalculadosComplementarFolhaAnalitica(INTEGER,INTEGER,INTEGER,VARCHAR) RETURNS SETOF colunasEventosCalculadosComplementaresAnalitica AS $$
DECLARE
    inCodContrato                           ALIAS FOR $1;
    inCodPeriodoMovimentacao                ALIAS FOR $2;
    inCodComplementar                       ALIAS FOR $3;
    stEntidade                           ALIAS FOR $4;
    stSql                                   VARCHAR := '';
    reRegistro                              RECORD;
    rwEventosCalculadosComplementaresAnalitica            colunasEventosCalculadosComplementaresAnalitica%ROWTYPE;
BEGIN
    stSql := montaeventosCalculadosFolhaAnaliticaResumida(0,inCodContrato,inCodPeriodoMovimentacao,inCodComplementar,'codigo',quote_literal('B,D'),stEntidade); 
    FOR reRegistro IN  EXECUTE stSql
    LOOP
        
    END LOOP;
    rwEventosCalculadosComplementaresAnalitica.base_previdencia         := 1.00;
    rwEventosCalculadosComplementaresAnalitica.desc_inss                := 1.00;
    rwEventosCalculadosComplementaresAnalitica.base_irrf                := 1.00;
    rwEventosCalculadosComplementaresAnalitica.desc_irrf                := 1.00;
    rwEventosCalculadosComplementaresAnalitica.base_fgts                := 1.00;
    rwEventosCalculadosComplementaresAnalitica.valor_recolhido_fgts     := 1.00;
    rwEventosCalculadosComplementaresAnalitica.valor_contribuicao       := 1.00;
    RETURN NEXT rwEventosCalculadosComplementaresAnalitica;    
END;
$$LANGUAGE 'plpgsql';


--SELECT * FROM eventosCalculadosFolhaAnalitica(0,12,29,'codigo','P','D') as retorno;

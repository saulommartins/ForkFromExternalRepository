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
* Script de DDL e DML
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: GRH_1974.sql 40818 2009-06-29 08:55:18Z fabio $
*
* Versão 1.97.4
*/

----------------
-- Ticket #15275
----------------

CREATE TYPE colunasUltimoContratoPensionistaContaSalario AS (
    cod_contrato    INTEGER,
    cod_agencia     INTEGER,
    cod_banco       INTEGER,
    nr_conta        VARCHAR
);

CREATE TYPE colunasUltimoContratoPensionistaOrgao AS (
    cod_contrato    INTEGER,
    cod_orgao       INTEGER
);

CREATE TYPE colunasUltimoContratoServidorContaSalario AS (
    cod_contrato    INTEGER,
    cod_agencia     INTEGER,
    cod_banco       INTEGER,
    nr_conta        VARCHAR
);

CREATE TYPE colunasUltimoContratoServidorLocal AS (
    cod_contrato    INTEGER,
    cod_local       INTEGER
);

CREATE TYPE colunasUltimoContratoServidorOrgao AS (
    cod_contrato    INTEGER,
    cod_orgao       INTEGER
);

CREATE TYPE colunasUltimoContratoServidorNomeacaoPosse AS (
    cod_contrato        INTEGER,
    dt_nomeacao         DATE,
    dt_posse            DATE,
    dt_admissao         DATE,
    dt_inicio_contagem  DATE
);

CREATE TYPE colunasUltimoContratoServidorFuncao AS (
    cod_contrato    INTEGER,
    cod_cargo       INTEGER
);

CREATE TYPE colunasUltimoContratoServidorPadrao AS (
    cod_contrato    INTEGER,
    cod_padrao      INTEGER
);

CREATE TYPE colunasUltimoContratoServidorEspecialidadeFuncao AS (
    cod_contrato                INTEGER,
    cod_especialidade_funcao    INTEGER
);

CREATE TYPE colunasUltimoContratoServidorSalario AS (
    cod_contrato        INTEGER,
    salario             NUMERIC,
    horas_mensais       NUMERIC,
    horas_semanais      NUMERIC
);

CREATE TYPE colunasUltimoContratoServidorSubDivisaoFuncao AS (
    cod_contrato            INTEGER,
    cod_sub_divisao_funcao  INTEGER
);

CREATE TYPE colunasUltimoAtributoContratoServidorValor AS (
    cod_contrato    INTEGER,
    cod_atributo    INTEGER,
    cod_modulo      INTEGER,
    valor           VARCHAR
);

CREATE TYPE colunasUltimoAtributoContratoPensionista AS (
    cod_contrato    INTEGER,
    cod_atributo    INTEGER,
    cod_modulo      INTEGER,
    cod_cadastro    INTEGER,
    valor           VARCHAR
);

CREATE TYPE colunasUltimoContratoServidorRegimeFuncao AS (
    cod_contrato        INTEGER,
    cod_regime_funcao   INTEGER
);

----------------
-- Ticket #15572
----------------

INSERT INTO administracao.relatorio (cod_gestao, cod_modulo, cod_relatorio, nom_relatorio, arquivo) VALUES (4, 27, 24, 'Ficha Financeira', 'relatorioFichaFinanceira.rptdesign');

CREATE TYPE colunasContratosRelatorioFichaFinanceira AS (
    cod_contrato        INTEGER,
    registro            INTEGER,
    cod_servidor        INTEGER,
    numcgm              INTEGER, 
    nom_cgm             VARCHAR, 
    dt_posse            VARCHAR,
    dt_nomeacao         VARCHAR,
    dt_admissao         VARCHAR,
    cod_orgao           INTEGER,
    desc_orgao          VARCHAR,
    cod_local           INTEGER,
    desc_local          VARCHAR,
    desc_funcao         VARCHAR
);


CREATE TYPE colunasTotaisValoresRelatorioFichaFinanceira AS (
    codigo_evento     VARCHAR,
    descricao_evento  VARCHAR,
    natureza_evento   VARCHAR,
    quantidade        NUMERIC(14,2),
    desdobramento     VARCHAR,
    proventos         NUMERIC(14,2),
    descontos         NUMERIC(14,2),
    valor             NUMERIC(14,2)
);


CREATE TYPE colunasOcorrenciasCalculoRelatorioFichaFinanceira AS (
    cod_periodo_movimentacao    INTEGER,
    cod_configuracao            INTEGER,
    cod_complementar            INTEGER,
    descricao_periodo           VARCHAR,
    descricao_configuracao      VARCHAR
);


CREATE TYPE colunasEventosCalculadosIntervalo AS (
    cod_periodo_movimentacao    INTEGER,  
    cod_contrato                INTEGER,  
    cod_evento                  INTEGER,  
    codigo                      CHARACTER(5) ,
    descricao                   CHARACTER(80),
    natureza                    CHARACTER(1) ,
    tipo                        CHARACTER(1) ,
    fixado                      CHARACTER(1) ,
    limite_calculo              BOOLEAN      ,
    apresenta_parcela           BOOLEAN      ,
    evento_sistema              BOOLEAN      ,
    sigla                       CHARACTER VARYING(5),
    valor                       NUMERIC(15,2),        
    quantidade                  NUMERIC(15,2),
    desdobramento               CHARACTER(1),
    desdobramento_texto         VARCHAR,
    sequencia                   INTEGER,
    desc_sequencia              CHARACTER VARYING(80)
);


----------------
-- Ticket #15175
----------------

CREATE TYPE colunasRecuperaDespesaPorPAORubricaDespesa AS (
    cod_despesa             INTEGER,
    cod_estrutural          VARCHAR,
    cod_conta               INTEGER,
    descricao_conta         VARCHAR,
    cod_recurso             INTEGER,
    cod_fonte               VARCHAR,
    descricao_recurso       VARCHAR
);

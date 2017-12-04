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
* $Id: GRH_1972.sql 59612 2014-09-02 12:00:51Z gelson $
*
* Versão 1.97.2
*/

SELECT atualizarbanco('GRANT ALL ON pessoal.contrato_servidor_conta_salario_historico TO GROUP urbem;');

----------------
-- Ticket #14955
----------------
select atualizarBanco ('ALTER TABLE ima.configuracao_convenio_banrisul  DROP CONSTRAINT pk_configuracao_convenio_banrisul_1;');
select atualizarBanco ('ALTER TABLE ima.configuracao_convenio_banrisul  DROP CONSTRAINT fk_configuracao_convenio_banrisul_1;');
select atualizarBanco ('ALTER TABLE ima.configuracao_convenio_banrisul  RENAME TO configuracao_convenio_banrisul_bkp;');

SELECT atualizarbanco('
CREATE TABLE ima.configuracao_convenio_banrisul (
    cod_convenio            INTEGER                 NOT NULL,
    cod_convenio_banco      VARCHAR(20)             NOT NULL,
    cod_banco               INTEGER                 NOT NULL,
    CONSTRAINT pk_configuracao_convenio_banrisul    PRIMARY KEY                (cod_convenio, cod_banco),
    CONSTRAINT fk_configuracao_convenio_banrisul_1  FOREIGN KEY                (cod_banco)
                                                    REFERENCES monetario.banco (cod_banco)
);
');

SELECT atualizarbanco('
GRANT ALL ON ima.configuracao_convenio_banrisul TO GROUP urbem;
');

SELECT atualizarbanco('
CREATE TABLE ima.configuracao_banrisul_conta (
    cod_convenio            INTEGER                 NOT NULL,
    cod_banco               INTEGER                 NOT NULL,
    cod_agencia             INTEGER                 NOT NULL,
    cod_conta_corrente      INTEGER                 NOT NULL,
    descricao               VARCHAR(60)             NOT NULL,
    CONSTRAINT pk_configuracao_banrisul_conta       PRIMARY KEY                               (cod_convenio, cod_banco, cod_agencia, cod_conta_corrente),
    CONSTRAINT fk_configuracao_banrisul_conta_1     FOREIGN KEY                               (cod_convenio, cod_banco)
                                                    REFERENCES ima.configuracao_convenio_banrisul (cod_convenio, cod_banco),
    CONSTRAINT fk_configuracao_banrisul_conta_2     FOREIGN KEY                               (cod_banco, cod_agencia, cod_conta_corrente)
                                                    REFERENCES monetario.conta_corrente       (cod_banco, cod_agencia, cod_conta_corrente)
);
');

SELECT atualizarbanco('
GRANT ALL ON ima.configuracao_banrisul_conta TO GROUP urbem;
');

SELECT atualizarbanco('
CREATE TABLE ima.configuracao_banrisul_orgao (
    cod_convenio            INTEGER                 NOT NULL,
    cod_banco               INTEGER                 NOT NULL,
    cod_agencia             INTEGER                 NOT NULL,
    cod_conta_corrente      INTEGER                 NOT NULL,
    cod_orgao               INTEGER                 NOT NULL,
    CONSTRAINT pk_configuracao_banrisul_orgao       PRIMARY KEY                             (cod_convenio, cod_banco, cod_agencia, cod_conta_corrente, cod_orgao),
    CONSTRAINT fk_configuracao_banrisul_orgao_1     FOREIGN KEY                             (cod_convenio, cod_banco, cod_agencia, cod_conta_corrente)
                                                    REFERENCES  ima.configuracao_banrisul_conta (cod_convenio, cod_banco, cod_agencia, cod_conta_corrente),
    CONSTRAINT fk_configuracao_banrisul_orgao_2     FOREIGN KEY                             (cod_orgao)
                                                    REFERENCES organograma.orgao            (cod_orgao)
 );
');

SELECT atualizarbanco('
GRANT ALL ON ima.configuracao_banrisul_orgao TO GROUP urbem;
');

SELECT atualizarbanco('
CREATE TABLE ima.configuracao_banrisul_local (
    cod_convenio            INTEGER                 NOT NULL,
    cod_banco               INTEGER                 NOT NULL,
    cod_agencia             INTEGER                 NOT NULL,
    cod_conta_corrente      INTEGER                 NOT NULL,
    cod_local               INTEGER                 NOT NULL,
    CONSTRAINT pk_configuracao_banrisul_local       PRIMARY KEY                             (cod_convenio, cod_banco, cod_agencia, cod_conta_corrente, cod_local),
    CONSTRAINT fk_configuracao_banrisul_local_1     FOREIGN KEY                             (cod_convenio, cod_banco, cod_agencia, cod_conta_corrente)
                                                    REFERENCES  ima.configuracao_banrisul_conta (cod_convenio, cod_banco, cod_agencia, cod_conta_corrente),
    CONSTRAINT fk_configuracao_banrisul_local_2     FOREIGN KEY                             (cod_local)
                                                    REFERENCES organograma.local            (cod_local)
 );
');

SELECT atualizarbanco('
GRANT ALL ON ima.configuracao_banrisul_local TO GROUP urbem;
');

SELECT atualizarbanco('
INSERT INTO ima.configuracao_convenio_banrisul
SELECT cod_convenio
     , cod_convenio_banco
     , cod_banco
  FROM ima.configuracao_convenio_banrisul_bkp;
');

SELECT atualizarbanco('
INSERT INTO ima.configuracao_banrisul_conta
SELECT cod_convenio
     , cod_banco
     , cod_agencia
     , cod_conta_corrente
     , cod_convenio_banco
  FROM ima.configuracao_convenio_banrisul_bkp;
');

SELECT atualizarbanco('
INSERT INTO ima.configuracao_banrisul_orgao
SELECT * 
FROM (
SELECT (SELECT cod_convenio FROM ima.configuracao_convenio_banrisul_bkp) as cod_convenio
     , (SELECT cod_banco FROM ima.configuracao_convenio_banrisul_bkp) as cod_banco
     , (SELECT cod_agencia FROM ima.configuracao_convenio_banrisul_bkp) as cod_agencia
     , (SELECT cod_conta_corrente FROM ima.configuracao_convenio_banrisul_bkp) as cod_conta_corrente
     , orgao.cod_orgao
  FROM organograma.orgao                                                                
     , organograma.organograma                                                          
     , organograma.orgao_nivel                                                          
     , organograma.nivel                                                                
     , organograma.vw_orgao_nivel as ovw                                                
     , (SELECT MAX(cod_organograma) as cod_organograma                                  
             , MAX(implantacao) AS data                                                 
          FROM organograma.organograma oo                                               
         WHERE implantacao <= to_char(now(), \'yyyy-mm-dd\')) as ultima_data       
WHERE organograma.cod_organograma = nivel.cod_organograma                               
  AND nivel.cod_organograma       = orgao_nivel.cod_organograma                         
  AND nivel.cod_nivel             = orgao_nivel.cod_nivel                               
  AND orgao_nivel.cod_orgao       = orgao.cod_orgao                                     
  AND orgao.cod_orgao             = ovw.cod_orgao                                       
  AND orgao_nivel.cod_organograma = ovw.cod_organograma                                 
  AND orgao_nivel.cod_organograma = ultima_data.cod_organograma                         
  AND nivel.cod_nivel             = ovw.nivel                                            
  AND organograma.ativo           = true
GROUP BY orgao.cod_orgao
) as tmp
WHERE cod_convenio is not null
');

SELECT atualizarbanco('DROP TABLE ima.configuracao_convenio_banrisul_bkp;');


CREATE TYPE colunasContratoServidor AS (
    cod_contrato                INTEGER,       
    cod_norma                   INTEGER,       
    cod_forma_pagamento         INTEGER,      
    cod_tipo_pagamento          INTEGER,       
    cod_tipo_salario            INTEGER,       
    cod_tipo_admissao           INTEGER,       
    cod_categoria               INTEGER,       
    cod_vinculo                 INTEGER,       
    cod_cargo                   INTEGER,       
    desc_cargo                  VARCHAR,
    cod_regime                  INTEGER,       
    desc_regime                 VARCHAR,       
    cod_sub_divisao             INTEGER,       
    desc_sub_divisao            VARCHAR,       
    nr_cartao_ponto             CHARACTER(10), 
    ativo                       BOOLEAN,       
    dt_opcao_fgts               DATE,          
    adiantamento                BOOLEAN,       
    cod_grade                   INTEGER,

    registro                    INTEGER,

    cod_servidor                INTEGER,               
    cod_uf                      INTEGER,               
    cod_municipio               INTEGER,               
    numcgm                      INTEGER,               
    nome_pai                    CHARACTER VARYING(80), 
    nome_mae                    CHARACTER VARYING(80), 
    zona_titulo                 CHARACTER(5),          
    secao_titulo                CHARACTER(5),          
    caminho_foto                CHARACTER VARYING(80), 
    nr_titulo_eleitor           CHARACTER VARYING(12), 
    cod_estado_civil            INTEGER,               
    cod_raca                    INTEGER,

    cod_orgao                   INTEGER,
    desc_orgao                  VARCHAR,
    orgao                       VARCHAR,
    cod_local                   INTEGER,
    desc_local                  VARCHAR,
    cod_regime_funcao           INTEGER,
    desc_regime_funcao          VARCHAR,
    cod_sub_divisao_funcao      INTEGER,
    desc_sub_divisao_funcao     VARCHAR,
    cod_funcao                  INTEGER,
    desc_funcao                 VARCHAR,
    cod_cbo_funcao              INTEGER,
    desc_cbo_funcao             VARCHAR,
    cod_especialidade_funcao    INTEGER,
    desc_especialidade_funcao   VARCHAR,
    cod_especialidade_cargo     INTEGER,
    desc_especialidade_cargo    VARCHAR,
    cod_tipo                    INTEGER,
    desc_tipo_cedencia          VARCHAR,
    cod_conselho                INTEGER,
    sigla_conselho              CHARACTER(10),
    desc_conselho               CHARACTER VARYING(80),
    cod_agencia_fgts            INTEGER,
    cod_banco_fgts              INTEGER,
    nr_conta_fgts               CHARACTER(15),
    cod_agencia_salario         INTEGER,
    cod_banco_salario           INTEGER,
    nr_conta_salario            CHARACTER(15),
    num_banco_salario           CHARACTER VARYING(10),
    nom_banco_salario           CHARACTER VARYING(80),
    num_agencia_salario         CHARACTER VARYING(10),
    nom_agencia_salario         CHARACTER VARYING(80),

    dt_validade_exame           DATE,
    dt_inicio_progressao        DATE,
    cod_nivel_padrao            INTEGER,
    dt_nomeacao                 DATE,
    dt_posse                    DATE,
    dt_admissao                 DATE,
    cod_ocorrencia              INTEGER,
    cod_padrao                  INTEGER,
    valor_padrao                NUMERIC(14,2),
    desc_padrao                 CHARACTER VARYING(80),
    cod_previdencia             INTEGER,
    salario                     NUMERIC(14,2),
    horas_mensais               NUMERIC(14,2),
    horas_semanais              NUMERIC(14,2),
    vigencia                    DATE,
    numcgm_sindicato            INTEGER,
    cod_cid                     INTEGER,
    numcgm_conjuge              INTEGER,
    nr_carteira_res             CHARACTER(15),
    cat_reservista              CHARACTER(1),
    origem_reservista           CHARACTER(1),

    nom_cgm                     CHARACTER VARYING(200),
    servidor_pis_pasep          CHARACTER(15),
    rg                          CHARACTER VARYING(15),
    cpf                         CHARACTER VARYING(20),
    dt_nascimento               DATE,

    valor_atributo              VARCHAR,

    data_laudo                  DATE
);      

CREATE TYPE colunasContratoPensionista AS (
    cod_contrato                INTEGER,                
    registro                    INTEGER,                
    cod_contrato_cedente        INTEGER,                
    cod_dependencia             INTEGER,                
    cod_pensionista             INTEGER,                
    num_beneficio               CHARACTER VARYING(15),  
    percentual_pagamento        NUMERIC(5,2),           
    dt_inicio_beneficio         DATE,                   
    dt_encerramento             DATE,                   
    motivo_encerramento         CHARACTER VARYING(200),
    cod_profissao               INTEGER,
    numcgm                      INTEGER,
    cod_grau                    INTEGER,

    cod_orgao                   INTEGER,
    desc_orgao                  VARCHAR,
    orgao                       VARCHAR,
    cod_local                   INTEGER,
    desc_local                  VARCHAR,

    cod_agencia_salario         INTEGER,
    cod_banco_salario           INTEGER,
    nr_conta_salario            CHARACTER(15),
    num_banco_salario           CHARACTER VARYING(10),
    nom_banco_salario           CHARACTER VARYING(80),
    num_agencia_salario         CHARACTER VARYING(10),
    nom_agencia_salario         CHARACTER VARYING(80),

    cod_previdencia             INTEGER,
    cod_processo                INTEGER,
    cod_cid                     INTEGER,

    cod_regime_funcao           INTEGER,
    desc_regime_funcao          VARCHAR,
    cod_sub_divisao_funcao      INTEGER,
    desc_sub_divisao_funcao     VARCHAR,
    cod_funcao                  INTEGER,
    desc_funcao                 VARCHAR,
    desc_cbo_funcao             VARCHAR,
    cod_especialidade_funcao    INTEGER,
    desc_especialidade_funcao   VARCHAR,

    nom_cgm                     CHARACTER VARYING(200),
    rg                          CHARACTER VARYING(15),
    cpf                         CHARACTER VARYING(20),
    dt_nascimento               DATE,
    
    valor_atributo              VARCHAR,

    data_laudo                  DATE
);

----------------
-- Ticket #15016
----------------
CREATE TYPE colunasConcederFerias   AS (
    cod_ferias              INTEGER,
    numcgm                  INTEGER,
    nom_cgm                 VARCHAR,
    registro                INTEGER,
    cod_contrato            INTEGER,
    desc_local              VARCHAR,
    desc_orgao              VARCHAR,
    orgao                   VARCHAR,
    dt_posse                DATE,
    dt_admissao             DATE,
    dt_nomeacao             DATE,
    desc_funcao             VARCHAR,
    desc_regime_funcao      VARCHAR,
    cod_regime_funcao       INTEGER,
    cod_funcao              INTEGER,
    cod_local               INTEGER,
    cod_orgao               INTEGER,
    bo_cadastradas          BOOLEAN,
    situacao                VARCHAR,
    dt_inicial_aquisitivo   DATE,
    dt_final_aquisitivo     DATE,
    dt_inicio               DATE,
    dt_fim                  DATE,
    mes_competencia         CHARACTER(2),
    ano_competencia         CHARACTER(4) 
);


----------------
-- Ticket #15042
----------------

SELECT atualizarbanco('
CREATE TABLE folhapagamento.reajuste_percentual(
    cod_reajuste    INTEGER             NOT NULL,
    valor           NUMERIC(14,4)       NOT NULL,
    CONSTRAINT pk_reajuste_percentual   PRIMARY KEY                         (cod_reajuste),
    CONSTRAINT fk_reajuste_percentual_1 FOREIGN KEY                         (cod_reajuste)
                                        REFERENCES folhapagamento.reajuste  (cod_reajuste)
 );
');

SELECT atualizarbanco('GRANT ALL ON folhapagamento.reajuste_percentual TO GROUP urbem;');

SELECT atualizarbanco('
CREATE TABLE folhapagamento.reajuste_absoluto(
    cod_reajuste    INTEGER             NOT NULL,
    valor           NUMERIC(14,2)       NOT NULL,
    CONSTRAINT pk_reajuste_absoluto     PRIMARY KEY                         (cod_reajuste),
    CONSTRAINT fk_reajuste_absoluto_1   FOREIGN KEY                         (cod_reajuste)
                                        REFERENCES folhapagamento.reajuste  (cod_reajuste)
 );
');

SELECT atualizarbanco('GRANT ALL ON folhapagamento.reajuste_absoluto TO GROUP urbem;');

SELECT atualizarbanco('
INSERT
  INTO folhapagamento.reajuste_percentual
SELECT cod_reajuste
     , percentual
  FROM folhapagamento.reajuste;
');

SELECT atualizarbanco('ALTER TABLE folhapagamento.reajuste DROP COLUMN percentual;');


----------------
-- Ticket #15052
----------------

SELECT atualizarbanco('ALTER TABLE diarias.diaria ADD COLUMN hr_inicio  TIME;');
SELECT atualizarbanco('ALTER TABLE diarias.diaria ADD COLUMN hr_termino TIME;');

SELECT atualizarbanco('UPDATE diarias.diaria SET hr_inicio = \'00:00\', hr_termino = \'23:59\';');

SELECT atualizarbanco('ALTER TABLE diarias.diaria ALTER COLUMN hr_inicio  SET NOT NULL;');
SELECT atualizarbanco('ALTER TABLE diarias.diaria ALTER COLUMN hr_termino SET NOT NULL;');

----------------
-- Ticket #15096
----------------
DROP TYPE linhaComprovanteRendimentosIRRF CASCADE;
CREATE TYPE linhaComprovanteRendimentosIRRF AS (
    numcgm                                  INTEGER,
    nom_cgm                                 VARCHAR,
    cpf                                     VARCHAR,
    cod_cid                                 VARCHAR,
    total_rendimentos                       NUMERIC(8,2),
    contribuicao_previdenciaria_oficial     NUMERIC(8,2),
    contribuicao_previdenciaria_privada     NUMERIC(8,2),
    pensao_alimenticia                      NUMERIC(8,2),
    imposto_renda_retido                    NUMERIC(8,2),
    diarias_ajuda_custo                     NUMERIC(8,2),
    informativo_aposentadoria               NUMERIC(8,2),
    pensao_proventos_molestia_acidente      NUMERIC(8,2),
    decimo_terceiro                         NUMERIC(8,2),    
    agrupamento                             VARCHAR
);

----------------
-- Ticket #15097
----------------
CREATE TYPE colunasCustomizavelEventos AS (
cpf                         CHARACTER VARYING(20),
cod_contrato                INTEGER,
registro                    INTEGER,
nom_cgm                     VARCHAR,
desc_orgao                  VARCHAR,
desc_local                  VARCHAR,
desc_funcao                 VARCHAR,
desc_cargo                  VARCHAR,
desc_especialidade_cargo    VARCHAR,
desc_especialidade_funcao   VARCHAR,
desc_padrao                 VARCHAR,
valor1                      NUMERIC(14,2),
quantidade1                 NUMERIC(14,2),
valor2                      NUMERIC(14,2),
quantidade2                 NUMERIC(14,2),
valor3                      NUMERIC(14,2),
quantidade3                 NUMERIC(14,2),
valor4                      NUMERIC(14,2),
quantidade4                 NUMERIC(14,2),
valor5                      NUMERIC(14,2),
quantidade5                 NUMERIC(14,2),
valor6                      NUMERIC(14,2),
quantidade6                 NUMERIC(14,2),
quantidade1_parc            INTEGER,
quantidade2_parc            INTEGER,
quantidade3_parc            INTEGER,
quantidade4_parc            INTEGER,
quantidade5_parc            INTEGER,
quantidade6_parc            INTEGER
);


----------------
-- Ticket #15102
----------------

CREATE TYPE linhaRelatorioCargos AS (
    agrupamento         VARCHAR,
    count_servidores    INTEGER,
    cod_local           INTEGER,
    cod_orgao           INTEGER,
    cod_sub_divisao     INTEGER,
    cod_cargo           INTEGER,
    cod_especialidade   INTEGER,
    descricao_cargo     VARCHAR,
    codigo_cbo          VARCHAR,
    cargo_cc            VARCHAR,
    funcao_gratificada  VARCHAR,
    cod_padrao          INTEGER,
    descricao_padrao    VARCHAR,
    horas_mensais       NUMERIC(5,2),
    horas_semanais      NUMERIC(5,2),
    valor               VARCHAR,
    vigencia            VARCHAR
);

CREATE TYPE linhaRelatorioCargosServidores AS (
    matricula           VARCHAR,
    nome                VARCHAR,
    dt_admissao         VARCHAR,
    regime_sub_divisao  VARCHAR,
    horas_mensais       VARCHAR,
    horas_semanais      VARCHAR
);


----------------
-- Ticket #15003
----------------

UPDATE administracao.acao
   SET nom_acao = 'Incluir Configuração de Assentamento'
 WHERE cod_acao = 835;

UPDATE administracao.acao
   SET nom_acao = 'Alterar Configuração de Assentamento'
 WHERE cod_acao = 836;

UPDATE administracao.acao
   SET nom_acao = 'Excluir Configuração de Assentamento'
 WHERE cod_acao = 837;

----------------
-- Ticket #15192
----------------
CREATE TYPE colunasEventosCalculados AS (
    cod_contrato        INTEGER,  
    cod_evento          INTEGER,  
    codigo              CHARACTER(5) ,
    descricao           CHARACTER(80),
    natureza            CHARACTER(1) ,
    tipo                CHARACTER(1) ,
    fixado              CHARACTER(1) ,
    limite_calculo      BOOLEAN      ,
    apresenta_parcela   BOOLEAN      ,
    evento_sistema      BOOLEAN      ,
    sigla               CHARACTER VARYING(5),
    valor               NUMERIC(15,2),        
    quantidade          NUMERIC(15,2),
    desdobramento       CHARACTER(1),
    desdobramento_texto VARCHAR,
    sequencia           INTEGER,
    desc_sequencia      CHARACTER VARYING(80),
    quantidade_parc     INTEGER
);


-----------------------------------------------------------------
-- CORRIGINDO FK P/ TABELA organograma.orgao EM ima.banpara_orgao
-----------------------------------------------------------------

SELECT atualizarbanco('ALTER TABLE ima.banpara_orgao ADD CONSTRAINT fk_banpara_orgao_1 FOREIGN KEY (cod_orgao) REFERENCES organograma.orgao (cod_orgao);');

----------------
-- Ticket #15098
----------------

CREATE TYPE colunasTotaisFolha AS (
    codigo              VARCHAR,
    descricao           VARCHAR,
    provento            NUMERIC(14,2),
    desconto            NUMERIC(14,2),
    agrupamento_banco   VARCHAR,
    agrupamento         VARCHAR
);

----------------
-- Ticket #14950
----------------

SELECT atualizarbanco ('
CREATE TABLE pessoal.contrato_servidor_forma_pagamento (
    cod_contrato        INTEGER         NOT NULL,
    timestamp           TIMESTAMP       NOT NULL DEFAULT (\'now\'::text)::timestamp(3) with time zone,
    cod_forma_pagamento INTEGER         NOT NULL,
    CONSTRAINT pk_contrato_servidor_forma_pagamento     PRIMARY KEY                             (cod_contrato, timestamp),
    CONSTRAINT fk_contrato_servidor_forma_pagamento_1   FOREIGN KEY                             (cod_contrato)
                                                        REFERENCES pessoal.contrato_servidor    (cod_contrato),
    CONSTRAINT fk_contrato_servidor_forma_pagamento_2   FOREIGN KEY                             (cod_forma_pagamento)
                                                        REFERENCES pessoal.forma_pagamento      (cod_forma_pagamento)
);
');

SELECT atualizarbanco ('GRANT ALL ON pessoal.contrato_servidor_forma_pagamento TO GROUP urbem;');

SELECT atualizarbanco ('
INSERT
  INTO pessoal.contrato_servidor_forma_pagamento
     ( cod_contrato
     , cod_forma_pagamento
     , timestamp
     )
SELECT cod_contrato
     , cod_forma_pagamento
     , \'1900-01-01 00:00:00.000\' as timestamp
  FROM pessoal.contrato_servidor
     ;
');

SELECT atualizarbanco ('ALTER TABLE pessoal.contrato_servidor DROP CONSTRAINT fk_contrato_servidor_8;');
SELECT atualizarbanco ('ALTER TABLE pessoal.contrato_servidor DROP COLUMN     cod_forma_pagamento;');


------------------------------------------------------------
-- EXCLUSAO DO RELATORIO customizavelEventosGrupos.rptdesign
------------------------------------------------------------

DELETE
  FROM administracao.relatorio
 WHERE cod_gestao = 4
   AND cod_modulo = 27
   AND cod_relatorio = 22;


----------------
-- Ticket #15018
----------------

SELECT atualizarbanco ('ALTER TABLE pessoal.lote_ferias ADD   COLUMN mes_competencia CHAR(2);');
SELECT atualizarbanco ('ALTER TABLE pessoal.lote_ferias ADD   COLUMN ano_competencia CHAR(4);');

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

    stData          VARCHAR[];
    stNome          VARCHAR[];
    stMes           VARCHAR;
    stAno           VARCHAR;

    stSql           VARCHAR;
    reRecord        RECORD;
    crCursor        REFCURSOR;

    stSqlUpdate     VARCHAR;
    stSqlEnt        VARCHAR;
    reRecordEnt     RECORD;
    crCursorEnt     REFCURSOR;
    
    intCount        INTEGER;

BEGIN

    stSql := '  SELECT *
                  FROM pessoal.lote_ferias;
             ';
    OPEN crCursor FOR EXECUTE stSql;
    LOOP
        FETCH crCursor INTO reRecord;
        EXIT WHEN NOT FOUND;

            stNome := string_to_array(reRecord.nome,' ');
            
            intCount := 1;
            WHILE stNome[intCount] IS NOT NULL LOOP
                intCount := intCount+1;
            END LOOP;
            intCount := intCount-1;
            
            stData := string_to_array(stNome[intCount],'/');

            stMes  := stData[1];
            stAno  := stData[2];

            IF     stMes = 'Janeiro'     THEN stMes := '01'; 
            ELSEIF stMes = 'Fevereiro'   THEN stMes := '02';
            ELSEIF stMes = 'Março'       THEN stMes := '03';
            ELSEIF stMes = 'Abril'       THEN stMes := '04';
            ELSEIF stMes = 'Maio'        THEN stMes := '05';
            ELSEIF stMes = 'Junho'       THEN stMes := '06';
            ELSEIF stMes = 'Julho'       THEN stMes := '07';
            ELSEIF stMes = 'Agosto'      THEN stMes := '08';
            ELSEIF stMes = 'Setembro'    THEN stMes := '09';
            ELSEIF stMes = 'Outubro'     THEN stMes := '10';
            ELSEIF stMes = 'Novembro'    THEN stMes := '11';
            ELSEIF stMes = 'Dezembro'    THEN stMes := '12';
            END IF;

            UPDATE pessoal.lote_ferias
               SET mes_competencia = stMes
                 , ano_competencia = stAno
             WHERE cod_lote = reRecord.cod_lote;

    END LOOP;
    CLOSE crCursor;

    stSqlEnt := '  SELECT DISTINCT cod_entidade
                     FROM administracao.entidade_rh
                    WHERE cod_entidade <> ( SELECT CAST(valor as integer)
                                              FROM administracao.configuracao
                                             WHERE exercicio = \'2009\'
                                               AND parametro = \'cod_entidade_prefeitura\'
                                          );
                ';
    OPEN crCursorEnt FOR EXECUTE stSqlEnt;
    LOOP
        FETCH crCursorEnt INTO reRecordEnt;
        EXIT WHEN NOT FOUND;

            stSql := '  SELECT *
                          FROM pessoal_'|| reRecordEnt.cod_entidade ||'.lote_ferias;
                     ';
            OPEN crCursor FOR EXECUTE stSql;
            LOOP
                FETCH crCursor INTO reRecord;
                EXIT WHEN NOT FOUND;
        
                    stNome := string_to_array(reRecord.nome,' ');
                    
                    intCount := 1;
                    WHILE stNome[intCount] IS NOT NULL LOOP
                        intCount := intCount+1;
                    END LOOP;
                    intCount := intCount-1;
                    
                    stData := string_to_array(stNome[intCount],'/');
        
                    stMes  := stData[1];
                    stAno  := stData[2];
        
                    IF     stMes = 'Janeiro'     THEN stMes := '01';
                    ELSEIF stMes = 'Fevereiro'   THEN stMes := '02';
                    ELSEIF stMes = 'Março'       THEN stMes := '03';
                    ELSEIF stMes = 'Abril'       THEN stMes := '04';
                    ELSEIF stMes = 'Maio'        THEN stMes := '05';
                    ELSEIF stMes = 'Junho'       THEN stMes := '06';
                    ELSEIF stMes = 'Julho'       THEN stMes := '07';
                    ELSEIF stMes = 'Agosto'      THEN stMes := '08';
                    ELSEIF stMes = 'Setembro'    THEN stMes := '09';
                    ELSEIF stMes = 'Outubro'     THEN stMes := '10';
                    ELSEIF stMes = 'Novembro'    THEN stMes := '11';
                    ELSEIF stMes = 'Dezembro'    THEN stMes := '12';
                    END IF;
        
                    stSqlUpdate := '    UPDATE pessoal_'|| reRecordEnt.cod_entidade ||'.lote_ferias
                                           SET mes_competencia = \''|| stMes ||'\'
                                             , ano_competencia = \''|| stAno ||'\'
                                         WHERE cod_lote = reRecord.cod_lote;
                                   ';
                    EXECUTE stSqlUpdate;
            END LOOP;
            CLOSE crCursor;

    END LOOP;
    CLOSE crCursorEnt;

END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();

SELECT atualizarbanco ('ALTER TABLE pessoal.lote_ferias ALTER COLUMN mes_competencia SET NOT NULL;');
SELECT atualizarbanco ('ALTER TABLE pessoal.lote_ferias ALTER COLUMN ano_competencia SET NOT NULL;');



SELECT atualizarbanco ('
CREATE TABLE pessoal.lote_ferias_contrato (
    cod_lote            INTEGER             NOT NULL,
    cod_contrato        INTEGER             NOT NULL,
    CONSTRAINT pk_lote_ferias_contrato      PRIMARY KEY                     (cod_lote, cod_contrato),
    CONSTRAINT fk_lote_ferias_contrato_1    FOREIGN KEY                     (cod_lote)
                                            REFERENCES pessoal.lote_ferias  (cod_lote),
    CONSTRAINT fk_lote_ferias_contrato_2    FOREIGN KEY                     (cod_contrato)
                                            REFERENCES pessoal.contrato     (cod_contrato)
);
');

SELECT atualizarbanco ('GRANT ALL ON pessoal.lote_ferias_contrato TO GROUP urbem;');

SELECT atualizarbanco ('
    INSERT
      INTO pessoal.lote_ferias_contrato
    SELECT plf.cod_lote
         , pf.cod_contrato
      FROM pessoal.lote_ferias          AS plf
INNER JOIN pessoal.lote_ferias_lote     AS plfl
        ON plfl.cod_lote = plf.cod_lote
INNER JOIN pessoal.ferias               AS pf
        ON pf.cod_ferias = plfl.cod_ferias
     WHERE plf.nome ilike \'%lote geral%\'
        OR plf.nome ilike \'%lote para contratos%\';
');



SELECT atualizarbanco ('
CREATE TABLE pessoal.lote_ferias_orgao (
    cod_lote            INTEGER             NOT NULL,
    cod_orgao           INTEGER             NOT NULL,
    CONSTRAINT pk_lote_ferias_orgao         PRIMARY KEY                     (cod_lote, cod_orgao),
    CONSTRAINT fk_lote_ferias_orgao_1       FOREIGN KEY                     (cod_lote)
                                            REFERENCES pessoal.lote_ferias  (cod_lote),
    CONSTRAINT fk_lote_ferias_orgao_2       FOREIGN KEY                     (cod_orgao)
                                            REFERENCES organograma.orgao    (cod_orgao)
);
');

SELECT atualizarbanco ('GRANT ALL ON pessoal.lote_ferias_orgao TO GROUP urbem;');

CREATE OR REPLACE FUNCTION insere_lote_ferias_orgao () RETURNS VOID AS $$
DECLARE

    stNome          VARCHAR[];
    stRegistros     VARCHAR[];
    inCountReg      INTEGER := 0;
    inCount         INTEGER := 0;
    stTemp          VARCHAR;

    stSql           VARCHAR;
    reRecord        RECORD;
    crCursor        REFCURSOR;

    stSqlInsert     VARCHAR;
    stSqlEnt        VARCHAR;
    reRecordEnt     RECORD;
    crCursorEnt     REFCURSOR;

BEGIN

    stSql := '  SELECT *
                  FROM pessoal.lote_ferias
                 WHERE nome ilike \'%lote para lotação%\';
             ';
    OPEN crCursor FOR EXECUTE stSql;
    LOOP
        FETCH crCursor INTO reRecord;
        EXIT WHEN NOT FOUND;

            stNome := string_to_array(reRecord.nome,' ');
            stTemp := trim(both '()' from stNome[6]);

            stRegistros := string_to_array(stTemp, ',');
            inCountReg  := array_upper(stRegistros, 1);
            FOR inCount IN 1..inCountReg LOOP
                INSERT
                  INTO pessoal.lote_ferias_orgao
                     ( cod_lote
                     , cod_orgao
                     )
                VALUES
                     ( reRecord.cod_lote
                     , CAST (stRegistros[inCount] AS INTEGER)
                     );
            END LOOP;

    END LOOP;
    CLOSE crCursor;

    stSqlEnt := '  SELECT DISTINCT cod_entidade
                     FROM administracao.entidade_rh
                    WHERE cod_entidade <> ( SELECT CAST(valor as integer)
                                              FROM administracao.configuracao
                                             WHERE exercicio = \'2009\'
                                               AND parametro = \'cod_entidade_prefeitura\'
                                          );
                ';
    OPEN crCursorEnt FOR EXECUTE stSqlEnt;
    LOOP
        FETCH crCursorEnt INTO reRecordEnt;
        EXIT WHEN NOT FOUND;

            stSql := '  SELECT *
                          FROM pessoal_'|| reRecordEnt.cod_entidade ||'.lote_ferias
                         WHERE nome ilike \'%lote para lotação%\';
                     ';
            OPEN crCursor FOR EXECUTE stSql;
            LOOP
                FETCH crCursor INTO reRecord;
                EXIT WHEN NOT FOUND;
        
                    stNome := string_to_array(reRecord.nome,' ');
                    stTemp := trim(both '()' from stNome[6]);
        
                    stRegistros := string_to_array(stTemp, ',');
                    inCountReg  := array_upper(stRegistros, 1);
                    FOR inCount IN 1..inCountReg LOOP
                        stSqlInsert := '    INSERT
                                              INTO pessoal_'|| reRecordEnt.cod_entidade ||'.lote_ferias_orgao
                                                 ( cod_lote
                                                 , cod_orgao
                                                 )
                                            VALUES
                                                 ( reRecord.cod_lote
                                                 , CAST (stRegistros[inCount] AS INTEGER)
                                                 );
                                       ';
                        EXECUTE stSqlInsert;
                    END LOOP;
        
            END LOOP;
            CLOSE crCursor;

    END LOOP;
    CLOSE crCursorEnt;

END;
$$ LANGUAGE 'plpgsql';

SELECT        insere_lote_ferias_orgao();
DROP FUNCTION insere_lote_ferias_orgao();



SELECT atualizarbanco ('
CREATE TABLE pessoal.lote_ferias_local (
    cod_lote            INTEGER             NOT NULL,
    cod_local           INTEGER             NOT NULL,
    CONSTRAINT pk_lote_ferias_local         PRIMARY KEY                     (cod_lote, cod_local),
    CONSTRAINT fk_lote_ferias_local_1       FOREIGN KEY                     (cod_lote)
                                            REFERENCES pessoal.lote_ferias  (cod_lote),
    CONSTRAINT fk_lote_ferias_local_2       FOREIGN KEY                     (cod_local)
                                            REFERENCES organograma.local    (cod_local)
);
');

SELECT atualizarbanco ('GRANT ALL ON pessoal.lote_ferias_local TO GROUP urbem;');

CREATE OR REPLACE FUNCTION insere_lote_ferias_local () RETURNS VOID AS $$
DECLARE

    stNome          VARCHAR[];
    stRegistros     VARCHAR[];
    inCountReg      INTEGER := 0;
    inCount         INTEGER := 0;
    stTemp          VARCHAR;

    stSql           VARCHAR;
    reRecord        RECORD;
    crCursor        REFCURSOR;

    stSqlInsert     VARCHAR;
    stSqlEnt        VARCHAR;
    reRecordEnt     RECORD;
    crCursorEnt     REFCURSOR;

BEGIN

    stSql := '  SELECT *
                  FROM pessoal.lote_ferias
                 WHERE nome ilike \'%lote para local%\';
             ';
    OPEN crCursor FOR EXECUTE stSql;
    LOOP
        FETCH crCursor INTO reRecord;
        EXIT WHEN NOT FOUND;

            stNome := string_to_array(reRecord.nome,' ');
            stTemp := trim(both '()' from stNome[6]);

            stRegistros := string_to_array(stTemp, ',');
            inCountReg  := array_upper(stRegistros, 1);
            FOR inCount IN 1..inCountReg LOOP
                INSERT
                  INTO pessoal.lote_ferias_local
                     ( cod_lote
                     , cod_local
                     )
                VALUES
                     ( reRecord.cod_lote
                     , CAST (stRegistros[inCount] AS INTEGER)
                     );
            END LOOP;

    END LOOP;
    CLOSE crCursor;

    stSqlEnt := '  SELECT DISTINCT cod_entidade
                     FROM administracao.entidade_rh
                    WHERE cod_entidade <> ( SELECT CAST(valor as integer)
                                              FROM administracao.configuracao
                                             WHERE exercicio = \'2009\'
                                               AND parametro = \'cod_entidade_prefeitura\'
                                          );
                ';
    OPEN crCursorEnt FOR EXECUTE stSqlEnt;
    LOOP
        FETCH crCursorEnt INTO reRecordEnt;
        EXIT WHEN NOT FOUND;

            stSql := '  SELECT *
                          FROM pessoal_'|| reRecordEnt.cod_entidade ||'.lote_ferias
                         WHERE nome ilike \'%lote para local%\';
                     ';
            OPEN crCursor FOR EXECUTE stSql;
            LOOP
                FETCH crCursor INTO reRecord;
                EXIT WHEN NOT FOUND;
        
                    stNome := string_to_array(reRecord.nome,' ');
                    stTemp := trim(both '()' from stNome[6]);
        
                    stRegistros := string_to_array(stTemp, ',');
                    inCountReg  := array_upper(stRegistros, 1);
                    FOR inCount IN 1..inCountReg LOOP
                        stSqlInsert := '    INSERT
                                              INTO pessoal_'|| reRecordEnt.cod_entidade ||'.lote_ferias_local
                                                 ( cod_lote
                                                 , cod_local
                                                 )
                                            VALUES
                                                 ( reRecord.cod_lote
                                                 , CAST (stRegistros[inCount] AS INTEGER)
                                                 );
                                       ';
                        EXECUTE stSqlInsert;
                    END LOOP;
        
            END LOOP;
            CLOSE crCursor;

    END LOOP;
    CLOSE crCursorEnt;

END;
$$ LANGUAGE 'plpgsql';

SELECT        insere_lote_ferias_local();
DROP FUNCTION insere_lote_ferias_local();



SELECT atualizarbanco ('
CREATE TABLE pessoal.lote_ferias_funcao (
    cod_lote            INTEGER             NOT NULL,
    cod_cargo           INTEGER             NOT NULL,
    CONSTRAINT pk_lote_ferias_funcao        PRIMARY KEY                     (cod_lote, cod_cargo),
    CONSTRAINT fk_lote_ferias_funcao_1      FOREIGN KEY                     (cod_lote)
                                            REFERENCES pessoal.lote_ferias  (cod_lote),
    CONSTRAINT fk_lote_ferias_funcao_2      FOREIGN KEY                     (cod_cargo)
                                            REFERENCES pessoal.cargo        (cod_cargo)
);
');

SELECT atualizarbanco ('GRANT ALL ON pessoal.lote_ferias_funcao TO GROUP urbem;');

CREATE OR REPLACE FUNCTION insere_lote_ferias_funcao () RETURNS VOID AS $$
DECLARE

    stNome          VARCHAR[];
    stRegistros     VARCHAR[];
    inCountReg      INTEGER := 0;
    inCount         INTEGER := 0;
    stTemp          VARCHAR;

    stSql           VARCHAR;
    reRecord        RECORD;
    crCursor        REFCURSOR;

    stSqlInsert     VARCHAR;
    stSqlEnt        VARCHAR;
    reRecordEnt     RECORD;
    crCursorEnt     REFCURSOR;

BEGIN

    stSql := '  SELECT *
                  FROM pessoal.lote_ferias
                 WHERE nome ilike \'%lote para função%\';
             ';
    OPEN crCursor FOR EXECUTE stSql;
    LOOP
        FETCH crCursor INTO reRecord;
        EXIT WHEN NOT FOUND;

            stNome := string_to_array(reRecord.nome,' ');
            stTemp := trim(both '()' from stNome[6]);

            stRegistros := string_to_array(stTemp, ',');
            inCountReg  := array_upper(stRegistros, 1);
            FOR inCount IN 1..inCountReg LOOP
                INSERT
                  INTO pessoal.lote_ferias_funcao
                     ( cod_lote
                     , cod_cargo
                     )
                VALUES
                     ( reRecord.cod_lote
                     , CAST (stRegistros[inCount] AS INTEGER)
                     );
            END LOOP;

    END LOOP;
    CLOSE crCursor;

    stSqlEnt := '  SELECT DISTINCT cod_entidade
                     FROM administracao.entidade_rh
                    WHERE cod_entidade <> ( SELECT CAST(valor as integer)
                                              FROM administracao.configuracao
                                             WHERE exercicio = \'2009\'
                                               AND parametro = \'cod_entidade_prefeitura\'
                                          );
                ';
    OPEN crCursorEnt FOR EXECUTE stSqlEnt;
    LOOP
        FETCH crCursorEnt INTO reRecordEnt;
        EXIT WHEN NOT FOUND;

            stSql := '  SELECT *
                          FROM pessoal_'|| reRecordEnt.cod_entidade ||'.lote_ferias
                         WHERE nome ilike \'%lote para função%\';
                     ';
            OPEN crCursor FOR EXECUTE stSql;
            LOOP
                FETCH crCursor INTO reRecord;
                EXIT WHEN NOT FOUND;
        
                    stNome := string_to_array(reRecord.nome,' ');
                    stTemp := trim(both '()' from stNome[6]);
        
                    stRegistros := string_to_array(stTemp, ',');
                    inCountReg  := array_upper(stRegistros, 1);
                    FOR inCount IN 1..inCountReg LOOP
                        stSqlInsert := '    INSERT
                                              INTO pessoal_'|| reRecordEnt.cod_entidade ||'.lote_ferias_funcao
                                                 ( cod_lote
                                                 , cod_cargo
                                                 )
                                            VALUES
                                                 ( reRecord.cod_lote
                                                 , CAST (stRegistros[inCount] AS INTEGER)
                                                 );
                                       ';
                        EXECUTE stSqlInsert;
                    END LOOP;
        
            END LOOP;
            CLOSE crCursor;

    END LOOP;
    CLOSE crCursorEnt;

END;
$$ LANGUAGE 'plpgsql';

SELECT        insere_lote_ferias_funcao();
DROP FUNCTION insere_lote_ferias_funcao();


----------------
-- Ticket #14950
----------------

select atualizarBanco('
CREATE OR REPLACE FUNCTION pessoal.fn_contrato_servidor_conta_salario_historico()  RETURNS TRIGGER AS $$
DECLARE
    reContaSalario      RECORD;
BEGIN
    If TG_OP=\'INSERT\' THEN
        INSERT INTO pessoal.contrato_servidor_conta_salario_historico
        (cod_contrato,cod_banco,cod_agencia,nr_conta) VALUES 
        (new.cod_contrato,new.cod_banco,new.cod_agencia,new.nr_conta);
    ELSE
         SELECT contrato_servidor_conta_salario.*
           INTO reContaSalario
           FROM pessoal.contrato_servidor_conta_salario
          WHERE contrato_servidor_conta_salario.cod_contrato = new.cod_contrato
            AND contrato_servidor_conta_salario.cod_banco = new.cod_banco
            AND contrato_servidor_conta_salario.cod_agencia = new.cod_agencia
            AND contrato_servidor_conta_salario.nr_conta = new.nr_conta;        

        IF reContaSalario.cod_contrato IS NULL THEN
            INSERT INTO pessoal.contrato_servidor_conta_salario_historico
            (cod_contrato,cod_banco,cod_agencia,nr_conta) VALUES 
            (new.cod_contrato,new.cod_banco,new.cod_agencia,new.nr_conta);
        END IF;
    END IF;
    Return new;
END;
$$ LANGUAGE plpgsql;
');

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE
    stSql           VARCHAR;
    stSqlEnt        VARCHAR;
    stSqlInsert     VARCHAR;
    inRetorno       INTEGER;
    reRecordEnt     RECORD;
BEGIN

    stSqlEnt := '  SELECT DISTINCT cod_entidade
                     FROM administracao.entidade_rh
                    WHERE cod_entidade <> ( SELECT CAST(valor as integer)
                                              FROM administracao.configuracao
                                             WHERE exercicio = \'2009\'
                                               AND parametro = \'cod_entidade_prefeitura\'
                                          );
                ';

    FOR reRecordEnt IN EXECUTE stSqlEnt LOOP
        stSql := '  SELECT 1
                      FROM information_schema.triggers 
                     WHERE trigger_schema = \'pessoal_'||reRecordEnt.cod_entidade||'\'
                       AND event_object_table = \'contrato_servidor_conta_salario\'
                     LIMIT 1;
                 ';
        inRetorno := selectIntoInteger(stSql);

        IF inRetorno IS NULL THEN
            stSqlInsert := 'CREATE TRIGGER tr_contrato_servidor_conta_salario_historico BEFORE INSERT OR UPDATE ON pessoal_'||reRecordEnt.cod_entidade||'.contrato_servidor_conta_salario FOR EACH ROW EXECUTE PROCEDURE pessoal_'||reRecordEnt.cod_entidade||'.fn_contrato_servidor_conta_salario_historico();';
            EXECUTE stSqlInsert;
            
            stSqlInsert := 'INSERT INTO pessoal_'||reRecordEnt.cod_entidade||'.contrato_servidor_conta_salario_historico SELECT cod_contrato, to_timestamp(\'1900-01-01\', \'yyyy-mm-dd\'), cod_agencia, cod_banco, nr_conta FROM pessoal_'||reRecordEnt.cod_entidade||'.contrato_servidor_conta_salario';
            EXECUTE stSqlInsert;
        END IF;
    END LOOP;
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();

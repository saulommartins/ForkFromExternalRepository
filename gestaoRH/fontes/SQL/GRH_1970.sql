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
* $Id: GRH_1961.sql 38308 2009-02-19 19:26:00Z fabio $
*
* Versão 1.97.0
*/

-- Criar acao no menu
INSERT INTO administracao.acao 
            (cod_acao
           , cod_funcionalidade
           , nom_acao
           , nom_arquivo
           , parametro
           , ordem
           , complemento_acao)
    VALUES ( 2483
           , 240
           , 'Totais da Folha'
           , 'FMTotaisFolha.php'
           , 'configurar'
           , '18'
           , ''); 

INSERT INTO administracao.acao 
            (cod_acao
           , cod_funcionalidade
           , nom_acao
           , nom_arquivo
           , parametro
           , ordem
           , complemento_acao)
    VALUES ( 2484
           , 276
           , 'Totais da Folha'
           , 'FLTotaisFolha.php'
           , 'imprimir'
           , '18'
           , ''); 


INSERT INTO administracao.relatorio 
         ( cod_gestao
         , cod_modulo
         , cod_relatorio
         , nom_relatorio    
         , arquivo )
    VALUES (  4        
         , 27        
         , 23           
         , 'Totais da Folha'
         , 'totaisDaFolha.rptdesign'
        );


----------------
-- Ticket #14604
----------------
select atualizarBanco ('ALTER TABLE folhapagamento.concessao_decimo ADD COLUMN folha_salario BOOLEAN;');
select atualizarBanco ('UPDATE folhapagamento.concessao_decimo SET folha_salario = FALSE;');
select atualizarBanco ('ALTER TABLE folhapagamento.concessao_decimo ALTER COLUMN folha_salario SET NOT NULL;');
select atualizarBanco ('ALTER TABLE folhapagamento.concessao_decimo ALTER COLUMN folha_salario SET DEFAULT FALSE;');


----------------
-- Ticket #14716
----------------
select atualizarBanco ('ALTER TABLE ima.configuracao_convenio_besc  DROP CONSTRAINT pk_configuracao_convenio_besc;');
select atualizarBanco ('ALTER TABLE ima.configuracao_convenio_besc  DROP CONSTRAINT fk_configuracao_convenio_besc_1;');
select atualizarBanco ('ALTER TABLE ima.configuracao_convenio_besc  RENAME TO configuracao_convenio_besc_bkp;');

SELECT atualizarbanco('
CREATE TABLE ima.configuracao_convenio_besc (
    cod_convenio            INTEGER             NOT NULL,
    cod_convenio_banco      VARCHAR(20)         NOT NULL,
    cod_banco               INTEGER             NOT NULL,
    CONSTRAINT pk_configuracao_convenio_besc    PRIMARY KEY                (cod_convenio, cod_banco),
    CONSTRAINT fk_configuracao_convenio_besc_1  FOREIGN KEY                (cod_banco)
                                                REFERENCES monetario.banco (cod_banco)
);
');

SELECT atualizarbanco('
GRANT ALL ON ima.configuracao_convenio_besc TO GROUP urbem;
');

SELECT atualizarbanco('
CREATE TABLE ima.configuracao_besc_conta (
    cod_convenio            INTEGER             NOT NULL,
    cod_banco               INTEGER             NOT NULL,
    cod_agencia             INTEGER             NOT NULL,
    cod_conta_corrente      INTEGER             NOT NULL,
    descricao               VARCHAR(60)         NOT NULL,
    CONSTRAINT pk_configuracao_besc_conta       PRIMARY KEY                               (cod_convenio, cod_banco, cod_agencia, cod_conta_corrente),
    CONSTRAINT fk_configuracao_besc_conta_1     FOREIGN KEY                               (cod_convenio, cod_banco)
                                                REFERENCES ima.configuracao_convenio_besc (cod_convenio, cod_banco),
    CONSTRAINT fk_configuracao_besc_conta_2     FOREIGN KEY                               (cod_banco, cod_agencia, cod_conta_corrente)
                                                REFERENCES monetario.conta_corrente       (cod_banco, cod_agencia, cod_conta_corrente)
);
');

SELECT atualizarbanco('
GRANT ALL ON ima.configuracao_besc_conta TO GROUP urbem;
');

SELECT atualizarbanco('
CREATE TABLE ima.configuracao_besc_orgao (
    cod_convenio            INTEGER             NOT NULL,
    cod_banco               INTEGER             NOT NULL,
    cod_agencia             INTEGER             NOT NULL,
    cod_conta_corrente      INTEGER             NOT NULL,
    cod_orgao               INTEGER             NOT NULL,
    CONSTRAINT pk_configuracao_besc_orgao       PRIMARY KEY                             (cod_convenio, cod_banco, cod_agencia, cod_conta_corrente, cod_orgao),
    CONSTRAINT fk_configuracao_besc_orgao_1     FOREIGN KEY                             (cod_convenio, cod_banco, cod_agencia, cod_conta_corrente)
                                                REFERENCES  ima.configuracao_besc_conta (cod_convenio, cod_banco, cod_agencia, cod_conta_corrente),
    CONSTRAINT fk_configuracao_besc_orgao_2     FOREIGN KEY                             (cod_orgao)
                                                REFERENCES organograma.orgao            (cod_orgao)
 );
');

SELECT atualizarbanco('
GRANT ALL ON ima.configuracao_besc_orgao TO GROUP urbem;
');

SELECT atualizarbanco('
CREATE TABLE ima.configuracao_besc_local (
    cod_convenio            INTEGER             NOT NULL,
    cod_banco               INTEGER             NOT NULL,
    cod_agencia             INTEGER             NOT NULL,
    cod_conta_corrente      INTEGER             NOT NULL,
    cod_local               INTEGER             NOT NULL,
    CONSTRAINT pk_configuracao_besc_local       PRIMARY KEY                             (cod_convenio, cod_banco, cod_agencia, cod_conta_corrente, cod_local),
    CONSTRAINT fk_configuracao_besc_local_1     FOREIGN KEY                             (cod_convenio, cod_banco, cod_agencia, cod_conta_corrente)
                                                REFERENCES  ima.configuracao_besc_conta (cod_convenio, cod_banco, cod_agencia, cod_conta_corrente),
    CONSTRAINT fk_configuracao_besc_local_2     FOREIGN KEY                             (cod_local)
                                                REFERENCES organograma.local            (cod_local)
 );
');

SELECT atualizarbanco('
GRANT ALL ON ima.configuracao_besc_local TO GROUP urbem;
');

SELECT atualizarbanco('
INSERT INTO ima.configuracao_convenio_besc
SELECT cod_convenio
     , cod_convenio_banco
     , cod_banco
  FROM ima.configuracao_convenio_besc_bkp;
');

SELECT atualizarbanco('DROP TABLE ima.configuracao_convenio_besc_bkp;');

----------------
-- Ticket #14718
----------------
SELECT atualizarbanco('
CREATE TABLE folhapagamento.configuracao_totais_folha (
    cod_configuracao        INTEGER             NOT NULL,
    descricao               VARCHAR(60)         NOT NULL,
    CONSTRAINT pk_configuracao_totais_folha     PRIMARY KEY (cod_configuracao)
 );
');

SELECT atualizarbanco('
GRANT ALL ON folhapagamento.configuracao_totais_folha TO GROUP urbem;
');

SELECT atualizarbanco('
CREATE TABLE folhapagamento.totais_folha_eventos (
    cod_configuracao        INTEGER             NOT NULL,
    cod_evento              INTEGER             NOT NULL,
    CONSTRAINT pk_totais_folha_eventos          PRIMARY KEY                      (cod_configuracao,cod_evento),
    CONSTRAINT fk_totais_folha_eventos_1        FOREIGN KEY                      (cod_evento)
                                                REFERENCES folhapagamento.evento (cod_evento)
 );
');

SELECT atualizarbanco('
GRANT ALL ON folhapagamento.totais_folha_eventos TO GROUP urbem;
');


----------------
-- Ticket #14569
----------------

CREATE TYPE linhaComprovanteRendimentosIRRF AS (
    nom_cgm                                 VARCHAR,
    cpf                                     VARCHAR,
    cod_contrato                            VARCHAR,
    registro                                VARCHAR,
    cod_cid                                 VARCHAR,
    total_rendimentos                       NUMERIC(8,2),
    contribuicao_previdenciaria_oficial     NUMERIC(8,2),
    contribuicao_previdenciaria_privada     NUMERIC(8,2),
    pensao_alimenticia                      NUMERIC(8,2),
    imposto_renda_retido                    NUMERIC(8,2),
    parcela_isenta_aposentadoria            NUMERIC(8,2),
    diarias_ajuda_custo                     NUMERIC(8,2),
    informativo_aposentadoria               NUMERIC(8,2),
    pensao_proventos_molestia_acidente      NUMERIC(8,2),
    decimo_terceiro                         NUMERIC(8,2),
    
    descricao_orgao                         VARCHAR,
    descricao_local                         VARCHAR,
    descricao_funcao                        VARCHAR,
    descricao_especialidade                 VARCHAR,
    descricao_atributo                      VARCHAR
);


----------------
-- Ticket #14874
----------------
select atualizarBanco ('ALTER TABLE pessoal.contrato_servidor DROP CONSTRAINT fk_contrato_servidor_6;');
select atualizarBanco ('UPDATE pessoal.contrato_servidor SET cod_tipo_salario = 10 WHERE cod_tipo_salario = 1;');
select atualizarBanco ('UPDATE pessoal.contrato_servidor SET cod_tipo_salario = 1 WHERE cod_tipo_salario = 3;');
select atualizarBanco ('UPDATE pessoal.contrato_servidor SET cod_tipo_salario = 3 WHERE cod_tipo_salario = 10;');
select atualizarBanco ('UPDATE pessoal.tipo_salario SET descricao = \'Horário\' WHERE cod_tipo_salario = 5;');
select atualizarBanco ('UPDATE pessoal.tipo_salario SET descricao = \'Tarefa\' WHERE cod_tipo_salario = 6;');
select atualizarBanco ('UPDATE pessoal.tipo_salario SET descricao = \'Mensal\' WHERE cod_tipo_salario = 1;');
select atualizarBanco ('UPDATE pessoal.tipo_salario SET descricao = \'Semanal\' WHERE cod_tipo_salario = 3;');
select atualizarBanco ('INSERT INTO pessoal.tipo_salario (cod_tipo_salario,descricao) VALUES (7,\'Outros\');');

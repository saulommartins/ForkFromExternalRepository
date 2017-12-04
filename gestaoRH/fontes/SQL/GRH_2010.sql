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
*
* Script de DDL e DML
*
* Versao 2.01.0
*
* Fabio Bertoldi - 20120713
*
*/

----------------
-- Ticket #17779
----------------

UPDATE administracao.acao SET nom_arquivo = 'FMManterBeneficiario.php' where cod_acao = 2809;

SELECT atualizarbanco('ALTER TABLE beneficio.beneficiario DROP CONSTRAINT pk_beneficiario;'                                                                                                           );
SELECT atualizarbanco('ALTER TABLE beneficio.beneficiario ADD  CONSTRAINT pk_beneficiario   PRIMARY KEY (cod_contrato, cgm_fornecedor, cod_modalidade, cod_tipo_convenio, codigo_usuario, timestamp);');
SELECT atualizarbanco('ALTER TABLE beneficio.beneficiario ADD  CONSTRAINT fk_beneficiario_6 FOREIGN KEY (grau_parentesco) REFERENCES cse.grau_parentesco (cod_grau);'                                 );

SELECT atualizarbanco('
CREATE TABLE beneficio.beneficiario_lancamento(
    cod_contrato                INTEGER         NOT NULL,
    cgm_fornecedor              INTEGER         NOT NULL,
    cod_modalidade              INTEGER         NOT NULL,
    cod_tipo_convenio           INTEGER         NOT NULL,
    codigo_usuario              INTEGER         NOT NULL,
    timestamp                   TIMESTAMP       NOT NULL,
    timestamp_lancamento        TIMESTAMP(3)    NOT NULL DEFAULT (\'now\'::TEXT)::TIMESTAMP(3) WITH TIME ZONE,
    valor                       NUMERIC(14,2)   NOT NULL,
    cod_periodo_movimentacao    INTEGER         NOT NULL,
    CONSTRAINT pk_beneficiario_lancamento       PRIMARY KEY                                    (cod_contrato, cgm_fornecedor, cod_modalidade, cod_tipo_convenio, codigo_usuario, timestamp, timestamp_lancamento),
    CONSTRAINT fk_beneficiario_lancamento_1     FOREIGN KEY                                    (cod_contrato, cgm_fornecedor, cod_modalidade, cod_tipo_convenio, codigo_usuario, timestamp)
                                                REFERENCES beneficio.beneficiario              (cod_contrato, cgm_fornecedor, cod_modalidade, cod_tipo_convenio, codigo_usuario, timestamp),
    CONSTRAINT fk_beneficiario_lancamento_2     FOREIGN KEY                                    (cod_periodo_movimentacao)
                                                REFERENCES folhapagamento.periodo_movimentacao (cod_periodo_movimentacao)
);
');

SELECT atualizarbanco('GRANT ALL ON beneficio.beneficiario_lancamento TO siamweb;');

INSERT INTO administracao.tabelas_rh(schema_cod, nome_tabela, sequencia) VALUES (5, 'beneficiario_lancamento',1);


----------------
-- Ticket #
----------------

INSERT
  INTO administracao.relatorio
  ( cod_gestao
  , cod_modulo
  , cod_relatorio
  , nom_relatorio
  , arquivo )
  VALUES
  ( 4
  , 22
  , 13
  , 'Relatório de Assentamento por Contrato'
  , 'assentamentos_contrato.rptdesign'
  );


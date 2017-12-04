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
* URBEM SoluÃ§Ãµes de GestÃ£o PÃºblica Ltda
* www.urbem.cnm.org.br
*
* $Id:$
*
* VersÃ£o 2.00.5
*/

----------------
-- Ticket #17779
----------------

INSERT
  INTO administracao.funcionalidade
  ( cod_funcionalidade
  , cod_modulo
  , nom_funcionalidade
  , nom_diretorio
  , ordem )
  VALUES
  ( 472
  , 26
  , 'Plano de Saúde'
  , 'instancias/planoSaude/'
  , 90
  );

INSERT
  INTO administracao.acao
  ( cod_acao
  , cod_funcionalidade
  , nom_arquivo
  , parametro
  , ordem
  , complemento_acao
  , nom_acao )
  VALUES
  ( 2809
  , 472
  , 'FMManterBeneficiarios.php'
  , 'manter'
  , 1
  , ''
  , 'Manter Beneficiários'
  );


INSERT
  INTO administracao.acao
  ( cod_acao
  , cod_funcionalidade
  , nom_arquivo
  , parametro
  , ordem
  , complemento_acao
  , nom_acao )
  VALUES
  ( 2810
  , 472
  , 'FMManterImportacaoMensal.php'
  , 'manter'
  , 2
  , ''
  , 'Manter Importação Mensal'
  );


SELECT atualizarbanco('
CREATE TABLE beneficio.modalidade_convenio_medico(
    cod_modalidade      INTEGER         NOT NULL,
    descricao           VARCHAR(80)     NOT NULL,
    CONSTRAINT pk_modalidade_convenio_medico     PRIMARY KEY (cod_modalidade)
);
');

SELECT atualizarbanco('
GRANT ALL ON beneficio.modalidade_convenio_medico TO urbem;
');

SELECT atualizarbanco('
INSERT INTO beneficio.modalidade_convenio_medico
       (cod_modalidade,descricao)
       values
       (30, \'Unimed Empresarial Regulamentado\');
');

SELECT atualizarbanco('
INSERT INTO beneficio.modalidade_convenio_medico
       (cod_modalidade,descricao)
       values
       (40, \'Unimed Empresarial Não Regulamentado\');
');

SELECT atualizarbanco('
CREATE TABLE beneficio.tipo_convenio_medico(
    cod_tipo_convenio   INTEGER         NOT NULL,
    descricao           VARCHAR(80)     NOT NULL,
    CONSTRAINT pk_tipo_convenio_medico  PRIMARY KEY (cod_tipo_convenio)
);
');

SELECT atualizarbanco('
GRANT ALL ON beneficio.tipo_convenio_medico TO urbem;
');

SELECT atualizarbanco('
INSERT INTO beneficio.tipo_convenio_medico
       (cod_tipo_convenio,descricao)
       values 
       (24, \'Unimed Apartamento\');
');

SELECT atualizarbanco('
INSERT INTO beneficio.tipo_convenio_medico
       (cod_tipo_convenio,descricao)
       values
       (41, \'Unimed Apartamento Especial\');
');

SELECT atualizarbanco('
INSERT INTO beneficio.tipo_convenio_medico
       (cod_tipo_convenio,descricao)
       values
       (126, \'Unimed Enfermaria Não Regulamentada\');
');

SELECT atualizarbanco('
INSERT INTO beneficio.tipo_convenio_medico
       (cod_tipo_convenio,descricao)
       values
       (634, \'Unimed Enfermaria Regulamentada\');
');


SELECT atualizarbanco('
CREATE TABLE beneficio.beneficiario(
    cod_contrato        INTEGER         NOT NULL,
    cgm_fornecedor      INTEGER         NOT NULL,
    cod_modalidade      INTEGER         NOT NULL,
    cod_tipo_convenio   INTEGER         NOT NULL,
    cgm_beneficiario    INTEGER         NOT NULL,
    timestamp           TIMESTAMP(3)    NOT NULL DEFAULT (\'now\'::TEXT)::TIMESTAMP(3) WITH TIME ZONE,
    grau_parentesco     INTEGER         NOT NULL,
    codigo_usuario      INTEGER         NOT NULL,
    dt_inicio           DATE            NOT NULL,
    dt_fim              DATE                    ,
    valor               NUMERIC(14,2)   NOT NULL,
    timestamp_excluido  TIMESTAMP(3),
    CONSTRAINT pk_beneficiario          PRIMARY KEY (cod_contrato, cgm_fornecedor, cod_modalidade, cod_tipo_convenio, cgm_beneficiario, timestamp),
    CONSTRAINT fk_beneficiario_1        FOREIGN KEY (cod_contrato)
                                        REFERENCES pessoal.contrato (cod_contrato),
    CONSTRAINT fk_beneficiario_2        FOREIGN KEY (cgm_fornecedor)
                                        REFERENCES beneficio.fornecedor(numcgm),
    CONSTRAINT fk_beneficiario_3        FOREIGN KEY (cod_modalidade)
                                        REFERENCES beneficio.modalidade_convenio_medico(cod_modalidade),
    CONSTRAINT fk_beneficiario_4        FOREIGN KEY (cod_tipo_convenio)
                                        REFERENCES beneficio.tipo_convenio_medico(cod_tipo_convenio),
    CONSTRAINT fk_beneficiario_5        FOREIGN KEY (cgm_beneficiario)
                                        REFERENCES sw_cgm(numcgm)
);
');

SELECT atualizarbanco('
GRANT ALL ON beneficio.beneficiario TO urbem;
');

INSERT INTO administracao.tabelas_rh(schema_cod, nome_tabela, sequencia) VALUES (5, 'modalidade_convenio_medico',1);
INSERT INTO administracao.tabelas_rh(schema_cod, nome_tabela, sequencia) VALUES (5, 'tipo_convenio_medico',1);
INSERT INTO administracao.tabelas_rh(schema_cod, nome_tabela, sequencia) VALUES (5, 'beneficiario',1);


----------------
-- Ticket #16588
----------------

INSERT
  INTO administracao.acao
  ( cod_acao
  , cod_funcionalidade
  , nom_arquivo
  , parametro
  , ordem
  , complemento_acao
  , nom_acao )
  VALUES
  ( 2811
  , 276
  , 'FLRelatorioAuditoriaFolha.php'
  , ''
  , 21
  , ''
  , 'Auditoria da Folha'
  );

INSERT
  INTO administracao.relatorio
  ( cod_gestao
  , cod_modulo
  , cod_relatorio
  , nom_relatorio
  , arquivo )
  VALUES
  ( 4
  , 27
  , 26
  , 'AuditoriaFolha_lotacao'
  , 'relatorioAuditoriaFolha.rptdesign'
  );

INSERT
  INTO administracao.relatorio
  ( cod_gestao
  , cod_modulo
  , cod_relatorio
  , nom_relatorio
  , arquivo )
  VALUES
  ( 4
  , 27
  , 27
  , 'AuditoriaFolha_lotacao'
  , 'relatorioAuditoriaFolha_lotacao.rptdesign'
  );

INSERT
  INTO administracao.relatorio
  ( cod_gestao
  , cod_modulo
  , cod_relatorio
  , nom_relatorio
  , arquivo )
  VALUES
  ( 4
  , 27
  , 28
  , 'AuditoriaFolha_lotacao'
  , 'relatorioAuditoriaFolha_local.rptdesign'
  );


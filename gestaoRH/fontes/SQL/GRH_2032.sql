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
* Versao 2.03.2
*
* Fabio Bertoldi - 20141001
*
*/

----------------
-- Ticket #22125
----------------

DROP   TYPE colunasDadosCertidaoTempoServidoCompleta CASCADE;
CREATE TYPE colunasDadosCertidaoTempoServidoCompleta AS (
       cod_contrato             integer
     , registro                 integer
     , nom_cgm                  varchar
     , dt_nascimento            varchar
     , sexo                     varchar
     , rg                       varchar
     , dt_emissao_rg            varchar
     , orgao_emissor_rg         varchar
     , servidor_pis_pasep       varchar
     , cpf                      varchar
     , nacionalidade            varchar
     , escolaridade             varchar
     , nome_pai                 varchar
     , nome_mae                 varchar
     , nr_titulo_eleitor        varchar
     , zona_titulo              varchar
     , secao_titulo             varchar
     , nom_estado               varchar
     , nom_municipio            varchar
     , sigla_uf                 varchar
     , sigla_cid                varchar
     , descricao_cid            varchar
     , numero                   varchar
     , serie                    varchar
     , orgao_expedidor          varchar
     , dt_emissao               varchar
     , sigla_uf_ctps            varchar
     , dt_pis_pasep             varchar
     , nr_carteira_res          varchar
     , cat_reservista           varchar
     , origem_reservista        varchar
     , dt_nomeacao              varchar
     , dt_posse                 varchar
     , dt_admissao              varchar
     , exercicio                varchar
     , num_norma                varchar
     , nom_norma                varchar
     , cod_tipo_admissao        varchar
     , tipo_admissao            varchar
     , num_ocorrencia           varchar
     , ocorrencia               varchar
     , cargo                    varchar
     , regime                   varchar
     , sub_divisao              varchar
     , especialidade            varchar
     , funcao                   varchar
     , regime_funcao            varchar
     , sub_divisao_funcao       varchar
     , especialidade_funcao     varchar
     , horas_mensais            varchar
     , horas_semanais           varchar
     , salario                  varchar
     , padrao                   varchar
     , orgao                    varchar
     , descricao_orgao          varchar
     , local                    varchar
     , dt_rescisao              varchar
     , num_causa                varchar
     , descricao_causa          varchar
     , progressao               varchar
);


----------------
-- Ticket #21970
----------------

INSERT
  INTO administracao.funcionalidade
     ( cod_funcionalidade
     , cod_modulo
     , nom_funcionalidade
     , nom_diretorio
     , ordem
     , ativo
     )
     VALUES
     ( 486
     , 26
     , 'Configuração'
     , 'instancias/configuracao/'
     , 0
     , TRUE
     );

INSERT
  INTO administracao.acao
     ( cod_acao
     , cod_funcionalidade
     , nom_arquivo
     , parametro
     , ordem
     , complemento_acao
     , nom_acao
     , ativo
     )
     VALUES
     ( 2988
     , 486
     , 'FMConfiguracaoPlanoSaude.php'
     , 'configurar'
     , 1
     , ''
     , 'Plano de Saúde'
     , TRUE
     );


CREATE TABLE beneficio.layout_plano_saude(
    cod_layout      INTEGER             NOT NULL,
    padrao          VARCHAR(25)         NOT NULL,
    CONSTRAINT pk_layout_plano_saude    PRIMARY KEY (cod_layout)
);
GRANT ALL ON beneficio.layout_plano_saude TO urbem;

INSERT INTO beneficio.layout_plano_saude VALUES (1, 'Unimed');


CREATE TABLE beneficio.layout_fornecedor(
    cgm_fornecedor      INTEGER         NOT NULL,
    cod_layout          INTEGER         NOT NULL,
    CONSTRAINT pk_layout_fornecedor     PRIMARY KEY                            (cgm_fornecedor),
    CONSTRAINT fk_layout_fornecedor_1   FOREIGN KEY                            (cgm_fornecedor)
                                        REFERENCES compras.fornecedor          (cgm_fornecedor),
    CONSTRAINT fk_layout_fornecedor_2   FOREIGN KEY                            (cod_layout)
                                        REFERENCES beneficio.layout_plano_saude(cod_layout)
);
GRANT ALL ON beneficio.layout_fornecedor TO urbem;


----------------
-- Ticket #20073
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
     , 14
     , 'Rescindir Contrato'
     , 'LHAnexoITermoRescisaoContrato.php'
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
     , 22
     , 15
     , 'Anexo VI Termo Homologação MTE'
     , 'LHAnexoVITermoHomologacaoMTE.php'
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
     , 22
     , 16
     , 'Anexo VII  Termo Quitação MTE'
     , 'LHAnexoVIITermoQuitacaoMTE.php'
     );
 




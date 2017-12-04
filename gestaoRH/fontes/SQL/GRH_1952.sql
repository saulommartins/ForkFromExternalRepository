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
* $Id: GRH_1952.sql 64421 2016-02-19 12:14:17Z fabio $
*
* Versão 1.95.1
*/


-----------------------------------------------------------------------------------------------
-- MOVIDA COLUNA 'situacao' DA TABELA 'olhapagamento.configuracao_empenho' PARA TABELA AUXILIAR
-- SOLICITADO POR DIEGO LEMOS DE SOUZA - 20090107 ---------------------------------------------

select atualizarBanco('
CREATE TABLE folhapagamento.configuracao_empenho_situacao (
    cod_configuracao        INTEGER         NOT NULL,
    sequencia               INTEGER         NOT NULL,
    exercicio               CHAR(4)         NOT NULL,
    situacao                CHAR(1)         NOT NULL,
    CONSTRAINT pk_configuracao_empenho_situacao     PRIMARY KEY                                     (cod_configuracao, sequencia, exercicio, situacao),
    CONSTRAINT fk_configuracao_empenho_situacao_1   FOREIGN KEY                                     (cod_configuracao, sequencia, exercicio)
                                                    REFERENCES folhapagamento.configuracao_empenho  (cod_configuracao, sequencia, exercicio)
);
');

select atualizarBanco('
GRANT ALL ON folhapagamento.configuracao_empenho_situacao TO GROUP urbem;
');

select atualizarBanco('
INSERT
  INTO folhapagamento.configuracao_empenho_situacao
SELECT cod_configuracao
     , sequencia
     , exercicio
     , situacao
  FROM folhapagamento.configuracao_empenho;
');

select atualizarBanco('
ALTER TABLE folhapagamento.configuracao_empenho DROP COLUMN situacao;
');



 ----------------
-- Ticket #14239
----------------
select atualizarBanco('
CREATE TABLE folhapagamento.deducao_dependente (
    numcgm                      INTEGER         NOT NULL,
    cod_periodo_movimentacao    INTEGER         NOT NULL,    
    cod_contrato                INTEGER         NOT NULL,
    cod_tipo                    INTEGER         NOT NULL,
    CONSTRAINT pk_deducao_dependente            PRIMARY KEY                                     (numcgm, cod_periodo_movimentacao, cod_tipo),    
    CONSTRAINT fk_deducao_dependente_1          FOREIGN KEY                                     (numcgm)
                                                REFERENCES sw_cgm_pessoa_fisica                 (numcgm),
    CONSTRAINT fk_deducao_dependente_2          FOREIGN KEY                                     (cod_periodo_movimentacao)
                                                REFERENCES folhapagamento.periodo_movimentacao  (cod_periodo_movimentacao),
    CONSTRAINT fk_deducao_dependente_3          FOREIGN KEY                                     (cod_contrato)
                                                REFERENCES pessoal.contrato                     (cod_contrato),
    CONSTRAINT fk_deducao_dependente_4          FOREIGN KEY                                     (cod_tipo)
                                                REFERENCES folhapagamento.tipo_folha            (cod_tipo)
);
');

select atualizarBanco('
GRANT ALL ON folhapagamento.deducao_dependente TO GROUP urbem;
');

select atualizarBanco('
CREATE TABLE folhapagamento.deducao_dependente_complementar (
    numcgm                      INTEGER                      NOT NULL,
    cod_periodo_movimentacao    INTEGER                      NOT NULL,    
    cod_tipo                    INTEGER                      NOT NULL, 
    cod_complementar            INTEGER                      NOT NULL,
    CONSTRAINT pk_deducao_dependente_complementar            PRIMARY KEY                                     (numcgm, cod_periodo_movimentacao, cod_tipo),    
    CONSTRAINT fk_deducao_dependente_complementar_1          FOREIGN KEY                                     (numcgm, cod_periodo_movimentacao, cod_tipo)
                                                             REFERENCES folhapagamento.deducao_dependente    (numcgm, cod_periodo_movimentacao, cod_tipo),
    CONSTRAINT fk_deducao_dependente_complementar_2          FOREIGN KEY                                     (cod_complementar, cod_periodo_movimentacao)
                                                             REFERENCES folhapagamento.complementar          (cod_complementar, cod_periodo_movimentacao)
);
');

select atualizarBanco('
GRANT ALL ON folhapagamento.deducao_dependente_complementar TO GROUP urbem;
');



----------------
-- Ticket #14300
----------------

INSERT INTO administracao.relatorio
          ( cod_gestao
          , cod_modulo
          , cod_relatorio
          , nom_relatorio
          , arquivo )
     VALUES ( 4
          , 27
          , 22
          , 'Relatório Customizável de Eventos'
          , 'customizavelEventosGrupos.rptdesign'
          );


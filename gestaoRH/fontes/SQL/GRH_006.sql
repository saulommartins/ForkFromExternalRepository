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
* $Revision: 28350 $
* $Name$
* $Author: gris $
* $Date: 2008-03-05 09:57:44 -0300 (Qua, 05 Mar 2008) $
*
* Versão 006.
*/

INSERT INTO administracao.relatorio
            (cod_gestao
          , cod_modulo
          , cod_relatorio
          , nom_relatorio
          , arquivo)
     VALUES (4
          , 40
          , 13
          , 'Manutenção de Conveniados IPE/RS'
          , 'conveniadosIpers.rptdesign');

------------
-- Ticket #12479
------------

select atualizarBanco('
CREATE TABLE folhapagamento.configuracao_ipe (
  cod_configuracao      integer      NOT NULL,
  vigencia              date         NOT NULL,
  cod_atributo_data     integer      NOT NULL,
  cod_modulo_data       integer      NOT NULL,
  cod_cadastro_data     integer      NOT NULL,
  cod_atributo_mat      integer      NOT NULL,
  cod_modulo_mat        integer      NOT NULL,
  cod_cadastro_mat      integer      NOT NULL,
  cod_evento_automatico integer      NOT NULL,
  cod_evento_base       integer      NOT NULL,
  codigo_orgao          integer      NOT NULL,
  contribuicao_pat      numeric(5,2) NOT NULL,
  contibuicao_serv      numeric(5,2) NOT NULL,
  CONSTRAINT pk_configuracao_ipe PRIMARY KEY(cod_configuracao,vigencia ),
  CONSTRAINT fk_configuracao_ipe_1 FOREIGN KEY(cod_evento_base) REFERENCES folhapagamento.evento(cod_evento),
  CONSTRAINT fk_configuracao_ipe_2 FOREIGN KEY(cod_evento_automatico) REFERENCES folhapagamento.evento(cod_evento),
  CONSTRAINT fk_configuracao_ipe_3 FOREIGN KEY(cod_cadastro_mat, cod_modulo_mat, cod_atributo_mat) REFERENCES administracao.atributo_dinamico(cod_cadastro, cod_modulo, cod_atributo),
  CONSTRAINT fk_configuracao_ipe_4 FOREIGN KEY(cod_cadastro_data, cod_modulo_data, cod_atributo_data) REFERENCES administracao.atributo_dinamico(cod_cadastro, cod_modulo, cod_atributo)
);
');
select atualizarBanco('
CREATE TABLE folhapagamento.configuracao_ipe_pensionista (
  cod_configuracao  integer NOT NULL,
  vigencia          date    NOT NULL,
  cod_atributo_data integer NOT NULL,
  cod_modulo_data   integer NOT NULL,
  cod_cadastro_data integer NOT NULL,
  cod_atributo_mat  integer NOT NULL,
  cod_modulo_mat    integer NOT NULL,
  cod_cadastro_mat  integer NOT NULL,
  CONSTRAINT pk_configuracao_ipe_pensionista PRIMARY KEY(vigencia, cod_configuracao),
  CONSTRAINT fk_configuracao_ipe_pensionista_1 FOREIGN KEY(cod_configuracao, vigencia) REFERENCES folhapagamento.configuracao_ipe(cod_configuracao, vigencia),
  CONSTRAINT fk_configuracao_ipe_pensionista_2 FOREIGN KEY(cod_cadastro_mat, cod_modulo_mat, cod_atributo_mat) REFERENCES administracao.atributo_dinamico(cod_cadastro, cod_modulo, cod_atributo),
  CONSTRAINT fk_configuracao_ipe_pensionista_3 FOREIGN KEY(cod_cadastro_data, cod_modulo_data, cod_atributo_data) REFERENCES administracao.atributo_dinamico(cod_cadastro, cod_modulo, cod_atributo)
);
');

select atualizarBanco('
GRANT INSERT, DELETE, UPDATE, SELECT ON folhapagamento.configuracao_ipe TO GROUP urbem;
');

select atualizarBanco('
GRANT INSERT, DELETE, UPDATE, SELECT ON folhapagamento.configuracao_ipe_pensionista TO GROUP urbem;
');

------------
-- Ticket #12478
------------

INSERT INTO administracao.funcionalidade
            (cod_funcionalidade
          , cod_modulo
          , nom_funcionalidade
          , nom_diretorio
          , ordem)
     SELECT 410
          , 40
          , 'IPERS'
          , 'instancias/IPERS/'
          , 11
      WHERE 0 = (SELECT COUNT(*)
                   FROM administracao.funcionalidade
                  WHERE cod_funcionalidade = 410);


INSERT INTO administracao.acao
            (cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao)
     VALUES (2238
          , 410
          , 'FLExportarIpers.php'
          , 'exportar'
          , 1
          , ''
          , 'Exportar Arquivo IPERS');

------------
-- Ticket #12477
------------


INSERT INTO administracao.acao
            (cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao)
     VALUES (2245
          , 240
          , 'LSConfiguracaoIpers.php'
          , 'alterar'
          , 16
          , ''
          , 'Alterar IPERS');

INSERT INTO administracao.acao
            (cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao)
     VALUES (2246
          , 240
          , 'LSConfiguracaoIpers.php'
          , 'excluir'
          , 17
          , ''
          , 'Excluir IPERS');

------------
-- Ticket #12477
------------

INSERT INTO administracao.acao
            (cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao)
     VALUES (2239
          , 240
          , 'FMConfiguracaoIpers.php'
          , 'incluir'
          , 15
          , ''
          , 'Incluir IPERS');




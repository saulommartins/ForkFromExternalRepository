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
* Versao 2.02.3
*
* Gelson Gonçalves - 20140320
*
*/

----------------
-- Ticket #21573
----------------

--Cria tabela
CREATE TABLE administracao.orgao_registro (
codigo INTEGER NOT NULL,
descricao VARCHAR(60) NULL,
CONSTRAINT pk_orgao_registro PRIMARY KEY (codigo)
);

--Carga dos dados
INSERT INTO administracao.orgao_registro (codigo, descricao) VALUES (0, 'Não Informado');
INSERT INTO administracao.orgao_registro (codigo, descricao) VALUES (1, 'Cartório de registro civil de pessoas jurídicas');
INSERT INTO administracao.orgao_registro (codigo, descricao) VALUES (2, 'Junta Comercial');
INSERT INTO administracao.orgao_registro (codigo, descricao) VALUES (3, 'Ordem dos Advogados do Brasil - OAB');

--Adiciona Campos e FK
ALTER TABLE sw_cgm_pessoa_juridica ADD cod_orgao_registro INTEGER NOT NULL DEFAULT 0;
ALTER TABLE sw_cgm_pessoa_juridica ADD num_registro VARCHAR(20) NOT NULL DEFAULT '';
ALTER TABLE sw_cgm_pessoa_juridica ADD dt_registro DATE;
ALTER TABLE sw_cgm_pessoa_juridica ADD num_registro_cvm VARCHAR(20) NOT NULL DEFAULT '';
ALTER TABLE sw_cgm_pessoa_juridica ADD dt_registro_cvm DATE;
ALTER TABLE sw_cgm_pessoa_juridica ADD objeto_social TEXT NOT NULL DEFAULT '';

ALTER TABLE sw_cgm_pessoa_juridica ADD CONSTRAINT fk_orgao_registro FOREIGN KEY (cod_orgao_registro) REFERENCES administracao.orgao_registro(codigo);

----------------
-- Ticket #
----------------

--Deleta as permissões das ações do módulo 
DELETE FROM administracao.permissao 
      WHERE cod_acao IN (SELECT cod_acao 
                           FROM administracao.acao 
                          WHERE cod_funcionalidade IN (SELECT cod_funcionalidade 
                                                         FROM administracao.funcionalidade 
                                                        WHERE cod_modulo = 0
                                                      )
                        );

--Desativa as ações das funcionalidades do módulo CSE
UPDATE administracao.acao 
   SET ativo = false
 WHERE cod_funcionalidade IN (SELECT cod_funcionalidade 
                                FROM administracao.funcionalidade 
                               WHERE cod_modulo = 0
                                  );

--Desativa as funcionalidades do módulo CSE
UPDATE administracao.funcionalidade 
   SET ativo = false
 WHERE cod_modulo = 0;

--Desativa o módulo CSE
UPDATE administracao.modulo 
   SET ativo = false
 WHERE cod_modulo = 0;
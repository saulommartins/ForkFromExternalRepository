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
* $Id: GP_1921.sql 59612 2014-09-02 12:00:51Z gelson $
*
* Versão 1.92.1
*/

----------------
-- Ticket #8956
----------------

UPDATE administracao.acao
   SET ordem = ordem + 1
 WHERE cod_funcionalidade = 356
   AND ordem > 4;

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2464
          , 356
          , 'FLManterCompraDireta.php'
          , 'consultar'
          , 5
          , ''
          , 'Consultar Compra Direta'
          );

----------------
-- Ticket #14387
----------------

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2474
          , 37
          , 'FLManterUtilizacao.php'
          , 'excluir'
          , 10
          , ''
          , 'Manutenção de Utilizações'
          );

INSERT INTO administracao.relatorio
         ( cod_gestao
         , cod_modulo
         , cod_relatorio
         , nom_relatorio
         , arquivo )
    VALUES ( 3
         , 29
         , 10
         , 'Saída por Autorização de Abastecimento'
         , 'saidaAutorizacaoAbastecimento.rptdesign'
         );


INSERT INTO administracao.relatorio
         ( cod_gestao
         , cod_modulo
         , cod_relatorio
         , nom_relatorio
         , arquivo )
    VALUES ( 3
         , 29
         , 11
         , 'Saída por Transferência'
         , 'saidaTransferencia.rptdesign'
         );

INSERT INTO administracao.relatorio
         ( cod_gestao
         , cod_modulo
         , cod_relatorio
         , nom_relatorio
         , arquivo )
    VALUES ( 3
         , 29
         , 12
         , 'Entrada por Transferência'
         , 'entradaTransferencia.rptdesign'
         );

INSERT INTO administracao.relatorio
         ( cod_gestao
         , cod_modulo
         , cod_relatorio
         , nom_relatorio
         , arquivo )
    VALUES ( 3
         , 29
         , 13
         , 'Saída por Estorno de Entrada'
         , 'saidaEstornoEntrada.rptdesign'
         );

DELETE FROM almoxarifado.natureza 
    WHERE cod_natureza  = 8 
      AND tipo_natureza = 'E';


----------------
-- Ticket #14551
----------------

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2475
          , 291
          , 'FLSaidaAutorizacaoAbastecimento.php'
          , 'saida'
          , 5
          , ''
          , 'Saída por Autorização de Abastecimento'
          );

----------------
-- Ticket #14551
----------------

CREATE TABLE almoxarifado.lancamento_autorizacao(
    cod_lancamento          INTEGER         NOT NULL,
    cod_item                INTEGER         NOT NULL,
    cod_marca               INTEGER         NOT NULL,
    cod_almoxarifado        INTEGER         NOT NULL,
    cod_centro              INTEGER         NOT NULL,
    exercicio               CHAR(4)         NOT NULL,
    cod_autorizacao         INTEGER         NOT NULL,
    CONSTRAINT pk_lancamento_autorizacao    PRIMARY KEY                                 (cod_lancamento, cod_item, cod_marca, cod_almoxarifado, cod_centro),
    CONSTRAINT fk_lancamento_autorizacao_1  FOREIGN KEY                                 (cod_lancamento, cod_item, cod_marca, cod_almoxarifado, cod_centro)
                                            REFERENCES almoxarifado.lancamento_material (cod_lancamento, cod_item, cod_marca, cod_almoxarifado, cod_centro),
    CONSTRAINT fk_lancamento_autorizacao_2  FOREIGN KEY                                 (exercicio, cod_autorizacao)
                                            REFERENCES frota.autorizacao                (exercicio, cod_autorizacao)
);

GRANT ALL ON almoxarifado.lancamento_autorizacao TO GROUP urbem;


INSERT
  INTO almoxarifado.natureza
     ( cod_natureza
     , tipo_natureza
     , descricao
     )
VALUES ( 12
     , 'S'
     , 'Saída por Autorização de Abastecimento'
     );

----------------
-- Ticket #14502
----------------

INSERT INTO administracao.funcionalidade
          ( cod_funcionalidade
          , cod_modulo
          , nom_funcionalidade
          , nom_diretorio
          , ordem )
     VALUES ( 453
          , 7
          , 'Posto'
          , 'instancias/posto/'
          , 5
          );
INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2466
          , 453
          , 'FMManterPosto.php'
          , 'incluir'
          , 1
          , ''
          , 'Incluir Posto'
          );
INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2467
          , 453
          , 'FLManterPosto.php'
          , 'alterar'
          , 2
          , ''
          , 'Alterar Posto'
          );
INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2468
          , 453
          , 'FLManterPosto.php'
          , 'excluir'
          , 3
          , ''
          , 'Excluir Posto'
          );

CREATE TABLE frota.posto(
    cgm_posto       INTEGER         NOT NULL,
    interno         BOOLEAN         NOT NULL,
    ativo           BOOLEAN         NOT NULL,
    CONSTRAINT pk_posto             PRIMARY KEY                         (cgm_posto),
    CONSTRAINT fk_posto_1           FOREIGN KEY                         (cgm_posto)
                                    REFERENCES sw_cgm_pessoa_juridica   (numcgm)
);

GRANT ALL ON frota.posto TO GROUP urbem;

-- Migra os postos existentes na tabela autorizacao
INSERT INTO frota.posto (cgm_posto, interno, ativo)
SELECT  cgm_fornecedor, false, true
FROM    frota.autorizacao
WHERE   NOT EXISTS ( SELECT 1 FROM frota.posto WHERE posto.cgm_posto = autorizacao.cgm_fornecedor )
GROUP BY cgm_fornecedor
;


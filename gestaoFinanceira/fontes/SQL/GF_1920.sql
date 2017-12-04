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
* $Id: GF_1920.sql 59612 2014-09-02 12:00:51Z gelson $
*
* Versão 1.92.0
*/

----------------
-- Ticket #13576
----------------

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     SELECT 2327
          , 168
          ,'FLMapaRecursos.php'
          , 'imprimir'
          , 45
          , ''
          , 'Relatório Mapa de Recursos'
      WHERE 0 = (
                  SELECT COUNT(1)
                    FROM administracao.acao
                   WHERE cod_acao = 2327
                );

INSERT INTO administracao.relatorio
         ( cod_gestao
         , cod_modulo
         , cod_relatorio
         , nom_relatorio
         , arquivo )
    SELECT 2
         , 8
         , 5
         ,'Relatório Mapa de Recursos'
         , 'relatorioMapaRecursos.rptdesign'
     WHERE 0 = (
                 SELECT COUNT(1)
                   FROM administracao.relatorio
                  WHERE cod_relatorio = 5
                    AND cod_modulo    = 8
                    AND cod_gestao    = 2
               );


----------------
-- Ticket #13685
----------------

INSERT INTO administracao.relatorio
         ( cod_gestao
         , cod_modulo
         , cod_relatorio
         , nom_relatorio
         , arquivo )
    SELECT 2
         , 10
         , 6
         ,'Prestação de Contas'
         , 'notaPrestacaoContas.rptdesign'
     WHERE 0 = (
                 SELECT COUNT(1)
                   FROM administracao.relatorio
                  WHERE cod_relatorio = 6
                    AND cod_modulo    = 10
                    AND cod_gestao    = 2
               );



-- ticket #11654 ---

DELETE
  FROM administracao.auditoria
 WHERE cod_acao = 1675;

DELETE
  FROM administracao.permissao
 WHERE cod_acao = 1675;

DELETE
  FROM administracao.acao
 WHERE cod_acao = 1675;


insert into 
    administracao.funcionalidade 
     SELECT 379
          , 10
          , 'Adiantamento / Subvenções'
          , 'instancias/adiantamentos/'
          , 6
      WHERE 0 = (
                  SELECT COUNT(1)
                    FROM administracao.funcionalidade
                   WHERE cod_funcionalidade = 379
                );

insert INTO 
    administracao.acao 
     SELECT 1674
          , 379
          ,'FLManterAdiantamentosSubvencoes.php'
          , 'incluir'
          , 1
          , ''
          , 'Prestação de Contas'
      WHERE 0 = (
                  SELECT COUNT(1)
                    FROM administracao.acao
                   WHERE cod_acao = 1674
                );



insert INTO 
    administracao.acao 
     SELECT 1679
          , 379
          ,'FLManterAdiantamentosSubvencoes.php'
          , 'consultar'
          , 3
          , ''
          , 'Consultar Prestação de Contas'
      WHERE 0 = (
                  SELECT COUNT(1)
                    FROM administracao.acao
                   WHERE cod_acao = 1679
                );

insert INTO 
    administracao.acao
     SELECT 1831
          , 379
          ,'FMManterResponsaveisAdiantamento.php'
          , 'incluir'
          , 4
          , ''
          , 'Incluir Responsáveis por Adiantamento'
      WHERE 0 = (
                  SELECT COUNT(1)
                    FROM administracao.acao
                   WHERE cod_acao = 1831
                ); 

insert INTO 
    administracao.acao 
     SELECT 1845
          , 379
          ,'FLManterResponsaveisAdiantamento.php'
          , 'alterar'
          , 5
          , ''
          , 'Alterar Responsáveis por Adiantamento'
      WHERE 0 = (
                  SELECT COUNT(1)
                    FROM administracao.acao
                   WHERE cod_acao = 1845
                );

insert INTO 
    administracao.acao 
     SELECT 1682
          , 379
          ,'FLManterResponsaveisAdiantamento.php'
          , 'excluir'
          , 6
          , ''
          , 'Excluir Responsáveis por Adiantamento'
      WHERE 0 = (
                  SELECT COUNT(1)
                    FROM administracao.acao
                   WHERE cod_acao = 1682
                );



----------------
-- Ticket #13584
----------------

ALTER TABLE tesouraria.recibo_extra_transferencia DROP CONSTRAINT pk_recibo_extra_transferencia;

ALTER TABLE tesouraria.recibo_extra_transferencia ADD  CONSTRAINT pk_recibo_extra_transferencia PRIMARY KEY (cod_recibo_extra, cod_entidade, exercicio, tipo_recibo, tipo, cod_lote);



----------------
-- Ticket #13703
----------------



INSERT INTO administracao.acao VALUES(2376, 319, 'FLReciboReceitaExtra.php','consultar','4','','Consultar Recibo');

INSERT INTO administracao.acao VALUES(2377, 320, 'FLReciboDespesaExtra.php','consultar','4','','Consultar Recibo');


----------------
-- Ticket #13743
----------------

ALTER TABLE empenho.item_prestacao_contas ALTER COLUMN num_documento DROP NOT NULL;



----------------
-- Ticket #13581
----------------

INSERT INTO administracao.relatorio
         ( cod_gestao
         , cod_modulo
         , cod_relatorio
         , nom_relatorio
         , arquivo )
    SELECT 2
         , 9
         , 2
         ,'Anexo 13'
         , 'anexo13.rptdesign'
     WHERE 0 = (
                 SELECT COUNT(1)
                   FROM administracao.relatorio
                  WHERE cod_relatorio = 2
                    AND cod_modulo    = 9
                    AND cod_gestao    = 2
               );

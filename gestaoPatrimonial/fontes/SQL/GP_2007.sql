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
* $Id:$
*
* Versão 2.00.7
*/

----------------
-- Ticket #18364
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
  ( 2821
  , 356
  , 'FMManterHomologacao.php'
  , 'manterHomolog'
  , 11
  , ''
  , 'Manter Homologação'
  );

UPDATE administracao.acao
   SET ordem = 17
 WHERE cod_acao = 2821
     ;

ALTER TABLE compras.homologacao DROP CONSTRAINT fk_homologacao_1;
ALTER TABLE compras.homologacao ADD CONSTRAINT fk_homologacao_1 FOREIGN KEY                        (exercicio_cotacao, cod_cotacao, cod_item, cgm_fornecedor, lote)
                                                                REFERENCES compras.julgamento_item (exercicio, cod_cotacao, cod_item, cgm_fornecedor, lote);

UPDATE administracao.acao
   SET parametro = 'excluir'
     , nom_acao = 'Excluir Inventário'
 WHERE cod_acao = 2405
         ;



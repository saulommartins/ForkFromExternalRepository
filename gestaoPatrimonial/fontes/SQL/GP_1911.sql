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
* $Id:  $
*
* Versão 1.91.1
*/

-- Estas acoes nao estao de acordo com o contexto da ordem de compra
-- nao serao utilizadas pelo sistema

--   INSERT INTO administracao.funcionalidade
--               (cod_funcionalidade
--             , cod_modulo
--             , nom_funcionalidade
--             , nom_diretorio
--             , ordem)
--        SELECT 352
--             , 35
--             , 'Nota de Compra'
--             , 'instancias/notaCompra/'
--             , 24
--         WHERE 0 = (SELECT COUNT(1)
--                      FROM administracao.funcionalidade
--                     WHERE cod_funcionalidade = 352);
--
--
--   INSERT INTO administracao.acao
--               (cod_acao
--             , cod_funcionalidade
--             , nom_arquivo
--             , parametro
--             , ordem
--             , complemento_acao
--             , nom_acao)
--        VALUES (1697
--             , 352
--             , 'FMManterNotaCompra.php'
--             , 'incluir'
--             , 1
--             , ''
--             , 'Incluir');
--
--   INSERT INTO administracao.acao
--               (cod_acao
--             , cod_funcionalidade
--             , nom_arquivo
--             , parametro
--             , ordem
--             , complemento_acao
--             , nom_acao)
--        VALUES (1698
--             , 352
--             , 'FLManterNotaCompra.php'
--             , 'consultar'
--             , 2
--             , ''
--             , 'Consultar');
--
--   INSERT INTO administracao.acao
--               (cod_acao
--             , cod_funcionalidade
--             , nom_arquivo
--             , parametro
--             , ordem
--             , complemento_acao
--             , nom_acao)
--        VALUES (1699
--             , 352
--             , 'LSManterNotaCompra.php'
--             , 'excluir'
--             , 3
--             , ''
--             , 'Excluir');






----------------
-- Ticket #13937
----------------

CREATE INDEX ix_lancamento_material_1    ON almoxarifado.lancamento_material(cod_item);

CREATE INDEX ix_catalogo_classificacao_1 ON almoxarifado.catalogo_classificacao(cod_catalogo);


----------------
-- Ticket #12845
----------------

INSERT INTO administracao.relatorio 
         ( cod_gestao 
         , cod_modulo 
         , cod_relatorio 
         , nom_relatorio 
         , arquivo ) 
    VALUES ( 3 
         , 37 
         , 3 
         , 'Mapa de Compra' 
         , 'mapaComparativoProposta.rptdesign' 
         );

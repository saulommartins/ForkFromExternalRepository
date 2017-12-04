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
* Pendencias.
*/

INSERT INTO administracao.funcionalidade
            (cod_funcionalidade
          , cod_modulo
          , nom_funcionalidade
          , nom_diretorio
          , ordem)
     SELECT 379
          , 10
          , 'Adiantamentos / Subvenções'
          , 'instancias/adiantamentos/'
          , 6
      WHERE 0 = (SELECT count(*)
                   FROM administracao.funcionalidade
                  WHERE cod_funcionalidade = 379);

 INSERT INTO administracao.acao
            (cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao)
     VALUES (1674
          , 379
          , 'FLManterAdiantamentosSubvencoes.php'
          , 'incluir'
          , 4
          , ''
          , 'Prestação de Contas');

INSERT INTO administracao.acao
            (cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao)
     VALUES (1845
          , 379
          , 'FLManterAdiantamentosSubvencoes.php'
          , 'anular'
          , 5
          , ''
          , 'Anular Prestação de Contas');

 INSERT INTO administracao.acao
            (cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao)
     VALUES (1831
          , 379
          , 'FLManterAdiantamentosSubvencoes.php'
          , 'consultar'
          , 6
          , ''
          , 'Consultar Prestação de Contas');


--
--
--

 INSERT INTO administracao.funcionalidade
            (cod_funcionalidade
          , cod_modulo
          , nom_funcionalidade
          , nom_diretorio
          , ordem)
     SELECT 379
          , 10
          , 'Adiantamentos / Subvenções'
          , 'instancias/adiantamentos/'
          , 6
      WHERE 0 = (SELECT count(*)
                   FROM administracao.funcionalidade
                  WHERE cod_funcionalidade = 379);


 INSERT INTO administracao.acao
            (cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao)
     VALUES (1675
          , 379
          , 'FMManterResponsaveisAdiantamento.php'
          , 'incluir'
          , 1
          , ''
          , 'Incluir Responsáveis');

 INSERT INTO administracao.acao
            (cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao)
     VALUES (1679
          , 379
          , 'FLManterResponsaveisAdiantamento.php'
          , 'alterar'
          , 2
          , ''
          , 'Alterar Responsáveis');

INSERT INTO administracao.acao
            (cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao)
     VALUES (1682
          , 379
          , 'FLManterResponsaveisAdiantamento.php'
          , 'excluir'
          , 3
          , ''
          , 'Excluir Responsáveis');


INSERT INTO administracao.acao
            (cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao)
     VALUES (1826
          , 267
          , 'FMManterArrecadacaoCarne.php'
          , 'incluir'
          , 9
          , ''
          , 'Arrecadação Por Carnê');

INSERT INTO administracao.acao
            (cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao)
     VALUES (1827
          , 267
          , 'FLManterArrecadacaoCarne.php'
          , 'estornar'
          , 10
          , ''
          , 'Estorno de Arrecadação Por Carnê');



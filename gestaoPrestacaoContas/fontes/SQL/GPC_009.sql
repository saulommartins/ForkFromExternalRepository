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
* URBEM SoluÃ§Ãµes de GestÃ£o PÃºblica Ltda
* www.urbem.cnm.org.br
*
* $Revision: 28350 $
* $Name$
* $Author: gris $
* $Date: 2008-03-05 09:57:44 -0300 (Qua, 05 Mar 2008) $
*
* VersÃ£o 009.
*/


-------------
--Ticket #12408
-------------

UPDATE administracao.acao SET nom_acao = 'Anexo I', ordem = 1 WHERE cod_acao = 1501;
UPDATE administracao.acao SET nom_acao = 'Anexo II', ordem = 2 WHERE cod_acao = 1502;
UPDATE administracao.acao SET nom_acao = 'Anexo III', ordem = 3 WHERE cod_acao = 1503;
UPDATE administracao.acao SET nom_acao = 'Anexo VI', ordem = 6 WHERE cod_acao = 2190;
UPDATE administracao.acao SET nom_acao = 'Anexo IV', ordem = 4 WHERE cod_acao = 1507;

INSERT INTO administracao.acao
            (cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao)
     VALUES (1506
          , 315
          , 'FLModelosRGF.php'
          , 'anexo3'
          , 3
          , ''
          , 'Anexo III');



-------------
--Ticket #12538
-------------

INSERT INTO administracao.funcionalidade
            (cod_funcionalidade
          , cod_modulo
          , nom_funcionalidade
          , nom_diretorio
          , ordem)
     SELECT 411
          , 36
          , 'AMF'
          , 'instancias/relatorios/'
          , 4
      WHERE 0 = (SELECT count(*)
                   FROM administracao.funcionalidade
                  WHERE cod_funcionalidade = 411);

INSERT INTO administracao.acao
            (cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao)
     VALUES (2243
          , 411
          , 'FLModelosAMF.php'
          , 'demons5'
          , 5
          , ''
          , 'Demonstrativo V');

INSERT INTO administracao.relatorio
            (cod_gestao
          , cod_modulo
          , cod_relatorio
          , nom_relatorio
          , arquivo)
     VALUES (6
          , 36
          , 30
          , 'AMF - Demonstrativo V'
          , 'AMFDemonstrativo5.rptdesign');

-------------
--Ticket #12541
-------------


-------------
--Ticket #12181
-------------

INSERT INTO administracao.relatorio
            (cod_gestao
          , cod_modulo
          , cod_relatorio
          , nom_relatorio
          , arquivo)
     VALUES (6
          , 36
          , 29
          , 'RREO - Anexo XI - Demonstrativo das Receitas de Operações de Crédito e Despesas de Capital', 'RREOAnexo11.rptdesign');

-------------
--Ticket #12386
-------------

INSERT INTO administracao.acao
            (cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao)
     VALUES (2230
          , 314
          , 'FLModelosRREO.php'
          , 'anexo11'
          , 11
          , ''
          , 'Anexo XI');

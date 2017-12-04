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
* VersÃ£o 011.
*/

INSERT INTO administracao.acao
            (cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao)
     VALUES (2220
          , 314
          , 'FLModelosRREO.php'
          , 'anexo16'
          , 16
          , ''
          , 'Anexo XVI');

----------------
-- Ticket #12815
----------------

INSERT INTO administracao.acao 
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , nom_acao
          )
     VALUES ( 2259
          , 315
          , 'FLModelosRGF.php'
          , 'anexo6'
          , 6
          ,'Anexo VI' 
          );

INSERT INTO administracao.relatorio 
          ( cod_gestao
          , cod_modulo
          , cod_relatorio
          , nom_relatorio
          , arquivo
          )
     VALUES ( 6
          , 36
          , 33
          , 'RGF - Anexo VI - Demonstrativo da Despesa com Pessoal'
          , 'RGFAnexo6.rptdesign' 
          );


----------------
-- Ticket #12816
----------------

INSERT INTO administracao.configuracao 
          ( exercicio
          , cod_modulo
          , parametro
          , valor
          )
     VALUES ( '2008'
          , 36
          , 'stn_anexo10_porcentagem'
          , 0 
          ); 


----------------
-- Ticket #12869
----------------

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , nom_acao)
     VALUES ( 2264
          , 314
          , 'FLModelosRREO.php'
          , 'anexo18'
          , 18
          , 'Anexo XVIII'
          );

INSERT INTO administracao.relatorio
          ( cod_gestao
          , cod_modulo
          , cod_relatorio
          , nom_relatorio
          , arquivo)
     VALUES ( 6
          , 36
          , 34
          , 'RREO - Anexo XVIII - Demonstrativo Simplificado do Relatório Resumido da Execução Orçamentária'
          , 'RREOAnexo18.rptdesign'
          );


----------------
-- Ticket #12872
----------------
INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , nom_acao)
     VALUES ( 2265
          , 315
          , 'FLModelosRGF.php'
          , 'anexo7'
          , 7
          , 'Anexo VII')

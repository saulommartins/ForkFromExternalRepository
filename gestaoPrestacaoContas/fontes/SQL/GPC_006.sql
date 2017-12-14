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
* Versão 005.
*/

 alter table  stn.vinculo_recurso drop constraint pk_vinculo_recurso;
 alter table  stn.vinculo_recurso add constraint pk_vinculo_recurso PRIMARY KEY (exercicio, cod_entidade, num_orgao, num_unidade, cod_recurso, cod_vinculo);

-------------
-- Ticket #12351
-------------

insert into stn.vinculo_stn_recurso
            (cod_vinculo
          , descricao)
     values (3
          , 'Salário Educação');

insert into stn.vinculo_stn_recurso
            (cod_vinculo
          , descricao)
     values (4
          , 'Operações de Crédito');

insert into stn.vinculo_stn_recurso
            (cod_vinculo
          , descricao)
     values (5
          , 'Outros Recursos MDE');


insert into administracao.acao
           (cod_acao
         , cod_funcionalidade
         , nom_arquivo
         , parametro
         , ordem
         , complemento_acao
         , nom_acao)
    values (2226
         , 406
         , 'FMManterRecurso.php'
         , 3
         , 3
         , ''
         , 'Vincular Recurso com Salário Educação');

insert into administracao.acao
            (cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao)
     values (2227
          , 406
          , 'FMManterRecurso.php'
          , 4
          , 4
          , ''
          , 'Vincular Recurso com Operações de Crédito MDE');

insert into administracao.acao
            (cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao)
     values (2228
          , 406
          , 'FMManterRecurso.php'
          , 5
          , 5
          , ''
          , 'Vincular Recurso com Outros Recursos da Educação');


INSERT INTO stn.vinculo_stn_recurso
            (cod_vinculo
          , descricao)
     VALUES (6
          , 'Despesas com Saúde');

INSERT INTO administracao.acao
            (cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao)
     VALUES (2231
          , 406
          , 'FMManterRecurso.php'
          , '3'
          , 3
          , ''
          , 'Vincular Recurso da Saúde');

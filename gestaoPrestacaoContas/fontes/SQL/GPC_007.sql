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
* Versão 007.
*/

ALTER TABLE administracao.relatorio ALTER COLUMN nom_relatorio TYPE VARCHAR(200);
UPDATE administracao.relatorio
   SET nom_relatorio = 'RREO - ANEXO XIV - DEMONSTRATIVO DA RECEITA DE ALIENAÇÃO DE ATIVOS E APLICAÇÃO DOS RECURSOS'
     , arquivo   = 'RREOAnexo14.rptdesign'
 WHERE cod_gestao = 6
   AND cod_modulo = 36
   AND cod_relatorio = 23;

INSERT INTO administracao.acao
            (cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao)
     VALUES (2225
          , 314
          , 'FLModelosRREO.php'
          , 'anexo9'
          , 9
          , ''
          , 'Anexo IX');

-------------
--Ticket #12412
-------------

 INSERT INTO administracao.relatorio
             (cod_gestao
           , cod_modulo
           , cod_relatorio
           , nom_relatorio
           , arquivo)
      VALUES (6
           , 36
           , 26
           , 'RREO - Anexo IX -Demonstrativo dos Restos a Pagar por Poder e Órgão'
           , 'RREOAnexo9.rptdesign');



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
     VALUES (2189
          , 314
          , 'FLModelosRREO.php'
          , 'anexo14'
          , 14
          , ''
          , 'Anexo XIV');

INSERT INTO administracao.acao
            (cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao)
     VALUES (2170
          , 315
          , 'FLModelosRGF.php'
          , 'anexo5'
          , 5
          , ''
          , 'Anexo V');

INSERT INTO administracao.acao
            (cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao)
     VALUES (1505
          , 315
          , 'FLModelosRGF.php'
          , 'anexo2'
          , 2
          , ''
          , 'Anexo II');

INSERT INTO administracao.acao
            (cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao)
     VALUES (2219
          , 314
          , 'FLModelosRREO.php'
          , 'anexo5'
          , 5
          , ''
          , 'Anexo V');

-------------
--Ticket #12424
-------------

INSERT INTO administracao.relatorio
            (cod_gestao
          , cod_modulo
          , cod_relatorio
          , nom_relatorio
          , arquivo)
     VALUES (6
          , 36
          , 28
          , 'RREO - Anexo V - Demonstrativo das Receitas e Despesas Previdenciárias do Regime Próprio dos Servidores Públicos'
          , 'RREOAnexo5.rptdesign');


-------------
--Ticket #12481
-------------

CREATE TABLE stn.recurso_rreo_anexo_14(
    exercicio varchar(4) NOT NULL,
    cod_recurso integer NOT NULL,
  CONSTRAINT pk_recurso_rreo_anexo_14 PRIMARY KEY(exercicio, cod_recurso),
  CONSTRAINT fk_recurso_rreo_anexo_14 FOREIGN KEY(exercicio,cod_recurso) REFERENCES orcamento.recurso(exercicio,cod_recurso)
);

GRANT INSERT, DELETE, UPDATE, SELECT ON stn.recurso_rreo_anexo_14 TO GROUP urbem;

-------------
--Ticket #12484
-------------

UPDATE administracao.acao
   SET parametro = '6'
     , ordem = 6
 WHERE cod_acao = 2231;

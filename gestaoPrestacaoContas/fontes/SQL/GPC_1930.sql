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
* Versão 1.93.0
*/

----------------
-- Ticket #14049
----------------

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2421
          , 406
          , 'FMManterParametrosRREO13.php'
          , 'incluir'
          , 12
          , ''
          , 'Parametros Anexo 13 RREO'
          );

CREATE TABLE stn.rreo_anexo_13 (
    exercicio                   CHAR(4)         NOT NULL,
    cod_entidade                INTEGER         NOT NULL,
    ano                         CHAR(4)         NOT NULL,
    vl_receita_previdenciaria   NUMERIC(14,2)   NOT NULL,
    vl_despesa_previdenciaria   NUMERIC(14,2)   NOT NULL,
    vl_saldo_financeiro         NUMERIC(14,2)   NOT NULL,
    CONSTRAINT pk_rreo_anexo_13                 PRIMARY KEY                     (exercicio,cod_entidade,ano),
    CONSTRAINT fk_rreo_anexo_13                 FOREIGN KEY                     (exercicio,cod_entidade)
                                                REFERENCES orcamento.entidade   (exercicio,cod_entidade)

);

GRANT ALL ON stn.rreo_anexo_13 TO GROUP urbem;

INSERT INTO administracao.relatorio
         ( cod_gestao
         , cod_modulo
         , cod_relatorio
         , nom_relatorio
         , arquivo )
    VALUES ( 6
         , 36
         , 35
         , 'RREO - Anexo XIII - Dem. Projeção Atuarial do RPPS'
         , 'RREOAnexo13.rptdesign'
         );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2422
          , 314
          , 'OCGeraRREOAnexo13.php'
          , 'anexo13'
          , 13
          , ''
          , 'Anexo XIII'
          );


----------------
-- Ticket #16006
----------------

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2766
          , 411
          , 'FLModelosAMF.php'
          , 'demons4'
          , 4
          , ''
          , 'Demonstrativo IV'
          );

INSERT INTO administracao.relatorio
          ( cod_gestao
          , cod_modulo
          , cod_relatorio
          , nom_relatorio
          , arquivo )
     VALUES ( 6
          , 36
          , 40
          , 'AMF - Demonstrativo IV'
          , 'AMFDemonstrativo4.rptdesign'
          );


----------------
-- Ticket #15964
----------------

INSERT INTO administracao.relatorio
          ( cod_gestao
          , cod_modulo
          , cod_relatorio
          , nom_relatorio
          , arquivo )
     VALUES ( 6
          , 36
          , 41
          , 'AMF - Demonstrativo III'
          , 'AMFDemonstrativo3.rptdesign'
          );

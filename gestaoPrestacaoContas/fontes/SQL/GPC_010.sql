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
* VersÃ£o 010.
*/


INSERT INTO administracao.acao
            (cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao)
     VALUES (2195
          , 314
          , 'FLModelosRREO.php'
          , 'anexo10'
          , 10
          , ''
          , 'Anexo X');


-------------
--Ticket #11555
-------------

INSERT INTO administracao.relatorio
             (cod_gestao
           , cod_modulo
           , cod_relatorio
           , nom_relatorio
           , arquivo)
      VALUES (6
           , 36
           , 27
           , 'RREO - Anexo X - Demonstrativo das Receitas e Despesas com Manutenção e Desenvolvimento do Ensino - MDE'
           , 'RREOAnexo10.rptdesign');

 INSERT INTO administracao.acao (cod_acao
                              , cod_funcionalidade
                              , nom_arquivo
                              , parametro
                              , ordem
                              , complemento_acao
                              , nom_acao)
                         VALUES (2214
                              , 314
                              , 'FLModelosRREO.php'
                              , 'anexo7'
                              , 7
                              , ''
                              , 'Anexo VII');

INSERT INTO administracao.relatorio
           (cod_gestao
         , cod_modulo
         , cod_relatorio
         , nom_relatorio
         , arquivo)
    VALUES (6
         , 36
         , 25
         , 'RREO - Anexo VII - Demonstrativo do Resultado Primário'
         , 'RREOAnexo7.rptdesign');

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
     VALUES (1504
          , 315
          , 'FLModelosRGF.php'
          , 'anexo1'
          , 1
          , ''
          , 'Anexo I');


-------------
--Ticket #12166
-------------

UPDATE administracao.relatorio
   SET nom_relatorio = 'RGF - Anexo I - Demonstrativo da Despesa com Pessoal'
 WHERE cod_gestao = 6
   AND cod_modulo = 36
   AND cod_relatorio = 1;

UPDATE administracao.relatorio
   SET nom_relatorio = 'RGF - Anexo VII - Demonstrativo dos Limites'
 WHERE cod_gestao = 6
   AND cod_modulo = 36
   AND cod_relatorio = 7;

INSERT INTO administracao.acao
            (cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao)
     VALUES (2244
          , 411
          , 'FLModelosAMF.php'
          , 'demons6'
          , 5
          , ''
          ,'Demonstrativo VI');

INSERT INTO administracao.relatorio
           (cod_gestao
          , cod_modulo
          , cod_relatorio
          , nom_relatorio
          , arquivo)
     VALUES (6
          , 36
          , 31
          , 'AMF - Demonstrativo VI'
          ,'AMFDemonstrativo6.rptdesign');

---------------
-- Ticket #12745
---------------

INSERT INTO administracao.relatorio
           (cod_gestao
         , cod_modulo
         , cod_relatorio
         , nom_relatorio
         , arquivo)
    VALUES (6
         , 36
         , 32
         , 'Anexo XVI'
         , 'RREOAnexo16.rptdesign');


INSERT INTO stn.vinculo_stn_recurso
            (cod_vinculo
          , descricao)
     VALUES (7
          , 'Recurso Transferência SUS');

INSERT INTO stn.vinculo_stn_recurso
            (cod_vinculo
          , descricao)
     VALUES (8
          , 'Recurso Operações Crédito Saúde');

INSERT INTO stn.vinculo_stn_recurso
            (cod_vinculo
          , descricao)
     VALUES (9
          , 'Outros Recursos Saúde');

INSERT INTO administracao.acao
            (cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao)
     VALUES (2256
          , 406
          , 'FMManterRecurso.php'
          , '7'
          , 7
          , ''
          , 'Vincular Recurso Transferencias SUS');

INSERT INTO administracao.acao
            (cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao)
     VALUES (2257
          , 406
          , 'FMManterRecurso.php'
          , '8'
          , 8
          , ''
          , 'Vincular Recurso Operações Crédito Saúde');

INSERT INTO administracao.acao
            (cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao)
     VALUES (2258
          , 406
          , 'FMManterRecurso.php'
          , '9'
          , 9
          , ''
          , 'Vincular Outros Recursos Saúde');



-- Solicitado por Henrique pelo Jabber
INSERT INTO administracao.configuracao VALUES ( '2008',36,'stn_anexo16_porcentagem',0 );

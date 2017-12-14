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
*
* Script de DDL e DML
*
* Versao 2.02.8
*
* Fabio Bertoldi - 20140709
*
*/

----------------
-- Ticket #21763
----------------

INSERT
  INTO administracao.funcionalidade
     ( cod_funcionalidade
     , cod_modulo
     , nom_funcionalidade
     , nom_diretorio
     , ordem
     , ativo
     )
VALUES
     ( 484
     , 55
     , 'Relatórios'
     , 'instancias/relatorios/'
     , 4
     , TRUE
     );

INSERT
  INTO administracao.acao
     ( cod_acao
     , cod_funcionalidade
     , nom_arquivo
     , parametro
     , ordem
     , complemento_acao
     , nom_acao
     , ativo
     )
VALUES
     ( 2967
     , 484
     , 'FLRelatorioDemonstrativoPessoal.php'
     , 'consultar'
     , 20
     , ''
     , 'Quadro Demonstrativo dos Gastos com Pessoal'
     , TRUE
     );

INSERT
  INTO administracao.relatorio
     ( cod_gestao
     , cod_modulo
     , cod_relatorio
     , nom_relatorio
     , arquivo )
     VALUES
     ( 6
     , 55
     , 1
     , 'Relatório do Quadro Demonstrativo dos Gastos com Pessoal'
     , 'TCEMGDemonstrativoPessoal.rptdesign'
     );


----------------
-- Ticket #21759
----------------

INSERT
  INTO administracao.acao
     ( cod_acao
     , cod_funcionalidade
     , nom_arquivo
     , parametro
     , ordem
     , complemento_acao
     , nom_acao
     , ativo
     )
     VALUES
     ( 2968
     , 484
     , 'FLRelatorioAnexoIII.php'
     , 'emitir'
     , 3
     , ''
     , 'Anexo III'
     , TRUE
     );

INSERT
  INTO administracao.relatorio
     ( cod_gestao
     , cod_modulo
     , cod_relatorio
     , nom_relatorio
     , arquivo )
     VALUES
     ( 6
     , 55
     , 3
     , 'Anexo III'
     , 'TCEMGRelatorioAnexoIII.rptdesign'
     );


----------------
-- Ticket #21834
----------------

ALTER TABLE tcepb.obras ADD COLUMN vl_obra numeric(14,2);


----------------
-- Ticket #21758
----------------

INSERT
  INTO administracao.acao
     ( cod_acao
     , cod_funcionalidade
     , nom_arquivo
     , parametro
     , ordem
     , complemento_acao
     , nom_acao
     , ativo
     )
     VALUES
     ( 2969
     , 484
     , 'FLRelatorioAnexoII.php'
     , 'consultar'
     , 2
     , ''
     , 'Anexo II'
     , TRUE
     );

INSERT
  INTO administracao.relatorio
     ( cod_gestao
     , cod_modulo
     , cod_relatorio
     , nom_relatorio
     , arquivo )
     VALUES
     ( 6
     , 55
     , 2
     , 'AnexoII'
     , 'TCEMGRelatorioAnexoII.rptdesign'
     );


----------------
-- Ticket #21764
----------------

INSERT
  INTO administracao.acao
     ( cod_acao
     , cod_funcionalidade
     , nom_arquivo
     , parametro
     , ordem
     , complemento_acao
     , nom_acao
     , ativo
     )
     VALUES
     ( 2970
     , 484
     , 'FLRelatorioDemonstrativoSaude.php'
     , 'imprimir'
     , 21
     , ''
     , 'Demons. da Aplicação nas Ações e Serviços Púb. de Saúde'
     , TRUE
     );

INSERT
  INTO administracao.relatorio
     ( cod_gestao
     , cod_modulo
     , cod_relatorio
     , nom_relatorio
     , arquivo )
     VALUES
     ( 6
     , 55
     , 4
     , 'Demonstrativo da Aplicação nas Ações e Serviços Públicos de Saúde'
     , 'demonstrativoSaude.rptdesign'
     );


----------------
-- Ticket #21873
----------------

INSERT
  INTO administracao.acao
     ( cod_acao
     , cod_funcionalidade
     , nom_arquivo
     , parametro
     , ordem
     , complemento_acao
     , nom_acao
     , ativo
     )
     VALUES
     ( 2971
     , 274
     , 'FLExtratoContaCorrente.php'
     , 'consultar'
     , 11
     , ''
     , 'Extrato de Conta C/c'
     , TRUE
     );


----------------
-- Ticket #21762
----------------

INSERT
  INTO administracao.acao
     ( cod_acao
     , cod_funcionalidade
     , nom_arquivo
     , parametro
     , ordem
     , complemento_acao
     , nom_acao
     , ativo
     )
     VALUES
     ( 2972
     , 484
     , 'FLRelatorioAnexo4.php'
     , 'consultar'
     , 4
     , ''
     , 'Anexo IV'
     , TRUE
     );

INSERT
  INTO administracao.relatorio
     ( cod_gestao
     , cod_modulo
     , cod_relatorio
     , nom_relatorio
     , arquivo )
     VALUES
     ( 6
     , 55
     , 5
     , 'Anexo IV'
     , 'Anexo4.rptdesign'
     );


----------------
-- Ticket #21757
----------------

INSERT
  INTO administracao.acao
     ( cod_acao
     , cod_funcionalidade
     , nom_arquivo
     , parametro
     , ordem
     , complemento_acao
     , nom_acao
     , ativo
     )
     VALUES
     ( 2974
     , 484
     , 'FLRelatorioAnexoI.php'
     , 'consultar'
     , 1
     , ''
     , 'Anexo I'
     , TRUE
     );

INSERT
  INTO administracao.relatorio
     ( cod_gestao
     , cod_modulo
     , cod_relatorio
     , nom_relatorio
     , arquivo )
     VALUES
     ( 6
     , 55
     , 6
     , 'AnexoI'
     , 'TCEMGRelatorioAnexoI.rptdesign'
     );


----------------
-- Ticket #21760
----------------

INSERT
  INTO administracao.acao
     ( cod_acao
     , cod_funcionalidade
     , nom_arquivo
     , parametro
     , ordem
     , complemento_acao
     , nom_acao
     , ativo
     )
     VALUES
     ( 2976
     , 484
     , 'FLRelatorioAnexoIIIA.php'
     , 'emitir'
     , 3
     , ''
     , 'Anexo III A'
     , TRUE
     );


----------------
-- Ticket #21956
----------------

INSERT
  INTO tcepb.tipo_obra
     ( exercicio
     , cod_tipo
     , descricao
     )
SELECT '2014' AS exercicio
     , cod_tipo
     , descricao
  FROM tcepb.tipo_obra
 WHERE exercicio = '2009'
     ;

INSERT
  INTO tcepb.tipo_situacao
     ( exercicio
     , cod_tipo
     , descricao
     )
SELECT '2014' AS exercicio
     , cod_tipo
     , descricao
  FROM tcepb.tipo_situacao
 WHERE exercicio = '2009'
     ;

INSERT
  INTO tcepb.tipo_fonte_obras
     ( exercicio
     , cod_tipo
     , descricao
     )
SELECT '2014' AS exercicio
     , cod_tipo
     , descricao
  FROM tcepb.tipo_fonte_obras
 WHERE exercicio = '2009'
     ;

INSERT
  INTO tcepb.tipo_categoria_obra
     ( exercicio
     , cod_tipo
     , descricao
     )
SELECT '2014' AS exercicio
     , cod_tipo
     , descricao
  FROM tcepb.tipo_categoria_obra
 WHERE exercicio = '2009'
     ;


----------------
-- Ticket #
----------------

INSERT
  INTO administracao.acao
     ( cod_acao
     , cod_funcionalidade
     , nom_arquivo
     , parametro
     , ordem
     , complemento_acao
     , nom_acao
     , ativo
     )
     VALUES
     ( 2977
     , 484
     , 'FLRelatorioDemonstrativoRCL.php'
     , 'consultar'
     , 11
     , ''
     , 'Demonstrativo RCL'
     , TRUE
     );

INSERT
  INTO administracao.relatorio
     ( cod_gestao
     , cod_modulo
     , cod_relatorio
     , nom_relatorio
     , arquivo )
     VALUES
     ( 6
     , 55
     , 7
     , 'Demonstrativo RCL'
     , 'LHTCEMGRelatorioDemonstrativoRCL.php'
     ); 


----------------
-- Ticket #21768
----------------

INSERT
  INTO administracao.acao
     ( cod_acao
     , cod_funcionalidade
     , nom_arquivo
     , parametro
     , ordem
     , complemento_acao
     , nom_acao
     , ativo
     )
     VALUES
     ( 2978
     , 484
     , 'FLRelatorioDividaFlutuante.php'
     , 'consultar'
     , 12
     , ''
     , 'Demonstração da Dívida Flutuante'
     , TRUE
     );

INSERT
  INTO administracao.relatorio
     ( cod_gestao
     , cod_modulo
     , cod_relatorio
     , nom_relatorio
     , arquivo )
     VALUES
     ( 6
     , 55
     , 8
     , 'Demonstração da Dívida Flutuante'
     , 'LHTCEMGRelatorioDividaFlutuante.php'
     );
 


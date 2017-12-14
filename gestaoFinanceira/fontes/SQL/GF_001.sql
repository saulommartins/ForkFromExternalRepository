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
* Versão 001.
*/


/* Comentado para ir apenas no pacote específico de Livramento
-- Inclusao dos inserts para atributo caracteristica peculiar no empenho
   INSERT INTO administracao.atributo_dinamico (
      cod_modulo,
      cod_cadastro,
      cod_atributo,
      cod_tipo,
      nao_nulo,
      nom_atributo,
      ajuda,
      mascara,
      ativo,
      interno,
      indexavel
   )VALUES(
      10 ,
      1 ,
      2001 ,
      3 ,
      false ,
      'Característica Peculiar',
      'Selecione a Característica Peculiar',
      '',
      true ,
      true ,
      false
   );

   INSERT INTO administracao.atributo_valor_padrao (
      cod_modulo,
      cod_cadastro,
      cod_atributo,
      cod_valor,
      ativo,
      valor_padrao
   )VALUES(
      10 ,
      1 ,
      2001 ,
      1 ,
      true ,
      'Não se Aplica'
   );

   INSERT INTO administracao.atributo_valor_padrao (
      cod_modulo,
      cod_cadastro,
      cod_atributo,
      cod_valor,
      ativo,
      valor_padrao
   )VALUES(
      10 ,
      1 ,
      2001 ,
      2 ,
      true ,
      '60% FUNDEB'
   );

   INSERT INTO administracao.atributo_valor_padrao (
      cod_modulo,
      cod_cadastro,
      cod_atributo,
      cod_valor,
      ativo,
      valor_padrao
   )VALUES(
      10 ,
      1 ,
      2001 ,
      3 ,
      true ,
      'Art. 21 Lei FUNDEB'
   );

   INSERT INTO administracao.atributo_valor_padrao (
      cod_modulo,
      cod_cadastro,
      cod_atributo,
      cod_valor,
      ativo,
      valor_padrao
   )VALUES(
      10 ,
      1 ,
      2001 ,
      4 ,
      true ,
      'Coleta Resíduos Sólidos Urbanos'
   );

   INSERT INTO administracao.atributo_valor_padrao (
      cod_modulo,
      cod_cadastro,
      cod_atributo,
      cod_valor,
      ativo,
      valor_padrao
   )VALUES(
      10 ,
      1 ,
      2001 ,
      5 ,
      true ,
      'Transporte Resíduos Sólidos'
   );

   INSERT INTO administracao.atributo_valor_padrao (
      cod_modulo,
      cod_cadastro,
      cod_atributo,
      cod_valor,
      ativo,
      valor_padrao
   )VALUES(
      10 ,
      1 ,
      2001 ,
      6 ,
      true ,
      'Disposição Final Resíduos Sólidos Urbanos'
   );

   INSERT INTO administracao.atributo_valor_padrao (
      cod_modulo,
      cod_cadastro,
      cod_atributo,
      cod_valor,
      ativo,
      valor_padrao
   )VALUES(
      10 ,
      1 ,
      2001 ,
      7 ,
      true ,
      'Coleta, Transporte e Tratamento Resíduos Serviços Saúde'
   );

   INSERT INTO administracao.atributo_valor_padrao (
      cod_modulo,
      cod_cadastro,
      cod_atributo,
      cod_valor,
      ativo,
      valor_padrao
   )VALUES(
      10 ,
      1 ,
      2001 ,
      8 ,
      true ,
      'Limpeza Vias Urbanas'
   );

   INSERT INTO administracao.atributo_valor_padrao (
      cod_modulo,
      cod_cadastro,
      cod_atributo,
      cod_valor,
      ativo,
      valor_padrao
   )VALUES(
      10 ,
      1 ,
      2001 ,
      9 ,
      true ,
      'Coleta Seletiva Resíduos Sólidos Urbanos'
   );

   INSERT INTO administracao.atributo_integridade (
      cod_modulo,
      cod_cadastro,
      cod_atributo,
      cod_integridade,
      regra
   )VALUES(
      10 ,
      1 ,
      2001 ,
      1 ,
      '2001,,'
   );

   INSERT INTO administracao.atributo_integridade (
      cod_modulo,
      cod_cadastro,
      cod_atributo,
      cod_integridade,
      regra
   )VALUES(
      10 ,
      1 ,
      2001 ,
      2 ,
      ' SELECT  FROM  WHERE  = VLR_VALIDA0 '
   );
*/


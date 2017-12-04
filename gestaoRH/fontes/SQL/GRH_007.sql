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
* Versão 006.
*/
CREATE TABLE administracao.buffers_texto (
  buffer       varchar(50) NOT NULL,
  valor        varchar(50) NOT NULL,
  CONSTRAINT pk_buffers_texto PRIMARY KEY(buffer)
);

GRANT INSERT, DELETE, UPDATE, SELECT ON administracao.buffers_texto TO GROUP urbem;

CREATE TABLE administracao.buffers_inteiro (
  buffer       varchar(50) NOT NULL,
  valor        integer NOT NULL,
  CONSTRAINT pk_buffers_inteiro PRIMARY KEY(buffer)
);

GRANT INSERT, DELETE, UPDATE, SELECT ON administracao.buffers_inteiro TO GROUP urbem;

CREATE TABLE administracao.buffers_numerico (
  buffer       varchar(50) NOT NULL,
  valor        numeric(15,2) NOT NULL,
  CONSTRAINT pk_buffers_numerico PRIMARY KEY(buffer)
);

GRANT INSERT, DELETE, UPDATE, SELECT ON administracao.buffers_numerico TO GROUP urbem;

CREATE TABLE administracao.buffers_timestamp (
  buffer       varchar(50) NOT NULL,
  valor        timestamp NOT NULL,
  CONSTRAINT pk_buffers_timestamp PRIMARY KEY(buffer)
);

GRANT INSERT, DELETE, UPDATE, SELECT ON administracao.buffers_timestamp TO GROUP urbem;


--Folha Salário
select atualizarBanco('
CREATE TABLE folhapagamento.registro_evento_fixos(
  cod_evento integer not null,
  codigo character(5) not null,
  cod_registro integer not null,
  cod_contrato integer not null,
  valor numeric(15,2) not null,
  quantidade numeric(15,2) not null,
  proporcional boolean not null,
  parcela integer,
  cod_periodo_movimentacao integer not null,
  timestamp timestamp without time zone not null,
  formula varchar(10),
  natureza character(1) not null,
  cod_configuracao integer not null     
);');

select atualizarBanco('
GRANT INSERT, DELETE, UPDATE, SELECT ON folhapagamento.registro_evento_fixos TO GROUP urbem;');

select atualizarBanco('
CREATE INDEX i_registro_evento_fixos
          ON folhapagamento.registro_evento_fixos
           (  cod_evento
            , codigo
            , cod_registro
            , cod_contrato
            , proporcional
            , cod_periodo_movimentacao
            , timestamp
            , natureza
            , cod_configuracao);');
            
select atualizarBanco('            
CREATE TABLE folhapagamento.registro_evento_ordenado(
  cod_evento integer not null,
  codigo character(5) not null,
  cod_registro integer not null,
  cod_contrato integer not null,
  valor numeric(15,2) not null,
  quantidade numeric(15,2) not null,
  proporcional boolean not null,
  parcela integer,
  cod_periodo_movimentacao integer not null,
  timestamp timestamp without time zone not null,
  formula varchar(10),
  natureza character(1) not null,
  cod_configuracao integer not null,
  sequencia integer not null     
);');

select atualizarBanco('
GRANT INSERT, DELETE, UPDATE, SELECT ON folhapagamento.registro_evento_ordenado TO GROUP urbem;');

select atualizarBanco('
CREATE INDEX i_registro_evento_ordenado 
          ON folhapagamento.registro_evento_ordenado
           (  cod_evento
            , codigo
            , cod_registro
            , cod_contrato
            , proporcional
            , cod_periodo_movimentacao
            , timestamp
            , natureza
            , cod_configuracao);');
--Folha Salário            
            
            
--Folha Férias            
select atualizarBanco('
CREATE TABLE folhapagamento.registro_evento_ferias_ordenado(
  cod_evento integer not null,
  codigo character(5) not null,
  cod_registro integer not null,
  cod_contrato integer not null,
  valor numeric(15,2) not null,
  quantidade numeric(15,2) not null,
  desdobramento character(1) not null,
  parcela integer,
  cod_periodo_movimentacao integer not null,
  timestamp timestamp without time zone not null,
  natureza character(1) not null,
  evento_sistema character(3),
  sequencia integer not null    
);');

select atualizarBanco('
GRANT INSERT, DELETE, UPDATE, SELECT ON folhapagamento.registro_evento_ferias_ordenado TO GROUP urbem;');

select atualizarBanco('
CREATE INDEX i_registro_evento_ferias_ordenado 
          ON folhapagamento.registro_evento_ferias_ordenado
           (  cod_evento
            , codigo
            , cod_registro
            , cod_contrato
            , desdobramento
            , cod_periodo_movimentacao
            , timestamp
            , natureza
            );');            
--Folha Férias


--Folha Rescisao
select atualizarBanco('
CREATE TABLE folhapagamento.registro_evento_rescisao_ordenado(
  cod_evento integer not null,
  codigo character(5) not null,
  cod_registro integer not null,
  cod_contrato integer not null,
  valor numeric(15,2) not null,
  quantidade numeric(15,2) not null,
  desdobramento character(1) not null,
  parcela integer,
  cod_periodo_movimentacao integer not null,
  timestamp timestamp without time zone not null,
  natureza character(1) not null,
  sequencia integer not null    
);');            

select atualizarBanco('
GRANT INSERT, DELETE, UPDATE, SELECT ON folhapagamento.registro_evento_rescisao_ordenado TO GROUP urbem;');

select atualizarBanco('
CREATE INDEX i_registro_evento_rescisao_ordenado
          ON folhapagamento.registro_evento_rescisao_ordenado
           (  cod_evento
            , codigo
            , cod_registro
            , cod_contrato
            , desdobramento
            , cod_periodo_movimentacao
            , timestamp
            , natureza
            );'); 

--Folha Rescisao

--Folha Descimo
select atualizarBanco('
CREATE TABLE folhapagamento.registro_evento_decimo_ordenado(
  cod_evento integer not null,
  codigo character(5) not null,
  cod_registro integer not null,
  cod_contrato integer not null,
  valor numeric(15,2) not null,
  quantidade numeric(15,2) not null,
  desdobramento character(1) not null,
  parcela integer,
  cod_periodo_movimentacao integer not null,
  timestamp timestamp without time zone not null,
  natureza character(1) not null,
  sequencia integer not null    
);');            

select atualizarBanco('
GRANT INSERT, DELETE, UPDATE, SELECT ON folhapagamento.registro_evento_decimo_ordenado TO GROUP urbem;');

select atualizarBanco('
CREATE INDEX i_registro_evento_decimo_ordenado
          ON folhapagamento.registro_evento_decimo_ordenado
           (  cod_evento
            , codigo
            , cod_registro
            , cod_contrato
            , desdobramento
            , cod_periodo_movimentacao
            , timestamp
            , natureza
            ); ');
--Folha Descimo      




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
* $Id: GPC_1912.sql 59612 2014-09-02 12:00:51Z gelson $
*
* Versão 1.91.2
*/

----------------
-- Ticket #14011
----------------

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2428
          , 406
          , 'FMVincularReceitaCorrenteLiquida.php'
          , 'incluirRCL'
          , 10
          , ''
          , 'Vincular Receita Corrente Liquida'
          );


CREATE TABLE stn.receita_corrente_liquida (
    mes             INTEGER         NOT NULL,
    ano             CHAR(4)         NOT NULL,
    exercicio       CHAR(4)         NOT NULL,
    cod_entidade    INTEGER         NOT NULL,
    valor           NUMERIC(14,2)   NOT NULL,
    CONSTRAINT pk_receita_corrente_liquida      PRIMARY KEY                     (mes,ano,exercicio,cod_entidade),
    CONSTRAINT fk_receita_corrente_liquida_1    FOREIGN KEY                     (exercicio,cod_entidade)
                                                REFERENCES orcamento.entidade   (exercicio,cod_entidade)
);

GRANT ALL ON TABLE stn.receita_corrente_liquida TO GROUP urbem; 


----------------
-- Ticket #14029
----------------

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2420
          , 406
          , 'FMVincularDespesaPessoal.php'
          , 'incluirDP'
          , 11
          , ''
          , 'Vincular Despesa Pessoal'
          );

CREATE TABLE stn.despesa_pessoal (
    mes             INTEGER         NOT NULL,
    ano             CHAR(4)         NOT NULL,
    exercicio       CHAR(4)         NOT NULL,
    cod_entidade    INTEGER         NOT NULL,
    valor           NUMERIC(14,2)   NOT NULL,
    CONSTRAINT pk_despesa_pessoal   PRIMARY KEY(mes,ano,exercicio,cod_entidade),
    CONSTRAINT fk_despesa_pessoal_1 FOREIGN KEY(exercicio,cod_entidade)
                                    REFERENCES orcamento.entidade(exercicio,cod_entidade)
);

GRANT ALL ON TABLE stn.despesa_pessoal TO GROUP urbem;


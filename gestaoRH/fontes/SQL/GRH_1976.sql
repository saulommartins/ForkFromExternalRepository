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
* $Id: $
*
* Versão 1.97.6
*/

------------------------------------------------------------------------------
-- RETIRANDO OBRIGATORIEDADE DOS CAMPOS nome E registro EM ima.erros_pasep_910
------------------------------------------------------------------------------

SELECT atualizarbanco('ALTER TABLE ima.erros_pasep_910  ALTER COLUMN nome DROP NOT NULL;');
SELECT atualizarbanco('ALTER TABLE ima.erros_pasep_910  ALTER COLUMN registro DROP NOT NULL;');


----------------
-- Ticket #
----------------

SELECT atualizarbanco('
CREATE TABLE ima.ocorrencia_cadastral_910(
    num_ocorrencia      INTEGER         NOT NULL,
    descricao           VARCHAR(180)    NOT NULL,
    CONSTRAINT pk_ocorrencia_cadastral_910      PRIMARY KEY (num_ocorrencia)
);
');

SELECT atualizarbanco('GRANT ALL ON ima.ocorrencia_cadastral_910 TO GROUP urbem;');

SELECT atualizarbanco('INSERT INTO ima.ocorrencia_cadastral_910 (num_ocorrencia, descricao) VALUES (1, \'PIS/PASEP não cadastrado no sistema. Servidor possui R$ a receber.\');');

INSERT
  INTO administracao.tabelas_rh
     ( schema_cod
     , nome_tabela
     , sequencia
     )
VALUES ( 7
     , 'ocorrencia_cadastral_910'
     , 1
     );


SELECT atualizarbanco('
CREATE TABLE ima.erros_cadastrais_pasep_910(
    cod_erro            INTEGER         NOT NULL,
    num_ocorrencia      INTEGER         NOT NULL,
    pis_pasep           VARCHAR(15)     NOT NULL,
    valor               NUMERIC(14,2)   NOT NULL,
    CONSTRAINT pk_erros_cadastrais_pasep_910    PRIMARY KEY (cod_erro),
    CONSTRAINT fk_erros_cadastrais_pasep_910_1  FOREIGN KEY (num_ocorrencia)
                                                REFERENCES ima.ocorrencia_cadastral_910 (num_ocorrencia)
);
');

SELECT atualizarbanco('GRANT ALL ON ima.erros_cadastrais_pasep_910 TO GROUP urbem;');

SELECT atualizarbanco('
CREATE TABLE ima.pagamento_910(
    cod_periodo_movimentacao        INTEGER         NOT NULL,
    cod_tipo                        INTEGER         NOT NULL,
    CONSTRAINT pk_pagamento_910     PRIMARY KEY                                     (cod_periodo_movimentacao),
    CONSTRAINT fk_pagamento_910_1   FOREIGN KEY                                     (cod_periodo_movimentacao)
                                    REFERENCES folhapagamento.periodo_movimentacao  (cod_periodo_movimentacao),
    CONSTRAINT fk_pagamento_910_2   FOREIGN KEY                                     (cod_tipo)
                                    REFERENCES folhapagamento.tipo_folha            (cod_tipo)
);
');

SELECT atualizarbanco('GRANT ALL ON ima.pagamento_910 TO GROUP urbem;');


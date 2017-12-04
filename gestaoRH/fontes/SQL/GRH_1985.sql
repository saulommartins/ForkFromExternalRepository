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
* $Id:$
*
* Versão 1.98.5
*/

----------------
-- Ticket #16293
----------------

SELECT atualizarbanco('ALTER TABLE estagio.estagiario_estagio_bolsa ADD COLUMN vale_refeicao   BOOLEAN NOT NULL DEFAULT FALSE;');
SELECT atualizarbanco('ALTER TABLE estagio.estagiario_estagio_bolsa ADD COLUMN vale_transporte BOOLEAN NOT NULL DEFAULT FALSE;');

SELECT atualizarbanco('
UPDATE estagio.estagiario_estagio_bolsa
   SET vale_refeicao = TRUE
  FROM estagio.estagiario_vale_refeicao
 WHERE estagiario_estagio_bolsa.cgm_instituicao_ensino = estagiario_vale_refeicao.cgm_instituicao_ensino
   AND estagiario_estagio_bolsa.cgm_estagiario         = estagiario_vale_refeicao.cgm_estagiario
   AND estagiario_estagio_bolsa.cod_curso              = estagiario_vale_refeicao.cod_curso
   AND estagiario_estagio_bolsa.cod_estagio            = estagiario_vale_refeicao.cod_estagio
   AND estagiario_estagio_bolsa.timestamp              = estagiario_vale_refeicao.timestamp
     ;
');

SELECT atualizarbanco('ALTER TABLE estagio.entidade_intermediadora ADD   COLUMN percentual_atual NUMERIC(5,2);');
SELECT atualizarbanco('UPDATE      estagio.entidade_intermediadora SET          percentual_atual = 0.00;');
SELECT atualizarbanco('ALTER TABLE estagio.entidade_intermediadora ALTER COLUMN percentual_atual SET NOT NULL;');

SELECT atualizarbanco('
CREATE TABLE estagio.entidade_contribuicao(
    numcgm          INTEGER                 NOT NULL,
    timestamp       TIMESTAMP               NOT NULL DEFAULT (\'now\'::text)::timestamp(3) WITH TIME ZONE,
    percentual      NUMERIC(5,2)            NOT NULL,
    CONSTRAINT pk_entidade_contribuicao     PRIMARY KEY                                 (numcgm, timestamp),
    CONSTRAINT fk_entidade_contribuicao_1   FOREIGN KEY                                 (numcgm)
                                            REFERENCES estagio.entidade_intermediadora  (numcgm)
);
');

SELECT atualizarbanco('
GRANT ALL ON estagio.entidade_contribuicao TO GROUP urbem;
');

SELECT atualizarbanco('
INSERT
  INTO estagio.entidade_contribuicao
     ( numcgm
     , percentual
     )
SELECT numcgm
     , 0.00 AS percentual
  FROM estagio.entidade_intermediadora
     ;
');


SELECT atualizarbanco('
CREATE TABLE estagio.tipo_contagem_vale(
    cod_tipo        INTEGER             NOT NULL,
    descricao       VARCHAR(20)         NOT NULL,
    CONSTRAINT pk_tipo_contagem_vale    PRIMARY KEY (cod_tipo)
);
');

SELECT atualizarbanco('
GRANT ALL ON estagio.tipo_contagem_vale TO GROUP urbem;
');

SELECT atualizarbanco('INSERT INTO estagio.tipo_contagem_vale (cod_tipo, descricao) VALUES (1, \'Diário\'     );');
SELECT atualizarbanco('INSERT INTO estagio.tipo_contagem_vale (cod_tipo, descricao) VALUES (2, \'Fixo Mensal\');');

SELECT atualizarbanco('
CREATE TABLE estagio.estagiario_vale_transporte(
    cgm_instituicao_ensino      INTEGER         NOT NULL,
    cgm_estagiario              INTEGER         NOT NULL,
    cod_curso                   INTEGER         NOT NULL,
    cod_estagio                 INTEGER         NOT NULL,
    timestamp                   TIMESTAMP       NOT NULL DEFAULT (\'now\'::text)::timestamp(3) WITH TIME ZONE,
    cod_tipo                    INTEGER         NOT NULL,
    quantidade                  NUMERIC(3)      NOT NULL,
    valor_unitario              NUMERIC(14,2)   NOT NULL,
    cod_calendar                INTEGER                 ,
    CONSTRAINT pk_estagiario_vale_transporte    PRIMARY KEY                                 (cgm_instituicao_ensino, cgm_estagiario, cod_curso, cod_estagio, timestamp),
    CONSTRAINT fk_estagiario_vale_transporte_1  FOREIGN KEY                                 (cgm_instituicao_ensino, cgm_estagiario, cod_curso, cod_estagio, timestamp)
                                                REFERENCES estagio.estagiario_estagio_bolsa (cgm_instituicao_ensino, cgm_estagiario, cod_curso, cod_estagio, timestamp),
    CONSTRAINT fk_estagiario_vale_transporte_2  FOREIGN KEY                                 (cod_calendar)
                                                REFERENCES calendario.calendario_cadastro   (cod_calendar),
    CONSTRAINT fk_estagiario_vale_transporte_3  FOREIGN KEY                                 (cod_tipo)
                                                REFERENCES estagio.tipo_contagem_vale       (cod_tipo)
);
');

SELECT atualizarbanco('
GRANT ALL ON estagio.estagiario_vale_transporte TO GROUP urbem;
');


----------------
-- Ticket #
----------------

SELECT atualizarbanco('ALTER TABLE folhapagamento.tabela_irrf_cid                      DROP CONSTRAINT fk_tabela_irrf_cid_1;       ');
SELECT atualizarbanco('ALTER TABLE folhapagamento.tabela_irrf_evento                   DROP CONSTRAINT fk_tabela_irrf_evento_1;    ');
SELECT atualizarbanco('ALTER TABLE folhapagamento.tabela_irrf_comprovante_rendimento   DROP CONSTRAINT fk_comprovante_rendimento_1;');
SELECT atualizarbanco('ALTER TABLE folhapagamento.faixa_desconto_irrf                  DROP CONSTRAINT fk_faixa_desconto_irrf_1;   ');
SELECT atualizarbanco('ALTER TABLE folhapagamento.tabela_irrf                          DROP CONSTRAINT pk_tabela_irrf;             ');

SELECT atualizarbanco('UPDATE folhapagamento.tabela_irrf
   SET cod_tabela = 1
 WHERE cod_tabela != 1;
');

SELECT atualizarbanco('UPDATE folhapagamento.tabela_irrf_evento
   SET cod_tabela = 1
 WHERE cod_tabela != 1;
');

SELECT atualizarbanco('UPDATE folhapagamento.tabela_irrf_comprovante_rendimento
   SET cod_tabela = 1
 WHERE cod_tabela != 1;
');

SELECT atualizarbanco('UPDATE folhapagamento.faixa_desconto_irrf
   SET cod_tabela = 1
 WHERE cod_tabela != 1;
');

SELECT atualizarbanco('UPDATE folhapagamento.tabela_irrf_cid
   SET cod_tabela = 1
 WHERE cod_tabela != 1;
');

SELECT atualizarbanco('ALTER TABLE folhapagamento.tabela_irrf                          ADD  CONSTRAINT pk_tabela_irrf PRIMARY KEY (cod_tabela, timestamp);                                                                          ');
SELECT atualizarbanco('ALTER TABLE folhapagamento.faixa_desconto_irrf                  ADD  CONSTRAINT fk_faixa_desconto_irrf_1 FOREIGN KEY (cod_tabela, timestamp) REFERENCES folhapagamento.tabela_irrf(cod_tabela, timestamp);   ');
SELECT atualizarbanco('ALTER TABLE folhapagamento.tabela_irrf_comprovante_rendimento   ADD  CONSTRAINT fk_comprovante_rendimento_1 FOREIGN KEY (timestamp, cod_tabela) REFERENCES folhapagamento.tabela_irrf(timestamp, cod_tabela);');
SELECT atualizarbanco('ALTER TABLE folhapagamento.tabela_irrf_evento                   ADD  CONSTRAINT fk_tabela_irrf_evento_1 FOREIGN KEY (cod_tabela, timestamp) REFERENCES folhapagamento.tabela_irrf(cod_tabela, timestamp);    ');
SELECT atualizarbanco('ALTER TABLE folhapagamento.tabela_irrf_cid                      ADD  CONSTRAINT fk_tabela_irrf_cid_1 FOREIGN KEY (cod_tabela, timestamp) REFERENCES folhapagamento.tabela_irrf(cod_tabela, timestamp);       ');


----------------
-- Ticket #16336
----------------

DROP FUNCTION ultimo_atributo_contrato_servidor_valor(varchar, integer);

DROP TYPE colunasUltimoAtributoContratoServidorValor;

CREATE TYPE colunasUltimoAtributoContratoServidorValor AS (
    cod_contrato    INTEGER,
    cod_atributo    INTEGER,
    cod_modulo      INTEGER,
    cod_cadastro   INTEGER,
    valor           VARCHAR
);

SELECT atualizarbanco('ALTER TABLE pessoal.contrato_servidor_ocorrencia DROP CONSTRAINT pk_contrato_servidor_ocorrencia;');
SELECT atualizarbanco('ALTER TABLE pessoal.contrato_servidor_ocorrencia ADD  CONSTRAINT pk_contrato_servidor_ocorrencia PRIMARY KEY (cod_contrato, timestamp);');

---------------------------------------------
-- CORRIGINDO CONFIGURACOES P/ EXERCICIO 2010
---------------------------------------------

UPDATE administracao.configuracao
   SET exercicio = '2010'
 WHERE parametro = 'num_sequencial_arquivo_hsbc'
   AND exercicio = '2009'
     ;

UPDATE administracao.configuracao
   SET exercicio = '2010'
 WHERE parametro = 'dt_num_sequencial_arquivo_hsbc'
   AND exercicio = '2009'
     ;


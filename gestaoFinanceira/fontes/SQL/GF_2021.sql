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
* Versao 2.02.1
*
* Fabio Bertoldi - 20130716
*
*/

-----------------------
-- INATIVANDO ACAO 1487
-----------------------

UPDATE administracao.acao SET ativo = FALSE WHERE cod_acao = 1487;


----------------
-- Ticket #21179
----------------

UPDATE administracao.acao SET ativo = FALSE WHERE cod_acao = 864;
UPDATE administracao.acao SET ativo = FALSE WHERE cod_acao = 869;


----------------
-- Ticket #20528
----------------

UPDATE administracao.configuracao
   SET valor = (
                 SELECT valor
                   FROM administracao.configuracao
                  WHERE cod_modulo = 8
                    AND exercicio  = '2014'
                    AND parametro  = 'unidade_medida_metas'
               )
 WHERE cod_modulo = 8
   AND exercicio = '2014'
   AND parametro = 'unidade_medida_metas_receita'
   AND valor     = ''
     ;

UPDATE administracao.configuracao
   SET valor = (
                 SELECT valor
                   FROM administracao.configuracao
                  WHERE cod_modulo = 8
                    AND exercicio  = '2014'
                    AND parametro  = 'unidade_medida_metas'
               )
 WHERE cod_modulo = 8
   AND exercicio = '2014'
   AND parametro = 'unidade_medida_metas_despesa'
   AND valor     = ''
     ;


----------------
-- Ticket #20183
----------------

INSERT
  INTO administracao.relatorio
  ( cod_gestao
  , cod_modulo
  , cod_relatorio
  , nom_relatorio
  , arquivo )
  VALUES
  ( 2
  , 9
  , 17
  , 'Balanço Orçamentario'
  , 'balancoOrcamentarioNovo.rptdesign'
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
     ( 2931
     , 63
     , 'FLBalancoOrcamentario.php'
     , 'gerar'
     , 32
     , ''
     , 'Balanço Orçamentário'
     , TRUE
     );


----------------
-- Ticket #21174
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
     ( 2920
     , 61
     , 'FLCancelarAberturaRestosAPagar.php'
     , 'excluir'
     , 10
     , ''
     , 'Cancelar Abertura Restos a Pagar'
     , TRUE
     );


----------------------------------------------------------------------
-- ALTERANDO COLUNA nom_lote EM CONTABILIDADE.LOTE - Silvia 20/02/2014
----------------------------------------------------------------------

ALTER TABLE contabilidade.lote ALTER COLUMN nom_lote TYPE varchar(200);


----------------
-- Ticket #21429
----------------

INSERT INTO administracao.configuracao VALUES ('2013', 30, 'seta_tipo_documento_tcemg', 'false');
INSERT INTO administracao.configuracao VALUES ('2014', 30, 'seta_tipo_documento_tcemg', 'false');

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN
    PERFORM 1
       FROM administracao.configuracao
      WHERE cod_modulo = 2
        AND exercicio  = '2014'
        AND parametro  = 'cod_uf'
        AND valor      = '11'
          ;
    IF FOUND THEN
        UPDATE administracao.configuracao
           SET valor = 'true'
         WHERE cod_modulo = 30
           AND parametro  = 'seta_tipo_documento_tcemg'
             ;
    END IF;
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();


CREATE TABLE tcemg.tipo_documento (
    cod_tipo        INTEGER         NOT NULL,
    descricao       VARCHAR(35)     NOT NULL,
    CONSTRAINT pk_tipo_documento    PRIMARY KEY (cod_tipo)
);
GRANT ALL ON tcemg.tipo_documento TO urbem;

INSERT INTO tcemg.tipo_documento VALUES ( 1, 'Cheque'             );
INSERT INTO tcemg.tipo_documento VALUES ( 2, 'DOC'                );
INSERT INTO tcemg.tipo_documento VALUES ( 3, 'TED'                );
INSERT INTO tcemg.tipo_documento VALUES ( 4, 'Bonderô'            );
INSERT INTO tcemg.tipo_documento VALUES ( 5, 'Dinheiro em espécie');
INSERT INTO tcemg.tipo_documento VALUES (99, 'Outros'             );


CREATE TABLE tcemg.pagamento_tipo_documento (
    cod_tipo_documento      INTEGER             NOT NULL,
    cod_entidade            INTEGER             NOT NULL,
    exercicio               VARCHAR(4)          NOT NULL,
    timestamp               TIMESTAMP           NOT NULL DEFAULT ('now'::text)::timestamp(3) with time zone,
    cod_nota                INTEGER             NOT NULL,
    num_documento           VARCHAR(15),
    CONSTRAINT pk_pagamento_tipo_documento      PRIMARY KEY                     (exercicio, cod_entidade, cod_nota, timestamp, cod_tipo_documento),
    CONSTRAINT fk_pagamento_tipo_documento_1    FOREIGN KEY                     (cod_entidade, exercicio, timestamp, cod_nota)
                                                REFERENCES tesouraria.pagamento (cod_entidade, exercicio, timestamp, cod_nota),
    CONSTRAINT fk_pagamento_tipo_documento_2    FOREIGN KEY                     (cod_tipo_documento)
                                                REFERENCES tcemg.tipo_documento (cod_tipo)
);
GRANT ALL ON tcemg.pagamento_tipo_documento TO urbem;



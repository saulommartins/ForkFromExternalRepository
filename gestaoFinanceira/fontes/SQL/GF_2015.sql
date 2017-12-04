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
* Versao 2.01.5
*
* Fabio Bertoldi - 20130205
*
*/

--------------------------------------------
-- RECRIANDO VIEW organograma.vw_orgao_nivel
--------------------------------------------

CREATE OR REPLACE FUNCTION dropview() RETURNS VOID AS $$
DECLARE

BEGIN
    PERFORM 1
       FROM pg_views
      WHERE schemaname = 'organograma'
        AND viewname   = 'vw_orgao_nivel'
          ;
    IF FOUND THEN
        DROP VIEW organograma.vw_orgao_nivel;
    END IF;
END;
$$ LANGUAGE 'plpgsql';

SELECT        dropview();
DROP FUNCTION dropview();

CREATE VIEW organograma.vw_orgao_nivel AS 
    SELECT o.cod_orgao
         , o.num_cgm_pf
         , o.cod_calendar
         , o.cod_norma
         , o.criacao
         , o.inativacao
         , o.sigla_orgao
         , orn.cod_organograma
         , organograma.fn_consulta_orgao(orn.cod_organograma, o.cod_orgao) AS orgao
         , publico.fn_mascarareduzida(organograma.fn_consulta_orgao(orn.cod_organograma, o.cod_orgao)) AS orgao_reduzido
         , publico.fn_nivel(organograma.fn_consulta_orgao(orn.cod_organograma, o.cod_orgao)) AS nivel
         , orn.cod_nivel
      FROM organograma.orgao o
         , organograma.orgao_nivel orn
     WHERE o.cod_orgao = orn.cod_orgao
  ORDER BY o.cod_orgao
         ;


----------------
-- Ticket #20075
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN
    PERFORM 1
       FROM pg_class
          , pg_attribute
          , pg_type
      WHERE pg_class.relname      = 'ordem_pagamento_retencao'
        AND pg_attribute.attname  = 'cod_receita'
        AND pg_attribute.attnum   > 0
        AND pg_attribute.attrelid = pg_class.oid
        AND pg_attribute.atttypid = pg_type.oid
          ;
    IF NOT FOUND THEN
        ALTER TABLE empenho.ordem_pagamento_retencao ADD COLUMN cod_receita INTEGER;
        ALTER TABLE empenho.ordem_pagamento_retencao ADD CONSTRAINT fk_ordem_pagamento_retencao_3 FOREIGN KEY                  (exercicio, cod_receita)
                                                                                                  REFERENCES orcamento.receita (exercicio, cod_receita);
    END IF;
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();


----------------
-- Ticket #20075
----------------

ALTER TABLE contabilidade.lancamento_retencao                               DROP CONSTRAINT fk_retencao_1;
ALTER TABLE tesouraria.arrecadacao_estornada_ordem_pagamento_retencao       DROP CONSTRAINT fk_arrecadacao_estornada_ordem_pagamento_retencao_2;
ALTER TABLE tesouraria.arrecadacao_ordem_pagamento_retencao                 DROP CONSTRAINT fk_arrecadacao_ordem_pagamento_retencao_2;
ALTER TABLE tesouraria.transferencia_estornada_ordem_pagamento_retencao     DROP CONSTRAINT fk_transferencia_estornada_ordem_pagamento_retencao_2;
ALTER TABLE tesouraria.transferencia_ordem_pagamento_retencao               DROP CONSTRAINT fk_transferencia_ordem_pagamento_retencao_2;
ALTER TABLE empenho.ordem_pagamento_retencao                                DROP CONSTRAINT pk_ordem_pagamento_retencao;

ALTER TABLE contabilidade.lancamento_retencao                               ADD sequencial integer DEFAULT 1 NOT NULL;
ALTER TABLE tesouraria.arrecadacao_estornada_ordem_pagamento_retencao       ADD sequencial integer DEFAULT 1 NOT NULL;
ALTER TABLE tesouraria.arrecadacao_ordem_pagamento_retencao                 ADD sequencial integer DEFAULT 1 NOT NULL;
ALTER TABLE tesouraria.transferencia_estornada_ordem_pagamento_retencao     ADD sequencial integer DEFAULT 1 NOT NULL;
ALTER TABLE tesouraria.transferencia_ordem_pagamento_retencao               ADD sequencial integer DEFAULT 1 NOT NULL;
ALTER TABLE empenho.ordem_pagamento_retencao                                ADD sequencial integer DEFAULT 1 NOT NULL;

ALTER TABLE empenho.ordem_pagamento_retencao                                ADD CONSTRAINT pk_ordem_pagamento_retencao PRIMARY KEY (exercicio, cod_entidade, cod_ordem, cod_plano, sequencial);
ALTER TABLE contabilidade.lancamento_retencao                               ADD CONSTRAINT fk_retencao_1 FOREIGN KEY (cod_ordem, cod_entidade, cod_plano, exercicio_retencao, sequencial) REFERENCES empenho.ordem_pagamento_retencao(cod_ordem, cod_entidade, cod_plano, exercicio, sequencial);
ALTER TABLE tesouraria.arrecadacao_estornada_ordem_pagamento_retencao       ADD CONSTRAINT fk_arrecadacao_estornada_ordem_pagamento_retencao_2 FOREIGN KEY (exercicio, cod_entidade, cod_ordem, cod_plano, sequencial) REFERENCES empenho.ordem_pagamento_retencao(exercicio, cod_entidade, cod_ordem, cod_plano, sequencial);
ALTER TABLE tesouraria.arrecadacao_ordem_pagamento_retencao                 ADD CONSTRAINT fk_arrecadacao_ordem_pagamento_retencao_2 FOREIGN KEY (exercicio, cod_entidade, cod_ordem, cod_plano, sequencial) REFERENCES empenho.ordem_pagamento_retencao(exercicio, cod_entidade, cod_ordem, cod_plano, sequencial);
ALTER TABLE tesouraria.transferencia_estornada_ordem_pagamento_retencao     ADD CONSTRAINT fk_transferencia_estornada_ordem_pagamento_retencao_2 FOREIGN KEY (exercicio, cod_entidade, cod_ordem, cod_plano, sequencial) REFERENCES empenho.ordem_pagamento_retencao(exercicio, cod_entidade, cod_ordem, cod_plano, sequencial);
ALTER TABLE tesouraria.transferencia_ordem_pagamento_retencao               ADD CONSTRAINT fk_transferencia_ordem_pagamento_retencao_2 FOREIGN KEY (exercicio, cod_entidade, cod_ordem, cod_plano, sequencial) REFERENCES empenho.ordem_pagamento_retencao(exercicio, cod_entidade, cod_ordem, cod_plano, sequencial);


----------------
-- Ticket #20017
----------------

INSERT INTO administracao.configuracao (exercicio, cod_modulo, parametro, valor) VALUES ('2013', 8, 'cod_entidade_consorcio', '');


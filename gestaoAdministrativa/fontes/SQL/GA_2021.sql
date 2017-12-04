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
* Fabio Bertoldi - 20130704
*
*/

----------------
-- Ticket #20248
----------------

UPDATE administracao.acao SET ativo = FALSE WHERE cod_acao = 162;


----------------
-- Ticket #20241
----------------

DELETE
  FROM administracao.permissao
 WHERE cod_acao IN (
                     247, 248, 249, 476, 477, 478, 680 -- GF, Contabilidade, Relatorios
                   , 1504, 1501, 1505, 1502, 1506, 1503, 1507, 2170, 2219, 2259, 2190, 2214, 2265, 2225, 2195, 2230, 2422, 2189, 2220, 2264 -- GPC, RREO e RGF ROMANOS
                   )
   AND ano_exercicio > '2012'
     ;


DELETE
  FROM administracao.permissao
 WHERE cod_acao IN (
                     2884, 2874, 2860, 2877, 2869, 2875, 2876, 2882, 2870, 2883, 2886, 2861, 2865, 2862, 2873, 2863, 2864, 2866, 2878, 2867 -- GPC, RREO e RGF NAO ROMANOS
                  )
   AND ano_exercicio < '2013'
     ;


----------------
-- Ticket #18745
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN

    PERFORM 1
       FROM information_schema.tables
      WHERE table_schema = 'administracao'
        AND table_name   = 'auditoria_detalhe'
          ;
    IF NOT FOUND THEN
        CREATE TABLE administracao.auditoria_detalhe(
            numcgm          INTEGER             NOT NULL,
            cod_acao        INTEGER             NOT NULL,
            timestamp       TIMESTAMP           NOT NULL,
            cod_detalhe     INTEGER             NOT NULL,
            valores         hstore              NOT NULL,
            CONSTRAINT pk_auditoria_detalhe     PRIMARY KEY                         (numcgm, cod_acao, timestamp, cod_detalhe),
            CONSTRAINT fk_auditoria_detalhe_1   FOREIGN KEY                         (numcgm, cod_acao, timestamp)
                                                REFERENCES administracao.auditoria  (numcgm, cod_acao, timestamp)
        );
        GRANT ALL ON administracao.auditoria_detalhe TO urbem;
    END IF;
    
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();


----------------
-- Ticket #21327
----------------

INSERT
  INTO administracao.permissao
     ( numcgm
     , cod_acao
     , ano_exercicio
     )
SELECT 0        AS numcgm
     , 2711     AS cod_acao
     , '2014'   AS ano_exercicio
 WHERE 0 = (
             SELECT COUNT(1)
               FROM administracao.permissao
              WHERE numcgm        = 0
                AND cod_acao      = 2711
                AND ano_exercicio = '2014'
           )
     ;


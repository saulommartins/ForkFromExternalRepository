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
* Versao 2.05.3
*
* Fabio Bertoldi - 20160719
*
*/

----------------
-- Ticket #23989
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
SELECT 3121
     , 451
     , 'FMVincularPlanoContas.php'
     , 'vincular'
     , 54
     , ''
     , 'Vincular Plano de Conta'
     , TRUE
 WHERE 0 = (
             SELECT COUNT(1)
               FROM administracao.acao
              WHERE cod_acao = 3121
                AND nom_acao = 'Vincular Plano de Conta'
           )
     ;

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN
    PERFORM 1
       FROM pg_tables
      WHERE schemaname = 'tcemg'
        AND tablename  = 'plano_contas'
          ;
    IF NOT FOUND THEN
        CREATE TABLE tcemg.plano_contas(
            cod_conta         INTEGER       NOT NULL,
            exercicio         CHAR(4)       NOT NULL,
            cod_uf            INTEGER       NOT NULL,
            cod_plano         INTEGER       NOT NULL,
            codigo_estrutural VARCHAR(30)   NOT NULL,
            CONSTRAINT pk_plano_contas_tcemg    PRIMARY KEY              (cod_conta, exercicio, cod_uf, cod_plano, codigo_estrutural),
            CONSTRAINT fk_plano_contas_tcemg_1  FOREIGN KEY                                    (exercicio, cod_conta)
                                                REFERENCES contabilidade.plano_conta           (exercicio, cod_conta),
            CONSTRAINT fk_plano_contas_tcemg_2  FOREIGN KEY                                    (cod_uf, cod_plano, codigo_estrutural)
                                                REFERENCES contabilidade.plano_conta_estrutura (cod_uf, cod_plano, codigo_estrutural)
        );
        GRANT ALL ON tcemg.plano_contas TO urbem;
    END IF;
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();


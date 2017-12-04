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
* Versao 2.04.5
*
* Fabio Bertoldi - 20151110
*
*/

----------------
-- Ticket #23357
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
     ( 3094
     , 240
     , 'FMManterConfiguracaoEventosAutomaticos.php'
     , 'configurar'
     , 20
     , ''
     , 'Configurar Eventos Automáticos'
     , TRUE
     );

INSERT
  INTO administracao.configuracao
     ( cod_modulo
     , exercicio
     , parametro
     , valor
     )
VALUES
     ( 27
     , '2015'
     , 'evento_automatico'
     , ''
     );
INSERT
  INTO administracao.configuracao
     ( cod_modulo
     , exercicio
     , parametro
     , valor
     )
VALUES
     ( 27
     , '2016'
     , 'evento_automatico'
     , ''
     );


----------------
-- Ticket #23355
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE
    stSQL       VARCHAR;
    reRecord    RECORD;
BEGIN
    INSERT
      INTO administracao.configuracao
         ( cod_modulo
         , exercicio
         , parametro
         , valor
         )
    VALUES
         ( 27
         , '2015'
         , 'adiantamento_13_salario'
         , ''
         );
    INSERT
      INTO administracao.configuracao
         ( cod_modulo
         , exercicio
         , parametro
         , valor
         )
    VALUES
         ( 27
         , '2016'
         , 'adiantamento_13_salario'
         , ''
         );

    stSQL := '
                 SELECT cod_entidade
                   FROM orcamento.entidade
               GROUP BY cod_entidade
                      ;
             ';
    FOR reRecord IN EXECUTE stSQL LOOP
        INSERT
          INTO administracao.configuracao_entidade
             ( exercicio
             , cod_entidade
             , cod_modulo
             , parametro
             , valor
             )
        SELECT '2015'
             , reRecord.cod_entidade
             , 27
             , 'adiantamento_13_salario_'||reRecord.cod_entidade::VARCHAR
             , ''
         WHERE 0 = (
                     SELECT COUNT(1)
                       FROM administracao.configuracao_entidade
                      WHERE exercicio    = '2015'
                        AND cod_entidade = reRecord.cod_entidade
                        AND cod_modulo   = 27
                        AND parametro    = 'adiantamento_13_salario_'||reRecord.cod_entidade::VARCHAR
                   )
             ;
        INSERT
          INTO administracao.configuracao
             ( exercicio
             , cod_modulo
             , parametro
             , valor
             )
        SELECT '2015'
             , 27
             , 'adiantamento_13_salario_'||reRecord.cod_entidade::VARCHAR
             , ''
         WHERE 0 = (
                     SELECT COUNT(1)
                       FROM administracao.configuracao
                      WHERE exercicio    = '2015'
                        AND cod_modulo   = 27
                        AND parametro    = 'adiantamento_13_salario_'||reRecord.cod_entidade::VARCHAR
                   )
             ;
        INSERT
          INTO administracao.configuracao_entidade
             ( exercicio
             , cod_entidade
             , cod_modulo
             , parametro
             , valor
             )
        SELECT '2016'
             , reRecord.cod_entidade
             , 27
             , 'adiantamento_13_salario_'||reRecord.cod_entidade::VARCHAR
             , ''
         WHERE 0 = (
                     SELECT COUNT(1)
                       FROM administracao.configuracao_entidade
                      WHERE exercicio    = '2016'
                        AND cod_entidade = reRecord.cod_entidade
                        AND cod_modulo   = 27
                        AND parametro    = 'adiantamento_13_salario_'||reRecord.cod_entidade::VARCHAR
                   )
             ;
        INSERT
          INTO administracao.configuracao
             ( exercicio
             , cod_modulo
             , parametro
             , valor
             )
        SELECT '2016'
             , 27
             , 'adiantamento_13_salario_'||reRecord.cod_entidade::VARCHAR
             , ''
         WHERE 0 = (
                     SELECT COUNT(1)
                       FROM administracao.configuracao
                      WHERE exercicio    = '2016'
                        AND cod_modulo   = 27
                        AND parametro    = 'adiantamento_13_salario_'||reRecord.cod_entidade::VARCHAR
                   )
             ;
    END LOOP;
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();


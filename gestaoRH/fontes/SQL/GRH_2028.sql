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
* Versao 2.02.7
*
* Fabio Bertoldi - 20140710
*
*/

----------------
-- Ticket #21876
----------------

CREATE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE
    stSqlEnt        VARCHAR;
    inTeste         INTEGER;
    reRecord        RECORD;
BEGIN

    stSqlEnt := '  SELECT DISTINCT cod_entidade
                     FROM administracao.entidade_rh
                    WHERE cod_entidade <> ( SELECT CAST(valor as integer)
                                              FROM administracao.configuracao
                                             WHERE exercicio = (
                                                                  SELECT MAX(exercicio)
                                                                    FROM administracao.configuracao
                                                               )
                                               AND parametro = ''cod_entidade_prefeitura''
                                          )
                        ;
                ';

    FOR reRecord IN EXECUTE stSqlEnt LOOP

        EXECUTE '
                  SELECT *
                     FROM pessoal_'|| reRecord.cod_entidade ||'.vinculo_empregaticio
                    WHERE cod_vinculo = 31
                        ;
                '
            INTO inTeste;
        IF inTeste > 0 THEN
            EXECUTE '
                      UPDATE pessoal_'|| reRecord.cod_entidade ||'.vinculo_empregaticio
                         SET descricao = ''Servidor regido pelo Regime Jurídico Único (federal, estadual e municipal) e militar, vinculado ao Regime Geral de Previdência Social.''
                       WHERE cod_vinculo = 31
                           ;
                    ';
        ELSE
            EXECUTE '
                      INSERT
                        INTO pessoal_'|| reRecord.cod_entidade ||'.vinculo_empregaticio
                           ( cod_vinculo
                           , descricao
                           )
                      VALUES
                           ( 31
                           , ''Servidor regido pelo Regime Jurídico Único (federal, estadual e municipal) e militar, vinculado ao Regime Geral de Previdência Social.''
                           );
                    ';
        END IF;


        EXECUTE '
                  SELECT cod_vinculo
                    FROM pessoal_'|| reRecord.cod_entidade ||'.vinculo_empregaticio
                   WHERE cod_vinculo = 95
                       ;
                '
           INTO inTeste;
        IF inTeste > 0 THEN
            EXECUTE '
                      UPDATE pessoal_'|| reRecord.cod_entidade ||'.vinculo_empregaticio
                         SET descricao = ''Contrato de Trabalho por Tempo Determinado, regido pela Lei nº 8.745, de 9 de dezembro de 1993, com a redação dada pela Lei nº 9.849, de 26 de outubro de 1999.''
                       WHERE cod_vinculo = 95
                           ;
                    ';
        ELSE
            EXECUTE '
                      INSERT
                        INTO pessoal_'|| reRecord.cod_entidade ||'.vinculo_empregaticio
                           ( cod_vinculo
                           , descricao
                           )
                      VALUES
                           ( 95
                           , ''Contrato de Trabalho por Tempo Determinado, regido pela Lei nº 8.745, de 9 de dezembro de 1993, com a redação dada pela Lei nº 9.849, de 26 de outubro de 1999.''
                           );
                    ';
        END IF;
    
        EXECUTE '
                  SELECT *
                     FROM pessoal_'|| reRecord.cod_entidade ||'.vinculo_empregaticio
                    WHERE cod_vinculo = 96
                        ;
                '
           INTO inTeste;
        IF inTeste > 0 THEN
            EXECUTE '
                      UPDATE pessoal_'|| reRecord.cod_entidade ||'.vinculo_empregaticio
                         SET descricao = ''Contrato de Trabalho por Prazo Determinado, regido por Lei Estadual.''
                       WHERE cod_vinculo = 96
                           ;
                    ';
        ELSE
            EXECUTE '
                      INSERT
                        INTO pessoal_'|| reRecord.cod_entidade ||'.vinculo_empregaticio
                           ( cod_vinculo
                           , descricao
                           )
                      VALUES
                           ( 96
                           , ''Contrato de Trabalho por Prazo Determinado, regido por Lei Estadual.''
                           );
                    ';
        END IF;
    
        EXECUTE '
                  SELECT *
                     FROM pessoal_'|| reRecord.cod_entidade ||'.vinculo_empregaticio
                    WHERE cod_vinculo = 97
                        ;
                '
           INTO inTeste;
        IF inTeste > 0 THEN
            EXECUTE '
                      UPDATE pessoal_'|| reRecord.cod_entidade ||'.vinculo_empregaticio
                         SET descricao = ''Contrato de Trabalho por Prazo Determinado, regido por Lei Municipal.''
                       WHERE cod_vinculo = 97
                           ;
                    ';
        ELSE
            EXECUTE '
                      INSERT
                        INTO pessoal_'|| reRecord.cod_entidade ||'.vinculo_empregaticio
                           ( cod_vinculo
                           , descricao
                           )
                      VALUES
                           ( 97
                           , ''Contrato de Trabalho por Prazo Determinado, regido por Lei Municipal.''
                           );
                    ';
        END IF;

    END LOOP;

END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();


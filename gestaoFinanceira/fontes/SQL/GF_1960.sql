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
* Versão 1.96.0
*/

CREATE OR REPLACE FUNCTION corrigeAcao() RETURNS VOID AS $$
DECLARE
    stSql           VARCHAR;
    stExercicioTMP  VARCHAR;
    inExercicio     INTEGER;
    inCount         INTEGER;
    reRegistro      RECORD;
BEGIN

    stSql := '
        SELECT acao_dados.cod_acao
             , acao_dados.titulo
             , acao_dados.detalhamento
          FROM ppa.acao
          JOIN ppa.acao_dados
            ON acao_dados.cod_acao             = acao.cod_acao
           AND acao_dados.timestamp_acao_dados = acao.ultimo_timestamp_acao_dados
         WHERE acao.cod_acao NOT IN ( SELECT num_pao
                                        FROM orcamento.pao
                                       WHERE exercicio = ''2010'' )
  ORDER BY acao.cod_acao
    ';

    inExercicio = 2010;
    FOR reRegistro IN EXECUTE stSql
    LOOP
        FOR inCount IN 0..3
        LOOP
            stExercicioTMP := CAST(inExercicio + inCount AS VARCHAR);

            INSERT INTO orcamento.pao
                      ( exercicio
                      , num_pao
                      , nom_pao
                      , detalhamento )
                 VALUES ( stExercicioTMP
                      , reRegistro.cod_acao
                      , reRegistro.titulo
                      , reRegistro.detalhamento );

             INSERT INTO orcamento.pao_ppa_acao
                       ( exercicio
                       , num_pao
                       , cod_acao )
                  VALUES ( stExercicioTMP
                       , reRegistro.cod_acao
                       , reRegistro.cod_acao ); 
        END LOOP;
    END LOOP;

END;
$$ LANGUAGE 'plpgsql';

SELECT        corrigeAcao();
DROP FUNCTION corrigeAcao();

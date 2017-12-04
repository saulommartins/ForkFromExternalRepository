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
    * Arquivo de consulta do arquivo Cargos
    * Data de Criação   : 28/07/2009
    
    
    * @author Analista      Tonismar Régis Bernardo
    * @author Desenvolvedor Eduardo Paculski Schitz
    
    * @package URBEM
    * @subpackage 
    
    $Id:$
*/

CREATE OR REPLACE FUNCTION tcepb.recuperaCargos(VARCHAR, VARCHAR) RETURNS SETOF RECORD AS $$
DECLARE
    stEntidade                      ALIAS FOR $1;
    stMesAnoReferencia              ALIAS FOR $2;
    stSql                           VARCHAR;
    stSqlAux                        VARCHAR;
    stSqlInsert                     VARCHAR;
    reRegistro                      RECORD;
    inNumOrgaos                     INTEGER;
    inCodCargo                      INTEGER;
    inIdentificador                 INTEGER;
    inCodPeriodoMovimentacao        INTEGER;
BEGIN

    --verifica se a sequence cargos_tce existe
    IF ((SELECT 1 FROM pg_catalog.pg_statio_user_sequences WHERE relname='cargos_tce') IS NOT NULL) THEN
        SELECT NEXTVAL('tcepb.cargos_tce')
          INTO inIdentificador;
    ELSE
        CREATE SEQUENCE tcepb.cargos_tce START 1;
        SELECT NEXTVAL('tcepb.cargos_tce')
          INTO inIdentificador;
    END IF;

    stSql := '
    CREATE TEMPORARY TABLE tmp_retorno_'|| inIdentificador ||' (
           cod_cargo            INTEGER
         , descricao            VARCHAR
         , cod_tipo_cargo_tce   INTEGER
         , escolaridade         INTEGER
         , cod_cbo              INTEGER
    ) ';

    EXECUTE stSql;

    inCodPeriodoMovimentacao := selectIntoInteger('SELECT cod_periodo_movimentacao
                                                     FROM folhapagamento'|| stEntidade ||'.periodo_movimentacao
                                                    WHERE to_char(dt_final, ''mmyyyy'') = '|| quote_literal(stMesAnoReferencia) ||' ');

    stSql := '
    CREATE TEMPORARY TABLE tmp_valores_'|| inIdentificador ||' AS (
        SELECT cod_cargo
             , descricao
             , cod_tipo_cargo_tce
             , escolaridade
             , cod_cbo
          FROM ( SELECT cargo.cod_cargo
                      , cargo.descricao
                      , tipo_cargo_tce.cod_tipo_cargo_tce
                      , 0 AS escolaridade
                      , cbo.codigo AS cod_cbo
                      , getVagasOcupadasCargo(sub_divisao.cod_regime, sub_divisao.cod_sub_divisao, cargo.cod_cargo, '|| inCodPeriodoMovimentacao ||', false, '|| quote_literal(stEntidade) ||') AS vagas_ocupadas
                   FROM pessoal'|| stEntidade ||'.cargo
                   JOIN pessoal'|| stEntidade ||'.cargo_sub_divisao
                     ON cargo_sub_divisao.cod_cargo = cargo.cod_cargo
                   JOIN pessoal'|| stEntidade ||'.sub_divisao
                     ON sub_divisao.cod_sub_divisao = cargo_sub_divisao.cod_sub_divisao
                   JOIN pessoal'|| stEntidade ||'.de_para_tipo_cargo
                     ON de_para_tipo_cargo.cod_sub_divisao = cargo_sub_divisao.cod_sub_divisao
                   JOIN tcepb.tipo_cargo_tce
                     ON tipo_cargo_tce.cod_tipo_cargo_tce = de_para_tipo_cargo.cod_tipo_cargo_tce
                   JOIN pessoal'|| stEntidade ||'.cbo_cargo
                     ON cbo_cargo.cod_cargo = cargo.cod_cargo
                    AND cbo_cargo.timestamp = ( SELECT MAX(timestamp)
                                                  FROM pessoal'|| stEntidade ||'.cbo_cargo
                                                 WHERE cbo_cargo.cod_cargo = cargo.cod_cargo
                                                   AND timestamp <= ( SELECT MAX(timestamp)
                                                                        FROM pessoal'|| stEntidade ||'.cargo_sub_divisao
                                                                       WHERE cargo_sub_divisao.cod_cargo = cargo.cod_cargo
                                                                         AND timestamp <= ( SELECT MAX(timestamp) AS timestamp
                                                                                              FROM folhapagamento'|| stEntidade ||'.periodo_movimentacao_situacao
                                                                                             WHERE cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||' ) ) )
                   JOIN pessoal'|| stEntidade ||'.cbo
                     ON cbo.cod_cbo = cbo_cargo.cod_cbo
                  WHERE NOT EXISTS ( SELECT 1
                                       FROM pessoal'|| stEntidade ||'.arquivo_cargos
                                      WHERE arquivo_cargos.cod_cargo          = cargo.cod_cargo
                                        AND arquivo_cargos.cod_tipo_cargo_tce = tipo_cargo_tce.cod_tipo_cargo_tce
                                        AND arquivo_cargos.periodo            <> '|| quote_literal(stMesAnoReferencia) ||')
               GROUP BY cargo.cod_cargo
                      , cargo.descricao
                      , tipo_cargo_tce.cod_tipo_cargo_tce
                      , cargo_sub_divisao.timestamp
                      , cbo.codigo
                      , sub_divisao.cod_regime
                      , sub_divisao.cod_sub_divisao
               ORDER BY cargo.cod_cargo, cargo_sub_divisao.timestamp
              ) AS tabela
          WHERE vagas_ocupadas > 0  
       GROUP BY cod_cargo
              , descricao
              , cod_tipo_cargo_tce
              , escolaridade
              , cod_cbo
       ORDER BY cod_cargo
    )
    ';

    EXECUTE stSql;

    EXECUTE 'DELETE FROM pessoal'|| stEntidade ||'.arquivo_cargos WHERE periodo = '|| quote_literal(stMesAnoReferencia) ||' ';

    stSql := ' SELECT * FROM tmp_valores_'|| inIdentificador;

    FOR reRegistro IN EXECUTE stSql LOOP
        inCodCargo := selectIntoInteger('SELECT cod_cargo
                                           FROM pessoal'|| stEntidade ||'.arquivo_cargos
                                          WHERE arquivo_cargos.cod_cargo          = '|| reRegistro.cod_cargo ||'
                                            AND arquivo_cargos.cod_tipo_cargo_tce = '|| reRegistro.cod_tipo_cargo_tce ||' ');

        IF (inCodCargo IS NULL) THEN
            stSqlAux := '
            INSERT INTO pessoal'|| stEntidade ||'.arquivo_cargos ( cod_cargo
                                                               , cod_tipo_cargo_tce
                                                               , periodo ) 
                                                        VALUES ( '|| reRegistro.cod_cargo ||'
                                                               , '|| reRegistro.cod_tipo_cargo_tce ||'
                                                               , '|| quote_literal(stMesAnoReferencia) ||' )';

            EXECUTE stSqlAux;
        END IF;

        stSqlInsert := '
            INSERT INTO tmp_retorno_'|| inIdentificador ||' ( cod_cargo
                                                          , descricao 
                                                          , cod_tipo_cargo_tce
                                                          , escolaridade
                                                          , cod_cbo )
                                                   VALUES ( '|| reRegistro.cod_cargo ||'
                                                          , '|| quote_literal(reRegistro.descricao) ||'
                                                          , '|| reRegistro.cod_tipo_cargo_tce ||'
                                                          , '|| reRegistro.escolaridade ||'
                                                          , '|| reRegistro.cod_cbo ||' ) ';

        EXECUTE stSqlInsert;
    END LOOP;

    stSql := ' SELECT * 
                 FROM tmp_retorno_'|| inIdentificador ||'
             GROUP BY cod_cargo
                    , descricao
                    , cod_tipo_cargo_tce
                    , escolaridade
                    , cod_cbo
             ORDER BY cod_cargo';

    FOR reRegistro IN EXECUTE stSql LOOP
        RETURN NEXT reRegistro;
    END LOOP;

    EXECUTE 'DROP TABLE tmp_retorno_'|| inIdentificador;
    EXECUTE 'DROP TABLE tmp_valores_'|| inIdentificador;
    
END;
$$ LANGUAGE 'plpgsql';

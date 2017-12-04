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
CREATE OR REPLACE FUNCTION recuperaFiltroGPS(VARCHAR, VARCHAR, VARCHAR) RETURNS VARCHAR AS $$
DECLARE
    stEntidade                  ALIAS FOR $1;
    stTipoFiltro                ALIAS FOR $2;
    stCodigos                   ALIAS FOR $3;
    stRetorno                   VARCHAR;
    stSQL                       VARCHAR;
    reRegistro                  RECORD;
BEGIN
    IF stTipoFiltro = 'contrato_todos' OR  stTipoFiltro = 'cgm_contrato_todos' THEN
        stRetorno := stCodigos;
    END IF;

    IF stTipoFiltro = 'lotacao_grupo' THEN
        stSQL := ' SELECT descricao
                     FROM ORGANOGRAMA.ORGAO_DESCRICAO
                    WHERE cod_orgao IN ('||stCodigos||') ';
    END IF;

    IF stTipoFiltro = 'local_grupo' THEN
        stSQL := ' SELECT descricao
                     FROM ORGANOGRAMA.LOCAL 
                    WHERE cod_local IN ('||stCodigos||') ';
    END IF;

    IF stTipoFiltro = 'regime_subdivisao_grupo' THEN
        stSQL := ' SELECT regime.descricao||'' - ''||sub_divisao.descricao as descricao
                     FROM pessoal'||stEntidade||'.sub_divisao
               INNER JOIN pessoal'||stEntidade||'.regime
                          using(cod_regime)
                    WHERE cod_sub_divisao IN ('||stCodigos||') ';
    END IF;
    IF stSQL IS NOT NULL THEN
        stRetorno := '';
        FOR reRegistro IN EXECUTE stSql LOOP
            IF stRetorno = '' THEN
               stRetorno := reRegistro.descricao;
            ELSE
               stRetorno := stRetorno||', '||reRegistro.descricao;
            END IF;
        END LOOP;
    END IF;
    RETURN stRetorno;
END;
$$ LANGUAGE 'plpgsql';


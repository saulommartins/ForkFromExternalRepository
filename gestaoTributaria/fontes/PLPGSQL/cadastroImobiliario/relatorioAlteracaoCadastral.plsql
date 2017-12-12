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
* Script de função PLPGSQL
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: relatorioAlteracaoCadastral.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Casos de uso: uc-05.01.09
*/

/*
$Log$
Revision 1.3  2006/09/15 10:19:51  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION imobiliario.fn_rl_alteracao_cadastral( VARCHAR , VARCHAR , VARCHAR ) RETURNS SETOF RECORD AS '
DECLARE
    stFiltroLote        ALIAS FOR $1;
    stFiltroImovel      ALIAS FOR $2;
    stDistinct          ALIAS FOR $3;
    stInscricao         VARCHAR   := '''';
    stSql               VARCHAR   := '''';
    reRegistro          RECORD;
    arRetorno           NUMERIC[];
    --crCursor            REFCURSOR;

BEGIN

--    IF stDistinct = ''TRUE'' THEN
--        stInscricao := '' DISTINCT ON( I.inscricao_municipal ) I.inscricao_municipal,'';
--    ELSE
        stInscricao := '' I.inscricao_municipal,'';
--    END IF;

    stSql := ''CREATE TEMPORARY TABLE tmp_lote_urbano_alteracao AS
                SELECT
                    ''||stInscricao||''
                    I.numero,
                    I.complemento,
                    C.numcgm,
                    C.nom_cgm||'''' - ''''||IP.cota as proprietario_cota,
                    L.cod_lote,
                    1 as tipo_lote,
                    LOC.cod_localizacao,
                    LOC.codigo_composto||'''' ''''||LOC.nom_localizacao as localizacao,
                    I.oid as oid_temp
                FROM
                    imobiliario.imovel               AS I,

                    imobiliario.proprietario         AS IP,
                    sw_cgm                           AS C,

                    imobiliario.imovel_lote          AS IL,
                    imobiliario.lote_urbano          AS L,

                    imobiliario.lote_localizacao     AS LL,
                    imobiliario.localizacao          AS LOC
                    WHERE
                    I.inscricao_municipal  = IL.inscricao_municipal AND
                    I.inscricao_municipal  = IP.inscricao_municipal AND
                    IP.numcgm              = C.numcgm               AND

                    IL.cod_lote            = L.cod_lote             AND
                    LL.cod_lote            = IL.cod_lote            AND
                    LL.cod_localizacao     = LOC.cod_localizacao
                    ''|| stFiltroLote ||''
                ORDER BY
                    I.inscricao_municipal
             '';
    EXECUTE stSql;
    stSql := ''CREATE TEMPORARY TABLE tmp_lote_rural_alteracao AS
                SELECT
                    ''||stInscricao||''
                    I.numero,
                    I.complemento,
                    C.numcgm,
                    C.nom_cgm||'''' - ''''||IP.cota as proprietario_cota,
                    L.cod_lote,
                    2 as tipo_lote,
                    LOC.cod_localizacao,
                    LOC.codigo_composto||'''' ''''||LOC.nom_localizacao as localizacao,
                    I.oid as oid_temp
                FROM
                    imobiliario.imovel               AS I,

                    imobiliario.proprietario         AS IP,
                    sw_cgm                           AS C,

                    imobiliario.imovel_lote          AS IL,
                    imobiliario.lote_rural           AS L,

                    imobiliario.lote_localizacao     AS LL,
                    imobiliario.localizacao          AS LOC
                WHERE
                    I.inscricao_municipal  = IL.inscricao_municipal AND
                    I.inscricao_municipal  = IP.inscricao_municipal AND
                    IP.numcgm              = C.numcgm               AND

                    IL.cod_lote            = L.cod_lote             AND
                    LL.cod_lote            = IL.cod_lote            AND
                    LL.cod_localizacao     = LOC.cod_localizacao
                    ''|| stFiltroLote ||''
                ORDER BY
                    I.inscricao_municipal
             '';
    EXECUTE stSql;

/*    CREATE UNIQUE INDEX unq_urbano_alteracao ON tmp_lote_urbano_alteracao(inscricao_municipal, numcgm);
    CREATE UNIQUE INDEX unq_rural_alteracao  ON tmp_lote_rural_alteracao (inscricao_municipal, numcgm);*/

    stSql := ''CREATE TEMPORARY TABLE tmp_imovel_alteracao AS
                SELECT inscricao_municipal, numcgm, numero, complemento, proprietario_cota, cod_lote, tipo_lote, cod_localizacao, localizacao FROM tmp_lote_urbano_alteracao
                    UNION
                SELECT inscricao_municipal, numcgm,  numero, complemento, proprietario_cota, cod_lote, tipo_lote, cod_localizacao, localizacao FROM tmp_lote_rural_alteracao
            '';
    EXECUTE stSql;

  /*  CREATE UNIQUE INDEX unq_imovel_alteracao ON tmp_imovel_alteracao (inscricao_municipal, numcgm);*/

    --seleciona todos imoveis
    stSql := ''
        SELECT
            I.inscricao_municipal,
            I.proprietario_cota,
            I.cod_lote,
            CASE
                WHEN I.tipo_lote = 1 THEN ''''Urbano''''
                WHEN I.tipo_lote = 2 THEN ''''Rural''''
            END AS tipo_lote,
            I.numero,
            I.complemento,
            I.cod_localizacao,
            I.localizacao,
            ICO.cod_condominio,
            II.creci,
            B.nom_bairro,
            TLO.nom_tipo||'''' ''''||NLO.nom_logradouro as logradouro,
            CASE
                WHEN BI.inscricao_municipal IS NULL THEN ''''Ativo''''
                WHEN BI.inscricao_municipal IS NOT NULL THEN ''''Baixado''''
            END AS situacao
        FROM
            tmp_imovel_alteracao AS I
            LEFT OUTER JOIN imobiliario.imovel_imobiliaria II ON
                II.inscricao_municipal = I.inscricao_municipal
            LEFT OUTER JOIN imobiliario.imovel_condominio ICO ON
                ICO.inscricao_municipal = I.inscricao_municipal
            LEFT OUTER JOIN imobiliario.baixa_imovel BI ON
                BI.inscricao_municipal = I.inscricao_municipal
            LEFT OUTER JOIN imobiliario.vw_matricula_imovel_atual MIA ON
                MIA.inscricao_municipal = I.inscricao_municipal,
            imobiliario.lote_bairro          AS LB,
            sw_bairro                        AS B,
            imobiliario.imovel_confrontacao  AS IC,
            imobiliario.confrontacao_trecho  AS CT,
            sw_logradouro                    AS LO,
            sw_nome_logradouro               AS NLO,
            sw_tipo_logradouro               AS TLO
        WHERE
            LB.cod_lote            = I.cod_lote             AND
            LB.cod_bairro          = B.cod_bairro           AND
            LB.cod_municipio       = B.cod_municipio        AND
            LB.cod_uf              = B.cod_uf               AND

            IC.inscricao_municipal = I.inscricao_municipal  AND
            IC.cod_lote            = I.cod_lote            AND

            CT.cod_confrontacao    = IC.cod_confrontacao    AND
            CT.cod_lote            = IC.cod_lote            AND
            CT.cod_logradouro      = LO.cod_logradouro      AND
            CT.principal           = true                  AND

            NLO.cod_logradouro     = LO.cod_logradouro      AND
            NLO.cod_tipo           = TLO.cod_tipo
            ''||stFiltroImovel;
    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN NEXT reRegistro;
    END LOOP;

/*    DROP INDEX unq_imovel_alteracao;
    DROP INDEX unq_urbano_alteracao;
    DROP INDEX unq_rural_alteracao;*/

    DROP TABLE tmp_lote_urbano_alteracao;
    DROP TABLE tmp_lote_rural_alteracao;
    DROP TABLE tmp_imovel_alteracao;

    RETURN;
END;
' LANGUAGE 'plpgsql';

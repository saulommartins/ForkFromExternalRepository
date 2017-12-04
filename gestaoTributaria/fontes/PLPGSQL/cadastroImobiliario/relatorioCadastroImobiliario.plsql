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
* $Id: relatorioCadastroImobiliario.plsql 64894 2016-04-12 18:12:52Z evandro $
*
* Casos de uso: uc-05.01.09
*/

CREATE OR REPLACE FUNCTION imobiliario.fn_rl_cadastro_imobiliario( VARCHAR , VARCHAR , VARCHAR, VARCHAR, VARCHAR, VARCHAR ) RETURNS SETOF RECORD AS $$
DECLARE
    stFiltroLote        ALIAS FOR $1;
    stFiltroImovel      ALIAS FOR $2;
    stDistinct          ALIAS FOR $3;
    stFiltAtribImovel   ALIAS FOR $4;
    stFiltAtribLote     ALIAS FOR $5;
    stFiltAtribEdif     ALIAS FOR $6;
    stTEMPAtribImob     VARCHAR   := '';
    stTEMPAtribLote     VARCHAR   := '';
    stTEMPAtribEdif     VARCHAR   := '';
    stInscricao         VARCHAR   := '';
    stSql               VARCHAR   := '';
    stAuxJoin           VARCHAR   := 'LEFT JOIN';
    reRegistro          RECORD;
    arRetorno           NUMERIC[];
    --crCursor            REFCURSOR;

BEGIN
    IF ( stFiltAtribImovel IS NULL ) OR ( stFiltAtribImovel = '' ) THEN 
        stTEMPAtribImob := ' GROUP BY atributo_imovel_valor.inscricao_municipal ';
    ELSE
        stTEMPAtribImob := stFiltAtribImovel;
    END IF;

    IF ( stFiltAtribLote IS NULL ) OR ( stFiltAtribLote = '' ) THEN 
        stTEMPAtribLote := ' GROUP BY cod_lote ';
    ELSE
        stTEMPAtribLote := stFiltAtribLote;
        stAuxJoin := 'INNER JOIN';
    END IF;

    IF ( stFiltAtribEdif IS NULL ) OR ( stFiltAtribEdif = '' ) THEN 
        stTEMPAtribEdif := '
            GROUP BY cod_construcao
                   , cod_tipo
        ';
    ELSE
        stTEMPAtribEdif := stFiltAtribEdif;
    END IF;

    IF stDistinct = 'TRUE' THEN
        stInscricao := ' DISTINCT ON( I.inscricao_municipal ) I.inscricao_municipal,';
    ELSE
        stInscricao := ' I.inscricao_municipal,';
    END IF;

    stSql := 'CREATE TEMPORARY TABLE tmp_lote_urbano AS
                SELECT DISTINCT ON ( I.inscricao_municipal ) I.inscricao_municipal
                     , I.numero
                     , I.complemento
                     , I.dt_cadastro
                     , C.numcgm
                     , C.nom_cgm||'' - ''||IP.cota as proprietario_cota
                     , L.cod_lote
                     , 1 as tipo_lote
                     , LL.valor
                     , LOC.cod_localizacao
                     , LOC.codigo_composto||'' ''||LOC.nom_localizacao as localizacao
                     , IL.timestamp
                     , I.cep
                     , I.oid as oid_temp
                  FROM imobiliario.imovel AS I
            INNER JOIN ( SELECT IIL.*
                           FROM imobiliario.imovel_lote IIL,
                              ( SELECT MAX (TIMESTAMP) AS TIMESTAMP
                                     , INSCRICAO_MUNICIPAL
                                  FROM imobiliario.imovel_lote
                              GROUP BY INSCRICAO_MUNICIPAL
                              ) AS IL
                          WHERE IIL.INSCRICAO_MUNICIPAL = IL.INSCRICAO_MUNICIPAL
                            AND IIL.TIMESTAMP = IL.TIMESTAMP
                       ) AS IL 
                    ON I.inscricao_municipal = IL.inscricao_municipal
            INNER JOIN imobiliario.proprietario AS IP
                    ON I.inscricao_municipal = IP.inscricao_municipal
            INNER JOIN sw_cgm AS C
                    ON IP.numcgm = C.numcgm
            INNER JOIN imobiliario.lote_urbano AS L
                    ON IL.cod_lote = L.cod_lote
            INNER JOIN imobiliario.lote_localizacao AS LL
                    ON LL.cod_lote = IL.cod_lote 
            INNER JOIN imobiliario.localizacao AS LOC
                    ON LL.cod_localizacao = LOC.cod_localizacao
            '||stAuxJoin||' ( SELECT DISTINCT tmp.cod_lote
                           FROM imobiliario.atributo_lote_urbano_valor AS tmp
                     INNER JOIN ( SELECT max(timestamp) AS timestamp
                                       , cod_lote
                                    FROM imobiliario.atributo_lote_urbano_valor
                                    '|| stTEMPAtribLote ||'
                                )AS tmp2
                             ON tmp.cod_lote = tmp2.cod_lote
                            AND tmp.timestamp = tmp2.timestamp
                       ) AS aluv
                    ON aluv.cod_lote = IL.cod_lote
                 WHERE LL.cod_localizacao = LOC.cod_localizacao
                       '|| stFiltroLote ||'
    ';

    EXECUTE stSql;

    stSql := 'CREATE TEMPORARY TABLE tmp_lote_rural AS
                    SELECT DISTINCT ON ( I.inscricao_municipal ) I.inscricao_municipal
                         , I.numero
                         , I.complemento
                         , C.numcgm
                         , I.dt_cadastro
                         , C.nom_cgm||'' - ''||IP.cota as proprietario_cota
                         , L.cod_lote
                         , 2 as tipo_lote
                         , LL.valor
                         , LOC.cod_localizacao
                         , LOC.codigo_composto||'' ''||LOC.nom_localizacao as localizacao
                         , IL.timestamp
                         , I.cep
                         , I.oid as oid_temp
                      FROM imobiliario.imovel AS I
                INNER JOIN ( SELECT IIL.*
                               FROM imobiliario.imovel_lote IIL
                                  , ( SELECT MAX (TIMESTAMP) AS TIMESTAMP
                                           , INSCRICAO_MUNICIPAL
                                        FROM imobiliario.imovel_lote
                                    GROUP BY INSCRICAO_MUNICIPAL
                                    ) AS IL
                              WHERE IIL.INSCRICAO_MUNICIPAL = IL.INSCRICAO_MUNICIPAL
                                AND IIL.TIMESTAMP = IL.TIMESTAMP
                           ) AS IL 
                        ON I.inscricao_municipal = IL.inscricao_municipal
                INNER JOIN imobiliario.proprietario AS IP
                        ON I.inscricao_municipal = IP.inscricao_municipal
                INNER JOIN sw_cgm AS C
                        ON IP.numcgm = C.numcgm
                INNER JOIN imobiliario.lote_rural AS L
                        ON IL.cod_lote = L.cod_lote
                INNER JOIN imobiliario.lote_localizacao AS LL
                        ON LL.cod_lote = IL.cod_lote 
                INNER JOIN imobiliario.localizacao AS LOC
                        ON LL.cod_localizacao = LOC.cod_localizacao
                '||stAuxJoin||' ( SELECT DISTINCT tmp.cod_lote
                               FROM imobiliario.atributo_lote_rural_valor AS tmp
                         INNER JOIN ( SELECT max(timestamp) AS timestamp
                                           , cod_lote
                                        FROM imobiliario.atributo_lote_rural_valor
                                        '|| stTEMPAtribLote ||'
                                    )AS tmp2
                                 ON tmp.cod_lote = tmp2.cod_lote
                                AND tmp.timestamp = tmp2.timestamp
                           ) AS aluv
                        ON aluv.cod_lote = IL.cod_lote
                     WHERE LL.cod_localizacao = LOC.cod_localizacao
                     '|| stFiltroLote ||'
    ';

    EXECUTE stSql;

    --CREATE UNIQUE INDEX unq_urbano ON tmp_lote_urbano(inscricao_municipal,timestamp);
    --CREATE UNIQUE INDEX unq_rural  ON tmp_lote_rural (inscricao_municipal,timestamp);

    stSql := 'CREATE TEMPORARY TABLE tmp_imovel AS
                  SELECT inscricao_municipal
                       , numero
                       , complemento
                       , numcgm
                       , dt_cadastro
                       , cep
                       , proprietario_cota
                       , cod_lote
                       , tipo_lote
                       , valor
                       , cod_localizacao
                       , localizacao 
                    FROM tmp_lote_urbano
                   UNION SELECT inscricao_municipal
                              , numero
                              , complemento
                              , numcgm
                              , dt_cadastro
                              , cep 
                              , proprietario_cota
                              , cod_lote
                              , tipo_lote
                              , valor
                              , cod_localizacao
                              , localizacao 
                           FROM tmp_lote_rural
    ';

    EXECUTE stSql;

    --CREATE UNIQUE INDEX unq_imovel ON tmp_imovel (inscricao_municipal,oid_temp);

    --seleciona todos imoveis
    stSql := '
        SELECT DISTINCT I.inscricao_municipal
             , I.proprietario_cota
             , I.cod_lote
             , I.dt_cadastro
             , CASE WHEN I.tipo_lote = 1 THEN ''Urbano''
                    WHEN I.tipo_lote = 2 THEN ''Rural''
                     END AS tipo_lote
             , I.valor as valor_lote
             , ( imobiliario.fn_busca_endereco_imovel_formatado ( I.inscricao_municipal ) ) as endereco
             , I.cep as cep
             , I.cod_localizacao
             , I.localizacao
             , ICO.cod_condominio
             , II.creci
             , B.nom_bairro
             , TLO.nom_tipo||'' ''||NLO.nom_logradouro as logradouro
             , CASE WHEN ((BI.inscricao_municipal IS NULL) or (BI.dt_termino IS NOT NULL)) THEN ''Ativo''
                    WHEN BI.inscricao_municipal IS NOT NULL THEN ''Baixado''
                     END AS situacao
          FROM tmp_imovel AS I
     LEFT JOIN ( SELECT DISTINCT tmp.inscricao_municipal
                   FROM imobiliario.atributo_imovel_valor AS tmp
             INNER JOIN ( SELECT atributo_imovel_valor.inscricao_municipal
                               , max(atributo_imovel_valor.timestamp) AS timestamp
                            FROM imobiliario.atributo_imovel_valor
                            '|| stTEMPAtribImob ||'
                        )AS tmp2
                     ON tmp.inscricao_municipal = tmp2.inscricao_municipal
                    AND tmp.timestamp = tmp2.timestamp
             INNER JOIN tmp_imovel
                     ON tmp_imovel.inscricao_municipal = tmp.inscricao_municipal
               )AS aiv
            ON aiv.inscricao_municipal = I.inscricao_municipal
     LEFT JOIN ( SELECT DISTINCT IUA.inscricao_municipal
                   FROM imobiliario.atributo_tipo_edificacao_valor AS tmp
             INNER JOIN ( SELECT max(timestamp) AS timestamp
                               , cod_construcao
                               , cod_tipo
                            FROM imobiliario.atributo_tipo_edificacao_valor
                            '|| stTEMPAtribEdif ||'
                        )AS tmp2
                     ON tmp.cod_construcao = tmp2.cod_construcao
                    AND tmp.cod_tipo = tmp2.cod_tipo
                    AND tmp.timestamp = tmp2.timestamp
             INNER JOIN imobiliario.unidade_autonoma IUA 
                     ON tmp.cod_construcao = IUA.cod_construcao
                    AND tmp.cod_tipo = IUA.cod_tipo
             INNER JOIN tmp_imovel
                     ON tmp_imovel.inscricao_municipal = IUA.inscricao_municipal
               )AS atev
            ON atev.inscricao_municipal = I.inscricao_municipal
     LEFT JOIN imobiliario.imovel_imobiliaria II 
            ON II.inscricao_municipal = I.inscricao_municipal
     LEFT JOIN imobiliario.imovel_condominio ICO 
            ON ICO.inscricao_municipal = I.inscricao_municipal
     LEFT JOIN ( SELECT BAI.*
                   FROM imobiliario.baixa_imovel AS BAI
                      , ( SELECT MAX (TIMESTAMP) AS TIMESTAMP
                               , inscricao_municipal
                            FROM imobiliario.baixa_imovel
                        GROUP BY inscricao_municipal
                        ) AS BI
                  WHERE BAI.inscricao_municipal = BI.inscricao_municipal 
                    AND BAI.timestamp = BI.timestamp 
               ) BI 
            ON BI.inscricao_municipal = I.inscricao_municipal
     LEFT JOIN imobiliario.lote_bairro  LB
            ON LB.cod_lote = I.cod_lote
     LEFT JOIN sw_bairro B
            ON LB.cod_bairro          = B.cod_bairro           
           AND LB.cod_municipio       = B.cod_municipio        
           AND LB.cod_uf              = B.cod_uf
     LEFT JOIN imobiliario.imovel_confrontacao  IC
            ON IC.inscricao_municipal = I.inscricao_municipal  
           AND IC.cod_lote            = I.cod_lote
     LEFT JOIN imobiliario.confrontacao_trecho  CT
            ON CT.cod_confrontacao    = IC.cod_confrontacao    
           AND CT.cod_lote            = IC.cod_lote            
           AND CT.principal           = true
     LEFT JOIN sw_logradouro LO
            ON CT.cod_logradouro      = LO.cod_logradouro
     LEFT JOIN ( SELECT snl.*
                   FROM ( SELECT NL.* 
                            from ( SELECT cod_logradouro
                                        , cod_tipo
                                        , nom_logradouro
                                        , max(timestamp) as timestamp
                                     FROM sw_nome_logradouro
                                 GROUP BY cod_logradouro
                                        , cod_tipo
                                        , nom_logradouro 
                                 ORDER BY 1
                                 ) NL
                       LEFT JOIN sw_tipo_logradouro TLO
                              ON NL.cod_tipo = TLO.cod_tipo
                        ) as snl
                   JOIN ( SELECT cod_logradouro
                               , max(timestamp) as timestamp
                            FROM sw_nome_logradouro
                        GROUP BY cod_logradouro
                        ) as snlm
                     ON snlm.cod_logradouro = snl.cod_logradouro 
                    and snlm.timestamp      = snl.timestamp
               )  NLO
            ON NLO.cod_logradouro     = LO.cod_logradouro
     LEFT JOIN sw_tipo_logradouro               TLO
            ON NLO.cod_tipo           = TLO.cod_tipo
         WHERE i.inscricao_municipal is not NULL
         '||stFiltroImovel||' 
      ';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN NEXT reRegistro;
    END LOOP;

    DROP TABLE tmp_lote_urbano;
    DROP TABLE tmp_lote_rural;
    DROP TABLE tmp_imovel;

    RETURN;
END;
$$LANGUAGE 'plpgsql';

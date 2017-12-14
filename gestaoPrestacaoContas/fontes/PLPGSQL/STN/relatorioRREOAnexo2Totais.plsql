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
    * PL do RREOAnexo2 - Arquivo STN da GPC 
    * Data de Criação   : 01/06/2008


    * @author Analista      Alexandre Melo
    * @author Desenvolvedor Alexandre Melo
    
    * @package URBEM
    * @subpackage 

    $Id:$
*/
/*
    ESTA PL SOMA OS VALORES RESULTANTES DE relatorioRREOAnexo2.plsql E relatorioRREOAnexo2Intra.plsql.
    Prog.: Alexandre Melo
*/
CREATE OR REPLACE FUNCTION stn.fn_anexo2_totais() RETURNS SETOF RECORD AS $$
DECLARE
    reRegistro      RECORD;
    stSql           VARCHAR := '';
BEGIN
         
    /*
        ****** SOMA tmp_orcamentaria COM tm_intra_orcamentaria *******
    */
    
    stSql := '
    SELECT 
           sum(vl_original)                as vl_original 
         , sum(vl_suplementacoes)          as vl_suplementacoes
         , sum(vl_empenhado_bimestre)      as vl_empenhado_bimestre
         , sum(vl_empenhado_ate_bimestre)  as vl_empenhado_ate_bimestre
         , sum(vl_liquidado_bimestre)      as vl_liquidado_bimestre
         , sum(vl_liquidado_ate_bimestre)  as vl_liquidado_ate_bimestre
      FROM
           ( SELECT   
                    sum(vl_original)               as vl_original
                  , sum(vl_suplementacoes)         as vl_suplementacoes
                  , sum(vl_empenhado_bimestre)     as vl_empenhado_bimestre
                  , sum(vl_empenhado_ate_bimestre) as vl_empenhado_ate_bimestre
                  , sum(vl_liquidado_bimestre)     as vl_liquidado_bimestre
                  , sum(vl_liquidado_ate_bimestre) as vl_liquidado_ate_bimestre
               FROM
                    tmp_orcamentarias
              WHERE
                    cod_subfuncao = 0

             UNION ALL 

             SELECT
                    vl_original
                  , vl_suplementacoes
                  , vl_empenhado_bimestre
                  , vl_empenhado_ate_bimestre
                  , vl_liquidado_bimestre
                  , vl_liquidado_ate_bimestre
               FROM
                    tmp_intra_orcamentarias
            ) AS tbl ';

    EXECUTE stSql;  

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN next reRegistro;
    END LOOP;
    
    RETURN;

END;
$$
language plpgsql;
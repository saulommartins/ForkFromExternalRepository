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
/**
Arquivo de mapeamento para a função que busca os dados dos serviços de terceiros
    * Data de Criação   : 30/01/2009

    * @author Analista      Tonismar Regis Bernardo
    * @author Desenvolvedor Lucas Andrades Mendes
    * @package URBEM
    * @subpackage

    $Id:$
*/

CREATE OR REPLACE FUNCTION tcemg.fn_variacao_patrimonial(VARCHAR, VARCHAR, INTEGER) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio         ALIAS FOR $1;
    stCodEntidade       ALIAS FOR $2;
    inMes               ALIAS FOR $3;
    stSql               VARCHAR := '';
    arDatas             VARCHAR[];
    reRegistro          RECORD;

BEGIN

arDatas := publico.mes(stExercicio,inMes);

CREATE TEMPORARY TABLE tmp_arquivo AS
            SELECT * 
            FROM contabilidade.fn_rl_balancete_verificacao( stExercicio
                                                            , ' cod_entidade IN  (2) '
                                                            , arDatas[0]
                                                            , arDatas[1]
                                                            ,'A'::CHAR)
            as retorno
                        ( cod_estrutural varchar                                                    
                        ,nivel integer                                                               
                        ,nom_conta varchar                                                           
                        ,cod_sistema integer                                                         
                        ,indicador_superavit char(12)                                                    
                        ,vl_saldo_anterior numeric                                                   
                        ,vl_saldo_debitos  numeric                                                   
                        ,vl_saldo_creditos numeric                                                   
                        ,vl_saldo_atual    numeric                                                   
                        )
            WHERE cod_estrutural like '3.0%'
            OR cod_estrutural like '4.0%' ;

EXECUTE stSql;

    stSql := '  SELECT   '||inMes||'
                        ,(SELECT vl_saldo_atual FROM tmp_arquivo WHERE cod_estrutural like ''3.0%'' ) as deficit
                        ,(SELECT vl_saldo_atual FROM tmp_arquivo WHERE cod_estrutural like ''4.0%'' ) as superavit
                FROM tmp_arquivo LIMIT 1; 
            ';

FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN NEXT reRegistro;
    END LOOP;

    DROP TABLE tmp_arquivo;

    RETURN;

END;
$$ LANGUAGE 'plpgsql';


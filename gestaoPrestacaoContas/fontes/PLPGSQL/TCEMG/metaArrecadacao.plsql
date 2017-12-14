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
    * Arquivo de mapeamento para a função que busca os dados das Metas de Arrecadação de Receita
    * Data de Criação   : 23/01/2008


    * @author Analista      Tonismar Regis Bernardo
    * @author Desenvolvedor Eduardo Paculski Schitz
    
    * @package URBEM
    * @subpackage 

    $Id:$
*/

CREATE OR REPLACE FUNCTION tcemg.fn_meta_arrecadacao(VARCHAR, VARCHAR, INTEGER) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio         ALIAS FOR $1;
    stCodEntidade       ALIAS FOR $2;
    inBimestre          ALIAS FOR $3;
    stSql               VARCHAR := '';
    reRegistro          RECORD;

BEGIN

    stSql := '
              SELECT previsao_receita.periodo AS bimestre
                   , SUM(previsao_receita.vl_periodo) AS valor_meta
                FROM orcamento.previsao_receita
                
          INNER JOIN orcamento.receita
                  ON receita.cod_receita = previsao_receita.cod_receita
                 AND receita.exercicio   = previsao_receita.exercicio
                 
               WHERE previsao_receita.exercicio = '|| quote_literal(stExercicio) || '
                 AND previsao_receita.periodo   = ' || inBimestre || '
                 AND receita.cod_entidade IN (' || stCodEntidade || ')
                 
            GROUP BY previsao_receita.periodo
          ';

    EXECUTE stSql;

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN NEXT reRegistro;
    END LOOP;

    RETURN;

END;
$$ LANGUAGE 'plpgsql';                                                                  
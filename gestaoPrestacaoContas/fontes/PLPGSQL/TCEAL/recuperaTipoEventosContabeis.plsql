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
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU ''LICENCA.txt'' *
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
* $Revision: 12203 $
* $Name$
* $Author: cleisson $
* $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $
*
* Casos de uso: uc-02.02.04
*/

/*
$Log$
Revision 1.6  2006/07/05 20:37:31  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION recupera_tipo_eventos_contabeis(VARCHAR) RETURNS SETOF RECORD AS $$
DECLARE
    reRegistro           RECORD;
    stExercicio          ALIAS FOR $1;
    stSql                VARCHAR := '';

BEGIN

    stSql := 'CREATE TEMPORARY TABLE tmp_conta AS
        SELECT                                                                        
                plano_conta.cod_conta,
                cod_estrutural,
                ''credito'' AS tipo_conta,
                1 AS sequencial

          FROM contabilidade.plano_analitica          
          
          JOIN contabilidade.conta_credito         
            ON conta_credito.cod_plano = plano_analitica.cod_plano
           AND conta_credito.exercicio = plano_analitica.exercicio

          JOIN contabilidade.plano_conta          
            ON plano_conta.cod_conta = plano_analitica.cod_conta
           AND plano_conta.exercicio = plano_analitica.exercicio                                                             
         
         WHERE plano_analitica.exercicio = ' || quote_literal(stExercicio) || '

         UNION

         SELECT                                                                                  
                plano_conta.cod_conta,
                cod_estrutural,
                ''debito'' AS tipo_conta,
                2 AS sequencial

           FROM contabilidade.plano_analitica          
                             
           JOIN contabilidade.conta_debito          
             ON conta_debito.cod_plano = plano_analitica.cod_plano    
            AND conta_debito.exercicio = plano_analitica.exercicio                                                          
                             
           JOIN contabilidade.plano_conta          
             ON plano_conta.cod_conta = plano_analitica.cod_conta   
            AND plano_conta.exercicio = plano_analitica.exercicio 

          WHERE plano_analitica.exercicio = ' || quote_literal(stExercicio) || '
          ';
    EXECUTE stSql;

    stSql := '

            SELECT * FROM (

                SELECT cod_conta, cod_estrutural, tipo_conta, 1 AS cod_evento, ''Empenho''::VARCHAR AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''6.2.2.1.1.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 1 AS cod_evento, ''Empenho''::VARCHAR AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''6.2.2.1.3.01.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 1 AS cod_evento, ''Empenho''::VARCHAR AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''8.2.1.1.1.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 1 AS cod_evento, ''Empenho''::VARCHAR AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''8.1.2.3.0.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 1 AS cod_evento, ''Empenho''::VARCHAR AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''5.2.2.9.2.01.01.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 1 AS cod_evento, ''Empenho''::VARCHAR AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''6.2.2.9.2.01.01.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 2 AS cod_evento, ''Anulação de Empenho'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''6.2.2.1.3.01.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 2 AS cod_evento, ''Anulação de Empenho'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''6.2.2.1.1.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 2 AS cod_evento, ''Anulação de Empenho'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''8.2.1.1.2.00.00.00.00.00%'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 2 AS cod_evento, ''Anulação de Empenho'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''8.2.1.1.1.00.00.00.00.00%'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 2 AS cod_evento, ''Anulação de Empenho'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''6.2.2.9.2.01.01.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 2 AS cod_evento, ''Anulação de Empenho'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''5.2.2.9.2.01.01.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 3 AS cod_evento, ''Liquidação'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''6.2.2.1.3.01.01.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 3 AS cod_evento, ''Liquidação'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''6.2.2.1.3.03.01.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 3 AS cod_evento, ''Liquidação'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''8.2.1.1.2.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 3 AS cod_evento, ''Liquidação'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''8.2.1.1.3.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 3 AS cod_evento, ''Liquidação'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''8.1.2.3.0.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 3 AS cod_evento, ''Liquidação'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''8.2.1.1.3.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 3 AS cod_evento, ''Liquidação'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''3.3.2.0.0.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 3 AS cod_evento, ''Liquidação'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''2.1.3.1.1.01.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 3 AS cod_evento, ''Liquidação'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''1.1.5.6.0.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 3 AS cod_evento, ''Liquidação'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''2.1.3.1.1.01.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 3 AS cod_evento, ''Liquidação'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''1.2.3.0.0.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 3 AS cod_evento, ''Liquidação'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''2.1.3.1.1.01.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 3 AS cod_evento, ''Liquidação'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''3.0.0.0.0.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 3 AS cod_evento, ''Liquidação'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''6.2.2.9.2.01.03.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 3 AS cod_evento, ''Liquidação'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''6.2.2.9.2.01.01.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 4 AS cod_evento, ''Estorno de Liquidação'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''6.2.2.1.3.03.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 4 AS cod_evento, ''Estorno de Liquidação'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''6.2.2.1.3.01.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 4 AS cod_evento, ''Estorno de Liquidação'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''8.2.1.1.3.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 4 AS cod_evento, ''Estorno de Liquidação'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''8.2.1.1.2.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 4 AS cod_evento, ''Estorno de Liquidação'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''8.1.2.3.1.02.02.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 4 AS cod_evento, ''Estorno de Liquidação'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''8.1.2.3.0.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 4 AS cod_evento, ''Estorno de Liquidação'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''2.1.3.1.1.01.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 4 AS cod_evento, ''Estorno de Liquidação'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''3.3.2.0.0.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 4 AS cod_evento, ''Estorno de Liquidação'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''2.1.3.1.1.01.01.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 4 AS cod_evento, ''Estorno de Liquidação'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''1.1.5.6.0.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 4 AS cod_evento, ''Estorno de Liquidação'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''2.1.3.1.1.01.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 4 AS cod_evento, ''Estorno de Liquidação'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''1.2.3.0.0.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 4 AS cod_evento, ''Estorno de Liquidação'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''2.1.1.1.0.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 4 AS cod_evento, ''Estorno de Liquidação'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''3.0.0.0.0.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 4 AS cod_evento, ''Estorno de Liquidação'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''6.2.2.9.2.01.01.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 4 AS cod_evento, ''Estorno de Liquidação'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''6.2.2.9.2.01.03.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 5 AS cod_evento, ''Pagamento Orçamentário'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''2.1.3.1.1.01.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 5 AS cod_evento, ''Pagamento Orçamentário'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''1.1.1.1.0.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 5 AS cod_evento, ''Pagamento Orçamentário'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''6.2.2.1.3.03.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 5 AS cod_evento, ''Pagamento Orçamentário'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''6.2.2.1.3.04.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 5 AS cod_evento, ''Pagamento Orçamentário'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''8.2.1.1.3.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 5 AS cod_evento, ''Pagamento Orçamentário'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''8.2.1.1.4.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 5 AS cod_evento, ''Pagamento Orçamentário'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''2.1.1.1.0.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 5 AS cod_evento, ''Pagamento Orçamentário'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''1.1.1.1.0.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 6 AS cod_evento, ''Estorno de Pagamento Orçamentário'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''1.1.1.1.0.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 6 AS cod_evento, ''Estorno de Pagamento Orçamentário'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''2.1.3.1.1.01.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 6 AS cod_evento, ''Estorno de Pagamento Orçamentário'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''6.2.2.1.3.04.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 6 AS cod_evento, ''Estorno de Pagamento Orçamentário'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''6.2.2.1.3.03.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 6 AS cod_evento, ''Estorno de Pagamento Orçamentário'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''8.2.1.1.4.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 6 AS cod_evento, ''Estorno de Pagamento Orçamentário'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''8.2.1.1.3.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 6 AS cod_evento, ''Estorno de Pagamento Orçamentário'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''1.1.1.1.0.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 6 AS cod_evento, ''Estorno de Pagamento Orçamentário'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''2.1.1.1.0.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 7 AS cod_evento, ''Almoxarifado - Distribuição do Material de Consumo'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''3.3.1.1.0.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 7 AS cod_evento, ''Almoxarifado - Distribuição do Material de Consumo'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''1.1.5.6.0.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 8 AS cod_evento, ''Arrecadação de Receita Orçamentária'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''6.2.1.1.0.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 8 AS cod_evento, ''Arrecadação de Receita Orçamentária'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''6.2.1.2.0.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 8 AS cod_evento, ''Arrecadação de Receita Orçamentária'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''7.2.1.1.0.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 8 AS cod_evento, ''Arrecadação de Receita Orçamentária'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''8.2.1.1.1.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 8 AS cod_evento, ''Arrecadação de Receita Orçamentária'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''1.1.1.1.0.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 8 AS cod_evento, ''Arrecadação de Receita Orçamentária'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''1.1.2.2.0.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 8 AS cod_evento, ''Arrecadação de Receita Orçamentária'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''1.1.1.1.0.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 8 AS cod_evento, ''Arrecadação de Receita Orçamentária'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''4.1.1.0.0.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 8 AS cod_evento, ''Arrecadação de Receita Orçamentária'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''1.1.1.1.0.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 8 AS cod_evento, ''Arrecadação de Receita Orçamentária'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''2.1.2.0.0.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 8 AS cod_evento, ''Arrecadação de Receita Orçamentária'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''1.1.1.1.0.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 8 AS cod_evento, ''Arrecadação de Receita Orçamentária'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''1.2.3.0.0.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 8 AS cod_evento, ''Arrecadação de Receita Orçamentária'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''1.1.1.1.0.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 8 AS cod_evento, ''Arrecadação de Receita Orçamentária'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''1.1.2.3.1.01.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 8 AS cod_evento, ''Arrecadação de Receita Orçamentária'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''1.1.1.1.0.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 8 AS cod_evento, ''Arrecadação de Receita Orçamentária'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''1.1.2.4.1.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 8 AS cod_evento, ''Arrecadação de Receita Orçamentária'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''1.1.1.1.0.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 8 AS cod_evento, ''Arrecadação de Receita Orçamentária'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''8.3.2.3.0.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 8 AS cod_evento, ''Arrecadação de Receita Orçamentária'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''8.3.2.4.0.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 9 AS cod_evento, ''Estorno Arrecadação de Receita Orçamentária'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''6.2.1.2.0.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 9 AS cod_evento, ''Estorno Arrecadação de Receita Orçamentária'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''6.2.1.1.0.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 9 AS cod_evento, ''Estorno Arrecadação de Receita Orçamentária'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''8.2.1.1.1.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 9 AS cod_evento, ''Estorno Arrecadação de Receita Orçamentária'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''7.2.1.1.0.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 9 AS cod_evento, ''Estorno Arrecadação de Receita Orçamentária'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''1.1.2.2.0.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 9 AS cod_evento, ''Estorno Arrecadação de Receita Orçamentária'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''1.1.1.1.0.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 9 AS cod_evento, ''Estorno Arrecadação de Receita Orçamentária'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''4.1.1.0.0.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 9 AS cod_evento, ''Estorno Arrecadação de Receita Orçamentária'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''1.1.1.1.0.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 9 AS cod_evento, ''Estorno Arrecadação de Receita Orçamentária'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''2.1.2.0.0.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 9 AS cod_evento, ''Estorno Arrecadação de Receita Orçamentária'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''1.1.1.1.0.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 9 AS cod_evento, ''Estorno Arrecadação de Receita Orçamentária'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''1.2.3.0.0.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 9 AS cod_evento, ''Estorno Arrecadação de Receita Orçamentária'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''1.1.1.1.0.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 9 AS cod_evento, ''Estorno Arrecadação de Receita Orçamentária'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''1.1.2.3.1.01.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 9 AS cod_evento, ''Estorno Arrecadação de Receita Orçamentária'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''1.1.1.1.0.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 9 AS cod_evento, ''Estorno Arrecadação de Receita Orçamentária'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''1.1.2.4.1.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 9 AS cod_evento, ''Estorno Arrecadação de Receita Orçamentária'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''1.1.1.1.0.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 9 AS cod_evento, ''Estorno Arrecadação de Receita Orçamentária'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''8.3.2.4.0.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 9 AS cod_evento, ''Estorno Arrecadação de Receita Orçamentária'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''8.3.2.3.0.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 10 AS cod_evento, ''Reconhecimento do Crédito Tributário'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''1.1.2.2.0.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 10 AS cod_evento, ''Reconhecimento do Crédito Tributário'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''4.1.0.0.0.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 11 AS cod_evento, ''Previsão da Receita Orçamentária'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''5.2.1.1.0.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 11 AS cod_evento, ''Previsão da Receita Orçamentária'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''6.2.1.1.0.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 12 AS cod_evento, ''Fixação da Despesa Orçamentária'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''5.2.2.1.1.01.01.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 12 AS cod_evento, ''Fixação da Despesa Orçamentária'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''6.2.2.1.1.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 13 AS cod_evento, ''Crédito Suplementar - Redução'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''6.2.2.1.1.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 13 AS cod_evento, ''Crédito Suplementar - Redução'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''5.2.2.1.9.01.09.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 13 AS cod_evento, ''Crédito Suplementar - Redução'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''5.2.2.1.2.01.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 13 AS cod_evento, ''Crédito Suplementar - Redução'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''6.2.2.1.1.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 13 AS cod_evento, ''Crédito Suplementar - Redução'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''5.2.2.1.3.03.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 13 AS cod_evento, ''Crédito Suplementar - Redução'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''5.2.2.1.3.99.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 14 AS cod_evento, ''Crédito Suplementar - Operação de Crédito'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''5.2.2.1.2.01.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 14 AS cod_evento, ''Crédito Suplementar - Operação de Crédito'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''6.2.2.1.1.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 14 AS cod_evento, ''Crédito Suplementar - Operação de Crédito'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''5.2.2.1.3.04.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 14 AS cod_evento, ''Crédito Suplementar - Operação de Crédito'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''5.2.2.1.3.99.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 15 AS cod_evento, ''Crédito Suplementar - Excesso de Arrecadação'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''5.2.2.1.2.01.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 15 AS cod_evento, ''Crédito Suplementar - Excesso de Arrecadação'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''6.2.2.1.1.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 15 AS cod_evento, ''Crédito Suplementar - Excesso de Arrecadação'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''5.2.2.1.3.02.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 15 AS cod_evento, ''Crédito Suplementar - Excesso de Arrecadação'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''5.2.2.1.3.99.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 16 AS cod_evento, ''Crédito Suplementar - Superavit'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''5.2.2.1.2.01.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 16 AS cod_evento, ''Crédito Suplementar - Superavit'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''6.2.2.1.1.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 16 AS cod_evento, ''Crédito Suplementar - Superavit'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''5.2.2.1.3.01.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 16 AS cod_evento, ''Crédito Suplementar - Superavit'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''5.2.2.1.3.99.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 17 AS cod_evento, ''Crédito Especial - Redução'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''6.2.2.1.1.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 17 AS cod_evento, ''Crédito Especial - Redução'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''5.2.2.1.9.01.09.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 17 AS cod_evento, ''Crédito Especial - Redução'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''5.2.2.1.2.02.01.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 17 AS cod_evento, ''Crédito Especial - Redução'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''6.2.2.1.1.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 17 AS cod_evento, ''Crédito Especial - Redução'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''5.2.2.1.3.03.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 17 AS cod_evento, ''Crédito Especial - Redução'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''5.2.2.1.3.99.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 18 AS cod_evento, ''Crédito Especial - Operação de Crédito'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''5.2.2.1.2.02.01.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 18 AS cod_evento, ''Crédito Especial - Operação de Crédito'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''6.2.2.1.1.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 18 AS cod_evento, ''Crédito Especial - Operação de Crédito'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''5.2.2.1.3.04.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 18 AS cod_evento, ''Crédito Especial - Operação de Crédito'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''5.2.2.1.3.99.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 19 AS cod_evento, ''Crédito Especial - Auxílios e Convênios'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''5.2.2.1.2.02.01.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 19 AS cod_evento, ''Crédito Especial - Auxílios e Convênios'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''6.2.2.1.1.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 19 AS cod_evento, ''Crédito Especial - Auxílios e Convênios'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''5.2.2.1.3.99.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 20 AS cod_evento, ''Crédito Especial - Excesso de Arrecadação'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''5.2.2.1.2.02.01.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 20 AS cod_evento, ''Crédito Especial - Excesso de Arrecadação'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''6.2.2.1.1.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 20 AS cod_evento, ''Crédito Especial - Excesso de Arrecadação'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''5.2.2.1.3.02.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 20 AS cod_evento, ''Crédito Especial - Excesso de Arrecadação'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''5.2.2.1.3.99.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 21 AS cod_evento, ''Crédito Especial - Superavit'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''5.2.2.1.2.02.01.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 21 AS cod_evento, ''Crédito Especial - Superavit'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''6.2.2.1.1.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 21 AS cod_evento, ''Crédito Especial - Superavit'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''5.2.2.1.3.02.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 21 AS cod_evento, ''Crédito Especial - Superavit'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''5.2.2.1.3.99.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 22 AS cod_evento, ''Crédito Extraordinário'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''5.2.2.1.2.03.01.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 22 AS cod_evento, ''Crédito Extraordinário'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''6.2.2.1.1.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 23 AS cod_evento, ''Reabrir Crédito Especial'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''5.2.2.1.2.02.02.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 23 AS cod_evento, ''Reabrir Crédito Especial'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''6.2.2.1.1.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 24 AS cod_evento, ''Reabrir Crédito Extraordinário'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''5.2.2.1.2.03.02.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 24 AS cod_evento, ''Reabrir Crédito Extraordinário'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''6.2.2.1.1.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 25 AS cod_evento, ''Anulação Externa - Dotação Suplementada'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''5.2.2.1.3.03.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 25 AS cod_evento, ''Anulação Externa - Dotação Suplementada'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''6.2.2.1.1.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 26 AS cod_evento, ''Anulação Externa - Dotação Reduzida'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''6.2.2.1.1.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 26 AS cod_evento, ''Anulação Externa - Dotação Reduzida'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''5.2.2.1.9.01.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 27 AS cod_evento, ''Transferência Recursos - Remanejamento'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''6.2.2.1.1.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 27 AS cod_evento, ''Transferência Recursos - Remanejamento'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''5.2.2.1.9.01.09.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 27 AS cod_evento, ''Transferência Recursos - Remanejamento'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''5.2.2.1.2.01.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 27 AS cod_evento, ''Transferência Recursos - Remanejamento'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''6.2.2.1.1.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 27 AS cod_evento, ''Transferência Recursos - Remanejamento'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''5.2.2.1.3.03.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 27 AS cod_evento, ''Transferência Recursos - Remanejamento'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''5.2.2.1.3.99.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 28 AS cod_evento, ''Transferência Recursos - Transposição'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''6.2.2.1.1.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 28 AS cod_evento, ''Transferência Recursos - Transposição'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''5.2.2.1.9.01.09.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 28 AS cod_evento, ''Transferência Recursos - Transposição'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''5.2.2.1.2.01.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 28 AS cod_evento, ''Transferência Recursos - Transposição'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''6.2.2.1.1.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 28 AS cod_evento, ''Transferência Recursos - Transposição'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''5.2.2.1.3.03.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 28 AS cod_evento, ''Transferência Recursos - Transposição'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''5.2.2.1.3.99.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 29 AS cod_evento, ''Transferência Recursos - Transferência'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''6.2.2.1.1.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 29 AS cod_evento, ''Transferência Recursos - Transferência'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''5.2.2.1.9.01.09.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 29 AS cod_evento, ''Transferência Recursos - Transferência'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''5.2.2.1.2.01.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 29 AS cod_evento, ''Transferência Recursos - Transferência'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''6.2.2.1.1.00.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 29 AS cod_evento, ''Transferência Recursos - Transferência'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''5.2.2.1.3.03.00.00.00.00'')

                UNION

                SELECT cod_conta, cod_estrutural, tipo_conta, 29 AS cod_evento, ''Transferência Recursos - Transferência'' AS nom_evento FROM tmp_conta WHERE cod_estrutural ilike (''5.2.2.1.3.99.00.00.00.00'')
            ) as tabela

            ORDER BY cod_evento
    ';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN NEXT reRegistro;
    END LOOP;

DROP TABLE tmp_conta;

END;
$$ LANGUAGE 'plpgsql';

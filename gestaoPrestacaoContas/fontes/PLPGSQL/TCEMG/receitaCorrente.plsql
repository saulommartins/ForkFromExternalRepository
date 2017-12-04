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
 * Função que busca os dados de receita corrente
 * Data de Criação   : 06/03/2015
 * 
 * @author Analista: Ane Pereira
 * @author Desenvolvedor: Arthur Cruz
 *
 * $Id: receitaCorrente.plsql 62772 2015-06-17 12:46:11Z michel $
*/
CREATE OR REPLACE FUNCTION tcemg.fn_receita_corrente(VARCHAR, VARCHAR, VARCHAR, VARCHAR) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio             ALIAS FOR $1;
    stCodEntidades          ALIAS FOR $2;
    dtInicial               ALIAS FOR $3;
    dtFinal                 ALIAS FOR $4;
  
    stSql                   VARCHAR := '';
    
    reRegistro              RECORD;
BEGIN
      
    stSql :='CREATE TEMPORARY TABLE tmp_balancete_receita AS 
                (
                  SELECT cod_estrutural                                                 
                       , ABS(valor_previsto) as valor_previsto
                       , ABS(arrecadado_periodo) as arrecadado_periodo
                       , ABS(arrecadado_ano) as arrecadado_ano
                       , ABS(diferenca) as diferenca
                    FROM orcamento.fn_balancete_receita('|| quote_literal( stExercicio ) ||'
                                                       , ''''
                                                       , '|| quote_literal( dtInicial ) ||'
                                                       ,'|| quote_literal( dtFinal ) ||'
                                                       ,'|| quote_literal( stCodEntidades ) ||'
                                                       ,''''
                                                       ,''''
                                                       ,''''
                                                       ,''''
                                                       ,''''
                                                       ,''''
                                                       ,''''
                                                       ) 
                      AS retorno                       (
                                                       cod_estrutural      VARCHAR ,
                                                       receita             INTEGER ,
                                                       recurso             VARCHAR ,
                                                       descricao           VARCHAR ,
                                                       valor_previsto      NUMERIC ,
                                                       arrecadado_periodo  NUMERIC ,
                                                       arrecadado_ano      NUMERIC ,
                                                       diferenca           NUMERIC
                                                       )

                   WHERE cod_estrutural = ''1.2.0.0.00.00.00.00.00'' -- receita de contribuições
                      OR cod_estrutural = ''1.5.0.0.00.00.00.00.00'' -- receita industrial
                      OR cod_estrutural = ''1.4.0.0.00.00.00.00.00'' -- receita agropecuaria
                      OR cod_estrutural = ''1.6.0.0.00.00.00.00.00'' -- receita servicos
                      OR cod_estrutural = ''1.9.0.0.00.00.00.00.00'' -- outras receitas correntes
                      OR (cod_estrutural ilike ''9.1.1%'' and receita is null) -- deducoesExcFundeb
                      OR cod_estrutural = ''1.1.2.0.00.00.00.00.00'' -- taxas
                      OR cod_estrutural = ''1.1.3.0.00.00.00.00.00'' -- contribuições de melhoria
                      OR cod_estrutural = ''1.1.1.2.02.00.00.00.00'' -- IPTU
                      OR cod_estrutural = ''1.1.1.3.05.00.00.00.00'' -- issqn imposto sobre serviço qualqer natureza
                      OR cod_estrutural = ''1.1.1.2.08.00.00.00.00'' -- ITBI Imposto Sobre Transmissao inter-vivos De Bens Imoveis E De
                      OR cod_estrutural = ''1.1.1.2.04.00.00.00.00'' -- IRRF
                      OR cod_estrutural = ''1.3.2.5.00.00.00.00.00'' -- receita Aplicação
                      OR cod_estrutural = ''1.3.0.0.00.00.00.00.00'' -- outras receitas
                      OR cod_estrutural = ''1.7.2.1.01.02.00.00.00'' -- FPM
                      OR cod_estrutural = ''1.7.2.2.01.01.00.00.00'' -- cota ICMS
                      OR cod_estrutural = ''1.7.2.2.01.04.00.00.00'' -- cota IPI
                      OR cod_estrutural = ''1.7.2.2.01.02.00.00.00'' -- cota IPVA
                      OR cod_estrutural = ''1.7.2.1.01.02.06.00.00'' -- transferencias de rec do FUNDEB
                      OR cod_estrutural = ''1.7.6.0.00.00.00.00.00'' -- Convenios
                      OR cod_estrutural = ''1.7.0.0.00.00.00.00.00'' -- outras transferencias uniao
                  
                ORDER BY cod_estrutural
                ) ';
    EXECUTE stSql;
    
    stSql := ' SELECT cod_estrutural
                    , COALESCE( valor_previsto      , 0.00 ) AS valor_previsto 
                    , COALESCE( arrecadado_periodo  , 0.00 ) AS arrecadado_periodo
                    , COALESCE( arrecadado_ano      , 0.00 ) AS arrecadado_ano
                    , COALESCE( diferenca           , 0.00 ) AS diferenca
                 FROM tmp_balancete_receita; ';
           
    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN next reRegistro;
    END LOOP;

    DROP TABLE tmp_balancete_receita;

    RETURN;
    
END;
$$ LANGUAGE 'plpgsql';
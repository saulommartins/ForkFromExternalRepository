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
    * Arquivo de mapeamento para a função que busca os dados de licitação
    * Data de Criação   : 13/03/2015


    * @author Analista      Gelson Wolowski Gonçalves
    * @author Desenvolvedor Michel Teixeira
    
    * @package URBEM
    * @subpackage 

    $Id: exercicioNumeroLicitacao.plsql 66670 2016-11-01 11:03:33Z franver $
*/

CREATE OR REPLACE FUNCTION tcemg.fn_exercicio_numero_licitacao(VARCHAR, VARCHAR) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio         ALIAS FOR $1;
    stCodEntidade       ALIAS FOR $2;
    stSql               VARCHAR := '';
    stFiltro            VARCHAR := '';
    reRegistro          RECORD;

BEGIN
    IF stExercicio <> '' THEN
        stFiltro := ' AND licitacao.exercicio = '''|| stExercicio || '''';
    END IF;

    stSql := 'SELECT licitacao.cod_licitacao
                   , licitacao.cod_modalidade
                   , licitacao.cod_entidade
                   , licitacao.exercicio	
                   , CASE WHEN config_entidade.bom_despacho IS TRUE AND licitacao_tc.exercicio_licitacao IS NOT NULL AND config_entidade.exercicio = licitacao.exercicio THEN
                                    licitacao_tc.exercicio_licitacao::varchar
                          ELSE
                                    licitacao.exercicio::varchar
				     END AS exercicio_licitacao
                   , CASE WHEN config_entidade.bom_despacho IS TRUE AND licitacao_tc.numero_licitacao IS NOT NULL AND config_entidade.exercicio = licitacao.exercicio THEN
                                    licitacao_tc.numero_licitacao::varchar
                          ELSE ';
                        IF stExercicio >= '2016' THEN
                            stSql := stSql|| ' LPAD(''''||licitacao.cod_entidade::varchar,2, ''0'')||LPAD(''''||licitacao.cod_modalidade::varchar,2, ''0'')||LPAD(''''||licitacao.cod_licitacao::varchar,8, ''0'') ';
                        ELSE
                            stSql := stSql|| ' licitacao.exercicio::varchar||LPAD(''''||licitacao.cod_entidade::varchar,2, ''0'')||LPAD(''''||licitacao.cod_modalidade::varchar,2, ''0'')||LPAD(''''||licitacao.cod_licitacao::varchar,4, ''0'') ';
                        END IF;
	
    stSql := stSql|| '
    			     END AS num_licitacao
                   
                   FROM licitacao.licitacao
                   
                   LEFT JOIN (
							SELECT CASE WHEN (configuracao.valor IS NOT NULL OR cgm_entidade.numcgm IS NOT NULL) THEN TRUE
										ELSE FALSE
								   END AS bom_despacho
								 , entidade.cod_entidade
								 , entidade.exercicio
							  FROM orcamento.entidade
						 LEFT JOIN sw_cgm AS cgm_entidade
								ON cgm_entidade.numcgm = entidade.numcgm
							   AND cgm_entidade.nom_cgm ~* ''bom despacho''
					     LEFT JOIN administracao.configuracao
								ON configuracao.cod_modulo = 2
								--RECEBE PADRÃO 2014, TICKET #22801
							   AND configuracao.exercicio  = ''2014''
							   AND configuracao.parametro  = ''cnpj''
							   AND configuracao.valor      = ''18301002000186''
						 ) AS config_entidade
					  ON config_entidade.cod_entidade = licitacao.cod_entidade
					 --RECEBE PADRÃO 2014, TICKET #22801
					 AND config_entidade.exercicio = ''2014''

			   LEFT JOIN tcemg.licitacao_tc
					  --RECEBE PADRÃO 2014(entidade.exercicio), TICKET #22801
					  ON licitacao_tc.exercicio = config_entidade.exercicio
					 AND licitacao_tc.cod_licitacao = licitacao.cod_licitacao
					 AND licitacao_tc.cod_modalidade = licitacao.cod_modalidade
					 AND licitacao_tc.cod_entidade = licitacao.cod_entidade
                     
              WHERE licitacao.cod_entidade IN ( '|| stCodEntidade ||')
                '|| stFiltro;

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN NEXT reRegistro;
    END LOOP;

    RETURN;

END;
$$ LANGUAGE 'plpgsql';                                                                  

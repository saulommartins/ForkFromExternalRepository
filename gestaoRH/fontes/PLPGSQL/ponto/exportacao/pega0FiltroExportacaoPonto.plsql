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
/* pega0FiltroExportacaoPonto
 * 
 * Data de Criação   : 23/10/2008


 * @author Analista      Dagiane Vieira
 * @author Desenvolvedor Diego Lemos de Souza
 
 * @package URBEM
 * @subpackage 

 $Id:$
 */
CREATE OR REPLACE FUNCTION pega0FiltroExportacaoPonto(VARCHAR,VARCHAR,VARCHAR) RETURNS VARCHAR as $$
DECLARE
    stTipoFiltro        ALIAS FOR $1;
    stCodigos           ALIAS FOR $2;
    stEntidade          ALIAS FOR $2;
    stSql               VARCHAR;
    stFiltros           VARCHAR:='';
    stExercicio         VARCHAR;
    arCodigos           VARCHAR[];
    reFiltros           RECORD;
BEGIN
    IF stTipoFiltro = 'lotacao' THEN
        SELECT max(valor) as exercicio
          into stExercicio
          FROM administracao.configuracao 
         WHERE parametro = 'ano_exercicio';    

        stSql := '  SELECT vw_orgao_nivel.orgao||''-''|| recuperaDescricaoOrgao(contrato_servidor_orgao.cod_orgao,('|| quote_literal(stExercicio ||'-01-01') ||')::date) as descricao
                      FROM organograma.vw_orgao_nivel
                     WHERE vw_orgao_nivel.cod_orgao IN ('|| stCodigos ||')
                  ORDER BY vw_orgao_nivel.orgao';
        FOR reFiltros IN EXECUTE stSql LOOP
            stFiltros := stFiltros || reFiltros.descricao ||' / ';
        END LOOP;
    END IF;
    IF stTipoFiltro = 'local' THEN
        stSql := '  SELECT local.cod_local||''-''|| local.descricao as descricao
                      FROM organograma.local
                     WHERE local.cod_local IN ('|| stCodigos ||')
                  ORDER BY local.descricao';
        FOR reFiltros IN EXECUTE stSql LOOP
            stFiltros := stFiltros || reFiltros.descricao ||' / ';
        END LOOP;
    END IF;

    IF stTipoFiltro = 'reg_sub_fun_esp' THEN
        arCodigos := string_to_array(stCodigos,'-');
        stSql := 'SELECT descricao
                    FROM pessoal'|| stEntidade ||'.regime
                   WHERE cod_regime IN ('|| arCodigos[1] ||')';
        stFiltros := stFiltros || 'Regime: ';
        FOR reFiltros IN EXECUTE stSql LOOP
            stFiltros := stFiltros || reFiltros.descricao ||' / ';
        END LOOP;
        stSql := 'SELECT descricao
                    FROM pessoal'|| stEntidade ||'.sub_divisao
                   WHERE cod_sub_divisao IN ('|| arCodigos[2] ||')';
        stFiltros := stFiltros || '  Subdivisão: ';
        FOR reFiltros IN EXECUTE stSql LOOP
            stFiltros := stFiltros || reFiltros.descricao ||' / ';
        END LOOP;
        stSql := 'SELECT descricao
                    FROM pessoal'|| stEntidade ||'.cargo
                   WHERE cod_cargo IN ('|| arCodigos[3] ||')';
        stFiltros := stFiltros || '  Função: ';
        FOR reFiltros IN EXECUTE stSql LOOP
            stFiltros := stFiltros || reFiltros.descricao ||' / ';
        END LOOP;
        IF arCodigos[4] IS NOT NULL THEN
            stSql := 'SELECT descricao
                        FROM pessoal'|| stEntidade ||'.especialidade
                       WHERE cod_especialidade IN ('|| arCodigos[4] ||')';
            stFiltros := stFiltros || '  Especialidade: ';
            FOR reFiltros IN EXECUTE stSql LOOP
                stFiltros := stFiltros || reFiltros.descricao ||' / ';
            END LOOP;
        END IF;
    END IF;
    IF stTipoFiltro != 'contrato' AND stTipoFiltro != 'cgm_contrato' AND stTipoFiltro != 'geral' THEN
        stFiltros := substr(stFiltros,0,char_length(stFiltros)-1);
    END IF;
    RETURN stFiltros;
END
$$ LANGUAGE 'plpgsql';

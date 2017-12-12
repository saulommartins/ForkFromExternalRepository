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
/* recuperarDirfPrestadoresServicoValorEmpenhoExercicio
 * 
 * Data de Criação : 23/01/2009


 * @author Analista : Dagiane   
 * @author Desenvolvedor : Rafael Garbin
 
 * @package URBEM
 * @subpackage 

 $Id:$
 */

CREATE OR REPLACE FUNCTION recuperarDirfPrestadoresServicoValorEmpenhoExercicio(VARCHAR, INTEGER, INTEGER, INTEGER, VARCHAR) RETURNS SETOF colunasDirfPrestadoresServicoValorEmpenhoExercicio AS $$
DECLARE
    stEntidade     ALIAS FOR $1;
    inExercicio    ALIAS FOR $2;    
    inCodEntidade  ALIAS FOR $3;    
    inNumCgm       ALIAS FOR $4;    
    stTipo         ALIAS FOR $5;
    rwDirf         colunasDirfPrestadoresServicoValorEmpenhoExercicio%ROWTYPE;
    stSql          VARCHAR;
    reRegistro     RECORD;
BEGIN

     stSql := '     SELECT pre_empenho.cgm_beneficiario as numcgm
                         , sum(empenho.fn_consultar_valor_empenhado_pago(  configuracao_dirf_prestador.exercicio               
                                                                          ,empenho.cod_empenho             
                                                                          ,empenho.cod_entidade            
                                                                       )) AS vlr_empenhado
                      FROM ima'|| stEntidade ||'.configuracao_dirf_prestador
                INNER JOIN orcamento.conta_despesa
                        ON configuracao_dirf_prestador.exercicio = conta_despesa.exercicio
                        AND configuracao_dirf_prestador.cod_conta = conta_despesa.cod_conta                
                INNER JOIN empenho.pre_empenho_despesa
                        ON configuracao_dirf_prestador.exercicio = pre_empenho_despesa.exercicio
                        AND configuracao_dirf_prestador.cod_conta = pre_empenho_despesa.cod_conta
                INNER JOIN empenho.pre_empenho
                        ON pre_empenho_despesa.exercicio = pre_empenho.exercicio
                    AND pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                INNER JOIN empenho.empenho
                        ON pre_empenho.exercicio = empenho.exercicio
                       AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                    WHERE configuracao_dirf_prestador.exercicio = '|| quote_literal(inExercicio) ||'
                      AND pre_empenho.cgm_beneficiario = '|| inNumCgm ||'
                      AND configuracao_dirf_prestador.tipo = '|| quote_literal(stTipo) ||'
                 GROUP BY numcgm';

    FOR reRegistro IN EXECUTE stSql LOOP  
        rwDirf.numcgm      := reRegistro.numcgm;
        rwDirf.vl_empenho  := COALESCE(reRegistro.vlr_empenhado,0.00);

        RETURN NEXT rwDirf;               
    END LOOP;

END;
$$ LANGUAGE 'plpgsql';   

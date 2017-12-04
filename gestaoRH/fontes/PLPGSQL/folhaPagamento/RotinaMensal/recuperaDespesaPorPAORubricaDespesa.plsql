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
 * Função PLPGSQL
 * Data de Criação   : 09/06/2009


 * @author Analista      -------
 * @author Desenvolvedor Alex
 
 * @package URBEM
 * @subpackage 

 $Id: $
 */
 
CREATE OR REPLACE FUNCTION recuperaDespesaPorPAORubricaDespesa(INTEGER, INTEGER, INTEGER, VARCHAR) RETURNS SETOF colunasRecuperaDespesaPorPAORubricaDespesa AS $$
DECLARE
    inCodEntidade                   ALIAS FOR $1;
    inExercicio                     ALIAS FOR $2;
    inNumPAO                        ALIAS FOR $3;
    stCodEstrutural                 ALIAS FOR $4;
    stSql                           VARCHAR;
    stCodEstruturalTmp              VARCHAR;
    arEstrutural                    VARCHAR[];
    reRegistro                      RECORD;
    inCount                         INTEGER := 0;
    boRetornar                      BOOLEAN := FALSE;
    rwDespesaPorPAORubricaDespesa   colunasRecuperaDespesaPorPAORubricaDespesa%ROWTYPE;
BEGIN
    stCodEstruturalTmp := stCodEstrutural;
    arEstrutural       := string_to_array(stCodEstruturalTmp,'.');
    inCount            := array_upper(arEstrutural, 1);
    
    WHILE LENGTH(stCodEstruturalTmp) > 1 LOOP
    
        stSql := '      SELECT despesa.cod_despesa
                             , despesa.num_pao
                             , conta_despesa.cod_estrutural as cod_estrutural
                             , conta_despesa.cod_conta
                             , conta_despesa.descricao as descricao_conta
                             , recurso.cod_recurso
                             , recurso.cod_fonte
                             , recurso.nom_recurso as descricao_recurso                             
                          FROM orcamento.despesa
                    INNER JOIN orcamento.conta_despesa
                            ON conta_despesa.cod_conta = despesa.cod_conta
                           AND conta_despesa.exercicio = despesa.exercicio
                    INNER JOIN orcamento.recurso 
                            ON recurso.cod_recurso = despesa.cod_recurso
                           AND recurso.exercicio   = despesa.exercicio
                    INNER JOIN orcamento.pao
                            ON pao.exercicio = despesa.exercicio
                           AND pao.num_pao = despesa.num_pao
                    INNER JOIN orcamento.pao_ppa_acao
                            ON pao_ppa_acao.exercicio = pao.exercicio
                           AND pao_ppa_acao.num_pao = pao.num_pao
                    INNER JOIN ppa.acao
                            ON acao.cod_acao = pao_ppa_acao.cod_acao
                         WHERE acao.num_acao = '|| inNumPAO ||'
                           AND despesa.exercicio = '|| quote_literal(inExercicio) ||'
                           AND despesa.cod_entidade = '|| inCodEntidade ||'
                           AND conta_despesa.cod_estrutural ILIKE '|| quote_literal(stCodEstruturalTmp||'%')||'';
                           
                           
        FOR reRegistro IN EXECUTE stSql LOOP
            boRetornar := TRUE;
            
            rwDespesaPorPAORubricaDespesa.cod_despesa         := reRegistro.cod_despesa;
            rwDespesaPorPAORubricaDespesa.cod_estrutural      := reRegistro.cod_estrutural;
            rwDespesaPorPAORubricaDespesa.cod_conta           := reRegistro.cod_conta; 
            rwDespesaPorPAORubricaDespesa.descricao_conta     := reRegistro.descricao_conta;
            rwDespesaPorPAORubricaDespesa.cod_recurso         := reRegistro.cod_recurso;
            rwDespesaPorPAORubricaDespesa.cod_fonte           := reRegistro.cod_fonte;
            rwDespesaPorPAORubricaDespesa.descricao_recurso   := reRegistro.descricao_recurso;
            
            RETURN NEXT rwDespesaPorPAORubricaDespesa;
        END LOOP;
        
        IF boRetornar IS TRUE THEN
            EXIT;
        END IF;
        
        arEstrutural[inCount] := '';
        stCodEstruturalTmp    := rtrim(array_to_string(arEstrutural,'.'),'.') ||'.';
        inCount               := inCount - 1;
        
    END LOOP;
    
END;
$$ LANGUAGE 'plpgsql';



--
--select * from recuperaDespesaPorPAORubricaDespesa(1, 2009,2010,'3.3.9.0.30.15.00.00.00');
--select * from recuperaDespesaPorPAORubricaDespesa(1, 2009,2010,'0.0.0.0.00.00.00.00.00');
--select * from recuperaDespesaPorPAORubricaDespesa(1, 2009,2010,'3.1.9.0.00.00.00.00.00');
--

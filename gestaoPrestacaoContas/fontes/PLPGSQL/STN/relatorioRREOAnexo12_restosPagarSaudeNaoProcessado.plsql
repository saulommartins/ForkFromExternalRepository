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
* Script de função PLPGSQL - Relatório STN - RREO - Anexo 16
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Revision: 29316 $
* $Name$
* $Author: $
* $Date: $
*
* Casos de uso: uc-04.05.28
*/

CREATE OR REPLACE FUNCTION stn.fn_rreo_anexo12_restos_pagar_saude_nao_processado( varchar, varchar, varchar ) RETURNS SETOF RECORD AS $$
DECLARE
    stExercicio      ALIAS FOR $1;
    stDtFinal        ALIAS FOR $2;
    stCodEntidades   ALIAS FOR $3;
    stDtInicial      VARCHAR :='';
    stExercicioAtual VARCHAR :='';
   
    stMascara                  VARCHAR := '99.99.99.999.9999.9999.99999999999999';
    stExercicioAnterior        VARCHAR := '';
    stDtFinalExercicioAnterior VARCHAR := '';
    stSql                      VARCHAR := '';
    ponto                      VARCHAR := '.';
    stDtInicialEmissao         VARCHAR := '';
    stDtFinalEmissao           VARCHAR := '';
    stDtInicialAux             VARCHAR := '';
    reRegistro                 RECORD;
    reReg                      RECORD;
    inMes                      INTEGER := 0;

BEGIN

    stDtInicial := '01/01/'||stExercicio;
    stExercicioAnterior := trim(to_char((to_number(stExercicio,'9999')-1),'9999'));
    
    stDtFinalExercicioAnterior := '31/12/'||stExercicioAnterior ;
    
    -- Pega o mês da data Final para validar se é o mês de dezembro no IF logo abaixo. Para fazer a cobnsulta dos restos a pagar.
    inMes := SUBSTR(stDtFinal, 4, 2)::INTEGER;
    
    IF (inMes <> 12) THEN
       stSql := 'SELECT DISTINCT exercicio FROM empenho.nota_liquidacao WHERE nota_liquidacao.exercicio <  cast('''||stExercicio||''' as varchar)  ';
    ELSE
       stSql := 'SELECT DISTINCT exercicio FROM empenho.nota_liquidacao WHERE nota_liquidacao.exercicio <=  cast('''||stExercicio||''' as varchar)  ';
    END IF;

    CREATE TEMPORARY TABLE tmp_empenhos (
        exercicio     CHAR(4),
        inscritos     NUMERIC(14,2),
        valor_pago    NUMERIC(14,2),
        valor_anulado NUMERIC(14,2),
        valor_apagar  NUMERIC(14,2)
    );

    FOR reReg IN EXECUTE stSql
    LOOP
        stExercicioAtual   := reReg.exercicio;
        stDtInicialEmissao := '01/01/'||reReg.exercicio;
        stDtFinalEmissao   := '31/12/'||reReg.exercicio;
        IF (LENGTH(stDtInicial) <> 10) THEN
            stDtInicialAux := stDtInicial || reReg.exercicio;
        ELSE
            stDtInicialAux := stDtInicial;
        END IF;

        stSql := '
        INSERT INTO tmp_empenhos
             SELECT exercicio
                  , SUM(inscritos)
                  , SUM(valor_pago)
                  , SUM(valor_anulado)
                  , SUM(inscritos - valor_anulado - valor_pago) as valor_apagar
               FROM (SELECT publico.fn_mascara_dinamica('''||stMascara||''',(ped_d_cd.num_orgao||'''||ponto||'''||ped_d_cd.num_unidade||'''||ponto||'''||ped_d_cd.cod_funcao||'''||ponto||'''||ped_d_cd.cod_subfuncao||'''||ponto||'''||ped_d_cd.cod_programa||'''||ponto||'''||ped_d_cd.num_pao||'''||ponto||'''||replace(ped_d_cd.cod_estrutural,''.'',''''))) as cod_estrutural
                  , e.cod_entidade as entidade
                  , (e.cod_entidade ||'''||'-'||'''||e.cod_empenho||'''||'/'||'''|| e.exercicio) as empenho
                  , e.exercicio         as exercicio
                  , pe.cgm_beneficiario as cgm
                  , cgm.nom_cgm         as razao_social
                  -- Valor Inscritos
                  , empenho.fn_empenho_empenhado( e.exercicio, e.cod_empenho, e.cod_entidade, '''||stDtInicialEmissao||''', '''||stDtFinalEmissao||''') 
                    - empenho.fn_empenho_anulado( e.exercicio, e.cod_empenho, e.cod_entidade, '''||stDtInicialEmissao||''', '''||stDtFinalExercicioAnterior||''')
                    - (empenho.fn_empenho_liquidado( e.exercicio, e.cod_empenho, e.cod_entidade, '''||stDtInicialEmissao||''', '''||stDtFinalExercicioAnterior||''') - empenho.fn_empenho_estorno_liquidacao( e.exercicio, e.cod_empenho, e.cod_entidade, '''||stDtInicialEmissao||''', '''||stDtFinalExercicioAnterior||''')) as inscritos
                  -- Valor pago
                  , CASE WHEN ('''||inMes||''' = 12 AND '''||stExercicio||''' = '''||stExercicioAtual||''')
                         THEN 0.00 
                         ELSE (empenho.fn_empenho_pago( e.exercicio, e.cod_empenho, e.cod_entidade,'''||stDtInicialAux||''','''||stDtFinal||''') - empenho.fn_empenho_estornado( e.exercicio, e.cod_empenho, e.cod_entidade, '''||stDtInicialAux||''' ,'''||stDtFinal||''')) 
                     END as valor_pago
                  -- Valor Anulado
                  , CASE WHEN ('''||inMes||''' = 12 AND '''||stExercicio||''' = '''||stExercicioAtual||''')
                         THEN 0.00 
                         ELSE empenho.fn_empenho_anulado( e.exercicio, e.cod_empenho, e.cod_entidade, '''||stDtInicialAux||''', '''||stDtFinal||''') 
                    END as valor_anulado
                  , to_char(e.dt_empenho   ,''dd/mm/yyyy'') as data
                  , to_char(e.dt_vencimento,''dd/mm/yyyy'') as data_vencimento
               FROM empenho.empenho     as e
                  , sw_cgm              as cgm
                  , empenho.pre_empenho as pe
    LEFT OUTER JOIN empenho.restos_pre_empenho as rpe
                 ON pe.exercicio        = rpe.exercicio
                AND pe.cod_pre_empenho  = rpe.cod_pre_empenho
    LEFT OUTER JOIN (SELECT ped.exercicio
                          , ped.cod_pre_empenho
                          , d.num_orgao
                          , d.num_unidade
                          , d.cod_recurso
                          , d.cod_programa
                          , d.num_pao
                          , cd.cod_estrutural
                          , d.cod_funcao
                          , d.cod_subfuncao
                          , rec.masc_recurso_red
                          , rec.cod_detalhamento
                       FROM empenho.pre_empenho_despesa as ped
                          , orcamento.despesa           as d
                       JOIN orcamento.recurso(''' || reReg.exercicio ||''') as rec
                         ON rec.exercicio = d.exercicio
                        AND rec.cod_recurso = d.cod_recurso
                          , orcamento.conta_despesa     as cd
                      WHERE ped.cod_despesa = d.cod_despesa
                        AND ped.exercicio   = d.exercicio
                        AND ped.cod_conta     = cd.cod_conta
                        AND ped.exercicio     = cd.exercicio
                    ) as ped_d_cd
                 ON pe.exercicio       = ped_d_cd.exercicio
                AND pe.cod_pre_empenho = ped_d_cd.cod_pre_empenho
              WHERE e.exercicio         =cast('''||reReg.exercicio||''' as varchar)
                AND e.exercicio         = pe.exercicio
                AND e.exercicio         = pe.exercicio
                AND e.cod_pre_empenho   = pe.cod_pre_empenho
                AND e.cod_entidade      IN ('||stCodEntidades||')
                AND pe.cgm_beneficiario = cgm.numcgm
            ';


     
            stSql := stSql || '
                AND CASE WHEN pe.implantado = true
                         THEN rpe.cod_funcao      ='||10||'
                         ELSE ped_d_cd.cod_funcao ='||10||'
                     END
            ';
        
        stSql := stSql || ' ) as tbl  GROUP BY exercicio  ';
      
        EXECUTE stSql;
    
    END LOOP;



    stSql := '
            SELECT exercicio
                 , inscritos
                 , valor_pago
                 , valor_anulado
                 , valor_apagar
              FROM tmp_empenhos  
          ORDER BY exercicio desc; ';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN next reRegistro;
    END LOOP;

    DROP TABLE tmp_empenhos;

    RETURN;
END;
$$ language 'plpgsql';

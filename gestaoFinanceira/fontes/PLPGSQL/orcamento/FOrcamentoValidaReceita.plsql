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
* Script de função PLPGSQL
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: FOrcamentoValidaReceita.plsql 64294 2016-01-11 12:55:40Z lisiane $
*
* Casos de uso: uc-02.01.22
*/
CREATE OR REPLACE FUNCTION orcamento.valida_receita (VARCHAR, VARCHAR) RETURNS SETOF RECORD AS $$
DECLARE

    stExercicio            ALIAS FOR $1;
    stClassificacaoReceita ALIAS FOR $2;
    
    stSql      VARCHAR := '';
    stSqlAux   VARCHAR := '';
    
    stClassificacaoNiveis VARCHAR[];
    stAuxiliar            VARCHAR := '';
    stTipoConta           VARCHAR := '';
    
    stMAscReduzidaNova      VARCHAR := '';
    stMAscReduzidaExistente VARCHAR := '';
    
    inCount                       INTEGER := 1;
    inCount2                      INTEGER := 1;
    inNivelClassificacaoNova      INTEGER := 0;
    inNivelClassificacaoExistente INTEGER := 0;

    boPosterior BOOLEAN := false;
    boAnterior  BOOLEAN := false;
    boNivel     BOOLEAN := false;
    
    reRecordSet              RECORD;
    reRecordSetClassificacao RECORD;
BEGIN
    SELECT publico.fn_nivel(stClassificacaoReceita)
    INTO inNivelClassificacaoNova;
    

    stClassificacaoNiveis := string_to_array(stClassificacaoReceita, '.');

    FOR inCount IN REVERSE inNivelClassificacaoNova..inCount LOOP
        
        FOR inCount2 IN inCount2..inCount LOOP
            stAuxiliar := stAuxiliar||stClassificacaoNiveis[inCount2]||'.';
        END LOOP;
        
        stAuxiliar := SUBSTR(stAuxiliar,1,(length(stAuxiliar)-1) );
        
        stSqlAux := '
              SELECT conta_receita.cod_estrutural
                   , publico.fn_nivel(conta_receita.cod_estrutural) AS nivel
                   , ''''::VARCHAR AS descricao
                   , ''false''::VARCHAR AS classificacao_receita_valida
                FROM orcamento.conta_receita
          INNER JOIN orcamento.receita
                  ON conta_receita.cod_conta = receita.cod_conta
                 AND conta_receita.exercicio = receita.exercicio
               WHERE conta_receita.exercicio = '''||stExercicio||'''
                 AND conta_receita.cod_estrutural ILIKE '''||stAuxiliar||'%''
            ORDER BY cod_estrutural
        ';

        FOR reRecordSetClassificacao IN EXECUTE stSqlAux
        LOOP
            IF reRecordSetClassificacao.cod_estrutural IS NOT NULL THEN
                -- Retornará false, se o nivel que será criado a receita for maior que o nivel já adicionado ao banco.
                inNivelClassificacaoExistente := publico.fn_nivel(reRecordSetClassificacao.cod_estrutural);
                
                IF inNivelClassificacaoExistente < inNivelClassificacaoNova THEN
                    reRecordSetClassificacao.classificacao_receita_valida := 'false';
                    reRecordSetClassificacao.descricao := 'anterior';
                    RETURN NEXT reRecordSetClassificacao;
                END IF;
                
                
                IF inNivelClassificacaoExistente = inNivelClassificacaoNova THEN
                    
                    PERFORM 1 FROM orcamento.conta_receita
                        INNER JOIN orcamento.receita                                                                                         
                                ON conta_receita.cod_conta = receita.cod_conta                                                               
                               AND conta_receita.exercicio = receita.exercicio                                                               
                             WHERE conta_receita.cod_estrutural ILIKE stAuxiliar||'%'
                               AND conta_receita.exercicio = stExercicio
                               AND conta_receita.cod_estrutural ILIKE stClassificacaoReceita;
                    
                    IF NOT FOUND THEN
                        reRecordSetClassificacao.classificacao_receita_valida := 'true';
                        RETURN NEXT reRecordSetClassificacao;
                    ELSE
                        reRecordSetClassificacao.classificacao_receita_valida := 'false';
                        reRecordSetClassificacao.descricao := 'igual';
                        RETURN NEXT reRecordSetClassificacao;
                    END IF;
                END IF;
                
                IF inNivelClassificacaoExistente > inNivelClassificacaoNova THEN
                    stMAscReduzidaNova := publico.fn_mascarareduzida(stClassificacaoReceita);
                    
                    PERFORM 1 FROM orcamento.conta_receita
                        INNER JOIN orcamento.receita                                                                                         
                                ON conta_receita.cod_conta = receita.cod_conta                                                               
                               AND conta_receita.exercicio = receita.exercicio                                                               
                             WHERE conta_receita.cod_estrutural ILIKE stAuxiliar||'%'
                               AND conta_receita.exercicio = stExercicio
                               AND conta_receita.cod_estrutural ILIKE stMAscReduzidaNova||'%';
                    IF NOT FOUND THEN
                        reRecordSetClassificacao.classificacao_receita_valida := 'true';
                        RETURN NEXT reRecordSetClassificacao;
                    ELSE
                        reRecordSetClassificacao.classificacao_receita_valida := 'false';
                        reRecordSetClassificacao.descricao := 'posterior';
                        RETURN NEXT reRecordSetClassificacao;
                    END IF;
                    stMAscReduzidaNova := '';
                END IF;
                RETURN;
            END IF;
        END LOOP;
        
         SELECT tipo_nivel_conta FROM orcamento.fn_tipo_conta_receita(stExercicio, stAuxiliar) AS tipo_nivel_conta   
         INTO stTipoConta;
         --Quando o estrutural auxiliar for sintetico, sai do loop
         IF stTipoConta = 'S' THEN
             reRecordSetClassificacao.classificacao_receita_valida := 'true';
             RETURN ;
         END IF;
         
        stAuxiliar := '';
        stTipoConta:='';
    END LOOP;
END;
$$ LANGUAGE 'plpgsql';

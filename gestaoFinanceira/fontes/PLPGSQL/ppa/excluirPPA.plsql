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
    * Verifica se o PPA está homologado
    * Data de Criação: 20/07/2016


    * @author Analista: Ane Caroline Fiegenbaum Pereira
    * @author Desenvolvedor: Evandro Melos

    * @package      URBEM
    * @subpackage   PPA

    * $Id: $
*/

CREATE OR REPLACE FUNCTION ppa.excluirPPA(INTEGER) RETURNS BOOLEAN AS $$
DECLARE
    inCodPPA                    ALIAS FOR $1;
    reRegistro                  RECORD;
    reRegistroAux               RECORD;
    boRetorno                   BOOLEAN = false;
    stSql                       VARCHAR := '';
    stSqlAux                    VARCHAR := '';
    
BEGIN
    
    --LOOP para a acao
    stSql := '
    SELECT DISTINCT 
            ppa.cod_ppa
           ,macro_objetivo.cod_macro
           ,programa_setorial.cod_setorial
           ,programa.cod_programa
           ,acao.cod_acao       
           ,pao_ppa_acao.exercicio
           ,acao_dados.timestamp_acao_dados
           ,acao_recurso.cod_recurso
           ,ppa.ano_inicio
           ,ppa.ano_final
    FROM ppa.ppa                                                  
    INNER JOIN ppa.macro_objetivo
        ON macro_objetivo.cod_ppa = ppa.cod_ppa                     
    INNER JOIN ppa.programa_setorial                                    
        ON programa_setorial.cod_macro = macro_objetivo.cod_macro   
    INNER JOIN ppa.programa                                             
        ON programa.cod_setorial = programa_setorial.cod_setorial    
    INNER JOIN ppa.acao                                                 
        ON acao.cod_programa = programa.cod_programa
    INNER JOIN ppa.acao_dados
        ON acao_dados.cod_acao = acao.cod_acao
    INNER JOIN orcamento.pao_ppa_acao
        ON pao_ppa_acao.cod_acao = acao.cod_acao
    LEFT JOIN ppa.acao_recurso
        ON acao_recurso.cod_acao                = acao_dados.cod_acao
        AND acao_recurso.timestamp_acao_dados   = acao_dados.timestamp_acao_dados
    
    WHERE ppa.cod_ppa = '||inCodPPA||'

    ORDER BY pao_ppa_acao.exercicio
            , cod_setorial
            , cod_programa
            , cod_acao
    ';

    FOR reRegistro IN EXECUTE stSql LOOP

        DELETE FROM orcamento.pao_ppa_acao             
            WHERE exercicio BETWEEN reRegistro.ano_inicio AND reRegistro.ano_final;

        stSqlAux := '   SELECT * 
                        FROM ppa.acao_recurso                         
                        WHERE exercicio_recurso BETWEEN '''||reRegistro.ano_inicio||''' AND '''||reRegistro.ano_final||'''                                                
                        ';
        FOR reRegistroAux IN EXECUTE stSqlAux
        LOOP            
            
            DELETE FROM ppa.acao_quantidade
                    WHERE cod_acao = reRegistroAux.cod_acao                      
                      AND exercicio_recurso = reRegistroAux.exercicio_recurso
                      AND ano = reRegistroAux.ano ;
            
            DELETE FROM ppa.acao_meta_fisica_realizada
                    WHERE cod_acao = reRegistroAux.cod_acao                      
                      AND exercicio_recurso = reRegistroAux.exercicio_recurso
                      AND ano = reRegistroAux.ano ;            
            
            DELETE FROM ppa.acao_recurso 
                    WHERE cod_acao = reRegistroAux.cod_acao
                      AND exercicio_recurso = reRegistroAux.exercicio_recurso;
        END LOOP;

        DELETE FROM ppa.acao_unidade_executora 
                WHERE cod_acao = reRegistro.cod_acao;

        DELETE FROM ppa.acao_norma
                WHERE cod_acao = reRegistro.cod_acao;
        
        DELETE FROM ppa.acao_dados 
                WHERE cod_acao = reRegistro.cod_acao;

        --Delete acao pelo cod_acao
        DELETE FROM ppa.acao 
                WHERE cod_acao = reRegistro.cod_acao;
    END LOOP;


    --LOOP PARA O PROGRAMA
    stSql:='SELECT  
                 ppa.cod_ppa
                ,macro_objetivo.cod_macro
                ,programa_setorial.cod_setorial
                ,programa.cod_programa                
                ,ppa.ano_inicio
                ,ppa.ano_final
            FROM ppa.ppa                                                  
            INNER JOIN ppa.macro_objetivo
                ON macro_objetivo.cod_ppa = ppa.cod_ppa                     
            INNER JOIN ppa.programa_setorial                                    
                ON programa_setorial.cod_macro = macro_objetivo.cod_macro   
            LEFT JOIN ppa.programa                                             
                ON programa.cod_setorial = programa_setorial.cod_setorial    
            WHERE ppa.cod_ppa = '||inCodPPA||'
    ';

    FOR reRegistro IN EXECUTE stSql LOOP

        DELETE FROM orcamento.programa_ppa_programa 
                WHERE exercicio BETWEEN reRegistro.ano_inicio AND reRegistro.ano_final;
        
        DELETE FROM ppa.programa_temporario_vigencia
            WHERE cod_programa = reRegistro.cod_programa;

        DELETE FROM ppa.programa_indicadores
            WHERE cod_programa = reRegistro.cod_programa;

        DELETE FROM ppa.programa_dados
            WHERE cod_programa = reRegistro.cod_programa;
        
        DELETE FROM ppa.programa 
                WHERE cod_programa = reRegistro.cod_programa;
    END LOOP;
    
    --LOOP para retirar codigo setorial e tabelas restantes usando a consulta anterior
    FOR reRegistro IN EXECUTE stSql LOOP
    
        DELETE FROM ppa.programa 
                WHERE cod_setorial = reRegistro.cod_setorial;

        DELETE FROM ppa.programa_setorial 
                WHERE cod_macro = reRegistro.cod_macro;
        
        DELETE FROM ppa.macro_objetivo 
                WHERE cod_ppa = reRegistro.cod_ppa
                  AND cod_macro = reRegistro.cod_macro;

        boRetorno:= true;
    END LOOP;
    
    RETURN boRetorno;
END;

$$ LANGUAGE 'plpgsql';

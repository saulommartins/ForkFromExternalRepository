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
 * recuperaRotuloValoresAcumuladosCalculo
 * Data de Criação   : 01/01/2008


 * @author Analista      Dagiane Vieira
 * @author Desenvolvedor Diego Lemos de Souza
 
 * @package URBEM
 * @subpackage 

 * @ignore # só use se FOR paginas que o cliente visualiza, se FOR mapeamento ou classe de negocio não se usa

 $Id:$
 */
-- Ticket #13872
CREATE OR REPLACE FUNCTION recuperaListaEventosAcumulados(integer,varchar) returns varchar as $$
DECLARE
    inCodContrato               ALIAS FOR $1;
    stEntidade                  ALIAS FOR $2;
    stSql                       VARCHAR;
    stCodEventos                VARCHAR:='';
    reRegistros                 RECORD;
    inCodPrevidencia            INTEGER;
BEGIN    
    --###################################
    --Busca de valores acumulados de IRRF    
    --###################################
    stSql := 'SELECT tabela_irrf_evento.*
                FROM folhapagamento'|| stEntidade ||'.tabela_irrf_evento
                   , (SELECT cod_tabela                                                 
                           , max(timestamp) as timestamp                                
                        FROM folhapagamento'|| stEntidade ||'.tabela_irrf_evento                          
                      GROUP BY cod_tabela) as max_tabela_irrf_evento                    
               WHERE tabela_irrf_evento.cod_tabela = max_tabela_irrf_evento.cod_tabela  
                 AND tabela_irrf_evento.timestamp = max_tabela_irrf_evento.timestamp';                                  
    FOR reRegistros IN EXECUTE stSql LOOP
        stCodEventos := stCodEventos || reRegistros.cod_evento  ||',';
    END LOOP;
    
    --##########################################
    --Busca de valores acumulados de Previdencia    
    --##########################################    
    stSql := 'SELECT contrato_servidor_previdencia.cod_previdencia
                FROM pessoal'|| stEntidade ||'.contrato_servidor_previdencia
                   , (SELECT cod_contrato
                           , cod_previdencia
                           , max(timestamp) as timestamp
                        FROM pessoal'|| stEntidade ||'.contrato_servidor_previdencia
                      GROUP BY cod_contrato
                             , cod_previdencia) as max_contrato_servidor_previdencia
                   , folhapagamento'|| stEntidade ||'.previdencia_previdencia
                   , (SELECT cod_previdencia
                           , max(timestamp) as timestamp
                        FROM folhapagamento'|| stEntidade ||'.previdencia_previdencia
                      GROUP BY cod_previdencia) as max_previdencia_previdencia     
               WHERE contrato_servidor_previdencia.bo_excluido is false
                 AND contrato_servidor_previdencia.cod_contrato = max_contrato_servidor_previdencia.cod_contrato
                 AND contrato_servidor_previdencia.cod_previdencia = max_contrato_servidor_previdencia.cod_previdencia
                 AND contrato_servidor_previdencia.timestamp = max_contrato_servidor_previdencia.timestamp
                 AND contrato_servidor_previdencia.cod_previdencia = previdencia_previdencia.cod_previdencia
                 AND previdencia_previdencia.cod_previdencia = max_previdencia_previdencia.cod_previdencia
                 AND previdencia_previdencia.timestamp = max_previdencia_previdencia.timestamp
                 AND previdencia_previdencia.tipo_previdencia = ''o''
                 AND contrato_servidor_previdencia.cod_contrato = '|| inCodContrato;
    inCodPrevidencia :=  selectIntoInteger(stSql);
    IF inCodPrevidencia is null THEN
        --Pensionista
        stSql := '
        SELECT contrato_pensionista_previdencia.cod_previdencia
          FROM pessoal'|| stEntidade ||'.contrato_pensionista_previdencia
             , (SELECT cod_contrato
                     , cod_previdencia
                     , max(timestamp) as timestamp
                  FROM pessoal'|| stEntidade ||'.contrato_pensionista_previdencia
                GROUP BY cod_contrato
                       , cod_previdencia) as max_contrato_pensionista_previdencia
             , folhapagamento'|| stEntidade ||'.previdencia_previdencia
             , (SELECT cod_previdencia
                     , max(timestamp) as timestamp
                  FROM folhapagamento'|| stEntidade ||'.previdencia_previdencia
                GROUP BY cod_previdencia) as max_previdencia_previdencia     
         WHERE contrato_pensionista_previdencia.cod_contrato = max_contrato_pensionista_previdencia.cod_contrato
           AND contrato_pensionista_previdencia.cod_previdencia = max_contrato_pensionista_previdencia.cod_previdencia
           AND contrato_pensionista_previdencia.timestamp = max_contrato_pensionista_previdencia.timestamp
           AND contrato_pensionista_previdencia.cod_previdencia = previdencia_previdencia.cod_previdencia
           AND previdencia_previdencia.cod_previdencia = max_previdencia_previdencia.cod_previdencia
           AND previdencia_previdencia.timestamp = max_previdencia_previdencia.timestamp
           AND previdencia_previdencia.tipo_previdencia = ''o''
           AND contrato_pensionista_previdencia.cod_contrato = '|| inCodContrato;
    END IF;
        
    IF inCodPrevidencia is not null THEN    
        stSql := 'SELECT previdencia_evento.*                                                
                    FROM folhapagamento'|| stEntidade ||'.previdencia_evento
                        , (SELECT cod_previdencia                           
                                , max(timestamp) as timestamp               
                             FROM folhapagamento'|| stEntidade ||'.previdencia_evento         
                           GROUP BY cod_previdencia) as max_previdencia_evento 
                    WHERE previdencia_evento.cod_previdencia = max_previdencia_evento.cod_previdencia 
                      AND previdencia_evento.timestamp       = max_previdencia_evento.timestamp    
                      AND previdencia_evento.cod_previdencia = '|| inCodPrevidencia;    
        FOR reRegistros IN EXECUTE stSql LOOP
            stCodEventos := stCodEventos || reRegistros.cod_evento  ||',';
        END LOOP;
    END IF;
        
    stCodEventos := substr(stCodEventos,1,char_length(stCodEventos)-1);
    return stCodEventos;
end 
$$ language 'plpgsql';

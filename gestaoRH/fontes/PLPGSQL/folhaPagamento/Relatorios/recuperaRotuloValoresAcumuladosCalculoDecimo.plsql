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
CREATE OR REPLACE FUNCTION recuperaRotuloValoresAcumuladosCalculoDecimo(integer,integer,integer,varchar,varchar) returns varchar as $$
DECLARE
    inCodContrato                       ALIAS FOR $1;
    inCodPeriodoMovimentacao            ALIAS FOR $2;
    inNumCGM                            ALIAS FOR $3;
    stNatureza                          ALIAS FOR $4;  
    stEntidade                          ALIAS FOR $5;
    stSql                       VARCHAR;
    stCodEventos                VARCHAR:='';
    stRetorno                   VARCHAR:='';
    reRegistros                 RECORD;
BEGIN    
    stCodEventos := recuperaListaEventosAcumulados(inCodContrato,stEntidade);   
    
    --Décimo
    stSql := 'SELECT contrato.*
                FROM pessoal'|| stEntidade ||'.contrato
                   , pessoal'|| stEntidade ||'.servidor_contrato_servidor
                   , pessoal'|| stEntidade ||'.servidor
               WHERE contrato.cod_contrato = servidor_contrato_servidor.cod_contrato
                 AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
                 AND contrato.cod_contrato IN (SELECT registro_evento_decimo.cod_contrato
                                                 FROM folhapagamento'|| stEntidade ||'.registro_evento_decimo
                                                    , folhapagamento'|| stEntidade ||'.evento_decimo_calculado
                                                    , folhapagamento'|| stEntidade ||'.evento
                                                WHERE registro_evento_decimo.cod_registro = evento_decimo_calculado.cod_registro
                                                  AND registro_evento_decimo.cod_evento = evento_decimo_calculado.cod_evento
                                                  AND registro_evento_decimo.desdobramento = evento_decimo_calculado.desdobramento
                                                  AND registro_evento_decimo.timestamp = evento_decimo_calculado.timestamp_registro
                                                  AND evento_decimo_calculado.cod_evento = evento.cod_evento
                                                  AND evento.natureza = '|| quote_literal(stNatureza) ||'
                                                  AND registro_evento_decimo.cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||'
                                                  AND evento_decimo_calculado.cod_evento IN ('|| stCodEventos ||')
                                             GROUP BY registro_evento_decimo.cod_contrato)
                 AND servidor.numcgm = '|| inNumCGM;    
    IF stNatureza = 'D'THEN
        stSql := stSql || ' AND contrato.cod_contrato != '|| inCodContrato;          
    END IF;                             
    FOR reRegistros IN EXECUTE stSql LOOP
        stRetorno := stRetorno || reRegistros.registro ||'(D)/';
    END LOOP;            
        
    IF char_length(stRetorno) > 1 THEN
        stRetorno := substr(stRetorno,1,char_length(stRetorno)-1);
    END IF;
    return stRetorno;    
end
$$ language 'plpgsql';


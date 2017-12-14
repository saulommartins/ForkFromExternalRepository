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
* script de funcao PLSQL
* 
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br

* $Revision: 27310 $
* $Name$
* $Autor: Marcia $
* Date: 2006/04/18 10:50:00 $
*
* Caso de uso: uc-04.05.14
*
* Objetivo: Por buffer pega o contrato e a data final da competencia 
* retornando o codigo da  previdencia oficial
*
* Utilizar para a criacao do buffer inCodPrevidenciaOficial
*/



CREATE OR REPLACE FUNCTION pega1PrevidenciaOficialDoContrato() RETURNS integer as $$

DECLARE
    inCodContrato             INTEGER;
    dtVigencia                VARCHAR;
    crCursor                  REFCURSOR;
    inCodPrevidencia          INTEGER := 0;
    inCodVinculo              INTEGER;
    stTimestamp               VARCHAR;
    stSql                     VARCHAR;
    stTable                   VARCHAR:='';
    stEntidade             VARCHAR;
 BEGIN
    stEntidade := recuperarBufferTexto('stEntidade');
    inCodContrato := recuperarBufferInteiro('inCodContrato');
    --inCodContrato := recuperaContratoServidorPensionista(inCodContrato);
    dtVigencia    := substr(recuperarBufferTexto('stDataFinalCompetencia'),1,10);

    --Inativo
    stSql := 'SELECT 1 as contador
                FROM pessoal'||stEntidade||'.aposentadoria  
                WHERE aposentadoria.cod_contrato = '||inCodContrato||'
                 AND NOT EXISTS (SELECT 1
                                   FROM pessoal'||stEntidade||'.aposentadoria_excluida
                                  WHERE aposentadoria_excluida.cod_contrato = aposentadoria.cod_contrato
                                    AND aposentadoria_excluida.timestamp_aposentadoria = aposentadoria.timestamp)';
    IF selectIntoInteger(stSql) THEN
        inCodVinculo := 2;                             
    END IF;
    
    --Pensionista
    IF inCodVinculo is null THEN
        stSql := 'SELECT 1 as contador
                    FROM pessoal'||stEntidade||'.contrato_pensionista
                   WHERE cod_contrato = '||inCodContrato;
        IF selectIntoInteger(stSql) THEN
            inCodVinculo := 3;                             
        END IF;         
    END IF;
    
    --Ativo
    IF inCodVinculo is null THEN
        inCodVinculo := 1;
    END IF;
    


    --BUSCA CÓDIGO E TIMESTAMP DA PREVIDENCIA
    --PARA BUSCAR OS EVENTOS VINCULADOS A ESSA PREVIDENCIA
    stSql := 'SELECT previdencia_previdencia.cod_previdencia
                    , max(previdencia_previdencia.timestamp) AS timestamp                   
                 FROM (SELECT previdencia_previdencia.*
                         FROM folhapagamento'||stEntidade||'.previdencia
                            , folhapagamento'||stEntidade||'.previdencia_previdencia

                    , (SELECT contrato_servidor_previdencia.cod_contrato
                            , contrato_servidor_previdencia.cod_previdencia
                         FROM pessoal'||stEntidade||'.contrato_servidor_previdencia
                            , (  SELECT max(timestamp) as timestamp
                                     , cod_contrato
                                  FROM pessoal'||stEntidade||'.contrato_servidor_previdencia
                              GROUP BY cod_contrato) as max_contrato_servidor_previdencia
                        WHERE contrato_servidor_previdencia.cod_contrato = max_contrato_servidor_previdencia.cod_contrato
                          AND contrato_servidor_previdencia.timestamp    = max_contrato_servidor_previdencia.timestamp
                          AND contrato_servidor_previdencia.bo_excluido = false
                        UNION
                       SELECT contrato_pensionista_previdencia.cod_contrato
                            , contrato_pensionista_previdencia.cod_previdencia
                         FROM pessoal'||stEntidade||'.contrato_pensionista_previdencia
                            , (  SELECT max(timestamp) as timestamp
                                     , cod_contrato
                                  FROM pessoal'||stEntidade||'.contrato_pensionista_previdencia
                              GROUP BY cod_contrato) as max_contrato_pensionista_previdencia
                        WHERE contrato_pensionista_previdencia.cod_contrato = max_contrato_pensionista_previdencia.cod_contrato
                          AND contrato_pensionista_previdencia.timestamp    = max_contrato_pensionista_previdencia.timestamp) as servidor_pensionista_previdencia

               
                        WHERE servidor_pensionista_previdencia.cod_previdencia = previdencia.cod_previdencia
                          AND previdencia.cod_previdencia = previdencia_previdencia.cod_previdencia
                          AND previdencia_previdencia.tipo_previdencia= ''o''
                          AND previdencia.cod_vinculo = '||inCodVinculo||'
                          AND previdencia_previdencia.vigencia        <= '''||dtVigencia||'''
                          AND servidor_pensionista_previdencia.cod_contrato = '||inCodContrato||') as previdencia_previdencia
               GROUP BY previdencia_previdencia.cod_previdencia';
    OPEN crCursor FOR EXECUTE stSql;
        FETCH crCursor INTO inCodPrevidencia,stTimestamp;
    CLOSE crCursor;

    IF inCodPrevidencia is null THEN
        inCodPrevidencia := 0;
        stTimestamp      := NULL;
    END IF;

    IF countBufferTexto('stTimestampPrevidencia') = 0 AND stTimestamp IS NOT NULL THEN
        stTimestamp := criarBufferTexto('stTimestampPrevidencia',stTimestamp);
    END IF;

    RETURN inCodPrevidencia;
END;
$$ LANGUAGE 'plpgsql';


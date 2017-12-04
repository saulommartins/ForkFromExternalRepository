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
--     * Pl para retorno do valor do padrão do cargo do contrato 
--     * Data de Criação: 10/03/2008
-- 
-- 
--     * @author Diego Lemos de Souza
-- 
--     * Casos de uso: uc-09.09.99
-- 
--     $Id: pega0CampoSalarioCargo.sql 31697 2008-08-04 19:33:31Z souzadl $
CREATE OR REPLACE FUNCTION pega0CampoSalarioCargo() RETURNS Numeric as $$

DECLARE
    inCodContrato               INTEGER;
    inCodPadrao                 INTEGER;
    inCodNivelPadrao            INTEGER;
    inDiferencaAnos             INTEGER;
    inDiferencaMeses            INTEGER;
    stVigencia                  VARCHAR:='';
    dtVigencia                  VARCHAR:='';
    dtInicioPrograssao          VARCHAR:='';
    stSql                       VARCHAR:='';
    stDataFinal                 VARCHAR;
    stDataInicial               VARCHAR;    
    nuSalario                   NUMERIC := 0.00;
    nuSalarioNivelPadrao        NUMERIC;
    crCursor                    REFCURSOR;
    stEntidade               VARCHAR := recuperarBufferTexto('stEntidade');
BEGIN
    inCodContrato := recuperarBufferInteiro('inCodContrato');
    inCodContrato := recuperaContratoServidorPensionista(inCodContrato);
    stVigencia := recuperarBufferTexto('stDatafinalCompetencia'); 
 
    dtVigencia := substr( stVigencia,1,10 );
    stSql := '  SELECT padrao_padrao.valor
                     , padrao_padrao.cod_padrao
                  FROM pessoal'|| stEntidade ||'.contrato_servidor
                     , pessoal'|| stEntidade ||'.cargo_padrao
                     , (  SELECT cod_cargo
                               , max(timestamp) as timestamp
                            FROM pessoal'|| stEntidade ||'.cargo_padrao
                        GROUP BY cod_cargo) as max_cargo_padrao
                     , folhapagamento'|| stEntidade ||'.padrao_padrao
                     , (  SELECT cod_padrao
                               , max(timestamp) as timestamp
                            FROM folhapagamento'|| stEntidade ||'.padrao_padrao
                        GROUP BY cod_padrao) as max_padrao_padrao
                 WHERE contrato_servidor.cod_cargo = cargo_padrao.cod_cargo
                   AND cargo_padrao.cod_cargo = max_cargo_padrao.cod_cargo
                   AND cargo_padrao.timestamp = max_cargo_padrao.timestamp
                   AND cargo_padrao.cod_padrao = padrao_padrao.cod_padrao
                   AND padrao_padrao.cod_padrao = max_padrao_padrao.cod_padrao
                   AND padrao_padrao.timestamp = max_padrao_padrao.timestamp
                   AND contrato_servidor.cod_contrato  = '|| inCodContrato ||'
                   AND padrao_padrao.vigencia <= '|| quote_literal(dtVigencia) ||' ';  
    OPEN crCursor FOR EXECUTE stSql;
        FETCH crCursor INTO nuSalario,inCodPadrao;
    CLOSE crCursor;      
    IF nuSalario is null THEN
        nuSalario := 0;
    END IF;  
    
    IF inCodPadrao IS NOT NULL THEN 
        stSql := 'SELECT contrato_servidor_inicio_progressao.dt_inicio_progressao
                    FROM pessoal'|| stEntidade ||'.contrato_servidor_inicio_progressao
                    , (  SELECT cod_contrato
                                , max(timestamp) as timestamp
                            FROM pessoal'|| stEntidade ||'.contrato_servidor_inicio_progressao
                        GROUP BY cod_contrato) as max_contrato_servidor_inicio_progressao
                WHERE contrato_servidor_inicio_progressao.cod_contrato = max_contrato_servidor_inicio_progressao.cod_contrato
                    AND contrato_servidor_inicio_progressao.timestamp = max_contrato_servidor_inicio_progressao.timestamp
                    AND NOT EXISTS (SELECT 1 
                                    FROM pessoal'|| stEntidade ||'.contrato_servidor_caso_causa
                                    WHERE contrato_servidor_inicio_progressao.cod_contrato = contrato_servidor_caso_causa.cod_contrato)
                    AND contrato_servidor_inicio_progressao.cod_contrato = '|| inCodContrato;                   
        dtInicioPrograssao := selectIntoVarchar(stSql);   
        IF dtInicioPrograssao IS NOT NULL THEN
            stDataFinal := stVigencia;
            stDataInicial := dtInicioPrograssao;
            inDiferencaAnos  := date_part('year',age(stDataFinal::timestamp,stDataInicial::timestamp));
            inDiferencaMeses := date_part('month',age(stDataFinal::timestamp,stDataInicial::timestamp));
            inDiferencaMeses := inDiferencaAnos * 12 + inDiferencaMeses;        
    
            stSql := 'SELECT nivel_padrao_nivel.valor
                        FROM folhapagamento'|| stEntidade ||'.nivel_padrao_nivel
                           , (  SELECT cod_padrao
                                     , cod_nivel_padrao
                                     , max(timestamp) as timestamp
                                  FROM folhapagamento'|| stEntidade ||'.nivel_padrao_nivel
                              GROUP BY cod_padrao
                                     , cod_nivel_padrao) as max_nivel_padrao_nivel
                       WHERE nivel_padrao_nivel.cod_padrao = max_nivel_padrao_nivel.cod_padrao
                         AND nivel_padrao_nivel.cod_nivel_padrao = max_nivel_padrao_nivel.cod_nivel_padrao
                         AND nivel_padrao_nivel.timestamp = max_nivel_padrao_nivel.timestamp
                         AND nivel_padrao_nivel.cod_padrao = '|| inCodPadrao ||'
                         AND nivel_padrao_nivel.qtdmeses <='|| inDiferencaMeses ||'
                    ORDER BY nivel_padrao_nivel.qtdmeses desc LIMIT 1';  
            nuSalarioNivelPadrao := selectIntoNumeric(stSql);
            IF nuSalarioNivelPadrao IS NOT NULL THEN
                nuSalario := nuSalarioNivelPadrao;
            END IF;
        END IF;
    END IF;                                            
    RETURN nuSalario  ;
END;
$$ LANGUAGE 'plpgsql';



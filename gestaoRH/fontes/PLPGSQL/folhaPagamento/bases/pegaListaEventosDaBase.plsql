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
--/**
--    * Função PLSQL
--    * Data de Criação: 09/07/2007
--
--
--    * @author Desenvolvedor: Rafael Garbin
--
--    * Casos de uso: uc-04.05.68
--
--    $Id: pegaListaEventoDaBase.plsql
--*/

CREATE OR REPLACE FUNCTION  pegaListaEventosDaBase(VARCHAR) RETURNS VARCHAR as $$ 
DECLARE
    stNomBase      ALIAS FOR $1;
    stEntidade     VARCHAR := '';
    stSql          VARCHAR := '';
    stRetorno      VARCHAR := '';
    reRegistro     RECORD;
BEGIN

    stEntidade = recuperarBufferTexto('stEntidade');
    stSql := ' SELECT evento.codigo                                  
                 FROM folhapagamento'||stEntidade||'.bases_evento       
                    , (SELECT cod_base                                           
                            , MAX(timestamp) as max_timestamp                    
                        FROM folhapagamento'||stEntidade||'.bases_evento 
                       GROUP BY cod_base                                          
                      ) as max_bases_evento
                    , folhapagamento'||stEntidade||'.evento
                    , folhapagamento'||stEntidade||'.bases
                WHERE bases_evento.cod_base  = max_bases_evento.cod_base          
                  AND bases_evento.timestamp = max_bases_evento.max_timestamp
                  AND bases_evento.cod_evento  = evento.cod_evento
                  AND bases_evento.cod_base  = bases.cod_base
                  AND bases.nom_base ilike ('||quote_literal(stNomBase)||')';

    FOR reRegistro IN EXECUTE stSql LOOP
        stRetorno = stRetorno||reRegistro.codigo||';';
    END LOOP;
  
    IF length(stRetorno)>0 THEN 
        stRetorno :=  substring(stRetorno, 1, length(stRetorno)-1);
    END IF;

    RETURN stRetorno;
END;
$$ LANGUAGE 'plpgsql';   

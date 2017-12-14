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
/* pega0ExtrasPorCalculo
 * 
 * Data de Criação   : 23/10/2008


 * @author Analista      Dagiane Vieira
 * @author Desenvolvedor Diego Lemos de Souza
 
 * @package URBEM
 * @subpackage 

 $Id:$
 */
CREATE OR REPLACE FUNCTION pega0ExtrasPorCalculo(INTEGER,VARCHAR,VARCHAR,VARCHAR,VARCHAR) RETURNS VARCHAR as $$
DECLARE
    inCodEvento             ALIAS FOR $1;
    stCodDia                ALIAS FOR $2;
    stTipoCalculo           ALIAS FOR $3;
    stHoras                 ALIAS FOR $4;
    stEntidade              ALIAS FOR $5;
    stExtras                VARCHAR := '00:00';
    stHorasTemp             VARCHAR;
    stSql                   VARCHAR;
    reRegistro              RECORD;
DECLARE
BEGIN
    stSql := 'SELECT faixas_horas_extra.horas
               FROM ponto'|| stEntidade ||'.formato_faixas_horas_extras
               JOIN ponto'|| stEntidade ||'.dados_exportacao
                 ON dados_exportacao.cod_formato = formato_faixas_horas_extras.cod_formato
                AND dados_exportacao.cod_dado = formato_faixas_horas_extras.cod_dado
               JOIN ponto'|| stEntidade ||'.faixas_horas_extra
                 ON faixas_horas_extra.cod_configuracao = formato_faixas_horas_extras.cod_configuracao
                AND faixas_horas_extra.timestamp = formato_faixas_horas_extras.timestamp
                AND faixas_horas_extra.cod_faixa = formato_faixas_horas_extras.cod_faixa
               JOIN ponto'|| stEntidade ||'.faixas_dias
                 ON faixas_horas_extra.cod_configuracao = faixas_dias.cod_configuracao
                AND faixas_horas_extra.timestamp = faixas_dias.timestamp
                AND faixas_horas_extra.cod_faixa = faixas_dias.cod_faixa
               JOIN ponto'|| stEntidade ||'.configuracao_relogio_ponto
                 ON configuracao_relogio_ponto.cod_configuracao = formato_faixas_horas_extras.cod_configuracao
                AND configuracao_relogio_ponto.ultimo_timestamp = formato_faixas_horas_extras.timestamp
              WHERE NOT EXISTS (SELECT 1
                                  FROM ponto'|| stEntidade ||'.configuracao_relogio_ponto_exclusao
                                 WHERE configuracao_relogio_ponto_exclusao.cod_configuracao = configuracao_relogio_ponto.cod_configuracao)
                AND faixas_dias.cod_dia IN ('|| stCodDia ||')
                AND dados_exportacao.cod_evento = '|| inCodEvento ||'
                AND faixas_horas_extra.calculo_horas_extra = '|| quote_literal(stTipoCalculo) ||'
           ORDER BY faixas_horas_extra.percentual';  
    IF stTipoCalculo = 'S' OR stTipoCalculo = 'M' THEN
        stSql := 'SELECT * FROM ('|| stSql ||') as tabela GROUP BY horas';
    END IF;
    stHorasTemp := stHoras;
    FOR reRegistro IN EXECUTE stSql LOOP
        IF reRegistro.horas <= stHorasTemp THEN
            stextras    := selectintovarchar('SELECT interval '|| quote_literal(stextras)    ||' + interval '|| quote_literal(reregistro.horas) ||' ');
            sthorastemp := selectintovarchar('SELECT interval '|| quote_literal(sthorastemp) ||' - interval '|| quote_literal(reregistro.horas) ||' ');
        END IF;
    END LOOP;
    return stExtras;
END
$$ LANGUAGE 'plpgsql';

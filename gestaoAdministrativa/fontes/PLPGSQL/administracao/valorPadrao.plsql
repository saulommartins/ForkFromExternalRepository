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
* $Revision: 3077 $
* $Name$
* $Author: pablo $
* $Date: 2005-11-29 14:53:37 -0200 (Ter, 29 Nov 2005) $
*
* Casos de uso: uc-01.03.96
*/

CREATE OR REPLACE FUNCTION administracao.valor_padrao(INTEGER,INTEGER ,INTEGER ,VARCHAR) RETURNS VARCHAR AS $$
DECLARE
    inCodAtributo       ALIAS FOR $1;
    inCodModulo         ALIAS FOR $2;
    inCodCadastro       ALIAS FOR $3;
    stValores           ALIAS FOR $4;
    stSql               VARCHAR   := '';
    stSaida             VARCHAR   := '';
    stValor             VARCHAR   := '';
    reRegistro          RECORD;
    inCodTipo           INTEGER;
    inCount             INTEGER := 1;
    inCount2            INTEGER := 1;
    arValores           VARCHAR[] := array[0];
    arEntrada           VARCHAR[] := array[0];

BEGIN
        SELECT INTO 
                   inCodTipo
                   cod_tipo
              FROM administracao.atributo_dinamico as atr
             WHERE atr.cod_atributo = inCodAtributo AND
                   atr.cod_cadastro = inCodCadastro AND
                   atr.cod_modulo   = inCodModulo;

        IF inCodTipo = 4 OR inCodTipo = 3 THEN
            FOR reRegistro IN  
                SELECT   ativo
                        ,cod_valor
                FROM
                         administracao.atributo_valor_padrao
                WHERE    cod_atributo = inCodAtributo AND
                         cod_cadastro = inCodCadastro AND
                         cod_modulo   = inCodModulo
                ORDER BY cod_valor
            LOOP
                IF reRegistro.ativo THEN
                    arValores[inCount] := reRegistro.cod_valor;
                    inCount := inCount + 1;
                END IF;
            END LOOP;
            inCount := 1;
            arEntrada := string_to_array(trim(stValores),',');
            WHILE true LOOP
                IF arValores[inCount] IS NULL THEN 
                    EXIT; --Saida do Loop
                END IF;
                inCount2 := 1;
                WHILE true LOOP
                    IF arEntrada[inCount2] IS NULL THEN 
                        EXIT; --Saida do Loop
                    END IF;
                    IF arValores[inCount] = arEntrada[inCount2] THEN
                        inCount2 := 0;
                        EXIT; --Saida do Loop
                    END IF;
                    inCount2 := inCount2 + 1;
                END LOOP;
                IF inCount2 <> 0 THEN
                    stSaida := stSaida || ',' || arValores[inCount];
                END IF;
                inCount := inCount + 1;
            END LOOP;
            IF length(stSaida) > 1 THEN
                stSaida := substr(stSaida,2,length(stSaida));
            END IF;
        ELSE
            SELECT INTO 
                   stSaida
                   valor_padrao
            FROM   administracao.atributo_valor_padrao
            WHERE  cod_atributo = inCodAtributo AND
                   cod_cadastro = inCodCadastro AND
                   cod_modulo   = inCodModulo;
        END IF;

    RETURN stSaida;
END;
$$ language 'plpgsql';

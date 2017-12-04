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
*
* $Id: $
*
* Casos de uso: uc-01.01.00
*/

CREATE OR REPLACE FUNCTION publico.fn_mes_extenso(date) RETURNS VARCHAR AS $$
DECLARE
  stData  DATE    :=$1;
  stTemp  VARCHAR :='';
  retorno VARCHAR :='';
BEGIN
    SELECT INTO stTemp lpad(to_char(stData,'mm'), 2, '0');

    IF stTemp = '01' THEN retorno := 'Janeiro';   END IF;
    IF stTemp = '02' THEN retorno := 'Fevereiro'; END IF;
    IF stTemp = '03' THEN retorno := 'Março';     END IF;
    IF stTemp = '04' THEN retorno := 'Abril';     END IF;
    IF stTemp = '05' THEN retorno := 'Maio';      END IF;
    IF stTemp = '06' THEN retorno := 'Junho';     END IF;
    IF stTemp = '07' THEN retorno := 'Julho';     END IF;
    IF stTemp = '08' THEN retorno := 'Agosto';    END IF;
    IF stTemp = '09' THEN retorno := 'Setembro';  END IF;
    IF stTemp = '10' THEN retorno := 'Outubro';   END IF;
    IF stTemp = '11' THEN retorno := 'Novembro';  END IF;
    IF stTemp = '12' THEN retorno := 'Dezembro';  END IF;
    IF stTemp = '00' THEN retorno := '';          END IF;

    RETURN trim(retorno);
END;
$$ LANGUAGE plpgsql IMMUTABLE;

CREATE OR REPLACE FUNCTION publico.fn_data_extenso(DATE) RETURNS VARCHAR AS $$
DECLARE
    dt        DATE:=$1;
    retorno   VARCHAR;
    stSQL     VARCHAR:='';     
    crCursor  REFCURSOR;
BEGIN
    stSQL := 'select to_char(date '''||dt||''', ''dd'')||'' de ''||publico.fn_mes_extenso('''||dt||''')||'' de ''||to_char(date '''||dt||''', ''yyyy'')';
    
    OPEN crCursor FOR EXECUTE stSql;
        FETCH crCursor INTO retorno;
    CLOSE crCursor;    
    
    RETURN trim(retorno);
END;
$$ LANGUAGE 'plpgsql';

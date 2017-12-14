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
* Agregacao que retorna um Array do tipo do campo requerido.
* Ex: 
* SELECT publico.concatenar_array(cod_modulo) FROM administracao.modulo WHERE cod_gestao=1;
*     array
*------------------
* {2,4,11,5,15,19}
*(1 registro)
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: concatenarArray.plsql 64421 2016-02-19 12:14:17Z fabio $
*
* Casos de uso: uc-01.01.00
*/

CREATE OR REPLACE FUNCTION criar_agg_array() RETURNS BOOLEAN AS $$
DECLARE

  varAchouArr VARCHAR;

BEGIN

  SELECT proname INTO varAchouArr
    FROM pg_proc
   WHERE proname = 'concatenar_array';

    IF NOT FOUND THEN

        CREATE AGGREGATE publico.concatenar_array (
            sfunc    = array_append,
            basetype = anyelement,
            stype    = anyarray,
            initcond = '{}'
        );

    ELSE

        DROP AGGREGATE publico.concatenar_array (anyelement);

        CREATE AGGREGATE publico.concatenar_array (
            sfunc    = array_append,
            basetype = anyelement,
            stype    = anyarray,
            initcond = '{}'
        );

    END IF;

    RETURN TRUE;
END;
$$ LANGUAGE 'plpgsql';

SELECT criar_agg_array();


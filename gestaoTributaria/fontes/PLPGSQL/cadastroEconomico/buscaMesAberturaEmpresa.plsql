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

*
* $Id: $
*
* Casos de uso: uc-05.03.05
*
*/

/*
$Log$

*/

CREATE OR REPLACE FUNCTION economico.buscaMesAberturaEmpresa( inInscricaoEconomica INTEGER )
RETURNS INTEGER AS $$
DECLARE
    dtAbertura      DATE;
    inMes           NUMERIC;
BEGIN

   SELECT dt_abertura
     INTO dtAbertura
     FROM economico.cadastro_economico
    WHERE inscricao_economica = inInscricaoEconomica
        ;

    inMes := split_part( dtAbertura ,'-' , 2 )::int;

   RETURN inMes;
END;
$$ LANGUAGE 'plpgsql';

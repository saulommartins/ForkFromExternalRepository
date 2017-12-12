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
/**
    * Resumo para Emissão das Autorizações de Empenho
    * Data de Criação: 19/07/2007
    
    
    * @author Analista: Dagiane
    * @author Desenvolvedor: Diego Lemos de Souza
    
    * @ignore
    
    $Revision: 25454 $
    $Name$
    $Author: souzadl $
    $Date: 2007-09-13 14:58:03 -0300 (Qui, 13 Set 2007) $
    
    * Casos de uso: uc-04.05.62   
*/


CREATE OR REPLACE FUNCTION fn_conta_mae(varchar) RETURNS varchar AS $$
DECLARE
   stString        ALIAS FOR $1;
    stSuperior      VARCHAR := '';

BEGIN

    SELECT
        CASE WHEN publico.fn_strcount (cast(estrutural as varchar),'.') >= 1 THEN
            publico.fn_mascarareduzida( substr( estrutural, 1, publico.fn_lposition(estrutural,'.')-1 ) )
        ELSE
            publico.fn_mascarareduzida(estrutural)
        END INTO stSuperior
    FROM (
        SELECT
            publico.fn_mascarareduzida(cast ( stString as varchar)) as estrutural
    ) as tabela;


RETURN stSuperior;
END;
$$ LANGUAGE 'plpgsql';

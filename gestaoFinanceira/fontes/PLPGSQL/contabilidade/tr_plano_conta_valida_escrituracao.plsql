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
 * 
 * Data de Criação   :

 * @author Analista
 * @author Desenvolvedor
.
 * @package URBEM

 *  $Id: 
*/

CREATE OR REPLACE FUNCTION tr_plano_conta_valida_escrituracao() RETURNS TRIGGER AS $$
DECLARE
BEGIN
    IF (     NEW.natureza_info       IS NULL
         AND NEW.natureza_saldo      IS NULL
         AND NEW.indicador_superavit IS NULL
         AND NEW.escrituracao        = 'sintetica'
       )
       OR
       ( NEW.escrituracao        = 'analitica'
       )
    THEN
        RETURN NEW;
    ELSE
        RAISE EXCEPTION 'Erro ao especificar conta Sintética ou Analítica!';
        -- RETURN NULL;
    END IF;
END;
$$ LANGUAGE 'plpgsql';


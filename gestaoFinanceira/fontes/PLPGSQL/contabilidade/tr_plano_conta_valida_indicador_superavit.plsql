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
/* tr_plano_conta_valida_indicador_superavit
*
* Data de Criacao : 18/10/2013

* @author Analista : 
* @author Desenvolvedor : 

* @package URBEM
* @subpackage

$Id:$
*/

CREATE OR REPLACE FUNCTION tr_plano_conta_valida_indicador_superavit() RETURNS TRIGGER AS $$
DECLARE
BEGIN
    IF (NEW.indicador_superavit IS NOT NULL) AND (trim(NEW.indicador_superavit) != '') THEN
        PERFORM 1
           FROM contabilidade.sistema_contabil
          WHERE (    '1' = ANY(string_to_array(grupos,','))
                  OR '2' = ANY(string_to_array(grupos,','))
                )
            AND NEW.cod_sistema = cod_sistema
              ;
        IF FOUND THEN
            RETURN NEW;
        ELSE
            RAISE EXCEPTION 'Indicador de Superávit (%) inválido para o Sistema Contábil utilizado (%)!',trim(NEW.indicador_superavit), NEW.cod_sistema;
            -- RETURN NULL;
        END IF;
    ELSE
        RETURN NEW;
    END IF;
END;
$$ LANGUAGE 'plpgsql';

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
    * Retorna a lista de receitas 
    * Data de Criação: 08/06/2009 
 
 
    * @author Analista:      Tonismar Bernardo <tonismar.bernardo@cnm.org.br> 
    * @author Desenvolvedor: Henrique Boaventura <henrique.boaventura@cnm.org.br> 
 
    * @package      URBEM 
    * @subpackage   LDO 
 
    * $Id: $ 
*/

CREATE OR REPLACE FUNCTION ldo.fn_verifica_homologacao_ldo(inCodPPA INTEGER, stAno CHAR(1)) RETURNS BOOLEAN AS $$
DECLARE
    boExist         BOOLEAN := FALSE;
    tpHomologacao   TIMESTAMP;
    tpLDO           TIMESTAMP;

BEGIN
   
    SELECT MAX(timestamp)
      INTO tpHomologacao
      FROM ldo.homologacao
     WHERE cod_ppa = inCodPPA
       AND ano     = stAno;

    IF(tpHomologacao IS NOT NULL)
    THEN
        
        SELECT timestamp
          INTO tpLDO
          FROM ldo.ldo
         WHERE cod_ppa = inCodPPA
           AND ano     = stAno;

        IF (tpLDO < tpHomologacao)
        THEN
            boExist := TRUE;
        END IF;
 
    END IF;

    RETURN boExist;

END;

$$ LANGUAGE 'plpgsql';


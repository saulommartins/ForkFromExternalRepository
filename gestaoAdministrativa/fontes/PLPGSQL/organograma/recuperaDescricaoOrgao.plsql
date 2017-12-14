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
/* recuperaDescricaoOrgao
 * 
 * Data de Criação :


 * @author Analista : Dagiane
 * @author Desenvolvedor : Rafael Garbin
 
 * @package URBEM
 * @subpackage 

 $Id:$
 */

CREATE OR REPLACE FUNCTION recuperaDescricaoOrgao(INTEGER,DATE) RETURNS VARCHAR AS $$
DECLARE
    inCodOrgao       ALIAS FOR $1;
    stData           ALIAS FOR $2;
    stDescricao      VARCHAR:='';
BEGIN
    SELECT orgao_descricao.descricao
      INTO stDescricao
      FROM organograma.orgao_descricao
     WHERE cod_orgao = inCodOrgao
       AND timestamp::date <= stData
  ORDER BY timestamp DESC
     LIMIT 1;

    IF trim(stDescricao) = '' OR stDescricao IS NULL THEN 
            SELECT orgao_descricao.descricao
              INTO stDescricao
              FROM organograma.orgao_descricao
             WHERE cod_orgao = inCodOrgao
          ORDER BY timestamp
             LIMIT 1;    
    END IF;
    
    RETURN stDescricao;
END;
$$ LANGUAGE 'plpgsql';

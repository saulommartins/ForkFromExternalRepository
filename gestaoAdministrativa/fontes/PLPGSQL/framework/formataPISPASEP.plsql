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
 * Titulo do arquivo Formata o código do PISPASEP
 * Data de Criação   : 16/09/2008


 * @author Analista      Dagiane
 * @author Desenvolvedor Rafael Garbin
 
 * @package URBEM
 * @subpackage 

 * @ignore # só use se for paginas que o cliente visualiza, se for mapeamento ou classe de negocio não se usa

 $Id:$
 */

CREATE OR REPLACE FUNCTION formataPISPASEP (VARCHAR) RETURNS varchar
AS $$
DECLARE
    pispasep              VARCHAR:=$1;
    pispasepFormatado     VARCHAR:='';
BEGIN
    pispasep := upper(trim(pispasep));
    pispasep := replace(pispasep, '.', '');
    pispasep := replace(pispasep, ',', '');
    pispasep := replace(pispasep, '-', '');
    pispasep := replace(pispasep, '/', '');
    pispasep := replace(pispasep, ' ', '');

    IF pispasep != '' and pispasep IS NOT NULL and length(pispasep)=11 THEN
        SELECT INTO pispasepFormatado
            substring(pispasep,1,3)||'.'||substring(pispasep,4,5)||'.'||substring(pispasep,9,2)||'-'||substring(pispasep,10,1);
    ELSE
        pispasep := '';
    END IF;

    RETURN pispasepFormatado;
END
$$ LANGUAGE 'plpgsql';

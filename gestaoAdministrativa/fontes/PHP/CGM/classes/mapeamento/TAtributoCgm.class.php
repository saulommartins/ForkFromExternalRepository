<?php
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
?>
<?php
/**
    * Classe de Mapeamento para tabela cgm
    * Data de Criação: 18/04/2009

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    $Revision: 28411 $
    $Name$
    $Author: diogo.zarpelon $
    $Date: 2008-03-06 16:32:26 -0300 (Qui, 06 Mar 2008) $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TAtributoCgm extends Persistente
{
function TAtributoCgm()
{
    parent::Persistente();
    $this->setTabela('sw_atributo_cgm');
    $this->setCampoCod('cog_atributo');

    $this->AddCampo('cod_atributo',    'integer', true,  '' , true,  false);
    $this->AddCampo('nom_atributo',    'varchar', true,  '' , false, false);
    $this->AddCampo('tipo',            'char'   , true,  '1', false, false);
    $this->AddCampo('valor_padrao',    'varchar', true,  '' , false, false);
}

function montaRecuperaRelacionamento()
{
    $stSql  = " SELECT ";
    $stSql .= "     AC.COD_ATRIBUTO AS COD_ATRIBUTO, ";
    $stSql .= "     AC.NOM_ATRIBUTO AS NOM_ATRIBUTO, ";
    $stSql .= "     AC.TIPO AS TIPO, ";
    $stSql .= "     AC.VALOR_PADRAO AS VALOR_PADRAO, ";
    $stSql .= "     CAV.NUMCGM AS NUMCGM, ";
    $stSql .= "     CAV.VALOR AS VALOR ";
    $stSql .= " FROM ";
    $stSql .= "     sw_atributo_cgm AS AC ";
    $stSql .= " LEFT JOIN ";
    $stSql .= "     sw_cgm_atributo_valor AS CAV ";
    $stSql .= " ON ";
    $stSql .= "     AC.COD_ATRIBUTO = CAV.COD_ATRIBUTO ";
    if ( $this->getDado('inNumCGM') ) {
        $stSql .= "     AND CAV.NUMCGM = ".$this->getDado('inNumCGM')." ";
    } else {
        $stSql .= "     AND CAV.NUMCGM IS NULL ";
    }

    return $stSql;
}

} // classe

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
    * Classe de mapeamento da tabela ponto.escala
    * Data de Criação: 10/10/2008

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Alex Cardoso

    * @package URBEM
    * @subpackage Mapeamento

    * Casos de uso: uc-04.10.02

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TPontoEscala extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPontoEscala()
{
    parent::Persistente();
    $this->setTabela("ponto.escala");

    $this->setCampoCod('cod_escala');
    $this->setComplementoChave('');

    $this->AddCampo('cod_escala'      ,'sequence' ,true  ,''    ,true,false);
    $this->AddCampo('descricao'       ,'varchar'  ,true  ,'80'  ,false,false);
    $this->AddCampo('ultimo_timestamp','timestamp',true  ,''    ,false,false);

}

function recuperaEscalasAtivas(&$rsRecordset,$stFiltro="",$stOrdem="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaEscalasAtivas",$rsRecordset,$stFiltro,$stOrdem,$boTransacao);
}

function montaRecuperaEscalasAtivas()
{
    $stSql .= "SELECT *\n";
    $stSql .= "  FROM ponto.escala\n";
    $stSql .= " WHERE NOT EXISTS (SELECT 1\n";
    $stSql .= "                     FROM ponto.escala_exclusao\n";
    $stSql .= "                    WHERE escala_exclusao.cod_escala = escala.cod_escala)\n";

    if ($this->getDado('cod_escala')) {
        $stSql .= " AND escala.cod_escala = ".$this->getDado('cod_escala')."\n";
    }

    if ($this->getDado('descricao')) {
        $stSql .= " AND escala.descricao ilike '".$this->getDado('descricao')."%'\n";
    }

    return $stSql;
}

}
?>

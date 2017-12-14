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
  * Página de
  * Data de criação : 25/10/2005

    * @author Analista:
    * @author Programador: Fernando Zank Correa Evangelista

    $Revision: 28706 $
    $Name$
    $Author: luiz $
    $Date: 2008-03-24 16:17:06 -0300 (Seg, 24 Mar 2008) $

    Caso de uso: uc-03.01.09
**/

/*
$Log$
Revision 1.9  2007/09/18 15:36:28  hboaventura
Adicionando ao repositório

Revision 1.8  2006/07/06 14:07:04  diego
Retirada tag de log com erro.

Revision 1.7  2006/07/06 12:11:27  diego

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TPatrimonioEspecie extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPatrimonioEspecie()
{
    parent::Persistente();
    $this->setTabela('patrimonio.especie');
    $this->setCampoCod('cod_especie');
    $this->setComplementoChave('cod_natureza, cod_grupo');

    $this->AddCampo('cod_especie','integer',true,'',true,false);
    $this->AddCampo('cod_grupo','integer',true,'',true,"TPatrimonioGrupo");
    $this->AddCampo('cod_natureza','integer',true,'',true,"TPatrimonioGrupo");
    $this->AddCampo('nom_especie','varchar',true,'60',false,false);

}

function recuperaEspecie(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
     return $this->executaRecupera("montaRecuperaEspecie",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaEspecie()
{
    $stSql = "
        SELECT especie.cod_especie
             , especie.nom_especie
             , grupo.cod_grupo
             , grupo.nom_grupo
             , natureza.cod_natureza
             , natureza.nom_natureza
          FROM patrimonio.especie
    INNER JOIN patrimonio.grupo
            ON grupo.cod_grupo = especie.cod_grupo
           AND grupo.cod_natureza = especie.cod_natureza
    INNER JOIN patrimonio.natureza
            ON natureza.cod_natureza = especie.cod_natureza
         WHERE ";
    if ( $this->getDado( 'cod_natureza' ) ) {
        $stSql .= " especie.cod_natureza = ".$this->getDado( 'cod_natureza' )."  AND ";
    }
    if ( $this->getDado( 'cod_grupo' ) ) {
        $stSql .= " especie.cod_grupo = ".$this->getDado( 'cod_grupo' )."  AND ";
    }
    if ( $this->getDado( 'cod_especie' ) ) {
        $stSql .= " especie.cod_especie = ".$this->getDado( 'cod_especie' )."  AND ";
    }
    if ( $this->getDado( 'nom_especie' ) ) {
        $stSql .= " especie.nom_especie = '".$this->getDado( 'nom_especie' )."'  AND ";
    }

    return substr($stSql,0,-6);
}

function recuperaMaxEspecie(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
     return $this->executaRecupera("montaRecuperaMaxEspecie",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaMaxEspecie()
{
    $stSql = "
        SELECT max(cod_especie) as max
          FROM patrimonio.especie";

    return $stSql;
}

}

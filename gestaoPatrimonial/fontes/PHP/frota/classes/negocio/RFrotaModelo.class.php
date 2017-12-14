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
  * Data de criação : 15/03/2006

    * @author Analista:
    * @author Programador: Fernando Zank Correa Evangelista

    $Revision: 12234 $
    $Name$
    $Author: diego $
    $Date: 2006-07-06 11:08:37 -0300 (Qui, 06 Jul 2006) $

    Caso de uso: uc-03.01.09
**/

/*
$Log$
Revision 1.4  2006/07/06 13:57:42  diego
Retirada tag de log com erro.

Revision 1.3  2006/07/06 12:11:17  diego

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GP_FRO_MAPEAMENTO."TFrotaModelo.class.php"                                        );
include_once ( CAM_GP_FRO_NEGOCIO."RFrotaMarca.class.php"                                            );
include_once ( CAM_FW_BANCO_DADOS."Transacao.class.php"                                              );

class RFrotaModelo
{
/**
    * @Acces Private
    * @var Object
*/

var $obTFrotaModelo;
/**
    * @Acces Private
    * @var Object
*/

var $obRFrotaMarca;

/**
    * @access Public
    * @param Object $Valor
*/

function setTFrotaModelo($valor) { $this->obTFrotaModelo = $valor; }
/**
    * @access Public
    * @param Object $Valor
*/
function setCodModelo($valor) { $this->inCodModelo = $valor; }

/**
    * @access Public
    * @return Object
*/
function getTFrotaModelo() { return $this->obTFrotaModelo; }
/**
    * @access Public
    * @return Object
*/
function getCodModelo() { return $this->inCodModelo; }

//método Construtor
function RFrotaModelo()
{
    $this->obTFrotaModelo           = new TFrotaModelo;
    $this->obRFrotaMarca            = new RFrotaMarca;

}
function listar(&$rsLista, $stOrder = "nom_grupo", $boTransacao = "")
{
    $obErro = new Erro;
    if ($this->obRFrotaMarca->getCodMarca()) {
        $stFiltro .= "and ma.cod_marca = ".$this->obRFrotaMarca->getCodMarca()."";
    }
    if ($this->inCodModelo) {
        $stFiltro .= "and mo.cod_modelo = ".$this->inCodModelo."";
    }

    $obErro = $this->obTFrotaModelo->recuperaModelo( $rsLista, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

}

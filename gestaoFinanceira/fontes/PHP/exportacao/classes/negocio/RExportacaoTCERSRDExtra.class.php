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
    * Classe de regra de negócio RD Extra
    * Data de Criação: 14/02/2005

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Diego LEmos de Souza

    * @package URBEM
    * @subpackage Regra

    $Revision: 30668 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.08.04
*/

/*
$Log$
Revision 1.8  2006/07/05 20:46:04  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GF_EXP_MAPEAMENTO."TExportacaoTCERSRDExtra.class.php"          );
include_once ( CAM_GF_CONT_NEGOCIO."RContabilidadePlanoConta.class.php"  	);

class RExportacaoTCERSRDExtra extends RContabilidadePlanoConta
{
var $obTExportacaoTCERSRDExtra;
var $inClassificacao;

//SETTERS
function setTExportacaoTCERSRDExtra($valor) { $this->obTExportacaoTCERSRDExtra      = $valor;  }
function setClassificacao($valor) { $this->inClassificacao      = $valor;  }

//GETTERS
function getTExportacaoTCERSRDExtra() { return $this->obTExportacaoTCERSRDExtra;      }
function getClassificacao() { return $this->inClassificacao;      }

//METODO CONSTRUTOR
/**
     * Método construtor
     * @access Private
*/
function RExportacaoTCERSRDExtra()
{
    parent::RContabilidadePlanoConta();
    $this->setTExportacaoTCERSRDExtra( new TExportacaoTCERSRDExtra() );
}

function salvar($boTransacao = "")
{
    $this->consultar( $boTransacao );
    $this->obTExportacaoTCERSRDExtra->setDado( "cod_conta", $this->getCodConta() );
    $this->obTExportacaoTCERSRDExtra->setDado( "exercicio", $this->getExercicio() );
    $this->obTExportacaoTCERSRDExtra->setDado( "classificacao", $this->getClassificacao() );
    $obErro = $this->obTExportacaoTCERSRDExtra->inclusao( $boTransacao );

    return $obErro;
}

function listar(&$rsRDExtra, $boTransacao = "")
{
    $this->obTExportacaoTCERSRDExtra->setDado( 'exercicio',$this->getExercicio() );
    $obErro = $this->obTExportacaoTCERSRDExtra->recuperaDadosRDExtra( $rsRDExtra, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

function listarClassificacao(&$rsUnidadeOrcamento, $boTransacao = "")
{
    $this->obTExportacaoTCERSRDExtra->setDado( 'exercicio',$this->getExercicio() );
    $obErro = $this->obTExportacaoTCERSRDExtra->recuperaDadosUniOrcam( $rsUnidadeOrcamento, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

}
?>

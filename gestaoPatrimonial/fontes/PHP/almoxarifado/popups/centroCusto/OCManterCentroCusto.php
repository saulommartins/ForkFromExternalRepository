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
* Arquivo instância para popup de Centro de Custo
* Data de Criação: 07/03/2006

* @author Analista: Diego Barbosa Victoria
* @author Desenvolvedor: Diego Barbosa Victoria

$Revision: 15963 $
$Name$
$Author: rodrigo $
$Date: 2006-09-26 08:43:09 -0300 (Ter, 26 Set 2006) $

* Casos de uso: uc-03.03.07
                uc-03.04.01
*/

/*
$Log$
Revision 1.7  2006/09/26 11:43:09  rodrigo
Retirada a execução do JS no SistemaLegado::executaFrameOculto e passado para Ajax

Revision 1.6  2006/09/25 09:41:37  rodrigo
*** empty log message ***

Revision 1.5  2006/07/10 19:40:16  rodrigo
Adicionado nos componentes de itens,marca e centro de custa a função ajax para manipulação dos dados.

Revision 1.4  2006/07/06 14:05:39  diego
Retirada tag de log com erro.

Revision 1.3  2006/07/06 12:10:10  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once(CAM_GP_ALM_NEGOCIO."RAlmoxarifadoPermissaoCentroDeCustos.class.php");
include_once(CAM_GP_ALM_NEGOCIO."RAlmoxarifadoCentroDeCustos.class.php");

$stCampoCod       = $_GET['stNomCampoCod'       ];
$stCampoDesc      = $_GET['stIdCampoDesc'       ];
$inCodigo         = $_REQUEST[ 'inCodigo'       ];
$inCodCentroCusto = $_REQUEST["inCodCentroCusto"];

switch ($_GET['stCtrl']) {

    case 'buscaPopup':
    default:
      if (isset($inCodigo)) {
        if ($usuario) {
           $obRegra = new RAlmoxarifadoPermissaoCentroDeCustos();
           $obRegra->addCentroDeCustos();
           $obRegra->roUltimoCentro->setCodigo( $inCodigo );
           $obRegra->obRCGMPessoaFisica->setNumCGM( Sessao::read('numCgm') );
           $obRegra->listar($rsCentroCusto);
           $stDescricao = $rsCentroCusto->getCampo('descricao');
        } else {
           $obRegra = new RAlmoxarifadoCentroDeCustos();
           $obRegra->setCodigo( $inCodigo );
           $obRegra->consultar();
           $stDescricao = $obRegra->getDescricao();
        }
       $stJs.="d.getElementById('".$stCampoDesc."').innerHTML='".$stDescricao."';";
       $stJs.="retornaValorBscInner('".$stCampoCod."','".$stCampoDesc."','".$_GET['stNomForm']."','".$stDescricao."');";
       if ($stDescricao=="") {
        $stJs.="alertaAviso('@Código do Centro de Custo(".$inCodigo.") não encontrado.','form','erro','".Sessao::getId()."');";
       }
      } elseif (isset($inCodCentroCusto)) {
        $obRegra = new RAlmoxarifadoCentroDeCustos();
        $obRegra->setCodigo( $inCodCentroCusto );
        $obRegra->consultar();
        $stDescricao = $obRegra->getDescricao();
        $stJs.="d.getElementById('".$stCampoDesc."').innerHTML = '".$stDescricao."';";
        $stJs.="retornaValorBscInner('".$stCampoCod."','".$stCampoDesc."','".$_GET['stNomForm']."','".$stDescricao."');";
       if ($stDescricao=="") {
        $stJs.="alertaAviso('@Código do Centro de Custo(".$inCodCentroCusto.") não encontrado.','form','erro','".Sessao::getId()."');";
       }
      } else {
          $stJs .= "f.inCodCentroCusto.value                       = '';      ";
          $stJs .= "d.getElementById('".$stCampoDesc."').innerHTML = '&nbsp;';";
      }
      echo $stJs;
    break;

}

?>

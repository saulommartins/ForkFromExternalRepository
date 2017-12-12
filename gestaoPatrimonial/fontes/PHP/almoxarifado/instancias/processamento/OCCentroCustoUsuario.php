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

$Id: OCCentroCustoUsuario.php 64005 2015-11-17 16:49:06Z michel $

* Casos de uso: uc-03.03.07
uc-03.04.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GP_ALM_NEGOCIO."RAlmoxarifadoPermissaoCentroDeCustos.class.php";
include_once CAM_GP_ALM_NEGOCIO."RAlmoxarifadoCentroDeCustos.class.php";
include_once CAM_GP_ALM_NEGOCIO."RAlmoxarifadoEstoqueItem.class.php";

$stCampoCod       = $request->get('stNomCampoCod');
$stCampoDesc      = $request->get('stIdCampoDesc');
$inCodigo         = $request->get('inCodigo');
$inCodCentroCusto = $request->get("inCodCentroCusto");

switch ($request->get('stCtrl')) {
    case 'buscaPopup':
    default:
        $boErro = true;
        $stJs = isset($stJs) ? $stJs : "";
        if ( (trim($inCodigo) != '') AND ($inCodigo != 0) ) {
            if ($request->get('usuario')==TRUE) {
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
            $stJs.="d.getElementById('".$stCampoDesc."').innerHTML='".$stDescricao."';                                                          \n";
            $stJs.="retornaValorBscInner('".$stCampoCod."','".$stCampoDesc."','".$request->get('stNomForm')."','".$stDescricao."');             \n";
            if ($stDescricao=="") {
                $stJs.="alertaAviso('@Código do Centro de Custo(".$inCodigo.") não encontrado.','form','erro','".Sessao::getId()."');           \n";
                $boErro = false;
            }

        } elseif (trim($inCodCentroCusto) != '') {
            $obRegra = new RAlmoxarifadoCentroDeCustos();
            $obRegra->setCodigo( $inCodCentroCusto );
            $obRegra->consultar();
            $stDescricao = $obRegra->getDescricao();
            $stJs.="d.getElementById('".$stCampoDesc."').innerHTML = '".$stDescricao."';                                                        \n";
            $stJs.="retornaValorBscInner('".$stCampoCod."','".$stCampoDesc."','".$request->get('stNomForm')."','".$stDescricao."');             \n";
            if ($stDescricao=="") {
                $stJs.="alertaAviso('@Código do Centro de Custo(".$inCodCentroCusto.") não encontrado.','form','erro','".Sessao::getId()."');   \n";
                $boErro = false;
            }
        } else {
            $stJs.="alertaAviso('@Código do Centro de Custo(".$inCodCentroCusto.") não encontrado.','form','erro','".Sessao::getId()."');       \n";
            $stJs.="f.".$stCampoCod.".value = '';                                                                                               \n";
            $stJs.="d.getElementById('".$stCampoDesc."').innerHTML = '&nbsp;';                                                                  \n";
            $boErro = false;
        }

        if($request->get('boLiberaFrame'))
            $stJs .= "LiberaFrames(true,true);                                                                                                  \n";

        echo $stJs;

    break;
}

?>

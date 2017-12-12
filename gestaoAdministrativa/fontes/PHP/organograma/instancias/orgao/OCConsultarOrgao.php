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
* Arquivo de instância para manutenção de orgao
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

* $Id: OCConsultarOrgao.php 59612 2014-09-02 12:00:51Z gelson $

Casos de uso: uc-01.05.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GA_ORGAN_NEGOCIO . "ROrganogramaOrgao.class.php"    );

//$obRegra = new ROrganogramaOrgao;
//$obRegra->setCodOrgao( $_REQUEST['inCodOrgao'] );
//$obRegra->obROrganograma->setCodOrganograma( $_REQUEST['inCodOrganograma'] );
//$obRegra->consultar();

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

function BuscaCGMOrgao()
{
    global $obRegra;
    $rsCGM = new RecordSet;
    if ( $obRegra->obRCgmPJ->getNumCGM () )
        $obRegra->obRCgmPJ->consultar( $rsCGM );

    $stNull = "&nbsp;";

    if ( $rsCGM->getNumLinhas() <= 0) {
        $stJs .= 'd.getElementById("stCGMOrgao").innerHTML = "'.$stNull.'";'."\n";
        $stJs .= 'd.getElementById("inTelefone").innerHTML = "'.$stNull.'";'."\n";
        $stJs .= 'd.getElementById("inRamal").innerHTML = "'.$stNull.'";'."\n";
        $stJs .= 'd.getElementById("stEndereco").innerHTML = "'.$stNull.'";'."\n";
        $stJs .= 'd.getElementById("inNumero").innerHTML = "'.$stNull.'";'."\n";
        $stJs .= 'd.getElementById("stEmailOrgao").innerHTML = "'.$stNull.'";'."\n";
    } else {
        $stJs .= 'd.getElementById("stCGMOrgao").innerHTML = "'.$rsCGM->getCampo('numcgm')." - ".trim($rsCGM->getCampo('nom_cgm')).'";'."\n";
        $stJs .= 'd.getElementById("inTelefone").innerHTML = "'.(trim($rsCGM->getCampo('fone_residencial'))?$rsCGM->getCampo('fone_residencial'):$stNull).'";'."\n";
        $stJs .= 'd.getElementById("inRamal").innerHTML = "'.(trim($rsCGM->getCampo('ramal_residencial'))?$rsCGM->getCampo('ramal_residencial'):$stNull).'";'."\n";
        $stJs .= 'd.getElementById("stEndereco").innerHTML = "'.($rsCGM->getCampo('logradouro')? ($rsCGM->getCampo('tipo_logradouro').' '.$rsCGM->getCampo('logradouro')):$stNull).'";'."\n";
        $stJs .= 'd.getElementById("inNumero").innerHTML = "'.(trim($rsCGM->getCampo('numero'))?$rsCGM->getCampo('numero'):$stNull).'";'."\n";
        $stJs .= 'd.getElementById("stEmailOrgao").innerHTML = "'.($rsCGM->getCampo('e_mail')?$rsCGM->getCampo('e_mail'):$stNull).'";'."\n";
    }

    return $stJs;
}

function BuscaCGMResponsavel()
{
    global $obRegra;
    $obRegra->obRCgmPF->consultarCGM ( $rsCGM );

    $stNull = "&nbsp;";

    if ( $rsCGM->getNumLinhas() <= 0) {
        $stJs .= 'd.getElementById("inTelefoneCelular").innerHTML = "'.$stNull.'";';
        $stJs .= 'd.getElementById("inTelefoneComercial").innerHTML = "'.$stNull.'";';
        $stJs .= 'd.getElementById("inRamalComercial").innerHTML = "'.$stNull.'";';
        $stJs .= 'd.getElementById("stEmailResponsavel").innerHTML = "'.$stNull.'";';
    } else {
        $stJs .= 'd.getElementById("stEmailResponsavel").innerHTML = "'.($rsCGM->getCampo('e_mail')?$rsCGM->getCampo('e_mail'):$stNull).'";';
        $stJs .= 'd.getElementById("inTelefoneCelular").innerHTML = "'.(trim($rsCGM->getCampo('fone_celular'))?$rsCGM->getCampo('fone_celular'):$stNull).'";';
        $stJs .= 'd.getElementById("inRamalComercial").innerHTML = "'.(trim($rsCGM->getCampo('ramal_comercial'))?$rsCGM->getCampo('ramal_comercial'):$stNull).'";';
        $stJs .= 'd.getElementById("inTelefoneComercial").innerHTML = "'.($rsCGM->getCampo('fone_comercial')?$rsCGM->getCampo('fone_comercial'):$stNull).'";';
    }

    return $stJs;
}

// Acoes por pagina
switch ($stCtrl) {
    case "BuscaCGMResponsavel":
        $stJs = BuscaCGMResponsavel();
    break;
    case "buscaCGMOrgao":
        $stJs = BuscaCGMOrgao();
    break;
    case 'preencheInner':
        //$obRegra = unserialize(sessao->transf3);
        $obRegra = Sessao::read('obRegra');
        $stJs = BuscaCGMOrgao();
        $stJs .= BuscaCGMResponsavel();
        echo $stJs;
        unset( $stJs );
    break;
}
if($stJs)
    sistemaLegado::executaFrameOculto($stJs);

?>

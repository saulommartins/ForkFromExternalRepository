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
    * Arquivo do Oculto de Consultar Saldo

    * Data de Criação: 04/07/2008

    * @author Analista: Tonismar R. Bernardo
    * @author Desenvolvedor: Henrique Girardi dos Santos

    * $Id: OCConsultarSaldo.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.04.40
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once CAM_GF_TES_NEGOCIO."RTesourariaSaldoTesouraria.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ImplantarSaldo";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";

switch ($_REQUEST["stCtrl"]) {
case 'mostraSpanContaBanco':
    if ($_REQUEST['inCodEntidade']) {
        //Define o objeto INNER para armazenar a Conta Banco
        $obBscConta = new BuscaInner;
        $obBscConta->setRotulo( "Conta" );
        $obBscConta->setTitle( "Informe a Conta" );
        $obBscConta->setNull( false );
        $obBscConta->setId( "stConta" );
        $obBscConta->setValue( '' );
        $obBscConta->obCampoCod->setName("inCodConta");
        $obBscConta->obCampoCod->setId("inCodConta");
        $obBscConta->obCampoCod->setValue( "" );
        $obBscConta->setFuncaoBusca ( "abrePopUp('".CAM_GF_CONT_POPUPS."planoConta/FLPlanoConta.php','frm','inCodConta','stConta','banco&inCodEntidade=".$_REQUEST['inCodEntidade']."','".Sessao::getId()."','800','550');" );
        $obBscConta->obCampoCod->obEvento->setOnChange("return false;");
        $obBscConta->obCampoCod->obEvento->setOnBlur("montaParametrosGET('buscaConta'); ");

        $obFormulario = new Formulario;
        $obFormulario->addComponente ( $obBscConta );

        $obFormulario->montaInnerHTML ();
        $stHTML = $obFormulario->getHTML ();

        $js = "$('spnContaBanco').innerHTML = '".$stHTML."' \n";

    } else {
        $js =  "$('spnContaBanco').innerHTML = ''; \n";
    }
    break;

case 'buscaConta':
    if ($_REQUEST['inCodEntidade'] && $_REQUEST['inCodConta']) {

        $obRegra = new RTesourariaSaldoTesouraria();
        $obRegra->obRContabilidadePlanoBanco->setCodPlano ( $_REQUEST["inCodConta"] );
        $obRegra->obRContabilidadePlanoBanco->setExercicio( Sessao::getExercicio()       );
        $obRegra->obRContabilidadePlanoBanco->obROrcamentoEntidade->setCodigoEntidade ( $_REQUEST["inCodEntidade"]);

        $obErro = $obRegra->obRContabilidadePlanoBanco->consultar();
        if (!$obErro->ocorreu()) {
            $codAgencia = $obRegra->obRContabilidadePlanoBanco->obRMONAgencia->getCodAgencia();
            if ($codAgencia <> "") {
               if ($_REQUEST['inCodEntidade'] AND ($obRegra->obRContabilidadePlanoBanco->obROrcamentoEntidade->getCodigoEntidade() <> $_REQUEST['inCodEntidade'])) {
                   $js  = "$('inCodConta').value = '';";
                   $js .= "$('stConta').innerHTML = '&nbsp;';";
                   SistemaLegado::exibeAviso(urlencode($obRegra->obRContabilidadePlanoBanco->getNomConta()." - Entidade diferente da informada"),"n_incluir","erro");
               } else {
                   $stDescricao = $obRegra->obRContabilidadePlanoBanco->getNomConta();
                   $obErro = $obRegra->consultar();
                   if (!$obErro->ocorreu()) {
                       $js .= "$('stConta').innerHTML = '".$stDescricao."';";
                   } else {
                       SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
                   }
               }
            } else {
                $js  = "$('inCodConta').value = '';";
                $js .= "$('stConta').innerHTML = '&nbsp;';";
                SistemaLegado::exibeAviso(urlencode($obRegra->obRContabilidadePlanoBanco->getCodPlano()." - Não é uma Conta de Banco"),"n_incluir","erro");
            }
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    } else {
        $js  = "$('inCodConta').value = '';";
        $js .= "$('stConta').innerHTML = '&nbsp;';";
    }

    break;

}

echo $js;
?>

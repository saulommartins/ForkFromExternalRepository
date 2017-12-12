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
    * Paginae Oculta para funcionalidade Implantar Saldo
    * Data de Criação   : 14/03/2006

    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @ignore

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2006-09-19 06:00:01 -0300 (Ter, 19 Set 2006) $

    * Casos de uso: uc-02.04.22

*/

/*
$Log$
Revision 1.6  2006/09/19 08:48:01  jose.eduardo
Bug #6993#

Revision 1.5  2006/07/05 20:39:21  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_TES_NEGOCIO."RTesourariaSaldoTesouraria.class.php"                              );

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
            $obBscConta->obCampoCod->setValue( "" );
            $obBscConta->setFuncaoBusca ( "abrePopUp('".CAM_GF_CONT_POPUPS."planoConta/FLPlanoConta.php','frm','inCodConta','stConta','banco&inCodEntidade='+document.frm.inCodigoEntidade.value,'".Sessao::getId()."','800','550');" );
            $obBscConta->obCampoCod->obEvento->setOnChange("return false;");
            $obBscConta->obCampoCod->obEvento->setOnBlur( "buscaDado('saldoContaBanco');" );

            $obFormulario = new Formulario;
            $obFormulario->addComponente ( $obBscConta );

            $obFormulario->montaInnerHTML ();
            $stHTML = $obFormulario->getHTML ();

            $stHTML = str_replace( "\n" ,"" ,$stHTML );
            $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
            $stHTML = str_replace( "  " ,"" ,$stHTML );
            $stHTML = str_replace( "'","\\'",$stHTML );
            $stHTML = str_replace( "\\\\'","\\'",$stHTML );

            $js .= "d.getElementById('spnContaBanco').innerHTML = '".$stHTML."' \n";

            SistemaLegado::executaFrameOculto("f.inCodigoEntidade.value = '".$_REQUEST['inCodEntidade']."'; \n".$js);

        } else {
            SistemaLegado::executaFrameOculto("d.getElementById('spnContaBanco').innerHTML           = ''; \n");
        }
    break;
    case 'saldoContaBanco':
        if ($_REQUEST['inCodEntidade']) {

            $obRegra = new RTesourariaSaldoTesouraria();
            $obRegra->obRContabilidadePlanoBanco->setCodPlano ( $_REQUEST["inCodConta"] );
            $obRegra->obRContabilidadePlanoBanco->setExercicio( Sessao::getExercicio()        );
            $obRegra->obRContabilidadePlanoBanco->obROrcamentoEntidade->setCodigoEntidade ( $_REQUEST["inCodEntidade"]);

            $obErro = $obRegra->obRContabilidadePlanoBanco->consultar();
            if (!$obErro->ocorreu()) {
                $codAgencia = $obRegra->obRContabilidadePlanoBanco->obRMONAgencia->getCodAgencia();
                if ($codAgencia <> "") {
                   if ($_REQUEST['inCodEntidade'] AND ($obRegra->obRContabilidadePlanoBanco->obROrcamentoEntidade->getCodigoEntidade() <> $_REQUEST['inCodEntidade'])) {
                       $js  = "f.inCodConta.value = '';";
                       $js .= "d.getElementById('stConta').innerHTML = '&nbsp;';";
                       $js .= "f.nuValor.value = '';";
                       SistemaLegado::exibeAviso(urlencode($obRegra->obRContabilidadePlanoBanco->getNomConta()." - Entidade diferente da informada"),"n_incluir","erro");
                   } else {
                       $stDescricao = $obRegra->obRContabilidadePlanoBanco->getNomConta();
                       $obErro = $obRegra->consultar();
                       if (!$obErro->ocorreu()) {
                           $js  = "f.nuValor.value = '".number_format($obRegra->getVlSaldo(), 2, ',', '.')."';";
                           $js .= "d.getElementById('stConta').innerHTML = '".$stDescricao."';";
                       } else {
                           SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
                       }
                   }
                } else {
                    $js  = "f.inCodConta.value = '';";
                    $js .= "d.getElementById('stConta').innerHTML = '&nbsp;';";
                    $js .= "f.nuValor.value = '';";
                    SistemaLegado::exibeAviso(urlencode($obRegra->obRContabilidadePlanoBanco->getCodPlano()." - Não é uma Conta de Banco"),"n_incluir","erro");
                }
            } else {
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
            }
        } else {
            $js  = "f.inCodConta.value = '';";
            $js .= "d.getElementById('stConta').innerHTML = '&nbsp;';";
            $js .= "f.nuValor.value = '';";
        }
        SistemaLegado::executaFrameOculto( $js );
    break;

}
?>

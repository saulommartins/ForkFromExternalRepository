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
    * Paginae Oculta para funcionalidade Manter Transferencia
    * Data de Criação   : 04/11/2005

    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @ignore

    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2006-10-23 13:27:48 -0300 (Seg, 23 Out 2006) $

    * Casos de uso: uc-02.04.09

*/

/*
$Log$
Revision 1.27  2006/10/23 16:27:48  domluc
Add opção para multiplos boletins

Revision 1.26  2006/09/19 08:48:54  jose.eduardo
Bug #6993#

Revision 1.25  2006/08/25 10:37:13  jose.eduardo
Bug #6652#

Revision 1.24  2006/08/08 20:01:46  jose.eduardo
Bug #6713#

Revision 1.23  2006/07/05 20:40:06  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_TES_NEGOCIO."RTesourariaBoletim.class.php" );
include_once( CAM_GF_TES_NEGOCIO."RTesourariaSaldoTesouraria.class.php" );
include_once( CAM_GF_CONT_NEGOCIO."RContabilidadePlanoBanco.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterTransferencia";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";

$obRTesourariaBoletim = new RTesourariaBoletim();

switch ($_REQUEST["stCtrl"]) {
    case 'alteraBoletim':
        $obRTesourariaBoletim = new RTesourariaBoletim();
        list( $inCodBoletim , $stDataBoletim ) = explode ( ':' , $_REQUEST['inCodBoletim'] );
        $obRTesourariaBoletim->setCodBoletim ( $inCodBoletim );
        $obRTesourariaBoletim->setExercicio( Sessao::getExercicio() );
        $obRTesourariaBoletim->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodEntidade'] );
        $obErro = $obRTesourariaBoletim->listarBoletimAberto ( $rsBoletimAberto );

        if ( !$obErro->ocorreu() && $rsBoletimAberto->getNumLinhas() == 1 ) {
            $stJs  = "f.inCodBoletim.value = '" . $rsBoletimAberto->getCampo( 'cod_boletim' ) . "';\r\n";
            $stJs .= "f.stDtBoletim.value = '" . $rsBoletimAberto->getCampo( 'dt_boletim' ) . "';\r\n";
            SistemaLegado::executaFrameOculto( "LiberaFrames(true,false);".$stJs );
        } else {
            $stJs  = "f.inCodBoletim.value = '';\r\n";
            $stJs .= "f.stDtBoletim.value = '';\r\n";
            SistemaLegado::executaFrameOculto( "LiberaFrames(true,false);".$stJs );
        }

    break;
    case 'buscaBoletim':
        if ($_REQUEST['inCodEntidade']) {
            require_once( CAM_GF_TES_COMPONENTES . 'ISelectBoletim.class.php' );
            $obISelectBoletim = new ISelectBoletim;
            $obISelectBoletim->obBoletim->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodEntidade']  );
            $obISelectBoletim->obBoletim->setExercicio( Sessao::getExercicio() );
            $obISelectBoletim->obEvento->setOnChange ( "buscaDado('alteraBoletim');");

            $obFormulario = new Formulario;
            $obFormulario->addComponente ( $obISelectBoletim );
            $obFormulario->montaInnerHtml();
            $stHTML = $obFormulario->getHTML();
            $stHTML = str_replace( "\n" ,"" ,$stHTML );
            $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
            $stHTML = str_replace( "  " ,"" ,$stHTML );
            $stHTML = str_replace( "'","\\'",$stHTML );
            $stHTML = str_replace( "\\\'","\\'",$stHTML );

            $stJs = "d.getElementById('spnBoletim').innerHTML = '".$stHTML."';\r\n";

        }
        SistemaLegado::executaFrameOculto( $stJs );
    break;
    case 'mostraSpanContas':
        if ($_REQUEST["inCodEntidade"]!="") {
            // Define Objeto BuscaInner para conta pagadora
            $obBscContaCredito = new BuscaInner;
            $obBscContaCredito->setRotulo ( "Conta a Crédito" );
            $obBscContaCredito->setTitle  ( "Informe a Conta a Crédito." );
            $obBscContaCredito->setId     ( "stNomContaCredito"  );
            $obBscContaCredito->setValue  ( ""                   );
            $obBscContaCredito->setNull   ( false         );
            $obBscContaCredito->obCampoCod->setName     ( "inCodPlanoCredito" );
            $obBscContaCredito->obCampoCod->setSize     ( 10           );
            $obBscContaCredito->obCampoCod->setNull     ( false        );
            $obBscContaCredito->obCampoCod->setMaxLength( 8            );
            $obBscContaCredito->obCampoCod->setValue    ( ""           );
            $obBscContaCredito->obCampoCod->setAlign    ( "left"       );
            $obBscContaCredito->setFuncaoBusca("abrePopUp('".CAM_GF_CONT_POPUPS."planoConta/FLPlanoConta.php','frm','inCodPlanoCredito','stNomContaCredito','tes_transf&inCodEntidade='+document.frm.inCodEntidade.value,'".Sessao::getId()."','800','550');");
            $obBscContaCredito->obCampoCod->obEvento->setOnChange("return false;");
            $obBscContaCredito->obCampoCod->obEvento->setOnBlur("BloqueiaFrames( true, false ); buscaDado('nomContaCredito');");

            $obBscContaDebito = new BuscaInner;
            $obBscContaDebito->setRotulo ( "Conta a Débito" );
            $obBscContaDebito->setTitle  ( "Informe a Conta a Crédito." );
            $obBscContaDebito->setId     ( "stNomContaDebito"  );
            $obBscContaDebito->setValue  ( ""   );
            $obBscContaDebito->setNull   ( false         );
            $obBscContaDebito->obCampoCod->setName     ( "inCodPlanoDebito" );
            $obBscContaDebito->obCampoCod->setSize     ( 10           );
            $obBscContaDebito->obCampoCod->setNull     ( false        );
            $obBscContaDebito->obCampoCod->setMaxLength( 8            );
            $obBscContaDebito->obCampoCod->setValue    ( ""  );
            $obBscContaDebito->obCampoCod->setAlign    ( "left"       );
            $obBscContaDebito->setFuncaoBusca("abrePopUp('".CAM_GF_CONT_POPUPS."planoConta/FLPlanoConta.php','frm','inCodPlanoDebito','stNomContaDebito','tes_transf&inCodEntidade='+document.frm.inCodEntidade.value,'".Sessao::getId()."','800','550');");
            $obBscContaDebito->setValoresBusca(CAM_GF_CONT_POPUPS.'planoConta/OCPlanoConta.php?'.Sessao::getId(),'frm', 'tes_transf');

            $obFormulario = new Formulario;
            $obFormulario->addComponente ($obBscContaDebito);
            $obFormulario->addComponente ($obBscContaCredito);
            $obFormulario->montaInnerHTML ();
            $stHTML = $obFormulario->getHTML ();

            $obFormulario->obJavaScript->montaJavaScript();
            $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
            $stEval = str_replace("\n","",$stEval);

            $stJs.= "f.stEval.value = f.stHdnValor.value + '$stEval'; \n";

            $stHTML = str_replace( "\n" ,"" ,$stHTML );
            $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
            $stHTML = str_replace( "  " ,"" ,$stHTML );
            $stHTML = str_replace( "'","\\'",$stHTML );
            $stHTML = str_replace( "\\\\'","\\'",$stHTML );
        } else {
            $stJs.= "f.stEval.value = f.stHdnValor.value;";
            $stHTML = "";
        }
        $stJs .= "d.getElementById('spnContas').innerHTML = '".$stHTML."';\r\n";
        $stJs .= "setTimeout(\"window.parent.frames['telaPrincipal'].buscaDado('buscaBoletim')\" , 600 );";
        SistemaLegado::executaFrameOculto( $stJs );

    break;

    case 'nomContaCredito':
        $stJs  = "LiberaFrames( true, false );";
        if ( $_REQUEST['inCodPlanoCredito'] && ($_REQUEST['inCodPlanoCredito'] != $_REQUEST['inCodPlanoDebito']) ) {
            $obRContabilidadePlanoContaAnalitica = new RContabilidadePlanoContaAnalitica();
            $obRContabilidadePlanoContaAnalitica->setExercicio( Sessao::getExercicio() );
            $obRContabilidadePlanoContaAnalitica->setCodPlano ( $_REQUEST['inCodPlanoCredito'] );
            $obRContabilidadePlanoContaAnalitica->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodEntidade'] );
            $obRContabilidadePlanoContaAnalitica->listarPlanoContaTransferencia($rsContaCredito);
            if ( $rsContaCredito->getCampo("cod_entidade") ) {
                if ( $rsContaCredito->getCampo("cod_entidade") == $_REQUEST['inCodEntidade'] ) {
                    $stDescricao = $rsContaCredito->getCampo("nom_conta");
                } else {
                    $stDescricao = '';
                }
            } else {
                $stDescricao = $rsContaCredito->getCampo("nom_conta");
            }
            if (!$stDescricao) {
                $stJs .= "alertaAviso('Conta inválida.(".$_REQUEST['inCodPlanoCredito'].")', '', 'erro','".Sessao::getId()."' );";
                $stJs .= "f.inCodPlanoCredito.value='';";
                $_REQUEST['inCodPlanoCredito'] = null;
            }
            $stDescricao = ( $stDescricao ) ? $stDescricao : '&nbsp;';
            $stJs .= "d.getElementById('stNomContaCredito').innerHTML='".$stDescricao."';";
            $stJs .= "f.stNomContaCredito.value='".$stDescricao."';";

            $obRTesourariaSaldoTesouraria = new RTesourariaSaldoTesouraria();
            $obRTesourariaSaldoTesouraria->obRContabilidadePlanoBanco->setExercicio( Sessao::getExercicio() );
            $obRTesourariaSaldoTesouraria->obRContabilidadePlanoBanco->setCodPlano ( $_REQUEST['inCodPlanoCredito'] );
            $obRTesourariaSaldoTesouraria->obRContabilidadePlanoBanco->consultar();
            if ( $obRTesourariaSaldoTesouraria->obRContabilidadePlanoBanco->obRMONAgencia->getCodAgencia() ) {

                $obRTesourariaSaldoTesouraria->consultarSaldoTesouraria( $nuVlSaldoContaBanco );
                $stJs .= "f.nuSaldoContaBanco.value   = '".$nuVlSaldoContaBanco."'; \n";
                $stJs .= "f.nuSaldoContaBancoBR.value = '".number_format($nuVlSaldoContaBanco,"2",",",".")."'; \n";
            }
            $stJs .= "f.nuValor.focus(); \n";
        } elseif ($_REQUEST['inCodPlanoCredito'] == $_REQUEST['inCodPlanoDebito']) {
            $stJs .= "f.inCodPlanoCredito.value = '';";
            $stJs .= "d.getElementById('stNomContaCredito').innerHTML = '&nbsp;';";
            $stJs .= "alertaAviso('A Conta Crédito deve ser diferente da Conta Débito! (".$_REQUEST['inCodPlanoDebito'].")', '', 'erro','".Sessao::getId()."' );";
        }
        SistemaLegado::executaFrameOculto( $stJs );
    break;
}
?>

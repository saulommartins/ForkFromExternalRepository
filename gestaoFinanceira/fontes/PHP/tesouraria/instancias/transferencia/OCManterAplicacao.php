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
    * Página de Formulário para efetuar Depósitos/Retiradas
    * Data de Criação   : 31/08/2006
    *
    * @author Analista: Cleisson Barboza
    * @author Desenvolvedor: Anderson C. Konze

    * @ignore

    $Revision: 30835 $
    $Name$
    $Author: luciano $
    $Date: 2007-09-17 12:08:28 -0300 (Seg, 17 Set 2007) $

    * Casos de uso: uc-02.04.28

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GF_CONT_COMPONENTES."IPopUpContaAnalitica.class.php";
include_once CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeUsuario.class.php";

switch ($_REQUEST['stCtrl']) {
    case 'alteraBoletim':
        require_once ( CAM_GF_TES_NEGOCIO . 'RTesourariaBoletim.class.php' );
        $obRTesourariaBoletim = new RTesourariaBoletim();
        list( $inCodBoletim , $stDataBoletim ) = explode ( ':' , $_REQUEST['inCodBoletim'] );
        $obRTesourariaBoletim->setCodBoletim ( $inCodBoletim );
        $obRTesourariaBoletim->setExercicio( Sessao::getExercicio() );
        $obRTesourariaBoletim->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodEntidade'] );
        $obErro = $obRTesourariaBoletim->listarBoletimAberto ( $rsBoletimAberto );

        if ( !$obErro->ocorreu() && $rsBoletimAberto->getNumLinhas() == 1 ) {
            $stJs  = "f.inCodBoletim.value = '" . $rsBoletimAberto->getCampo( 'cod_boletim' ) . "';\r\n";
            $stJs .= "f.stDtBoletim.value = '" . $rsBoletimAberto->getCampo( 'dt_boletim' ) . "';\r\n";
            //SistemaLegado::executaFrameOculto( "LiberaFrames(true,false);".$stJs );
        } else {
            $stJs  = "f.inCodBoletim.value = '';\r\n";
            $stJs .= "f.stDtBoletim.value = '';\r\n";
            //SistemaLegado::executaFrameOculto( "LiberaFrames(true,false);".$stJs );
        }
        $stJs .= "montaParametrosGET( 'verificaContas','inCodPlanoCredito,inCodPlanoDebito,inCodEntidade,stDtBoletim'); \r\n";
        //exit;
    break;
    case 'buscaBoletim':
        if ($_REQUEST['inCodEntidade']) {
            require_once( CAM_GF_TES_COMPONENTES . 'ISelectBoletim.class.php' );
            $obISelectBoletim = new ISelectBoletim;
            $obISelectBoletim->obBoletim->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodEntidade']  );
            $obISelectBoletim->obBoletim->setExercicio( Sessao::getExercicio() );
            //$obISelectBoletim->obEvento->setOnChange ( "montaParametrosGET('alteraBoletim', 'inCodEntidade,inCodBoletim');");

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

            if ($_REQUEST['HdnCodBoletim']) {
                  $stJs .= "d.getElementById('inCodBoletim').value = '".$_REQUEST['HdnCodBoletim']."'; ";
                  $stJs .= "d.getElementById('HdnCodBoletim').value = ''; ";
            }

        }

        SistemaLegado::executaFrameOculto( $stJs );
        exit;
    break;
    case 'montaSpanContas':

                $obCmbEntidades = Sessao::read('obIEntidade');

                // Define Objeto BuscaInner da conta para aplicação
                $obBscContaDebito = new IPopUpContaAnalitica( $obCmbEntidades->obSelect);
                $obBscContaDebito->setRotulo                      ( "Conta Aplicação" );
                $obBscContaDebito->setTitle                       ( "Informe a conta entrada." );
                $obBscContaDebito->setId                          ( "stNomContaDebito" );
                $obBscContaDebito->obCampoCod->setName            ( "inCodPlanoDebito" );
                $obBscContaDebito->obCampoCod->setId              ( "inCodPlanoDebito" );
                $obBscContaDebito->setNull                        ( false );
                $obBscContaDebito->setTipoBusca                   ( "tes_aplicacao_entrada" );
                $obBscContaDebito->obCampoCod->obEvento->setOnBlur( " montaParametrosGET( 'verificaContas','inCodPlanoCredito,inCodPlanoDebito,inCodEntidade,stDtBoletim');");

                // Define Objeto BuscaInner da conta para contrapartida
                $obBscContaCredito = new IPopUpContaAnalitica( $obCmbEntidades->obSelect );
                $obBscContaCredito->setRotulo                      ( "Contrapartida"    );
                $obBscContaCredito->setTitle                       ( "Informe a conta saída." );
                $obBscContaCredito->setId                          ( "stNomContaCredito" );
                $obBscContaCredito->obCampoCod->setName            ( "inCodPlanoCredito" );
                $obBscContaCredito->obCampoCod->setId              ( "inCodPlanoCredito" );
                $obBscContaCredito->setNull                        ( false );
                $obBscContaCredito->setTipoBusca                   ( "tes_aplicacao_contrapartida"    );
                $obBscContaCredito->obCampoCod->obEvento->setOnBlur( " montaParametrosGET( 'verificaContas','inCodPlanoCredito,inCodPlanoDebito,inCodEntidade,stDtBoletim');");

                $obFormulario = new Formulario;
                $obFormulario->addComponente ( $obBscContaDebito  );
                $obFormulario->addComponente ( $obBscContaCredito );

                $obFormulario->montaInnerHTML ();
                $stHTML = $obFormulario->getHTML ();

                $stHTML = str_replace( "\n" ,"" ,$stHTML );
                $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
                $stHTML = str_replace( "  " ,"" ,$stHTML );
                $stHTML = str_replace( "'","\\'",$stHTML );
                $stHTML = str_replace( "\\\\'","\\'",$stHTML );

                $stJs  = "d.getElementById('spnContas').innerHTML = '".$stHTML."';\n";
                $stJs .= "d.getElementById('inCodPlanoDebito').focus();\n";
    break;

    case 'verificaContas':
            if ( ($_GET['inCodPlanoCredito']) && ($_GET['inCodPlanoCredito'] != $_GET['inCodPlanoDebito']) ) {
                include_once( CAM_GF_TES_MAPEAMENTO."TTesourariaTransferencia.class.php" );
                $obTTesourariaTransferencia = new TTesourariaTransferencia();
                $obTTesourariaTransferencia->setDado("stExercicio",Sessao::getExercicio() );
                $obTTesourariaTransferencia->setDado("inCodPlano",$_GET['inCodPlanoCredito']);
                $obTTesourariaTransferencia->setDado("stDtBoletim",$_GET['stDtBoletim']);
                $obTTesourariaTransferencia->verificaSaldoContaAnalitica($nuVlSaldoContaAnalitica);

                $stJs .= "d.getElementById('nuSaldoContaAnalitica').value   = '".$nuVlSaldoContaAnalitica."'; \n";
                $stJs .= "d.getElementById('nuSaldoContaAnaliticaBR').value = '".number_format($nuVlSaldoContaAnalitica,"2",",",".")."'; \n";
            }
            if ( $_GET['inCodPlanoCredito']  && ( $_GET['inCodPlanoCredito'] == $_GET['inCodPlanoDebito']) ) {
                $stJs .= "alertaAviso('A conta de contrapartida não pode ser a mesma de aplicação! (".$_GET['inCodPlanoCredito'].")', '', 'erro','".Sessao::getId()."' );";
                $_erro++;
            }
            if ($_erro) $stJs .= "d.getElementById('Ok').disabled = true;\n ";
            else        $stJs .= "d.getElementById('Ok').disabled = false; \n";
    break;

    case 'buscaEmpenho':
        include_once CAM_GF_EMP_NEGOCIO.'REmpenhoEmpenho.class.php';
        $obREmpenhoEmpenho = new REmpenhoEmpenho;

        $obREmpenhoEmpenho->obROrcamentoEntidade->setCodigoEntidade ( TRIM($_REQUEST["inCodEntidade"]) );
        $obREmpenhoEmpenho->setExercicio                            ( $_REQUEST["stExercicioEmpenho"]  );
        if ($_REQUEST["inCodigoEmpenho"] != '') {
            $obREmpenhoEmpenho->setCodEmpenhoInicial                    ( $_REQUEST["inCodigoEmpenho"]     );
            $obREmpenhoEmpenho->setCodEmpenhoFinal                      ( $_REQUEST["inCodigoEmpenho"]     );    
            $obREmpenhoEmpenho->listarEmpenhosPopUp($rsLista);
            
            if ( $rsLista->getNumLinhas() > 0 ){
                $stJs .= " jQuery('#inCodigoEmpenho').val('".$rsLista->getCampo('cod_empenho')."'); ";
                $stJs .= " jQuery('#stDescEmpenho').html('".$rsLista->getCampo('nom_fornecedor')."'); ";
            }else{
                $stJs .= " jQuery('#inCodigoEmpenho').val(''); ";
                $stJs .= " jQuery('#stDescEmpenho').html('&nbsp;'); ";
                $stJs .= " alertaAviso('Empenho informado não existe.','frm','erro','".Sessao::getId()."'); \n";
            }
        }else{
            $stJs .= " jQuery('#inCodigoEmpenho').val(''); ";
            $stJs .= " jQuery('#stDescEmpenho').html('&nbsp;'); ";
        }
    break;

    case 'liberaCampoEmpenho':
        if ( ($_REQUEST["inCodEntidade"] != '') && ($_REQUEST["stExercicioEmpenho"] != '') ){
            $stJs .= " jQuery('#inCodigoEmpenho').removeProp('disabled'); ";
            $stJs .= " jQuery('#stExercicioEmpenho').change(function(){
                            jQuery('#inCodigoEmpenho').val('');
                            jQuery('#stDescEmpenho').html('&nbsp;');                                
                        });";
            $stJs .= " jQuery('#stLinkBusca').show(); ";
        }
        if ( ($_REQUEST["inCodEntidade"] == '') || ($_REQUEST["stExercicioEmpenho"] == '') ){
            $stJs .= "  jQuery('#inCodigoEmpenho').val(''); "; 
            $stJs .= "  jQuery('#stDescEmpenho').html('&nbsp;'); ";
            $stJs .= "  jQuery('#inCodigoEmpenho').prop('disabled',true); ";
            $stJs .= "  jQuery('#stLinkBusca').hide(); ";
        }
    break;
}

if ($stJs) {
    echo $stJs;
}
?>

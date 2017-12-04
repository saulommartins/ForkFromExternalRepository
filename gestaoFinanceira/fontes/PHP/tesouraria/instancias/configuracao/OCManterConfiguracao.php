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
    * Paginae Oculta para funcionalidade Manter configuração
    * Data de Criação   : 01/09/2005

    * @author Analista: Lucas Leusin
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Revision: 31054 $
    $Name$
    $Autor:$
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.04.01

*/

/*
$Log$
Revision 1.8  2006/07/05 20:39:21  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once(CAM_GF_TES_NEGOCIO."RTesourariaConfiguracao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

function montaLista($arRecordSet , $boExecuta = true)
{
        for ( $x = 0; $x < count( $arRecordSet ); $x++ ) {
            if ($arRecordSet[$x]['situacao'] == 't') {
                $arRecordSet[$x]['situacao_link'] = "<a href='javascript:mudaStatus(\"".$arRecordSet[$x]['id_assinatura']."\", \"f\")'>Ativo</a>";
            } else {
                $arRecordSet[$x]['situacao_link'] = "<a href='javascript:mudaStatus(\"".$arRecordSet[$x]['id_assinatura']."\", \"t\")'>Inativo</a>";
            }
        }

        $rsLista = new RecordSet;
        $rsLista->preenche( $arRecordSet );
        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setRecordSet( $rsLista );
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Nome");
        $obLista->ultimoCabecalho->setWidth( 45 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Cargo");
        $obLista->ultimoCabecalho->setWidth( 32 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Status");
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 8 );
        $obLista->commitCabecalho();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "nom_cgm" );
        $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "cargo" );
        $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "situacao_link" );
        $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
        $obLista->commitDado();
        $obLista->addAcao();
//        $obLista->ultimaAcao->setAcao( "ALTERAR" );
//        $obLista->ultimaAcao->setFuncao( true );
//        $obLista->ultimaAcao->setLink( "JavaScript:alterarAssinatura();" );
        $obLista->ultimaAcao->addCampo("1","id_assinatura" );
        $obLista->ultimaAcao->addCampo("2","numcgm");
        $obLista->ultimaAcao->addCampo("3","nom_cgm");
        $obLista->ultimaAcao->addCampo("4","cargo");
        $obLista->ultimaAcao->addCampo("5","situacao");
        $obLista->ultimaAcao->addCampo("6","cod_entidade");
        $obLista->commitAcao();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->setLink( "JavaScript:excluirAssinatura();" );
        $obLista->ultimaAcao->addCampo("1","numcgm");
        $obLista->commitAcao();

        $obLista->montaHTML();
        $stHTML = $obLista->getHTML();
        $stHTML = str_replace( "\n" ,"" ,$stHTML );
        $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
        $stHTML = str_replace( "  " ,"" ,$stHTML );
        $stHTML = str_replace( "'","\\'",$stHTML );

        if ($boExecuta) {
            SistemaLegado::executaFrameOculto("d.getElementById('spnLista').innerHTML = '".$stHTML."';");
        } else {
            return $stHTML;
        }
}

switch ($stCtrl) {
    case 'montaListaAssinatura':
        if ($_REQUEST['inCodEntidade']) {
            $obRTesourariaConfiguracao = new RTesourariaConfiguracao();
            $obRTesourariaConfiguracao->setExercicio( Sessao::getExercicio() );

            $arAssinatura = array();
            $inCount = 0;
            $obRTesourariaConfiguracao->addAssinatura();
            $obRTesourariaConfiguracao->roUltimaAssinatura->obROrcamentoEntidade->setCodigoEntidade($_REQUEST['inCodEntidade']);
            $obRTesourariaConfiguracao->consultarAssinatura();
            $arRTesourariaAssinatura = $obRTesourariaConfiguracao->getAssinatura();
            if (count( $arRTesourariaAssinatura)) {
                foreach ($arRTesourariaAssinatura as $obRTesourariaAssinatura) {
                    if ( $obRTesourariaAssinatura->getTipo() == "BO" ) {
                        $arAssinatura[$inCount]['id_assinatura' ] = $inCount;
                        $arAssinatura[$inCount]['numcgm'        ] = $obRTesourariaAssinatura->obRCGM->getNumCGM();
                        $arAssinatura[$inCount]['nom_cgm'       ] = $obRTesourariaAssinatura->obRCGM->getNomCGM();
                        $arAssinatura[$inCount]['cargo'         ] = $obRTesourariaAssinatura->getCargo();
                        $arAssinatura[$inCount]['situacao'      ] = $obRTesourariaAssinatura->getSituacao();
                        $arAssinatura[$inCount]['cod_entidade'  ] = $obRTesourariaAssinatura->obROrcamentoEntidade->getCodigoEntidade();
                        $inCount++;
                    }
                }
            }
            if ($arAssinatura[0]['numcgm']) {
                Sessao::write('arAssinatura', $arAssinatura);
                $stHTML = montaLista(Sessao::read('arAssinatura'));
            } else {
                $stHTML = "";
            }
        } else {
            $stHTML = "";
        }
        $stJs = "d.getElementById('spnLista').innerHTML = '".$stHTML."';";

    case 'montarReiniciarNumeracao':
        if ($_REQUEST['inNumeracaoComprovacao'] == 2) {
            // Define objeto Select para tipo de númeração de comprovação
            $obCmbReiniciarNumeracao = new Select;
            $obCmbReiniciarNumeracao->setRotulo    ( "Reiniciar Numeração"          );
            $obCmbReiniciarNumeracao->setName      ( "boReiniciarNumeracao"         );
            $obCmbReiniciarNumeracao->addOption    ( "true" ,"Sim"                  );
            $obCmbReiniciarNumeracao->addOption    ( "false","Não"                  );
            $obCmbReiniciarNumeracao->setStyle     ( "width: 120px"                 );
            $obCmbReiniciarNumeracao->setValue     ( $_GET['boReiniciaComprovacao'] );
            $obCmbReiniciarNumeracao->setNull      ( false                          );
            $obCmbReiniciarNumeracao->montaHtml();
            $stHtml = $obCmbReiniciarNumeracao->getHTML();

            $obFormulario = new Formulario;
            $obFormulario->addComponente( $obCmbReiniciarNumeracao );
            $obFormulario->montaInnerHTML();
            $stHtml = $obFormulario->getHTML();

        } else $stHtml = "";

        $stJs .= "parent.frames['telaPrincipal'].document.getElementById( 'spnResetaNumeracao' ).innerHTML = '".$stHtml."';";
        SistemaLegado::executaFrameOculto( $stJs );
    break;

    case 'mudaStatus':
        $arAssinatura = Sessao::read('arAssinatura');
        $arAssinatura[$_GET['inIdAssinatura']]['situacao'] = $_GET['boNovoStatus'];
        Sessao::write('arAssinatura',$arAssinatura);
        montaLista( Sessao::read('arAssinatura'));
    break;

    case 'alterarAssinatura':
        $boErro = false;
        $arAssinatura = Sessao::read('arAssinatura');
        foreach ($arAssinatura as $id => $arAssinatura) {
            if( $_POST['inNumCgm'] == $arAssinatura['numcgm'] )
                if( $_POST['inIdAssinatura'] != $id )
                    $boErro = true;
        }

        if (!$boErro) {
            $arAssinatura[$_POST['inIdAssinatura']]['id_assinatura'] = $_POST['inIdAssinatura'];
            $arAssinatura[$_POST['inIdAssinatura']]['numcgm'   ]     = $_POST['inNumCgm'  ];
            $arAssinatura[$_POST['inIdAssinatura']]['nom_cgm'  ]     = $_POST['stNomCgm'  ];
            $arAssinatura[$_POST['inIdAssinatura']]['cargo'    ]     = $_POST['stCargo'   ];
            $arAssinatura[$_POST['inIdAssinatura']]['situacao' ]     = $_POST['boSituacao'];
            $arAssinatura[$_POST['inIdAssinatura']]['cod_entidade']  = $_POST['inCodEntidade'];

            Sessao::write('arAssinatura',$arAssinatura);
            $stHTML = montaLista( Sessao::read('arAssinatura') );
        } else SistemaLegado::executaFrameOculto( "alertaAviso( 'O CGM escolhido já está na lista!','form','erro','".Sessao::getId()."' );" );

    break;

    case 'incluirAssinatura':
        $boErro = false;
        $arAssinatura = Sessao::read('arAssinatura');

        if ( count($arAssinatura) > 0 ) {
            foreach ($arAssinatura as $arTemp) {
                if ($_POST['inNumCgm'] == $arTemp['numcgm'] and $_POST['inCodEntidade'] == $arTemp['cod_entidade']) {
                    $boErro = true;
                }
            }
        }

        if (!$boErro) {
            $inCount = sizeof($arAssinatura);
            if ($inCount<3) {
                $arAssinatura[$inCount]['id_assinatura'] = $inCount;
                $arAssinatura[$inCount]['numcgm'   ] = $_POST['inNumCgm'  ];
                $arAssinatura[$inCount]['nom_cgm'  ] = $_POST['stNomCgm'  ];
                $arAssinatura[$inCount]['cargo'    ] = $_POST['stCargo'   ];
                $arAssinatura[$inCount]['situacao' ] = $_POST['boSituacao'];
                $arAssinatura[$inCount]['cod_entidade' ] = $_POST['inCodEntidade'];
                Sessao::write('arAssinatura',$arAssinatura);
                $stHTML = montaLista( Sessao::read('arAssinatura'));
            } else {
                SistemaLegado::executaFrameOculto( "alertaAviso( 'O número máximo de Assinaturas permitidas é 3!','form','erro','".Sessao::getId()."' );" );
            }

        } else SistemaLegado::executaFrameOculto( "alertaAviso( 'O CGM escolhido já está na lista!','form','erro','".Sessao::getId()."' );" );
    break;

    case 'excluirAssinatura':
        $arArray = array();
        $inCount = 0;
        $arAssinatura = Sessao::read('arAssinatura');
        foreach ($arAssinatura as $arTemp) {
            if ( ($arTemp['numcgm'] ) != $_GET['inNumCgm'] ) {
                $arArray[$inCount]['id_assinatura'] = $inCount;
                $arArray[$inCount]['numcgm'       ] = $arTemp['numcgm'         ];
                $arArray[$inCount]['nom_cgm'      ] = $arTemp['nom_cgm'        ];
                $arArray[$inCount]['cargo'        ] = $arTemp['cargo'          ];
                $arArray[$inCount]['situacao'     ] = $arTemp['situacao'       ];
                $arArray[$inCount]['cod_entidade' ] = $arTemp['cod_entidade'   ];
                $inCount++;
            }
        }

        Sessao::write('arAssinatura', $arArray);
        montaLista( Sessao::read('arAssinatura'));
    break;
}
?>

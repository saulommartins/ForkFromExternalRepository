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
    * Página de processamento oculto para o Parcelamento de Créditos
    * Data de Criação: 12/05/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho

    * $Id: OCParcelarCreditos.php 63839 2015-10-22 18:08:07Z franver $

* Casos de uso: uc-05.03.20
*/

/*
$Log$
Revision 1.3  2006/09/15 11:16:00  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_ADM_NEGOCIO."RUsuario.class.php"       );
include_once ( CAM_GT_ARR_NEGOCIO."RARRGrupo.class.php"  );

$obRARRGrupo = new RARRGrupo;
$inCodModulo = $obRARRGrupo->getCodModulo() ;
$stNull = '&nbsp;';

function montaParcelas($numParcelas, $primeiroVencimento, $valorParcelas)
{
        $cont = 0;
        $arrParcelas = array();
        while ($cont < $numParcelas) {

            if ($cont == 0) {
                if ($valorParcelas*$numParcelas != $_REQUEST['flTotalApurado']) {
                    $arrParcelas[$cont]['valor']         = $valorParcelas - ( $valorParcelas*$numParcelas-($_REQUEST['flTotalApurado']) );
                } else {
                    $arrParcelas[$cont]['valor']             = $valorParcelas ;
                }
            } else {
                $arrParcelas[$cont]['valor']             = $valorParcelas ;
            }

            $arrParcelas[$cont]['nr_parcela']    = $cont+1;
            $arrParcelas[$cont]['vencimento']   = $primeiroVencimento;

            $arrVencimento = explode ('/', $primeiroVencimento);
            $nextmonth = mktime ( 0, 0, 0, $arrVencimento[1]+1, $arrVencimento[0],  $arrVencimento[2]);
            $primeiroVencimento = strftime("%d/%m/%Y", $nextmonth);
            $cont++;
        }

        $rsListaParcelas = new RecordSet;
        $rsListaParcelas->preenche ( $arrParcelas);
        $rsListaParcelas->addFormatacao( "valor"   , "NUMERIC_BR" );

        $obLista = new Lista;
        $obLista->setRecordSet            (   $rsListaParcelas   );
        $obLista->setTitulo                    ( "Lista de Novas Parcelas"  );
        $obLista->setMostraPaginacao           ( false                  );

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;"               );
        $obLista->ultimoCabecalho->setWidth    ( 5                      );
        $obLista->commitCabecalho ();
        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "Parcela" );
        $obLista->ultimoCabecalho->setWidth         ( 10 );
        $obLista->commitCabecalho ();
        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "Vencimento" );
        $obLista->ultimoCabecalho->setWidth         ( 20 );
        $obLista->commitCabecalho ();
        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "Valor (R$)"            );
        $obLista->ultimoCabecalho->setWidth    ( 20                      );
        $obLista->commitCabecalho ();

        $obLista->addDado         ();
        $obLista->ultimoDado->setCampo         ( "nr_parcela" );
        $obLista->ultimoDado->setAlinhamento ("CENTRO");
        $obLista->commitDado    ();

        $obTxtV = new TextBox;
        $obTxtV->setName ('Ven');
        $obTxtV->setValue ( '[vencimento]' );
        $obLista->addDadoComponente                 ( $obTxtV        );
        $obLista->ultimoDado->setAlinhamento     ( 'CENTRO'                    );
        $obLista->ultimoDado->setCampo              ( "vencimento"             );
        $obLista->commitDadoComponente           (                                   );

        $obLista->addDado         ();
        $obLista->ultimoDado->setCampo         ( "valor" );
        $obLista->ultimoDado->setAlinhamento ("CENTRO");
        $obLista->commitDado    ();

        $obLista->montaHTML                    (                        );
        $stHTML =  $obLista->getHtml       (                        );
        $stHTML = str_replace                  ( "\n","",$stHTML        );
        $stHTML = str_replace                  ( "  ","",$stHTML        );
        $stHTML = str_replace                  ( "'","\\'",$stHTML      );

    $js .= "d.getElementById('obSpanNovasParcelas').innerHTML = '".$stHTML ."';\n";

    sistemaLegado::executaFrameOculto($js);
}

//echo 'ACAO: ' . $_REQUEST["stCtrl"].'<br>';

switch ($_REQUEST ["stCtrl"]) {
    case "limpar":
        Sessao::write( 'creditos', array() );
        Sessao::write( 'acrescimos', array() );
        break;

    case "buscaCredito":
        $arValores = explode('.',$_REQUEST["inCodCredito"]);
        // array [0]> cod_credito [1]> cod_especie [2]> cod_genero [3]> cod_natureza
        $obRARRGrupo->obRMONCredito->setCodCredito  ($arValores[0]);
        $obRARRGrupo->obRMONCredito->setCodEspecie  ($arValores[1]);
        $obRARRGrupo->obRMONCredito->setCodGenero   ($arValores[2]);
        $obRARRGrupo->obRMONCredito->setCodNatureza ($arValores[3]);
        // VERIFICAR PERMISSAO
        //$obRARRGrupo->obRMONCredito->consultarCreditoPermissao();
        $obRARRGrupo->obRMONCredito->consultarCredito();

        $inCodCredito = $obRARRGrupo->obRMONCredito->getCodCredito();
        $stDescricao = $obRARRGrupo->obRMONCredito->getDescricao() ;

        if ( !empty($stDescricao) ) {
            $stJs .= "d.getElementById('stCredito').innerHTML = '".$stDescricao."';\n";
            $stJs .= "f.inCodigoCredito.value ='".$inCodCredito."';\n";
            //$stJs .= "d.getElementById('inCodigoCredito').innerHTML = '".$inCodCredito."';\n";
            if ( $stAcao == 'incluir')
                $stJs .= "d.getElementById('stTipoCalculo').checked = true;\n";
        } else {
            $stJs .= "f.inCodCredito.value ='';\n";
            $stJs .= "f.inCodCredito.focus();\n";
            $stJs .= "d.getElementById('stCredito').innerHTML = '&nbsp;';\n";
            $stJs .= "alertaAviso('@Crédito informado nao existe. (".$_REQUEST["inCodCredito"].")','form','erro','".Sessao::getId()."');";
        }

    break;

    case "buscaGrupo":
        $obRARRGrupo->setCodGrupo($_REQUEST["inCodGrupo"]);
        $obRARRGrupo->consultarGrupo();

        $inCodGrupo     = $obRARRGrupo->getCodGrupo () ;
        $stDescricao    = $obRARRGrupo->getDescricao() ;
        $inCodModulo    = $obRARRGrupo->getCodModulo() ;
        $stExercicio    = $obRARRGrupo->getExercicio() ;
        if ( !empty($stDescricao) ) {
            $stJs .= "d.getElementById('stGrupo').innerHTML = '".$stDescricao." / ".$stExercicio."';\n";
            $stJs .= "d.getElementById('spnEmissao').innerHTML = '';\n";
            $stJs .= "f.inCodModulo.value = '".$inCodModulo."';\n";
            $stJs .= "d.getElementById('stTipoEmissao').checked = false;\n";
            $stJs .= "f.inCodGrupo.focus();\n";
        } else {
            $stJs .= "f.inCodGrupo.value ='';\n";
            $stJs .= "f.inCodGrupo.focus();\n";
            $stJs .= "d.getElementById('stGrupo').innerHTML = '&nbsp;';\n";
            $stJs .= "alertaAviso('@Grupo informado nao existe. (".$_REQUEST["inCodGrupo"].")','form','erro','".Sessao::getId()."');";
        }
    break;
    case "buscaIE":
        if ($_REQUEST["inInscricaoEconomica"]) {
            include_once(CAM_GT_CEM_NEGOCIO."RCEMInscricaoEconomica.class.php");
            $obRCEMInscricaoEconomica = new RCEMInscricaoEconomica;
            $obRCEMInscricaoEconomica->setInscricaoEconomica($_REQUEST["inInscricaoEconomica"]);
            $obRCEMInscricaoEconomica->consultarNomeInscricaoEconomica($rsInscricao);
            if ( !$rsInscricao->eof()) {
                $js .= "f.inInscricaoEconomica.value = '".$_REQUEST["inInscricaoEconomica"]."';\n";
                $js .= "d.getElementById('stInscricaoEconomica').innerHTML= '".$rsInscricao->getCampo("nom_cgm")."' ;\n";
            } else {
                $stMsg = "Inscrição Econômica ".$_REQUEST["inInscricaoEconomica"]."  não encontrada!";
                $js = "alertaAviso('@".$stMsg."','form','erro','".Sessao::getId()."');";
                $js .= "d.getElementById('stInscricaoEconomica').innerHTML= '&nbsp;';\n";
                $js .= "f.inInscricaoEconomica.value = '".$null ."';\n";
            }
        } else {
            $js .= "d.getElementById('stInscricaoEconomica').innerHTML= '&nbsp;';\n";
        }
        SistemaLegado::executaFrameOculto($js);
    break;

    case "procuraImovel":
        include_once ( CAM_GT_CIM_NEGOCIO."RCIMUnidadeAutonoma.class.php"       );
        $obRCIMUnidadeAutonoma = new RCIMUnidadeAutonoma( new RCIMImovel( new RCIMLote) );
        $stJs = "";
        $stNull = "&nbsp;";
        if ($_REQUEST["inInscricaoImobiliaria"]) {
            $obRCIMUnidadeAutonoma->roRCIMImovel->setNumeroInscricao( $_REQUEST["inInscricaoImobiliaria"] );
            $obRCIMUnidadeAutonoma->roRCIMImovel->listarImoveisConsulta( $rsImoveis );

            if ( $rsImoveis->eof() ) {
                //nao encontrada
                $stJs .= 'f.inInscricaoImobiliaria.value = "";';
                $stJs .= 'f.inInscricaoImobiliaria.focus();';
                $stJs .= 'd.getElementById("stInscricaoInscricaoImobiliaria").innerHTML = "'.$stNull.'";';
                $stJs .= "alertaAviso('@Código de inscrição imobiliária inválido. (".$_REQUEST['inInscricaoImobiliaria'].")','form','erro','".Sessao::getId()."');";
            } else {
                $stJs .= 'd.getElementById("stInscricaoInscricaoImobiliaria").innerHTML = "'.$rsImoveis->getCampo("endereco").'";';
            }
        } else {
            $stJs .= 'd.getElementById("stInscricaoInscricaoImobiliaria").innerHTML = "'.$stNull.'";';
        }

        SistemaLegado::executaFrameOculto( $stJs );
        break;

    case "buscaContribuinte":
        $obRCGM = new RCGM;
        if ($_REQUEST[ 'inCodContribuinte' ] != "") {
            $obRCGM->setNumCGM( $_REQUEST['inCodContribuinte'] );
            $obRCGM->consultar( $rsCGM );
            $stNull = "&nbsp;";
            if ( $rsCGM->getNumLinhas() <= 0) {
                $stJs .= 'f.inCodContribuinte.value = "";';
                $stJs .= 'f.inCodContribuinte.focus();';
                //$stJs .= 'd.getElementById("innerCGM").innerHTML = "'.$stNull.'";';
                $stJs .= 'd.getElementById("stContribuinte").innerHTML = "'.$stNull.'";';
                $stJs .= "SistemaLegado::alertaAviso('@Valor inválido. (".$_REQUEST['inCodContribuinte'].")','form','erro','".Sessao::getId()."');";
            } else {
                $stJs .= 'd.getElementById("stContribuinte").innerHTML = "'.($rsCGM->getCampo('nom_cgm')?$rsCGM->getCampo('nom_cgm'):$stNull).'";';
                $stJs .= 'f.stNomeContribuinte.value = "'. $rsCGM->getCampo('nom_cgm') .'";';
            }
        } else {
            $stJs .= 'f.inCodContribuinte.value = "'. $stNull .'";';
            $stJs .= 'f.stNomeContribuinte.value = "'. $stNull.'";';
            $stJs .= 'd.getElementById("stContribuinte").innerHTML = "'.$stNull.'";';
        }
        SistemaLegado::executaFrameOculto($stJs);
    break;

    case "CalculaValorParcelas":

        if ( ( $_REQUEST['inNumParcelas'] < 1 ) || ($_REQUEST['inNumParcelas'] > 12 )) {
            $stJs .= "alertaAviso('@Número de parcelas inválido [$inNumParcelas]', 'form','erro','".Sessao::getId()."');";
            $stJs .= 'f.inNumParcelas.value="1";';
        } else {
            $valorParcelas = ( $_REQUEST['flTotalApurado'] / $_REQUEST['inNumParcelas']);
            $valorParcelas = number_format( $valorParcelas, 2 );
            $stJs  = 'f.flValorPorParcela.value="'. $valorParcelas .'";';
            $stJs .= 'd.getElementById("stValorPorParcela").innerHTML = "R$ '.str_replace(".",",",$valorParcelas).'";';

            montaParcelas ( $inNumParcelas, $_REQUEST['dtVencimento'], $valorParcelas );
        }
    break;

}
SistemaLegado::executaFrameOculto($stJs);
SistemaLegado::LiberaFrames();
?>

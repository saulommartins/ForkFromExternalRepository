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
    * Página de processamento oculto para o cadastro de vigências
    * Data de Criação   : 28/03/2005

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Teixeira Stephanou

    * @ignore

    * $Id: OCManterVigencia.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.02
*/

/*
$Log$
Revision 1.4  2006/09/18 10:30:39  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GT_CIM_NEGOCIO . "RCIMNivel.class.php"         );

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

$rsRecordset               = new Recordset;

/************ Funções do Arquivo ********************/

function exemplo($rsRecordSet, $boExecuta=true)
{
}

/************ Fim de Funções do Arquivo *************/

// Acoes por pagina
switch ($stCtrl) {
    case "buscaCGMFiltro":
        if ($_POST[ 'inNumCGM' ] != '' && $_POST[ 'inNumCGM' ] != '0') {
            $obRCGM->setNumCGM( $_POST[ 'inNumCGM' ] );
            $obRCGM->consultar( $rsCGM );

            $inNumLinhas = $rsCGM->getNumLinhas();
            if ($inNumLinhas <= 0) {
                $stJs .= 'f.inNumCGM.value = "";';
                $stJs .= 'f.inNumCGM.focus();';
                $stJs .= 'd.getElementById("campoInner").innerHTML = "&nbsp;";';
                $stJs .= "SistemaLegado::alertaAviso('@Número do CGM não encontrado. (".$_POST["inNumCGM"].")','form','erro','".Sessao::getId()."');";
            } else {
                $stNomCgm = $rsCGM->getCampo("nom_cgm");
                $stJs .= 'd.getElementById("campoInner").innerHTML = "'.$stNomCgm.'";';
            }
        } elseif ($_POST[ 'inNumCGM' ] == '0') {
            $stJs .= "SistemaLegado::alertaAviso('@CGM inválido! (".$_POST["inNumCGM"].")','form','erro','".Sessao::getId()."');";
        }
    break;
       case "buscaProcesso":
        if ($_POST['inProcesso'] != '') {
            list($inProcesso,$inExercicio) = explode("/",$_POST['inProcesso']);
            $obRProcesso->setCodigoProcesso( $inProcesso  );
            $obRProcesso->setExercicio     ( $inExercicio );
            $obErro = $obRProcesso->validarProcesso();

            if ( $obErro->ocorreu() ) {
                echo "ERRO!!!<br>";
                $stJs .= 'f.inProcesso.value = "";';
                $stJs .= 'f.inProcesso.focus();';
                $stJs .= "SistemaLegado::alertaAviso('@Processo não encontrado. (".$_POST["inProcesso"].")','form','erro','".Sessao::getId()."');";
            }
        }
    break;
    case "buscaCreci":
        $obRCIMCorretagem = new RCIMCorretagem;
        $rsCorretagem     = new RecordSet;
        if ($_REQUEST["stCreci"]) {
            $obRCIMCorretagem->setRegistroCreci( $_REQUEST["stCreci"]);
            $obRCIMCorretagem->buscaCorretagem ( $rsCorretagem       );
            if ( $rsCorretagem->eof() ) {
                $stJs  = 'd.getElementById("stNomeCreci").innerHTML = "&nbsp;";';
                $stJs .= "erro = true;\n";
                $stJs .= "mensagem += 'Registro Creci inválido!';\n";
                $stJs .= "SistemaLegado::alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');\n";
            } else {
                $stJs  = 'd.getElementById("stNomeCreci").innerHTML = "'.$rsCorretagem->getCampo("nom_cgm").'";';
            }
        }
    break;
    case "limparFormulario":
        $arLimpa = array();
        Sessao::write('Documentos' , $arLimpa);
        Sessao::write('Adquirentes', $arLimpa);
        $stJs .= "d.frm.inInscricaoImobiliaria.readOnly = false;";
        $stJs .= "d.frm.reset();";
        $stJs .= "d.getElementById('spnDocumentosNatureza').innerHTML = '';";
        $stJs .= "d.getElementById('campoInner').innerHTML = '&nbsp;';";
        $stJs .= "d.getElementById('stNomeCreci').innerHTML = '&nbsp;';";
        $stJs .= "d.getElementById('spnAdquirentes').innerHTML = '';";
        $stJs .= "d.getElementById('spnProprietarios').innerHTML = '';";
        $stJs .= "d.frm.inInscricaoImobiliaria.focus();";
    break;
    case "validaData":
        $stDataLimite = "15000421";
        $stDataEfetivacao = $_REQUEST["stDataEfetivacao"];
        $stDiaEfetivacao = substr($stDataEfetivacao,0,2);
        $stMesEfetivacao = substr($stDataEfetivacao,3,5);
        $stAnoEfetivacao = substr($stDataEfetivacao,6);
        $stDataEfetivacao = $stAnoEfetivacao.$stMesEfetivacao.$stDiaEfetivacao;
        if ($stDataEfetivacao < $stDataLimite) {
            $stJs .= "    erro = true;                                                                      ";
            $stJs .= "    f.stDataEfetivacao.value=\"\";                                                 ";
            $stJs .= "    mensagem += \"@Campo Data da Inscrição deve ser posterior a 21/04/1500!\";        ";
            $stJs .= "    SistemaLegado::alertaAviso(mensagem,'form','erro','".Sessao::getId()."', '../');                     ";
            $stJs .= "    f.stDataEfetivacao.focus();                                                    ";
        }
    break;
    case "UltimoNivel":
        $obRCIMNivel = new RCIMNivel;
        $obRCIMNivel->setCodigoVigencia($_REQUEST["inCodigoVigencia"]);
        $obRCIMNivel->recuperaUltimoNivel    ( $rsUltimoNivel );
        $stNivelSuperior = $rsUltimoNivel->getCampo( "nom_nivel" );
        if(empty($stNivelSuperior))
            $stNivelSuperior = "&nbsp;";
        $stJs .= "d.getElementById('stNivelSuperior').innerHTML = '".$stNivelSuperior."'";

    break;
    $obRCIMNivel->recuperaUltimoNivel    ( $rsUltimoNivel );
    $stNivelSuperior = $rsUltimoNivel->getCampo( "nom_nivel" );
}
if( $stJs )
    SistemaLegado::executaFrameOculto($stJs);
?>

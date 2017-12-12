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
    * Processamento do popup de Vale-Tranporte Servidor Detalhar Quantidade Diária
    * Data de Criação: 18/10/2005

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30880 $
    $Name$
    $Author: tiago $
    $Date: 2007-06-28 15:07:38 -0300 (Qui, 28 Jun 2007) $

    * Casos de uso: uc-04.06.09
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GRH_BEN_NEGOCIO."RBeneficioContratoServidorConcessaoValeTransporte.class.php"         );

//Define o nome dos arquivos PHP
$stPrograma = "QuantidadeDiaria";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$obCalendario = new Calendario;
$inMes     = ( strlen(Sessao::read('inCodMes')) == 1 ) ? '0'.Sessao::read('inCodMes') : Sessao::read('inCodMes');
$inAno     = Sessao::read('inAno');
$inDiasMes = $obCalendario->retornaUltimoDiaMes($inMes,$inAno);
$dtInicial = "01/".$inMes."/".$inAno;
$dtFinal   = $inDiasMes."/".$inMes."/".$inAno;

$obRBeneficioContratoServidorConcessaoValeTransporte = new RBeneficioContratoServidorConcessaoValeTransporte;
$obRBeneficioContratoServidorConcessaoValeTransporte->addRBeneficioConcessaoValeTransporte();
$obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->obRCalendario->setCodCalendar( Sessao::read('inCodCalendario') );
$obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->obRCalendario->addFeriadoVariavel();
$obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->obRCalendario->ultimoFeriadoVariavel->setDtInicial($dtInicial);
$obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->obRCalendario->ultimoFeriadoVariavel->setDtFinal($dtFinal);
$obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->obRCalendario->listarFeriados( $rsFeriados );
$inQuantidadeMensal = 0;
$arValeDias = array();
for ($inDia=1;$inDia<=$inDiasMes;$inDia++) {
    $arTemp                  = array();
    $inIndex                 = $inDia ;
    $inDia                   = ( strlen($inDia) == 1 ) ? '0'.$inDia : $inDia;
    $stData                  = $inDia."/".$inMes."/".$inAno;
    $boCalcula               = true;
    $boObrigatorio           = ( $_POST['boObrigatorio_'.$inIndex] == 'on' ) ? true : false;
    $inQuantidade            = $_POST['inQuantidade_'.$inIndex];
    $arTemp['stData']        = $stData;
    $arTemp['boObrigatorio'] = $boObrigatorio;
    $arTemp['inQuantidade']  = $inQuantidade;
    $arValeDias[]            = $arTemp;
    while ( !$rsFeriados->eof() ) {
        if ( $rsFeriados->getCampo('dt_feriado') == $stData ) {
            $boCalcula = false;
        }
        $rsFeriados->proximo();
    }
    $rsFeriados->setPrimeiroElemento();
    if ($boCalcula or $boObrigatorio) {
        $inQuantidadeMensal = $inQuantidadeMensal + $inQuantidade;
    }
}
Sessao::write('valeDias', $arValeDias);
$stJs .= "window.parent.window.opener.document.frm.inQuantidadeMensal.value = ".$inQuantidadeMensal.";  \n";
$stJs .= "window.parent.window.opener.document.frm.inQuantidadeMensal.disabled = true;                  \n";
$stJs .= "window.parent.window.opener.document.frm.hdnQuantidadeMensal.value =".$inQuantidadeMensal.";  \n";
for ($inIndex=1;$inIndex<=7;$inIndex++) {
    //$stJs .= "window.parent.window.opener.document.frm.inQuantidade_$inIndex.disabled = true ;          \n";
    //$stJs .= "window.parent.window.opener.document.frm.boObrigatorio_$inIndex.disabled = true;          \n";
}
$stJs .= "window.parent.window.opener.document.links['cancelarDetalhar_1'].href = \"JavaScript:buscaValor('cancelarDetalhar');\"; \n";
$stJs .= "window.parent.close(); \n";

sistemaLegado::executaIFrameOculto($stJs);
?>

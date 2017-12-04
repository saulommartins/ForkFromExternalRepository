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
    * Arquivo de Oculto da DIRF.
    * Data de Criação: 22/11/2007

    * @author Diego Lemos de Souza

    * Casos de uso: uc-04.08.15

    $Id: OCExportarDIRF.php 64913 2016-04-12 20:16:19Z michel $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ExportarDIRF";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

function gerarSpanNumeroRecibo(Request $request)
{
    $stHtml = "";
    $stEval = "";
    if ($request->get('stIndicador') == "1" and $request->get('inAnoCompetencia') >= 2002) {
        $obIntNumeroEntrega = new Inteiro();
        $obIntNumeroEntrega->setRotulo("Número do Recibo da Última Declaração Entregue");
        $obIntNumeroEntrega->setName("inNumeroRecibo");
        $obIntNumeroEntrega->setTitle("Digite o número do recibo da última declaração entregue.");
        $obIntNumeroEntrega->setNull(false);

        $obFormulario = new Formulario();
        $obFormulario->addComponente($obIntNumeroEntrega);
        $obFormulario->montaInnerHTML();
        $obFormulario->obJavaScript->montaJavaScript();
        $stHtml = $obFormulario->getHTML();
        $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    }
    $stJs  = "d.getElementById('spnNumeroRecibo').innerHTML = '".$stHtml."'\n";
    $stJs .= "f.hdnNumeroRecibo.value = '".$stEval."'\n;";

    return $stJs;
}

function submeter()
{
    $stJs .= "BloqueiaFrames(true,false);\n";
    $stJs .= "parent.frames[2].Salvar();    \n";

    return $stJs;
}

function desabilitaPagamentoSemRetencao()
{
    $stJs .= "  if(jq('#boPrestadoresServico').is(':checked')){
                    jq('#boPrestadoresServicoTodos').removeAttr('disabled');
                }else{
                    jq('#boPrestadoresServicoTodos').attr('checked',false);
                    jq('#boPrestadoresServicoTodos').attr('disabled',true);
                }
            ";

    return $stJs;
}

switch ($request->get('stCtrl')) {
    case "gerarSpanNumeroRecibo":
        $stJs .= gerarSpanNumeroRecibo($request);
    break;
    case "submeter":
        $stJs = submeter();
    break;
    case "desabilitaPagamentoSemRetencao":
        $stJs = desabilitaPagamentoSemRetencao();
    break;
}

if ($stJs) {
    echo $stJs;
}

?>

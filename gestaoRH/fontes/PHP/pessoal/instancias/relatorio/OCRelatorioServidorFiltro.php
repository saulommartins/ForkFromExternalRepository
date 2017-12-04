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
* Pagina de oculto para filtro do relatório do servidor
* Data de Criação   : 15/07/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Diego Lemos de Souza

* @ignore

$Revision: 30566 $
$Name$
$Autor: $
$Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

*Casos de uso: uc-04.04.12
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GA_CGM_NEGOCIO."RCGMPessoaFisica.class.php"         );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalServidor.class.php"         );

$stCtrl = $_REQUEST['stCtrl'];

//Define o nome dos arquivos PHP
$stPrograma    = "RelatorioServidor";
$pgFilt        = "FL".$stPrograma.".php";
$pgOculFilt    = "OC".$stPrograma."Filtro.php";
$pgOcul        = "OC".$stPrograma."Filtro.php";
$pgJs          = "JS".$stPrograma.".js";

###########################ATIVOS / APOSENTADOS#####################################

function gerarSpanAtivosAposentados()
{
    $stSituacao = $_REQUEST["stSituacao"];

    $stJs .= limparSpans();

    include_once(CAM_GRH_PES_COMPONENTES."IFiltroComponentes.class.php");
    $obIFiltroComponentes = new IFiltroComponentes();
    $obIFiltroComponentes->setMatricula();
    $obIFiltroComponentes->setCGMMatricula();
    $obIFiltroComponentes->setLocal();
    $obIFiltroComponentes->setLotacao();
    $obIFiltroComponentes->setAtributoServidor();    
    $obIFiltroComponentes->setGeral(false);

    $obFormulario = new Formulario();

    switch ($stSituacao) {
        case 'todos':
            $obFormulario->addTitulo("Todos");
            break;
        case 'ativos':
                $obFormulario->addTitulo("Ativos");
                $obIFiltroComponentes->setAtivos();
            break;
        case 'aposentados':
                $obFormulario->addTitulo("Aposentados");
                $obIFiltroComponentes->setAposentados();
            break;
        case 'rescindidos':
                $obFormulario->addTitulo("Rescindidos");
                $obIFiltroComponentes->setRescisao();
            break;
    }

    $obIFiltroComponentes->geraFormulario($obFormulario);
    
    $obFormulario->montaInnerHTML();
    $obFormulario->obJavaScript->montaJavaScript();
    $stHtml = $obFormulario->getHTML();

    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);
    
    $stJs .= "d.getElementById('spnCadastro').innerHTML = '$stHtml';\n";
    $stJs .= "f.hdnTipoFiltroExtra.value = '$stEval';\n";

    return $stJs;

}

###########################PENSIONISTAS#####################################

function gerarSpanPensionistas()
{
    $stSituacao = $_GET["stSituacao"];

    $stJs .= limparSpans();

    include_once(CAM_GRH_PES_COMPONENTES."IFiltroComponentes.class.php");
    $obIFiltroComponentes = new IFiltroComponentes();
    $obIFiltroComponentes->setMatriculaPensionista();
    $obIFiltroComponentes->setCGMMatriculaPensionista();
    $obIFiltroComponentes->setLotacao();
    $obIFiltroComponentes->setGeral(false);
    
    $obFormulario = new Formulario();
    $obFormulario->addTitulo("Pensionistas");
    $obIFiltroComponentes->geraFormulario($obFormulario);
    $obFormulario->montaInnerHTML();
    $obFormulario->obJavaScript->montaJavaScript();
    $stHtml = $obFormulario->getHTML();    
    $stJs .= "d.getElementById('spnCadastro').innerHTML = '$stHtml';\n";
    return $stJs;
}

###########################LIMPA SPANS#####################################

function limparSpans()
{
    #Cadastro
    $stJs .= "d.getElementById('spnCadastro').innerHTML = '';\n";

    return $stJs;
}

switch ($_REQUEST ["stCtrl"]) {
    case "gerarSpanAtivosAposentados":        
        $stJs .= gerarSpanAtivosAposentados();
        break;
    case "gerarSpanPensionistas":        
        $stJs .= gerarSpanPensionistas();
        break;
}

if ( $stJs )
    sistemaLegado::executaFrameOculto($stJs);

?>

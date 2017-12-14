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
    * Página de Formulário do Exportação Remessa Banrisul
    * Data de Criação: 10/06/2008

    * @author Analista: Dagiane	Vieira
    * @author Desenvolvedor: <Alex Cardoso>

    * @ignore

    $Id: $

    * Casos de uso: uc-04.08.26
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                             );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );

//Define o nome dos arquivos PHP
$stPrograma = "CreditoBanrisul";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgDown     = "DW".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

$obRFolhaPagamentoPeriodoMovimentacao = new RFolhaPagamentoPeriodoMovimentacao;
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao($obRFolhaPagamentoPeriodoMovimentacao);

$stAcao      = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName                             ( "stAcao"                                                              );
$obHdnAcao->setValue                            ( $stAcao                                                               );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName                             ( "stCtrl"                                                              );
$obHdnCtrl->setValue                            ( $stCtrl                                                               );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction                              ( $pgProc                                                               );
$obForm->setTarget                              ( "oculto"                                                              );

$stTabela1 .= "<table border=0 width=100%>";
$stTabela1 .= "<tr><td align=center colspan=2 width=100%><font size=3><b>Resumo Remessa Banrisul</b></font></td></tr>";
$stTabela1 .= "<tr><td align=right width=50%><font size=-1>Competência:</font></td>";

$stCompetencia  = ( $_GET['inCodMes'] >= 10 )? $_GET['inCodMes'] : "0".$_GET['inCodMes'];
$stCompetencia .= "/".$_GET['inAno'];

$stTabela1 .= "<td align=left width=50%><font size=-1>".$stCompetencia."</font></td></tr>";
$stTabela1 .= "<tr><td align=right width=50%><font size=-1>Tipo de Cálculo:</font></td>";
$stTabela1 .= "<td align=left width=50%><font size=-1>Salário</font></td></tr>";

if ($_GET["stSituacao"]) {
    switch ($_GET["stSituacao"]) {
        case "ativos";
            $stCadastro = "Ativos";
            break;
        case "aposentados";
            $stCadastro = "Aposentados";
            break;
        case "pensionistas";
            $stCadastro = "Pensionistas";
            break;
        case "todos";
            $stCadastro = "Todos";
            break;
    }
}

$stTabela1 .= "<tr><td align=right width=50%><font size=-1>Cadastro:</font></td>";
$stTabela1 .= "<td align=left width=50%><font size=-1>".$stCadastro."</font></td></tr>";
$stTabela1 .= "<tr><td align=right width=50%><font size=-1>Quantidade de Registros:</font></td>";
$stTabela1 .= "<td align=left width=50%><font size=-1>".Sessao::read('inQuantRegistros')."</font></td></tr>";
$stTabela1 .= "<tr><td align=right width=50%><font size=-1>Download:</font></td>";
$arArquivoDownload = Sessao::read('arArquivosDownload');
$stTabela1 .= "<td align=left width=50%><font size=-1><a href='".$pgDown."?arq=".$arArquivoDownload[0]['stLink']."&label=".$arArquivoDownload[0]['stNomeArquivo']."'>".$arArquivoDownload[0]['stNomeArquivo']."</a></font></td></tr>";
$stTabela1 .= "</table>";
$stTabela1 .= "</center>";

$spnResumo = new Span();
$spnResumo->setValue($stTabela1);

SistemaLegado::LiberaFrames();

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario();
$obFormulario->addForm                          ( $obForm                                                               );
$obFormulario->addTitulo                        ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right"      );
$obFormulario->addHidden                        ( $obHdnAcao                                                            );
$obFormulario->addHidden                        ( $obHdnCtrl                                                            );
$obFormulario->addSpan($spnResumo);
$obFormulario->show();
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

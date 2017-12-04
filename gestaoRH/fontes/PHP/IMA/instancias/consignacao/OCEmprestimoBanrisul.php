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
/*
 * Página oculta para processamento das telas de empréstimos Banrisul
 * Data de Criação   : 01/09/2009

 * @author Analista      Dagine Rodrigues Vieira
 * @author Desenvolvedor Cassiano de Vasconcellos Ferreira

 * @package URBEM
 * @subpackage

 * @ignore

 $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

function montaInnerHtmlImporta()
{
    $obFileImportacao = new FileBox();
    $obFileImportacao->setRotulo('Caminho do arquivo');
    $obFileImportacao->setName('stArquivoImportacao');
    $obFileImportacao->setSize(30);
    $obFileImportacao->setNull(false);

    $obInnerFormulario = new Formulario;
    $obInnerFormulario->addComponente($obFileImportacao);
    $obInnerFormulario->montaInnerHTML();
    $obInnerFormulario->obJavaScript->montaJavaScript();

    $stInnerHtml = $obInnerFormulario->getHTML();
    $stinnerJavaScript = $obInnerFormulario->obJavaScript->getInnerJavaScript();
    $stJs  = "jQuery('#spnFiltroArquivo').html('".$stInnerHtml."');\n";
    $stJs .= "jQuery('#stHdnArquivoImportacao').val('".$stinnerJavaScript."');";
    $stJs .= "jQuery('#stAcao').val('validarArquivo');";

    return $stJs;
}

function montaInnerHtmlExporta()
{
    include_once CAM_GRH_PES_COMPONENTES.'IFiltroCompetencia.class.php';

    $obComboSituacao = new Select;
    $obComboSituacao->setRotulo( "Cadastro"                            );
    $obComboSituacao->setTitle ( "Selecione o cadastro para filtro."   );
    $obComboSituacao->setName  ( "stSituacao"                          );
    $obComboSituacao->setValue ( "ativos"                              );
    $obComboSituacao->setStyle ( "width: 200px"                        );
    $obComboSituacao->addOption( "ativos", "Ativos"                    );
    $obComboSituacao->addOption( "aposentados", "Aposentados"          );
    $obComboSituacao->addOption( "pensionistas", "Pensionistas"        );
    $obComboSituacao->addOption( "todos", "Todos"                      );
    $obComboSituacao->setNull  ( false                                 );
    $obComboSituacao->obEvento->setOnChange("montaParametrosGET('geraSpanCadastro','stSituacao');");

    $obIFiltroCompetencia = new IFiltroCompetencia;

    $obSpanCadastro = new Span;
    $obSpanCadastro->setId('spnCadastro');

    $obInnerFormulario = new Formulario;
    $obInnerFormulario->addComponente($obComboSituacao);
    $obIFiltroCompetencia->geraFormulario($obInnerFormulario);
    $obInnerFormulario->addSpan($obSpanCadastro);
    $obInnerFormulario->montaInnerHTML();
    $obInnerFormulario->obJavaScript->montaJavaScript();

    $stInnerHtml = $obInnerFormulario->getHTML();
    $stJs  = "jQuery('#spnFiltroArquivo').html('".$stInnerHtml."');\n";
    $stJs .= "jQuery('#stHdnArquivoImportacao').val('".$stinnerJavaScript."');";
    $stJs .= "jQuery('#stAcao').val('exportar');";
    $stJs .= geraSpanCadastro(1);

    return $stJs;
}

function geraSpanCadastro($inCadastro)
{
        switch ($_GET["stSituacao"]) {
        case "pensionistas"://pensionistas

            return gerarSpanPensionistas();
            break;
        case "pensao_judicial"://pensao judicial

            return gerarSpanPensaoJudicial();
            break;
        default:
            return gerarSpanGeral();
            break;
    }
}

function limparSpans()
{
    #Cadastro
    $stJs .= "d.getElementById('spnCadastro').innerHTML = '';\n";

    return $stJs;
}

function gerarSpanGeral()
{
    $stSituacao = $_GET["stSituacao"];
    $stJs .= limparSpans();
    include_once CAM_GRH_PES_COMPONENTES."IFiltroComponentes.class.php";
    $obIFiltroComponentes = new IFiltroComponentes();
    if ($_GET["stSituacao"] != 'todos') {
       $obIFiltroComponentes->setMatricula();
    }

    $obIFiltroComponentes->setLotacao();
    $obIFiltroComponentes->setFiltroPadrao('geral');

    $obFormulario = new Formulario();
    switch ($stSituacao) {

        case 'aposentados':
                $obFormulario->addTitulo("Aposentados");
                $obIFiltroComponentes->setAposentados();
            break;
        case 'rescindidos':
                $obFormulario->addTitulo("Rescindidos");
                $obIFiltroComponentes->setRescisao();
            break;
        case 'todos':
                $obFormulario->addTitulo("Todos");
                $obIFiltroComponentes->setTodos();
            break;
        case 'ativos':
            default:
                $obFormulario->addTitulo("Ativos");
                $obIFiltroComponentes->setAtivos();
            break;
    }

    $obIFiltroComponentes->geraFormulario($obFormulario);
    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();
    $stJs .= "d.getElementById('spnCadastro').innerHTML = '$stHtml';\n";

    return $stJs;
}

function gerarSpanPensionistas()
{
    include_once CAM_GRH_PES_COMPONENTES."IFiltroComponentes.class.php";
    $stSituacao = $_GET["stSituacao"];
    $stJs .= limparSpans();

    $obIFiltroComponentes = new IFiltroComponentes();
    $obIFiltroComponentes->setMatriculaPensionista();
    $obIFiltroComponentes->setLotacao();
    $obIFiltroComponentes->setFiltroPadrao('geral');

    $obFormulario = new Formulario();
    $obFormulario->addTitulo("Pensionistas");
    $obIFiltroComponentes->geraFormulario($obFormulario);

    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();

    $stJs .= "d.getElementById('spnCadastro').innerHTML = '$stHtml ';\n";

    return $stJs;
}

switch ($_GET['stCtrl']) {
    case 'geraSpanImportacao':
        echo montaInnerHtmlImporta();
    break;
    case 'geraSpanExportacao':
        echo montaInnerHtmlExporta();
    break;
    case 'geraSpanCadastro':
        echo geraSpanCadastro($_GET['inCadastro']);
    break;
}
?>

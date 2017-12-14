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
    * Página oculta para Emitir Relatório de Histórico de Férias
    * Data de Criação: 17/08/2006

    * @author Analista: Vandr� Miguel Ramos
    * @author Desenvolvedor: Bruce Cruz de Sena

    * @ignore
    * Casos de uso: uc-04.04.27
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

$stPrograma = "RelatorioHistoricoFerias";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

function gerarOrdenacaoContrato()
{
    $obFormulario = new Formulario;

    $obCmbOrdenacaoContrato = new Select;
    $obCmbOrdenacaoContrato->setName                        ( "stOrdenacaoContrato"        );
    $obCmbOrdenacaoContrato->setValue                       ( 'A'                          );
    $obCmbOrdenacaoContrato->setRotulo                      ( "Ordenar por servidor"       );
    $obCmbOrdenacaoContrato->setTitle                       ( "Selecione a ordenação da Matrícula do Servidor." );
    $obCmbOrdenacaoContrato->addOption                      ( "A","Alfabética"             );
    $obCmbOrdenacaoContrato->addOption                      ( "N","Numérica"               );
    $obCmbOrdenacaoContrato->setStyle                       ( "width: 250px"               );

    $obFormulario->addComponente                            ( $obCmbOrdenacaoContrato      );
    $obFormulario->montaInnerHTML();
    $obFormulario->obJavaScript->montaJavaScript();

    $stJs  = "jQuery('#spnOrdenacao').html('".$obFormulario->getHTML()."');\n";

    return $stJs;
}

function gerarOrdenacaoLotacao()
{
    $obFormulario = new Formulario;

    $obCmbOrdenacaoLotacao = new Select;
    $obCmbOrdenacaoLotacao->setName                         ( "stOrdenacaoLotacao"                  );
    $obCmbOrdenacaoLotacao->setValue                        ( 'A'                                   );
    $obCmbOrdenacaoLotacao->setRotulo                       ( "Ordenar por lotação"                 );
    $obCmbOrdenacaoLotacao->setTitle                        ( "Selecione a ordenação para lotação." );
    $obCmbOrdenacaoLotacao->addOption                       ( "A","Alfabética"                      );
    $obCmbOrdenacaoLotacao->addOption                       ( "N","Numérica"                        );
    $obCmbOrdenacaoLotacao->setStyle                        ( "width: 250px"                        );

    $obFormulario->addComponente                            ( $obCmbOrdenacaoLotacao                );
    $obFormulario->montaInnerHTML();
    $obFormulario->obJavaScript->montaJavaScript();

    $stJs  = "jQuery('#spnOrdenacao').html('".$obFormulario->getHTML()."');\n";

    return $stJs;
}

function gerarOrdenacaoLocal()
{
    $obFormulario = new Formulario;

    $obCmbOrdenacaoLocal = new Select;
    $obCmbOrdenacaoLocal->setName                          ( "stOrdenacaoLocal"                   );
    $obCmbOrdenacaoLocal->setValue                         ( 'A'                                  );
    $obCmbOrdenacaoLocal->setRotulo                        ( "Ordenar por local"                  );
    $obCmbOrdenacaoLocal->setTitle                         ( "Selecione a ordenação para o local.");
    $obCmbOrdenacaoLocal->addOption                        ( "A","Alfabética"                     );
    $obCmbOrdenacaoLocal->addOption                        ( "N","Numérica"                       );
    $obCmbOrdenacaoLocal->setStyle                         ( "width: 250px"                       );

    $obFormulario->addComponente                            ( $obCmbOrdenacaoLocal                );
    $obFormulario->montaInnerHTML();
    $obFormulario->obJavaScript->montaJavaScript();

    $stJs  = "jQuery('#spnOrdenacao').html('".$obFormulario->getHTML()."');\n";

    return $stJs;
}

function montarFiltroOrdenacao()
{
    switch (trim($_REQUEST["stTipoFiltro"])) {
        case "lotacao":
            $stJs = gerarOrdenacaoLotacao();
            break;
        case "local":
            $stJs = gerarOrdenacaoLocal();
            break;
        default:
            $stJs = gerarOrdenacaoContrato();
            break;
    }

    return $stJs;
}

switch ($_REQUEST["stCtrl"]) {
    case "montarFiltroOrdenacao":
        $stJs = montarFiltroOrdenacao();
    break;
    
    default:
        $stValoresFiltro = "";
        $obErro = new Erro;
        
        switch ($_REQUEST['stTipoFiltro']) {
            case "contrato_todos":
            case "cgm_contrato_todos":
                $stValoresFiltro = "";
                $arContratos = Sessao::read("arContratos");
                
                if(!is_array($arContratos)) {
                    $obErro->setDescricao('É necessário adicionar ao menos um registro na lista de filtros');
                    break;
                }
                
                foreach ($arContratos as $arContrato) {
                    $stValoresFiltro .= $arContrato["cod_contrato"].",";
                }
                $stValoresFiltro = substr($stValoresFiltro,0,strlen($stValoresFiltro)-1);
            break;
            
            case "funcao":
                if(!is_array($_REQUEST["inCodFuncaoSelecionados"])) {
                    $obErro->setDescricao('É necessário adicionar ao menos um registro na lista de filtros');
                    break;
                }
                
                $stValoresFiltro = implode(",",$_REQUEST["inCodFuncaoSelecionados"]);
            break;
            
            case "lotacao":
                if(!is_array($_REQUEST["inCodLotacaoSelecionados"])) {
                    $obErro->setDescricao('É necessário adicionar ao menos um registro na lista de filtros');
                    break;
                }
                
                $stValoresFiltro = implode(",",$_REQUEST["inCodLotacaoSelecionados"]);
            break;
            
            case "local":
                if(!is_array($_REQUEST["inCodLocalSelecionados"])) {
                    $obErro->setDescricao('É necessário adicionar ao menos um registro na lista de filtros');
                    break;
                }
                
                $stValoresFiltro = implode(",",$_REQUEST["inCodLocalSelecionados"]);
            break;
            
            case "sub_divisao":
                if(!is_array($_REQUEST["inCodSubDivisaoSelecionados"])) {
                    $obErro->setDescricao('É necessário adicionar ao menos um registro na lista de filtros');
                    break;
                }
                
                $stValoresFiltro .= implode(",",$_REQUEST["inCodSubDivisaoSelecionados"]);
            break;
        }
        
        if(!$obErro->ocorreu()) {
            Sessao::write('stTipoFiltro', $_REQUEST['stTipoFiltro']);
            Sessao::write('stValoresFiltro', $stValoresFiltro);
            Sessao::write('dtDataLimite', $_REQUEST['dtDataLimite']);
            Sessao::write('stOrdenacaoLotacao', $_REQUEST['stOrdenacaoLotacao']);
            Sessao::write('stOrdenacaoRegime', $_REQUEST['stOrdenacaoRegime']);
            Sessao::write('stOrdenacaoContrato', $_REQUEST['stOrdenacaoContrato']);
    
            $js = " parent.frames['telaPrincipal'].document.frm.target = 'telaPrincipal';";
            $js.= " parent.frames['telaPrincipal'].document.frm.action = '".$pgProc."';";
            $js.= " parent.frames['telaPrincipal'].document.frm.submit();";
            SistemaLegado::executaFrameOculto($js);
        } else {
            // Adicionado include nesta região, pois se adicionado no cabecalho gera erro no carregar página de filtro.
            include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
            
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()), 'n_incluir', 'erro');
        }
    break;
}

if ($stJs) {
    echo $stJs;
}

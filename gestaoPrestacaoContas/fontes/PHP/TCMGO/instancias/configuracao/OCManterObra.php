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
    * Página de Oculto
    * Data de Criação   : 16/04/2007

    * @author Analista      Só Deus sabe
    * @author Desenvolvedor Yemanjá

    * @package URBEM
    * @subpackage

    * @ignore

    $Id: OCManterObra.php 63835 2015-10-22 13:53:31Z franver $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterObra";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";

$stCtrl = $_REQUEST['stCtrl'];

function listaEmpenhos()
{

    $obLista = new Lista;
    $rsEmpenhos = new RecordSet;
    $rsEmpenhos->preenche ( Sessao::read('arEmpenhos') );

    $obLista->setRecordset( $rsEmpenhos );
    $obLista->setTitulo ( 'Lista de empenhos' );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Entidade");
    $obLista->ultimoCabecalho->setWidth( 5);
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Empenho");
    $obLista->ultimoCabecalho->setWidth( 5);
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Data");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Fornecedor");
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "cod_entidade" );
    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "cod_empenho" );
    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "data_empenho" );
    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "nom_cgm" );
    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLista->commitDado();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Ação");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "javascript: executaFuncaoAjax('delEmpenho');" );
    $obLista->ultimaAcao->addCampo("","&codEmpenho=[cod_empenho]&codEntidade=[cod_entidade]");
    $obLista->commitAcao();

    $obLista->montaHTML();

    $html = $obLista->getHTML();
    $html = str_replace("\n","",$html);
    $html = str_replace("  ","",$html);
    $html = str_replace("'","\\'",$html);

    $stJs .= "d.getElementById('spnEmpenho').innerHTML = '';\n";
    $stJs .= "d.getElementById('spnEmpenho').innerHTML = '".$html."';\n";

    return $stJs;

}

function carregaEmpenhos($inCodObra, $inAnoOBra)
{
    $rsEmpenhos = new RecordSet;
    $arEmpenhos = array();
    $arRegistro = array();
    include_once( TTGO.'TTGOObrasEmpenho.class.php' );

    $obTTGOObrasEmpenho = new TTGOObrasEmpenho;
    $obTTGOObrasEmpenho->setDado ( 'cod_obra', $inCodObra );
    $obTTGOObrasEmpenho->setDado ( 'ano_obra', $inAnoOBra );
    $obTTGOObrasEmpenho->buscaEmpenhos ( $rsEmpenhos );

    while (!$rsEmpenhos->eof() ) {
        $arRegistro['cod_entidade'] = $rsEmpenhos->getCampo ( 'cod_entidade' );
        $arRegistro['cod_empenho' ] = $rsEmpenhos->getCampo ( 'cod_empenho'  );
        $arRegistro['data_empenho'] = $rsEmpenhos->getCampo ( 'dt_empenho'   );
        $arRegistro['nom_cgm'     ] = $rsEmpenhos->getCampo ( 'nom_cgm'      );
        $arEmpenhos[] = $arRegistro ;

        $rsEmpenhos->proximo();
    }
    Sessao::write('arEmpenhos', $arEmpenhos);
}

$arEmpenhos = Sessao::read('arEmpenhos');
$stJs = '';
switch ($stCtrl) {
    case 'buscaEmpenho':
        $stFornecedor = '&nbsp;';
        if ($_GET['inCodigoEmpenho']) {
            include_once ( CAM_GF_EMP_NEGOCIO."REmpenhoEmpenho.class.php" );
            $obREmpenhoEmpenho = new REmpenhoEmpenho;
            $obREmpenhoEmpenho->obROrcamentoEntidade->setCodigoEntidade ( $_REQUEST["inCodEntidade"]  );
            $obREmpenhoEmpenho->setExercicio  ( Sessao::getExercicio() );
            $obREmpenhoEmpenho->setCodEmpenhoInicial ( $_REQUEST["inCodigoEmpenho"] );
            $obREmpenhoEmpenho->setCodEmpenhoFinal   ( $_REQUEST["inCodigoEmpenho"] );
            $obREmpenhoEmpenho->setSituacao ( 5 );
            $obREmpenhoEmpenho->listar($rsLista);
            if ( $rsLista->getCampo('nom_fornecedor') ) {
                $stFornecedor = str_replace( "'","\'",$rsLista->getCampo( "nom_fornecedor" ) );
            } else {
                $stJs .= "f.inCodigoEmpenho.value='';";
                $stJs .= "d.getElementById( 'stNomFornecedor' ).innerHTML = '&nbsp;';";
                $stJs .= "alertaAviso('Empenho informado está anulado ou não existe.','frm','erro','".Sessao::getId()."'); \n";
            }
        }
        $stJs .= "d.getElementById('stNomFornecedor').innerHTML = '$stFornecedor';\n";
    break;

    case 'incluirEmpenho' :

        $arRequest = array();
        $arRequest = explode('/', $_REQUEST['inCodEmpenho']);
        $inReqCodEmpenho = $arRequest[0];

        $boIncluir = true;
        if ( count( $arEmpenhos ) > 0 ) {
            foreach ($arEmpenhos as $key => $array) {
                $stCod = $array['cod_empenho'];
                $stEnt = $array['cod_entidade'];

                if ($inReqCodEmpenho == $stCod && $_REQUEST['inCodEntidade'] == $stEnt) {
                    $boIncluir = false;
                    $stErro = "Este empenho já está na lista.";
                    break;
                }
            }
        }

        if ($boIncluir) {
            include_once( CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenho.class.php" );
            $arEmpenho = explode('/', $_REQUEST["inCodEmpenho"]);

            $obTEmpenhoEmpenho = new TEmpenhoEmpenho;
            $obTEmpenhoEmpenho->setDado( 'cod_entidade'   , $_REQUEST["inCodEntidade"]);
            $obTEmpenhoEmpenho->setDado( 'cod_empenho'    , $arEmpenho[0]             );
            $obTEmpenhoEmpenho->setDado( 'exercicio'      , $arEmpenho[1]             );
            $obTEmpenhoEmpenho->recuperaEmpenhoObra ($rsLista);

            $arRegistro['cod_entidade'] = $rsLista->getCampo('cod_entidade');
            $arRegistro['cod_empenho' ] = $rsLista->getCampo('cod_empenho');
            $arRegistro['data_empenho'] = $rsLista->getCampo('dt_empenho');
            $arRegistro['nom_cgm'     ] = $rsLista->getCampo('nom_fornecedor');
            $arRegistro['exercicio'   ] = $rsLista->getCampo('exercicio_empenho');

            $arEmpenhos[] = $arRegistro ;

            Sessao::write('arEmpenhos', $arEmpenhos);
            $stJs = listaEmpenhos();
        } else {
            $stJs .= "alertaAviso('$stErro','form','erro','".Sessao::getId()."');\n";
        }

    break;

    case 'delEmpenho':

        $arTempEmp = array();
        foreach ($arEmpenhos as $registro) {
            if ($registro['cod_empenho'] . $registro['cod_entidade'] != $_GET['codEmpenho']. $_GET['codEntidade']) {
                $arTempEmp[] = $registro;
            }
        }
        Sessao::write('arEmpenhos', $arTempEmp);
        $stJs = listaEmpenhos();

    break;

}

if ($stJs) {
    echo $stJs;
}

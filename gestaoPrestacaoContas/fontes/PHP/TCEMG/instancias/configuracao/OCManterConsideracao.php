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

/**
  * Página de Formulario de Configuração de Consideracoes dos Arquivos
  * Data de Criação: 25/02/2014

  * @author Analista:      Sergio Santos
  * @author Desenvolvedor: Evandro Melos
  *
  * @ignore
  * $Id: OCManterConsideracao.php 62857 2015-06-30 13:53:56Z franver $
  * $Date: $
  * $Author: $
  * $Rev: $
  *
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GPC_TCEMG_MAPEAMENTO.'TTCEMGConsideracaoArquivoDescricao.class.php';


//Define o nome dos arquivos PHP
$stPrograma = "ManterConsideracao";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];


function montaSpanCodigos(){
  global $request;  
  
  //Filtros  
  $inCodEntidade = $request->get('inCodEntidade');
  $inMes = $request->get('inMes');
  $stExercicio = Sessao::getExercicio();
  $stTipoExportacao = $request->get('stTipoExportacao');
  
  $stFiltro = " WHERE cod_entidade = ".$inCodEntidade;
  $stFiltro .= " AND periodo = ".$inMes;
  $stFiltro .= " AND modulo_sicom = '".$stTipoExportacao."'";
  $stFiltro .= " AND exercicio = '".$stExercicio."'";
  
  //Lista de códigos cadastrados para cada entidade
  $TTCEMGConsideracaoArquivoDescricao = new TTCEMGConsideracaoArquivoDescricao;
  $TTCEMGConsideracaoArquivoDescricao->recuperaDescricaoArquivos($rsConsideracao, $stFiltro, $boTransacao);

  //se não existir arquivos para aquela entidade naquele periodo e exercicio cria novos arquivos em branco
  if ($rsConsideracao->getNumLinhas() < 1) {
    $TTCEMGConsideracaoArquivoDescricao->setDado('cod_entidade',$inCodEntidade);
    $TTCEMGConsideracaoArquivoDescricao->setDado('periodo'     ,$inMes );
    $TTCEMGConsideracaoArquivoDescricao->setDado('modulo_sicom', $stTipoExportacao );
    $TTCEMGConsideracaoArquivoDescricao->setDado('exercicio'   ,"'".$stExercicio."'");
    $TTCEMGConsideracaoArquivoDescricao->insereNovosArquivosPeriodo($boTransacao);
    $TTCEMGConsideracaoArquivoDescricao->recuperaDescricaoArquivos($rsConsideracao, $stFiltro, $boTransacao);
  }
  
  $obFormulario = new Formulario;
  $obFormulario->addForm ( $obForm );

  $obLista = new Lista();
  $obLista->setMostraPaginacao(false);
  $obLista->setTitulo('Lista de Arquivos');
  $obLista->setRecordSet($rsConsideracao);
  
  //Cabeçalhos
  $obLista->addCabecalho('', 1);
  $obLista->addCabecalho('Arquivo', 5);
  
  //Dados
  $obLista->addDado();
  $obLista->ultimoDado->setAlinhamento('ESQUERDA');
  $obLista->ultimoDado->setCampo('[cod_arquivo] - [nom_arquivo]');
  $obLista->commitDadoComponente();
  
  $obTxtConsideracao = new TextArea;
  $obTxtConsideracao->setRotulo             ( "Consideração"                 );
  $obTxtConsideracao->setName               ( 'stConsideracao_[cod_arquivo]_[nom_arquivo]' );
  $obTxtConsideracao->setValue              ( '[descricao]'                  );
  $obTxtConsideracao->setNull               ( false                          );
  $obTxtConsideracao->setMaxCaracteres      ( 3000                           );
  $obTxtConsideracao->setRows               ( 2                              );
  $obTxtConsideracao->setCols               ( 150                            );
  $obTxtConsideracao->setTitle              ( "Informações complementares"   );
  
  $obLista->addCabecalho('Consideração', 10);
  $obLista->addDadoComponente( $obTxtConsideracao , false);
  $obLista->ultimoDado->setAlinhamento('CENTRO');
  $obLista->ultimoDado->setCampo( "descricao" );
  $obLista->commitDadoComponente();
  
  $obLista->montaInnerHTML();
  $stHTML = $obLista->getHTML();
  
  $stJs = "jQuery('#spnCodigos').html('".$stHTML."');";
  
  return $stJs;
}

// Acoes por pagina
switch ($stCtrl) {
    case "montaSpanCodigos":
            $stJs = montaSpanCodigos();
          break;
}

if ($stJs) {
    echo $stJs;
}

?>
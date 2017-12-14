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
    * Página de processamento
    * Data de Criação: 23/03/2009

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Grasiele Torres

    * @ignore

    * Caso de uso: uc-03.03.31

    $Id: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_ALM_MAPEAMENTO.'TAlmoxarifadoNatureza.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ReemitirEntrada";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stCtrl = $_REQUEST['stCtrl'];

Sessao::setTrataExcecao( true );

$stAcao = $request->get('stAcao');

$inNumLancamento    = $_REQUEST['inNumLancamento'];
$inCodNatureza      = $_REQUEST['inCodNatureza'];
$exercicioReemissao = $_REQUEST['exercicio'];

$obTAlmoxarifadoNatureza = new TAlmoxarifadoNatureza;
$obTAlmoxarifadoNatureza->setDado('tipo_natureza' , 'E');
$obTAlmoxarifadoNatureza->setDado('cod_natureza'  , $inCodNatureza);
$obTAlmoxarifadoNatureza->recuperaPorChave($rsDescricao);
$NomNatureza = $rsDescricao->getCampo('descricao');

switch ($inCodNatureza) {
    //Entrada por Ordem de Compra
    case 1:
        $stCaminho    = CAM_GP_ALM_INSTANCIAS . 'entrada/OCGeraMovimentacaoDiversa.php?'.Sessao::getId().'&stAcao='.$stAcao;
        SistemaLegado::alertaAviso($stCaminho.'&inNumLancamento='.$inNumLancamento.'&exercicioReemissao='.$exercicioReemissao.'&inCodNatureza='.$inCodNatureza.'&inNomNatureza='.$NomNatureza,"Entrada por Ordem de Compra","incluir","aviso", Sessao::getId(), "../");
        break;

    //Entrada por Transferência
    case 2:
        $stCaminho    = CAM_GP_ALM_INSTANCIAS . 'entrada/OCGeraMovimentacaoTransferencia.php?'.Sessao::getId().'&stAcao='.$stAcao;
        SistemaLegado::alertaAviso($stCaminho."&inNumLancamento=".$inNumLancamento.'&exercicioReemissao='.$exercicioReemissao.'&inNomNatureza='.$NomNatureza,"Entrada por Transferência","incluir","aviso", Sessao::getId(), "../");
        break;

    //Entrada por Doação
    case 3:
        $stCaminho    = CAM_GP_ALM_INSTANCIAS . 'entrada/OCGeraMovimentacaoDiversa.php?'.Sessao::getId().'&stAcao='.$stAcao;
        SistemaLegado::alertaAviso($stCaminho.'&inNumLancamento='.$inNumLancamento.'&exercicioReemissao='.$exercicioReemissao.'&inCodNatureza='.$inCodNatureza.'&inNomNatureza='.$NomNatureza,"Entrada por Doação","incluir","aviso", Sessao::getId(), "../");
        break;

    //Entrada por Requisição
    case 7:
        $stCaminho    = CAM_GP_ALM_INSTANCIAS . 'entrada/OCGeraMovimentacaoDiversa.php?'.Sessao::getId().'&stAcao='.$stAcao;
        SistemaLegado::alertaAviso($stCaminho.'&inNumLancamento='.$inNumLancamento.'&exercicioReemissao='.$exercicioReemissao.'&inCodNatureza='.$inCodNatureza.'&inNomNatureza='.$NomNatureza,"Entrada por Requisição","incluir","aviso", Sessao::getId(), "../");
        break;

    //Entrada Diversa
    case 9:
        $stCaminho    = CAM_GP_ALM_INSTANCIAS . 'entrada/OCGeraMovimentacaoDiversa.php?'.Sessao::getId().'&stAcao='.$stAcao;
        SistemaLegado::alertaAviso($stCaminho.'&inNumLancamento='.$inNumLancamento.'&exercicioReemissao='.$exercicioReemissao.'&inCodNatureza='.$inCodNatureza.'&inNomNatureza='.$NomNatureza,"Entrada Diversa","incluir","aviso", Sessao::getId(), "../");
        break;

}
Sessao::encerraExcecao();

?>

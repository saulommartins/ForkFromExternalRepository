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
    * Arquivo de Processamento
    * Data de Criação: 30/07/2007

    * @author Analista: Dagiane
    * @author Desenvolvedor: André Machado

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 31527 $
    $Name$
    $Author: souzadl $
    $Date: 2007-09-13 13:11:28 -0300 (Qui, 13 Set 2007) $

    * Casos de uso: uc-04.05.59
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//Define o nome dos arquivos PHP
$stPrograma = "ManterDescontoExterno";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgOcul = "OC".$stPrograma.".php";

include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoDescontoExternoPrevidencia.class.php");
include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoDescontoExternoPrevidenciaValor.class.php");
include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoDescontoExternoPrevidenciaAnulado.class.php");
include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php");
$obTPessoalContrato = new TPessoalContrato;
$obTFolhaPagamentoDescontoExternoPrevidencia      = new TFolhaPagamentoDescontoExternoPrevidencia;
$obTFolhaPagamentoDescontoExternoPrevidenciaValor = new TFolhaPagamentoDescontoExternoPrevidenciaValor;
$obTFolhaPagamentoDescontoExternoPrevidenciaAnulado = new TFolhaPagamentoDescontoExternoPrevidenciaAnulado;
$obTFolhaPagamentoDescontoExternoPrevidencia->obTPessoalContrato = &$obTPessoalContrato;
$obTFolhaPagamentoDescontoExternoPrevidenciaValor->obTFolhaPagamentoDescontoExternoPrevidencia = &$obTFolhaPagamentoDescontoExternoPrevidencia;
$obTFolhaPagamentoDescontoExternoPrevidenciaAnulado->obTFolhaPagamentoDescontoExternoPrevidencia = &$obTFolhaPagamentoDescontoExternoPrevidencia;

$stAcao         = $_REQUEST['stAcao'];
$inContrato     = ($_REQUEST['inContrato']) ? $_REQUEST['inContrato'] : $_REQUEST['inRegistro'];
$inValor        = $_REQUEST['inValor'];
$inValorBase    = $_REQUEST['inValorBase'];
$dtVigencia     = $_REQUEST['dtVigencia'];

Sessao::setTrataExcecao(true);
$stFiltro = " WHERE registro = ".$inContrato;
$obTPessoalContrato->recuperaTodos( $rsContrato, $stFiltro );

switch ($stAcao) {
    case "incluir":
    case "alterar":
        $obTPessoalContrato->setDado ( "cod_contrato",  $rsContrato->getCampo("cod_contrato")     );
        $obTFolhaPagamentoDescontoExternoPrevidencia->recuperaRelacionamento( $rsDescontoPrevidencia);

        if( SistemaLegado::dataToBr($rsDescontoPrevidencia->getCampo("vigencia"))  != $dtVigencia ||
            number_format($rsDescontoPrevidencia->getCampo("vl_base_previdencia"),2,',','.')    != $inValorBase ||
            number_format($rsDescontoPrevidencia->getCampo("valor_previdencia"),2,',','.') !=  $inValor){

            $obTFolhaPagamentoDescontoExternoPrevidencia->setDado('cod_contrato', $rsContrato->getCampo("cod_contrato") );
            $obTFolhaPagamentoDescontoExternoPrevidencia->setDado('vigencia', $dtVigencia);
            $obTFolhaPagamentoDescontoExternoPrevidencia->setDado('vl_base_previdencia', $inValorBase);
            $obTFolhaPagamentoDescontoExternoPrevidencia->inclusao();

            if ($inValor > 0) {
                $obTFolhaPagamentoDescontoExternoPrevidenciaValor->obTFolhaPagamentoDescontoExternoPrevidencia = &$obTFolhaPagamentoDescontoExternoPrevidencia;
                $obTFolhaPagamentoDescontoExternoPrevidenciaValor->setDado ( 'valor_previdencia', $inValor);
                $obTFolhaPagamentoDescontoExternoPrevidenciaValor->inclusao();
            }
            $stMsg = ( $stAcao == 'incluir' ) ? "Inclusão concluída com sucesso!" : "Alteração concluída com sucesso!";
        } else {
            $stMsg = ( $stAcao == 'incluir' ) ? "Dados já existem na base!" : "Nenhum dado foi alterado!";
        }
        Sessao::encerraExcecao();
        sistemaLegado::alertaAviso($pgForm,$stMsg,"incluir","aviso", Sessao::getId(), "../");
    break;
    case "excluir";
        $stFiltro  = " AND cod_contrato = ".$rsContrato->getCampo("cod_contrato");
        $obTFolhaPagamentoDescontoExternoPrevidencia->recuperaParaExclusao( $rsDescontoPrevidencia,$stFiltro);

        while (!$rsDescontoPrevidencia->eof()) {
            $obTPessoalContrato->setDado ( 'cod_contrato', $rsDescontoPrevidencia->getCampo("cod_contrato") );
            $obTFolhaPagamentoDescontoExternoPrevidencia->setDado ( 'timestamp', $rsDescontoPrevidencia->getCampo("timestamp") );
            $obTFolhaPagamentoDescontoExternoPrevidenciaAnulado->inclusao();
            $rsDescontoPrevidencia->proximo();
        }
        Sessao::encerraExcecao();
        sistemaLegado::alertaAviso($pgList,"Exclusão concluída com sucesso!","excluir","aviso", Sessao::getId(), "../");
    break;
}

?>

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

    * @author Desenvolvedor: Tiago Finger

    * Casos de uso: uc-04.05.60

    $Id: PRManterDescontoExterno.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroContrato.class.php"                                      );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );

$link = Sessao::read("link");
$stAcao = $_REQUEST["stAcao"] ? $_REQUEST["stAcao"] : $_GET["stAcao"];
$stLink = "&pg=".$link["pg"]."&pos=".$link["pos"];

//Define o nome dos arquivos PHP
$stPrograma = "ManterDescontoExterno";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgFilt = "FL".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgOcul = "OC".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgJS   = "JS".$stPrograma.".js";

include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoDescontoExternoIRRF.class.php"					);
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoDescontoExternoIRRFValor.class.php"				);
include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoDescontoExternoIRRFAnulado.class.php");
include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php");
$obTPessoalContrato = new TPessoalContrato;
$obTFolhaPagamentoDescontoExternoIRRF = new TFolhaPagamentoDescontoExternoIRRF;
$obTFolhaPagamentoDescontoExternoIRRFValor = new TFolhaPagamentoDescontoExternoIRRFValor;
$obTFolhaPagamentoDescontoExternoIRRFAnulado = new TFolhaPagamentoDescontoExternoIRRFAnulado;
$obTFolhaPagamentoDescontoExternoIRRF->obTPessoalContrato = &$obTPessoalContrato;
$obTFolhaPagamentoDescontoExternoIRRFValor->obTFolhaPagamentoDescontoExternoIRRF = &$obTFolhaPagamentoDescontoExternoIRRF;
$obTFolhaPagamentoDescontoExternoIRRFAnulado->obTFolhaPagamentoDescontoExternoIRRF = &$obTFolhaPagamentoDescontoExternoIRRF;

$stAcao = $request->get('stAcao');
$inContrato     = ($_REQUEST['inContrato']) ? $_REQUEST['inContrato'] : $_REQUEST['inRegistro'];
$fiValorBaseIRRF = $_REQUEST['fiValorBaseIRRF'];
$fiValorDescontoIRRF = ( !empty($_REQUEST['fiValorDescontoIRRF']) ) ? $_REQUEST['fiValorDescontoIRRF'] : '0,00';
$stDataVigencia = $_REQUEST['stDataVigencia'];

Sessao::setTrataExcecao(true);
$stFiltro = " WHERE registro = ".$inContrato;
$obTPessoalContrato->recuperaTodos( $rsContrato, $stFiltro );
switch ($stAcao) {
    case "incluir":
    case "alterar";
        if ($fiValorBaseIRRF <= 0) {
            Sessao::getExcecao()->setDescricao("O Valor Base do IRRF deve será maior que zero.");
        }
        $obTFolhaPagamentoDescontoExternoIRRF->setDado ( 'cod_contrato',  $inContrato     );
        $obTFolhaPagamentoDescontoExternoIRRF->recuperaRelacionamento( $rsDescontoExternoIRRF );
        if( number_format( (double) $rsDescontoExternoIRRF->getCampo("vl_base_irrf"), 2, ',', '.' ) != $fiValorBaseIRRF ||
            number_format( (double) $rsDescontoExternoIRRF->getCampo("valor_irrf"), 2, ',', '.' ) != $fiValorDescontoIRRF ||
            SistemaLegado::dataToBr($rsDescontoExternoIRRF->getCampo("vigencia")) != $stDataVigencia ){

            $obTPessoalContrato->setDado("cod_contrato",$rsContrato->getCampo("cod_contrato"));
            $obTFolhaPagamentoDescontoExternoIRRF->setDado ( 'vl_base_IRRF',  $fiValorBaseIRRF   );
            $obTFolhaPagamentoDescontoExternoIRRF->setDado ( 'vigencia',      $stDataVigencia  	 );
            $obTFolhaPagamentoDescontoExternoIRRF->inclusao();

            if ($fiValorDescontoIRRF > 0) {
                $obTFolhaPagamentoDescontoExternoIRRFValor->setDado ( 'valor_IRRF', $fiValorDescontoIRRF );
                $obTFolhaPagamentoDescontoExternoIRRFValor->inclusao();
            }

            $stMsg = ( $stAcao == 'incluir' ) ? "Inclusão concluída com sucesso!" : "Alteração concluída com sucesso!";
    } else {
            $stMsg = ( $stAcao == 'incluir' ) ? "Dados já existem na base!" : "Nenhum dado foi alterado!";
        }
        Sessao::encerraExcecao();
        sistemaLegado::alertaAviso($pgForm,$stMsg,"incluir","aviso", Sessao::getId(), "../");
        break;
    case "excluir":
        $stFiltro  = " AND cod_contrato = ".$rsContrato->getCampo("cod_contrato");
        $obTFolhaPagamentoDescontoExternoIRRF->recuperaParaExclusao( $rsDescontoExternoIRRF,$stFiltro);

        while (!$rsDescontoExternoIRRF->eof()) {
            $obTPessoalContrato->setDado ( 'cod_contrato',  $rsDescontoExternoIRRF->getCampo("cod_contrato") );
            $obTFolhaPagamentoDescontoExternoIRRFAnulado->setDado ( 'timestamp',    $rsDescontoExternoIRRF->getCampo("timestamp")   );
            $obTFolhaPagamentoDescontoExternoIRRFAnulado->inclusao();
            $rsDescontoExternoIRRF->proximo();
        }
        Sessao::encerraExcecao();
        sistemaLegado::alertaAviso($pgList,"Exclusão concluída com sucesso!","excluir","aviso", Sessao::getId(), "../");
        break;
}

?>

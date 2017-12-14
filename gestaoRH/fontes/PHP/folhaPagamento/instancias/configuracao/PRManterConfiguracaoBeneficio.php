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
    * Página de Processamento do Configuração do Cálculo de Benefícios
    * Data de Criação: 27/06/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30711 $
    $Name$
    $Author: vandre $
    $Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

    * Casos de uso: uc-04.05.45
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoBeneficioEvento.class.php" );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoTipoEventoBeneficio.class.php" );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoBeneficio.class.php" );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoBeneficioFornecedor.class.php" );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php" );


$stAcao = $_REQUEST["stAcao"] ? $_REQUEST["stAcao"] : $_GET["stAcao"];
//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoBeneficio";
$pgFilt = "FL".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgOcul = "OC".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgJS   = "JS".$stPrograma.".js";

switch ($stAcao) {
    case "alterar":
        Sessao::setTrataExcecao(true);
        
        if ( $_POST["inCodigoEvento"] != "" ) {
            $obTFolhaPagamentoConfiguracaoBeneficio = new TFolhaPagamentoConfiguracaoBeneficio;
            $obTFolhaPagamentoConfiguracaoBeneficio->setDado('cod_configuracao', 1);
            $obTFolhaPagamentoConfiguracaoBeneficio->inclusao();
        
            $obTFolhaPagamentoTipoEventoBeneficio = new TFolhaPagamentoTipoEventoBeneficio;
            $obTFolhaPagamentoTipoEventoBeneficio->recuperaTodos($rsTipoEventoBeneficio, " WHERE cod_beneficio = 1");        

            $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento;
            $obTFolhaPagamentoEvento->recuperaTodos($rsEvento, " WHERE codigo = '".$_POST["inCodigoEvento"]."'");

            $obTFolhaPagamentoBeneficioEvento = new TFolhaPagamentoBeneficioEvento;
            $obTFolhaPagamentoBeneficioEvento->setDado("cod_configuracao", $obTFolhaPagamentoConfiguracaoBeneficio->getDado("cod_configuracao"));
            $obTFolhaPagamentoBeneficioEvento->setDado("timestamp"       , $obTFolhaPagamentoConfiguracaoBeneficio->getDado("timestamp"));
            $obTFolhaPagamentoBeneficioEvento->setDado("cod_evento"      , $rsEvento->getCampo("cod_evento"));
            $obTFolhaPagamentoBeneficioEvento->setDado("cod_tipo"        , $rsTipoEventoBeneficio->getCampo("cod_tipo"));
            $obTFolhaPagamentoBeneficioEvento->inclusao();
        }else{
            $obTFolhaPagamentoConfiguracaoBeneficio = new TFolhaPagamentoConfiguracaoBeneficio;
            $obTFolhaPagamentoConfiguracaoBeneficio->setDado('cod_configuracao', 1);
            $obTFolhaPagamentoConfiguracaoBeneficio->exclusao();
        
            $obTFolhaPagamentoBeneficioEvento = new TFolhaPagamentoBeneficioEvento;
            $obTFolhaPagamentoBeneficioEvento->setDado("cod_configuracao", 1);
            $obTFolhaPagamentoBeneficioEvento->exclusao();
        }

        $arPlanos = Sessao::read('arPlanos');
        $i = 1 + 1;        
        if ( $arPlanos > 0 ) {
            foreach ($arPlanos as $registro) {
                $obTFolhaPagamentoConfiguracaoBeneficio = new TFolhaPagamentoConfiguracaoBeneficio;
                $obTFolhaPagamentoConfiguracaoBeneficio->setDado('cod_configuracao', $i); $i++;
                $obTFolhaPagamentoConfiguracaoBeneficio->inclusao();
            
                $obTFolhaPagamentoTipoEventoBeneficio = new TFolhaPagamentoTipoEventoBeneficio;
                $obTFolhaPagamentoTipoEventoBeneficio->recuperaTodos($rsTipoEventoBeneficio, " WHERE cod_beneficio = 2");
            
                $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento;
                $obTFolhaPagamentoEvento->recuperaTodos($rsEvento, " WHERE codigo = '".$registro["codigo"]."'");
            
                $obTFolhaPagamentoBeneficioEvento = new TFolhaPagamentoBeneficioEvento;
                $obTFolhaPagamentoBeneficioEvento->setDado("cod_configuracao", $obTFolhaPagamentoConfiguracaoBeneficio->getDado("cod_configuracao"));
                $obTFolhaPagamentoBeneficioEvento->setDado("timestamp"       , $obTFolhaPagamentoConfiguracaoBeneficio->getDado("timestamp"));
                $obTFolhaPagamentoBeneficioEvento->setDado("cod_evento"      , $rsEvento->getCampo("cod_evento"));
                $obTFolhaPagamentoBeneficioEvento->setDado("cod_tipo"        , $rsTipoEventoBeneficio->getCampo("cod_tipo"));
                $obTFolhaPagamentoBeneficioEvento->inclusao();
            
                $obTFolhaPagamentoConfiguracaoBeneficioFornecedor = new TFolhaPagamentoConfiguracaoBeneficioFornecedor;
                $obTFolhaPagamentoConfiguracaoBeneficioFornecedor->setDado("cod_configuracao", $obTFolhaPagamentoConfiguracaoBeneficio->getDado('cod_configuracao'));
                $obTFolhaPagamentoConfiguracaoBeneficioFornecedor->setDado("timestamp"       , $obTFolhaPagamentoConfiguracaoBeneficio->getDado('timestamp'));
                $obTFolhaPagamentoConfiguracaoBeneficioFornecedor->setDado("cgm_fornecedor"  , $registro['numcgm']);
                $obTFolhaPagamentoConfiguracaoBeneficioFornecedor->inclusao();
            }
        }
        
        Sessao::encerraExcecao();
        sistemaLegado::alertaAviso($pgForm,"Configurar cálculo de benefícios concluido com sucesso.","incluir","aviso", Sessao::getId(), "../");
    break;
}

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
    * Página de processamento do IMA Configuração
    * Data de Criação: 21/03/2007

    * @author Analista: Dagiane
    * @author Desenvolvedor: Tiago Finger

    * Casos de uso: uc-04.08.06
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GRH_IMA_MAPEAMENTO."TIMAExportacaoTCMBA.class.php"                                 );
include_once( CAM_GRH_IMA_MAPEAMENTO."TIMAExportacaoTCMBASubDivisao.class.php"  				     );
include_once( CAM_GRH_IMA_MAPEAMENTO."TIMATipoServidor.class.php"                                    );

//Define o nome dos arquivos
$stPrograma = "ManterConfiguracaoTCMBA";
$pgFilt = "FL".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgOcul = "OC".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgJS   = "JS".$stPrograma.".js";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

switch ($stAcao) {
    case "configurar":
        Sessao::setTrataExcecao(true);

        //EXPORTAÇÃO TCM/BA
        $obTIMAExportacaoTCMBA = new TIMAExportacaoTCMBA;
        $obTIMAExportacaoTCMBA->recuperaTodos( $rsExportacaoTCMBA );

        $obTIMAExportacaoTCMBA->setDado("cod_entidade", $_POST['inCodEntidade'] );
        $obTIMAExportacaoTCMBA->setDado("num_entidade", $_POST['inNumEntidade']);
        if ( $rsExportacaoTCMBA->getNumLinhas() == -1 ) {
            $boVerificaOperacao = true;
            $obTIMAExportacaoTCMBA->inclusao();
        } else {
            $boVerificaOperacao = false;
            $obTIMAExportacaoTCMBA->setDado("cod_configuracao", $rsExportacaoTCMBA->getCampo("cod_configuracao"));
            $obTIMAExportacaoTCMBA->alteracao();
        }

        //EXPORTAÇÃO TCM/BA
        $obTIMAExportacaoTCMBA->recuperaTodos( $rsExportacaoTCMBA );
        $rsExportacaoTCMBA->setPrimeiroElemento();

        //EXPORTAÇÃO TCM/BA SUBDIVISÃO
        $obTIMAExportacaoTCMBASubDivisao = new TIMAExportacaoTCMBASubDivisao;
        $obTIMAExportacaoTCMBASubDivisao->recuperaTodos( $rsExportacaoTCMBASubDivisao );

        //TIPOS SERVIDORES
        $obTIMATipoServidor = new TIMATipoServidor;
        $obTIMATipoServidor->recuperaTodos( $rsTiposServidores );
        while ( !$rsTiposServidores->eof() ) {

            $obTIMAExportacaoTCMBASubDivisao->setDado("cod_tipo_servidor", $rsTiposServidores->getCampo("cod_tipo_servidor"));
            $obTIMAExportacaoTCMBASubDivisao->setDado("cod_configuracao", $rsExportacaoTCMBA->getCampo("cod_configuracao"));
            $stNomeCampo = "inCodSubDivisaoSelecionados".$rsTiposServidores->getCampo("cod_tipo_servidor");

            //EXCLUIR
            $obTIMAExportacaoTCMBASubDivisao->recuperaTodos( $rsExportacaoTCMBASubDivisao );
            while ( !$rsExportacaoTCMBASubDivisao->eof() ) {
                if ( !@in_array($rsExportacaoTCMBASubDivisao->getCampo("cod_sub_divisao"), $_POST[$stNomeCampo]) ) {
                    $obTIMAExportacaoTCMBASubDivisao->setDado("cod_sub_divisao", $rsExportacaoTCMBASubDivisao->getCampo("cod_sub_divisao"));
                    $obTIMAExportacaoTCMBASubDivisao->exclusao();
                }
                $rsExportacaoTCMBASubDivisao->proximo();
            }

            //COLOCA EM UMA MATRIZ AS SUBDIVISÔES ONDE O INDICE É O CÓDIGO DO SERVIDOR
            $obTIMAExportacaoTCMBASubDivisao->recuperaTodos( $rsExportacaoTCMBASubDivisao );
            while ( !$rsExportacaoTCMBASubDivisao->eof() ) {
                $stIndice = 'servidor'.$rsExportacaoTCMBASubDivisao->getCampo("cod_tipo_servidor");
                $arCodSubDivisao[$stIndice][] = $rsExportacaoTCMBASubDivisao->getCampo("cod_sub_divisao");
                $rsExportacaoTCMBASubDivisao->proximo();
            }

            //ALTERAR E INCLUIR
            foreach ($_POST[$stNomeCampo] as $inCodSubDivisao) {
                $obTIMAExportacaoTCMBASubDivisao->setDado("cod_sub_divisao", $inCodSubDivisao);
                if ( $rsExportacaoTCMBASubDivisao->getNumLinhas() == -1 ) {
                    $obTIMAExportacaoTCMBASubDivisao->inclusao();
                } elseif ( !@in_array($inCodSubDivisao, $arCodSubDivisao['servidor'. $rsTiposServidores->getCampo("cod_tipo_servidor")]) ) {
                    $obTIMAExportacaoTCMBASubDivisao->inclusao();
                } else {
                    $obTIMAExportacaoTCMBASubDivisao->alteracao();
                }
            }

            $rsTiposServidores->proximo();
        }

        if ($boVerificaOperacao) {
            $stMensagem = 'Configuração da exportação TCM/BA incluída com sucesso!';
        } else {
            $stMensagem = 'Configuração da exportação TCM/BA atualizada com sucesso!';
        }

           Sessao::encerraExcecao();
        sistemaLegado::alertaAviso($pgForm,"","incluir","aviso", Sessao::getId(), "../");
        sistemaLegado::exibeAviso(urlencode($stMensagem),'','');
        break;
}
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

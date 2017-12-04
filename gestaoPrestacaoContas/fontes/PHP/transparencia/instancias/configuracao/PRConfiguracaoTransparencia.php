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
  * Arquivo de Processamento para Exportação ao portal da Transparência

  * @author Diogo Zarpelon.

  * Casos de uso: uc-04.08.27

  $Id:$
  */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php";
include_once CAM_GA_ADM_NEGOCIO."RConfiguracaoConfiguracao.class.php";
include_once CAM_GPC_TRANSPARENCIA_MAPEAMENTO."TConfiguracaoTransparencia.class.php";
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/crontab/CrontabManager.class.php';

$stPrograma = "ConfiguracaoTransparencia";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";

$obTFolhaPagamentoEvento = new TFolhaPagamentoEvento;

$stAcao = $request->get('stAcao');

$obErro = new Erro;

switch ($stAcao) {
    case "alterar":
        Sessao::setTrataExcecao(true);

        # Parametrização da Exportação Automática

        $stHashIdentificador    = $_POST['stHashIdentificador'];
        $stExportacaoAutomatica = (isset($_POST['boExportaAutomatico']) && $_POST['boExportaAutomatico'] == 'true') ? 'true' : 'false';
        $stEventosRemuneracao   = (isset($_POST['inCodEventoSelecionadosRemuneracao'])) ? implode(',', $_POST['inCodEventoSelecionadosRemuneracao']) : '';
        $stEventosRedutorTeto   = (isset($_POST['inCodEventoSelecionadosRedutorTeto'])) ? implode(',', $_POST['inCodEventoSelecionadosRedutorTeto']) : '';
        $stEventosVerba         = (isset($_POST['inCodEventoSelecionadosVerba'])) ? implode(',', $_POST['inCodEventoSelecionadosVerba']) : '';
        $stEventosDeducoes      = (isset($_POST['inCodEventoSelecionadosDeducoes'])) ? implode(',', $_POST['inCodEventoSelecionadosDeducoes']) : '';
        $stEventosJetons        = (isset($_POST['inCodEventoSelecionadosJetons'])) ? implode(',', $_POST['inCodEventoSelecionadosJetons']) : '';

        $obRAdministracaoConfiguracaoRemuneracao = new RConfiguracaoConfiguracao;
        $obRAdministracaoConfiguracaoRemuneracao->setExercicio(Sessao::getExercicio());
        $obRAdministracaoConfiguracaoRemuneracao->setCodModulo(8);
        $obRAdministracaoConfiguracaoRemuneracao->setParametro('remuneracao_eventual');
        $obRAdministracaoConfiguracaoRemuneracao->setValor($stEventosRemuneracao);
        $obErro= $obRAdministracaoConfiguracaoRemuneracao->alterar();

        $obRAdministracaoConfiguracaoRedutorTeto = new RConfiguracaoConfiguracao;
        $obRAdministracaoConfiguracaoRedutorTeto->setExercicio(Sessao::getExercicio());
        $obRAdministracaoConfiguracaoRedutorTeto->setCodModulo(8);
        $obRAdministracaoConfiguracaoRedutorTeto->setParametro('redutor_teto');
        $obRAdministracaoConfiguracaoRedutorTeto->setValor($stEventosRedutorTeto);
        $obErro= $obRAdministracaoConfiguracaoRedutorTeto->alterar();
        
        $obRAdministracaoConfiguracaoVerba = new RConfiguracaoConfiguracao;
        $obRAdministracaoConfiguracaoVerba->setExercicio(Sessao::getExercicio());
        $obRAdministracaoConfiguracaoVerba->setCodModulo(8);
        $obRAdministracaoConfiguracaoVerba->setParametro('verbas_indenizatorias');
        $obRAdministracaoConfiguracaoVerba->setValor($stEventosVerba);
        $obErro= $obRAdministracaoConfiguracaoVerba->alterar();
        
        $obRAdministracaoConfiguracaoDeducoes = new RConfiguracaoConfiguracao;
        $obRAdministracaoConfiguracaoDeducoes->setExercicio(Sessao::getExercicio());
        $obRAdministracaoConfiguracaoDeducoes->setCodModulo(8);
        $obRAdministracaoConfiguracaoDeducoes->setParametro('demais_deducoes');
        $obRAdministracaoConfiguracaoDeducoes->setValor($stEventosDeducoes);
        $obErro= $obRAdministracaoConfiguracaoDeducoes->alterar();

        $obRAdministracaoConfiguracaoJetons = new RConfiguracaoConfiguracao;
        $obRAdministracaoConfiguracaoJetons->setExercicio(Sessao::getExercicio());
        $obRAdministracaoConfiguracaoJetons->setCodModulo(8);
        $obRAdministracaoConfiguracaoJetons->setParametro('pagamento_jetons');
        $obRAdministracaoConfiguracaoJetons->setValor($stEventosJetons);
        $obErro= $obRAdministracaoConfiguracaoJetons->alterar();
        
        $obRAdministracaoOrgaoPoderExecutivo = new RConfiguracaoConfiguracao;
        $obRAdministracaoOrgaoPoderExecutivo->setExercicio(Sessao::getExercicio());
        $obRAdministracaoOrgaoPoderExecutivo->setCodModulo(58);
        $obRAdministracaoOrgaoPoderExecutivo->setParametro('orgao_prefeitura');
        $obRAdministracaoOrgaoPoderExecutivo->setValor($_REQUEST['inCodOrgaoExecutivo']);
        $obErro= $obRAdministracaoOrgaoPoderExecutivo->alterar();
        
        $obRAdministracaoUnidadePoderExecutivo = new RConfiguracaoConfiguracao;
        $obRAdministracaoUnidadePoderExecutivo->setExercicio(Sessao::getExercicio());
        $obRAdministracaoUnidadePoderExecutivo->setCodModulo(58);
        $obRAdministracaoUnidadePoderExecutivo->setParametro('unidade_prefeitura');
        $obRAdministracaoUnidadePoderExecutivo->setValor($_REQUEST['inCodUnidadeExecutivo']);
        $obErro= $obRAdministracaoUnidadePoderExecutivo->alterar();

        $obRAdministracaoOrgaoPoderLegislativo = new RConfiguracaoConfiguracao;
        $obRAdministracaoOrgaoPoderLegislativo->setExercicio(Sessao::getExercicio());
        $obRAdministracaoOrgaoPoderLegislativo->setCodModulo(58);
        $obRAdministracaoOrgaoPoderLegislativo->setParametro('orgao_camara');
        $obRAdministracaoOrgaoPoderLegislativo->setValor($_REQUEST['inCodOrgaoLegislativo']);
        $obErro= $obRAdministracaoOrgaoPoderLegislativo->alterar();

        $obRAdministracaoUnidadePoderLegislativo = new RConfiguracaoConfiguracao;
        $obRAdministracaoUnidadePoderLegislativo->setExercicio(Sessao::getExercicio());
        $obRAdministracaoUnidadePoderLegislativo->setCodModulo(58);
        $obRAdministracaoUnidadePoderLegislativo->setParametro('unidade_camara');
        $obRAdministracaoUnidadePoderLegislativo->setValor($_REQUEST['inCodUnidadeLegislativo']);
        $obErro= $obRAdministracaoUnidadePoderLegislativo->alterar();

        $obRAdministracaoConfiguracaoOrgaoRPPS = new RConfiguracaoConfiguracao;
        $obRAdministracaoConfiguracaoOrgaoRPPS->setExercicio(Sessao::getExercicio());
        $obRAdministracaoConfiguracaoOrgaoRPPS->setCodModulo(58);
        $obRAdministracaoConfiguracaoOrgaoRPPS->setParametro('orgao_rpps');
        $obRAdministracaoConfiguracaoOrgaoRPPS->setValor($_POST['inCodOrgaoRPPS']?$_POST['inCodOrgaoRPPS']:$_POST['inCodOrgaoExecutivo']);
        $obErro= $obRAdministracaoConfiguracaoOrgaoRPPS->alterar();

        $obRAdministracaoConfiguracaoUnidadeRPPS = new RConfiguracaoConfiguracao;
        $obRAdministracaoConfiguracaoUnidadeRPPS->setExercicio(Sessao::getExercicio());
        $obRAdministracaoConfiguracaoUnidadeRPPS->setCodModulo(58);
        $obRAdministracaoConfiguracaoUnidadeRPPS->setParametro('unidade_rpps');
        $obRAdministracaoConfiguracaoUnidadeRPPS->setValor($_POST['inCodUnidadeRPPS']?$_POST['inCodUnidadeRPPS']:$_POST['inCodUnidadeExecutivo']);
        $obErro= $obRAdministracaoConfiguracaoUnidadeRPPS->alterar();

        $obRAdministracaoConfiguracaoOrgaoOutros = new RConfiguracaoConfiguracao;
        $obRAdministracaoConfiguracaoOrgaoOutros->setExercicio(Sessao::getExercicio());
        $obRAdministracaoConfiguracaoOrgaoOutros->setCodModulo(58);
        $obRAdministracaoConfiguracaoOrgaoOutros->setParametro('orgao_outros');
        $obRAdministracaoConfiguracaoOrgaoOutros->setValor($_POST['inCodOrgaoOutros']?$_POST['inCodOrgaoOutros']:$_POST['inCodOrgaoExecutivo']);
        $obErro= $obRAdministracaoConfiguracaoOrgaoOutros->alterar();

        $obRAdministracaoConfiguracaoUnidadeOutros = new RConfiguracaoConfiguracao;
        $obRAdministracaoConfiguracaoUnidadeOutros->setExercicio(Sessao::getExercicio());
        $obRAdministracaoConfiguracaoUnidadeOutros->setCodModulo(58);
        $obRAdministracaoConfiguracaoUnidadeOutros->setParametro('unidade_outros');
        $obRAdministracaoConfiguracaoUnidadeOutros->setValor($_POST['inCodUnidadeOutros']?$_POST['inCodUnidadeOutros']:$_POST['inCodUnidadeExecutivo']);
        $obErro= $obRAdministracaoConfiguracaoUnidadeOutros->alterar();

        # Flag Exportação Automática
        $obRAdministracaoConfiguracaoHashIdentificador = new RConfiguracaoConfiguracao;
        $obRAdministracaoConfiguracaoHashIdentificador->setExercicio(Sessao::getExercicio());
        $obRAdministracaoConfiguracaoHashIdentificador->setCodModulo(58);
        $obRAdministracaoConfiguracaoHashIdentificador->setParametro('hash_identificador');
        $obRAdministracaoConfiguracaoHashIdentificador->setValor($stHashIdentificador);
        $obErro = $obRAdministracaoConfiguracaoHashIdentificador->alterar();
        
        $obRAdministracaoConfiguracaoExportaAutomatico = new RConfiguracaoConfiguracao;
        $obRAdministracaoConfiguracaoExportaAutomatico->setExercicio(Sessao::getExercicio());
        $obRAdministracaoConfiguracaoExportaAutomatico->setCodModulo(58);
        $obRAdministracaoConfiguracaoExportaAutomatico->setParametro('exporta_automatico');
        $obRAdministracaoConfiguracaoExportaAutomatico->setValor($stExportacaoAutomatica);
        $obErro = $obRAdministracaoConfiguracaoExportaAutomatico->alterar();

        
        if ($stExportacaoAutomatica == 'true') {
            # Path do Urbem, necessário para a chamada correta na Cron.
            $stPathUrbem = SistemaLegado::pegaDado("valor", "administracao.configuracao", "WHERE parametro = 'diretorio' AND exercicio = '".Sessao::getExercicio()."'");

            $comments[] = "Criação e exportação dos arquivos para o Portal de Transparência, executado diariamente as 01:00hr p.m #transparencia";
            $comments[] = "urbem-path:".$stPathUrbem. " #transparencia";

            $crontab = new CrontabManager();

            $job = $crontab->newJob();
            $job->onHour('1')
                ->doJob("php ".$stPathUrbem."/gestaoPrestacaoContas/fontes/PHP/transparencia/instancias/exportacao/PRExportarAutomaticoTransparencia.php");

            $job->addComments($comments);

            # Recupera o ID do Job na Cron para verificar se já foi incluso.
            $stJobId = shell_exec("crontab -l | grep PRExportarAutomaticoTransparencia|cut -f2 -d#");
            $stJobId = trim($stJobId);

            $boJobExists = $crontab->jobExists($stJobId);

            # Se não existir, cria um job na Cron.
            if (!$boJobExists) {
                $crontab->add($job);
                $crontab->save();
            }

        } else {
            # Recupera o ID do Job na Cron.
            $stJobId = shell_exec("crontab -l | grep PRExportarAutomaticoTransparencia|cut -f2 -d#");
            $stJobId = trim($stJobId);

            $crontab = new CrontabManager();
            
            $boJobExists = $crontab->jobExists($stJobId);

            # Remove o job caso seja encontrado. A tag é utilizada para encontrar comentários da Job e assim deletar.
            if ($boJobExists) {
                $stTag = "#transparencia";

                $crontab->deleteJob($stJobId, $stTag);
                $crontab->save(false);
            }
        }
        
        Sessao::encerraExcecao();

    break;
}

if (!$obErro->ocorreu()) {
    SistemaLegado::alertaAviso($pgForm,"parâmetros atualizados", "incluir", "aviso", Sessao::getId(), "../");
} else {
    SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>

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
    * Página de Processamento da Configuração dos Eventos Automáticos
    * Data de Criação: 09/11/2015
    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Jean da Silva
    * @package URBEM
    * @subpackage
    * @ignore
    * Casos de uso: uc-04.05.45
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );

$stAcao = $request->get("stAcao");
//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoEventosAutomaticos";
$pgFilt = "FL".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgOcul = "OC".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgJS   = "JS".$stPrograma.".js";

$obErro = new Erro;
$obTransacao = new Transacao;
$boFlagTransacao = false;
$obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

switch ($stAcao) {
    case "configurar":
        if (!$obErro->ocorreu()) {
            $arEventos = Sessao::read('arEventos');
            
            foreach ($arEventos as $registro) {
                $stEventos .= $registro["cod_evento"].",";
            }
            $stEventos = substr($stEventos, 0, strlen($stEventos)-1);

            $obTAdministracaoConfiguracao = new TAdministracaoConfiguracao;
            $obTAdministracaoConfiguracao->setDado("cod_modulo", 27);
            $obTAdministracaoConfiguracao->setDado("exercicio", Sessao::getExercicio());
            $obTAdministracaoConfiguracao->setDado("parametro", "evento_automatico");
            $obTAdministracaoConfiguracao->setDado("valor",$stEventos);
            
            $obErro = $obTAdministracaoConfiguracao->alteracao($boTransacao);
        }

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgForm,"Configurar lançamentos de eventos automáticos concluído com sucesso." ,"incluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::alertaAviso($pgForm.Sessao::read('link')."&stErro=".urlencode($obErro->getDescricao()),"" ,"n_excluir","aviso", Sessao::getId(), "../");
        }
    break;
}

$obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTAdministracaoConfiguracao );

?>
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
  * Página de processamento para Configurar IDE
  * Data de criação : 07/01/2014

  * @author Analista:    Eduardo Paculski Schitz
  * @author Programador: Franver Sarmento de Moraes

  * @ignore

  $Id: $
  $Date: $
  $Author: $
  $Rev: $

  $Rev$:
  $Author$:
  $Date$:

  **/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGConfigurarIDE.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoIDE";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

$obErro = new Erro;
switch ($stAcao) {
    case 'incluir':
        $obTransacao = new Transacao;
        $obTTCEMGConfigurarIDE = new TTCEMGConfigurarIDE();
        $obErro = $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

        if (!$obErro->ocorreu) {
            $obTTCEMGConfigurarIDE->setDado('cod_municipio', $request->get('inCodMunicipio'));
            $obTTCEMGConfigurarIDE->setDado('exercicio', Sessao::getExercicio());
            $obTTCEMGConfigurarIDE->setDado('opcao_semestralidade', $request->get('opcaoSemestralidade'));
            $obErro = $obTTCEMGConfigurarIDE->inclusao($boTransacao);

            if (!$obErro->ocorreu()) {
                SistemaLegado::alertaAviso($pgForm."?".Sessao::getId(),"Configuração IDE","incluir","aviso", Sessao::getId(), "../");
            } else {
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
            }

            $obTransacao->fechaTransacao($boFlagTransacao,$boTransacao,$obErro,$obTTCEMGConfigurarIDE);
        }

        break;
    case 'alterar':
        $obTransacao = new Transacao;
        $obTTCEMGConfigurarIDE = new TTCEMGConfigurarIDE();
        $obErro = $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

        if (!$obErro->ocorreu) {
            $obTTCEMGConfigurarIDE->setDado('cod_municipio', $request->get('inCodMunicipio'));
            $obTTCEMGConfigurarIDE->setDado('exercicio', Sessao::getExercicio());
            $obTTCEMGConfigurarIDE->setDado('opcao_semestralidade', $request->get('opcaoSemestralidade'));
            $obErro = $obTTCEMGConfigurarIDE->alteracao($boTransacao);

            if (!$obErro->ocorreu()) {
                SistemaLegado::alertaAviso($pgForm."?".Sessao::getId(),"Configuração IDE","alterar","aviso", Sessao::getId(), "../");
            } else {
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
            }

            $obTransacao->fechaTransacao($boFlagTransacao,$boTransacao,$obErro,$obTTCEMGConfigurarIDE);
        }
        break;
}
?>

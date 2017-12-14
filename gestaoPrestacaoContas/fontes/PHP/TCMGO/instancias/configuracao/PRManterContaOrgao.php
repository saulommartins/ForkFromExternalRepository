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
    * Página de Processamento
    * Data de Criação   : 16/04/2007

    * @author Henrique Boaventura

    * @ignore

    * $Id: PRManterContaOrgao.php 61678 2015-02-24 19:28:14Z jean $

    * Casos de uso : uc-06.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(TTGO.'TTGOOrgaoPlanoBanco.class.php');
include_once(TTGO.'TTGOOrgao.class.php');

//Define o nome dos arquivos PHP
$stPrograma = "ManterContaOrgao";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

Sessao::setTrataExcecao( true );
$arContas = Sessao::read('arContas');

switch ($_REQUEST['stAcao']) {
    case 'incluir' :
            $obTTGOOrgaoPlanoBanco = new TTGOOrgaoPlanoBanco();
            $obTTGOOrgaoPlanoBanco->setDado('exercicio', Sessao::getExercicio());
            $obTTGOOrgaoPlanoBanco->setDado('num_orgao', Sessao::read('inOrgao'));
            $obTTGOOrgaoPlanoBanco->exclusao();

            $obTTGOOrgao = new TTGOOrgao();

            if ( is_array($arContas)  ) {
                foreach ($arContas as $arAux) {
                    $obTTGOOrgao->setDado('num_orgao', $arAux['num_orgao']);
                    $obTTGOOrgao->setDado('exercicio', Sessao::getExercicio());
                    $obTTGOOrgao->recuperaPorChave($rsRecordSet);

                    if ($rsRecordSet->getNumLinhas() > 0) {
                        $obTTGOOrgaoPlanoBanco->setDado('num_orgao',$arAux['num_orgao']);
                        $obTTGOOrgaoPlanoBanco->setDado('cod_plano',$arAux['cod_plano']);
                        $obTTGOOrgaoPlanoBanco->inclusao();
                    } else {
                        $stMensagem = "Deve ser configurado o orgão ".$arAux['num_orgao']." - ".$arAux['nom_orgao']." antes!";
                        break;
                    }
                }

                if (!$stMensagem) {
                    SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=$stAcao","Configuração ","incluir","incluir_n", Sessao::getId(), "../");
                } else {
                    sistemaLegado::exibeAviso(urlencode($stMensagem),"n_incluir","erro");
                }
            } else {
                sistemaLegado::exibeAviso(urlencode('É necessário cadastrar pelo uma conta!'),"n_incluir","erro");
            }
}

Sessao::encerraExcecao();
